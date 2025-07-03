<?php
/**
 * License limited to a single site, for use on another site please purchase a license for this module.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Dingedi.com
 * @copyright Copyright 2023 © Dingedi All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 * @category  Dingedi PrestaShop Modules
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'dgtranslationall/vendor/autoload.php';

class Dgtranslationall extends Module
{
    /** @var array $dgModuleConfig */
    private $dgModuleConfig;

    /** @var string $page */
    private $page;

    public function __construct()
    {
        $this->name = 'dgtranslationall';
        $this->tab = 'i18n_localization';
        $this->version = '4.17.0';
        $this->author = 'Dingedi';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->module_key = 'ef7f1e2fa626e241965461a073a1a77e';
        $this->displayName = $this->l('Translate all - Free and unlimited translation');
        $this->description = $this->l('Translate your entire shop automatically! With more than 3000 shops translated in more than 110 languages since its creation, Translation of all is the best module to translate your shop.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->page = empty(Tools::getValue('dgtranslationallpage')) ? 'index' : Tools::getValue('dgtranslationallpage');

        if (Tools::getValue('ajax') === '1' && Tools::getValue('configure') === $this->name) {
            ob_start();
            $action = Tools::getValue('action');

            if (strncmp($action, 'Content', strlen('Content')) === 0) {
                $this->initContent();
            } else if (strncmp($action, 'Modules', strlen('Modules')) === 0) {
                $this->initModules();
            } else if (strncmp($action, 'Themes', strlen('Themes')) === 0 || strncmp($action, 'Mails', strlen('Mails')) === 0) {
                $this->initThemesAndMails();
            }

            $this->dgModuleConfig['module_id'] = '48962';
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::install()
            && \Dingedi\PsTranslationsApi\DgTranslationTools::install()
            && $this->registerHook('actionObjectUpdateBefore')
            && $this->registerHook('actionObjectAddAfter')
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('displayDashboardToolbarTopMenu');
    }

    public function uninstall()
    {
        return parent::uninstall()
            && $this->unregisterHook('actionObjectUpdateBefore')
            && $this->unregisterHook('actionObjectAddAfter')
            && $this->unregisterHook('displayBackOfficeHeader')
            && \Dingedi\PsTranslationsApi\DgTranslationTools::uninstall($this->name);
    }

    public function getContent()
    {
        $this->context->smarty->assign([
            'dgtranslationall_page' => $this->page,
            'dgtranslationall_default_link' => Tools::getHttpHost(true) . __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/index.php?controller=AdminModules&configure=dgtranslationall&token=' . Tools::getValue('token'),
            'dgtranslationall_config' => $this->dgModuleConfig,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }











//STARTdgcontenttranslation
    /**
     * @param bool $loadTableOnly
     */
    public function initContent($loadTableOnly = false)
    {
        if ($loadTableOnly === false) {
            $this->dgModuleConfig = array(
                'is_ps_16' => \Dingedi\PsTools\DgShopInfos::isPrestaShop16(),
                'add_language_link' => \Dingedi\PsTools\DgTools::getAdminLink('AdminLocalization'),
                'link_admin_db_backup' => \Dingedi\PsTools\DgTools::getAdminLink('AdminBackup'),
                'link_admin_update_url_settings' => \Dingedi\PsTools\DgTools::getAdminLink('AdminMeta'),
                'cron_cli_command' => $this->getCronCliCommand(),
                'default_lang' => \Dingedi\PsTranslationsApi\DgTranslationTools::getDefaultLangId(),
                'module_name' => $this->name,
                'module_version' => $this->version,
                'module_id' => '47738',
                'show_leave_review' => \Dingedi\PsTranslationsApi\DgLeaveReview::canReview(),
                'has_review' => \Dingedi\PsTranslationsApi\DgLeaveReview::hasReview(),
                'free_chars' => (new \Dingedi\PsTranslationsApi\Configuration\DgFreeCharsConfiguration())->get('remaining')
            );

            if (method_exists($this, 'getProData')) {
                $this->dgModuleConfig['pro_data'] = $this->getProData();
            }
        }

        require_once _PS_MODULE_DIR_ . $this->name . '/classes/Tables/TablesAdapterList.php';

        TablesAdapterList::register();
    }

    public function ajaxProcessContentGetFilterData()
    {
        $data = $this->checkAndGetParams(['table', 'filter']);

        $table = $this->getContentTable($data['table']);
        $this->jsonResponse($table->getFilterData($data['filter']));
    }

    public function ajaxProcessContentSearchInTable()
    {
        $data = $this->checkAndGetParams(['search', 'type', 'name', 'id_lang']);

        $table = $this->getContentTable($data['name']);

        $searchQuery = trim($data['search']);

        $findLike = $table->findAllLike(
            $searchQuery,
            $data['id_lang']
        );

        $results = $findLike['results'];
        $tableFields = $table->getFields(false);

        foreach (['title', 'name'] as $field) {
            if (in_array($field, $tableFields)) {
                array_unshift($tableFields, ...array_splice($tableFields, array_search($field, $tableFields), 1));
            }
        }

        $ids = array_map(function ($r) use ($table) {
            return $r[$table->getPrimaryKey()];
        }, $results);

        $defaultLangId = \Dingedi\PsTranslationsApi\DgTranslationTools::getDefaultLangId();


        if (isset($data['filters'])) {
            $table->setRequestFilters($data['filters']);
        }

        $sourceElems = $table->findAll([$table->getPrimaryKey() => $ids, 'id_lang' => $defaultLangId]);

        $idsSource = array_map(function ($r) use ($table) {
            return $r[$table->getPrimaryKey()];
        }, $sourceElems);

        $results = array_filter($results, function ($r) use ($table, $idsSource) {
            return in_array($r[$table->getPrimaryKey()], $idsSource);
        });

        $tableFields = array_merge([$table->getPrimaryKey()], $tableFields);

        $formatted = [];

        foreach ($results as $result) {
            $formatted[] = [
                'key' => $table->getPrimaryKey(),
                'source' => array_values(array_filter($sourceElems, function ($s) use ($table, $result) {
                    return (int)$s[$table->getPrimaryKey()] === (int)$result[$table->getPrimaryKey()];
                }))[0],
                'translated' => $result
            ];
        }

        $this->jsonResponse(array(
            'success' => true,
            'data' => [
                'fields' => $tableFields,
                'results' => $formatted,
            ]
        ));
    }

    public function ajaxProcessContentSearchWords()
    {
        $data = $this->checkAndGetParams(['search_query', 'advanced_search']);

        $advancedSearch = $data['advanced_search'] === "true";
        $searchQuery = (string)$data['search_query'];
        $searchTable = (string)$data['search_table'];
        $searchIdLang = (string)$data['search_id_lang'];

        if (trim($searchQuery) === "") {
            $this->jsonResponse(array(
                'success' => true,
                'results' => array()
            ));
        }

        $tables = $this->getContentTables();
        $tables = array_map(function ($elem) {
            return $elem['tables'];
        }, $tables);

        $tablesArray = array();

        foreach ($tables as $table) {
            $tablesArray = array_merge($tablesArray, $table);
        }

        $results = array();
        /** @var \Dingedi\TablesTranslation\DgTableTranslatable16 $table */
        foreach ($tablesArray as $table) {
            if ($searchTable !== '' && $searchTable !== $table->getTableName(false)) {
                continue;
            }

            $findLike = $table->findAllLike($searchQuery, ($searchIdLang === "" ? null : (int)$searchIdLang), null, $advancedSearch);

            $searchRegex = $findLike['regex'];
            $tableResults = $findLike['results'];

            foreach ($tableResults as $item) {
                if (\Language::getLanguage((int)$item['id_lang']) === false) {
                    continue;
                }

                foreach ($item as $field => $value) {
                    $value = html_entity_decode($value, ENT_QUOTES | ENT_COMPAT, 'UTF-8');

                    $marked = $value;

                    if ($marked === strip_tags($marked)) {
                        if (preg_match($searchRegex, $marked, $matches)) {
                            foreach (array_unique($matches) as $match) {
                                $marked = preg_replace($searchRegex, '<mark>' . $searchQuery . '</mark>', $marked);
                            }
                        }
                    } else {
                        $dgHtmlParser = new \Dingedi\PsTranslationsApi\Html\DgHTMLParser($marked);

                        foreach ($dgHtmlParser->getTextNodes() as $node) {
                            $node->nodeValue = str_replace(array('<mark>', '</mark>'), array('', ''), $node->nodeValue);
                            $node->nodeValue = preg_replace($searchRegex, '<mark>' . $searchQuery . '</mark>', $node->nodeValue);
                        }

                        $marked = $dgHtmlParser->getHTMLOutput();
                    }

                    if (strpos($marked, '<mark>') !== false && strpos($marked, '</mark>') !== false && trim($marked) !== trim($value) && (str_replace("\n", '', strip_tags($marked, '<mark>')) !== str_replace("\n", '', strip_tags($value, '<mark>')))) {
                        $result = array(
                            'id_lang' => $item['id_lang'],
                            'table' => $table->getTableName(false),
                            'field' => $field,
                            'id' => $item[$table->getPrimaryKey()],
                            'text' => strip_tags($marked, '<mark>')
                        );

                        $results[] = $result;
                    }
                }
            }
        }

        $this->jsonResponse(array(
            'success' => true,
            'results' => array_values(array_unique($results, SORT_REGULAR))
        ));
    }

    public function ajaxProcessContentReplaceWords()
    {
        $data = $this->checkAndGetParams(array('rows', 'search_query', 'advanced_search', 'replace_query'));

        $rows = (array)$data['rows'];
        $advancedSearch = $data['advanced_search'] === "true";
        $searchQuery = (string)$data['search_query'];
        $replaceQuery = (string)$data['replace_query'];

        $regex = '/';

        $searchQueryQuoted = preg_quote($searchQuery, '/');

        if (!$advancedSearch) {
            $regex .= '(\b' . $searchQueryQuoted . '\b)';
        } else {
            $regex .= '(' . $searchQueryQuoted . ')';
        }

        $regex .= '/um';


        foreach ($rows as $row) {
            /** @var \Dingedi\TablesTranslation\DgTableTranslatable16 $table */
            $table = $this->getContentTable($row['table']);

            $selectQuery = new \DbQuery();
            $selectQuery->select($row['field'])
                ->from($table->getTableName(false))
                ->where($table->getPrimaryKey() . ' = ' . $row['id'])
                ->where('id_lang = ' . $row['id_lang']);

            $query = $selectQuery->build();

            if ($table->isMultiShop()) {
                $query .= ' ' . \Shop::addSqlRestriction();
            }

            $result = \Db::getInstance()->executeS($query)[0];

            if (!empty($result)) {
                $value = $result[$row['field']];

                if ($value === strip_tags($value)) {
                    $value = \pSQL(preg_replace($regex, $replaceQuery, $value));

                    if (isset($table->getFieldsRewrite()[$row['field']])) {
                        $value = \Tools::link_rewrite($value);
                    }
                } else {
                    $value = html_entity_decode($value, ENT_QUOTES | ENT_COMPAT, 'UTF-8');

                    $dgHtmlParser = new \Dingedi\PsTranslationsApi\Html\DgHTMLParser($value);

                    foreach ($dgHtmlParser->getTextNodes() as $node) {
                        $node->nodeValue = preg_replace($regex, $replaceQuery, $node->nodeValue);
                    }

                    $value = \pSQL($dgHtmlParser->getHTMLOutput(), true);
                }

                \Db::getInstance()->update($table->getTableName(false), array($row['field'] => $value), $table->getPrimaryKey() . ' = ' . $row['id'] . ' AND id_lang = ' . $row['id_lang'] . ' ' . ($table->isMultiShop() ? Shop::addSqlRestriction() : ''));
            }
        }

        $this->jsonResponse(array(
            'success' => true
        ));
    }

    public function ajaxProcessGlobalSetDefaultLang()
    {
        $data = $this->checkAndGetParams(array(
            'id_lang'
        ));

        Configuration::updateValue('dingedi_default_lang', (int)$data['id_lang']);

        $this->jsonResponse(array(
            'success' => 1
        ));
    }

    public function ajaxProcessGlobalSaveSettings()
    {
        \Dingedi\PsTranslationsApi\DgTranslationTools::saveSettings();

        $this->jsonResponse(array(
            'success' => 1,
            'message' => $this->l('Settings successfully saved')
        ));
    }

    public function ajaxProcessGlobalSaveApiKeys()
    {
        \Dingedi\PsTranslationsApi\DgTranslationTools::saveApiKeys();

        $this->jsonResponse(array(
            'success' => 1,
            'message' => $this->l('API keys successfully saved')
        ));
    }

    public function ajaxProcessGlobalTestApiKey()
    {
        \Dingedi\PsTranslationsApi\DgTranslationTools::saveApiKeys(true);

        try {
            \Dingedi\PsTranslationsApi\DgTranslateApi::translate("Hi", "en", "fr", 0);
        } catch (\Exception $e) {
            $this->jsonError(array(
                'message' => $this->l('The API key does not work') . ': ' . $e->getMessage()
            ));
        }

        $this->jsonResponse(array(
            'message' => $this->l('The API key is working correctly')
        ));
    }

    /**
     * @throws Exception
     */
    public function ajaxProcessContentGetPercentageTranslation()
    {
        $data = $this->checkAndGetParams(array(
            'name', 'id_lang_to'
        ));

        $translation_data = Tools::getValue('translation_data');
        $current = isset($translation_data['current']) ? (int)$translation_data['current'] : false;

        $tableName = (string)$data['name'];
        $id_lang_to = (int)$data['id_lang_to'];

        $dgTableTranslatable = $this->getContentTable($tableName, false);
        $dgTableCalculateMissingTranslations = new \Dingedi\TablesTranslation\DgTableCalculateMissingTranslations($dgTableTranslatable);

        $this->jsonResponse($dgTableCalculateMissingTranslations->getTranslationsPercent(Language::getLanguage($id_lang_to), $current));
    }

    /**
     * @return mixed[]
     */
    private function getContentModuleBaseData()
    {
        return array(
            'moduleConfig' => array_merge(
                $this->dgModuleConfig,
                array('languages' => $this->getLanguages()),
                array('translations' => $this->getContentModuleTranslations()),
                array('translationsProviders' => \Dingedi\PsTranslationsApi\DgTranslationTools::getTranslationsConfiguration()),
                array('shopConfig' => \Dingedi\PsTranslationsApi\DgTranslationTools::getShopConfig())
            )
        );
    }

    public function ajaxProcessContentGetData()
    {
        $this->jsonResponse(array_merge(
            $this->getContentModuleBaseData(),
            array(
                'translatable' => $this->getContentTables(),
                'failed_translation' => \Dingedi\PsTranslationsApi\models\FailedTranslation::getAll('tables')
            )
        ));
    }

    public function ajaxProcessContentCreateBackup()
    {
        if (\Dingedi\PsTranslationsApi\DgTranslationTools::isAutoBackupEnabled()) {
            $data = $this->checkAndGetParams(array('tables'));

            $result = \Dingedi\TablesTranslation\DgTableBackup::createBackup($data['tables']);

            if (!$result) {
                $this->jsonError([
                    'error' => 1
                ]);
            }
        }

        $this->jsonResponse([
            'success' => 1
        ]);
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function ajaxProcessContentDeleteFailedTranslation()
    {
        $data = $this->checkAndGetParams(array('id'));

        $failedTranslation = \Dingedi\PsTranslationsApi\models\FailedTranslation::getEmptyInstance((int)$data['id']);
        $failedTranslation->delete();

        $this->jsonResponse(array(
            'success' => 1
        ));
    }

    /**
     * @throws Exception
     */
    public function ajaxProcessContentGetModalData()
    {
        $data = $this->checkAndGetParams(array('tableName'));

        $this->jsonResponse(array_merge(
            $this->getContentModuleBaseData(),
            array('table' => $this->getContentTable($data['tableName']))
        ));
    }

    public function ajaxProcessContentTranslate()
    {
        $data = $this->checkAndGetParams(array(
            'name', 'id_lang_from', 'id_lang_to', 'latin', 'overwrite', 'requests', 'current'
        ));

        try {
            $this->jsonResponse($this->translateContentTable(
                (string)$data['name'],
                (int)$data['id_lang_from'],
                (int)$data['id_lang_to'],
                (int)$data['latin'],
                ($data['overwrite'] === 'true'),
                ((int)$data['requests'] > 1) ? (int)$data['current'] : 1
            ));
        } catch (Exception $exception) {
            $responseData = array(
                'message' => $exception->getMessage(),
            );

            if (isset($data['retry']) && (int)$data['retry'] === 2) {
                try {
                    $failedTranslation = \Dingedi\PsTranslationsApi\models\FailedTranslation::addNew(
                        $exception,
                        'tables-' . $data['name']
                    );

                    $responseData['failed_translation'] = $failedTranslation;
                } catch (\PrestaShopException $e) {
                }
            }

            $this->jsonError($responseData);
        }
    }

    /**
     * @return mixed[]
     */
    private function getContentModuleTranslations()
    {
        return [
            'api_keys' => [
                'microsoftProvider' => [
                    ['label' => $this->l('Global'), 'value' => 'api'],
                    ['label' => $this->l('North America'), 'value' => 'api-nam'],
                    ['label' => $this->l('Europe'), 'value' => 'api-eur'],
                    ['label' => $this->l('Asia Pacific'), 'value' => 'api-apc'],
                ],
            ],
            'Setup wizard' => $this->l('Setup wizard'),
            'Next' => $this->l('Next'),
            'Previous' => $this->l('Previous'),
            'Message from %s' => $this->l('Message from %s'),
            'Free monthly quota of %s characters' => $this->l('Free monthly quota of %s characters'),
            'Pricing options' => $this->l('Pricing options'),
            'Obtain an API key' => $this->l('Obtain an API key'),
            'offers a trial offer: %s credit for %s months' => $this->l('offers a trial offer: %s credit for %s months'),
            'Finish' => $this->l('Finish'),
            'Message from' => $this->l('Message from'),
            'You must have a Google Cloud account before starting.' => $this->l('You must have a Google Cloud account before starting.'),
            'Click here' => $this->l('Click here'),
            'to create one.' => $this->l('to create one.'),
            'Go to the Google Cloud dashboard' => $this->l('Go to the Google Cloud dashboard'),
            'Click on "Select a project"' => $this->l('Click on "Select a project"'),
            'Click on "New project"' => $this->l('Click on "New project"'),
            'Create a project' => $this->l('Create a project'),
            'Select the project' => $this->l('Select the project'),
            'Click on "APIs & Services"' => $this->l('Click on "APIs & Services"'),
            'Click on "Enable APIs and Services"' => $this->l('Click on "Enable APIs and Services"'),
            'Search and select "Cloud Translation API"' => $this->l('Search and select "Cloud Translation API"'),
            'Enable "Cloud Translation API' => $this->l('Enable "Cloud Translation API'),
            'You can skip these two steps if you already have a billing account in your Google Cloud account.' => $this->l('You can skip these two steps if you already have a billing account in your Google Cloud account.'),
            'Create an API key' => $this->l('Create an API key'),
            'Copy and enter it in the module' => $this->l('Copy and enter it in the module'),
            'Click to view in full screen' => $this->l('Click to view in full screen'),
            'You must have a Microsoft Azure account before starting.' => $this->l('You must have a Microsoft Azure account before starting.'),
            'Go to the Microsoft Azure dashboard' => $this->l('Go to the Microsoft Azure dashboard'),
            'Click on "Create a ressource"' => $this->l('Click on "Create a ressource"'),
            'Search "Translator text"' => $this->l('Search "Translator text"'),
            'Click on "Create"' => $this->l('Click on "Create"'),
            'Fill in the required fields and click on "Review + create"' => $this->l('Fill in the required fields and click on "Review + create"'),
            'Go to ressource' => $this->l('Go to ressource'),
            'Click on "Keys and Endpoint"' => $this->l('Click on "Keys and Endpoint"'),
            'Copy KEY 1 enter it in the module' => $this->l('Copy KEY 1 enter it in the module'),
            'Configure API keys' => $this->l('Configure API keys'),
            'Apply for all shops' => $this->l('Apply for all shops'),
            'API Key' => $this->l('API Key'),
            'Save' => $this->l('Save'),
            'Server' => $this->l('Server'),
            'Location' => $this->l('Location'),
            'Show' => $this->l('Show'),
            'Hide' => $this->l('Hide'),
            'Plan' => $this->l('Plan'),
            'offers a free offer with a quota of %s characters per month' => $this->l('offers a free offer with a quota of %s characters per month'),
            'Add a language' => $this->l('Add a language'),
            'API Keys' => $this->l('API Keys'),
            'Excluded words' => $this->l('Excluded words'),
            'Exclude words from translation' => $this->l('Exclude words from translation'),
            'The words you add will not be translated. For example, you can add brand names.' => $this->l('The words you add will not be translated. For example, you can add brand names.'),
            'Performance' => $this->l('Performance'),
            'Elements per query' => $this->l('Elements per query'),
            'Tools' => $this->l('Tools'),
            'Settings' => $this->l('Settings'),
            'Content translation' => $this->l('Content translation'),
            'Translation' => $this->l('Translation'),
            'Translate active or inactive elements' => $this->l('Translate active or inactive elements'),
            'If you choose active, only the active elements (your products for example) will be translated.' => $this->l('If you choose active, only the active elements (your products for example) will be translated.'),
            'All' => $this->l('All'),
            'Inactive' => $this->l('Inactive'),
            'Active' => $this->l('Active'),
            'You must refresh your browser page after saving this setting.' => $this->l('You must refresh your browser page after saving this setting.'),
            'Exclude all brands' => $this->l('Exclude all brands'),
            'Add a word to exclude from the translation' => $this->l('Add a word to exclude from the translation'),
            'Do not forget to make a backup of your database before starting the translation!' => $this->l('Do not forget to make a backup of your database before starting the translation!'),
            'Select all' => $this->l('Select all'),
            'items' => $this->l('items'),
            'Fields to translate' => $this->l('Fields to translate'),
            'selected fields' => $this->l('selected fields'),
            'new' => $this->l('new'),
            'Automatic translation' => $this->l('Automatic translation'),
            'Enabled' => $this->l('Enabled'),
            'Disabled' => $this->l('Disabled'),
            'No translation service is configured. Please configure one in order to be able to launch translations.' => $this->l('No translation service is configured. Please configure one in order to be able to launch translations.'),
            'Smart dictionary' => $this->l('Smart dictionary'),
            'Add' => $this->l('Add'),
            'Add a word' => $this->l('Add a word'),
            'If the translation of certain words do not fit, you can define your own translations here.' => $this->l('If the translation of certain words do not fit, you can define your own translations here.'),
            'Add the word whose translation you want to change first, then add the translations you want.' => $this->l('Add the word whose translation you want to change first, then add the translations you want.'),
            'Find and replace' => $this->l('Find and replace'),
            'Find the text to replace' => $this->l('Find the text to replace'),
            'Search' => $this->l('Search'),
            'ID' => $this->l('ID'),
            'Type' => $this->l('Type'),
            'Language' => $this->l('Language'),
            'Replace' => $this->l('Replace'),
            'Replace by' => $this->l('Replace by'),
            'No result' => $this->l('No result'),
            'Enable automatic translation of your content. Example: when you modify the description of a product, the descriptions of the selected languages will be translated automatically.' => $this->l('Enable automatic translation of your content. Example: when you modify the description of a product, the descriptions of the selected languages will be translated automatically.'),
            'Find and replace words found in the different contents of your shop' => $this->l('Find and replace words found in the different contents of your shop'),
            'Modules translation' => $this->l('Modules translation'),
            'Themes and emails translation' => $this->l('Themes and emails translation'),
            'This feature is available in the PRO version' => $this->l('This feature is available in the PRO version'),
            'See the module' => $this->l('See the module'),
            'Any question ?' => $this->l('Any question ?'),
            'contact us' => $this->l('contact us'),
            'Service to use' => $this->l('Service to use'),
            'Video tutorial' => $this->l('Video tutorial'),
            'Configuration' => $this->l('Configuration'),
            'Supported languages' => $this->l('Supported languages'),
            'List of ISO codes accepted for translation' => $this->l('List of ISO codes accepted for translation'),
            'PrestaShop Addons order ID' => $this->l('PrestaShop Addons order ID'),
            'We offer a free and unlimited translation service. To be able to use this service, please configure your PrestaShop Addons order ID.' => $this->l('We offer a free and unlimited translation service. To be able to use this service, please configure your PrestaShop Addons order ID.'),
            'Leave a review on our module and get better translation speed for free.' => $this->l('Leave a review on our module and get better translation speed for free.'),
            'Leave a review' => $this->l('Leave a review'),
            'Accented URLs are disabled, urls will not be translated for languages using non-Latin characters.' => $this->l('Accented URLs are disabled, urls will not be translated for languages using non-Latin characters.'),
            'Some installed languages use non-Latin characters:' => $this->l('Some installed languages use non-Latin characters:'),
            'Change settings' => $this->l('Change settings'),
            'Please configure the selected translation service' => $this->l('Please configure the selected translation service'),
            'Not available with' => $this->l('Not available with'),
            'Translation speed' => $this->l('Translation speed'),
            'The value corresponds to the number of items (e.g. products) translated in each query.' => $this->l('The value corresponds to the number of items (e.g. products) translated in each query.'),
            'Very low' => $this->l('Very low'),
            'Low' => $this->l('Low'),
            'Normal' => $this->l('Normal'),
            'High' => $this->l('High'),
            'Custom' => $this->l('Custom'),
            'Help' => $this->l('Help'),
            'Close' => $this->l('Close'),
            'Here are several solutions to solve this error:' => $this->l('Here are several solutions to solve this error:'),
            'Reduce the translation speed in the module settings.' => $this->l('Reduce the translation speed in the module settings.'),
            'Increase the "max_execution_time" parameter in the php.ini configuration file of your server.' => $this->l('Increase the "max_execution_time" parameter in the php.ini configuration file of your server.'),
            'Contact your server manager to increase the "Timeout" setting of your Apache web server.' => $this->l('Contact your server manager to increase the "Timeout" setting of your Apache web server.'),
            'If the error is still present, please contact us.' => $this->l('If the error is still present, please contact us.'),
            'show details' => $this->l('show details'),
            'This word already exists for this language' => $this->l('This word already exists for this language'),
            'Update' => $this->l('Update'),
            'Estimated time remaining before the end of the translation.' => $this->l('Estimated time remaining before the end of the translation.'),
            'Friendly URLs are disabled, internal links in your content will not be translated.' => $this->l('Friendly URLs are disabled, internal links in your content will not be translated.'),
            'Translate' => $this->l('Translate'),
            'Advanced parameters' => $this->l('Advanced parameters'),
            'Overwrite all translations' => $this->l('Overwrite all translations'),
            'Latin option (for supported languages)' => $this->l('Latin option (for supported languages)'),
            'My source text is in Latin characters' => $this->l('My source text is in Latin characters'),
            'I want to translate into Latin characters' => $this->l('I want to translate into Latin characters'),
            'The source language is the language you want to translate from' => $this->l('The source language is the language you want to translate from'),
            'Languages to translate' => $this->l('Languages to translate'),
            'The languages to translate are the languages you want to translate, from the source language selected previously' => $this->l('The languages to translate are the languages you want to translate, from the source language selected previously'),
            'Source language' => $this->l('Source language'),
            'Stop' => $this->l('Stop'),
            'Server error' => $this->l('Server error'),
            'Access your invoices on PrestaShop Addons' => $this->l('Access your invoices on PrestaShop Addons'),
            'Copy the order ID of the module' => $this->l('Copy the order ID of the module'),
            'Remember to save changes before translating' => $this->l('Remember to save changes before translating'),
            'Formality' => $this->l('Formality'),
            'Sets whether the translated text should lean towards formal or informal language for supported languages' => $this->l('Sets whether the translated text should lean towards formal or informal language for supported languages'),
            'Default' => $this->l('Default'),
            'Formal' => $this->l('Formal'),
            'Informal' => $this->l('Informal'),
            'Unselect all' => $this->l('Unselect all'),
            'Translate all' => $this->l('Translate all'),
            'State' => $this->l('State'),
            'Warning, spaces are present which could alter the formatting after the translation' => $this->l('Warning, spaces are present which could alter the formatting after the translation'),
            'Recommended parameters' => $this->l('Recommended parameters'),
            'No' => $this->l('No'),
            'Apply' => $this->l('Apply'),
            'Custom value' => $this->l('Custom value'),
            'Languages' => $this->l('Languages'),
            'Translate button' => $this->l('Translate button'),
            'Display a translate button in your back office to translate directly from a content page (product, category, attributes, etc.)' => $this->l('Display a translate button in your back office to translate directly from a content page (product, category, attributes, etc.)'),
            'Recommended settings' => $this->l('Recommended settings'),
            'Apply recommended settings' => $this->l('Apply recommended settings'),
            'Keep current settings' => $this->l('Keep current settings'),
            'Calculate characters to translate' => $this->l('Calculate characters to translate'),
            'Translate only internal links' => $this->l('Translate only internal links'),
            'There are' => $this->l('There are'),
            'Characters to translate' => $this->l('Characters to translate'),
            'The actual number of characters sent to the API may vary.' => $this->l('The actual number of characters sent to the API may vary.'),
            'Cron job' => $this->l('Cron job'),
            'Run your translations in the background with a cron job ' => $this->l('Run your translations in the background with a cron job '),
            'Command:' => $this->l('Command:'),
            'This table contains a lot of data. To load the display of the translation percentages, please click manually on the button.' => $this->l('This table contains a lot of data. To load the display of the translation percentages, please click manually on the button.'),
            'Load by clicking' => $this->l('Load by clicking'),
            'Unable to calculate the number of characters to translate. Please check that all selected content are loaded.' => $this->l('Unable to calculate the number of characters to translate. Please check that all selected content are loaded.'),
            'Translation pending' => $this->l('Translation pending'),
            'An error occurred while translating' => $this->l('An error occurred while translating'),
            'From' => $this->l('From'),
            'To' => $this->l('To'),
            'requests' => $this->l('requests'),
            'Progression status' => $this->l('Progression status'),
            'Options' => $this->l('Options'),
            'Delete' => $this->l('Delete'),
            'Restart' => $this->l('Restart'),
            'Translate by ID' => $this->l('Translate by ID'),
            'If you deactivate this option, similar words with upper or lower case will also be taken into account. For the word "hello" variants like "Hello", "HELLO", etc will also be taken into account.' => $this->l('If you deactivate this option, similar words with upper or lower case will also be taken into account. For the word "hello" variants like "Hello", "HELLO", etc will also be taken into account.'),
            'exact word' => $this->l('exact word'),
            'Latin' => $this->l('Latin'),
            'Filters' => $this->l('Filters'),
            'filters' => $this->l('filters'),
            'Documentation' => $this->l('Documentation'),
            'Support' => $this->l('Support'),
            'See our modules' => $this->l('See our modules'),
            'Leave a comment' => $this->l('Leave a comment'),
            'Code ISO' => $this->l('Code ISO'),
            'Search in progress' => $this->l('Search in progress'),
            'Replace all' => $this->l('Replace all'),
            'Enabled when:' => $this->l('Enabled when:'),
            'Addition' => $this->l('Addition'),
            'Please read the warnings and recommendations related to creating backup.' => $this->l('Please read the warnings and recommendations related to creating backup.'),
            'Read' => $this->l('Read'),
            'Activate this option to automatically create a backup of the tables in your database that will be affected by a translation process.' => $this->l('Activate this option to automatically create a backup of the tables in your database that will be affected by a translation process.'),
            'Example: if you translate your products, a backup of the "product_lang" table will be made before the translation.' => $this->l('Example: if you translate your products, a backup of the "product_lang" table will be made before the translation.'),
            'See all backups' => $this->l('See all backups'),
            'State:' => $this->l('State:'),
            'Backup failed' => $this->l('Backup failed'),
            'Continue' => $this->l('Continue'),
            'Cancel' => $this->l('Cancel'),
            'The automatic backup function is activated however the task failed' => $this->l('The automatic backup function is activated however the task failed'),
            'Do you want to continue the translation process?' => $this->l('Do you want to continue the translation process?'),
            'Automatic Backup' => $this->l('Automatic Backup'),
            'Leave a review on our module and instantly get better translation speed' => $this->l('Leave a review on our module and instantly get better translation speed'),
            'modules' => $this->l('modules'),
            'themes' => $this->l('themes'),
            'emails' => $this->l('emails'),
            'are installed on your store and are maybe not translated' => $this->l('are installed on your store and are maybe not translated'),
            'Folder ID' => $this->l('Folder ID'),
            'Search all occurrences of the search including inside a word' => $this->l('Search all occurrences of the search including inside a word'),
            'Translation in progress' => $this->l('Translation in progress'),
            'Search for elements to translate' => $this->l('Search for elements to translate'),
            'Failed to load translations, click to try again' => $this->l('Failed to load translations, click to try again'),
            'Check that the API key is valid. This will consume 2 characters.' => $this->l('Check that the API key is valid. This will consume 2 characters.'),
            'Test the API key' => $this->l('Test the API key'),
            'Translation service not configured' => $this->l('Translation service not configured'),
            'An API key for the selected translation service must be configured before starting the translation' => $this->l('An API key for the selected translation service must be configured before starting the translation'),
            'Configure API Key' => $this->l('Configure API Key'),
            'Go to "Parameters > API Key"' => $this->l('Go to "Parameters > API Key"'),
            'Once your review is submitted, click the button below to start your translation with better translation speed.' => $this->l('Once your review is submitted, click the button below to start your translation with better translation speed.'),
            'Click here to reopen the page' => $this->l('Click here to reopen the page'),
            'Start translation' => $this->l('Start translation'),
            'Do not show again' => $this->l('Do not show again'),
            'No thanks' => $this->l('No thanks'),
            'You benefit from faster translation speed.' => $this->l('You benefit from faster translation speed.'),
            'Please note that a translation speed that is too high for your chosen server or translation service may result in server errors.' => $this->l('Please note that a translation speed that is too high for your chosen server or translation service may result in server errors.'),
            'Fast' => $this->l('Fast'),
            'Very fast' => $this->l('Very fast'),
            'Help to solve the error' => $this->l('Help to solve the error'),
            'Add selected items' => $this->l('Add selected items'),
            'Selected' => $this->l('Selected'),
            'selected elements' => $this->l('selected elements'),
            'beta' => $this->l('beta'),
            'Copy without translate' => $this->l('Copy without translate'),
            'This language is disabled, the links present in the content cannot be translated.' => $this->l('This language is disabled, the links present in the content cannot be translated.'),
            'Copy in clipboard' => $this->l('Copy in clipboard'),
            'Copy error log' => $this->l('Copy error log'),
            'Temperature' => $this->l('Temperature'),
            'What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.' => $this->l('What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.'),
            'Translation sentence' => $this->l('Translation sentence'),
            'Be careful when changing this setting, the translation may no longer work.' => $this->l('Be careful when changing this setting, the translation may no longer work.'),
            'error' => $this->l('error'),
            'update language' => $this->l('update language'),
            'or' => $this->l('or'),
            'retry' => $this->l('retry'),
            'Update all language' => $this->l('Update all language'),
            'Retry all' => $this->l('Retry all'),
            'Temporary offer available, get 1 million characters for free' => $this->l('Temporary offer available, get 1 million characters for free'),
            'See' => $this->l('See'),
            'temporary offer' => $this->l('temporary offer'),
            'Show more' => $this->l('Show more'),
            'Temporary offer' => $this->l('Temporary offer'),
            'Get instantly a million characters for free to use the translation service of your choice by leaving a review on our module' => $this->l('Get instantly a million characters for free to use the translation service of your choice by leaving a review on our module'),
            'Leave a review to benefit from this offer' => $this->l('Leave a review to benefit from this offer'),
            'Service' => $this->l('Service'),
            'Get instantly a million characters for free to use the translation service of your choice by leaving a review on our module' => $this->l('Get instantly a million characters for free to use the translation service of your choice by leaving a review on our module'),
            'Also get better translation speed for' => $this->l('Also get better translation speed for'),
            'Temporary offer' => $this->l('Temporary offer'),
            'Get instantly a million characters for free to use the translation service of your choice by leaving a review on our module' => $this->l('Get instantly a million characters for free to use the translation service of your choice by leaving a review on our module'),
            'Leave a review to benefit from this offer' => $this->l('Leave a review to benefit from this offer'),
            'Service' => $this->l('Service'),
            'Get instantly a million characters for free to use the translation service of your choice by leaving a review on our module' => $this->l('Get instantly a million characters for free to use the translation service of your choice by leaving a review on our module'),
            'Also get better translation speed for' => $this->l('Also get better translation speed for'),
            'Once your review is submitted, wait a few seconds and refresh the page to benefit from the offer.' => $this->l('Once your review is submitted, wait a few seconds and refresh the page to benefit from the offer.'),
            'Refresh' => $this->l('Refresh'),
            'Please configure "Dingedi Free Translate" and leave a review to benefit from this offer' => $this->l('Please configure "Dingedi Free Translate" and leave a review to benefit from this offer'),
            'Allows you to translate some linked elements' => $this->l('Allows you to translate some linked elements'),
            'Linked elements' => $this->l('Linked elements'),
            'linked items' => $this->l('linked items'),
            'Fields' => $this->l('Fields'),
            'Start' => $this->l('Start'),
            'End' => $this->l('End'),
            'You benefit from a million free characters' => $this->l('You benefit from a million free characters'),
        ];
    }

    /**
     * @return mixed[]
     */
    public function getContentTables()
    {
        return array(
            array(
                'group_name' => $this->l('Catalog'),
                'icon' => 'store',
                'tables' => $this->filterExistingTables(array(
                    new Product_lang(),
                    new Category_lang(),

                    new Feature_lang(),
                    new Feature_value_lang(),

                    new Attribute_lang(),
                    new Attribute_group_lang(),
                ))
            ),
            array(
                'group_name' => $this->l('Pages'),
                'icon' => 'desktop_mac',
                'tables' => $this->filterExistingTables(array(
                    new Cms_lang(),
                    new Cms_category_lang(),
                    new Meta_lang(),
                ))
            ),
            array(
                'group_name' => $this->l('Suppliers'),
                'icon' => 'account_circle',
                'tables' => $this->filterExistingTables(array(
                    new Supplier_lang(),
                ))
            ),
            array(
                'group_name' => $this->l('Manufacturers'),
                'icon' => 'business',
                'tables' => $this->filterExistingTables(array(
                    new Manufacturer_lang(),
                ))
            ),
            array(
                'group_name' => $this->l('Orders'),
                'icon' => 'shopping_basket',
                'tables' => $this->filterExistingTables(array(
                    new Order_message_lang(),
                    new Order_return_state_lang(),
                    new Order_state_lang(),
                    new Supply_order_state_lang(),
                ))
            ),
            array(
                'group_name' => $this->l('Others'),
                'icon' => 'info',
                'tables' => $this->filterExistingTables(array(
                    new Attachment_lang(),
                    new Carrier_lang(),
                    new Cart_rule_lang(),
                    new Contact_lang(),
                    new Customization_field_lang(),
                    new Gender_lang(),
                    new Homeslider_slides_lang(),
                    new Image_lang(),
                    new Linksmenutop_lang(),
                    new Profile_lang(),
                    new Quick_access_lang(),
                    new Reassurance_lang(),
                    new Risk_lang(),
                    new Stock_mvt_reason_lang(),
                    new Psreassurance_lang(),
                ))
            ),
        );
    }

    /**
     * @throws Exception
     * @param string $table_name
     * @param int $idLangFrom
     * @param int $idLangTo
     * @param int $latin
     * @param bool $overwrite
     * @param int $paginate
     * @return mixed[]
     */
    public function translateContentTable($table_name, $idLangFrom, $idLangTo, $latin, $overwrite, $paginate)
    {
        $dgTableTranslatable = $this->getContentTable($table_name, false);
        $dgTableTranslation = new \Dingedi\TablesTranslation\DgTableTranslation($dgTableTranslatable, $idLangFrom, $idLangTo, $overwrite, $latin);
        $dataToReturn = $dgTableTranslation->translate($paginate);

        $message = $this->l('Data has been translated');

        if (\Tools::getValue('translate-modal') === 'true') {
            $message .= '. ' . $this->l('Refresh the page to see the translations');
        }

        $response = [
            'success' => 1,
            'message' => $message,
        ];

        if (is_array($dataToReturn)) {
            $response['data'] = $dataToReturn;
        }

        return $response;
    }

    /**
     * @throws Exception
     * @return \Dingedi\TablesTranslation\DgTableTranslatable16|\Dingedi\TablesTranslation\DgTableTranslatable17
     * @param string $table_name
     * @param bool $withPrefix
     */
    public function getContentTable($table_name, $withPrefix = false)
    {
        foreach ($this->getContentTables() as $tablesGroup) {
            /** @var \Dingedi\TablesTranslation\DgTableTranslatable16|\Dingedi\TablesTranslation\DgTableTranslatable17 $table */
            foreach ($tablesGroup['tables'] as $table) {
                if ($table->getTableName($withPrefix) === $table_name) {
                    return $table;
                }
            }
        }

        if (method_exists($this, 'initModules')) {
            $this->initModules(true);

            foreach (\Dingedi\TablesTranslation\TablesAdaptersStore::getInstance()->getAdapters() as $table) {
                if ($table->getTableName($withPrefix) === $table_name) {
                    return $table;
                }
            }

            if ($table = \Dingedi\TablesTranslation\DgTablesList::getObject($table_name)) {
                return $table;
            }
        }

        throw new \Exception('This table does not exist');
    }

    /**
     * @param bool $add
     */
    private function _hookAutomaticTranslation($params, $add = false)
    {
        $add = (bool) $add;
        $translation_data = Tools::getValue('translation_data');

        $automaticTranslationConfiguration = (new \Dingedi\PsTranslationsApi\Configuration\AutomaticTranslationConfiguration);

        if ($automaticTranslationConfiguration->get('enabled') === false || ($translation_data && array_key_exists('automatic_progress', $translation_data) && $translation_data['automatic_progress'] === true)) {
            return true;
        }

        Configuration::set('dingedi_translation_filter', 2);
        $this->initContent(true);

        $object = $params['object'];

        /** @var \Dingedi\TablesTranslation\AbstractTableAdapter|false $supportModel */
        $supportModel = \Dingedi\TablesTranslation\TablesAdaptersStore::getInstance()->supportObjectModel($object);

        if ($supportModel === false || !property_exists($object, 'id')) {
            return true;
        }

        $oldItem = $supportModel->findOneByPrimaryKey($object->id, array('id_lang' => $automaticTranslationConfiguration->get('id_lang_from')))[0];
        $new = array();
        $old = array();

        foreach ($supportModel->getFields() as $field) {
            if (property_exists($object, $field)) {
                $_field = $object->$field;

                if (is_array($_field)) {
                    $new[$field] = $_field[$automaticTranslationConfiguration->get('id_lang_from')];
                    $old[$field] = $oldItem[$field];
                }
            }
        }

        list($old, $new) = array_map(function ($array) {
            return array_map(function ($k) {
                $k = html_entity_decode(str_replace(array("\n", "\r", "\n\r"), array('', '', ''), $k));

                if (strip_tags($k) !== $k) {
                    $k = \Tools::purifyHTML($k);
                }

                return $k;
            }, $array);
        }, [$old, $new]);

        // get fields with diff
        $updatedFields = array_keys(array_diff_assoc($old, $new));

        if (count($supportModel->getDynamicFields())) {
            $updatedFields = array_merge($updatedFields, $supportModel->dynamic_fields);
        }

        $supportItemRewrite = $supportModel->supportedItemRewrite(array_flip($updatedFields));

        if ($add) {
            $updatedFields = array_keys($old);
        } else {
            $updatedFields = array_diff($updatedFields, array_keys($supportModel->getFieldsRewrite()));
        }

        // if a field that need link regeneration is modified, add the field that need to be regenerated
        if ($supportItemRewrite !== false) {
            $updatedFields[] = array_keys($supportItemRewrite)[0];
        }

        if (!$automaticTranslationConfiguration->get('translate_all')) {
            $fields = \Dingedi\PsTranslationsApi\DgTranslationTools::automaticTranslationGetFields($supportModel->getTableName(false));

            if ($fields === false) {
                return true;
            }

            if (is_array($fields)) {
                $updatedFields = array_intersect($updatedFields, $fields);
            }
        }

        if (count($updatedFields) === 0) {
            return true;
        }

        foreach (explode(',', $automaticTranslationConfiguration->get('ids_langs_to')) as $idLang) {
            try {
                $_POST['translation_data'] = array(
                    'automatic_progress' => true,
                    'selected_fields' => array_unique($updatedFields),
                    'plage_enabled' => 'true',
                    'start_id' => $object->id,
                    'end_id' => $object->id
                );

                $object->update();

                Configuration::set('dingedi_translation_filter', 2);
                $this->translateContentTable($supportModel->getTableName(false), $automaticTranslationConfiguration->get('id_lang_from'), (int)$idLang, 0, true, 1);

                $translated = $supportModel->findOneByPrimaryKey($object->id, array('id_lang' => $idLang))[0];

                foreach ($updatedFields as $field) {
                    if (property_exists($object, $field) && isset($translated[$field])) {
                        if (!is_array($object->{$field})) {
                            $object->{$field} = [];
                        }

                        $object->{$field}[(int)$idLang] = $translated[$field];
                    }
                }

                $object->update();
            } catch (Exception $e) {
            }
        }
    }

    public function hookActionObjectAddAfter($params)
    {
        if (\Dingedi\PsTranslationsApi\DgTranslationTools::automaticTranslationForAddition() === false) {
            return true;
        }

        return $this->_hookAutomaticTranslation($params, true);
    }

    public function hookActionObjectUpdateBefore($params)
    {
        if (\Dingedi\PsTranslationsApi\DgTranslationTools::automaticTranslationForUpdate() === false) {
            return true;
        }

        return $this->_hookAutomaticTranslation($params);
    }

    /**
     * @param array<\Dingedi\TablesTranslation\DgTableTranslatable16> $tables
     * @throws PrestaShopDatabaseException
     * @return mixed[]
     */
    private function filterExistingTables($tables)
    {
        return array_values(array_filter($tables, function ($i) {
            return $i->isExist() === true;
        }));
    }

    private function checkAndGetParams(array $required)
    {
        $data = Tools::getValue('translation_data');

        try {
            if (\Dingedi\PsTools\DgTools::hasParameters($data, $required)) {
                return $data;
            }
        } catch (\Dingedi\PsTools\Exception\MissingParametersException $e) {
            $this->jsonError($e->getMessage());
        }
    }

    /**
     * @return string
     */
    private function getCronCliCommand()
    {
        $php = '';
        if (defined('PHP_BINDIR') && is_string(PHP_BINDIR)) {
            $php = PHP_BINDIR . '/';
        }
        $php .= "php";

        if (\Tools::version_compare(_PS_VERSION_, '1.7.6.2', '>')) {
            $consolePath = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'bin/console';
            $command = $php . ' ' . $consolePath . ' ' . $this->name . ':translate';
        } else {
            $consolePath = _PS_MODULE_DIR_ . $this->name . '/cron.php';
            $command = $php . ' ' . $consolePath;
        }

        $command .= ' --from_lang=FROM_LANG --dest_lang=DEST_LANG --tables="TABLES" --overwrite=OVERWRITE --range=RANGE';

        return $command;
    }

    /**
     * @return void
     * @throws JsonException
     * @param mixed[]|string $data
     */
    private function jsonResponse($data)
    {
        \Dingedi\PsTools\DgTools::jsonResponse($data);
    }

    /**
     * @return void
     * @throws JsonException
     * @param mixed[]|string $data
     */
    private function jsonError($data)
    {
        \Dingedi\PsTools\DgTools::jsonError($data);
    }

    private function loadAssets()
    {
        if (\Dingedi\PsTools\DgShopInfos::isPrestaShop16()) {
            $this->context->controller->css_files['https://fonts.googleapis.com/icon?family=Material+Icons#'] = 'all';
        }

        $this->context->controller->css_files[$this->_path . 'views/css/prestashop-ui-kit.css?v=' . $this->version] = 'all';
        $this->context->controller->js_files[] = $this->_path . 'views/js/dg.runtime.js?v=' . $this->version;
        $this->context->controller->js_files[] = $this->_path . 'views/js/dg.vendors.js?v=' . $this->version;
    }

    public function hookDisplayDashboardToolbarTopMenu()
    {
        if (!\Module::isInstalled('dgtranslationall') || !\Module::isEnabled('dgtranslationall')) {
            return $this->display(__FILE__, 'views/templates/admin/hook/toolbar.tpl');
        }
    }

//ENDdgcontenttranslation











    //STARTmodules

    private function initModules($loadTableOnly = false)
    {
        if ($loadTableOnly === false) {
            $this->dgModuleConfig = array(
                'add_language_link' => $this->context->link->getAdminLink('AdminLocalization'),
                'link_admin_db_backup' => \Dingedi\PsTools\DgTools::getAdminLink('AdminBackup'),
                'link_admin_update_url_settings' => \Dingedi\PsTools\DgTools::getAdminLink('AdminMeta'),
                'default_lang' => \Dingedi\PsTranslationsApi\DgTranslationTools::getDefaultLangId(),
                'module_name' => $this->name,
                'module_version' => $this->version,
                'show_leave_review' => \Dingedi\PsTranslationsApi\DgLeaveReview::canReview(),
                'has_review' => \Dingedi\PsTranslationsApi\DgLeaveReview::hasReview(),
                'free_chars' => (new \Dingedi\PsTranslationsApi\Configuration\DgFreeCharsConfiguration())->get('remaining')
            );
        }

        require_once _PS_MODULE_DIR_ . 'dgtranslationall/classes/Modules/autoload.php';

        ModulesTablesAdapterList::register();
    }

    public function ajaxProcessModulesGetWidgetData()
    {
        $this->jsonResponse(array(
            'moduleConfig' => array_merge(
                $this->dgModuleConfig,
                array('languages' => $this->getLanguages()),
                array('translations' => $this->getModulesModuleTranslations()),
                array('translationsProviders' => \Dingedi\PsTranslationsApi\DgTranslationTools::getTranslationsConfiguration()),
                array('shopConfig' => \Dingedi\PsTranslationsApi\DgTranslationTools::getShopConfig())
            ),
        ));
    }

    public function ajaxProcessModulesTranslateWidget()
    {
        $data = $this->checkAndGetParams(array('id_lang_from', 'id_lang_to', 'text', 'latin'));

        $from = (int)$data['id_lang_from'];
        $to = (int)$data['id_lang_to'];
        $text = (string)$data['text'];
        $latin = (int)$data['latin'];

        try {
            if (trim($text) !== "") {
                if (strncmp($text, "|DGTAGSTOKENS|", strlen("|DGTAGSTOKENS|")) === 0) {
                    $splitted = explode('|DGTAGSTOKENS|', $text)[1];
                    $splitted = explode(',', $splitted);
                    $translated = array();

                    foreach ($splitted as $str) {
                        $translated[] = \Dingedi\PsTranslationsApi\DgTranslateApi::translate(
                            $str,
                            \Dingedi\PsTools\DgTools::getLocale((int)$from),
                            \Dingedi\PsTools\DgTools::getLocale((int)$to),
                            $latin
                        );
                    }

                    $text = implode(', ', $translated);
                } else {
                    $text = \Dingedi\PsTranslationsApi\DgTranslateApi::translate(
                        $text,
                        \Dingedi\PsTools\DgTools::getLocale((int)$from),
                        \Dingedi\PsTools\DgTools::getLocale((int)$to),
                        $latin
                    );
                }
            }
        } catch (Exception $e) {
            $this->jsonError($e->getMessage());
        }

        $this->jsonResponse(array(
            'success' => 1,
            'message' => $this->l('The data has been translated. Remember to save the changes.'),
            'text' => $text
        ));
    }

    public function ajaxProcessModulesGetMissingTranslations()
    {
        $this->getFileLangItemsMissingTranslation('modules');
    }

    private function getFileLangItemsMissingTranslation($type)
    {
        $data = $this->checkAndGetParams(array('name', 'id_lang_to'));

        $obj = $type === 'modules' ? DgModulesList::getObject((string)$data['name'], (int)$data['id_lang_to']) : DgThemesList::getObject((string)$data['name'], (int)$data['id_lang_to']);

        $this->jsonResponse(
            $obj->jsonSerialize()
        );
    }

    private function fileLangItemsTranslate($type)
    {
        $data = $this->checkAndGetParams(array('name', 'id_lang_from', 'id_lang_to', 'latin', 'translations'));

        try {
            $obj = $type === 'modules' ? DgModulesList::getObject((string)$data['name'], (int)$data['id_lang_to']) : DgThemesList::getObject((string)$data['name'], (int)$data['id_lang_to']);

            $translations = array();

            foreach ($data['translations'] as $translation) {
                foreach ($translation as $k => $v) {
                    $translations[$k] = $v;
                }
            }

            $obj->translateMissingTranslations($translations, (int)$data['id_lang_from'], (int)$data['latin']);
        } catch (Exception $exception) {
            $responseData = array(
                'message' => $exception->getMessage(),
            );

            if (isset($data['retry']) && (int)$data['retry'] === 2) {
                try {
                    $failedTranslation = \Dingedi\PsTranslationsApi\models\FailedTranslation::addNew(
                        $exception,
                        $type . '-' . $data['name']
                    );

                    $responseData['failed_translation'] = $failedTranslation;
                } catch (\PrestaShopException $e) {
                }
            }

            $this->jsonError($responseData);
        }

        $this->jsonResponse(array(
            'success' => 1,
            'message' => $this->l('The selected modules have been translated.')
        ));
    }

    public function ajaxProcessThemesTranslate()
    {
        $this->fileLangItemsTranslate('themes');
    }

    public function ajaxProcessModulesTranslate()
    {
        $this->fileLangItemsTranslate('modules');
    }

    public function ajaxProcessModulesGetData()
    {
        $this->jsonResponse(array(
            'moduleConfig' => array_merge(
                $this->dgModuleConfig,
                array('languages' => $this->getLanguages()),
                array('translations' => $this->getModulesModuleTranslations()),
                array('translationsProviders' => \Dingedi\PsTranslationsApi\DgTranslationTools::getTranslationsConfiguration()),
                array('shopConfig' => \Dingedi\PsTranslationsApi\DgTranslationTools::getShopConfig())
            ),
            'modulesFiles' => $this->getModulesTranslatableFilesList(),
            'modulesTables' => $this->getModulesTranslatableTablesList(),
            'failed_translation' => array_merge(
                \Dingedi\PsTranslationsApi\models\FailedTranslation::getAll('tables'),
                \Dingedi\PsTranslationsApi\models\FailedTranslation::getAll('modules')
            )
        ));
    }

    private function getModulesModuleTranslations()
    {
        return array_merge(
            $this->getContentModuleTranslations(),
            array(
                'form' => array(
                    'modules_filter' => array(
                        'search' => $this->l('Search'),
                        'others' => $this->l('Others'),
                    ),
                    'button' => array(
                        'translate' => $this->l('Translate'),
                        'stop' => $this->l('Stop'),
                    ),
                    'languages' => array(
                        'locked_from' => array(
                            'default' => $this->l('English by default'),
                            'change' => $this->l('Change default language'),
                            'reset' => $this->l('Reset'),
                            'warning' => $this->l('warning'),
                            'help' => $this->l('The source language of PrestaShop modules by default is English. Only change this setting if you are sure what you are doing')
                        ),
                        'advanced_settings' => $this->l('Advanced parameters'),
                        'source' => $this->l('Language source'),
                        'from_help' => $this->l('The source language is the language you want to translate from'),
                        'to' => $this->l('Languages to translate'),
                        'to_help' => $this->l('The languages to translate are the languages you want to translate, from the source language selected previously'),
                        'latin_title' => $this->l('Latin option (for supported languages)'),
                        'latin_input' => $this->l('My source text is in Latin characters'),
                        'latin_output' => $this->l('I want to translate into Latin characters'),
                        'overwrite' => $this->l('Overwrite all translations'),
                    ),
                ),
                'modules' => array(
                    'alerts' => array(
                        'already_translated' => $this->l('The selected modules are already fully translated into this language.'),
                    ),
                ),
                'table' => array(
                    'head' => array(
                        'module' => $this->l('Module'),
                        'modules' => $this->l('Modules'),
                        'prestashop' => 'PrestaShop',
                        'addons' => 'Addons',
                        'action' => $this->l('Action'),
                        'search' => $this->l('Search'),
                    ),
                    'options' => array(
                        'modules_files' => $this->l('Modules Databases (Front Office)'),
                        'modules_databases' => $this->l('Interface translation (Back Office)'),
                    )
                ),
                'tables' => array(
                    'load_by_click_message' => $this->l('This table contains a lot of data. To load the display of the translation percentages, please click manually on the button.'),
                    'load_by_click' => $this->l('Load by clicking'),
                    'error_loading' => $this->l('Error when retrieving translation percentages.'),
                ),
                'groups' => array(
                    'certified' => $this->l('Belongs to PrestaShop, does not come from an external module.'),
                    'server_error' => $this->l('Server error'),
                    'error' => $this->l('Error'),
                ),
                'api_keys' => array(
                    'microsoftProvider' => array(
                        array('label' => $this->l('Global'), 'value' => 'api'),
                        array('label' => $this->l('North America'), 'value' => 'api-nam'),
                        array('label' => $this->l('Europe'), 'value' => 'api-eur'),
                        array('label' => $this->l('Asia Pacific'), 'value' => 'api-apc'),
                    ),
                ),
                'Setup wizard' => $this->l('Setup wizard'),
                'Next' => $this->l('Next'),
                'Previous' => $this->l('Previous'),
                'Message from %s' => $this->l('Message from %s'),
                'Free monthly quota of %s characters' => $this->l('Free monthly quota of %s characters'),
                'Pricing options' => $this->l('Pricing options'),
                'Obtain an API key' => $this->l('Obtain an API key'),
                'offers a trial offer: %s credit for %s months' => $this->l('offers a trial offer: %s credit for %s months'),
                'Finish' => $this->l('Finish'),
                'Message from' => $this->l('Message from'),
                'You must have a Google Cloud account before starting.' => $this->l('You must have a Google Cloud account before starting.'),
                'Click here' => $this->l('Click here'),
                'to create one.' => $this->l('to create one.'),
                'Go to the Google Cloud dashboard' => $this->l('Go to the Google Cloud dashboard'),
                'Click on "Select a project"' => $this->l('Click on "Select a project"'),
                'Click on "New project"' => $this->l('Click on "New project"'),
                'Create a project' => $this->l('Create a project'),
                'Select the project' => $this->l('Select the project'),
                'Click on "APIs & Services"' => $this->l('Click on "APIs & Services"'),
                'Click on "Enable APIs and Services"' => $this->l('Click on "Enable APIs and Services"'),
                'Search and select "Cloud Translation API"' => $this->l('Search and select "Cloud Translation API"'),
                'Enable "Cloud Translation API' => $this->l('Enable "Cloud Translation API'),
                'You can skip these two steps if you already have a billing account in your Google Cloud account.' => $this->l('You can skip these two steps if you already have a billing account in your Google Cloud account.'),
                'Create an API key' => $this->l('Create an API key'),
                'Copy and enter it in the module' => $this->l('Copy and enter it in the module'),
                'Click to view in full screen' => $this->l('Click to view in full screen'),
                'You must have a Microsoft Azure account before starting.' => $this->l('You must have a Microsoft Azure account before starting.'),
                'Go to the Microsoft Azure dashboard' => $this->l('Go to the Microsoft Azure dashboard'),
                'Click on "Create a ressource"' => $this->l('Click on "Create a ressource"'),
                'Search "Translator text"' => $this->l('Search "Translator text"'),
                'Click on "Create"' => $this->l('Click on "Create"'),
                'Fill in the required fields and click on "Review + create"' => $this->l('Fill in the required fields and click on "Review + create"'),
                'Go to ressource' => $this->l('Go to ressource'),
                'Click on "Keys and Endpoint"' => $this->l('Click on "Keys and Endpoint"'),
                'Copy KEY 1 enter it in the module' => $this->l('Copy KEY 1 enter it in the module'),
                'Configure API keys' => $this->l('Configure API keys'),
                'Apply for all shops' => $this->l('Apply for all shops'),
                'API Key' => $this->l('API Key'),
                'Save' => $this->l('Save'),
                'Server' => $this->l('Server'),
                'Location' => $this->l('Location'),
                'Show' => $this->l('Show'),
                'Hide' => $this->l('Hide'),
                'Plan' => $this->l('Plan'),
                'offers a free offer with a quota of %s characters per month' => $this->l('offers a free offer with a quota of %s characters per month'),
                'Add a language' => $this->l('Add a language'),
                'Translation of modules' => $this->l('Translation of modules'),
                'Module database translation' => $this->l('Module database translation'),
                'Tools' => $this->l('Tools'),
                'Settings' => $this->l('Settings'),
                'Excluded words' => $this->l('Excluded words'),
                'Exclude words from translation' => $this->l('Exclude words from translation'),
                'The words you add will not be translated. For example, you can add brand names.' => $this->l('The words you add will not be translated. For example, you can add brand names.'),
                'API Keys' => $this->l('API Keys'),
                'Performance' => $this->l('Performance'),
                'Exclude all brands' => $this->l('Exclude all brands'),
                'Add a word to exclude from the translation' => $this->l('Add a word to exclude from the translation'),
                'Elements per query' => $this->l('Elements per query'),
                'Do not forget to make a backup of your database before starting the translation!' => $this->l('Do not forget to make a backup of your database before starting the translation!'),
                'If the translation of certain words do not fit, you can define your own translations here.' => $this->l('If the translation of certain words do not fit, you can define your own translations here.'),
                'Add a word' => $this->l('Add a word'),
                'Smart dictionary' => $this->l('Smart dictionary'),
                'new' => $this->l('new'),
                'Add' => $this->l('Add'),
                'Disabled' => $this->l('Disabled'),
                'Enabled' => $this->l('Enabled'),
                'Add the word whose translation you want to change first, then add the translations you want.' => $this->l('Add the word whose translation you want to change first, then add the translations you want.'),
                'Service to use' => $this->l('Service to use'),
                'Video tutorial' => $this->l('Video tutorial'),
                'Configuration' => $this->l('Configuration'),
                'Supported languages' => $this->l('Supported languages'),
                'List of ISO codes accepted for translation' => $this->l('List of ISO codes accepted for translation'),
                'PrestaShop Addons order ID' => $this->l('PrestaShop Addons order ID'),
                'We offer a free and unlimited translation service. To be able to use this service, please configure your PrestaShop Addons order ID.' => $this->l('We offer a free and unlimited translation service. To be able to use this service, please configure your PrestaShop Addons order ID.'),
                'Leave a review on our module and get better translation speed for free.' => $this->l('Leave a review on our module and get better translation speed for free.'),
                'Leave a review' => $this->l('Leave a review'),
                'Accented URLs are disabled, urls will not be translated for languages using non-Latin characters.' => $this->l('Accented URLs are disabled, urls will not be translated for languages using non-Latin characters.'),
                'Some installed languages use non-Latin characters:' => $this->l('Some installed languages use non-Latin characters:'),
                'Change settings' => $this->l('Change settings'),
                'Select all' => $this->l('Select all'),
                'items' => $this->l('items'),
                'Fields to translate' => $this->l('Fields to translate'),
                'Translation speed' => $this->l('Translation speed'),
                'The value corresponds to the number of items (e.g. products) translated in each query.' => $this->l('The value corresponds to the number of items (e.g. products) translated in each query.'),
                'Very low' => $this->l('Very low'),
                'Low' => $this->l('Low'),
                'Normal' => $this->l('Normal'),
                'High' => $this->l('High'),
                'Custom' => $this->l('Custom'),
                'Not available with' => $this->l('Not available with'),
                'This word already exists for this language' => $this->l('This word already exists for this language'),
                'Update' => $this->l('Update'),
                'Estimated time remaining before the end of the translation.' => $this->l('Estimated time remaining before the end of the translation.'),
                'Order ID without the #' => $this->l('Order ID without the #'),
                'Advanced parameters' => $this->l('Advanced parameters'),
                'Overwrite all translations' => $this->l('Overwrite all translations'),
                'Latin option (for supported languages)' => $this->l('Latin option (for supported languages)'),
                'My source text is in Latin characters' => $this->l('My source text is in Latin characters'),
                'I want to translate into Latin characters' => $this->l('I want to translate into Latin characters'),
                'Languages to translate' => $this->l('Languages to translate'),
                'The languages to translate are the languages you want to translate, from the source language selected previously' => $this->l('The languages to translate are the languages you want to translate, from the source language selected previously'),
                'Friendly URLs are disabled, internal links in your content will not be translated.' => $this->l('Friendly URLs are disabled, internal links in your content will not be translated.'),
                'Source language' => $this->l('Source language'),
                'English by default' => $this->l('English by default'),
                'Change default language' => $this->l('Change default language'),
                'The source language is the language you want to translate from' => $this->l('The source language is the language you want to translate from'),
                'Server error' => $this->l('Server error'),
                'show details' => $this->l('show details'),
                'Help' => $this->l('Help'),
                'Here are several solutions to solve this error:' => $this->l('Here are several solutions to solve this error:'),
                'Reduce the translation speed in the module settings.' => $this->l('Reduce the translation speed in the module settings.'),
                'Increase the "max_execution_time" parameter in the php.ini configuration file of your server.' => $this->l('Increase the "max_execution_time" parameter in the php.ini configuration file of your server.'),
                'Contact your server manager to increase the "Timeout" setting of your Apache web server.' => $this->l('Contact your server manager to increase the "Timeout" setting of your Apache web server.'),
                'If the error is still present, please contact us.' => $this->l('If the error is still present, please contact us.'),
                'Close' => $this->l('Close'),
                'Translate fields' => $this->l('Translate fields'),
                'A translation button will appear next to the multilingual fields of the modules to allow you to translate them' => $this->l('A translation button will appear next to the multilingual fields of the modules to allow you to translate them'),
                'Always enable' => $this->l('Always enable'),
                'Also display on content pages (categories, attributes, pages, etc.)' => $this->l('Also display on content pages (categories, attributes, pages, etc.)'),
                'This element belongs to this module:' => $this->l('This element belongs to this module:'),
                'Reset' => $this->l('Reset'),
                'warning' => $this->l('warning'),
                'Modules' => $this->l('Modules'),
                'Show only installed modules' => $this->l('Show only installed modules'),
                'Show only enabled modules' => $this->l('Show only enabled modules'),
            )
        );
    }

    private function getModulesTranslatableTablesList()
    {
        $list = $this->filterExistingTables(\Dingedi\TablesTranslation\DgTablesList::getList());

        usort($list, function ($a, $b) {
            return $a->getTableName() > $b->getTableName();
        });

        return array(
            array(
                'group_name' => '',
                'icon' => '',
                'tables' => $list
            )
        );
    }

    private function getModulesTranslatableFilesList()
    {
        $list = DgModulesList::getList();

        usort($list, function ($a, $b) {
            return $a['name'] > $b['name'];
        });

        return $list;
    }

//ENDmodules

//STARTthemes-and-emails
    private function initThemesAndMails()
    {
        $this->dgModuleConfig = array(
            'add_language_link' => $this->context->link->getAdminLink('AdminLocalization'),
            'link_admin_db_backup' => \Dingedi\PsTools\DgTools::getAdminLink('AdminBackup'),
            'link_admin_update_url_settings' => \Dingedi\PsTools\DgTools::getAdminLink('AdminMeta'),
            'is_16' => \Dingedi\PsTools\DgShopInfos::isPrestaShop16(),
            'module_name' => $this->name,
            'module_version' => $this->version,
            'default_lang' => \Dingedi\PsTranslationsApi\DgTranslationTools::getDefaultLangId(),
            'show_leave_review' => \Dingedi\PsTranslationsApi\DgLeaveReview::canReview(),
            'has_review' => \Dingedi\PsTranslationsApi\DgLeaveReview::hasReview(),
            'free_chars' => (new \Dingedi\PsTranslationsApi\Configuration\DgFreeCharsConfiguration())->get('remaining')
        );

        require_once _PS_MODULE_DIR_ . 'dgtranslationall/classes/Mails/autoload.php';
        require_once _PS_MODULE_DIR_ . 'dgtranslationall/classes/Themes/autoload.php';
    }

    public function ajaxProcessMailsGetList()
    {
        $data = $this->checkAndGetParams(array('id_lang_from'));

        $this->jsonResponse(DgMailsList::getList((int)$data['id_lang_from']));
    }

    public function ajaxProcessThemesGetMissingTranslations()
    {
        $this->getFileLangItemsMissingTranslation('themes');
    }


    public function ajaxProcessMailsTranslate()
    {
        $data = $this->checkAndGetParams(array('id_lang_from', 'id_lang_to', 'path', 'overwrite', 'latin'));

        try {
            $dgMailTranslatable = DgMailsList::getObject((string)$data['path'], (int)$data['id_lang_from']);
            $dgMailTranslatable->translate((int)$data['id_lang_to'], ($data['overwrite'] === 'true'), (int)$data['latin']);
        } catch (Exception $e) {
            $this->jsonError($e->getMessage());
        }

        $this->jsonResponse(array(
            'success' => 1,
            'message' => $this->l('The selected mails have been translated.')
        ));
    }

    public function ajaxProcessThemesAndMailsGetData()
    {
        $this->jsonResponse(array(
            'moduleConfig' => array_merge(
                $this->dgModuleConfig,
                array('languages' => $this->getLanguages()),
                array('translations' => $this->getThemesModuleTranslations()),
                array('translationsProviders' => \Dingedi\PsTranslationsApi\DgTranslationTools::getTranslationsConfiguration()),
                array('shopConfig' => \Dingedi\PsTranslationsApi\DgTranslationTools::getShopConfig())
            ),
            'themes' => DgThemesList::getList(),
            'failed_translation' => \Dingedi\PsTranslationsApi\models\FailedTranslation::getAll('themes')
        ));
    }

    public function ajaxProcessThemesReloadLanguage()
    {
        $data = $this->checkAndGetParams(['id_lang']);
        $language = new \Language($data['id_lang']);

        if (class_exists('\PrestaShop\PrestaShop\Adapter\SymfonyContainer')) {
            $container = \PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance();
        } else {
            $container = Context::getContext()->controller->getContainer();
        }

        $languagePackImporter = $container->get('prestashop.adapter.language.pack.importer');
        $languagePackImporter->import($language->iso_code);

        $this->jsonResponse(['success' => true]);
    }

    private function getThemesModuleTranslations()
    {
        return array_merge(
            $this->getContentModuleTranslations(),
            array(
                'form' => array(
                    'button' => array(
                        'translate' => $this->l('Translate'),
                        'stop' => $this->l('Stop'),
                    ),
                    'languages' => array(
                        'locked_from' => array(
                            'default' => $this->l('English by default'),
                            'change' => $this->l('Change default language'),
                            'reset' => $this->l('Reset'),
                            'warning' => $this->l('warning'),
                            'help' => $this->l('The source language of PrestaShop themes by default is English. Only change this setting if you are sure what you are doing')
                        ),
                        'advanced_settings' => $this->l('Advanced parameters'),
                        'source' => $this->l('Language from'),
                        'from_help' => $this->l('The source language is the language you want to translate from'),
                        'to' => $this->l('Languages to translate'),
                        'to_help' => $this->l('The languages to translate are the languages you want to translate, from the source language selected previously'),
                        'latin_title' => $this->l('Latin option (for supported languages)'),
                        'latin_input' => $this->l('My source text is in Latin characters'),
                        'latin_output' => $this->l('I want to translate into Latin characters'),
                        'overwrite' => $this->l('Overwrite all translations'),
                    ),
                ),
                'mails' => array(
                    'errors' => array(
                        'server' => $this->l('Server error'),
                    ),
                    'untranslated' => $this->l('Untranslated'),
                    'theme' => $this->l('Theme'),
                    'core' => $this->l('core'),
                    'modules' => $this->l('modules'),
                    'theme_emails' => $this->l('Theme emails'),
                    'active' => $this->l('active'),
                    'core_emails' => $this->l('Core emails (PrestaShop)'),
                    'available' => $this->l('This email is available in'),
                    'availability' => $this->l('The emails you want to translate must be available in the source language.')
                ),
                'themes' => array(
                    'errors' => array(
                        'unstranslatable_error_ps' => $this->l('If after several translations, some translations remain untranslated, it may be an error related to PrestaShop and not to our module.'),
                        'get_error' => $this->l('Your server returned an error when retrieving translations.'),
                        'get_error_ps' => $this->l('An error has occurred. PrestaShop could not recover the translation files for the selected language. This error is related to PrestaShop.'),
                    ),
                    'alerts' => array(
                        'already_translated' => $this->l('The selected themes are already fully translated into this language.'),
                    ),
                ),
                'api_keys' => array(
                    'microsoftProvider' => array(
                        array('label' => $this->l('Global'), 'value' => 'api'),
                        array('label' => $this->l('North America'), 'value' => 'api-nam'),
                        array('label' => $this->l('Europe'), 'value' => 'api-eur'),
                        array('label' => $this->l('Asia Pacific'), 'value' => 'api-apc'),
                    ),
                ),
                'Previous' => $this->l('Previous'),
                'Message from %s' => $this->l('Message from %s'),
                'Free monthly quota of %s characters' => $this->l('Free monthly quota of %s characters'),
                'Pricing options' => $this->l('Pricing options'),
                'Obtain an API key' => $this->l('Obtain an API key'),
                'offers a trial offer: %s credit for %s months' => $this->l('offers a trial offer: %s credit for %s months'),
                'Finish' => $this->l('Finish'),
                'Message from' => $this->l('Message from'),
                'You must have a Google Cloud account before starting.' => $this->l('You must have a Google Cloud account before starting.'),
                'Click here' => $this->l('Click here'),
                'to create one.' => $this->l('to create one.'),
                'Go to the Google Cloud dashboard' => $this->l('Go to the Google Cloud dashboard'),
                'Click on "Select a project"' => $this->l('Click on "Select a project"'),
                'Click on "New project"' => $this->l('Click on "New project"'),
                'Create a project' => $this->l('Create a project'),
                'Select the project' => $this->l('Select the project'),
                'Click on "APIs & Services"' => $this->l('Click on "APIs & Services"'),
                'Click on "Enable APIs and Services"' => $this->l('Click on "Enable APIs and Services"'),
                'Search and select "Cloud Translation API"' => $this->l('Search and select "Cloud Translation API"'),
                'Enable "Cloud Translation API' => $this->l('Enable "Cloud Translation API'),
                'You can skip these two steps if you already have a billing account in your Google Cloud account.' => $this->l('You can skip these two steps if you already have a billing account in your Google Cloud account.'),
                'Create an API key' => $this->l('Create an API key'),
                'Copy and enter it in the module' => $this->l('Copy and enter it in the module'),
                'Click to view in full screen' => $this->l('Click to view in full screen'),
                'You must have a Microsoft Azure account before starting.' => $this->l('You must have a Microsoft Azure account before starting.'),
                'Go to the Microsoft Azure dashboard' => $this->l('Go to the Microsoft Azure dashboard'),
                'Click on "Create a ressource"' => $this->l('Click on "Create a ressource"'),
                'Search "Translator text"' => $this->l('Search "Translator text"'),
                'Click on "Create"' => $this->l('Click on "Create"'),
                'Fill in the required fields and click on "Review + create"' => $this->l('Fill in the required fields and click on "Review + create"'),
                'Go to ressource' => $this->l('Go to ressource'),
                'Click on "Keys and Endpoint"' => $this->l('Click on "Keys and Endpoint"'),
                'Copy KEY 1 enter it in the module' => $this->l('Copy KEY 1 enter it in the module'),
                'Configure API keys' => $this->l('Configure API keys'),
                'Apply for all shops' => $this->l('Apply for all shops'),
                'API Key' => $this->l('API Key'),
                'Save' => $this->l('Save'),
                'Server' => $this->l('Server'),
                'Location' => $this->l('Location'),
                'Show' => $this->l('Show'),
                'Hide' => $this->l('Hide'),
                'Plan' => $this->l('Plan'),
                'offers a free offer with a quota of %s characters per month' => $this->l('offers a free offer with a quota of %s characters per month'),
                'Add a language' => $this->l('Add a language'),
                'Tools' => $this->l('Tools'),
                'Settings' => $this->l('Settings'),
                'Excluded words' => $this->l('Excluded words'),
                'Exclude words from translation' => $this->l('Exclude words from translation'),
                'The words you add will not be translated. For example, you can add brand names.' => $this->l('The words you add will not be translated. For example, you can add brand names.'),
                'Exclude all brands' => $this->l('Exclude all brands'),
                'Add a word to exclude from the translation' => $this->l('Add a word to exclude from the translation'),
                'API Keys' => $this->l('API Keys'),
                'Performance' => $this->l('Performance'),
                'Elements per query' => $this->l('Elements per query'),
                'Theme translation' => $this->l('Theme translation'),
                'Email translation' => $this->l('Email translation'),
                'Setup wizard' => $this->l('Setup wizard'),
                'Do not forget to make a backup of your database before starting the translation!' => $this->l('Do not forget to make a backup of your database before starting the translation!'),
                'new' => $this->l('new'),
                'Add' => $this->l('Add'),
                'Disabled' => $this->l('Disabled'),
                'Enabled' => $this->l('Enabled'),
                'Smart dictionary' => $this->l('Smart dictionary'),
                'If the translation of certain words do not fit, you can define your own translations here.' => $this->l('If the translation of certain words do not fit, you can define your own translations here.'),
                'Add a word' => $this->l('Add a word'),
                'Service to use' => $this->l('Service to use'),
                'Video tutorial' => $this->l('Video tutorial'),
                'Configuration' => $this->l('Configuration'),
                'Supported languages' => $this->l('Supported languages'),
                'List of ISO codes accepted for translation' => $this->l('List of ISO codes accepted for translation'),
                'PrestaShop Addons order ID' => $this->l('PrestaShop Addons order ID'),
                'We offer a free and unlimited translation service. To be able to use this service, please configure your PrestaShop Addons order ID.' => $this->l('We offer a free and unlimited translation service. To be able to use this service, please configure your PrestaShop Addons order ID.'),
                'Leave a review on our module and get better translation speed for free.' => $this->l('Leave a review on our module and get better translation speed for free.'),
                'Leave a review' => $this->l('Leave a review'),
                'Accented URLs are disabled, urls will not be translated for languages using non-Latin characters.' => $this->l('Accented URLs are disabled, urls will not be translated for languages using non-Latin characters.'),
                'Some installed languages use non-Latin characters:' => $this->l('Some installed languages use non-Latin characters:'),
                'Change settings' => $this->l('Change settings'),
                'Translation speed' => $this->l('Translation speed'),
                'The value corresponds to the number of items (e.g. products) translated in each query.' => $this->l('The value corresponds to the number of items (e.g. products) translated in each query.'),
                'Very low' => $this->l('Very low'),
                'Low' => $this->l('Low'),
                'Normal' => $this->l('Normal'),
                'High' => $this->l('High'),
                'Custom' => $this->l('Custom'),
                'Not available with' => $this->l('Not available with'),
                'This word already exists for this language' => $this->l('This word already exists for this language'),
                'Update' => $this->l('Update'),
                'Estimated time remaining before the end of the translation.' => $this->l('Estimated time remaining before the end of the translation.'),
                'Order ID without the #' => $this->l('Order ID without the #'),
                'Friendly URLs are disabled, internal links in your content will not be translated.' => $this->l('Friendly URLs are disabled, internal links in your content will not be translated.'),
                'Source language' => $this->l('Source language'),
                'English by default' => $this->l('English by default'),
                'Change default language' => $this->l('Change default language'),
                'The source language is the language you want to translate from' => $this->l('The source language is the language you want to translate from'),
                'Languages to translate' => $this->l('Languages to translate'),
                'The languages to translate are the languages you want to translate, from the source language selected previously' => $this->l('The languages to translate are the languages you want to translate, from the source language selected previously'),
            )
        );
    }

    public function ajaxProcessLangFileClearCache()
    {
        if (\Dingedi\PsTools\DgShopInfos::isPrestaShop16()) {
            \Tools::clearSmartyCache();
        } else {
            \Tools::clearAllCache();
        }
    }

//ENDthemes-and-emails

    private function getLanguages()
    {
        return array_map(function ($language) {
            return [
                'value' => $language['id_lang'],
                'label' => $language['name'],
                'iso_code' => $language['iso_code'],
                'locale' => isset($language['locale']) ? $language['locale'] : null,
                'active' => $language['active']
            ];
        }, \Language::getLanguages(false));
    }

    public function displayTranslateTableModal($configure, $controller)
    {
        if (!\Dingedi\PsTranslationsApi\DgTranslationTools::getTranslationModalEnabled()
            || $configure == 'dgcreativeelementstranslation' || $configure == 'dgcontenttranslation'
        ) {
            return false;
        }

        $controllerAdapterModal = \Dingedi\TablesTranslation\TablesAdaptersStore::getInstance()->supportController($controller, false);
        $tableAdapterModal = \Dingedi\TablesTranslation\TablesAdaptersStore::getInstance()->supportController($controller);

        if (
            $tableAdapterModal instanceof \Dingedi\TablesTranslation\AbstractTableAdapter
        ) {
            $id = (int)$tableAdapterModal->getObjectIdInRequest();

            if ($id) {
                $this->context->controller->js_files[] = $this->_path . 'views/js/dg.translate-modal.js?v=' . $this->version;

                return array(
                    'dgTranslateModal' => array(
                        'type' => 'table',
                        'tableLabel' => $tableAdapterModal->getLabel(),
                        'tableName' => $tableAdapterModal->table,
                        'id' => $id,
                        'controller' => $tableAdapterModal->controller ? $tableAdapterModal->controller : null,
                        'module' => $tableAdapterModal->module ? $tableAdapterModal->module : null
                    )
                );
            }
        } else if ($controllerAdapterModal instanceof \Dingedi\TablesTranslation\AbstractTableAdapter) {
            $this->context->controller->js_files[] = $this->_path . 'views/js/dg.translate-modal.js?v=' . $this->version;

            return array(
                'dgTranslateModal' => array(
                    'type' => 'table',
                    'tableLabel' => $controllerAdapterModal->getLabel(),
                    'tableName' => $controllerAdapterModal->table,
                    'controller' => $controllerAdapterModal->controller ? $controllerAdapterModal->controller : null,
                    'module' => $controllerAdapterModal->module ? $controllerAdapterModal->module : null
                )
            );
        }

        return false;
    }

    public function displayTranslateFileLangItemsModal($configure, $controller)
    {
        $type = Tools::getValue('type');

        if (
            ($controller === 'AdminTranslations' && in_array($type, ['themes', 'modules']))
            || Tools::getIsset('configure')
        ) {
            if ($type === false) {
                $type = 'modules';
            }

            $this->context->controller->js_files[] = $this->_path . 'views/js/dg.translate-filelangitems.js?v=' . $this->version;

            $item = ($type === 'themes') ? Tools::getValue('selected') : Tools::getValue('selected', Tools::getValue('module', Tools::getValue('configure')));

            if ($type === 'themes' && $item === '0') {
                $item = 'classic';
            }


            $id_lang = false;

            if (Tools::getIsset('locale')) {
                $id_lang = \Language::getIdByLocale(Tools::getValue('locale'));
            } else if (Tools::getIsset('lang')) {
                $id_lang = \Language::getIdByIso(Tools::getValue('lang'));
            }

            return array(
                'dgTranslateFileLangItemsModal' => array(
                    'type' => ucfirst($type),
                    'item' => $item,
                    'id_lang' => $id_lang
                )
            );
        }

        return false;
    }


    public function hookDisplayBackOfficeHeader()
    {
        if(!\Module::isEnabled($this->name)) {
            return;
        }

        $configure = Tools::getValue('configure');
        $controller = Tools::getValue('controller');

        if (in_array($configure, array('dgcontenttranslation', 'dgcreativeelementstranslation'))) {
            return;
        }

        $this->initContent(true);
        $this->initModules(true);

        $js_vars = array();

        $translateTableModalJsVars = $this->displayTranslateTableModal($configure, $controller);
        $translateFileLangsItemJsVars = false;

        if ($translateTableModalJsVars !== false) {
            $js_vars = array_merge($js_vars, $translateTableModalJsVars);
        } else {
            $translateFileLangsItemJsVars = $this->displayTranslateFileLangItemsModal($configure, $controller);
            if ($translateFileLangsItemJsVars !== false) {
                $js_vars = array_merge($js_vars, $translateFileLangsItemJsVars);
            }
        }

        if ($controller !== "") {
            if ((
                    \Dingedi\PsTranslationsApi\DgTranslationTools::getTranslationFieldsAlwaysEnabled() === 1
                    || (\Dingedi\PsTranslationsApi\DgTranslationTools::getTranslationFieldsEnabled() && $translateFileLangsItemJsVars !== false))
                && $configure !== $this->name) {
                $this->loadAssets();
                $js_vars['modules_widget'] = 1;
                $this->context->controller->js_files[] = $this->_path . 'views/js/dg.modules-widget.js?v=' . $this->version;
            }
        }


        if ($configure === $this->name || !empty($js_vars)) {
            $this->loadAssets();

            $type = Tools::getValue('dgtranslationallpage');

            if (in_array($type, array('content', 'modules', 'themes'))) {
                $this->context->controller->js_files[] = $this->_path . 'views/js/dg.' . $type . '-admin.js?v=' . $this->version;
            }

            $dg_base_url = $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name;
            $ps_base_uri = rtrim(__PS_BASE_URI__, '/');

            $js_vars = array_merge(array(
                'dg_base_url' => $dg_base_url,
                'ps_base_uri' => $ps_base_uri,
                'ps_id_shop' => \Context::getContext()->shop->id
            ), $js_vars);

            $js_vars['ps_faviconnotificationbo'] = 'undefined';

            if (\Dingedi\PsTools\DgShopInfos::isPrestaShop16()) {
                $this->context->smarty->assign($js_vars);

                return $this->display(__FILE__, 'views/templates/admin/hook/js_vars.tpl');
            } else {
                Media::addJsDef($js_vars);
            }
        }
    }
}
