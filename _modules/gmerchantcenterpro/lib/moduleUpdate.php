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

use Gmerchantcenterpro\Admin\adminUpdate;
use Gmerchantcenterpro\Common\dirReader;
use Gmerchantcenterpro\Common\fileClass;
use Gmerchantcenterpro\Configuration\moduleConfiguration;
use Gmerchantcenterpro\Install\installController;
use Gmerchantcenterpro\Models\categoryTaxonomy;
use Gmerchantcenterpro\Models\Feeds;

/**
 * Class that manage all action update to do, has been executed on each module get content
 */
class moduleUpdate
{
    /**
     * @var array
     */
    protected $aErrors = [];

    /**
     * @param mixed $sType
     * @param array|null $aParam
     *
     * @return mixed
     */
    public function run($sType, array $aParam = null)
    {
        // get type
        $sType = empty($sType) ? 'tables' : $sType;

        switch ($sType) {
            case 'tables':
            case 'fields':
            case 'hooks':
            case 'templates':
            case 'moduleAdminTab':
            case 'xmlFiles':
            case 'feedsDatabaseMigration':
            case 'secureTaxonomies':
                call_user_func_array([$this, 'update' . ucfirst($sType)], [$aParam]);

                break;
            case 'configuration': // use case - update configuration
                call_user_func([$this, 'update' . ucfirst($sType)], $aParam);

                break;
            default:
                break;
        }
    }

    /**
     * @param array|null $aParam
     *
     * @return mixed
     */
    private function updateTables(array $aParam = null)
    {
        // set transaction
        \Db::getInstance()->Execute('BEGIN');

        if (!empty(moduleConfiguration::getSqlUpdateData()['table'])) {
            $iCount = 1;
            // loop on each elt to update SQL
            foreach (moduleConfiguration::getSqlUpdateData()['table'] as $sTable => $sSqlFile) {
                // execute query
                $bResult = \Db::getInstance()->ExecuteS('SHOW TABLES LIKE "' . _DB_PREFIX_ . strtolower(moduleConfiguration::GMCP_MODULE_NAME) . '_' . \bqSQL($sTable) . '"');

                // if empty - update
                if (empty($bResult)) {
                    // use case - KO update
                    if (!installController::run('install', 'sql', moduleConfiguration::GMCP_PATH_SQL . $sSqlFile)) {
                        $this->aErrors[] = [
                            'msg' => \GMerchantCenterPro::$oModule->l('There is an error around the SQL table update', 'moduleupdate'),
                            'code' => intval(190 + $iCount),
                            'file' => $sSqlFile,
                            'context' => \GMerchantCenterPro::$oModule->l('Issue around table update for: ', 'moduleupdate') . $sTable,
                        ];
                        ++$iCount;
                    }
                }
            }
        }

        if (empty($this->aErrors)) {
            \Db::getInstance()->Execute('COMMIT');
        } else {
            \Db::getInstance()->Execute('ROLLBACK');
        }
    }

    /**
     * @param array|null $aParam
     *
     * @return mixed
     */
    private function updateFields(array $aParam = null)
    {
        // set transaction
        \Db::getInstance()->Execute('BEGIN');

        if (!empty(moduleConfiguration::getSqlUpdateData()['field'])) {
            $iCount = 1;
            // loop on each elt to update SQL
            foreach (moduleConfiguration::getSqlUpdateData()['field'] as $sFieldName => $aOption) {
                // execute query
                $bResult = \Db::getInstance()->ExecuteS('SHOW COLUMNS FROM ' . _DB_PREFIX_ . strtolower(moduleConfiguration::GMCP_MODULE_NAME) . '_' . \bqSQL($aOption['table']) . ' LIKE "' . \pSQL($sFieldName) . '"');

                // if empty - update
                if (empty($bResult)) {
                    // use case - KO update
                    if (!installController::run('install', 'sql', moduleConfiguration::GMCP_PATH_SQL . $aOption['file'])) {
                        $aErrors[] = [
                            'field' => $sFieldName,
                            'linked' => $aOption['table'],
                            'file' => $aOption['file'],
                        ];
                        $this->aErrors[] = [
                            'msg' => \GMerchantCenterPro::$oModule->l(
                                'There is an error around the SQL field update!',
                                'moduleupdate'
                            ),
                            'code' => intval(180 + $iCount),
                            'file' => $aOption['file'],
                            'context' => \GMerchantCenterPro::$oModule->l('Issue around field update for: ', 'moduleupdate') . $sFieldName,
                        ];
                        ++$iCount;
                    }
                }
            }
        }

        if (empty($this->aErrors)) {
            \Db::getInstance()->Execute('COMMIT');
        } else {
            \Db::getInstance()->Execute('ROLLBACK');
        }
    }

