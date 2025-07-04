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

if (!defined('_PS_VERSION_'))
	exit;
function upgrade_module_3_1_9($object)
{
    $posts = Db::getInstance()->executeS('SELECT id_post FROM `'._DB_PREFIX_.'ybc_blog_post` WHERE id_category_default=0');
    if($posts)
    {
        foreach($posts as $post)
        {
            $id_category = (int)Db::getInstance()->getValue('SELECT id_category FROM `'._DB_PREFIX_.'ybc_blog_category` WHERE id_post='.(int)$post['id_post']);
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ybc_blog_post` SET id_category_default="'.(int)$id_category.'" WHERE id_post="'.(int)$post['id_post'].'"');
        }
    }
    unset($object);
    return true;
}