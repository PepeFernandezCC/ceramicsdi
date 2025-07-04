<?php
/**
 * Google Merchant Center Pro - Controller to handle taxonomy management
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
use Gmerchantcenterpro\Configuration\moduleConfiguration;
use Gmerchantcenterpro\Dao\moduleDao;
use Gmerchantcenterpro\Models\categoryTaxonomy;
use Gmerchantcenterpro\Models\googleTaxonomy;
use Gmerchantcenterpro\ModuleLib\moduleTools;
/**
 * Controller to handle the taxonomies association
 */
class AdminGmcpTaxonomyController extends \ModuleAdminController
{
    /** @var bool Enable bootstrap */
    public $bootstrap = true;
    protected $override_template = true;
    private $isoLang;
    public function __construct()
    {
        parent::__construct();
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->multishop_context = \Shop::CONTEXT_SHOP;
        }
        $this->isoLang = \Tools::getValue('sLangIso');
    }
    public function initContent()
    {
        parent::initContent();
        if (empty($this->isoLang)) {
            $this->redirectToTaxonomiesTab();
            return;
        }
        $this->initTaxonomyContent();
        if (Tools::getValue('action') === 'autocomplete') {
            $this->processAutocomplete();
        }
        $this->handleTemplateDisplay();
    }
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();
        $moduleDir = _MODULE_DIR_ . $this->module->name;
        $cssFiles = [
            $moduleDir . '/views/css/bootstrap4.css'
        ];
        if (\GMerchantCenterPro::$bCompare90) {
            $cssFiles[] = $moduleDir . '/views/css/admin-ps9.css';
        } else {
            $cssFiles[] = $moduleDir . '/views/css/admin.css';
            $cssFiles[] = $moduleDir . '/views/css/taxonomie.css';
        }
        $jsFiles = [
            $moduleDir . '/views/js/module.js',
            $moduleDir . '/views/js/taxonomies.js'
        ];
        if (\GMerchantCenterPro::$bCompare1770) {
            foreach ($cssFiles as $css) {
                $this->context->controller->addCSS($css);
            }
            foreach ($jsFiles as $js) {
                $this->context->controller->addJS($js);
            }
        } else {
            $this->context->controller->addCSS($cssFiles);
            $this->context->controller->addJS($jsFiles);
        }
    }
    public function postProcess()
    {
        if (Tools::isSubmit('save_btn')) {
            $this->processSaveTaxonomies();
        }
        if (Tools::isSubmit('gmcTaxonomies') || Tools::isSubmit('fpaTaxonomies') || Tools::isSubmit('tkpTaxonomies')) {
            $this->processImportTaxonomies();
        }
        return true;
    }
    private function processSaveTaxonomies()
    {
        try {
            $isoExplode = explode('-', Tools::getValue('sLangIso'));
            $googleCategories = Tools::getValue('bt_google-cat');
            $id_lang = Language::getIdByIso($isoExplode[0]) ?: Configuration::get('PS_LANG_DEFAULT');
            if (categoryTaxonomy::deleteGoogleCategory((int) Context::getContext()->shop->id, $this->isoLang)) {
                foreach ($googleCategories as $idShopCategorie => $googleCategoryValue) {
                    if (!empty($googleCategoryValue)) {
                        categoryTaxonomy::insertGoogleCategory(
                            (int) Context::getContext()->shop->id,
                            $idShopCategorie,
                            $googleCategoryValue,
                            $this->isoLang
                        );
                    }
                }
            }
            $this->confirmations[] = $this->module->l('The mapping of your categories to the official Google categories has been successfully updated.');
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }
    private function processImportTaxonomies()
    {
        try {
            $moduleSource = $this->getImportSource();
            if (!empty($moduleSource)) {
                $dataToImport = moduleTools::getTaxonomiesToImport($this->isoLang);
                if (categoryTaxonomy::deleteGoogleCategory((int) Context::getContext()->shop->id, $this->isoLang)) {
                    $this->importTaxonomyData($dataToImport[$moduleSource]);
                }
                $this->confirmations[] = $this->module->l('The mapping of your categories to the official Google categories has been successfully imported.');
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }
    private function getImportSource()
    {
        if (!empty(Tools::isSubmit('gmcTaxonomies'))) {
            return 'gmcTaxonomies';
        }
        if (!empty(Tools::isSubmit('fpaTaxonomies'))) {
            return 'fpaTaxonomies';
        }
        if (!empty(Tools::isSubmit('tkpTaxonomies'))) {
            return 'tkpTaxonomies';
        }
        return '';
    }
    private function importTaxonomyData($data)
    {
        if (empty($data)) {
            return;
        }
        foreach ($data as $item) {
            if (empty($item['txt_taxonomy'])) {
                continue;
            }
            $taxonomyValue = is_string($item['txt_taxonomy']) && json_decode($item['txt_taxonomy'], true)
                ? str_replace('\"', '', json_decode($item['txt_taxonomy']))
                : $item['txt_taxonomy'];
            categoryTaxonomy::insertGoogleCategory(
                (int) Context::getContext()->shop->id,
                $item['id_category'],
                $taxonomyValue,
                $item['lang']
            );
        }
    }
    public function processAutocomplete()
    {
        $items = [];
        $query = Tools::getValue('query');
        $taxonomyFound = [];
        if (strlen($query) >= 4) {
            $words = explode(' ', $query);
            $items = googleTaxonomy::autocompleteSearch($this->isoLang, $words);
            if (!empty($items) && is_array($items)) {
                foreach ($items as $data) {
                    $taxonomyFound[] = $data['value'];
                }
            }
        }
        exit(json_encode($taxonomyFound));
    }
    private function initTaxonomyContent()
    {
        $isoExplode = explode('-', $this->isoLang);
        $id_lang = Language::getIdByIso($isoExplode[0]) ?: Configuration::get('PS_LANG_DEFAULT');
        Media::addJsDef([
            'btGmcp' => [
                'taxonomyController' => $this->context->link->getAdminLink('AdminGmcpTaxonomy') . '&iLangId=' . $id_lang . '&sLangIso=' . $this->isoLang,
            ],
        ]);
        $shopCategories = $this->getFormattedShopCategories($id_lang);
        $this->context->smarty->assign([
            'moduleUrl' => Context::getContext()->link->getAdminLink('AdminModules') . '&configure=gmerchantcenterpro&tab=taxonomies',
            'idLang' => $id_lang,
            'isoLang' => $this->isoLang,
            'currencyIso' => Language::getIsoById(GMerchantCenterPro::$iCurrentLang),
            'maxPostVar' => ini_get('max_input_vars'),
            'shopCategories' => $shopCategories,
            'shopCategoriesCount' => count($shopCategories),
            'faqLink' => 'http://faq.businesstech.fr',
            'taxonomiesToImport' => moduleTools::getTaxonomiesToImport($this->isoLang),
            'sModuleName' => moduleConfiguration::GMCP_MODULE_SET_NAME,
        ]);
    }
    private function getFormattedShopCategories($id_lang)
    {
        $shopCategories = moduleDao::getShopCategories(
            (int) Context::getContext()->shop->id,
            (int) $id_lang,
            GMerchantCenterPro::$conf['GMCP_HOME_CAT_ID']
        );
        foreach ($shopCategories as &$category) {
            $googleCat = categoryTaxonomy::getGoogleCategories(
                (int) Context::getContext()->shop->id,
                $category['id_category'],
                $this->isoLang
            );
            $category['google_category_name'] = !empty($googleCat['txt_taxonomy'])
                ? str_replace('\"', '', json_decode($googleCat['txt_taxonomy']))
                : '';
        }
        unset($category);
        return $shopCategories;
    }
    private function handleTemplateDisplay()
    {
        if (GMerchantCenterPro::$bCompare80) {
            $this->setTemplate($this->getTemplatePath());
            return;
        }
        $template = 'module:' . $this->module->name . '/views/templates/admin/tab/taxonomies.tpl';
        $this->context->smarty->assign([
            'content' => $this->content . $this->module->fetch($template),
        ]);
    }
    private function redirectToTaxonomiesTab()
    {
        Tools::redirect(
            Context::getContext()->link->getAdminLink('AdminModules') . '&configure=gmerchantcenterpro&tab=taxonomies'
        );
    }
    public function getTemplatePath()
    {
        return GMerchantCenterPro::$bCompare80
            ? '@Modules/gmerchantcenterpro/views/templates/admin/tab/taxonomies.tpl'
            : _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/tab/taxonomies.tpl';
    }
    public function createTemplate($tpl_name = null)
    {
        if (!\GMerchantCenterPro::$bCompare80) {
            return parent::createTemplate($tpl_name);
        }
        $tpl_name = $tpl_name ?: $this->template;
        $smarty = $this->context->smarty;
        $templatePath = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/tab/taxonomies.tpl';
        return $smarty->createTemplate($templatePath, $smarty);
    }
}
