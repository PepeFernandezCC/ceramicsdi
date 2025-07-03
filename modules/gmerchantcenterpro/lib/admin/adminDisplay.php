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

namespace Gmerchantcenterpro\Admin;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Gmerchantcenterpro\Configuration\moduleConfiguration;
use Gmerchantcenterpro\Dao\cartRulesDao;
use Gmerchantcenterpro\Dao\customLabelDao;
use Gmerchantcenterpro\Dao\moduleDao;
use Gmerchantcenterpro\Exclusion\exclusionDao;
use Gmerchantcenterpro\Exclusion\exclusionRender;
use Gmerchantcenterpro\Models\customLabelDynamicBestSales;
use Gmerchantcenterpro\Models\customLabelDynamicCategories;
use Gmerchantcenterpro\Models\customLabelDynamicFeature;
use Gmerchantcenterpro\Models\customLabelDynamicNewProduct;
use Gmerchantcenterpro\Models\customLabelDynamicPriceRange;
use Gmerchantcenterpro\Models\customLabelTags;
use Gmerchantcenterpro\Models\exclusionProduct;
use Gmerchantcenterpro\Models\exportBrands;
use Gmerchantcenterpro\Models\exportCategories;
use Gmerchantcenterpro\Models\Feeds;
use Gmerchantcenterpro\Models\googleTaxonomy;
use Gmerchantcenterpro\Models\Reporting;
use Gmerchantcenterpro\ModuleLib\moduleTools;
use Gmerchantcenterpro\ModuleLib\moduleWarning;

class adminDisplay implements adminInterface
{
    /*
     * display all configured data admin tabs
     *
     * @param string $sType => define which method to execute
     * @param array $aParam
     * @return array
     */
    /**
     * set singleton
     *
     * @return obj
     */
    public static function create()
    {
        static $oDisplay;

        if (null === $oDisplay) {
            $oDisplay = new adminDisplay();
        }

        return $oDisplay;
    }

    public function run($sType, array $aParam = null)
    {
        // set variables
        $aDisplayData = [];

        if (empty($sType)) {
            $sType = 'tabs';
        }

        switch ($sType) {
            case 'tabs':
            case 'stepPopup':
            case 'basics':
            case 'prerequisites':
            case 'feed':
            case 'advancedFeed':
            case 'google':
            case 'customLabel':
            case 'customLabelProduct':
            case 'autocomplete':
            case 'feedList':
            case 'reporting':
            case 'reportingBox':
            case 'searchProduct':
            case 'searchSimpleProduct':
            case 'exclusionRule':
            case 'excludeValue':
            case 'rulesSummary':
            case 'exclusionRuleProducts':
            case 'inventory':
            case 'newCustomFeed':
            case 'googleCustomerReviews':
                $aDisplayData = call_user_func_array([$this, 'display' . ucfirst($sType)], [$aParam]);

                break;
            default:
                break;
        }
        // use case - generic assign
        if (!empty($aDisplayData)) {
            $aDisplayInfo['assign']['bMultiShop'] = moduleTools::checkGroupMultiShop();
            $aDisplayData['assign'] = array_merge($aDisplayData['assign'], $this->assign());
        }

        return $aDisplayData;
    }

    /**
     * assigns transverse data
     *
     * @return array
     */
    private function assign()
    {
        $isGremarketing = \GMerchantCenterPro::$gremarketingModule;

        // Reforce the option if another value has been saved before
        if (!empty($isGremarketing)) {
            \Configuration::updateValue('GMCP_FEED_PREF_ID', 'tag-id-basic');
        }

        // set smarty variables
        $aAssign = [
            'sURI' => moduleTools::truncateUri(['&sAction']),
            'sCtrlParamName' => moduleConfiguration::GMCP_PARAM_CTRL_NAME,
            'sController' => moduleConfiguration::GMCP_ADMIN_CTRL,
            'aQueryParams' => moduleConfiguration::getRequestParams(),
            'sDisplay' => \Tools::getValue('sDisplay'),
            'iCurrentLang' => intval(\GMerchantCenterPro::$iCurrentLang),
            'sCurrentLang' => \GMerchantCenterPro::$sCurrentLang,
            'sCurrentIso' => \Language::getIsoById(\GMerchantCenterPro::$iCurrentLang),
            'sFaqLang' => moduleTools::getFaqLang(\GMerchantCenterPro::$sCurrentLang),
            'sTs' => time(),
            'bAjaxMode' => (\GMerchantCenterPro::$sQueryMode == 'xhr' ? true : false),
            'sLoadingImg' => moduleConfiguration::GMCP_URL_IMG . 'admin/bx_loader.gif',
            'sHeaderInclude' => moduleTools::getTemplatePath('views/templates/admin/header.tpl'),
            'sErrorInclude' => moduleTools::getTemplatePath('views/templates/admin/error.tpl'),
            'sConfirmInclude' => moduleTools::getTemplatePath('views/templates/admin/confirm.tpl'),
            'bCompare17' => \GMerchantCenterPro::$bCompare17,
            'bConfigureStep1' => \GMerchantCenterPro::$conf['GMCP_CONF_STEP_1'],
            'bConfigureStep2' => \GMerchantCenterPro::$conf['GMCP_CONF_STEP_2'],
            'bConfigureStep3' => \GMerchantCenterPro::$conf['GMCP_CONF_STEP_3'],
            'moduleJsPath' => moduleConfiguration::GMCP_URL_JS,
            'moduleCssPath' => moduleConfiguration::GMCP_URL_CSS,
            'imagePath' => moduleConfiguration::GMCP_URL_IMG,
            'useJs' => moduleConfiguration::GMCP_USE_JS,
            'faqUrl' => moduleConfiguration::GMCP_BT_FAQ_MAIN_URL,
            'isGremarketing' => $isGremarketing,
        ];

        return $aAssign;
    }

