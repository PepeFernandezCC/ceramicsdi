<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';

class CorreosOficialOrders extends Module
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function getCustomerOrder($id_order)
    {
        $query = "
        SELECT
            po.id_order,
            po.id_cart,
            pa.firstname as customer_firstname,
            pa.lastname as customer_lastname,
            pa.dni as customer_dni,
            pa.address1 as delivery_address,
            pa.address2 as delivery_address2,
            pa.city as delivery_city,
            pa.postcode as delivery_postcode,
            pa.phone as delivery_phone,
            pa.phone,
            pa.phone_mobile,
            cus.email as customer_email,
            pc.iso_code as delivery_country_iso

        FROM " . _DB_PREFIX_ . "orders po
        LEFT JOIN " . _DB_PREFIX_ . "customer cus ON (cus.id_customer = po.id_customer)
        LEFT JOIN " . _DB_PREFIX_ . "address pa ON (po.id_address_delivery = pa.id_address)
        LEFT JOIN " . _DB_PREFIX_ . "state ps ON (ps.id_state = pa.id_state)
        LEFT JOIN " . _DB_PREFIX_ . "country pc ON (pa.id_country = pc.id_country)
        WHERE po.id_order=$id_order";

        //ps.name as delivery_state,

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
        
        $result['delivery_phone'] = CorreosOficialUtils::cleanTelephoneNumber($result['delivery_phone']);
        
        $result['phone'] = CorreosOficialUtils::cleanTelephoneNumber($result['phone']);          
        
        return $result;
    }

    public static function getRequestRecord($id_order)
    {
        $query = "SELECT * FROM " . _DB_PREFIX_ . "correos_oficial_requests cor
        LEFT JOIN " . _DB_PREFIX_ . "orders po ON (cor.id_cart = po.id_cart)
        WHERE po.id_order=$id_order";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }

    protected const SUBQUERY = "(SELECT psc2.name AS old_name FROM " . _DB_PREFIX_ . "carrier psc2 WHERE id_reference=psc.id_reference ORDER BY id_carrier ASC LIMIT 1)";

    public static function getCorreosOrder($id_order)
    {
        $query = "SELECT * FROM " . _DB_PREFIX_ . "correos_oficial_orders coo
        LEFT JOIN  " . _DB_PREFIX_ . "correos_oficial_products cop ON (cop.id = coo.id_product )
        WHERE coo.id_order = '$id_order'";

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }

    public static function getCorreosPackages($id_order, $exp_number)
    {
        $query = "SELECT * FROM " . _DB_PREFIX_ . "correos_oficial_orders coo
        LEFT JOIN " . _DB_PREFIX_ . "correos_oficial_saved_orders coso ON (coso.exp_number = coo.shipping_number)
        WHERE coo.id_order=$id_order";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    public static function getCorreosReturn($id_order)
    {
        $query = "SELECT * FROM " . _DB_PREFIX_ . "correos_oficial_returns WHERE id_order = $id_order";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }

    public static function getCorreosPackagesReturn($id_order)
    {
        $query = "SELECT * FROM " . _DB_PREFIX_ . "correos_oficial_returns cor
        LEFT JOIN " . _DB_PREFIX_ . "correos_oficial_saved_returns cosr ON (cosr.exp_number = cor.shipping_number)
        WHERE cor.id_order=$id_order";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    public static function getCorreosPickupReturn($id_order)
    {
        $query = "SELECT * FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_pickups_returns WHERE id_order = $id_order";
        $result = self::launchQuery($query, true);

        if ($result) {
            return $result[0];
        }
    }

    public static function launchQuery($query, $as_array = false)
    {
        $dao = new CorreosOficialDao();
        return $dao->getRecordsWithQuery($query, $as_array);
    }

    public function getIdCarrier($id_order, $id_product)
    {
        $dao = new CorreosOficialDao();

        $order = new Order($id_order);

        // Consultamos si es transportista propio de PS
        $result = $dao->readRecord(
            'carrier',
            "WHERE id_carrier='$order->id_carrier' AND external_module_name!='correosoficial'",
            "count(id_carrier) as ps_carrier_count"
        );

        // Si es transportista propio de PS recuperamos el id_carrier si está asociado en Ajustes->ZyT
        if ($result[0]->ps_carrier_count) {
            $table = 'correos_oficial_carriers_products';
            $co_order = new CorreosOficialOrder($id_order);
            $address = new Address($co_order->id_address_delivery);
            $id_zone = $address->getZoneById($co_order->id_address_delivery);
            // Obtenemos objeto con relaciones de carrier, producto y zona
            $result = $dao->readRecord($table, "WHERE id_product='$id_product' and id_zone='$id_zone' and id_shop=".$this->context->shop->id);

        }

        // Si no está asociado en correos_oficial_carriers_products buscamos en correos_oficial_products
        if (!isset($result->ps_carrier)) {
            $table = 'correos_oficial_products';
            // Obtenemos objeto con relaciones de carrier, producto
            $result = $dao->readRecord($table, "WHERE id='$id_product'");
        }

        return $result[0]->id_carrier;
    }

    public static function updateOrderManifestDate($id_order)
    {
        $sql = "UPDATE " . _DB_PREFIX_ . "correos_oficial_orders SET manifest_date=NOW()
        WHERE id_order=$id_order";
        Db::getInstance()->execute($sql);
    }
}
