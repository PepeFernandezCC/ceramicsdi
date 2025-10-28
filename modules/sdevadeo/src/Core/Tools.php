<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from ScaleDEV.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from ScaleDEV is strictly forbidden.
 * In order to obtain a license, please contact us: contact@scaledev.fr
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise à une licence commerciale
 * concédée par la société ScaleDEV.
 * Toute utilisation, reproduction, modification ou distribution du présent
 * fichier source sans contrat de licence écrit de la part de ScaleDEV est
 * expressément interdite.
 * Pour obtenir une licence, veuillez nous contacter : contact@scaledev.fr
 * ...........................................................................
 * @author ScaleDEV <contact@scaledev.fr>
 * @copyright Copyright (c) ScaleDEV - 12 RUE CHARLES MORET - 10120 SAINT-ANDRE-LES-VERGERS - FRANCE
 * @license Commercial license
 * @package Scaledev\Adeo
 * Support: support@scaledev.fr
 */

namespace Scaledev\Adeo\Core;

use Context;
use Db;
use DbQuery;
use Exception;
use Language;
use Product;
use ReflectionClass;
use Scaledev\Adeo\Component\Configuration;
use Scaledev\MiraklPhpConnector\Request\Offer\GetOfferExportErrorReportRequest;
use Scaledev\MiraklPhpConnector\Request\Offer\GetOfferExportInformationRequest;
use Scaledev\MiraklPhpConnector\Request\Product\GetProductExportErrorReportRequest;
use Scaledev\MiraklPhpConnector\Request\Product\GetProductExportInformationRequest;
use Scaledev\MiraklPhpConnector\Request\Product\GetProductExportReportRequest;
use Scaledev\MiraklPhpConnector\Response\Offer\GetOfferExportErrorReportResponse;
use Scaledev\MiraklPhpConnector\Response\Offer\GetOfferExportInformationResponse;
use Scaledev\MiraklPhpConnector\Response\Product\GetProductExportErrorReportResponse;
use Scaledev\MiraklPhpConnector\Response\Product\GetProductExportInformationResponse;
use Scaledev\MiraklPhpConnector\Response\Product\GetProductExportReportResponse;
use SdevAdeoCategoryRule;
use Shop;
use SplFileObject;
use Validate;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class Tools
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
class Tools extends \ToolsCore
{
    /**
     * The IP address to use for debug on IP filtering.
     */
    const IP_FILTER = null;

    /**
     * Module's name for file regex
     */
    const NAME = 'SdevAdeo';

    /**
     * Encode a value to the JSON format.
     *
     * @param array $data Data to encode to JSON format.
     * @param int $flags Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS,
     * JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, JSON_UNESCAPED_UNICODE.
     * JSON_THROW_ON_ERROR The behaviour of these constants is described on the JSON constants page.
     * @param int $depth Set the maximum depth. Must be greater than zero.
     * @return false|string A JSON encoded string or FALSE on failure.
     */
    public static function jsonEncode($data, $flags = 0, $depth = 512)
    {
        if (self::version_compare(_PS_VERSION_, '1.7', '<')) {
            return parent::jsonEncode($data, $flags, $depth);
        }

        return json_encode($data, $flags, $depth);
    }

    /**
     * Decode a JSON string.
     *
     * @param string $json The JSON string to decode.
     * @param bool $toArray Define whether to get an associative array instead of an object.
     * @param int $depth User specified recursion depth.
     * @param int $flags Bitmask of JSON decode options: JSON_BIGINT_AS_STRING decodes large integers as their original
     * string value. JSON_INVALID_UTF8_IGNORE ignores invalid UTF-8 characters, JSON_INVALID_UTF8_SUBSTITUTE converts
     * invalid UTF-8 characters to \0xfffd, JSON_OBJECT_AS_ARRAY decodes JSON objects as PHP array, since 7.2.0 used by
     * default if $assoc parameter is null, JSON_THROW_ON_ERROR when passed this flag, the error behaviour of these
     * functions is changed. The global error state is left untouched, and if an error occurs that would otherwise set
     * it, these functions instead throw a JsonException.
     * @return array|mixed The value encoded in JSON in appropriate PHP type. Values true, false and null
     * (case-insensitive) are returned as TRUE, FALSE and NULL respectively.
     * NULL is returned if the json cannot be decoded or if the encoded data is deeper than the recursion limit.
     */
    public static function jsonDecode($json, $toArray = false, $depth = 512, $flags = 0)
    {
        if (self::version_compare(_PS_VERSION_, '1.7', '<')) {
            return parent::jsonDecode($json, (bool)$toArray, $depth, $flags);
        }

        return json_decode($json, (bool)$toArray, $depth, $flags);
    }