    /**
     *  method displays advice form
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayStepPopup(array $aPost = null)
    {
        $aAssign = [];

        // clean headers
        @ob_end_clean();

        // force xhr mode activated
        \GMerchantCenterPro::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/step-popup.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * displays admin's first page with all tabs
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayTabs(array $aPost = null)
    {
        $iSupportToUse = moduleConfiguration::GMCP_SUPPORT_BT;
        // set smarty variables
        $aAssign = [
            'sDocUri' => _MODULE_DIR_ . moduleConfiguration::GMCP_MODULE_NAME . '/',
            'faqLink' => 'http://faq.businesstech.fr',
            'sDocName' => 'readme_' . ((\GMerchantCenterPro::$sCurrentLang == 'fr') ? 'fr' : 'en') . '.pdf',
            'sCurrentIso' => \Language::getIsoById(\GMerchantCenterPro::$iCurrentLang),
            'sCrossSellingUrl' => !empty($iSupportToUse) ? moduleConfiguration::GMCP_MODULE_URL . '?utm_campaign=internal-module-ad&utm_source=banniere&utm_medium=' . moduleConfiguration::GMCP_MODULE_NAME : moduleConfiguration::GMCP_MODULE_URL . \GMerchantCenterPro::$sCurrentLang . '/6_business-tech',
            'sContactUs' => !empty($iSupportToUse) ? moduleConfiguration::GMCP_MODULE_URL . ((\GMerchantCenterPro::$sCurrentLang == 'fr') ? 'fr/contactez-nous' : 'en/contact-us') : moduleConfiguration::GMCP_MODULE_URL . ((\GMerchantCenterPro::$sCurrentLang == 'fr') ? 'fr/ecrire-au-developpeur?id_product=' . moduleConfiguration::GMCP_SUPPORT_ID : 'en/write-to-developper?id_product=' . moduleConfiguration::GMCP_SUPPORT_ID),
            'sRateUrl' => !empty($iSupportToUse) ? moduleConfiguration::GMCP_MODULE_URL . ((\GMerchantCenterPro::$sCurrentLang == 'fr') ? 'fr/modules-prestashop-google-et-publicite/45-google-merchant-center-pro-module-pour-prestashop-0656272492397.html' : 'en/google-and-advertising-modules-for-prestashop/45-google-merchant-center-pro-module-for-prestashop-0656272492397.html') : moduleConfiguration::GMCP_MODULE_URL . ((\GMerchantCenterPro::$sCurrentLang == 'fr') ? '/fr/ratings.php' : '/en/ratings.php'),
        ];

        // check curl_init and file_get_contents to get the distant Google taxonomy file
        moduleWarning::create()->run('directive', 'allow_url_fopen', [], true);
        $bTmpStopExec = moduleWarning::create()->bStopExecution;
        moduleWarning::create()->bStopExecution = false;
        moduleWarning::create()->run('function', 'curl_init', [], true);

        if ($bTmpStopExec && moduleWarning::create()->bStopExecution) {
            $aAssign['bCurlAndContentStopExec'] = true;
        }

        // check if multi-shop configuration
        if (
            version_compare(_PS_VERSION_, '1.5', '>')
            && \Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')
            && strpos(\Context::getContext()->cookie->shopContext, 'g-') !== false
        ) {
            $aAssign['bMultishopGroupStopExec'] = true;
        }

        // check if we hide the config
        if (
            !empty($aAssign['bFileStopExec'])
            || !empty($aAssign['bCurlAndContentStopExec'])
            || !empty($aAssign['bMultishopGroupStopExec'])
        ) {
            $aAssign['bHideConfiguration'] = true;
        }

        $aAssign['autocmp_js'] = __PS_BASE_URI__ . 'js/jquery/plugins/autocomplete/jquery.autocomplete.js';
        $aAssign['autocmp_css'] = __PS_BASE_URI__ . 'js/jquery/plugins/autocomplete/jquery.autocomplete.css';

        $aData = $this->displayBasics($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);
        $aData = $this->displayFeed($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);
        $aData = $this->displayAdvancedFeed($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);
        $aData = $this->displayGoogle($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);
        $aData = $this->displayFeedList($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);
        $aData = $this->displayReporting($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);
        $aData = $this->displayInventory($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);
        $aData = $this->displayInventoryFeed($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);
        $aData = $this->displayNewCustomFeed($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);
        $aData = $this->displayGoogleCustomerReviews($aPost);
        $aAssign = array_merge($aAssign, $aData['assign']);

        // assign all included templates files
        $aAssign['sPrerequisitesInclude'] = moduleTools::getTemplatePath('views/templates/admin/prerequisites.tpl');
        $aAssign['sBasicsInclude'] = moduleTools::getTemplatePath('views/templates/admin/basics.tpl');
        $aAssign['sFeedInclude'] = moduleTools::getTemplatePath('views/templates/admin/feed-settings.tpl');
        $aAssign['sGoogleInclude'] = moduleTools::getTemplatePath('views/templates/admin/google-settings.tpl');
        $aAssign['sAdvanceFeed'] = moduleTools::getTemplatePath('views/templates/admin/advanced-settings.tpl');
        $aAssign['sLocalInventoryFeed'] = moduleTools::getTemplatePath('views/templates/admin/local-inventory-settings.tpl');
        $aAssign['sFeedListInclude'] = moduleTools::getTemplatePath('views/templates/admin/feed-list.tpl');
        $aAssign['sFeedListLiaInclude'] = moduleTools::getTemplatePath('views/templates/admin/feed-lia-list.tpl');
        $aAssign['sReportingInclude'] = moduleTools::getTemplatePath('views/templates/admin/reporting-settings.tpl');
        $aAssign['googleCustomerReviews'] = moduleTools::getTemplatePath('views/templates/admin/google-customer-reviews.tpl');
        $aAssign['sCustomFeed'] = moduleTools::getTemplatePath('views/templates/admin/new-custom-feed.tpl');
        $aAssign['sTopBar'] = moduleTools::getTemplatePath('views/templates/admin/top.tpl');
        $aAssign['sModuleVersion'] = \GMerchantCenterPro::$oModule->version;

        return [
            'tpl' => 'admin/body.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * displays basic settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayBasics(array $aPost = null)
    {
        $excluded_words = json_decode(\GMerchantCenterPro::$conf['GMCP_EXCLUDED_WORDS'], true);
        $excluded_output = '';
        if (!empty($excluded_words)) {
            if (is_array($excluded_words)) {
                foreach ($excluded_words as $word) {
                    $excluded_output .= $word;

                    if ($word != end($excluded_words)) {
                        $excluded_output .= ',';
                    }
                }
            }
        }

        $aAssign = [
            'sDocUri' => _MODULE_DIR_ . moduleConfiguration::GMCP_MODULE_NAME . '/',
            'sDocName' => 'readme_' . ((\GMerchantCenterPro::$sCurrentLang == 'fr') ? 'fr' : 'en') . '.pdf',
            'sLink' => (!empty(\GMerchantCenterPro::$conf['GMCP_LINK']) ? \GMerchantCenterPro::$conf['GMCP_LINK'] : \GMerchantCenterPro::$sHost),
            'sPrefixId' => \GMerchantCenterPro::$conf['GMCP_ID_PREFIX'],
            'iProductPerCycle' => \GMerchantCenterPro::$conf['GMCP_AJAX_CYCLE'],
            'sImgSize' => \GMerchantCenterPro::$conf['GMCP_IMG_SIZE'],
            'coverPosition' => \GMerchantCenterPro::$conf['GMCP_IMG_COVER_POSITION'],
            'bAddImages' => \GMerchantCenterPro::$conf['GMCP_ADD_IMAGES'],
            'aHomeCatLanguages' => \GMerchantCenterPro::$conf['GMCP_HOME_CAT'],
            'iHomeCatId' => \GMerchantCenterPro::$conf['GMCP_HOME_CAT_ID'],
            'bAddCurrency' => \GMerchantCenterPro::$conf['GMCP_ADD_CURRENCY'],
            'iAdvancedProductName' => \GMerchantCenterPro::$conf['GMCP_ADV_PRODUCT_NAME'],
            'iAdvancedProductTitle' => \GMerchantCenterPro::$conf['GMCP_ADV_PROD_TITLE'],
            'sFeedToken' => \GMerchantCenterPro::$conf['GMCP_FEED_TOKEN'],
            'aImageTypes' => \ImageType::getImagesTypes('products'),
            'sCondition' => \GMerchantCenterPro::$conf['GMCP_COND'],
            'aAvailableCondition' => moduleTools::getConditionType(),
            'sProductTitle' => \GMerchantCenterPro::$conf['GMCP_P_TITLE'],
            'bSimpleId' => \GMerchantCenterPro::$conf['GMCP_SIMPLE_PROD_ID'],
            'bIdentifierExist' => \GMerchantCenterPro::$conf['GMCP_FORCE_IDENTIFIER'],
            'bUseProductSize' => \GMerchantCenterPro::$conf['GMCP_PRODUCT_DIMENSION'],
            'excludedWords' => $excluded_output,
            'feedTagId' => \GMerchantCenterPro::$conf['GMCP_FEED_PREF_ID'],
        ];

        $aCategories = \Category::getCategories(intval(\GMerchantCenterPro::$iCurrentLang), false);
        $aAssign['aHomeCat'] = moduleTools::recursiveCategoryTree($aCategories, [], current(current($aCategories)), 1);

        // get all active languages in order to loop on field form which need to manage translation
        $aAssign['aLangs'] = \Language::getLanguages();

        // use case - detect if home category name has been filled
        $aAssign['aHomeCatLanguages'] = $this->getDefaultTranslations('GMCP_HOME_CAT', 'HOME_CAT_NAME');
        $aAssign['aProdNamePrefix'] = !empty(\GMerchantCenterPro::$conf['GMCP_ADV_PROD_NAME_PREFIX']) ? $this->getDefaultTranslations('GMCP_ADV_PROD_NAME_PREFIX', '') : [];
        $aAssign['aProdNameSuffix'] = !empty(\GMerchantCenterPro::$conf['GMCP_ADV_PROD_NAME_SUFFIX']) ? $this->getDefaultTranslations('GMCP_ADV_PROD_NAME_SUFFIX', '') : [];

        if (is_array($aAssign['aHomeCatLanguages'])) {
            foreach ($aAssign['aLangs'] as $aLang) {
                if (!isset($aAssign['aHomeCatLanguages'][$aLang['id_lang']])) {
                    $aAssign['aHomeCatLanguages'][$aLang['id_lang']] = moduleConfiguration::GMCP_HOME_CAT_NAME['en'];
                }
            }
        }

        return [
            'tpl' => 'admin/basics.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * returns the matching requested translations
     *
     * @param string $sSerializedVar
     * @param string $sGlobalVar
     *
     * @return array
     */
    private function getDefaultTranslations($sSerializedVar, $sGlobalVar)
    {
        $aTranslations = [];

        if (!empty(\GMerchantCenterPro::$conf[strtoupper($sSerializedVar)])) {
            $aTranslations = moduleTools::handleGetConfigurationData(\GMerchantCenterPro::$conf[strtoupper($sSerializedVar)], ['allowed_classes' => false]);
        } else {
            foreach (moduleConfiguration::GMCP_HOME_CAT_NAME as $sIsoCode => $sTranslation) {
                $iLangId = moduleTools::getLangId($sIsoCode);

                if ($iLangId) {
                    // get Id by iso
                    $aTranslations[$iLangId] = $sTranslation;
                }
            }
        }

        return $aTranslations;
    }

