<?php
/**
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    FME Modules
*  @copyright © 2024 FME Modules
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

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
