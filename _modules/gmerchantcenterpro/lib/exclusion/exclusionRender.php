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

namespace Gmerchantcenterpro\Exclusion;

if (!defined('_PS_VERSION_')) {
    exit;
}
use Gmerchantcenterpro\Configuration\moduleConfiguration;
use Gmerchantcenterpro\ModuleLib\moduleTools;

class exclusionRender
{
    // the current lang
    private $iLang = '';

    /**
     * method display all configured data admin tabs
     *
     * @param string $sType => define which method to execute
     * @param array $aParam
     *
     * @return mixed
     */
    public function render($sType, array $aParam = null, $aDataRules = null)
    {
        $this->iLang = \GMerchantCenterPro::$iCurrentLang;

        if (!empty($sType)) {
            return call_user_func_array([$this, 'render' . ucfirst($sType)], [$aParam, $aDataRules]);
        }
    }

    /**
     * method return the suppliers values
     *
     * @param array $aParam
     * @param array $aDataRules
     *
     * @return array
     */
    private function renderSupplier($aParam = null, $aDataRules = null)
    {
        $aSuppliers = \Supplier::getSuppliers();
        $aIndexedSuppliers = [];
        $iRuleId = \Tools::getValue('iRuleId');

        if (!empty($iRuleId)) {
            $aData = exclusionDao::getExclusionRulesById((int)$iRuleId);
            $aRuleData = moduleTools::handleGetConfigurationData($aData['exclusion_value']);
            $aIndexedSuppliers = $aRuleData['exclusionData'];
        }

        $aData['aFormatSuppliers'] = moduleTools::recursiveSupplierTree($aSuppliers, $aIndexedSuppliers);

        if (empty($aData['aFormatSuppliers'])) {
            $aData['sSupplierMessage'] = 1;
        }

        return $aData;
    }

    /**
     * method return the word values
     *
     * @param array $aParam
     * @param array $aDataRules
     *
     * @return array
     */
    private function renderWord($aParam = null, $aDataRules = null)
    {
        $aData = [];

        if (!empty($aDataRules)) {
            // get the exclusion value one
            $aTmpData = moduleTools::handleGetConfigurationData($aDataRules['exclusion_value']);
            $aData['sExclusionOn'] = $aTmpData['exclusionOn'];
            $aData['iExclusionData'] = $aTmpData['exclusionData'];
            $aData['bDisplayField'] = true;
        }

        // To manage the refresh element on the form
        $bRefresh = !empty($aParam['bRefresh']) ? true : false;

        if (empty($bRefresh)) {
            $aData['aWordExlusionTypeWord'] = moduleConfiguration::getRulesWordType();
        } else {
            $aData['bDisplayField'] = true;
        }

        return $aData;
    }

    /**
     * method return the feature values
     *
     * @param array $aParam
     * @param array $aDataRules
     *
     * @return array
     */
    private function renderFeature($aParam = null, $aDataRules = null)
    {
        $aData = [];

        if (!empty($aDataRules)) {
            // get the exclusion value one
            $aTmpData = moduleTools::handleGetConfigurationData($aDataRules['exclusion_value']);
            $aData['iExclusionData'] = $aTmpData['exclusionData'];
        }

        // To manage the refresh element on the form
        $bRefresh = !empty($aParam['bRefresh']) ? true : false;

        if (empty($bRefresh)) {
            $aData['aFeatures'] = \Feature::getFeatures($this->iLang);
        } else {
            $aFeatureData = !empty($aParam['iFeatureId']) ? \FeatureValue::getFeatureValuesWithLang($this->iLang, (int)$aParam['iFeatureId']) : [];
            $aData['aFeaturesValues'] = $aFeatureData;
            $aData['bEmptyFeatureValue'] = empty($aFeatureData) ? true : false;
        }

        return $aData;
    }

    /**
     * method return the attribute values
     *
     * @param array $aParam
     * @param array $aDataRules
     *
     * @return array
     */
    private function renderAttribute($aParam = null, $aDataRules = null)
    {
        $aData = [];

        if (!empty($aDataRules)) {
            // get the exclusion value one
            $aTmpData = moduleTools::handleGetConfigurationData($aDataRules['exclusion_value']);
            $aData['iExclusionData'] = $aTmpData['exclusionData'];
        }

        // To manage the refresh element on the form
        $bRefresh = !empty($aParam['bRefresh']) ? true : false;

        if (empty($bRefresh)) {
            $aData['aAttributes'] = \AttributeGroup::getAttributesGroups($this->iLang);
        } else {
            $aAttributeData = !empty($aParam['iAttributeId']) ? \AttributeGroup::getAttributes($this->iLang,
                (int)$aParam['iAttributeId']) : [];
            $aData['aAttributeValues'] = $aAttributeData;
            $aData['bEmptyAttributeValue'] = empty($aAttributeData) ? true : false;
        }

        return $aData;
    }

