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

    public static function getCustomerShowTax($idCustomer) {
        $customerGroups = customer::checkCustomerGroup($idCustomer);

        if($customerGroups['default_group'] == '5' || $customerGroups['default_group'] == '6') {
            return false;
        }

        if (in_array('5', $customerGroups['customer_groups']) || in_array('6', $customerGroups['customer_groups'])) {
            return false;
        }

        return true;
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
        /**
     * Get Address as array.
     *
     * @param int $idAddress Address ID
     * @param int|null $idLang Language ID
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null
     */
    public function getSimpleAddress($idAddress, $idLang = null)
    {
        if (!$this->id || !(int) $idAddress || !$idAddress) {
            return [
                'id' => '',
                'alias' => '',
                'firstname' => '',
                'lastname' => '',
                'company' => '',
                'address1' => '',
                'address2' => '',
                'postcode' => '',
                'city' => '',
                'id_state' => '',
                'state' => '',
                'state_iso' => '',
                'id_country' => '',
                'country' => '',
                'country_iso' => '',
                'other' => '',
                'phone' => '',
                'phone_mobile' => '',
                'vat_number' => '',
                'dni' => '',
                'is_invoice' => ''
            ];
        }

        $sql = $this->getSimpleAddressSql($idAddress, $idLang);
        $res = Db::getInstance()->executeS($sql);
        if (count($res) === 1) {
            return $res[0];
        } else {
            return $res;
        }
    }

    /**
     * Get SQL query to retrieve Address in an array.
     *
     * @param int|null $idAddress Address ID
     * @param int|null $idLang Language ID
     *
     * @return string
     */
    public function getSimpleAddressSql($idAddress = null, $idLang = null)
    {
        if (null === $idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $shareOrder = (bool) Context::getContext()->shop->getGroup()->share_order;

        $sql = 'SELECT DISTINCT
                      a.`id_address` AS `id`,
                      a.`alias`,
                      a.`firstname`,
                      a.`lastname`,
                      a.`company`,
                      a.`address1`,
                      a.`address2`,
                      a.`postcode`,
                      a.`city`,
                      a.`id_state`,
                      s.name AS state,
                      s.`iso_code` AS state_iso,
                      a.`id_country`,
                      cl.`name` AS country,
                      co.`iso_code` AS country_iso,
                      a.`other`,
                      a.`phone`,
                      a.`phone_mobile`,
                      a.`vat_number`,
                      a.`dni`,
                      a.`is_invoice`

                    FROM `' . _DB_PREFIX_ . 'address` a
                    LEFT JOIN `' . _DB_PREFIX_ . 'country` co ON (a.`id_country` = co.`id_country`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (co.`id_country` = cl.`id_country`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'state` s ON (s.`id_state` = a.`id_state`)
                    ' . ($shareOrder ? '' : Shop::addSqlAssociation('country', 'co')) . '
                    WHERE
                        `id_lang` = ' . (int) $idLang . '
                        AND `id_customer` = ' . (int) $this->id . '
                        AND a.`deleted` = 0
                        AND a.`active` = 1';

        if (null !== $idAddress) {
            $sql .= ' AND a.`id_address` = ' . (int) $idAddress;
        }

        return $sql;
    }

}