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
use Gmerchantcenterpro\ModuleLib\moduleTools;

class adminController extends baseController
{
    /**
     * @param array $aParams
     */
    public function __construct(array $aParams = null)
    {
        // defines type to execute
        // use case : no key sAction sent in POST mode (no form has been posted => first page is displayed with admin-display.class.php)
        // use case : key sAction sent in POST mode (form or ajax query posted ).
        $sAction = (!\Tools::getIsset('sAction') || (\Tools::getIsset('sAction') && 'display' == \Tools::getValue('sAction'))) ? (\Tools::getIsset('sAction') ? \Tools::getValue('sAction') : 'display') : \Tools::getValue('sAction');

        // set action
        $this->setAction($sAction);

        // set type
        $this->setType();
    }

    /**
     * execute abstract derived admin object
     *
     * @param array $aRequest : request
     *
     * @return array $aDisplay : empty => false / not empty => true
     */
    public function run($aRequest)
    {
        // set
        $aDisplay = [];
        $aParams = [];

        switch (self::$sAction) {
            case 'display':
                $oAdminType = adminDisplay::create();

                // update new module keys
                moduleTools::updateConfiguration();

                // get configuration options
                moduleTools::getConfiguration([
                    'GMCP_COLOR_OPT',
                    'GMCP_SIZE_OPT',
                    'GMCP_SHIP_CARRIERS',
                    'GMCP_CHECK_EXPORT',
                    'GMCP_CHECK_EXPORT_STOCK',
                    'GMCP_PROD_EXCL',
                    'GMCP_FEED_TAX',
                    'GMCP_FREE_SHIP_PROD',
                    'GMCP_PAUSED_PROD',
                ]);

                // set js msg translation
                moduleTools::translateJsMsg();

                // set params
                $aParams['oJsTranslatedMsg'] = moduleTools::jsonEncode(moduleConfiguration::getJsMessage());

                // use case - type not define => first page requested
                if (empty(self::$sType)) {
                    // update module version
                    \Configuration::updateValue('GMCP_VERSION', \GMerchantCenterPro::$oModule->version);

                    // update module if necessary
                    $aParams['aUpdateErrors'] = \GMerchantCenterPro::$oModule->updateModule();
                }

                break;
            case 'update':
                $oAdminType = adminUpdate::create();

                break;
            case 'delete':
                $oAdminType = adminDelete::create();

                break;
            case 'generate':
                $oAdminType = adminGenerate::create();

                break;
            case 'send':
                $oAdminType = BT_AdminSend::create();

                break;
            default:
                $oAdminType = false;

                break;
        }

        // process data to use in view (tpl)
        if (!empty($oAdminType)) {
            // execute good action in admin
            // only displayed with key : tpl and assign in order to display good smarty template
            $aDisplay = $oAdminType->run(parent::$sType, $aRequest);

            if (!empty($aDisplay)) {
                $aDisplay['assign'] = array_merge($aDisplay['assign'], $aParams, ['bAddJsCss' => true]);
            }
        }

        return $aDisplay;
    }
}
