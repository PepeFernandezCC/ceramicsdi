<?php

class Customer extends CustomerCore {

    public static function assignCustomerGroup($idCustomer)
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