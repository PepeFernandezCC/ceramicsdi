<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class DeliveryPriceCalculator extends Module
{
    public function __construct()
    {
        $this->name = 'deliverypricecalculator';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'Ceramic Connection';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_,
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Calculadora de Precio de Envío');
        $this->description = $this->l('Calculadora de coste de envío por país/provincia/código postal en el carrito');
        $this->confirmUninstall = $this->l('¿Estás seguro de que quieres desinstalar?');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayCartVoucherDeliveryCalculator')
            && $this->registerHook('displayHeader');
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/front.css');
        $this->context->controller->addJS($this->_path . 'views/js/front.js');

        Media::addJsDef([
            'deliveryPriceCalculatorPriceUrl' => $this->context->link->getModuleLink($this->name, 'price'),
            'deliveryPriceCalculatorProvincesUrl' => $this->context->link->getModuleLink($this->name, 'provinces'),
            'deliveryPriceCalculatorProductEstimateUrl' => $this->context->link->getModuleLink($this->name, 'productestimate'),
        ]);
    }

    public function hookDisplayCartVoucherDeliveryCalculator($params)
    {
        if (!$this->active) {
            return '';
        }

        $cart = $this->context->cart;

        if (!$cart || !$cart->id) {
            return '';
        }

        $this->context->smarty->assign([
            'countryList' => Country::getCountries($this->context->language->id),
            'package_weight' => $cart->getTotalWeight(),
            'cart_id' => $cart->id,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/delivery_price_calculator.tpl');
    }
}
