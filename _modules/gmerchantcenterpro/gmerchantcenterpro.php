<?php
/**
 * Google Merchant Center Pro
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech 2024 - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 * @version 1.9.11
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once dirname(__FILE__) . '/vendor/autoload.php';
use Gmerchantcenterpro\Admin\baseController;
use Gmerchantcenterpro\Configuration\moduleConfiguration;
use Gmerchantcenterpro\Hook\hookController;
use Gmerchantcenterpro\Install\installController;
use Gmerchantcenterpro\ModuleLib\moduleTools;
use Gmerchantcenterpro\ModuleLib\moduleUpdate;
use Gmerchantcenterpro\ModuleLib\moduleWarning;
class GMerchantCenterPro extends Module
{
    public static $conf = [];
    public static $iCurrentLang;
    public static $sCurrentLang;
    public static $oCookie;
    public static $oModule;
    public static $sQueryMode;
    public static $sBASE_URI;
    public static $sHost = '';
    public static $iShopId = 1;
    public static $bCompare17 = false;
    public static $bCompare1730 = false;
    public static $bCompare1770 = false;
    public static $bCompare80 = false;
    public static $bCompare90 = false;
    public static $oContext;
    public static $aAvailableLanguages = [];
    public static $bAdvancedPack = false;
    public static $shopReviewsModule = false;
    public static $gremarketingModule = false;
    public static $aAvailableLangCurrencyCountry = [];
    public static $sFilePrefix = '';
    public $aErrors;
    public function __construct()
    {
        $this->name = 'gmerchantcenterpro';
        $this->module_key = '742dd70356f9527ea97f65dd7e3c2c41';
        $this->tab = 'seo';
        $this->version = '1.9.11';
        $this->author = 'Business Tech';
        $this->ps_versions_compliancy = ['min' => '1.7.7.0', 'max' => _PS_VERSION_];
        parent::__construct();
        $this->displayName = $this->l('Google Merchant Center PRO (Google Shopping)');
        $this->description = $this->l('The PRO version of Google Merchant Center: even more control on product data, product reviews and promotions feeds, Google Customer Reviews program and more!');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall the module Google Merchant Center PRO (Google Shopping)?');
        self::$bCompare17 = version_compare(_PS_VERSION_, '1.7.0.0', '>=');
        self::$bCompare1730 = version_compare(_PS_VERSION_, '1.7.3.0', '>=');
        self::$bCompare1770 = version_compare(_PS_VERSION_, '1.7.7.0', '>=');
        self::$bCompare80 = version_compare(_PS_VERSION_, '8.0.0', '>=');
        self::$bCompare90 = version_compare(_PS_VERSION_, '9.0.0', '>=');
        self::$oContext = $this->context;
        self::$iShopId = self::$oContext->shop->id;
        self::$oCookie = $this->context->cookie;
        self::$iCurrentLang = self::$oContext->cookie->id_lang;
        self::$sCurrentLang = (string)moduleTools::getLangIso();
        self::$oModule = $this;
        if (!empty(self::$bCompare17)) {
            $this->bootstrap = true;
        }
        self::$sBASE_URI = $this->_path;
        self::$sHost = moduleTools::setHost();
        self::$bAdvancedPack = moduleTools::isInstalled('pm_advancedpack');
        self::$shopReviewsModule = moduleTools::isInstalled('gsnippetsreviews', [], true, false);
        self::$gremarketingModule = moduleTools::isInstalled('gremarketing');
        moduleTools::getConfiguration(['GMCP_COLOR_OPT', 'GMCP_SIZE_OPT', 'GMCP_SHIP_CARRIERS', 'GMCP_CHECK_EXPORT', 'GMCP_CHECK_EXPORT_STOCK', 'GMCP_FEED_TAX', 'GMCP_FREE_PROD_PRICE_SHIP_CARRIERS', 'GMCP_NO_TAX_SHIP_CARRIERS', 'GMCP_FREE_SHIP_CARRIERS']);
        self::$aAvailableLanguages = moduleTools::getAvailableLanguages(self::$iShopId);
        self::$aAvailableLangCurrencyCountry = moduleTools::getLangCurrencyCountry(self::$aAvailableLanguages);
        self::$sQueryMode = \Tools::getValue('sMode');
    }
    public function install()
    {
        $bReturn = true;
        if (
            !parent::install()
            || !installController::run('install', 'sql', moduleConfiguration::GMCP_PATH_SQL . moduleConfiguration::GMCP_INSTALL_SQL_FILE)
            || !installController::run('install', 'config', ['bConfigOnly' => true])
        ) {
            $bReturn = false;
        }
        return $bReturn;
    }
    public function uninstall()
    {
        $bReturn = true;
        if (
            !parent::uninstall()
            || !installController::run('uninstall', 'config')
        ) {
            $bReturn = false;
        }
        return $bReturn;
    }
    public function getContent()
    {
        try {
            self::$sFilePrefix = moduleTools::setXmlFilePrefix();
            $aDisplay = [];
            $sContent = '';
            $sControllerType = (!\Tools::getIsset(moduleConfiguration::GMCP_PARAM_CTRL_NAME) || (\Tools::getIsset(moduleConfiguration::GMCP_PARAM_CTRL_NAME) && 'admin' == \Tools::getValue(moduleConfiguration::GMCP_PARAM_CTRL_NAME))) ? (\Tools::getIsset(moduleConfiguration::GMCP_PARAM_CTRL_NAME) ? \Tools::getValue(moduleConfiguration::GMCP_PARAM_CTRL_NAME) : 'admin') : \Tools::getValue(moduleConfiguration::GMCP_PARAM_CTRL_NAME);
            $oCtrl = baseController::get($sControllerType);
            $aDisplay = $oCtrl->run(array_merge($_GET, $_POST));
            if (!empty($aDisplay)) {
                $aDisplay['assign'] = array_merge($aDisplay['assign'], [
                    'oJsTranslatedMsg' => moduleTools::jsonEncode(moduleConfiguration::getJsMessage()),
                    'bAddJsCss' => true,
                ]);
                $sContent = $this->displayModule((string)$aDisplay['tpl'], (array)$aDisplay['assign']);
                if (!empty(self::$sQueryMode)) {
                    echo $sContent;
                } else {
                    return $sContent;
                }
            } else {
                throw new \Exception('action returns empty content', 110);
            }
        } catch (\Exception $e) {
            $this->aErrors[] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            $sContent = $this->displayErrorModule();
            if (!empty(self::$sQueryMode)) {
                echo $sContent;
            } else {
                return $sContent;
            }
        }
        if (!empty(self::$sQueryMode)) {
            exit;
        }
    }
    public function hookDisplayHeader()
    {
        return $this->execHook('display', 'header');
    }
    private function execHook($sHookType, $sAction, array $aParams = null)
    {
        $aDisplay = [];
        try {
            if (
                !empty($aParams['cache'])
                && !empty($aParams['template'])
                && !empty($aParams['cacheId'])
            ) {
                $bUseCache = !$this->isCached($aParams['template'], $this->getCacheId($aParams['cacheId'])) ? false : true;
                if ($bUseCache) {
                    $aDisplay['tpl'] = $aParams['template'];
                    $aDisplay['assign'] = [];
                }
            } else {
                $bUseCache = false;
            }
            if (!$bUseCache) {
                $oHook = new hookController($sHookType, $sAction);
                $aDisplay = $oHook->run($aParams);
            }
            if (!empty($aDisplay)) {
                return $this->displayModule($aDisplay['tpl'], $aDisplay['assign'], $bUseCache, !empty($aParams['cacheId']) ? $aParams['cacheId'] : null);
            } else {
                throw new \Exception('Chosen hook returned empty content', 110);
            }
        } catch (\Exception $e) {
            $this->aErrors[] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            return $this->displayErrorModule();
        }
    }
    public function setErrorHandler($iErrno, $sErrstr, $sErrFile, $iErrLine, $aErrContext)
    {
        switch ($iErrno) {
            case E_USER_ERROR:
                $this->aErrors[] = [
                    'msg' => 'Fatal error <b>' . $sErrstr . '</b>',
                    'code' => $iErrno,
                    'file' => $sErrFile,
                    'line' => $iErrLine,
                    'context' => $aErrContext,
                ];
                break;
            case E_USER_WARNING:
                $this->aErrors[] = [
                    'msg' => 'Warning <b>' . $sErrstr . '</b>',
                    'code' => $iErrno,
                    'file' => $sErrFile,
                    'line' => $iErrLine,
                    'context' => $aErrContext,
                ];
                break;
            case E_USER_NOTICE:
                $this->aErrors[] = [
                    'msg' => 'Notice <b>' . $sErrstr . '</b>',
                    'code' => $iErrno,
                    'file' => $sErrFile,
                    'line' => $iErrLine,
                    'context' => $aErrContext,
                ];
                break;
            default:
                $this->aErrors[] = [
                    'msg' => 'Unknow error <b>' . $sErrstr . '</b>',
                    'code' => $iErrno,
                    'file' => $sErrFile,
                    'line' => $iErrLine,
                    'context' => $aErrContext,
                ];
                break;
        }
        return $this->displayErrorModule();
    }
    public function displayModule($sTplName, $aAssign, $bUseCache = false, $iICacheId = null)
    {
        if (file_exists(_PS_MODULE_DIR_ . 'gmerchantcenterpro/views/templates/' . $sTplName) && is_file(_PS_MODULE_DIR_ . 'gmerchantcenterpro/views/templates/' . $sTplName)) {
            $aAssign = array_merge(
                $aAssign,
                ['sModuleName' => \Tools::strtolower(moduleConfiguration::GMCP_MODULE_NAME), 'bDebug' => moduleConfiguration::GMCP_DEBUG]
            );
            if (!empty($bUseCache) && !empty($iICacheId)) {
                return $this->display(__FILE__, $sTplName, $this->getCacheId($iICacheId));
            } 
            else {
                self::$oContext->smarty->assign($aAssign);
                return $this->display(__FILE__, 'views/templates/' . $sTplName);
            }
        } else {
            throw new \Exception('Template "' . $sTplName . '" doesn\'t exists', 120);
        }
    }
    public function displayErrorModule()
    {
        self::$oContext->smarty->assign(
            [
                'sHomeURI' => moduleTools::truncateUri(),
                'aErrors' => $this->aErrors,
                'sModuleName' => \Tools::strtolower(moduleConfiguration::GMCP_MODULE_NAME),
                'bDebug' => moduleConfiguration::GMCP_DEBUG,
            ]
        );
        return $this->display(__FILE__, 'views/templates/' . moduleConfiguration::GMCP_TPL_HOOK_PATH . 'error.tpl');
    }
    public function updateModule()
    {
        moduleWarning::create()->run('module', 'gmerchantcenter', [], true);
        moduleUpdate::create()->run('tables');
        moduleUpdate::create()->run('fields');
        moduleUpdate::create()->run('templates');
        moduleUpdate::create()->run('hooks');
        moduleUpdate::create()->run('module_update');
        moduleUpdate::create()->run('configuration', ['languages']);
        moduleUpdate::create()->run('configuration', ['color']);
        moduleUpdate::create()->run('configuration', ['size']);
        moduleUpdate::create()->run('feedsDatabaseMigration');
        moduleUpdate::create()->run('moduleAdminTab');
        moduleUpdate::create()->run('secureTaxonomies');
        $aErrors = moduleUpdate::create()->getErrors();
        moduleUpdate::create()->run(
            'xmlFiles',
            ['aAvailableData' => \GMerchantCenterPro::$aAvailableLangCurrencyCountry]
        );
        $aErrors = moduleUpdate::create()->getErrors();
        moduleUpdate::create()->run('xmlFiles', ['aAvailableData' => \GMerchantCenterPro::$aAvailableLangCurrencyCountry]);
        if (
            empty($aErrors)
            && moduleUpdate::create()->getErrors()
        ) {
            moduleWarning::create()->bStopExecution = true;
        }
        return moduleUpdate::create()->getErrors();
    }
}
