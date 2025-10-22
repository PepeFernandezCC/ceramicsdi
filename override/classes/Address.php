<?php
class Address extends AddressCore
{

    public static function getVatApiData($id_address) {
        $address = new address($id_address);
        $cif = $address->dni;
        $vat = $address->vat_number;
        $country = $address->id_country;
        $customer = $address->id_customer;
        $validate = true;
        

        if ($country == 6) {
            customer::removeIntracomunitaryGroup($customer);
            $validate = false;
        }

        return [
            'vat_number' => ($vat == '' || $vat == NULL) ? $cif : $vat,
            'customer' => $customer,
            'country' => $country, 
            'validate' => $validate
        ];


    }

    public static function updateIntracomunitaryAddress($idAddress, $vatNumber) {
            Db::getInstance()->update('address', [
                'vat_number' => $vatNumber,
            ], 'id_address = ' . $idAddress);
    }
    
    public static function getAddressValidations($id_address) {
        $query = 'SELECT * FROM `ps_address` WHERE `id_address` = ' . (int)$id_address ;
        $addresses = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        $address = $addresses[0];
        $dni_error = false;
        $postCode_error = false;
        $error = 'Sin errores';
        $spain = false; 
        if ($address['id_country'] == '6') {
            $spain = true;
        } 
        if($spain) {
            if ($address['dni'] == '') {
                $dni_error = true;
            }
        }else{
            if ($address['dni'] == '' && $address['company'] != '') {
                $dni_error = true;
            }
        }

        if ($address['postcode'] == '') {
            $postCode_error = true;
        }

        if ($dni_error || $postCode_error) {

            if ($dni_error) {
                $error = 'Error en el campo dni';
            }
            if ($postCode_error) {
                $error = 'Error en el campo postal';
            }
            if ($dni_error && $postCode_error) {
                $error = 'Errores en varios campos';
            }

            return [
                'validations' => false,
                'dni_error' => $dni_error,
                'postCode_error' => $postCode_error, 
                'spain' => $spain,
                'error' => $error
            ];
        }

        return [
            'validations' => true,
            'dni_error' => $dni_error,
            'postCode_error' => $postCode_error, 
            'spain' => $spain,
            'error' => $error
        ];

    }
}
