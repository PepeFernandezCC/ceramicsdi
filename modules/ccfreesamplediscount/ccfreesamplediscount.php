<?php
/**
 * CERAMIC CONNECTION - Free sample discount
 *
 * Detects products priced at 0.01 in the cart and applies an equivalent
 * cart rule discount so the customer does not pay for sample products while
 * external shipping integrations still receive product lines with value > 0.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class CcFreeSampleDiscount extends Module
{
    /** @var bool Prevent recursive hook execution when applying/removing cart rules. */
    protected static $isSyncing = false;

    const CART_RULE_CODE_PREFIX = 'CCFREESAMPLE-';
    const DISCOUNT_LABEL = 'Descuento por muestras gratis';
    const SAMPLE_UNIT_PRICE_CENTS = 1;

    /**
     * Use a tax-included voucher amount.
     *
     * This avoids carts such as:
     *   samples 0.05 - discount 0.05 = tax residue 0.01
     *
     * If your prices are tax excluded and you want the exact tax-excluded
     * discount instead, set this to false and use reduction_tax = 0.
     */
    const DISCOUNT_TAX_INCLUDED = true;

    public function __construct()
    {
        $this->name = 'ccfreesamplediscount';
        $this->tab = 'pricing_promotion';
        $this->version = '0.1.5';
        $this->author = 'CERAMIC CONNECTION';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('CC Free Sample Discount');
        $this->description = $this->l('Applies an automatic discount for products priced at 0.01 so free samples remain free at checkout.');
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('actionCartSave')
            && $this->registerHook('actionFrontControllerInitAfter')
            && $this->registerHook('displayShoppingCart')
            && $this->registerHook('displayShoppingCartFooter')
            && $this->registerHook('displayBeforeCarrier')
            && $this->registerHook('actionValidateOrder');
    }

    public function uninstall()
    {
        return $this->deleteOpenModuleCartRules() && parent::uninstall();
    }

    /**
     * Recalculate the sample discount every time the cart is saved.
     *
     * IMPORTANT:
     * PrestaShop also saves/updates the cart while loading normal front pages
     * such as category pages. Running Cart::getProducts() from this hook in
     * those pages can trigger Product::getPriceStatic() and die(Tools::displayError()).
     * Therefore the hook is limited to cart/checkout contexts.
     *
     * @param array $params
     */
    public function hookActionCartSave($params)
    {
        if (!$this->shouldSyncInCurrentContext(false)) {
            return;
        }

        $this->syncFromHookParams($params);
    }

    /**
     * Extra safety net for production themes/checkouts: ensure the rule exists
     * when the front controller loads an existing cart.
     *
     * @param array $params
     */
    public function hookActionFrontControllerInitAfter($params)
    {
        if (!$this->shouldSyncInCurrentContext(false)) {
            return;
        }

        $this->syncFromHookParams($params);
    }

    /**
     * Recalculate on the shopping cart page. This hook returns no markup.
     *
     * @param array $params
     * @return string
     */
    public function hookDisplayShoppingCart($params)
    {
        $this->syncFromHookParams($params);
        return '';
    }

    /**
     * Recalculate on cart footer if the theme uses this hook.
     *
     * @param array $params
     * @return string
     */
    public function hookDisplayShoppingCartFooter($params)
    {
        $this->syncFromHookParams($params);
        return '';
    }

    /**
     * Recalculate before carrier selection in checkout.
     *
     * @param array $params
     * @return string
     */
    public function hookDisplayBeforeCarrier($params)
    {
        $this->syncFromHookParams($params);
        return '';
    }

    /**
     * Only run automatic cart-rule syncing in controllers where it is actually
     * needed. This is the main protection against fatal errors while browsing
     * category/product/home pages.
     *
     * @param bool $force
     * @return bool
     */
    protected function shouldSyncInCurrentContext($force = false)
    {
        if ($force) {
            return true;
        }

        if (defined('_PS_ADMIN_DIR_')) {
            return false;
        }

        if (!isset($this->context) || !isset($this->context->controller)) {
            return false;
        }

        $phpSelf = isset($this->context->controller->php_self)
            ? (string) $this->context->controller->php_self
            : '';

        $controller = Tools::getValue('controller');
        $controller = is_string($controller) ? Tools::strtolower($controller) : '';

        $allowed = array(
            'cart',
            'order',
            'order-opc',
            'orderopc',
            'checkout',
            'order-confirmation',
            'orderconfirmation',
        );

        return in_array($phpSelf, $allowed, true) || in_array($controller, $allowed, true);
    }

    /**
     * Resolve the current cart from hook params/context and sync it.
     *
     * @param array $params
     */
    protected function syncFromHookParams($params)
    {
        if (self::$isSyncing) {
            return;
        }

        $cart = null;
        if (isset($params['cart']) && Validate::isLoadedObject($params['cart'])) {
            $cart = $params['cart'];
        } elseif (isset($this->context->cart) && Validate::isLoadedObject($this->context->cart)) {
            $cart = $this->context->cart;
        }

        if (!$cart || !(int) $cart->id) {
            return;
        }

        $this->syncCartRuleForCart($cart);
    }

    /**
     * Once the order is validated, detach the module rule from the cart.
     * The discount remains copied into the order by PrestaShop.
     *
     * @param array $params
     */
    public function hookActionValidateOrder($params)
    {
        if (empty($params['cart']) || !Validate::isLoadedObject($params['cart'])) {
            return;
        }

        $cart = $params['cart'];
        $cartRule = $this->findModuleCartRuleForCart((int) $cart->id);
        if (!Validate::isLoadedObject($cartRule)) {
            return;
        }

        // Do not delete the cart rule here, to avoid interfering with order history.
        // Just make it unusable after this cart has become an order.
        $cartRule->active = 0;
        $cartRule->date_to = date('Y-m-d H:i:s', strtotime('-1 minute'));
        $cartRule->update();
    }

    /**
     * Create, update or remove the automatic cart rule for a cart.
     *
     * @param Cart $cart
     */
    protected function syncCartRuleForCart(Cart $cart)
    {
        self::$isSyncing = true;

        try {
            $discountAmount = $this->calculateSampleDiscountAmount($cart);
            $cartRule = $this->findModuleCartRuleForCart((int) $cart->id);

            if ($discountAmount <= 0) {
                if (Validate::isLoadedObject($cartRule)) {
                    $cart->removeCartRule((int) $cartRule->id);
                    $cartRule->delete();
                }
                return;
            }

            if (!Validate::isLoadedObject($cartRule)) {
                $cartRule = $this->createCartRuleForCart($cart, $discountAmount);
                if (Validate::isLoadedObject($cartRule)) {
                    $cart->addCartRule((int) $cartRule->id);
                }
            } else {
                $cartRule->reduction_amount = (float) $discountAmount;
                $cartRule->reduction_tax = self::DISCOUNT_TAX_INCLUDED ? 1 : 0;
                $cartRule->reduction_currency = (int) $cart->id_currency;
                $cartRule->active = 1;
                $cartRule->date_to = date('Y-m-d H:i:s', strtotime('+30 days'));
                $cartRule->update();

                if (!$this->cartHasRule($cart, (int) $cartRule->id)) {
                    $cart->addCartRule((int) $cartRule->id);
                }
            }
        } catch (Exception $e) {
            PrestaShopLogger::addLog(
                sprintf('[%s] %s', $this->name, $e->getMessage()),
                3,
                null,
                'Cart',
                (int) $cart->id,
                true
            );
        } finally {
            self::$isSyncing = false;
        }
    }

    /**
     * Calculates the discount amount using direct SQL instead of Cart::getProducts().
     *
     * This is deliberate: Cart::getProducts() can enter Product::getPriceStatic()
     * and die(Tools::displayError()) when the cart contains an inconsistent line
     * or when PrestaShop is in the middle of refreshing cart context. Direct SQL
     * is safer for this module because we only need to detect products whose base
     * unit price is 0.01.
     *
     * @param Cart $cart
     * @return float
     */
    protected function calculateSampleDiscountAmount(Cart $cart)
    {
        $discountAmount = 0.0;
        $rows = $this->getCartProductRowsSafely($cart);

        if (empty($rows) || !is_array($rows)) {
            return 0.0;
        }

        foreach ($rows as $product) {
            $quantity = isset($product['cart_quantity']) ? (int) $product['cart_quantity'] : 0;
            if ($quantity <= 0) {
                continue;
            }

            $unitPriceTaxExcl = (float) $product['unit_price_tax_excl'];
            $taxExclCents = (int) round($unitPriceTaxExcl * 100);

            if ($taxExclCents !== self::SAMPLE_UNIT_PRICE_CENTS) {
                continue;
            }

            if (self::DISCOUNT_TAX_INCLUDED) {
                $taxRate = $this->getProductTaxRateSafely(
                    (int) $product['id_product'],
                    (int) $cart->id_address_delivery
                );

                $unitAmount = $unitPriceTaxExcl * (1 + ($taxRate / 100));
            } else {
                $unitAmount = $unitPriceTaxExcl;
            }

            $discountAmount += $unitAmount * $quantity;
        }

        return $this->roundAmountForCartCurrency($discountAmount, $cart);
    }

    /**
     * Read cart lines and product base prices without invoking Product price
     * calculation. Combination additional price is included when present.
     *
     * @param Cart $cart
     * @return array
     */
    protected function getCartProductRowsSafely(Cart $cart)
    {
        $idShop = (int) $cart->id_shop;
        if ($idShop <= 0 && isset($this->context->shop) && (int) $this->context->shop->id > 0) {
            $idShop = (int) $this->context->shop->id;
        }

        $sql = '
            SELECT
                cp.`id_product`,
                cp.`id_product_attribute`,
                SUM(cp.`quantity`) AS cart_quantity,
                (ps.`price` + IFNULL(pas.`price`, 0)) AS unit_price_tax_excl
            FROM `' . _DB_PREFIX_ . 'cart_product` cp
            INNER JOIN `' . _DB_PREFIX_ . 'product_shop` ps
                ON ps.`id_product` = cp.`id_product`
                AND ps.`id_shop` = ' . (int) $idShop . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` pas
                ON pas.`id_product_attribute` = cp.`id_product_attribute`
                AND pas.`id_shop` = ' . (int) $idShop . '
            WHERE cp.`id_cart` = ' . (int) $cart->id . '
            GROUP BY cp.`id_product`, cp.`id_product_attribute`, ps.`price`, pas.`price`
        ';

        $rows = Db::getInstance()->executeS($sql);

        return is_array($rows) ? $rows : array();
    }

    /**
     * @param int $idProduct
     * @param int $idAddress
     * @return float
     */
    protected function getProductTaxRateSafely($idProduct, $idAddress)
    {
        if ((int) $idProduct <= 0) {
            return 0.0;
        }

        try {
            return (float) Tax::getProductTaxRate((int) $idProduct, (int) $idAddress);
        } catch (Exception $e) {
            PrestaShopLogger::addLog(
                sprintf('[%s] Could not calculate tax rate for product %d: %s', $this->name, (int) $idProduct, $e->getMessage()),
                2,
                null,
                'Product',
                (int) $idProduct,
                true
            );
        }

        return 0.0;
    }

    /**
     * @param float $amount
     * @param Cart $cart
     * @return float
     */
    protected function roundAmountForCartCurrency($amount, Cart $cart)
    {
        $precision = 2;

        if (!empty($cart->id_currency)) {
            $currency = new Currency((int) $cart->id_currency);
            if (Validate::isLoadedObject($currency) && isset($currency->precision)) {
                $precision = (int) $currency->precision;
            }
        }

        if (method_exists('Tools', 'ps_round')) {
            return (float) Tools::ps_round((float) $amount, $precision);
        }

        return (float) round((float) $amount, $precision);
    }

    /**
     * @param Cart $cart
     * @param float $discountAmount
     * @return CartRule|null
     */
    protected function createCartRuleForCart(Cart $cart, $discountAmount)
    {
        $cartRule = new CartRule();
        $cartRule->name = $this->buildMultilangName(self::DISCOUNT_LABEL);
        $cartRule->description = 'Automatic discount generated by module ' . $this->name . ' for cart #' . (int) $cart->id;
        $cartRule->code = self::CART_RULE_CODE_PREFIX . (int) $cart->id;
        $cartRule->id_customer = (int) $cart->id_customer;
        $cartRule->date_from = date('Y-m-d H:i:s', strtotime('-1 day'));
        $cartRule->date_to = date('Y-m-d H:i:s', strtotime('+30 days'));
        $cartRule->quantity = 1;
        $cartRule->quantity_per_user = 1;
        $cartRule->priority = 1;
        $cartRule->partial_use = 0;
        $cartRule->active = 1;

        $cartRule->minimum_amount = 0;
        $cartRule->minimum_amount_tax = 1;
        $cartRule->minimum_amount_currency = (int) $cart->id_currency;
        $cartRule->minimum_amount_shipping = 0;

        $cartRule->cart_rule_restriction = 0;
        $cartRule->country_restriction = 0;
        $cartRule->carrier_restriction = 0;
        $cartRule->group_restriction = 0;
        $cartRule->product_restriction = 0;
        $cartRule->shop_restriction = 0;

        $cartRule->free_shipping = 0;
        $cartRule->reduction_percent = 0;
        $cartRule->reduction_amount = (float) $discountAmount;
        $cartRule->reduction_tax = self::DISCOUNT_TAX_INCLUDED ? 1 : 0;
        $cartRule->reduction_currency = (int) $cart->id_currency;
        $cartRule->reduction_product = 0;
        $cartRule->gift_product = 0;
        $cartRule->gift_product_attribute = 0;

        if (!$cartRule->add()) {
            return null;
        }

        return $cartRule;
    }

    /**
     * @param int $cartId
     * @return CartRule|null
     */
    protected function findModuleCartRuleForCart($cartId)
    {
        $code = pSQL(self::CART_RULE_CODE_PREFIX . (int) $cartId);
        $idCartRule = (int) Db::getInstance()->getValue(
            'SELECT id_cart_rule FROM `' . _DB_PREFIX_ . 'cart_rule` WHERE `code` = "' . $code . '"'
        );

        if ($idCartRule <= 0) {
            return null;
        }

        $cartRule = new CartRule($idCartRule);
        return Validate::isLoadedObject($cartRule) ? $cartRule : null;
    }

    /**
     * @param Cart $cart
     * @param int $cartRuleId
     * @return bool
     */
    protected function cartHasRule(Cart $cart, $cartRuleId)
    {
        $exists = (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'cart_cart_rule`
             WHERE `id_cart` = ' . (int) $cart->id . '
             AND `id_cart_rule` = ' . (int) $cartRuleId
        );

        return $exists > 0;
    }

    /**
     * @param string $name
     * @return array
     */
    protected function buildMultilangName($name)
    {
        $translations = $this->getDiscountLabelTranslations();
        $names = array();

        foreach (Language::getLanguages(false) as $language) {
            $isoCode = isset($language['iso_code']) ? Tools::strtolower($language['iso_code']) : '';
            $names[(int) $language['id_lang']] = isset($translations[$isoCode])
                ? $translations[$isoCode]
                : $name;
        }

        return $names;
    }

    /**
     * CartRule names by PrestaShop language ISO code.
     *
     * @return array
     */
    protected function getDiscountLabelTranslations()
    {
        return array(
            'es' => 'Descuento por muestras gratis',
            'fr' => 'Remise sur les échantillons gratuits',
            'en' => 'Free samples discount',
            'de' => 'Rabatt für kostenlose Muster',
            'pt' => 'Desconto para amostras grátis',
            'br' => 'Desconto para amostras grátis',
            'nl' => 'Korting voor gratis stalen',
        );
    }

    /**
     * Cleanup only cart rules generated by this module.
     *
     * @return bool
     */
    protected function deleteOpenModuleCartRules()
    {
        $ids = Db::getInstance()->executeS(
            'SELECT id_cart_rule FROM `' . _DB_PREFIX_ . 'cart_rule`
             WHERE `code` LIKE "' . pSQL(self::CART_RULE_CODE_PREFIX) . '%"'
        );

        if (!is_array($ids)) {
            return true;
        }

        foreach ($ids as $row) {
            $cartRule = new CartRule((int) $row['id_cart_rule']);
            if (Validate::isLoadedObject($cartRule)) {
                $cartRule->delete();
            }
        }

        return true;
    }
}