    /**
     * @param array|null $aParam
     *
     * @return mixed
     */
    private function updateHooks(array $aParam = null)
    {
        // use case - hook register ko
        if (!installController::run('install', 'config', ['bHookOnly' => true])) {
            $this->aErrors[] = [
                'msg' => \GMerchantCenterPro::$oModule->l(
                    'There is an error around the HOOKS update!',
                    'moduleupdate'
                ),
                'code' => 170,
                'file' => \GMerchantCenterPro::$oModule->l(
                    'see the variable moduleConfiguration::GMCP_HOOKS in the conf/common.conf.php file',
                    'moduleupdate'
                ),
                'context' => \GMerchantCenterPro::$oModule->l('Issue around hook update', 'moduleupdate'),
            ];
        }
    }

    /**
     * @param array|null $aParam
     *
     * @return mixed
     */
    private function updateTemplates(array $aParam = null)
    {
        // get templates files
        $aTplFiles = dirReader::create()->run([
            'path' => moduleConfiguration::GMCP_PATH_LIB_INSTALL,
            'recursive' => true,
            'extension' => 'tpl',
            'subpath' => true,
        ]);

        if (!empty($aTplFiles)) {
            $smarty = \Context::getContext()->smarty;

            if (method_exists($smarty, 'clearCompiledTemplate')) {
                $smarty->clearCompiledTemplate();
            } elseif (method_exists($smarty, 'clear_compiled_tpl')) {
                foreach ($aTplFiles as $aFile) {
                    $smarty->clear_compiled_tpl($aFile['filename']);
                }
            }
        }
    }

    /**
     * @param array|null $aParam
     *
     * @return mixed
     */
    private function updateModuleAdminTab(array $aParam = null)
    {
        foreach (moduleConfiguration::GMCP_TABS as $sModuleTabName => $aTab) {
            if (isset($aTab['oldName'])) {
                if (\Tab::getIdFromClassName($aTab['oldName']) != false) {
                    // use case - if uninstall succeeded
                    if (installController::run('uninstall', 'tab', ['name' => $aTab['oldName']])) {
                        // install new admin tab
                        installController::run('install', 'tab', ['name' => $sModuleTabName]);
                    }
                }
            } else {
                installController::run('install', 'tab', ['name' => $sModuleTabName]);
            }
        }
    }

