<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Cbqmeta extends Module
{
    public function __construct()
    {
        $this->name = 'cbqmeta';
        $this->tab = 'analytics_stats';
        $this->version = '1.0.0';
        $this->author = 'CERAMIC CONNECTION';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('CBQ Meta (Custom SDK)');
        $this->description = $this->l('Loads custom cbq SDK and tracks PageView, AddToCart, InitiateCheckout and Purchase.');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayOrderConfirmation');
    }

    public function hookDisplayHeader($params)
    {
        $this->context->controller->registerJavascript(
            $this->name . '-js',
            'modules/' . $this->name . '/views/js/cbqmeta.js',
            ['position' => 'bottom', 'priority' => 150]
        );

        $this->context->smarty->assign([
            'cbqmeta_host' => 'https://conversionsapimeta.ceramicconnection.es/',
            'cbqmeta_pixel_id' => '1995950068885803330',
            'cbqmeta_sdk_url' => 'https://conversionsapimeta.ceramicconnection.es/sdk/1995950068885803330/events.js',
            'cbqmeta_controller' => (string)$this->context->controller->php_self,
            'cbqmeta_currency' => $this->context->currency ? (string)$this->context->currency->iso_code : 'EUR',
        ]);

        return $this->display(__FILE__, 'views/templates/hook/header.tpl');
    }

    public function hookDisplayOrderConfirmation($params)
    {
        if (empty($params['order']) || !Validate::isLoadedObject($params['order'])) {
            return '';
        }

        $order = $params['order'];
        $currency = new Currency((int)$order->id_currency);
        $value = (float)$order->total_paid_tax_incl;

        $this->context->smarty->assign([
            'purchase_value' => number_format($value, 2, '.', ''),
            'purchase_currency' => $currency && Validate::isLoadedObject($currency) ? (string)$currency->iso_code : 'EUR',
            'purchase_order_id' => (int)$order->id,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/order_confirmation.tpl');
    }
}
