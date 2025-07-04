<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Clase genérica para lo relacionado con pedidos o recogidas de Correos o CEX
 */
class CorreosOficialCheckout extends Module
{
    private $db;

    public function __construct()
    {
        self::$db = \Db::getInstance();
    }

    /**
     * Consigue un campo de la base de datos.
     * @param string $value valor que devuelvo: * para devolver el conjunto
     * @param string $key lo que busco
     * @param string $field el campo
     * @param table: tabla
     */
    public static function getValue($value, $key, $field, $table)
    {
        $query="SELECT $value FROM "._DB_PREFIX_."$table WHERE $field='$key'";
        return Db::getInstance()->getRow($query);
    }

    public static function getValueConf($field, $table)
    {
        $query="SELECT value FROM "._DB_PREFIX_."$table WHERE name='$field'";
        return Db::getInstance()->getRow($query);
    }

    public static function getCarrierParams($carrier)
    {
        $sql= 'SELECT * FROM '._DB_PREFIX_.'correos_oficial_products 
        WHERE id_carrier="'.$carrier.'"';
        return Db::getInstance()->getRow($sql);
    }

    public static function insertCartIntoRequests($id_cart)
    {
        $query="SELECT * FROM ". _DB_PREFIX_ ."correos_oficial_requests WHERE id_cart='$id_cart'";

        return Db::getInstance()->getRow($query);
    }

    public static function insertReferenceCode($id_cart, $reference_code, $data, $existing_cart = null)
    {
        $data = CorreosOficialUtils::replaceUnicodeCharacters($data);

        if (!$existing_cart) {
            $query = "INSERT INTO ". _DB_PREFIX_ ."correos_oficial_requests (id_cart, reference_code, data)
        VALUES ('$id_cart', '$reference_code', '$data')";
        } else {
            $query = "UPDATE ". _DB_PREFIX_ ."correos_oficial_requests SET reference_code='$reference_code',
                  data='$data' WHERE id_cart='$id_cart'";
        }
        Db::getInstance()->execute($query);
    }
    
}
