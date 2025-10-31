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
 * @param Ybc_blog $object
 * @return bool
 */
function upgrade_module_3_0_1($object)
{
    if (!$object->isRegisteredInHook('blogArchivesBlock'))
        $object->registerHook('blogArchivesBlock');
    if (!$object->isRegisteredInHook('blogComments'))
        $object->registerHook('blogComments');
    if (!$object->isRegisteredInHook('blogPositiveAuthor'))
        $object->registerHook('blogPositiveAuthor');
    if (!$object->isRegisteredInHook('displayCustomerAccount'))
        $object->registerHook('displayCustomerAccount');
    if (!$object->isRegisteredInHook('displayMyAccountBlock'))
        $object->registerHook('displayMyAccountBlock');
    if (!$object->isRegisteredInHook('blogRssSideBar'))
        $object->registerHook('blogRssSideBar');
    $sqls=array();
    
    if(!Ybc_blog_defines::checkCreatedColumn('ybc_blog_post','is_customer'))
        $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ybc_blog_post` ADD COLUMN `is_customer` INT(1) DEFAULT NULL AFTER `added_by`';
    if(!Ybc_blog_defines::checkCreatedColumn('ybc_blog_employee','is_customer'))
        $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ybc_blog_employee` ADD COLUMN `is_customer` INT(1) DEFAULT NULL AFTER `name`';
    if(!Ybc_blog_defines::checkCreatedColumn('ybc_blog_comment','customer_reply'))
        $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ybc_blog_comment` ADD COLUMN `customer_reply` INT(1) DEFAULT NULL AFTER `replied_by`';
    if(!Ybc_blog_defines::checkCreatedColumn('ybc_blog_comment','viewed'))
        $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ybc_blog_comment` ADD COLUMN `viewed` INT(1) DEFAULT NULL AFTER `rating`';
    if(!Ybc_blog_defines::checkCreatedColumn('ybc_blog_post_category','position'))
        $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ybc_blog_post_category` ADD COLUMN `position` INT(1) DEFAULT NULL AFTER `id_category`';
    if(!Ybc_blog_defines::checkCreatedColumn('ybc_blog_employee','status'))
        $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ybc_blog_employee` ADD COLUMN `status` INT(1) DEFAULT NULL AFTER `avata`';
    if(!Ybc_blog_defines::checkCreatedColumn('ybc_blog_gallery','thumb'))
        $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ybc_blog_gallery` ADD COLUMN `thumb` varchar(222) DEFAULT NULL AFTER `image`';
    if(!Ybc_blog_defines::checkCreatedColumn('ybc_blog_category','thumb'))
        $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ybc_blog_category` ADD COLUMN `thumb` varchar(222) DEFAULT NULL AFTER `image`';
    if(!Ybc_blog_defines::checkCreatedColumn('ybc_blog_post_lang','meta_title'))
    {
        $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ybc_blog_post_lang` ADD COLUMN `meta_title` VARCHAR(1000) DEFAULT NULL AFTER `title`';
    }
    if(!Ybc_blog_defines::checkCreatedColumn('ybc_blog_category_lang','meta_title'))
    {
        $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ybc_blog_category_lang` ADD COLUMN `meta_title` VARCHAR(1000) DEFAULT NULL AFTER `title`';
    }
    if(!Ybc_blog_defines::checkCreatedColumn('ybc_blog_post_lang','url_alias'))
    {
        $query='ALTER TABLE `'._DB_PREFIX_.'ybc_blog_post_lang` ADD COLUMN `url_alias` VARCHAR(1000) DEFAULT NULL AFTER `title`';
        Db::getInstance()->execute($query);
        if(Ybc_blog_defines::checkCreatedColumn('ybc_blog_post','url_alias'))
        {
            $posts = Db::getInstance()->executeS('SELECT url_alias,id_post FROM `'._DB_PREFIX_.'ybc_blog_post`');
            if($posts)
            {
                foreach($posts as $post)
                {
                     Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ybc_blog_post_lang` SET url_alias="'.pSQL($post['url_alias']).'" WHERE id_post='.(int)$post['id_post']) ;
                }   
            } 
            Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ybc_blog_post` DROP `url_alias`');   
        }                
    }
    if(!Ybc_blog_defines::checkCreatedColumn('ybc_blog_category_lang','url_alias'))
    {
        $query='ALTER TABLE `'._DB_PREFIX_.'ybc_blog_category_lang` ADD COLUMN `url_alias` INT(1) DEFAULT NULL AFTER `title`';
        Db::getInstance()->execute($query);
        if(Ybc_blog_defines::checkCreatedColumn('ybc_blog_category','url_alias'))
        {
            $categories = Db::getInstance()->executeS('SELECT url_alias,id_category FROM `'._DB_PREFIX_.'ybc_blog_category`');
            if($categories)
            {
                foreach($categories as $category)
                {
                    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ybc_blog_category_lang` SET url_alias="'.pSQL($category['url_alias']).'" WHERE id_category='.(int)$category['id_category']);
                }
            }
            Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ybc_blog_category` DROP `url_alias`');
        }
    }
    if(!Ybc_blog_defines::checkCreatedColumn('ybc_blog_slide_lang','url'))
    {
        $query='ALTER TABLE `'._DB_PREFIX_.'ybc_blog_slide_lang` ADD COLUMN `url` INT(1) DEFAULT NULL AFTER `caption`';
        Db::getInstance()->execute($query);
        if(Ybc_blog_defines::checkCreatedColumn('ybc_blog_slide','url'))
        {
            $slides= Db::getInstance()->executeS('SELECT id_slide,url FROM `'._DB_PREFIX_.'ybc_blog_slide`');
            if($slides)
            {
                foreach($slides as $slide)
                {
                    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ybc_blog_slide_lang` SET url="'.pSQL($slide['url']).'" WHERE id_slide='.(int)$slide['id_slide']);
                }
            }
            Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ybc_blog_slide` DROP `url`');
        }
    }
    Configuration::updateValue('YBC_BLOG_HOME_PER_ROW',4);
    $sqls[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ybc_blog_log_view`(
    `ip` varchar(50) DEFAULT NULL,
    `id_post` INT(11) NOT NULL,
    `browser` varchar(70) DEFAULT NULL,
    `id_customer` INT (11) DEFAULT NULL,
    `datetime_added` datetime NOT NULL
    ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=UTF8';
    $sqls[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ybc_blog_log_like`(
    `ip` varchar(50) DEFAULT NULL,
    `id_post` INT(11) NOT NULL,
    `browser` varchar(70) DEFAULT NULL,
    `id_customer` INT (11) DEFAULT NULL,
    `datetime_added` datetime NOT NULL
    ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=UTF8';
    $sqls[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."ybc_blog_reply` (
      `id_reply` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `id_comment` int(11) DEFAULT NULL,
      `id_user` int(11) DEFAULT NULL,
      `name` varchar(5000) CHARACTER SET utf8 DEFAULT NULL,
      `email` varchar(5000) CHARACTER SET utf8 DEFAULT NULL,
      `reply` text CHARACTER SET utf8,
      `id_employee` int(11) DEFAULT NULL,
      `approved` INT(1),
      `datetime_added` datetime NOT NULL,
      `datetime_updated` datetime NOT NULL,
      PRIMARY KEY (`id_reply`)
    ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=UTF8";
    $sqls[] ="CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."ybc_blog_post_related_categories` ( 
        `id_post` INT(11) NOT NULL , 
        `id_category` INT(11) NOT NULL )
    ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=UTF8";
    $sqls[]="CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."ybc_blog_polls` ( 
    `id_polls` INT(11) NOT NULL AUTO_INCREMENT ,
    `id_user` INT(11) NOT NULL , 
    `name` VARCHAR(222) CHARACTER SET utf8 NOT NULL , 
    `email` VARCHAR(222) NOT NULL , 
    `id_post` INT(11) NOT NULL , 
    `polls` INT(1) NOT NULL , 
    `feedback` TEXT CHARACTER SET utf8 NOT NULL, 
    `dateadd` DATETIME NOT NULL ,
     PRIMARY KEY (`id_polls`)) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=UTF8";
    foreach($sqls as $sql)
    {
        Db::getInstance()->execute($sql);
    }
    return $object->_uninstallTabs() &&  $object->_installTabs() && $object->_installDefault();
    
}