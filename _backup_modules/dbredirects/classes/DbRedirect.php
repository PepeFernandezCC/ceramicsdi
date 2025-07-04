<?php

class DbRedirect extends ObjectModel
{

    public $id;
    public $id_dbredirects;
    public $active = 1;
    public $type;
    public $url_antigua;
    public $url_nueva;
    public $date_add;

    public static $definition = array(
        'table' => 'dbredirects',
        'primary' => 'id_dbredirects',
        'multilang' => false,
        'fields' => array(
            'active' =>			array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'type' =>		    array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'url_antigua' =>	array('type' => self::TYPE_STRING, 'required' => false , 'validate' => 'isCleanHtml', 'size' => 255),
            'url_nueva' =>	    array('type' => self::TYPE_STRING, 'required' => false , 'validate' => 'isCleanHtml', 'size' => 255),
            'date_add' =>	    array('type' => self::TYPE_DATE),
        ),
    );

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
    }

    public function isToggleStatus($id_dbredirects){
        $sql = "SELECT active FROM "._DB_PREFIX_."dbredirects WHERE id_dbredirects = '$id_dbredirects'";
        $status = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        if($status == 0){
            $active = 1;
        } else {
            $active = 0;
        }
        $update = "UPDATE "._DB_PREFIX_."dbredirects SET active = '$active' WHERE id_dbredirects = '$id_dbredirects'";
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update);

        die(Tools::jsonEncode(
            array(
                'status' => true,
                'message' => 'Actualizado correctamente',
            )
        ));
    }

    static function isRedirect($url){
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow("
                SELECT *
                FROM `" . _DB_PREFIX_ . "dbredirects`
                WHERE `url_antigua`= '$url' AND `active` = 1
                ORDER BY `id_dbredirects` DESC
            ");
    }

}