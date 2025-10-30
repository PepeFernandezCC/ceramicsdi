<?php
/**
 * PrettyURLs
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 *  @author    FME Modules
 *  @copyright 2024 fmemodules All right reserved
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 *  @category  FMM Modules
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_0_0($module)
{
    Configuration::updateValue('FMM_PRETTY_ATTR_ID', true);
    Configuration::updateValue('FMM_PRETTY_ID', true);
    Configuration::updateValue('FMM_PRETTY_REDIRECT_PRD_URLS', true);
    Configuration::updateValue('FMM_PRETTY_ID_CATEGORY', true);
    Configuration::updateValue('FMM_PRETTY_REDIRECT_CATEGORY_URLS', true);
    Configuration::updateValue('FMM_PRETTY_ID_CMS', true);
	Configuration::updateValue('FMM_PRETTY_REDIRECT_CMS_URLS', true);
    Configuration::updateValue('FMM_PRETTY_ID_CMS_CAT', true);
	Configuration::updateValue('FMM_PRETTY_REDIRECT_CMS_CAT_URLS', true);
    Configuration::updateValue('FMM_PRETTY_ID_BRANDS', true);
	Configuration::updateValue('FMM_PRETTY_REDIRECT_BRANDS_URLS', true);
    Configuration::updateValue('FMM_PRETTY_ID_SUPPLIER', true);
	Configuration::updateValue('FMM_PRETTY_REDIRECT_SUPPLIER_URLS', true);
    $module->registerHook('displayTwitterSummaryCard');
    $module->registerHook('displayHeader');
    $module->registerHook('top');
    $module->initTokenSecure();
    $module->uninstallTabOldMod();
    $module->installTab();
    $sql = [];
    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'redirecturl`(
				`id_redirecturl`	int(10) NOT NULL auto_increment,
				`redirect_type`		int(10) NOT NULL,
				`old_uri`			varchar(255) NOT NULL,
				`new_url`			varchar(500) NOT NULL,
				PRIMARY KEY			(`id_redirecturl`))';

    $sql[] = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'redirecturl_shop(
					`id_redirecturl` int(11) NOT NULL,
					`id_shop` int(11) NOT NULL,
					PRIMARY KEY  (`id_redirecturl`, `id_shop`),
					KEY `id_shop` (`id_shop`)
                   ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';

    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'report404`(
				`id_report`	int(10) NOT NULL auto_increment,
				`url_not_found`		varchar(255) NOT NULL,
				`id_shop`		int(10) NOT NULL,
				PRIMARY KEY			(`id_report`))';

    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fmm_404_pages`(
			`id_fmm_404_pages`	int(10) NOT NULL auto_increment,
			`redirect_type`		varchar(20),
			`id_shop` int(11) NOT NULL,
			`id_shop_group` int(11) NOT NULL,
			`broken_url`			varchar(255) NOT NULL,
			`redirection_url`	    varchar(500) NOT NULL,
			PRIMARY KEY			(`id_fmm_404_pages`))';
    foreach ($sql as $query) {
        if (Db::getInstance()->execute($query) == false) {
            return false;
        }
    }
    return true;
}
