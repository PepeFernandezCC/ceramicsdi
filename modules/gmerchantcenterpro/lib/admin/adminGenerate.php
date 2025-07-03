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
use Gmerchantcenterpro\Dao\moduleDao;
use Gmerchantcenterpro\Models\exclusionProduct;
use Gmerchantcenterpro\ModuleLib\moduleTools;
use Gmerchantcenterpro\Xml\xmlGenerateDiscount;
use Gmerchantcenterpro\Xml\xmlGenerateLocal;
use Gmerchantcenterpro\Xml\xmlGenerateProduct;
use Gmerchantcenterpro\Xml\xmlGenerateReviews;

class adminGenerate implements adminInterface
{
    /**
     * @var object : current object generate by _generateXml
     */
    public $oCurrentObj;

    /**
     * generate data feed content
     *
     * @param string $sType => define which method to execute
     * @param array $aParam
     *
     * @return array
     */
    public function run($sType, array $aParam = null)
    {
        // set variables
        $aData = [];

        switch ($sType) {
            case 'xml': // use case - generate XML file
            case 'flyOutput': // use case - generate XML file on fly output
            case 'cron': // use case - generate XML file via the cron execution
                // execute match function
                $aData = call_user_func_array([$this, 'generate' . ucfirst($sType)], [$aParam]);

                break;
            default:
                break;
        }

        return $aData;
    }

    /**
     * generate an XML file
     *
     * @param array $aPost
     *
     * @return array
     *
     * @throws
     */
    private function generateXml($aParams = null)
    {
        try {
            $sType = !empty(strtolower(\Tools::getValue('sFeedType'))) ? strtolower(\Tools::getValue('sFeedType')) : strtolower(\Tools::getValue('feed_type'));

            if ($sType == 'product') {
                $feed = new xmlGenerateProduct($sType, $aParams);
            } elseif ($sType == 'local') {
                $feed = new xmlGenerateLocal($sType, $aParams);
            } elseif ($sType == 'discount') {
                $feed = new xmlGenerateDiscount($sType, $aParams);
            } elseif ($sType == 'reviews') {
                $feed = new xmlGenerateReviews($sType, $aParams);
            } else {
                return false;
            }

            return $feed->generate();
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }
    }