    /**
     * displays feeds settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayFeed(array $aPost = null)
    {
        if (\GMerchantCenterPro::$sQueryMode == 'xhr') {
            // clean headers
            @ob_end_clean();
        }

        $aAssign = [
            'bExportMode' => \GMerchantCenterPro::$conf['GMCP_EXPORT_MODE'],
            'bExportOOS' => \GMerchantCenterPro::$conf['GMCP_EXPORT_OOS'],
            'bExcludeNoEan' => \GMerchantCenterPro::$conf['GMCP_EXC_NO_EAN'],
            'bExcludeNoMref' => \GMerchantCenterPro::$conf['GMCP_EXC_NO_MREF'],
            'iMinPrice' => \GMerchantCenterPro::$conf['GMCP_MIN_PRICE'],
            'iMaxWeight' => \GMerchantCenterPro::$conf['GMCP_MAX_WEIGHT'],
            'bProductOosOrder' => \GMerchantCenterPro::$conf['GMCP_EXPORT_PROD_OOS_ORDER'],
            'bProductCombos' => \GMerchantCenterPro::$conf['GMCP_P_COMBOS'],
            'iDescType' => \GMerchantCenterPro::$conf['GMCP_P_DESCR_TYPE'],
            'aDescriptionType' => moduleTools::getDescriptionType(),
            'iIncludeStock' => \GMerchantCenterPro::$conf['GMCP_INC_STOCK'],
            'bIncludeTagAdult' => \GMerchantCenterPro::$conf['GMCP_INC_TAG_ADULT'],
            'bIncludeTagCost' => \GMerchantCenterPro::$conf['GMCP_INC_COST'],
            'bIncludeSize' => \GMerchantCenterPro::$conf['GMCP_INC_SIZE'],
            'aAttributeGroups' => \AttributeGroup::getAttributesGroups((int) \GMerchantCenterPro::$oContext->cookie->id_lang),
            'aFeatures' => \Feature::getFeatures((int) \GMerchantCenterPro::$oContext->cookie->id_lang),
            'aSizeOptions' => \GMerchantCenterPro::$conf['GMCP_SIZE_OPT'],
            'sIncludeColor' => \GMerchantCenterPro::$conf['GMCP_INC_COLOR'],
            'bIncludeMaterial' => \GMerchantCenterPro::$conf['GMCP_INC_MATER'],
            'bIncludePattern' => \GMerchantCenterPro::$conf['GMCP_INC_PATT'],
            'bIncludeGender' => \GMerchantCenterPro::$conf['GMCP_INC_GEND'],
            'bIncludeAge' => \GMerchantCenterPro::$conf['GMCP_INC_AGE'],
            'bIncludeEnergy' => \GMerchantCenterPro::$conf['GMCP_INC_ENERGY'],
            'bExcludedDest' => \GMerchantCenterPro::$conf['GMCP_EXCLUDED_DEST'],
            'bIncludeShippingLabel' => \GMerchantCenterPro::$conf['GMCP_INC_SHIPPING_LABEL'],
            'bIncludeUnitpricingMeasure' => \GMerchantCenterPro::$conf['GMCP_INC_UNIT_PRICING'],
            'bIncludeUnitBasepricingMeasure' => \GMerchantCenterPro::$conf['GMCP_INC_B_UNIT_PRICING'],
            'bShippingUse' => \GMerchantCenterPro::$conf['GMCP_SHIPPING_USE'],
            'bDimensionUse' => \GMerchantCenterPro::$conf['GMCP_DIMENSION'],
            'aExcludedProducts' => \GMerchantCenterPro::$conf['GMCP_PROD_EXCL'],
            'sGtinPreference' => \GMerchantCenterPro::$conf['GMCP_GTIN_PREF'],
            'aShippingCarriers' => [],
            'bSizeSystem' => \GMerchantCenterPro::$conf['GMCP_SIZE_SYSTEM'],
            'bSizeType' => \GMerchantCenterPro::$conf['GMCP_SIZE_TYPE'],
            'aFreeShippingProducts' => \GMerchantCenterPro::$conf['GMCP_FREE_SHIP_PROD'],
            'aFreePausedroducts' => \GMerchantCenterPro::$conf['GMCP_PAUSED_PROD'],
            'tagPause' => \GMerchantCenterPro::$conf['GMCP_TAG_PAUSE_VALUE'],
            'sIncludeSize' => \GMerchantCenterPro::$conf['GMCP_INC_SIZE'],
            'bRewriteNumAttrValues' => \GMerchantCenterPro::$conf['GMCP_URL_NUM_ATTR_REWRITE'],
            'bIncludeAttributeValue' => \GMerchantCenterPro::$conf['GMCP_INCL_ATTR_VALUE'],
            'bUrlInclAttrId' => \GMerchantCenterPro::$conf['GMCP_URL_ATTR_ID_INCL'],
            'sComboSeparator' => \GMerchantCenterPro::$conf['GMCP_COMBO_SEPARATOR'],
            'bExcludedCountry' => \GMerchantCenterPro::$conf['GMCP_EXCLUDED_COUNTRY'],
            'shipsFrom' => \GMerchantCenterPro::$conf['GMCP_SHIPS_FROM'],
            'freeShippingPrice' => \GMerchantCenterPro::$conf['GMCP_FREE_SHIPPING_PRICE'],
            'bIncludeAnchor' => \GMerchantCenterPro::$conf['GMCP_INCL_ANCHOR'],
            'handleTagAdultLink' => \Context::getContext()->link->getAdminLink('AdminGmcpTagProduct') . '&tag=adult',
            'handleTagMaterialLink' => \Context::getContext()->link->getAdminLink('AdminGmcpTagProduct') . '&tag=material',
            'handleTagPatternLink' => \Context::getContext()->link->getAdminLink('AdminGmcpTagProduct') . '&tag=pattern',
            'handleTagGenderLink' => \Context::getContext()->link->getAdminLink('AdminGmcpTagProduct') . '&tag=gender',
            'handleTagAgeGroupeLink' => \Context::getContext()->link->getAdminLink('AdminGmcpTagProduct') . '&tag=agegroup',
            'handleSizeType' => \Context::getContext()->link->getAdminLink('AdminGmcpTagProduct') . '&tag=sizeType',
            'handleSizeSystem' => \Context::getContext()->link->getAdminLink('AdminGmcpTagProduct') . '&tag=sizeSystem',
            'handleTagEnergyLink' => \Context::getContext()->link->getAdminLink('AdminGmcpTagProduct') . '&tag=energy',
            'handleTagShippingLabelLink' => \Context::getContext()->link->getAdminLink('AdminGmcpTagProduct') . '&tag=shipping_label',
            'handleUnitPriceMeasureLink' => \Context::getContext()->link->getAdminLink('AdminGmcpTagProduct') . '&tag=unit_pricing_measure',
            'handleBaseUnitPricingMeasureLink' => \Context::getContext()->link->getAdminLink('AdminGmcpTagProduct') . '&tag=base_unit_pricing_measure',
            'handleExcludedDestinationLink' => \Context::getContext()->link->getAdminLink('AdminGmcpTagProduct') . '&tag=excluded_destination',
            'handleExcludedCountryLink' => \Context::getContext()->link->getAdminLink('AdminGmcpTagProduct') . '&tag=excluded_country',
        ];

        // handle product IDs and Names list to format them for the autocomplete feature
        if (!empty($aAssign['aExcludedProducts'])) {
            $sProdIds = '';
            $sProdNames = '';

            foreach ($aAssign['aExcludedProducts'] as $iKey => $sProdId) {
                $aProdIds = explode('¤', $sProdId);
                $oProduct = new \Product($aProdIds[0], false, \GMerchantCenterPro::$iCurrentLang);

                // check if we export with combinations
                if (!empty($aProdIds[0])) {
                    $oProduct->name .= moduleTools::getProductCombinationName($aProdIds[1], \GMerchantCenterPro::$iCurrentLang, \GMerchantCenterPro::$iShopId);

                    $sProdIds .= $sProdId . '-';
                    $sProdNames .= $oProduct->name . '||';

                    $aAssign['aProducts'][] = [
                        'id' => $sProdId,
                        'name' => $oProduct->name,
                        'attrId' => $aProdIds[1],
                        'stringIds' => $sProdId,
                    ];
                }
            }
            $aAssign['sProductIds'] = $sProdIds;
            $aAssign['sProductNames'] = $sProdNames;
        }

        // handle product IDs and Names list for export product free shipping
        if (!empty($aAssign['aFreeShippingProducts'])) {
            $sProdIds = '';
            $sProdNames = '';

            foreach ($aAssign['aFreeShippingProducts'] as $iKey => $sProdId) {
                $aProdIds = explode('¤', $sProdId);
                $oProduct = new \Product($aProdIds[0], false, \GMerchantCenterPro::$iCurrentLang);

                // check if we export with combinations
                if (!empty($aProdIds[1])) {
                    $oProduct->name .= moduleTools::getProductCombinationName($aProdIds[1], \GMerchantCenterPro::$iCurrentLang, \GMerchantCenterPro::$iShopId);
                }

                $sProdIds .= $sProdId . '-';
                $sProdNames .= $oProduct->name . '||';

                $aAssign['aProductsFreeShipping'][] = [
                    'id' => $sProdId,
                    'name' => $oProduct->name,
                    'attrId' => $aProdIds[1],
                    'stringIds' => $sProdId,
                ];
            }
            $aAssign['sProductFreeShippingIds'] = $sProdIds;
            $aAssign['sProductFreeShippingNames'] = str_replace('"', '', $sProdNames);
        }

        if (!empty($aAssign['aFreePausedroducts'])) {
            $sProdIds = '';
            $sProdNames = '';

            foreach ($aAssign['aFreePausedroducts'] as $iKey => $sProdId) {
                $aProdIds = explode('¤', $sProdId);
                $oProduct = new \Product($aProdIds[0], false, \GMerchantCenterPro::$iCurrentLang);

                // check if we export with combinations
                if (!empty($aProdIds[1])) {
                    $oProduct->name .= moduleTools::getProductCombinationName($aProdIds[1], \GMerchantCenterPro::$iCurrentLang, \GMerchantCenterPro::$iShopId);
                }

                $sProdIds .= $sProdId . '-';
                $sProdNames .= $oProduct->name . '||';

                $aAssign['aProductsPaused'][] = [
                    'id' => $sProdId,
                    'name' => $oProduct->name,
                    'attrId' => $aProdIds[1],
                    'stringIds' => $sProdId,
                ];
            }
            $aAssign['sProductPauseIds'] = $sProdIds;
            $aAssign['sProductPauseNames'] = str_replace('"', '', $sProdNames);
        }

        if (isset(\GMerchantCenterPro::$conf['GMCP_COLOR_OPT']['attribute'])) {
            $aAssign['aColorOptions']['attribute'] = !empty(\GMerchantCenterPro::$conf['GMCP_COLOR_OPT']['attribute']) ? \GMerchantCenterPro::$conf['GMCP_COLOR_OPT']['attribute'] : [0];
        }
        if (isset(\GMerchantCenterPro::$conf['GMCP_COLOR_OPT']['feature'])) {
            $aAssign['aColorOptions']['feature'] = !empty(\GMerchantCenterPro::$conf['GMCP_COLOR_OPT']['feature']) ? \GMerchantCenterPro::$conf['GMCP_COLOR_OPT']['feature'] : [0];
        }

        if (isset(\GMerchantCenterPro::$conf['aSizeOptions']['attribute'])) {
            $aAssign['aSizeOptions']['attribute'] = !empty(\GMerchantCenterPro::$conf['GMCP_SIZE_OPT']['attribute']) ? \GMerchantCenterPro::$conf['GMCP_SIZE_OPT']['attribute'] : [0];
        }
        if (isset(\GMerchantCenterPro::$conf['aSizeOptions']['feature'])) {
            $aAssign['aSizeOptions']['feature'] = !empty(\GMerchantCenterPro::$conf['GMCP_SIZE_OPT']['feature']) ? \GMerchantCenterPro::$conf['GMCP_SIZE_OPT']['feature'] : [0];
        }

        // get available categories and manufacturers
        $aCategories = \Category::getCategories(intval(\GMerchantCenterPro::$iCurrentLang), false);
        $aBrands = \Manufacturer::getManufacturers();

        $aStartCategories = current($aCategories);
        $aFirst = current($aStartCategories);
        $iStart = (int) \Category::getRootCategory()->id;

        // get registered categories and brands
        $aIndexedCategories = [];
        $aIndexedBrands = [];

        // use case - get categories or brands according to the export mode
        if (\GMerchantCenterPro::$conf['GMCP_EXPORT_MODE'] == 1) {
            $aIndexedBrands = exportBrands::getBrands(\GMerchantCenterPro::$iShopId);
        } else {
            $aIndexedCategories = exportCategories::getCategories(\GMerchantCenterPro::$iShopId);
        }

        // format categories and brands
        $aAssign['aFormatCat'] = moduleTools::recursiveCategoryTree($aCategories, $aIndexedCategories, $aFirst, $iStart, null, true);
        $aAssign['aFormatBrands'] = moduleTools::recursiveBrandTree($aBrands, $aIndexedBrands, $aFirst, $iStart);
        $aAssign['iShopCatCount'] = count($aAssign['aFormatCat']);
        $aAssign['iMaxPostVars'] = ini_get('max_input_vars');

        if (!empty(\GMerchantCenterPro::$aAvailableLangCurrencyCountry)) {
            foreach (\GMerchantCenterPro::$aAvailableLangCurrencyCountry as $aData) {
                // handle price with tax or not
                $aAssign['aFeedTax'][] = [
                    'tax' => moduleTools::isTax($aData['langIso'], $aData['countryIso']),
                    'country' => $aData['countryIso'],
                    'lang' => $aData['langIso'],
                    'langId' => $aData['langId'],
                ];
            }
        }

        $availableLanguage = moduleTools::getAvailableLanguages((int) \GMerchantCenterPro::$iShopId);
        $hasData = Feeds::hasSavedData(\GMerchantCenterPro::$iShopId, moduleConfiguration::GMCP_TABLE_PREFIX);
        $freeProductShippingPrice = is_string(\GMerchantCenterPro::$conf['GMCP_FREE_PROD_PRICE_SHIP_CARRIERS']) ? moduleTools::handleGetConfigurationData(\GMerchantCenterPro::$conf['GMCP_FREE_PROD_PRICE_SHIP_CARRIERS'], ['allowed_classes' => false]) : [];
        $carrierNoTax = is_string(\GMerchantCenterPro::$conf['GMCP_NO_TAX_SHIP_CARRIERS']) ? moduleTools::handleGetConfigurationData(\GMerchantCenterPro::$conf['GMCP_NO_TAX_SHIP_CARRIERS'], ['allowed_classes' => false]) : [];
        $isFreeCarrier = is_string(\GMerchantCenterPro::$conf['GMCP_FREE_SHIP_CARRIERS']) ? moduleTools::handleGetConfigurationData(\GMerchantCenterPro::$conf['GMCP_FREE_SHIP_CARRIERS'], ['allowed_classes' => false]) : [];

        if (!empty($hasData)) {
            $availableFeed = Feeds::getAvailableFeeds((int) \GMerchantCenterPro::$iShopId, moduleConfiguration::GMCP_TABLE_PREFIX);
            if (!empty($availableFeed)) {
                foreach ($availableLanguage as $lang) {
                    $current_feed_shop = Feeds::getFeedLangData($lang['iso_code'], (int) \GMerchantCenterPro::$iShopId, strtolower(moduleConfiguration::GMCP_MODULE_NAME));
                    if (isset($current_feed_shop)) {
                        foreach ($current_feed_shop as $feed) {
                            $iCountryId = \Country::getByIso(\Tools::strtolower($feed['iso_country']));
                            if (!empty($iCountryId)) {
                                $country = new \Country($iCountryId);
                                if (!empty($country->active)) {
                                    $iCountryZone = \Country::getIdZone($iCountryId);
                                    if (!empty($iCountryZone)) {
                                        $aCarriers = moduleTools::getAvailableCarriers((int) $iCountryZone, \GMerchantCenterPro::$iCurrentLang);
                                        if (!empty($aCarriers)) {
                                            $id_currency = \Currency::getIdByIsoCode($feed['iso_currency']);
                                            $currency = new \Currency($id_currency);
                                            if (!empty($currency->iso_code)) {
                                                if (!array_key_exists($feed['iso_country'], $aAssign['aShippingCarriers'])) {
                                                    $aAssign['aShippingCarriers'][$feed['iso_country']] = [
                                                        'name' => $country->name,
                                                        'carriers' => $aCarriers,
                                                        'shippingCarrierId' => (!empty(\GMerchantCenterPro::$conf['GMCP_SHIP_CARRIERS'][$feed['iso_country']]) ? \GMerchantCenterPro::$conf['GMCP_SHIP_CARRIERS'][$feed['iso_country']] : 0),
                                                        'noTaxCarrier' => (!empty($carrierNoTax) ? (isset($carrierNoTax[$feed['iso_country']]) ? $carrierNoTax[$feed['iso_country']] : 0) : 0),
                                                        'free' => (!empty($isFreeCarrier) ? (isset($isFreeCarrier[$feed['iso_country']]) ? $isFreeCarrier[$feed['iso_country']] : 0) : 0),
                                                        'productFree' => (!empty($freeProductShippingPrice) ? $freeProductShippingPrice[$feed['iso_country']] : 0),
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
            }
        }

        $aExclusionRules = exclusionDao::getExclusionRules();
        $aAssign['aExclusionRules'] = moduleTools::getExclusionRulesName($aExclusionRules);

        return [
            'tpl' => 'admin/feed-settings.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * displays advancedFeed settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayAdvancedFeed(array $aPost = null)
    {
        if (\GMerchantCenterPro::$sQueryMode == 'xhr') {
            // clean headers
            @ob_end_clean();
        }
        $aCartRulesChannel = [];

        // get discount information for preview
        $aDisplayDiscount = cartRulesDao::getCartRules(
            (string) \GMerchantCenterPro::$conf['GMCP_DSC_NAME'],
            (string) \GMerchantCenterPro::$conf['GMCP_DSC_DATE_FROM'],
            (string) \GMerchantCenterPro::$conf['GMCP_DSC_DATE_TO'],
            (string) \GMerchantCenterPro::$conf['GMCP_DSC_MIN_AMOUNT'],
            \GMerchantCenterPro::$conf['GMCP_DSC_VALUE_MIN'],
            \GMerchantCenterPro::$conf['GMCP_DSC_VALUE_MAX'],
            \GMerchantCenterPro::$conf['GMCP_DSC_TYPE'],
            \GMerchantCenterPro::$conf['GMCP_DSC_CUMULABLE']
        );

        // Handle the channel value
        if (is_array($aDisplayDiscount) && !empty($aDisplayDiscount)) {
            foreach ($aDisplayDiscount as $aData) {
                if (!empty($aData['id_cart_rule'])) {
                    $sChannel = cartRulesDao::getGoogleChannel($aData['id_cart_rule']);
                    $aCartRulesChannel[$aData['id_cart_rule']] = $sChannel;
                }
            }
        }

        $aAssign = [
            'bFilterName' => \GMerchantCenterPro::$conf['GMCP_DSC_FILT_NAME'],
            'bFilterDate' => \GMerchantCenterPro::$conf['GMCP_DSC_FILT_DATE'],
            'bFilterMinAmount' => \GMerchantCenterPro::$conf['GMCP_DSC_FILT_MIN_AMOUNT'],
            'bFilterValue' => \GMerchantCenterPro::$conf['GMCP_DSC_FILT_VALUE'],
            'bFilterType' => \GMerchantCenterPro::$conf['GMCP_DSC_FILT_TYPE'],
            'bFilterCumulable' => \GMerchantCenterPro::$conf['GMCP_DSC_FILT_CUMU'],
            'bFilterFor' => \GMerchantCenterPro::$conf['GMCP_DSC_FILT_FOR'],
            'sDiscountName' => \GMerchantCenterPro::$conf['GMCP_DSC_NAME'],
            'sDiscountDateFrom' => \GMerchantCenterPro::$conf['GMCP_DSC_DATE_FROM'],
            'sDiscountDateTo' => \GMerchantCenterPro::$conf['GMCP_DSC_DATE_TO'],
            'sDiscountMinAmount' => \GMerchantCenterPro::$conf['GMCP_DSC_MIN_AMOUNT'],
            'sDiscountValueMin' => \GMerchantCenterPro::$conf['GMCP_DSC_VALUE_MIN'],
            'sDiscountValueMax' => \GMerchantCenterPro::$conf['GMCP_DSC_VALUE_MAX'],
            'bDiscountType' => \GMerchantCenterPro::$conf['GMCP_DSC_TYPE'],
            'sDiscountCumulable' => \GMerchantCenterPro::$conf['GMCP_DSC_CUMULABLE'],
            'aDiscountAvailable' => $aDisplayDiscount,
            'aCartRulesChannel' => $aCartRulesChannel,
            'aDiscountChannel' => moduleConfiguration::GMCP_DISCOUNT_CHANNEL,
            'bInvPrice' => \GMerchantCenterPro::$conf['GMCP_INV_PRICE'],
            'bInvStock' => \GMerchantCenterPro::$conf['GMCP_INV_STOCK'],
            'bSalePrice' => \GMerchantCenterPro::$conf['GMCP_INV_SALE_PRICE'],
            'bGsnippetsReviews' => moduleTools::isInstalled('gsnippetsreviews', [], false, true),
            'bProductComment' => moduleTools::isInstalled('productcomments', [], false, true),
            'aPromotionDestination' => moduleTools::handleGetConfigurationData(\GMerchantCenterPro::$conf['GMCP_PROMO_DEST'], ['allowed_classes' => false]),
        ];

        $aDataForbidden = moduleTools::handleGetConfigurationData(\GMerchantCenterPro::$conf['GMCP_FORBIDDEN_WORDS'], ['allowed_classes' => false]);
        $aAssign['sForbiddenWords'] = '';
        if (!empty($aDataForbidden)) {
            if (is_array($aDataForbidden)) {
                foreach ($aDataForbidden as $sDataForbidden) {
                    $aAssign['sForbiddenWords'] .= $sDataForbidden;

                    if ($sDataForbidden != end($aDataForbidden)) {
                        $aAssign['sForbiddenWords'] .= ',';
                    }
                }
            }
        }

        return [
            'tpl' => 'admin/advanced-settings.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * displays Google settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayGoogle(array $aPost = null)
    {
        if (\GMerchantCenterPro::$sQueryMode == 'xhr') {
            // clean headers
            @ob_end_clean();
        }

        $aAssign = [
            'aCountryTaxonomies' => moduleTools::getAvailableTaxonomyCountries(moduleConfiguration::GMCP_AVAILABLE_COUNTRIES),
            'sGoogleCatListInclude' => moduleTools::getTemplatePath('views/templates/admin/google-category-list.tpl'),
            'taxonomyController' => \Context::getContext()->link->getAdminLink('AdminGmcpTaxonomy'),
            'aTags' => customLabelTags::getTags(\GMerchantCenterPro::$iShopId),
            'sUtmCampaign' => \GMerchantCenterPro::$conf['GMCP_UTM_CAMPAIGN'],
            'sUtmSource' => \GMerchantCenterPro::$conf['GMCP_UTM_SOURCE'],
            'sUtmMedium' => \GMerchantCenterPro::$conf['GMCP_UTM_MEDIUM'],
            'bUtmContent' => \GMerchantCenterPro::$conf['GMCP_UTM_CONTENT'],
        ];

        foreach ($aAssign['aCountryTaxonomies'] as $sIsoCode => &$aTaxonomy) {
            $aTaxonomy['countryList'] = implode(', ', $aTaxonomy['countries']);
            $aTaxonomy['updated'] = googleTaxonomy::checkTaxonomyUpdate($sIsoCode);
        }

        return [
            'tpl' => 'admin/google-settings.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * displays feed list
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayFeedList(array $aPost = null)
    {
        if (\GMerchantCenterPro::$sQueryMode == 'xhr') {
            // clean headers
            @ob_end_clean();
        }

        $aAssign = [
            'iShopId' => \GMerchantCenterPro::$iShopId,
            'sGmcLink' => \GMerchantCenterPro::$conf['GMCP_LINK'],
            'bReporting' => \GMerchantCenterPro::$conf['GMCP_REPORTING'],
            'iTotalProductToExport' => moduleDao::getProductIds(\GMerchantCenterPro::$iShopId, (int) \GMerchantCenterPro::$conf['GMCP_EXPORT_MODE'], true),
            'iTotalDiscountToExport' => cartRulesDao::getCartRulesId(),
            'iTotalProduct' => moduleDao::countProducts(\GMerchantCenterPro::$iShopId, (int) \GMerchantCenterPro::$conf['GMCP_P_COMBOS']),
            'aFeedFileList' => [],
            'aFeedFileListReviews' => [],
            'aFlyFileList' => [],
            'bExcludedProduct' => exclusionProduct::isExcludedProduct(),
        ];
        $aAssign['aCronLangProduct'] = (!empty(\GMerchantCenterPro::$conf['GMCP_CHECK_EXPORT']) ? \GMerchantCenterPro::$conf['GMCP_CHECK_EXPORT'] : []);
        $aAssign['aCronLangReviews'] = (!empty(\GMerchantCenterPro::$conf['GMCP_CHECK_EXPORT_REVIEWS']) ? \GMerchantCenterPro::$conf['GMCP_CHECK_EXPORT_REVIEWS'] : []);

        // handle data feed file name
        if (!empty($aAssign['sGmcLink'])) {
            $sFileSuffix = '';
            // handle type of dat feed file name and data feed for on-the-fly-output
            foreach (moduleConfiguration::GMCP_DATA_FEED_TYPE as $sType) {
                $aAssign['aFeedFileList' . ucfirst($sType)] = [];
                $aAssign['aCronList' . ucfirst($sType)] = [];
                $aAssign['aFlyFileList' . ucfirst($sType)] = [];

                // handle manual xml file and on-the-fly output
                if (!empty(\GMerchantCenterPro::$aAvailableLangCurrencyCountry)) {
                    // Use case - Cron per country
                    foreach (\GMerchantCenterPro::$aAvailableLangCurrencyCountry as $sKey => $aData) {
                        // SET THE XML FILE SUFFIX
                        $sFileSuffix = moduleTools::buildFileSuffix($aData['langIso'], $aData['countryIso'], $aData['currencyIso'], 0, $sType);

                        $sFileName = \GMerchantCenterPro::$sFilePrefix . '.' . $sFileSuffix . '.xml';

                        // use case - for all data feed except the discount
                        if ($sType != 'discount' && $sType != 'reviews') {
                            if (is_file(moduleConfiguration::GMCP_SHOP_PATH_ROOT . $sFileName)) {
                                // Array of XML file list
                                $aAssign['aFeedFileList' . ucfirst($sType)][] = [
                                    'link' => $aAssign['sGmcLink'] . __PS_BASE_URI__ . $sFileName,
                                    'filename' => $sFileName,
                                    'filemtime' => date('d-m-Y H:i:s', filemtime(moduleConfiguration::GMCP_SHOP_PATH_ROOT . $sFileName)),
                                    'checked' => (in_array($aData['langIso'] . '_' . $aData['countryIso'] . '_' . $aData['currencyIso'], $aAssign['aCronLang' . ucfirst($sType)]) ? true : false),
                                    'country' => $aData['countryIso'],
                                    'countryName' => $aData['countryName'],
                                    'lang' => $aData['langIso'],
                                    'langName' => $aData['langName'],
                                    'currencyIso' => $aData['currencyIso'],
                                    'currencySign' => $aData['currencySign'],
                                    'langId' => $aData['langId'],
                                    'taxonomy' => $aData['taxonomy'],
                                    'full' => strtoupper($aData['langIso']) . '_' . strtoupper($aData['countryIso']) . '_' . strtoupper($aData['currencyIso']),
                                    'is_default' => $aData['is_default'],
                                    'id_feed' => $aData['id_feed'],
                                ];

                                $aAssign['aCronList' . ucfirst($sType)][] = [
                                    'currencyIsoCron' => $aData['currencyIso'],
                                    'country' => $aData['countryIso'],
                                    'lang' => $aData['langIso'],
                                    'link' => \Context::getContext()->link->getModuleLink(moduleConfiguration::GMCP_MODULE_SET_NAME, moduleConfiguration::GMCP_CTRL_CRON, ['id_shop' => \GMerchantCenterPro::$iShopId, 'gmcp_lang_id' => $aData['langId'], 'country' => $aData['countryIso'], 'currency_iso' => $aData['currencyIso'], 'token' => \GMerchantCenterPro::$conf['GMCP_FEED_TOKEN'], 'sType' => 'cron', 'feed_type' => $sType]),
                                    'currencySign' => $aData['currencySign'],
                                    'countryName' => $aData['countryName'],
                                    'langName' => $aData['langName'],
                                    'taxonomy' => $aData['taxonomy'],
                                ];
                            }
                        }
                    }

                    // FLY OUTPUT
                    foreach (\GMerchantCenterPro::$aAvailableLangCurrencyCountry as $sKey => $aData) {
                        $aAssign['aFlyFileList' . ucfirst($sType)][] = [
                            'currencyIso' => $aData['currencyIso'],
                            'iso_code' => $aData['langIso'],
                            'countryIso' => $aData['countryIso'],
                            'link' => \Context::getContext()->link->getModuleLink(moduleConfiguration::GMCP_MODULE_SET_NAME, moduleConfiguration::GMCP_CTRL_FLY, ['id_shop' => \GMerchantCenterPro::$iShopId, 'gmcp_lang_id' => $aData['langId'], 'country' => $aData['countryIso'], 'currency_iso' => $aData['currencyIso'], 'token' => \GMerchantCenterPro::$conf['GMCP_FEED_TOKEN'], 'sType' => 'flyOutput', 'feed_type' => $sType]),
                            'currencySign' => $aData['currencySign'],
                            'countryName' => $aData['countryName'],
                            'langName' => $aData['langName'],
                            'taxonomy' => $aData['taxonomy'],
                            'is_default' => $aData['is_default'],
                            'id_feed' => $aData['id_feed'],
                        ];
                    }
                }

                // handle the cron URL for each data feed type
                $aAssign['sCronUrl' . ucfirst($sType)] = \Context::getContext()->link->getModuleLink(moduleConfiguration::GMCP_MODULE_SET_NAME, moduleConfiguration::GMCP_CTRL_CRON, ['id_shop' => \GMerchantCenterPro::$iShopId, 'feed_type' => $sType, 'token' => \GMerchantCenterPro::$conf['GMCP_FEED_TOKEN']]);
            }
        }

        return [
            'tpl' => 'admin/feed-list.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * displays reporting settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayReporting(array $aPost = null)
    {
        $aAssign = [
            'aLangCurrencies' => moduleTools::getGeneratedReport(),
            'bReporting' => \GMerchantCenterPro::$conf['GMCP_REPORTING'],
        ];

        return [
            'tpl' => 'admin/reporting-settings.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * displays autocomplete google categories
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayAutocomplete(array $aPost = null)
    {
        // clean headers
        @ob_end_clean();

        // set
        $sOutput = '';

        $sLangIso = \Tools::getValue('sLangIso');
        $sQuery = \Tools::getValue('q');

        // explode query string
        $aWords = explode(' ', $sQuery);

        // get matching query
        $aItems = googleTaxonomy::autocompleteSearch($sLangIso, $aWords);

        if (
            !empty($aItems)
            && is_array($aItems)
        ) {
            foreach ($aItems as $aItem) {
                $sOutput .= trim($aItem['value']) . "\n";
            }
        }
        echo $sOutput;
        exit;
    }

    /**
     * displays custom labels
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayCustomLabel(array $aPost = null)
    {
        // clean headers
        @ob_end_clean();

        // force xhr mode
        \GMerchantCenterPro::$sQueryMode = 'xhr';

        $aAssign = [
            'aCustomLabelType' => moduleConfiguration::getCustomLabelName(),
            'aCustomBestType' => moduleConfiguration::getCustomLabelBestType(),
            'aCustomBestPeriodType' => moduleConfiguration::getCustomLabelBestPeriodType(),
            'aFeatureAvailable' => moduleDao::getFeature(),
            'sCurrency' => \Currency::getDefaultCurrency()->sign,
            'sUriAutoComplete' => empty(\GMerchantCenterPro::$bCompare80) ? 'ajax_products_list.php' : 'index.php?controller=AdminProducts&ajax=1&action=productsList&token=' . \Tools::getAdminTokenLite('AdminProducts'),
        ];

        // get available categories and manufacturers
        $aCategories = \Category::getCategories(intval(\GMerchantCenterPro::$iCurrentLang), false);
        $aBrands = \Manufacturer::getManufacturers();
        $aSuppliers = \Supplier::getSuppliers();

        $aStartCategories = current($aCategories);
        $aFirst = current($aStartCategories);
        $iStart = (int) \Category::getRootCategory()->id;

        // get registered categories and brands and suppliers
        $aIndexedCategories = [];
        $aIndexedBrands = [];
        $aIndexedSuppliers = [];

        // use case - get categories or brands or suppliers according to the id tag
        $iTagId = \Tools::getValue('iTagId');
        $aTag = [];

        if (!empty($iTagId)) {
            $aTag = customLabelTags::getTags(\GMerchantCenterPro::$iShopId, $iTagId);

            // manage categories association for each type tag using categories
            $aClManualIndexedCategories = customLabelTags::getTags(null, $iTagId, 'cats', 'category');
            $aClDynamicIndexedCategories = customLabelDynamicCategories::getDynamicCat($iTagId);

            // merge result for return good check box for each categories
            $aIndexedCategories = array_merge($aClManualIndexedCategories, $aClDynamicIndexedCategories);

            $aIndexedBrands = customLabelTags::getTags(null, $iTagId, 'brands', 'brand');
            $aIndexedSuppliers = customLabelTags::getTags(null, $iTagId, 'suppliers', 'supplier');
            $aIndexedProducts = customLabelTags::getTags(null, $iTagId, 'products');

            // handle product IDs and Names list to format them for the autocomplete feature
            if (!empty($aIndexedProducts)) {
                $sProdIds = '';
                $sProdNames = '';
                foreach ($aIndexedProducts as $iKey => $iProdId) {
                    if (!empty($iProdId)) {
                        $sProdIds .= $iProdId['id_product'] . '-';
                        $sProdNames .= $iProdId['product_name'] . '||';
                        $aAssign['aProducts'][] = [
                            'id' => $iProdId['id_product'],
                            'name' => $iProdId['product_name'],
                        ];
                    }
                }
                $aAssign['sProductIds'] = $sProdIds;
                $aAssign['sProductNames'] = $sProdNames;
            }

            $aFeatureSelected = customLabelDynamicFeature::getFeatureSave($iTagId);
            $sDateNewProduct = customLabelDynamicNewProduct::getDynamicNew($iTagId);
            $aBestSales = customLabelDynamicBestSales::getDynamicBestSales($iTagId);
            $aPriceRange = customLabelDynamicPriceRange::getDynamicPriceRange($iTagId);

            $aAssign['bActive'] = $aTag[0]['active'];
            $aAssign['sDate'] = $aTag[0]['end_date'];
            $aAssign['customLabelSetPosition'] = $aTag[0]['custom_label_set_postion'];
            $aAssign['iFeatureId'] = $aFeatureSelected['id_feature'];
            $aAssign['aProductIds'] = $aIndexedProducts;
            $aAssign['sDateNewPoduct'] = $sDateNewProduct['from_date'];

            // Use case for best sale
            $aAssign['fAmount'] = $aBestSales['amount'];
            $aAssign['sUnit'] = $aBestSales['unit'];

            if ($aBestSales['start_date'] != '0000-00-00 00:00:00') {
                $aAssign['sStartDate'] = $aBestSales['start_date'];
            }

            if ($aBestSales['end_date'] != '0000-00-00 00:00:00') {
                $aAssign['sEndDate'] = $aBestSales['end_date'];
            }

            // Use case for price range CL
            $aAssign['fPriceMin'] = $aPriceRange['price_min'];
            $aAssign['fPriceMax'] = $aPriceRange['price_max'];
        }

        // format categories and brands and suppliers
        $aAssign['aTag'] = (count($aTag) == 1 && isset($aTag[0])) ? $aTag[0] : $aTag;
        $aAssign['aFormatCat'] = moduleTools::recursiveCategoryTree($aCategories, $aIndexedCategories, $aFirst, $iStart);
        $aAssign['aFormatBrands'] = moduleTools::recursiveBrandTree($aBrands, $aIndexedBrands, $aFirst, $iStart);
        $aAssign['aFormatSuppliers'] = moduleTools::recursiveSupplierTree($aSuppliers, $aIndexedSuppliers, $aFirst, $iStart);
        $aAssign['iShopCatCount'] = count($aAssign['aFormatCat']);
        $aAssign['iMaxPostVars'] = ini_get('max_input_vars');
        $aAssign['labelPosition'] = moduleConfiguration::getCustomLabelPosition();

        // manage autocomplete
        $aProduct = \Product::getSimpleProducts(\GMerchantCenterPro::$iShopId);

        foreach ($aProduct as $key => $value) {
            // set the string for autocomplete
            $sProduct[$key] = $value;
        }

        $aAssign['sProduct'] = $sProduct;

        return [
            'tpl' => 'admin/google-custom-label.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * displays products are associated to the CL
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayCustomLabelProduct(array $aPost = null)
    {
        // clean headers
        @ob_end_clean();

        $aAssign = [];

        $iTagId = \Tools::getValue('iTagId');

        foreach (moduleConfiguration::GMCP_CUSTOM_LABEL_PRODUCT_FILTER as $aFilter) {
            $aProductIds = customLabelDao::getCustomLabelProductIds($iTagId, $aFilter);

            if (!empty($aProductIds)) {
                foreach ($aProductIds as $aProductId) {
                    if (is_array($aProductId)) {
                        $oProduct = new \Product((int) $aProductId['id_product'], true, \GMerchantCenterPro::$iCurrentLang);
                        $aAssign['aProduct'][(int) $aProductId['id_product']] = ['id' => $oProduct->id, 'name' => $oProduct->name];
                    }
                }
            }
        }

        // force xhr mode
        \GMerchantCenterPro::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/google-custom-label-products.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * displayReporting() method displays reporting fancybox
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayReportingBox(array $aPost = null)
    {
        // clean headers
        @ob_end_clean();

        $aAssign = [];
        $aTmp = [];

        // get the current lang ID
        $sLang = \Tools::getValue('lang');
        $iProductCount = \Tools::getValue('count');
        $currency_iso = \Tools::getValue('sCurrencyIso');

        if (!empty($currency_iso)) {
            $sLang = $sLang . '_' . $currency_iso;
        }

        if (!empty($sLang)) {
            $reporting = Reporting::getReportingData($sLang, \GMerchantCenterPro::$iShopId);
            $reporting_details = json_decode($reporting, true);
            $details_iso_data = explode('_', $sLang);

            if (!empty($reporting_details)) {
                static $products = [];

                $id_lang = \Language::getIdByIso($details_iso_data[0]);
                $language = new \Language((int) $id_lang);
                $id_currency = \Currency::getIdByIsoCode($details_iso_data[2]);
                $currency = new \Currency($id_currency);
                $id_country = \Country::getByIso(\Tools::strtolower($details_iso_data[1]));
                $country = new \Country((int) $id_country);

                // check if exists counter key in the reporting
                if (!empty($reporting_details['counter'][0])) {
                    if (empty($iProductCount)) {
                        $iProductCount = $reporting_details['counter'][0]['products'];
                    }
                    unset($reporting_details['counter']);
                }

                // load facebook tags
                $aGoogleTags = moduleTools::loadGoogleTags();

                foreach ($reporting_details as $sTagName => &$aGTag) {
                    $aTmp[$aGoogleTags[$sTagName]['type']][$sTagName]['count'] = count($aGTag);
                    $aTmp[$aGoogleTags[$sTagName]['type']][$sTagName]['label'] = (isset($aGoogleTags[$sTagName]) ? $aGoogleTags[$sTagName]['label'] : '');
                    $aTmp[$aGoogleTags[$sTagName]['type']][$sTagName]['msg'] = (isset($aGoogleTags[$sTagName]) ? $aGoogleTags[$sTagName]['msg'] : '');
                    $aTmp[$aGoogleTags[$sTagName]['type']][$sTagName]['faq_id'] = (isset($aGoogleTags[$sTagName]) ? (int) ($aGoogleTags[$sTagName]['faq_id']) : 0);
                    $aTmp[$aGoogleTags[$sTagName]['type']][$sTagName]['anchor'] = (isset($aGoogleTags[$sTagName]) ? $aGoogleTags[$sTagName]['anchor'] : '');
                    $aTmp[$aGoogleTags[$sTagName]['type']][$sTagName]['mandatory'] = (isset($aGoogleTags[$sTagName]) ? $aGoogleTags[$sTagName]['mandatory'] : false);

                    // detect the old format system and the new format
                    if (
                        isset($aGTag[0]['productId'])
                        && strstr($aGTag[0]['productId'], '_')
                    ) {
                        foreach ($aGTag as $iKey => &$aProdValue) {
                            list($iProdId, $iAttributeId) = explode('_', $aProdValue['productId']);
                            if (empty($products[$aProdValue['productId']])) {
                                // get the product obj
                                $oProduct = new \Product((int) $iProdId, true, (int) $id_lang);
                                $oCategory = new \Category((int) $oProduct->id_category_default, (int) $id_lang);

                                // set the product URL
                                $aProdValue['productUrl'] = moduleTools::getProductLink($oProduct, $id_lang, $oCategory->link_rewrite);
                                // set the product name
                                $aProdValue['productName'] = $oProduct->name;

                                // if combination
                                if (!empty($iAttributeId)) {
                                    $product_category = new \Category((int) $oProduct->getDefaultCategory(), (int) $id_lang);
                                    $aProdValue['productUrl'] = \Context::getContext()->link->getProductLink($oProduct, null, \Tools::strtolower($product_category->link_rewrite), null, (int) $id_lang, (int) \GMerchantCenterPro::$iShopId, (int) $iAttributeId, true);

                                    // get the combination attributes to format the product name
                                    $aCombinationAttr = moduleDao::getProductComboAttributes($iAttributeId, $id_lang, \GMerchantCenterPro::$iShopId);

                                    if (!empty($aCombinationAttr)) {
                                        $sExtraName = '';
                                        foreach ($aCombinationAttr as $c) {
                                            $sExtraName .= ' ' . \Tools::stripslashes($c['name']);
                                        }
                                        $aProdValue['productName'] .= $sExtraName;
                                    }
                                }
                                unset($oProduct);
                                unset($oCategory);

                                $products[$aProdValue['productId']] = [
                                    'productId' => $iProdId,
                                    'productAttrId' => $iAttributeId,
                                    'productUrl' => $aProdValue['productUrl'],
                                    'productName' => $aProdValue['productName'],
                                ];
                            }
                            $aProdValue = $products[$aProdValue['productId']];
                        }
                    }
                    $aTmp[$aGoogleTags[$sTagName]['type']][$sTagName]['data'] = $aGTag;
                }
                $products = [];
                ksort($aTmp);
                unset($reporting_details);
                unset($aGoogleTags);

                $aAssign = [
                    'sLangName' => $language->name,
                    'sCountryName' => $country->name[$id_lang],
                    'aReport' => $aTmp,
                    'iProductCount' => (int) $iProductCount,
                    'sPath' => moduleConfiguration::GMCP_SHOP_PATH_ROOT,
                    'sFaqURL' => 'http://faq.businesstech.fr/',
                    'sToken' => \Tools::getAdminTokenLite('AdminProducts'),
                    'sProductLinkController' => $_SERVER['SCRIPT_URI'] . '?controller=AdminProducts',
                    'sProductAction' => '&updateproduct',
                ];
            } else {
                $aAssign['aErrors'][] = [
                    'msg' => \GMerchantCenterPro::$oModule->l('There is no reporting for this language-country association', 'adminDisplay.php') . ' : ' . $details_iso_data[0] . ' - ' . $details_iso_data[1],
                    'code' => 190,
                ];
            }
        } else {
            $aAssign['aErrors'][] = [
                'msg' => \GMerchantCenterPro::$oModule->l('Language ISO and country ISO aren\'t well formatted', 'adminDisplay.php'),
                'code' => 191,
            ];
        }

        // force xhr mode
        \GMerchantCenterPro::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/reporting-box.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * displays search product name for autocomplete
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displaySearchProduct(array $aPost = null)
    {
        // clean headers
        @ob_end_clean();

        // set
        $sOutput = '';

        // get the query to search
        $sSearch = \Tools::getValue('q');

        // get if we are in the case for CL
        $bCustomLabel = \Tools::getValue('isCustomLabel');
        $sExcludedList = \Tools::getValue('excludeIds');

        $bUseCombo = !empty($bCustomLabel) ? false : (int) \GMerchantCenterPro::$conf['GMCP_P_COMBOS'];

        if (!empty($sSearch)) {
            $aMatchingProducts = moduleDao::searchProducts($sSearch, $bUseCombo, $sExcludedList);

            if (!empty($aMatchingProducts)) {
                foreach ($aMatchingProducts as $aProduct) {
                    // check if we export with combinations
                    if (!empty($aProduct['id_product_attribute'])) {
                        $aCombinations = moduleDao::getProductComboAttributes($aProduct['id_product_attribute'], \GMerchantCenterPro::$iCurrentLang, \GMerchantCenterPro::$iShopId);

                        if (!empty($aCombinations)) {
                            $sExtraName = '';
                            foreach ($aCombinations as $c) {
                                $sExtraName .= ' ' . \Tools::stripslashes($c['name']);
                            }
                            $aProduct['name'] .= $sExtraName;
                        }
                    }
                    $sOutput .= trim($aProduct['name']) . '|' . (int) $aProduct['id_product'] . '|' . (!empty($aProduct['id_product_attribute']) ? $aProduct['id_product_attribute'] : '0') . "\n";
                }
            }
        }

        // force xhr mode
        \GMerchantCenterPro::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/product-search.tpl',
            'assign' => ['json' => $sOutput],
        ];
    }

    /**
     * displays search product name for autocomplete
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displaySearchSimpleProduct(array $aPost = null)
    {
        // clean headers
        @ob_end_clean();

        // set
        $sOutput = '';

        // get the query to search
        $sSearch = \Tools::getValue('q');

        // get if we are in the case for CL
        $bCustomLabel = \Tools::getValue('isCustomLabel');
        $sExcludedList = \Tools::getValue('excludeIds');

        if (!empty($sSearch)) {
            $aMatchingProducts = moduleDao::searchProducts($sSearch, false, $sExcludedList);

            if (!empty($aMatchingProducts)) {
                foreach ($aMatchingProducts as $aProduct) {
                    $sOutput .= trim($aProduct['name']) . '|' . (int) $aProduct['id_product'] . "\n";
                }
            }
        }

        // force xhr mode
        \GMerchantCenterPro::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/product-search.tpl',
            'assign' => ['json' => $sOutput],
        ];
    }

    /**
     *  method displays the affected products by the rule
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayExclusionRuleProducts(array $aPost = null)
    {
        // clean headers
        @ob_end_clean();

        $aAssign = [];
        $aExcludedProducts = [];

        $iRuleId = \Tools::getValue('iRuleId');

        if (!empty($iRuleId)) {
            $aProducts = exclusionDao::getProductExcludedById($iRuleId);

            foreach ($aProducts as $aProduct) {
                $oProduct = new \Product($aProduct['id_product'], true, \GMerchantCenterpro::$iCurrentLang);

                if (is_object($oProduct)) {
                    $sProductName = $oProduct->name;

                    // Use case manage the name with Combo value
                    if (!empty(\GMerchantCenterpro::$conf['GMCP_P_COMBOS'])) {
                        $sComboName = moduleTools::getProductCombinationName($aProduct['id_product_attribute'], \GMerchantCenterpro::$iCurrentLang, \GMerchantCenterpro::$iShopId);
                        $sProductName .= ' ' . $sComboName;
                    }

                    $aExcludedProducts[] = [
                        'id' => $oProduct->id,
                        'name' => $sProductName,
                    ];
                }
            }
        }

        unset($oProduct);

        $aAssign['aProductsData'] = $aExcludedProducts;

        // force xhr mode
        \GMerchantCenterpro::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/excluded-products.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method displays custom rules
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayExcludeValue(array $aPost)
    {
        // clean headers
        @ob_end_clean();
        $iRuleId = \Tools::getValue('iRuleId');
        $bAddTmpRules = \Tools::getValue('bUpdate');

        $aAssign = [];

        // Init the render object
        $oRender = new exclusionRender();

        $aAssign = $oRender->render($aPost['sExclusionType']);
        // Use case for update rule
        if (!empty($iRuleId) && !empty($bAddTmpRules)) {
            $aData = exclusionDao::getExclusionRulesById((int) $iRuleId);
            $aAssign['aDataRule'] = moduleTools::handleGetConfigurationData($aData['exclusion_value'], ['allowed_classes' => false]);
            $aAssign['sType'] = $aData['type'];
            $aAssign['iRuleId'] = $aData['id'];

            // Use case for to add on the tmp rules for update display
            $aTmpData = moduleTools::handleGetConfigurationData($aData['exclusion_value'], ['allowed_classes' => false]);

            foreach ($aTmpData as $sKey => $aRuleDetailData) {
                // Use case for a rules detail
                if ($sKey == 'aRulesDetail') {
                    foreach ($aRuleDetailData as $aRuleDetailFilter) {
                        if (!exclusionDao::addTmpDataRules(\GMerchantCenterpro::$iShopId, $aData['type'], $aRuleDetailFilter)) {
                            throw new \Exception(\GMerchantCenterpro::$oModule->l('Could not add tmp rules', 'adminUpdate') . '.', 700);
                        }
                    }
                }
            }
        }

        // Use case for feature values
        if (!empty($aPost['iFeatureId']) || $aPost['sExclusionType'] == 'feature') {
            $aAssign = $oRender->render('feature', $aPost, $aData);
        }

        // Use case for attribute values on ajax request
        if ($aPost['sExclusionType'] == 'attribute' || !empty($aPost['iAttributeId'])) {
            $aAssign = $oRender->render('attribute', $aPost, $aData);
        }
        // Use case for words values
        if ($aPost['sExclusionType'] == 'word') {
            $aAssign = $oRender->render('word', $aPost, $aData);
        }

        // Use case for category values
        if ($aPost['sExclusionType'] == 'category') {
            $aAssign = $oRender->render('category', $aPost, $aData);
        }

        // Use case for manufacturer values
        if ($aPost['sExclusionType'] == 'manufacturer') {
            $aAssign = $oRender->render('manufacturer', $aPost, $aData);
        }

        // Use case for supplier values
        if ($aPost['sExclusionType'] == 'supplier') {
            $aAssign = $oRender->render('supplier', $aPost, $aData);
        }

        // Force XHR
        \GMerchantCenterpro::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/exclusion-values.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method displays custom rules
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayRulesSummary(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        $aAssign = [];
        if (!empty($aPost['sTmpRules'])) {
            // Init the render object
            $oRender = new exclusionRender();

            // Use case for the delete
            if (!empty($aPost['sDelete']) && !empty($aPost['iRuleId'])) {
                exclusionDao::deleteTmpRules($aPost['iRuleId']);
            }

            $aRulesData = $oRender->render('Rules', $aPost);

            if (!empty($aRulesData)) {
                $aAssign['aTmpRules'] = $aRulesData;
                $aAssign['aProducts'] = $oRender->render('Products', null, $aRulesData);
            }
        }

        \GMerchantCenterpro::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/rules-summary.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method displays custom rules form configuration
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayExclusionRule(array $aPost = null)
    {
        exclusionDao::cleanTmpRules();
        exclusionDao::resetIncrement();

        $aAssign = [];
        // clean headers
        @ob_end_clean();
        $iRuleId = \Tools::getValue('iRuleId');
        $aDataRules = exclusionDao::getExclusionRulesById((int) $iRuleId);

        $aAssign['bRefreshRules '] = false;

        // Use case for the refresh rules
        $aAssign = [
            'aExclusionType' => moduleConfiguration::getExclusionType(),
            'aExclusionWordType' => moduleConfiguration::getRulesWordType(),
            'aFeatures' => \Feature::getFeatures(\GMerchantCenterpro::$iCurrentLang),
            'aAttributes' => \AttributeGroup::getAttributesGroups(\GMerchantCenterpro::$iCurrentLang),
            'iRuleId' => !empty($iRuleId) ? $iRuleId : '',
        ];

        // Use case for update rule
        if (!empty($iRuleId)) {
            $aAssign['aDataRule'] = $aDataRules;
        }

        // force xhr mode
        \GMerchantCenterpro::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/exclusion-rules.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * returns ids used for PrestaShop flags displaying
     *
     * @return string
     */
    private function getFlagIds()
    {
        // set
        $sFlagIds = '';

        if (!empty($this->aFlagIds)) {
            // loop on each ids
            foreach ($this->aFlagIds as $sId) {
                $sFlagIds .= $sId . '¤';
            }

            $sFlagIds = substr($sFlagIds, 0, strlen($sFlagIds) - 2);
        }

        return $sFlagIds;
    }

