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

$sql = array();

$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'redirecturl`';

$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'redirecturl_shop`';

$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'report404`';

$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'fmm_404_pages`';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
