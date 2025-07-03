<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class CorreosOficialCarrier extends CarrierModule
{

    public const NAME = 'correosoficial';
    protected const SUBQUERY = "(SELECT psc2.name AS old_name FROM " . _DB_PREFIX_ . "carrier psc2 WHERE id_reference=psc.id_reference ORDER BY id_carrier ASC LIMIT 1)";

    public function __construct()
    {
        // Métodos estáticos
    }

    public function carrierExists($new_carrier)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "carrier where name='$new_carrier' and external_module_name='" . self::NAME . "' and deleted=0";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (count($result)) {
            $sql = "UPDATE " . _DB_PREFIX_ . "carrier SET active='1'
                  where name='" . $new_carrier . "' and external_module_name='" . self::NAME . "' and deleted=0";
            Db::getInstance()->execute($sql);
            return true;
        } else {
            return false;
        }
    }

    public static function getCarrier($id_carrier_order)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "carrier psc LEFT JOIN
        " . _DB_PREFIX_ . "correos_oficial_products cocp
        ON ( " . self::SUBQUERY . "= cocp.name)
        WHERE psc.id_carrier='" . $id_carrier_order . "'";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public static function getCarrierByProductId($id_carrier)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "correos_oficial_products WHERE id='" . $id_carrier . "'";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public function getCarriers()
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "carrier psc
            LEFT JOIN " . _DB_PREFIX_ . "correos_oficial_products coc ON ( " . self::SUBQUERY . "= cop.name)
            WHERE psc.external_module_name='" . self::NAME . "' and psc.active = 1 and psc.deleted = 0";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function getCarriersProducts($id_carrier_order, $id_zone, $id_shop)
    {
        $sql = "SELECT id_product FROM " . _DB_PREFIX_ . "correos_oficial_carriers_products WHERE id_carrier='" . $id_carrier_order . "' AND id_zone='" . $id_zone . "' AND id_shop='" . $id_shop . "'";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public static function getCarriersByCompany($company, $include_all = false)
    {
        $are_enabled = '';

        if (!$include_all) {
            $are_enabled = 'and psc.active = 1 and';
        }

        if ($company != 'both') {
            $sql = "SELECT *,cop.id as my_id FROM " . _DB_PREFIX_ . "correos_oficial_products cop
            WHERE cop.company ='" . $company . "'";
        } else {
            $sql = "SELECT *,cop.id as my_id FROM " . _DB_PREFIX_ . "correos_oficial_products cop";
        }
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function getClientCodeByCompany($company)
    {
        $sql = "
        SELECT * FROM " . _DB_PREFIX_ . "correos_oficial_codes
        WHERE company = '" . $company . "'";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public function getCarrierId($carrier)
    {
        $sql = "
        SELECT id_carrier FROM " . _DB_PREFIX_ . "carrier where name='$carrier'
        and external_module_name='" . self::NAME . "' and deleted='0'";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function getCarrierType($id_carrier)
    {
        $sql = "
        SELECT cop.product_type FROM " . _DB_PREFIX_ . "carrier psc
        LEFT JOIN " . _DB_PREFIX_ . "correos_oficial_products cop ON (" . self::SUBQUERY . " = psc.id_reference)
        WHERE psc.external_module_name='" . self::NAME . "' and psc.active = 1 and psc.deleted = 0 and psc.id_carrier='" . $id_carrier . "'";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function resetCarriers($id_shop)
    {
        // Si es multitienda reseteamos la tabla ps_carrier_shop
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
            $sql = "DELETE pcs FROM " . _DB_PREFIX_ . "carrier_shop AS pcs
            JOIN " . _DB_PREFIX_ . "carrier AS pc ON pcs.id_carrier = pc.id_carrier
            WHERE pc.external_module_name='" . self::NAME . "' AND pcs.id_shop = ".$id_shop;
        } else {
            $sql = "UPDATE " . _DB_PREFIX_ . "carrier SET active='0'
            where external_module_name='" . self::NAME . "'";
        }

        Db::getInstance()->execute($sql);
    }

    public function addCarrier($product)
    {
        global $co_module_url_ps;
        $carrier = new Carrier();

        $carrier->name = $this->l($product->name, 'CorreosOficialCarrier');
        $carrier->is_module = true;
        $carrier->active = 1;
        $carrier->need_range = 1;
        $carrier->shipping_external = false;
        $carrier->range_behavior = 0;
        $carrier->external_module_name = self::NAME;
        $carrier->shipping_method = 2;
        //$carrier->max_weight = $product->max_weight;
        $carrier->url = $product->url;

        foreach (Language::getLanguages() as $lang) {
            $carrier->delay[$lang['id_lang']] = $this->l($product->delay, 'CorreosOficialCarrier');
        }

        if ($carrier->add()) {
            //$this->addZones($carrier);
            $this->addGroups($carrier);
            $this->addRanges($carrier);

            if ($product->company == 'Correos') {
                copy(dirname(__FILE__) . '/../views/img/carriers/logo_carrier_correos.jpg', _PS_SHIP_IMG_DIR_ . '/' . (int) $carrier->id . '.jpg');
            } else if ($product->company == 'CEX') {
                copy(dirname(__FILE__) . '/../views/img/carriers/logo_carrier_cex.jpg', _PS_SHIP_IMG_DIR_ . '/' . (int) $carrier->id . '.jpg');
            }

            Configuration::updateValue('MYSHIPPINGMODULE_CARRIER_ID', (int) $carrier->id);

            $this->updateProductCorreosOficial((int) $carrier->id, $product->id);
            return $carrier;
        }

        return false;
    }

    public function addGroups($carrier)
    {
        $groups_ids = array();
        $groups = Group::getGroups(Context::getContext()->language->id);
        foreach ($groups as $group) {
            $groups_ids[] = $group['id_group'];
        }
        $carrier->setGroups($groups_ids);
    }

    public function addRanges($carrier)
    {
        $range_price = new RangePrice();
        $range_price->id_carrier = $carrier->id;
        $range_price->delimiter1 = '0';
        $range_price->delimiter2 = '10000';
        $range_price->add();

        $range_weight = new RangeWeight();
        $range_weight->id_carrier = $carrier->id;
        $range_weight->delimiter1 = '0';
        $range_weight->delimiter2 = '10000';
        $range_weight->add();
    }

    protected function addZones($carrier)
    {
        $zones = Zone::getZones();

        foreach ($zones as $zone) {
            $carrier->addZone($zone['id_zone']);
        }
    }

    public function getOrderShippingCost($params, $shipping_cost)
    {
        if (Context::getContext()->customer->logged) {
            $id_address_delivery = Context::getContext()->cart->id_address_delivery;
            $address = new Address($id_address_delivery);

            /**
             * Send the details through the API
             * Return the price sent by the API
             */
            return 10;
        }

        return $shipping_cost;
    }
    public function getOrderShippingCostExternal($params)
    {
        return true;
    }

    public function updateProductCorreosOficial($id_carrier, $id_product)
    {
        $sql = "UPDATE " . _DB_PREFIX_ . "correos_oficial_products SET id_carrier='$id_carrier'
        WHERE id='$id_product'";
        Db::getInstance()->execute($sql);
    }
    /**
	 * Multitienda
	 */
    public function updateCarrierShop($id_carrier, $id_shop)
    {
        $sql = "INSERT INTO " . _DB_PREFIX_ . "carrier_shop (`id_carrier`, `id_shop`) VALUES ($id_carrier,$id_shop) ON DUPLICATE KEY UPDATE id_shop  = VALUES(id_shop)";
        Db::getInstance()->execute($sql);
    }

    public static function updateProductFromCarriersPS($id_carrier, $new_id_carrier, $active, $id_shop)
    {
        $sql = "UPDATE " . _DB_PREFIX_ . "correos_oficial_products SET id_carrier='$new_id_carrier'
        WHERE id_carrier='$id_carrier'";
        Db::getInstance()->execute($sql);

        $sql = "UPDATE " . _DB_PREFIX_ . "correos_oficial_products_shop SET active='$active'
        WHERE id_product=(SELECT id FROM " . _DB_PREFIX_ . "correos_oficial_products WHERE id_carrier='$new_id_carrier') and id_shop = $id_shop";
        Db::getInstance()->execute($sql);

    }

    public static function updateOrdersFromCarriersPS($table, $id_carrier, $new_id_carrier)
    {
        $sql = "UPDATE " . _DB_PREFIX_ . "$table SET id_carrier='$new_id_carrier'
        WHERE id_carrier='$id_carrier'";
        Db::getInstance()->execute($sql);
    }

    /**
	 * Consigue la compañia desde un pedido
	 * @param int el id del pedido
	 * @return string Correos o CEX
	 */
	public static function getCompanyByOrder($id_order) {
        $findCompany = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("
                Select
                " . _DB_PREFIX_ . "correos_oficial_products.company,
                " . _DB_PREFIX_ . "orders.id_order
                From
                " . _DB_PREFIX_ . "orders Inner Join
                " . _DB_PREFIX_ . "order_carrier On " . _DB_PREFIX_ . "order_carrier.id_order = " . _DB_PREFIX_ . "orders.id_order Inner Join
                " . _DB_PREFIX_ . "correos_oficial_products On " . _DB_PREFIX_ . "order_carrier.id_carrier = " . _DB_PREFIX_ . "correos_oficial_products.id_carrier
                Where
                " . _DB_PREFIX_ . "orders.id_order = $id_order
        ");

        // Si tenemos resultados devolvemos la compañia
        if (count($findCompany)) {
            return $findCompany[0]['company'];
        }else{
            return false;
        }
	}

}
