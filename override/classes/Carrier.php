<?php
use PrestaShop\Module\Mbeshipping\Lib\MbeWs;
use PrestaShop\Module\Mbeshipping\Helper\DataHelper;
use PrestaShop\Module\Mbeshipping\Helper\RatesHelper;
class Carrier extends CarrierCore
{


    public static function getDefaultCarrierByAddressId($addressId, $weight = null) {

        $address = new address($addressId);
        $id_state = $address->id_state;
        $id_country = $address->id_country;
        
        /*SERVIDOR*/
        
        $correos = 254;
        $correos_internacional = 266;
        $camion = 256;
        $camion_internacional = 265;
        $seur = 271;
        $transaher = 264;
        
        /* EXCEPCIONES POR ZONA */
        
        $transaher_zones = ['65', '66', '67', '68'];
        $weight = $weight ? $weight : 20;
        $delivery_by_truck = $weight ? true : false ;
        $id_carrier = $delivery_by_truck ? $camion_internacional : $correos_internacional;

        //calcular id_zone

        $id_zone = State::getIdZone((int)$id_state);

        if ($id_country == 6) {//Envío en españa
            $id_carrier = $delivery_by_truck ? $camion : $correos;
        }

        if (in_array($id_zone, $transaher_zones)) {
            $id_carrier = $delivery_by_truck ? $transaher : $correos_internacional;
        }

        return $id_carrier;

    }

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
