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
use Gmerchantcenterpro\Exclusion\exclusionDao;
use Gmerchantcenterpro\Exclusion\exclusionTools;
use Gmerchantcenterpro\Models\customLabelTags;
use Gmerchantcenterpro\Models\exportBrands;
use Gmerchantcenterpro\Models\exportCategories;
use Gmerchantcenterpro\Models\Feeds;
use Gmerchantcenterpro\Models\googleTaxonomy;
use Gmerchantcenterpro\ModuleLib\labelTools;
use Gmerchantcenterpro\ModuleLib\moduleTools;

/**
 * This class handle all update made on the module configuration
 */
class adminUpdate implements adminInterface
{
    /**
     * update all tabs content of admin page
     *
     * @param string $sType => define which method to execute
     * @param array $aParam
     *
     * @return array
     */
    public function run($sType, array $aParam = [])
    {
        $aDisplayData = [];

        switch ($sType) {
            case 'stepPopup':
            case 'basic':
            case 'shopLink':
            case 'feed':
            case 'advancedfeed':
            case 'feedList':
            case 'label':
            case 'labelState':
            case 'customLabelList':
            case 'position':
            case 'customLabelDate':
            case 'customCheck':
            case 'google':
            case 'reporting':
            case 'googleCategoriesSync':
            case 'xml':
            case 'exclusionRule':
            case 'rulesList':
            case 'inventory':
            case 'newFeed':
            case 'googleCustomerReviews':
                $aDisplayData = call_user_func_array([$this, 'update' . ucfirst($sType)], [$aParam]);

                break;
            default:
                break;
        }

        return $aDisplayData;
    }

