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
        
        //Obtener carrier local

        /*LOCAL*/
        /*
        $correos = 254;
        $correos_internacional = 243;
        $camion = 256;
        $camion_internacional = 195;
        $seur = 229;
        $transaher = 251;
        */
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
}
