<?php

/**
 * Reorders the payment options returned by the core finder so they no longer
 * follow the hook/module grouping, but the order configured in the backoffice
 * (module "paymentoptionsorder") instead.
 *
 * The list of possible options is NOT hardcoded here: every real render
 * "observes" whatever options PrestaShop actually returned and records any
 * new one into Configuration::PAYMENT_OPTIONS_REGISTRY (see
 * modules/paymentoptionsorder/classes/PaymentOptionsOrderModel.php). The
 * backoffice screen then lets the merchant drag-reorder whatever is
 * currently active, and this class simply applies that saved order.
 *
 * Each entry is matched, in this priority:
 *   1) by $option['module_name'] when it looks like a technical id
 *      namespaced under the hook group key (e.g. ps_checkout sets
 *      "ps_checkout-paypal", "ps_checkout-paylater", ...)
 *   2) by "<hook module key>:<position inside its group>" (used for modules
 *      like revolutpayment/redsyspur/ps_wirepayment whose module_name is a
 *      translated label instead of a technical identifier, but whose
 *      internal ordering is fixed in code)
 *
 * If the "paymentoptionsorder" module is not installed (or nothing has been
 * observed yet), options are left in their original order.
 *
 * @author Julio Colás
 */
class PaymentOptionsFinder extends PaymentOptionsFinderCore
{
    public function present($free = false)
    {
        $formattedOptions = parent::present($free);

        if ($free) {
            return $formattedOptions;
        }

        $modelClass = $this->loadModelClass();

        $flatOptions = [];
        $observedOptions = [];
        $originalIndex = 0;

        foreach ($formattedOptions as $moduleKey => $options) {
            foreach (array_values($options) as $position => $option) {
                $key = $modelClass
                    ? $modelClass::deriveKey($moduleKey, $position, $option)
                    : $moduleKey . ':' . $position;

                $flatOptions[] = [
                    'option' => $option,
                    'key' => $key,
                    'original_index' => $originalIndex++,
                ];

                $observedOptions[] = [
                    'key' => $key,
                    'module' => $moduleKey,
                    'label' => isset($option['call_to_action_text']) ? (string) $option['call_to_action_text'] : $key,
                ];
            }
        }

        if ($modelClass) {
            $modelClass::observe($observedOptions);
            $savedOrder = $modelClass::getSavedOrder();
        } else {
            $savedOrder = [];
        }

        // Explicit tie-breaker on the original position so the sort stays
        // deterministic for keys with equal (or missing) weight.
        usort($flatOptions, function (array $a, array $b) use ($savedOrder) {
            $weightA = array_search($a['key'], $savedOrder, true);
            $weightB = array_search($b['key'], $savedOrder, true);
            $weightA = $weightA === false ? count($savedOrder) : $weightA;
            $weightB = $weightB === false ? count($savedOrder) : $weightB;

            return [$weightA, $a['original_index']] <=> [$weightB, $b['original_index']];
        });

        return [
            'sorted' => array_map(function (array $entry) {
                return $entry['option'];
            }, $flatOptions),
        ];
    }

    /**
     * @return string|null Fully-qualified class name, or null if the ordering module isn't available
     */
    private function loadModelClass()
    {
        $modelFile = _PS_MODULE_DIR_ . 'paymentoptionsorder/classes/PaymentOptionsOrderModel.php';

        if (!Module::isEnabled('paymentoptionsorder') || !file_exists($modelFile)) {
            return null;
        }

        require_once $modelFile;

        return 'PaymentOptionsOrderModel';
    }
}
