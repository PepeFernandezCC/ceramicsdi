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
use Gmerchantcenterpro\Models\customLabelDynamicBestSales;
use Gmerchantcenterpro\Models\customLabelDynamicCategories;
use Gmerchantcenterpro\Models\customLabelDynamicFeature;
use Gmerchantcenterpro\Models\customLabelDynamicLastProductNotOrder;
use Gmerchantcenterpro\Models\customLabelDynamicNewProduct;
use Gmerchantcenterpro\Models\customLabelDynamicPriceRange;
use Gmerchantcenterpro\Models\customLabelDynamicProducts;
use Gmerchantcenterpro\Models\customLabelTags;
use Gmerchantcenterpro\Models\Feeds;
use Gmerchantcenterpro\ModuleLib\moduleTools;

class adminDelete implements adminInterface
{
    /**
     * delete content
     *
     * @param string $sType => define which method to execute
     * @param array $aParam
     *
     * @return array
     */
    public function run($sType, array $aParam = [])
    {
        // set variables
        $aDisplayData = [];

        switch ($sType) {
            case 'label': // use case - delete custom label
            case 'exclusionRule': // use case - delete custom label
            case 'feed': // use case - delete custom feed
                // execute match function
                $aDisplayData = call_user_func_array([$this, 'delete' . ucfirst($sType)], [$aParam]);

                break;
            default:
                break;
        }

        return $aDisplayData;
    }

    /**
     * delete one tag label
     *
     * @param array $aPost
     *
     * @return array
     */
    private function deleteLabel(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $aData = [];
        $sDeleteType = \Tools::getValue('sDeleteType');
        $bContinu = false;
        $aIdsDelete = [];

        try {
            if (!empty($sDeleteType)) {
                if ($sDeleteType == 'one') {
                    $iTagId = \Tools::getValue('iTagId');
                    $bContinu = true;
                } elseif ($sDeleteType == 'bulk') {
                    $aIdsDelete = explode(',', \Tools::getValue('iTagIds'));
                    $bContinu = true;
                }
            }

            if ($bContinu == false) {
                throw new \Exception(\GMerchantCenterPro::$oModule->l('Your Custom label ID(s) are not valid', 'adminUpdate') . '.', 700);
            } else {
                if ($sDeleteType == 'one') {
                    if (!empty($iTagId)) {
                        customLabelDynamicProducts::deleteTag((int)$iTagId);
                        customLabelDynamicFeature::deleteFeatureSave((int)$iTagId);
                        customLabelDynamicCategories::deleteDynamicCat((int)$iTagId);
                        customLabelDynamicNewProduct::deleteDynamicNew((int)$iTagId);
                        customLabelDynamicBestSales::deleteDynamicBestSales((int)$iTagId);
                        customLabelDynamicPriceRange::deleteDynamicPriceRange((int)$iTagId);
                        customLabelDynamicLastProductNotOrder::deleteTag((int)$iTagId);
                        customLabelTags::deleteTag((int)$iTagId, moduleConfiguration::GMCP_LABEL_LIST);
                    }
                } elseif ($sDeleteType == 'bulk') {
                    foreach ($aIdsDelete as $aCurrentClId) {
                        if (!empty($aCurrentClId)) {
                            customLabelDynamicProducts::deleteTag((int)$aCurrentClId);
                            customLabelDynamicFeature::deleteFeatureSave((int)$aCurrentClId);
                            customLabelDynamicCategories::deleteDynamicCat((int)$aCurrentClId);
                            customLabelDynamicNewProduct::deleteDynamicNew((int)$aCurrentClId);
                            customLabelDynamicBestSales::deleteDynamicBestSales((int)$aCurrentClId);
                            customLabelDynamicPriceRange::deleteDynamicPriceRange((int)$aCurrentClId);
                            customLabelDynamicLastProductNotOrder::deleteTag((int)$aCurrentClId);
                            customLabelTags::deleteTag((int)$aCurrentClId, moduleConfiguration::GMCP_LABEL_LIST);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }

        // get configuration options
        moduleTools::getConfiguration();

        // get run of admin display in order to display first page of admin with basics settings updated
        $aDisplay = adminDisplay::create()->run('google');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ], $aData);

        return $aDisplay;
    }

    /**
     *  method delete exclusion rules
     *
     * @param array $aPost
     *
     * @return array
     */
    private function deleteExclusionRule(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $aData = [];

        try {
            $iRuleId = \Tools::getValue('iRuleId');
            $sType = \Tools::getValue('sDeleteType');

            if (empty($iRuleId) || empty($sType)) {
                throw new \Exception(\GMerchantCenterPro::$oModule->l('Your rule ID isn\'t valid or the type of deletion is no valide', 'adminUpdate') . '.', 100);
            } else {
                if (!exclusionDao::deleteExclusionRules($iRuleId, $sType)) {
                    throw new \Exception(\GMerchantCenterPro::$oModule->l('Error while deleting the rule', 'adminUpdate') . '.', 101);
                }

                if (!exclusionDao::deleteProductExcluded($iRuleId)) {
                    throw new \Exception(\GMerchantCenterPro::$oModule->l('Error while deleting excluded products', 'adminUpdate') . '.', 102);
                }
            }
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        // get configuration options
        moduleTools::getConfiguration();

        // get run of admin display in order to display first page of admin with basics settings updated
        $aDisplay = adminDisplay::create()->run('feed');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ], $aData);

        return $aDisplay;
    }

    /**
     *  method delete exclusion rules
     *
     * @param array $aPost
     *
     * @return array
     */
    private function deleteFeed(array $aPost)
    {
        if (\GMerchantCenterPro::$sQueryMode == 'xhr') {
            // clean headers
            @ob_end_clean();
        }

        // set
        $aData = [];
        $exportMode = '';

        try {
            $idFeed = \Tools::getValue('id_feed');
            if (!empty($idFeed)) {
                Feeds::deleteFeed($idFeed);
                $exportMode = \Tools::getValue('export_mode');
            }
            // Todo delete
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        // get configuration options
        moduleTools::getConfiguration();

        // get run of admin display in order to display first page of admin with basics settings updated
        $aDisplay = adminDisplay::create()->run('feedList');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
            'exportMode' => $exportMode,
        ], $aData);

        return $aDisplay;
    }

    /**
     * set singleton
     *
     * @return adminDelete
     */
    public static function create()
    {
        static $oDelete;

        if (null === $oDelete) {
            $oDelete = new adminDelete();
        }

        return $oDelete;
    }
}
