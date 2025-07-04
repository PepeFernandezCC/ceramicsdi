<?php
/**
 * Google Merchant Center Pro - Controller to handle product tag associations
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
use Gmerchantcenterpro\Models\featureCategoryTag;
use Gmerchantcenterpro\ModuleLib\moduleTools;
/**
 * Controller to handle the association between products and tags
 */
class AdminGmcpTagProductController extends ModuleAdminController
{
    /** @var bool Enable bootstrap */
    public $bootstrap = true;
    protected $override_template = true;
    private $tagType;
    private $validTagTypes = ['gender', 'agegroup', 'adult', 'material', 'pattern', 'sizeType', 'sizeSystem', 'energy', 'energy_min', 'energy_max', 'shipping_label', 'unit_pricing_measure', 'base_unit_pricing_measure', 'excluded_destination', 'excluded_country', 'agegroup_product', 'gender_product', 'adult_product'];
    public function __construct()
    {
        parent::__construct();
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->multishop_context = Shop::CONTEXT_SHOP;
        }
    }
    public function initContent()
    {
        parent::initContent();
        $this->tagType = $this->validateTagType(Tools::getValue('tag'));
        $shopCategories = $this->getFormattedShopCategories();
        $availableLanguage = moduleTools::getAvailableLanguages((int) $this->context->shop->id);
        $countries = moduleTools::getLangCurrencyCountry($availableLanguage);
        $countryToHandle = array_map(function ($country) {
            return $country['countryIso'];
        }, $countries);
        $this->context->smarty->assign([
            'aShopCategories' => $shopCategories,
            'aFeatures' => Feature::getFeatures(GMerchantCenterPro::$iCurrentLang),
            'tagType' => $this->tagType,
            'useMaterial' => GMerchantCenterPro::$conf['GMCP_INC_MATER'],
            'usePattern' => GMerchantCenterPro::$conf['GMCP_INC_PATT'],
            'useGender' => GMerchantCenterPro::$conf['GMCP_INC_GEND'],
            'useAgegroup' => GMerchantCenterPro::$conf['GMCP_INC_AGE'],
            'useAdult' => GMerchantCenterPro::$conf['GMCP_INC_TAG_ADULT'],
            'useGenderProduct' => GMerchantCenterPro::$conf['GMCP_USE_GENDER_PRODUCT'],
            'useAgeGroupProduct' => GMerchantCenterPro::$conf['GMCP_USE_AGEGROUP_PRODUCT'],
            'useAdultProduct' => GMerchantCenterPro::$conf['GMCP_USE_ADULT_PRODUCT'],
            'useTag' => $this->tagType,
            'moduleUrl' => $this->getModuleUrl(),
            'sModuleName' => moduleConfiguration::GMCP_MODULE_SET_NAME,
            'currentTagHandle' => $this->tagType,
            'faqLink' => 'http://faq.businesstech.fr',
            'bSizeType' => GMerchantCenterPro::$conf['GMCP_SIZE_TYPE'],
            'bSizeSystem' => GMerchantCenterPro::$conf['GMCP_SIZE_SYSTEM'],
            'bEnergy' => GMerchantCenterPro::$conf['GMCP_INC_ENERGY'],
            'bShippingLabel' => GMerchantCenterPro::$conf['GMCP_INC_SHIPPING_LABEL'],
            'bUnitpricingMeasure' => GMerchantCenterPro::$conf['GMCP_INC_UNIT_PRICING'],
            'bUnitBasepricingMeasure' => GMerchantCenterPro::$conf['GMCP_INC_B_UNIT_PRICING'],
            'bExcludedDest' => GMerchantCenterPro::$conf['GMCP_EXCLUDED_DEST'],
            'bExcludedCountry' => GMerchantCenterPro::$conf['GMCP_EXCLUDED_COUNTRY'],
            'aCountries' => array_unique($countryToHandle),
        ]);
        $this->handleTemplateDisplay();
    }
    private function validateTagType($tagType)
    {
        if (empty($tagType) || !in_array($tagType, $this->validTagTypes, true)) {
            return 'gender'; 
        }
        return (string) $tagType;
    }
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $cssFiles[] = _MODULE_DIR_ . $this->module->name . '/views/css/bootstrap4.css';
        if (GMerchantCenterPro::$bCompare90) {
            $cssFiles[] = _MODULE_DIR_ . $this->module->name . '/views/css/admin-ps9.css';
        } else {
            $cssFiles[] = _MODULE_DIR_ . $this->module->name . '/views/css/admin.css';
        }
        $jsFiles = [
            _MODULE_DIR_ . $this->module->name . '/views/js/tag.js',
            _MODULE_DIR_ . $this->module->name . '/views/js/module.js',
            _MODULE_DIR_ . $this->module->name . '/views/js/feature_by_cat.js',
            _MODULE_DIR_ . $this->module->name . '/views/js/feedList.js',
            _MODULE_DIR_ . $this->module->name . '/views/js/custom_label.js',
        ];
        if (GMerchantCenterPro::$bCompare1770) {
            $this->addCSS($cssFiles);
            $this->addJS($jsFiles);
        } else {
            $this->context->controller->addCSS($cssFiles);
            $this->context->controller->addJS($jsFiles);
        }
    }
    public function postProcess()
    {
        if (!Tools::isSubmit('save_btn')) {
            return;
        }
        try {
            $tagMode = Tools::getValue('set_tag_mode');
            $tagType = Tools::getValue('tag');
            $this->updateTagConfiguration($tagMode, $tagType);
            $this->saveCategories();
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            return;
        }
        Tools::redirect(Context::getContext()->link->getAdminLink('AdminGmcpTagProduct') . '&tag=' . $tagType);
        $this->confirmations[] = $this->module->l('Settings updated');
    }
    public function getTemplatePath()
    {
        return GMerchantCenterPro::$bCompare80
            ? '@PrestaShop/Admin/tag.html.twig'
            : _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/tab/tag.tpl';
    }
    public function createTemplate($tpl_name = null)
    {
        if (!GMerchantCenterPro::$bCompare80) {
            return parent::createTemplate($tpl_name);
        }
        $tpl_name = $tpl_name ?: $this->template;
        $smarty = $this->context->smarty;
        $moduleName = $this->module->name;
        $templatePath = _PS_MODULE_DIR_ . "$moduleName/views/templates/admin/tab/tag.tpl";
        return $smarty->createTemplate($templatePath, $smarty);
    }
    private function getFormattedShopCategories()
    {
        $categories = moduleDao::getShopCategories(
            GMerchantCenterPro::$iShopId,
            GMerchantCenterPro::$iCurrentLang,
            GMerchantCenterPro::$conf['GMCP_HOME_CAT_ID']
        );
        foreach ($categories as &$category) {
            $features = featureCategoryTag::getFeaturesByCategory(
                $category['id_category'],
                GMerchantCenterPro::$iShopId
            );
            $category = array_merge($category, [
                'material' => '',
                'pattern' => '',
                'agegroup' => '',
                'gender' => '',
                'adult' => '',
                'sizeType' => '',
                'sizeSystem' => '',
                'energy' => '',
                'energy_min' => '',
                'energy_max' => '',
                'shipping_label' => '',
                'unit_pricing_measure' => '',
                'base_unit_pricing_measure' => '',
                'excluded_destination' => [],
                'excluded_country' => [],
                'agegroup_product' => [],
                'gender_product' => [],
                'adult_product' => [],
            ]);
            if (!empty($features)) {
                $features['excluded_destination'] = !empty($features['excluded_destination'])
                    ? explode(' ', $features['excluded_destination']) : [];
                $features['excluded_country'] = !empty($features['excluded_country'])
                    ? explode(' ', $features['excluded_country']) : [];
                $category = array_merge($category, $features);
            }
        }
        return $categories;
    }
    private function updateTagConfiguration($tagMode, $tagType)
    {
        $value = ($tagMode == 'product_data') ? 1 : 0;
        $configMap = [
            'gender' => 'GMCP_USE_GENDER_PRODUCT',
            'agegroup' => 'GMCP_USE_AGEGROUP_PRODUCT',
            'adult' => 'GMCP_USE_ADULT_PRODUCT',
        ];
        if (isset($configMap[$tagType])) {
            Configuration::updateValue($configMap[$tagType], $value);
        }
    }
    private function saveCategories()
    {
        $categories = [];
        foreach (moduleConfiguration::GMCP_TAG_LIST as $tagType) {
            $values = Tools::getValue($tagType);
            if (!empty($values) && is_array($values)) {
                foreach ($values as $catId => $val) {
                    $catId = (int) $catId;
                    if ($catId <= 0) {
                        continue;
                    }
                    $categories[$catId][$tagType] = htmlspecialchars(
                        strip_tags($val),
                        ENT_QUOTES | ENT_HTML5,
                        'UTF-8'
                    );
                }
            }
        }
        featureCategoryTag::cleanTable((int) $this->context->shop->id);
        if (empty($categories)) {
            return;
        }
        foreach ($categories as $categoryId => $value) {
            $featureCategory = new featureCategoryTag();
            $featureCategory->id_cat = (int) $categoryId;
            $featureCategory->values = moduleTools::handleSetConfigurationData($value);
            $featureCategory->id_shop = (int) $this->context->shop->id;
            $featureCategory->add();
        }
    }
    private function getModuleUrl()
    {
        $redirectTab = $this->tagType == 'adult' ? 'adult' : 'appreal';
        return Context::getContext()->link->getAdminLink('AdminModules')
            . '&configure=gmerchantcenterpro&tab=' . $redirectTab;
    }
    private function handleTemplateDisplay()
    {
        if (GMerchantCenterPro::$bCompare80) {
            $this->setTemplate($this->getTemplatePath());
            return;
        }
        $template = 'module:' . $this->module->name . '/views/templates/admin/tab/tag.tpl';
        $this->context->smarty->assign([
            'content' => $this->content . $this->module->fetch($template),
        ]);
    }
}