    /**
     * @param array $aPost
     *
     * @return array
     */
    private function updateStepPopup(array $aPost)
    {
        @ob_end_clean();
        $aAssign = [];

        \Configuration::updateValue('GMCP_CONF_STEP_3', 1);
        \GMerchantCenterPro::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/body.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * @param array $aPost
     *
     * @return array
     */
    private function updateBasic(array $aPost)
    {
        @ob_end_clean();
        $aData = [];

        try {
            // register title
            $sShopLink = \Tools::getValue('bt_link');
            $sAdvancedProdName = \Tools::getValue('bt_advanced-prod-name');
            $words = \Tools::getValue('bt_excluded_words');
            $excluded_words = [];

            // clean the end slash if exists
            if (substr($sShopLink, -1) == '/') {
                $sShopLink = substr($sShopLink, 0, strlen($sShopLink) - 1);
            }
            \Configuration::updateValue('GMCP_LINK', str_replace(' ', '', $sShopLink));
            \Configuration::updateValue('GMCP_SIMPLE_PROD_ID', \Tools::getValue('bt_simple_id'));
            \Configuration::updateValue('GMCP_ID_PREFIX', moduleTools::cleanUpPrefix(\Tools::getValue('bt_prefix-id')));
            \Configuration::updateValue('GMCP_AJAX_CYCLE', \Tools::getValue('bt_ajax-cycle', 200));
            \Configuration::updateValue('GMCP_IMG_SIZE', \Tools::getValue('bt_image-size'));
            \Configuration::updateValue('GMCP_IMG_COVER_POSITION', \Tools::getValue('bt_image-cover-position'));
            \Configuration::updateValue('GMCP_HOME_CAT_ID', \Tools::getValue('bt_home-cat-id'));
            \Configuration::updateValue('GMCP_ADD_IMAGES', \Tools::getValue('bt_add_images'));
            \Configuration::updateValue('GMCP_PRODUCT_DIMENSION', \Tools::getValue('bt_manage_product_size'));
            \Configuration::updateValue('GMCP_FORCE_IDENTIFIER', \Tools::getValue('bt_identifier_exist'));
            \Configuration::updateValue('GMCP_ADD_CURRENCY', \Tools::getValue('bt_add-currency'));
            \Configuration::updateValue('GMCP_COND', \Tools::getValue('bt_product-condition'));
            \Configuration::updateValue('GMCP_FEED_PROTECTION', 1);
            \Configuration::updateValue('GMCP_FEED_TOKEN', \Tools::getValue('bt_feed-token'));
            \Configuration::updateValue('GMCP_ADV_PROD_TITLE', \Tools::getValue('bt_advanced-prod-title'));
            \Configuration::updateValue('GMCP_CONF_STEP_1', 1);
            \Configuration::updateValue('GMCP_ADV_PRODUCT_NAME', $sAdvancedProdName);
            \Configuration::updateValue('GMCP_FEED_PREF_ID', \Tools::getValue('bt_feed-tag-id'));

            if (\Tools::getIsset('bt_prod-title')) {
                \Configuration::updateValue('GMCP_P_TITLE', \Tools::getValue('bt_prod-title'));
            }

            if ($sAdvancedProdName == 5) {
                $this->updateLang($aPost, 'bt_advanced_prefix_name', 'GMCP_ADV_PROD_NAME_PREFIX', false, \GMerchantCenterPro::$oModule->l('product name prefix', 'adminUpdate'), false);
                $this->updateLang($aPost, 'bt_advanced_suffix_name', 'GMCP_ADV_PROD_NAME_SUFFIX', false, \GMerchantCenterPro::$oModule->l('product name suffix', 'adminUpdate'), false);
            }
            $this->updateLang($aPost, 'bt_home-cat-name', 'GMCP_HOME_CAT', false, \GMerchantCenterPro::$oModule->l('type of product sold', 'adminUpdate'));

            if (!empty($words)) {
                // Use case if we have 2 expression or more
                $strPos = strpos($words, ',');
                if (!empty($strPos)) {
                    $excluded_words = explode(',', $words);
                } else {
                    $excluded_words[0] = $words;
                }
            }

            \Configuration::updateValue('GMCP_EXCLUDED_WORDS', json_encode($excluded_words));
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }

        moduleTools::getConfiguration();
        $aDisplay = adminDisplay::create()->run('basics');

        $aDisplay['assign'] = array_merge($aDisplay['assign'], ['bUpdate' => (empty($aData['aErrors']) ? true : false)], $aData);
        \GMerchantCenterPro::$sQueryMode = 'xhr';

        return $aDisplay;
    }

    /**
     * @param array $aPost
     *
     * @return array
     */
    private function updateFeed(array $aPost)
    {
        @ob_end_clean();
        $aData = [];

        try {
            \Configuration::updateValue('GMCP_CONF_STEP_2', 1);

            /* USE CASE - update categories and brands to export */
            if (\Tools::getIsset('bt_export')) {
                $bExportMode = \Tools::getValue('bt_export');

                \Configuration::updateValue('GMCP_EXPORT_MODE', $bExportMode);

                if ($bExportMode == 0) {
                    $aCategoryBox = \Tools::getValue('bt_category-box');

                    if (!empty($aCategoryBox)) {
                        exportCategories::cleanTable((int)\Context::getContext()->shop->id);

                        foreach ($aCategoryBox as $iCatId) {
                            $export_category = new exportCategories();
                            $export_category->id_category = (int) $iCatId;
                            $export_category->id_shop = (int)\Context::getContext()->shop->id;
                            $export_category->add();
                        }
                    }
                } else {
                    $aBrandBox = \Tools::getValue('bt_brand-box');

                    if (!empty($aBrandBox)) {
                        exportBrands::cleanTable((int)\Context::getContext()->shop->id);

                        foreach ($aBrandBox as $iBrandId) {
                            $export_brand = new exportBrands();
                            $export_brand->id_brands = (int) $iBrandId;
                            $export_brand->id_shop = (int)\Context::getContext()->shop->id;
                            $export_brand->add();
                        }
                    }
                }
            }

            /* USE CASE - update exclusion rules */
            if (\Tools::getIsset('bt_export-oos')) {
                $bExportOOSMode = \Tools::getValue('bt_export-oos');
                \Configuration::updateValue('GMCP_EXPORT_OOS', $bExportOOSMode);

                if ($bExportOOSMode) {
                    \Configuration::updateValue('GMCP_EXPORT_PROD_OOS_ORDER', \Tools::getValue('bt_product-oos-order'));
                }
            }

            // handle if we export or not products without EAN code
            if (\Tools::getIsset('bt_excl-no-ean')) {
                $bExportNoEan = \Tools::getValue('bt_excl-no-ean');
                \Configuration::updateValue('GMCP_EXC_NO_EAN', $bExportNoEan);
            }
            // handle if we export or not products without manufacturer code
            if (\Tools::getIsset('bt_excl-no-mref')) {
                $bExportNoMref = \Tools::getValue('bt_excl-no-mref');
                \Configuration::updateValue('GMCP_EXC_NO_MREF', $bExportNoMref);
            }

            // handle if we export products over a min price
            if (\Tools::getIsset('bt_min-price')) {
                $fMinPrice = \Tools::getValue('bt_min-price');
                $fMinPrice = !empty($fMinPrice) ? number_format((float)str_replace(',', '.', $fMinPrice), 2) : 0.00;
                \Configuration::updateValue('GMCP_MIN_PRICE', $fMinPrice);
            }

            // handle if we export products over a weight
            if (\Tools::getIsset('bt_max-weight')) {
                $fMaxWeight = \Tools::getValue('bt_max-weight');
                $fMaxWeight = !empty($fMaxWeight) ? number_format((float)str_replace(',', '.', $fMaxWeight), 2) : 0.00;
                \Configuration::updateValue('GMCP_MAX_WEIGHT', $fMaxWeight);
            }
            /* USE CASE - update feed data options */
            if (\Tools::getIsset('bt_prod-title')) {
                // how to export products
                \Configuration::updateValue('GMCP_P_TITLE', \Tools::getValue('bt_prod-title'));
            }

            /* USE CASE - update combo export */
            if (\Tools::getIsset('bt_prod-combos')) {
                // how to export products
                $bProductCombos = \Tools::getValue('bt_prod-combos');
                \Configuration::updateValue('GMCP_P_COMBOS', $bProductCombos);

                if (!empty($bProductCombos)) {
                    // use case - options around the combination URLs for the export each combination as a single product
                    if (\Tools::getIsset('bt_rewrite-num-attr')) {
                        \Configuration::updateValue('GMCP_URL_NUM_ATTR_REWRITE', \Tools::getValue('bt_rewrite-num-attr'));
                    }
                    if (\Tools::getIsset('bt_include_attribute_values')) {
                        \Configuration::updateValue('GMCP_INCL_ATTR_VALUE', \Tools::getValue('bt_include_attribute_values'));
                    }
                    if (\Tools::getIsset('bt_incl-attr-id')) {
                        \Configuration::updateValue('GMCP_URL_ATTR_ID_INCL', \Tools::getValue('bt_incl-attr-id'));
                    }
                    if (\Tools::getIsset('handle_backorder')) {
                        \Configuration::updateValue('GMCP_HANDLE_BACK_ORDER', \Tools::getValue('handle_backorder'));
                    }
                    if (\Tools::getIsset('bt_combo-separator')) {
                        \Configuration::updateValue('GMCP_COMBO_SEPARATOR', \Tools::getValue('bt_combo-separator'));
                    }
                    if (\Tools::getIsset('bt_include_anchor')) {
                        \Configuration::updateValue('GMCP_INCL_ANCHOR', \Tools::getValue('bt_include_anchor'));
                    }
                }
            }

            // how to use the product desc
            if (\Tools::getIsset('bt_prod-desc-type')) {
                $iProdDescType = \Tools::getValue('bt_prod-desc-type');
                \Configuration::updateValue('GMCP_P_DESCR_TYPE', \Tools::getValue('bt_prod-desc-type'));
            }

            // product availability
            if (\Tools::getIsset('bt_incl-stock')) {
                $bInclStock = \Tools::getValue('bt_incl-stock');
                \Configuration::updateValue('GMCP_INC_STOCK', $bInclStock);
            }

            // include adult tag
            if (\Tools::getIsset('bt_incl-tag-adult')) {
                $bInclAdultTag = \Tools::getValue('bt_incl-tag-adult');
                \Configuration::updateValue('GMCP_INC_TAG_ADULT', $bInclAdultTag);
            }
            // include cost of good sold
            if (\Tools::getIsset('bt_incl-tag-cost')) {
                \Configuration::updateValue('GMCP_INC_COST', \Tools::getValue('bt_incl-tag-cost'));
            }

            if (\Tools::getIsset('bt_ships_from')) {
                \Configuration::updateValue('GMCP_SHIPS_FROM', strtoupper(\Tools::getValue('bt_ships_from')));
            }

            // include size tag
            if (\Tools::getIsset('bt_incl-size')) {
                $sInclSize = \Tools::getValue('bt_incl-size');
                $aSizeIds = \Tools::getValue('bt_size-opt');
                \Configuration::updateValue('GMCP_INC_SIZE', $sInclSize);

                // update attributes and the feature for size tag
                if (!empty($sInclSize) && !empty($aSizeIds)) {
                    \Configuration::updateValue('GMCP_SIZE_OPT', moduleTools::handleSetConfigurationData($aSizeIds));
                }
            }

            // include color tag
            if (\Tools::getIsset('bt_incl-color')) {
                $sInclColor = \Tools::getValue('bt_incl-color');
                $aColorIds = \Tools::getValue('bt_color-opt');
                \Configuration::updateValue('GMCP_INC_COLOR', $sInclColor);
                // update attributes and the feature for color tag
                if (!empty($sInclColor) && !empty($aColorIds)) {
                    \Configuration::updateValue('GMCP_COLOR_OPT', moduleTools::handleSetConfigurationData($aColorIds));
                }
            }

            /* USE CASE - update apparel feed options */
            // include material tag
            if (\Tools::getIsset('bt_incl-material')) {
                $bInclMaterial = \Tools::getValue('bt_incl-material');
                \Configuration::updateValue('GMCP_INC_MATER', $bInclMaterial);
            }

            // include pattern tag
            if (\Tools::getIsset('bt_incl-pattern')) {
                $bInclPattern = \Tools::getValue('bt_incl-pattern');
                \Configuration::updateValue('GMCP_INC_PATT', $bInclPattern);
            }

            // include gender tag
            if (\Tools::getIsset('bt_incl-gender')) {
                $bInclGender = \Tools::getValue('bt_incl-gender');
                \Configuration::updateValue('GMCP_INC_GEND', $bInclGender);
            }

            // include age group tag
            if (\Tools::getIsset('bt_incl-age')) {
                $bInclAge = \Tools::getValue('bt_incl-age');
                \Configuration::updateValue('GMCP_INC_AGE', $bInclAge);
            }

            // include size type
            if (\Tools::getIsset('bt_incl-size_type')) {
                $bInclSizeType = \Tools::getValue('bt_incl-size_type');
                \Configuration::updateValue('GMCP_SIZE_TYPE', $bInclSizeType);
            }

            // include size system
            if (\Tools::getIsset('bt_incl-size_system')) {
                $bInclSizeSystem = \Tools::getValue('bt_incl-size_system');
                \Configuration::updateValue('GMCP_SIZE_SYSTEM', $bInclSizeSystem);
            }

            /* USE case for advanced tag */
            if (\Tools::getIsset('bt_incl-energy')) {
                $bInclEnergy = \Tools::getValue('bt_incl-energy');
                \Configuration::updateValue('GMCP_INC_ENERGY', $bInclEnergy);
            }

            if (\Tools::getIsset('bt_excl_dest')) {
                $bExclDest = \Tools::getValue('bt_excl_dest');
                \Configuration::updateValue('GMCP_EXCLUDED_DEST', $bExclDest);
            }

            // include exclusion destination
            if (\Tools::getIsset('bt_excl_country')) {
                $bExclCountry = \Tools::getValue('bt_excl_country');
                \Configuration::updateValue('GMCP_EXCLUDED_COUNTRY', $bExclCountry);
            }

            if (\Tools::getIsset('bt_incl-shipping-label')) {
                \Configuration::updateValue('GMCP_INC_SHIPPING_LABEL', \Tools::getValue('bt_incl-shipping-label'));
            }

            if (\Tools::getIsset('bt_incl_unit_pricing_measure')) {
                \Configuration::updateValue('GMCP_INC_UNIT_PRICING', \Tools::getValue('bt_incl_unit_pricing_measure'));
            }

            if (\Tools::getIsset('bt_incl_unit_base_pricing_measure')) {
                \Configuration::updateValue('GMCP_INC_B_UNIT_PRICING', \Tools::getValue('bt_incl_unit_base_pricing_measure'));
            }

            /* USE CASE - update tax and shipping fees options */
            if (\Tools::getIsset('bt_manage-shipping')) {
                $bShippingUse = \Tools::getValue('bt_manage-shipping');
                \Configuration::updateValue('GMCP_SHIPPING_USE', $bShippingUse);
            }

            if (\Tools::getIsset('bt_manage-dimension')) {
                $bDimension = \Tools::getValue('bt_manage-dimension');
                \Configuration::updateValue('GMCP_DIMENSION', $bDimension);
            }

            if (\Tools::getIsset('bt_free_shipping_price')) {
                \Configuration::updateValue('GMCP_FREE_SHIPPING_PRICE', \Tools::getValue('bt_free_shipping_price'));
            }

            if (\Tools::getIsset('bt_ship-carriers')) {
                $aShippingCarriers = [];
                $aPostShippingCarriers = \Tools::getValue('bt_ship-carriers');

                if (!empty($aPostShippingCarriers) && is_array($aPostShippingCarriers)) {
                    foreach ($aPostShippingCarriers as $iKey => $mVal) {
                        $aShippingCarriers[$iKey] = $mVal;
                    }
                    $sShippingCarriers = moduleTools::handleSetConfigurationData($aShippingCarriers);
                } else {
                    $sShippingCarriers = '';
                }
                \Configuration::updateValue('GMCP_SHIP_CARRIERS', $sShippingCarriers);
            }

            if (\Tools::getIsset('bt_ship-carriers_free_product_price')) {
                $shippingFreeCarrier = [];
                $aPostShippingCarriers = \Tools::getValue('bt_ship-carriers_free_product_price');

                if (!empty($aPostShippingCarriers) && is_array($aPostShippingCarriers)) {
                    foreach ($aPostShippingCarriers as $iKey => $mVal) {
                        $shippingFreeCarrier[$iKey] = $mVal;
                    }
                    $sShippingCarriers = moduleTools::handleSetConfigurationData($shippingFreeCarrier);
                } else {
                    $sShippingCarriers = '';
                }
                \Configuration::updateValue('GMCP_FREE_PROD_PRICE_SHIP_CARRIERS', $sShippingCarriers);
            }

            if (\Tools::getIsset('bt_ship-carriers_no_tax')) {
                $shippingNoTax = [];
                $carrierNoTax = \Tools::getValue('bt_ship-carriers_no_tax');

                if (!empty($carrierNoTax) && is_array($carrierNoTax)) {
                    foreach ($carrierNoTax as $iKey => $mVal) {
                        $shippingNoTax[$iKey] = $mVal;
                    }
                    $shippingNoTaxSaved = moduleTools::handleSetConfigurationData($shippingNoTax);
                } else {
                    $shippingNoTaxSaved = '';
                }
                \Configuration::updateValue('GMCP_NO_TAX_SHIP_CARRIERS', $shippingNoTaxSaved);
            } else {
                \Configuration::updateValue('GMCP_NO_TAX_SHIP_CARRIERS', '');
            }

            if (\Tools::getIsset('bt_ship-carriers_free')) {
                $freeCarrierData = [];
                $freeCarrier = \Tools::getValue('bt_ship-carriers_free');

                if (!empty($freeCarrier) && is_array($freeCarrier)) {
                    foreach ($freeCarrier as $iKey => $mVal) {
                        $freeCarrierData[$iKey] = $mVal;
                    }
                    $freeCarrierSaved = moduleTools::handleSetConfigurationData($freeCarrierData);
                } else {
                    $freeCarrierSaved = '';
                }
                \Configuration::updateValue('GMCP_FREE_SHIP_CARRIERS', $freeCarrierSaved);
            } else {
                \Configuration::updateValue('GMCP_FREE_SHIP_CARRIERS', '');
            }

            // update attributes and the feature for size tag
            if (\Tools::getIsset('hiddenProductIds')) {
                $sExcludedIds = \Tools::getValue('hiddenProductIds');

                // get an array of
                $aExcludedIds = !empty($sExcludedIds) ? explode('-', $sExcludedIds) : [];

                if (!empty($aExcludedIds)) {
                    array_pop($aExcludedIds);
                }

                \Configuration::updateValue('GMCP_PROD_EXCL', moduleTools::handleSetConfigurationData($aExcludedIds));
            }

            if (\Tools::getIsset('hiddenProductFreeShippingIds')) {
                $sFreeShippingProductIds = \Tools::getValue('hiddenProductFreeShippingIds');
                $aIdsFreeShipping = !empty($sFreeShippingProductIds) ? explode('-', $sFreeShippingProductIds) : [];

                if (!empty($sFreeShippingProductIds)) {
                    array_pop($aIdsFreeShipping);
                }
                \Configuration::updateValue('GMCP_FREE_SHIP_PROD', moduleTools::handleSetConfigurationData($aIdsFreeShipping));
            }

            if (\Tools::getIsset('hiddenProductPauseIds')) {
                $sPausedProductProductIds = \Tools::getValue('hiddenProductPauseIds');
                $aIdsPausedProduct = !empty($sPausedProductProductIds) ? explode('-', $sPausedProductProductIds) : [];

                if (!empty($sPausedProductProductIds)) {
                    array_pop($aIdsPausedProduct);
                }
                \Configuration::updateValue('GMCP_PAUSED_PROD', moduleTools::handleSetConfigurationData($aIdsPausedProduct));
            }

            if (\Tools::getIsset('bt_tag_pause')) {
                $tagPause = \Tools::getValue('bt_tag_pause');
                \Configuration::updateValue('GMCP_TAG_PAUSE_VALUE', $tagPause);
            }

            // select the order to check the EAN-13 or UPC
            if (\Tools::getIsset('bt_gtin-pref')) {
                $sGtinPref = \Tools::getValue('bt_gtin-pref');
                \Configuration::updateValue('GMCP_GTIN_PREF', $sGtinPref);
            }

            if (\Tools::getValue('sDisplay') == 'tax') {
                // update feed tax
                $aTmpFeedTax = \Tools::getValue('bt_feed-tax') != false ? \Tools::getValue('bt_feed-tax') : [];
                $aFeedTaxHidden = \Tools::getValue('bt_feed-tax-hidden');
                $aFeedTax = [];

                foreach ($aFeedTaxHidden as $sFeed) {
                    $aFeedTax[$sFeed] = in_array($sFeed, $aTmpFeedTax) ? 1 : 0;
                }

                \Configuration::updateValue('GMCP_FEED_TAX', moduleTools::handleSetConfigurationData($aFeedTax));
            }

            \Configuration::updateValue('GMCP_USE_GEOLOC', \Tools::getValue('bt_geoloc'));
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }

        moduleTools::getConfiguration(['GMCP_COLOR_OPT', 'GMCP_SIZE_OPT', 'GMCP_SHIP_CARRIERS', 'GMCP_PROD_EXCL', 'GMCP_FEED_TAX', 'GMCP_FREE_SHIP_PROD', 'GMCP_PAUSED_PROD']);
        $aDisplay = adminDisplay::create()->run('feed');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ], $aData);

