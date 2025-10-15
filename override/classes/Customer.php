<?php

class Customer extends CustomerCore {

    public static function assignIntracomunitaryGroup($idCustomer)
    {

        $customerGroups = customer::checkCustomerGroup($idCustomer);

        if($customerGroups['default_group'] != '6') {

            Db::getInstance()->update('customer', [
                'id_default_group' => '6',
            ], 'id_customer = ' . $idCustomer);

        }

        if (!in_array('6', $customerGroups['customer_groups'])) {
            Db::getInstance()->insert('customer_group', [
                'id_customer' => (int)$idCustomer,
                'id_group' => '6'
            ]);

        }

    }


    public static function updateCustomerSiret($customerId, $siret) {
            Db::getInstance()->update('customer', [
                'siret' => $siret,
            ], 'id_customer = ' . $customerId);
    }

    public static function insertIntracomunitaryLog($response, $message, $vatNumber, $idCustomer, $id_country) {

            Db::getInstance()->insert('intracomunitary_log', [
                'id_customer' => (int)$idCustomer,
                'vat_number' => $vatNumber,
                'id_country' => $id_country,
                'response' => $response ? '1' : '0',
                'message' => $message,
                'date' => date("d-m-Y H:i:s")
            ]);
    }

    public static function removeIntracomunitaryGroup($idCustomer) {
        $customerGroups = customer::checkCustomerGroup($idCustomer);

        // Paso 1: Si existe el grupo 6, eliminarlo
        if (in_array('6', $customerGroups['customer_groups'])) {
            Db::getInstance()->delete('customer_group', 'id_customer = ' . (int)$idCustomer . ' AND id_group = 6');
        }

        // Paso 2: Decidir nuevo grupo por defecto
        $newDefaultGroup = in_array('5', $customerGroups['customer_groups']) ? '5' : '3';

        // Actualizar el grupo por defecto
        Db::getInstance()->update('customer', [
            'id_default_group' => $newDefaultGroup
        ], 'id_customer = ' . (int)$idCustomer);
    }

    public static function checkCustomerGroup($idCustomer) {

        $defaultGroupQuery = 'SELECT id_default_group FROM ' ._DB_PREFIX_ . 'customer WHERE id_customer = '.(int)$idCustomer;
        $groupArrayQuery = 'SELECT id_group FROM '. _DB_PREFIX_ . 'customer_group WHERE id_customer = ' .(int)$idCustomer;

        $defaultGroup = Db::getInstance()->getValue($defaultGroupQuery);
        $groupArray = Db::getInstance()->executeS($groupArrayQuery);

        return [
            'default_group' => $defaultGroup,
            'customer_groups' => array_column($groupArray, 'id_group')
        ];

    }
    
}