<?php
/**
 * Pretty URLs
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    FMM Modules
 * @copyright Copyright 2024 © FMM Modules All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  FMM Modules
 * @package   Prettyurls
*/

if (!defined('_PS_VERSION_')) {
	exit;
}
if (!defined('_MYSQL_ENGINE_')) {
	define('_MYSQL_ENGINE_', 'MyISAM');
}

include_once dirname(__FILE__) . '/classes/RedirectUrl.php';
include_once dirname(__FILE__) . '/classes/Manage404Report.php';
include_once dirname(__FILE__) . '/classes/AllBrokenLinks.php';
include_once dirname(__FILE__) . '/classes/checkStatus.php';
use PrestaShop\Autoload\PrestashopAutoload;

class Prettyurls extends Module
{
	public $default_routes = [];
	protected $type_array = null;
    protected $newpsversion = null;
	private $sql_checks = [];
	public $cron = false;
    public const HOOK_ADD_URLS_SM = 'SitemapAppendUrls';
	public function __construct()
	{
		$this->name = 'prettyurls';
		$this->tab = 'seo';
		$this->version = '3.1.0';
		$this->author = 'FMM Modules';
		$this->bootstrap = true;
		$this->module_key = 'db5a06ecc8e806d8941ed9a91d6d3ad2';
		parent::__construct();
		$this->ps_versions_compliancy = ['min' => '1.6.1', 'max' => _PS_VERSION_];
		$this->type_array = ['home', 'meta', 'product', 'category', 'manufacturer', 'supplier', 'cms', 'module'];
		$this->newpsversion = Tools::version_compare(_PS_VERSION_, '1.7.8', '>=') ? 1 : 0;
		$this->displayName = $this->l('Pretty URLs');
		$this->description = $this->l('This module will remove IDs from URLs and make them SEO friendly with many other options');
		$this->tabClass = 'AdminManageRedirect';
		$this->robots_file = _PS_ROOT_DIR_ . '/robots.txt';
		$default_routes = Dispatcher::getInstance()->default_routes;
		$productRoute = $default_routes['product_rule']['rule'];
	}

	public function install()
	{
		Configuration::updateValue('FMM_PRETTY_ATTR_ID', true);
		Configuration::updateValue('FMM_PRETTY_ID', true);
		Configuration::updateValue('FMM_PRETTY_REDIRECT_PRD_URLS', true);
		Configuration::updateValue('FMM_PRETTY_ID_CATEGORY', true);
		Configuration::updateValue('FMM_PRETTY_REDIRECT_CATEGORY_URLS', true);
		Configuration::updateValue('FMM_PRETTY_ID_CMS', true);
		Configuration::updateValue('FMM_PRETTY_REDIRECT_CMS_URLS', true);
		Configuration::updateValue('FMM_PRETTY_ID_CMS_CAT', true);
		Configuration::updateValue('FMM_PRETTY_REDIRECT_CMS_CAT_URLS', true);
		Configuration::updateValue('FMM_PRETTY_ID_BRANDS', true);
		Configuration::updateValue('FMM_PRETTY_REDIRECT_BRANDS_URLS', true);
		Configuration::updateValue('FMM_PRETTY_ID_SUPPLIER', true);
		Configuration::updateValue('FMM_PRETTY_REDIRECT_SUPPLIER_URLS', true);
		include(dirname(__FILE__).'/sql/install.php');
		return (parent::install()
				&& $this->installTab()
				&& $this->moveFiles()
				&& $this->clearCache()
				&& $this->registerHook('displayBackOfficeHeader')
				&& $this->initTokenSecure()
				&& $this->registerHook('displayTwitterSummaryCard')
				&& $this->registerHook('displayHeader'));
	}

	public function uninstall()
	{
		include(dirname(__FILE__).'/sql/uninstall.php');
		return ($this->removeOverloadedFiles()
				&& parent::uninstall()
				&& $this->uninstallTab()
				&& $this->clearCache());
	}

	public function initTokenSecure()
    {
        $now = date('Y-m-d H:i:s');
		$time_string = strtotime($now);
		$time_string = md5(_COOKIE_KEY_.$time_string);
		Configuration::updateValue('FMM_PRETTY_TOKEN', $time_string);
        return true;
    }
	
	public function installTab()
	{
		$tab = new Tab();
		$tab->active = 1;
		$tab->class_name = $this->tabClass;
		$tab->name = array();
		foreach (Language::getLanguages(true) as $lang) {
			$tab->name[$lang['id_lang']] = 'URL Redirect';
		}
		$tab->id_parent = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) ? (int)Tab::getIdFromClassName('AdminParentMeta') : (int)Tab::getIdFromClassName('AdminParentPreferences');
		$tab->module = $this->name;
		
