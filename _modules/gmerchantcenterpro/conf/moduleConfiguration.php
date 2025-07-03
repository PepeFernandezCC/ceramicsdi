<?php
/**
 * Google Merchant Center Pro
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

namespace Gmerchantcenterpro\Configuration;
if (!defined('_PS_VERSION_')) {
    exit;
}
class moduleConfiguration
{
    const GMCP_MODULE_NAME = 'GMCP';
    const GMCP_MODULE_SET_NAME = 'gmerchantcenterpro';
    const GMCP_TABLE_PREFIX = 'gmcp';
    const GMCP_SUPPORT_ID = '20908';
    const GMCP_SUPPORT_BT = false;
    const GMCP_SUPPORT_URL = 'https://addons.prestashop.com/';
    const GMCP_PATH_TPL = _PS_MODULE_DIR_ . 'gmerchantcenterpro/views/templates/';
    const GMCP_SHOP_PATH_ROOT = _PS_ROOT_DIR_ . '/';
    const GMCP_PATH_CONF = _PS_MODULE_DIR_ . 'gmerchantcenterpro/conf/';
    const GMCP_PATH_SQL = _PS_MODULE_DIR_ . 'gmerchantcenterpro/sql/';
    const GMCP_LIB_DAO = _PS_MODULE_DIR_ . 'gmerchantcenterpro/lib/dao/';
    const GMCP_URL_JS = _MODULE_DIR_ . 'gmerchantcenterpro/views/js/';
    const GMCP_URL_JS_GCR = _MODULE_DIR_ . 'gmerchantcenterpro/views/js/gcr/';
    const GMCP_URL_CSS = _MODULE_DIR_ . 'gmerchantcenterpro/views/css/';
    const GMCP_MODULE_URL = _MODULE_DIR_ . 'gmerchantcenterpro/';
    const GMCP_URL_IMG = _MODULE_DIR_ . 'gmerchantcenterpro/views/img/';
    const GMCP_DEBUG = false;
    const GMCP_USE_JS = true;
    const GMCP_PARAM_CTRL_NAME = 'sController';
    const GMCP_ADMIN_CTRL = 'admin';
    const GMCP_CTRL_CRON = 'cron';
    const GMCP_CTRL_FLY = 'fly';
    const GMCP_REPORTING_DIR = _PS_MODULE_DIR_ . 'gmerchantcenterpro/reporting/';
    const GMCP_PATH_LIB_HOOK = _PS_MODULE_DIR_ . 'gmerchantcenterpro/lib/hook/';
    const GMCP_TPL_FRONT_PATH = 'front/';
    const GMCP_TPL_HOOK_PATH = 'hook/';
    const GMCP_PATH_LIB_INSTALL = _PS_MODULE_DIR_ . 'gmerchantcenterpro/lib/install/';
    const GMCP_INSTALL_SQL_FILE = 'install.sql';
    const GMCP_UNINSTALL_SQL_FILE = 'uninstall.sql';
    const GMCP_LOG_JAM_SQL = false;
    const GMCP_LOG_JAM_CONFIG = false;
    const GMCP_BT_FAQ_MAIN_URL = 'http://faq.businesstech.fr/';
    const GMCP_GOOGLE_TAXONOMY_URL = 'https://www.google.com/basepages/producttype/';
    const GMCP_FEED_TITLE_LENGTH = 150;
    const GMCP_IMG_LIMIT = 20;
    const GMCP_CUSTOM_LABEL_LIMIT = 5;
    const GMCP_TAG_LIST = [
        'material',
        'pattern',
        'agegroup',
        'gender',
        'adult',
        'sizeType',
        'sizeSystem',
        'energy',
        'energy_min',
        'energy_max',
        'shipping_label',
        'unit_pricing_measure',
        'base_unit_pricing_measure',
        'excluded_destination',
        'excluded_country',
        'agegroup_product',
        'gender_product',
        'adult_product',
    ];
    const GMCP_LABEL_LIST = [
        'cats' => 'category',
        'brands' => 'brand',
        'suppliers' => 'supplier',
        'dynamic_best_sale' => 'dynamic_best_sale',
        'dynamic_features' => 'dynamic_features',
        'dynamic_new_product' => 'dynamic_new_product',
        'price_range' => 'price_range',
    ];
    const GMCP_WEIGHT_UNIT = ['kg', 'lb', 'g', 'oz'];
    public static function getTaxonomies()
    {
        $available_taxonomies = ['en-US', 'en-GB', 'fr-FR', 'de-DE', 'it-IT', 'es-ES', 'zh-CN', 'ja-JP', 'pt-BR', 'cs-CZ', 'ru-RU', 'sv-SE', 'da-DK', 'no-NO', 'pl-PL', 'ar-SA'];
        sort($available_taxonomies);
        return $available_taxonomies;
    }
    const GMCP_AVAILABLE_COUNTRIES = [
        'en' => [
            'IE' => ['currency' => ['EUR'], 'taxonomy' => 'en-US'],
            'GB' => ['currency' => ['GBP', 'KES', 'NGN', 'PAB', 'PKR', 'DZD', 'AOA', 'BYN', 'KHR', 'XAF', 'XOF', 'ETB', 'GHS', 'JOD', 'KZT', 'KWD', 'LBP', 'MGA', 'MUR', 'MAD', 'MZN', 'MMK', 'NPR', 'NIO', 'OMR', 'PYG', 'PEN', 'RON', 'XOF', 'LKR', 'UGX', 'UYU', 'UZS', 'ZMW'], 'taxonomy' => 'en-US'],
            'US' => ['currency' => ['USD', 'KES', 'NGN', 'PAB', 'PKR', 'DZD', 'AOA', 'BYN', 'KHR', 'XAF', 'XOF', 'ETB', 'GHS', 'JOD', 'KZT', 'KWD', 'LBP', 'MGA', 'MUR', 'MAD', 'MZN', 'MMK', 'NPR', 'NIO', 'OMR', 'PYG', 'PEN', 'RON', 'XOF', 'LKR', 'UGX', 'UYU', 'UZS', 'ZMW'], 'taxonomy' => 'en-US'],
            'AU' => ['currency' => ['AUD'], 'taxonomy' => 'en-US'],
            'CA' => ['currency' => ['CAD'], 'taxonomy' => 'en-US'],
            'IN' => ['currency' => ['INR'], 'taxonomy' => 'en-US'],
            'CH' => ['currency' => ['CHF'], 'taxonomy' => 'en-US'],
            'BE' => ['currency' => ['EUR'], 'taxonomy' => 'en-US'],
            'DK' => ['currency' => ['DKK'], 'taxonomy' => 'en-US'],
            'NO' => ['currency' => ['NOK'], 'taxonomy' => 'en-US'],
            'MY' => ['currency' => ['MYR'], 'taxonomy' => 'en-US'],
            'ID' => ['currency' => ['RP'], 'taxonomy' => 'en-US'],
            'SE' => ['currency' => ['SEK'], 'taxonomy' => 'en-US'],
            'HK' => ['currency' => ['HKD'], 'taxonomy' => 'en-US'],
            'MX' => ['currency' => ['MXN'], 'taxonomy' => 'en-US'],
            'NZ' => ['currency' => ['NZD'], 'taxonomy' => 'en-US'],
            'PH' => ['currency' => ['PHP'], 'taxonomy' => 'en-US'],
            'SG' => ['currency' => ['SGD'], 'taxonomy' => 'en-US'],
            'TW' => ['currency' => ['TWD'], 'taxonomy' => 'en-US'],
            'AE' => ['currency' => ['AED', 'DZD', 'EGP', 'TND'], 'taxonomy' => 'en-US'],
            'DE' => ['currency' => ['EUR'], 'taxonomy' => 'en-US'],
            'AT' => ['currency' => ['EUR'], 'taxonomy' => 'en-US'],
            'NL' => ['currency' => ['EUR'], 'taxonomy' => 'en-US'],
            'TR' => ['currency' => ['TRY'], 'taxonomy' => 'en-US'],
            'ZA' => ['currency' => ['ZAR'], 'taxonomy' => 'en-US'],
            'CZ' => ['currency' => ['CZK'], 'taxonomy' => 'en-US'],
            'IL' => ['currency' => ['ILS'], 'taxonomy' => 'en-US'],
            'VN' => ['currency' => ['VND'], 'taxonomy' => 'en-US'],
            'TH' => ['currency' => ['THB'], 'taxonomy' => 'en-US'],
            'KO' => ['currency' => ['KRW'], 'taxonomy' => 'en-US'],
            'AR' => ['currency' => ['ARS', 'CRC', 'DOP', 'GTQ'], 'taxonomy' => 'en-US'],
            'BR' => ['currency' => ['BRL'], 'taxonomy' => 'en-US'],
            'CL' => ['currency' => ['CLP'], 'taxonomy' => 'en-US'],
            'CO' => ['currency' => ['COP'], 'taxonomy' => 'en-US'],
            'IT' => ['currency' => ['EUR'], 'taxonomy' => 'en-US'],
            'JP' => ['currency' => ['JPY'], 'taxonomy' => 'en-US'],
            'PL' => ['currency' => ['PLN'], 'taxonomy' => 'en-US'],
            'RU' => ['currency' => ['RUB', 'GEL'], 'taxonomy' => 'en-US'],
            'PT' => ['currency' => ['EUR'], 'taxonomy' => 'en-US'],
            'SA' => ['currency' => ['AED', 'SAR', 'DZD', 'EGP'], 'taxonomy' => 'en-US'],
            'ES' => ['currency' => ['EUR', 'GTQ'], 'taxonomy' => 'en-US'],
            'GE' => ['currency' => ['KAS'], 'taxonomy' => 'en-US'],
            'UR' => ['currency' => ['PKR'], 'taxonomy' => 'en-US'],
            'VE' => ['currency' => ['VES'], 'taxonomy' => 'en-US'],
            'SK' => ['currency' => ['EUR'], 'taxonomy' => 'en-US'],
            'HU' => ['currency' => ['HUF'], 'taxonomy' => 'en-US'],
            'KW' => ['currency' => ['KWD'], 'taxonomy' => 'en-US'],
        ],
        'gb' => [
            'AU' => ['currency' => ['AUD'], 'taxonomy' => 'en-GB'],
            'IE' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
            'IN' => ['currency' => ['INR'], 'taxonomy' => 'en-GB'],
            'CH' => ['currency' => ['CHF'], 'taxonomy' => 'en-GB'],
            'BE' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
            'DK' => ['currency' => ['DKK'], 'taxonomy' => 'en-GB'],
            'NO' => ['currency' => ['NOK'], 'taxonomy' => 'en-GB'],
            'MY' => ['currency' => ['MYR'], 'taxonomy' => 'en-GB'],
            'ID' => ['currency' => ['IDR'], 'taxonomy' => 'en-GB'],
            'SE' => ['currency' => ['SEK'], 'taxonomy' => 'en-GB'],
            'HK' => ['currency' => ['HKD'], 'taxonomy' => 'en-GB'],
            'MX' => ['currency' => ['MXN'], 'taxonomy' => 'en-GB'],
            'NZ' => ['currency' => ['NZD'], 'taxonomy' => 'en-GB'],
            'PH' => ['currency' => ['PHP'], 'taxonomy' => 'en-GB'],
            'SG' => ['currency' => ['SGD'], 'taxonomy' => 'en-GB'],
            'TW' => ['currency' => ['TWD'], 'taxonomy' => 'en-GB'],
            'SA' => ['currency' => ['AED, SAR', 'DZD', 'EGP', 'TND'], 'taxonomy' => 'en-GB'],
            'DE' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
            'AT' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
            'NL' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
            'TR' => ['currency' => ['TRY'], 'taxonomy' => 'en-GB'],
            'ZA' => ['currency' => ['ZAR'], 'taxonomy' => 'en-GB'],
            'CZ' => ['currency' => ['CZK'], 'taxonomy' => 'en-GB'],
            'IL' => ['currency' => ['ILS'], 'taxonomy' => 'en-GB'],
            'VN' => ['currency' => ['VND'], 'taxonomy' => 'en-GB'],
            'TH' => ['currency' => ['THB'], 'taxonomy' => 'en-GB'],
            'US' => ['currency' => ['USD'], 'taxonomy' => 'en-GB'],
            'GB' => ['currency' => ['GBP'], 'taxonomy' => 'en-GB'],
            'KO' => ['currency' => ['KRW'], 'taxonomy' => 'en-GB'],
            'AR' => ['currency' => ['ARS', 'CRC', 'DOP', 'GTQ'], 'taxonomy' => 'en-GB'],
            'BR' => ['currency' => ['BRL'], 'taxonomy' => 'en-GB'],
            'CL' => ['currency' => ['CLP'], 'taxonomy' => 'en-GB'],
            'CO' => ['currency' => ['COP'], 'taxonomy' => 'en-GB'],
            'IT' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
            'JP' => ['currency' => ['JPY'], 'taxonomy' => 'en-GB'],
            'PL' => ['currency' => ['PLN'], 'taxonomy' => 'en-GB'],
            'RU' => ['currency' => ['RUB', 'GEL'], 'taxonomy' => 'en-GB'],
            'PT' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
            'ES' => ['currency' => ['EUR', 'GTQ'], 'taxonomy' => 'en-GB'],
            'GE' => ['currency' => ['KAS'], 'taxonomy' => 'en-GB'],
            'UR' => ['currency' => ['PKR'], 'taxonomy' => 'en-GB'],
            'VE' => ['currency' => ['VES'], 'taxonomy' => 'en-GB'],
            'SK' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
            'HU' => ['currency' => ['HUF'], 'taxonomy' => 'en-GB'],
        ],
        'fr' => [
            'FR' => ['currency' => ['EUR', 'TND', 'DZD', 'XAF', 'XOF', 'MGA', 'MAD'], 'taxonomy' => 'fr-FR'],
            'CH' => ['currency' => ['CHF'], 'taxonomy' => 'fr-FR'],
            'CA' => ['currency' => ['CAD'], 'taxonomy' => 'fr-FR'],
            'BE' => ['currency' => ['EUR'], 'taxonomy' => 'fr-FR'],
            'SA' => ['currency' => ['DZD'], 'taxonomy' => 'fr-FR'],
        ],
        'de' => [
            'EN' => ['currency' => ['EUR'], 'taxonomy' => 'de-DE'],
            'DE' => ['currency' => ['EUR'], 'taxonomy' => 'de-DE'],
            'CH' => ['currency' => ['CHF'], 'taxonomy' => 'de-DE'],
            'AT' => ['currency' => ['EUR'], 'taxonomy' => 'de-DE'],
            'BE' => ['currency' => ['EUR'], 'taxonomy' => 'de-DE'],
        ],
        'it' => [
            'IT' => ['currency' => ['EUR'], 'taxonomy' => 'it-IT'],
            'CH' => ['currency' => ['CHF'], 'taxonomy' => 'it-IT'],
        ],
        'nl' => [
            'NL' => ['currency' => ['EUR'], 'taxonomy' => 'nl-NL'],
            'BE' => ['currency' => ['EUR'], 'taxonomy' => 'nl-NL'],
        ],
        'es' => [
            'ES' => ['currency' => ['EUR', 'MXN', 'ARS', 'CLP', 'COP', 'USD', 'CRC', 'GTQ', 'PYG', 'NIO', 'PEN', 'UYU'], 'taxonomy' => 'es-ES'],
            'MX' => ['currency' => ['MXN', 'EUR', 'ARS', 'CLP', 'COP', 'USD', 'CRC', 'GTQ', 'PYG', 'NIO', 'PEN', 'UYU'], 'taxonomy' => 'es-ES'],
            'AR' => ['currency' => ['ARS', 'EUR', 'MXN', 'CLP', 'COP', 'USD', 'CRC', 'GTQ', 'PYG', 'NIO', 'PEN', 'UYU'], 'taxonomy' => 'es-ES'],
            'CL' => ['currency' => ['CLP', 'EUR', 'MXN', 'ARS', 'COP', 'USD', 'CRC', 'GTQ', 'PYG', 'NIO', 'PEN', 'UYU'], 'taxonomy' => 'es-ES'],
            'CO' => ['currency' => ['COP', 'EUR', 'MXN', 'ARS', 'CLP', 'USD', 'CRC', 'GTQ', 'PYG', 'NIO', 'PEN', 'UYU'], 'taxonomy' => 'es-ES'],
            'US' => ['currency' => ['USD', 'EUR', 'MXN', 'ARS', 'CLP', 'COP', 'CRC', 'GTQ', 'PYG', 'NIO', 'PEN', 'UYU'], 'taxonomy' => 'es-ES'],
        ],
        'mx' => [
            'ES' => ['currency' => ['EUR', 'MXN', 'ARS', 'CLP', 'COP', 'USD'], 'taxonomy' => 'es-ES'],
            'MX' => ['currency' => ['EUR', 'MXN', 'ARS', 'CLP', 'COP'], 'taxonomy' => 'es-ES'],
            'AR' => ['currency' => ['ARS', 'EUR', 'MXN', 'CLP', 'COP', 'USD'], 'taxonomy' => 'es-ES'],
            'CL' => ['currency' => ['CLP', 'EUR', 'MXN', 'ARS', 'COP', 'USD'], 'taxonomy' => 'es-ES'],
            'CO' => ['currency' => ['COP', 'EUR', 'MXN', 'ARS', 'CLP', 'USD'], 'taxonomy' => 'es-ES'],
            'US' => ['currency' => ['USD', 'EUR', 'MXN', 'ARS', 'CLP', 'COP'], 'taxonomy' => 'es-ES'],
        ],
        'ca' => [
            'ES' => ['currency' => ['EUR'], 'taxonomy' => 'es-ES'],
        ],
        'zh' => [
            'CN' => ['currency' => ['CNY'], 'taxonomy' => 'zh-CN'],
            'EN' => ['currency' => ['CNY'], 'taxonomy' => 'zh-CN'],
            'HK' => ['currency' => ['HKD'], 'taxonomy' => 'zh-CN'],
            'TW' => ['currency' => ['TWD'], 'taxonomy' => 'zh-CN'],
            'AU' => ['currency' => ['AUD'], 'taxonomy' => 'zh-CN'],
            'CA' => ['currency' => ['CAD'], 'taxonomy' => 'zh-CN'],
            'US' => ['currency' => ['USD'], 'taxonomy' => 'zh-CN'],
            'SG' => ['currency' => ['SGD'], 'taxonomy' => 'zh-CN'],
        ],
        'ja' => [
            'JP' => ['currency' => ['JPY'], 'taxonomy' => 'ja-JP'],
        ],
        'br' => [
            'BR' => ['currency' => ['BRL'], 'taxonomy' => 'pt-BR'],
        ],
        'cs' => [
            'CZ' => ['currency' => ['CZK'], 'taxonomy' => 'cs-CZ'],
        ],
        'ru' => [
            'RU' => ['currency' => ['RUB', 'BYR', 'GEL', 'BYN', 'KZT', 'KWD', 'UZS'], 'taxonomy' => 'ru-RU'],
            'UA' => ['currency' => ['UAH'], 'taxonomy' => 'ru-RU'],
        ],
        'sv' => [
            'SE' => ['currency' => ['SEK'], 'taxonomy' => 'sv-SE'],
            'EN' => ['currency' => ['SEK'], 'taxonomy' => 'sv-SE'],
        ],
        'da' => [
            'DK' => ['currency' => ['DKK'], 'taxonomy' => 'da-DK'],
            'EN' => ['currency' => ['DKK'], 'taxonomy' => 'da-DK'],
        ],
        'no' => [
            'NO' => ['currency' => ['NOK'], 'taxonomy' => 'no-NO'],
        ],
        'pl' => [
            'PL' => ['currency' => ['PLN'], 'taxonomy' => 'pl-PL'],
        ],
        'tr' => [
            'TR' => ['currency' => ['TRY'], 'taxonomy' => 'tr-TR'],
        ],
        'ms' => [
            'MY' => ['currency' => ['MYR'], 'taxonomy' => 'en-US'],
        ],
        'pt' => [
            'PT' => ['currency' => ['EUR', 'AOA', 'MZN'], 'taxonomy' => 'es-ES'],
        ],
        'ar' => [
            'SA' => ['currency' => ['SAR', 'AED', 'DZD', 'CRC', 'EGP', 'TND', 'DZD', 'JOD', 'LBP', 'MAD', 'OMR'], 'taxonomy' => 'ar-SA'],
            'AE' => ['currency' => ['AED', 'SAR', 'DZD', 'EGP', 'DZD', 'JOD'], 'taxonomy' => 'ar-SA'],
            'KW' => ['currency' => ['KWD'], 'taxonomy' => 'ar-SA'],
        ],
        'id' => [
            'ID' => ['currency' => ['IDR'], 'taxonomy' => 'en-US'],
        ],
        'he' => [
            'IL' => ['currency' => ['ILS'], 'taxonomy' => 'en-US'],
        ],
        'vn' => [
            'VN' => ['currency' => ['VND'], 'taxonomy' => 'en-US'],
        ],
        'uk' => [
            'UA' => ['currency' => ['UAH'], 'taxonomy' => 'en-US'],
        ],
        'th' => [
            'TH' => ['currency' => ['THB'], 'taxonomy' => 'en-US'],
        ],
        'ko' => [
            'KO' => ['currency' => ['KRW'], 'taxonomy' => 'en-US'],
        ],
        'fi' => [
            'FI' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
        ],
        'hu' => [
            'HU' => ['currency' => ['HUF'], 'taxonomy' => 'en-GB'],
        ],
        'ag' => [
            'AR' => ['currency' => ['CRC', 'DOP', 'GTQ'], 'taxonomy' => 'es-ES'],
        ],
        'ur' => [
            'UR' => ['currency' => ['PKR'], 'taxonomy' => 'en-US'],
        ],
        've' => [
            'VE' => ['currency' => ['VES'], 'taxonomy' => 'es-ES'],
        ],
        'sk' => [
            'SK' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
        ],
        'ro' => [
            'RO' => ['currency' => ['RON'], 'taxonomy' => 'en-GB'],
        ],
        'el' => [
            'GR' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
        ],
        'lt' => [
            'LT' => ['currency' => ['EUR'], 'taxonomy' => 'en-GB'],
        ],
        'qc' => [
            'CA' => ['currency' => ['cAD'], 'taxonomy' => 'fr-FR'],
        ],
    ];
    const GMCP_WEIGHT_UNITS = ['kg', 'lb', 'g', 'oz'];
    const GMCP_DIMENSION_UNITS = ['cm', 'in'];
    const GMCP_HOOKS = [
        ['name' => 'displayHeader', 'use' => false, 'title' => 'Header'],
    ];
    const GMCP_TABS = [
        [
            'name' => [
                'en' => 'Taxonomie association',
            ],
            'class_name' => 'AdminGmcpTaxonomy',
            'icon' => 'settings_applications',
            'hide' => true,
            'parent' => '',
        ],
        [
            'name' => [
                'en' => 'Tag association',
            ],
            'class_name' => 'AdminGmcpTagProduct',
            'icon' => 'settings_applications',
            'hide' => true,
            'parent' => '',
        ],
    ];
    const GMCP_TAGS_TYPE = [
        'home' => 'home',
        'category' => 'category',
        'product' => 'product',
        'cart' => 'cart',
        'purchase' => 'purchase',
        'search' => 'searchresults',
        'other' => 'other',
        'manufacturer' => 'manufacturer',
        'promotion' => 'promotion',
        'newproducts' => 'newproducts',
        'bestsales' => 'bestsales',
        'paymentInfo' => 'paymentInfo',
        'instantSearch' => 'instantSearch',
        'productSub' => 'productSub',
        'checkout' => 'checkout',
    ];
    const GMCP_HOME_CAT_NAME = [
        'en' => 'home',
        'fr' => 'accueil',
        'it' => 'ignazio',
        'es' => 'ignacio',
    ];
    const GMCP_PARAM_FOR_XML = [
        'iShopId',
        'sFilename',
        'iLangId',
        'sLangIso',
        'sCountryIso',
        'sCurrencyIso',
        'iFloor',
        'iStep',
        'iTotal',
        'iProcess',
        'bExcludedProduct',
    ];
    public static function getRulesTypeLabel()
    {
        return [
            'supplier' => \GMerchantCenterPro::$oModule->l('one or several supplier(s)', 'moduleConfiguration'),
            'word' => \GMerchantCenterPro::$oModule->l('a word or sequence of words', 'moduleConfiguration'),
            'feature' => \GMerchantCenterPro::$oModule->l('a feature', 'moduleConfiguration'),
            'attribute' => \GMerchantCenterPro::$oModule->l('an attribute', 'moduleConfiguration'),
            'specificProduct' => \GMerchantCenterPro::$oModule->l('a specific product or combination', 'moduleConfiguration'),
        ];
    }
    public static function getRulesWordType()
    {
        return [
            'title' => \GMerchantCenterPro::$oModule->l('Product title', 'moduleConfiguration'),
            'description' => \GMerchantCenterPro::$oModule->l('Product description', 'moduleConfiguration'),
            'both' => \GMerchantCenterPro::$oModule->l('Product title or description', 'moduleConfiguration'),
        ];
    }
    public static function getExclusionType()
    {
        return [
            'word' => \GMerchantCenterPro::$oModule->l('A word or a sequence of words', 'moduleConfiguration'),
            'feature' => \GMerchantCenterPro::$oModule->l('A feature', 'moduleConfiguration'),
            'attribute' => \GMerchantCenterPro::$oModule->l('An attribute', 'moduleConfiguration'),
            'specificProduct' => \GMerchantCenterPro::$oModule->l('A specific product or combination', 'moduleConfiguration'),
        ];
    }
    public static function getCustomLabelName()
    {
        return [
            'custom_label' => \GMerchantCenterPro::$oModule->l('Basic', 'moduleConfiguration'),
            'dynamic_categorie' => \GMerchantCenterPro::$oModule->l('Categories (Dynamic mode)', 'moduleConfiguration'),
            'dynamic_features_list' => \GMerchantCenterPro::$oModule->l('Features (Dynamic mode)', 'moduleConfiguration'),
            'dynamic_new_product' => \GMerchantCenterPro::$oModule->l('New products (Dynamic mode)', 'moduleConfiguration'),
            'dynamic_best_sale' => \GMerchantCenterPro::$oModule->l('Best sales (Dynamic mode)', 'moduleConfiguration'),
            'dynamic_price_range' => \GMerchantCenterPro::$oModule->l('Price Range (Dynamic mode)', 'moduleConfiguration'),
            'dynamic_last_order' => \GMerchantCenterPro::$oModule->l('Last product ordered (Dynamic mode)', 'moduleConfiguration'),
            'dynamic_promotion' => \GMerchantCenterPro::$oModule->l('Product promotions (Dynamic mode)', 'moduleConfiguration'),
            'dynamic_not_sell' => \GMerchantCenterPro::$oModule->l('Products not sold (Dynamic mode)', 'moduleConfiguration'),
        ];
    }
    public static function getCustomLabelBestType()
    {
        return ['unit' => \GMerchantCenterPro::$oModule->l('Units sold', 'moduleConfiguration'), 'Items sold' => \GMerchantCenterPro::$oModule->l('Revenue generated', 'moduleConfiguration')];
    }
    public static function getCustomLabelBestPeriodType()
    {
        return ['period' => \GMerchantCenterPro::$oModule->l('Period', 'moduleConfiguration'), 'days' => \GMerchantCenterPro::$oModule->l('For X lasts days', 'moduleConfiguration')];
    }
    const GMCP_CUSTOM_LABEL_PRODUCT_FILTER = [
        'category' => [
            'sFieldSelect' => 'id_category',
            'sPopulateTable' => 'gmcp_tags_cats',
            'bUsePsTable' => 1,
            'bUseCategory' => 1,
            'sPsTable' => 'category_product',
            'sPsTableWhere' => 'id_category',
        ],
        'brand' => [
            'sFieldSelect' => 'id_brand',
            'sPopulateTable' => 'gmcp_tags_brands',
            'bUsePsTable' => 1,
            'sPsTable' => 'product',
            'sPsTableWhere' => 'id_manufacturer',
        ],
        'supplier' => [
            'sFieldSelect' => 'id_supplier',
            'sPopulateTable' => 'gmcp_tags_suppliers',
            'bUsePsTable' => 1,
            'sPsTable' => 'product',
            'sPsTableWhere' => 'id_manufacturer',
        ],
        'product' => [
            'sFieldSelect' => 'id_product',
            'sPopulateTable' => 'gmcp_tags_products',
            'bUsePsTable' => 0,
            'sPsTable' => '',
            'sPsTableWhere' => '',
        ],
        'dyn_cat' => [
            'sFieldSelect' => 'id_category',
            'sPopulateTable' => 'gmcp_tags_dynamic_categories',
            'bUsePsTable' => 1,
            'sPsTable' => 'category_product',
            'sPsTableWhere' => 'id_category',
        ],
        'dyn_feature' => [
            'sFieldSelect' => 'id_feature',
            'sPopulateTable' => 'gmcp_tags_dynamic_features',
            'bUsePsTable' => 1,
            'sPsTable' => 'feature_product',
            'sPsTableWhere' => 'id_feature',
        ],
        'dyn_new_product' => [
            'sFieldSelect' => 'id_product',
            'sPopulateTable' => 'gmcp_tags_dynamic_new_product',
            'bUsePsTable' => 0,
            'sPsTable' => '',
            'sPsTableWhere' => '',
        ],
        'dyn_best_dale' => [
            'sFieldSelect' => 'id_product',
            'sPopulateTable' => 'gmcp_tags_dynamic_best_sale',
            'bUsePsTable' => 0,
            'sPsTable' => '',
            'sPsTableWhere' => '',
        ],
        'dyn_price_range' => [
            'sFieldSelect' => 'id_product',
            'sPopulateTable' => 'gmcp_tags_price_range',
            'bUsePsTable' => 0,
            'sPsTable' => '',
            'sPsTableWhere' => '',
        ],
        'dyn_ordered' => [
            'sFieldSelect' => 'id_product',
            'sPopulateTable' => 'gmcp_tags_dynamic_last_product_ordered',
            'bUsePsTable' => 0,
            'sPsTable' => '',
            'sPsTableWhere' => '',
        ],
        'dyn_last_product' => [
            'sFieldSelect' => 'id_product',
            'sPopulateTable' => 'gmcp_tags_dynamic_last_product_not_ordered',
            'bUsePsTable' => 0,
            'sPsTable' => '',
            'sPsTableWhere' => '',
        ],
    ];
    const GMCP_DISCOUNT_CHANNEL = ['Shopping ads', 'Free listings'];
    const GMCP_DATA_FEED_TYPE = ['product', 'discount', 'reviews'];
    public static function getConfVar()
    {
        return [
            'GMCP_VERSION' => '',
            'GMCP_HOME_CAT' => '',
            'GMCP_LINK' => '',
            'GMCP_ID_PREFIX' => '',
            'GMCP_AJAX_CYCLE' => 1000,
            'GMCP_EXPORT_OOS' => 1,
            'GMCP_COND' => 'new',
            'GMCP_P_COMBOS' => 1,
            'GMCP_P_DESCR_TYPE' => 3,
            'GMCP_IMG_SIZE' => \ImageType::getFormattedName('large'),
            'GMCP_IMG_COVER_POSITION' => 1,
            'GMCP_EXC_NO_EAN' => 0,
            'GMCP_EXC_NO_MREF' => 0,
            'GMCP_MIN_PRICE' => 0,
            'GMCP_INC_STOCK' => 1,
            'GMCP_INC_FEAT' => 0,
            'GMCP_FEAT_OPT' => 0,
            'GMCP_INC_GENRE' => 0,
            'GMCP_GENRE_OPT' => 0,
            'GMCP_INC_SIZE' => 0,
            'GMCP_SIZE_OPT' => [],
            'GMCP_INC_COLOR' => '',
            'GMCP_COLOR_OPT' => [],
            'GMCP_INC_MATER' => 0,
            'GMCP_INC_ENERGY' => 0,
            'GMCP_EXCLUDED_DEST' => 0,
            'GMCP_INC_SHIPPING_LABEL' => 0,
            'GMCP_INC_UNIT_PRICING' => 0,
            'GMCP_INC_B_UNIT_PRICING' => 0,
            'GMCP_MATER_OPT' => 0,
            'GMCP_INC_PATT' => 0,
            'GMCP_PATT_OPT' => 0,
            'GMCP_INC_GEND' => 0,
            'GMCP_GEND_OPT' => 0,
            'GMCP_INC_ADULT' => 0,
            'GMCP_INC_COST' => 0,
            'GMCP_ADULT_OPT' => 0,
            'GMCP_INC_AGE' => 0,
            'GMCP_AGE_OPT' => 0,
            'GMCP_SHIP_CARRIERS' => '',
            'GMCP_REPORTING' => 1,
            'GMCP_HOME_CAT_ID' => 1,
            'GMCP_MPN_TYPE' => 'supplier_ref',
            'GMCP_INC_ID_EXISTS' => 0,
            'GMCP_ADD_CURRENCY' => 0,
            'GMCP_UTM_CAMPAIGN' => '',
            'GMCP_UTM_SOURCE' => '',
            'GMCP_UTM_MEDIUM' => '',
            'GMCP_UTM_CONTENT' => 0,
            'GMCP_FEED_PROTECTION' => 1,
            'GMCP_FEED_TOKEN' => md5(rand(1000, 1000000) . time()),
            'GMCP_EXPORT_MODE' => 0,
            'GMCP_ADV_PRODUCT_NAME' => 0,
            'GMCP_ADV_PROD_TITLE' => 0,
            'GMCP_CHECK_EXPORT' => '',
            'GMCP_FEED_TAX' => '',
            'GMCP_INC_TAG_ADULT' => 0,
            'GMCP_SHIPPING_USE' => 1,
            'GMCP_DSC_FILT_NAME' => 0,
            'GMCP_DSC_FILT_DATE' => 0,
            'GMCP_DSC_FILT_MIN_AMOUNT' => 0,
            'GMCP_DSC_FILT_VALUE' => 0,
            'GMCP_DSC_FILT_TYPE' => 0,
            'GMCP_DSC_FILT_CUMU' => 0,
            'GMCP_DSC_FILT_FOR' => 0,
            'GMCP_DSC_NAME' => '',
            'GMCP_DSC_DATE_FROM' => '',
            'GMCP_DSC_DATE_TO' => '',
            'GMCP_DSC_MIN_AMOUNT' => '',
            'GMCP_DSC_VALUE_MIN' => 0,
            'GMCP_DSC_VALUE_MAX' => 0,
            'GMCP_DSC_TYPE' => 0,
            'GMCP_DSC_CUMULABLE' => 0,
            'GMCP_INV_PRICE' => 0,
            'GMCP_INV_STOCK' => 0,
            'GMCP_INV_SALE_PRICE' => 0,
            'GMCP_CL_TYPE' => 'Manual',
            'GMCP_IMPORT_FROM_GMC' => 1,
            'GMCP_PROD_EXCL' => '',
            'GMCP_GTIN_PREF' => 'ean',
            'GMCP_SIZE_TYPE' => '',
            'GMCP_SIZE_SYSTEM' => '',
            'GMCP_FREE_SHIP_PROD' => '',
            'GMCP_PAUSED_PROD' => '',
            'GMCP_TAG_PAUSE_VALUE' => 0,
            'GMCP_URL_ATTR_ID_INCL' => 0,
            'GMCP_HANDLE_BACK_ORDER' => 0,
            'GMCP_URL_NUM_ATTR_REWRITE' => 0,
            'GMCP_INCL_ATTR_VALUE' => 1,
            'GMCP_MAX_WEIGHT' => 0,
            'GMCP_P_TITLE' => 'title',
            'GMCP_ADV_PROD_NAME_PREFIX' => [],
            'GMCP_ADV_PROD_NAME_SUFFIX' => [],
            'GMCP_FORBIDDEN_WORDS' => '',
            'GMCP_EXPORT_PROD_OOS_ORDER' => 0,
            'GMCP_ADD_IMAGES' => 1,
            'GMCP_CONF_STEP_1' => 0,
            'GMCP_CONF_STEP_2' => 0,
            'GMCP_CONF_STEP_3' => 0,
            'GMCP_SIMPLE_PROD_ID' => 0,
            'GMCP_FORCE_IDENTIFIER' => 0,
            'GMCP_API_KEY' => '',
            'GMCP_OAUTH' => [],
            'GMCP_SHOP_LINK_API' => 0,
            'GMCP_EXCLUDED_COUNTRY' => '',
            'GMCP_PROMO_DEST' => '',
            'GMCP_COMBO_SEPARATOR' => 'v',
            'GMCP_MIN_HANDLING_TIME' => '2',
            'GMCP_MAX_HANDLING_TIME' => '4',
            'GMCP_FUNDED_PROMO' => 'none',
            'GMCP_DIMENSION' => 0,
            'GMCP_STORE_CODE' => '',
            'GMCP_LIA_PICKUP' => 'reserve',
            'GMCP_LIA_PICKUP_SLA' => 'same day',
            'GMCP_PRODUCT_DIMENSION' => false,
            'GMCP_SHIPS_FROM' => '',
            'GMCP_FREE_SHIPPING_PRICE' => 0,
            'GMCP_EXCLUDED_WORDS' => '',
            'GMCP_INCL_ANCHOR' => 0,
            'GMCP_FREE_PROD_PRICE_SHIP_CARRIERS' => '',
            'GMCP_NO_TAX_SHIP_CARRIERS' => '',
            'GMCP_FREE_SHIP_CARRIERS' => '',
            'GMCP_USE_GENDER_PRODUCT' => 0,
            'GMCP_USE_AGEGROUP_PRODUCT' => 0,
            'GMCP_USE_ADULT_PRODUCT' => 0,
            'GMCP_FEED_PREF_ID' => 'tag-id-basic',
            'GMCP_MERCHANT_ID' => '',
            'GMCP_SHIPPING_PROCESS' => 1,
            'GMCP_SAME_DAY_PROCESS' => false,
            'GMCP_CUT_OFF_HOUR' => '',
            'GMCP_CUT_OFF_MIN' => '',
            'GMCP_CLOSED_DAY' => [],
            'GMCP_HOLIDAYS' => [],
            'GMCP_SHIP_TIME' => [],
            'GMCP_ORDER_STATE' => [],
            'GMCP_GCR_BADGE' => false,
            'GMCP_GCR_ACTIVATE' => false,
            'GMCP_GCR_PRODUCT_GTIN' => false,
            'GMCP_HANDLE_TAXO_JSON' => 0,
            'GMCP_CL_USE_DEFAULT_CAT' => 1,
            'GMCP_CL_MODE_OR_AND' => 1,
            'GMCP_USE_GEOLOC' => 0,
        ];
    }
    public static function getJsMessage()
    {
        return [
            'link' => \GMerchantCenterPro::$oModule->l('You have not filled in the shop URL', 'moduleConfiguration'),
            'token' => \GMerchantCenterPro::$oModule->l('Field is required or token must be 32 characters', 'moduleConfiguration'),
            'customlabel' => \GMerchantCenterPro::$oModule->l('You have not indicated a name for your custom label', 'moduleConfiguration'),
            'dateNewProduct' => \GMerchantCenterPro::$oModule->l('You have not indicated a date for new product management', 'moduleConfiguration'),
            'amount' => \GMerchantCenterPro::$oModule->l('You have not indicated an amount for best-seller determination', 'moduleConfiguration'),
            'category' => \GMerchantCenterPro::$oModule->l('You have not selected any category to be exported', 'moduleConfiguration'),
            'brand' => \GMerchantCenterPro::$oModule->l('You have not selected any any brand to be exported', 'moduleConfiguration'),
            'color' => \GMerchantCenterPro::$oModule->l('You have not selected any attribute or feature to be associated with the color tag', 'moduleConfiguration'),
            'voucher_amount' => \GMerchantCenterPro::$oModule->l('Please enter a cart rule name', 'moduleConfiguration'),
            'voucher_date_from' => \GMerchantCenterPro::$oModule->l('Please enter a start date', 'moduleConfiguration'),
            'voucher_date_to' => \GMerchantCenterPro::$oModule->l('Please enter a end date', 'moduleConfiguration'),
            'voucher_min_amount' => \GMerchantCenterPro::$oModule->l('Please enter a minimum purchase amount required', 'moduleConfiguration'),
            'voucher_amount_min' => \GMerchantCenterPro::$oModule->l('Please enter a minimum value', 'moduleConfiguration'),
            'voucher_amount_max' => \GMerchantCenterPro::$oModule->l('Please enter a maximum value', 'moduleConfiguration'),
            'cl_feature_message' => \GMerchantCenterPro::$oModule->l('You have not indicated the feature to be used', 'moduleConfiguration'),
            'merchantId' => \GMerchantCenterPro::$oModule->l('You have not filled in your Merchant Center ID', 'moduleConfiguration'),
            'homecat' => \GMerchantCenterPro::$oModule->l('Please fill in the fields for all your store\'s languages', 'moduleConfiguration'),
            'coverPosition' => \GMerchantCenterPro::$oModule->l('Please enter a value for the position of the image you want to use as the cover image', 'moduleConfiguration'),
            'merchantCenterId' => \GMerchantCenterPro::$oModule->l('Your Google Merchant Center ID is required', 'moduleConfiguration'),
            'hourFormat' => \GMerchantCenterPro::$oModule->l('The hour field is required and need beeing between 0 to 24', 'moduleConfiguration'),
            'minuteFormat' => \GMerchantCenterPro::$oModule->l('The minute field is required and need beeing between 0 to 59', 'moduleConfiguration'),
            'processTime' => \GMerchantCenterPro::$oModule->l('The process time is required', 'moduleConfiguration'),
        ];
    }
    public static function getMonths()
    {
        return [
            'en' => [
                'short' => ['', 'Jan.', 'Feb.', 'March', 'Apr.', 'May', 'June', 'July', 'Aug.', 'Sept.', 'Oct.', 'Nov.', 'Dec.'],
                'long' => ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            ],
            'fr' => [
                'short' => ['', 'Jan.', 'F&eacute;v.', 'Mars', 'Avr.', 'Mai', 'Juin', 'Juil.', 'Aout', 'Sept.', 'Oct.', 'Nov.', 'D&eacute;c.'],
                'long' => ['', 'Janvier', 'F&eacute;vrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'D&eacute;cembre'],
            ],
            'de' => [
                'short' => ['', 'Jan.', 'Feb.', 'M' . chr(132) . 'rz', 'Apr.', 'Mai', 'Juni', 'Juli', 'Aug.', 'Sept.', 'Okt.', 'Nov.', 'Dez.'],
                'long' => ['', 'Januar', 'Februar', 'M' . chr(132) . 'rz', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
            ],
            'it' => [
                'short' => ['', 'Gen.', 'Feb.', 'Marzo', 'Apr.', 'Mag.', 'Giu.', 'Lug.', 'Ago.', 'Sett.', 'Ott.', 'Nov.', 'Dic.'],
                'long' => ['', 'Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'],
            ],
            'es' => [
                'short' => ['', 'Ene.', 'Feb.', 'Marzo', 'Abr.', 'Mayo', 'Junio', 'Jul.', 'Ago.', 'Sept.', 'Oct.', 'Nov.', 'Dic.'],
                'long' => ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            ],
        ];
    }
    public static function getSqlUpdateData()
    {
        return [
            'table' => [
                '1800' => 'update-1800.sql',
                '1900' => 'update-1900.sql',
            ],
            'field' => [
                'feed_is_default' => ['table' => 'feeds', 'file' => 'update-gmcp-feed-is-default.sql'],
                'channel' => ['table' => 'discount_association', 'file' => 'update-channel.sql'],
                'custom_label_set_postion' => ['table' => 'tags', 'file' => 'update-label-position.sql'],
            ],
        ];
    }
    public static function getRequestParams()
    {
        return [
            'basic' => ['action' => 'update', 'type' => 'basic'],
            'feed' => ['action' => 'update', 'type' => 'feed'],
            'feedDisplay' => ['action' => 'display', 'type' => 'feed'],
            'google' => ['action' => 'update', 'type' => 'google'],
            'feedList' => ['action' => 'display', 'type' => 'feedList'],
            'feedListUpdate' => ['action' => 'update', 'type' => 'feedList'],
            'reporting' => ['action' => 'update', 'type' => 'reporting'],
            'reportingBox' => ['action' => 'display', 'type' => 'reportingBox'],
            'tag' => ['action' => 'display', 'type' => 'tag'],
            'tagUpdate' => ['action' => 'update', 'type' => 'tag'],
            'googleCatSync' => ['action' => 'update', 'type' => 'googleCategoriesSync'],
            'custom' => ['action' => 'display', 'type' => 'customLabel'],
            'customUpdate' => ['action' => 'update', 'type' => 'label'],
            'customDelete' => ['action' => 'delete', 'type' => 'label'],
            'customActivate' => ['action' => 'update', 'type' => 'labelState'],
            'autocomplete' => ['action' => 'display', 'type' => 'autocomplete'],
            'dataFeed' => ['action' => 'update', 'type' => 'xml'],
            'advancedfeed' => ['action' => 'update', 'type' => 'advancedfeed'],
            'discount' => ['action' => 'update', 'type' => 'discount'],
            'position' => ['action' => 'update', 'type' => 'position'],
            'checkDate' => ['action' => 'update', 'type' => 'customLabelDate'],
            'customProduct' => ['action' => 'display', 'type' => 'customLabelProduct'],
            'searchProduct' => ['action' => 'display', 'type' => 'searchProduct'],
            'searchSimpleProduct' => ['action' => 'display', 'type' => 'searchSimpleProduct'],
            'exclusionRule' => ['action' => 'display', 'type' => 'exclusionRule'],
            'exclusionRuleDelete' => ['action' => 'delete', 'type' => 'exclusionRule'],
            'rulesSummary' => ['action' => 'display', 'type' => 'rulesSummary'],
            'rulesList' => ['action' => 'update', 'type' => 'rulesList'],
            'exclusionRuleForm' => ['action' => 'update', 'type' => 'exclusionRule'],
            'excludeValue' => ['action' => 'display', 'type' => 'excludeValue'],
            'rulesActivate' => ['action' => 'update', 'type' => 'rulesActivate'],
            'exclusionRuleProducts' => ['action' => 'display', 'type' => 'exclusionRuleProducts'],
            'stepPopup' => ['action' => 'display', 'type' => 'stepPopup'],
            'stepPopupUpd' => ['action' => 'update', 'type' => 'stepPopup'],
            'shopLink' => ['action' => 'update', 'type' => 'shopLink'],
            'local_inventory' => ['action' => 'update', 'type' => 'inventory'],
            'newFeed' => ['action' => 'update', 'type' => 'newFeed'],
            'taxonomy' => ['action' => 'display', 'type' => 'taxonomy'],
            'deleteFeed' => ['action' => 'delete', 'type' => 'feed'],
            'googleCustomerReviews' => ['action' => 'update', 'type' => 'googleCustomerReviews'],
        ];
    }
    const GMCP_EXCLUDED_DEST_VALUE = [
        'shopping' => 'Shopping Ads',
        'display' => 'Display Ads',
        'local' => 'Local inventory ads',
        'free-listing' => 'Free listings',
        'free-local-listing' => 'Free local listings',
    ];
    const GMCP_LANG_TO_REMOVED_OFFERID = [
        'FR', 'EN', 'US', 'GB', 'DE', 'IT', 'NL', 'ES', 'MX', 'ZA', 'CA', 'JA',
        'BR', 'CR', 'RU', 'SV', 'DA', 'NO', 'PL', 'TR', 'MS', 'PT', 'AR', 'ID',
        'HE', 'VN', 'UK', 'SV', 'TH', 'KO', 'FI', 'HU', 'AG', 'UR', 'VE', 'SK', 'RO', 'EI', 'LT',
    ];
    public static function getWeekDays()
    {
        return [
            1 => \GMerchantCenterPro::$oModule->l('Monday', 'moduleConfiguration'),
            2 => \GMerchantCenterPro::$oModule->l('Tuesday', 'moduleConfiguration'),
            3 => \GMerchantCenterPro::$oModule->l('Wednesday', 'moduleConfiguration'),
            4 => \GMerchantCenterPro::$oModule->l('Thursday', 'moduleConfiguration'),
            5 => \GMerchantCenterPro::$oModule->l('Friday', 'moduleConfiguration'),
            6 => \GMerchantCenterPro::$oModule->l('Saturday', 'moduleConfiguration'),
            0 => \GMerchantCenterPro::$oModule->l('Sunday', 'moduleConfiguration'),
        ];
    }
    public static function getHolidays()
    {
        return [
            1 => ['name' => \GMerchantCenterPro::$oModule->l('January', 'moduleConfiguration'), 'nbDays' => 31, 'month_number' => '01'],
            2 => ['name' => \GMerchantCenterPro::$oModule->l('February', 'moduleConfiguration'), 'nbDays' => 29, 'month_number' => '02'],
            3 => ['name' => \GMerchantCenterPro::$oModule->l('March', 'moduleConfiguration'), 'nbDays' => 31, 'month_number' => '03'],
            4 => ['name' => \GMerchantCenterPro::$oModule->l('April', 'moduleConfiguration'), 'nbDays' => 30, 'month_number' => '04'],
            5 => ['name' => \GMerchantCenterPro::$oModule->l('May', 'moduleConfiguration'), 'nbDays' => 31, 'month_number' => '05'],
            6 => ['name' => \GMerchantCenterPro::$oModule->l('June', 'moduleConfiguration'), 'nbDays' => 30, 'month_number' => '06'],
            7 => ['name' => \GMerchantCenterPro::$oModule->l('July', 'moduleConfiguration'), 'nbDays' => 31, 'month_number' => '07'],
            8 => ['name' => \GMerchantCenterPro::$oModule->l('August', 'moduleConfiguration'), 'nbDays' => 31, 'month_number' => '08'],
            9 => ['name' => \GMerchantCenterPro::$oModule->l('September', 'moduleConfiguration'), 'nbDays' => 30, 'month_number' => '09'],
            10 => ['name' => \GMerchantCenterPro::$oModule->l('October', 'moduleConfiguration'), 'nbDays' => 31, 'month_number' => '10'],
            11 => ['name' => \GMerchantCenterPro::$oModule->l('November', 'moduleConfiguration'), 'nbDays' => 30, 'month_number' => '11'],
            12 => ['name' => \GMerchantCenterPro::$oModule->l('December', 'moduleConfiguration'), 'nbDays' => 31, 'month_number' => '12'],
        ];
    }
    const GMCP_FUNDED_PROMO = ['none', 'all'];
    const GMCP_FORBIDDEN_STRING = [
        'special_symbol_1' => [
            'sToReplace' => '&',
            'sReplaceBy' => '',
        ],
        'special_symbol_2' => [
            'sToReplace' => '!',
            'sReplaceBy' => '',
        ],
        'special_symbol_3' => [
            'sToReplace' => '***',
            'sReplaceBy' => '',
        ],
    ];
    public static function getCustomLabelPosition()
    {
        return [
            'custom_label_0',
            'custom_label_1',
            'custom_label_2',
            'custom_label_3',
            'custom_label_4',
        ];
    }
}