    /**
     * @param array|null $aParam
     *
     * @return mixed
     */
    private function updateXmlFiles(array $aParam = null)
    {
        $oUpdate = adminUpdate::create();
        $oUpdate->run('customLabelDate');

        if (!empty($aParam['aAvailableData']) && is_array($aParam['aAvailableData'])) {
            $iCount = 1;

            foreach ($aParam['aAvailableData'] as $aData) {
                // check if file exist
                $sFileSuffix = moduleTools::buildFileSuffix($aData['langIso'], $aData['countryIso'], $aData['currencyIso'], (int)\Context::getContext()->shop->id, 'product');
                $sFilePath = \GMerchantCenterPro::$sFilePrefix . '.' . $sFileSuffix . '.xml';
                $sFileSuffixReviews = moduleTools::buildFileSuffix($aData['langIso'], $aData['countryIso'], $aData['currencyIso'], (int)\Context::getContext()->shop->id, 'reviews');
                $sFilePathReviews = \GMerchantCenterPro::$sFilePrefix . '.' . $sFileSuffixReviews . '.xml';
                $sFileSuffixLocal = moduleTools::buildFileSuffix($aData['langIso'], $aData['countryIso'], $aData['currencyIso'], (int)\Context::getContext()->shop->id, 'local');
                $sFilePathLocal = \GMerchantCenterPro::$sFilePrefix . '.' . $sFileSuffixLocal . '.xml';
                $bLocalFileExists = true;

                if (
                    !is_file(moduleConfiguration::GMCP_SHOP_PATH_ROOT . $sFilePath)
                    && !is_file(moduleConfiguration::GMCP_SHOP_PATH_ROOT . $sFilePathReviews)
                    && !is_file(moduleConfiguration::GMCP_SHOP_PATH_ROOT . $sFilePathLocal)
                ) {
                    try {
                        fileClass::create()->write(moduleConfiguration::GMCP_SHOP_PATH_ROOT . $sFilePath, '');
                        fileClass::create()->write(moduleConfiguration::GMCP_SHOP_PATH_ROOT . $sFilePathReviews, '');
                        fileClass::create()->write(moduleConfiguration::GMCP_SHOP_PATH_ROOT . $sFilePathLocal, '');

                        // test if file exists
                        $bProductFileExists = is_file(moduleConfiguration::GMCP_SHOP_PATH_ROOT . $sFilePath);
                        $bReviewsFileExists = is_file(moduleConfiguration::GMCP_SHOP_PATH_ROOT . $sFilePathReviews);
                        $bReviewsFileExists = is_file(moduleConfiguration::GMCP_SHOP_PATH_ROOT . $sFilePathLocal);
                    } catch (\Exception $e) {
                        \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
                        $bProductFileExists = false;
                        $bReviewsFileExists = false;
                        $bLocalFileExists = false;
                    }

                    if (
                        !$bProductFileExists
                        || !$bReviewsFileExists
                        || !$bLocalFileExists
                    ) {
                        $aError = [
                            'msg' => \GMerchantCenterPro::$oModule->l('There is an error around the creation of the data feed XML file in the shop\'s root directory', 'moduleupdate'),
                            'code' => intval(160 + $iCount),
                            'file' => moduleConfiguration::GMCP_SHOP_PATH_ROOT . $sFilePath,
                            'context' => \GMerchantCenterPro::$oModule->l(
                                'Issue around the xml files which have to be generated in the shop\'s root directory',
                                'moduleupdate'
                            ),
                            'howTo' => \GMerchantCenterPro::$oModule->l('Please follow our FAQ about problems when creating XML files at the root of your shop', 'moduleupdate') . '&nbsp;=>&nbsp;<i class="icon-question-sign"></i>&nbsp;<a href="' . moduleConfiguration::GMCP_BT_FAQ_MAIN_URL . 'faq.php?id=21" target="_blank">FAQ</a>',
                        ];
                        $this->aErrors[] = $aError;
                        ++$iCount;
                    }
                }
            }
        }
    }

    /**
     * @param mixed $sType
     *
     * @return void
     */
    private function updateConfiguration($sType)
    {
        switch ($sType) {
            case 'languages':
                $aHomeCat = \Configuration::get('GMCP_HOME_CAT');
                if (empty($aHomeCat)) {
                    $aHomeCat = [];
                    foreach (\GMerchantCenterPro::$aAvailableLanguages as $aLanguage) {
                        $aHomeCat[$aLanguage['id_lang']] = !empty(moduleConfiguration::GMCP_HOME_CAT_NAME[$aLanguage['iso_code']]) ? moduleConfiguration::GMCP_HOME_CAT_NAME[$aLanguage['iso_code']] : '';
                    }
                    \Configuration::updateValue('GMCP_HOME_CAT', moduleTools::handleSetConfigurationData($aHomeCat));
                } elseif (is_array(\GMerchantCenterPro::$conf['GMCP_HOME_CAT'])) {
                    \Configuration::updateValue('GMCP_HOME_CAT', moduleTools::handleSetConfigurationData(\GMerchantCenterPro::$conf['GMCP_HOME_CAT']));
                }

                break;
            case 'color':
                if (!empty(\GMerchantCenterPro::$conf['GMCP_COLOR_OPT'])) {
                    if (is_numeric(\GMerchantCenterPro::$conf['GMCP_COLOR_OPT'])) {
                        \GMerchantCenterPro::$conf['GMCP_COLOR_OPT'] = [\GMerchantCenterPro::$conf['GMCP_COLOR_OPT']];

                        $aAttributeIds = [];
                        foreach (\GMerchantCenterPro::$conf['GMCP_COLOR_OPT'] as $iAttributeId) {
                            $aAttributeIds['attribute'][] = $iAttributeId;
                        }
                        \Configuration::updateValue('GMCP_COLOR_OPT', moduleTools::handleSetConfigurationData($aAttributeIds));
                    }
                }

                break;
            case 'size':
                if (!empty(\GMerchantCenterPro::$conf['GMCP_SIZE_OPT'])) {
                    if (is_numeric(\GMerchantCenterPro::$conf['GMCP_SIZE_OPT'])) {
                        \GMerchantCenterPro::$conf['GMCP_SIZE_OPT'] = [\GMerchantCenterPro::$conf['GMCP_SIZE_OPT']];

                        $aAttributeIds = [];
                        foreach (\GMerchantCenterPro::$conf['GMCP_SIZE_OPT'] as $iAttributeId) {
                            $aAttributeIds['attribute'][] = $iAttributeId;
                        }
                        \Configuration::updateValue('GMCP_SIZE_OPT', moduleTools::handleSetConfigurationData($aAttributeIds));
                    }
                }

                break;
            default:
                break;
        }
    }