		$subtab = new Tab();
        $subtab->class_name = 'AdminManage404Report';
        $subtab->id_parent = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) ? (int)Tab::getIdFromClassName('AdminParentMeta') : (int)Tab::getIdFromClassName('AdminParentPreferences');
        $subtab->module = $this->name;
        $subtab->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('Manage 404 Report');
        $subtab->add();

        $subtab = new Tab();
        $subtab->class_name = 'AdminManageAll404Pages';
        $subtab->id_parent = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) ? (int)Tab::getIdFromClassName('AdminParentMeta') : (int)Tab::getIdFromClassName('AdminParentPreferences');
        $subtab->module = $this->name;
        $subtab->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('Manage All 404 Pages');
        $subtab->add();
		return $tab->add();
	}
	
	public function uninstallTabOldMod()
	{
		$id_tab = (int)Tab::getIdFromClassName('AdminPrettyUrls');
		if ($id_tab) {
			$tab = new Tab($id_tab);
			return $tab->delete();
		}
		return true;
	}
	
	public function uninstallTab()
	{
		$id_tab = (int)Tab::getIdFromClassName('AdminManageRedirect');
		if ($id_tab) {
			$tab = new Tab($id_tab);
			$tab->delete();
			$id_tab = (int)Tab::getIdFromClassName('AdminManage404Report');
			$tab = new Tab($id_tab);
			$tab->delete();
			$id_tab = (int)Tab::getIdFromClassName('AdminManageAll404Pages');
			$tab = new Tab($id_tab);
			$tab->delete();
		}
		return true;
	}
	
	public function moveFiles()
	{
		if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
			Tools::deleteFile(_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'ProductController.php');
			Tools::deleteFile(_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'CategoryController.php');
			Tools::copy(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'override'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'CategoryController.php',
						_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'CategoryController.php');
			Tools::copy(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'override'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'ManufacturerController.php',
						_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'ManufacturerController.php');
			Tools::copy(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'override'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'SupplierController.php',
						_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'SupplierController.php');
			// Tools::copy(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'ProductController.php',
			// 			_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'ProductController.php');
			if (Tools::version_compare(_PS_VERSION_, '1.7.4.0', '<')) {
				// Tools::copy(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'CartController.php',
				// 		_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'CartController.php');
			}
		}
		return true;
	}

	private function clearCache()
	{
		Tools::clearSmartyCache();
		$timestamp = time();
		if (file_exists(_PS_CACHE_DIR_.'class_index.php') && Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
			(rename(_PS_CACHE_DIR_.'class_index.php', _PS_CACHE_DIR_.$timestamp.'_class_index.php'));
		}
		if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
			Tools::clearSf2Cache();
		}
		if (Tools::version_compare(_PS_VERSION_, '8.0.0', '>=')) {
			Cache::clean('Module::isEnabled*');
		}
		return true;
	}

	public function removeOverloadedFiles()
	{
		Tools::deleteFile(_PS_OVERRIDE_DIR_.'classes'.DIRECTORY_SEPARATOR.'Dispatcher.php');
		Tools::deleteFile(_PS_OVERRIDE_DIR_.'classes'.DIRECTORY_SEPARATOR.'Link.php');
		if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
			Tools::deleteFile(_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'CategoryController.php');
			Tools::deleteFile(_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'ProductController.php');
			Tools::deleteFile(_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'ManufacturerController.php');
			Tools::deleteFile(_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'SupplierController.php');
			Tools::deleteFile(_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'CartController.php');
			Tools::generateIndex();
		}
		return true;
	}

	protected function crawlDbForId($rew)
    {
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        $sql = new DbQuery();
        $sql->select('`id_category`');
        $sql->from('category_lang');
        $sql->where('`id_lang` = '.(int)$id_lang.' AND `id_shop` = '.(int)$id_shop.' AND `link_rewrite` = "'.pSQL($rew).'"');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    protected function crawlDbForProId($rew)
	{
	    $id_lang = $this->context->language->id;
	    $id_shop = $this->context->shop->id;
	    $sql = new DbQuery();
	    $sql->select('`id_product`');
	    $sql->from('product_lang');
	    $sql->where('`id_lang` = '.(int)$id_lang.' AND `id_shop` = '.(int)$id_shop.' AND `link_rewrite` = "'.pSQL($rew).'"');
	    return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
	}

	public function hookDisplayHeader()
    {
    	$controller = $this->context->controller;
    	if ($controller instanceof CategoryController) {
	    	$link_rewrite = Tools::safeOutput(urldecode(Tools::getValue('category_rewrite')));
		    $cat_pattern = '/.*?\/([0-9]+)\-([_a-zA-Z0-9-\pL]*)/';
		    preg_match($cat_pattern, $_SERVER['REQUEST_URI'], $url_array);

		    if (isset($url_array[2]) && $url_array[2] != '') {
		        $link_rewrite = $url_array[2];
		    }

		    if ($link_rewrite) {
		        $id_lang = $this->context->language->id;
		        $id_shop = $this->context->shop->id;
		        $sql = 'SELECT `id_category`
		                FROM '._DB_PREFIX_.'category_lang
		                WHERE `link_rewrite` = \''.pSQL($link_rewrite).'\'
		                AND `id_lang` = '.(int)$id_lang.'
		                AND `id_shop` = '.(int)$id_shop;
		        $id_category = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

		        if ($id_category > 0) {
		            $_POST['id_category'] = $id_category;
		            $_GET['category_rewrite'] = '';

		            $canonical_url = $this->context->link->getCategoryLink($id_category);
		        }
		    }

		    $allow_accented_chars = (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
		    if ($allow_accented_chars > 0) {
		        $id_category = (int)Tools::getValue('id_category');
		        if ($id_category <= 0) {
		            $id = (int)$this->crawlDbForId($_GET['category_rewrite']);
		            if ($id > 0) {
		                $_POST['id_category'] = $id;
		                
		                $canonical_url = $this->context->link->getCategoryLink($id);
		            }
		        }
		    }
		}

	    //----------------------------------------------------

	    if ($this->context->controller instanceof ProductController) {
	        $link_rewrite = Tools::safeOutput(urldecode(Tools::getValue('product_rewrite')));
	        $prod_pattern = '/.*?\/([0-9]+)\-([_a-zA-Z0-9-\pL]*)\.html/';
	        preg_match($prod_pattern, $_SERVER['REQUEST_URI'], $url_array);

	        // Check if the URL contains a product rewrite
	        if (isset($url_array[2]) && $url_array[2] != '') {
	            $link_rewrite = $url_array[2];
	        }

	        // If the product rewrite is found
	        if ($link_rewrite) {
	            $id_lang = $this->context->language->id;
	            $id_shop = $this->context->shop->id;

	            // Query to get the product ID based on the link_rewrite
	            $sql = 'SELECT id_product
	                    FROM '._DB_PREFIX_.'product_lang
	                    WHERE link_rewrite = \''.pSQL($link_rewrite).'\'
	                    AND id_lang = '.(int)$id_lang.'
	                    AND id_shop = '.(int)$id_shop;

	            $id_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

	            // If product ID is found
	            if ($id_product > 0) {
	                $_POST['id_product'] = $id_product;
	                $_GET['product_rewrite'] = '';
	            } else {
	                // Secondary pattern matching if no product found initially
	                $prod_pattern_sec = '/.*?\/([0-9]+)\-([_a-zA-Z0-9-\pL]*\-[0-9\pL]*)\.html/';
	                preg_match($prod_pattern_sec, $_SERVER['REQUEST_URI'], $url_array_sec);

	                if (isset($url_array_sec[2]) && $url_array_sec[2] != '') {
	                    $segments = explode('-', $url_array_sec[2]);
	                    array_pop($segments); // Remove last segment (which could be the ID or numeric portion)
	                    $link_rewrite = implode('-', $segments);
	                }

	                // Second query to try to find the product again
	                $sql = 'SELECT id_product
	                        FROM '._DB_PREFIX_.'product_lang
	                        WHERE link_rewrite = \''.pSQL($link_rewrite).'\'
	                        AND id_lang = '.(int)$id_lang.'
	                        AND id_shop = '.(int)$id_shop;

	                $id_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

	                // If product is found in the second attempt
	                if ($id_product > 0) {
	                    $_POST['id_product'] = $id_product;
	                    $_GET['product_rewrite'] = '';
	                }
	            }
	        }
	        // Fix for accented chars URLs
	        $allow_accented_chars = (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
	        if ($allow_accented_chars > 0) {
	            $id_product = (int)Tools::getValue('id_product');
	            if ($id_product <= 0) {
	                $id = (int)$this->crawlDbForProId(Tools::getValue('product_rewrite'));
	                if ($id > 0) {
	                    $_POST['id_product'] = $id;
	                }
	            }
	        }
	    }
	    //----------------------------------------------------

        $id_product = (int) Tools::getValue('id_product');
        if ($id_product > 0) {
            $this->context->controller->addJS($this->_path . 'views/js/seometamanager.js');
        }
        $seo_title = '';
        $seo_desc = '';
        $seo_keys = '';
        $context = Context::getContext();
        $ps_ver = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
        $id_lang = (int) $context->language->id;
        $product_id = (int) Tools::getValue('id_product');
        $category_id = (int) Tools::getValue('id_category');
        $cms_id = (int) Tools::getValue('id_cms');
        $page_name = Dispatcher::getInstance()->getController();
        $state_home_title = (int) Configuration::get('FMM_STM_STATE_HOME_TITLE');
        $state_home_desc = (int) Configuration::get('FMM_STM_STATE_HOME_DESC');
        $state_home_keywords = (int) Configuration::get('FMM_STM_STATE_HOME_TAGS');
        // If home page - SEO
        if ($page_name == 'index') {
            if ($state_home_title > 0) {
                $home_title = Configuration::get('FMM_STM_HOME_TITLE_TXT', $id_lang);
                $home_title = $this->getFormattedValues($home_title);
                if (!empty($home_title)) {
                    $seo_title = $home_title;
                    // Alternative meathod for ps 1.6
                    $context->smarty->assign('meta_title', $home_title);
                }
            }
            if ($state_home_desc > 0) {
                $home_desc = Configuration::get('FMM_STM_HOME_DESC_TXT', $id_lang);
                $home_desc = $this->getFormattedValues($home_desc);
                if (!empty($home_desc)) {
                    $seo_desc = $home_desc;
                    // Alternative meathod for ps 1.6
                    $context->smarty->assign('meta_description', $home_desc);
                }
            }
            if ($state_home_keywords > 0) {
                $home_keywords = Configuration::get('FMM_STM_HOME_TAGS_TXT', $id_lang);
                $home_keywords = $this->getFormattedValues($home_keywords);
                if (!empty($home_keywords)) {
                    $seo_keys = $home_keywords;
                    // Alternative meathod for ps 1.6
                    $context->smarty->assign('meta_keywords', $home_keywords);
                }
            }
        } elseif ($cms_id > 0) {
            $state_cms_title = (int) Configuration::get('FMM_STM_STATE_CMS_TITLE');
            $state_cms_desc = (int) Configuration::get('FMM_STM_STATE_CMS_DESC');
            $state_cms_keywords = (int) Configuration::get('FMM_STM_STATE_CMS_TAGS');
            if ($state_cms_title > 0) {
                $cms_title = Configuration::get('FMM_STM_CMS_TITLE_TXT', $id_lang);
                $cms_title = $this->getFormattedValues($cms_title, false, false, $cms_id);
                if (!empty($cms_title)) {
                    $seo_title = $cms_title;
                    // Alternative meathod for ps 1.6
                    $context->smarty->assign('meta_title', $cms_title);
                }
            }
            if ($state_cms_desc > 0) {
                $cms_desc = Configuration::get('FMM_STM_CMS_DESC_TXT', $id_lang);
                $cms_desc = $this->getFormattedValues($cms_desc, false, false, $cms_id);
                if (!empty($cms_desc)) {
                    $seo_desc = $cms_desc;
                    // Alternative meathod for ps 1.6
                    $context->smarty->assign('meta_description', $cms_desc);
                }
            }
            if ($state_cms_keywords > 0) {
                $cms_keywords = Configuration::get('FMM_STM_CMS_TAGS_TXT', $id_lang);
                $cms_keywords = $this->getFormattedValues($cms_keywords, false, false, $cms_id);
                if (!empty($cms_keywords)) {
                    $seo_keys = $cms_keywords;
                    // Alternative meathod for ps 1.6
                    $context->smarty->assign('meta_keywords', $cms_keywords);
                }
            }
        } elseif ($category_id > 0) {
            $state_category_title = (int) Configuration::get('FMM_STM_STATE_CATEGORY_TITLE');
            $state_category_desc = (int) Configuration::get('FMM_STM_STATE_CATEGORY_DESC');
            $state_category_keywords = (int) Configuration::get('FMM_STM_STATE_CATEGORY_TAGS');
            if ($state_category_title > 0) {
                $category_title = Configuration::get('FMM_STM_CATEGORY_TITLE_TXT', $id_lang);
                $category_title = $this->getFormattedValues($category_title, false, $category_id, false);
                if (!empty($category_title)) {
                    $seo_title = $category_title;
                    // Alternative meathod for ps 1.6
                    $context->smarty->assign('meta_title', $category_title);
                }
            }
            if ($state_category_desc > 0) {
                $category_desc = Configuration::get('FMM_STM_CATEGORY_DESC_TXT', $id_lang);
                $category_desc = $this->getFormattedValues($category_desc, false, $category_id, false);
                if (!empty($category_desc)) {
                    $seo_desc = $category_desc;
                    // Alternative meathod for ps 1.6
                    $context->smarty->assign('meta_description', $category_desc);
                }
            }
            if ($state_category_keywords > 0) {
                $category_keywords = Configuration::get('FMM_STM_CATEGORY_TAGS_TXT', $id_lang);
                $category_keywords = $this->getFormattedValues($category_keywords, false, $category_id, false);
                if (!empty($category_keywords)) {
                    $seo_keys = $category_keywords;
                    // Alternative meathod for ps 1.6
                    $context->smarty->assign('meta_keywords', $category_keywords);
                }
            }
        } elseif ($product_id > 0) {
            $state_product_title = (int) Configuration::get('FMM_STM_STATE_PRODUCT_TITLE');
            $state_product_desc = (int) Configuration::get('FMM_STM_STATE_PRODUCT_DESC');
            $state_product_keywords = (int) Configuration::get('FMM_STM_STATE_PRODUCT_TAGS');
            if ($state_product_title > 0) {
                $product_title = Configuration::get('FMM_STM_PRODUCT_TITLE_TXT', $id_lang);
                $product_title = $this->getFormattedValues($product_title, $product_id, false, false);
                if (!empty($product_title)) {
                    $seo_title = $product_title;
                    // Alternative meathod for ps 1.6
                    $context->smarty->assign('meta_title', $product_title);
                }
            }
            if ($state_product_desc > 0) {
                $product_desc = Configuration::get('FMM_STM_PRODUCT_DESC_TXT', $id_lang);
                $product_desc = $this->getFormattedValues($product_desc, $product_id, false, false);
                if (!empty($product_desc)) {
                    $seo_desc = $product_desc;
                    // Alternative meathod for ps 1.6
                    $context->smarty->assign('meta_description', $product_desc);
                }
            }
            if ($state_product_keywords > 0) {
                $product_keywords = Configuration::get('FMM_STM_PRODUCT_TAGS_TXT', $id_lang);
                $product_keywords = $this->getFormattedValues($product_keywords, $product_id, false, false);
                if (!empty($product_keywords)) {
                    $seo_keys = $product_keywords;
                    // Alternative meathod for ps 1.6
                    $context->smarty->assign('meta_keywords', $product_keywords);
                }
            }
        }
        // For ps 1.7 only as not supported in ps 1.6
        if ($ps_ver > 0 && ($page_name == 'index' || $page_name == 'product' || $page_name == 'cms' || $page_name == 'category')) {
            $this->getTemplateVarPage($seo_title, $seo_desc, $seo_keys);
        }
		
		$trigger = (int) Configuration::get('REDIRECT_TRIGGER');
        // old url redirection
        $red_request_uri = $_SERVER['REQUEST_URI'];
        if ($trigger > 0) {
            $request_uri_eplode = explode('/', $red_request_uri);
            $request_uri_eplode = array_filter($request_uri_eplode);
            $request_uri = end($request_uri_eplode);
            $uri_exists = $this->checkUriExistance($request_uri);
            if (!empty($uri_exists)) {
                Tools::redirect($uri_exists);
            } else {
                $this->notFound();
            }
        } else {
            $this->notFound();
        }
    }

	
	public function hookDisplayBackOfficeHeader()
	{
		$this->context->controller->addCSS($this->_path.'views/css/admin.css');
		$this->context->controller->addJS($this->_path.'views/js/admin.js');
	}
	
	public function disable($force_all = true)
    {
		if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
			Tools::deleteFile(_PS_OVERRIDE_DIR_.'classes'.DIRECTORY_SEPARATOR.'Dispatcher.php');
			Tools::deleteFile(_PS_OVERRIDE_DIR_.'classes'.DIRECTORY_SEPARATOR.'Link.php');
		}
		return true;
	}
	
	public function getContent()
    {
		$this->html = $this->display(__FILE__, 'views/templates/hook/info.tpl');
		$this->html_link = $this->display(__FILE__, 'views/templates/hook/perf_link.tpl');
		$warning = $this->html;
		$warning .= $this->displayWarning($this->l('1. Turn OFF the cache for the testing of new URLs, you can turn it ON later.'));
		$warning .= $this->displayWarning($this->l('2. Please make sure overrides are NOT disabled in ').$this->html_link);
		return $warning . $this->postProcess() . $this->_displayForm();
	}
	
	public function postProcess()
    {
		if (Tools::isSubmit('submit' . $this->name)) {
			$token = Configuration::get('FMM_PRETTY_TOKEN');
			if (!$token || empty($token)) {
				$this->initTokenSecure();
			}
			$redirectCmsCatUrls = (int) Tools::getValue('FMM_PRETTY_REDIRECT_CMS_CAT_URLS');
			//Product URLs
			$putIdAttr = (int) Tools::getValue('FMM_PRETTY_ATTR_ID');
			$productRoute = $this->getRoute('product_rule');
			if ($putIdAttr > 0) {
				if (strpos($productRoute, '{id_product_attribute:-}') !== false) {
					$productRoute = str_replace('{id_product_attribute:-}', '', $productRoute);
					Configuration::updateValue('PS_ROUTE_product_rule', $productRoute);
					Cache::clean('Module::isInstalled' . $this->name);
				}
				elseif (strpos($productRoute, '{id_product_attribute}') !== false) {
					$productRoute = str_replace('{id_product_attribute}-', '', $productRoute);
					Configuration::updateValue('PS_ROUTE_product_rule', $productRoute);
					Cache::clean('Module::isInstalled' . $this->name);
				}
			}
			elseif ($putIdAttr <= 0 && (strpos($productRoute, '{id_product_attribute:-}') === false)) {
				$productRoute = str_replace('{rewrite}', '{id_product_attribute:-}{rewrite}', $productRoute);
				Configuration::updateValue('PS_ROUTE_product_rule', $productRoute);
				Cache::clean('Module::isInstalled' . $this->name);
			}
			
			$idProductUrl = (int) Tools::getValue('FMM_PRETTY_ID');
			if ($idProductUrl > 0) {
				if (strpos($productRoute, '{id:-}') !== false) {
					$productRoute = str_replace('{id:-}', '', $productRoute);
					Configuration::updateValue('PS_ROUTE_product_rule', $productRoute);
					Cache::clean('Module::isInstalled' . $this->name);
				}
				elseif (strpos($productRoute, '{id}-') !== false) {
					$productRoute = str_replace('{id}-', '', $productRoute);
					Configuration::updateValue('PS_ROUTE_product_rule', $productRoute);
					Cache::clean('Module::isInstalled' . $this->name);
				}
			}
			elseif ($idProductUrl <= 0 && strpos($productRoute, '{id:-}') === false && strpos($productRoute, '{id}') === false) {
				if (strpos($productRoute, '{id_product_attribute:-}') !== false) {
					$productRoute = str_replace('{id_product_attribute:-}', '{id:-}{id_product_attribute:-}', $productRoute);
					Configuration::updateValue('PS_ROUTE_product_rule', $productRoute);
					Cache::clean('Module::isInstalled' . $this->name);
				}
				else {
					$productRoute = str_replace('{rewrite}', '{id:-}{rewrite}', $productRoute);
					Configuration::updateValue('PS_ROUTE_product_rule', $productRoute);
					Cache::clean('Module::isInstalled' . $this->name);
				}
			}
			$redirectProductUrls = (int) Tools::getValue('FMM_PRETTY_REDIRECT_PRD_URLS');
			$overrideFileOfPrdCont = _PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'ProductController.php';
			if ($redirectProductUrls > 0 && (int)Tools::file_exists_no_cache($overrideFileOfPrdCont) <= 0) {
				// Tools::copy(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'ProductController.php', $overrideFileOfPrdCont);
				// Cache::clean('Module::isInstalled' . $this->name);
				// PrestashopAutoload::getInstance()->generateIndex();
			}
			elseif ($redirectProductUrls <= 0) {
				Tools::deleteFile(_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'ProductController.php');
				Cache::clean('Module::isInstalled' . $this->name);
				PrestashopAutoload::getInstance()->generateIndex();
			}
			//Category URLs
			$categoryIdResult = (int) Tools::getValue('FMM_PRETTY_ID_CATEGORY');
			$categoryRoute = $this->getRoute('category_rule');
			if ($categoryIdResult > 0) {
				if (strpos($categoryRoute, '{id:-}') !== false) {
					$categoryRoute = str_replace('{id:-}', '', $categoryRoute);
					Configuration::updateValue('PS_ROUTE_category_rule', $categoryRoute);
					Cache::clean('Module::isInstalled' . $this->name);
				}
				elseif (strpos($categoryRoute, '{id}-') !== false) {
					$categoryRoute = str_replace('{id}-', '', $categoryRoute);
					Configuration::updateValue('PS_ROUTE_category_rule', $categoryRoute);
					Cache::clean('Module::isInstalled' . $this->name);
				}
			}
			elseif ($categoryIdResult <= 0 && strpos($categoryRoute, '{id:-}') === false && strpos($categoryRoute, '{id}') === false) {
				$categoryRoute = str_replace('{rewrite}', '{id:-}{rewrite}', $categoryRoute);
				Configuration::updateValue('PS_ROUTE_category_rule', $categoryRoute);
				Cache::clean('Module::isInstalled' . $this->name);
			}
			$redirectCategoryUrls = (int) Tools::getValue('FMM_PRETTY_REDIRECT_CATEGORY_URLS');
			$overrideFileOfCatCont = _PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'CategoryController.php';
			if ($redirectCategoryUrls > 0 && (int)Tools::file_exists_no_cache($overrideFileOfCatCont) <= 0) {
				Tools::copy(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'override'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'CategoryController.php', $overrideFileOfCatCont);
				Cache::clean('Module::isInstalled' . $this->name);
				PrestashopAutoload::getInstance()->generateIndex();
			}
			elseif ($redirectCategoryUrls <= 0) {
				Tools::deleteFile(_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'CategoryController.php');
				Cache::clean('Module::isInstalled' . $this->name);
				PrestashopAutoload::getInstance()->generateIndex();
			}
			//CMS URLs
			$cmsIdResult = (int) Tools::getValue('FMM_PRETTY_ID_CMS');
			$cmsRoute = $this->getRoute('cms_rule');
			if ($cmsIdResult > 0) {
				if (strpos($cmsRoute, '{id:-}') !== false) {
					$cmsRoute = str_replace('{id:-}', '', $cmsRoute);
					Configuration::updateValue('PS_ROUTE_cms_rule', $cmsRoute);
					Cache::clean('Module::isInstalled' . $this->name);
				}
				elseif (strpos($cmsRoute, '{id}-') !== false) {
					$cmsRoute = str_replace('{id}-', '', $cmsRoute);
					Configuration::updateValue('PS_ROUTE_cms_rule', $cmsRoute);
					Cache::clean('Module::isInstalled' . $this->name);
				}
			}
			elseif ($cmsIdResult <= 0 && strpos($cmsRoute, '{id:-}') === false && strpos($cmsRoute, '{id}') === false) {
				$cmsRoute = str_replace('{rewrite}', '{id:-}{rewrite}', $cmsRoute);
				Configuration::updateValue('PS_ROUTE_cms_rule', $cmsRoute);
				Cache::clean('Module::isInstalled' . $this->name);
			}
			$redirectCmsUrls = (int) Tools::getValue('FMM_PRETTY_REDIRECT_CMS_URLS');
			$overrideFileOfCMSCont = _PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'CmsController.php';
			if ($redirectCmsUrls > 0 && (int)Tools::file_exists_no_cache($overrideFileOfCMSCont) <= 0) {
				Tools::copy(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'override'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'CmsController.php', $overrideFileOfCMSCont);
				Cache::clean('Module::isInstalled' . $this->name);
				PrestashopAutoload::getInstance()->generateIndex();
			}
			//CMS Category URLs
			$cmsCatIdResult = (int) Tools::getValue('FMM_PRETTY_ID_CMS_CAT');
			$cmsCatRoute = $this->getRoute('cms_category_rule');
			if ($cmsCatIdResult > 0) {
				if (strpos($cmsCatRoute, '{id:-}') !== false) {
					$cmsCatRoute = str_replace('{id:-}', '', $cmsCatRoute);
					Configuration::updateValue('PS_ROUTE_cms_category_rule', $cmsCatRoute);
					Cache::clean('Module::isInstalled' . $this->name);
				}
				elseif (strpos($cmsCatRoute, '{id}-') !== false) {
					$cmsCatRoute = str_replace('{id}-', '', $cmsCatRoute);
					Configuration::updateValue('PS_ROUTE_cms_category_rule', $cmsCatRoute);
					Cache::clean('Module::isInstalled' . $this->name);
				}
			}
			elseif ($cmsCatIdResult <= 0 && strpos($cmsCatRoute, '{id:-}') === false && strpos($cmsCatRoute, '{id}') === false) {
				$cmsCatRoute = str_replace('{rewrite}', '{id:-}{rewrite}', $cmsCatRoute);
				Configuration::updateValue('PS_ROUTE_cms_category_rule', $cmsCatRoute);
				Cache::clean('Module::isInstalled' . $this->name);
			}
			//Brands URLs
			$brandsIdResult = (int) Tools::getValue('FMM_PRETTY_ID_BRANDS');
			$brandsRoute = $this->getRoute('manufacturer_rule');
			if ($brandsIdResult > 0) {
				if (strpos($brandsRoute, '{id:-}') !== false) {
					$brandsRoute = str_replace('{id:-}', '', $brandsRoute);
					Configuration::updateValue('PS_ROUTE_manufacturer_rule', $brandsRoute);
					Cache::clean('Module::isInstalled' . $this->name);
				}
				elseif (strpos($brandsRoute, '{id}-') !== false) {
					$brandsRoute = str_replace('{id}-', '', $brandsRoute);
					Configuration::updateValue('PS_ROUTE_manufacturer_rule', $brandsRoute);
					Cache::clean('Module::isInstalled' . $this->name);
				}
			}
			elseif ($brandsIdResult <= 0 && strpos($brandsRoute, '{id:-}') === false && strpos($brandsRoute, '{id}') === false) {
				$brandsRoute = str_replace('{rewrite}', '{id:-}{rewrite}', $brandsRoute);
				Configuration::updateValue('PS_ROUTE_manufacturer_rule', $brandsRoute);
				Cache::clean('Module::isInstalled' . $this->name);
			}
			$redirectBrandsUrls = (int) Tools::getValue('FMM_PRETTY_REDIRECT_BRANDS_URLS');
			$overrideFileOfBrandsCont = _PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'ManufacturerController.php';
			if ($redirectBrandsUrls > 0 && (int)Tools::file_exists_no_cache($overrideFileOfBrandsCont) <= 0) {
				Tools::copy(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'override'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'ManufacturerController.php', $overrideFileOfBrandsCont);
				Cache::clean('Module::isInstalled' . $this->name);
				PrestashopAutoload::getInstance()->generateIndex();
			}
			elseif ($redirectBrandsUrls <= 0) {
				Tools::deleteFile(_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'ManufacturerController.php');
				Cache::clean('Module::isInstalled' . $this->name);
				PrestashopAutoload::getInstance()->generateIndex();
			}
			//SUPPLIER URLs
			$supplierIdResult = (int) Tools::getValue('FMM_PRETTY_ID_SUPPLIER');
			$supplierRoute = $this->getRoute('supplier_rule');
			if ($supplierIdResult > 0) {
				if (strpos($supplierRoute, '{id:-}') !== false) {
					$supplierRoute = str_replace('{id:-}', '', $supplierRoute);
					Configuration::updateValue('PS_ROUTE_supplier_rule', $supplierRoute);
					Cache::clean('Module::isInstalled' . $this->name);
				}
				elseif (strpos($supplierRoute, '{id}-') !== false) {
					$supplierRoute = str_replace('{id}-', '', $supplierRoute);
					Configuration::updateValue('PS_ROUTE_supplier_rule', $supplierRoute);
					Cache::clean('Module::isInstalled' . $this->name);
				}
			}
			elseif ($supplierIdResult <= 0 && strpos($supplierRoute, '{id:-}') === false && strpos($supplierRoute, '{id}') === false) {
				$supplierRoute = str_replace('{rewrite}', '{id:-}{rewrite}', $supplierRoute);
				Configuration::updateValue('PS_ROUTE_supplier_rule', $supplierRoute);
				Cache::clean('Module::isInstalled' . $this->name);
			}
			$redirectSupplierUrls = (int) Tools::getValue('FMM_PRETTY_REDIRECT_SUPPLIER_URLS');
			$overrideFileOfSupplierCont = _PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'SupplierController.php';
			if ($redirectSupplierUrls > 0 && (int)Tools::file_exists_no_cache($overrideFileOfSupplierCont) <= 0) {
				Tools::copy(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'override'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'SupplierController.php', $overrideFileOfSupplierCont);
				Cache::clean('Module::isInstalled' . $this->name);
				PrestashopAutoload::getInstance()->generateIndex();
			}
			elseif ($redirectSupplierUrls <= 0) {
				Tools::deleteFile(_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'front'.DIRECTORY_SEPARATOR.'SupplierController.php');
				Cache::clean('Module::isInstalled' . $this->name);
				PrestashopAutoload::getInstance()->generateIndex();
			}
			Configuration::updateValue('FMM_PRETTY_ISO', (int) Tools::getValue('FMM_PRETTY_ISO'));
			Configuration::updateValue('FMM_PRETTY_ATTR_ALIAS', (int) Tools::getValue('FMM_PRETTY_ATTR_ALIAS'));
			Configuration::updateValue('FMM_PRETTY_ATTR_ID', (int) $putIdAttr);
			Configuration::updateValue('FMM_PRETTY_ID', (int) $idProductUrl);
			Configuration::updateValue('FMM_PRETTY_REDIRECT_PRD_URLS', (int) $redirectProductUrls);
			Configuration::updateValue('FMM_PRETTY_REDIRECT_CATEGORY_URLS', (int) $redirectCategoryUrls);
			Configuration::updateValue('FMM_PRETTY_ID_CATEGORY', (int) $categoryIdResult);
			Configuration::updateValue('FMM_PRETTY_REDIRECT_CMS_URLS', (int) $redirectCmsUrls);
			Configuration::updateValue('FMM_PRETTY_ID_CMS', (int) $cmsIdResult);
			Configuration::updateValue('FMM_PRETTY_REDIRECT_CMS_CAT_URLS', (int) $redirectCmsCatUrls);
			Configuration::updateValue('FMM_PRETTY_ID_CMS_CAT', (int) $cmsCatIdResult);
			Configuration::updateValue('FMM_PRETTY_REDIRECT_BRANDS_URLS', (int) $redirectBrandsUrls);
			Configuration::updateValue('FMM_PRETTY_ID_BRANDS', (int) $brandsIdResult);
			Configuration::updateValue('FMM_PRETTY_REDIRECT_SUPPLIER_URLS', (int) $redirectSupplierUrls);
			Configuration::updateValue('FMM_PRETTY_ID_SUPPLIER', (int) $supplierIdResult);
			$this->context->controller->confirmations[] = $this->l('The settings have been updated.');

		}
		elseif (Tools::isSubmit('submitRedirects')) {
			$redirect_404 = Tools::getValue('REDIRECT_404');
            if ($redirect_404 == 2) {
                $cms_pages = Tools::getValue('cms_pages');
                Configuration::updateValue('REDIRECT_404_CMS', $cms_pages);
            }
            Configuration::updateValue('REDIRECT_404', $redirect_404);
            Configuration::updateValue('REDIRECT_TRIGGER', Tools::getValue('REDIRECT_TRIGGER'));
            if (empty($this->context->controller->errors)) {
                $this->context->controller->confirmations[] = $this->l('The settings have been updated.');
            }
		}
		elseif (Tools::isSubmit('submitSeoMetaTagsManager')) {
            $languages = Language::getLanguages(false);
            $values = [];
            foreach ($languages as $lang) {
                $values['FMM_STM_HOME_TITLE_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_HOME_TITLE_TXT_' . $lang['id_lang']);
                $values['FMM_STM_HOME_DESC_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_HOME_DESC_TXT_' . $lang['id_lang']);
                $values['FMM_STM_HOME_TAGS_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_HOME_TAGS_TXT_' . $lang['id_lang']);
                $values['FMM_STM_CMS_TITLE_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_CMS_TITLE_TXT_' . $lang['id_lang']);
                $values['FMM_STM_CMS_DESC_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_CMS_DESC_TXT_' . $lang['id_lang']);
                $values['FMM_STM_CMS_TAGS_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_CMS_TAGS_TXT_' . $lang['id_lang']);
                $values['FMM_STM_CATEGORY_TITLE_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_CATEGORY_TITLE_TXT_' . $lang['id_lang']);
                $values['FMM_STM_CATEGORY_DESC_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_CATEGORY_DESC_TXT_' . $lang['id_lang']);
                $values['FMM_STM_CATEGORY_TAGS_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_CATEGORY_TAGS_TXT_' . $lang['id_lang']);
                $values['FMM_STM_PRODUCT_TITLE_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_PRODUCT_TITLE_TXT_' . $lang['id_lang']);
                $values['FMM_STM_PRODUCT_DESC_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_PRODUCT_DESC_TXT_' . $lang['id_lang']);
                $values['FMM_STM_PRODUCT_TAGS_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_PRODUCT_TAGS_TXT_' . $lang['id_lang']);
            }
            Configuration::updateValue('FMM_STM_HOME_TITLE_TXT', $values['FMM_STM_HOME_TITLE_TXT']);
            Configuration::updateValue('FMM_STM_HOME_DESC_TXT', $values['FMM_STM_HOME_DESC_TXT']);
            Configuration::updateValue('FMM_STM_HOME_TAGS_TXT', $values['FMM_STM_HOME_TAGS_TXT']);
            Configuration::updateValue('FMM_STM_STATE_HOME_TITLE', (int) Tools::getValue('FMM_STM_STATE_HOME_TITLE'));
            Configuration::updateValue('FMM_STM_STATE_HOME_DESC', (int) Tools::getValue('FMM_STM_STATE_HOME_DESC'));
            Configuration::updateValue('FMM_STM_STATE_HOME_TAGS', (int) Tools::getValue('FMM_STM_STATE_HOME_TAGS'));
            Configuration::updateValue('FMM_STM_CMS_TITLE_TXT', $values['FMM_STM_CMS_TITLE_TXT']);
            Configuration::updateValue('FMM_STM_CMS_DESC_TXT', $values['FMM_STM_CMS_DESC_TXT']);
            Configuration::updateValue('FMM_STM_CMS_TAGS_TXT', $values['FMM_STM_CMS_TAGS_TXT']);
            Configuration::updateValue('FMM_STM_STATE_CMS_TITLE', (int) Tools::getValue('FMM_STM_STATE_CMS_TITLE'));
            Configuration::updateValue('FMM_STM_STATE_CMS_DESC', (int) Tools::getValue('FMM_STM_STATE_CMS_DESC'));
            Configuration::updateValue('FMM_STM_STATE_CMS_TAGS', (int) Tools::getValue('FMM_STM_STATE_CMS_TAGS'));
            Configuration::updateValue('FMM_STM_CATEGORY_TITLE_TXT', $values['FMM_STM_CATEGORY_TITLE_TXT']);
            Configuration::updateValue('FMM_STM_CATEGORY_DESC_TXT', $values['FMM_STM_CATEGORY_DESC_TXT']);
            Configuration::updateValue('FMM_STM_CATEGORY_TAGS_TXT', $values['FMM_STM_CATEGORY_TAGS_TXT']);
            Configuration::updateValue('FMM_STM_STATE_CATEGORY_TITLE', (int) Tools::getValue('FMM_STM_STATE_CATEGORY_TITLE'));
            Configuration::updateValue('FMM_STM_STATE_CATEGORY_DESC', (int) Tools::getValue('FMM_STM_STATE_CATEGORY_DESC'));
            Configuration::updateValue('FMM_STM_STATE_CATEGORY_TAGS', (int) Tools::getValue('FMM_STM_STATE_CATEGORY_TAGS'));
            Configuration::updateValue('FMM_STM_PRODUCT_TITLE_TXT', $values['FMM_STM_PRODUCT_TITLE_TXT']);
            Configuration::updateValue('FMM_STM_PRODUCT_DESC_TXT', $values['FMM_STM_PRODUCT_DESC_TXT']);
            Configuration::updateValue('FMM_STM_PRODUCT_TAGS_TXT', $values['FMM_STM_PRODUCT_TAGS_TXT']);
            Configuration::updateValue('FMM_STM_STATE_PRODUCT_TITLE', (int) Tools::getValue('FMM_STM_STATE_PRODUCT_TITLE'));
            Configuration::updateValue('FMM_STM_STATE_PRODUCT_DESC', (int) Tools::getValue('FMM_STM_STATE_PRODUCT_DESC'));
            Configuration::updateValue('FMM_STM_STATE_PRODUCT_TAGS', (int) Tools::getValue('FMM_STM_STATE_PRODUCT_TAGS'));

            return $this->displayConfirmation($this->l('The settings have been updated.'));
        }
		elseif (Tools::isSubmit('submit' . $this->name.'Robots')) {
			$robots_content = Tools::getValue('FMM_PRETTY_ROBOTS');
			$this->UpdateRobotsFile($robots_content);
			return $this->displayConfirmation($this->l('The robots.txt file have been updated.'));
        }
		elseif (Tools::isSubmit('submit' . $this->name.'Sitemap')) {
			$shop = (int) Tools::getValue('fmm_sitemap_shop');
            $images_included_cdn = (int) Tools::getValue('images_included_cdn');
            $images_included_ping_se = (int) Tools::getValue('images_included_ping_se');
            Configuration::updateValue('GSITEMAP_CDN_OPT', $images_included_cdn);
            Configuration::updateValue('GSITEMAP_PING_SE_OPT', $images_included_ping_se);
            Configuration::updateValue('GSITEMAP_FREQUENCY', pSQL(Tools::getValue('gsitemap_frequency')));
            Configuration::updateValue('GSITEMAP_INDEX_CHECK', '');
            Configuration::updateValue('GSITEMAP_CHECK_IMAGE_FILE', pSQL(Tools::getValue('images_included')));
            Configuration::updateValue('GSITEMAP_URL_SCHEME', pSQL(Tools::getValue('https')));
            $type_sitemap = (int) Tools::getValue('sitemap');
            $meta = '';
            if (Tools::getValue('gsitemap_meta')) {
                $meta .= implode(', ', Tools::getValue('gsitemap_meta'));
            }
            Configuration::updateValue('GSITEMAP_DISABLE_LINKS', $meta);
            if ($shop > 0) {
                $this->emptySitemap($shop);
                $this->createSitemap($shop, $type_sitemap);
            } else {
                $this->emptySitemap();
                $this->createSitemap(false, $type_sitemap);
            }
			return $this->displayConfirmation($this->l('The sitemaps file is generated.'));
		}
		elseif (Tools::isSubmit('submit' . $this->name.'Graphs')) {
			$lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
			$fmm_fb_title = Tools::getValue('fmm_fb_title_' . $lang->id);
			$fmm_fb_descript = Tools::getValue('fmm_fb_descript_' . $lang->id);
			$fmm_fb_image = Tools::getValue('fmm_fb_image');
			$twitterimagealt = Tools::getValue('twitterimagealt_' . $lang->id);
			$twitterdescription = Tools::getValue('twitterdescription_' . $lang->id);
			$twittertitle = Tools::getValue('twittertitle_' . $lang->id);
			$twittersite = Tools::getValue('twittersite');
			$twitterimage = Tools::getValue('twitterimage');
			$twittercardtype = Tools::getValue('twittercardtype');
			$twittercard_status = Tools::getValue('twittercard_status');
			$facebookgraph_status = Tools::getValue('facebookgraph_status');
			if (!empty($twitterimagealt) && !empty($twittersite) && !empty($twittertitle) &&
				!empty($twitterdescription) &&
				!empty($fmm_fb_title)
				&& !empty($fmm_fb_descript)) {
				if (!empty($_FILES['twitterimage']['name']) || !empty($_FILES['fmm_fb_image']['name'])) {
					$target_dir = _PS_IMG_DIR_;
					if (!empty($_FILES['twitterimage']['name'])) {
						$target_file = $target_dir . basename($_FILES['twitterimage']['name']);
						$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
						$file_size = $_FILES['twitterimage']['size'];
						$image_info = getimagesize($_FILES['twitterimage']['tmp_name']);
						$image_width = $image_info[0];
						$image_height = $image_info[1];
						if (Tools::getValue('twittercardtype') == 0) {
							if ($image_width >= '144' &&
								$image_width <= '4096' &&
								$image_height >= '144' &&
								$image_height <= '4096') {
							} else {
								$this->context->controller->errors[] = $this->l('image
								should minimum dimensions of 144x144 or
								maximum of 4096x4096 pixels');
							}
						} else {
							if ($image_width >= '300' &&
								$image_width <= '4096' &&
								$image_height >= '157' &&
								$image_height <= '4096') {
							} else {
								$this->context->controller->errors[] = $this->l('image should
								with minimum dimensions of 300x157 or
								maximum of 4096x4096 pixels');
							}
						}
						if ($file_size <= '5242880') {
						} else {
							$this->context->controller->errors[] = $this->l('Image is more than 5MB.');
						}
						if ($imageFileType != 'jpg' &&
							$imageFileType != 'png' &&
							$imageFileType != 'WEBP' &&
							$imageFileType != 'gif') {
							$this->context->controller->errors[] = $this->l('only JPG, WEBP, PNG & GIF files are allowed.');
						} else {
							if (move_uploaded_file(
								$_FILES['twitterimage']['tmp_name'],
								$target_file
							)
							) {
								$twitterimage = $_FILES['twitterimage']['name'];
								Configuration::updateValue('twitterimage', (string) $twitterimage);
							} else {
							}
						}
					}
					if (!empty($_FILES['fmm_fb_image']['name'])) {
						$target_fb_file = $target_dir . basename($_FILES['fmm_fb_image']['name']);
						$image_info = getimagesize($_FILES['fmm_fb_image']['tmp_name']);
						$image_fb_width = $image_info[0];
						$image_fb_height = $image_info[1];
						if ($image_fb_width > '100' && $image_fb_height > '100') {
							if (move_uploaded_file(
								$_FILES['fmm_fb_image']['tmp_name'],
								$target_fb_file
							)
							) {
								$fmm_fb_image = $_FILES['fmm_fb_image']['name'];
								Configuration::updateValue('fmm_fb_image', (string) $fmm_fb_image);
							} else {
							}
						} else {
							$this->context->controller->errors[] = $this->l('image
							should minimum dimensions greater than 100x100');
						}
					}
				} else {
				}
				$title_length = Tools::strlen($twittertitle);
				$descrip_length = Tools::strlen($twitterdescription);
				$twitimgalt_length = Tools::strlen($twitterimagealt);
				if ($title_length > 70) {
					$this->context->controller->errors[] = $this->l('title
					length more than 70 not allowed');
				} elseif ($descrip_length > 200) {
					$this->context->controller->errors[] = $this->l('description
					length more than 200 not allowed');
				} elseif ($twitimgalt_length > 420) {
					$this->context->controller->errors[] = $this->l('image alt
					length more than 420 not allowed');
				} else {
					$check = $this->validateUsername($twittersite);
					if ($check == 1) {
						$languages = Language::getLanguages(false);
						$values = [];
						foreach ($languages as $lang) {
							$values['twitterimagealt'][$lang['id_lang']] =
							Tools::getValue('twitterimagealt_' . $lang['id_lang']);
							$values['twittertitle'][$lang['id_lang']] = Tools::getValue('twittertitle_' . $lang['id_lang']);
							$values['twitterdescription'][$lang['id_lang']] =
							Tools::getValue('twitterdescription_' . $lang['id_lang']);
							$values['fmm_fb_descript'][$lang['id_lang']] =
							Tools::getValue('fmm_fb_descript_' . $lang['id_lang']);
							$values['fmm_fb_title'][$lang['id_lang']] =
							Tools::getValue('fmm_fb_title_' . $lang['id_lang']);
						}
						Configuration::updateValue('twitterdescription', $values['twitterdescription']);
						Configuration::updateValue('twitterimagealt', $values['twitterimagealt']);
						Configuration::updateValue('twittertitle', $values['twittertitle']);
						Configuration::updateValue('twittersite', (string) $twittersite);
						Configuration::updateValue('fmm_fb_descript', $values['fmm_fb_descript']);
						Configuration::updateValue('fmm_fb_title', $values['fmm_fb_title']);
						Configuration::updateValue('twittercard_status', (string) $twittercard_status);
						Configuration::updateValue('facebookgraph_status', (string) $facebookgraph_status);
						Configuration::updateValue('twittercardtype', (string) $twittercardtype);
					} else {
						$this->context->controller->errors[] =
						$this->l('incorrect twitter username please enter corect one');
					}
				}
			} else {
				$this->context->controller->errors[] = $this->l('Some fields are missing');
			}
			if (empty($this->context->controller->errors)) {
				return $this->displayConfirmation($this->l('The settings are updated.'));
			}
		}
		elseif (Tools::getValue('genrobots')) {
			$this->generateRobotsFile();
			$redirect = AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules').'&tab_module=robots&robotsgenerated=1&conf=4#robots';
			Tools::redirectAdmin($redirect);
		}

        return '';
	}

	private function _displayForm()
    {
        $type = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 'ps17' : 'ps16';
		$module_tab = Tools::getValue('tab_module');
        $this->context->smarty->assign([
            '_ps_ver' => $type,
            'general_form' => $this->generalForm(),
			'duplicates_form' => $this->reportForm(),
			'robots_form' => $this->robotsForm(),
			'sitemap_form' => $this->sitemapForm(),
			'graphs_form' => $this->graphsForm(),
			'meta_form' => $this->metaForm(),
			'redirects_form' => $this->redirectsForm(),
			'module_tab' => $module_tab
        ]);

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }
	
	public function generalForm()
    {
		$fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'tabs' => [
                    'general' => $this->l('General'),
                    'product_urls' => $this->l('Product URLs'),
					'category_urls' => $this->l('Category URLs'),
					'cms_urls' => $this->l('CMS URLs'),
					'cms_cat_urls' => $this->l('CMS Category URLs'),
					'brand_urls' => $this->l('Brands URLs'),
					'supplier_urls' => $this->l('Supplier URLs'),
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Remove Language ISO from Default Language?'),
                        'name' => 'FMM_PRETTY_ISO',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'pretty_iso_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'pretty_iso_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                        'tab' => 'general',
                    ],
					[
                        'type' => 'switch',
                        'label' => $this->l('Remove ID From URLs?'),
                        'name' => 'FMM_PRETTY_ID',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'pretty_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'pretty_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                        'tab' => 'product_urls',
                    ],
					[
                        'type' => 'switch',
                        'label' => $this->l('Remove ID Attribute?'),
                        'name' => 'FMM_PRETTY_ATTR_ID',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'pretty_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'pretty_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                        'tab' => 'product_urls',
                    ],
					[
                        'type' => 'switch',
                        'label' => $this->l('Remove Attribute Alias?'),
                        'name' => 'FMM_PRETTY_ATTR_ALIAS',
                        'required' => false,
						'desc' => $this->l('For example the hash content like #/1-size-s/8-color-white'),
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'pretty_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'pretty_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                        'tab' => 'product_urls',
                    ],
					[
                        'type' => 'switch',
                        'label' => $this->l('Redirect OLD URLs to NEW?'),
                        'name' => 'FMM_PRETTY_REDIRECT_PRD_URLS',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'pretty_redi_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'pretty_redi_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                        'tab' => 'product_urls',
                    ],
					[
						'type' => 'text',
                        'label' => $this->l('Product URL Structure'),
                        'name' => 'FMM_PRETTY_PRODUCT_URL_SCHEME',
						'disabled' => true,
						'desc' => $this->l('Can be modified in "Shop Parameters - Traffic & SEO" settings'),
						'tab' => 'product_urls',
					],
					[
                        'type' => 'switch',
                        'label' => $this->l('Remove ID From URLs?'),
                        'name' => 'FMM_PRETTY_ID_CATEGORY',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'pretty_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'pretty_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                        'tab' => 'category_urls',
                    ],
					[
                        'type' => 'switch',
                        'label' => $this->l('Redirect OLD URLs to NEW?'),
                        'name' => 'FMM_PRETTY_REDIRECT_CATEGORY_URLS',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'pretty_redi_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'pretty_redi_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                        'tab' => 'category_urls',
                    ],
					[
						'type' => 'text',
                        'label' => $this->l('Category URL Structure'),
                        'name' => 'FMM_PRETTY_CATEGORY_URL_SCHEME',
						'disabled' => true,
						'desc' => $this->l('Can be modified in "Shop Parameters - Traffic & SEO" settings'),
						'tab' => 'category_urls',
					],
					[
                        'type' => 'switch',
                        'label' => $this->l('Remove ID From URLs?'),
                        'name' => 'FMM_PRETTY_ID_CMS',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'pretty_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'pretty_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                        'tab' => 'cms_urls',
                    ],
					[
                        'type' => 'switch',
                        'label' => $this->l('Redirect OLD URLs to NEW?'),
                        'name' => 'FMM_PRETTY_REDIRECT_CMS_URLS',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'pretty_redi_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'pretty_redi_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                        'tab' => 'cms_urls',
                    ],
					[
						'type' => 'text',
                        'label' => $this->l('CMS URL Structure'),
                        'name' => 'FMM_PRETTY_CMS_URL_SCHEME',
						'disabled' => true,
						'desc' => $this->l('Can be modified in "Shop Parameters - Traffic & SEO" settings'),
						'tab' => 'cms_urls',
					],
					[
                        'type' => 'switch',
                        'label' => $this->l('Remove ID From URLs?'),
                        'name' => 'FMM_PRETTY_ID_CMS_CAT',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'pretty_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'pretty_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                        'tab' => 'cms_cat_urls',
                    ],
					[
                        'type' => 'switch',
                        'label' => $this->l('Redirect OLD URLs to NEW?'),
                        'name' => 'FMM_PRETTY_REDIRECT_CMS_CAT_URLS',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'pretty_redi_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'pretty_redi_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                        'tab' => 'cms_cat_urls',
                    ],
					[
						'type' => 'text',
                        'label' => $this->l('CMS Category URL Structure'),
                        'name' => 'FMM_PRETTY_CMS_CAT_URL_SCHEME',
						'disabled' => true,
						'desc' => $this->l('Can be modified in "Shop Parameters - Traffic & SEO" settings'),
						'tab' => 'cms_cat_urls',
					],
					[
                        'type' => 'switch',
                        'label' => $this->l('Remove ID From URLs?'),
                        'name' => 'FMM_PRETTY_ID_BRANDS',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'pretty_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'pretty_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                        'tab' => 'brand_urls',
                    ],
					[
                        'type' => 'switch',
                        'label' => $this->l('Redirect OLD URLs to NEW?'),
                        'name' => 'FMM_PRETTY_REDIRECT_BRANDS_URLS',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'pretty_redi_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'pretty_redi_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                        'tab' => 'brand_urls',
                    ],
					[
						'type' => 'text',
                        'label' => $this->l('Manufacturer URL Structure'),
                        'name' => 'FMM_PRETTY_BRANDS_URL_SCHEME',
						'disabled' => true,
						'desc' => $this->l('Can be modified in "Shop Parameters - Traffic & SEO" settings'),
						'tab' => 'brand_urls',
					],
					[
                        'type' => 'switch',
                        'label' => $this->l('Remove ID From URLs?'),
                        'name' => 'FMM_PRETTY_ID_SUPPLIER',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'pretty_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'pretty_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                        'tab' => 'supplier_urls',
                    ],
					[
                        'type' => 'switch',
                        'label' => $this->l('Redirect OLD URLs to NEW?'),
                        'name' => 'FMM_PRETTY_REDIRECT_SUPPLIER_URLS',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'pretty_redi_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'pretty_redi_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                        'tab' => 'supplier_urls',
                    ],
					[
						'type' => 'text',
                        'label' => $this->l('SUPPLIER URL Structure'),
                        'name' => 'FMM_PRETTY_SUPPLIER_URL_SCHEME',
						'disabled' => true,
						'desc' => $this->l('Can be modified in "Shop Parameters - Traffic & SEO" settings'),
						'tab' => 'supplier_urls',
					],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
		$helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }
	
	public function getConfigFieldsValues()
    {
        $PRD_URL_RULE = $this->getRoute('product_rule');
		$robots_txt_content = file_exists($this->robots_file) ? Tools::file_get_contents($this->robots_file) : '';
        $fields = [];
		$fields['FMM_PRETTY_ISO'] = (int) Configuration::get('FMM_PRETTY_ISO');
		$fields['FMM_PRETTY_ATTR_ALIAS'] = (int) Configuration::get('FMM_PRETTY_ATTR_ALIAS');
		$fields['FMM_PRETTY_PRODUCT_URL_SCHEME'] = $PRD_URL_RULE;
		$fields['FMM_PRETTY_ATTR_ID'] = (int) Configuration::get('FMM_PRETTY_ATTR_ID');
		$fields['FMM_PRETTY_ID'] = (int) Configuration::get('FMM_PRETTY_ID');
		$fields['FMM_PRETTY_REDIRECT_PRD_URLS'] = (int) Configuration::get('FMM_PRETTY_REDIRECT_PRD_URLS');
		$fields['FMM_PRETTY_CATEGORY_URL_SCHEME'] = $this->getRoute('category_rule');
		$fields['FMM_PRETTY_ID_CATEGORY'] = (int) Configuration::get('FMM_PRETTY_ID_CATEGORY');
		$fields['FMM_PRETTY_REDIRECT_CATEGORY_URLS'] = (int) Configuration::get('FMM_PRETTY_REDIRECT_CATEGORY_URLS');
		$fields['FMM_PRETTY_CMS_URL_SCHEME'] = $this->getRoute('cms_rule');
		$fields['FMM_PRETTY_ID_CMS'] = (int) Configuration::get('FMM_PRETTY_ID_CMS');
		$fields['FMM_PRETTY_REDIRECT_CMS_URLS'] = (int) Configuration::get('FMM_PRETTY_REDIRECT_CMS_URLS');
		$fields['FMM_PRETTY_CMS_CAT_URL_SCHEME'] = $this->getRoute('cms_category_rule');
		$fields['FMM_PRETTY_ID_CMS_CAT'] = (int) Configuration::get('FMM_PRETTY_ID_CMS_CAT');
		$fields['FMM_PRETTY_REDIRECT_CMS_CAT_URLS'] = (int) Configuration::get('FMM_PRETTY_REDIRECT_CMS_CAT_URLS');
		$fields['FMM_PRETTY_BRANDS_URL_SCHEME'] = $this->getRoute('manufacturer_rule');
		$fields['FMM_PRETTY_ID_BRANDS'] = (int) Configuration::get('FMM_PRETTY_ID_BRANDS');
		$fields['FMM_PRETTY_REDIRECT_BRANDS_URLS'] = (int) Configuration::get('FMM_PRETTY_REDIRECT_BRANDS_URLS');
		$fields['FMM_PRETTY_SUPPLIER_URL_SCHEME'] = $this->getRoute('supplier_rule');
		$fields['FMM_PRETTY_ID_SUPPLIER'] = (int) Configuration::get('FMM_PRETTY_ID_SUPPLIER');
		$fields['FMM_PRETTY_REDIRECT_SUPPLIER_URLS'] = (int) Configuration::get('FMM_PRETTY_REDIRECT_SUPPLIER_URLS');
		$fields['fmm_sitemap_shop'] = '';
		$fields['gsitemap_frequency'] = '';
		$fields['https'] = '';
		$fields['sitemap'] = '';
		$fields['FMM_PRETTY_ROBOTS'] = $robots_txt_content;

        return $fields;
    }
	
	public function getRoute($id_route)
	{
		$default_routes = Dispatcher::getInstance()->default_routes;
		$route = Configuration::get('PS_ROUTE_'.$id_route);
        $route = (empty($route)) ? $default_routes[$id_route]['rule'] : $route;
		return $route;
	}
	
	public function reportForm()
    {
		$product_urls = $this->getAllProductCollisions();
		$category_urls = $this->getAllCategoryCollisions();
		$compare_urls = $this->getAllCompareCollisions();
		$langs = Language::getLanguages();
		$langs = count($langs);
		
		$this->context->smarty->assign(array(
			'product_coll' => $product_urls,
			'category_coll' => $category_urls,
			'compare_coll' => $compare_urls,
			'langs_active' => (int)$langs,
			'lnk' => $this->context->link,
		));
		return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'prettyurls/views/templates/admin/tabs/duplicates.tpl');
    }
	
	public function metaForm()
    {
        $languages = Language::getLanguages(false);
        $this->context->smarty->assign('lang_count', (int) count($languages));
        $this->html = $this->display(__FILE__, 'views/templates/hook/tags.tpl');
        $type = (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) ? 'switch' : 'radio';
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Home Meta Tags Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => $type,
                        'class' => 't',
                        'is_bool' => true,
                        'label' => $this->l('Enable Home Title:'),
                        'name' => 'FMM_STM_STATE_HOME_TITLE',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Home Title Text'),
                        'name' => 'FMM_STM_HOME_TITLE_TXT',
                        'desc' => $this->l('Enter meta title for home page like My Shop (shoptitle)'),
                    ],
                    [
                        'type' => $type,
                        'class' => 't',
                        'is_bool' => true,
                        'label' => $this->l('Enable Home Description:'),
                        'name' => 'FMM_STM_STATE_HOME_DESC',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Home Description Text'),
                        'name' => 'FMM_STM_HOME_DESC_TXT',
                        'desc' => $this->l('Enter meta description for home page.'),
                    ],
                    [
                        'type' => $type,
                        'class' => 't',
                        'is_bool' => true,
                        'label' => $this->l('Enable Home Tags:'),
                        'name' => 'FMM_STM_STATE_HOME_TAGS',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Home Meta Tags'),
                        'name' => 'FMM_STM_HOME_TAGS_TXT',
                        'desc' => $this->l('Enter meta tags for home page.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];

        $fields_form_ii = [
            'form' => [
                'legend' => [
                    'title' => $this->l('CMS Meta Tags Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => $type,
                        'class' => 't',
                        'is_bool' => true,
                        'label' => $this->l('Enable CMS Title:'),
                        'name' => 'FMM_STM_STATE_CMS_TITLE',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('CMS Title Text'),
                        'name' => 'FMM_STM_CMS_TITLE_TXT',
                        'desc' => $this->l('Enter meta title for CMS page, keywords available are shoptitle, pagetitle'),
                    ],
                    [
                        'type' => $type,
                        'class' => 't',
                        'is_bool' => true,
                        'label' => $this->l('Enable CMS Description:'),
                        'name' => 'FMM_STM_STATE_CMS_DESC',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('CMS Description Text'),
                        'name' => 'FMM_STM_CMS_DESC_TXT',
                        'desc' => $this->l('Enter meta description for CMS page, keywords available are shoptitle, pagetitle.'),
                    ],
                    [
                        'type' => $type,
                        'class' => 't',
                        'is_bool' => true,
                        'label' => $this->l('Enable CMS Tags:'),
                        'name' => 'FMM_STM_STATE_CMS_TAGS',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('CMS Meta Tags'),
                        'name' => 'FMM_STM_CMS_TAGS_TXT',
                        'desc' => $this->l('Enter meta tags for CMS page, keywords available are shoptitle, pagetitle.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];

        $fields_form_iii = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Category Meta Tags Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => $type,
                        'class' => 't',
                        'is_bool' => true,
                        'label' => $this->l('Enable Category Title:'),
                        'name' => 'FMM_STM_STATE_CATEGORY_TITLE',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Category Title Text'),
                        'name' => 'FMM_STM_CATEGORY_TITLE_TXT',
                        'desc' => $this->l('Enter meta title for Category page, keywords available are shoptitle, categoryname, parentcategory'),
                    ],
                    [
                        'type' => $type,
                        'class' => 't',
                        'is_bool' => true,
                        'label' => $this->l('Enable Category Description:'),
                        'name' => 'FMM_STM_STATE_CATEGORY_DESC',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Category Description Text'),
                        'name' => 'FMM_STM_CATEGORY_DESC_TXT',
                        'desc' => $this->l('Enter meta description for Category page, keywords available are shoptitle, categoryname, parentcategory.'),
                    ],
                    [
                        'type' => $type,
                        'class' => 't',
                        'is_bool' => true,
                        'label' => $this->l('Enable Category Tags:'),
                        'name' => 'FMM_STM_STATE_CATEGORY_TAGS',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Category Meta Tags'),
                        'name' => 'FMM_STM_CATEGORY_TAGS_TXT',
                        'desc' => $this->l('Enter meta tags for Category page, keywords available are shoptitle, categoryname, parentcategory.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
        $fields_form_iv = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Product Meta Tags Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => $type,
                        'class' => 't',
                        'is_bool' => true,
                        'label' => $this->l('Enable Product Title:'),
                        'name' => 'FMM_STM_STATE_PRODUCT_TITLE',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Product Title Text'),
                        'name' => 'FMM_STM_PRODUCT_TITLE_TXT',
                        'desc' => $this->l('Click on below tags to add them.') . $this->html,
                    ],
                    [
                        'type' => $type,
                        'class' => 't',
                        'is_bool' => true,
                        'label' => $this->l('Enable Product Description:'),
                        'name' => 'FMM_STM_STATE_PRODUCT_DESC',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Product Description Text'),
                        'name' => 'FMM_STM_PRODUCT_DESC_TXT',
                        'desc' => $this->l('Click on below tags to add them.') . $this->html,
                    ],
                    [
                        'type' => $type,
                        'class' => 't',
                        'is_bool' => true,
                        'label' => $this->l('Enable Product Tags:'),
                        'name' => 'FMM_STM_STATE_PRODUCT_TAGS',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Product Meta Tags'),
                        'name' => 'FMM_STM_PRODUCT_TAGS_TXT',
                        'desc' => $this->l('Click on below tags to add them.') . $this->html,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSeoMetaTagsManager';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=meta&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesMeta(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form,
            $fields_form_ii,
            $fields_form_iii,
            $fields_form_iv,
        ]
        );
    }

    public function getConfigFieldsValuesMeta()
    {
        $fields = [];
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $fields['FMM_STM_HOME_TITLE_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_HOME_TITLE_TXT_' . $lang['id_lang'], Configuration::get('FMM_STM_HOME_TITLE_TXT', $lang['id_lang']));
            $fields['FMM_STM_HOME_DESC_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_HOME_DESC_TXT_' . $lang['id_lang'], Configuration::get('FMM_STM_HOME_DESC_TXT', $lang['id_lang']));
            $fields['FMM_STM_HOME_TAGS_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_HOME_TAGS_TXT_' . $lang['id_lang'], Configuration::get('FMM_STM_HOME_TAGS_TXT', $lang['id_lang']));
            $fields['FMM_STM_CMS_TITLE_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_CMS_TITLE_TXT_' . $lang['id_lang'], Configuration::get('FMM_STM_CMS_TITLE_TXT', $lang['id_lang']));
            $fields['FMM_STM_CMS_DESC_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_CMS_DESC_TXT_' . $lang['id_lang'], Configuration::get('FMM_STM_CMS_DESC_TXT', $lang['id_lang']));
            $fields['FMM_STM_CMS_TAGS_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_CMS_TAGS_TXT_' . $lang['id_lang'], Configuration::get('FMM_STM_CMS_TAGS_TXT', $lang['id_lang']));
            $fields['FMM_STM_CATEGORY_TITLE_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_CATEGORY_TITLE_TXT_' . $lang['id_lang'], Configuration::get('FMM_STM_CATEGORY_TITLE_TXT', $lang['id_lang']));
            $fields['FMM_STM_CATEGORY_DESC_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_CATEGORY_DESC_TXT_' . $lang['id_lang'], Configuration::get('FMM_STM_CATEGORY_DESC_TXT', $lang['id_lang']));
            $fields['FMM_STM_CATEGORY_TAGS_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_CATEGORY_TAGS_TXT_' . $lang['id_lang'], Configuration::get('FMM_STM_CATEGORY_TAGS_TXT', $lang['id_lang']));
            $fields['FMM_STM_PRODUCT_TITLE_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_PRODUCT_TITLE_TXT_' . $lang['id_lang'], Configuration::get('FMM_STM_PRODUCT_TITLE_TXT', $lang['id_lang']));
            $fields['FMM_STM_PRODUCT_DESC_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_PRODUCT_DESC_TXT_' . $lang['id_lang'], Configuration::get('FMM_STM_PRODUCT_DESC_TXT', $lang['id_lang']));
            $fields['FMM_STM_PRODUCT_TAGS_TXT'][$lang['id_lang']] = Tools::getValue('FMM_STM_PRODUCT_TAGS_TXT_' . $lang['id_lang'], Configuration::get('FMM_STM_PRODUCT_TAGS_TXT', $lang['id_lang']));
        }
        $fields['FMM_STM_STATE_HOME_TITLE'] = (int) Configuration::get('FMM_STM_STATE_HOME_TITLE');
        $fields['FMM_STM_STATE_HOME_DESC'] = (int) Configuration::get('FMM_STM_STATE_HOME_DESC');
        $fields['FMM_STM_STATE_HOME_TAGS'] = (int) Configuration::get('FMM_STM_STATE_HOME_TAGS');
        $fields['FMM_STM_STATE_CMS_TITLE'] = (int) Configuration::get('FMM_STM_STATE_CMS_TITLE');
        $fields['FMM_STM_STATE_CMS_DESC'] = (int) Configuration::get('FMM_STM_STATE_CMS_DESC');
        $fields['FMM_STM_STATE_CMS_TAGS'] = (int) Configuration::get('FMM_STM_STATE_CMS_TAGS');
        $fields['FMM_STM_STATE_CATEGORY_TITLE'] = (int) Configuration::get('FMM_STM_STATE_CATEGORY_TITLE');
        $fields['FMM_STM_STATE_CATEGORY_DESC'] = (int) Configuration::get('FMM_STM_STATE_CATEGORY_DESC');
        $fields['FMM_STM_STATE_CATEGORY_TAGS'] = (int) Configuration::get('FMM_STM_STATE_CATEGORY_TAGS');
        $fields['FMM_STM_STATE_PRODUCT_TITLE'] = (int) Configuration::get('FMM_STM_STATE_PRODUCT_TITLE');
        $fields['FMM_STM_STATE_PRODUCT_DESC'] = (int) Configuration::get('FMM_STM_STATE_PRODUCT_DESC');
        $fields['FMM_STM_STATE_PRODUCT_TAGS'] = (int) Configuration::get('FMM_STM_STATE_PRODUCT_TAGS');

        return $fields;
    }
	
	public function robotsForm()
    {
		$fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Robots.txt Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Robots Content'),
                        'name' => 'FMM_PRETTY_ROBOTS',
                        'required' => false,
						'class' => 'fmm_pretty_robots_textblk'
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
				'buttons' => [
					'cancel' => [
						'title' => $this->l('Generate New File'),
						'class' => 'btn btn-default fmm_pretty_urls_btn_red',
						'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules').'&genrobots=1&tab_module=robots',
					],
				],
            ],
        ];
		$helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name.'Robots';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=robots&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }
	
	
	public function sitemapForm()
    {
		$sitemap = [
            [
                'name' => $this->l('All URLs'),
                'value' => 0,
            ],
            [
                'name' => $this->l('Only CMS Pages URL'),
                'value' => 1,
            ],
            [
                'name' => $this->l('Only Product Pages URL'),
                'value' => 2,
            ],
            [
                'name' => $this->l('Only Category Pages URL'),
                'value' => 3,
            ],
        ];
		$shops = [];
		$shop_collection = Shop::getShops(true);
        foreach ($shop_collection as $shop) {
            $each = [
                'name' => $shop['name'],
                'value' => $shop['id_shop'],
            ];
            array_push($shops, $each);
        }
		$frequency = [
            [
                'name' => $this->l('always'),
                'value' => 'always',
            ],
            [
                'name' => $this->l('hourly'),
                'value' => 'hourly',
            ],
            [
                'name' => $this->l('daily'),
                'value' => 'daily',
            ],
            [
                'name' => $this->l('weekly'),
                'value' => 'weekly',
            ],
            [
                'name' => $this->l('monthly'),
                'value' => 'monthly',
            ],
            [
                'name' => $this->l('yearly'),
                'value' => 'yearly',
            ],
            [
                'name' => $this->l('never'),
                'value' => 'never',
            ],
        ];
		$type_of_links = [
            [
                'name' => $this->l('HTTPS'),
                'value' => 1,
            ],
            [
                'name' => $this->l('HTTP'),
                'value' => 0,
            ],
        ];
		$fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Generate New Sitemap'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'select',
                        'label' => $this->l('Sitemap to generate:'),
                        'name' => 'sitemap',
						'options' => array(
                            'query' => $sitemap,
                            'id' => 'value',
                            'name' => 'name',
                        ),
                    ],
					[
                        'type' => 'select',
                        'label' => $this->l('Select Shop:'),
                        'name' => 'fmm_sitemap_shop',
						'options' => array(
                            'query' => $shops,
                            'id' => 'value',
                            'name' => 'name',
                        ),
                    ],
					[
                        'type' => 'select',
                        'label' => $this->l('Update Frequency:'),
                        'name' => 'gsitemap_frequency',
						'options' => array(
                            'query' => $frequency,
                            'id' => 'value',
                            'name' => 'name',
                        ),
                    ],
					[
                        'type' => 'select',
                        'label' => $this->l('Generate Links with:'),
                        'name' => 'https',
						'options' => array(
                            'query' => $type_of_links,
                            'id' => 'value',
                            'name' => 'name',
                        ),
                    ],
					[
                        'type' => 'opts',
                        'label' => $this->l('Select Options:'),
						'name' => 'opts'
                    ],
					[
                        'type' => 'pages',
                        'label' => $this->l('Select Pages you DO NOT want in sitemap.'),
						'name' => 'pages'
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
		$helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name.'Sitemap';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=sitemap&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
		$gsitemap_last_export = Configuration::get('GSITEMAP_LAST_EXPORT');
        $cdn_use = (int) Configuration::get('GSITEMAP_CDN_OPT');
        $cdn_use_html = ($cdn_use > 0) ? ' checked="checked"' : '';
        $ping_use = (int) Configuration::get('GSITEMAP_PING_SE_OPT');
        $ping_se_use_html = ($ping_use > 0) ? ' checked="checked"' : '';
        $prefix = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) ? 'https://' : 'http://';
        $gsitemap_store_url = $prefix . Tools::getShopDomain(false, true) . __PS_BASE_URI__;
        $store_metas = Meta::getMetasByIdLang((int) $this->context->cookie->id_lang);
        $gsitemap_links = $this->getSitemapRaw();
        $helper->tpl_vars = [
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];
		$this->context->smarty->assign(
            [
                'gsitemap_links' => $gsitemap_links,
                'gsitemap_store_url' => $gsitemap_store_url,
                'gsitemap_store_url_shopid' => (int) $this->context->shop->id,
                'advanceseo_token' => Configuration::get('FMM_PRETTY_TOKEN'),
                'gsitemap_last_export' => $gsitemap_last_export,
                'store_metas' => $store_metas,
                'cdn_use' => $cdn_use,
                'cdn_use_html' => $cdn_use_html,
                'ping_use' => $ping_use,
                'ping_se_use_html' => $ping_se_use_html,
            ]
        );
		$_html = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'prettyurls/views/templates/admin/sitemaps.tpl');
        return $_html.$helper->generateForm([$fields_form]);
    }
	
	public function redirectsForm()
    {
        $redirect_types = [
            ['value' => 0, 'name' => $this->l('Default')],
            ['value' => 1, 'name' => $this->l('Home Page')],
            ['value' => 2, 'name' => $this->l('CMS Page')],
        ];
        $redirect_trigger = [
            ['value' => 0, 'name' => $this->l('.htacces File')],
            ['value' => 1, 'name' => $this->l('Controller')],
        ];

        $fields_form = [
            'form' => [
                'tinymce' => false,
                'legend' => [
                    'title' => $this->l('URL Redirects Settings'),
                    'icon' => 'icon-cog',
                ],
                'input' => [
                    [
                        'type' => 'select',
                        'label' => $this->l('Redirect Trigger?:'),
                        'name' => 'REDIRECT_TRIGGER',
                        'desc' => $this->l(
                            'Controller redirects are flat and does not has
                            type e.g(301,302) on other hand htaccess
                            redirects might not be supported by all servers.'
                        ),
                        'options' => [
                            'query' => $redirect_trigger,
                            'id' => 'value',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Redirect 404 :'),
                        'name' => 'REDIRECT_404',
                        'desc' => $this->l('Redirect 404 (Page Not Found) to selected page.'),
                        'options' => [
                            'query' => $redirect_types,
                            'id' => 'value',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'cms',
                        'name' => 'cms',
                        'label' => $this->l('CMS Pages'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get(
            'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
        ) ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = [];
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitRedirects';
        $helper->currentIndex = $this->context->link->getAdminLink(
            'AdminModules',
            false
        ) . '&configure=' . $this->name .
        '&tab_module=redirects&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
		$url_redirects = $this->context->link->getAdminLink('AdminManageRedirect', true);
		$url_redirects_404 = $this->context->link->getAdminLink('AdminManageAll404Pages', true);
		$url_redirects_reports = $this->context->link->getAdminLink('AdminManage404Report', true);
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValuesRedirects(),
            'cms_pages' => CMS::getLinks((int) $this->context->language->id),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'version' => _PS_VERSION_,
            'token' => Tools::getAdminTokenLite('AdminModules'),
			'manage_link_urlredirects' => $url_redirects,
			'manage_link_urlredirects_404' => $url_redirects_404,
			'manage_link_urlredirects_reports' => $url_redirects_reports
        ];

        return $helper->generateForm([$fields_form]);
    }
	
	public function getConfigFieldsValuesRedirects()
    {
        $return = [
            'REDIRECT_404' => (int) Tools::getValue(
                'REDIRECT_404',
                (int) Configuration::get('REDIRECT_404')
            ),
            'REDIRECT_TRIGGER' => (int) Tools::getValue(
                'REDIRECT_TRIGGER',
                (int) Configuration::get('REDIRECT_TRIGGER')
            ),
            'REDIRECT_404_CMS' => (int) Configuration::get('REDIRECT_404_CMS'),
        ];

        return $return;
    }
	
	public function graphsForm()
    {
		$image_url = '';
        $image_fb_url = '';
        $image_name = Configuration::get('twitterimage', null);
        $image_fb_name = Configuration::get('fmm_fb_image', null);

        if (!empty($image_name)) {
            $imgPathUrl = $image_name;

            $image = _PS_IMG_DIR_ . $imgPathUrl;

            $image_url = ImageManager::thumbnail(
                $image,
                $this->table . '_' . (string) $image_name,
                250,
                true,
                true
            );
        }
        if (!empty($image_fb_name)) {
            $imgPathUrl = $image_fb_name;

            $image = _PS_IMG_DIR_ . $imgPathUrl;

            $image_fb_url = ImageManager::thumbnail(
                $image,
                $this->table . '_' . (string) $image_fb_name,
                250,
                true,
                true
            );
        }
        $status = [
            'type' => 'switch',

            'label' => $this->l('Status?'),

            'name' => 'twittercard_status',

            'required' => false,

            'class' => 't',

            'is_bool' => true,

            'values' => [
                [
                    'id' => 'active_on',

                    'value' => 1,

                    'label' => $this->l('Yes'),
                ],

                [
                    'id' => 'active_off',

                    'value' => 0,

                    'label' => $this->l('No'),
                ],
            ],
        ];

        $status_i = [
            'type' => 'switch',

            'label' => $this->l('Status?'),

            'name' => 'facebookgraph_status',

            'required' => false,

            'class' => 't',

            'is_bool' => true,

            'values' => [
                [
                    'id' => 'active_on',

                    'value' => 1,

                    'label' => $this->l('Yes'),
                ],
                [
                    'id' => 'active_off',

                    'value' => 0,

                    'label' => $this->l('No'),
                ],
            ],
        ];
		$help = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'prettyurls/views/templates/hook/graphs_help.tpl');
		$tags = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'prettyurls/views/templates/admin/help_tags.tpl');
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Twitter Summary card'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    $status,
                    [
                        'type' => 'radio',
                        'label' => $this->l('Twitter card type'),
                        'name' => 'twittercardtype',
                        'class' => 't',
                        'required' => true,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'summary',
                                'value' => 0,
                                'label' => $this->l('summary'),
                            ],
                            [
                                'id' => 'summary_large_image',
                                'value' => 1,
                                'label' => $this->l('Summary large image'),
                            ],
                        ],
                    ],
                    [
                        'label' => $this->l('Twitter:site'),
                        'type' => 'text',
                        'name' => 'twittersite',
                        'col' => '4',
                        'required' => true,
                        'desc' => $this->l('@username of website. Either twitter:site or twitter:site:id is required.'),
                    ],
                    [
                        'label' => $this->l('Twitter:Title'),
                        'type' => 'text',
                        'name' => 'twittertitle',
                        'col' => '4',
                        'maxlength' => 70,
                        'lang' => true,
                        'required' => true,
                        'desc' => $this->l('Title of content (max 70 characters').$tags,
                    ],
                    [
                        'label' => $this->l('Twitter:Description'),
                        'type' => 'textarea',
                        'name' => 'twitterdescription',
                        'col' => '4',
                        'maxlength' => 200,
                        'lang' => true,
                        'required' => true,
                        'desc' => $this->l('Description of content maximum 200 characters').$tags,
                    ],

                    [
                        'type' => 'file',
                        'label' => $this->l('Twitter:Image'),
                        'name' => 'twitterimage',
                        'image' => $image_url ? $image_url : false,
                        'display_image' => true,
                        'required' => true,
                        'value' => true,
                        'desc' => $this->l('* Images must be less than 5MB in size.
                                *JPG, PNG, WEBP and GIF formats are supported.
                                *image minimum dimensions of 144x144 or maximum of 4096x4096 pixels '),
                    ],
                    [
                        'label' => $this->l('Twitter:Image:Alt'),
                        'type' => 'textarea',
                        'name' => 'twitterimagealt',
                        'col' => '4',
                        'lang' => true,
                        'required' => true,
                        'desc' => $this->l('A text description of the image maximum 420  characters').$tags,
                    ],
                ],
				'warning' => $help,
            ],
        ];
        $fields_form_i = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Facebook graph'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    $status_i,

                    [
                        'label' => $this->l('Facebook:Title'),
                        'type' => 'text',
                        'name' => 'fmm_fb_title',
                        'col' => '4',
                        'maxlength' => 70,
                        'lang' => true,
                        'required' => true,
                        'desc' => $this->l('Title of content (max 70 characters').$tags,
                    ],
                    [
                        'label' => $this->l('Facebook:Description'),
                        'type' => 'textarea',
                        'name' => 'fmm_fb_descript',
                        'col' => '4',
                        'maxlength' => 200,
                        'lang' => true,
                        'required' => true,
                        'desc' => $this->l('Description of content maximum 200 characters').$tags,
                    ],

                    [
                        'type' => 'file',
                        'label' => $this->l('Facebook:Image'),
                        'name' => 'fmm_fb_image',
                        'image' => $image_fb_url ? $image_fb_url : false,
                        'display_image' => true,
                        'required' => true,
                        'value' => true,
                        'desc' => $this->l('* *image minimum dimensions greater then 100x100  pixels '),
                    ],
                ],
				'warning' => $help,
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
		$helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name.'Graphs';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=graphs&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFormValuesGraphs(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form, $fields_form_i]);
    }
	
	protected function getConfigFormValuesGraphs()
    {
        $languages = Language::getLanguages(false);
        $fields = [];
        foreach ($languages as $lang) {
            $fields['twittertitle'][$lang['id_lang']] = Tools::getValue(
                'twittertitle_' . $lang['id_lang'],
                Configuration::get(
                    'twittertitle',
                    $lang['id_lang']
                )
            );
            $fields['twitterdescription'][$lang['id_lang']] = Tools::getValue(
                'twitterdescription_' . $lang['id_lang'],
                Configuration::get(
                    'twitterdescription',
                    $lang['id_lang']
                )
            );
            $fields['twitterimagealt'][$lang['id_lang']] = Tools::getValue(
                'twitterimagealt_' . $lang['id_lang'],
                Configuration::get(
                    'twitterimagealt',
                    $lang['id_lang']
                )
            );
            $fields['fmm_fb_title'][$lang['id_lang']] = Tools::getValue(
                'fmm_fb_title_' . $lang['id_lang'],
                Configuration::get(
                    'fmm_fb_title',
                    $lang['id_lang']
                )
            );
            $fields['fmm_fb_descript'][$lang['id_lang']] = Tools::getValue(
                'fmm_fb_descript_' . $lang['id_lang'],
                Configuration::get(
                    'fmm_fb_descript',
                    $lang['id_lang']
                )
            );
        }
        $fields['twittersite'] = (string) Configuration::get('twittersite');
        $fields['twittercard_status'] = (string) Configuration::get('twittercard_status');
        $fields['facebookgraph_status'] = (string) Configuration::get('facebookgraph_status');
        $fields['twittercardtype'] = (int) Configuration::get('twittercardtype');

        return $fields;
    }
	
	public function UpdateRobotsFile($robots_txt_content)
    {
        if (!$write = @fopen($this->robots_file, 'w')) {
            $this->errors[] = sprintf(Tools::displayError('Cannot write into file: %s. Please check write permissions.'), $this->robots_file);
        } else {
            fwrite($write, $robots_txt_content);
        }

        fclose($write);
    }
	
	public function getRobotsContent()
    {
        $tab = [];

        // Directories
        $tab['Directories'] = ['classes/', 'config/', 'download/', 'mails/', 'modules/', 'translations/', 'tools/'];

        // Files
        $disallow_controllers = [
            'addresses', 'address', 'authentication', 'cart', 'discount', 'footer',
            'get-file', 'header', 'history', 'identity', 'images.inc', 'init', 'my-account', 'order', 'order-opc',
            'order-slip', 'order-detail', 'order-follow', 'order-return', 'order-confirmation', 'pagination', 'password',
            'pdf-invoice', 'pdf-order-return', 'pdf-order-slip', 'product-sort', 'search', 'statistics', 'attachment', 'guest-tracking',
        ];

        // Rewrite files
        $tab['Files'] = [];
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $sql = 'SELECT ml.url_rewrite, l.iso_code
					FROM ' . _DB_PREFIX_ . 'meta m
					INNER JOIN ' . _DB_PREFIX_ . 'meta_lang ml ON ml.id_meta = m.id_meta
					INNER JOIN ' . _DB_PREFIX_ . 'lang l ON l.id_lang = ml.id_lang
					WHERE l.active = 1 AND m.page IN (\'' . implode('\', \'', $disallow_controllers) . '\')';
            if ($results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
                foreach ($results as $row) {
                    $tab['Files'][$row['iso_code']][] = $row['url_rewrite'];
                }
            }
        }

        $tab['GB'] = [
            '?orderby=', '?orderway=', '?tag=', '?id_currency=', '?search_query=', '?back=', '?n=',
            '&orderby=', '&orderway=', '&tag=', '&id_currency=', '&search_query=', '&back=', '&n=',
        ];

        foreach ($disallow_controllers as $controller) {
            $tab['GB'][] = 'controller=' . $controller;
        }

        return $tab;
    }
	
	public function generateRobotsFile()
    {
        $this->robots_data = $this->getRobotsContent();
        if (!$write_fd = @fopen($this->robots_file, 'w')) {
            $this->errors[] = sprintf(Tools::displayError('Cannot write into file: %s. Please check write permissions.'), $this->robots_file);
        } else {
            fwrite($write_fd, "# robots.txt automaticaly generated by PrestaShop e-commerce open-source solution\n");
            fwrite($write_fd, "# http://www.prestashop.com - http://www.prestashop.com/forums\n");
            fwrite($write_fd, "# This file is to prevent the crawling and indexing of certain parts\n");
            fwrite($write_fd, "# of your site by web crawlers and spiders run by sites like Yahoo!\n");
            fwrite($write_fd, "# and Google. By telling these \"robots\" where not to go on your site,\n");
            fwrite($write_fd, "# you save bandwidth and server resources.\n");
            fwrite($write_fd, "# For more information about the robots.txt standard, see:\n");
            fwrite($write_fd, "# http://www.robotstxt.org/robotstxt.html\n");

            // User-Agent
            fwrite($write_fd, "User-agent: *\n");

            // Private pages
            if (count($this->robots_data['GB'])) {
                fwrite($write_fd, "# Private pages\n");
                foreach ($this->robots_data['GB'] as $gb) {
                    fwrite($write_fd, 'Disallow: /*' . $gb . "\n");
                }
            }

            // Directories
            if (count($this->robots_data['Directories'])) {
                fwrite($write_fd, "# Directories\n");
                foreach ($this->robots_data['Directories'] as $dir) {
                    fwrite($write_fd, 'Disallow: */' . $dir . "\n");
                }
            }

            // Files
            if (count($this->robots_data['Files'])) {
                $languages = Language::getLanguages();
                fwrite($write_fd, "# Files\n");
                foreach ($this->robots_data['Files'] as $iso_code => $files) {
                    foreach ($files as $file) {
                        if (count($languages) > 1) {
                            fwrite($write_fd, 'Disallow: /*' . $iso_code . '/' . $file . "\n");
                        } else {
                            fwrite($write_fd, 'Disallow: /' . $file . "\n");
                        }
                    }
                }
            }

            // Sitemap
            if (file_exists($this->sitemap) && filesize($this->sitemap)) {
                fwrite($write_fd, "# Sitemap\n");
                $sitemap_filename = basename($this->sitemap);
                fwrite($write_fd, 'Sitemap: ' . (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . __PS_BASE_URI__ . $sitemap_filename . "\n");
            }

            fclose($write_fd);
        }
    }
	
	public function getSitemapRaw()
    {
        return Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'gsitemap_sitemap`');
    }
	
	protected function normalizeDirectory($directory)
    {
        $last = $directory[Tools::strlen($directory) - 1];

        if (in_array($last, ['/', '\\'])) {
            $directory[Tools::strlen($directory) - 1] = DIRECTORY_SEPARATOR;

            return $directory;
        }

        $directory .= DIRECTORY_SEPARATOR;

        return $directory;
    }
	
	public function emptySitemap($id_shop = 0)
    {
        if (!isset($this->context)) {
            $this->context = new Context();
        }
        if ($id_shop != 0) {
            $this->context->shop = new Shop((int) $id_shop);
        }
        $links = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'gsitemap_sitemap` WHERE id_shop = ' . (int) $this->context->shop->id);
        if ($links) {
            foreach ($links as $link) {
                @unlink($this->normalizeDirectory(_PS_ROOT_DIR_) . $link['link']);
            }

            return Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'gsitemap_sitemap` WHERE id_shop = ' . (int) $this->context->shop->id);
        }

        return true;
    }

    private function _getHomeLink(&$link_sitemap, $lang, &$index, &$i)
    {
        if (Configuration::get('GSITEMAP_URL_SCHEME') && Configuration::get('GSITEMAP_URL_SCHEME') > 0) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        return $this->_addLinkToSitemap(
            $link_sitemap, [
                'type' => 'home',
                'page' => 'home',
                'link' => $protocol . Tools::getShopDomainSsl(false) . $this->context->shop->getBaseURI() . (method_exists('Language', 'isMultiLanguageActivated') ? Language::isMultiLanguageActivated() ? $lang['iso_code'] . '/' : '' : ''),
                'image' => false,
            ], $lang['iso_code'], $index, $i, -1
        );
    }

    private function _getMetaLink(&$link_sitemap, $lang, &$index, &$i, $id_meta = 0)
    {
        if (method_exists('ShopUrl', 'resetMainDomainCache')) {
            ShopUrl::resetMainDomainCache();
        }

        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            $metas = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'meta` WHERE `configurable` > 0 AND `id_meta` >= ' . (int) $id_meta . ' ORDER BY `id_meta` ASC');
        } else {
            $metas = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'meta` WHERE `id_meta` >= ' . (int) $id_meta . ' ORDER BY `id_meta` ASC');
        }
        foreach ($metas as $meta) {
            $url = '';
            if (!in_array($meta['id_meta'], explode(',', Configuration::get('GSITEMAP_DISABLE_LINKS')))) {
                $url_rewrite = Db::getInstance()->getValue('SELECT url_rewrite, id_shop FROM `' . _DB_PREFIX_ . 'meta_lang` WHERE `id_meta` = ' . (int) $meta['id_meta'] . ' AND `id_shop` =' . (int) $this->context->shop->id . ' AND `id_lang` = ' . (int) $lang['id_lang']);
                Dispatcher::getInstance()->addRoute($meta['page'], isset($url_rewrite) ? $url_rewrite : $meta['page'], $meta['page'], $lang['id_lang']);
                $uri_path = Dispatcher::getInstance()->createUrl($meta['page'], $lang['id_lang'], [], (bool) Configuration::get('PS_REWRITING_SETTINGS'));
                $url .= Tools::getShopDomainSsl(true) . (($this->context->shop->virtual_uri) ? __PS_BASE_URI__ . $this->context->shop->virtual_uri : __PS_BASE_URI__) . (Language::isMultiLanguageActivated() ? $lang['iso_code'] . '/' : '') . ltrim($uri_path, '/');
                if (Configuration::get('GSITEMAP_URL_SCHEME') <= 0) {
                    $url = str_replace('https', 'http', $url);
                } elseif (Configuration::get('GSITEMAP_URL_SCHEME') > 0 && strpos($url, 'https') <= 0) {
                    $url = str_replace('http', 'https', $url);
                }
                if (!$this->_addLinkToSitemap(
                    $link_sitemap, [
                        'type' => 'meta',
                        'page' => $meta['page'],
                        'link' => $url,
                        'image' => false,
                    ], $lang['iso_code'], $index, $i, $meta['id_meta']
                )) {
                    return false;
                }
            }
        }

        return true;
    }

    private function _getProductLink(&$link_sitemap, $lang, &$index, &$i, $id_product = 0)
    {
        $link = new Link();
        if (method_exists('ShopUrl', 'resetMainDomainCache')) {
            ShopUrl::resetMainDomainCache();
        }

        $products_id = Db::getInstance()->executeS('SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product_shop` WHERE `id_product` >= ' . (int) $id_product . ' AND `active` = 1 AND `id_shop`=' . $this->context->shop->id . ' ORDER BY `id_product` ASC');

        foreach ($products_id as $product_id) {
            $product = new Product((int) $product_id['id_product'], false, (int) $lang['id_lang']);

            $url = $link->getProductLink($product, $product->link_rewrite, htmlspecialchars(strip_tags($product->category)), $product->ean13, (int) $lang['id_lang'], (int) $this->context->shop->id, 0, true);

            $id_image = Product::getCover((int) $product_id['id_product']);
            if (isset($id_image['id_image'])) {
                if ($this->newpsversion) {
                    $img_type = ImageType::getFormattedName('large');
                } else {
                    $img_type = ImageType::getFormatedName('large');
                }
                $image_link = $this->context->link->getImageLink($product->link_rewrite, $product->id . '-' . (int) $id_image['id_image'], $img_type);
                $image_link = (!in_array(rtrim(Context::getContext()->shop->virtual_uri, '/'), explode('/', $image_link))) ? str_replace(
                    [
                        'https',
                        Context::getContext()->shop->domain . Context::getContext()->shop->physical_uri,
                    ], [
                        'http',
                        Context::getContext()->shop->domain . Context::getContext()->shop->physical_uri . Context::getContext()->shop->virtual_uri,
                    ], $image_link
                ) : $image_link;
            }
            $file_headers = (Configuration::get('GSITEMAP_CHECK_IMAGE_FILE')) ? @get_headers($image_link) : true;
            $image_product = [];
            if (Configuration::get('GSITEMAP_URL_SCHEME') <= 0) {
                $image_link = str_replace('https', 'http', $image_link);
                $url = str_replace('https', 'http', $url);
            } elseif (Configuration::get('GSITEMAP_URL_SCHEME') > 0 && strpos($image_link, 'https') <= 0) {
                $image_link = str_replace('http', 'https', $image_link);
            }
            if (Configuration::get('GSITEMAP_URL_SCHEME') > 0 && strpos($url, 'https') <= 0) {
                $url = str_replace('http', 'https', $url);
            }
            if (isset($image_link) && ($file_headers === true)) {
                $image_product = [
                    'title_img' => htmlspecialchars(strip_tags($product->name)),
                    'caption' => htmlspecialchars(strip_tags($product->description_short)),
                    'link' => $image_link,
                ];
            }
            if (!$this->_addLinkToSitemap(
                $link_sitemap, [
                    'type' => 'product',
                    'page' => 'product',
                    'lastmod' => $product->date_upd,
                    'link' => $url,
                    'image' => $image_product,
                ], $lang['iso_code'], $index, $i, $product_id['id_product']
            )) {
                return false;
            }

            unset($image_link);
        }

        return true;
    }

    private function _getCategoryLink(&$link_sitemap, $lang, &$index, &$i, $id_category = 0)
    {
        $link = new Link();
        if (method_exists('ShopUrl', 'resetMainDomainCache')) {
            ShopUrl::resetMainDomainCache();
        }

        $categories_id = Db::getInstance()->ExecuteS(
            'SELECT c.id_category FROM `' . _DB_PREFIX_ . 'category` c
                INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON c.`id_category` = cs.`id_category`
                WHERE c.`id_category` >= ' . (int) $id_category . ' AND c.`active` = 1 AND c.`id_category` != 1 AND c.id_parent > 0 AND c.`id_category` > 0 AND cs.`id_shop` = ' . (int) $this->context->shop->id . ' ORDER BY c.`id_category` ASC'
        );

        foreach ($categories_id as $category_id) {
            $category = new Category((int) $category_id['id_category'], (int) $lang['id_lang']);
            $url = $link->getCategoryLink($category, urlencode($category->link_rewrite), (int) $lang['id_lang']);

            if ($category->id_image) {
                if ($this->newpsversion) {
                    $img_type = ImageType::getFormattedName('category');
                } else {
                    $img_type = ImageType::getFormatedName('category');
                }
                $image_link = $this->context->link->getCatImageLink($category->link_rewrite, (int) $category->id_image, $img_type);
                $image_link = (!in_array(rtrim(Context::getContext()->shop->virtual_uri, '/'), explode('/', $image_link))) ? str_replace(
                    [
                        'https',
                        Context::getContext()->shop->domain . Context::getContext()->shop->physical_uri,
                    ], [
                        'http',
                        Context::getContext()->shop->domain . Context::getContext()->shop->physical_uri . Context::getContext()->shop->virtual_uri,
                    ], $image_link
                ) : $image_link;
            }
            $file_headers = (Configuration::get('GSITEMAP_CHECK_IMAGE_FILE')) ? @get_headers($image_link) : true;
            if (Configuration::get('GSITEMAP_URL_SCHEME') <= 0) {
                if (isset($image_link)) {
                    $image_link = str_replace('https', 'http', $image_link);
                }
                $url = str_replace('https', 'http', $url);
            } elseif (Configuration::get('GSITEMAP_URL_SCHEME') > 0 && isset($image_link)) {
                if (strpos($image_link, 'https') <= 0) {
                    $image_link = str_replace('http', 'https', $image_link);
                }
            }
            if (Configuration::get('GSITEMAP_URL_SCHEME') > 0 && strpos($url, 'https') <= 0) {
                $url = str_replace('http', 'https', $url);
            }
            $image_category = [];
            if (isset($image_link) && ($file_headers === true)) {
                $image_category = [
                    'title_img' => htmlspecialchars(strip_tags($category->name)),
                    'link' => $image_link,
                ];
            }
            if (!$this->_addLinkToSitemap(
                $link_sitemap, [
                    'type' => 'category',
                    'page' => 'category',
                    'lastmod' => $category->date_upd,
                    'link' => $url,
                    'image' => $image_category,
                ], $lang['iso_code'], $index, $i, (int) $category_id['id_category']
            )) {
                return false;
            }

            unset($image_link);
        }

        return true;
    }

    private function _getManufacturerLink(&$link_sitemap, $lang, &$index, &$i, $id_manufacturer = 0)
    {
        $link = new Link();
        if (method_exists('ShopUrl', 'resetMainDomainCache')) {
            ShopUrl::resetMainDomainCache();
        }
        $manufacturers_id = Db::getInstance()->ExecuteS(
            'SELECT m.`id_manufacturer` FROM `' . _DB_PREFIX_ . 'manufacturer` m
            INNER JOIN `' . _DB_PREFIX_ . 'manufacturer_lang` ml on m.`id_manufacturer` = ml.`id_manufacturer`' .
            ($this->tableColumnExists(_DB_PREFIX_ . 'manufacturer_shop') ? ' INNER JOIN `' . _DB_PREFIX_ . 'manufacturer_shop` ms ON m.`id_manufacturer` = ms.`id_manufacturer` ' : '') .
            ' WHERE m.`active` = 1  AND m.`id_manufacturer` >= ' . (int) $id_manufacturer .
            ($this->tableColumnExists(_DB_PREFIX_ . 'manufacturer_shop') ? ' AND ms.`id_shop` = ' . (int) $this->context->shop->id : '') .
            ' AND ml.`id_lang` = ' . (int) $lang['id_lang'] .
            ' ORDER BY m.`id_manufacturer` ASC'
        );
        foreach ($manufacturers_id as $manufacturer_id) {
            $manufacturer = new Manufacturer((int) $manufacturer_id['id_manufacturer'], $lang['id_lang']);
            $url = $link->getManufacturerLink($manufacturer, $manufacturer->link_rewrite, $lang['id_lang']);
            if ($this->newpsversion) {
                $img_type = ImageType::getFormattedName('medium');
            } else {
                $img_type = ImageType::getFormatedName('medium');
            }
            $image_link = 'http' . (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE') ? 's' : '') . '://' . Tools::getMediaServer(_THEME_MANU_DIR_) . _THEME_MANU_DIR_ . ((!file_exists(_PS_MANU_IMG_DIR_ . '/' . (int) $manufacturer->id . '-' . $img_type . '.jpg')) ? $lang['iso_code'] . '-default' : (int) $manufacturer->id) . '-' . $img_type . '.jpg';
            $image_link = (!in_array(rtrim(Context::getContext()->shop->virtual_uri, '/'), explode('/', $image_link))) ? str_replace(
                [
                    'https',
                    Context::getContext()->shop->domain . Context::getContext()->shop->physical_uri,
                ], [
                    'http',
                    Context::getContext()->shop->domain . Context::getContext()->shop->physical_uri . Context::getContext()->shop->virtual_uri,
                ], $image_link
            ) : $image_link;

            $file_headers = (Configuration::get('GSITEMAP_CHECK_IMAGE_FILE')) ? @get_headers($image_link) : true;
            $manifacturer_image = [];
            if (Configuration::get('GSITEMAP_URL_SCHEME') <= 0) {
                if (isset($image_link)) {
                    $image_link = str_replace('https', 'http', $image_link);
                }
                $url = str_replace('https', 'http', $url);
            } elseif (Configuration::get('GSITEMAP_URL_SCHEME') > 0 && isset($image_link)) {
                if (strpos($image_link, 'https') <= 0) {
                    $image_link = str_replace('http', 'https', $image_link);
                }
            }
            if (Configuration::get('GSITEMAP_URL_SCHEME') > 0 && strpos($url, 'https') <= 0) {
                $url = str_replace('http', 'https', $url);
            }
            if ($file_headers === true) {
                $manifacturer_image = [
                    'title_img' => htmlspecialchars(strip_tags($manufacturer->name)),
                    'caption' => htmlspecialchars(strip_tags($manufacturer->short_description)),
                    'link' => $image_link,
                ];
            }
            if (!$this->_addLinkToSitemap(
                $link_sitemap, [
                    'type' => 'manufacturer',
                    'page' => 'manufacturer',
                    'lastmod' => $manufacturer->date_upd,
                    'link' => $url,
                    'image' => $manifacturer_image,
                ], $lang['iso_code'], $index, $i, $manufacturer_id['id_manufacturer']
            )) {
                return false;
            }
        }

        return true;
    }

    private function _getSupplierLink(&$link_sitemap, $lang, &$index, &$i, $id_supplier = 0)
    {
        $link = new Link();
        if (method_exists('ShopUrl', 'resetMainDomainCache')) {
            ShopUrl::resetMainDomainCache();
        }
        $suppliers_id = Db::getInstance()->ExecuteS(
            'SELECT s.`id_supplier` FROM `' . _DB_PREFIX_ . 'supplier` s
            INNER JOIN `' . _DB_PREFIX_ . 'supplier_lang` sl ON s.`id_supplier` = sl.`id_supplier` ' .
            ($this->tableColumnExists(_DB_PREFIX_ . 'supplier_shop') ? 'INNER JOIN `' . _DB_PREFIX_ . 'supplier_shop` ss ON s.`id_supplier` = ss.`id_supplier`' : '') . '
            WHERE s.`active` = 1 AND s.`id_supplier` >= ' . (int) $id_supplier .
            ($this->tableColumnExists(_DB_PREFIX_ . 'supplier_shop') ? ' AND ss.`id_shop` = ' . (int) $this->context->shop->id : '') . '
            AND sl.`id_lang` = ' . (int) $lang['id_lang'] . '
            ORDER BY s.`id_supplier` ASC'
        );
        foreach ($suppliers_id as $supplier_id) {
            $supplier = new Supplier((int) $supplier_id['id_supplier'], $lang['id_lang']);
            $url = $link->getSupplierLink($supplier, $supplier->link_rewrite, $lang['id_lang']);
            if ($this->newpsversion) {
                $img_type = ImageType::getFormattedName('medium');
            } else {
                $img_type = ImageType::getFormatedName('medium');
            }
            $image_link = 'http://' . Tools::getMediaServer(_THEME_SUP_DIR_) . _THEME_SUP_DIR_ . ((!file_exists(_THEME_SUP_DIR_ . '/' . (int) $supplier->id . '-' . $img_type . '.jpg')) ? $lang['iso_code'] . '-default' : (int) $supplier->id) . '-' . $img_type . '.jpg';
            $image_link = (!in_array(rtrim(Context::getContext()->shop->virtual_uri, '/'), explode('/', $image_link))) ? str_replace(
                [
                    'https',
                    Context::getContext()->shop->domain . Context::getContext()->shop->physical_uri,
                ], [
                    'http',
                    Context::getContext()->shop->domain . Context::getContext()->shop->physical_uri . Context::getContext()->shop->virtual_uri,
                ], $image_link
            ) : $image_link;

            $file_headers = (Configuration::get('GSITEMAP_CHECK_IMAGE_FILE')) ? @get_headers($image_link) : true;
            $supplier_image = [];
            if (Configuration::get('GSITEMAP_URL_SCHEME') <= 0) {
                if (isset($image_link)) {
                    $image_link = str_replace('https', 'http', $image_link);
                }
                $url = str_replace('https', 'http', $url);
            } elseif (Configuration::get('GSITEMAP_URL_SCHEME') > 0 && isset($image_link)) {
                if (strpos($image_link, 'https') <= 0) {
                    $image_link = str_replace('http', 'https', $image_link);
                }
            }
            if (Configuration::get('GSITEMAP_URL_SCHEME') > 0 && strpos($url, 'https') <= 0) {
                $url = str_replace('http', 'https', $url);
            }
            if ($this->newpsversion) {
                $img_type = ImageType::getFormattedName('medium');
            } else {
                $img_type = ImageType::getFormatedName('medium');
            }
            if ($file_headers === true) {
                $supplier_image = [
                    'title_img' => htmlspecialchars(strip_tags($supplier->name)),
                    'link' => 'http' . (Configuration::get('GSITEMAP_URL_SCHEME') ? 's' : '') . '://' . Tools::getMediaServer(_THEME_SUP_DIR_) . _THEME_SUP_DIR_ . ((!file_exists(_THEME_SUP_DIR_ . '/' . (int) $supplier->id . '-' . $img_type . '.jpg')) ? $lang['iso_code'] . '-default' : (int) $supplier->id) . '-' . $img_type . '.jpg',
                ];
            }
            if (!$this->_addLinkToSitemap(
                $link_sitemap, [
                    'type' => 'supplier',
                    'page' => 'supplier',
                    'lastmod' => $supplier->date_upd,
                    'link' => $url,
                    'image' => $supplier_image,
                ], $lang['iso_code'], $index, $i, $supplier_id['id_supplier']
            )) {
                return false;
            }
        }

        return true;
    }

    private function _getCmsLink(&$link_sitemap, $lang, &$index, &$i, $id_cms = 0)
    {
        $link = new Link();
        if (method_exists('ShopUrl', 'resetMainDomainCache')) {
            ShopUrl::resetMainDomainCache();
        }
        $cmss_id = Db::getInstance()->ExecuteS('SELECT c.`id_cms` FROM `' . _DB_PREFIX_ . 'cms` c INNER JOIN `' . _DB_PREFIX_ . 'cms_lang` cl ON c.`id_cms` = cl.`id_cms` ' . 'INNER JOIN `' . _DB_PREFIX_ . 'cms_shop` cs ON c.`id_cms` = cs.`id_cms` ' . 'INNER JOIN `' . _DB_PREFIX_ . 'cms_category` cc ON c.id_cms_category = cc.id_cms_category AND cc.active = 1
            WHERE c.`active` =1 AND c.`indexation` =1 AND c.`id_cms` >= ' . (int) $id_cms . ' AND cs.id_shop = ' . (int) $this->context->shop->id . ' AND cl.`id_lang` = ' . (int) $lang['id_lang'] . ' GROUP BY  c.`id_cms` ORDER BY c.`id_cms` ASC');

        if (is_array($cmss_id)) {
            foreach ($cmss_id as $cms_id) {
                $cms = new CMS((int) $cms_id['id_cms'], $lang['id_lang']);
                $cms->link_rewrite = urlencode(is_array($cms->link_rewrite) ? $cms->link_rewrite[(int) $lang['id_lang']] : $cms->link_rewrite);
                $url = $link->getCMSLink($cms, null, null, $lang['id_lang']);
                if (Configuration::get('GSITEMAP_URL_SCHEME') <= 0) {
                    $url = str_replace('https', 'http', $url);
                } elseif (Configuration::get('GSITEMAP_URL_SCHEME') > 0 && isset($url)) {
                    if (strpos($url, 'https') <= 0) {
                        $url = str_replace('http', 'https', $url);
                    }
                }
                if (!$this->_addLinkToSitemap(
                    $link_sitemap, [
                        'type' => 'cms',
                        'page' => 'cms',
                        'link' => $url,
                        'image' => false,
                    ], $lang['iso_code'], $index, $i, $cms_id['id_cms']
                )) {
                    return false;
                }
            }
        }

        return true;
    }

    private function _getModuleLink(&$link_sitemap, $lang, &$index, &$i, $num_link = 0)
    {
        $modules_links = Hook::exec(self::HOOK_ADD_URLS_SM, ['lang' => $lang], null, true);
        if (empty($modules_links) || !is_array($modules_links)) {
            return true;
        }
        $links = [];
        foreach ($modules_links as $module_links) {
            $links = array_merge($links, $module_links);
        }
        foreach ($module_links as $n => $link) {
            if ($num_link > $n) {
                continue;
            }
            $link['type'] = 'module';
            if (!$this->_addLinkToSitemap($link_sitemap, $link, $lang['iso_code'], $index, $i, $n)) {
                return false;
            }
        }

        return true;
    }

    public function createSitemap($id_shop = 0, $type_of_sitemap = 0)
    {
        if (@fopen($this->normalizeDirectory(_PS_ROOT_DIR_) . '/test.txt', 'w') == false) {
            $this->context->smarty->assign('google_maps_error', $this->l('An error occured while trying to check your file permissions. Please adjust your permissions to allow PrestaShop to write a file in your root directory.'));

            return false;
        } else {
            @unlink($this->normalizeDirectory(_PS_ROOT_DIR_) . 'test.txt');
        }

        if ($id_shop != 0) {
            $this->context->shop = new Shop((int) $id_shop);
        }

        $type = Tools::getValue('type') ? Tools::getValue('type') : '';
        $languages = Language::getLanguages(true, $id_shop);
        $lang_stop = Tools::getValue('lang') ? true : false;
        $id_obj = Tools::getValue('id') ? (int) Tools::getValue('id') : 0;
        foreach ($languages as $lang) {
            $i = 0;
            $index = (Tools::getValue('index') && Tools::getValue('lang') == $lang['iso_code']) ? (int) Tools::getValue('index') : 0;
            if ($lang_stop && $lang['iso_code'] != Tools::getValue('lang')) {
                continue;
            } elseif ($lang_stop && $lang['iso_code'] == Tools::getValue('lang')) {
                $lang_stop = false;
            }
            if ($type_of_sitemap > 0 && $type_of_sitemap == 1) {// only CMS pages
                $this->type_array = ['home', 'cms'];
            } elseif ($type_of_sitemap > 0 && $type_of_sitemap == 2) {// only product urls
                $this->type_array = ['home', 'product'];
            } elseif ($type_of_sitemap > 0 && $type_of_sitemap == 3) {// only category urls
                $this->type_array = ['home', 'category'];
            }
            $link_sitemap = [];
            foreach ($this->type_array as $type_val) {
                if ($type == '' || $type == $type_val) {
                    $function = '_get' . Tools::ucfirst($type_val) . 'Link';
                    if (!$this->$function($link_sitemap, $lang, $index, $i, $id_obj)) {
                        return false;
                    }
                    $type = '';
                    $id_obj = 0;
                }
            }
            $this->_recursiveSitemapCreator($link_sitemap, $lang['iso_code'], $index);

            $index = 0;
        }

        $this->_createIndexSitemap();
        Configuration::updateValue('GSITEMAP_LAST_EXPORT', date('r'));
        $ping_se = (int) Configuration::get('GSITEMAP_PING_SE_OPT');
        if ($ping_se > 0) {
            Tools::file_get_contents('http://www.google.com/webmasters/sitemaps/ping?sitemap=' . urlencode('http' . (Configuration::get('PS_SSL_ENABLED') ? 's' : '') . '://' . Tools::getShopDomain(false, true) . $this->context->shop->physical_uri . $this->context->shop->virtual_uri . $this->context->shop->id . '_index_sitemap.xml'));
            Tools::file_get_contents('https://www.google.com/webmasters/sitemaps/ping?sitemap=' . urlencode('http' . (Configuration::get('PS_SSL_ENABLED') ? 's' : '') . '://' . Tools::getShopDomain(false, true) . $this->context->shop->physical_uri . $this->context->shop->virtual_uri . $this->context->shop->id . '_index_sitemap.xml'));
            Tools::file_get_contents('https://www.bing.com/ping?sitemap=' . urlencode('http' . (Configuration::get('PS_SSL_ENABLED') ? 's' : '') . '://' . Tools::getShopDomain(false, true) . $this->context->shop->physical_uri . $this->context->shop->virtual_uri . $this->context->shop->id . '_index_sitemap.xml'));
        }

        if ($this->cron) {
            exit;
        }
        $this->redirect_after = AdminController::$currentIndex . '&conf=4&token=' . Tools::getAdminTokenLite('AdminModules').'&configure=' . $this->name;
        //Tools::redirectAdmin($this->redirect_after);
    }

    private function _saveSitemapLink($sitemap)
    {
        if ($sitemap) {
            return Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'gsitemap_sitemap` (`link`, id_shop) VALUES (\'' . pSQL($sitemap) . '\', ' . (int) $this->context->shop->id . ')');
        }

        return false;
    }

    private function _recursiveSitemapCreator($link_sitemap, $lang, &$index)
    {
        if (!count($link_sitemap)) {
            return false;
        }

        $sitemap_link = $this->context->shop->id . '_' . $lang . '_' . $index . '_sitemap.xml';
        $write_fd = fopen($this->normalizeDirectory(_PS_ROOT_DIR_) . $sitemap_link, 'w');

        fwrite($write_fd, '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\r\n");
        foreach ($link_sitemap as $file) {
            fwrite($write_fd, '<url>' . "\r\n");
            $lastmod = (isset($file['lastmod']) && !empty($file['lastmod'])) ? date('c', strtotime($file['lastmod'])) : null;
            $this->_addSitemapNode($write_fd, htmlspecialchars(strip_tags($file['link'])), $this->_getPriorityPage($file['page']), Configuration::get('GSITEMAP_FREQUENCY'), $lastmod);
            if ($file['image']) {
                $base_protocol = 'http' . (Configuration::get('GSITEMAP_URL_SCHEME') ? 's' : '') . '://';
                $shop_domain = $base_protocol . Tools::getShopDomain(false, true);
                $cdn_use = (int) Configuration::get('GSITEMAP_CDN_OPT');
                $img_media_file = $base_protocol . Tools::getMediaServer($file['image']['link']);

                if ($cdn_use > 0) {
                    $img_cdn_file = str_replace($shop_domain, $img_media_file, $file['image']['link']);
                    $this->_addSitemapNodeImage(
                        $write_fd, htmlspecialchars(strip_tags($img_cdn_file)), isset($file['image']['title_img']) ? htmlspecialchars(
                            str_replace(
                                [
                                    "\r\n",
                                    "\r",
                                    "\n",
                                ], '', strip_tags($file['image']['title_img'])
                            )
                        ) : '', isset($file['image']['caption']) ? htmlspecialchars(
                            str_replace(
                                [
                                    "\r\n",
                                    "\r",
                                    "\n",
                                ], '', strip_tags($file['image']['caption'])
                            )
                        ) : ''
                    );
                } else {
                    $this->_addSitemapNodeImage(
                        $write_fd, htmlspecialchars(strip_tags($file['image']['link'])), isset($file['image']['title_img']) ? htmlspecialchars(
                            str_replace(
                                [
                                    "\r\n",
                                    "\r",
                                    "\n",
                                ], '', strip_tags($file['image']['title_img'])
                            )
                        ) : '', isset($file['image']['caption']) ? htmlspecialchars(
                            str_replace(
                                [
                                    "\r\n",
                                    "\r",
                                    "\n",
                                ], '', strip_tags($file['image']['caption'])
                            )
                        ) : ''
                    );
                }
            }
            fwrite($write_fd, '</url>' . "\r\n");
        }
        fwrite($write_fd, '</urlset>' . "\r\n");
        fclose($write_fd);
        $this->_saveSitemapLink($sitemap_link);
        ++$index;

        return true;
    }

    private function _getPriorityPage($page)
    {
        return Configuration::get('GSITEMAP_PRIORITY_' . Tools::strtoupper($page)) ? Configuration::get('GSITEMAP_PRIORITY_' . Tools::strtoupper($page)) : 0.1;
    }

    private function _addSitemapNode($fd, $loc, $priority, $change_freq, $last_mod = null)
    {
        fwrite($fd, '<loc>' . (Configuration::get('PS_REWRITING_SETTINGS') ? '<![CDATA[' . $loc . ']]>' : $loc) . '</loc>' . "\r\n" . '<priority>' . number_format($priority, 1, '.', '') . '</priority>' . "\r\n" . ($last_mod ? '<lastmod>' . date('c', strtotime($last_mod)) . '</lastmod>' : '') . "\r\n" . '<changefreq>' . $change_freq . '</changefreq>' . "\r\n");
    }

    private function _addSitemapNodeImage($fd, $link, $title, $caption)
    {
        fwrite($fd, '<image:image>' . "\r\n" . '<image:loc>' . (Configuration::get('PS_REWRITING_SETTINGS') ? '<![CDATA[' . $link . ']]>' : $link) . '</image:loc>' . "\r\n" . '<image:caption><![CDATA[' . $caption . ']]></image:caption>' . "\r\n" . '<image:title><![CDATA[' . $title . ']]></image:title>' . "\r\n" . '</image:image>' . "\r\n");
    }

    private function _createIndexSitemap()
    {
        $sitemaps = Db::getInstance()->ExecuteS('SELECT `link` FROM `' . _DB_PREFIX_ . 'gsitemap_sitemap` WHERE id_shop = ' . $this->context->shop->id);
        if (!$sitemaps) {
            return false;
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>';
        $xml_feed = new SimpleXMLElement($xml);

        foreach ($sitemaps as $link) {
            $sitemap = $xml_feed->addChild('sitemap');
            $sitemap->addChild('loc', 'http' . (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE') ? 's' : '') . '://' . Tools::getShopDomain(false, true) . __PS_BASE_URI__ . $link['link']);
            $sitemap->addChild('lastmod', date('c'));
        }
        file_put_contents($this->normalizeDirectory(_PS_ROOT_DIR_) . $this->context->shop->id . '_index_sitemap.xml', $xml_feed->asXML());

        return true;
    }

    private function tableColumnExists($table_name, $column = null)
    {
        if (array_key_exists($table_name, $this->sql_checks)) {
            if (!empty($column) && array_key_exists($column, $this->sql_checks[$table_name])) {
                return $this->sql_checks[$table_name][$column];
            } else {
                return $this->sql_checks[$table_name];
            }
        }
        $table = Db::getInstance()->ExecuteS('SHOW TABLES LIKE \'' . bqSQL($table_name) . '\'');
        if (empty($column)) {
            if (count($table) < 1) {
                return $this->sql_checks[$table_name] = false;
            } else {
                $this->sql_checks[$table_name] = true;
            }
        } else {
            $table = Db::getInstance()->ExecuteS('SELECT * FROM `' . bqSQL($table_name) . '` LIMIT 1');

            return $this->sql_checks[$table_name][$column] = array_key_exists($column, current($table));
        }

        return true;
    }

    public function _addLinkToSitemap(&$link_sitemap, $new_link, $lang, &$index, &$i)
    {
        if ($i <= 25000 && memory_get_usage() < 100000000) {
            $link_sitemap[] = $new_link;
            ++$i;

            return true;
        } else {
            $this->_recursiveSitemapCreator($link_sitemap, $lang, $index);
        }
    }
	
	public function validateUsername($username)
    {
        return preg_match('/^@[A-Za-z0-9_]{1,15}$/', $username);
    }
	
	public function hookDisplayTwitterSummaryCard($params)
    {
        $card = '';
        $twittercard = Configuration::get('twittercardtype', null);
        if ($twittercard == 0) {
            $card = 'summary';
        } else {
            $card = 'summary_large_image';
        }
        $context = Context::getContext();
        $twittercard = $card;
        $version = _PS_VERSION_;
        $this->context->smarty->assign('version', $version);
        $lang = $this->context->language->id;
        $fmm_tsc_twitter_status = Configuration::get('twittercard_status', null);
        $fmm_fg_facebook_status = Configuration::get('facebookgraph_status', null);
        $this->context->smarty->assign([
            'fmm_tsc_twitter_status' => $fmm_tsc_twitter_status,
            'fmm_fg_facebook_status' => $fmm_fg_facebook_status,
        ]);
        $twitterimage = Configuration::get('twitterimage', null);
        $twiitersite = Configuration::get('twittersite', null);
        $base = __PS_BASE_URI__;
        $base_dir_ssl = (Configuration::get('PS_SSL_ENABLED') ?
        'https://' : 'http://') . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT);
        $image_path = $base_dir_ssl . $base . 'img/' . $twitterimage;
        $fmm_fb_image = Configuration::get('fmm_fb_image', null);
        $image_fb_path = $base_dir_ssl . $base . 'img/' . $fmm_fb_image;
        $fmm_fb_title = Configuration::get('fmm_fb_title', $lang);
        $fmm_fb_descript = Configuration::get('fmm_fb_descript', $lang);
        $page_name = Dispatcher::getInstance()->getController();
        if ($page_name == 'category' && Tools::getValue('id_category')) {
            $category = new Category(
                (int) Tools::getValue('id_category'),
                (int) $context->language->id,
                (int) $context->shop->id
            );
            if (Validate::isLoadedObject($category)) {
                $id = (int) Tools::getValue('id_category');
                $twittertitle = $this->getFormattedValues(Configuration::get('twittertitle', $lang), false, $id, false);
                $twitterdescription = $this->getFormattedValues(Configuration::get('twitterdescription', $lang), false, $id, false);
                $twitterimagealt = $this->getFormattedValues(Configuration::get('twitterimagealt', $lang), false, $id, false);
                $image_path = '';
                $this->context->smarty->assign([
                    'twitter_card' => $twittercard,
                    'twiiter_site' => $twiitersite,
                    'twitter_title' => $twittertitle,
                    'twitter_description' => $twitterdescription,
                    'twitter_imagealt' => $twitterimagealt,
                    'image' => $image_path,
                    'image_fb' => $image_fb_path,
                    'type' => 'website',
                    'title_fb' => $this->getFormattedValues(Configuration::get('fmm_fb_title', $lang), false, $id, false),
                    'description_fb' => $this->getFormattedValues(Configuration::get('fmm_fb_descript', $lang), false, $id, false),
                    'pagem' => 'category',
                ]);
            }
        } elseif ($page_name == 'product' && Tools::getValue('id_product')) {
            $product = new Product(
                (int) Tools::getValue('id_product'),
                false,
                (int) $context->language->id,
                (int) $context->shop->id
            );
            $id = (int) Tools::getValue('id_product');
            $cover = Product::getCover($id);
            if (Validate::isLoadedObject($product)) {
                $image_path = str_replace(
                    'http://',
                    Tools::getShopProtocol(),
                    $context->link->getImageLink(
                        $product->link_rewrite,
                        $cover['id_image'],
                        $this->getFormatedName('home')
                    )
                );
                $fmm_product_price_tax_exc = Product::getPriceStatic($id, false, null, 2, null, false, false);
                $fmm_product_price_amount = Product::getPriceStatic($id, true, null, 2, null, false, false);
                $twittertitle = $this->getFormattedValues(Configuration::get('twittertitle', $lang), $id, false, false);
                $twitterdescription = $this->getFormattedValues(Configuration::get('twitterdescription', $lang), $id, false, false);
                $twitterimagealt = $this->getFormattedValues(Configuration::get('twitterimagealt', $lang), $id, false, false);
                $weight = Configuration::get('PS_WEIGHT_UNIT');
                $this->context->smarty->assign([
                    'twitter_card' => $twittercard,
                    'twiiter_site' => $twiitersite,
                    'twitter_title' => $twittertitle,
                    'twitter_description' => $twitterdescription,
                    'twitter_imagealt' => $twitterimagealt,
                    'image' => $image_path,
                    'pagem' => 'product',
                    'image_fb' => $image_fb_path,
                    'type' => 'product',
                    'title_fb' => $this->getFormattedValues(Configuration::get('fmm_fb_title', $lang), $id, false, false),
                    'description_fb' => $this->getFormattedValues(Configuration::get('fmm_fb_descript', $lang), $id, false, false),
                    'product_tax_exc' => $fmm_product_price_tax_exc,
                    'product_price_amount' => $fmm_product_price_amount,
                    'unit_weight' => $weight,
                ]);
            }
        } elseif ($page_name == 'cms' && Tools::getValue('id_cms')) {
            $id = (int) Tools::getValue('id_cms');
            $cms = new CMS($id, (int) $context->language->id);
            $cms_title = $cms->meta_title;
            if (!empty($cms_title)) {
                $twittertitle = $cms_title;
                $fmm_fb_title = $cms_title;
            }
            $twitterimagealt = $this->getFormattedValues(Configuration::get('twitterimagealt', $lang), false, false, $id);
            $twittertitle = $this->getFormattedValues(Configuration::get('twittertitle', $lang), false, false, $id);
            $twitterdescription = $this->getFormattedValues(Configuration::get('twitterdescription', $lang), false, false, $id);
            $this->context->smarty->assign([
                'twitter_card' => $twittercard,
                'twiiter_site' => $twiitersite,
                'twitter_title' => $twittertitle,
                'twitter_description' => $twitterdescription,
                'twitter_imagealt' => $twitterimagealt,
                'image' => $image_path,
                'image_fb' => $image_fb_path,
                'type' => 'website',
                'title_fb' => $this->getFormattedValues(Configuration::get('fmm_fb_title', $lang), false, false, $id),
                'description_fb' => $this->getFormattedValues(Configuration::get('fmm_fb_descript', $lang), false, false, $id),
                'pagem' => '',
            ]);
        } else {
            if (empty($lang)) {
                $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
                $lang = $lang->id;
            }
            $twitterimagealt = Configuration::get('twitterimagealt', $lang);
            $twittertitle = Configuration::get('twittertitle', $lang);
            $twitterdescription = Configuration::get('twitterdescription', $lang);
			$twittertitle = $this->getFormattedValues($twittertitle, false, false, false);
			$twitterdescription = $this->getFormattedValues($twitterdescription, false, false, false);
			$twitterimagealt = $this->getFormattedValues($twitterimagealt, false, false, false);
			
            $this->context->smarty->assign([
                'twitter_card' => $twittercard,
                'twiiter_site' => $twiitersite,
                'twitter_title' => $twittertitle,
                'twitter_description' => $twitterdescription,
                'twitter_imagealt' => $twitterimagealt,
                'image' => $image_path,
                'image_fb' => $image_fb_path,
                'type' => 'website',
                'title_fb' => $this->getFormattedValues(Configuration::get('fmm_fb_title', $lang), false, false, false),
                'description_fb' => $this->getFormattedValues(Configuration::get('fmm_fb_descript', $lang), false, false, false),
                'pagem' => '',
            ]);
        }

        return $this->display(__FILE__, 'views/templates/hook/card.tpl');
    }
	
	public function getFormatedName($name)
    {
        $theme_name = Context::getContext()->shop->theme_name;
        $name_without_theme_name = str_replace(['_' . $theme_name, $theme_name . '_'], '', $name);
        // check if the theme name is already in $name if yes only return $name
        if (strstr($name, $theme_name) && ImageType::getByNameNType($name, 'products')) {
            return $name;
        } elseif (ImageType::getByNameNType($name_without_theme_name . '_' . $theme_name, 'products')) {
            return $name_without_theme_name . '_' . $theme_name;
        } elseif (ImageType::getByNameNType($theme_name . '_' . $name_without_theme_name, 'products')) {
            return $theme_name . '_' . $name_without_theme_name;
        } else {
            return $name_without_theme_name . '_default';
        }
    }
	
	private function getAllProductCollisions()
	{
		$langs = Language::getLanguages();
		$langs = count($langs);
		return Db::getInstance()->executeS('
		SELECT DISTINCT `link_rewrite`, GROUP_CONCAT(DISTINCT `id_product`) as id_product, `name`, count(`link_rewrite`) as times
		FROM `'._DB_PREFIX_.'product_lang`
		GROUP BY `link_rewrite`
		HAVING COUNT(`link_rewrite`) > '.(int)$langs);
	}

	private function getAllCategoryCollisions()
	{
		$langs = Language::getLanguages();
		$langs = count($langs);
		return Db::getInstance()->executeS('
		SELECT DISTINCT `link_rewrite`, GROUP_CONCAT(DISTINCT `id_category`) as id_category, `name`, count(`link_rewrite`) as times
		FROM `'._DB_PREFIX_.'category_lang`
		GROUP BY `link_rewrite`
		HAVING COUNT(`link_rewrite`) > '.(int)$langs);
	}

	private function getAllCompareCollisions()
	{
		return Db::getInstance()->executeS('
		SELECT DISTINCT p.`link_rewrite`, p.`id_product`, c.`link_rewrite`, c.`id_category`
		FROM `'._DB_PREFIX_.'product_lang` p, `'._DB_PREFIX_.'category_lang` c
		WHERE p.`link_rewrite` = c.`link_rewrite`');
	}
	
	private function getFormattedValues($value, $prd = 0, $cat = 0, $cms = 0)
    {
        $context = Context::getContext();
        $id_lang = (int) $context->language->id;
        $shoptitle = Configuration::get('PS_SHOP_NAME');
        $cmstitle = '';
        $categorytitle = '';
        $parentcategory = '';
        $productname = '';
        $productcategory = '';
        $productdesc = '';
        $productshortdesc = '';
        $productreference = '';
        $productmanufacturer = '';
        $productretailpricewithtax = '';
        $productretailpricewithouttax = '';
        $productspecificpricewithtax = '';
        $productspecificpricewithouttax = '';
        $productreduction = '';
        $productfeature = '';
        if ($prd > 0) {
            $product = new Product((int) $prd, true, (int) $id_lang);
            $productname = $product->name;
            $productcategory_class = new Category((int) $product->id_category_default, $id_lang);
            $productcategory = $productcategory_class->name;
            $productdesc = Tools::getDescriptionClean($product->description);
            $productshortdesc = Tools::getDescriptionClean($product->description_short);
            $productreference = $product->reference;
            $productmanufacturer = $product->manufacturer_name;
            $productfeature = $product->getFeaturesStatic($prd);
            $productfeatures_collection = [];
            $productretailpricewithtax = Product::getPriceStatic($prd, true, null, 2, null, false, false);
            $productretailpricewithtax = Tools::displayPrice($productretailpricewithtax);
            $productretailpricewithouttax = Product::getPriceStatic($prd, false, null, 2, null, false, false);
            $productretailpricewithouttax = Tools::displayPrice($productretailpricewithouttax);
            $productspecificpricewithtax = Product::getPriceStatic($prd, true, null, 2);
            $productspecificpricewithtax = Tools::displayPrice($productspecificpricewithtax);
            $productspecificpricewithouttax = Product::getPriceStatic($prd, false, null, 2);
            $productspecificpricewithouttax = Tools::displayPrice($productspecificpricewithouttax);
            $productreduction = Product::getPriceStatic($prd, true, null, 2, null, true);
            $productreduction = Tools::displayPrice($productreduction);
            if (!empty($productfeature)) {
                foreach ($productfeature as $feature) {
                    $feature_one = Feature::getFeature($id_lang, $feature['id_feature']);
                    $feature_two = new FeatureValue($feature['id_feature_value'], $id_lang);
                    $feature_complete = $feature_one['name'] . ':' . $feature_two->value;
                    array_push($productfeatures_collection, $feature_complete);
                }
                $productfeature = implode(',', $productfeatures_collection);
            } else {
                $productfeature = '';
            }
        } elseif ($cms > 0) {
            $cms_class = new CMS($cms, $id_lang);
            $cmstitle = $cms_class->meta_title;
        } elseif ($cat > 0) {
            $cat_class = new Category($cat, $id_lang);
            $categorytitle = $cat_class->name;
            $parent_class = new Category((int) $cat_class->id_parent, $id_lang);
            if ((int) $cat_class->id_parent > 0) {
                $parentcategory = $parent_class->name;
            }
        }
        $search_arr = ['productname',
            'productcategory',
            'shoptitle',
            'productdesc',
            'productshortdesc',
            'productreference',
            'productmanufacturer',
            'productfeature',
            'productretailpricewithtax',
            'productretailpricewithouttax',
            'productspecificpricewithtax',
            'productspecificpricewithouttax',
            'productreduction',
            'pagetitle',
            'categoryname',
            'parentcategory',
        ];
        $replace_arr = [$productname,
            $productcategory,
            $shoptitle,
            $productdesc,
            $productshortdesc,
            $productreference,
            $productmanufacturer,
            $productfeature,
            $productretailpricewithtax,
            $productretailpricewithouttax,
            $productspecificpricewithtax,
            $productspecificpricewithouttax,
            $productreduction,
            $cmstitle,
            $categorytitle,
            $parentcategory,
        ];
        $value = str_replace($search_arr, $replace_arr, $value);

        return $value;
    }

    public function getTemplateVarPage($title = '', $desc = '', $keys = '')
    {
        $page = $this->context->controller->getTemplateVarPage();
        if (!empty($title)) {
            $page['meta']['title'] = $title;
        }
        if (!empty($desc)) {
            $page['meta']['description'] = $desc;
        }
        if (!empty($keys)) {
            $page['meta']['keywords'] = $keys;
        }
        $this->context->smarty->assign('page', $page);
    }

    public function notFound()
    {
        $id_lang = (int) $this->context->language->id;
        $id_shop = (int) $this->context->shop->id;
        $ssl = Tools::usingSecureMode();
        $redirect_404 = (int) Configuration::get('REDIRECT_404');
        if ($redirect_404 == 0 && $this->context->controller->php_self == 'pagenotfound') {
            $this->saveReport404();
        }
        // 404 redirection
        if ($redirect_404 != 0 && $this->context->controller->php_self == 'pagenotfound') {
            $this->saveReport404();
            switch ($redirect_404) {
                case 1:
                    // redirect to home page
                    Tools::redirect($this->getHomeLink($id_shop, $ssl, false));
                    break;
                case 2:
                    // redirect to cms page
                    $redirect_404_CMS = (int) Configuration::get('REDIRECT_404_CMS');
                    $cms = new CMS((int) $redirect_404_CMS);
                    Tools::redirect(
                        $this->context->link->getCMSLink($cms, null, (bool) $ssl, $id_lang, $id_shop, false)
                    );
                    break;
            }
        }
    }
	
	public function saveReport404()
    {
        $current_url_not_found = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') .
            "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $reporturl = new Manage404Report();
        $reporturl->url_not_found = $current_url_not_found;
        $reporturl->id_shop = $this->context->shop->id;
        $reporturl->save();
    }
	
	protected function addUrlToHtaccess()
    {
        $obj = new RedirectUrl();
        $path = _PS_ROOT_DIR_ . '/.htaccess';
        $trigger = (int) Configuration::get('REDIRECT_TRIGGER');
        $old_htaccess_content = Tools::file_get_contents($path);
        $redirects_collection = $obj->getAllRedirects();
        if ($trigger <= 0) {
            if (file_exists($path)) {
                $content = Tools::file_get_contents($path);
                if (preg_match('#^(.*)\#begin_redirecturl.*\#end_redirecturl[^\n]*(.*)$#s', $content, $m)) {
                    if (!$write_fd = @fopen($path, 'w')) {
                        $this->errors[] = Tools::displayError('.htaccess not found OR not editable.');

                        return false;
                    } elseif ($write_fd = @fopen($path, 'w')) {
                        fwrite($write_fd, $m[1]);
                        fwrite($write_fd, "\n#begin_redirecturl\n");

                        // RedirectMatch or Redirect
                        foreach ($redirects_collection as $redirect) {
                            fwrite(
                                $write_fd,
                                'RedirectMatch ' . $redirect['redirect_type'] . ' ' .
                                $redirect['old_uri'] . ' ' . $redirect['new_url'] . "\n"
                            );
                        }

                        fwrite($write_fd, "#end_redirecturl\n");
                        fclose($write_fd);
                    }
                } else {
                    if (!$write_fd = @fopen($path, 'w')) {
                        $this->errors[] = Tools::displayError('.htaccess not found OR not editable.');

                        return false;
                    } elseif ($write_fd = @fopen($path, 'w')) {
                        fwrite($write_fd, $old_htaccess_content);
                        fwrite($write_fd, "\n#begin_redirecturl\n");
                        foreach ($redirects_collection as $redirect) {
                            fwrite(
                                $write_fd,
                                'RedirectMatch ' . $redirect['redirect_type'] . ' ' .
                                $redirect['old_uri'] . ' ' . $redirect['new_url'] . "\n"
                            );
                        }

                        fwrite($write_fd, "#end_redirecturl\n");
                        fclose($write_fd);
                    }
                }
            }
        }
    }
	
	protected static function rewindBomAware($handle)
    {
        // A rewind wrapper that skips BOM signature wrongly
        if (!is_resource($handle)) {
            return false;
        }

        rewind($handle);
        if (fread($handle, 3) != "\xEF\xBB\xBF") {
            rewind($handle);
        }
    }
	
	protected function getNbrColumn($handle, $glue)
    {
        if (!is_resource($handle)) {
            return false;
        }

        $tmp = fgetcsv($handle, MAX_LINE_SIZE, $glue);
        $this->rewindBomAware($handle);

        return count($tmp);
    }
	
	private function checkUriExistance($request_uri)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `new_url`
		FROM `' . _DB_PREFIX_ . 'redirecturl`
		WHERE `old_uri` = "/' . pSQL($request_uri) . '"');
		if (is_array($result) && !empty($result)) {
			return $result['new_url'];
		}
		else {
			return false;
		}
    }
	
	protected function getHomeLink($id_shop = null, $ssl = null, $relative_protocol = false)
    {
        static $force_ssl = null;

        if ($ssl === null) {
            if ($force_ssl === null) {
                $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            }

            $ssl = $force_ssl;
        }

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $id_shop !== null) {
            $shop = new Shop($id_shop);
        } else {
            $shop = Context::getContext()->shop;
        }

        if ($relative_protocol) {
            $base = '//' . ($ssl ? $shop->domain_ssl : $shop->domain);
        } else {
            $base = (($ssl) ? 'https://' . $shop->domain_ssl : 'http://' . $shop->domain);
        }

        return $base . $shop->getBaseURI();
    }
}