    /**
     * displays inventory
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayInventory(array $aPost = null)
    {
        $aAssign = [
            'sStoreCode' => \GMerchantCenterPro::$conf['GMCP_STORE_CODE'],
            'aLiaPickup' => [
                [
                    'value' => 'buy',
                    'label' => \GMerchantCenterpro::$oModule->l('Buy', 'adminUpdate'),
                ],
                [
                    'value' => 'reserve',
                    'label' => \GMerchantCenterpro::$oModule->l('Reserve', 'adminUpdate'),
                ],
                [
                    'value' => 'ship to store',
                    'label' => \GMerchantCenterpro::$oModule->l('Ship to store', 'adminUpdate'),
                ],
                [
                    'value' => 'not supported',
                    'label' => \GMerchantCenterpro::$oModule->l('Not supported', 'adminUpdate'),
                ],
            ],
            'sLiaPikcup' => \GMerchantCenterPro::$conf['GMCP_LIA_PICKUP'],
            'aLiaPickupSla' => [
                [
                    'value' => 'same day',
                    'label' => \GMerchantCenterpro::$oModule->l('Same day', 'adminUpdate'),
                ],
                [
                    'value' => 'next day',
                    'label' => \GMerchantCenterpro::$oModule->l('Next day', 'adminUpdate'),
                ],
                [
                    'value' => '2-day',
                    'label' => \GMerchantCenterpro::$oModule->l('2 days', 'adminUpdate'),
                ],
                [
                    'value' => '3-day',
                    'label' => \GMerchantCenterpro::$oModule->l('3 days', 'adminUpdate'),
                ],
                [
                    'value' => '4-day',
                    'label' => \GMerchantCenterpro::$oModule->l('4 days', 'adminUpdate'),
                ],
                [
                    'value' => '5-day',
                    'label' => \GMerchantCenterpro::$oModule->l('5 days', 'adminUpdate'),
                ],
                [
                    'value' => '6-day',
                    'label' => \GMerchantCenterpro::$oModule->l('6 days', 'adminUpdate'),
                ],
                [
                    'value' => 'multi-week',
                    'label' => \GMerchantCenterpro::$oModule->l('More than one week', 'adminUpdate'),
                ],
            ],
            'sLiaPikcupSla' => \GMerchantCenterPro::$conf['GMCP_LIA_PICKUP_SLA'],
        ];

        return [
            'tpl' => 'admin/local-inventory-settings.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * displays inventory feed
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayInventoryFeed(array $aPost = null)
    {
        $aAssign = [
            'iShopId' => \GMerchantCenterPro::$iShopId,
            'sGmcLink' => \GMerchantCenterPro::$conf['GMCP_LINK'],
            'bReporting' => \GMerchantCenterPro::$conf['GMCP_REPORTING'],
            'iTotalProductToExport' => moduleDao::getProductIds(\GMerchantCenterPro::$iShopId, (int) \GMerchantCenterPro::$conf['GMCP_EXPORT_MODE'], true),
            'iTotalDiscountToExport' => cartRulesDao::getCartRulesId(),
            'iTotalProduct' => moduleDao::countProducts(\GMerchantCenterPro::$iShopId, (int) \GMerchantCenterPro::$conf['GMCP_P_COMBOS']),
        ];

        if (!empty($aAssign['sGmcLink'])) {
            if (!empty(\GMerchantCenterPro::$aAvailableLangCurrencyCountry)) {
                foreach (\GMerchantCenterPro::$aAvailableLangCurrencyCountry as $aData) {
                    $aAssign['aFlyFileListLocal'][] = [
                        'currencyIso' => $aData['currencyIso'],
                        'iso_code' => $aData['langIso'],
                        'countryIso' => $aData['countryIso'],
                        'link' => \Context::getContext()->link->getModuleLink(moduleConfiguration::GMCP_MODULE_SET_NAME, moduleConfiguration::GMCP_CTRL_FLY, ['id_shop' => \GMerchantCenterPro::$iShopId, 'gmcp_lang_id' => $aData['langId'], 'country' => $aData['countryIso'], 'currency_iso' => $aData['currencyIso'], 'token' => \GMerchantCenterPro::$conf['GMCP_FEED_TOKEN'], 'sType' => 'flyOutput', 'feed_type' => 'local']),
                        'currencySign' => $aData['currencySign'],
                        'countryName' => $aData['countryName'],
                        'langName' => $aData['langName'],
                    ];
                }
            }
        }

        return [
            'tpl' => 'admin/feed-lia-list.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * displays google customer reviews settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayGoogleCustomerReviews(array $aPost = null)
    {
        $aAssign = [
            'merchantCenterId' => \GMerchantCenterPro::$conf['GMCP_MERCHANT_ID'],
            'sameDayProcess' => \GMerchantCenterPro::$conf['GMCP_SAME_DAY_PROCESS'],
            'activateBadge' => \GMerchantCenterPro::$conf['GMCP_GCR_BADGE'],
            'useProductGtin' => \GMerchantCenterPro::$conf['GMCP_GCR_PRODUCT_GTIN'],
            'activateGcr' => \GMerchantCenterPro::$conf['GMCP_GCR_ACTIVATE'],
            'cutOffHour' => \GMerchantCenterPro::$conf['GMCP_CUT_OFF_HOUR'],
            'cutOffMin' => \GMerchantCenterPro::$conf['GMCP_CUT_OFF_MIN'],
            'estimatedProcess' => \GMerchantCenterPro::$conf['GMCP_SHIPPING_PROCESS'],
            'weekDays' => moduleConfiguration::getWeekDays(),
            'holidays' => moduleConfiguration::getHolidays(),
            'closedDay' => explode(',', \GMerchantCenterPro::$conf['GMCP_CLOSED_DAY']),
            'closeHoliday' => explode(',', \GMerchantCenterPro::$conf['GMCP_HOLIDAYS']),
            'orderStatusSaved' => explode(',', \GMerchantCenterPro::$conf['GMCP_ORDER_STATE']),
            'haveToSelectOrderState' => \GMerchantCenterPro::$conf['GMCP_ORDER_STATE'],
            'shipTime' => moduleTools::handleGetConfigurationData(\GMerchantCenterPro::$conf['GMCP_SHIP_TIME'], ['allowed_classes' => false]),
            'carriers' => \Carrier::getCarriers((int) \GMerchantCenterPro::$iCurrentLang, true, false, false, null, 5),
            'orderStatuses' => \OrderState::getOrderStates((int) \GMerchantCenterPro::$iCurrentLang),
        ];

        return [
            'tpl' => 'admin/google-customer-reviews.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * method displays the new custom feed form
     *
     * @param array $aPost
     *
     * @return array
     */
    private function displayNewCustomFeed(array $aPost = null)
    {
        $aAssign = [
            'shop_lang' => \Language::getLanguages(false, (int) \GMerchantCenterPro::$iShopId),
            'country_shop' => \Country::getCountries(\GMerchantCenterPro::$iCurrentLang, true, false, false),
            'currency_shop' => \Currency::getCurrenciesByIdShop((int) \GMerchantCenterPro::$iShopId),
            'taxonomies' => moduleConfiguration::getTaxonomies(),
        ];

        return [
            'tpl' => 'admin/new-custom-feed.tpl',
            'assign' => $aAssign,
        ];
    }
}