        return $aDisplay;
    }

    /**
     * @param array $aPost
     *
     * @return array
     */
    private function updateAdvancedfeed(array $aPost)
    {
        $aData = [];

        try {
            \Configuration::updateValue('GMCP_DSC_FILT_NAME', \Tools::getValue('bt_option-name'));
            \Configuration::updateValue('GMCP_DSC_FILT_DATE', \Tools::getValue('bt_option-date'));
            \Configuration::updateValue('GMCP_DSC_FILT_MIN_AMOUNT', \Tools::getValue('bt_option-min-amount'));
            \Configuration::updateValue('GMCP_DSC_FILT_VALUE', \Tools::getValue('bt_option-value'));
            \Configuration::updateValue('GMCP_DSC_FILT_TYPE', \Tools::getValue('bt_option-type'));
            \Configuration::updateValue('GMCP_DSC_FILT_CUMU', \Tools::getValue('bt_option-cumulable'));
            \Configuration::updateValue('GMCP_DSC_FILT_FOR', \Tools::getValue('bt_option-for'));
            \Configuration::updateValue('GMCP_DSC_NAME', \Tools::getValue('bt_discount-name'));
            \Configuration::updateValue('GMCP_DSC_DATE_FROM', \Tools::getValue('bt_discount-date-from'));
            \Configuration::updateValue('GMCP_DSC_DATE_TO', \Tools::getValue('bt_discount-date-to'));
            \Configuration::updateValue('GMCP_DSC_VALUE_MIN', \Tools::getValue('bt_discount-value-min'));
            \Configuration::updateValue('GMCP_DSC_VALUE_MAX', \Tools::getValue('bt_discount-value-max'));
            \Configuration::updateValue('GMCP_DSC_MIN_AMOUNT', \Tools::getValue('bt_discount-min-amount'));
            \Configuration::updateValue('GMCP_DSC_TYPE', \Tools::getValue('bt_discount-type'));
            \Configuration::updateValue('GMCP_DSC_CUMULABLE', \Tools::getValue('bt_discount-cumulable'));
            \Configuration::updateValue('GMCP_PROMO_DEST', moduleTools::handleSetConfigurationData(\Tools::getValue('bt-discount_channel')));

            // Use case for review feed
            $sWord = \Tools::getValue('bt_words-review-forbidden');
            $aWords = [];

            if (!empty($sWord)) {
                // Use case if we have 2 expression or more
                $strPos = strpos($sWord, ',');
                if (!empty($strPos)) {
                    $aWords = explode(',', $sWord);
                } else {
                    $aWords[0] = $sWord;
                }
            }

            \Configuration::updateValue('GMCP_FORBIDDEN_WORDS', moduleTools::handleSetConfigurationData($aWords));
            \Configuration::updateValue('GMCP_INV_PRICE', \Tools::getValue('bt_inventory-price'));
            \Configuration::updateValue('GMCP_INV_STOCK', \Tools::getValue('bt_inventory-stock'));
            \Configuration::updateValue('GMCP_INV_SALE_PRICE', \Tools::getValue('bt_inventory-sale-price'));
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }

        moduleTools::getConfiguration();
        $aDisplay = adminDisplay::create()->run('advancedFeed');

        $aDisplay['assign'] = array_merge($aDisplay['assign'], ['bUpdate' => (empty($aData['aErrors']) ? true : false)], $aData);

        return $aDisplay;
    }

    /**
     * @param array $aPost
     *
     * @return array
     */
    private function updateFeedList(array $aPost)
    {
        @ob_end_clean();
        $aData = [];

        try {
            $sDisplay = \Tools::getValue('sDisplay');

            // update cron export
            if ($sDisplay == 'data') {
                $aCronExport = \Tools::getValue('bt_cron-export');
                \Configuration::updateValue('GMCP_CHECK_EXPORT', moduleTools::handleSetConfigurationData($aCronExport));
            }
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }

        moduleTools::getConfiguration(['GMCP_CHECK_EXPORT', 'GMCP_FEED_TAX']);
        $aDisplay = adminDisplay::create()->run('feedList');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], ['bUpdate' => (empty($aData['aErrors']) ? true : false)], $aData);

        return $aDisplay;
    }

    /**
     * @param array $aPost
     *
     * @return array
     */
    private function updateLabel(array $aPost)
    {
        @ob_end_clean();
        $aAssign = [];

        try {
            // get the label name
            $sLabelName = \Tools::getValue('bt_label-name');
            $iTagId = \Tools::getValue('bt_tag-id');
            $sLabelType = \Tools::getValue('bt_cl-type');
            $bActivateTag = \Tools::getValue('bt_cl-statut');
            $sDateEnd = \Tools::getValue('bt_cl_date_end');
            $sDateNewProduct = \Tools::getValue('bt_cl_dyn_date_start');
            $sProductSpecific = \Tools::getValue('hiddenProductIds-cl');
            $aProductSpecific = !empty($sProductSpecific) ? explode('-', $sProductSpecific) : [];
            $sBestSaleType = \Tools::getValue('dynamic_best_sales_unit');
            $fBestSaleAmount = \Tools::getValue('bt_cl_dyn_amount');
            $sBestSaleStartDate = \Tools::getValue('bt_dyn_best_sale_start');
            $sBestSalesEndDate = \Tools::getValue('bt_dyn_best_sale_end');
            $fPriceMin = \Tools::getValue('bt_dyn_min_price');
            $fPriceMax = \Tools::getValue('bt_dyn_max_price');
            $sLastOrderedStart = \Tools::getValue('bt_dyn_last_order_start');
            $sLastOrderedEnd = \Tools::getValue('bt_dyn_last_order_end');
            $noOrderDateStart = \Tools::getValue('bt_dyn_not_sell_start');
            $noOrderDateEnd = \Tools::getValue('bt_dyn_not_sell_end');
            $iLastId = (int) customLabelTags::getLastId();
            $iNextId = $iLastId + 1;
            $customSetPosition = \Tools::getValue('bt_cl_association');

            if (!empty($sLabelName)) {
                // Use case update tag
                if (!empty($iTagId)) {
                    $iPositionTag = customLabelTags::getTagPosition($iTagId);
                    customLabelTags::updateTag($iTagId, $sLabelName, $sLabelType, $customSetPosition, $bActivateTag, $iPositionTag, $sDateEnd);
                    labelTools::cleanTag($iTagId, $sLabelType);
                } // use case - create tag
                else {
                    $iTagId = customLabelTags::addTag((int)\Context::getContext()->shop->id, $sLabelName, $sLabelType, $customSetPosition, $bActivateTag, $iNextId, $sDateEnd);
                }

                if ($sLabelType == 'custom_label' || $sLabelType == 'dynamic_new_product') {
                    labelTools::handleDefautTag($iTagId, $sLabelType, $aProductSpecific);
                }

                if ($sLabelType == 'dynamic_features_list') {
                    labelTools::handleFeatureTag($iTagId, (int) \Tools::getValue('dynamic_features_list'));
                }

                if ($sLabelType == 'dynamic_categorie') {
                    labelTools::handleCatDynamicTag($iTagId, \Tools::getValue('bt_category-box'));
                }

                if ($sLabelType == 'dynamic_new_product') {
                    labelTools::handleDynamicNewProduct($iTagId, $sDateNewProduct);
                }

                if ($sLabelType == 'dynamic_best_sale') {
                    labelTools::handleDynamicBestSales($iTagId, $sBestSaleType, $fBestSaleAmount, $sBestSaleStartDate, $sBestSalesEndDate);
                }

                if ($sLabelType == 'dynamic_price_range') {
                    labelTools::handleDynamicPriceRange($iTagId, $fPriceMin, $fPriceMax);
                }

                if ($sLabelType == 'dynamic_last_order') {
                    labelTools::handleDynamicLastOrdered($iTagId, $sLastOrderedStart, $sLastOrderedEnd);
                }

                if ($sLabelType == 'dynamic_promotion') {
                    labelTools::handleDynamicPromotion($iTagId, $sLastOrderedStart, $sLastOrderedEnd);
                }

                if ($sLabelType == 'dynamic_not_sell') {
                    labelTools::handleDynamicNotOrder($iTagId, $noOrderDateStart, $noOrderDateEnd);
                }
            }
        } catch (\Exception $e) {
            $aAssign['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }

        // check update OK
        $aAssign['bUpdate'] = empty($aAssign['aErrors']) ? true : false;
        $aAssign['sErrorInclude'] = moduleTools::getTemplatePath('views/templates/admin/error.tpl');
        \GMerchantCenterPro::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/google-custom-label-update.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * @param array $aPost
     *
     * @return array
     */
    private function updateLabelState(array $aPost)
    {
        @ob_end_clean();
        $aData = [];
        $sDeleteType = \Tools::getValue('sDeleteType');
        $iTagId = \Tools::getValue('iTagId');
        $aTagIds = \Tools::getValue('iTagIds');

        try {
            if (in_array($sDeleteType, ['one', 'bulk'])) {
                if ($sDeleteType == 'one' && !empty($iTagId)) {
                    customLabelTags::updateTagStatus($iTagId, (int) \Tools::getValue('bActive'));
                } elseif (
                    $sDeleteType == 'bulk'
                    && !empty($aTagIds)
                ) {
                    $aIdsDelete = explode(',', $aTagIds);

                    foreach ($aIdsDelete as $aCurrentClId) {
                        customLabelTags::updateTagStatus((int)$aCurrentClId, (int) \Tools::getValue('bActive'));
                    }
                }
            } else {
                throw new \Exception(\GMerchantCenterPro::$oModule->l('Your custom label ID is not valid or some parameters are wrong', 'adminUpdate') . '.', 600);
            }
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }

        moduleTools::getConfiguration();
        $aDisplay = adminDisplay::create()->run('google');
        $aDisplay['assign'] = array_merge($aDisplay['assign'], ['bUpdate' => (empty($aData['aErrors']) ? true : false)], $aData);

        return $aDisplay;
    }

    /**
     * @param array $aPost
     *
     * @return array
     */
    private function updatePosition(array $aPost)
    {
        \GMerchantCenterPro::$sQueryMode = 'xhr';

        $iTagIdMoveToNewPos = \Tools::getValue('iTagIdMoveToNewPos');
        $iNewPosition = \Tools::getValue('iNewPosition');
        $iTagIdMoveToOldPos = \Tools::getValue('iTagIdMoveToOldPos');
        $iOldPosition = \Tools::getValue('iOldPosition');
        $aData = [];

        customLabelTags::updatePositionTag($iTagIdMoveToNewPos, $iNewPosition, (int)\Context::getContext()->shop->id);
        customLabelTags::updatePositionTag($iTagIdMoveToOldPos, $iOldPosition, (int)\Context::getContext()->shop->id);
        moduleTools::getConfiguration();

        $aDisplay = adminDisplay::create()->run('google');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], ['bUpdate' => (empty($aData['aErrors']) ? true : false)], $aData);

        return $aDisplay;
    }

    /**
     * @param array|null $aPost
     *
     * @return array
     */
    private function updateCustomLabelDate(array $aPost = null)
    {
        @ob_end_clean();
        $aData = [];

        $sDateToday = date('Y-m-d');

        // get all tag information id and date
        $aTags = customLabelTags::getTagDate((int)\Context::getContext()->shop->id);

        // make the process for each tag with date
        foreach ($aTags as $aTag) {
            $iDateCompare = moduleTools::dateCompare($sDateToday, (string) $aTag['end_date']);
            $iPositionTag = customLabelTags::getTagPosition((int) $aTag['id_tag']);

            // made update tag statut if date is over
            if ($iDateCompare == 1) {
                // update tag statut
                customLabelTags::updateProcessDate((int) $aTag['id_tag'], 0, $iPositionTag['position']);
            }
        }

        $aDisplay = adminDisplay::create()->run('google');
        $aDisplay['assign'] = array_merge($aDisplay['assign'], ['bUpdate' => (empty($aData['aErrors']) ? true : false)], $aData);

        return $aDisplay;
    }

    /**
     * @param array $aPost
     *
     * @return array
     */
    private function updateGoogle(array $aPost)
    {
        @ob_end_clean();
        $aData = [];

        try {
            \Configuration::updateValue('GMCP_UTM_CAMPAIGN', \Tools::getValue('bt_utm-campaign'));
            \Configuration::updateValue('GMCP_UTM_SOURCE', \Tools::getValue('bt_utm-source'));
            \Configuration::updateValue('GMCP_UTM_MEDIUM', \Tools::getValue('bt_utm-medium'));
            \Configuration::updateValue('GMCP_UTM_CONTENT', \Tools::getValue('bt_utm_content'));
            \Configuration::updateValue('GMCP_CL_USE_DEFAULT_CAT', \Tools::getValue('cl_default_cat'));
            \Configuration::updateValue('GMCP_CL_MODE_OR_AND', \Tools::getValue('bt_cl_mode_or_and'));
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }

        // get configuration options
        moduleTools::getConfiguration(['GMCP_COLOR_OPT', 'GMCP_SIZE_OPT', 'GMCP_SHIP_CARRIERS']);
        $aDisplay = adminDisplay::create()->run('google');
        $aDisplay['assign'] = array_merge($aDisplay['assign'], ['bUpdate' => (empty($aData['aErrors']) ? true : false)], $aData);

        return $aDisplay;
    }

    /**
     * @param array $aPost
     *
     * @return array
     */
    private function updateReporting(array $aPost)
    {
        @ob_end_clean();
        $aData = [];

        try {
            \Configuration::updateValue('GMCP_REPORTING', \Tools::getValue('bt_reporting'));
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }

        moduleTools::getConfiguration();
        $aDisplay = adminDisplay::create()->run('reporting');

        $aDisplay['assign'] = array_merge($aDisplay['assign'], ['bUpdate' => (empty($aData['aErrors']) ? true : false)], $aData);

        return $aDisplay;
    }

    /**
     * @param array $aPost
     *
     * @return array
     */
    private function updateGoogleCategoriesSync(array $aPost)
    {
        @ob_end_clean();
        $aAssign = [];

        try {
            $sLangIso = \Tools::getValue('sLangIso');

            if ($sLangIso != false) {
                $sContent = moduleTools::getGoogleFile(moduleConfiguration::GMCP_GOOGLE_TAXONOMY_URL . 'taxonomy.' . basename($sLangIso) . '.txt');

                if ($sContent || \Tools::strlen($sContent) != 0) {
                    $aLines = explode("\n", trim($sContent));

                    if (is_array($aLines)) {
                        googleTaxonomy::clean($sLangIso);

                        foreach ($aLines as $index => $sLine) {
                            if ($index > 0) {
                                googleTaxonomy::addTaxonomy($sLine, $sLangIso);
                            }
                        }
                    }
                }
                $aAssign['aCountryTaxonomies'] = moduleTools::getAvailableTaxonomyCountries();

                foreach ($aAssign['aCountryTaxonomies'] as $sIsoCode => &$aTaxonomy) {
                    $aTaxonomy['countryList'] = implode(', ', $aTaxonomy['countries']);
                    $aTaxonomy['currentUpdated'] = $sLangIso == $sIsoCode ? true : false;
                    $aTaxonomy['updated'] = googleTaxonomy::checkTaxonomyUpdate($sIsoCode);
                }
            }
        } catch (\Exception $e) {
            $aAssign['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }

        // check update OK
        $aAssign['bUpdate'] = empty($aAssign['aErrors']) ? true : false;
        $aAssign['sURI'] = moduleTools::truncateUri('&sAction');
        $aAssign['sCtrlParamName'] = moduleConfiguration::GMCP_PARAM_CTRL_NAME;
        $aAssign['sController'] = moduleConfiguration::GMCP_ADMIN_CTRL;
        $aAssign['aQueryParams'] = moduleConfiguration::getRequestParams();
        $aAssign['iCurrentLang'] = intval(\GMerchantCenterPro::$iCurrentLang);
        $aAssign['sCurrentLang'] = \GMerchantCenterPro::$sCurrentLang;
        $aAssign['taxonomyController'] = \Context::getContext()->link->getAdminLink('AdminGmcpTaxonomy');
        \GMerchantCenterPro::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/google-category-list.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * @param array $aPost
     *
     * @return array
     */
    private function updateXml(array $aPost)
    {
        @ob_end_clean();
        $aAssign = [];

        try {
            $iShopId = \Tools::getValue('iShopId');
            $sFilename = \Tools::getValue('sFilename');
            $iLangId = \Tools::getValue('iLangId');
            $sLangIso = \Tools::getValue('sLangIso');
            $sCountryIso = \Tools::getValue('sCountryIso');
            $iFloor = \Tools::getValue('iFloor');
            $iTotal = \Tools::getValue('iTotal');
            $iProcess = \Tools::getValue('iProcess');
            $sDataFeedType = \Tools::getValue('feed_type');

            if (($iShopId != false && is_numeric($iShopId))
                && ($sFilename != false && is_string($sFilename))
                && ($iLangId != false && is_numeric($iLangId))
                && ($sLangIso != false && is_string($sLangIso))
                && ($sCountryIso != false && is_string($sCountryIso))
                && ($iFloor !== false && is_numeric($iFloor))
                && ($iTotal != false && is_numeric($iTotal))
                && ($iProcess !== false && is_numeric($iProcess))
            ) {
                $_POST['iShopId'] = $iShopId;
                $_POST['sFilename'] = $sFilename;
                $_POST['iLangId'] = $iLangId;
                $_POST['sLangIso'] = $sLangIso;
                $_POST['sCountryIso'] = \Tools::strtoupper($sCountryIso);
                $_POST['iFloor'] = $iFloor;
                $_POST['iStep'] = \GMerchantCenterPro::$conf['GMCP_AJAX_CYCLE'];
                $_POST['iTotal'] = $iTotal;
                $_POST['iProcess'] = $iProcess;
                $_POST['feed_type'] = $sDataFeedType;

                // exec the generate class to generate the XML files
                $aGenerate = adminGenerate::create()->run('xml', ['reporting' => \GMerchantCenterPro::$conf['GMCP_REPORTING']]);

                if (empty($aGenerate['assign']['aErrors'])) {
                    $aAssign['status'] = 'ok';
                    $aAssign['counter'] = $iFloor + $_POST['iStep'];
                    $aAssign['process'] = $aGenerate['assign']['process'];
                } else {
                    $aAssign['status'] = 'ko';
                    $aAssign['error'] = $aGenerate['assign']['aErrors'];
                }
            } else {
                $sMsg = \GMerchantCenterPro::$oModule->l(
                    'The server has returned an unsecure request error (wrong parameters)! Please check each parameter by comparing type and value below!',
                    'adminUpdate'
                ) . '.<br/>';
                $sMsg .= \GMerchantCenterPro::$oModule->l('Shop ID', 'adminUpdate') . ': ' . $iShopId . '<br/>' . \GMerchantCenterPro::$oModule->l('File name', 'adminUpdate') . ': ' . $sFilename . '<br/>'
                    . \GMerchantCenterPro::$oModule->l('Language ID', 'adminUpdate') . ': ' . $iLangId . '<br/>'
                    . \GMerchantCenterPro::$oModule->l('Language ISO', 'adminUpdate') . ': ' . $sLangIso . '<br/>' . \GMerchantCenterPro::$oModule->l('country ISO', 'adminUpdate') . ': ' . $sCountryIso . '<br/>'
                    . \GMerchantCenterPro::$oModule->l('Step', 'adminUpdate') . ': ' . $iFloor . '<br/>' . \GMerchantCenterPro::$oModule->l('Total products to process', 'adminUpdate') . ': ' . $iTotal . '<br/>'
                    . \GMerchantCenterPro::$oModule->l('Total products to process (without counting combinations)', 'adminUpdate') . ': ' . $iTotal . '<br/>'
                    . \GMerchantCenterPro::$oModule->l('Stock the real number of products to process', 'adminUpdate') . ': ' . $iProcess . '<br/>';

                throw new \Exception($sMsg, 594);
            }
        } catch (\Exception $e) {
            $aAssign['status'] = 'ko';
            $aAssign['error'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }
        \GMerchantCenterPro::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/feed-generate.tpl',
            'assign' => ['json' => moduleTools::jsonEncode($aAssign)],
        ];
    }

    /**
     * method update the exclusion rules
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateExclusionRule(array $aPost)
    {
        @ob_end_clean();
        $aAssign = [];
        $aRuleDetails = [];

        try {
            $bActive = \Tools::getValue('bt_excl-rule-active');
            $sExclusionName = \Tools::getValue('bt-exclusion-name');
            $sExclusionType = \Tools::getValue('bt-exclusion-type');
            $iExclusionId = \Tools::getValue('bt-exclusion-id');
            $sExclusionWordType = \Tools::getValue('bt-exclusion-word-type');
            $sExclusionWord = \Tools::getValue('word-exclusion-value');
            $sExclusionFeature = \Tools::getValue('bt-exclusion-feature');
            $sExclusionFeatureValue = \Tools::getValue('bt-feature-value');
            $sExclusionAttribute = \Tools::getValue('bt-exclusion-attribute');
            $sExclusionAttributeValue = \Tools::getValue('bt-attribute-value');
            $sExclusionCategories = \Tools::getValue('bt_category-box');
            $sExclusionManufacturer = \Tools::getValue('bt_brand-box');
            $sExclusionSupplier = \Tools::getValue('bt_supplier-box');
            $sProductSpecificExclusion = \Tools::getValue('hiddenProductIds');

            // Use case to build the exlusion rule when it is a word type
            if ($sExclusionType == 'word') {
                if (!empty($sExclusionWordType) && !empty($sExclusionWord)) {
                    $aRulevalue = [
                        'exclusionOn' => $sExclusionWordType,
                        'exclusionData' => $sExclusionWord,
                    ];
                }
            } elseif ($sExclusionType == 'feature') {
                $aRulevalue = [
                    'exclusionOn' => $sExclusionFeature,
                    'exclusionData' => $sExclusionFeatureValue,
                ];
            } elseif ($sExclusionType == 'attribute') {
                $aRulevalue = [
                    'exclusionOn' => $sExclusionAttribute,
                    'exclusionData' => $sExclusionAttributeValue,
                ];
            } elseif ($sExclusionType == 'specificProduct') {
                $aRulevalue = [
                    'exclusionOn' => '',
                    'exclusionData' => $sProductSpecificExclusion,
                ];
            } elseif ($sExclusionType == 'category') {
                $aRulevalue = [
                    'exclusionOn' => '',
                    'exclusionData' => $sExclusionCategories,
                ];
            } elseif ($sExclusionType == 'manufacturer') {
                $aRulevalue = [
                    'exclusionOn' => '',
                    'exclusionData' => $sExclusionManufacturer,
                ];
            } elseif ($sExclusionType == 'supplier') {
                $aRulevalue = [
                    'exclusionOn' => '',
                    'exclusionData' => $sExclusionSupplier,
                ];
            }

            // Use case to manage the product ids according to the rules values
            $aRulevalue['aProductIds'] = exclusionTools::getProductFromRules();
            $aRuleDetails = exclusionDao::getTmpRules();

            // Stock the rules preferences
            foreach ($aRuleDetails as $aRuleDetail) {
                $aRulevalue['aRulesDetail'][] = $aRuleDetail['exclusion_values'];
            }
            $sExclusionValue = moduleTools::handleSetConfigurationData($aRulevalue);

            // use case for add rules
            if (empty($iExclusionId)) {
                if (exclusionDao::addExclusionRule($bActive, (int)\Context::getContext()->shop->id, $sExclusionName, $sExclusionType, $sExclusionValue)) {
                    // Use case for the product exclusion tab according to the option active
                    $aLastRule = exclusionDao::getLastRuleId();
                    foreach ($aRulevalue['aProductIds'] as $aProductData) {
                        if (!empty(\GMerchantCenterPro::$conf['GMCP_P_COMBOS'])) {
                            if (!exclusionDao::addProductExcluded((int)$aLastRule['last_id'], $aProductData['id_product'], $aProductData['id_product_attribute'])) {
                                throw new \Exception(\GMerchantCenterPro::$oModule->l('Error while adding the rule', 'adminUpdate') . '.', 1101);
                            }
                        } else {
                            if (!exclusionDao::addProductExcluded($aLastRule['last_id'], $aProductData, 0)) {
                                throw new \Exception(\GMerchantCenterPro::$oModule->l('Error while adding the rule', 'adminUpdate') . '.', 1102);
                            }
                        }
                    }
                }
            } else {
                if (exclusionDao::updateExclusionRule($bActive, (int)\Context::getContext()->shop->id, $sExclusionName, $sExclusionType, $sExclusionValue, $iExclusionId)) {
                    // Use case for the product exclusion tab according to the option active
                    if (empty($bActive)) {
                        if (!exclusionDao::deleteProductExcluded($iExclusionId)) {
                            throw new \Exception(\GMerchantCenterPro::$oModule->l('Error while deleting product exclusions', 'adminUpdate') . '.', 1104);
                        }
                    } else {
                        foreach ($aRulevalue['aProductIds'] as $aProductData) {
                            if (!empty(\GMerchantCenterPro::$conf['GMCP_P_COMBOS'])) {
                                if (!exclusionDao::addProductExcluded($iExclusionId, $aProductData['id_product'], $aProductData['id_product_attribute'])) {
                                    throw new \Exception(\GMerchantCenterPro::$oModule->l('Error while adding product exclusions', 'adminUpdate') . '.', 1105);
                                }
                            } else {
                                if (!exclusionDao::addProductExcluded($iExclusionId, $aProductData, 0)) {
                                    throw new \Exception(\GMerchantCenterPro::$oModule->l('Error while adding product exclusions', 'adminUpdate') . '.', 1106);
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $aAssign['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }

        // check update OK
        $aAssign['bUpdate'] = empty($aAssign['aErrors']) ? true : false;
        $aAssign['sErrorInclude'] = moduleTools::getTemplatePath('views/templates/admin/error.tpl');
        \GMerchantCenterPro::$sQueryMode = 'xhr';

        return [
            'tpl' => 'admin/confirm-exclusion-rules.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * @param array $aPost
     *
     * @return array
     */
    private function updateRulesList(array $aPost)
    {
        @ob_end_clean();
        $aData = [];

        try {
            $iRuleId = \Tools::getValue('iRuleId');
            $sType = \Tools::getValue('sUpdateType');
            $bActivate = \Tools::getValue('bActivate');

            if (!empty($iRuleId) && !empty($sType)) {
                if (exclusionDao::updateRulesStatus($iRuleId, $sType, $bActivate)) {
                    if (!empty($bActivate)) {
                        $aProducts = exclusionTools::getProductFromRules();
                        foreach ($aProducts as $aProductData) {
                            if (!exclusionDao::addProductExcluded($iRuleId, $aProductData['id_product'], $aProductData['id_product_attribute'])) {
                                throw new \Exception(\GMerchantCenterPro::$oModule->l('Error while adding product exclusions', 'adminUpdate') . '.', 1202);
                            }
                        }
                    } else {
                        if (!exclusionDao::deleteProductExcluded($iRuleId)) {
                            throw new \Exception(\GMerchantCenterPro::$oModule->l('Error while deleting product exclusions', 'adminUpdate') . '.', 1203);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }

        moduleTools::getConfiguration();
        $aDisplay = adminDisplay::create()->run('feed');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], ['bUpdate' => (empty($aData['aErrors']) ? true : false)], $aData);
        unset($aData);

        return $aDisplay;
    }

    /**
     * @param array $aPost
     * @param mixed $sFieldName
     * @param mixed $sGlobalName
     * @param bool $bCheckOnly
     * @param string $sErrorDisplayName
     * @param bool $bNeedAllVal
     *
     * @return array
     */
    private function updateLang(array $aPost, $sFieldName, $sGlobalName, $bCheckOnly = false, $sErrorDisplayName = '', $bNeedAllVal = true)
    {
        // check title in each active language
        $aLangs = [];

        foreach (\Language::getLanguages() as $nKey => $aLang) {
            if (empty($aPost[$sFieldName . '_' . $aLang['id_lang']]) && !empty($bNeedAllVal)) {
                $sException = \GMerchantCenterPro::$oModule->l('One title of', 'adminUpdate') . ' " ' . (!empty($sErrorDisplayName) ? $sErrorDisplayName : $sFieldName) . ' " ' . \GMerchantCenterPro::$oModule->l('have not been filled', 'adminUpdate') . '.';

                throw new \Exception($sException, 1300);
            } else {
                $aLangs[$aLang['id_lang']] = strip_tags($aPost[$sFieldName . '_' . $aLang['id_lang']]);
            }
        }
        if (!$bCheckOnly) {
            // update titles
            if (!\Configuration::updateValue($sGlobalName, moduleTools::handleSetConfigurationData($aLangs))) {
                $sException = \GMerchantCenterPro::$oModule->l('An error occurred during', 'adminUpdate') . ' " ' . $sGlobalName . ' " ' . \GMerchantCenterPro::$oModule->l('update', 'adminUpdate') . '.';

                throw new \Exception($sException, 1301);
            }
        }

        return $aLangs;
    }

    /**
     * update custom label product association durong the data feed update
     *
     * @param array $aPost
     *
     * @return void
     */
    private function updateCustomCheck(array $aPost = null)
    {
        labelTools::updateCustomLabelFeedProcess();
    }

    /**
     * update iventory settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateInventory(array $aPost)
    {
        @ob_end_clean();
        $aData = [];

        try {
            // Update configuration values
            \Configuration::updateValue('GMCP_STORE_CODE', \Tools::getValue('bt_store_code'));
            \Configuration::updateValue('GMCP_LIA_PICKUP', \Tools::getValue('bt_lia_pickup'));
            \Configuration::updateValue('GMCP_LIA_PICKUP_SLA', \Tools::getValue('bt_lia_pickup_sla'));
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }

        moduleTools::getConfiguration();
        $aDisplay = adminDisplay::create()->run('inventory');
        $aDisplay['assign'] = array_merge($aDisplay['assign'], ['bUpdate' => (empty($aData['aErrors']) ? true : false)], $aData);
        \GMerchantCenterPro::$sQueryMode = 'xhr';

        return $aDisplay;
    }

    /**
     * method update new feed config settings
     *
     * @param array $aPost
     *
     * @return array
     */
    private function updateNewFeed(array $aPost)
    {
        @ob_end_clean();
        $aData = [];

        $lang_code = \Tools::getValue('bt-new-feed-lang');
        $country_code = \Tools::getValue('bt-new-feed-country');
        $currency = \Tools::getValue('bt-new-feed-currency');
        $taxonomy = \Tools::getValue('bt-new-feed-taxonomy');

        // Check if the data feed combination exist
        $feedExist = Feeds::feedExist($lang_code, $country_code, $currency, $taxonomy, (int) (int)\Context::getContext()->shop->id);

        // Use case error message if the data feed already exist or add the data on the data base
        if (!empty($feedExist)) {
            $aData['aErrors'] = true;
        } else {
            // Make the insert
            $feed = new Feeds();
            $feed->iso_lang = $lang_code;
            $feed->iso_country = $country_code;
            $feed->iso_currency = $currency;
            $feed->taxonomy = $taxonomy;
            $feed->id_shop = (int)\Context::getContext()->shop->id;
            $feed->feed_is_default = 0;
            $feed->add();
        }

        try {
            $aAssign = ['saved' => true, 'feed_exist' => !empty($feedExist) ? true : false];
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
            $aAssign['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        moduleTools::getConfiguration();
        $aDisplay = adminDisplay::create()->run('newCustomFeed');

        $aDisplay['assign'] = array_merge($aDisplay['assign'], ['bUpdate' => (empty($aData['aErrors']) ? true : false)], $aData);
        \GMerchantCenterPro::$sQueryMode = 'xhr';

        // destruct
        unset($aData);

        return $aDisplay;
    }

    /**
     * @param array $aPost
     *
     * @return array
     */
    private function updateGoogleCustomerReviews(array $aPost)
    {
        @ob_end_clean();
        $aData = [];

        $closedDay = !empty(\Tools::getValue('closed_days')) ? \Tools::getValue('closed_days') : [];
        $closedHolidays = !empty(\Tools::getValue('holidays')) ? \Tools::getValue('holidays') : [];
        $orderStates = !empty(\Tools::getValue('bt_ok_order_states')) ? (\Tools::getValue('bt_ok_order_states')) : [];

        try {
            \Configuration::updateValue('GMCP_MERCHANT_ID', (int) \Tools::getValue('bt_merchant-center-id'));
            \Configuration::updateValue('GMCP_SAME_DAY_PROCESS', \Tools::getValue('same_day_process'));
            \Configuration::updateValue('GMCP_GCR_BADGE', \Tools::getValue('activate_badge'));
            \Configuration::updateValue('GMCP_GCR_PRODUCT_GTIN', \Tools::getValue('use_product_gtin'));
            \Configuration::updateValue('GMCP_GCR_ACTIVATE', \Tools::getValue('activate_gcr'));
            \Configuration::updateValue('GMCP_CUT_OFF_HOUR', (int) \Tools::getValue('cut_off_day_hour'));
            \Configuration::updateValue('GMCP_CUT_OFF_MIN', (int) \Tools::getValue('cut_off_day_minute'));
            \Configuration::updateValue('GMCP_SHIPPING_PROCESS', (int) \Tools::getValue('bt_estimated_process'));
            \Configuration::updateValue('GMCP_CLOSED_DAY', implode(',', $closedDay));
            \Configuration::updateValue('GMCP_HOLIDAYS', implode(',', $closedHolidays));
            \Configuration::updateValue('GMCP_ORDER_STATE', implode(',', $orderStates));
            \Configuration::updateValue('GMCP_SHIP_TIME', moduleTools::handleSetConfigurationData(\Tools::getValue('ship_time')));
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }

        moduleTools::getConfiguration();
        $aDisplay = adminDisplay::create()->run('googleCustomerReviews');

        $aDisplay['assign'] = array_merge($aDisplay['assign'], ['bUpdate' => (empty($aData['aErrors']) ? true : false)], $aData);
        \GMerchantCenterPro::$sQueryMode = 'xhr';

        return $aDisplay;
    }

    /**
     * create() method set singleton
     *
     * @return adminUpdate
     */
    public static function create()
    {
        static $oUpdate;

        if (null === $oUpdate) {
            $oUpdate = new adminUpdate();
        }

        return $oUpdate;
    }
}
