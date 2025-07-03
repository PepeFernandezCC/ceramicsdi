<?php
defined('_PS_VERSION_') or exit;
abstract class ObjectModel extends ObjectModelCore
{
    /*
    * module: layerslider
    * date: 2025-06-05 14:17:56
    * version: 6.6.9
    */
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        if (version_compare(_PS_VERSION_, '1.7.1', '<') && !defined('_PS_ADMIN_DIR_')) {
            $class = get_class($this);
            $map = array(
                'CategoriesClass' => 'description',
                'NewsClass' => 'content',
            );
            if (isset($map[$class]) && ($ls = Module::getInstanceByName('layerslider'))) {
                $ls->filterShortcode($this->{$map[$class]});
            }
        }
    }
}