    /**
     * generate the XML feed by the fly output
     *
     * @param array $aPost
     *
     * @return array
     */
    private function generateFlyOutput(array $aPost = null)
    {
        $aAssign = [];

        try {
            // get the token
            $sToken = \Tools::getValue('token');

            if (
                !empty(\GMerchantCenterPro::$conf['GMCP_FEED_TOKEN']) && $sToken != \GMerchantCenterPro::$conf['GMCP_FEED_TOKEN']
            ) {
                throw new \Exception(\GMerchantCenterPro::$oModule->l('Invalid security token', 'adminGenerate') . '.', 810);
            }

            // get data feed params
            $_POST['iShopId'] = !empty(\Tools::getValue('id_shop')) ? (int) \Tools::getValue('id_shop') : \GMerchantCenterPro::$iShopId;
            $_POST['iLangId'] = !empty(\Tools::getValue('gmcp_lang_id')) ? \Tools::getValue('gmcp_lang_id') : \Tools::getValue('id_lang');
            $_POST['sLangIso'] = moduleTools::getLangIso($_POST['iLangId']);
            $_POST['sCountryIso'] = \Tools::getValue('country');
            $_POST['sCurrencyIso'] = \Tools::getValue('currency_iso');
            $_POST['iFloor'] = 0;
            $_POST['iTotal'] = 0;
            $_POST['iStep'] = 0;
            $_POST['iProcess'] = 0;
            $_POST['bOutput'] = 1;
            $_POST['sFeedType'] = \Tools::getValue('feed_type');
            $_POST['bExcludedProduct'] = exclusionProduct::isExcludedProduct();

            // set the filename
            $sFileSuffix = moduleTools::buildFileSuffix($_POST['sLangIso'], $_POST['sCountryIso'], $_POST['sCurrencyIso'], 0, $_POST['sType']);
            $_POST['sFilename'] = \GMerchantCenterPro::$sFilePrefix . '.' . $sFileSuffix . '.xml';

            // execute the generate XML function
            $this->generateXml($_POST['sType']);
        } catch (\Exception $e) {
            $aAssign['sErrorInclude'] = moduleTools::getTemplatePath('views/templates/admin/error.tpl');
            $aAssign['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }

        return [
            'tpl' => 'admin/feed-generate-output.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * generate the XML feed by the cron execution
     *
     * @param array $aPost
     *
     * @return array
     */
    private function generateCron(array $aPost = null)
    {
        $aAssign = [];
        $aLang = [];
        $aLocalisation = [];

        try {
            // get the token
            $sToken = \Tools::getValue('token');
            $sType = \Tools::getValue('feed_type') != false ? \Tools::getValue('feed_type') : \Tools::getValue('sFeedType');
            // use case - individual data feed cron
            $sCountry = \Tools::getValue('country');
            $iLang = \Tools::getValue('gmcp_lang_id');
            $sCurrency = \Tools::getValue('currency_iso');
            $sUrlSuffix = $sType;

            // get the token if necessary
            if (
                !empty(\GMerchantCenterPro::$conf['GMCP_FEED_TOKEN'])
                && $sToken != \GMerchantCenterPro::$conf['GMCP_FEED_TOKEN']
            ) {
                throw new \Exception(\GMerchantCenterPro::$oModule->l('Invalid security token', 'adminGenerate') . '.', 820);
            }

            // check if this is the first time execution of the CRON
            $_POST['aLangIds'] = \Tools::getValue('aLangIds');
            $_POST['iShopId'] = !empty(\Tools::getValue('id_shop')) ? (int) \Tools::getValue('id_shop') : \GMerchantCenterPro::$iShopId;
            $_POST['sFeedType'] = $sType;

            // first execution
            if (empty($_POST['aLangIds'])) {
                // use case - individual data feed cron
                if (
                    !empty($sCountry)
                    && !empty($iLang)
                ) {
                    $aDataFeedCron[] = moduleTools::getLangIso($iLang) . '_' . $sCountry . '_' . $sCurrency;
                } // use case - the general data feed cron URL
                else {
                    // get selected data feed
                    $aDataFeedCron = \GMerchantCenterPro::$conf['GMCP_CHECK_EXPORT'];
                }

                foreach ($aDataFeedCron as $iKey => &$sLangIso) {
                    $sLangIso = \Tools::strtolower($sLangIso);
                }

                \Context::getContext()->cookie->id_lang = $iLang;
                $aLocalisation[] = moduleTools::getLangIso($iLang) . '_' . $sCountry . '_' . $sCurrency;

                if (!empty($aLocalisation[0])) {
                    list($sLangIso, $sCountryIso, $sCurrencyIso) = explode('_', $aLocalisation[0]);
                    $_POST['iLangId'] = moduleTools::getLangId($sLangIso);
                    $_POST['iCurrentLang'] = 0;
                    $_POST['sLangIso'] = $sLangIso;
                    $_POST['sCountryIso'] = $sCountryIso;
                    $_POST['sCurrencyIso'] = $sCurrencyIso;
                    $_POST['iStep'] = \GMerchantCenterPro::$conf['GMCP_AJAX_CYCLE'];
                    $_POST['iFloor'] = 0;
                    $_POST['iProcess'] = 0;
                    $_POST['bExcludedProduct'] = exclusionProduct::isExcludedProduct();
                }

                // get the total products to export
                $_POST['iTotal'] = moduleDao::getProductIds($_POST['iShopId'], (int) \GMerchantCenterPro::$conf['GMCP_EXPORT_MODE'], true);

                // set the filename
                $sFileSuffix = moduleTools::buildFileSuffix($_POST['sLangIso'], $_POST['sCountryIso'], $_POST['sCurrencyIso'], $_POST['iShopId'], $sUrlSuffix);
                $_POST['sFilename'] = \GMerchantCenterPro::$sFilePrefix . '.' . $sFileSuffix . '.xml';

                // get lang
                $_POST['aLangIds'] = $aLocalisation;
            } else {
                $_POST['iCurrentLang'] = \Tools::getValue('iCurrentLang');
                $_POST['aLangIds'] = \Tools::getValue('aLangIds');

                list($sLangIso, $sCountryIso, $sCurrencyIso) = explode('_', $_POST['aLangIds'][$_POST['iCurrentLang']]);

                if (!empty($aDataFeedTax)) {
                    $bUseTax = in_array($sLangIso . '_' . $sCountryIso . '_' . $sCurrencyIso, $aDataFeedTax) ? 1 : 0;
                }

                // get data feed params
                $_POST['iLangId'] = moduleTools::getLangId($sLangIso);
                $_POST['sLangIso'] = $sLangIso;
                $_POST['sCountryIso'] = $sCountryIso;
                $_POST['iFloor'] = \Tools::getValue('iFloor');
                $_POST['sCurrencyIso'] = $sCurrencyIso;
                $_POST['iTotal'] = \Tools::getValue('iTotal');
                $_POST['iStep'] = \Tools::getValue('iStep');
                $_POST['iProcess'] = \Tools::getValue('iProcess');
                $_POST['bExcludedProduct'] = \Tools::getValue('bExcludedProduct');

                // set the filename
                $sFileSuffix = moduleTools::buildFileSuffix($_POST['sLangIso'], $_POST['sCountryIso'], $_POST['sCurrencyIso'], $_POST['iShopId'], $sUrlSuffix);
                $_POST['sFilename'] = \GMerchantCenterPro::$sFilePrefix . '.' . $sFileSuffix . '.xml';
            }

            // execute the generate XML function
            $aContent = $this->generateXml($sType);

            if (empty($aContent['assign']['aErrors'])) {
                // handle the cron URL
                $sCronUrl = \Context::getContext()->link->getModuleLink(moduleConfiguration::GMCP_MODULE_SET_NAME, moduleConfiguration::GMCP_CTRL_CRON, ['id_shop' => \GMerchantCenterPro::$iShopId]);

                // check if the feed protection is activated
                if (!empty($sToken)) {
                    $sCronUrl .= '&token=' . $sToken;
                }
                if (!empty($sType)) {
                    $sCronUrl .= '&sFeedType=' . $sType;
                }

                // set the base cron URL
                $sCronUrl .= '&aLangIds[]=' . implode('&aLangIds[]=', $_POST['aLangIds']) . '&iTotal=' . (int) $_POST['iTotal'] . '&iStep=' . (int) $_POST['iStep'] . '&bExcludedProduct=' . $_POST['bExcludedProduct'];

                if (
                    !empty($aContent['assign']['bContinueStatus'])
                    && empty($aContent['assign']['bFinishStatus'])
                ) {
                    $_POST['iFloor'] += $_POST['iStep'];
                    $_POST['iProcess'] = $aContent['assign']['process'];
                    // header location
                    header('Location: ' . $sCronUrl . '&iCurrentLang=' . $_POST['iCurrentLang'] . '&iFloor=' . $_POST['iFloor'] . '&iProcess=' . $_POST['iProcess']);
                    exit;
                } elseif (
                    empty($aContent['assign']['bContinueStatus'])
                    && !empty($aContent['assign']['bFinishStatus'])
                    && isset($_POST['aLangIds'][$_POST['iCurrentLang'] + 1])
                ) {
                    // header location
                    header('Location: ' . $sCronUrl . '&iCurrentLang=' . ($_POST['iCurrentLang'] + 1) . '&iFloor=0&iProcess=0');
                    exit;
                }
            }
        } catch (\Exception $e) {
            $aAssign['sErrorInclude'] = moduleTools::getTemplatePath('views/templates/admin/error.tpl');
            $aAssign['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        return [
            'tpl' => 'admin/feed-generate-output.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * set singleton
     *
     * @return obj
     */
    public static function create()
    {
        static $oGenerate;

        if (null === $oGenerate) {
            $oGenerate = new adminGenerate();
        }

        return $oGenerate;
    }
}
