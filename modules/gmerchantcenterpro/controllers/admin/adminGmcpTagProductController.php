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
if (!defined('_PS_VERSION_')) {
    exit;
}

use Gmerchantcenterpro\Configuration\moduleConfiguration;
use Gmerchantcenterpro\Dao\moduleDao;
use Gmerchantcenterpro\Models\featureCategoryTag;
use Gmerchantcenterpro\ModuleLib\moduleTools;

class AdminGmcpTagProductController extends ModuleAdminController
{
    /**
     * init content
     *
     * @since 1.5.0
     *
     * @return html
     */
    public function initContent()
    {
        parent::initContent();

        $shopCategories = moduleDao::getShopCategories(\Gmerchantcenterpro::$iShopId, \Gmerchantcenterpro::$iCurrentLang, \Gmerchantcenterpro::$conf['GMCP_HOME_CAT_ID'], \Gmerchantcenterpro::$conf['GMCP_HOME_CAT']);
        $countryToHandle = [];
        $availableLanguage = [];

        foreach ($shopCategories as &$category) {
            // get feature by category Id
            $aFeatures = featureCategoryTag::getFeaturesByCategory($category['id_category'], \Gmerchantcenterpro::$iShopId, moduleConfiguration::GMCP_TABLE_PREFIX);

            if (!empty($aFeatures)) {
                $category['material'] = $aFeatures['material'];
                $category['pattern'] = $aFeatures['pattern'];
                $category['agegroup'] = $aFeatures['agegroup'];
                $category['gender'] = $aFeatures['gender'];
                $category['adult'] = $aFeatures['adult'];
                $category['sizeType'] = $aFeatures['sizeType'];
                $category['sizeSystem'] = $aFeatures['sizeSystem'];
                $category['energy'] = $aFeatures['energy'];
                $category['energy_min'] = $aFeatures['energy_min'];
                $category['energy_max'] = $aFeatures['energy_max'];
                $category['shipping_label'] = $aFeatures['shipping_label'];
                $category['unit_pricing_measure'] = $aFeatures['unit_pricing_measure'];
                $category['base_unit_pricing_measure'] = $aFeatures['base_unit_pricing_measure'];
                $category['excluded_destination'] = !empty($aFeatures['excluded_destination']) ? explode(' ', $aFeatures['excluded_destination']) : [];
                $category['excluded_country'] = !empty($aFeatures['excluded_country']) ? explode(' ', $aFeatures['excluded_country']) : [];
                $category['agegroup_product'] = isset($aFeatures['agegroup_product']) ? $aFeatures['agegroup_product'] : [];
                $category['gender_product'] = isset($aFeatures['gender_product']) ? $aFeatures['gender_product'] : [];
                $category['adult_product'] = isset($aFeatures['adult_product']) ? $aFeatures['adult_product'] : [];
            } else {
                $category['material'] = [];
                $category['pattern'] = [];
                $category['agegroup'] = [];
                $category['gender'] = [];
                $category['adult'] = [];
                $category['adult'] = [];
                $category['sizeType'] = [];
                $category['sizeSystem'] = [];
                $category['energy'] = [];
                $category['energy_min'] = [];
                $category['energy_max'] = [];
                $category['shipping_label'] = [];
                $category['unit_pricing_measure'] = [];
                $category['base_unit_pricing_measure'] = [];
                $category['excluded_destination'] = [];
                $category['agegroup_product'] = [];
                $category['gender_product'] = [];
                $category['adult_product'] = [];
                $category['excluded_country'] = [];
            }
        }

        $tagType = \Tools::getValue('tag');
        $redirectTab = $tagType == 'adult' ? 'adult' : 'appreal';
        $availableLanguage = moduleTools::getAvailableLanguages(\GMerchantCenterPro::$iShopId);
        $countries = moduleTools::getLangCurrencyCountry($availableLanguage, moduleConfiguration::GMCP_AVAILABLE_COUNTRIES);

        foreach ($countries as $key => $country) {
            $countryToHandle[] = $country['countryIso'];
        }

        $this->context->smarty->assign([
            'aShopCategories' => $shopCategories,
            'aFeatures' => \Feature::getFeatures(\Gmerchantcenterpro::$iCurrentLang),
            'tagType' => $tagType,
            'useMaterial' => \Gmerchantcenterpro::$conf['GMCP_INC_MATER'],
            'usePattern' => \Gmerchantcenterpro::$conf['GMCP_INC_PATT'],
            'useGender' => \Gmerchantcenterpro::$conf['GMCP_INC_GEND'],
            'useAgegroup' => \Gmerchantcenterpro::$conf['GMCP_INC_AGE'],
            'useAdult' => \Gmerchantcenterpro::$conf['GMCP_INC_TAG_ADULT'],
            'bSizeType' => \GMerchantCenterPro::$conf['GMCP_SIZE_TYPE'],
            'bSizeSystem' => \GMerchantCenterPro::$conf['GMCP_SIZE_SYSTEM'],
            'bEnergy' => \GMerchantCenterPro::$conf['GMCP_INC_ENERGY'],
            'bShippingLabel' => \GMerchantCenterPro::$conf['GMCP_INC_SHIPPING_LABEL'],
            'bUnitpricingMeasure' => \GMerchantCenterPro::$conf['GMCP_INC_UNIT_PRICING'],
            'bUnitBasepricingMeasure' => \GMerchantCenterPro::$conf['GMCP_INC_B_UNIT_PRICING'],
            'bExcludedDest' => \GMerchantCenterPro::$conf['GMCP_EXCLUDED_DEST'],
            'bExcludedCountry' => \GMerchantCenterPro::$conf['GMCP_EXCLUDED_COUNTRY'],
            'useGenderProduct' => \Gmerchantcenterpro::$conf['GMCP_USE_GENDER_PRODUCT'],
            'useAgeGroupProduct' => \Gmerchantcenterpro::$conf['GMCP_USE_AGEGROUP_PRODUCT'],
            'useAdultProduct' => \Gmerchantcenterpro::$conf['GMCP_USE_ADULT_PRODUCT'],
            'useTag' => \Tools::getValue('tag'),
            'moduleUrl' => \Context::getContext()->link->getAdminLink('AdminModules') . '&configure=gmerchantcenterpro&tab=' . $redirectTab,
            'sModuleName' => moduleConfiguration::GMCP_MODULE_SET_NAME,
            'currentTagHandle' => \Tools::getValue('tag'),
            'aCountries' => array_unique($countryToHandle),
        ]);

        $this->context->smarty->assign([
            'content' => $this->content . $this->module->fetch('module:gmerchantcenterpro/views/templates/admin/tab/tag.tpl'),
        ]);
    }

