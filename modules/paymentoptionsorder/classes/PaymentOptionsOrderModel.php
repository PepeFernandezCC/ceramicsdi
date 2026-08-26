<?php
/**
 * CERAMIC CONNECTION - Payment options order
 *
 * Model layer. This is fully self-discovering: it does NOT hardcode which
 * payment modules or sub-options exist. Instead:
 *
 *  - Every time a real customer reaches the checkout payment step, the
 *    override (override/classes/checkout/PaymentOptionsFinder.php) calls
 *    observe() with whatever options PrestaShop actually returned that
 *    render, and any option never seen before gets recorded here
 *    (Configuration::PAYMENT_OPTIONS_REGISTRY).
 *  - The backoffice ordering screen shows only registry entries whose owning
 *    module is currently installed and enabled (Module::isEnabled()) - so
 *    installing a new payment module makes it appear automatically (as soon
 *    as it's been seen once at checkout), and uninstalling/disabling one
 *    makes it disappear automatically, without any code change.
 *  - The chosen order (Configuration::PAYMENT_OPTIONS_ORDER) is applied at
 *    checkout; PrestaShop's own cart/currency/country/group rules still
 *    decide which options actually show for a given cart - this module only
 *    controls the relative order among whatever ends up showing.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class PaymentOptionsOrderModel
{
    const CONFIG_KEY_ORDER = 'PAYMENT_OPTIONS_ORDER';
    const CONFIG_KEY_REGISTRY = 'PAYMENT_OPTIONS_REGISTRY';

    /**
     * Derives a stable key for one payment option.
     *
     * Prefers $option['module_name'] when it looks like a technical id the
     * module namespaced itself under the hook group key (e.g. ps_checkout
     * returns "ps_checkout-paypal", "ps_checkout-paylater", ...) - that's
     * stable regardless of the option's position among its siblings.
     *
     * Falls back to "<hookModuleKey>:<positionInGroup>" for modules that
     * only expose a human/translated label as module_name (e.g.
     * revolutpayment). This is stable as long as the module always returns
     * its own sub-options in the same relative order, which is the normal
     * case since that order is hardcoded in the module's own PHP.
     *
     * @param string $hookModuleKey The array key PrestaShop grouped this option under (real module technical name)
     * @param int $position Position of this option inside its group, this render
     * @param array $option The formatted option (as returned by PaymentOptionsFinderCore::present())
     *
     * @return string
     */
    public static function deriveKey($hookModuleKey, $position, array $option)
    {
        $moduleName = isset($option['module_name']) ? (string) $option['module_name'] : '';

        if ($moduleName !== '' && strpos($moduleName, $hookModuleKey . '-') === 0) {
            return $moduleName;
        }

        return $hookModuleKey . ':' . $position;
    }

    /**
     * Merges newly observed options into the registry. Cheap no-op (no DB
     * write) when nothing new was seen.
     *
     * @param array<int, array{key: string, module: string, label: string}> $observedOptions
     */
    public static function observe(array $observedOptions)
    {
        $registry = self::getRawRegistry();
        $changed = false;

        foreach ($observedOptions as $observed) {
            $key = $observed['key'];
            $entry = ['label' => $observed['label'], 'module' => $observed['module']];

            if (!isset($registry[$key]) || $registry[$key] !== $entry) {
                $registry[$key] = $entry;
                $changed = true;
            }
        }

        if ($changed) {
            Configuration::updateValue(self::CONFIG_KEY_REGISTRY, json_encode($registry));
        }
    }

    /**
     * @return array<string, array{label: string, module: string}>
     */
    public static function getRawRegistry()
    {
        $registry = json_decode((string) Configuration::get(self::CONFIG_KEY_REGISTRY), true);

        return is_array($registry) ? $registry : [];
    }

    /**
     * Every option ever observed, regardless of whether its module is
     * currently active. Used to define the full key-space for ordering
     * purposes, so a temporarily disabled module keeps its saved position.
     *
     * @return array<string, array{label: string, module: string}>
     */
    public static function getKnownOptionsDefinition()
    {
        return self::getRawRegistry();
    }

    /**
     * Subset of the registry whose owning module is currently installed and
     * enabled - what the backoffice ordering screen should display/let the
     * user drag.
     *
     * @return array<string, array{label: string, module: string}>
     */
    public static function getActiveOptionsDefinition()
    {
        return array_filter(self::getKnownOptionsDefinition(), function (array $def) {
            return Module::isEnabled($def['module']);
        });
    }

    /**
     * Saved order as a flat array of keys, guaranteed to contain every known
     * registry key exactly once (missing keys appended in registry
     * insertion order, keys no longer in the registry are dropped).
     *
     * @return string[]
     */
    public static function getSavedOrder()
    {
        $known = array_keys(self::getKnownOptionsDefinition());
        $saved = json_decode((string) Configuration::get(self::CONFIG_KEY_ORDER), true);

        if (!is_array($saved)) {
            return $known;
        }

        $saved = array_values(array_intersect($saved, $known));
        $missing = array_diff($known, $saved);

        return array_merge($saved, $missing);
    }

    /**
     * Same as getSavedOrder(), limited to currently active modules - this is
     * the order actually applied at checkout among the visible options.
     *
     * @return string[]
     */
    public static function getSavedActiveOrder()
    {
        $active = array_keys(self::getActiveOptionsDefinition());

        return array_values(array_intersect(self::getSavedOrder(), $active));
    }

    /**
     * @param string[] $submittedKeys Active keys, in the order submitted by the form
     *
     * @return bool
     */
    public static function saveOrder(array $submittedKeys)
    {
        $active = array_keys(self::getActiveOptionsDefinition());
        $orderedActiveKeys = array_values(array_intersect($submittedKeys, $active));

        // Keep inactive/unseen modules' relative order untouched (they can't
        // be part of $submittedKeys since they're not shown in the form) and
        // append them after the reordered active ones.
        $previousOrder = self::getSavedOrder();
        $restKeepingOrder = array_values(array_diff($previousOrder, $orderedActiveKeys));

        $finalOrder = array_merge($orderedActiveKeys, $restKeepingOrder);

        return Configuration::updateValue(self::CONFIG_KEY_ORDER, json_encode($finalOrder));
    }

    /**
     * Active options, ordered, resolved to [key, label] pairs, ready for
     * display in the backoffice drag & drop list.
     *
     * @return array<int, array{key: string, label: string}>
     */
    public static function getOrderedItemsForDisplay()
    {
        $definitions = self::getActiveOptionsDefinition();
        $items = [];

        foreach (self::getSavedActiveOrder() as $key) {
            $items[] = [
                'key' => $key,
                'label' => $definitions[$key]['label'],
            ];
        }

        return $items;
    }
}
