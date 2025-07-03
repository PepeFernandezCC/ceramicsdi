<?php

class HTMLTemplateDeliverySlip extends HTMLTemplateDeliverySlipCore
{
    public function __construct(OrderInvoice $order_invoice, Smarty $smarty, $bulk_mode = false)
    {
        $this->order_invoice = $order_invoice;
        $this->order = new Order($this->order_invoice->id_order);
        $this->smarty = $smarty;

        if (!isset($this->order_invoice->shop_address) || !$this->order_invoice->shop_address) {
            $this->order_invoice->shop_address = OrderInvoice::getCurrentFormattedShopAddress((int) $this->order->id_shop);
            if (!$bulk_mode) {
                OrderInvoice::fixAllShopAddresses();
            }
        }

        $this->date = Tools::displayDate($order_invoice->delivery_date);
        $prefix = Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id);
        $this->title = sprintf(self::l('%1$s%2$06d'), $prefix, $this->order_invoice->delivery_number);

        $this->shop = new Shop((int) $this->order->id_shop);
    }

}
