<?php
use PrestaShop\Module\Mbeshipping\Lib\MbeWs;
use PrestaShop\Module\Mbeshipping\Helper\DataHelper;
use PrestaShop\Module\Mbeshipping\Helper\RatesHelper;
class Carrier extends CarrierCore
{

    public static function getCheapestDeliveryOptionByCart(Cart $cart, $useTax = false)
    {
        if (!Validate::isLoadedObject($cart)) {
            return null;
        }

        $id_address = (int)$cart->id_address_delivery;
        if (!$id_address) {
            return null;
        }

        $address = new Address($id_address);
        if (!Validate::isLoadedObject($address)) {
            return null;
        }

        $country = new Country((int)$address->id_country);
        if (!Validate::isLoadedObject($country)) {
            return null;
        }

        $delivery_option_list = $cart->getDeliveryOptionList($country);

        if (!is_array($delivery_option_list) || empty($delivery_option_list)) {
            return null;
        }

        $bestOption = null;
        $bestPrice = null;

        foreach ($delivery_option_list as $addressId => $options) {
            if (!is_array($options) || empty($options)) {
                continue;
            }

            foreach ($options as $optionKey => $option) {
                if (empty($option['carrier_list']) || !is_array($option['carrier_list'])) {
                    continue;
                }

                $price = $useTax
                    ? (float)$option['total_price_with_tax']
                    : (float)$option['total_price_without_tax'];

                if ($bestPrice === null || $price < $bestPrice) {
                    $bestPrice = $price;

                    $firstCarrier = reset($option['carrier_list']);
                    $id_carrier = null;

                    if (isset($firstCarrier['instance']) && Validate::isLoadedObject($firstCarrier['instance'])) {
                        $id_carrier = (int)$firstCarrier['instance']->id;
                    } elseif (isset($firstCarrier['id_carrier'])) {
                        $id_carrier = (int)$firstCarrier['id_carrier'];
                    }

                    $bestOption = [
                        'id_carrier' => $id_carrier,
                        'option_key' => $optionKey,
                        'price_with_tax' => isset($option['total_price_with_tax']) ? (float)$option['total_price_with_tax'] : 0,
                        'price_without_tax' => isset($option['total_price_without_tax']) ? (float)$option['total_price_without_tax'] : 0,
                    ];
                }
            }
        }

        return $bestOption;
    }
}