    /**
     * @return mixed
     */
    private function updateFeedsDatabaseMigration()
    {
        $hasData = Feeds::hasSavedData((int)\Context::getContext()->shop->id);

        if (!empty(moduleConfiguration::GMCP_AVAILABLE_COUNTRIES) && empty($hasData)) {
            foreach (moduleConfiguration::GMCP_AVAILABLE_COUNTRIES as $lang_code => $data) {
                if (is_array($data)) {
                    foreach ($data as $country_code => $data_entry) {
                        if (is_array($data_entry) && isset($data_entry['currency'])) {
                            foreach ($data_entry['currency'] as $currency) {
                                $feed = new Feeds();
                                $feed->iso_lang = $lang_code;
                                $feed->iso_country = $country_code;
                                $feed->iso_currency = $currency;
                                $feed->taxonomy = $data_entry['taxonomy'];
                                $feed->id_shop = (int)\Context::getContext()->shop->id;
                                $feed->feed_is_default = 1;
                                $feed->add();
                            }
                        }
                    }
                }
            }

            return \Tools::redirectAdmin(\Context::getContext()->link->getAdminLink('AdminModules') . '&configure=gmerchantcenterpro');
        }
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return empty($this->aErrors) ? false : $this->aErrors;
    }

    /**
     * @return moduleUpdate
     */
    public static function create()
    {
        static $oModuleUpdate;

        if (null === $oModuleUpdate) {
            $oModuleUpdate = new moduleUpdate();
        }

        return $oModuleUpdate;
    }

    /**
     * @return mixed
     */
    private function updateSecureTaxonomies()
    {
        try {
            if (empty(\GMerchantCenterPro::$conf['GMCP_HANDLE_TAXO_JSON'])) {
                if (!empty(categoryTaxonomy::hasTaxonomies((int)\Context::getContext()->shop->id))) {
                    $taxonomies = categoryTaxonomy::getAllTaxonomies((int)\Context::getContext()->shop->id);
                    foreach ($taxonomies as $taxonomy) {
                        if (!empty($taxonomy['txt_taxonomy'])) {
                            $is_json = is_string($taxonomy['txt_taxonomy']) && !empty(json_decode($taxonomy['txt_taxonomy'])) ? true : false;

                            if (empty($is_json)) {
                                categoryTaxonomy::deleteSpecificGoogleCategory($taxonomy['id_shop'], $taxonomy['lang'], $taxonomy['id_category']);
                                categoryTaxonomy::insertGoogleCategory($taxonomy['id_shop'], $taxonomy['id_category'], $taxonomy['txt_taxonomy'], $taxonomy['lang']);
                            }
                        }
                    }
                }

                \Configuration::updateValue('GMCP_HANDLE_TAXO_JSON', 1);

                return \Tools::redirectAdmin(\Context::getContext()->link->getAdminLink('AdminModules') . '&configure=gmerchantcenterpro');
            }
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }
    }
}
