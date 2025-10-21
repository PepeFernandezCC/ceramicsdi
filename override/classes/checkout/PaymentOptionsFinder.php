<?php

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class PaymentOptionsFinder extends PaymentOptionsFinderCore
{
    public function find()
    {
        return CorreosOficialCheckout::filterPaymentsMethods(parent::find(Context::getContext()));
    }
}