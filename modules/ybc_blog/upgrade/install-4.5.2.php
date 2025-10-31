<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }
/**
 * @param Ybc_blog $module
 * @return bool
 * @throws PrestaShopDatabaseException
 */
function upgrade_module_4_5_2($module)
{
    Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ybc_blog_post_product` (
    `id_post` INT(11) NOT NULL , 
    `id_product` INT(11) NOT NULL , 
    PRIMARY KEY (`id_post`, `id_product`)) ENGINE= '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
    $posts = Db::getInstance()->executeS('SELECT id_post, products FROM `'._DB_PREFIX_.'ybc_blog_post` WHERE products!=""');
    if($posts)
    {
        foreach($posts as $post)
        {
            if($post['products'])
            {
                $products = explode('-',$post['products']);
                $products = array_unique($products);
                foreach($products as $id_product)
                {
                    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ybc_blog_post_product`(id_post,id_product) VALUES ("'.(int)$post['id_post'].'","'.(int)$id_product.'")');
                }
            }
        }
    }
    $module->registerHook('actionProductFormBuilderModifier');
    $module->registerHook('displayAdminProductsSeller');
    if(version_compare(_PS_VERSION_,'8.0','>='))
    {
        Tools::copy(dirname(__FILE__).'/../config/ybc_services.yml',dirname(__FILE__).'/../config/services.yml');
    }
    if(version_compare(_PS_VERSION_,'1.7','<'))
    {
        $module->recurseCopy(dirname(__FILE__) . '/../views/templates/admin/_configure/templates', _PS_OVERRIDE_DIR_ . 'controllers/admin/templates',true);
    }
    return true;
}