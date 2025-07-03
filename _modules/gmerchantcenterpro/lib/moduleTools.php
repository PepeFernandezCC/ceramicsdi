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

namespace Gmerchantcenterpro\ModuleLib;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Gmerchantcenterpro\Configuration\moduleConfiguration;
use Gmerchantcenterpro\Dao\moduleDao;
use Gmerchantcenterpro\Models\Feeds;
use Gmerchantcenterpro\Models\Reporting;

class moduleTools
{
    /**
     * all details of the shop group or one required detail
     *
     * @param string $sDetail
     *
     * @return mixed : array or mixed
     */
    public static function getGroupShopDetail($sDetail = null)
    {
        // get the current group shop
        $oGroupShop = new \ShopGroup(\Context::getContext()->shop->id_shop_group);

        $aDetails = $oGroupShop->getFields();

        return $sDetail !== null ? (isset($aDetails[$sDetail]) ? $aDetails[$sDetail] : false) : $aDetails;
    }

    /**
     * returns good translated errors
     */
    public static function translateJsMsg()
    {
        return moduleConfiguration::getJsMessage();
    }

    /**
     * update new keys in new module version
     */
    public static function updateConfiguration()
    {
        // check to update new module version
        foreach (moduleConfiguration::getConfVar() as $sKey => $mVal) {
            // use case - not exists
            if (\Configuration::get($sKey) === false) {
                // update key/ value
                \Configuration::updateValue($sKey, $mVal);
            }
        }
    }

    /**
     * set all constant module in ps_configuration
     *
     * @param array $aOptionListToUnserialize
     * @param int $iShopId
     */
    public static function getConfiguration($aOptionListToUnserialize = null, $iShopId = null)
    {
        // get configuration options
        if (null !== $iShopId && is_numeric($iShopId)) {
            \GMerchantCenterPro::$conf = \Configuration::getMultiple(array_keys(moduleConfiguration::getConfVar()), null, null, $iShopId);
        } else {
            \GMerchantCenterPro::$conf = \Configuration::getMultiple(array_keys(moduleConfiguration::getConfVar()));
        }

        if (!empty($aOptionListToUnserialize) && is_array($aOptionListToUnserialize)) {
            foreach ($aOptionListToUnserialize as $sOption) {
                if (!empty(\GMerchantCenterPro::$conf[strtoupper($sOption)]) && is_string(\GMerchantCenterPro::$conf[strtoupper($sOption)])) {
                    \GMerchantCenterPro::$conf[strtoupper($sOption)] = moduleTools::handleGetConfigurationData(\GMerchantCenterPro::$conf[strtoupper($sOption)]);
                }
            }
        }
    }

    /**
     * defines if the language is active
     *
     * @param mixed $mLang
     *
     * @return bool
     */
    public static function isActiveLang($mLang)
    {
        if (is_numeric($mLang)) {
            $sField = 'id_lang';
        } else {
            $sField = 'iso_code';
            $mLang = strtolower($mLang);
        }

        $mResult = \Db::getInstance()->getValue('SELECT count(*) FROM `' . _DB_PREFIX_ . 'lang` WHERE active = 1 AND `' . $sField . '` = "' . pSQL($mLang) . '"');

        return !empty($mResult) ? true : false;
    }

    /**
     * set good iso lang
     *
     * @return string
     */
    public static function getLangIso($iLangId = null)
    {
        if (null === $iLangId) {
            $iLangId = \GMerchantCenterPro::$iCurrentLang;
        }

        // get iso lang
        $sIsoLang = \Language::getIsoById($iLangId);

        if (false === $sIsoLang) {
            $sIsoLang = 'en';
        }

        return $sIsoLang;
    }

    /**
     * return Lang id from iso code
     *
     * @param string $sIsoCode
     *
     * @return int
     */
    public static function getLangId($sIsoCode, $iDefaultId = null)
    {
        // get iso lang
        $iLangId = \Language::getIdByIso($sIsoCode);

        if (empty($iLangId) && $iDefaultId !== null) {
            $iLangId = $iDefaultId;
        }

        return $iLangId;
    }

    /**
     * Handle the list of acitve languages
     *
     * @param int $id_shop
     *
     * @return array
     */
    public static function getAvailableLanguages($id_shop)
    {
        // set
        $available_languages = [];

        $shop_languages = \Language::getLanguages(false, (int) $id_shop);

        foreach ($shop_languages as $language) {
            if ($language['active']) {
                $available_languages[] = $language;
            }
        }

        return $available_languages;
    }