    /**
     * manages to initialize controller's media
     *
     * @param bool $isNewTheme
     *
     * @return string
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();

        $this->addCss(_MODULE_DIR_ . $this->module->name . '/views/css/admin.css');
        $this->addCss(_MODULE_DIR_ . $this->module->name . '/views/css/bootstrap4.css');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/tag.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/module.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/feature_by_cat.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/feedList.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/custom_label.js');
    }

    /**
     * Post process
     *
     * @since 1.5.0
     *
     * @return void
     */
    public function postProcess()
    {
        if (Tools::isSubmit('save_btn')) {
            $isAdded = false;

            try {
                $tagMode = \Tools::getValue('set_tag_mode');
                $tagType = \Tools::getValue('tag');

                if ($tagMode == 'bulk') {
                    if ($tagType == 'gender') {
                        \Configuration::updateValue('GMCP_USE_GENDER_PRODUCT', 0);
                    }

                    if ($tagType == 'agegroup') {
                        \Configuration::updateValue('GMCP_USE_AGEGROUP_PRODUCT', 0);
                    }

                    if ($tagType == 'adult') {
                        \Configuration::updateValue('GMCP_USE_ADULT_PRODUCT', 0);
                    }
                } elseif ($tagMode == 'product_data') {
                    if ($tagType == 'gender') {
                        \Configuration::updateValue('GMCP_USE_GENDER_PRODUCT', 1);
                    }

                    if ($tagType == 'agegroup') {
                        \Configuration::updateValue('GMCP_USE_AGEGROUP_PRODUCT', 1);
                    }

                    if ($tagType == 'adult') {
                        \Configuration::updateValue('GMCP_USE_ADULT_PRODUCT', 1);
                    }
                }

                $categories = [];

                /* USE CASE - handle all tags configured */
                foreach (moduleConfiguration::GMCP_TAG_LIST as $sTagType) {
                    if (!empty(\Tools::getValue($sTagType)) && is_array(\Tools::getValue($sTagType))) {
                        foreach (\Tools::getValue($sTagType) as $iCatId => $mVal) {
                            $categories[$iCatId][$sTagType] = strip_tags($mVal);
                        }
                    }
                }
                // Clean
                featureCategoryTag::cleanTable(\Gmerchantcenterpro::$iShopId, moduleConfiguration::GMCP_TABLE_PREFIX);

                if (!empty($categories)) {
                    foreach ($categories as $id_category => $value) {
                        $feature_category = new FeatureCategoryTag();
                        $feature_category->id_cat = (int) $id_category;
                        $feature_category->values = moduleTools::handleSetConfigurationData($value);
                        $feature_category->id_shop = (int) \Gmerchantcenterpro::$iShopId;
                        if ($feature_category->add()) {
                            $isAdded = true;
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->errors[] = $e->getMessage();
            }

            if (!empty($isAdded)) {
                \Tools::redirect(\Context::getContext()->link->getAdminLink('AdminGmcpTagProduct') . '&tag=' . $tagType);
                $this->confirmations[] = $this->module->l('Settings updated');
            }
        }
    }
}
