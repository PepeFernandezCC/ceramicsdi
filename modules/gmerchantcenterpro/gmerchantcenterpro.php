<?php
/**
 * Google Merchant Center Pro
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech 2024 - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 * @version 1.9.4
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
    /**
     * @var array : array of set configuration
     */
    public static $conf = [];

    /**
     * @var int : store id of default lang
     */
    public static $iCurrentLang;

    /**
     * @var int : store iso of default lang
     */
    public static $sCurrentLang;

    /**
     * @var obj : store cookie obj
     */
    public static $oCookie;

    /**
     * @var obj : obj module itself
     */
    public static $oModule = [];

    /**
     * @var string : query mode - detect XHR
     */
    public static $sQueryMode;

    /**
     * @var string : base of URI in prestashop
     */
    public static $sBASE_URI;

    /**
     * @var string : store the current domain
     */
    public static $sHost = '';

    /**
     * @var int : shop id used for 1.5 and for multi shop
     */
    public static $iShopId = 1;

    /**
     * @var bool : get compare version for PS 1.7
     */
    public static $bCompare17 = false;

    /**
     * @var bool : get compare version for PS 1.7.3.0
     */
    public static $bCompare1730 = false;

    /**
     * @var bool : get compare version for PS 1.7.7.0
     */
    public static $bCompare1770 = false;

    /**
     * @var bool : get compare version for PS 8
     */
    public static $bCompare80 = false;

    /**
     * @var obj : get context object
     */
    public static $oContext;

    /**
     * @var array : store the available languages
     */
    public static $aAvailableLanguages = [];

    /**
     * @var bool : check advanced pack module installation
     */
    public static $bAdvancedPack = false;

    /**
     * @var bool : check if BusinessTech Shop product reviews is installed
     */
    public static $shopReviewsModule = false;

    /**
     * @var bool : check if BusinessTech Google dynamic remarketing is installed
     */
    public static $gremarketingModule = false;

    /**
     * @var array : store the available related languages / countries / currencies
     */
    public static $aAvailableLangCurrencyCountry = [];

    /**
     * @var string : store the XML file's prefix
     */
    public static $sFilePrefix = '';

    /**
     * @var array : array get error
     */
    public $aErrors;

    /**
     * assigns few information about module and instantiate parent class
     */
    public function __construct()
    {
        $this->name = 'gmerchantcenterpro';
        $this->module_key = '742dd70356f9527ea97f65dd7e3c2c41';
        $this->tab = 'seo';
        $this->version = '1.9.4';
        $this->author = 'Business Tech';
        $this->ps_versions_compliancy = ['min' => '1.7.4.0', 'max' => _PS_VERSION_];

        parent::__construct();

        $this->displayName = $this->l('Google Merchant Center PRO (Google Shopping)');
        $this->description = $this->l('The PRO version of Google Merchant Center: even more control on product data, product reviews and promotions feeds, Google Customer Reviews program and more!');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall the module Google Merchant Center PRO (Google Shopping)?');

        // compare PS version
        self::$bCompare17 = version_compare(_PS_VERSION_, '1.7.0.0', '>=');
        self::$bCompare1730 = version_compare(_PS_VERSION_, '1.7.3.0', '>=');
        self::$bCompare1770 = version_compare(_PS_VERSION_, '1.7.7.0', '>=');
        self::$bCompare80 = version_compare(_PS_VERSION_, '8.0.0', '>=');

        self::$oContext = $this->context;
        // get shop id
        self::$iShopId = self::$oContext->shop->id;

        // get cookie obj
        self::$oCookie = $this->context->cookie;

        // get current  lang id
        self::$iCurrentLang = self::$oContext->cookie->id_lang;

        // get current lang iso
        self::$sCurrentLang = moduleTools::getLangIso();

        // stock itself obj
        self::$oModule = $this;

        // set bootstrap
        if (!empty(self::$bCompare17)) {
            $this->bootstrap = true;
        }

        // set base of URI
        self::$sBASE_URI = $this->_path;
        self::$sHost = moduleTools::setHost();
        self::$bAdvancedPack = moduleTools::isInstalled('pm_advancedpack');
        self::$shopReviewsModule = moduleTools::isInstalled('gsnippetsreviews', [], true, false);
        self::$gremarketingModule = moduleTools::isInstalled('gremarketing');

        // get configuration options
        moduleTools::getConfiguration(['GMCP_COLOR_OPT', 'GMCP_SIZE_OPT', 'GMCP_SHIP_CARRIERS', 'GMCP_CHECK_EXPORT', 'GMCP_CHECK_EXPORT_STOCK', 'GMCP_FEED_TAX', 'GMCP_FREE_PROD_PRICE_SHIP_CARRIERS', 'GMCP_NO_TAX_SHIP_CARRIERS', 'GMCP_FREE_SHIP_CARRIERS']);

        // get available languages
        self::$aAvailableLanguages = moduleTools::getAvailableLanguages(self::$iShopId);

        // get available languages / currencies / countries
        self::$aAvailableLangCurrencyCountry = moduleTools::getLangCurrencyCountry(self::$aAvailableLanguages, moduleConfiguration::GMCP_AVAILABLE_COUNTRIES);

        // get call mode - Ajax or dynamic - used for clean headers and footer in ajax request
        self::$sQueryMode = \Tools::getValue('sMode');
    }

    /**
     * installs all mandatory structure (DB or Files) => sql queries and update values and hooks registered
     *
     * @return bool
     */
    public function install()
    {
        // set return
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

    /**
     * uninstalls all mandatory structure (DB or Files)
     *
     * @return bool
     */
    public function uninstall()
    {
        // set return
        $bReturn = true;

        if (
            !parent::uninstall()
            || !installController::run('uninstall', 'config')
        ) {
            $bReturn = false;
        }

        return $bReturn;
    }

    /**
     * manages all data in Back Office
     *
     * @return string
     */
    public function getContent()
    {
        try {
            // transverse execution
            self::$sFilePrefix = moduleTools::setXmlFilePrefix();

            // get controller type
            $sControllerType = (!\Tools::getIsset(moduleConfiguration::GMCP_PARAM_CTRL_NAME) || (\Tools::getIsset(moduleConfiguration::GMCP_PARAM_CTRL_NAME) && 'admin' == \Tools::getValue(moduleConfiguration::GMCP_PARAM_CTRL_NAME))) ? (\Tools::getIsset(moduleConfiguration::GMCP_PARAM_CTRL_NAME) ? \Tools::getValue(moduleConfiguration::GMCP_PARAM_CTRL_NAME) : 'admin') : \Tools::getValue(moduleConfiguration::GMCP_PARAM_CTRL_NAME);

            // instantiate matched controller object
            $oCtrl = baseController::get($sControllerType);

            // execute good action in admin
            // only displayed with key : tpl and assign in order to display good smarty template
            $aDisplay = $oCtrl->run(array_merge($_GET, $_POST));

            if (!empty($aDisplay)) {
                $aDisplay['assign'] = array_merge($aDisplay['assign'], [
                    'oJsTranslatedMsg' => moduleTools::jsonEncode(moduleConfiguration::getJsMessage()),
                    'bAddJsCss' => true,
                ]);

                // get content
                $sContent = $this->displayModule($aDisplay['tpl'], $aDisplay['assign']);

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

            // get content
            $sContent = $this->displayErrorModule();

            if (!empty(self::$sQueryMode)) {
                echo $sContent;
            } else {
                return $sContent;
            }
        }
        // exit clean with XHR mode
        if (!empty(self::$sQueryMode)) {
            exit;
        }
    }

    /**
     * hookDisplayHeader() method displays customized module content on header
     *
     * @return string
     */
    public function hookDisplayHeader()
    {
        return $this->execHook('display', 'header');
    }

    /**
     * displays selected hook content
     *
     * @param string $sHookType
     * @param string $sAction
     * @param array $aParams
     *
     * @return string
     */
    private function execHook($sHookType, $sAction, array $aParams = null)
    {
        // set
        $aDisplay = [];

        try {
            // use cache or not
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

            // detect cache or not
            if (!$bUseCache) {
                // define which hook class is executed in order to display good content in good zone in shop
                $oHook = new hookController($sHookType, $sAction);

                // displays good block content
                $aDisplay = $oHook->run($aParams);
            }

            // execute good action in admin
            // only displayed with key : tpl and assign in order to display good smarty template
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

    /**
     * manages module error
     *
     * @param string $sTplName
     * @param array $aAssign
     */
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

    /**
     * displays views
     *
     * @param string $sTplName
     * @param array $aAssign
     * @param bool $bUseCache
     * @param int $iICacheId
     *
     * @return string html
     *
     * @throws Exception
     */
    public function displayModule($sTplName, $aAssign, $bUseCache = false, $iICacheId = null)
    {
        if (file_exists(_PS_MODULE_DIR_ . 'gmerchantcenterpro/views/templates/' . $sTplName) && is_file(_PS_MODULE_DIR_ . 'gmerchantcenterpro/views/templates/' . $sTplName)) {
            $aAssign = array_merge(
                $aAssign,
                ['sModuleName' => \Tools::strtolower(moduleConfiguration::GMCP_MODULE_NAME), 'bDebug' => moduleConfiguration::GMCP_DEBUG]
            );

            // use cache
            if (!empty($bUseCache) && !empty($iICacheId)) {
                return $this->display(__FILE__, $sTplName, $this->getCacheId($iICacheId));
            } // not use cache
            else {
                self::$oContext->smarty->assign($aAssign);

                return $this->display(__FILE__, 'views/templates/' . $sTplName);
            }
        } else {
            throw new \Exception('Template "' . $sTplName . '" doesn\'t exists', 120);
        }
    }

    /**
     * displays view with error
     *
     * @param string $sTplName
     * @param array $aAssign
     *
     * @return string html
     */
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

    /**
     * updates module as necessary
     *
     * @return array
     */
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

        $aErrors = moduleUpdate::create()->getErrors();

        // initialize XML files
        moduleUpdate::create()->run(
            'xmlFiles',
            ['aAvailableData' => \GMerchantCenterPro::$aAvailableLangCurrencyCountry]
        );
        $aErrors = moduleUpdate::create()->getErrors();

        // initialize XML files
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
