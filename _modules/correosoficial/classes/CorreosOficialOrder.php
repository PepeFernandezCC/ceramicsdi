<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class CorreosOficialOrder extends Order
{
    private $id_order;
    private $order;

    public function __construct($id_order)
    {
        $this->id_order = $id_order;
        parent::__construct($id_order);
        $this->order = new Order($this->id_order);

    }

    public function getFirstMessage()
    {
        return $this->order->getFirstMessage();
    }

    public function getTotalWeight()
    {
        $cart = new Cart($this->order->id_cart);
        return $cart->getTotalWeight();
    }

    public function getTotalPaid($currency = null)
    {
        return $this->order->total_paid;
    }

    public function getSubTotal()
    {
        return $this->order->total_products;
    }

    public function getUnits()
    {
        $sql = "SELECT count(id_order) as orderUnits FROM " . CorreosOficialUtils::getPrefix() . "order_detail WHERE id_order='" . $this->id_order . "'";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        return $result[0]['orderUnits'];
    }

    public function isCashOnDeliveryMethodType()
    {
        $paymentMethodCodSelected = CorreosOficialConfigDao::getConfigValue('CashOnDeliveryMethod');
        return ($this->order->module == $paymentMethodCodSelected) ? 1 : 0;
    }

    public function getCurrentState(){
        return $this->order->getCurrentState();
    }

    public function orderExist()
    {
        $sql = "SELECT count(id_order) as c FROM " . CorreosOficialUtils::getPrefix() . "orders WHERE id_order='" . (int) $this->id_order . "'";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        return $result['c'];
    }

    public function getShippingNumbersByIdOrderForSavedOrder() {
        $prefix = _DB_PREFIX_;
        $sql = "SELECT coso.shipping_number FROM {$prefix}correos_oficial_saved_orders coso
                JOIN {$prefix}correos_oficial_orders coo ON coso.exp_number = coo.shipping_number 
                WHERE coo.id_order={$this->id_order}";

        $results = Db::getInstance()->executeS($sql);
        return $this->mergeArraysIntoOne($results);
    }

    public function getShippingNumbersByIdOrderForReturns()
    {
        $prefix = _DB_PREFIX_;
        $sql = "SELECT shipping_number FROM {$prefix}correos_oficial_saved_returns WHERE id_order = {$this->id_order}";

        $results = Db::getInstance()->executeS($sql);
        return $this->mergeArraysIntoOne($results);
    }

    public function getExpeditionNumberByIdOrderForReturn()
    {
        $prefix = _DB_PREFIX_;
        $sql = "SELECT exp_number FROM {$prefix}correos_oficial_saved_returns WHERE id_order = {$this->id_order}";
        $result = Db::getInstance()->getValue($sql);
        return $result;
    }

    public function getExpeditionNumberByIdOrderForSavedOrder() {
        $prefix = _DB_PREFIX_;
        $sql = "SELECT exp_number FROM {$prefix}correos_oficial_saved_orders WHERE id_order = {$this->id_order}";
        return Db::getInstance()->getValue($sql);
    }

    public function getCarrierTypeByOrderId() {
        $prefix = _DB_PREFIX_;
        $sql = "SELECT carrier_type FROM {$prefix}correos_oficial_orders WHERE id_order = {$this->id_order}";
        return Db::getInstance()->getValue($sql);
    }

    public function mergeArraysIntoOne($shipping_numbers) {
		$clean_shipping_numbers = [];
		foreach($shipping_numbers as $shipping_number) {
            if($shipping_number['shipping_number'] == null) {
                $clean_shipping_numbers[] = $shipping_number['exp_number'];
            } else {
                $clean_shipping_numbers[] = $shipping_number['shipping_number'];
            }
		}

		return $clean_shipping_numbers;
	}
}
