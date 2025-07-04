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
use Gmerchantcenterpro\Models\categoryTaxonomy;
use Gmerchantcenterpro\Models\googleTaxonomy;
use Gmerchantcenterpro\ModuleLib\moduleTools;

class AdminGmcpTaxonomyController extends \ModuleAdminController
{
    /**
     * init content
     *
     * @since 1.9.0
     *
     * @return html
     */
    public function initContent()
    {
        parent::initContent();

        $isoLang = \Tools::getValue('sLangIso');

        if (!empty($isoLang)) {
            $isoExplode = explode('-', \Tools::getValue('sLangIso'));
            $id_lang = \Language::getIdByIso($isoExplode[0]);

            // Use case if the installed id_lang is deleted, we force use the current default lang of the shop
            if (empty($id_lang)) {
                $id_lang = \Configuration::get('PS_LANG_DEFAULT');
            }

            $jsDefs = [];
            $jsDefs['taxonomyController'] = $this->context->link->getAdminLink('AdminGmcpTaxonomy') . '&iLangId=' . $id_lang . '&sLangIso=' . $isoLang;
            \Media::addJsDef(['btGmcp' => $jsDefs]);

            $shopCategories = moduleDao::getShopCategories(\Gmerchantcenterpro::$iShopId, (int) $id_lang, \Gmerchantcenterpro::$conf['GMCP_HOME_CAT_ID'], \Gmerchantcenterpro::$conf['GMCP_HOME_CAT']);

            foreach ($shopCategories as &$category) {
                // get google taxonomy
                $aGoogleCat = categoryTaxonomy::getGoogleCategories(\Gmerchantcenterpro::$iShopId, $category['id_category'], $isoLang, moduleConfiguration::GMCP_TABLE_PREFIX);
                // assign the current taxonomy
                $category['google_category_name'] = is_array($aGoogleCat) && isset($aGoogleCat['txt_taxonomy']) ? $aGoogleCat['txt_taxonomy'] : '';
            }

            $this->context->smarty->assign([
                'moduleUrl' => \Context::getContext()->link->getAdminLink('AdminModules') . '&configure=gmerchantcenterpro&tab=taxonomies',
                'idLang' => $id_lang,
                'isoLang' => $isoLang,
                'currencyIso' => \Language::getIsoById(\Gmerchantcenterpro::$iCurrentLang),
                'maxPostVar' => ini_get('max_input_vars'),
                'shopCategories' => $shopCategories,
                'shopCategoriesCount' => count($shopCategories),
                'faqLink' => 'http://faq.businesstech.fr',
                'taxonomiesToImport' => moduleTools::getTaxonomiesToImport($isoLang),
                'sModuleName' => moduleConfiguration::GMCP_MODULE_SET_NAME,
            ]);

            // execute the ajax call
            if (\Tools::getValue('action') == 'autocomplete') {
                $this->processAutocomplete();
            }

            $this->context->smarty->assign([
                'content' => $this->content . $this->module->fetch('module:gmerchantcenterpro/views/templates/admin/tab/taxonomies.tpl'),
            ]);
        } else {
            \Tools::redirect(\Context::getContext()->link->getAdminLink('AdminModules') . '&configure=gmerchantcenterpro&tab=taxonomies');
        }
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
        $this->addCss(_MODULE_DIR_ . $this->module->name . '/views/css/taxonomie.css');
        $this->addCss(_MODULE_DIR_ . $this->module->name . '/views/css/bootstrap4.css');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/module.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/taxonomies.js');
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
        $isoLang = \Tools::getValue('sLangIso');

        if (Tools::isSubmit('save_btn')) {
            try {
                $isoExplode = explode('-', \Tools::getValue('sLangIso'));
                $googleCategories = \Tools::getValue('bt_google-cat');
                $id_lang = \Language::getIdByIso($isoExplode[0]);

                // Use case if the installed id_lang is deleted, we force use the current default lang of the shop
                if (empty($id_lang)) {
                    $id_lang = \Configuration::get('PS_LANG_DEFAULT');
                }

                // delete previous google matching categories
                if (categoryTaxonomy::deleteGoogleCategory(\Gmerchantcenterpro::$iShopId, $isoLang, moduleConfiguration::GMCP_TABLE_PREFIX)) {
                    foreach ($googleCategories as $idShopCategorie => $googleCategoryValue) {
                        if (!empty($googleCategoryValue)) {
                            // insert each category
                            categoryTaxonomy::insertGoogleCategory(\Gmerchantcenterpro::$iShopId, $idShopCategorie, $googleCategoryValue, $isoLang);
                        }
                    }
                }

                $this->confirmations[] = $this->module->l('The mapping of your categories to the official Google categories has been successfully updated.');
            } catch (\Exception $e) {
                $this->errors[] = $e->getMessage();
            }
        }

        if (Tools::isSubmit('gmcTaxonomies') || Tools::isSubmit('fpaTaxonomies') || Tools::isSubmit('tkpTaxonomies')) {
            try {
                $moduleSource = '';

                if (!empty(Tools::isSubmit('gmcTaxonomies'))) {
                    $moduleSource = 'gmcTaxonomies';
                } elseif (!empty(Tools::isSubmit('fpaTaxonomies'))) {
                    $moduleSource = 'fpaTaxonomies';
                } elseif (!empty(Tools::isSubmit('tkpTaxonomies'))) {
                    $moduleSource = 'tkpTaxonomies';
                }

                if (!empty($moduleSource)) {
                    $dataToImport = moduleTools::getTaxonomiesToImport($isoLang);

                    if (categoryTaxonomy::deleteGoogleCategory(\Gmerchantcenterpro::$iShopId, $isoLang, moduleConfiguration::GMCP_TABLE_PREFIX)) {
                        if (!empty($dataToImport)) {
                            foreach ($dataToImport[$moduleSource] as $data) {
                                categoryTaxonomy::insertGoogleCategory(\Gmerchantcenterpro::$iShopId, $data['id_category'], $data['txt_taxonomy'], $data['lang']);
                            }
                        }
                    }
                    $this->confirmations[] = $this->module->l('The mapping of your categories to the official Google categories has been successfully imported.');
                }
            } catch (\Exception $e) {
                $this->errors[] = $e->getMessage();
            }
        }
    }

    /**
     * manages the search customer autcomplete
     *
     * @return string
     */
    public function processAutocomplete()
    {
        $items = [];
        $isoLang = \Tools::getValue('sLangIso');
        $query = \Tools::getValue('query');
        $words = explode(' ', $query);
        $taxonomyFound = [];

        if (strlen($query) >= 4) {
            $items = [];
            $items = googleTaxonomy::autocompleteSearch($isoLang, $words, moduleConfiguration::GMCP_TABLE_PREFIX);
            if (!empty($items) && is_array($items)) {
                foreach ($items as $data) {
                    $taxonomyFound[] = $data['value'];
                }
            }
        }

        exit(json_encode($taxonomyFound));
    }
}