    /**
     * method return the word values
     *
     * @param array $aParam
     * @param array $aDataRules
     *
     * @return array
     */
    private function renderSpecificProduct($aParam = null, $aDataRules = null)
    {
        $aData = [];

        $aData['bSpecifiqueProduct'] = true;

        return $aData;
    }

    /**
     * method return the current rules configuration
     *
     * @param array $aParam
     *
     * @return array
     */
    private function renderRules($aParam = null)
    {
        $bNeedUpdate = false;
        $aData = [];

        // To force the update rules data when we don't manage the delete
        if (!empty($aParam['sTmpRules']) && empty($aParam['sDelete'])) {
            $bNeedUpdate = true;
        }

        // Use case for the update simuation of the ajax
        if ($aParam['sTmpRules'] == 'false') {
            $bNeedUpdate = false;
        }

        $aData = exclusionTools::extractTmpRulesData($aParam, $bNeedUpdate);
        $aOutputData = [];

        // Format the output data
        foreach ($aData as $sKey => $sValue) {
            $aOutputData[$sKey]['id'] = $sValue['id'];
            $aOutputData[$sKey]['sType'] = $sValue['type'];
            $aOutputData[$sKey]['data'] = exclusionTools::getRulesLabel($sValue['type']);
            $aOutputData[$sKey]['filter'] = exclusionTools::getRulesDetail($sValue['type'], moduleTools::handleGetConfigurationData($sValue['exclusion_values']));

            // Use case to get the attribute id to manage the good values on the product name + combination
            if ($sValue['type'] == 'attribute') {
                $aOutputData[$sKey]['attributeId'] = moduleTools::handleGetConfigurationData($sValue['exclusion_values'])['filter_2'];
            }
        }

        return $aOutputData;
    }

    /**
     * method return the current rules configuration
     *
     * @param array $aParam
     * @param array $aDataRules
     *
     * @return array
     */
    private function renderProducts($aParam = null, $aDataRules = null)
    {
        $aOutputDataProduct = [];

        foreach ($aDataRules as $aDataRule) {
            // For all cases except attribute because the behavior can be different
            if (empty(\GMerchantCenterPro::$conf['GMCP_P_COMBOS'])) {
                $aProducts = array_unique(exclusionTools::getProductFromRules());
                foreach ($aProducts as $sKey => $aProductIds) {
                    // Init product data to get details
                    $oProduct = new \Product((int)$aProductIds, true, (int)\GMerchantCenterPro::$iCurrentLang);
                    if (is_object($oProduct)) {
                        $aOutputDataProduct[$sKey]['id'] = $oProduct->id;
                        $aOutputDataProduct[$sKey]['name'] = $oProduct->name;
                    }
                }
            } else {
                $aProducts = \GMerchantCenterPro::$conf['GMCP_P_COMBOS'] == 0 ? array_unique(exclusionTools::getProductFromRules()) : exclusionTools::getProductFromRules();
                foreach ($aProducts as $sPropductKey => $aProductId) {
                    // Check if product ID is an array or scalar
                    $iProductId = is_array($aProductId) ? $aProductId['id_product'] : (int)$aProductId;
                    $oProduct = new \Product($iProductId, true, (int)\GMerchantCenterPro::$iCurrentLang);

                    // Only process combination name if we have a valid product and combination ID
                    $sCombinationName = '';
                    if (is_array($aProductId) && isset($aProductId['id_product_attribute']) && $aProductId['id_product_attribute'] > 0) {
                        $aCombinationAttrData = moduleTools::getProductCombinationName(
                            (int)$aProductId['id_product_attribute'],
                            \GMerchantCenterPro::$iCurrentLang,
                            (int)\Context::getContext()->shop->id
                        );
                        $sCombinationName = ' ' . $aCombinationAttrData;
                    }

                    if (is_object($oProduct)) {
                        $aOutputDataProduct[$sPropductKey]['id'] = $iProductId;
                        $aOutputDataProduct[$sPropductKey]['name'] = $oProduct->name . $sCombinationName;
                    }
                }
            }
        }

        return $aOutputDataProduct;
    }
}