    /**
     * returns information about languages / countries and currencies available for Google
     *
     * @param array $available_languages
     *
     * @return array
     */
    public static function getLangCurrencyCountry(array $available_languages)
    {
        // Force database update to be sure we could make the migration
        moduleUpdate::create()->run('tables');
        moduleUpdate::create()->run('fields');
        $output_data = [];

        $hasData = Feeds::hasSavedData((int) \Context::getContext()->shop->id);

        if (!empty($hasData)) {
            $available_feeds = Feeds::getAvailableFeeds((int) \Context::getContext()->shop->id);

            if (!empty($available_feeds)) {
                foreach ($available_languages as $lang) {
                    $current_feed_shop = Feeds::getFeedLangData($lang['iso_code'], (int) \Context::getContext()->shop->id);

                    if (!empty($current_feed_shop)) {
                        foreach ($current_feed_shop as $feed) {
                            $language = new \Language($lang['id_lang']);
                            $id_country = \Country::getByIso(\Tools::strtolower($feed['iso_country']));

                            if (!empty($id_country)) {
                                $country_name = \Country::getNameById(\GMerchantCenterPro::$iCurrentLang, $id_country);
                                $country = new \Country($id_country);

                                if (!empty($country->id)) {
                                    if (!empty($country->active)) {
                                        $id_currency = \Currency::getIdByIsoCode($feed['iso_currency']);
                                        $currency = new \Currency($id_currency);

                                        if (!empty($currency->iso_code)) {
                                            $output_data[] = [
                                                'langId' => $language->id,
                                                'langIso' => $language->iso_code,
                                                'countryIso' => $country->iso_code,
                                                'currencyIso' => $currency->iso_code,
                                                'currencyId' => $currency->id,
                                                'currencyFirst' => 1,
                                                'langName' => $language->name,
                                                'countryName' => $country_name,
                                                'currencySign' => $currency->sign,
                                                'taxonomy' => $feed['taxonomy'],
                                                'is_default' => $feed['feed_is_default'],
                                                'id_feed' => $feed['id_feed'],
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        sort($output_data);

        return $output_data;
    }

    /**
     * returns current currency sign or id
     *
     * @param string $sField : field name has to be returned
     * @param int $iCurrencyId : currency id
     *
     * @return mixed : string or array
     */
    public static function getCurrency($sField = null, $iCurrencyId = null)
    {
        // set
        $mCurrency = null;

        // get currency id
        if (null === $iCurrencyId) {
            $iCurrencyId = \Configuration::get('PS_CURRENCY_DEFAULT');
        }

        $aCurrency = \Currency::getCurrency($iCurrencyId);

        if ($sField !== null) {
            switch ($sField) {
                case 'id_currency':
                    $mCurrency = $aCurrency['id_currency'];

                    break;
                case 'name':
                    $mCurrency = $aCurrency['name'];

                    break;
                case 'iso_code':
                    $mCurrency = $aCurrency['iso_code'];

                    break;
                case 'iso_code_num':
                    $mCurrency = $aCurrency['iso_code_num'];

                    break;
                case 'sign':
                    $mCurrency = $aCurrency['sign'];

                    break;
                case 'conversion_rate':
                    $mCurrency = $aCurrency['conversion_rate'];

                    break;
                case 'format':
                    $mCurrency = $aCurrency['format'];

                    break;
                default:
                    $mCurrency = $aCurrency;

                    break;
            }
        }

        return $mCurrency;
    }

    /**
     * returns timestamp
     *
     * @param string $sDate
     * @param string $sType
     *
     * @return mixed : bool or int
     */
    public static function getTimeStamp($sDate, $sType = 'en')
    {
        // set variable
        $iTimeStamp = false;

        // get date
        $aTmpDate = explode(' ', str_replace(['-', '/', ':'], ' ', $sDate));

        if (count($aTmpDate) > 1) {
            if ($sType == 'en') {
                $iTimeStamp = mktime(0, 0, 0, (int) $aTmpDate[0], (int) $aTmpDate[1], (int) $aTmpDate[2]);
            } elseif ($sType == 'db') {
                $iTimeStamp = mktime(0, 0, 0, (int) $aTmpDate[1], (int) $aTmpDate[2], (int) $aTmpDate[0]);
            } else {
                $iTimeStamp = mktime(0, 0, 0, (int) $aTmpDate[1], (int) $aTmpDate[0], (int) $aTmpDate[2]);
            }
        }

        return $iTimeStamp;
    }

    /**
     * returns a formatted date
     *
     * @param int $iTimestamp
     * @param mixed $mLocale
     * @param string $sLangIso
     *
     * @return string
     */
    public static function formatTimestamp($iTimestamp, $sTemplate = null, $mLocale = false, $sLangIso = null)
    {
        // set
        $sDate = '';

        if ($mLocale !== false) {
            if (null === $sTemplate) {
                $sTemplate = '%d %h. %Y';
            }
            // set date with locale format
            $sDate = strftime($sTemplate, $iTimestamp);
        } else {
            // get Lang ISO
            $sLangIso = ($sLangIso !== null) ? $sLangIso : \GMerchantCenterPro::$sCurrentLang;

            switch ($sTemplate) {
                case 'snippet':
                    $sDate = date('d', $iTimestamp) . ' ' . (!empty(moduleConfiguration::getMonths()[$sLangIso]) ? moduleConfiguration::getMonths()[$sLangIso]['long'][date('n', $iTimestamp)] : date('M', $iTimestamp)) . ' ' . date('Y', $iTimestamp);

                    break;
                default:
                    // set date with matching month or with default language
                    $sDate = date('d', $iTimestamp) . ' ' . (!empty(moduleConfiguration::getMonths()[$sLangIso]) ? moduleConfiguration::getMonths()[$sLangIso]['short'][date('n', $iTimestamp)] : date('M', $iTimestamp)) . ' ' . date('Y', $iTimestamp);

                    break;
            }
        }

        return $sDate;
    }

    /**
     * returns formatted URI for page name type
     *
     * @return mixed
     */
    public static function getPageName()
    {
        $sScriptName = '';

        // use case - script name filled
        if (!empty($_SERVER['SCRIPT_NAME'])) {
            $sScriptName = $_SERVER['SCRIPT_NAME'];
        } // use case - php_self filled
        elseif ($_SERVER['PHP_SELF']) {
            $sScriptName = $_SERVER['PHP_SELF'];
        } // use case - default script name
        else {
            $sScriptName = 'index.php';
        }

        return substr(basename($sScriptName), 0, strpos(basename($sScriptName), '.'));
    }

    /**
     * returns template path
     *
     * @param string $sTemplate
     *
     * @return string
     */
    public static function getTemplatePath($sTemplate)
    {
        return \GMerchantCenterPro::$oModule->getTemplatePath($sTemplate);
    }

    /**
     * returns product link
     *
     * @param \Product $oProduct
     * @param int $iLangId
     * @param string $sCatRewrite
     *
     * @return string
     */
    public static function getProductLink($oProduct, $iLangId, $sCatRewrite = '')
    {
        $sProdUrl = '';

        if (\Configuration::get('PS_REWRITING_SETTINGS')) {
            $sProdUrl = \Context::getContext()->link->getProductLink($oProduct, null, null, null, (int) $iLangId, null, 0, true);
        } else {
            $sProdUrl = \Context::getContext()->link->getProductLink($oProduct, null, null, null, (int) $iLangId, null, 0, false);
        }

        return $sProdUrl;
    }

    /**
     * returns the product condition
     *
     * @param string $sCondition
     *
     * @return string
     */
    public static function getProductCondition($sCondition = null)
    {
        $sResult = '';

        if ($sCondition !== null && in_array($sCondition, ['new', 'used', 'refurbished'])) {
            $sResult = $sCondition;
        } else {
            $sResult = !empty(\GMerchantCenterPro::$conf['GMCP_COND']) ? \GMerchantCenterPro::$conf['GMCP_COND'] : 'new';
        }

        return $sResult;
    }

    /**
     * returns product image
     *
     * @param \Product $oProduct
     * @param string $sImageType
     * @param array $aForceImage
     *
     * @return string
     */
    public static function getProductImage(\Product &$oProduct, $sImageType = null, $aForceImage = [])
    {
        $sImgUrl = '';

        if (\Validate::isLoadedObject($oProduct)) {
            // use case - get Image
            $aImage = !empty($aForceImage) ? $aForceImage : $oProduct->getImages(\GMerchantCenterPro::$iCurrentLang);

            if (!empty($aImage)) {
                // get image url
                if ($sImageType !== null) {
                    if (!\GMerchantCenterPro::$bCompare90) {
                        $sImgUrl = \Context::getContext()->link->getImageLink($oProduct->link_rewrite, $oProduct->id . '-' . $aImage['id_image'], $sImageType);
                    } else {
                        $sImgUrl = \Context::getContext()->link->getImageLink($oProduct->link_rewrite, $aImage['id_image']);
                    }
                } else {
                    $sImgUrl = \Context::getContext()->link->getImageLink($oProduct->link_rewrite, $oProduct->id . '-' . $aImage['id_image']);
                }
            }
        }

        return $sImgUrl;
    }

    /**
     * truncate current request_uri in order to delete params : sAction and sType
     *
     * @param string $mNeedle
     *
     * @return mixed
     */
    public static function truncateUri($mNeedle = '&sAction')
    {
        // set tmp
        $aQuery = [$mNeedle];

        // get URI
        $sURI = $_SERVER['REQUEST_URI'];

        foreach ($aQuery as $sNeedle) {
            $sURI = strstr($sURI, $sNeedle) ? substr($sURI, 0, strpos($sURI, $sNeedle)) : $sURI;
        }

        return $sURI;
    }

    /**
     * detects available method and apply json encode
     *
     * @return string
     */
    public static function jsonEncode($aData)
    {
        return json_encode($aData);
    }

    /**
     * detects available method and apply json decode
     *
     * @return mixed
     */
    public static function jsonDecode($aData)
    {
        return json_decode($aData);
    }

    /**
     * method check if specific module and module's vars are available
     *
     * @param string $sModuleName
     * @param array $aCheckedVars
     * @param bool $bObjReturn
     * @param bool $bOnlyInstalled
     *
     * @return mixed : true or false or obj
     */
    public static function isInstalled($sModuleName, array $aCheckedVars = [], $bObjReturn = false, $bOnlyInstalled = false)
    {
        $mReturn = false;

        // use case - check module is installed in DB
        if (\Module::isInstalled($sModuleName)) {
            if (!$bOnlyInstalled) {
                $oModule = \Module::getInstanceByName($sModuleName);

                if (!empty($oModule)) {
                    // check if module is activated
                    $aActivated = \Db::getInstance()->ExecuteS('SELECT id_module as id, active FROM ' . _DB_PREFIX_ . 'module WHERE name = "' . pSQL($sModuleName) . '" AND active = 1');

                    if (!empty($aActivated[0]['active'])) {
                        $mReturn = true;

                        if (version_compare(_PS_VERSION_, '1.5', '>')) {
                            $aActivated = \Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'module_shop WHERE id_module = ' . pSQL($aActivated[0]['id']) . ' AND id_shop = ' . \Context::getContext()->shop->id);

                            if (empty($aActivated)) {
                                $mReturn = false;
                            }
                        }

                        if ($mReturn) {
                            if (!empty($aCheckedVars)) {
                                foreach ($aCheckedVars as $sVarName) {
                                    $mVar = \Configuration::get($sVarName);

                                    if (empty($mVar)) {
                                        $mReturn = false;
                                    }
                                }
                            }
                        }
                    }
                }

                if ($mReturn && $bObjReturn) {
                    $mReturn = $oModule;
                }

                unset($oModule);
            } else {
                $mReturn = true;
            }
        }

        return $mReturn;
    }

    /**
     * check if the product is a valid obj
     *
     * @param int $iProdId
     * @param int $iLangId
     * @param bool $bObjReturn
     * @param bool $bAllProperties
     *
     * @return mixed : true or false
     */
    public static function isProductObj($iProdId, $iLangId, $bObjReturn = false, $bAllProperties = false)
    {
        // set
        $bReturn = false;

        $oProduct = new \Product($iProdId, $bAllProperties, $iLangId);

        if (\Validate::isLoadedObject($oProduct)) {
            $bReturn = true;
        }

        return !empty($bObjReturn) && $bReturn ? $oProduct : $bReturn;
    }

    /**
     * to compare date
     *
     * @param string $sDate1
     * @param string $sDate2
     *                       return int : difference entre les dates
     */
    public static function dateCompare($sDate1, $sDate2)
    {
        $dDate1 = date_create($sDate1);
        $dDate2 = date_create($sDate2);
        $iDiff = date_diff($dDate1, $dDate2);

        // if date2 > date1 return 0 else return 1
        return $iDiff->invert;
    }

    /**
     * write breadcrumbs of product for category
     *
     * @param int $iCatId
     * @param int $iLangId
     * @param string $sPath
     * @param bool $bEncoding
     *
     * @return string
     */
    public static function getProductPath($iCatId, $iLangId, $sPath = '', $bEncoding = true)
    {
        $oCategory = new \Category($iCatId);

        return \Validate::isLoadedObject($oCategory) ? str_replace('>', ' > ', strip_tags(self::getPath((int) $oCategory->id, (int) $iLangId, $sPath, $bEncoding))) : '';
    }

    /**
     * write breadcrumbs of product for category
     *
     * Forced to redo the function from Tools here as it works with cookie
     * for language, not a passed parameter in the function
     *
     * @param int $iCatId
     * @param int $iLangId
     * @param string $sPath
     * @param bool $bEncoding
     *
     * @return string
     */
    public static function getPath($iCatId, $iLangId, $sPath = '', $bEncoding = true)
    {
        $mReturn = '';

        if ($iCatId == 1) {
            $mReturn = $sPath;
        } else {
            // get pipe
            $sPipe = ' > ';

            $sFullPath = '';

            $aInterval = \Category::getInterval($iCatId);
            $aIntervalRoot = \Category::getInterval(\Context::getContext()->shop->getCategory());

            if (!empty($aInterval) && !empty($aIntervalRoot)) {
                $sQuery = 'SELECT c.id_category, cl.name, cl.link_rewrite'
                    . ' FROM ' . _DB_PREFIX_ . 'category c'
                    . (version_compare(_PS_VERSION_, '1.5', '>') ? \Shop::addSqlAssociation('category', 'c', false) : '')
                    . ' LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl ON (cl.id_category = c.id_category' . \Shop::addSqlRestrictionOnLang('cl') . ')'
                    . ' WHERE c.nleft <= ' . (int) $aInterval['nleft']
                    . ' AND c.nright >= ' . (int) $aInterval['nright']
                    . ' AND c.nleft >= ' . (int) $aIntervalRoot['nleft']
                    . ' AND c.nright <= ' . (int) $aIntervalRoot['nright']
                    . ' AND cl.id_lang = ' . (int) $iLangId
                    . ' AND c.level_depth > ' . (int) $aIntervalRoot['level_depth']
                    . ' ORDER BY c.level_depth ASC';

                $aCategories = \Db::getInstance()->executeS($sQuery);

                $iCount = 1;
                $nCategories = count($aCategories);

                foreach ($aCategories as $aCategory) {
                    $sFullPath .= ($bEncoding ? htmlentities($aCategory['name'], ENT_NOQUOTES, 'UTF-8') : $aCategory['name']) . (($iCount++ != $nCategories || !empty($sPath)) ? $sPipe : '');
                }

                $mReturn = $sFullPath . $sPath;
            }
        }

        return $mReturn;
    }

    /**
     * process categories to generate tree of them
     *
     * @param array $aCategories
     * @param array $aIndexedCat
     * @param array $aCurrentCat
     * @param int $iCurrentIndex
     * @param int $iDefaultId
     * @param bool $bFirstExec
     *
     * @return array
     */
    public static function recursiveCategoryTree(array $aCategories, array $aIndexedCat, $aCurrentCat, $iCurrentIndex = 1, $iDefaultId = null, $bFirstExec = false)
    {
        // set variables
        static $_aTmpCat;
        static $_aFormatCat;

        if ($bFirstExec) {
            $_aTmpCat = null;
            $_aFormatCat = null;
        }

        if (!isset($_aTmpCat[$aCurrentCat['infos']['id_parent']])) {
            $_aTmpCat[$aCurrentCat['infos']['id_parent']] = 0;
        }

        ++$_aTmpCat[$aCurrentCat['infos']['id_parent']];

        // calculate new level
        $aCurrentCat['infos']['iNewLevel'] = $aCurrentCat['infos']['level_depth'] + (version_compare(_PS_VERSION_, '1.5.0') != -1 ? 0 : 1);
        // calculate type of gif to display - displays tree in good
        $aCurrentCat['infos']['sGifType'] = (count($aCategories[$aCurrentCat['infos']['id_parent']]) == $_aTmpCat[$aCurrentCat['infos']['id_parent']] ? 'f' : 'b');

        // calculate if checked
        if (in_array($iCurrentIndex, $aIndexedCat)) {
            $aCurrentCat['infos']['bCurrent'] = true;
        } else {
            $aCurrentCat['infos']['bCurrent'] = false;
        }

        // define classname with default cat id
        $aCurrentCat['infos']['mDefaultCat'] = ($iDefaultId === null) ? 'default' : $iCurrentIndex;

        $_aFormatCat[] = $aCurrentCat['infos'];

        if (isset($aCategories[$iCurrentIndex])) {
            foreach ($aCategories[$iCurrentIndex] as $iCatId => $aCat) {
                if ($iCatId != 'infos') {
                    self::recursiveCategoryTree($aCategories, $aIndexedCat, $aCategories[$iCurrentIndex][$iCatId], $iCatId);
                }
            }
        }

        return $_aFormatCat;
    }

    /**
     * process brands to generate tree of them
     *
     * @param array $aBrands
     * @param array $aIndexedBrands
     *
     * @return array
     */
    public static function recursiveBrandTree(array $aBrands, array $aIndexedBrands)
    {
        // set
        $aFormatBrands = [];

        foreach ($aBrands as $iIndex => $aBrand) {
            $aFormatBrands[] = [
                'id' => $aBrand['id_manufacturer'],
                'name' => $aBrand['name'],
                'checked' => (in_array($aBrand['id_manufacturer'], $aIndexedBrands) ? true : false),
            ];
        }

        return $aFormatBrands;
    }

    /**
     * process suppliers to generate tree of them
     *
     * @param array $aSuppliers
     * @param array $aIndexedSuppliers
     *
     * @return array
     */
    public static function recursiveSupplierTree(array $aSuppliers, array $aIndexedSuppliers)
    {
        // set
        $aFormatSuppliers = [];

        foreach ($aSuppliers as $iIndex => $aSupplier) {
            $aFormatSuppliers[] = [
                'id' => $aSupplier['id_supplier'],
                'name' => $aSupplier['name'],
                'checked' => (in_array($aSupplier['id_supplier'], $aIndexedSuppliers) ? true : false),
            ];
        }

        return $aFormatSuppliers;
    }

    /**
     * round on numeric
     *
     * @param float $fVal
     * @param int $iPrecision
     *
     * @return float
     */
    public static function round($fVal, $iPrecision = 2)
    {
        if (method_exists('Tools', 'ps_round')) {
            $fVal = \Tools::ps_round((float) $fVal, $iPrecision);
        } else {
            $fVal = round((float) $fVal, $iPrecision);
        }

        return $fVal;
    }

    /**
     * set host
     *
     * @return string
     */
    public static function setHost()
    {
        if (\Configuration::get('PS_SHOP_DOMAIN') != false) {
            $sURL = 'http://' . \Configuration::get('PS_SHOP_DOMAIN');
        } else {
            $sURL = 'http://' . $_SERVER['HTTP_HOST'];
        }

        return $sURL;
    }

    /**
     * getBaseLink
     *
     * @return string
     */
    public static function getBaseLink()
    {
        static $baseLink = null;
        if ($baseLink === null) {
            $context = \Context::getContext();
            $force_ssl = (\Configuration::get('PS_SSL_ENABLED') && \Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            $ssl = $force_ssl;
            $base = (($ssl && \Configuration::get('PS_SSL_ENABLED')) ? 'https://' . $context->shop->domain_ssl : 'http://' . $context->shop->domain);
            $baseLink = $base . $context->shop->getBaseURI();
        }

        return $baseLink;
    }

    /**
     * set the XML file's prefix
     *
     * @return string
     */
    public static function setXmlFilePrefix()
    {
        return 'gmerchantcenterpro' . \GMerchantCenterPro::$conf['GMCP_FEED_TOKEN'];
    }

    /**
     * clear all generated files
     *
     * @return mixed
     */
    public static function cleanUpFiles()
    {
        foreach (\GMerchantCenterPro::$aAvailableLanguages as $aLanguage) {
            // get each countries by language
            $aCountries = moduleConfiguration::GMCP_AVAILABLE_COUNTRIES[$aLanguage['iso_code']];

            foreach ($aCountries as $sCountry => $aLocaleData) {
                // detect file's suffix and clear file
                $fileSuffix = self::buildFileSuffix($aLanguage['iso_code'], $sCountry, 'product');
                @unlink(moduleConfiguration::GMCP_SHOP_PATH_ROOT . \GMerchantCenterPro::$sFilePrefix . '.' . $fileSuffix . '.xml');

                $fileSuffixStock = self::buildFileSuffix($aLanguage['iso_code'], $sCountry, 'stock');
                @unlink(moduleConfiguration::GMCP_SHOP_PATH_ROOT . \GMerchantCenterPro::$sFilePrefix . '.' . $fileSuffixStock . '.xml');

                $fileSuffixReviews = self::buildFileSuffix($aLanguage['iso_code'], $sCountry, 'reviews');
                @unlink(moduleConfiguration::GMCP_SHOP_PATH_ROOT . \GMerchantCenterPro::$sFilePrefix . '.' . $fileSuffixReviews . '.xml');
            }
        }
    }

    /**
     * Build file suffix based on language and country ISO code
     *
     * @param string $sLangIso
     * @param string $sCountryIso
     * @param int $iShopId
     *
     * @return string
     */
    public static function buildFileSuffix($sLangIso, $sCountryIso, $sCurrency, $iShopId = 0, $sType = null)
    {
        if (\Tools::strtolower($sLangIso) == \Tools::strtolower($sCountryIso)) {
            $sSuffix = \Tools::strtolower($sLangIso);
        } else {
            $sSuffix = \Tools::strtolower($sLangIso) . '.' . \Tools::strtolower($sCountryIso);
        }

        $sSuffix .= '.' . $sCurrency;
        $sSuffix .= ($iShopId ? '.shop' . $iShopId : '.shop' . (int) \Context::getContext()->shop->id);

        if (!empty($sType)) {
            $sSuffix .= '.' . (string) $sType;
        }

        return $sSuffix;
    }

    /**
     * returns all available condition
     */
    public static function getConditionType()
    {
        return [
            'new' => \GMerchantCenterPro::$oModule->l('New', 'moduleTools'),
            'used' => \GMerchantCenterPro::$oModule->l('Used', 'moduleTools'),
            'refurbished' => \GMerchantCenterPro::$oModule->l('Refurbished', 'moduleTools'),
        ];
    }

    /**
     *returns all available description
     */
    public static function getDescriptionType()
    {
        return [
            1 => \GMerchantCenterPro::$oModule->l('Short description', 'moduleTools'),
            2 => \GMerchantCenterPro::$oModule->l('Long description', 'moduleTools'),
            3 => \GMerchantCenterPro::$oModule->l('Both', 'moduleTools'),
            4 => \GMerchantCenterPro::$oModule->l('Meta-description', 'moduleTools'),
        ];
    }

    /**
     * set all available attributes managed in google flux
     */
    public static function loadGoogleTags()
    {
        return [
            '_no_available_for_order' => [
                'label' => \GMerchantCenterPro::$oModule->l('Product not available for order', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('Products not exported because you don\'t allow them to be ordered when out of stock', 'moduleTools') . '.',
                'faq_id' => 237,
                'anchor' => '',
            ],
            '_no_product_name' => [
                'label' => \GMerchantCenterPro::$oModule->l('Missing product name', 'moduleTools'),
                'type' => 'error',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('Products not exported because the product names are missing', 'moduleTools') . '.',
                'faq_id' => 210,
                'anchor' => '',
            ],
            '_no_required_data' => [
                'label' => \GMerchantCenterPro::$oModule->l('Missing mandatory information', 'moduleTools'),
                'type' => 'error',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('Products not exported because one of this mandatory product information is missing: name / description / link / image link', 'moduleTools') . '.',
                'faq_id' => 0,
                'anchor' => '',
            ],
            '_no_export_no_supplier_ref' => [
                'label' => \GMerchantCenterPro::$oModule->l('Product without MPN', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('Products not exported because they do not have a MPN reference', 'moduleTools') . '.',
                'faq_id' => 198,
                'anchor' => '',
            ],
            '_no_export_no_ean_upc' => [
                'label' => \GMerchantCenterPro::$oModule->l('Product without GTIN code (UPC, EAN13/JAN or ISBN)', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('Products not exported because they do not have a GTIN code (UPC, EAN13/JAN or ISBN)', 'moduleTools') . '.',
                'faq_id' => 192,
                'anchor' => '',
            ],
            '_no_export_no_stock' => [
                'label' => \GMerchantCenterPro::$oModule->l('No stock', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('Products not exported because they are out of stock', 'moduleTools') . '.',
                'faq_id' => 22,
                'anchor' => '',
            ],
            '_no_export_min_price' => [
                'label' => \GMerchantCenterPro::$oModule->l('Product under min price', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('Products not exported because their price is lower than the minimum value defined in the configuration', 'moduleTools') . '.',
                'faq_id' => 22,
                'anchor' => '',
            ],
            // Product exported but missing information
            'excluded' => [
                'label' => \GMerchantCenterPro::$oModule->l('Excluded product list', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('this product or combination has been excluded from your feed as you defined it in the exclusion rules tab', 'moduleTools') . '.',
                'faq_id' => 22,
                'anchor' => '',
            ],
            'id' => [
                'label' => \GMerchantCenterPro::$oModule->l('Missing product ID', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => \GMerchantCenterPro::$oModule->l('The "ID" tag => This is the unique identifier of the item', 'moduleTools') . '.',
                'faq_id' => 194,
                'anchor' => 'prod_id',
            ],
            'title' => [
                'label' => \GMerchantCenterPro::$oModule->l('Missing product title', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => \GMerchantCenterPro::$oModule->l('The "TITLE" tag => This is the title of the item', 'moduleTools') . '.',
                'faq_id' => 210,
                'anchor' => 'title',
            ],
            'description' => [
                'label' => \GMerchantCenterPro::$oModule->l('Missing product description', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => \GMerchantCenterPro::$oModule->l('The "DESCRIPTION" tag => This is the description of the item', 'moduleTools') . '.',
                'faq_id' => 196,
                'anchor' => 'prod_description',
            ],
            'google_product_category' => [
                'label' => \GMerchantCenterPro::$oModule->l('No Google category', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "GOOGLE PRODUCT CATEGORY" tag => You have to associate each product default category with an official Google category', 'moduleTools') . '.',
                'faq_id' => 212,
                'anchor' => 'google_category',
            ],
            'product_type' => [
                'label' => \GMerchantCenterPro::$oModule->l('No product type', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "PRODUCT TYPE" tag => Unlike the "Google Product Category" tag, the "Product Type" tag contains the information about the category of the product according to your own classification', 'moduleTools') . '.',
                'faq_id' => 211,
                'anchor' => 'prod_type',
            ],
            'link' => [
                'label' => \GMerchantCenterPro::$oModule->l('Missing product link', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => \GMerchantCenterPro::$oModule->l('The "LINK" tag => This is the link of the item', 'moduleTools') . '.',
                'faq_id' => 204,
                'anchor' => 'prod_link',
            ],
            'image_link' => [
                'label' => \GMerchantCenterPro::$oModule->l('Missing image link', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => \GMerchantCenterPro::$oModule->l('The "IMAGE LINK" tag => This is the URL of the main image of the product', 'moduleTools') . '.',
                'faq_id' => 203,
                'anchor' => 'image_link',
            ],
            'condition' => [
                'label' => \GMerchantCenterPro::$oModule->l('Missing product condition', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => \GMerchantCenterPro::$oModule->l('The "CONDITION" tag => This is the condition of the item', 'moduleTools') . '.',
                'faq_id' => 195,
                'anchor' => 'prod_condition',
            ],
            'availability' => [
                'label' => \GMerchantCenterPro::$oModule->l('Missing product availability', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => \GMerchantCenterPro::$oModule->l('The "AVAILABILITY" tag => This indicates the availability of the item', 'moduleTools') . '.',
                'faq_id' => 213,
                'anchor' => 'prod_availability',
            ],
            'price' => [
                'label' => \GMerchantCenterPro::$oModule->l('Missing product price', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => \GMerchantCenterPro::$oModule->l('The "PRICE" tag => This is the price of the item', 'moduleTools') . '.',
                'faq_id' => 190,
                'anchor' => 'prod_price',
            ],
            'gtin' => [
                'label' => \GMerchantCenterPro::$oModule->l('No GTIN code', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "GTIN" tag => The "Global Trade Item Number" is one of the Unique Product Identifiers', 'moduleTools') . '.',
                'faq_id' => 192,
                'anchor' => 'prod_gtin',
            ],
            'brand' => [
                'label' => \GMerchantCenterPro::$oModule->l('No product brand', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "BRAND" tag => The product brand is one of the Unique Product Identifiers', 'moduleTools') . '.',
                'faq_id' => 197,
                'anchor' => 'prod_brand',
            ],
            'mpn' => [
                'label' => \GMerchantCenterPro::$oModule->l('No MPN reference', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "MPN tag=> The "Manufacturer Part Number" of a product is one of the Unique Product Identifiers', 'moduleTools') . '.',
                'faq_id' => 198,
                'anchor' => 'prod_mpn',
            ],
            'adult' => [
                'label' => \GMerchantCenterPro::$oModule->l('No adult tag', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "ADULT" tag => This tag indicates that the item is for adults only', 'moduleTools') . '.',
                'faq_id' => 222,
                'anchor' => 'adult',
            ],
            'gender' => [
                'label' => \GMerchantCenterPro::$oModule->l('No gender tag', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "GENDER" tag => This tag allows you specify the gender of the people for whom your product is dedicated', 'moduleTools') . '.',
                'faq_id' => 209,
                'anchor' => 'gender',
            ],
            'age_group' => [
                'label' => \GMerchantCenterPro::$oModule->l('No age group tag', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "AGE GROUP" tag => This tag allows you to specify the age group of people for whom your product is dedicated', 'moduleTools') . '.',
                'faq_id' => 202,
                'anchor' => 'age_group',
            ],
            'color' => [
                'label' => \GMerchantCenterPro::$oModule->l('No color', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "COLOR" tag => This tag indicates the color of the item', 'moduleTools') . '.',
                'faq_id' => 199,
                'anchor' => 'size_color',
            ],
            'size' => [
                'label' => \GMerchantCenterPro::$oModule->l('No size', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "SIZE" tag => This tag indicates the size of the item', 'moduleTools') . '.',
                'faq_id' => 201,
                'anchor' => 'size_color',
            ],
            'sizeType' => [
                'label' => \GMerchantCenterPro::$oModule->l('No size type', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "SIZE TYPE" tag => This tag allows you to give an additional information about clothing size', 'moduleTools') . '.',
                'faq_id' => 220,
                'anchor' => 'sizeTyp',
            ],
            'sizeSystem' => [
                'label' => \GMerchantCenterPro::$oModule->l('No size system', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "SIZE SYSTEM" tag => This tag allows you to indicate which country’s sizing system you use for the item', 'moduleTools') . '.',
                'faq_id' => 221,
                'anchor' => 'sizeTyp',
            ],
            'material' => [
                'label' => \GMerchantCenterPro::$oModule->l('No material tag', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "MATERIAL" tag => This tag indicates the material the item is made from', 'moduleTools') . '.',
                'faq_id' => 205,
                'anchor' => 'pattern',
            ],
            'pattern' => [
                'label' => \GMerchantCenterPro::$oModule->l('No pattern tag', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "PATTERN" tag => This tag indicates the pattern or graphic print on the item', 'moduleTools') . '.',
                'faq_id' => 206,
                'anchor' => 'pattern',
            ],
            'energy' => [
                'label' => \GMerchantCenterPro::$oModule->l('No energy tag', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "ENERGY EFFICIENCY CLASS" tag => This tag indicates the energy efficiency class of the item', 'moduleTools') . '.',
                'faq_id' => 232,
                'anchor' => '',
            ],
            'shipping_label' => [
                'label' => \GMerchantCenterPro::$oModule->l('No shipping label', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "SHIPPING LABEL" tag => If you want to set different shipping costs for specific groups of items, use this tag to apply a label to the items', 'moduleTools') . '.',
                'faq_id' => 235,
                'anchor' => '',
            ],
            'unit_pricing_measure' => [
                'label' => \GMerchantCenterPro::$oModule->l('No unit pricing measure', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "UNIT PRICING MEASURE" tag => This tag represents the total quantity or dimension of your item', 'moduleTools') . '.',
                'faq_id' => 241,
                'anchor' => '',
            ],
            'unit_pricing_base_measure' => [
                'label' => \GMerchantCenterPro::$oModule->l('No unit pricing base measure', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "UNIT PRICING BASE MEASURE" tag => This tag matches the volume / surface / dimension etc... your users will have to consider as a reference value', 'moduleTools') . '.',
                'faq_id' => 241,
                'anchor' => '',
            ],
            'item_group_id' => [
                'label' => \GMerchantCenterPro::$oModule->l('No item group id', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "ITEM GROUP ID" tag => All items that are variants of a same product must have the same item group id', 'moduleTools') . '.',
                'faq_id' => 0,
                'anchor' => '',
            ],
            'shipping_weight' => [
                'label' => \GMerchantCenterPro::$oModule->l('No information on package weight', 'moduleTools'),
                'type' => 'warning',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('The "SHIPPING WEIGHT" tag => This is the weight of the item used to calculate the shipping cost of the item', 'moduleTools') . '.',
                'faq_id' => 214,
                'anchor' => 'shipping_weight',
            ],
            'shipping' => [
                'label' => \GMerchantCenterPro::$oModule->l('Missing shipping information', 'moduleTools'),
                'type' => 'error',
                'mandatory' => true,
                'msg' => \GMerchantCenterPro::$oModule->l('The "SHIPPING" tag => The shipping tag lets you override shipping information for specific items', 'moduleTools') . '.',
                'faq_id' => 51,
                'anchor' => '',
            ],
            // Product exported which do not respect Google prerequisites
            'title_length' => [
                'label' => \GMerchantCenterPro::$oModule->l('Too long title', 'moduleTools'),
                'type' => 'notice',
                'mandatory' => false,
                'msg' => \GMerchantCenterPro::$oModule->l('Google requires your product titles to be no more than 150 characters long', 'moduleTools') . '.',
                'faq_id' => 210,
                'anchor' => '',
            ],
        ];
    }

    /**
     * returns the Google taxonomy file's content
     *
     * @param string $sUrl
     *
     * @return string
     */
    public static function getGoogleFile($sUrl)
    {
        $sContent = false;

        // Let's try first with file_get_contents
        if (ini_get('allow_url_fopen')) {
            $sContent = (method_exists(
                'Tools',
                'file_get_contents'
            ) ? \Tools::file_get_contents($sUrl) : file_get_contents($sUrl));
        }

        // Returns false ? Try with CURL if available
        if ($sContent === false && function_exists('curl_init')) {
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $sUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_VERBOSE => true,
            ]);

            $sContent = @curl_exec($ch);
            curl_close($ch);
        }

        // Will return false if no method is available, or if either fails
        // This will cause a JavaScript alert to be triggered by the AJAX call
        return $sContent;
    }

    /**
     * method returns the generated report files
     *
     * @return array
     */
    public static function getGeneratedReport()
    {
        $reporting_output = [];
        $reportingList = Reporting::getReportingList((int) \Context::getContext()->shop->id);

        if (!empty($reportingList)) {
            foreach ($reportingList as $list) {
                $reporting_data = explode('_', $list['iso_feed']);

                $id_lang = \Language::getIdByIso($reporting_data[0]);
                $language = new \Language((int) $id_lang);

                $id_currency = \Currency::getIdByIsoCode($reporting_data[2]);
                $currency = new \Currency($id_currency);

                $id_country = \Country::getByIso(\Tools::strtolower($reporting_data[1]));
                $country = new \Country((int) $id_country);

                $reporting_output[] = [
                    'full' => $reporting_data[0] . '_' . $reporting_data[1] . '_' . $reporting_data[2],
                    'lang_iso' => $language->name . ' - ' . \Tools::strtoupper($language->iso_code),
                    'currency' => $currency->sign . ' - ' . $currency->iso_code,
                    'country' => \Country::getNameById(\GMerchantCenterPro::$iCurrentLang, $country->id) . ' - ' . $country->iso_code,
                ];
            }
        }

        return $reporting_output;
    }

    /**
     * format the product title by uncap or not or leave uppercase only first character of each word
     *
     * @param string $sTitle
     * @param int $iFormatMode
     *
     * @return string
     */
    public static function formatProductTitle($sTitle, $iFormatMode = 0)
    {
        $sResult = '';

        // format title
        if ($iFormatMode == 0) {
            $sResult = self::strToUtf8($sTitle);
        } else {
            $sResult = self::strToLowerUtf8($sTitle);

            if ($iFormatMode == 1) {
                $aResult = explode(' ', $sResult);

                foreach ($aResult as &$sWord) {
                    $sWord = \Tools::ucfirst(trim($sWord));
                }

                $sResult = implode(' ', $aResult);
            } else {
                $sResult = \Tools::ucfirst(trim($sResult));
            }
        }

        return $sResult;
    }

    /**
     * uncap the product title
     *
     * @param int $iAdvancedProdName
     * @param string $sProdName
     * @param string $sCatName
     * @param string $sManufacturerName
     * @param int $iLength
     * @param int $iLangId
     * @param string $sPrefix
     * @param string $sSuffix
     *
     * @return string
     */
    public static function truncateProductTitle($iAdvancedProdName, $sProdName, $sCatName, $sManufacturerName, $iLength, $iLangId, $sPrefix, $sSuffix)
    {
        if (function_exists('mb_substr')) {
            switch ($iAdvancedProdName) {
                case 0:
                    $sProdName = mb_substr($sProdName, 0, $iLength);

                    break;
                case 1:
                    $sProdName = mb_substr($sCatName . ' - ' . $sProdName, 0, $iLength);

                    break;
                case 2:
                    $sProdName = mb_substr($sProdName . ' - ' . $sCatName, 0, $iLength);

                    break;
                case 3:
                    $sBrand = !empty($sManufacturerName) ? $sManufacturerName . ' - ' : '';
                    $sProdName = mb_substr($sBrand . $sProdName, 0, $iLength);

                    break;
                case 4:
                    $sBrand = !empty($sManufacturerName) ? ' - ' . $sManufacturerName : '';
                    $sProdName = mb_substr($sProdName . $sBrand, 0, $iLength);

                    break;
                case 5:
                    $aPrefix = moduleTools::handleGetConfigurationData($sPrefix);
                    $aSuffix = moduleTools::handleGetConfigurationData($sSuffix);

                    // Use case for prefix
                    if (!empty($sPrefix)) {
                        $sProdName = $aPrefix[$iLangId] . ' ' . $sProdName;
                    }

                    // Use case for suffix
                    if (!empty($sSuffix)) {
                        $sProdName = $sProdName . ' ' . $aSuffix[$iLangId];
                    }

                    break;
                default:
                    break;
            }
        }

        return stripslashes($sProdName);
    }

    /**
     * Used by uncapProductTitle. strtolower doesn't work with UTF-8
     * The second solution if no mb_strtolower available is not perfect but will work
     * with most European languages. Worse comes to worse, the person may chose not to uncap
     *
     * @param $sString
     *
     * @return string
     */
    public static function strToLowerUtf8($sString)
    {
        return function_exists('mb_strtolower') ? mb_strtolower($sString, 'utf-8') : utf8_encode(\Tools::strtolower(utf8_decode($sString)));
    }

    /**
     * Used by uncapProductTitle. strToUtf8 doesn't work with UTF-8
     * The second solution if no mb_convert_encoding available is not perfect but will work
     * with most European languages. Worse comes to worse, the person may chose not to uncap
     *
     * @param $sString
     *
     * @return string
     */
    public static function strToUtf8($sString)
    {
        return function_exists('mb_convert_encoding') ? mb_convert_encoding($sString, 'utf-8') : utf8_encode(utf8_decode($sString));
    }

    /**
     * Check file based on language and country ISO code
     *
     * @param string $sIsoLang
     * @param string $sIsoCountry
     * @param string $sType
     *
     * @return bool
     */
    public static function checkReportFile($sIsoLang, $sIsoCountry, $sType, $sCurrencyIso)
    {
        $sFilename = moduleConfiguration::GMCP_REPORTING_DIR . 'reporting-' . $sIsoLang . '-' . \Tools::strtolower($sIsoCountry) . '-' . $sCurrencyIso . '-' . $sType . '.txt';

        return (file_exists($sFilename) && filesize($sFilename)) ? true : false;
    }

    /**
     * clean up MS Word style quotes and other characters Google does not like
     *
     * @param string $str
     *
     * @return string
     */
    public static function cleanUp($str)
    {
        $str = str_replace('<br>', "\n", $str);
        $str = str_replace('<br />', "\n", $str);
        $str = str_replace('</p>', "\n", $str);
        $str = str_replace('<p>', '', $str);
        $str = str_replace('©', '', $str);
        $str = str_replace('&copy;', '', $str);

        $quotes = [
            "\xC2\xAB" => '"', // « (U+00AB) in UTF-8
            "\xC2\xBB" => '"', // » (U+00BB) in UTF-8
            "\xE2\x80\x98" => "'", // ‘ (U+2018) in UTF-8
            "\xE2\x80\x99" => "'", // ’ (U+2019) in UTF-8
            "\xE2\x80\x9A" => "'", // ‚ (U+201A) in UTF-8
            "\xE2\x80\x9B" => "'", // ‛ (U+201B) in UTF-8
            "\xE2\x80\x9C" => '"', // “ (U+201C) in UTF-8
            "\xE2\x80\x9D" => '"', // ” (U+201D) in UTF-8
            "\xE2\x80\x9E" => '"', // „ (U+201E) in UTF-8
            "\xE2\x80\x9F" => '"', // ‟ (U+201F) in UTF-8
            "\xE2\x80\xB9" => "'", // ‹ (U+2039) in UTF-8
            "\xE2\x80\xBA" => "'", // › (U+203A) in UTF-8
            "\xE2\x80\x94" => '-', // —
        ];

        $str = strtr($str, $quotes);

        return trim(strip_tags($str));
    }

    /**
     * removed accent from a string
     *
     * @param string $str
     *
     * @return string
     */
    public static function removeAccent($str)
    {
        return preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($str, ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Clean up no valid letter for review feed and clean the HTTP and HTTPS because this is forbidden with
     * Google data feed review
     *
     * @param string $sReview
     *
     * @return string
     */
    public static function cleanUpReview($sReview)
    {
        $sReview = str_replace('&', '', $sReview);
        // remove all kind of link from review text
        $sReview = preg_replace('/\b((https?|ftp|file):\/\/|www\.)[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', ' ', $sReview);

        return trim(strip_tags($sReview));
    }

    /**
     * format the date for Google prerequisistes
     *
     * @param string $sDate
     *
     * @return string
     */
    public static function formatDateISO8601($sDate)
    {
        $sDate = new \DateTime($sDate);

        return $sDate->format(\DateTime::ISO8601);
    }

    /**
     * format the date for Google reviews feed
     *
     * @param string $sDate
     *
     * @return string
     */
    public static function formatDateReviews($sDate)
    {
        $sDate = new \DateTime($sDate);

        return $sDate->format(\DateTime::W3C);
    }

    /**
     * format the long title for Google promotion feed long title
     *
     * @param string $sText
     *
     * @return string
     */
    public static function formatTextForGoogle($sText)
    {
        foreach (moduleConfiguration::GMCP_FORBIDDEN_STRING as $sKey => $sForbidden) {
            $sText = str_replace((string) $sForbidden['sToReplace'], (string) $sForbidden['sReplaceBy'], $sText);
        }

        $sText = substr($sText, 0, 60);

        return $sText;
    }

    /**
     * format the product name with combination
     *
     * @param int $iAttrId
     * @param int $iCurrentLang
     * @param int $iShopId
     *
     * @return mixed
     */
    public static function getProductCombinationName($iAttrId, $iCurrentLang, $iShopId)
    {
        // Use case to add or not combination data
        if (!empty(\GMerchantCenterPro::$conf['GMCP_INCL_ATTR_VALUE'])) {
            // set var
            $sProductName = '';
            $aCombinations = moduleDao::getProductComboAttributes($iAttrId, $iCurrentLang, $iShopId);

            if (!empty($aCombinations)) {
                $sExtraName = '';
                foreach ($aCombinations as $c) {
                    $sExtraName .= ' ' . stripslashes($c['name']);
                }
                $sProductName .= $sExtraName;
            }

            return (string) $sProductName;
        }
    }

    /**
     * detect if we use price tax or not for the specific feed
     *
     * @param string $sLangIso
     * @param string $sCountryIso
     *
     * @return bool
     */
    public static function isTax($sLangIso, $sCountryIso)
    {
        // handle tax and shipping fees
        $aFeedTax = (!empty(\GMerchantCenterPro::$conf['GMCP_FEED_TAX']) ? \GMerchantCenterPro::$conf['GMCP_FEED_TAX'] : []);

        // handle price with tax or not
        if (!empty($aFeedTax)) {
            $bUseTax = array_key_exists(
                \Tools::strtolower($sLangIso) . '_' . \Tools::strtoupper($sCountryIso),
                $aFeedTax
            ) ? $aFeedTax[\Tools::strtolower($sLangIso) . '_' . \Tools::strtoupper($sCountryIso)] : 1;
        } else {
            $bUseTax = 1;
        }

        return $bUseTax;
    }

    /**
     * check the gtin value
     *
     * @param string $sPriority the priority
     * @param array $aProduct the product information
     *
     * @return string
     */
    public static function getGtin($sPriority, $aProduct)
    {
        $sGtin = '';

        if ($sPriority == 'ean') {
            if (
                !empty($aProduct['ean13'])
                && (\Tools::strlen($aProduct['ean13']) == 8
                    || \Tools::strlen($aProduct['ean13']) == 12
                    || \Tools::strlen($aProduct['ean13']) == 13)
            ) {
                $sGtin = $aProduct['ean13'];
            } elseif (
                !empty($aProduct['upc'])
                && (\Tools::strlen($aProduct['upc']) == 8
                    || \Tools::strlen($aProduct['upc']) == 12
                    || \Tools::strlen($aProduct['upc']) == 13)
            ) {
                $sGtin = $aProduct['upc'];
            }
        } elseif ($sPriority == 'upc') {
            if (
                !empty($aProduct['upc'])
                && (\Tools::strlen($aProduct['upc']) == 8
                    || \Tools::strlen($aProduct['upc']) == 12
                    || \Tools::strlen($aProduct['upc']) == 13)
            ) {
                $sGtin = $aProduct['upc'];
            } elseif (
                !empty($aProduct['ean13'])
                && (\Tools::strlen($aProduct['ean13']) == 8
                    || \Tools::strlen($aProduct['ean13']) == 12
                    || \Tools::strlen($aProduct['ean13']) == 13)
            ) {
                $sGtin = $aProduct['ean13'];
            }
        } elseif ($sPriority == 'isbn') {
            if (!empty($aProduct['isbn']) && \Tools::strlen($aProduct['isbn']) == 13) {
                $sGtin = $aProduct['isbn'];
            } elseif (
                !empty($aProduct['ean13'])
                && (\Tools::strlen($aProduct['ean13']) == 8
                    || \Tools::strlen($aProduct['ean13']) == 12
                    || \Tools::strlen($aProduct['ean13']) == 13)
            ) {
                $sGtin = $aProduct['ean13'];
            }
        }

        return $sGtin;
    }

    /**
     * check if multi-shop is activated and if the group or global context is used
     *
     * @return bool
     */
    public static function checkGroupMultiShop()
    {
        return \Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && empty(\GMerchantCenterPro::$oCookie->shopContext);
    }

    /**
     * cleanUpPrefix remove special caracters from the prefix
     *
     * @param string $sPrefix
     *
     * @return string
     */
    public static function cleanUpPrefix($sPrefix)
    {
        $sPrefix = str_replace('<br>', "\n", $sPrefix);
        $sPrefix = str_replace('<br />', "\n", $sPrefix);
        $sPrefix = str_replace('</p>', "\n", $sPrefix);
        $sPrefix = str_replace('<p>', '', $sPrefix);
        $sPrefix = str_replace('&', '', $sPrefix);

        $quotes = [
            "\xC2\xAB" => '"', // « (U+00AB) in UTF-8
            "\xC2\xBB" => '"', // » (U+00BB) in UTF-8
            "\xE2\x80\x98" => "'", // ‘ (U+2018) in UTF-8
            "\xE2\x80\x99" => "'", // ’ (U+2019) in UTF-8
            "\xE2\x80\x9A" => "'", // ‚ (U+201A) in UTF-8
            "\xE2\x80\x9B" => "'", // ‛ (U+201B) in UTF-8
            "\xE2\x80\x9C" => '"', // “ (U+201C) in UTF-8
            "\xE2\x80\x9D" => '"', // ” (U+201D) in UTF-8
            "\xE2\x80\x9E" => '"', // „ (U+201E) in UTF-8
            "\xE2\x80\x9F" => '"', // ‟ (U+201F) in UTF-8
            "\xE2\x80\xB9" => "'", // ‹ (U+2039) in UTF-8
            "\xE2\x80\xBA" => "'", // › (U+203A) in UTF-8
            "\xE2\x80\x94" => '-', // —
        ];

        $sPrefix = strtr($sPrefix, $quotes);

        return (string) trim(strip_tags($sPrefix));
    }

    /**
     * getExclusionRulesName()
     *
     * @param array $aExclusionRules the rules
     *
     * @return array
     */
    public static function getExclusionRulesName($aExclusionRules)
    {
        // Array to format th;e values with good value
        $aData = $aExclusionRules;

        foreach ($aExclusionRules as $sKey => $sValue) {
            $aTmpData = moduleTools::handleGetConfigurationData($sValue['exclusion_value']);

            if ($sValue['type'] !== null) {
                switch ($sValue['type']) {
                    case 'word':
                        if (isset($aTmpData['exclusionData'])) {
                            $aData[$sKey]['exclusion_value_text'] = $aTmpData['exclusionData'];
                        }

                        break;
                    case 'feature':
                        $aFeature = \FeatureValue::getFeatureValuesWithLang(\GMerchantCenterPro::$iCurrentLang, (int) $aTmpData['exclusionOn']);
                        foreach ($aFeature as $sFeature) {
                            if (
                                $sFeature['id_feature_value'] == (int) $aTmpData['exclusionData']
                            ) {
                                $aData[$sKey]['exclusion_value_text'] = $sFeature['value'];
                            }
                        }

                        break;
                    case 'attribute':
                        $aAttribute = \AttributeGroup::getAttributes(\GMerchantCenterPro::$iCurrentLang, (int) $aTmpData['exclusionOn']);

                        foreach ($aAttribute as $sAttribute) {
                            if (
                                $sAttribute['id_attribute'] == (int) $aTmpData['exclusionData']
                            ) {
                                $aData[$sKey]['exclusion_value_text'] = $sAttribute['name'];
                            }
                        }

                        break;
                    default:
                        $sType = '';

                        break;
                }
                unset($aTmpData);
                unset($aFeature);
                unset($aAttribute);
            }
        }

        return $aData;
    }

    /**
     * get the FAQ lang
     *
     * @param string $sLangIso
     */
    public static function getFaqLang($sLangIso)
    {
        $sLang = '';

        if ($sLangIso == 'en' || $sLangIso == 'fr') {
            $sLang = $sLangIso;
        } else {
            $sLang = 'en';
        }

        return $sLang;
    }

    /**
     * Sanitize product properties formatted as array instead of a string matching to the current language
     *
     * @param $property
     * @param $iLangId
     *
     * @return mixed|string
     */
    public static function sanitizeProductProperty($property, $iLangId)
    {
        $content = '';

        // check if the product name is an array
        if (is_array($property)) {
            if (count($property) == 1) {
                $content = reset($property);
            } elseif (isset($property[$iLangId])) {
                $content = $property[$iLangId];
            }
        } else {
            $content = $property;
        }

        return $content;
    }

    /**
     * get the dimension in the good format you can check all data about this in https://support.google.com/merchants/answer/6324498?hl=en
     *
     * @param $width
     * @param $height
     * @param $length
     * @param $weight
     *
     * @return mixed
     */
    public static function getDimension($width, $height, $length, $weight = null)
    {
        $aDimension = [];

        // Only handle if unit is valid for Google
        if (in_array(\Tools::strtolower(\Configuration::get('PS_DIMENSION_UNIT')), moduleConfiguration::GMCP_DIMENSION_UNITS)) {
            // Convert the data
            $width = (int) number_format($width, 2, '.', '');
            $height = (int) number_format($height, 2, '.', '');
            $length = (int) number_format($length, 2, '.', '');

            // Use case for CM
            if (\Configuration::get('PS_DIMENSION_UNIT') == 'cm') {
                if ($width > 1 && $width <= 400 && $height > 1 && $height <= 400 && $length > 1 && $length <= 400) {
                    $aDimension['shipping_width'] = $width . ' ' . \Tools::strtolower(\Configuration::get('PS_DIMENSION_UNIT'));
                    $aDimension['shipping_height'] = $height . ' ' . \Tools::strtolower(\Configuration::get('PS_DIMENSION_UNIT'));
                    $aDimension['shipping_length'] = $length . ' ' . \Tools::strtolower(\Configuration::get('PS_DIMENSION_UNIT'));
                    $aDimension['product_width'] = $width . ' ' . \Tools::strtolower(\Configuration::get('PS_DIMENSION_UNIT'));
                    $aDimension['product_height'] = $height . ' ' . \Tools::strtolower(\Configuration::get('PS_DIMENSION_UNIT'));
                    $aDimension['product_length'] = $length . ' ' . \Tools::strtolower(\Configuration::get('PS_DIMENSION_UNIT'));

                    // Use case for the weight for the poduct data
                    if (!empty($weight)) {
                        $aDimension['product_weight'] = number_format($weight, 2, '.', '') . ' ' . \Tools::strtolower(\Configuration::get('PS_WEIGHT_UNIT'));
                    }
                }
            }

            // Use case for inch
            if (\Configuration::get('PS_DIMENSION_UNIT') == 'in') {
                if ($width > 1 && $width <= 150 && $height > 1 && $height <= 150 && $length > 1 && $length <= 150) {
                    $aDimension['shipping_width'] = $width . ' ' . \Tools::strtolower(\Configuration::get('PS_DIMENSION_UNIT'));
                    $aDimension['shipping_height'] = $height . ' ' . \Tools::strtolower(\Configuration::get('PS_DIMENSION_UNIT'));
                    $aDimension['shipping_length'] = $length . ' ' . \Tools::strtolower(\Configuration::get('PS_DIMENSION_UNIT'));
                    $aDimension['product_width'] = $width . ' ' . \Tools::strtolower(\Configuration::get('PS_DIMENSION_UNIT'));
                    $aDimension['product_height'] = $height . ' ' . \Tools::strtolower(\Configuration::get('PS_DIMENSION_UNIT'));
                    $aDimension['product_length'] = $length . ' ' . \Tools::strtolower(\Configuration::get('PS_DIMENSION_UNIT'));

                    // Use case for the weight for the poduct data
                    if (!empty($weight)) {
                        $aDimension['product_weight'] = number_format($weight, 2, '.', '') . ' ' . \Tools::strtolower(\Configuration::get('PS_WEIGHT_UNIT'));
                    }
                }
            }

            return $aDimension;
        }
    }

    /**
     * method return available countries supported by Google
     *
     * @return array
     */
    public static function getAvailableTaxonomyCountries()
    {
        $saved_taxonomies = Feeds::getSavedTaxonomies((int) (int) \Context::getContext()->shop->id);
        $shop_countries = \Country::getCountries((int) \GMerchantCenterPro::$oContext->cookie->id_lang, true);
        $taxonomies_output = [];

        if (!empty($saved_taxonomies)) {
            foreach ($saved_taxonomies as $data) {
                $id_country = \Country::getByIso(\Tools::strtolower($data['iso_country']));
                if (isset($shop_countries[$id_country])) {
                    $country = new \Country($id_country);
                    $taxonomies_output[$data['taxonomy']]['countries'][] = isset($country->name[\GMerchantCenterPro::$oContext->cookie->id_lang]) ? $country->name[\GMerchantCenterPro::$oContext->cookie->id_lang] : '';
                    $taxonomies_output[$data['taxonomy']]['id_lang'] = 1;
                }
            }
        }

        foreach ($taxonomies_output as $key => $data_output) {
            if (!empty($data_output['countries'])) {
                $taxonomies_output[$key]['countries'] = array_unique($data_output['countries']);
            }
        }

        return $taxonomies_output;
    }

    /**
     * returns available carriers for one country zone
     *
     * @param int $iCountryZone
     *
     * @return array
     */
    public static function getAvailableCarriers($iCountryZone)
    {
        return \Carrier::getCarriers((int) \GMerchantCenterPro::$oContext->cookie->id_lang, false, false, (int) $iCountryZone, null, 5);
    }

    /**
     * Handle the excluded word of the title
     *
     * @param $product_name
     *
     * @return mixed|string
     */
    public static function handleExcludedWords($product_name)
    {
        $excluded_words = json_decode(\GMerchantCenterPro::$conf['GMCP_EXCLUDED_WORDS'], true);
        $product_name_clean = $product_name;

        if (!empty($excluded_words) && is_array($excluded_words)) {
            foreach ($excluded_words as $word) {
                $product_name_clean = str_replace($word, '', $product_name_clean);
                $product_name_clean = str_replace(ucfirst($word), '', $product_name_clean);
                $product_name_clean = str_replace(strtoupper($word), '', $product_name_clean);
                $product_name_clean = str_replace(strtolower($word), '', $product_name_clean);
                $product_name_clean = str_replace('  ', ' ', $product_name_clean);
            }
        }

        return $product_name_clean;
    }

    /**
     *  Method build the product url for data feed according to the module feed options
     *
     * @param object $product
     * @param int $langId
     * @param int $currencyId
     * @param int $idShop
     * @param int $ipa
     *
     * @return string
     */
    public static function buildProductUrl($product, $langId, $currencyId, $idShop, $ipa = null)
    {
        $url = '';

        $product_category = new \Category((int) $product->getDefaultCategory(), (int) $langId);

        // Use to force the context and use the good attribute translation in anchor
        \Context::getContext()->language->id = (int) $langId;
        $addAnchor = \GMerchantCenterPro::$conf['GMCP_INCL_ANCHOR'];
        $useAttributeId = \GMerchantCenterPro::$conf['GMCP_URL_ATTR_ID_INCL'];
        $url = \Context::getContext()->link->getProductLink($product, null, \Tools::strtolower($product_category->link_rewrite), null, (int) $langId, (int) $idShop, (int) $ipa, false, false, $useAttributeId, [], $addAnchor);
        $urlExtractPart = '';
        // handle the advanced parameters
        // format the current URL with currency or Google campaign parameters
        if (!empty(\GMerchantCenterPro::$conf['GMCP_ADD_CURRENCY'])) {
            $urlExtractPart = substr($url, (strrpos($url, '#') ?: -1) + 1);
            $anchorPosition = strpos($url, '#');
            $url = str_replace('#' . $urlExtractPart, '', $url);
            $url .= (strpos($url, '?') !== false) ? '&SubmitCurrency=1&id_currency=' . (int) $currencyId : '?SubmitCurrency=1&id_currency=' . (int) $currencyId;
        }
        if (!empty(\GMerchantCenterPro::$conf['GMCP_UTM_CAMPAIGN'])) {
            $url .= (strpos($url, '?') !== false) ? '&utm_campaign=' . \GMerchantCenterPro::$conf['GMCP_UTM_CAMPAIGN'] : '?utm_campaign=' . \GMerchantCenterPro::$conf['GMCP_UTM_CAMPAIGN'];
        }
        if (!empty(\GMerchantCenterPro::$conf['GMCP_UTM_SOURCE'])) {
            $url .= (strpos($url, '?') !== false) ? '&utm_source=' . \GMerchantCenterPro::$conf['GMCP_UTM_SOURCE'] : '?utm_source=' . \GMerchantCenterPro::$conf['GMCP_UTM_SOURCE'];
        }
        if (!empty(\GMerchantCenterPro::$conf['GMCP_UTM_CAMPAIGN'])) {
            $url .= (strpos($url, '?') !== false) ? '&utm_medium=' . \GMerchantCenterPro::$conf['GMCP_UTM_MEDIUM'] : '?utm_medium=' . \GMerchantCenterPro::$conf['GMCP_UTM_MEDIUM'];
        }

        if (!empty($addAnchor) && !empty($anchorPosition)) {
            if (!empty($urlExtractPart)) {
                $url .= '#' . $urlExtractPart;
            }
        }

        return $url;
    }

    /**
     * Method check the taxonomies from others modules feed
     *
     * @param string $isoLang
     *
     * @return array
     */
    public static function getTaxonomiesToImport($isoLang)
    {
        $gmcTaxonomies = [];
        $gmcpTaxonomies = [];
        $fbdaTaxonomies = [];
        $tkpTaxonomies = [];

        $checkGmcTable = ' show tables like "' . _DB_PREFIX_ . 'gmc_taxonomy_categories"';
        if (!empty(\Db::getInstance()->executeS($checkGmcTable))) {
            $gmcQuery = new \DbQuery();
            $gmcQuery->select('*');
            $gmcQuery->from('gmc_taxonomy_categories', 'gtc');
            $gmcQuery->where('gtc.id_shop=' . (int) (int) \Context::getContext()->shop->id);
            $gmcQuery->where('gtc.lang="' . \pSQL($isoLang) . '"');

            $gmcTaxonomies = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($gmcQuery);
        }

        $checkGmcpTable = ' show tables like "' . _DB_PREFIX_ . 'gmcp_taxonomy_categories"';
        if (!empty(\Db::getInstance()->executeS($checkGmcpTable))) {
            $gmcpQuery = new \DbQuery();
            $gmcpQuery->select('*');
            $gmcpQuery->from('gmcp_taxonomy_categories', 'gtc');
            $gmcpQuery->where('gtc.id_shop=' . (int) (int) \Context::getContext()->shop->id);
            $gmcpQuery->where('gtc.lang="' . \pSQL($isoLang) . '"');

            $gmcpTaxonomies = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($gmcpQuery);
        }

        $checkFbdaTable = ' show tables like "' . _DB_PREFIX_ . 'fpa_taxonomy_categories"';

        if (!empty(\Db::getInstance()->executeS($checkFbdaTable))) {
            $fbdaQuery = new \DbQuery();
            $fbdaQuery->select('*');
            $fbdaQuery->from('fpa_taxonomy_categories', 'gtc');
            $fbdaQuery->where('gtc.id_shop=' . (int) (int) \Context::getContext()->shop->id);
            $fbdaQuery->where('gtc.lang="' . \pSQL($isoLang) . '"');

            $fbdaTaxonomies = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($fbdaQuery);
        }

        $checkTkpTable = ' show tables like "' . _DB_PREFIX_ . 'tkp_taxonomy_categories"';
        if (!empty(\Db::getInstance()->executeS($checkTkpTable))) {
            $tkpQuery = new \DbQuery();
            $tkpQuery->select('*');
            $tkpQuery->from('tkp_taxonomy_categories', 'gtc');
            $tkpQuery->where('gtc.id_shop=' . (int) (int) \Context::getContext()->shop->id);
            $tkpQuery->where('gtc.lang="' . \pSQL($isoLang) . '"');

            $tkpTaxonomies = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($tkpQuery);
        }

        return [
            'gmcTaxonomies' => $gmcTaxonomies,
            'gmcpTaxonomies' => $gmcpTaxonomies,
            'fpaTaxonomies' => $fbdaTaxonomies,
            'tkpTaxonomies' => $tkpTaxonomies,
        ];
    }

    /**
     * a cleaned desc string
     *
     * @param string $shortDesc
     * @param string $longDesc
     * @param string $metaDesc
     *
     * @return string
     */
    public static function getProductDesc($shortDesc, $longDesc, $metaDesc)
    {
        $sDesc = '';
        // set product description
        switch (\GMerchantCenterPro::$conf['GMCP_P_DESCR_TYPE']) {
            case 1:
                $sDesc = !empty($shortDesc) ? $shortDesc : '';

                break;
            case 2:
                $sDesc = !empty($longDesc) ? $longDesc : '';

                break;
            case 3:
                $sDesc = '';
                if (!empty($shortDesc)) {
                    $sDesc = $shortDesc;
                }
                if (!empty($longDesc)) {
                    $sDesc .= (!empty($sDesc) ? ' ' : '') . $longDesc;
                }

                break;
            case 4:
                $sDesc = !empty($metaDesc) ? $metaDesc : '';

                break;
            default:
                $sDesc = !empty($longDesc) ? $longDesc : '';

                break;
        }

        if (!empty($sDesc)) {
            $sDesc = \Tools::substr(moduleTools::cleanUp($sDesc), 0, 4999);
            strlen($sDesc) == 1 ? $sDesc = '' : '';
        }

        return $sDesc;
    }

    /**
     * method to choose how to construct and factorize product feed ids
     *
     * @param int $idProduct
     * @param string $country
     * @param string $xmlType
     * @param int $idProductAttribute
     * @param string $separator
     *
     * @return string
     */
    public static function constructFeedIdsBasic($idProduct, $country, $xmlType = null, $idProductAttribute = null, $separator = null)
    {
        $idOutput = '';
        $prefixId = '';

        if (empty(\GMerchantCenterPro::$conf['GMCP_SIMPLE_PROD_ID'])) {
            $prefixId = \Tools::strtoupper(\GMerchantCenterPro::$conf['GMCP_ID_PREFIX']) . \Tools::strtoupper($country);
        }

        if ($xmlType == 'combination') {
            $idOutput = $prefixId . $idProduct . $separator . $idProductAttribute;
        } elseif ($xmlType == 'product') {
            $idOutput = $prefixId . $idProduct;
        }

        return $idOutput;
    }

    /**
     * method to choose how to construct and factorize product feed ids
     *
     * @param int $idProduct
     * @param int $idLang
     * @param string $xmlType
     * @param int $idProductAttribute
     * @param string $separator
     * @param int $eanProduct
     * @param string $country
     *
     * @return string
     */
    public static function constructFeedIdsEan($idProduct, $idLang, $xmlType = null, $idProductAttribute = null, $separator = null, $eanProduct = null, $country = '')
    {
        $idOutput = '';

        if (
            !empty($eanProduct)
            && (\Tools::strlen($eanProduct) == 8
                || \Tools::strlen($eanProduct) == 12
                || \Tools::strlen($eanProduct) == 13)
        ) {
            $idOutput = $eanProduct;
        } else {
            $idOutput = moduleTools::constructFeedIdsBasic($idProduct, $country, $xmlType, $idProductAttribute, $separator);
        }

        return $idOutput;
    }

    /**
     * method to choose how to construct and factorize product feed ids
     *
     * @param int $idProduct
     * @param int $idLang
     * @param string $xmlType
     * @param int $idProductAttribute
     * @param string $separator
     * @param int $refProduct
     * @param string $country
     *
     * @return string
     */
    public static function constructFeedIdsRef($idProduct, $idLang, $xmlType = null, $idProductAttribute = null, $separator = null, $refProduct = null, $country = '')
    {
        $idOutput = '';

        if (!empty($refProduct)) {
            $idOutput = $refProduct;
        } else {
            $idOutput = moduleTools::constructFeedIdsBasic($idProduct, $country, $xmlType, $idProductAttribute, $separator);
        }

        return $idOutput;
    }

    /**
     * method get the delivery country for GCR tag
     *
     * @param int $idAddressDelivery
     *
     * @return mixed
     */
    public static function getDeliveryCountryCode($idAddressDelivery)
    {
        $deliveryAddress = new \Address((int) $idAddressDelivery);
        if (\Validate::isLoadedObject($deliveryAddress)) {
            $deliveryCountry = new \Country((int) $deliveryAddress->id_country, (int) \GMerchantCenterPro::$iCurrentLang);

            return $deliveryCountry->iso_code;
        }
    }

    /**
     * method get the estimated shipping date
     *
     * @param object $order
     *
     * @return mixed
     */
    public static function getEstimatedShippingDate($order)
    {
        if (!empty(\GMerchantCenterPro::$conf['GMCP_GCR_ACTIVATE'])) {
            // Set necessary variables
            $order_date_time = $order->date_add;
            $ship_date = '';
            if (!empty($order_date_time)) {
                list($order_date, $order_time) = explode(' ', $order_date_time);
                $tomorrow_date = self::nextDay($order_date);
                $cutoff_time = \GMerchantCenterPro::$conf['GMCP_CUT_OFF_HOUR'] . ':' . \GMerchantCenterPro::$conf['GMCP_CUT_OFF_MIN'] . ':00';

                // Date / time formating / init for comparison
                list($oh, $om, $os) = explode(':', $order_time);
                list($ch, $cm, $cs) = explode(':', $cutoff_time);
                $order_time_ts = mktime((int) $oh, (int) $om, (int) $os);
                $cutoff_time_ts = mktime((int) $ch, (int) $cm, (int) $cs);

                // Let's first determine the basic expected shipping date based on order processing preferences
                if (\GMerchantCenterPro::$conf['GMCP_SAME_DAY_PROCESS']) {
                    $ship_date = (($order_time_ts < $cutoff_time_ts) ? $order_date : $tomorrow_date);
                } else {
                    $ship_date = self::getStandardExpectedDate($order_date);
                }

                // Finally, let's test the date until we get a date that is OK for shipping
                $cnt = 1;
                $limit = 30; // let's avoid infinite loops in case all week days or holidays are checked. 30 will do

                while (self::canShipOnThatDay($ship_date) === false && $cnt < $limit) {
                    $ship_date = self::nextDay($ship_date);
                    ++$cnt;
                }
            }

            return $ship_date;
        }
    }

    /**
     * method get the next day
     *
     * @param string $date
     *
     * @return mixed
     */
    public static function nextDay($date)
    {
        $ts = strtotime($date);

        return date('Y-m-d', $ts + 86400);
    }

    /**
     * Get standard expected shipping date when same-day shipping is not checked
     *
     * @param string $order_date
     *
     * @return mixed
     */
    public static function getStandardExpectedDate($order_date)
    {
        // if value of GTRUSTEDSTORES_PROCESS_TIME is 2, 2013-12-10 becomes 2013-12-12 for example
        for ($i = 1; $i <= (int) \GMerchantCenterPro::$conf['GMCP_SHIPPING_PROCESS']; ++$i) {
            $order_date = self::nextDay($order_date);
        }

        return $order_date;
    }

    /**
     * Check to see if an order can be shipped on a specific date
     *
     * @param string $date
     *
     * @return bool
     */
    public static function canShipOnThatDay($date)
    {
        // Let's check closed weekdays first
        $ts = strtotime($date);
        $daynum = date('w', $ts);

        if (in_array($daynum, explode(',', \GMerchantCenterPro::$conf['GMCP_CLOSED_DAY']))) {
            return false;
        }

        // Then, let's check for holidays
        list($y, $m, $d) = explode('-', $date);
        $datekey = $m . '_' . str_replace('0', '', $d);
        if (in_array($datekey, explode(',', \GMerchantCenterPro::$conf['GMCP_HOLIDAYS']))) {
            return false;
        }

        return true;
    }

    /**
     * Estimate the develivery date
     *
     * @param object $order
     * @param string $shipping_date
     *
     * @return string
     */
    public static function getEstimatedDeliveryDate($order, $shipping_date)
    {
        $delivery_date = $shipping_date;

        $shipping_times = moduleTools::handleGetConfigurationData(\GMerchantCenterPro::$conf['GMCP_SHIP_TIME']);

        if (isset($shipping_times[$order->id_carrier])) {
            $ship_time = (int) $shipping_times[$order->id_carrier];
        } else {
            $ship_time = 2;
        }

        for ($i = 1; $i <= (int) $ship_time; ++$i) {
            $delivery_date = self::nextDay($delivery_date);
        }

        return (string) $delivery_date;
    }

    /**
     * check the gtin value
     *
     * @param array $products the product information
     *
     * @return mixed
     */
    public static function getTagGtin($products)
    {
        $gtin = [];

        if (is_array($products)) {
            foreach ($products as $product) {
                $productObject = new \Product((int) $product['product_id']);
                if (\Validate::isLoadedObject($productObject)) {
                    if (
                        !empty($productObject->ean13)
                        && (\Tools::strlen($productObject->ean13) == 8
                            || \Tools::strlen($productObject->ean13) == 12
                            || \Tools::strlen($productObject->ean13) == 13)
                    ) {
                        $gtin[] = $productObject->ean13;
                    }
                }
            }
            unset($productObject);
        }

        return $gtin;
    }

    /**
     * method use for get saved data
     *
     * @param mixed $data the data information
     *
     * @return mixed
     */
    public static function handleGetConfigurationData($data)
    {
        $is_json = false;

        if (!empty($data)) {
            $is_json = is_string($data) && is_array(json_decode($data, true)) ? true : false;
        }

        if (empty($is_json)) {
            $handle = 'unserial';
            $handle .= 'ize';

            if ($data !== 'false') {
                return call_user_func($handle, $data);
            }
        } else {
            return json_decode($data, true);
        }
    }

    /**
     * method use for set saved data
     *
     * @param mixed $data the data information
     *
     * @return string
     */
    public static function handleSetConfigurationData($data)
    {
        return json_encode($data);
    }

    /**
     * handle the
     *
     * @param string $iso_country
     * @param object $product
     * @param int $ipa
     *
     * @return array
     */
    public static function handleGeolocPrice($iso_country, $product, $ipa = null)
    {
        $updated_prices = [];
        $geolocalized_price_raw = 0;
        $geolocalized_price_raw_no_discount = 0;

        if (empty($ipa)) {
            $price_raw_no_tax = \Product::getPriceStatic((int) $product->id, false, null, 6);
            $price_raw_tax = \Product::getPriceStatic((int) $product->id, true, null, 6);
            $price_raw_no_discount_no_tax = \Product::getPriceStatic((int) $product->id, false, null, 6, null, false, false);
            $price_raw_no_discount_tax = \Product::getPriceStatic((int) $product->id, true, null, 6, null, false, false);
        } else {
            $price_raw_no_tax = \Product::getPriceStatic((int) $product->id, false, $ipa, 6);
            $price_raw_tax = \Product::getPriceStatic((int) $product->id, true, $ipa, 6);
            $price_raw_no_discount_no_tax = \Product::getPriceStatic((int) $product->id, false, $ipa, 6, null, false, false);
            $price_raw_no_discount_tax = \Product::getPriceStatic((int) $product->id, true, $ipa, 6, null, false, false);
        }

        $id_country = \Country::getByIso($iso_country);
        $taxRates = \TaxRulesGroup::getAssociatedTaxRatesByIdCountry((int) $id_country);
        $product_tax_rate = \Tax::getProductTaxRate((int) $product->id);
        $country_tax_rate = !empty($product->id_tax_rules_group) ? $taxRates[(int) $product->id_tax_rules_group] : $product_tax_rate;

        if ((float) $country_tax_rate != (float) $product_tax_rate && !empty($country_tax_rate)) {
            $geolocalized_price_raw = $price_raw_no_tax * (1 + ($country_tax_rate / 100));
            $geolocalized_price_raw_no_discount = $price_raw_no_discount_no_tax * (1 + ($country_tax_rate / 100));
        } else {
            $geolocalized_price_raw = $price_raw_tax;
            $geolocalized_price_raw_no_discount = $price_raw_no_discount_tax;
        }

        $updated_prices = [
            'price_raw' => $geolocalized_price_raw,
            'price_raw_no_discount' => $geolocalized_price_raw_no_discount,
        ];

        return $updated_prices;
    }

    /**
     * Performs a secure HTTP request to the PrestaShop Addons/PM API
     *
     * This method handles communication with the PrestaShop Addons marketplace API
     * using proper security measures and error handling.
     *
     * @param array<string, mixed> $data Additional parameters to send with the request
     * @param string $c Domain component (default: prestashop)
     * @param string $s Subdomain component (default: api.addons)
     *
     * @return mixed|false JSON decoded response or false on failure
     *
     * @throws \InvalidArgumentException If domain parameters are invalid
     * @throws \RuntimeException If the request fails
     */
    private static function doHttpRequest(array $data = [], $c = 'prestashop', $s = 'api.addons')
    {
        // Input validation
        if (!is_string($c) || !is_string($s) || empty($c) || empty($s)) {
            throw new \InvalidArgumentException('Invalid domain parameters provided');
        }

        // Sanitize domain components
        $c = preg_replace('/[^a-z0-9\-]/', '', strtolower($c));
        $s = preg_replace('/[^a-z0-9\-]/', '', strtolower($s));

        // Merge default data with provided data
        $defaultData = [
            'version' => defined('_PS_VERSION_') ? _PS_VERSION_ : '',
            'iso_lang' => \Tools::strtolower(\GMerchantCenterPro::$sCurrentLang),
            'iso_code' => \Tools::strtolower(\Country::getIsoById((int) \Configuration::get('PS_COUNTRY_DEFAULT'))),
            'module_key' => isset(\GMerchantCenterPro::$oModule->module_key) ? \GMerchantCenterPro::$oModule->module_key : '',
            'method' => 'contributor',
            'action' => 'all_products',
        ];
        $data = array_merge($defaultData, $data);

        // Build request
        $postData = http_build_query($data);
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/x-www-form-urlencoded',
                    'Content-Length: ' . strlen($postData),
                    'User-Agent: GMerchantCenterPro Module',
                    'Accept: application/json',
                ],
                'content' => $postData,
                'timeout' => 15,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false,
            ],
        ];

        $context = stream_context_create($options);
        $url = 'https://api.addons.prestashop.com/';

        // Make request with error handling
        try {
            $response = \Tools::file_get_contents($url, false, $context);

            if ($response === false) {
                // Log error details
                $error = error_get_last();
                \PrestaShopLogger::addLog(
                    'HTTP request failed: ' . (isset($error['message']) ? $error['message'] : 'Unknown error'),
                    3
                );

                return false;
            }

            // Parse and validate response
            $decodedResponse = json_decode($response);
            if (json_last_error() !== JSON_ERROR_NONE) {
                \PrestaShopLogger::addLog(
                    'JSON decode error: ' . json_last_error_msg(),
                    3
                );

                return false;
            }

            if (empty($decodedResponse)) {
                \PrestaShopLogger::addLog(
                    'Empty response received from API',
                    3
                );

                return false;
            }

            return $decodedResponse;
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog(
                'Exception in HTTP request: ' . $e->getMessage(),
                3
            );

            return false;
        }
    }

    /**
     * Gets the list of installed PM modules from the Addons API with caching
     *
     * @return array<int|string, array<string|mixed>>
     */
    private static function getAddonsModulesFromApi()
    {
        // Check if cache exists and is valid
        $cacheKey = 'BT_MODULES_CS';
        $cacheDateKey = 'BT_MODULES_CS_LAST_UPDATE';
        $cacheData = \Configuration::get($cacheKey);
        $cacheDate = (int) \Configuration::get($cacheDateKey);
        $cacheExpiration = strtotime('+2 day', $cacheDate);

        // Return cache data if valid
        if (!empty($cacheData) && $cacheExpiration > time()) {
            $decodedCache = json_decode($cacheData, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decodedCache;
            }
        }

        // Get fresh data from API
        $apiResponse = self::doHttpRequest();
        if (empty($apiResponse) || empty($apiResponse->products)) {
            return [];
        }

        // Transform and clean data
        $modulesData = [];
        foreach ($apiResponse->products as $module) {
            if (empty($module->id)) {
                continue;
            }

            // Get default shop currency ISO code
            $defaultCurrency = \Currency::getDefaultCurrency();
            $currencyIsoCode = $defaultCurrency ? $defaultCurrency->iso_code : 'EUR';

            $modulesData[(int) $module->id] = [
                'name' => (string) $module->name,
                'displayName' => (string) $module->displayName,
                'url' => (string) $module->url,
                'compatibility_from' => (string) $module->compatibility->from,
                'compatibility_to' => (string) $module->compatibility->to,
                'version' => (string) $module->version,
                'description' => (string) $module->description,
                'img' => (string) $module->img,
                'nbRates' => (int) $module->nbRates,
                'avgRate' => (float) $module->avgRate,
                'cover' => (string) $module->cover->big,
                'price' => (float) $module->price->{$currencyIsoCode},
            ];
        }

        // Store in cache
        \Configuration::updateValue($cacheKey, json_encode($modulesData));
        \Configuration::updateValue($cacheDateKey, time());

        return $modulesData;
    }

    /**
     * Get filtered list of installed modules from cache
     *
     * @param string $technical_module_name Technical name of the current module
     *
     * @return array<int|string, array<string|mixed>> List of filtered modules
     */
    public static function getModulesFromCache($technical_module_name)
    {
        if (empty($technical_module_name) || !is_string($technical_module_name)) {
            return [];
        }

        $modules = self::getAddonsModulesFromApi();
        if (empty($modules)) {
            return [];
        }

        $excludedTerms = ['bf', 'facebookads'];
        $filteredModules = [];

        foreach ($modules as $module) {
            if (!isset($module['name']) || !is_string($module['name'])) {
                continue;
            }

            $moduleName = strtolower($module['name']);
            $technicalName = strtolower($technical_module_name);

            // Skip current and related modules
            if ($moduleName === $technicalName
                || ($technicalName === 'gmerchantcenterpro' && stripos($moduleName, 'gmerchantcenter') !== false)
                || stripos($moduleName, $technicalName) !== false) {
                continue;
            }

            // Skip excluded terms
            $excluded = false;
            foreach ($excludedTerms as $term) {
                if (stripos($moduleName, $term) !== false) {
                    $excluded = true;

                    break;
                }
            }

            if (!$excluded) {
                $filteredModules[] = $module;
            }
        }

        // Randomize filtered modules
        shuffle($filteredModules);

        return $filteredModules;
    }

    /**
     * Calculate shipping fees for a product
     *
     * @param bool $useShipping Whether shipping should be used
     * @param array $freeShipping Array of free shipping products/combinations
     * @param int $productId Product ID
     * @param float $priceRaw Raw product price
     * @param object $currency Currency object
     * @param object $currentCarrier Current carrier object
     * @param float $additionalShippingCost Additional shipping cost
     * @param array|null $combination Optional combination data
     * @param object|null $data Additional data object
     *
     * @return string Formatted shipping price with currency
     */
    public static function calculateShippingFees(
        $useShipping,
        $freeShipping,
        $productId,
        $priceRaw,
        $currency,
        $currentCarrier,
        $additionalShippingCost,
        $combination = null,
        $data = null
    ) {
        // Handle free shipping cases first
        if (self::isProductFreeShipping($useShipping, $freeShipping, $productId, $combination)) {
            return self::formatShippingPrice(0, $currency);
        }

        // Calculate shipping fees for paid shipping
        $basePrice = self::calculateBaseShippingFee($priceRaw, $currency, $data);
        $finalPrice = self::applyCarrierRules($basePrice, $currentCarrier, $data, $additionalShippingCost);

        return self::formatShippingPrice($finalPrice, $currency);
    }

    /**
     * Check if product has free shipping
     */
    private static function isProductFreeShipping($useShipping, $freeShipping, $productId, $combination)
    {
        if (empty($useShipping)) {
            return true;
        }

        if (!empty($freeShipping[$productId])) {
            if ($combination) {
                return in_array($combination['id_product_attribute'], $freeShipping[$productId]);
            }
            return true;
        }

        return false;
    }

    /**
     * Calculate base shipping fee
     */
    private static function calculateBaseShippingFee($priceRaw, $currency, $data)
    {
        $priceDefaultTax = \Tools::convertPrice((float) $priceRaw, $currency, false);
        return (float) self::getProductShippingFees($priceDefaultTax, $data);
    }

    /**
     * Apply carrier specific rules and additional costs
     */
    private static function applyCarrierRules($basePrice, $currentCarrier, $data, $additionalShippingCost)
    {
        $finalPrice = $basePrice;

        if (!empty($data->p)) {
            $productCarriers = $data->p->getCarriers();
            if (empty($productCarriers)) {
                $finalPrice = self::applyCarrierTax($finalPrice, $currentCarrier, $additionalShippingCost);
            } else {
                $finalPrice = self::handleMultipleCarriers($basePrice, $currentCarrier, $data, $productCarriers);
            }
        }

        return $finalPrice + $additionalShippingCost;
    }

    /**
     * Apply carrier tax to shipping price
     */
    private static function applyCarrierTax($price, $currentCarrier, $additionalShippingCost)
    {
        if (!empty($currentCarrier->id)) {
            $carrierTax = \Tax::getCarrierTaxRate((int) $currentCarrier->id);
            $additionalShippingCost *= (1 + ($carrierTax / 100));
        }
        return $price;
    }

    /**
     * Handle multiple carriers case
     */
    private static function handleMultipleCarriers($basePrice, $currentCarrier, $data, $productCarriers)
    {
        foreach ($productCarriers as $carrier) {
            if ($carrier['id_carrier'] != $currentCarrier->id) {
                $data->currentCarrier = new \Carrier($carrier['id_carrier']);
            }
            $basePrice = self::calculateBaseShippingFee($data->step->price, $data->currency, $data);
        }
        return $basePrice;
    }

    /**
     * Format shipping price with currency
     */
    private static function formatShippingPrice($price, $currency)
    {
        return number_format((float) $price, 2, '.', '') . ' ' . $currency->iso_code;
    }

    /**
     * method handle the shipping cost
     *
     * @param float $product_price
     *
     * @return float
     */
    public static function getProductShippingFees($product_price, $data)
    {
        // set vars
        $shipping_cost = (float) 0;
        $process = true;

        // Free shipping on price ?
        if (((float) $data->shippingConfig['PS_SHIPPING_FREE_PRICE'] > 0) && ((float) $product_price >= (float) $data->shippingConfig['PS_SHIPPING_FREE_PRICE'])) {
            $process = false;
        }
        // Free shipping on weight ?
        if (((float) $data->shippingConfig['PS_SHIPPING_FREE_WEIGHT'] > 0) && ((float) $data->step->weight >= (float) $data->shippingConfig['PS_SHIPPING_FREE_WEIGHT'])) {
            $process = false;
        }

        // Only handle shiping cost if don't have free shipping option set to yes
        if (empty($data->step->carrier_free)) {
            // only in case of not free shipping weight or price
            if ($process && !empty($data->currentCarrier->id)) {
                $shipping_method = ($data->currentCarrier->getShippingMethod() == \Carrier::SHIPPING_METHOD_WEIGHT) ? 'weight' : 'price';

                // Get main shipping fee
                if ($shipping_method == 'weight') {
                    $shipping_cost += $data->currentCarrier->getDeliveryPriceByWeight($data->step->weight, $data->currentZone->id);
                } else {
                    $shipping_cost += $data->currentCarrier->getDeliveryPriceByPrice($product_price, $data->currentZone->id);
                }
                unset($shipping_method);

                // Add handling fees if applicable
                if (!empty($data->shippingConfig['PS_SHIPPING_HANDLING']) && !empty($data->currentCarrier->shipping_handling)) {
                    $shipping_cost += (float) $data->shippingConfig['PS_SHIPPING_HANDLING'];
                }

                // Apply tax
                if (!empty($data->step->carrier_tax)) {
                    $carrier_tax = \Tax::getCarrierTaxRate((int) $data->currentCarrier->id);
                    $shipping_cost *= (1 + ($carrier_tax / 100));
                }

                // Covert to correct currency and format
                $shipping_cost = \Tools::convertPrice((float) $shipping_cost, $data->currency);
                $shipping_cost = number_format((float) $shipping_cost, 2, '.', '') . $data->currency->iso_code;
            }
        } else {
            $shipping_cost = number_format((float) $shipping_cost, 2, '.', '') . $data->currency->iso_code;
        }

        if ($product_price >= $data->step->carrier_product_price_free && $data->step->carrier_product_price_free > 0) {
            $shipping_cost = 0;
            $shipping_cost = number_format((float) $shipping_cost, 2, '.', '') . $data->currency->iso_code;
        }

        return $shipping_cost;
    }
}