    /**
     * Define if Windows is used.
     *
     * @return bool
     */
    public static function isWindowsUsed()
    {
        return self::strtoupper(self::substr(PHP_OS, 0, 3)) === 'WIN';
    }

    /**
     * Get a request as an array.
     *
     * @return array|mixed
     */
    public static function getRequest()
    {
        if (
            $request = self::jsonDecode(
                self::file_get_contents('php://input'),
                true
            )
        ) {
            return $request;
        }

        if (array_key_exists('params', $_POST)) {
            return $_POST['params'];
        }

        return array();
    }

    /**
     * Get the shop's domain.
     *
     * @param bool $withProtocol Define whether to get the shop's domain with
     * the protocol or not.
     * @param bool $withBaseUri Define whether to get the shop's domain with the
     * base URI or not.
     * @return string
     */
    public static function getShopDomain($withProtocol = true, $withBaseUri = false)
    {
        return ((self::usingSecureMode() && \Configuration::get('PS_SSL_ENABLED_EVERYWHERE'))
            ? self::getShopDomainSsl((bool)$withProtocol)
            : parent::getShopDomain((bool)$withProtocol)
        ).($withBaseUri ? __PS_BASE_URI__ : null);
    }

    /**
     * Get the IP address.
     *
     * @return string The IP address.
     */
    public static function getIpAddress()
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
            && preg_match_all(
                '#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s',
                $_SERVER['HTTP_X_FORWARDED_FOR'],
                $matches
            )
        ) {
            foreach ($matches[0] as $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])
            && preg_match(
                '/^([0-9]{1,3}\.){3}[0-9]{1,3}$/',
                $_SERVER['HTTP_CLIENT_IP']
            )
        ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP'])
            && preg_match(
                '/^([0-9]{1,3}\.){3}[0-9]{1,3}$/',
                $_SERVER['HTTP_CF_CONNECTING_IP']
            )
        ) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (isset($_SERVER['HTTP_X_REAL_IP'])
            && preg_match(
                '/^([0-9]{1,3}\.){3}[0-9]{1,3}$/',
                $_SERVER['HTTP_X_REAL_IP']
            )
        ) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        }

        return $ip;
    }

    /**
     * Define whether is an IP filtering.
     *
     * @return bool
     */
    public static function hasIpFilter()
    {
        return (self::IP_FILTER && (self::IP_FILTER == self::getIpAddress()));
    }

    /**
     * Debug a variable.
     *
     * @param mixed $var The variable to debug.
     * @param bool $die Define whether to die the script or not.
     */
    public static function debug($var, $die = true)
    {
        if (self::IP_FILTER === null || self::hasIpFilter()) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);

            echo '<pre>';

            print_r(
                '<strong>'
                .$backtrace[0]['file']
                .' on line '
                .$backtrace[0]['line']
                .':</strong>'
            );

            echo '<hr />';

            print_r('('.gettype($var).') ');
            print_r($var);

            echo '</pre>';

            if ($die) {
                die;
            }
        }
    }

    /**
     * Define if a variable is an array and empty.
     *
     * @param mixed $var The variable to test.
     * @return bool
     */
    public static function isEmptyArray($var)
    {
        return is_array($var) && empty($var);
    }

    /**
     * Define if a variable is an array and not empty.
     *
     * @param mixed $var The variable to test.
     * @return bool
     */
    public static function isCompletedArray($var)
    {
        return is_array($var) && !empty($var);
    }

    /**
     * Define if a key exists in an array and whether is an array.
     *
     * @param string $key The array's key to test.
     * @param array $array The array to test.
     * @return bool
     * @throws Exception
     */
    public static function isSubArray($key, array $array)
    {
        if (!is_string($key)) {
            throw new Exception(sprintf(
                'The parameter $key must be a string, %s given!',
                gettype($key)
            ));
        }

        return array_key_exists($key, $array) && is_array($array[$key]);
    }

    /**
     * Get only ID and name of an array data.
     *
     * @param array $array
     * @param string $id
     * @return array
     */
    public static function getOnlyIdAndName($array, $id = null)
    {
        $datas = $array;
        $return = [];

        foreach ($datas as $data) {
            if ($id) {
                $return[$data[$id]] = $data['name'];
            } else {
                $return[$data[array_keys($data)[0]]] = $data['name'];
            }
        }

        return $return;
    }

    /**
     * Get model list.
     *
     * @return array
     */
    public static function getModelList()
    {
        $model_list = array();
        foreach (scandir(dirname(dirname(dirname(__FILE__))).'/models') as $file) {
            if (preg_match('[('.self::NAME.')([A-Z]{1}[a-z]*)*(\.php)]', $file) != false) {
                $model_list[] = str_replace('.php', '', $file);
            }
        }
        return (array)$model_list;
    }

    /**
     * Transform API Translation to prestashop ObjectModel translation's array
     *
     * @pararm array
     * @return array|false
     * @throws \PrestaShopException
     */
    public static function formatTranslations($src)
    {
        if (!is_array($src)) {
            return false;
        }
        $activeLanguagesFormatted = array();
        $activeLanguages = Language::getIsoIds(1);
        foreach ($activeLanguages as $language) {
            $activeLanguagesFormatted[] = $language['id_lang'];
        }
        $translations = array();
        foreach ($src as $translation) {
            $isoLang = substr($translation['locale'], 0, 2);
            if (!($isoId = Language::getIdByIso($isoLang))
                && $isoLang == 'en') {
                $isoId = Language::getIdByIso('gb');
            }

            if (!$isoId) {
                continue;
            }
            // Check if Language is enabled
            if (in_array($isoId, $activeLanguagesFormatted)) {
                $translations[$isoId] = $translation['value'];
            }
        }
        return $translations;
    }

    /**
     * Convert an object to an array
     *
     * @param object $object
     * @return array
     */
    public static function objectToArray($object)
    {
        $reflectionClass = new ReflectionClass(get_class($object));
        $array = array();
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $array[$property->getName()] = $property->getValue($object);
            $property->setAccessible(false);
        }
        return $array;
    }

    public static function array_values_recursive($arr)
    {
        $tempArr=[];
        foreach ($arr as $key => $value)
        {
            if (is_object($value)) {
                $value = self::objectToArray($value);
            }
            if (is_array($value))
            {
                $tempArr[$key] = self::array_values_recursive($value);
            } else {
                $tempArr[$key] =  $value;
            }
        }

        return $tempArr;
    }

    /**
     * Check if category is enabled
     *
     * @param int $prestashopCategory
     * @param int $idShop
     * @return bool
     */
    public static function isCategoryEnabled($prestashopCategory, $idShop) {
        $query = (new \DbQuery())
            ->select(\SdevAdeoCategoryMapping::COLUMN_ID)
            ->from(\SdevAdeoCategoryMapping::getTableName())
            ->where(
                \SdevAdeoCategoryMapping::COLUMN_ACTIVE_CATEGORY.' = 1 AND '.
                \SdevAdeoCategoryMapping::COLUMN_PRESTASHOP_CATEGORY.' = '.(int)$prestashopCategory.
                ' AND '.\SdevAdeoCategoryMapping::COLUMN_ID_SHOP.' = '.(int)$idShop
            )
        ;

        return (bool)\Db::getInstance()->getValue($query);
    }

    /**
     * Return the category rule corresponding to the prestashop category
     *
     * @param int $prestashopCategory
     * @param int $idShop
     * @return array | false
     * @throws \PrestaShopDatabaseException
     */
    public static function getCategoryRule($prestashopCategory, $idShop)
    {
        $query = (new \DbQuery())
            ->select('DISTINCT cr.*')
            ->from(
                SdevAdeoCategoryRule::getTableName(),
                'cr'
            )
            ->innerJoin(
                \SdevAdeoCategoryMapping::getTableName(),
                'cm',
                'cm.'.\SdevAdeoCategoryMapping::COLUMN_CATEGORY_RULE.' = cr.'.SdevAdeoCategoryRule::COLUMN_ID.
                ' AND cm.'.\SdevAdeoCategoryMapping::COLUMN_ACTIVE_CATEGORY.' = 1'.
                ' AND cm.'.\SdevAdeoCategoryMapping::COLUMN_PRESTASHOP_CATEGORY.' = '.(int)$prestashopCategory.
                ' AND cm.'.\SdevAdeoCategoryMapping::COLUMN_ID_SHOP.' = '.(int)$idShop
            )
        ;

        $categoryRule = Db::getInstance()->executeS($query);
        if (empty($categoryRule)) {
            if (
                ($id_parent = (new \Category($prestashopCategory))->id_parent)
                && \Validate::isLoadedObject(
                    $prestashopCategory = (new \Category($id_parent))
                )
            ){
                return self::getCategoryRule($prestashopCategory->id, $idShop);
            } else {
                return false;
            }
        } else {
            $categoryRule[0]['pricingRule'] = self::getPricingRule($categoryRule[0]['id']);
            return $categoryRule[0];
        }
    }

    /**
     * Return the pricing rule corresponding to the category rule
     *
     * @return array
     * @throws \PrestaShopDatabaseException
     */
    public static function getPricingRule($categoryRuleId)
    {
        $query = (new \DbQuery())
            ->select('pr.minAmount, pr.maxAmount, pr.value as pricingRuleValue, pr.typePercent as pricingRuleTypePercent')
            ->from(\SdevAdeoPricingRule::getTableName(), 'pr')
            ->where('categoryRule = '.(int)$categoryRuleId);
        return Db::getInstance()->executeS($query);
    }

    /**
     * Get error message with error code and add this error into the logs file.
     *
     * @param string $flow - Flow type (offers, products, ...).
     * @param int $error_code - Error's code.
     * @param Product $product - Product object.
     * @param int $id_product_attribute - Product attribute's ID.
     * @param string $additional_parameter Parameter provided to the message
     * @return array
     */
    public static function addProductFlowLogs($id_shop, $flow, $error_code, $product, $id_product_attribute = null, $additional_parameter = null)
    {
        $module = \Module::getInstanceByName('sdevadeo');
        // Get the error message according to the error code.
        switch ($error_code) {
            // SUCCESS
            case 100:
                $error_msg = $module->l('Product exported with success');
                break;

            // FILTERED
            case 201:
                $error_msg = $module->l('Inactive product');
                break;
            case 202:
                $error_msg = $module->l('Default category not mapped');
                break;
            case 203:
                $error_msg = $module->l('The manufacturer product is disallowed');
                break;
            case 204:
                $error_msg = $module->l('The supplier product is disallowed');
                break;
            case 205:
                $error_msg = $module->l('Manufacturer not mapped');
                break;

            // ERROR
            case 301:
                $error_msg = $module->l('Product have no manufacturer set');
                break;
            case 302:
                $error_msg = $module->l('Product have no ean reference set or wrong format (13 characters)');
                break;
            case 303:
                $error_msg = $module->l('Required attribute mapping missing');
                break;
            case 304:
                $error_msg = $module->l(
                    sprintf(
                        'Required attribute %s missing',
                        $additional_parameter
                    )
                );
                break;
            case 305:
                $error_msg = $module->l('Price is null');
                break;
            case 306:
                $error_msg = $module->l('No stock found');
                break;
            case 307:
                $error_msg = $module->l('Reference is duplicated');
                break;
            case 308:
                $error_msg = $module->l('Reference is missing');
                break;
            case 309:
                $error_msg = $module->l('The condition of the product is not new');
                break;
            case 310:
                $error_msg = $module->l('The product has no carrier mapped');
                break;
            case 311:
                $error_msg = $module->l('The product tax is not mapped or missing for a country');
                break;
        };

        // Get the error type according to the error code.
        if ($error_code == 100) {
        $error_type = 'success';
        } elseif ($error_code >= 300) {
        $error_type = 'error';
        } elseif ($error_code >= 200) {
        $error_type = 'filtered';
        }

        $dir = \Module::getInstanceByName('sdevadeo')->getLocalPath().'logs/flux/'.(self::strtolower($flow) == 'product' ? 'product' : 'offer').'/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $logs_file = $dir.(self::strtolower($flow) == 'product' ? 'product' : 'offer').'_'.self::strtolower(str_replace('-', '_', self::str2url(Shop::getShop($id_shop)['name'])).'.txt');

        $reference = self::getProductReferenceByIds(array(
            'id_product' => $product->id,
            'id_product_attribute' => $id_product_attribute
        ));

        // Creating of logs file.
        file_put_contents(
            $logs_file,
            '[' . date('d/m/Y H:i:s') . '] '
            . 'ERROR_TYPE: ' . self::strtoupper($error_type)
            . '; REF_PRODUCT: ' . $reference
            . '; ID_PRODUCT: ' . $product->id
            . '; ID_PRODUCT_ATTRIBUTE: ' . ($id_product_attribute = $id_product_attribute
                ? $id_product_attribute
                : 'No attribute')
            . '; ERROR_CODE: SDEV-ERROR-' . $error_code
            . '; ERROR_MESSAGE: ' . $error_msg . ';' . "\n",
            FILE_APPEND
        );

        $current_log = array(
            'error_type' => self::strtoupper($error_type),
            'ref_product' => $reference,
            'id_product' => (int)$product->id,
            'id_product_attribute' => (int)$id_product_attribute,
            'error_code' => 'SDEV-ERROR-'.$error_code,
            'error_message' => $error_msg
        );

        return $current_log;
    }

    /**
     * Get product reference by IDs.
     *
     * @param array $ids - Must contain 'id_product' and 'id_product_attribute' if you have it.
     * @return string|bool
     */
    private static function getProductReferenceByIds($ids)
    {
        $query = (new \DbQuery())
            ->select('reference')
            ->from('product'. ($ids['id_product_attribute'] ? '_attribute' : ''))
            ->where('`id_product` = ' . (int)$ids['id_product']
            . ($ids['id_product_attribute'] ? ' AND `id_product_attribute` = ' . (int)$ids['id_product_attribute'] : '')
            )
        ;

        if (($ref = Db::getInstance()->getValue($query)) || !$ids['id_product_attribute']) {
            return $ref;
        }
        // if product attribute reference is not set then take the product reference
        $query = (new \DbQuery())
            ->select('reference')
            ->from('product')
            ->where('`id_product` = ' . (int)$ids['id_product'])
        ;
        return Db::getInstance()->getValue($query);
    }

    /**
     * Replace Product::_getAttributeImageAssociations to use the product image position.
     *
     * @param int $id_product_attribute
     * @return array
     */
    public static function _getAttributeImageAssociations($id_product_attribute)
    {
        $combination_images = array();

        $data = Db::getInstance()->executeS(
            'SELECT i.`id_image`
            FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai
                LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.id_image = pai.id_image)
                LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` psis ON (psis.id_image = i.id_image AND psis.id_shop = ' . (int)Context::getContext()->shop->id . ')
            WHERE pai.`id_product_attribute` = ' . (int)$id_product_attribute
            . ' ORDER BY i.position'
        );

        if ($data) {
            foreach ($data as $row) {
                $combination_images[] = (int)$row['id_image'];
            }
        }

        return $combination_images;
    }

    /**
     * Return either the product list to proceed or the total count
     *
     * @param $count
     * @return array|bool|\mysqli_result|\PDOStatement|resource|string|null
     * @throws \PrestaShopDatabaseException
     */
    public static function getProductListToProceed($count = false, $limit = 0, $offset = 0)
    {
        $db = Db::getInstance();
        $shopId = Context::getContext()->shop->id;
        $query = (new \DbQuery())
            ->select('ps.id_product, pas.id_product_attribute, ps.active')
			->from('product_shop', 'ps')
            ->leftJoin(
                'product_attribute_shop',
                'pas',
                'pas.id_product = ps.id_product'
            )
            ->where('ps.id_shop = '.(int)$shopId)
            ->orderBy('ps.id_product, pas.id_product_attribute ASC')
            ->groupBy('ps.id_product, pas.id_product_attribute')
        ;
        if ($limit) {
            $query->limit($limit, $offset);
        }

        $results = $db->executeS($query);

        return $count ? $db->numRows() : $results;
    }

    /**
     * @param $reference
     * @return array
     */
    public static function getProductIdByReference($reference)
    {
        $query = (new \DbQuery())
            ->select('p.id_product, pa.id_product_attribute')
            ->from('product', 'p')
            ->leftJoin('product_attribute',
                'pa',
                'p.id_product = pa.id_product'
            )
            ->where('(p.reference = \''. pSQL($reference).'\' OR pa.reference = \''.pSQL($reference).'\')');
        return Db::getInstance()->executeS($query)[0];
    }

    /**
     * Clean the reports that are older
     *
     * @param int $isProductFlow
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public static function cleanAndGetFlowReport($isProductFlow)
    {
        Db::getInstance()->delete(
            \SdevAdeoImportLogs::getTableName(),
            'creation_date < CURDATE() - INTERVAL 30 DAY'
        );
        $context = Context::getContext();
        return Db::getInstance()->executeS((new \DbQuery())
            ->select('*')
            ->from(\SdevAdeoImportLogs::getTableName())
            ->where('shop_id = '.(int)$context->shop->id)
            ->where('is_product_import = \''.(int)$isProductFlow.'\'')
            ->limit(10)
            ->orderBy('id_import DESC')
        );
    }

    /**
     * @return array
     */
    public static function updateOfferReport($date = false)
    {
        $module = \Module::getInstanceByName('sdevadeo');
        $finalLogs = array();
        $shopName = str_replace('-', '_', self::str2url(Context::getContext()->shop->name));
        $reportLog = self::cleanAndGetFlowReport(0);
        if (!$reportLog) {
            return [];
        }
        foreach ($reportLog as $report) {
            $logs = array();
            /** @var GetOfferExportInformationResponse $response */
            $response = (new GetOfferExportInformationRequest())
                ->setRequestParameter($report['id_import'])
                ->execute(
                    Configuration::getValue(Configuration::API_KEY),
                    Configuration::getValue(Configuration::API_ENV)
                )
                ->getResponse();
            $exportInformation = $response->getExportInformation();
            $logs['nb_products_read'] = $exportInformation->getLinesRead();
            $logs['nb_products_treated'] = $exportInformation->getLinesInSuccess();
            $logs['nb_products_pending'] = $exportInformation->getLinesInPending();
            $logs['nb_products_error'] = $exportInformation->getLinesInError();
            $logs['nb_products_inserted'] = $exportInformation->getOfferInserted();
            $logs['nb_products_deleted'] = $exportInformation->getOfferDeleted();
            $logs['mode'] = $exportInformation->getMode();
            $logs['status'] = $exportInformation->getStatus();
            $logs['date'] = date("d-m-Y H:i:s", strtotime($exportInformation->getDateCreated()));

            if ($response->getExportInformation()->isHasErrorReport() == 'true') {
                /** @var GetOfferExportErrorReportResponse $response */
                $response = (new GetOfferExportErrorReportRequest())
                    ->setRequestParameter($report['id_import'])
                    ->execute(
                        Configuration::getValue(Configuration::API_KEY),
                        Configuration::getValue(Configuration::API_ENV)
                    )
                    ->getResponse();
                $dir = dirname(dirname(dirname(__FILE__))) . '/logs/report/offer/' . str_replace('-', '_', self::str2url($shopName));
                if (!file_exists($dir . '/offer_' . $report['id_import'] . '.csv')) {
                    if (!is_dir($dir)) {
                        mkdir(
                            $dir,
                            0755,
                            true
                        );
                    }
                    if (file_put_contents($dir . '/offer_' . $report['id_import'] . '.csv', $response->getReportContent())) {
                        $logs['report_log'] = self::getShopDomain(true) . _MODULE_DIR_ . $module->name . '/logs/report/offer/'.$shopName.'/offer_' . $report['id_import'] . '.csv';
                    }
                } else {
                    $logs['report_log'] = self::getShopDomain(true) . _MODULE_DIR_ . $module->name . '/logs/report/offer/'.$shopName.'/offer_' . $report['id_import'] . '.csv';
                }
            } else {
                $logs['report_log'] = $module->l('Error log not initialized yet.');
            }
            $logs['id_import'] = $report['id_import'];
            $logs['date_update'] = $date;
            $finalLogs[] = $logs;
        }
        return $finalLogs;
    }

    /**
     * @return array
     */
    public static function updateProductReport()
    {
        $module = \Module::getInstanceByName('sdevadeo');
        $reportLog = self::cleanAndGetFlowReport(1);
        if (empty($reportLog)) {
            return array();
        }
        $logs = array();
        $shopName = str_replace('-', '_', self::str2url(Context::getContext()->shop->name));
        foreach ($reportLog as $report) {
            $errors = $success = array();
            /** @var GetProductExportInformationResponse $response */
            $response = (new GetProductExportInformationRequest())
                ->setRequestParameter($report['id_import'])
                ->execute(
                    Configuration::getValue(Configuration::API_KEY),
                    Configuration::getValue(Configuration::API_ENV)
                )
                ->getResponse();
            Configuration::updateValue(Configuration::DATE_PRODUCT_REPORT, $date = date('d-m-Y H:i:s'));
            $status = $response->getExportInformation()->getImportStatus();
            if (!in_array($status,['COMPLETE', 'SENT'])) {
                if (in_array($status, ['CANCELLED', 'FAILED', 'TRANSFORMATION_FAILED'])) {
                    $status = $module->l('FAILED');
                } else {
                    $status = $module->l('PENDING');
                }

                $logs[] = array(
                    'nb_products_read' => $response->getExportInformation()->getTransformLinesRead(),
                    'nb_products_treated' => $response->getExportInformation()->getTransformLinesInSuccess(),
                    'nb_products_error' => $response->getExportInformation()->getTransformLinesWithWarning(),
                    'nb_products_rejected' => $response->getExportInformation()->getIntegrationDetails()->getRejectedProducts(),

                    'report_log' => $module->l('Log not initialized yet.'),
                    'status' => $status,
                    'date' => date("d-m-Y H:i:s", strtotime($response->getExportInformation()->getDateCreated())),
                    'id_import' => $report['id_import'],
                    'date_update' => $date
                );
            } else {
                // Handle reports
                if ($response->getExportInformation()->isHasNewProductReport() == 'true') {
                    $reportFilename = $module->getLocalPath() . 'logs/report/product/' . $shopName . '/product_new_' . $report['id_import'] . '.csv';
                    if (!file_exists($reportFilename)) {
                        if (!is_dir($module->getLocalPath() . 'logs/report/product/' . $shopName)) {
                            mkdir($module->getLocalPath() . 'logs/report/product/' . $shopName, 0755, true);
                        }
                        /** @var GetProductExportReportResponse $response */
                        $exportResponse = (new GetProductExportReportRequest())
                            ->setRequestParameter($report['id_import'])
                            ->execute(
                                Configuration::getValue(Configuration::API_KEY),
                                Configuration::getValue(Configuration::API_ENV)
                            )
                            ->getResponse();
                        file_put_contents(
                            $reportFilename,
                            $exportResponse->getReportContent()
                        );
                    }
                    $success = array('successReportUrl' =>  self::getShopDomain(true) . _MODULE_DIR_ . $module->name . '/logs/report/product/' . $shopName . '/product_new_' . $report['id_import'] . '.csv');
                }
                if ($response->getExportInformation()->isHasErrorReport() == 'true') {
                    $reportFilename = $module->getLocalPath() . 'logs/report/product/' . $shopName . '/product_error_' . $report['id_import'] . '.csv';
                    if (!file_exists($reportFilename)) {
                        if (!is_dir($module->getLocalPath() . 'logs/report/product/' . $shopName)) {
                            mkdir($module->getLocalPath() . 'logs/report/product/' . $shopName, 0755, true);
                        }
                        /** @var GetProductExportErrorReportResponse $response */
                        $exportResponse = (new GetProductExportErrorReportRequest())
                            ->setRequestParameter($report['id_import'])
                            ->execute(
                                Configuration::getValue(Configuration::API_KEY),
                                Configuration::getValue(Configuration::API_ENV)
                            )
                            ->getResponse();
                        file_put_contents(
                            $reportFilename,
                            $exportResponse->getReportContent()
                        );
                    }
                    $errors = array('errorReportUrl' => self::getShopDomain(true) . _MODULE_DIR_ . $module->name . '/logs/report/product/' . $shopName . '/product_error_' . $report['id_import'] . '.csv');
                }

                $logs[] = array(
                    'nb_products_read' => $response->getExportInformation()->getTransformLinesRead(),
                    'nb_products_treated' => $response->getExportInformation()->getTransformLinesInSuccess(),
                    'nb_products_error' => $response->getExportInformation()->getTransformLinesWithWarning(),
                    'nb_products_rejected' => $response->getExportInformation()->getIntegrationDetails()->getRejectedProducts(),
                    'report_log' => array($success, $errors),
                    'status' => $status,
                    'date' => date("d-m-Y H:i:s", strtotime($response->getExportInformation()->getDateCreated())),
                    'id_import' => $report['id_import'],
                    'date_update' => $date
                );
            }
        }
        return $logs;
    }

    /**
     * Check if marketplace order is already imported
     *
     * @param string $mp_order_id
     * @return false|string
     */
    public static function getIdByMpOrderId($mp_order_id)
    {
        return Db::getInstance()->getValue('SELECT `id_order` FROM `' . _DB_PREFIX_ . 'orders` WHERE (`mp_order_id` = \'' . pSQL($mp_order_id) . '\' OR `mp_order_id` LIKE \'' . pSQL($mp_order_id) . '|%\')');
    }

    /**
     * Get MpOrderId for a given order identifier
     *
     * @param $order_id
     * @return mixed|string
     */
    public static function getMpOrderIdById($order_id)
    {
        $mp_order_id = Db::getInstance()->getValue('SELECT `mp_order_id` FROM `' . _DB_PREFIX_ . 'orders` WHERE `id_order` = ' . (int)$order_id);
        $datas = explode('|', $mp_order_id);

        return $datas[0];
    }


    /**
     * Check if a field exists into a table.
     *
     * @param string $table
     * @param string $column_name
     * @return boolean
     */
    public static function dbColumnExist($table, $column_name)
    {
        $columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . pSQL($table) . '`');

        foreach ($columns as $column) {
            if ($column['Field'] == $column_name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add a field into a table.
     *
     * @param string $table
     * @param string $column_name
     * @param string $type
     * @param integer $size
     * @param boolean $not_null
     * @return boolean
     */
    public static function dbAddColumn($table, $column_name, $type = 'VARCHAR', $size = 255, $not_null = false)
    {
        if (!self::dbColumnExist($table, $column_name)) {
            $sql = 'ALTER TABLE `' . _DB_PREFIX_ . pSQL($table) . '` ADD `' . pSQL($column_name) . '` ' . pSQL($type);

            if ($type == 'VARCHAR') {
                $sql .= '(' . (int)$size . ') ';
            }

            $sql .= $not_null ? ' NOT NULL' : ' NULL';

            return (bool)Db::getInstance()->execute($sql);
        }

        return true;
    }

    /**
     * Check if an index exists into a table.
     *
     * @param string $table
     * @param string $column_name
     * @return boolean
     */
    public static function dbIndexExist($table, $column_name)
    {
        $indexes = Db::getInstance()->executeS('SHOW INDEX FROM `' . _DB_PREFIX_ . pSQL($table) . '`');

        foreach ($indexes as $index) {
            if ($index['Column_name'] == $column_name) {
                return true;
            }
        }

        return false;
    }


    /**
     * Add an index into a table.
     *
     * @param string $table
     * @param string $column_name
     * @return boolean
     */
    public static function dbAddIndex($table, $column_name)
    {
        if (!self::dbIndexExist($table, $column_name)) {
            return (bool)Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . pSQL($table) . '` ADD INDEX (`' . pSQL($column_name) . '`)');
        }

        return true;
    }

    /**
     * @param string $city
     * @return string
     */
    public static function cleanCity($city)
    {
        return str_replace('_', '-', $city);
    }

    /**
     * @param $address
     * @return false|string
     */
    public static function cleanAddress($address)
    {
        $address = preg_replace("#[^\w0-9@, ]#", "", $address);
        $address = str_replace(array('.', '@', '!', '¨'), ' ', $address);
        $address = str_replace(array('_', '+'), '-', $address);
        $address = self::substr($address, 0, 128);

        return $address;
    }

    /**
     * @param $name
     * @return string
     * @throws Exception
     */
    public static function cleanName($name)
    {
        $name = preg_replace('`'.self::cleanNonUnicodeSupport('/[0-9!<>,;?=+()@#"°{}²_$%:]+/').'`u', '', $name);
        $name = self::substr($name, 0, 32);
        $name = (!empty($name) ? $name : '-');

        return $name;
    }

    public static function getIdOrderCarrier($id_order)
    {
        return (int)Db::getInstance()->getValue('
            SELECT `id_order_carrier`
            FROM `'._DB_PREFIX_.'order_carrier`
            WHERE `id_order` = '.(int)$id_order
        );
    }

    /**
     * Get product attributes.
     *
     * @param int $id_product
     * @param int $id_product_attribute
     * @return array
     */
    public static function getAttributesParams($id_product, $id_product_attribute, $id_lang = null)
    {
        $id_lang ?: (int)Context::getContext()->language->id;

        $query = (new \DbQuery())
            ->select('a.`id_attribute`, a.`id_attribute_group`, al.`name`, agl.`name` as `group`')
            ->from('attribute', 'a')
            ->leftJoin(
                'attribute_lang',
                'al',
                'al.`id_attribute` = a.`id_attribute` AND al.`id_lang` = ' . (int)$id_lang
            )
            ->leftJoin(
                'product_attribute_combination',
                'pac',
                'pac.`id_attribute` = a.`id_attribute`'
            )
            ->leftJoin(
                'product_attribute',
                'pa',
                'pa.`id_product_attribute` = pac.`id_product_attribute`'
            )
            ->leftJoin(
                'attribute_group_lang',
                'agl',
                'a.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int)$id_lang
            )
            ->where('pa.`id_product` = ' . (int)$id_product)
            ->where('pac.`id_product_attribute` = ' . (int)$id_product_attribute)
            ->where('agl.`id_lang` = ' . (int)$id_lang)
            ;

        return Db::getInstance()->executeS($query);
    }

    /**
     * For a given reference, returns the corresponding id.
     *
     * @param string $reference
     *
     * @return int|string Product identifier
     */
    public static function getIdByReference($reference)
    {
        if (empty($reference)) {
            return 0;
        }

        if (!Validate::isReference($reference)) {
            return 0;
        }

        $query = (new DbQuery())
            ->select('p.id_product')
            ->from('product', 'p')
            ->where('p.reference = \'' . pSQL($reference) . '\'');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * @param int $idTaxRulesGroup
     * @param int $idCountry
     * @return false|string|null
     */
    public static function getTaxByTaxRulesGroupAndCountry($idTaxRulesGroup, $idCountry)
    {
        $query = (new DbQuery())
            ->select('tr.id_tax')
            ->from('tax_rule', 'tr')
            ->innerJoin(
                'tax_rules_group_shop',
                'trgs',
                implode(' AND ', [
                    ('tr.id_tax_rules_group = trgs.id_tax_rules_group'),
                    ('trgs.id_shop = '.Context::getContext()->shop->id),
                ])
            )
            ->where(
                implode(' AND ', [
                    ('tr.id_country = ' . $idCountry),
                    ('tr.id_tax_rules_group = ' . $idTaxRulesGroup),
                ])
            );

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
}
