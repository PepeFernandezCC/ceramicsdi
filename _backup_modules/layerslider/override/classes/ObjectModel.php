<?php
/**
 * Creative Slider - Responsive Slideshow Module
 * https://creativeslider.webshopworks.com
 *
 * @author    WebshopWorks <info@webshopworks.com>
 * @copyright 2015-2020 WebshopWorks
 * @license   One Domain Licence
 *
 * Not allowed to resell or redistribute this software
 */

defined('_PS_VERSION_') or exit;

abstract class ObjectModel extends ObjectModelCore
{
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
