<?php
/**
 * UrlRedirects
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    FMM Modules
 * @copyright Copyright 2023 © FMM Modules
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  FMM Modules
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class Manage404Report extends ObjectModel
{
    public $id_report;
    public $url_not_found;
    public $id_shop;
    public static $definition = [
        'table' => 'report404',
        'primary' => 'id_report',
        'fields' => [
            'url_not_found' => ['type' => self::TYPE_STRING, 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT, 'required' => true],
        ],
    ];

    public static function isExists($url_404)
    {
        $id_shop = (int) Context::getContext()->shop->id;

        return (bool) Db::getInstance()->executeS('
		SELECT * FROM `' . _DB_PREFIX_ . 'report404` r
		WHERE r.`url_not_found` = "' . pSQL($url_404) . '"
		AND r.`id_shop` = ' . (int) $id_shop);
    }
}
