<?php

class CorreosProducts extends ObjectModel
{
    public $id;
    public $name;
    public $delay;
    public $active;
    public $company;
    public $url;
    public $codigoProducto;
    public $id_carrier;
    public $product_type;
    public $max_packages;

    public static $definition = [
        'table' => 'correos_oficial_products',
        'primary' => 'id',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => [
            'id' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'company' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName'],
            'url' => ['type' => self::TYPE_STRING, 'validate' => 'isAbsoluteUrl'],
            'codigoProducto' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName'],
            'id_carrier' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'product_type' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName'],
            'max_packages' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
        ],
    ];

    public static function getCorreosProductByIdCarrier($id_carrier)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'correos_oficial_products WHERE `id_carrier` = ' . (int) $id_carrier;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }
}
