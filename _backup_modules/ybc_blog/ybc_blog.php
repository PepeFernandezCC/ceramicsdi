<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if(!defined('_PS_VERSION_'))
	exit;
include_once(_PS_MODULE_DIR_.'ybc_blog/classes/ybc_blog_category_class.php');
include_once(_PS_MODULE_DIR_.'ybc_blog/classes/ybc_blog_post_class.php');
include_once(_PS_MODULE_DIR_.'ybc_blog/classes/ybc_blog_paggination_class.php');
include_once(_PS_MODULE_DIR_.'ybc_blog/classes/ybc_blog_comment_class.php');
include_once(_PS_MODULE_DIR_.'ybc_blog/classes/ybc_blog_reply_class.php');
include_once(_PS_MODULE_DIR_.'ybc_blog/classes/ybc_blog_polls_class.php');
include_once(_PS_MODULE_DIR_.'ybc_blog/classes/ybc_blog_slide_class.php');
include_once(_PS_MODULE_DIR_.'ybc_blog/classes/ybc_blog_gallery_class.php');
include_once(_PS_MODULE_DIR_.'ybc_blog/classes/ybc_blog_link_class.php');
include_once(_PS_MODULE_DIR_.'ybc_blog/classes/ybc_blog_employee_class.php');
include_once(_PS_MODULE_DIR_.'ybc_blog/classes/ybc_blog_email_template_class.php');
include_once(_PS_MODULE_DIR_.'ybc_blog/classes/ImportExport.php');
include_once(_PS_MODULE_DIR_.'ybc_blog/classes/ybc_browser.php');
include_once(_PS_MODULE_DIR_.'ybc_blog/ybc_blog_defines.php');
if(!function_exists('ets_getCookie'))
    include_once(_PS_MODULE_DIR_.'ybc_blog/classes/cookie');
if (!defined('_PS_YBC_BLOG_IMG_DIR_')) {
    define('_PS_YBC_BLOG_IMG_DIR_', _PS_IMG_DIR_.'ybc_blog/');
}
if (!defined('_PS_YBC_BLOG_IMG_')) {
    define('_PS_YBC_BLOG_IMG_', _PS_IMG_.'ybc_blog/');
}
if (!defined('_YBC_BLOG_CACHE_DIR_'))
    define('_YBC_BLOG_CACHE_DIR_', _PS_CACHE_DIR_ . 'ybc_blog/');
class Ybc_blog extends Module
{
    private $depthLevel = false;
    private $prefix = '-';
    private $blogCategoryDropDown;
    public $baseAdminPath;
    private $errorMessage = false;
    private $_html = '';
    public $blogDir;
    public $alias;
    public $friendly;
    public $is17 = false;
    public $configTabs = array();
    public $import_ok=false;   
    public $errors = array();
    public $sort = false;public $controls;
    public function __construct()
	{
        $this->name = 'ybc_blog';
		$this->tab = 'front_office_features';
		$this->version = '4.4.3';
		$this->author = 'PrestaHero';
		$this->need_instance = 0;
		$this->secure_key = Tools::encrypt($this->name);
		$this->bootstrap = true;
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $this->is17 = true; 
        $this->module_key = 'da314fdf1af6d043f9b2f15dce2bef1e';
        parent::__construct();
        $this->shortlink = 'https://mf.short-link.org/';
        $configure = Tools::getValue('configure');
        $controller = Tools::getValue('controller');
        if(!Configuration::get('YBC_BLOG_POST_SORT_BY'))
            $this->sort = 'p.datetime_added DESC, ';
        else
        {
            if(Configuration::get('YBC_BLOG_POST_SORT_BY')=='sort_order')
                $this->sort = 'p.sort_order ASC, ';
            elseif(Configuration::get('YBC_BLOG_POST_SORT_BY')=='id_post')
                $this->sort = 'p.datetime_added DESC, ';
            else
                $this->sort = 'p.'.Configuration::get('YBC_BLOG_POST_SORT_BY').' DESC, ';
        }

		$this->displayName = $this->l('BLOG');
        $this->description = $this->l('The most powerful, flexible and feature-rich blog module for Prestashop. BLOG provides everything you need to create a professional blog area for your website.');
		$this->ps_versions_compliancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);
        $this->configTabs = array(
            'general' => $this->l('General'),
            'gallery' => $this->l('Gallery'),
            'slider' => $this->l('Slider'),
            'comment' => $this->l('Likes and Comments'), 
            'polls' => $this->l('Polls'),
            'design' => $this->l('Design'),
        );
        $this->blogDir = $this->_path;  
        $this->alias = Configuration::get('YBC_BLOG_ALIAS',$this->context->language->id) ? : Configuration::get('YBC_BLOG_ALIAS',Configuration::get('PS_LANG_DEFAULT'));
        $this->friendly = (int)Configuration::get('YBC_BLOG_FRIENDLY_URL') && (int)Configuration::get('PS_REWRITING_SETTINGS') ? true : false;    
        $g_recaptcha = Tools::getValue('g-recaptcha-response');
        $recaptcha = $g_recaptcha && Validate::isCleanHtml($g_recaptcha) ? $g_recaptcha: '';
        $secret = Configuration::get('YBC_BLOG_CAPTCHA_TYPE')=='google' ? Configuration::get('YBC_BLOG_CAPTCHA_SECRET_KEY') : Configuration::get('YBC_BLOG_CAPTCHA_SECRET_KEY3');
        $this->link_capcha="https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $recaptcha . "&remoteip=" . Tools::getRemoteAddr();
        $this->controls = array('category','post','comment','polls','slide','gallery','seo','sitemap','rss','socials','email','image','sidebar','homepage','postlistpage','postpage','categorypage','productpage','employees','customer','export','config','comment_reply','author');
        
    }
    public function enable($force_all = false)
    {
        require_once(dirname(__FILE__) . '/classes/OverrideUtil');
        $class= 'Ybc_blog_overrideUtil';
        $method = 'resolveConflict';
        call_user_func_array(array($class, $method),array($this));
        return parent::enable($force_all);
    }
    public function disable($force_all = false)
    {
        require_once(dirname(__FILE__) . '/classes/OverrideUtil');
        $parentResult = parent::disable($force_all);
        $class= 'Ybc_blog_overrideUtil';
        $method = 'restoreReplacedMethod';
        call_user_func_array(array($class, $method),array($this));
        return $parentResult;
    }
    /**
	 * @see Module::install()
	 */
    public function install()
	{
	    return parent::install()&& $this->registerHook('displayLeftColumn')
        && $this->registerHook('displayBackOfficeHeader') 
        && $this->registerHook('displayHome') 
        && $this->registerHook('displayHeader')
        && $this->registerHook('displayFooter')
        && $this->registerHook('blogSearchBlock')
        && $this->registerHook('blogTagsBlock')
        && $this->registerHook('blogNewsBlock')
        && $this->registerHook('blogCategoriesBlock')
        && $this->registerHook('blogSlidersBlock')
        && $this->registerHook('blogGalleryBlock')
        && $this->registerHook('blogPopularPostsBlock')
        && $this->registerHook('moduleRoutes')
        && $this->registerHook('blogSidebar')
        && $this->registerHook('blogFeaturedPostsBlock')
        && $this->registerHook('displayRightColumn')
        && $this->registerHook('displayFooterProduct')
        && $this->registerHook('blogArchivesBlock')
        && $this->registerHook('blogComments')
        && $this->registerHook('blogPositiveAuthor')
        && $this->registerHook('blogRssCategory')
        && $this->registerHook('customerAccount')
        && $this->registerHook('displayMyAccountBlock')
        && $this->registerHook('displayLeftFormManagament')
        && $this->registerHook('displayRightFormManagament')
        && $this->registerHook('displayLeftFormComments')
        && $this->registerHook('displayRightFormComments')
        && $this->registerHook('blogRssSideBar')
        && $this->registerHook('blogRssAuthor')
        && $this->registerHook('blogCategoryBlock')
        && $this->registerHook('displayBackOfficeFooter')
        && $this->registerHook('displayFooterYourAccount')
        && $this->registerHook('actionObjectLanguageAddAfter')
        && $this->registerHook('displayFooterCategory')
        && $this->_installDb()
        && $this->_installTabs() && $this->_copyForderMail();
    }    
    /**
	 * @see Module::uninstall()
	 */
	public function uninstall()
	{
        return parent::uninstall() &&  $this->_uninstallDb() && $this->_uninstallTabs();
    }
    private function _installDb()
    {
        if(!is_dir(_PS_YBC_BLOG_IMG_DIR_))
            @mkdir(_PS_YBC_BLOG_IMG_DIR_);
        if(file_exists(dirname(__FILE__).'/index.php'))
            Tools::copy(dirname(__FILE__).'/index.php',_PS_YBC_BLOG_IMG_DIR_.'index.php');
        if(!is_dir(_PS_YBC_BLOG_IMG_DIR_.'slide/'))
            @mkdir(_PS_YBC_BLOG_IMG_DIR_.'slide/');
        if(file_exists(dirname(__FILE__).'/index.php'))
            Tools::copy(dirname(__FILE__).'/index.php',_PS_YBC_BLOG_IMG_DIR_.'slide/index.php');
        if(!is_dir(_PS_YBC_BLOG_IMG_DIR_.'post/'))
            @mkdir(_PS_YBC_BLOG_IMG_DIR_.'/post');
        if(file_exists(dirname(__FILE__).'/index.php'))
            Tools::copy(dirname(__FILE__).'/index.php',_PS_YBC_BLOG_IMG_DIR_.'post/index.php');
        if(!is_dir(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'))
            @mkdir(_PS_YBC_BLOG_IMG_DIR_.'/post/thumb');
        if(file_exists(dirname(__FILE__).'/index.php'))
            Tools::copy(dirname(__FILE__).'/index.php',_PS_YBC_BLOG_IMG_DIR_.'post/thumb/index.php');
        if(!is_dir(_PS_YBC_BLOG_IMG_DIR_.'gallery/'))
            @mkdir(_PS_YBC_BLOG_IMG_DIR_.'gallery/');
        if(file_exists(dirname(__FILE__).'/index.php'))
            Tools::copy(dirname(__FILE__).'/index.php',_PS_YBC_BLOG_IMG_DIR_.'gallery/index.php');
        if(!is_dir(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'))
            @mkdir(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/');
        if(file_exists(dirname(__FILE__).'/index.php'))
            Tools::copy(dirname(__FILE__).'/index.php',_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/index.php');
        if(!is_dir(_PS_YBC_BLOG_IMG_DIR_.'category/'))
            @mkdir(_PS_YBC_BLOG_IMG_DIR_.'category/');
        if(file_exists(dirname(__FILE__).'/index.php'))
            Tools::copy(dirname(__FILE__).'/index.php',_PS_YBC_BLOG_IMG_DIR_.'category/index.php');
        if(!is_dir(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'))
            @mkdir(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/');
        if(file_exists(dirname(__FILE__).'/index.php'))
            Tools::copy(dirname(__FILE__).'/index.php',_PS_YBC_BLOG_IMG_DIR_.'category/thumb/index.php');
        if(!is_dir(_PS_YBC_BLOG_IMG_DIR_.'avata/'))
            @mkdir(_PS_YBC_BLOG_IMG_DIR_.'avata/');
        if(file_exists(dirname(__FILE__).'/index.php'))
            Tools::copy(dirname(__FILE__).'/index.php',_PS_YBC_BLOG_IMG_DIR_.'avata/index.php');
        $languages = Language::getLanguages(false);
        //Install db structure
        Configuration::updateValue('PS_ALLOW_HTML_IFRAME',1);
        require_once(dirname(__FILE__).'/install/sql.php');
        require_once(dirname(__FILE__).'/install/data.php');   
        $ybc_defines = new Ybc_blog_defines();        
        if($ybc_defines->configs)
        {
            foreach($ybc_defines->configs as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($ybc_defines->configs_seo)
        {
            foreach($ybc_defines->configs_seo as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($ybc_defines->configs_sitemap)
        {
            foreach($ybc_defines->configs_sitemap as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($ybc_defines->configs_homepage)
        {
            $ybc_defines->configs_homepage['YBC_BLOG_SHOW_CATEGORIES_BLOCK_HOME']=array(
                'label' => $this->l('Select blog categories to display'),
                    'type' => 'blog_categories',
                    'html_content' =>$this->displayBlogCategoryTre(Ybc_blog_category_class::getBlogCategoriesTree(0,true,$this->context->language->id,0,false),$this->getSelectedCategories()),
					'categories' => Ybc_blog_category_class::getBlogCategoriesTree(0,true,$this->context->language->id,0,false),
					'name' => 'categories',
                    'selected_categories' => $this->getSelectedCategories(),
                    'default' =>'',
            );
            foreach($ybc_defines->configs_homepage as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($ybc_defines->configs_postpage)
        {
            foreach($ybc_defines->configs_postpage as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($ybc_defines->configs_postlistpage)
        {
            foreach($ybc_defines->configs_postlistpage as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($ybc_defines->configs_categorypage)
        {
            foreach($ybc_defines->configs_categorypage as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($ybc_defines->configs_productpage)
        {
            foreach($ybc_defines->configs_productpage as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($ybc_defines->configs_sidebar)
        {
            foreach($ybc_defines->configs_sidebar as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($ybc_defines->configs_email)
        {
            foreach($ybc_defines->configs_email as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($ybc_defines->socials)
        {
            foreach($ybc_defines->socials as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($ybc_defines->rss)
        {
            foreach($ybc_defines->rss as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($ybc_defines->customer_settings)
        {
            foreach($ybc_defines->customer_settings as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $values = array();
                    foreach($languages as $lang)
                    {
                        $values[$lang['id_lang']] = isset($config['default']) ? $config['default'] : '';
                    }
                    Configuration::updateValue($key, $values);
                }
                else
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }
        if($ybc_defines->configs_image)
        {
            foreach($ybc_defines->configs_image as $key=>$config)
            {
                if($config['type']=='image')
                {
                    Configuration::updateValue($key.'_WIDTH',$config['default'][0]);
                    Configuration::updateValue($key.'_HEIGHT',$config['default'][1]);
                }
                else
                {
                    Configuration::updateValue($key, isset($config['default']) ? $config['default'] : '');
                }
                
            }
        } 
        Configuration::updateValue('YBC_BLOG_ALERT_EMAILS',Configuration::get('PS_SHOP_EMAIL'));
        if (defined('_PS_ADMIN_DIR_'))
        {
            $adminforder= str_replace(_PS_ROOT_DIR_,'',_PS_ADMIN_DIR_);
            $adminforder= trim(trim($adminforder,'\\'),'/');
            Configuration::updateValue('YBC_BLOG_ADMIN_FORDER',$adminforder);
        }
        $this->refreshCssCustom();
        $this->initEmailTemplate();
        return true;
    }
    public function _copyForderMail()
    {
        $languages = Language::getLanguages(false);
        $temp_dir_ltr = dirname(__FILE__) . '/mails/en';
        if ($languages && is_array($languages))
        {
            if (!@file_exists($temp_dir_ltr))
                return true;
            foreach ($languages as $language)
            {
                if(isset($language['iso_code']) && $language['iso_code'] != 'en')
                {
                     if (($new_dir = dirname(__FILE__) . '/mails/'. $language['iso_code']))
                     {
                        
                        $this->recurseCopy($temp_dir_ltr, $new_dir);
                     }
                }
            }
        }
        return true;
    }
    public function deleteDir($dir)
    {
        $dir = rtrim($dir,'/');
        $files = glob($dir.'/*'); 
        foreach($files as $file){ 
            if(is_dir($file))
                $this->deleteDir($file);
            elseif(is_file($file) && file_exists($file))
                @unlink($file); 
        }
        @rmdir($dir);
        return true;
    }
    public function recurseCopy($src, $dst)
    {
        if(!@file_exists($src))
            return false;
        $dir = opendir($src);
        if (!@is_dir($dst))
            @mkdir($dst);
        while(false !== ($file = readdir($dir)))
        {
            if (( $file != '.' ) && ($file != '..' ))
            {
                if (is_dir($src . '/' . $file)) {
                    $this->recurseCopy($src . '/' . $file,$dst . '/' . $file);
                }
                elseif (!@file_exists($dst . '/' . $file))
                {
                    @copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
    private function _uninstallDb()
    {
        $ybc_defines = new Ybc_blog_defines();
        if($ybc_defines->configs)
        {
            foreach($ybc_defines->configs as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($ybc_defines->configs_seo)
        {
            foreach($ybc_defines->configs_seo as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($ybc_defines->configs_sitemap)
        {
            foreach($ybc_defines->configs_sitemap as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($ybc_defines->configs_homepage)
        {
            foreach($ybc_defines->configs_homepage as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($ybc_defines->configs_postpage)
        {
            foreach($ybc_defines->configs_postpage as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($ybc_defines->configs_postlistpage)
        {
            foreach($ybc_defines->configs_postlistpage as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($ybc_defines->configs_categorypage)
        {
            foreach($ybc_defines->configs_categorypage as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($ybc_defines->configs_productpage)
        {
            foreach($ybc_defines->configs_productpage as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($ybc_defines->configs_sidebar)
        {
            foreach($ybc_defines->configs_sidebar as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($ybc_defines->configs_email)
        {
            foreach($ybc_defines->configs_email as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($ybc_defines->socials)
        {
            foreach($ybc_defines->socials as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($ybc_defines->rss)
        {
            foreach($ybc_defines->rss as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        if($ybc_defines->customer_settings)
        {
            foreach($ybc_defines->customer_settings as $key => $config)
            {
                Configuration::deleteByName($key);
            }
            unset($config);
        }
        Ybc_blog_defines::deleteTableDb();
        $this->deleteDir(_PS_YBC_BLOG_IMG_DIR_);
        if(file_exists(_YBC_BLOG_CACHE_DIR_.'ybc_blog.data.zip'))
            unlink(_YBC_BLOG_CACHE_DIR_.'ybc_blog.data.zip');
        return true;
    }
    public function getContent()
	{
        if(!$this->active)
            return $this->displayWarning($this->l('Module is disabled'));
        //Ajax search
        $this->ajaxProductSearch();
        $this->ajaxPostSearch();
        $this->ajaxCustomerSearch();
        //Init
        $action = Tools::getValue('action');
        if($action=='getCountMessageYbcBlog')
        {   
            die(
                json_encode(
                    array(
                        'count' => Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.viewed=0',false),
                    )
                )   
            );
        }
        $ybc_defines = new Ybc_blog_defines();
	   $this->baseAdminPath = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
	   $this->context->controller->addJqueryPlugin('tagify');
       $this->context->controller->addJqueryUI('ui.sortable');
	   $control = trim(Tools::getValue('control'));
       if(!$control) 
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=post&list=true');
       if($control=='category')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Blog posts and blog categories'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postCategory();   
       }
       elseif($control=='post')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Blog posts and blog categories'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postPost();   
       }
       elseif($control=='config')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Global settings'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postConfig($ybc_defines->configs);   
       }
       elseif($control=='sitemap')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Sitemap'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postConfig($ybc_defines->configs_sitemap);   
       }
       elseif($control=='seo')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Seo'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postConfig($ybc_defines->configs_seo);   
       }
       elseif($control=='image')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Image'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postConfig($ybc_defines->configs_image,_PS_YBC_BLOG_IMG_DIR_.'avata/',Configuration::get('YBC_BLOG_IMAGE_AVATA_WIDTH',300),Configuration::get('YBC_BLOG_IMAGE_AVATA_HEIGHT',300));
            if(Tools::isSubmit('deldefaultavataimage'))
            {
                if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'avata/'.Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT')))
                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'avata/'.Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT'));
                Configuration::updateValue('YBC_BLOG_IMAGE_AVATA_DEFAULT','');  
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(
                        array(
                            'messageType' => 'success',
                            'message' => $this->displayConfirmation($this->l('Image deleted')),
                            'image_default' => $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/default_customer.png'),
                        )
                    ));
                }                                                          
            }   
       }
       elseif($control=='sidebar')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Sidebar'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postConfig($ybc_defines->configs_sidebar);  
            if($action=='updateSidebarOrdering')
            {
                $positions= Tools::getValue('sidebar-position-sidebar');
                if($positions && is_array($positions) && Ybc_blog::validateArray($positions))
                {
                    foreach($positions as $key=> $position)
                        $positions[$key] ='sidebar_'.$position;
                    Configuration::updateValue('YBC_BLOG_POSITION_SIDEBAR',implode(',',$positions));
                    die(
                        json_encode(
                            array(
                                'messageType' => 'success',
                                'message'=> $this->displaySuccessMessage($this->l('Position updated')),
                            )
                        )
                    );
                }
                else
                {
                    die(
                        json_encode(
                            array(
                                'messageType'=>'error',
                                'message'=> $this->displayError($this->l('Update failed')),
                            )
                        )
                    );
                }
                
            } 
            if($action=='updateBlock')
            {
                $field = Tools::getValue('field');
                $value_filed = Tools::getValue('value_filed');
                if(Validate::isConfigName($field) && Validate::isCleanHtml($value_filed,true))
                {
                    Configuration::updateValue(Tools::getValue('field'),Tools::getValue('value_filed'));    
                    die(json_encode(
                        array(
                            'messageType' => 'success',
                            'message'=> $this->displaySuccessMessage($this->l('Updated successfully')),
                        )
                    ));
                }
                else
                {
                    die(json_encode(
                        array(
                            'messageType' => 'error',
                            'message'=> $this->displayError($this->l('Update failed')),
                        )
                    ));
                }
            } 
       }
       elseif($control=='homepage')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Home page'))
                return $this->display(__FILE__,'error_access.tpl');
            $ybc_defines->configs_homepage['YBC_BLOG_SHOW_CATEGORIES_BLOCK_HOME']=array(
                'label' => $this->l('Select blog categories to display'),
                    'type' => 'blog_categories',
                    'html_content' =>$this->displayBlogCategoryTre(Ybc_blog_category_class::getBlogCategoriesTree(0,true,$this->context->language->id,0,false),$this->getSelectedCategories()),
					'categories' => Ybc_blog_category_class::getBlogCategoriesTree(0,true,$this->context->language->id,0,false),
					'name' => 'categories',
                    'selected_categories' => $this->getSelectedCategories(),
                    'default' =>'',
            );
            $this->_postConfig($ybc_defines->configs_homepage);  
            if($action=='updateSidebarOrdering')
            {
                $positions= Tools::getValue('sidebar-position-homepage');
                if($positions && is_array($positions) && Ybc_blog::validateArray($positions))
                {
                    foreach($positions as $key=> $position)
                        $positions[$key] ='homepage_'.$position;
                    Configuration::updateValue('YBC_BLOG_POSITION_HOMEPAGE',implode(',',$positions));
                    die(
                        json_encode(
                            array(
                                'messageType' => 'success',
                                'message'=> $this->displaySuccessMessage($this->l('Position updated')),
                            )
                        )
                    );
                }
                else
                {
                    die(
                        json_encode(
                            array(
                                'messageType'=>'error',
                                'message'=> $this->displayError($this->l('Update failed')),
                            )
                        )
                    );
                }    
                
            } 
            if($action=='updateBlock')
            {
                $field = Tools::getValue('field');
                $value_filed = Tools::getValue('value_filed');
                if(Validate::isConfigName($field) && Validate::isCleanHtml($value_filed,true))
                {
                    Configuration::updateValue(Tools::getValue('field'),Tools::getValue('value_filed'));    
                    die(json_encode(
                        array(
                            'messageType' => 'success',
                            'message'=> $this->displaySuccessMessage($this->l('Updated successfully')),
                        )
                    ));
                }
                else
                {
                    die(json_encode(
                        array(
                            'messageType' => 'error',
                            'message'=> $this->displayError($this->l('Update failed')),
                        )
                    ));
                }    
            } 
       }
       elseif($control=='postpage')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Post detail page'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postConfig($ybc_defines->configs_postpage);   
       }
       elseif($control=='postlistpage')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Post listing pages'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postConfig($ybc_defines->configs_postlistpage);   
       }
       elseif($control=='categorypage')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Category page'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postConfig($ybc_defines->configs_categorypage);   
       }
       elseif($control=='productpage')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Product detail page'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postConfig($ybc_defines->configs_productpage);   
       }
       elseif($control=='email')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Email'))
                return $this->display(__FILE__,'error_access.tpl');
            if(Tools::isSubmit('saveEmailTemplate') || Tools::isSubmit('change_enabled'))
            {
                $this->submitSaveEamilTemplate();
            }
            elseif(Tools::isSubmit('submitBulkEnabled') && ($id_email_template = Tools::getValue('bulk_ybc_email')) )
            {
                Ybc_blog_email_template_class::submitBulkEnabled($id_email_template);
                Tools::redirectAdmin($this->baseAdminPath.'&control=email&conf=4');
            }
            elseif(Tools::isSubmit('submitBulkDiasabled') && ($id_email_template = Tools::getValue('bulk_ybc_email')) )
            {
                Ybc_blog_email_template_class::submitBulkDiasabled($id_email_template);
                Tools::redirectAdmin($this->baseAdminPath.'&control=email&conf=4');
            }
            else
            {
                $this->_postConfig($ybc_defines->configs_email);   
            }
            
       }
       elseif($control=='socials')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Socials'))
                return $this->display(__FILE__,'error_access.tpl');
             $this->_postConfig($ybc_defines->socials);   
       }      
       elseif($control=='comment')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Blog comments'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postComment();   
       }
       elseif($control=='polls')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Blog comments'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postPolls();   
       }
       elseif($control=='gallery')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Blog gallery'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postGallery();   
       }
       elseif($control=='slide')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Blog slider'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postSlide();   
       }
       elseif($control=='export')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Import/Export'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postExport();
       }
       elseif($control=='employees')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Authors'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postEmployee();
       }
       elseif($control=='rss')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Rss feed'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postRSS();
       }
       elseif($control=='author')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Authors'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postCustomerSettingAuthor();
       }
       elseif($control=='customer')
       {
            if(!$this->checkProfileEmployee($this->context->employee->id,'Authors'))
                return $this->display(__FILE__,'error_access.tpl');
            $this->_postCustomer();
       }
       elseif($control=='comment_reply')
       {
            $this->_posstReply();
       }
       return $this->getAminHtml($control);
    }
    public function renderAdminBodyHtml($control)
    {
        $ybc_defines = new Ybc_blog_defines();
       if($control=='category')
       {
            $this->renderCategoryForm();   
       }
       elseif($control=='post')
       {
            $this->renderPostForm();   
       }
       elseif($control=='config')
       {
            $this->renderConfig($ybc_defines->configs, $this->l('Global settings'),'icon-AdminAdmin');   
       }
       elseif($control=='seo')
       {
            $this->renderConfig($ybc_defines->configs_seo, $this->l('Seo'),'icon-seo');   
       }
       elseif($control=='image')
       {
            $this->renderConfig($ybc_defines->configs_image, $this->l('Image'),'icon-cogs');   
       }
       elseif($control=='email')
       {
            if(($id_ybc_blog_email_template = (int)Tools::getValue('id_ybc_blog_email_template')) && ($email_template = new Ybc_blog_email_template_class($id_ybc_blog_email_template)) && Validate::isLoadedObject($email_template))
            {
                $this->_html .= $email_template->renderForm().$email_template->previewTemplate();
            }
            else
            {
                $this->renderConfig($ybc_defines->configs_email, $this->l('Email configuration'),'icon-email');
                $this->_html .= Ybc_blog_email_template_class::getInstance()->renderList(Tools::getAllValues());
            }
            
       }
       elseif($control=='sidebar')
       {
            $this->renderConfig($ybc_defines->configs_sidebar, $this->l('Sidebar'),'icon-sidebar');   
       }
       elseif($control=='homepage')
       {
            $ybc_defines->configs_homepage['YBC_BLOG_SHOW_CATEGORIES_BLOCK_HOME']=array(
                'label' => $this->l('Select blog categories to display'),
                    'type' => 'blog_categories',
                    'html_content' =>$this->displayBlogCategoryTre(Ybc_blog_category_class::getBlogCategoriesTree(0,true,$this->context->language->id,0,false),$this->getSelectedCategories()),
					'categories' => Ybc_blog_category_class::getBlogCategoriesTree(0,true,$this->context->language->id,0,false),
					'name' => 'categories',
                    'selected_categories' => $this->getSelectedCategories(),
                    'default' =>'',
            );
            $this->renderConfig($ybc_defines->configs_homepage, $this->l('Home page'),'icon-homepage');   
       }
       elseif($control=='postpage')
       {
            $this->renderConfig($ybc_defines->configs_postpage, $this->l('Post details page'),'icon-postpage');   
       }
       elseif($control=='postlistpage')
       {
            $this->renderConfig($ybc_defines->configs_postlistpage, $this->l('Post listing pages'),'icon-postlistpage');   
       }
       elseif($control=='categorypage')
       {
            $this->renderConfig($ybc_defines->configs_categorypage, $this->l('Product categories page'),'icon-categorypage');   
       }
       elseif($control=='productpage')
       {
            $this->renderConfig($ybc_defines->configs_productpage, $this->l('Product details page'),'icon-productpage');   
       }
       elseif($control=='sitemap')
       {
            $this->renderConfig($ybc_defines->configs_sitemap, $this->l('Google sitemap'),'icon-sitemap');   
       }
       elseif($control=='socials')
       {
            $this->renderConfig($ybc_defines->socials, $this->l('Socials'),'icon-socials'); ;   
       }
       elseif($control=='rss')
       {
            $this->renderRSS();
       }
       elseif($control=='comment')
       {
            $this->renderCommentsForm();   
       }
       elseif($control=='polls')
       {
            $this->renderPollsForm();
       }
       elseif($control=='gallery')
       {
            $this->renderGalleryForm();   
       }
       elseif($control=='slide')
       {
            $this->renderSlideForm();   
       }
       elseif($control=='export')
       {
            $this->renderExportForm();   
       }
       elseif($control=='employees')
       {
            $this->renderEmployeeFrom();
       }
       elseif($control=='customer')
       {
            $this->renderCustomerForm();
       }
       elseif($control=='author')
       {
            $this->renderAuthorForm();
       }
       elseif($control=='comment_reply')
       {
            $this->displayReplyComment();
       }
       return $this->_html;
    }
    public function getAminHtml($control)
    {
        $id_post = (int)Tools::getValue('id_post');
        $id_category = (int)Tools::getValue('id_category');
        $this->smarty->assign(array(
            'ybc_blog_ajax_url' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&ajaxproductsearch=true',
            'ybc_blog_author_ajax_url' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&ajaxCustomersearch=true',
            'ybc_blog_default_lang' => Configuration::get('PS_LANG_DEFAULT'),
            'ybc_blog_is_updating' => (int)$id_post || (int)$id_category ? 1 :  0,
            'ybc_blog_is_config_page' => $control == 'config' ? 1 : 0,
            'ybc_blog_invalid_file' => $this->l('Invalid file'),
            'ybc_blog_module_dir' => $this->_path,
            'ybc_blog_sidebar' => $this->renderSidebar(),
            'ybc_blog_body_html' => $this->renderAdminBodyHtml($control),
            'ybc_blog_error_message' => $this->errorMessage,
            'control' => $control,
        ));
        return $this->display(__FILE__, 'admin.tpl');
    }
        
    /**
     * Category 
     */
    
    public function renderCategoryForm()
    {
        $this->baseAdminPath = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        //List 
        if(Tools::isSubmit('list'))
        {
            $fields_list = array(
                'id_category' => array(
                    'title' => $this->l('Id'),
                    'width' => 40,
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true,
                ),
                'thumb_link'=>array(
                    'title'=> $this->l('Image'),
                    'type' => 'text',
                    'strip_tag'=>false,
                ),
                'title' => array(
                    'title' => $this->l('Name'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true
                ),
                'description' => array(
                    'title' => $this->l('Description'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true
                ),
                'sort_order' => array(
                    'title' => $this->l('Sort order'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true,
                    'update_position' => true,
                ),
                'enabled' => array(
                    'title' => $this->l('Enabled'),
                    'type' => 'active',
                    'sort' => true,
                    'filter' => true,
                    'strip_tag' => false,
                    'filter_list' => array(
                        'id_option' => 'enabled',
                        'value' => 'title',
                        'list' => array(
                            0 => array(
                                'enabled' => 1,
                                'title' => $this->l('Yes')
                            ),
                            1 => array(
                                'enabled' => 0,
                                'title' => $this->l('No')
                            )
                        )
                    )
                ),
            );
            //Filter
            $filter = "";
            if(($idCategory = trim(Tools::getValue('id_category')))!='' && Validate::isCleanHtml($idCategory))
                $filter .= " AND c.id_category = ".(int)$idCategory;
            if(($sort_order = trim(Tools::getValue('sort_order')))!='' && Validate::isCleanHtml($sort_order))
                $filter .= " AND c.sort_order = ".(int)$sort_order;
            if(($title = trim(Tools::getValue('title')))!='' && Validate::isCleanHtml($title))
                $filter .= " AND cl.title like '%".pSQL($title)."%'";
            if(($description =trim(Tools::getValue('description')))!='' && Validate::isCleanHtml($description))
                $filter .= " AND cl.description like '%".pSQL($description)."%'";
             if(($enabled = trim(Tools::getValue('enabled')))!='' && Validate::isCleanHtml($enabled))
                $filter .= " AND c.enabled =".(int)$enabled;
            if($filter)
                $show_reset = true;
            else
                $show_reset =false;
            //Sort
            $sort = "";
            $sort_post = Tools::strtolower(trim(Tools::getValue('sort')));
            $sort_type = Tools::strtolower(Tools::getValue('sort_type','desc'));
            if(!in_array($sort_type,array('desc','asc')))
                $sort_type ='desc';
            if($sort_post && isset($fields_list[$sort_post]))
            {
                $sort .= $sort_post." ".($sort_type=='asc' ? ' ASC ' :' DESC ')." , ";
            }
            else
                $sort = "c.sort_order ASC,";
            
            //Paggination
            $id_parent = (int)Tools::getValue('id_parent');
            $page = (int)Tools::getValue('page');
            $totalRecords = (int)Ybc_blog_category_class::countCategoriesWithFilter($filter,$id_parent);
            $paggination = new Ybc_blog_paggination_class();            
            $paggination->total = $totalRecords;
            $paggination->url = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=category&list=true'.($id_parent ? '&id_parent='.(int)$id_parent:'').'&page=_page_'.$this->getUrlExtra($fields_list);
            $paggination->limit =  (int)Tools::getValue('paginator_ybc_category_select_limit',20);
            $paggination->name ='ybc_category';
            $totalPages = ceil($totalRecords / $paggination->limit);
            if($page > $totalPages)
                $page = $totalPages;
            $paggination->page = $page;
            $start = $paggination->limit * ($page - 1);
            if($start < 0)
                $start = 0;
            $categories = Ybc_blog_category_class::getCategoriesWithFilter($filter, $sort, $start, $paggination->limit,$id_parent);
            if($categories)
            {
                foreach($categories as &$cat)
                {
                    $cat['view_url'] = $this->getLink('blog',array('id_category' => $cat['id_category']));
                    if(Ybc_blog_category_class::getChildrenBlogCategories($cat['id_category'],false) )
                    {
                        $cat['child_view_url'] = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=category&list=true&id_parent='.(int)$cat['id_category'];
                    }
                    if($cat['thumb'] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$cat['thumb']))
                        $cat['thumb_link'] = '<img src="'._PS_YBC_BLOG_IMG_.'category/thumb/'.$cat['thumb'].'" style="width:40px;"/>';
                    elseif($cat['image'] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$cat['image']))
                        $cat['thumb_link'] = '<img src="'._PS_YBC_BLOG_IMG_.'category/'.$cat['image'].'" style="width:40px;"/>';
                    else
                        $cat['thumb_link']='';
                }
            }
            $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
            $paggination->style_links = $this->l('links');
            $paggination->style_results = $this->l('results');
            $thumb='';
            $lever=0;
            $listData = array(
                'name' => 'ybc_category',
                'actions' => array('edit', 'delete', 'view'),
                'currentIndex' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=category'.($paggination->limit!=20 ? '&paginator_ybc_category_select_limit='.$paggination->limit:''),
                'identifier' => 'id_category',
                'show_toolbar' => true,
                'show_action' => true,
                'title' => ($id_parent ? '<a href="'.$this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=category&list=true" title="'.$this->l('Categories').'">':'').$this->l('Categories').($id_parent ? '</a>' :''). ( $id_parent ?  $this->getThumbCategory($id_parent,$thumb,$lever):''),
                'fields_list' => $fields_list,
                'field_values' => $categories,
                'paggination' => $paggination->render(),
                'filter_params' => $this->getFilterParams($fields_list),
                'show_reset' => $show_reset,
                'totalRecords' => $totalRecords,
                'sort'=> $sort_post,
                'sort_type' => $sort_type,
            );            
            return $this->_html .= $this->renderList($listData);      
        }
        //Form
        if(($id_category =  (int)Tools::getValue('id_category')))
        {
            $blogCategory= new Ybc_blog_category_class($id_category);
        }
        else
            $blogCategory= new Ybc_blog_category_class();
        $blogcategoriesTree= Ybc_blog_category_class::getBlogCategoriesTree(0,true,$this->context->language->id,$id_category);
        $depth_level =-1;
        $this->getBlogCategoriesDropdown($blogcategoriesTree,$depth_level,$blogCategory->id_parent,$id_category);  
        $blogCategoryotpionsHtml = $this->blogCategoryDropDown;
        $fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Manage categories'),	
                    'icon' => 'icon-AdminCatalog',			
				),
				'input' => array(
                    array(
                        'type'=>'select_category',
                        'label'=>$this->l('Parent category'),
                        'name'=>'id_parent',
                        'blogCategoryotpionsHtml'=>$blogCategoryotpionsHtml,
                        'form_group_class'=>'parent_category',
                        'tab'=>'basic',
                    ),					
					array(
						'type' => 'text',
						'label' => $this->l('Category title'),
						'name' => 'title',
						'lang' => true,    
                        'required' => true,   
                        'class' => 'title',  
                        'tab'=>'basic', 
                        'desc' => $this->l('Invalid characters: <>;=#{}'),           
					), 
                    array(
						'type' => 'text',
						'label' => $this->l('Meta title'),
						'name' => 'meta_title',
						'lang' => true,        
                        'tab'=>'seo',            
					), 
                    array(
						'type' => 'textarea',
						'label' => $this->l('Meta description'),
						'name' => 'meta_description',
                        'lang' => true,	
                        'tab'=>'seo',
                        'desc' => $this->l('Should contain your focus keyword and be attractive. Meta description should be less than 300 characters.'),				
					),
                    array(
						'type' => 'tags',
						'label' => $this->l('Meta keywords'),
						'name' => 'meta_keywords',
                        'lang' => true,
                        'tab'=>'seo',
                        'hint' => array(
    						$this->l('To add "keywords" click in the field, write something, and then press "Enter."'),
    					),
                        'desc'=>$this->l('Enter your focus keywords and minor keywords'),						
					),
                    array(
						'type' => 'textarea',
						'label' => $this->l('Description'),
						'name' => 'description',
						'lang' => true,  
                        'tab'=>'basic',
                        'autoload_rte' => true,                      
					),
					array(
						'type' => 'text',
						'label' => $this->l('Url alias'),
						'name' => 'url_alias',
                        'required' => true,
                        'lang'=>true,
                        'tab'=>'seo',
                        'hint' => $this->l('Only letters and the hyphen (-) character are allowed.'),
                        'desc' => $this->l('Should be as short as possible and contain your focus keyword.').($id_category ? $this->displayText($this->l('View category'),'a','ybc_link_view',null,$this->getLink('blog',array('id_category'=>$id_category)),true):''),						
					),
                    array(
						'type' => 'file_lang',
						'label' => $this->l('Category thumbnail image'),
						'name' => 'thumb',
                        'imageType' => 'thumb',
                        'tab'=>'basic',
                        'desc' =>sprintf($this->l('Accepted formats: jpg, jpeg, png, gif. Limit: %dMb. Recommended size: %sx%s.'),Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),Configuration::get('YBC_BLOG_IMAGE_CATEGORY_THUMB_WIDTH',null,null,null,300),Configuration::get('YBC_BLOG_IMAGE_CATEGORY_THUMB_HEIGHT',null,null,null,170))						
					),
                    array(
						'type' => 'file_lang',
						'label' => $this->l('Main category image'),
						'name' => 'image',
                        'tab'=>'basic',
                        'desc' => sprintf($this->l('Accepted formats: jpg, jpeg, png, gif. Limit: %dMb. Recommended size: %sx%s.'),Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),Configuration::get('YBC_BLOG_IMAGE_CATEGORY_WIDTH',null,null,null,1920),Configuration::get('YBC_BLOG_IMAGE_CATEGORY_HEIGHT',null,null,null,750)),               						
					),
                    array(
						'type' => 'switch',
						'label' => $this->l('Enabled'),
						'name' => 'enabled',
                        'is_bool' => true,
                        'tab'=>'basic',
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						)					
					),
                    array(
                        'type' => 'hidden', 
                        'name' => 'control'
                    )
                ),
                'submit' => array(
					'title' => $this->l('Save'),
				)
            ),
		);
        $helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'saveCategory';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
			'fields_value' => $this->getFieldsValues(Ybc_blog_defines::$categoryFields,'id_category','Ybc_blog_category_class','saveCategory'),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
            'link' => $this->context->link,
            'cancel_url' => $this->baseAdminPath.'&control=category&list=true',
            'post_key' => 'id_category',
            'tab_category'=>true,
            'image_baseurl' =>_PS_YBC_BLOG_IMG_.'category/',
            'image_baseurl_thumb' => _PS_YBC_BLOG_IMG_.'category/thumb/',
            'addNewUrl' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=category',
		);
        
        if($id_category && ($category = new Ybc_blog_category_class($id_category)) && Validate::isLoadedObject($category) )
        {
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_category');
            if($category->image)
            {             
                $helper->tpl_vars['img_del_link'] = $this->baseAdminPath.'&id_category='.$id_category.'&delcategoryimage=true&control=category';                
            }
            if($category->thumb)
            {             
                $helper->tpl_vars['thumb_del_link'] = $this->baseAdminPath.'&id_category='.$id_category.'&delcategorythumb=true&control=category';                
            }
        }
		$helper->override_folder = '/';
        $this->_html .= $helper->generateForm(array($fields_form));			
    }
    private function _postCategory()
    {
        $errors = array();
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $id_category = (int)Tools::getValue('id_category');
        if($id_category && !Validate::isLoadedObject(new Ybc_blog_category_class($id_category)) && !Tools::isSubmit('list'))
            Tools::redirectAdmin($this->baseAdminPath);
        /**
         * Change status 
         */
         if(Tools::isSubmit('change_enabled'))
         {
            Hook::exec('actionUpdateBlog', array(
                'id_category' =>(int)$id_category,
            ));
            $status = (int)Tools::getValue('change_enabled') ?  1 : 0;
            $field = Tools::getValue('field');         
            if(($field == 'enabled' && $id_category))
            {
                Ybc_blog_defines::changeStatus('category',$field,$id_category,$status);
                if($status==1)
                    $title= $this->l('Click to disabled');
                else
                    $title=$this->l('Click to enabled');
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(array(
                        'listId' => $id_category,
                        'enabled' => $status,
                        'field' => $field,
                        'message' => $this->displaySuccessMessage($this->l('The status has been successfully updated')) ,
                        'messageType'=>'success',
                        'title'=>$title,
                        'href' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=category&change_enabled='.($status ? '0' : '1').'&field='.$field.'&id_category='.$id_category,
                    )));
                }
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=category&list=true');
            }
         }
        /**
         * Delete image 
         */         
         if($id_category && ($category = new Ybc_blog_category_class($id_category)) && Validate::isLoadedObject($category) && Tools::isSubmit('delcategoryimage'))
         {
            Hook::exec('actionUpdateBlog', array(
                'id_category' => (int)$id_category,
            ));
            $idLang = (int)Tools::getValue('id_lang');
            if(isset($category->image[$idLang]) && $category->image[$idLang] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$category->image[$idLang]))
            {
                $oldImage = $category->image[$idLang];
                $category->image[$idLang] = '';
                $category->datetime_modified = date('Y-m-d H:i:s');
                $category->modified_by = (int)$this->context->employee->id;
                if($category->update())
                {
                    if(!in_array($oldImage,$category->image) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$oldImage))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/'.$oldImage);
                }  
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(
                        array(
                            'messageType' => 'success',
                            'message' => $this->displayConfirmation($this->l('Category image deleted')),
                        )
                    ));
                }                 
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_category='.$id_category.'&control=category');
            }
            else
                $errors[] = $this->l('Image does not exist');   
         }
         if($id_category && ($category = new Ybc_blog_category_class($id_category)) && Validate::isLoadedObject($category)  && Tools::isSubmit('delcategorythumb'))
         {
            Hook::exec('actionUpdateBlog', array(
                'id_category' => (int)$id_category,
            ));
            $idLang = (int)Tools::getValue('id_lang');
            if(isset($category->thumb[$idLang]) && $category->thumb[$idLang] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$category->thumb[$idLang]))
            {
                $oldThumb = $category->thumb[$idLang];
                $category->thumb[$idLang] = '';
                $category->datetime_modified = date('Y-m-d H:i:s');
                $category->modified_by = (int)$this->context->employee->id;
                if($category->update())
                {
                    if(!in_array($oldThumb,$category->thumb) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$oldThumb) )
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$oldThumb);
                } 
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(
                        array(
                            'messageType' => 'success',
                            'message' => $this->displayConfirmation($this->l('Category thumbnail image deleted')),
                        )
                    ));
                }                 
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_category='.$id_category.'&control=category');
            }
            else
                $errors[] = $this->l('Thumbnail does not exist');   
         }
        /**
         * Delete category 
         */ 
         if(Tools::isSubmit('del'))
         {
                $id_category = (int)Tools::getValue('id_category');
                Hook::exec('actionUpdateBlog', array(
                    'id_category' => (int)$id_category,
                ));
                $category = new Ybc_blog_category_class($id_category);
                if(!Validate::isLoadedObject($category))
                    $errors[] = $this->l('Category does not exist');
                elseif($category->delete())
                {                
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=category&list=true');
                }                
                else
                    $errors[] = $this->l('Could not delete the category. Please try again');    
         }    
         if(($action = Tools::getValue('action')) && $action=='updateCategoryOrdering' && ($categories=Tools::getValue('cateogires')) && Ybc_blog::validateArray($categories,'isInt'))
         {
                $page = (int)Tools::getValue('page',1);
                if(Ybc_blog_category_class::updateCategoryOrdering($categories,$page))
                {
                    die(
                        json_encode(
                            array(
                                'page'=>$page,
                            )
                        )
                    );
                }

        }              
        /**
         * Save category 
         */
        if(Tools::isSubmit('saveCategory'))
        {       
            $id_parent = (int)Tools::getValue('id_parent');
            if($id_category && ($category = new Ybc_blog_category_class($id_category)) && Validate::isLoadedObject($category) )
            {
                Hook::exec('actionUpdateBlog', array(
                    'id_category' => (int)$id_category,
                ));
                $category->datetime_modified = date('Y-m-d H:i:s');
                $category->modified_by = (int)$this->context->employee->id;
                if($id_parent!=$category->id_parent)
                {
                    $category->sort_order = 1+ (int)Ybc_blog_category_class::getMaxSortOrder($id_parent);
                }
            }
            else
            {
                $category = new Ybc_blog_category_class();
                $category->datetime_added = date('Y-m-d H:i:s');
                $category->datetime_modified = date('Y-m-d H:i:s');
                $category->modified_by = (int)$this->context->employee->id;
                $category->added_by = (int)$this->context->employee->id;
                $category->sort_order = 1+ (int)Ybc_blog_category_class::getMaxSortOrder($id_parent);
            }
            $category->enabled = (int)trim(Tools::getValue('enabled',1)) ? 1 : 0;
            $category->id_parent = (int)$id_parent;
            $languages = Language::getLanguages(false);
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
            $title_default = trim(Tools::getValue('title_'.$id_lang_default));
            if(!$title_default)
                $errors[] = $this->l('Title is required');
            if($title_default && !Validate::isCleanHtml($title_default))
                $errors[] = $this->l('Title is not valid');
            $meta_title_default = Tools::getValue('meta_title_'.$id_lang_default);
            if($meta_title_default && !Validate::isCleanHtml($meta_title_default))
                $errors[] = $this->l('Meta title is not valid');
            $url_alias_default = Tools::getValue('url_alias_'.$id_lang_default);
            if(!$url_alias_default)
                $errors[] = $this->l('Url alias is required');
            if($url_alias_default && !Ybc_blog::checkIsLinkRewrite($url_alias_default))
                $errors[] = $this->l('Url alias is not valid');
            elseif($url_alias_default && Ybc_blog_category_class::checkUrlAliasExists($url_alias_default,$category->id) )
                $errors[] = $this->l('Url alias has already existed');
            $meta_description_default = Tools::getValue('meta_description_'.$id_lang_default);
            if($meta_description_default && !Validate::isCleanHtml($meta_description_default,true))
                $errors[] = $this->l('Meta description is not valid');
            $meta_keywords_default = Tools::getValue('meta_keywords_'.$id_lang_default);
            if($meta_keywords_default && !Validate::isTagsList($meta_keywords_default))
                $errors[] = $this->l('Meta keyword is not valid');
            $description_default = Tools::getValue('description_'.$id_lang_default);
            if($description_default && !Validate::isCleanHtml($description_default,true))
                $errors[] = $this->l('Description is not valid');
            if(!$errors)
            {
                foreach ($languages as $language)
    			{	
                    $id_lang = (int)$language['id_lang'];
                    $title = trim(Tools::getValue('title_'.$language['id_lang']));
                    if($title && !Validate::isCleanHtml($title))
                        $errors[] = sprintf($this->l('Title in %s is not valid'),$language['name']);
                    else
    			         $category->title[$language['id_lang']] = $title != '' ?  $title:  $title;
                    $meta_title = trim(Tools::getValue('meta_title_'.$language['id_lang']));
                    if($meta_title && !Validate::isCleanHtml($meta_title))
                        $errors[] = sprintf($this->l('Meta title in %s is not valid'),$language['name']);
                    else
                        $category->meta_title[$language['id_lang']] = $meta_title != '' ? $meta_title :  $meta_title_default;
                    $url_alias = trim(Tools::getValue('url_alias_'.$language['id_lang']));
                    if($url_alias && !Ybc_blog::checkIsLinkRewrite($url_alias))
                        $errors[] = sprintf($this->l('Url alias in %s is not valid'),$language['name']);
                    elseif($url_alias && Ybc_blog_category_class::checkUrlAliasExists($url_alias,$category->id) )
                        $errors[] = sprintf($this->l('Url alias in %s has already existed'),$language['name']);
                    else
                        $category->url_alias[$language['id_lang']] = $url_alias != '' ? $url_alias :  $url_alias_default;
                    $meta_description = Tools::getValue('meta_description_'.$id_lang);
                    if($meta_description && !Validate::isCleanHtml($meta_description, true))
                        $errors[] = sprintf($this->l('Meta description in %s is not valid'),$language['name']);
                    else
                        $category->meta_description[$language['id_lang']] = $meta_description != '' ? $meta_description :  $meta_description_default;
                    $meta_keywords = Tools::getValue('meta_keywords_'.$id_lang);
                    if($meta_keywords && !Validate::isTagsList($meta_keywords, true))
                        $errors[] = sprintf($this->l('Meta keywords in %s are not valid'),$language['name']);
                    else
                        $category->meta_keywords[$language['id_lang']] = $meta_keywords != '' ? $meta_keywords : $meta_keywords_default;
                    $description = Tools::getValue('description_'.$id_lang);
                    if($description && !Validate::isCleanHtml($description, true))
                        $errors[] = sprintf($this->l('Description in %s is not valid'),$language['name']);   
                    $category->description[$language['id_lang']] = $description != '' ? $description :  $description_default;
                                 	
                }
            }
            
            /**
             * Upload image 
             */  
            $oldImages = array();
            $newImages = array();  
            $oldThumbs = array();
            $newThumbs = array();
            $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024*1024;
            foreach($languages as $language)
            {
                if(isset($_FILES['image_'.$language['id_lang']]['tmp_name']) && isset($_FILES['image_'.$language['id_lang']]['name']) && $_FILES['image_'.$language['id_lang']]['name'])
                {
                    $_FILES['image_'.$language['id_lang']]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES['image_'.$language['id_lang']]['name']);
                    if(!Validate::isFileName($_FILES['image_'.$language['id_lang']]['name']))
                        $errors[] = sprintf($this->l('Image name is not valid in %s'),$language['iso_code']);
                    elseif($_FILES['image_'.$language['id_lang']]['size'] > $max_file_size)
                        $errors[] = sprintf($this->l('Image file is too large. Limit: %s'),Tools::ps_round($max_file_size/1048576,2).'Mb');
                    else
                    {
                        if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$_FILES['image_'.$language['id_lang']]['name']))
                        {
                            $_FILES['image_'.$language['id_lang']]['name'] = $this->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'category/',$_FILES['image_'.$language['id_lang']]['name']);
                        }
                        $type = Tools::strtolower(Tools::substr(strrchr($_FILES['image_'.$language['id_lang']]['name'], '.'), 1));
            			$imagesize = @getimagesize($_FILES['image_'.$language['id_lang']]['tmp_name']);
            			if (isset($_FILES['image_'.$language['id_lang']]) &&				
            				!empty($_FILES['image_'.$language['id_lang']]['tmp_name']) &&
            				!empty($imagesize) &&
            				in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
            			)
            			{
            				$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');    				
            				if ($error = ImageManager::validateUpload($_FILES['image_'.$language['id_lang']]))
            					$errors[] = $error;
            				elseif (!$temp_name || !move_uploaded_file($_FILES['image_'.$language['id_lang']]['tmp_name'], $temp_name))
            					$errors[] = $this->l('Cannot upload the file in').' '.$language['iso_code'];
            				elseif (!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'category/'.$_FILES['image_'.$language['id_lang']]['name'], Configuration::get('YBC_BLOG_IMAGE_CATEGORY_WIDTH',null,null,null,1920), Configuration::get('YBC_BLOG_IMAGE_CATEGORY_HEIGHT',null,null,null,750), $type))
            					$errors[] = $this->displayError($this->l('An error occurred during the image upload process in').' '.$language['iso_code']);
            				if (isset($temp_name) && file_exists($temp_name))
            					@unlink($temp_name);
                            if($category->image[$language['id_lang']])
                                $oldImages[$language['id_lang']] = $category->image[$language['id_lang']];
                            $category->image[$language['id_lang']] = $_FILES['image_'.$language['id_lang']]['name'];
                            $newImages[$language['id_lang']] = $category->image[$language['id_lang']];			
            			}
                        else
                            $errors[] = sprintf($this->l('Image is not valid in %s'),$language['iso_code']);
                    }
                    
                }
                if(isset($_FILES['thumb_'.$language['id_lang']]['tmp_name']) && isset($_FILES['thumb_'.$language['id_lang']]['name']) && $_FILES['thumb_'.$language['id_lang']]['name'])
                {
                    $_FILES['thumb_'.$language['id_lang']]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES['thumb_'.$language['id_lang']]['name']);
                    if(!Validate::isFileName($_FILES['thumb_'.$language['id_lang']]['name']))
                        $errors[] = sprintf($this->l('Thumbnail image name is not valid in %s'),$language['iso_code']);
                    elseif($_FILES['thumb_'.$language['id_lang']]['size'] > $max_file_size)
                        $errors[] = sprintf($this->l('Thumbnail image file is too large. Limit: %s'),Tools::ps_round($max_file_size/1048576,2).'Mb');
                    else
                    {
                        if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$_FILES['thumb_'.$language['id_lang']]['name']))
                        {
                            $_FILES['thumb_'.$language['id_lang']]['name'] = $this->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/',$_FILES['thumb_'.$language['id_lang']]['name']);
                        }
                        $type = Tools::strtolower(Tools::substr(strrchr($_FILES['thumb_'.$language['id_lang']]['name'], '.'), 1));
            			$imagesize = @getimagesize($_FILES['thumb_'.$language['id_lang']]['tmp_name']);
            			if (isset($_FILES['thumb_'.$language['id_lang']]) &&				
            				!empty($_FILES['thumb_'.$language['id_lang']]['tmp_name']) &&
            				!empty($imagesize) &&
            				in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
            			)
            			{
            				$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');    				
            				if ($error = ImageManager::validateUpload($_FILES['thumb_'.$language['id_lang']]))
            					$errors[] = $error;
            				elseif (!$temp_name || !move_uploaded_file($_FILES['thumb_'.$language['id_lang']]['tmp_name'], $temp_name))
            					$errors[] = $this->l('Cannot upload the file in').' '.$language['iso_code'];
            				elseif (!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$_FILES['thumb_'.$language['id_lang']]['name'], Configuration::get('YBC_BLOG_IMAGE_CATEGORY_THUMB_WIDTH',null,null,null,300), Configuration::get('YBC_BLOG_IMAGE_CATEGORY_THUMB_HEIGHT',null,null,null,170), $type))
            					$errors[] = $this->displayError($this->l('An error occurred during the image upload process in').' '.$language['iso_code']);
            				if (isset($temp_name) && file_exists($temp_name))
            					@unlink($temp_name);
                            if($category->thumb[$language['id_lang']])
                                $oldThumbs[$language['id_lang']] = $category->thumb[$language['id_lang']];
                            $category->thumb[$language['id_lang']] = $_FILES['thumb_'.$language['id_lang']]['name'];
                            $newThumbs[] = $category->thumb[$language['id_lang']];			
            			}
                        else
                            $errors[] = sprintf($this->l('Thumbnail image is not valid in %s'),$language['iso_code']);
                    }
                }
            }
            foreach($languages as $language)
            {
                if(!$category->image[$language['id_lang']])
                    $category->image[$language['id_lang']] = $category->image[$id_lang_default];
                if(!$category->thumb[$language['id_lang']])
                    $category->thumb[$language['id_lang']] = $category->thumb[$id_lang_default];
            }      
            /**
             * Save 
             */    
             
            if(!$errors)
            {
                if (!$id_category)
    			{
    				if (!$category->add())
                    {
                        $errors[] = $this->displayError($this->l('The category could not be added.'));
                        if($newImages)
                        {
                            foreach($newImages as $newImage)
                            {
                                if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$newImage))
                                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/'.$newImage);
                            }
                        }  
                        if($newThumbs)
                        {
                            foreach($newThumbs as $newThumb)
                            {
                                if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$newThumb))
                                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$newThumb);
                            }
                        }                  
                    }
                    else
                    {
                        $id_category = Ybc_blog_defines::getMaxId('category','id_category');
                        Hook::exec('actionUpdateBlogImage', array(
                            'id_category' =>(int)$category->id,
                            'image' => $newImages ? $category->image :false,
                            'thumb' => $newThumbs ? $category->thumb : false,
                        ));
                    }                	                    
    			}				
    			elseif (!$category->update())
                {
                    if($newImages)
                    {
                        foreach($newImages as $newImage)
                        {
                            if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$newImage))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/'.$newImage);
                        }
                    }  
                    if($newThumbs)
                    {
                        foreach($newThumbs as $newThumb)
                        {
                            if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$newThumb))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$newThumb);
                        }
                    }
                    $errors[] = $this->displayError($this->l('The category could not be updated.'));
                }
                else
                {
                    if($oldImages)
                    {
                        foreach($oldImages as $oldImage)
                        {
                            if(!in_array($oldImage,$category->image) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$oldImage))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/'.$oldImage);
                        }
                    }  
                    if($oldThumbs)
                    {
                        foreach($oldThumbs as $oldThumb)
                        {
                            if(!in_array($oldThumb,$category->thumb) &&  file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$oldThumb))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$oldThumb);
                        }
                    } 
                    Hook::exec('actionUpdateBlogImage', array(
                        'id_category' =>(int)$category->id,
                        'image' => $newImages ? $category->image :false,
                        'thumb' => $newThumbs ? $category->thumb : false,
                    ));
                }
    					                
            }
         }
         if (count($errors))
         {
            if($newImages)
            {
                foreach($newImages as $newImage)
                {
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$newImage))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/'.$newImage);
                }
            }  
            if($newThumbs)
            {
                foreach($newThumbs as $newThumb)
                {
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$newThumb))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$newThumb);
                }
            }
            $this->errorMessage = $this->displayError($errors);  
         }
         $changedImages = array();
         if(!$errors && isset($newImages) && $newImages && isset($category) && $category->id){
            foreach($newImages as $id_lang=> $newImage)
            {
                $changedImages[] = array(
                    'name' => 'image_'.$id_lang,
                    'url' => _PS_YBC_BLOG_IMG_.'category/'.$newImage,
                    'delete_url' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_category='.$id_category.'&delcategoryimage=true&control=category&id_lang='.$id_lang,
                );
            }
         } 
         if(!$errors && isset($newThumbs) && $newThumbs && isset($category) && $category->id){
                foreach($newThumbs as $id_lang => $newThumb)
                {
                    $changedImages[] = array(
                        'name' => 'thumb_'.$id_lang,
                        'url' => _PS_YBC_BLOG_IMG_.'category/thumb/'.$newThumb,
                        'delete_url' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_category='.$id_category.'&delcategorythumb=true&control=category&id_lang='.$id_lang,
                    );
                }
         }        
         if(Tools::isSubmit('ajax'))
            {
                die(json_encode(
                    array(
                        'messageType' => $errors ? 'error' : 'success',
                        'message' => $errors ? $this->errorMessage : (isset($id_category) && $id_category ? $this->displaySuccessMessage($this->l('Category updated'),$this->l('View category'),$this->getLink('blog',array('id_category'=>$id_category))) : $this->displayConfirmation($this->l('Category updated'))),
                        'images' => isset($changedImages) && $changedImages ? $changedImages : array(),
                        'postUrl' => !$errors && Tools::isSubmit('saveCategory') && !(int)$id_category ? $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_category='.Ybc_blog_defines::getMaxId('category','id_category').'&control=category' : 0,
                        'itemKey' => 'id_category',
                        'itemId' => !$errors && Tools::isSubmit('saveCategory') && !(int)$id_category ? Ybc_blog_defines::getMaxId('category','id_category') : ((int)$id_category > 0 ? (int)$id_category : 0),
                    )
                ));
            } 
         if (Tools::isSubmit('saveCategory') && Tools::isSubmit('id_category'))
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_category='.$id_category.'&control=category');
		 elseif (Tools::isSubmit('saveCategory'))
         {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=3&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_category='.Ybc_blog_defines::getMaxId('category','id_category').'&control=category');
         }
    }
    public function displaySuccessMessage($msg, $title = false, $link = false)
    {

         $this->context->smarty->assign(array(
            'msg' => $msg,
            'title' => $title,
            'link' => $link
         ));
         if($msg)
            return $this->displayConfirmation($this->display(__FILE__, 'success_message.tpl'));
    }
    /**
     * Post 
     */
    public function renderPostListByCustomer()
    {
        if(!Tools::isSubmit('editpost') && !Tools::isSubmit('addpost'))
        {
            $fields_list = array(
                'id_post' => array(
                    'title' => $this->l('Id'),
                    'width' => 40,
                    'type' => 'text',
                    'sort' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','sort'=>'id_post','sort_type'=>'asc')),
                    'sort_desc' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','sort'=>'id_post','sort_type'=>'desc')),
                    'filter' => true,
                ),
                'thumb_link'=>array(
                    'title'=> $this->l('Image'),
                    'type' => 'text',
                    'strip_tag'=>false,
                ),
                'title' => array(
                    'title' => $this->l('Title'),
                    'type' => 'text',
                    'sort' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','sort'=>'title','sort_type'=>'asc')),
                    'sort_desc' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','sort'=>'title','sort_type'=>'desc')),
                    'filter' => true,
                    'strip_tag'=>false,
                ),
                'total_comment' => array(
                    'title' => $this->l('Comments'),
                    'type' => 'text',
                    'sort' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','sort'=>'total_comment','sort_type'=>'asc')),
                    'sort_desc' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','sort'=>'total_comment','sort_type'=>'desc')),
                ),
                'enabled' => array(
                    'title' => $this->l('Status'),
                    'type' => 'active',
                    'sort' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','sort'=>'enable','sort_type'=>'asc')),
                    'sort_desc' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','sort'=>'enable','sort_type'=>'desc')),
                    'filter' => true,
                    'strip_tag' => false,
                    'filter_list' => array(
                        'id_option' => 'enabled',
                        'value' => 'title',
                        'list' => array(
                            0 => array(
                                'enabled' => 1,
                                'title' => $this->l('Published')
                            ),
                            1=>array(
                                'enabled'=>-1,
                                'title' =>$this->l('Pending')
                            ),
                            2 => array(
                                'enabled' => 0,
                                'title' => $this->l('Unpublished')
                            ),
                            3 => array(
                                'enabled' => 2,
                                'title' => $this->l('Schedule publish date')
                            ),
                        )
                    )
                )
            );
            //Filter
            $filter=" AND p.added_by =".(int)$this->context->customer->id." AND p.is_customer=1";
            $show_reset = false;
            if(($idPost = trim(Tools::getValue('id_post')))!='' && Validate::isCleanHtml($idPost)){
                $filter .= " AND p.id_post = ".(int)$idPost;
                $show_reset = true;
            }
            if(($sort_order = trim(Tools::getValue('sort_order')))!='' && Validate::isCleanHtml($sort_order))
            {
                $filter .= " AND p.sort_order = ".(int)$sort_order;
                $show_reset = true;
            }
            if(($click_number = trim(Tools::getValue('click_number')))!='' && Validate::isCleanHtml($click_number))
            {
                $filter .= " AND p.click_number = ".(int)$click_number;
                $show_reset = true;
            }
            if(($likes = trim(Tools::getValue('likes')))!='' && Validate::isCleanHtml($likes))
            {
                $filter .= " AND p.likes = ".(int)$likes;
                $show_reset = true;
            }
            if(($title = trim(Tools::getValue('title')))!='' && Validate::isCleanHtml($title))
            {
                $filter .= " AND pl.title like '%".pSQL($title)."%'";
                $show_reset = true;
            }
            if(($description = trim(Tools::getValue('description')))!='' && Validate::isCleanHtml($description))
            {
                $filter .= " AND pl.description like '%".pSQL($description)."%'";
                $show_reset = true;
            }
            if(($idCategory = trim(Tools::getValue('id_category')))!='' && Validate::isCleanHtml($idCategory))
            {
                $filter .= " AND p.id_post IN (SELECT id_post FROM `"._DB_PREFIX_."ybc_blog_post_category` WHERE id_category = ".(int)$idCategory.") ";
                $show_reset = true;
            }
            if(($enabled = trim(Tools::getValue('enabled')))!='' && Validate::isCleanHtml($enabled))
            {
                $filter .= " AND p.enabled = ".(int)$enabled;
                $show_reset = true;
            }
            if(($is_featured = trim(Tools::getValue('is_featured')))!='' && Validate::isCleanHtml($is_featured))
            {
                $filter .= " AND p.is_featured = ".(int)$is_featured;
            }
            //Sort
            $sort = "";
            $sort_post = Tools::strtolower(Tools::getValue('sort'));
            $sort_type = Tools::strtolower(Tools::getValue('sort_type','desc'));
            if(!in_array($sort_type,array('desc','asc')))
                $sort_type = 'desc';
            if($sort_post && isset($fields_list[$sort_post]))
            {
                $sort .= $sort_post." ".($sort_type=='asc' ? ' ASC ' :' DESC ')." , ";
            }
            else
                $sort = false;
            //Paggination
            $page = (int)Tools::getValue('page');
            if($page<1)
                $page=1;
            $totalRecords = (int)Ybc_blog_post_class::countPostsWithFilter($filter);
            $paggination = new Ybc_blog_paggination_class();            
            $paggination->total = $totalRecords;
            $paggination->url = $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','page'=>'_page_',)).$this->getUrlExtraFrontEnd($fields_list,'ybc_submit_ybc_post');
            $paggination->limit =  (int)Tools::getValue('paginator_ybc_post_select_limit',20);
            $totalPages = ceil($totalRecords / $paggination->limit);
            if($page > $totalPages)
                $page = $totalPages;
            $paggination->page = $page;
            $start = $paggination->limit * ($page - 1);
            if($start < 0)
                $start = 0;
            $posts = Ybc_blog_post_class::getPostsWithFilter($filter, $sort, $start, $paggination->limit);
            if($posts)
            {
                foreach($posts as &$post)
                {
                    $post['id_category'] = $this->getCategoriesStrByIdPost($post['id_post']);
                    $post['view_url'] = $this->getLink('blog',array('id_post'=>$post['id_post']));
                    $post['title']= '<a href="'.$post['view_url'].'" title="'.$post['title'].'">'.$post['title'].'</a>';
                    if(($privileges= explode(',',Configuration::get('YBC_BLOG_AUTHOR_PRIVILEGES'))) && in_array('edit_blog',$privileges))
                    {
                        $post['edit_url'] = $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','editpost'=>1,'id_post'=>$post['id_post']));
                    }
                    if(($privileges= explode(',',Configuration::get('YBC_BLOG_AUTHOR_PRIVILEGES'))) && in_array('delete_blog',$privileges))
                    {
                        $post['delete_url'] = $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','deletepost'=>1,'id_post'=>$post['id_post']));
                    }
                }
            }
            $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
            $paggination->style_links = $this->l('links');
            $paggination->style_results = $this->l('results');
            $listData = array(
                'name' => 'ybc_post',
                'actions' => array('edit', 'delete', 'view'),
                'currentIndex' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post')).($paggination->limit!=20 ? '&paginator_ybc_post_select_limit='.$paggination->limit:''),
                'identifier' => 'id_post',
                'show_toolbar' => true,
                'show_action' => true,
                'title' => $this->l('Blog posts'),
                'fields_list' => $fields_list,
                'field_values' => $posts,
                'paggination' => $paggination->render(),
                'filter_params' => $this->getFilterParamsFontEnd($fields_list,'ybc_submit_ybc_post'),
                'show_reset' => $show_reset,
                'totalRecords' => $totalRecords,
                'totalPost' => (int)Ybc_blog_post_class::countPostsWithFilter(" AND p.added_by =".(int)$this->context->customer->id." AND p.is_customer=1"),
                'preview_link' => $this->getLink('blog'),
                'show_add_new' => true,
                'link_addnew'=> $this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'post','addpost'=>1)),
                'sort'=> $sort_post,
                'sort_type'=> $sort_type,
                                
            );            
            return $this->renderListPostByCustomer($listData);
        }
        else
            return $this->displayFormBlog();
    }
    public function renderPostForm($filter='',$list=false)
    {
        //List
        $show_reset=false;
        if(Tools::isSubmit('list') || $list)
        {
            $fields_list = array(
                'id_post' => array(
                    'title' => $this->l('Id'),
                    'width' => 40,
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true,
                ),
                'thumb_link'=>array(
                    'title'=> $this->l('Image'),
                    'type' => 'text',
                    'strip_tag'=>false,
                ),
                'title' => array(
                    'title' => $this->l('Title'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true
                ),
                'id_category' => array(
                    'title' => $this->l('Categories'),
                    'type' => 'select',
                    'sort' => true,
                    'filter' => true,
                    'strip_tag' => false,
                    'filter_list' => array(
                        'id_option' => 'id_category',
                        'value' => 'title',
                        'list' => Ybc_blog_category_class::getCategories()
                    )
                ),
                'name_author'=>(
                      array(
                        'title'=>$this->l('Author'),
                        'type' => 'text',
                        'filter'=>true,
                        'strip_tag'=>false,
                      )
                ),
                'sort_order' => array(
                    'title' => $this->l('Sort order'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true,
                    'update_position' => true,
                ),
                'position' => array(
                    'title' => $this->l('Sort order'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true,
                    'update_position' => true,
                ),
                'click_number' => array(
                    'title' => $this->l('Views'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true,
                ),
                'likes' => array(
                    'title' => $this->l('Likes'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true,
                ),
                'enabled' => array(
                    'title' => $this->l('Status'),
                    'type' => 'active',
                    'sort' => true,
                    'filter' => true,
                    'strip_tag' => false,

                    'filter_list' => array(
                        'id_option' => 'enabled',
                        'value' => 'title',
                        'list' => array(
                            0 => array(
                                'enabled' => 1,
                                'title' => $this->l('Published')
                            ),
                            1=>array(
                                'enabled'=>-1,
                                'title' =>$this->l('Pending')
                            ),
                            2 => array(
                                'enabled' => 0,
                                'title' => $this->l('Disabled')
                            ),
                            2 => array(
                                'enabled' => -2,
                                'title' => $this->l('Preview')
                            ),
                            3=>array(
                                'enabled'=>2,
                                'title' =>$this->l('Schedule publish date')
                            )
                        )
                    )
                ),
                'is_featured' => array(
                    'title' => $this->l('Featured'),
                    'type' => 'active',
                    'sort' => true,
                    'filter' => true,
                    'strip_tag' => false,
                    'filter_list' => array(
                        'id_option' => 'is_featured',
                        'value' => 'title',
                        'list' => array(
                            0 => array(
                                'is_featured' => 1,
                                'title' => $this->l('Yes')
                            ),
                            1 => array(
                                'is_featured' => 0,
                                'title' => $this->l('No')
                            )
                        )
                    )
                ),
            );
            if(($idCategory = trim(Tools::getValue('id_category')))!='' && Validate::isInt($idCategory))
                unset($fields_list['sort_order']);
            else
                unset($fields_list['position']);
            //Filter
            if(($idPost = trim(Tools::getValue('id_post')))!='' && Validate::isCleanHtml($idPost))
                $filter .= " AND p.id_post = ".(int)$idPost;
            if(($sort_order = trim(Tools::getValue('sort_order')))!='' && Validate::isCleanHtml($sort_order))
                $filter .= " AND p.sort_order = ".(int)$sort_order;
            if(($click_number = trim(Tools::getValue('click_number')))!='' && Validate::isCleanHtml($click_number))
                $filter .= " AND p.click_number = ".(int)$click_number;
            if(($likes = trim(Tools::getValue('likes')))!='' && Validate::isCleanHtml($likes))
                $filter .= " AND p.likes = ".(int)$likes;
            if(($title = trim(Tools::getValue('title')))!='' && Validate::isCleanHtml($title))
                $filter .= " AND pl.title like '%".pSQL($title)."%'";
            if(($description = trim(Tools::getValue('description')))!='' && Validate::isCleanHtml($description))
                $filter .= " AND pl.description like '%".pSQL($description)."%'";
            if(($id_category = trim(Tools::getValue('id_category')))!='' && Validate::isCleanHtml($id_category))
                $filter .= " AND p.id_post IN (SELECT id_post FROM `"._DB_PREFIX_."ybc_blog_post_category` WHERE id_category = ".(int)$id_category.") ";
            if(($enabled = trim(Tools::getValue('enabled')))!='' && Validate::isCleanHtml($enabled))
                $filter .= " AND p.enabled = ".(int)$enabled;
            if(($is_featured = trim(Tools::getValue('is_featured')))!='' && Validate::isCleanHtml($is_featured))
                $filter .= " AND p.is_featured = ".(int)$is_featured;
            if(($name_author = trim(Tools::getValue('name_author')))!='' && Validate::isCleanHtml($name_author))
                $filter .=" AND (CONCAT(e.firstname,' ', e.lastname) like '%".pSQL($name_author)."%' OR CONCAT(c.firstname,' ', c.lastname) like '%".pSQL($name_author)."%')";
            //Sort
            $sort = 'p.id_post DESC,';
            $sort_post = Tools::strtolower(trim(Tools::getValue('sort')));
            $sort_type = Tools::strtolower(Tools::getValue('sort_type','desc'));
            if($sort_post && isset($fields_list[$sort_post]))
            {
                $sort = $sort_post." ".($sort_type=='asc' ? ' ASC ' :' DESC ')." , ";
            }
            if($filter)
                $show_reset=true;

            //Paggination
            $page = (int)Tools::getValue('page');
            if($page<=1)
                $page =1;
            $totalRecords = (int)Ybc_blog_post_class::countPostsWithFilter($filter,false);
            $paggination = new Ybc_blog_paggination_class();
            $paggination->total = $totalRecords;
            $paggination->url = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=post&list=true&page=_page_'.$this->getUrlExtra($fields_list);
            $paggination->limit =  (int)Tools::getValue('paginator_ybc_post_select_limit',20);
            $paggination->name ='ybc_post';
            $totalPages = ceil($totalRecords / $paggination->limit);
            if($page > $totalPages)
                $page = $totalPages;
            $paggination->page = $page;
            $start = $paggination->limit * ($page - 1);
            if($start < 0)
                $start = 0;
            $posts = Ybc_blog_post_class::getPostsWithFilter($filter, $sort, $start, $paggination->limit,false);
            if($posts)
            {
                foreach($posts as &$post)
                {
                    $post['id_category'] = $this->getCategoriesStrByIdPost($post['id_post']);
                    $url = $this->getLink('blog',array('id_post'=>$post['id_post']));
                    if($post['enabled']==-2)
                    {
                        if(Tools::strpos('?',$url)!==false)
                            $url .= '&preview=1';
                        else
                            $url .= '?preview=1';
                    }
                    $post['view_url'] = $url;
                }
            }
            $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
            $paggination->style_links = $this->l('links');
            $paggination->style_results = $this->l('results');
            $listData = array(
                'name' => 'ybc_post',
                'actions' => array('edit', 'delete', 'view'),
                'currentIndex' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=post'.($paggination->limit!=20 ? '&paginator_ybc_post_select_limit='.$paggination->limit:''),
                'identifier' => 'id_post',
                'show_toolbar' => true,
                'show_action' => true,
                'title' => $this->l('Posts'),
                'fields_list' => $fields_list,
                'field_values' => $posts,
                'paggination' => $paggination->render(),
                'filter_params' => $this->getFilterParams($fields_list),
                'show_reset' =>  $show_reset,
                'totalRecords' => $totalRecords,
                'preview_link' => $this->getLink('blog'),
                'sort' => $sort_post ? : 'id_post',
                'sort_type' => $sort_type,
            );
            return $list? $this->renderList($listData): $this->_html .= $this->renderList($listData);
        }
        //Form
        $id_post = (int)Tools::getValue('id_post');
        $fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Manage posts'),
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Post title'),
						'name' => 'title',
						'lang' => true,
                        'required' => true,
                        'tab'=>'basic',
                        'class' => 'title',
					),
                    array(
                        'type' => 'text',
						'label' => $this->l('Meta title'),
						'name' => 'meta_title',
						'lang' => true,
                        'tab'=>'seo',
                        'desc' => $this->l('Should contain your focus keyword and be attractive'),
                    ),
                    array(
						'type' => 'textarea',
						'label' => $this->l('Meta description'),
						'name' => 'meta_description',
                        'lang' => true,
                        'tab'=>'seo',
                        'desc' => $this->l('Should contain your focus keyword and be attractive. Meta description should be less than 300 characters.'),
					),
                    array(
						'type' => 'tags',
						'label' => $this->l('Meta keywords'),
						'name' => 'meta_keywords',
                        'lang' => true,
                        'tab'=>'seo',
                        'hint' => array(
    						$this->l('To add "keywords" click in the field, write something, and then press "Enter."'),
    					),
                        'desc'=>$this->l('Enter your focus keywords and minor keywords'),
					),
                    array(
						'type' => 'text',
						'label' => $this->l('Url alias'),
						'name' => 'url_alias',
                        'required' => true,
                        'lang'=>true,
                        'tab'=>'seo',
                        'desc' => $this->l('Should be as short as possible and contain your focus keyword.').($id_post ? $this->displayText($this->l('View post'),'a','ybc_link_view',null,$this->getLink('blog',array('id_post'=>$id_post)),true):''),
					),
                    array(
						'type' => 'tags',
						'label' => $this->l('Tags'),
						'name' => 'tags',
                        'lang' => true,
                        'tab'=>'option',
                        'hint' => array(
    						$this->l('To add "tags" click in the field, write something, and then press "Enter."'),
    					),
                        'desc'=>$this->l('Tags are separated by a comma. Related posts are the posts in the same tag or in the same post categories.'),
					),
                    array(
						'type' => 'textarea',
						'label' => $this->l('Short description'),
						'name' => 'short_description',
						'lang' => true,
                        'required' => true,
                        'autoload_rte' => true,
                        'tab'=>'basic',
                        'desc' => $this->l('Short description is displayed in post listing pages'),
					),
                    array(
						'type' => 'textarea',
						'label' => $this->l('Post content'),
						'name' => 'description',
						'lang' => true,
                        'autoload_rte' => true,
                        'required' => true,
                        'tab'=>'basic',
                        'desc' => $this->l('Post content is displayed in post details page (single page).'),
					),
                    array(
						'type' => 'file_lang',
						'label' => $this->l('Post thumbnail'),
						'name' => 'thumb',
                        'imageType' => 'thumb',
                        'required' => true,
                        'tab'=>'basic',
                        'desc' => sprintf($this->l('Accepted formats: jpg, jpeg, png, gif. Limit: %dMb. Recommended size: %sx%s. Post thumbnail image is required. You should adjust your image to the recommended size before uploading it.'),Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_WIDTH',null,null,null,260),Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_HEIGHT',null,null,null,180)),
					),
                    array(
						'type' => 'file_lang',
						'label' => $this->l('Blog post main image'),
						'name' => 'image',
                        'tab'=>'basic',
                        'desc' => sprintf($this->l('Accepted formats: jpg, jpeg, png, gif. Limit: %dMb. Recommended size: %sx%s.'),Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),Configuration::get('YBC_BLOG_IMAGE_BLOG_WIDTH',null,null,null,1920),Configuration::get('YBC_BLOG_IMAGE_BLOG_HEIGHT',null,null,null,750))
					),
                    array(
    					'type' => 'blog_categories',
    					'label' => $this->l('Post categories'),
                        'html_content' =>$this->displayBlogCategoryTre(Ybc_blog_category_class::getBlogCategoriesTree(0),$this->getSelectedCategories($id_post)),
    					'categories' => Ybc_blog_category_class::getBlogCategoriesTree(0),
    					'name' => 'categories',
                        'required' => true,
                        'tab'=>'basic',
                        'selected_categories' => $this->getSelectedCategories($id_post)
    				),
                    array(
                        'type'  => 'categories2',
                        'label' => $this->l('Related product'),
                        'name'  => 'product_categories',
                        'tab'=>'option',
                        'tree'  => array(
                            'id'      => 'product-categories-tree',
                            'selected_categories' => Ybc_blog_category_class::getSelectedRelatedProductCategories($id_post),
                            'use_search' => true,
                            'use_checkbox' => true,
                        ),
                    ),
                    array(
						'type' => 'products_search',
						'label' => $this->l('Include products'),
						'name' => 'products',
                        'selected_products' => $this->getSelectedProducts($id_post),
                        'tab'=>'option',
                    ),
                    array(
                        'type' => 'exclude_products',
                        'label' => $this->l('Exclude products'),
                        'name' => 'exclude_products',
                        'selected_products' => $this->getExcludeProducts($id_post),
                        'tab'=>'option',
                    ),
                    array(
    					'type'  => 'categories',
    					'label' => $this->l('Related product categories'),
    					'name'  => 'related_categories',
                        'tab'=>'option',
    					'tree'  => array(
    						'id'      => 'categories-tree',
    						'selected_categories' => Ybc_blog_category_class::getSelectedRelatedCategories($id_post),
                            'use_search' => true,
                            'use_checkbox' => true,
    					),
                        'showRequired' => true,
                        'desc' => $this->l('Check on product categories that you want to display this post on their "Related posts" section on the front office'),
    				),
                    array(
						'type' => 'text',
						'label' => $this->l('Views'),
						'name' => 'click_number',
                        'required' => true,
                        'tab'=>'option',
                        'desc' => $this->l('The number of post view will be increased from this number'),
					),
                    array(
						'type' => 'text',
						'label' => $this->l('Likes'),
						'name' => 'likes',
                        'required' => true,
                        'tab'=>'option',
                        'desc' => $this->l('The number of post likes will be increased from this number'),
					),
                    array(
						'type' => 'switch',
						'label' => $this->l('Is featured post'),
						'name' => 'is_featured',
                        'is_bool' => true,
                        'tab'=>'option',
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
                        'desc' => $this->l('Enable this if you want to display this post in "Featured posts" section on the front office')
					),
                    array(
						'type' => 'select',
						'label' => $this->l('Status'),
						'name' => 'enabled',
                        'tab'=>'basic',
						'options' => array(
                            'query' => array(
                                    array(
                                        'id_option' => 1,
                                        'name' => $this->l('Published')
                                    ),
                                    array(
                                        'id_option' => -1,
                                        'name' => $this->l('Pending')
                                    ),
                                    array(
                                        'id_option'=>-2,
                                        'name' => $this->l('Preview'),
                                    ),
                                    array(
                                        'id_option' => 0,
                                        'name' => $this->l('Disabled')
                                    ),
                                    array(
                                        'id_option' => 2,
                                        'name' => $this->l('Schedule publish date')
                                    ),
                                ),
                             'id' => 'id_option',
                			 'name' => 'name'
                        ),
					),
                    array(
						'type' => 'datetime',
						'label' => $this->l('Publish date'),
						'name' => 'datetime_added',
                        'tab'=>'basic',
					),
                    array(
						'type' => 'date',
						'label' => $this->l('Schedule publish date'),
						'name' => 'datetime_active',
                        'tab'=>'basic',
                        'required2'=>true,
                        'desc'=> $this->l('You can select the time to automatically publish this post. Leave blank to save this post as draft'),
					),
                    array(
                        'type' => 'hidden',
                        'name' => 'control'
                    )
                ),
                'submit' => array(
					'title' => $this->l('Save'),
				),
                'buttons'=> array(
                    array(
                        'type'=>'submit',
                        'name' =>'submitSaveAndPreview',
                        'title' => $this->l('Save and preview'),
                        'class' => $id_post && isset($post) && $post->enabled!=-2 ? 'pull-right hide':'pull-right',
                        'icon'=>'process-icon-save',
                    )
                ),
            ),
		);
        if (isset($fields_form['form']['input'])) {
            foreach ($fields_form['form']['input'] as $key => &$params) {
                if($params['type']=='categories2')
                {
                    if (!isset($params['tree']['id'])) {
                        throw new PrestaShopException('Id must be filled for categories tree');
                    }
                    $tree = new HelperTreeCategories($params['tree']['id'], isset($params['tree']['title']) ? $params['tree']['title'] : null);
                    if (isset($params['name'])) {
                        $tree->setInputName($params['name']);
                    }
                    if (isset($params['tree']['selected_categories'])) {
                        $tree->setSelectedCategories($params['tree']['selected_categories']);
                    }

                    if (isset($params['tree']['disabled_categories'])) {
                        $tree->setDisabledCategories($params['tree']['disabled_categories']);
                    }

                    if (isset($params['tree']['root_category'])) {
                        $tree->setRootCategory($params['tree']['root_category']);
                    }

                    if (isset($params['tree']['use_search'])) {
                        $tree->setUseSearch($params['tree']['use_search']);
                    }

                    if (isset($params['tree']['use_checkbox'])) {
                        $tree->setUseCheckBox($params['tree']['use_checkbox']);
                    }
                    if (isset($params['tree']['set_data'])) {
                        $tree->setData($params['tree']['set_data']);
                    }
                    $this->context->smarty->assign('categories_tree2', $tree->render());
                    break;
                }
            }
        }
        $helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'savePost';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = $this->context->employee->id ? Tools::getAdminTokenLite('AdminModules'): false;
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
			'fields_value' => $this->getFieldsValues(Ybc_blog_defines::$postFields,'id_post','Ybc_blog_post_class','savePost'),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
            'link' => $this->context->link,
            'post_key' => 'id_post',
            'tab_post' => true,
            'check_suspend' => Ybc_blog_post_class::checkPostSuspend($id_post),
            'form_author_post' => $this->getFormAuthorPost($id_post),
            'cancel_url' => $this->baseAdminPath.'&control=post&list=true',
            'image_baseurl' => _PS_YBC_BLOG_IMG_.'post/',
            'image_baseurl_thumb' => _PS_YBC_BLOG_IMG_.'post/thumb/',
            'addNewUrl' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=post',
            'preview_link' => $id_post ? $this->getLink('blog',array('id_post'=>$id_post)):'',
		);
        if($id_post && ($post = new Ybc_blog_post_class((int)$id_post)) && Validate::isLoadedObject($post) )
        {
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_post');
            if($post->image)
            {
                $helper->tpl_vars['img_del_link'] = $this->baseAdminPath.'&id_post='.$id_post.'&delpostimage=true&control=post';
            }
            if($post->thumb)
            {
                $helper->tpl_vars['thumb_del_link'] = $this->baseAdminPath.'&id_post='.$id_post.'&delpostthumb=true&control=post';
            }
        }

		$helper->override_folder = '/';
        $this->_html .= $helper->generateForm(array($fields_form));
    }

    public function getFormAuthorPost($id_post)
    {
        if($id_post && ($post = new Ybc_blog_post_class($id_post)) && Validate::isLoadedObject($post))
        {
            $this->context->smarty->assign(
                array(
                    'YBC_BLOG_ALLOW_CUSTOMER_AUTHOR' => Configuration::get('YBC_BLOG_ALLOW_CUSTOMER_AUTHOR') && Ybc_blog_post_employee_class::countCustomersFilter(false),
                    'admin_authors' => Ybc_blog_post_employee_class::getAuthors(),
                    'post'=> Ybc_blog_post_class::getPostByID($id_post),
                    'author' => Ybc_blog_post_class::getAuthorByIdPost($id_post)
                )
            );
            return $this->display(__FILE__,'form_author_post.tpl');
        }
        return '';
    }
    private function _postCustomer()
    {
        $errors=array();
        if(Tools::isSubmit('deleteAllPostCustomer') && ($id_author = (int)Tools::getValue('id_author')))
        {
            Hook::exec('actionUpdateBlog', array(
                'id_author' => (int)$id_author,
            ));
            if(Ybc_blog_post_class::deleteAllPostCustomerByIdAuthor($id_author))
            {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=customer&list=true');
            }

        }
        if(Tools::isSubmit('delemployeeimage') && ($id_customer = (int)Tools::getValue('id_customer')))
        {
            $id_employee_post= Ybc_blog_post_employee_class::getIdEmployeePostById($id_customer);
            Hook::exec('actionUpdateBlog', array(
                'id_author' =>(int)$id_customer,
            ));
            $employeePost = new Ybc_blog_post_employee_class($id_employee_post);
            $employeePost->avata='';
            if($employeePost->update())
            {
                if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'avata/'.$employeePost->avata))
                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'avata/'.$employeePost->avata);
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(
                        array(
                            'messageType' => 'success',
                            'message' => $this->displayConfirmation($this->l('Avatar image deleted')),
                        )
                    ));
                }
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=post');
            }

                 
        }
        if(Tools::isSubmit('change_enabled'))
        {
            $status = (int)Tools::getValue('change_enabled');
            $field = Tools::getValue('field');
            $id_customer = (int)Tools::getValue('id_customer');  
            Hook::exec('actionUpdateBlog', array(
                'id_author' =>(int)$id_customer,
            ));
            $id_employee_post = (int)Ybc_blog_post_employee_class::getIdEmployeePostById($id_customer);
            if(($field == 'status' && $id_customer))
            {
                if($id_employee_post)
                {
                    $employeePost= new Ybc_blog_post_employee_class($id_employee_post);
                    $employeePost->status=$status;
                    $employeePost->update();
                }
                else
                {
                    $employeePost = new Ybc_blog_post_employee_class();
                    $employeePost->status=$status;
                    $customer = new Customer($id_customer);
                    $employeePost->id_employee = $id_customer;
                    $employeePost->is_customer=1;
                    $employeePost->name = $customer->firstname.' '.$customer->lastname;
                    $employeePost->add();
                }  
                if($status==1)
                    $title= $this->l('Click to suspend'); 
                else
                    $title= $this->l('Click to active');
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(array(
                        'listId' => $id_customer,
                        'enabled' => $status,
                        'field' => $field,
                        'message' => $this->displaySuccessMessage($this->l('The status has been successfully updated')),
                        'messageType'=>'success',
                        'title'=>$title,
                        'href' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=customer&change_enabled='.($status ? '0' : '1').'&field='.$field.'&id_customer='.$id_customer,
                    )));
                }  
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=customer&list=true');
            }
        }
        if(($id_customer=(int)Tools::getValue('id_customer')))
        {
            $id_employee_post= (int)Ybc_blog_post_employee_class::getIdEmployeePostById($id_customer);
            if(Tools::isSubmit('saveBlogEmployee'))
            {
                Hook::exec('actionUpdateBlog', array(
                    'id_author' =>(int)$id_customer,
                ));
                if($id_employee_post)
                {
                    $employeePost= new Ybc_blog_post_employee_class($id_employee_post);
                }
                else
                    $employeePost = new Ybc_blog_post_employee_class();
                $employeePost->id_employee=$id_customer;
                $employeePost->is_customer=1;
                $employeePost->status = (int)Tools::getValue('status');
                $name = Tools::getValue('name');
                if(!$name)
                {
                    $errors[]=$this->l('Name is required');
                }
                elseif(!Validate::isCleanHtml($name))
                    $errors[]=$this->l('Name is not valid');
                else
                    $employeePost->name = $name;
                $description_default = Tools::getValue('description_'.Configuration::get('PS_LANG_DEFAULT'));
                if($description_default && !Validate::isCleanHtml($description_default))
                    $errors[] = $this->l('Description is not valid');
                $employeePost->profile_employee = '';
                $languages= Language::getLanguages(false);
                if(!$errors)
                {
                    foreach($languages as $language)
                    {
                        $description = Tools::getValue('description_'.$language['id_lang']);
                        if($description && !Validate::isCleanHtml($description,true))
                            $errors[] = sprintf($this->l('Description in %s is not valid'),$language['name']);
                        $employeePost->description[$language['id_lang']] = $description ? : $description_default;
                    }
                }
                $oldImage = false;
                $newImage = false;  
                $changedImages=array(); 
                if(isset($_FILES['avata']['tmp_name']) && isset($_FILES['avata']['name']) && $_FILES['avata']['name'])
                {
                    $_FILES['avata']['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES['avata']['name']);
                    if(!Validate::isFileName($_FILES['avata']['name']))
                    {
                        $errors[] = $this->l('Avatar is invalid');
                    }
                    else
                    {
                        if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'avata/'.$_FILES['avata']['name']))
                        {
                            $file_name = $this->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'avata/',$_FILES['avata']['name']);
                        }
                        else
                            $file_name = $_FILES['avata']['name'];
                        $type = Tools::strtolower(Tools::substr(strrchr($_FILES['avata']['name'], '.'), 1));
                        $imagesize = @getimagesize($_FILES['avata']['tmp_name']);
                        if (isset($_FILES['avata']) &&
                            !empty($_FILES['avata']['tmp_name']) &&
                            !empty($imagesize) &&
                            in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                        )
                        {
                            $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                            $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE');
                            if($_FILES['avata']['size'] > $max_file_size*1024*1024)
                                $errors[] = sprintf($this->l('Avatar image file is too large. Limit: %sMb'),$max_file_size);
                            elseif (!$temp_name || !move_uploaded_file($_FILES['avata']['tmp_name'], $temp_name))
                                $errors[] = $this->l('Cannot upload the file');
                            elseif (!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'avata/'.$file_name, null, null, $type))
                                $errors[] = $this->displayError($this->l('An error occurred during the image upload process.'));
                            if (isset($temp_name) && file_exists($temp_name))
                                @unlink($temp_name);
                            if($employeePost->avata)
                                $oldImage = _PS_YBC_BLOG_IMG_DIR_.'avata/'.$employeePost->avata;
                            $employeePost->avata = $file_name;
                            $newImage = _PS_YBC_BLOG_IMG_DIR_.'avata/'.$employeePost->avata;
                        }
                        else
                            $errors[] = $this->l('Avatar is invalid');
                    }
                }
                if(!$errors)
                {
                    if($id_employee_post)
                    {
                        if(!$employeePost->update())
                            $errors[] = $this->displayError($this->l('The employee could not be updated.'));
                    }
                    else
                        if(!$employeePost->add())
                            $errors[] = $this->displayError($this->l('The employee could not be updated.'));
                            
                }
                if (count($errors))
                {
                    if($newImage && file_exists($newImage))
                        @unlink($newImage);
                    $this->errorMessage = $this->displayError($errors);  
                }
                elseif($oldImage && file_exists($oldImage))
                    @unlink($oldImage);
                if(isset($newImage) && $newImage && file_exists($newImage) && !$errors && isset($employeePost)){
                    $changedImages[] = array(
                        'name' => 'avata',
                        'url' => _PS_YBC_BLOG_IMG_.'avata/'.$employeePost->avata,
                        'delete_url' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_customer='.$id_customer.'&delemployeeimage=true&control=customer',
                    );
                }  
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(
                        array(
                            'messageType' => $errors ? 'error' : 'success',
                            'message' => $errors ? $this->errorMessage :  $this->displaySuccessMessage($this->l('Customer - Author has been saved')),
                            'images' => isset($changedImages) && $changedImages ? $changedImages : array(),
                            'postUrl' => !$errors && Tools::isSubmit('saveBlogEmployee') && (int)$id_customer ? $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_customer='.(int)$id_customer.'&control=customer' : 0,
                            'itemKey' => 'id_employee',
                            'itemId' => !$errors ? $id_customer:0,
                        )
                    ));
                }        
                if(!$errors)
                {
                    if (Tools::isSubmit('saveBlogEmployee') && Tools::isSubmit('id_customer'))
            			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_customer='.$id_customer.'&control=customer');
               }
            }
        }
    }
    private function _postEmployee()
    {
        $errors=array();
        if(Tools::isSubmit('deleteAllPostEmployee') && ($id_author=(int)Tools::getValue('id_author')) )
        {
            Hook::exec('actionUpdateBlog', array(
                'id_author' =>(int)$id_author,
            ));
            if(Ybc_blog_post_class::deleteAllPostByIdAuthor($id_author,false))
            {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=employees&list=true');
            }
        }
        if(Tools::isSubmit('delemployeeimage') && ($id_employee = (int)Tools::getValue('id_employee')))
        {
            $id_employee_post= (int)Ybc_blog_post_employee_class::getIdEmployeePostById($id_employee,false);
            Hook::exec('actionUpdateBlog', array(
                'id_author' =>(int)$id_employee,
            ));
            $employeePost = new Ybc_blog_post_employee_class($id_employee_post);
            if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'avata/'.$employeePost->avata))
                @unlink(_PS_YBC_BLOG_IMG_DIR_.'avata/'.$employeePost->avata);
            $employeePost->avata='';
            $employeePost->update();
            if(Tools::isSubmit('ajax'))
            {
                die(json_encode(
                    array(
                        'messageType' => 'success',
                        'message' => $this->displayConfirmation($this->l('Avatar image deleted')),
                    )
                ));
            }            
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=post');
                 
        }
        if(Tools::isSubmit('change_enabled'))
        {
            $status=(int)Tools::getValue('change_enabled');
            $field = Tools::getValue('field');
            $id_employee = (int)Tools::getValue('id_employee');  
            Hook::exec('actionUpdateBlog', array(
                'id_author' =>(int)$id_employee,
            ));
            $id_employee_post= (int)Ybc_blog_post_employee_class::getIdEmployeePostById($id_employee,false);
            if(($field == 'status' && $id_employee))
            {
                if($id_employee_post)
                {
                    $employeePost= new Ybc_blog_post_employee_class($id_employee_post);
                    $employeePost->status=$status;
                    $employeePost->update();
                }
                else
                {
                    $employeePost = new Ybc_blog_post_employee_class();
                    $employeePost->status=$status;
                    $employee = new Employee($id_employee);
                    $employeePost->id_employee = $id_employee;
                    $employeePost->name = $employee->firstname.' '.$employee->lastname;
                    $employeePost->add();
                } 
                if($status==1)
                    $title= $this->l('Click to suspend'); 
                else
                    $title= $this->l('Click to active');
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(array(
                        'listId' => $id_employee,
                        'enabled' => $status,
                        'field' => $field,
                        'message' => $this->displaySuccessMessage($this->l('The status has been successfully updated')),
                        'messageType'=>'success',
                        'title'=>$title,
                        'href' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=employees&change_enabled='.($status ? '0' : '1').'&field='.$field.'&id_employee='.$id_employee,
                    )));
                }  
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=employees&list=true');
            }
        }
        if(($id_employee = (int)Tools::getValue('id_employee')))
        {
            $id_employee_post= (int)Ybc_blog_post_employee_class::getIdEmployeePostById($id_employee,false);
            if(Tools::isSubmit('saveBlogEmployee'))
            {
                Hook::exec('actionUpdateBlog', array(
                    'id_author' =>(int)$id_employee,
                ));
                if($id_employee_post)
                {
                    $employeePost= new Ybc_blog_post_employee_class($id_employee_post);
                }
                else
                    $employeePost = new Ybc_blog_post_employee_class();
                $employeePost->id_employee=$id_employee;
                $employeePost->is_customer=0;
                $name = Tools::getValue('name');
                if(!$name)
                {
                    $errors[]=$this->l('Name is required');
                }
                elseif(!Validate::isCleanHtml($name))
                    $errors[]=$this->l('Name is not valid');
                else
                    $employeePost->name = $name;
                $description_default = Tools::getValue('description_'.Configuration::get('PS_LANG_DEFAULT'));
                if($description_default && !Validate::isCleanHtml($description_default))
                    $errors[] = $this->l('Introduction is not valid');
                $profile_employee = Tools::getValue('profile_employee');
                if($profile_employee && !Validate::isCleanHtml($profile_employee))
                    $errors[] = $this->l('Profile is not valid');
                else
                    $employeePost->profile_employee = implode(',',$profile_employee);
                $employeePost->status = (int)Tools::getValue('status');
                $languages= Language::getLanguages(false);
                if(!$errors)
                {
                    foreach($languages as $language)
                    {
                        $description  = Tools::getValue('description_'.$language['id_lang']);
                        if($description && !Validate::isCleanHtml($description))
                            $errors[] = sprintf($this->l('Introduction in %s not valid'),$language['name']);
                        else
                            $employeePost->description[$language['id_lang']] = $description ? : $description_default;
                    }
                }
                $oldImage = false;
                $newImage = false;  
                $changedImages=array(); 
                if(isset($_FILES['avata']['tmp_name']) && isset($_FILES['avata']['name']) && $_FILES['avata']['name'])
                {
                    $_FILES['avata']['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES['avata']['name']);
                    if(!Validate::isFileName($_FILES['avata']['name']))
                    {
                        $errors[] = $this->l('Avatar is invalid');
                    }
                    else
                    {
                        if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'avata/'.$_FILES['avata']['name']))
                        {
                            $file_name = $this->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'avata/',$_FILES['avata']['name']);
                        }
                        else
                            $file_name = $_FILES['avata']['name'];
                        $type = Tools::strtolower(Tools::substr(strrchr($_FILES['avata']['name'], '.'), 1));
                        $imagesize = @getimagesize($_FILES['avata']['tmp_name']);
                        if (isset($_FILES['avata']) &&
                            !empty($_FILES['avata']['tmp_name']) &&
                            !empty($imagesize) &&
                            in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                        )
                        {
                            $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                            $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE');
                            if($_FILES['avata']['size'] > $max_file_size*1024*1024)
                                $errors[] = sprintf($this->l('Avatar image file is too large. Limit: %sMb'),$max_file_size);
                            elseif (!$temp_name || !move_uploaded_file($_FILES['avata']['tmp_name'], $temp_name))
                                $errors[] = $this->l('Cannot upload the file');
                            elseif (!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'avata/'.$file_name, Configuration::get('YBC_BLOG_IMAGE_AVATA_WIDTH',null,null,null,300), Configuration::get('YBC_BLOG_IMAGE_AVATA_HEIGHT',null,null,null,300), $type))
                                $errors[] = $this->displayError($this->l('An error occurred during the image upload process.'));
                            if (isset($temp_name) && file_exists($temp_name))
                                @unlink($temp_name);
                            if($employeePost->avata)
                                $oldImage = _PS_YBC_BLOG_IMG_DIR_.'avata/'.$employeePost->avata;
                            $employeePost->avata = $file_name;
                            $newImage = _PS_YBC_BLOG_IMG_DIR_.'avata/'.$employeePost->avata;
                        }
                        else
                            $errors[] = $this->l('Avatar is invalid');
                    }

                                  
                }
                if(!$errors)
                {
                    if($id_employee_post)
                    {
                        if(!$employeePost->update())
                            $errors[] = $this->displayError($this->l('The employee could not be updated.'));
                    }
                    else
                        if(!$employeePost->add())
                            $errors[] = $this->displayError($this->l('The employee could not be updated.'));
                }
                if (count($errors))
                {
                    if($newImage && file_exists($newImage))
                        @unlink($newImage);
                    $this->errorMessage = $this->displayError($errors);  
                }
                elseif($oldImage && file_exists($oldImage))
                    @unlink($oldImage);
                if(isset($newImage) && $newImage && file_exists($newImage) && !$errors && isset($employeePost)){
                    $changedImages[] = array(
                        'name' => 'avata',
                        'url' => _PS_YBC_BLOG_IMG_.'avata/'.$employeePost->avata,
                        'delete_url' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_employee='.$id_employee.'&delemployeeimage=true&control=employees',
                    );
                }  
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(
                        array(
                            'messageType' => $errors ? 'error' : 'success',
                            'message' => $errors ? $this->errorMessage :  $this->displaySuccessMessage($this->l('Administrator - Author has been saved')),
                            'images' => isset($changedImages) && $changedImages ? $changedImages : array(),
                            'postUrl' => !$errors && Tools::isSubmit('saveBlogEmployee') && $id_employee ? $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_employee='.$id_employee.'&control=employees' : 0,
                            'itemKey' => 'id_employee',
                            'itemId' => !$errors ? $id_employee:0,
                        )
                    ));
                }        
                if(!$errors)
                {
                    if (Tools::isSubmit('saveBlogEmployee') && Tools::isSubmit('id_employee'))
            			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_employee='.$id_employee.'&control=employees');
               }
            }
        }
    }
    private function _postPost()
    {
    $errors = array();
    $id_post = (int)Tools::getValue('id_post');
    if($id_post && !Validate::isLoadedObject(new Ybc_blog_post_class($id_post)) && !Tools::isSubmit('list'))
        Tools::redirectAdmin($this->baseAdminPath);
    /**
     * Change status 
     */
     if(Tools::isSubmit('change_enabled'))
     {
        $status = (int)Tools::getValue('change_enabled') ?  1 : 0;
        $field = Tools::getValue('field');
        $id_post = (int)Tools::getValue('id_post');   
        Hook::exec('actionUpdateBlog', array(
            'id_post' =>(int)$id_post,
        ));         
        if(($field == 'enabled' || $field=='is_featured') && $id_post)
        {
            $post_class= new Ybc_blog_post_class($id_post);
            Hook::exec('actionUpdateBlog', array(
                'id_post' =>(int)$id_post,
            ));
            Ybc_blog_defines::changeStatus('post',$field,$id_post,$status);
            $customer= new Customer($post_class->added_by);
            if(($subject = Ybc_blog_email_template_class::getSubjectByTemplate('approved_blog_customer',$customer->id_lang)) && $field == 'enabled' && $status==1 && $post_class->is_customer)
            {
                $template_customer_vars=array(
                    '{customer_name}' => $customer->firstname .' '.$customer->lastname,
                    '{post_title}' => $post_class->title[$this->context->language->id],
                    '{post_link}'=> $this->getLink('blog',array('id_post'=>$post_class->id)),
                    '{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
                    '{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')
                );
                Mail::Send(
        			$customer->id_lang,
        			'approved_blog_customer',
        			$subject,
        			$template_customer_vars,
			        $customer->email,
        			$customer->firstname .' '.$customer->lastname,
        			null,
        			null,
        			null,
        			null,
        			dirname(__FILE__).'/mails/'
                );
            }
            if($field=='enabled')
            {
                if($status==1)
                    $title=$this->l('Click to mark as draft');
                else
                    $title = $this->l('Click to mark as published');
            }
            else
            {
                if($status==1)
                    $title=$this->l('Click to unmark featured post');
                else
                    $title = $this->l('Click to mark as featured');
            }
            if(Tools::isSubmit('ajax'))
            {
                die(json_encode(array(
                    'listId' => $id_post,
                    'enabled' => $status,
                    'field' => $field,
                    'message' =>$field == 'enabled' ? $this->displaySuccessMessage($this->l('The status has been successfully updated')):$this->displaySuccessMessage($this->l('The featured post has been successfully updated')),
                    'messageType'=>'success',
                    'title' => $title,
                    'href' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=post&change_enabled='.($status ? '0' : '1').'&field='.$field.'&id_post='.$id_post,
                )));
            }
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=post&list=true');
        }
     }
     if(($action = Tools::getValue('action')) && $action=='updatePostOrdering' && ($posts=Tools::getValue('posts')) && Ybc_blog::validateArray($posts,'isInt'))
     {
        $page = (int)Tools::getValue('page',1);
        $id_category = (int)Tools::getValue('id_category');
        if(Ybc_blog_post_class::updatePostOrdering($posts,$page,$id_category))
        {
            die(
                json_encode(
                    array(
                        'page'=>$page,
                    )
                )
            );
        }

     }
     
    /**
     * Delete image 
     */         
     if($id_post && ($post = new Ybc_blog_post_class($id_post)) && Validate::isLoadedObject($post) && (Tools::isSubmit('delpostimage') || Tools::isSubmit('delpostthumb')))
     {
        $post->datetime_modified = date('Y-m-d H:i:s');
        $post->modified_by = (int)$this->context->employee->id;
        Hook::exec('actionUpdateBlog', array(
            'id_post' =>(int)$id_post,
        )); 
        if(Tools::isSubmit('delpostthumb'))
        {
            $id_lang = (int)Tools::getValue('id_lang');
            if(isset($post->thumb[$id_lang]) && $post->thumb[$id_lang])
            { 
                $oldThumb = $post->thumb[$id_lang];
                $post->thumb[$id_lang] = $post->thumb[(int)Configuration::get('PS_LANG_DEFAULT')];              
                $post->update();
                if(!in_array($oldThumb,$post->thumb) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$oldThumb))
                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$oldThumb);    
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(
                        array(
                            'messageType' => 'success',
                            'message' => $this->displayConfirmation($this->l('Thumbnail image deleted')),
                        )
                    ));
                }            
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_post='.$id_post.'&control=post');
            }
            else
            {
                $errors[] = $this->l('Thumbnail image does not exist');
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(
                        array(
                            'messageType' => 'error',
                            'message' => $this->displayError($errors),
                        )
                    ));
                }  
                
            }
                 
        }
        elseif(Tools::isSubmit('delpostimage'))
        {
            $id_lang = (int)Tools::getValue('id_lang');
            if(isset($post->image[$id_lang]) && $post->image[$id_lang])
            {
                $oldImage = $post->image[$id_lang];
                $post->image[$id_lang] = '';                
                $post->update(); 
                if(!in_array($oldImage,$post->image) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/'.$oldImage))
                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/'.$oldImage); 
                Hook::exec('actionUpdateBlog', array(
                    'id_post' =>(int)$id_post,
                ));
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(
                        array(
                            'messageType' => 'success',
                            'message' => $this->displayConfirmation($this->l('Image deleted')),
                        )
                    ));
                }               
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_post='.$id_post.'&control=post');                        
            }
            else
            {
                $errors[] = $this->l('Image does not exist'); 
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(
                        array(
                            'messageType' => 'error',
                            'message' => $this->displayError($errors),
                        )
                    ));
                }  
            }                      
        }
        else
            $errors[] = $this->l('Image does not exist');   
     }
    /**
     * Delete post 
     */ 
     if(Tools::isSubmit('del'))
     {            
        $id_post = (int)Tools::getValue('id_post');
        Hook::exec('actionUpdateBlog', array(
            'id_post' =>(int)$id_post,
        ));
        if(!Validate::isLoadedObject(new Ybc_blog_post_class($id_post)))
            $errors[] = $this->l('Post does not exist');
        elseif(Ybc_blog_post_class::_deletePost($id_post))
        {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=post&list=true');
        }                
        else
            $errors[] = $this->l('Could not delete the post. Please try again');
     }                  
    /**
     * Save post 
     */
    if(Tools::isSubmit('savePost'))
    {  
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $datetime_added = Tools::getValue('datetime_added'); 
        if($id_post && ($post = new Ybc_blog_post_class($id_post)) && Validate::isLoadedObject($post))
        {
            if($datetime_added)
                $post->datetime_added = $datetime_added;
            $post->datetime_modified = $datetime_added ?: date('Y-m-d H:i:s');
            $post->modified_by = (int)$this->context->employee->id;
            $post->is_customer= (int)Tools::getValue('is_customer');
            if($post->is_customer)
            {
                $customer_author = (int)Tools::getValue('customer_author');
                if(!$customer_author)
                    $errors[]=  $this->l('Community - Authors is required');
                else
                    $post->added_by = (int)$customer_author;
            }
            else
            {
                $admin_author = (int)Tools::getValue('admin_author');
                if(!$admin_author)
                    $errors[]=  $this->l('Administrator - Author is required');
                else
                    $post->added_by = (int)$admin_author;
            }    
            
        }
        else
        {
            $post = new Ybc_blog_post_class();
            $post->datetime_added = $datetime_added && Validate::isDate($datetime_added) ? $datetime_added : date('Y-m-d H:i:s');
            $post->datetime_modified = $datetime_added && Validate::isDate($datetime_added) ? $datetime_added : date('Y-m-d H:i:s');
            $post->modified_by = (int)$this->context->employee->id;
            $post->added_by = (int)$this->context->employee->id;
            $post->is_customer=0;
            $post->sort_order =1 + (int)Ybc_blog_post_class::getMaxOrder();
        }
        $inputAccessories = trim(trim(Tools::getValue('inputAccessories','')),'-');
        if($inputAccessories && Validate::isCleanHtml($inputAccessories))
            $post->products = $inputAccessories;
        else
            $post->products = '';
        $exclude_products = trim(trim(Tools::getValue('exclude_products','')),'-');
        if($exclude_products && Validate::isCleanHtml($exclude_products))
            $post->exclude_products = $exclude_products;
        else
            $post->exclude_products = '';
        $enabled = $post->enabled;
        if($post->id || !Tools::isSubmit('submitSaveAndPreview'))
            $post->enabled = (int)Tools::getValue('enabled');
        else
            $post->enabled = -2;
        if($enabled!=$post->enabled && $post->enabled==1)
            $updatestatus=true;
        else
            $updatestatus=false;
        $post->is_featured = (int)Tools::getValue('is_featured') ? 1 : 0;
        if($post->enabled==2)
        {
            $datetime_active = Tools::getValue('datetime_active');
            if($datetime_active=='')
                $errors[]=$this->l('Publish date is required');
            elseif($datetime_active=='0000-00-00' || !Validate::isDate($datetime_active))
                $errors[] = $this->l('Publish date is not valid');
            else
                $post->datetime_active = $datetime_active;
        }
        elseif(!$post->id)
            $post->datetime_active = date('Y-m-d');
        $languages = Language::getLanguages(false);
        $post->click_number = (int)Tools::getValue('click_number');
        $post->likes = (int)Tools::getValue('likes');
        $tags = array();                     
        $categories = Tools::getValue('blog_categories');            
        $title_default = trim(Tools::getValue('title_'.$id_lang_default));
        if($title_default=='')
            $errors[] = $this->l('You need to set blog post title');
        elseif(!Validate::isCatalogName($title_default))
            $errors[] = $this->l('Blog post title is not valid');
        $short_description_default = trim(Tools::getValue('short_description_'.$id_lang_default));
        if($short_description_default=='')
            $errors[] = $this->l('You need to set blog post short description');
        elseif(!Validate::isCleanHtml($short_description_default,true))
            $errors[] = $this->l('Blog post short description is not valid');
        $description_default = trim(Tools::getValue('description_'.$id_lang_default));
        if($description_default=='')
            $errors[] = $this->l('You need to set blog post content');  
        elseif(!Validate::isCleanHtml($description_default,true))
            $errors[] = $this->l('Blog post content is not valid');
        $url_alias_default = Tools::getValue('url_alias_'.$id_lang_default);          
        if($url_alias_default=='')
            $errors[] = $this->l('Url alias is required');
        elseif(!Validate::isLinkRewrite($url_alias_default))
            $errors[] = $this->l('Url alias is not valid');
        if(!$categories || !is_array($categories))
            $errors[] = $this->l('You need to choose at least 1 category'); 
        elseif(!Ybc_blog::validateArray($categories))
            $errors[] = $this->l('Categories is not valid'); 
        $main_category = (int)Tools::getValue('main_category');
        if(!$main_category)
            $errors[] = $this->l('Main category is required');
        elseif(!in_array($main_category,$categories))
            $errors[] = $this->l('Main category is not valid');
        else    
            $post->id_category_default = (int)$main_category;
        $click_number = Tools::getValue('click_number');
        if($click_number=='')
            $errors[] = $this->l('Views are required');
        elseif(!Validate::isUnsignedInt($click_number))
            $errors[] = $this->l('Views are not valid');
        $likes = Tools::getValue('likes');
        if($likes=='')
            $errors[] = $this->l('Likes are required');
        elseif(!Validate::isUnsignedInt($likes))
            $errors[] = $this->l('Likes are not valid');
        if(!$post->thumb[$id_lang_default] && !(isset($_FILES['thumb_'.$id_lang_default]['tmp_name']) && isset($_FILES['thumb_'.$id_lang_default]['name']) && $_FILES['thumb_'.$id_lang_default]['name']))
            $errors[]= $this->l('Post thumbnail image is required');
        $meta_title_default = trim(Tools::getValue('meta_title_'.$id_lang_default));
        if($meta_title_default && !Validate::isGenericName($meta_title_default))
            $errors[] = $this->l('Meta title is not valid');
        $meta_description_default = trim(Tools::getValue('meta_description_'.$id_lang_default));
        if($meta_description_default && !Validate::isGenericName($meta_description_default))
            $errors[] = $this->l('Meta description is not valid');
        $meta_keywords_default = trim(Tools::getValue('meta_keywords_'.$id_lang_default));
        if($meta_keywords_default && !Validate::isTagsList($meta_keywords_default))
            $errors[] = $this->l('Meta keyword is not valid');
        if(!$errors)
        {
            foreach ($languages as $language)
    		{			
    			$title = trim(Tools::getValue('title_'.$language['id_lang']));
                $meta_title = trim(Tools::getValue('meta_title_'.$language['id_lang']));
                $url_alias = trim(Tools::getValue('url_alias_'.$language['id_lang']));
                if($title && !Validate::isCatalogName($title))
                    $errors[] = sprintf($this->l('Title in %s is not valid'),$language['name']);
                else
                    $post->title[$language['id_lang']] = $title ? $title : $title_default;
                if($meta_title && !Validate::isGenericName($meta_title))
                    $errors[] = sprintf($this->l('Meta title in %s is not valid'),$language['name']);
                else
                    $post->meta_title[$language['id_lang']] = $meta_title ? $meta_title: $meta_title_default;
                if($url_alias && str_replace(array('0','1','2','3','4','5','6','7','8','9'),'',Tools::substr($url_alias,0,1))=='')
                    $errors[] = sprintf($this->l('Post alias in %s cannot have number on the start position because it will cause error when you enable "Remove post ID" option'),$language['name']);  
                elseif($url_alias && !Ybc_blog::checkIsLinkRewrite($url_alias))
                    $errors[] = sprintf($this->l('Url alias in %s is not valid'),$language['name']);
                elseif($url_alias && Ybc_blog_post_class::checkUrlAliasExists($url_alias,$post->id))
                    $errors[] = sprintf($this->l('Url alias in %s has already existed'),$language['name']);
                else
                    $post->url_alias[$language['id_lang']]= $url_alias ? $url_alias : $url_alias_default;                    
                $meta_description = trim(Tools::getValue('meta_description_'.$language['id_lang']));
                if($meta_description && !Validate::isGenericName($meta_description, true))
                    $errors[] = sprintf($this->l('Meta description in %s is not valid'),$language['name']);
                else
                     $post->meta_description[$language['id_lang']] = $meta_description ? $meta_description :  $meta_description_default;
                $meta_keywords = trim(Tools::getValue('meta_keywords_'.$language['id_lang']));
                if($meta_keywords && !Validate::isTagsList($meta_keywords, true))
                    $errors[] = sprintf($this->l('Meta keywords in %s are not valid'),$language['name']);
                else
                    $post->meta_keywords[$language['id_lang']] = $meta_keywords != '' ? $meta_keywords :  $meta_keywords_default;
                $short_description = trim(Tools::getValue('short_description_'.$language['id_lang']));
                if($short_description && !Validate::isCleanHtml($short_description, true) )
                    $errors[] = sprintf($this->l('Short description in %s is not valid'),$language['name']);
                elseif($short_description && !self::checkIframeHTML($short_description))
                    $errors[] = sprintf($this->l('Short description in %s is not valid'),$language['name']).$this->displayErrorIframe();
                else
                    $post->short_description[$language['id_lang']] = $short_description != '' ? $short_description :  $short_description_default;
                $description = Tools::getValue('description_'.$language['id_lang']);
                if(trim($description) && !Validate::isCleanHtml($description, true))
                    $errors[] = sprintf($this->l('Description in %s is not valid'),$language['name']);
                elseif($description && !self::checkIframeHTML($description))
                    $errors[] = sprintf($this->l('Description in %s is not valid.'),$language['name']).' '.$this->displayErrorIframe();
                else
                    $post->description[$language['id_lang']] = $description != '' ? $description :  $description_default;
                if($post->products && !preg_match('/^[0-9]+([\-0-9])*$/', $post->products))
                {
                    $errors[] = $this->l('Products are not valid');
                }
                $tagStr = trim(Tools::getValue('tags_'.$language['id_lang']));
                if($tagStr && Validate::isTagsList($tagStr))
                    $tags[$language['id_lang']] = explode(',',$tagStr);
                elseif($tagStr && !Validate::isTagsList($tagStr))
                {
                    $tags[$language['id_lang']] = array();
                    $errors[] = $this->l('Tags in '.$language['name'].' are not valid');
                }
                else
                    $tags[$language['id_lang']] = array();                                                           
            }
        }          
         
        $oldImages = array();
        $newImages = array();  
        $oldThumbs = array();
        $newThumbs = array(); 
        foreach($languages as $language)
        {
            /**
             * Upload image 
             */ 
            $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024*1024;
            
            if(isset($_FILES['image_'.$language['id_lang']]['tmp_name']) && isset($_FILES['image_'.$language['id_lang']]['name']) && $_FILES['image_'.$language['id_lang']]['name'])
            {
                $_FILES['image_'.$language['id_lang']]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES['image_'.$language['id_lang']]['name']);
                if(!Validate::isFileName($_FILES['image_'.$language['id_lang']]['name']))
                    $errors[] = sprintf($this->l('Image name is not valid in %s'),$language['iso_code']);
                elseif($_FILES['image_'.$language['id_lang']]['size'] > $max_file_size)
                    $errors[] = sprintf($this->l('Image file is too large. Limit: %s'),Tools::ps_round($max_file_size/1048576,2).'Mb');
                else
                {
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/'.$_FILES['image_'.$language['id_lang']]['name']))
                    {
                        $_FILES['image_'.$language['id_lang']]['name'] = $this->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'post/',$_FILES['image_'.$language['id_lang']]['name']);
                    }                
                    $type = Tools::strtolower(Tools::substr(strrchr($_FILES['image_'.$language['id_lang']]['name'], '.'), 1));
        			$imagesize = @getimagesize($_FILES['image_'.$language['id_lang']]['tmp_name']);
        			if (isset($_FILES['image_'.$language['id_lang']]) &&
        				in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
        			)
        			{
        			 
        				$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');    				
        				if ($error = ImageManager::validateUpload($_FILES['image_'.$language['id_lang']]))
        					$errors[] = $error;
        				elseif (!$temp_name || !move_uploaded_file($_FILES['image_'.$language['id_lang']]['tmp_name'], $temp_name))
        					$errors[] = $this->l('Cannot upload the file in').' '.$language['iso_code'];
        				elseif (!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'post/'.$_FILES['image_'.$language['id_lang']]['name'], Configuration::get('YBC_BLOG_IMAGE_BLOG_WIDTH',null,null,null,1920), Configuration::get('YBC_BLOG_IMAGE_BLOG_HEIGHT',null,null,null,750), $type))
        					$errors[] = $this->l('An error occurred during the image upload process in').' '.$language['iso_code'];
        				if (isset($temp_name) && file_exists($temp_name))
        					@unlink($temp_name);
                        if($post->image[$language['id_lang']])
                            $oldImages[$language['id_lang']] = $post->image[$language['id_lang']];
                        $post->image[$language['id_lang']] = $_FILES['image_'.$language['id_lang']]['name'];
                        $newImages[$language['id_lang']] = $post->image[$language['id_lang']];			
        			}
                    elseif(isset($_FILES['image_'.$language['id_lang']]) &&				
        				!empty($_FILES['image_'.$language['id_lang']]['tmp_name']) &&
        				!empty($imagesize) &&
        				in_array($type, array('jpg', 'gif', 'jpeg', 'png')
        			))
                    $errors[] = $this->l('Image is invalid in').' '.$language['iso_code'];
                }             
            }
            
           
            /**
             * Upload thumbnail
             */  
              
            if(isset($_FILES['thumb_'.$language['id_lang']]['tmp_name']) && isset($_FILES['thumb_'.$language['id_lang']]['name']) && $_FILES['thumb_'.$language['id_lang']]['name'])
            {
                $_FILES['thumb_'.$language['id_lang']]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES['thumb_'.$language['id_lang']]['name']);
                if(!Validate::isFileName($_FILES['thumb_'.$language['id_lang']]['name']))
                    $errors[] = sprintf($this->l('Thumbnail image name is not valid in %s'),$language['iso_code']);
                elseif($_FILES['thumb_'.$language['id_lang']]['size'] > $max_file_size)
                    $errors[] = sprintf($this->l('Thumbnail image file is too large. Limit: %s'),Tools::ps_round($max_file_size/1048576,2).'Mb');
                else
                {
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$_FILES['thumb_'.$language['id_lang']]['name']))
                    {
                        $_FILES['thumb_'.$language['id_lang']]['name'] = $this->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/',$_FILES['thumb_'.$language['id_lang']]['name']);
                    }                
                    $type = Tools::strtolower(Tools::substr(strrchr($_FILES['thumb_'.$language['id_lang']]['name'], '.'), 1));
        			$thumbsize = @getimagesize($_FILES['thumb_'.$language['id_lang']]['tmp_name']);
        			if (isset($_FILES['thumb_'.$language['id_lang']]) &&				
        				!empty($_FILES['thumb_'.$language['id_lang']]['tmp_name']) &&
        				!empty($thumbsize) &&
        				in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
        			)
        			{
        				$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');    				
        				if ($error = ImageManager::validateUpload($_FILES['thumb_'.$language['id_lang']]))
        					$errors[] = $error;
        				elseif (!$temp_name || !move_uploaded_file($_FILES['thumb_'.$language['id_lang']]['tmp_name'], $temp_name))
        					$errors[] = $this->l('Cannot upload the file in').' '.$language['iso_code'];
        				elseif (!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$_FILES['thumb_'.$language['id_lang']]['name'], Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_WIDTH',null,null,null,260), Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_HEIGHT',null,null,null,180), $type))
        					$errors[] = $this->l('An error occurred during the thumbnail upload process in').' '.$language['iso_code'];
        				if (isset($temp_name) && file_exists($temp_name))
        					@unlink($temp_name);
                        if($post->thumb[$language['id_lang']])
                            $oldThumbs[$language['id_lang']] = $post->thumb[$language['id_lang']];
                        $post->thumb[$language['id_lang']] = $_FILES['thumb_'.$language['id_lang']]['name'];
                        $newThumbs[$language['id_lang']] = $post->thumb[$language['id_lang']];			
        			}
                    elseif(isset($_FILES['thumb_'.$language['id_lang']]) &&
        				!in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
        			)
                    $errors[] = $this->l('Thumbnail image is invalid in').' '.$language['iso_code'];   
                 }             
            }
        } 
        				
        foreach($languages as $language)
        {
            if(!$post->thumb[$language['id_lang']])
                $post->thumb[$language['id_lang']] = $post->thumb[$id_lang_default];
            if(!$post->image[$language['id_lang']])
                $post->image[$language['id_lang']] = $post->image[$id_lang_default];
        }
        /**
         * Save 
         */    
        $changedImages = array();
        if(!$errors)
        {
            if (!$id_post)
			{
				if (!$post->add())
                {
                    $errors[] = $this->displayError($this->l('The post could not be added.')); 
                    if($newImages)
                    {
                        foreach($newImages as $newImage)
                            if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/'.$newImage))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/'.$newImage);
                    }
                    if($newThumbs)
                        foreach($newThumbs as $newThumb)
                           if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$newThumb))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$newThumb); 
                }    					
                else
                {
                    $id_post = Ybc_blog_defines::getMaxId('post','id_post');
                    Ybc_blog_post_class::updateCategories($categories, $id_post);
                    $relatedCategories= Tools::getValue('related_categories');
                    if(Ybc_blog::validateArray($relatedCategories))
                       Ybc_blog_post_class::updateRelatedCategories($relatedCategories,$id_post);
                    $product_categories = Tools::getValue('product_categories');
                    if(self::validateArray($product_categories))
                    {
                        Ybc_blog_post_class::updateRelatedProductCategories($product_categories,$id_post);
                    }
                    Ybc_blog_post_class::updateTags($id_post, $tags);
                    Hook::exec('actionUpdateBlogImage', array(
                        'id_post' =>(int)$post->id,
                        'image' => $newImages ? $post->image :false,
                        'thumb' => $newThumbs ? $post->thumb : false,
                    ));
                }
                                    
			}				
			elseif (!$post->update())
            {
                if($newImages)
                {
                    foreach($newImages as $newImage)
                        if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/'.$newImage))
                            @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/'.$newImage);
                }
                if($newThumbs)
                    foreach($newThumbs as $newThumb)
                       if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$newThumb))
                            @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$newThumb); 
                $errors[] = $this->displayError($this->l('The post could not be updated.'));
            }    					
            else
            {
                if($oldImages)
                {
                    foreach($oldImages as $oldImage)
                    {
                        if(!in_array($oldImage,$post->image) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/'.$oldImage))
                            @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/'.$oldImage);
                    }
                }
                if($oldThumbs)
                {
                    foreach($oldThumbs as $oldThumb)
                    {
                        if(!in_array($oldThumb,$post->thumb) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$oldThumb))
                            @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$oldThumb); 
                    }
                }
                Hook::exec('actionUpdateBlogImage', array(
                    'id_post' =>(int)$post->id,
                    'image' => $newImages ? $post->image :false,
                    'thumb' => $newThumbs ? $post->thumb : false,
                ));
                Ybc_blog_post_class::updateCategories($categories, $id_post);
                $relatedCategories= Tools::getValue('related_categories');
                if(Ybc_blog::validateArray($relatedCategories))
                    Ybc_blog_post_class::updateRelatedCategories($relatedCategories,$id_post);
                $product_categories = Tools::getValue('product_categories');
                if(self::validateArray($product_categories))
                {
                    Ybc_blog_post_class::updateRelatedProductCategories($product_categories,$id_post);
                }
                Ybc_blog_post_class::updateTags($id_post, $tags);
                $customer= new Customer($post->added_by);
                if(($subject = Ybc_blog_email_template_class::getSubjectByTemplate('approved_blog_customer',$customer->id_lang)) && $updatestatus &&  $post->is_customer)
                {
                    $template_customer_vars=array(
                        '{customer_name}' => $customer->firstname .' '.$customer->lastname,
                        '{post_title}' => $post->title[$this->context->language->id],
                        '{post_link}'=> $this->getLink('blog',array('id_post'=>$post->id)),
                        '{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
                        '{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')
                    );
                    Mail::Send(
            			$customer->id_lang,
            			'approved_blog_customer',
            			$this->l('Your post has been approved'),
            			$template_customer_vars,
    			        $customer->email,
            			$customer->firstname .' '.$customer->lastname,
            			null,
            			null,
            			null,
            			null,
            			dirname(__FILE__).'/mails/'
                    );
                }
            }  
            Hook::exec('actionUpdateBlog', array(
                'id_post' =>(int)$post->id,
            ));                               
        }
     }
     if (count($errors))
     {
        if($newImages)
        {
            foreach($newImages as $newImage)
                if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/'.$newImage))
                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/'.$newImage);
        }
        if($newThumbs)
            foreach($newThumbs as $newThumb)
               if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$newThumb))
                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$newThumb); 
        $this->errorMessage = $this->displayError($errors);  
     }
     if(isset($newThumbs) && $newThumbs && !$errors && isset($post))
     {
        foreach($languages as $language)
        {
           $changedImages[] = array(
                'name' => 'thumb_'.$language['id_lang'],
                'url' => _PS_YBC_BLOG_IMG_.'post/thumb/'.$post->thumb[$language['id_lang']],
                'delete_url' => false,
            ); 
        }
     } 
     if(isset($newImages) && $newImages && !$errors && isset($post)){
        foreach($languages as $language)
        {
            $changedImages[] = array(
                'name' => 'image_'.$language['id_lang'],
                'url' => _PS_YBC_BLOG_IMG_.'post/'.$post->image[$language['id_lang']],
                'delete_url' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_post='.$id_post.'&delpostimage=true&control=post&id_lang='.$language['id_lang'],
            );
        }
        
     }  
     if(Tools::isSubmit('ajax'))
     {
            $itemId= !$errors && Tools::isSubmit('savePost') && !(int)$id_post ? Ybc_blog_defines::getMaxId('post','id_post') : ((int)$id_post > 0 ? (int)$id_post : 0);
            $array = array(
                'messageType' => $errors ? 'error' : 'success',
                'message' => $errors ? $this->errorMessage : (isset($id_post) && $id_post ? $this->displaySuccessMessage($this->l('Post has been saved'),$this->l('View this post'),$this->getLink('blog',array('id_post'=>$id_post))) : $this->displayConfirmation($this->l('Post saved'))),
                'images' => isset($changedImages) && $changedImages ? $changedImages : array(),
                'postUrl' => !$errors && Tools::isSubmit('savePost') && !(int)$id_post ? $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_post='.Ybc_blog_defines::getMaxId('post','id_post').'&control=post' : 0,
                'itemKey' => 'id_post',
                'itemId' => $itemId,
                'link_preview'=> Tools::isSubmit('submitSaveAndPreview') && !$errors  ? $this->getLink('blog',array('id_post'=>$post->id,'preview'=>1)):'',
            );
            if(!$errors)
                $array['form_author_post']= $this->getFormAuthorPost($itemId);
            die(json_encode(
                $array
            ));
     }        
     
     if(!$errors)
     {
        if (Tools::isSubmit('savePost') && Tools::isSubmit('id_post'))
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_post='.$id_post.'&control=post');
		 elseif (Tools::isSubmit('savePost'))
         {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=3&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_post='.Ybc_blog_defines::getMaxId('post','id_post').'&control=post');
         }
     }
}
public function getSelectedCategories($id_post=0)
{
    if(Tools::isSubmit('submitPostStay'))
    {
        $categories = Tools::getValue('blog_categories');
        if(is_array($categories) && Ybc_blog::validateArray($categories))
            return $categories;
        else
            return array();
    }            
    $categories = array();
    if($id_post)
    {
        $rows = Ybc_blog_post_class::getOnlyCategoryBlog($id_post);
        if($rows)
        {
            foreach($rows as $row)
            {
                $categories[] = $row['id_category'];
            }
        }
    }
    else
        $categories = Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK_HOME') ? explode(',',Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK_HOME')):array();
    return $categories;        
}
public function getSelectedProducts($id_post)
{
    $products = array();
    $inputAccessories = Tools::getValue('inputAccessories');
    if(Tools::isSubmit('inputAccessories') && trim(trim($inputAccessories),',') && Validate::isCleanHtml($inputAccessories))
    {
        $products = explode('-', trim(trim($inputAccessories),'-'));
    }
    elseif($id_post)
    {
        $post = new Ybc_blog_post_class($id_post);
        $products = explode('-', trim($post->products,'-'));
    }        
    if($products)
    {
        foreach($products as $key => &$product)
        {
            $product = (int)$product;
        }
        return Ybc_blog_post_class::getProductsByIDs($products);
    }        
    return false;
}
public function getExcludeProducts($id_post)
{
    $products = array();
    $inputAccessories = Tools::getValue('exclude_products');
    if(Tools::isSubmit('exclude_products') && trim(trim($inputAccessories),',') && Validate::isCleanHtml($inputAccessories))
    {
        $products = explode('-', trim(trim($inputAccessories),'-'));
    }
    elseif($id_post)
    {
        $post = new Ybc_blog_post_class($id_post);
        $products = explode('-', trim($post->exclude_products,'-'));
    }
    if($products)
    {
        foreach($products as $key => &$product)
        {
            $product = (int)$product;
        }
        return Ybc_blog_post_class::getProductsByIDs($products);
    }
    return false;
}
/**
 * Sidebar 
 */
 public function renderSidebar()
 {
    $this->baseAdminPath = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
    $list = array(
        array(
            'label' => $this->l('Posts'),
            'url' => $this->baseAdminPath.'&control=post&list=true',
            'id' => 'ybc_tab_post',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Blog posts and blog categories'),
            'controller'=>'AdminYbcBlogPost',
            'icon' => 'icon-AdminPriceRule'
        ),
        array(
            'label' => $this->l('Categories'),
            'url' => $this->baseAdminPath.'&control=category&list=true',
            'id' => 'ybc_tab_category',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Blog posts and blog categories'),
            'controller'=>'AdminYbcBlogCategory',
            'icon' => 'icon-AdminCatalog'
        ),
        array(
            'label' => $this->l('Comments'),
            'url' => $this->baseAdminPath.'&control=comment&list=true',
            'id' => 'ybc_tab_comment',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Blog comments'),
            'controller'=>'AdminYbcBlogComment',
            'icon' => 'icon-comments',
            'total_result' => Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.viewed=0',false),
        ),
        array(
            'label' => $this->l('Polls'),
            'url' => $this->baseAdminPath.'&control=polls&list=true',
            'id' => 'ybc_tab_polls',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Blog comments'),
            'controller'=>'AdminYbcBlogPolls',
            'icon' => 'icon-polls',
        ),
        array(
            'label' => $this->l('Slider'),
            'url' => $this->baseAdminPath.'&control=slide&list=true',
            'id' => 'ybc_tab_slide',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Blog slider'),
            'icon' => 'icon-AdminParentModules',
            'controller'=>'AdminYbcBlogSlider',
        ),
        array(
            'label' => $this->l('Photo gallery'),
            'url' => $this->baseAdminPath.'&control=gallery&list=true',
            'id' => 'ybc_tab_gallery',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Blog gallery'),
            'icon' => 'icon-AdminDashboard',
            'controller'=>'AdminYbcBlogGallery',
        ),
        array(
            'label' => $this->l('Seo'),
            'url' => $this->baseAdminPath.'&control=seo&list=true',
            'id' => 'ybc_tab_seo',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Seo'),
            'icon' => 'icon-seo',
            'controller'=>'AdminYbcBlogSeo',
        ),
        array(
            'label' => $this->l('Google sitemap'),
            'url' => $this->baseAdminPath.'&control=sitemap&list=true',
            'id' => 'ybc_tab_sitemap',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Sitemap'),
            'icon' => 'icon-sitemap',
            'controller'=>'AdminYbcBlogSitemap',
        ),
        array(
            'label' => $this->l('RSS feed'),
            'url' => $this->baseAdminPath.'&control=rss&list=true',
            'id' => 'ybc_tab_rss',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Rss feed'),
            'icon' => 'icon-rss',
            'controller'=>'AdminYbcBlogRSS',
        ),
        array(
            'label' => $this->l('Socials'),
            'url' => $this->baseAdminPath.'&control=socials&list=true',
            'id' => 'ybc_tab_socials',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Socials'),
            'icon' => 'icon-socials',
            'controller'=>'AdminYbcBlogSocials',
        ),
        array(
            'label' => $this->l('Email'),
            'url' => $this->baseAdminPath.'&control=email&list=true',
            'id' => 'ybc_tab_email',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Email'),
            'icon' => 'icon-email',
            'controller'=>'AdminYbcBlogEmail',
        ),
        array(
            'label'=> $this->l('Image'),
            'id'=>'ybc_tab_image',
            'icon'=>'icon-image',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Image'),
            'url' => $this->baseAdminPath.'&control=image&list=true',
            'controller'=>'AdminYbcBlogImage',
        ),
        array(
            'label' => $this->l('Sidebar'),
            'url' => $this->baseAdminPath.'&control=sidebar&list=true',
            'id' => 'ybc_tab_sidebar',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Sidebar'),
            'icon' => 'icon-sidebar',
            'controller'=>'AdminYbcBlogSidebar',
        ),
        array(
            'label' => $this->l('Home page'),
            'url' => $this->baseAdminPath.'&control=homepage&list=true',
            'id' => 'ybc_tab_homepage',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Home page'),
            'icon' => 'icon-homepage',
            'controller'=>'AdminYbcBlogHomepage',
        ),
        array(
            'label' => $this->l('Post listing pages'),
            'url' => $this->baseAdminPath.'&control=postlistpage&list=true',
            'id' => 'ybc_tab_postlistpage',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Post listing pages'),
            'icon' => 'icon-postlistpage',
            'controller'=>'AdminYbcBlogPostListPage',
        ),
        array(
            'label' => $this->l('Post details page'),
            'url' => $this->baseAdminPath.'&control=postpage&list=true',
            'id' => 'ybc_tab_postpage',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Post detail page'),
            'icon' => 'icon-postpage',
            'controller'=>'AdminYbcBlogPostpage',
        ),
        array(
            'label' => $this->l('Product categories page'),
            'url' => $this->baseAdminPath.'&control=categorypage&list=true',
            'id' => 'ybc_tab_categorypage',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Category page'),
            'icon' => 'icon-categorypage',
            'controller'=>'AdminYbcBlogCategorypage',
        ),
        array(
            'label' => $this->l('Product details page'),
            'url' => $this->baseAdminPath.'&control=productpage&list=true',
            'id' => 'ybc_tab_productpage',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Product detail page'),
            'icon' => 'icon-productpage',
            'controller'=>'AdminYbcBlogProductpage',
        ),
        array(
            'label'=> $this->l('Authors'),
            'id'=>'ybc_tab_employees',
            'icon'=>'icon-user',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Authors'),
            'url' => $this->baseAdminPath.'&control=employees&list=true',
            'controller'=>'AdminYbcBlogAuthor',
        ),
        array(
            'label' => $this->l('Import/Export'),
            'url' => $this->baseAdminPath.'&control=export',
            'id' => 'ybc_tab_export',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Import/Export'),
            'icon' => 'icon-exchange',
            'controller'=>'AdminYbcBlogImport',
        ),
        array(
            'label' => $this->l('Statistics'),
            'url' => $this->context->link->getAdminLink('AdminYbcBlogStatistics'),
            'id' => 'ybc_tab_statistics',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Statistics'),
            'icon' => 'icon-chart',
            'controller'=>'AdminYbcBlogStatistics',
        ),
        array(
            'label' => $this->l('Global settings'),
            'url' => $this->baseAdminPath.'&control=config',
            'id' => 'ybc_tab_config',
            'hasAccess' => $this->checkProfileEmployee($this->context->employee->id,'Global settings'),
            'icon' => 'icon-AdminAdmin',
            'controller'=>'AdminYbcBlogSetting',
        ),
    );
    $control = Tools::getValue('control');
    $controller = Tools::getValue('controller');
    $this->context->smarty->assign(
		array(
			'link' => $this->context->link,
			'list' => $list,
            'admin_path' => $this->baseAdminPath,
            'active' => 'ybc_tab_'.($control && in_array($control,$this->controls) ? $control : ($controller=='AdminYbcBlogStatistics' ? 'statistics'  :'post'))			
		)
	);
    return $this->display(__FILE__, 'sidebar.tpl');
 }
/**
 * Functions 
 */

public function getFieldsCustomerValues()
{
    $fields=array();
    $id_customer = (int)Tools::getValue('id_customer');
    if($id_employee_post= (int)Ybc_blog_post_employee_class::getIdEmployeePostById($id_customer))
    {
        $blogEmployee = new Ybc_blog_post_employee_class($id_employee_post);
        $fields['status'] =(int)$blogEmployee->status;
    }
    else
    {
        $blogEmployee = new Ybc_blog_post_employee_class();
        $fields['status'] = 1; 
    }
        
    $customer = new Customer($id_customer);
    $fields['id_customer'] = $customer->id;
    $fields['name'] =$blogEmployee->name?$blogEmployee->name:$customer->firstname.' '.$customer->lastname;
    $languages= Language::getLanguages(false);
    foreach($languages as $language)
    {
        $fields['description'][$language['id_lang']] =$blogEmployee->description[$language['id_lang']];
    }
    $fields['control'] =trim(Tools::getValue('control')) ? : '';  
    return $fields;
}
public function getFieldsEmployeeValues()
{
    $fields=array();
    $id_employee = (int)Tools::getValue('id_employee');
    if($id_employee_post = (int)Ybc_blog_post_employee_class::getIdEmployeePostById($id_employee,false))
    {
        $blogEmployee = new Ybc_blog_post_employee_class($id_employee_post);
        $fields['status'] = $blogEmployee->status;
    }
    else
    {
        $blogEmployee = new Ybc_blog_post_employee_class();
        $fields['status'] = 1;
    }
    $employee = new Employee($id_employee);
    $fields['id_employee'] = $employee->id;
    $fields['name'] = Tools::getValue('name',$blogEmployee->name? $blogEmployee->name:$employee->firstname.' '.$employee->lastname);
    $languages= Language::getLanguages(false);
    $fields['profile_employee'] = Tools::getValue('profile_employee',$blogEmployee->profile_employee ? explode(',',$blogEmployee->profile_employee):array());
    foreach($languages as $language)
    {
        $fields['description'][$language['id_lang']] = Tools::getValue('description_'.$language['id_lang'],isset($blogEmployee->description[$language['id_lang']]) ? $blogEmployee->description[$language['id_lang']] :'');
    }
    $fields['control'] =trim(Tools::getValue('control')) ? : '';
    
    return $fields;
}
public function getFieldsValues($formFields, $primaryKey, $objClass, $saveBtnName)
{
	$fields = array();        
	if (Tools::isSubmit($primaryKey))
	{
		$obj = new $objClass((int)Tools::getValue($primaryKey));
		$fields[$primaryKey] = (int)Tools::getValue($primaryKey, $obj->$primaryKey);            
	}
	else
    {
        $obj = new $objClass();
    }
    foreach($formFields as $field)
    {
        if(!isset($field['primary_key']) && !isset($field['multi_lang']) && !isset($field['connection']))
        {
            $fieldName = $field['name'];
            $fields[$field['name']] = trim(Tools::getValue($field['name'], $obj->$fieldName));       
        }
                 
    }   
    $languages = Language::getLanguages(false);
    
    /**
     *  Default
     */
    
    if(!Tools::isSubmit($saveBtnName) && !Tools::isSubmit($primaryKey))
    {
        foreach($formFields as $field)
        {
            if(isset($field['default']) && !isset($field['multi_lang']))
            {
                if(isset($field['default_submit']))
                    $fields[$field['name']] = Tools::getValue($field['name']) ? : $field['default'];
                else
                    $fields[$field['name']] = $field['default'];
            }
        }
    }
    
    /**
     * Multiple language 
     */
	foreach ($languages as $lang)
	{
	    foreach($formFields as $field)
        {
            if(!Tools::isSubmit($saveBtnName) && !Tools::isSubmit($primaryKey))
            {
                if(isset($field['multi_lang']))
                {
                    if(isset($field['default']))
                        $fields[$field['name']][$lang['id_lang']] = $field['default'];
                    else
                        $fields[$field['name']][$lang['id_lang']] = '';
                }
            }
            elseif(Tools::isSubmit($saveBtnName))
            {
                if(isset($field['multi_lang']))
                    $fields[$field['name']][$lang['id_lang']] = Tools::getValue($field['name'].'_'.(int)$lang['id_lang']);  
                
            }
            else{                    
                if(isset($field['multi_lang']))
                {
                    $fieldName = $field['name'];
                    $field_langs = $obj->$fieldName;                        
                    $fields[$field['name']][$lang['id_lang']] = isset($field_langs[$lang['id_lang']]) ? $field_langs[$lang['id_lang']]:'';
                }                        
            }                
        }
	}
    $fields['control'] = trim(Tools::getValue('control')) ? : '';
    
    /**
     * Tags 
     */
     if($primaryKey=='id_post')
     {
        $id_post = (int)Tools::getValue('id_post');
        foreach ($languages as $lang)
        {
            if(Tools::isSubmit('savePost'))
            {                    
                $fields['tags'][$lang['id_lang']] = trim(trim(Tools::getValue('tags_'.(int)$lang['id_lang'])),',') ? : '';
            }
            else
                $fields['tags'][$lang['id_lang']] = Ybc_blog_post_class::getTagStr((int)$id_post, (int)$lang['id_lang']);
            
        }            
     }
     return $fields;
}
public function renderList($listData)
{      
    if(isset($listData['fields_list']) && $listData['fields_list'])
    {
        foreach($listData['fields_list'] as $key => &$val)
        {
            $control = Tools::getValue('control');
            if(isset($val['filter']) && $val['filter'] && $val['type']=='int')
            {
                $val['active']['max'] =  trim(Tools::getValue($key.'_max'));   
                $val['active']['min'] =  trim(Tools::getValue($key.'_min'));   
            }  
            elseif($listData['name']=='ybc_blog_employee' && $control!='employees')
            {
                $val['active']='';
            }
            elseif($listData['name']=='ybc_blog_customer' && $control!='customer')
            {
                $val['active']='';
            }
            elseif($key=='has_post' && !Tools::isSubmit('has_post'))
                $val['active']=1;
            else               
                $val['active'] = trim(Tools::getValue($key));
        }
    }    
    $this->context->smarty->assign($listData);
    return $this->display(__FILE__, 'list_helper.tpl');
}
public function renderListByCustomer($listData)
{
    if(isset($listData['fields_list']) && $listData['fields_list'])
    {
        foreach($listData['fields_list'] as $key => &$val)
        {
            $val['active'] = trim(Tools::getValue($key));
        }
    }    
    $this->context->smarty->assign($listData);
    return $this->display(__FILE__, 'list_helper_customer.tpl');
}
public function renderListPostByCustomer($listData)
{
    if(isset($listData['fields_list']) && $listData['fields_list'])
    {
        foreach($listData['fields_list'] as $key => &$val)
        {
            $val['active'] = trim(Tools::getValue($key));
        }
    }    
    $this->context->smarty->assign($listData);
    return $this->display(__FILE__, 'list_post_by_customer.tpl');
}
public function getUrlExtra($field_list)
{
    $params = '';
    $sort = Tools::strtolower(Tools::getValue('sort'));
    $sort_type = Tools::strtolower(Tools::getValue('sort_type','desc'));
    if(!in_array($sort_type,array('desc','asc')))
        $sort_type = 'desc';
    if($sort && isset($field_list[trim($sort)]))
    {
        $params .= '&sort='.trim($sort).'&sort_type='.(trim($sort_type) =='asc' ? 'asc' : 'desc');
    }
    if($field_list)
    {
        foreach($field_list as $key => $val)
        {
            if(($value = Tools::getValue($key))!='' && Validate::isCleanHtml($value))
            {
                $params .= '&'.$key.'='.urlencode($value);
            }
        }
        unset($val);
    }
    return $params;
}
public function getUrlExtraFrontEnd($field_list,$submit)
{
    $params = '';
    $sort = Tools::strtolower(Tools::getValue('sort'));
    $sort_type = Tools::strtolower(Tools::getValue('sort_type','desc'));
    if(!in_array($sort_type,array('desc','asc')))
        $sort_type = 'desc';
    if($sort && isset($field_list[trim($sort)]))
    {
        $params .= '&sort='.trim($sort).'&sort_type='.(trim($sort_type) =='asc' ? 'asc' : 'desc');
    }
    if($field_list)
    {
        $ok=false;
        foreach($field_list as $key => $val)
        {
            if(($value = Tools::getValue($key))!='' && Validate::isCleanHtml($value))
            {
                $params .= '&'.$key.'='.urlencode($value);
                $ok=true;
            }
        }
        if($ok)
            $params .='&'.$submit.'=1';
        unset($val);
    }
    return $params;
}
public function getFilterParams($field_list)
{
    $params = '';        
    if($field_list)
    {
        foreach($field_list as $key => $val)
        {
            if(($value = Tools::getValue($key))!='' && Validate::isCleanHtml($value))
            {
                $params .= '&'.$key.'='.urlencode($value);
            }
        }
        unset($val);
    }
    return $params;
}
public function getFilterParamsFontEnd($field_list,$submit)
{
    $params = '';        
    if($field_list)
    {
        foreach($field_list as $key => $val)
        {
            if(($value = Tools::getValue($key))!='' && Validate::isCleanHtml($value))
            {
                $params .= '&'.$key.'='.urlencode($value);
            }
        }
        unset($val);
    }
    if($params)
        $params .='&'.$submit.'=1';
    return $params;
}
public function getCategoriesStrByIdPost($id_post)
{
    $categories = Ybc_blog_post_class::getOnlyCategoryBlog($id_post);
    $this->smarty->assign(array('categories' => $categories));
    return $this->display(__FILE__,'categories_str.tpl');
}
public function getPostById($id_post)
{
    $filter = ' AND (ybe.status>=0 OR ybe.status is NULL OR e.id_profile=1) AND p.id_post = '.(int)$id_post;
    $posts= Ybc_blog_post_class::getPostsWithFilter($filter,false,false,false,false);
    if($posts)
    {
        $posts[0]['pending'] = $posts[0]['added_by']==$this->context->customer->id && $posts[0]['is_customer'] && ($posts[0]['enabled']==1 || $posts[0]['enabled']==-1) ? 1 :0;
        return $posts[0];
    }
    return false;
}
public function renderSettingCustomer()
{
    $ybc_defines = new Ybc_blog_defines();
    $configs = $ybc_defines->customer_settings;
    $fields_form = array(
		'form' => array(
			'input' => array(),
            'submit' => array(
				'title' => $this->l('Save'),
			)
        ),
	);
    if($configs)
    {
        foreach($configs as $key => $config)
        {
            $arg = array(
                'name' => isset($config['multiple']) && $config['multiple']? $key.'[]' :$key,
                'type' => $config['type'],
                'label' => $config['label'],
                'desc' => isset($config['desc']) ? $config['desc'] : false,
                'required' => isset($config['required']) && $config['required'] ? true : false,
                'options' => isset($config['options']) && $config['options'] ? $config['options'] : array(),
                'values' => isset($config['values']) ? $config['values']:false,
                'multiple' => isset($config['multiple'])? $config['multiple'] : false,
                'lang' => isset($config['lang']) ? $config['lang'] : false,
                'class' => isset($config['class']) ? $config['class'] : '',
                'form_group_class' => isset($config['form_group_class']) ? $config['form_group_class'] : '',
                'tab' => isset($config['tab']) && $config['tab'] ? $config['tab'] : 'general',
                'suffix' => isset($config['suffix']) && $config['suffix'] ? $config['suffix'] : false,
                'html_content' => isset($config['html_content']) ? $this->displayBlogCategoryTre(Ybc_blog_category_class::getBlogCategoriesTree(0),Configuration::get('YBC_BLOG_CATEGOGY_CUSTOMER') ? explode(',',Configuration::get('YBC_BLOG_CATEGOGY_CUSTOMER')):array(),$key) : false,
                'selected_categories' => isset($config['selected_categories']) ? $config['selected_categories'] : false,
                'categories' => isset($config['categories'])? Ybc_blog_category_class::getBlogCategoriesTree(0) :false,
            );
            if(isset($arg['suffix']) && !$arg['suffix'])
                unset($arg['suffix']);
            $fields_form['form']['input'][] = $arg;
        }
    }       
    $helper = new HelperForm();
	$helper->show_toolbar = false;
	$helper->table = $this->table;
	$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
	$helper->default_form_language = $lang->id;
	$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
	$this->fields_form = array();
	$helper->module = $this;
	$helper->identifier = $this->identifier;
	$helper->submit_action = 'saveCustomerAuthor';
	$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=author';
	$helper->token = Tools::getAdminTokenLite('AdminModules');
	$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));        
    $fields = array();        
    $languages = Language::getLanguages(false);
    $helper->override_folder = '/';
    if(Tools::isSubmit('saveCustomerAuthor'))
    {            
        if($configs)
        {
            foreach($configs as $key => $config)
            {       
                if(isset($config['lang']) && $config['lang'])
                {                        
                    foreach($languages as $l)
                    {
                        $fields[$key][$l['id_lang']] = Tools::getValue($key.'_'.$l['id_lang'],isset($config['default']) ? $config['default'] : '');
                    }
                }
                else
                    $fields[$key] = Tools::getValue($key,isset($config['default']) ? $config['default'] : '');                    
            }
        }
        
    }
    else
    {
        if($configs)
        {
                foreach($configs as $key => $config)
                {
                    if(isset($config['lang']) && $config['lang'])
                    {                    
                        foreach($languages as $l)
                        {
                            $fields[$key][$l['id_lang']] = Configuration::get($key,$l['id_lang']);
                        }
                    }
                    elseif($config['type']=='checkbox')
                    {
                        $fields[$key] =Configuration::get($key)? explode(',',Configuration::get($key)):array();
                    }
                        
                    else
                        $fields[$key] = Configuration::get($key);     
                                   
                }
        }
    }
    $helper->tpl_vars = array(
		'base_url' => $this->context->shop->getBaseURL(),
		'language' => array(
			'id_lang' => $language->id,
			'iso_code' => $language->iso_code
		),
		'fields_value' => $fields,
		'languages' => $this->context->controller->getLanguages(),
		'id_language' => $this->context->language->id,
        'cancel_url' => $this->baseAdminPath.'&control=post&list=true',
        'isConfigForm' => true,
        'image_baseurl' => _PS_YBC_BLOG_IMG_,
        'name_controller' => 'ybc-blog-panel-settings',
    );
    
    $this->_html .= $helper->generateForm(array($fields_form));
}
/**
 * Render config form 
 */
 public function renderRSS()
 {
    $ybc_defines = new Ybc_blog_defines();
    $configs = $ybc_defines->rss;
    $fields_form = array(
		'form' => array(
			'legend' => array(
				'title' => $this->l('RSS feed'),
				'icon' => 'icon-rss'
			),
			'input' => array(),
            'submit' => array(
				'title' => $this->l('Save'),
			)
        ),
	);
    if($configs)
    {
        foreach($configs as $key => $config)
        {
            $arg = array(
                'name' => isset($config['multiple']) && $config['multiple']? $key.'[]' :$key,
                'type' => $config['type'],
                'label' => $config['label'],
                'desc' => isset($config['desc']) ? $config['desc'] : false,
                'required' => isset($config['required']) && $config['required'] ? true : false,
                'options' => isset($config['options']) && $config['options'] ? $config['options'] : array(),
                'values' => isset($config['values']) ? $config['values']:false,
                'multiple' => isset($config['multiple'])? $config['multiple'] : false,
                'lang' => isset($config['lang']) ? $config['lang'] : false,
                'class' => isset($config['class']) ? $config['class'] : '',
                'form_group_class' => isset($config['form_group_class']) ? $config['form_group_class'] : '',
                'tab' => isset($config['tab']) && $config['tab'] ? $config['tab'] : 'general',
                'suffix' => isset($config['suffix']) && $config['suffix'] ? $config['suffix'] : false,
            );
            if(isset($arg['suffix']) && !$arg['suffix'])
                unset($arg['suffix']);
            $fields_form['form']['input'][] = $arg;
        }
    }        
    $helper = new HelperForm();
	$helper->show_toolbar = false;
	$helper->table = $this->table;
	$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
	$helper->default_form_language = $lang->id;
	$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
	$this->fields_form = array();
	$helper->module = $this;
	$helper->identifier = $this->identifier;
	$helper->submit_action = 'saveRSS';
	$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=rss';
	$helper->token = Tools::getAdminTokenLite('AdminModules');
	$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));        
    $fields = array();        
    $languages = Language::getLanguages(false);
    $helper->override_folder = '/';
    if(Tools::isSubmit('saveRSS'))
    {            
        if($configs)
        {
            foreach($configs as $key => $config)
            {       
                if(isset($config['lang']) && $config['lang'])
                {                        
                    foreach($languages as $l)
                    {
                        $fields[$key][$l['id_lang']] = Tools::getValue($key.'_'.$l['id_lang'],isset($config['default']) ? $config['default'] : '');
                    }
                }
                else
                    $fields[$key] = Tools::getValue($key,isset($config['default']) ? $config['default'] : '');                    
            }
        }
    }
    else
    {
        if($configs)
        {
                foreach($configs as $key => $config)
                {
                    if(isset($config['lang']) && $config['lang'])
                    {                    
                        foreach($languages as $l)
                        {
                            $fields[$key][$l['id_lang']] = Configuration::get($key,$l['id_lang']);
                        }
                    }
                    elseif($config['type']=='checkbox')
                    {
                        $fields[$key] =Configuration::get($key)? explode(',',Configuration::get($key)):array();
                    }
                        
                    else
                        $fields[$key] = Configuration::get($key);     
                                   
                }
        }
    }
    $urls_rss=array();
    $languages = Language::getLanguages(true);
    foreach($languages as $lang)
        $urls_rss[]= array(
            'link'=>$this->getLink('rss',array(),$lang['id_lang']),
            'img'=> $this->getBaseLink().'img/l/'.$lang['id_lang'].'.jpg'
        );
    $helper->tpl_vars = array(
		'base_url' => $this->context->shop->getBaseURL(),
		'language' => array(
			'id_lang' => $language->id,
			'iso_code' => $language->iso_code
		),
		'fields_value' => $fields,
		'languages' => $this->context->controller->getLanguages(),
		'id_language' => $this->context->language->id,
        'cancel_url' => $this->baseAdminPath.'&control=post&list=true',
        'isConfigForm' => true,
        'urls_rss' => $urls_rss,
        'image_baseurl' => _PS_YBC_BLOG_IMG_,
    );
    $this->_html .= $helper->generateForm(array($fields_form));
 }
 public function renderConfig($configs,$title,$icon)
 {
    $fields_form = array(
		'form' => array(
			'legend' => array(
				'title' => $title,
				'icon' => $icon!='icon-email' ? $icon:'icon-AdminAdmin',
			),
			'input' => array(),
            'submit' => array(
				'title' => $this->l('Save'),
			)
        ),
	);
    if($configs)
    {
        foreach($configs as $key => $config)
        {
            $arg = array(
                'name' => $key,
                'type' => $config['type'],
                'label' => $config['label'],
                'autoload_rte' => isset($config['autoload_rte'])? $config['autoload_rte'] :false,
                'desc' => isset($config['desc']) ? $config['desc'] : false,
                'required' => isset($config['required']) && $config['required'] ? true : false,
                'required2' => isset($config['required2']) && $config['required2'] ? true : false,
                'options' => isset($config['options']) && $config['options'] ? $config['options'] : array(),
                'values' => isset($config['values']) ? $config['values'] : array(),
                'lang' => isset($config['lang']) ? $config['lang'] : false,
                'class' => isset($config['class']) ? $config['class'] : '',
                'form_group_class' => isset($config['form_group_class']) ? $config['form_group_class'] : '',
                'tab' => isset($config['tab']) && $config['tab'] ? $config['tab'] : 'general',
                'suffix' => isset($config['suffix']) && $config['suffix'] ? $config['suffix'] : false,
                'html_content' => isset($config['html_content']) ? $config['html_content']:false,
                'categories' => isset($config['categories']) ? $config['categories']:false,
                'col' => isset($config['col']) ? $config['col']:9,
                'selected_categories' => isset($config['selected_categories']) ? $config['selected_categories']:false,
            );
            if(isset($arg['suffix']) && !$arg['suffix'])
                unset($arg['suffix']);
            $fields_form['form']['input'][] = $arg;
        }
    }  
    $control = Tools::getValue('control');    
    if(!in_array($control,$this->controls))
        $control = 'config';  
    $helper = new HelperForm();
	$helper->show_toolbar = false;
	$helper->table = $this->table;
	$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
	$helper->default_form_language = $lang->id;
	$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
	$this->fields_form = array();
	$helper->module = $this;
	$helper->identifier = $this->identifier;
	$helper->submit_action = 'saveConfig';
	$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control='.$control;
	$helper->token = Tools::getAdminTokenLite('AdminModules');
	$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));        
    $fields = array();        
    $languages = Language::getLanguages(false);
    $helper->override_folder = '/';
    if(Tools::isSubmit('saveConfig'))
    {            
        if($configs)
        {
            foreach($configs as $key => $config)
            {                    
                if(isset($config['lang']) && $config['lang'])
                {                        
                    foreach($languages as $l)
                    {
                        $fields[$key][$l['id_lang']] = Tools::getValue($key.'_'.$l['id_lang'],isset($config['default']) ? $config['default'] : '');
                    }
                }
                else
                    $fields[$key] = Tools::getValue($key,isset($config['default']) ? $config['default'] : '');                    
            }
        }
    }
    else
    {
        if($configs)
        {
                foreach($configs as $key => $config)
                {
                    if(isset($config['lang']) && $config['lang'])
                    {                    
                        foreach($languages as $l)
                        {
                            $fields[$key][$l['id_lang']] = Configuration::get($key,$l['id_lang']);
                        }
                    }
                    elseif($config['type']=='checkbox')
                        $fields[$key] = explode(',',Configuration::get($key)); 
                    elseif($config['type']=='image')
                    {
                        $fields[$key]['width'] = Configuration::get($key.'_WIDTH');
                        $fields[$key]['height'] = Configuration::get($key.'_HEIGHT');
                    }
                    elseif($config['type']=='file')
                    {    
                        if(Configuration::get($key))
                        {
                            $display_img = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.Configuration::get($key));
                            $img_del_link = $this->baseAdminPath.'&deldefaultavataimage=true&control=image';
                        }
                        else
                        {
                            $display_img = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/default_customer.png');
                        }             
                    }    
                    else
                        $fields[$key] = Configuration::get($key);                   
                }
        }
    }
    $urls_sitemap=array();
    $languages = Language::getLanguages(true);
    foreach($languages as $lang)
        $urls_sitemap[]= array(
            'link'=>trim($this->getBaseLink(),'/').'/'.$lang['iso_code'].'/modules/ybc_blog/sitemap.xml',
            'img'=> $this->getBaseLink().'img/l/'.$lang['id_lang'].'.jpg'
        );
    $sidebars=array(
        'sidebar_new' => array(
            'title'=>$this->l('Latest posts'),
            'name'=> 'YBC_BLOG_SHOW_LATEST_NEWS_BLOCK',
        ),
        'sidebar_popular' =>array(
            'name'=>'YBC_BLOG_SHOW_POPULAR_POST_BLOCK',
            'title'=>  $this->l('Popular posts'),
        ),
        'sidebar_featured' => array(
            'title'=>$this->l('Featured posts'),
            'name'=>'YBC_BLOG_SHOW_FEATURED_BLOCK',
        ),
        'sidebar_gallery' => array(
            'title'=>$this->l('Photo gallery'),
            'name'=>'YBC_BLOG_SHOW_GALLERY_BLOCK',
        ),
        'sidebar_archived' => array(
            'title'=>$this->l('Archived posts'),
            'name'=>'YBC_BLOG_SHOW_ARCHIVES_BLOCK',
        ),
        'sidebar_categories' => array(
            'title'=>$this->l('Blog categories'),
            'name'=>'YBC_BLOG_SHOW_CATEGORIES_BLOCK',
        ),
        'sidebar_search' => array(
            'title'=>$this->l('Search in blog'),
            'name'=>'YBC_BLOG_SHOW_SEARCH_BLOCK',
        ),
        'sidebar_tags' => array(
            'title'=>$this->l('Blog tags'),
            'name'=>'YBC_BLOG_SHOW_TAGS_BLOCK'
        ),
        'sidebar_comments' => array(
            'title'=>$this->l('Latest comments'),
            'name'=>'YBC_BLOG_SHOW_COMMENT_BLOCK',
        ),
        'sidebar_authors' => array(
            'title'=>$this->l('Top authors'),
            'name'=>'YBC_BLOG_SHOW_AUTHOR_BLOCK',
        ),
        'sidebar_htmlbox' => array(
            'title'=>$this->l('HTML box'),
            'name'=>'YBC_BLOG_SHOW_HTML_BOX',
        ),
        'sidebar_rss' => array(
            'title'=>$this->l('Blog Rss'),
            'name'=>'YBC_BLOG_ENABLE_RSS_SIDEBAR',
        ),
    );
    $homepages=array(
        'homepage_new'=>array(
            'title'=>$this->l('Latest posts'),
            'name'=>'YBC_BLOG_SHOW_LATEST_BLOCK_HOME',
        ),
        'homepage_popular' => array(
            'title'=>$this->l('Popular posts'),
            'name'=>'YBC_BLOG_SHOW_POPULAR_BLOCK_HOME'
        ),
        'homepage_featured' => array(
            'title'=>$this->l('Featured posts'),
            'name'=> 'YBC_BLOG_SHOW_FEATURED_BLOCK_HOME',
        ),
        'homepage_categories' => array(
            'title'=>$this->l('Featured categories'),
            'name'=> 'YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME',
        ),
        'homepage_gallery' => array(
            'title'=>$this->l('Photo gallery'),
            'name'=>'YBC_BLOG_SHOW_GALLERY_BLOCK_HOME',
        ),
    );
    $position_sidebar= explode(',',Configuration::get('YBC_BLOG_POSITION_SIDEBAR') ? Configuration::get('YBC_BLOG_POSITION_SIDEBAR'):'sidebar_categories,sidebar_search,sidebar_new,sidebar_popular,sidebar_featured,sidebar_tags,sidebar_gallery,sidebar_archived,sidebar_comments,sidebar_authors,sidebar_htmlbox,sidebar_rss');
    if(!in_array('sidebar_htmlbox',$position_sidebar))
        $position_sidebar[]='sidebar_htmlbox';
    $position_homepages= explode(',',Configuration::get('YBC_BLOG_POSITION_HOMEPAGE')? Configuration::get('YBC_BLOG_POSITION_HOMEPAGE'):'homepage_new,homepage_popular,homepage_featured,homepage_categories,homepage_gallery');
    $helper->tpl_vars = array(
		'base_url' => $this->context->shop->getBaseURL(),
		'language' => array(
			'id_lang' => $language->id,
			'iso_code' => $language->iso_code
		),
		'fields_value' => $fields,
		'languages' => $this->context->controller->getLanguages(),
		'id_language' => $this->context->language->id,
        'cancel_url' => $icon!='icon-email' ? $this->baseAdminPath.'&control=post&list=true':false,
        'isConfigForm' => true,
        'sidebars'=>$sidebars,
        'position_sidebar'=>$position_sidebar,
        'url_sitemap' => trim($this->getBaseLink(),'/').'/modules/ybc_blog/sitemap.xml',
        'urls_sitemap' => count($urls_sitemap) > 1 ? $urls_sitemap : false,
        'homepages' => $homepages,
        'position_homepages'=>$position_homepages,
        'configTabs' => $control=='config'? $this->configTabs:array(),
        'image_baseurl' => _PS_YBC_BLOG_IMG_,
        'display_img' => isset($display_img)? $display_img : '',
        'img_del_link' => isset($img_del_link) ? $img_del_link :'',
        'link_module_blog' => $this->_path,
    );
    
    $this->_html .= $helper->generateForm(array($fields_form));		
 }
 
 private function _postConfig($configs,$dirImg='',$width_image='',$height_image='')
 {
    $errors = array();
    $languages = Language::getLanguages(false);
    $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
    $key_values = array();
    $aliasArg = array('YBC_BLOG_ALIAS','YBC_BLOG_ALIAS_POST','YBC_BLOG_ALIAS_CATEGORY','YBC_BLOG_ALIAS_GALLERY','YBC_BLOG_ALIAS_LATEST','YBC_BLOG_ALIAS_POPULAR','YBC_BLOG_ALIAS_FEATURED','YBC_BLOG_ALIAS_SEARCH','YBC_BLOG_ALIAS_AUTHOR','YBC_BLOG_ALIAS_AUTHOR2','YBC_BLOG_ALIAS_TAG');
    if(Tools::isSubmit('saveConfig'))
    { 
        Hook::exec('actionUpdateBlog', array());
        if($configs)
        {
            foreach($configs as $key => $config)
            {
                $label = $config['label'];
                if(isset($config['lang']) && $config['lang'])
                {
                    $key_lang_default = trim(Tools::getValue($key.'_'.$id_lang_default));
                    if(isset($config['required']) && $config['required'] && $config['type']!='switch' && $key_lang_default=='')
                    {
                        $errors[] = sprintf($this->l('%s is required'),$config['label']);
                    }
                    if($key_lang_default && in_array($key,$aliasArg) && !Validate::isLinkRewrite($key_lang_default))
                        $errors[] = sprintf($this->l('%s is not valid'),$config['label']);  
                    elseif($key_lang_default && !Validate::isCleanHtml($key_lang_default))
                        $errors[] = sprintf($this->l('%s is not valid'),$config['label']);   
                    $key_values[$key][$id_lang_default] = $key_lang_default;  
                    foreach($languages as $language)
                    {
                        $id_lang = (int)$language['id_lang'];
                        if($id_lang!=$id_lang_default)
                        {
                            $key_lang = trim(Tools::getValue($key.'_'.$id_lang));
                            if($key_lang && in_array($key,$aliasArg) && !Validate::isLinkRewrite($key_lang))
                                $errors[] = sprintf($this->l('%s is not valid in %s'),$config['label'],$language['iso_code']);  
                            elseif($key_lang && !Validate::isCleanHtml($key_lang))
                                $errors[] = sprintf($this->l('%s is not valid in %s'),$config['label'],$language['iso_code']); 
                            $key_values[$key][$id_lang] = $key_lang;  
                        }
                    }                   
                }
                elseif($config['type']=='image')
                {
                    $key_width = Tools::getValue($key.'_WIDTH');
                    if(!$key_width)
                        $errors[] = sprintf($this->l('%s width is required'),$label);
                    elseif(!Validate::isFloat($key_width))
                        $errors[] = sprintf($this->l('%s width is not valid'),$label);
                    elseif($key_width && ($key_width <50 ||$key_width >3000))
                        $errors[] = sprintf($this->l('%s width needs to be from 50 to 3000'),$label);
                    $key_height = Tools::getValue($key.'_HEIGHT');
                    if(!$key_height)
                        $errors[] = sprintf($this->l('%s height is required'),$label);
                    elseif(!Validate::isFloat($key_height))
                        $errors[] = sprintf($this->l('%s height is not valid'),$label);
                    elseif($key_height && ($key_height<50 || $key_height>3000) )
                        $errors[] = sprintf($this->l('%s height needs to be from 50 to 3000'),$label);
                    $key_values[$key.'_WIDTH'] = $key_width;
                    $key_values[$key.'_HEIGHT'] = $key_height;
                }
                else
                {
                    $key_value = Tools::getValue($key);
                    if(isset($config['required']) && $config['required'] && $config['type']!='switch' && trim($key_value) == '')
                    {
                        $errors[] = sprintf($this->l('%s is required'),$config['label']);
                    }
                    if(trim($key_value) && isset($config['validate']) && method_exists('Validate',$config['validate']))
                    {
                        $validate = $config['validate'];
                        if(!Validate::$validate(trim($key_value)))
                            $errors[] = sprintf($this->l('%s is not valid'),$config['label']); 
                        unset($validate);
                    }
                    elseif($key_value && !is_array($key_value) && !Validate::isCleanHtml(trim($key_value)))
                    {
                        $errors[] = sprintf($this->l('%s is not valid'),$config['label']); 
                    }
                    elseif($key_value && is_array($key_value) && !Ybc_blog::validateArray($key_value))
                        $errors[] = sprintf($this->l('%s is not valid'),$config['label']); 
                    $key_values[$key] = $key_value;
                }                    
            }
        }
        $YBC_BLOG_CAPTCHA_TYPE = Tools::getValue('YBC_BLOG_CAPTCHA_TYPE');
        if($YBC_BLOG_CAPTCHA_TYPE=='google' && !$key_values['YBC_BLOG_CAPTCHA_SITE_KEY'])
        {
            $errors[] = $this->l('Site key is required');
        }
        if($YBC_BLOG_CAPTCHA_TYPE=='google3' && !$key_values['YBC_BLOG_CAPTCHA_SITE_KEY3'])
        {
            $errors[] = $this->l('Site key is required');
        }
        if($YBC_BLOG_CAPTCHA_TYPE=='google' && !$key_values['YBC_BLOG_CAPTCHA_SECRET_KEY'])
        {
            $errors[] = $this->l('Secret key is required');
        }
        if($YBC_BLOG_CAPTCHA_TYPE=='google3' && !$key_values['YBC_BLOG_CAPTCHA_SECRET_KEY3'])
        {
            $errors[] = $this->l('Secret key is required');
        }
        //Custom validation
        $control = Tools::getValue('control');
        if($control=='seo')
        {
            if(!$errors)
            {
                $aliasArg = array('YBC_BLOG_ALIAS','YBC_BLOG_ALIAS_POST','YBC_BLOG_ALIAS_CATEGORY','YBC_BLOG_ALIAS_GALLERY','YBC_BLOG_ALIAS_LATEST','YBC_BLOG_ALIAS_POPULAR','YBC_BLOG_ALIAS_FEATURED','YBC_BLOG_ALIAS_SEARCH','YBC_BLOG_ALIAS_AUTHOR','YBC_BLOG_ALIAS_AUTHOR2','YBC_BLOG_ALIAS_TAG');
                $alias = array();
                foreach($languages as $lang)
                {
                    $alias[$lang['id_lang']]=array();
                    foreach($aliasArg as $aliaKey)
                    {
                        $postedAlias = trim(Tools::getValue($aliaKey.'_'.$lang['id_lang']));
                        
                        if($postedAlias && in_array($postedAlias,$alias[$lang['id_lang']]))
                        {
                            $errors[] = sprintf($this->l('Alias needs to be unique in %s'),$lang['iso_code']);
                            break;                        
                        }
                        elseif($postedAlias){
                            $alias[$lang['id_lang']][] = $postedAlias;
                        }
                    } 
                }
                
            }
        }
        if(Tools::isSubmit('YBC_BLOG_SHOW_AUTHOR_BLOCK') && isset($key_values['YBC_BLOG_AUTHOR_NUMBER']) &&  (int)$key_values['YBC_BLOG_AUTHOR_NUMBER'] <= 0)
            $errors[] = $this->l('Maximum number of positive authors needs to be greater than 0');
        if(Tools::isSubmit('YBC_BLOG_SHOW_COMMENT_BLOCK') && isset($key_values['YBC_BLOG_COMMENT_LENGTH']) && (int)$key_values['YBC_BLOG_COMMENT_LENGTH'] <= 0)
            $errors[] = $this->l('Maximum comment length of latest comments displayed needs to be greater than 0');
        if(Tools::isSubmit('YBC_BLOG_SHOW_COMMENT_BLOCK') && isset($key_values['YBC_BLOG_COMMENT_NUMBER']) &&  (int)$key_values['YBC_BLOG_COMMENT_NUMBER'] <= 0)
            $errors[] = $this->l('Maximum number of latest comments displayed in sidebar needs to be greater than 0');
        if(Tools::isSubmit('YBC_BLOG_GALLERY_BLOCK_SIDEBAR_SLIDER_ENABLED') && $key_values['YBC_BLOG_GALLERY_POST_NUMBER'] && (int)$key_values['YBC_BLOG_GALLERY_POST_NUMBER'] <= 0)
            $errors[] = $this->l('Maximum number of featured gallery images displayed needs to be greater than 0');
        if(Tools::isSubmit('YBC_BLOG_SHOW_LATEST_NEWS_BLOCK') && isset($key_values['YBC_BLOG_LATES_POST_NUMBER']) &&  (int)$key_values['YBC_BLOG_LATES_POST_NUMBER'] <= 0)
            $errors[] = $this->l('Number of latest posts displayed needs to be greater than 0');
        if(Tools::isSubmit('YBC_BLOG_SHOW_POPULAR_POST_BLOCK') && isset($key_values['YBC_BLOG_PUPULAR_POST_NUMBER']) &&  (int)$key_values['YBC_BLOG_PUPULAR_POST_NUMBER'] <= 0)
            $errors[] = $this->l('Number of popular posts displayed needs to be greater than 0');
        if(Tools::isSubmit('YBC_BLOG_SHOW_FEATURED_BLOCK') && isset($key_values['YBC_BLOG_FEATURED_POST_NUMBER']) &&  (int)$key_values['YBC_BLOG_FEATURED_POST_NUMBER'] <= 0)
            $errors[] = $this->l('Maximum number of featured posts displayed needs to be greater than 0');
        if(Tools::isSubmit('YBC_BLOG_LATES_POST_NUMBER') && isset($key_values['YBC_BLOG_MAX_COMMENT']) &&  (int)$key_values['YBC_BLOG_MAX_COMMENT'] < 0)
            $errors[] = $this->l('Maximum number of latest comments displayed needs to be greater than 0');
        if(Tools::isSubmit('YBC_BLOG_DEFAULT_RATING') && (int)$key_values['YBC_BLOG_DEFAULT_RATING'] < 1 || (int)$key_values['YBC_BLOG_DEFAULT_RATING'] >5)
            $errors[] = $this->l('Default rating must be between 1 - 5');     
        if(Tools::isSubmit('YBC_BLOG_ITEMS_PER_PAGE') && $key_values['YBC_BLOG_ITEMS_PER_PAGE']!='' && Validate::isInt($key_values['YBC_BLOG_ITEMS_PER_PAGE']) && (int)$key_values['YBC_BLOG_ITEMS_PER_PAGE'] <= 0)
            $errors[] = $this->l('Number of posts per page on main page needs to be greater than 0');
        if(Tools::isSubmit('YBC_BLOG_SHOW_TAGS_BLOCK') && isset($key_values['YBC_BLOG_TAGS_NUMBER']) &&  (int)$key_values['YBC_BLOG_TAGS_NUMBER'] <= 0)
            $errors[] = $this->l('Maximum number of tags displayed on Tags block needs to be greater than 0');
        if(Tools::isSubmit('YBC_BLOG_POST_EXCERPT_LENGTH') && (int)$key_values['YBC_BLOG_POST_EXCERPT_LENGTH'] < 0)
            $errors[] = $this->l('Post excerpt length cannot be smaller than 0');
        if(Tools::isSubmit('YBC_BLOG_GALLERY_PER_PAGE') && (int)$key_values['YBC_BLOG_GALLERY_PER_PAGE'] <= 0)
            $errors[] = $this->l('Number of image per page needs to be greater than 0');
        if(Tools::isSubmit('YBC_BLOG_COMMENT_PER_PAGE') && (int)$key_values['YBC_BLOG_COMMENT_PER_PAGE'] <= 0)
            $errors[] = $this->l('Number of comment per page needs to be greater than 0');
        if($control=='homepage')
        {
            if(isset($key_values['YBC_BLOG_SHOW_LATEST_BLOCK_HOME']) && $key_values['YBC_BLOG_SHOW_LATEST_BLOCK_HOME'] && Validate::isUnsignedInt($key_values['YBC_BLOG_LATEST_POST_NUMBER_HOME']))
            {
                if($key_values['YBC_BLOG_LATEST_POST_NUMBER_HOME']=='')
                    $errors[] = $this->l('Maximum number of latest posts displayed is required');
                elseif($key_values['YBC_BLOG_LATEST_POST_NUMBER_HOME']<=0)
                    $errors[] = $this->l('Maximum number of latest posts displayed needs to be greater than 0');
            }
            if(isset($key_values['YBC_BLOG_SHOW_POPULAR_BLOCK_HOME']) && $key_values['YBC_BLOG_SHOW_POPULAR_BLOCK_HOME'] && Validate::isUnsignedInt($key_values['YBC_BLOG_POPULAR_POST_NUMBER_HOME']))
            {
                if($key_values['YBC_BLOG_POPULAR_POST_NUMBER_HOME']=='')
                    $errors[] = $errors[] = $this->l('Maximum number of popular posts displayed is required');
                elseif($key_values['YBC_BLOG_POPULAR_POST_NUMBER_HOME']<=0)
                    $errors[] = $this->l('Maximum number of popular posts displayed needs to be greater than 0');
            }
            if(isset($key_values['YBC_BLOG_SHOW_FEATURED_BLOCK_HOME']) && $key_values['YBC_BLOG_SHOW_FEATURED_BLOCK_HOME'] && Validate::isUnsignedInt($key_values['YBC_BLOG_FEATURED_POST_NUMBER_HOME']))
            {
                if($key_values['YBC_BLOG_FEATURED_POST_NUMBER_HOME']=='')
                    $errors[] = $this->l('Maximum number of featured posts displayed is required');
                elseif($key_values['YBC_BLOG_FEATURED_POST_NUMBER_HOME'] <=0)
                    $errors[] = $this->l('Maximum number of featured posts displayed needs to be greater than 0');
            }
            if(isset($key_values['YBC_BLOG_SHOW_GALLERY_BLOCK_HOME']) && $key_values['YBC_BLOG_SHOW_GALLERY_BLOCK_HOME'] && Validate::isUnsignedInt($key_values['YBC_BLOG_GALLERY_POST_NUMBER_HOME']))
            {
                if($key_values['YBC_BLOG_GALLERY_POST_NUMBER_HOME']=='')
                    $errors[] = $this->l('Maximum number of featured gallery images displayed is required');
                elseif($key_values['YBC_BLOG_GALLERY_POST_NUMBER_HOME']<=0)
                    $errors[] = $this->l('Maximum number of featured gallery images displayed needs to be greater than 0');
            }
            if(isset($key_values['YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME']) && $key_values['YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME'] && Validate::isUnsignedInt($key_values['YBC_BLOG_CATEGORY_POST_NUMBER_HOME']))
            {
                if($key_values['YBC_BLOG_CATEGORY_POST_NUMBER_HOME']=='')
                    $errors[] = $this->l('Maximum number of post categories displayed is required');
                elseif($key_values['YBC_BLOG_CATEGORY_POST_NUMBER_HOME']<=0)
                    $errors[] = $this->l('Maximum number of post categories displayed needs to be greater than 0');
            }    
        }
        if($emailsStr = Tools::getValue('YBC_BLOG_ALERT_EMAILS'))
        {
            $emails = explode(',',$emailsStr);
            if($emails)
            {
                foreach($emails as $email)
                {
                    if(!Validate::isEmail(trim($email)))
                    {
                        $errors[] = $this->l('One of the submitted emails is not valid');
                        break;
                    }
                }
            }
        }
        if(!$errors)
        {
            if($configs)
            {
                foreach($configs as $key => $config)
                {
                    if(isset($config['lang']) && $config['lang'])
                    {
                        $valules = array();
                        foreach($languages as $lang)
                        {
                            if($config['type']=='switch')                                                           
                                $valules[$lang['id_lang']] = (int)$key_values[$key][$lang['id_lang']] ? 1 : 0;                                
                            else
                                $valules[$lang['id_lang']] = $key_values[$key][$lang['id_lang']] ? : $key_values[$key][$id_lang_default];
                        }
                        Configuration::updateValue($key,$valules,true);
                    }
                    else
                    {
                        if($config['type']=='switch')
                        {                           
                            Configuration::updateValue($key,(int)$key_values[$key] ? 1 : 0);
                        }
                        elseif($config['type']=='checkbox')
                            Configuration::updateValue($key,implode(',',$key_values[$key])); 
                        elseif($config['type']=='image')
                        {
                            Configuration::updateValue($key.'_WIDTH',$key_values[$key.'_WIDTH']);
                            Configuration::updateValue($key.'_HEIGHT',$key_values[$key.'_HEIGHT']);
                        }
                        elseif($config['type']=='blog_categories' && ($blog_categories  = Tools::getValue('blog_categories')) && is_array($blog_categories) && Ybc_blog::validateArray($blog_categories))
                        {
                            Configuration::updateValue($key,implode(',',$blog_categories));
                        }
                        elseif($config['type']=='file')
                        {      
                            if(isset($_FILES[$key]['tmp_name']) && isset($_FILES[$key]['name']) && $_FILES[$key]['name'])
                            {
                                $_FILES[$key]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES[$key]['name']);
                                if(!Validate::isFileName($_FILES[$key]['name']))
                                {
                                    $errors[] = $this->l('Image is not valid');
                                }
                                else
                                {
                                    if(file_exists($dirImg.$_FILES[$key]['name']))
                                    {
                                        $_FILES[$key]['name'] = $this->createNewFileName($dirImg,$_FILES[$key]['name']);
                                    }
                                    $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'], '.'), 1));
                                    $imagesize = @getimagesize($_FILES[$key]['tmp_name']);
                                    if (isset($_FILES[$key]) &&
                                        !empty($_FILES[$key]['tmp_name']) &&
                                        !empty($imagesize) &&
                                        in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                                    )
                                    {
                                        $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                                        $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE');
                                        if($_FILES[$key]['size'] > $max_file_size*1024*1024)
                                            $errors[] = sprintf($this->l('Image file is too large. Limit: %sMb'),$max_file_size);
                                        elseif (!$temp_name || !move_uploaded_file($_FILES[$key]['tmp_name'], $temp_name))
                                            $errors[] = $this->l('Cannot upload the file');
                                        elseif(!ImageManager::resize($temp_name, $dirImg.$_FILES[$key]['name'], $width_image, $height_image, $type))
                                            $errors[] = $this->displayError($this->l('An error occurred during the image upload process.'));
                                        if (isset($temp_name) && file_exists($temp_name))
                                            @unlink($temp_name);
                                        if(($img = Configuration::get($key)))
                                        {
                                            if(file_exists($dirImg.$img))
                                                @unlink($dirImg.$img);
                                        }
                                        Configuration::updateValue($key,$_FILES[$key]['name']);
                                    }
                                }

                                
                            }
                        }
                        else
                            Configuration::updateValue($key,trim($key_values[$key]));   
                    }                        
                }
            }
            $this->refreshCssCustom();
        }
        if (count($errors))
        {
           $this->errorMessage = $this->displayError($errors);  
        }
        if($control=='sidebar')
        {
            $config_values=array(
                'YBC_BLOG_SHOW_CATEGORIES_BLOCK' => Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK'),
                'YBC_BLOG_SHOW_POPULAR_POST_BLOCK' => Configuration::get('YBC_BLOG_SHOW_POPULAR_POST_BLOCK'),
                'YBC_BLOG_SHOW_LATEST_NEWS_BLOCK' => Configuration::get('YBC_BLOG_SHOW_LATEST_NEWS_BLOCK'),
                'YBC_BLOG_SHOW_GALLERY_BLOCK' => Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK'),
                'YBC_BLOG_SHOW_ARCHIVES_BLOCK' => Configuration::get('YBC_BLOG_SHOW_ARCHIVES_BLOCK'),
                'YBC_BLOG_SHOW_SEARCH_BLOCK' => Configuration::get('YBC_BLOG_SHOW_SEARCH_BLOCK'),
                'YBC_BLOG_SHOW_TAGS_BLOCK' => Configuration::get('YBC_BLOG_SHOW_TAGS_BLOCK'),
                'YBC_BLOG_SHOW_COMMENT_BLOCK' => Configuration::get('YBC_BLOG_SHOW_COMMENT_BLOCK'),
                'YBC_BLOG_SHOW_AUTHOR_BLOCK' => Configuration::get('YBC_BLOG_SHOW_AUTHOR_BLOCK'),
                'YBC_BLOG_SHOW_HTML_BOX' => Configuration::get('YBC_BLOG_SHOW_HTML_BOX'),
                'YBC_BLOG_SHOW_FEATURED_BLOCK' => Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK'),
            );
        }
        if($control=='homepage')
        {
            $config_values=array(
                'YBC_BLOG_SHOW_LATEST_BLOCK_HOME' => Configuration::get('YBC_BLOG_SHOW_LATEST_BLOCK_HOME'),
                'YBC_BLOG_SHOW_POPULAR_BLOCK_HOME' => Configuration::get('YBC_BLOG_SHOW_POPULAR_BLOCK_HOME'),
                'YBC_BLOG_SHOW_FEATURED_BLOCK_HOME' => Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK_HOME'),
                'YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME' => Configuration::get('YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME'),
                'YBC_BLOG_SHOW_GALLERY_BLOCK_HOME' => Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK_HOME'),
            );
        }
        if(Tools::isSubmit('ajax'))
        {
            die(json_encode(
                array(
                    'messageType' => $errors ? 'error' : 'success',
                    'message' => $errors ? $this->errorMessage : $this->displayConfirmation($this->l('Configuration saved')),
                    'ybc_link_desc'=>$this->getLink(),
                    'config_values' => isset($config_values) ? $config_values:'',
                )
            ));
        }
        
        if(!count($errors))
           Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control='.$control);            
    }
 }
 public function refreshCssCustom()
 {
    $color = Configuration::get('YBC_BLOG_CUSTOM_COLOR');
    if(!$color) 
        $color = '#FF4C65';
    $color_hover= Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER');
    if(!$color_hover)
        $color_hover='#FF4C65';
    $css = file_exists(dirname(__FILE__).'/views/css/dynamic_style.css') ? Tools::file_get_contents(dirname(__FILE__).'/views/css/dynamic_style.css') : ''; 
    if($css)
        $css = str_replace(array('[color]','[color_hover]'),array($color,$color_hover),$css);
    file_put_contents(dirname(__FILE__).'/views/css/custom.css',$css);
 }
 public function getLink($controller = 'blog', $params = array(),$id_lang=0)
 {
    $context = Context::getContext();      
    $id_lang =  $id_lang ? $id_lang : $context->language->id;
    $alias = $this->alias;
    $friendly = $this->friendly;
    $blogLink = new Ybc_blog_link_class();
    $subfix = (int)Configuration::get('YBC_BLOG_URL_SUBFIX') ? '.html' : '';
    $page = isset($params['page']) && $params['page'] ? $params['page'] : '';
    if(trim($page)!='')
    {
        $page = $page.'/';
    }
    else
        $page='';
    if($friendly && $alias)
    {    
        $url = $blogLink->getBaseLinkFriendly(null, null).$blogLink->getLangLinkFriendly($id_lang, null, null).$alias.'/';
        if($controller=='gallery')
        {                
           $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_GALLERY',$id_lang)) ? $subAlias : 'gallery').($page ? '/'.rtrim($page,'/') : '');
           return $url;
        }
        elseif($controller=='category')
        {
            $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORIES',$id_lang)) ? $subAlias : 'categories').($page ? '/'.rtrim($page,'/') : '');
            return $url;
        }
        elseif($controller=='comment')
        {
            $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_COMMENTS',$id_lang)) ? $subAlias : 'comments').($page ? '/'.rtrim($page,'/') : '');
            return $url;
        }
        elseif($controller=='rss')
        {
            $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS',$id_lang)) ? $subAlias : 'rss');
            if(isset($params['id_category']) && $categoryAlias = Ybc_blog_category_class::getCategoryAlias((int)$params['id_category'],$id_lang))
            {
                $url .= '/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORY',$id_lang)) ? $subAlias : 'category').'/'.(int)$params['id_category'].'-'.$categoryAlias.$subfix;
            }
            elseif(isset($params['id_author']) && isset($params['is_customer']) && $params['is_customer'] &&  $authorAlias = (isset($params['alias']) ? $params['alias'] : 'community-author'))
            {
                $url .= '/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR2',$id_lang)) ? $subAlias : 'community-author').'/'.(int)$params['id_author'].'-'.$authorAlias;
            }
            elseif(isset($params['id_author']) && $authorAlias = (isset($params['alias']) ? $params['alias'] : 'author'))
            {
                $url .= '/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR',$id_lang)) ? $subAlias : 'author').'/'.(int)$params['id_author'].'-'.$authorAlias;
            }
            elseif(isset($params['latest_posts']))
            {
                $url .= '/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS_LATEST',$id_lang)) ? $subAlias : 'latest-posts');
            }
            elseif(isset($params['popular_posts']))
            {
                $url .= '/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS_POPULAR',$id_lang)) ? $subAlias : 'popular-posts');
            }
            elseif(isset($params['featured_posts']))
            {
                $url .= '/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS_FEATURED',$id_lang)) ? $subAlias : 'featured-posts');
            }
            return $url;
        }
        elseif($controller=='blog')
        {
            if(isset($params['edit_comment']) && (int)$params['edit_comment'] && isset($params['id_post']) && $params['id_post'] && $postAlias = Ybc_blog_post_class::getPostAlias((int)$params['id_post'],$id_lang))
            {
                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_POST',$id_lang)) ? $subAlias : 'post').'/'.(int)$params['id_post'].'-'.(int)$params['edit_comment'].'-'.$postAlias.$subfix;
            }
            elseif( isset($params['all_comment']) && $params['all_comment'] &&  isset($params['id_post']) && $postAlias = Ybc_blog_post_class::getPostAlias((int)$params['id_post'],$id_lang))
            {
                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_POST',$id_lang)) ? $subAlias : 'post').'/allcomments/'.(int)$params['id_post'].'-'.$postAlias.$subfix;
            }
            elseif(isset($params['id_post']) && $postAlias = Ybc_blog_post_class::getPostAlias((int)$params['id_post'],$id_lang))
            {
                if(Configuration::get('YBC_BLOG_URL_NO_ID'))
                    $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_POST',$id_lang)) ? $subAlias : 'post').'/'.$postAlias.$subfix;
                else
                    $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_POST',$id_lang)) ? $subAlias : 'post').'/'.$params['id_post'].'-'.$postAlias.$subfix;
            }
            elseif(isset($params['id_category']) && $categoryAlias = Ybc_blog_category_class::getCategoryAlias((int)$params['id_category'],$id_lang))
            {
                 if(Configuration::get('YBC_BLOG_URL_NO_ID'))
                    $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORY',$id_lang)) ? $subAlias : 'category').($page ? '/'.rtrim($page) : '/').$categoryAlias.$subfix;
                 else
                    $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORY',$id_lang)) ? $subAlias : 'category').($page ? '/'.rtrim($page) : '/').$params['id_category'].'-'.$categoryAlias.$subfix;
            }
            elseif(isset($params['id_author']) && isset($params['is_customer']) && $params['is_customer'] && $authorAlias = (isset($params['alias']) ? $params['alias'] : 'community-author'))
            {
                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR2',$id_lang)) ? $subAlias : 'community-author').($page ? '/'.rtrim($page) : '/').(int)$params['id_author'].'-'.$authorAlias;
            }
            elseif(isset($params['id_author']) && $authorAlias = (isset($params['alias']) ? $params['alias'] : 'author'))
            {

                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR',$id_lang)) ? $subAlias : 'author').'/'.$page.(int)$params['id_author'].'-'.$authorAlias;
            }
            elseif(isset($params['tag']))
            {
                $url .= $page.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_TAG',$id_lang)) ? $subAlias : 'tag').'/'.(string)$params['tag'];
            }
            elseif(isset($params['search']))
            {
                $url .= $page.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_SEARCH',$id_lang)) ? $subAlias : 'search').'/'.(string)$params['search'];
            }
            elseif(isset($params['latest']))
            {
                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_LATEST',$id_lang)) ? $subAlias : 'latest').($page ? '/'.rtrim($page,'/') : '');
            }
            elseif(isset($params['popular']))
            {
                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_POPULAR',$id_lang)) ? $subAlias : 'popular').($page ? '/'.rtrim($page,'/') : '');
            }
            elseif(isset($params['featured']))
            {
                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_FEATURED',$id_lang)) ? $subAlias : 'featured').($page ? '/'.rtrim($page,'/') : '');
            }
            elseif(isset($params['month']) && isset($params['year']))
            {
                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_MONTHS',$id_lang)) ? $subAlias : 'month').'/'.$params['month'].'/'.$params['year'].($page ? '/'.rtrim($page,'/') : '');
            }
            elseif(isset($params['year']))
            {
                $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_YEARS',$id_lang)) ? $subAlias : 'year').'/'.$params['year'].($page ? '/'.rtrim($page,'/') : '');
            }
            else
            {
                if($page)
                    $url .= trim($page,'/');
                else
                    $url = rtrim($url,'/');
            }
            if(isset($params['edit_comment']) && (int)$params['edit_comment'] && isset($params['id_post']) && $params['id_post'])  
                $url .='#ybc-blog-form-comment';
            return $url;            
        }
        elseif($controller=='author')
        {
            $url .= (($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR',$id_lang)) ? $subAlias : 'author').($page ? '/'.rtrim($page,'/') : '');
            return $url;
        }            
    }
    return $this->context->link->getModuleLink($this->name,$controller,$params,null,$id_lang);
 }
 public function getEverageReviews($id_post)
 {
    $totalRating = Ybc_blog_post_class::getTotalReviewsWithRating($id_post);
    $numRating = Ybc_blog_post_class::countTotalReviewsWithRating($id_post);
    if($numRating > 0)
    {
        $rat = Tools::ps_round($totalRating/$numRating,2);
        $rat_ceil = ceil($totalRating/$numRating);
        $rat_floor = floor($totalRating/$numRating);
        if($rat_ceil-$rat <=0.25)
            return $rat_ceil;
        if($rat-$rat_floor<=0.25)   
            return $rat_floor;
        return $rat_floor+0.5;        
    }
        
    return 0;        
 }
 
 /**
  * Hooks 
  */
 public function hookDisplayLeftColumn()
 {
    $fc = Tools::getValue('fc');
    $module = Tools::getValue('module');
    if(Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY') && ($fc!='module' || $module!=$this->name))
        return '';
    $params=array();
    $sidebars=array(
            'sidebar_new' => Configuration::get('YBC_BLOG_SHOW_LATEST_NEWS_BLOCK') ? $this->hookBlogNewsBlock($params):'',
            'sidebar_popular' => Configuration::get('YBC_BLOG_SHOW_POPULAR_POST_BLOCK') ? $this->hookBlogPopularPostsBlock($params):'',
            'sidebar_featured' => Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK') ? $this->hookBlogFeaturedPostsBlock($params):'',
            'sidebar_gallery' => Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK') ? $this->hookBlogGalleryBlock($params):'',
            'sidebar_archived' => Configuration::get('YBC_BLOG_SHOW_ARCHIVES_BLOCK') ? $this->hookBlogArchivesBlock():'',
            'sidebar_categories' => Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK') ? $this->hookBlogCategoriesBlock():'',
            'sidebar_search' => Configuration::get('YBC_BLOG_SHOW_SEARCH_BLOCK') ? $this->hookBlogSearchBlock():'',
            'sidebar_tags' => Configuration::get('YBC_BLOG_SHOW_TAGS_BLOCK') ? $this->hookBlogTagsBlock():'',
            'sidebar_comments' => Configuration::get('YBC_BLOG_SHOW_COMMENT_BLOCK') ? $this->hookBlogComments():'',
            'sidebar_authors' => Configuration::get('YBC_BLOG_SHOW_AUTHOR_BLOCK') ? $this->hookBlogPositiveAuthor():'',
            'sidebar_htmlbox' => Configuration::get('YBC_BLOG_SHOW_HTML_BOX') ? $this->displayHtmlContent():'',
            'sidebar_rss' => Configuration::get('YBC_BLOG_ENABLE_RSS_SIDEBAR') && in_array('side_bar',explode(',',Configuration::get('YBC_BLOC_RSS_DISPLAY'))) ? $this->hookBlogRssSideBar():'',
    );
    $sidebars_postion= explode(',',Configuration::get('YBC_BLOG_POSITION_SIDEBAR') ? Configuration::get('YBC_BLOG_POSITION_SIDEBAR') :'sidebar_categories,sidebar_search,sidebar_new,sidebar_popular,sidebar_featured,sidebar_tags,sidebar_gallery,sidebar_archived,sidebar_comments,sidebar_authors,sidebar_htmlbox,sidebar_rss');
    if(!in_array('sidebar_htmlbox',$sidebars_postion))
        $sidebars_postion[] = 'sidebar_htmlbox';
    $display_slidebar = false;
    if($sidebars)
    {
        foreach($sidebars as $sidebar)
        {
            if($sidebar)
            {
                $display_slidebar = true;
                break;
            }    
        }
    }
    if(!$display_slidebar)
        return '';
    $this->context->smarty->assign(
        array(
            'sidebars_postion' => $sidebars_postion,
            'sidebars'=>$sidebars,
            'display_slidebar' => $display_slidebar,
        )
    );
    return $this->display(__FILE__, 'blocks.tpl');
  }
  public function displayHtmlContent()
  {
    if($content = Configuration::get('YBC_BLOG_CONTENT_HTML_BOX',$this->context->language->id))
    {
        $this->context->smarty->assign(
            array(
                'html_content_box' => $content,
                'page' => 'html_box',
                'html_title_box' => Configuration::get('YBC_BLOG_TITLE_HTML_BOX',$this->context->language->id) ? : $this->l('Html box'),
            )
        );
        return $this->display(__FILE__,'html_box.tpl');
    }
    return '';
  }
  public function hookBlogSidebar()
  {
      return $this->hookDisplayLeftColumn();
  }
  public function hookRightColumn()
  {
      return $this->hookDisplayLeftColumn();
  }      
  public function hookDisplayBackOfficeHeader()
  {
        $this->context->controller->addCSS($this->_path.'views/css/admin_all.css');
        $this->context->controller->addJS($this->_path.'views/js/admin_all.js');
        $controller = Tools::getValue('controller');
        $configure = Tools::getValue('configure');
        if(($controller=='AdminModules' && $configure==$this->name) || $controller=='AdminYbcBlogStatistics')
        {
            $this->context->controller->addCSS($this->_path.'views/css/admin.css');
            if(!$this->is17)
            {
                $this->context->controller->addCSS($this->_path.'views/css/admin_fix16.css'); 
            }
        }
        if($controller=='AdminYbcBlogStatistics')
        {
            $this->context->controller->addJquery();
            $this->context->controller->addJqueryPlugin('autocomplete');
            $this->context->controller->addCSS((__PS_BASE_URI__).'modules/'.$this->name.'/views/css/nv.d3_rtl.css','all');
            $this->context->controller->addCSS((__PS_BASE_URI__).'modules/'.$this->name.'/views/css/nv.d3.css','all');
        }
  }
  public function hookDisplayFooter()
  {
        $this->smarty->assign(array(
                'like_url' => $this->getLink('like'),
                'YBC_BLOG_SLIDER_SPEED' => (int)Configuration::get('YBC_BLOG_SLIDER_SPEED') > 0 ? (int)Configuration::get('YBC_BLOG_SLIDER_SPEED') : 5000,
                'YBC_BLOG_GALLERY_SPEED' => (int)Configuration::get('YBC_BLOG_GALLERY_SPEED') > 0 ? (int)Configuration::get('YBC_BLOG_GALLERY_SPEED') : 5000,
                'YBC_BLOG_GALLERY_SKIN' => Configuration::get('YBC_BLOG_GALLERY_SKIN') ? Configuration::get('YBC_BLOG_GALLERY_SKIN') : 'default',
                'YBC_BLOG_GALLERY_AUTO_PLAY' => (int)Configuration::get('YBC_BLOG_GALLERY_AUTO_PLAY') ? 1 : 0,
                'ybc_like_error' =>  addslashes($this->l('There was a problem while submitting your request. Try again later'))                                   
            )
        );
        return $this->display(__FILE__, 'footer.tpl');
  }
  public function hookDisplayHeader()
  {
    return $this->hookHeader();
  }
  public function hookHeader()
  { 
        $this->assignConfig();
        $controller = Tools::getValue('controller'); 
        $fc = Tools::getValue('fc');
        $module = Tools::getValue('module');
        if($controller=='myaccount'){
            $this->context->controller->addCSS($this->_path.'views/css/material-icons.css');
            $this->context->controller->addCSS($this->_path.'views/css/blog.css');
            return '';
        }
        if($controller=='index'  && Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY') && !Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK_HOME') && !Configuration::get('YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME') && !Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK_HOME') && !Configuration::get('YBC_BLOG_SHOW_POPULAR_BLOCK_HOME') && !Configuration::get('YBC_BLOG_SHOW_LATEST_BLOCK_HOME')) 
            return '';
        if($controller=='index'  && !Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY') && !Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK_HOME') && !Configuration::get('YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME') && !Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK_HOME') && !Configuration::get('YBC_BLOG_SHOW_POPULAR_BLOCK_HOME') && !Configuration::get('YBC_BLOG_SHOW_LATEST_BLOCK_HOME') && !Configuration::get('YBC_BLOG_ENABLE_RSS') && !Configuration::get('YBC_BLOG_SHOW_AUTHOR_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_COMMENT_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_TAGS_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_SEARCH_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_ARCHIVES_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_POPULAR_POST_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_LATEST_NEWS_BLOCK')) 
            return '';
        if( ($fc!='module' || $module!=$this->name) && $controller!='index' && $controller!='product' &&  $controller!='category'  && !Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY') && !Configuration::get('YBC_BLOG_ENABLE_RSS') && !Configuration::get('YBC_BLOG_SHOW_AUTHOR_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_COMMENT_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_TAGS_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_SEARCH_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_ARCHIVES_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_POPULAR_POST_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_LATEST_NEWS_BLOCK'))
            return '';
        elseif(($fc!='module' || $module!=$this->name) && $controller!='index' && $controller!='product' && $controller!='category'  && Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY'))
            return '';
        if($controller=='category' && $fc!='module'  && Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY') && !Configuration::get('YBC_BLOG_DISPLAY_CATEGORY_PAGE')) 
            return '';
        if($controller=='product'  && Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY') && !Configuration::get('YBC_BLOG_DISPLAY_PRODUCT_PAGE')) 
            return '';
        if($controller=='product'  && !Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY') && !Configuration::get('YBC_BLOG_DISPLAY_PRODUCT_PAGE')&& !Configuration::get('YBC_BLOG_ENABLE_RSS') && !Configuration::get('YBC_BLOG_SHOW_AUTHOR_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_COMMENT_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_TAGS_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_SEARCH_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_ARCHIVES_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_POPULAR_POST_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_LATEST_NEWS_BLOCK')) 
            return '';
        if($controller=='category' && $fc!='module'  && !Configuration::get('YBC_BLOG_DISPLAY_BLOG_ONLY') && !Configuration::get('YBC_BLOG_DISPLAY_CATEGORY_PAGE')&& !Configuration::get('YBC_BLOG_ENABLE_RSS') && !Configuration::get('YBC_BLOG_SHOW_AUTHOR_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_COMMENT_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_TAGS_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_SEARCH_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_ARCHIVES_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_POPULAR_POST_BLOCK') && !Configuration::get('YBC_BLOG_SHOW_LATEST_NEWS_BLOCK')) 
            return '';
        if(Module::isInstalled('ybc_blog')&& Module::isEnabled('ybc_blog'))
        {
            if(Ybc_blog_defines::checkCreatedColumn('ybc_blog_post','datetime_active'))
                Ybc_blog_post_class::autoActivePost();
        }

        if($controller!='index'){
            $this->context->controller->addJS($this->_path.'views/js/slick.js');
            $this->context->controller->addCSS($this->_path.'views/css/slick.css');
            $this->context->controller->addJS($this->_path.'views/js/owl.carousel.js');
            $this->context->controller->addJS($this->_path.'views/js/jquery.prettyPhoto.js');
            $this->context->controller->addJS($this->_path.'views/js/prettyPhoto.inc.js');
            $this->context->controller->addJS($this->_path.'views/js/jquery.lazyload.min.js'); 
            $this->context->controller->addJS($this->_path.'views/js/blog.js');           
            $this->context->controller->addCSS($this->_path.'views/css/prettyPhoto.css');
            $this->context->controller->addCSS($this->_path.'views/css/material-icons.css');
            $this->context->controller->addCSS($this->_path.'views/css/owl.carousel.css');
            $this->context->controller->addCSS($this->_path.'views/css/owl.theme.css');
            $this->context->controller->addCSS($this->_path.'views/css/owl.transitions.css');
            $this->context->controller->addCSS($this->_path.'views/css/blog.css');
            
        }       
        if($controller=='index') {
            if(Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK_HOME'))
            {
                $this->context->controller->addJS($this->_path.'views/js/jquery.prettyPhoto.js');
                $this->context->controller->addJS($this->_path.'views/js/prettyPhoto.inc.js');
                $this->context->controller->addCSS($this->_path.'views/css/prettyPhoto.css');
            }
            if(Configuration::get('YBC_BLOG_HOME_POST_TYPE')=='carousel' || (Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK_HOME') && Configuration::get('YBC_BLOG_GALLERY_BLOCK_HOME_SLIDER_ENABLED')))
            {
                $this->context->controller->addJS($this->_path.'views/js/owl.carousel.js');
                $this->context->controller->addCSS($this->_path.'views/css/owl.carousel.css');
                $this->context->controller->addCSS($this->_path.'views/css/owl.theme.css');
                $this->context->controller->addCSS($this->_path.'views/css/owl.transitions.css');
            }
            $this->context->controller->addJS($this->_path.'views/js/home_blog.js');
            if(!$this->is17)           
                $this->context->controller->addCSS($this->_path.'views/css/material-icons.css');
            $this->context->controller->addCSS($this->_path.'views/css/blog_home.css');
        }
              
        if(Configuration::get('YBC_BLOG_RTL_MODE')=='auto' && isset($this->context->language->is_rtl) && $this->context->language->is_rtl || Configuration::get('YBC_BLOG_RTL_MODE')=='rtl')
            $this->context->controller->addCSS($this->_path.'views/css/rtl.css'); 
        if($fc=='module' && $module=='ybc_blog')
        {
            $this->context->controller->addJS($this->_path.'views/js/jquery.nivo.slider.js');
            $this->context->controller->addCSS($this->_path.'views/css/nivo-slider.css');
            $this->context->controller->addCSS($this->_path.'views/css/themes/default/default.css');                
        }
        if($controller=='category' && Configuration::get('YBC_BLOG_DISPLAY_CATEGORY_PAGE') && $id_category=(int)Tools::getValue('id_category'))
        {
            if(Tools::isSubmit('displayPostRelatedCategories'))
            {
                die(json_encode(
                    array(
                        'html_block' => $this->displayPostRelatedCategories($id_category),
                    )
                ));
            }
            $this->context->controller->addJS($this->_path.'views/js/related.js'); 
        }
        return $this->getInternalStyles();
  }
  public function assignConfig()
  {
      $assign = array();
      $ybc_defines = new Ybc_blog_defines();
      foreach($ybc_defines->configs as $key => $val)
      {
            $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
      }
      foreach($ybc_defines->configs_seo as $key => $val)
      {
            $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
      }
      foreach($ybc_defines->configs_sitemap as $key => $val)
      {
            $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
      }
      foreach($ybc_defines->configs_sidebar as $key => $val)
      {
            $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
      }
      $ybc_defines->configs_homepage['YBC_BLOG_SHOW_CATEGORIES_BLOCK_HOME']=array(
            'label' => $this->l('Select blog categories to display'),
                'type' => 'blog_categories',
                'html_content' =>$this->displayBlogCategoryTre(Ybc_blog_category_class::getBlogCategoriesTree(0,true,$this->context->language->id,0,false),$this->getSelectedCategories()),
    			'categories' => Ybc_blog_category_class::getBlogCategoriesTree(0,true,$this->context->language->id,0,false),
    			'name' => 'categories',
                'selected_categories' => $this->getSelectedCategories(),
                'default' =>'',
      );
      foreach($ybc_defines->configs_homepage as $key => $val)
      {
            $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
      }
      foreach($ybc_defines->configs_postpage as $key => $val)
      {
            $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
      }
      foreach($ybc_defines->configs_postlistpage as $key => $val)
      {
            $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
      }
      foreach($ybc_defines->configs_productpage as $key => $val)
      {
            $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
      }
      foreach($ybc_defines->configs_categorypage as $key => $val)
      {
            $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
      }
      foreach($ybc_defines->configs_email as $key => $val)
      {
            $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
      }
      foreach($ybc_defines->socials as $key => $val)
      {
            $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
      }
      foreach($ybc_defines->customer_settings as $key => $val)
      {
            $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
      }
      foreach($ybc_defines->rss as $key => $val)
      {
            $assign[$key] = isset($val['lang']) && $val['lang'] ? Configuration::get($key, $this->context->language->id) : ($val['type']=='checkbox' || $val['type']=='blog_categories' ? explode(',',Configuration::get($key)) : Configuration::get($key));
      }
      if(Configuration::get('YBC_BLOG_RTL_MODE')=='auto' && isset($this->context->language->is_rtl) && $this->context->language->is_rtl || Configuration::get('YBC_BLOG_RTL_MODE')=='rtl')
        $rtl = true;
     else
        $rtl = false;
      $assign['YBC_BLOG_RTL_CLASS'] = $rtl ? 'ybc_blog_rtl_mode' : 'ybc_blog_ltr_mode'; 
      $assign['YBC_BLOG_SHOP_URI'] = _PS_BASE_URL_.__PS_BASE_URI__;  
      $fc = Tools::getValue('fc');
      $module = Tools::getValue('module');
      $controller = Tools::getValue('controller');
      $tabmanagament = Tools::getValue('tabmanagament');
      if($fc=='module' && $module=='ybc_blog' && $controller=='managementblog' && $tabmanagament=='post')
      {

            $this->context->smarty->assign('add_tmce',true);
      }
      $rating = (int)Tools::getValue('rating');
      $id_post = (int)Tools::getValue('id_post');
      $this->context->smarty->assign(
            array(
                'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT'),
                'allowGuestsComments' => (int)Configuration::get('YBC_BLOG_ALLOW_GUEST_COMMENT') ? true : false,
                'blogCommentAction' => $this->getLink('blog',array('id_post'=>(int)$id_post)),
                'hasLoggedIn' => $this->context->customer->isLogged(true), 
                'allow_report_comment' =>(int)Configuration::get('YBC_BLOG_ALLOW_REPORT') ? true : false,
                'display_related_products' =>(int)Configuration::get('YBC_BLOG_SHOW_RELATED_PRODUCTS') ? true : false,
                'allow_rating' => (int)Configuration::get('YBC_BLOG_ALLOW_RATING') ? true : false,
                'default_rating' => (int)$rating > 0 && (int)$rating <=5 ? (int)$rating  :(int)Configuration::get('YBC_BLOG_DEFAULT_RATING'),
                'use_capcha' => (int)Configuration::get('YBC_BLOG_USE_CAPCHA') ? true : false,
                'use_facebook_share' => (int)Configuration::get('YBC_BLOG_ENABLE_FACEBOOK_SHARE') ? true : false,
                'use_google_share' => (int)Configuration::get('YBC_BLOG_ENABLE_GOOGLE_SHARE') ? true : false,
                'use_twitter_share' => (int)Configuration::get('YBC_BLOG_ENABLE_TWITTER_SHARE') ? true : false,                    
                'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                'show_date' => (int)Configuration::get('YBC_BLOG_SHOW_POST_DATE') ? true : false,
                'show_tags' => (int)Configuration::get('YBC_BLOG_SHOW_POST_TAGS') ? true : false,
                'show_categories' => (int)Configuration::get('YBC_BLOG_SHOW_POST_CATEGORIES') ? true : false,
                'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                'enable_slideshow' => (int)Configuration::get('YBC_BLOG_ENABLE_POST_SLIDESHOW') ? true : false,
                'prettyAutoPlay' => (int)Configuration::get('YBC_BLOG_GALLERY_AUTO_PLAY') ? 1 : 0,
                'show_author' => (int)Configuration::get('YBC_BLOG_SHOW_POST_AUTHOR') ? 1 : 0,
                'date_format' => trim((string)Configuration::get('YBC_BLOG_DATE_FORMAT')),
                'blog_layout' => Tools::strtolower(Configuration::get('YBC_BLOG_LAYOUT')), 
                'blog_related_product_type' => Tools::strtolower(Configuration::get('YBC_RELATED_PRODUCTS_TYPE')),
                'blog_related_posts_type' => Tools::strtolower(Configuration::get('YBC_RELATED_POSTS_TYPE')),
                'blog_dir' => $this->blogDir,
                'image_folder' => _PS_YBC_BLOG_IMG_,
            )
      );          
      $this->context->smarty->assign(array('blog_config' => $assign));
  }
  public function loadMoreBlog($postData)
  {
        $this->context->smarty->assign(
            array(
                'blog_posts' => $postData['posts'],
                'blog_paggination' => $postData['paggination'],
                'blog_category' => $postData['category'],
                'blog_latest' => $postData['latest'],
                'blog_dir' => $postData['blogDir'],
                'blog_tag' => $postData['tag'],
                'blog_search' => $postData['search'],
                'is_main_page' => !$postData['category'] && !$postData['tag'] && !$postData['search'] && !Tools::isSubmit('latest') && !Tools::isSubmit('id_author') ? true : false,
                'allow_rating' => (int)Configuration::get('YBC_BLOG_ALLOW_RATING') ? true : false,
                'show_featured_post' => (int)Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK') ? true : false,
                'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                'show_date' => (int)Configuration::get('YBC_BLOG_SHOW_POST_DATE') ? true : false,
                'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                'path' => $this->getBreadCrumb(),
                'date_format' => trim((string)Configuration::get('YBC_BLOG_DATE_FORMAT')),
                'show_categories' => (int)Configuration::get('YBC_BLOG_SHOW_POST_CATEGORIES') ? true : false, 
                'blog_layout' => Tools::strtolower(Configuration::get('YBC_BLOG_LAYOUT')),   
                'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')),
                'author' => $postData['author'],     
                'breadcrumb' => $this->is17 ? $this->getBreadCrumb() : false,
                'loadajax'=>1,              
            )
        );
        $this->assignConfig();
        $list_blog = $this->context->smarty ->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/hook/blog_list.tpl');
        die(
            json_encode(
                array(
                     'list_blog'=> $list_blog,   
                     'blog_paggination'=>$postData['paggination'],            
                )
            )
        );
  }
  public function loadMoreAuhors($authors,$panigation)
  {
        $this->context->smarty->assign(
            array(
                'is_main_page' =>false,
                'allow_rating' => (int)Configuration::get('YBC_BLOG_ALLOW_RATING') ? true : false,
                'show_featured_post' => (int)Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK') ? true : false,
                'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                'show_date' => (int)Configuration::get('YBC_BLOG_SHOW_POST_DATE') ? true : false,
                'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                'path' => $this->getBreadCrumb(),
                'date_format' => trim((string)Configuration::get('YBC_BLOG_DATE_FORMAT')),
                'show_categories' => (int)Configuration::get('YBC_BLOG_SHOW_POST_CATEGORIES') ? true : false, 
                'blog_layout' => Tools::strtolower(Configuration::get('YBC_BLOG_LAYOUT')),   
                'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')), 
                'authors' => $authors,
                'blog_paggination' => $panigation,
                'breadcrumb' => $this->is17 ? $this->getBreadCrumb() : false, 
            )
       );
       die(
            json_encode(
                array(
                     'list_blog'=> $this->display(__FILE__,'authors_list.tpl'),   
                     'blog_paggination'=>$panigation,            
                )
            )
        ); 
  }
  public function loadMoreCategories($categoryData)
  {
    
        $this->context->smarty->assign(
            array(
                'blog_categories' => $categoryData['categories'],
                'blog_paggination' => $categoryData['paggination'],
                'path' => $this->getBreadCrumb(),
                'blog_layout' => Tools::strtolower(Configuration::get('YBC_BLOG_LAYOUT')),                 
                'breadcrumb' => $this->is17 ? $this->getBreadCrumb() : false,
                'show_date' => (int)Configuration::get('YBC_BLOG_SHOW_POST_DATE') ? true : false,
                'date_format' => trim((string)Configuration::get('YBC_BLOG_DATE_FORMAT')),
                'image_folder' => _PS_YBC_BLOG_IMG_.'category/',
            )
        );
        $this->assignConfig();
        die(
            json_encode(
                array(
                     'list_blog'=> $this->display(__FILE__,'categories_list.tpl'),   
                     'blog_paggination'=>$categoryData['paggination'],            
                )
            )
        );
  }
  public function loadMoreComments($posts,$paggination)
  {
        $this->context->smarty->assign(
            array(
                'posts' => $posts,
                'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false,
                'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                'sidebar_post_type' => Configuration::get('YBC_BLOG_SIDEBAR_POST_TYPE'),
                'date_format' => trim((string)Configuration::get('YBC_BLOG_DATE_FORMAT')),
                'comment_length' => (int)Configuration::get('YBC_BLOG_COMMENT_LENGTH') ? (int)Configuration::get('YBC_BLOG_COMMENT_LENGTH'):120,
                'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')), 
                'blog_layout' => Tools::strtolower(Configuration::get('YBC_BLOG_LAYOUT')),
                'comment_paggination' => $paggination->render(),          
                'show_date' => (int)Configuration::get('YBC_BLOG_SHOW_POST_DATE') ? true : false,
                'image_folder' => _PS_YBC_BLOG_IMG_.'avata/',
            )
        );
        $this->assignConfig();
        die(
            json_encode(
                array(
                     'list_blog'=> $this->display(__FILE__,'comment_list.tpl'),   
                     'blog_paggination'=> $paggination->render(),            
                )
            )
        );
  }
  public function hookBlogSearchBlock()
  {
        if(!Configuration::get('YBC_BLOG_SHOW_SEARCH_BLOCK'))
            return;
        $search = trim(Tools::getValue('search'));
        $this->smarty->assign(
            array(
                'action' => $this->getLink('blog'),
                'search' => Validate::isCleanHtml($search) ? urldecode($search):'',
                'id_lang' => $this->context->language->id
            )
        );
        if(($blog_search = trim(Tools::getValue('blog_search')))!='' && Validate::isCleanHtml($blog_search))
        {
            Tools::redirect($this->getLink('blog',array('search'=> urlencode($blog_search))));
        }
        return $this->display(__FILE__, 'search_block.tpl');
  }
  public function hookBlogRssSideBar()
  {
        $this->context->smarty->assign(
            array(
                'url_rss' => $this->getLink('rss'),
                'link_latest_posts' => $this->getLink('rss',array('latest_posts'=>1)),
                'link_popular_posts' => $this->getLink('rss',array('popular_posts'=>1)),
                'link_featured_posts' => $this->getLink('rss',array('featured_posts'=>1)),
            )
        );
        return $this->display(__FILE__,'rss_block.tpl');
  }
  public function hookBlogComments()
  {
        if(!Configuration::get('YBC_BLOG_SHOW_COMMENT_BLOCK'))
            return '';
        $limit = Configuration::get('YBC_BLOG_COMMENT_NUMBER') ? (int)Configuration::get('YBC_BLOG_COMMENT_NUMBER'):20;
        $posts = Ybc_blog_comment_class::getCommentsWithFilter(' AND bc.approved=1','bc.id_comment DESC,',0,$limit);
        if($posts)
        {
            foreach($posts as &$post)
            {
                $post['link'] = $this->getLink('blog',array('id_post' => $post['id_post']));
                if($post['thumb'])
                    $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                $post['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.$post['id_post'].' AND approved=1');
                $post['liked'] = $this->isLikedPost($post['id_post']);
                if(!$post['name'] && $post['id_user'] && ($customer = new Customer($post['id_user'])) && Validate::isLoadedObject($customer))
                    $post['name'] = $customer->firstname.' '.$customer->lastname;
                if($post['id_user'])
                {
                    if(($id = Ybc_blog_post_employee_class::getIdEmployeePostById($post['id_user'])) && ($postUer = new Ybc_blog_post_employee_class($id)) && Validate::isLoadedObject($postUer) && $postUer->avata)
                    {
                        $post['avata'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.$postUer->avata);
                    }
                    else
                       $post['avata'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.(Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT')? Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT') :'default_customer.png'));
                }
                else
                {
                    $post['avata'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.(Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT')? Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT') :'default_customer.png'));
                }
                $post['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($post['id_post'],false,true);
            }
        }
        $this->smarty->assign(
            array(
                'posts' => $posts,
                'all_comment_link' => $this->getLink('comment'),
                'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false,
                'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                'sidebar_post_type' => Configuration::get('YBC_BLOG_SIDEBAR_POST_TYPE'),
                'date_format' => trim((string)Configuration::get('YBC_BLOG_DATE_FORMAT')),
                'comment_length' => (int)Configuration::get('YBC_BLOG_COMMENT_LENGTH') ? (int)Configuration::get('YBC_BLOG_COMMENT_LENGTH'):120,
                'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')),
                'page' => 'comment_block',
            )
        );
        return $this->display(__FILE__,'comment_block.tpl');
  }
    public static function file_get_contents($url, $use_include_path = false, $stream_context = null, $curl_timeout = 60)
    {
        if ($stream_context == null && preg_match('/^https?:\/\//', $url)) {
            $stream_context = stream_context_create(array(
                "http" => array(
                    "timeout" => $curl_timeout,
                    "max_redirects" => 101,
                    "header" => 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36'
                ),
                "ssl"=>array(
                    "allow_self_signed"=>true,
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            ));
        }
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => html_entity_decode($url),
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => $curl_timeout,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_FOLLOWLOCATION => true,
            ));
            $content = curl_exec($curl);
            curl_close($curl);
            return $content;
        } elseif (in_array(ini_get('allow_url_fopen'), array('On', 'on', '1')) || !preg_match('/^https?:\/\//', $url)) {
            return Tools::file_get_contents($url, $use_include_path, $stream_context);
        } else {
            return false;
        }
    }
  public function hookBlogPositiveAuthor()
  {
        if(!Configuration::get('YBC_BLOG_SHOW_AUTHOR_BLOCK'))
            return '';
        $authors = Ybc_blog_post_class::getBlogPositiveAuthor();
        $this->context->smarty->assign(
            array(
                'authors'=>$authors,
                'author_link' => $this->getLink('author'),
                'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false,
                'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                'sidebar_post_type' => Configuration::get('YBC_BLOG_SIDEBAR_POST_TYPE'),
                'date_format' => trim((string)Configuration::get('YBC_BLOG_DATE_FORMAT')),
                'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')), 
                'page' => 'positive_author',
            )
        );
        return $this->display(__FILE__,'positive_author.tpl');
  }
  public function hookBlogCategoriesBlock()
  {       
        if(!Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK'))
            return;
        $id = (int)Tools::getValue('id_category');
        $module = Tools::getValue('module');
        if($id && $module==$this->name)
            $id_category = (int)$id;
        elseif(($category_url_alias = Tools::getValue('category_url_alias')) && Validate::isLinkRewrite($category_url_alias))
        {
            $id_category = (int)Ybc_blog_category_class::getIDCategoryByUrlAlias($category_url_alias);
        }
        elseif($id_post = (int)Tools::getValue('id_post'))
        {
            $post = new Ybc_blog_post_class($id_post);
            $id_category = $post->id_category_default;
        }
        elseif(($post_url_alias = Tools::getValue('post_url_alias')) && Validate::isLinkRewrite($post_url_alias))
        {
            $id_post = (int)Ybc_blog_post_class::getIDPostByUrlAlias($post_url_alias);
            if($id_post)
            {
                $post = new Ybc_blog_post_class($id_post);
                $id_category = $post->id_category_default; 
            }
            else
                $id_category=0;
        }
        else    
            $id_category=0;
        $this->smarty->assign(
            array(
                'active' => $id_category,
                'link_view_all'=> $this->getLink('category'),
            )
        );
        $blockCategTree = Ybc_blog_category_class::getBlogCategoriesTree(0);
        $this->context->smarty->assign('blockCategTree', $blockCategTree);
        $this->smarty->assign('branche_tpl_path', _PS_MODULE_DIR_.'ybc_blog/views/templates/hook/category-tree-branch.tpl');
        return $this->display(__FILE__, 'categories_block.tpl');
  }
  public function displayBlogCategoriesSub($id_category) {
        $this->smarty->assign(
            array(
                'active' => (int)Tools::getValue('id_category'),
            )
        );
        $blockCategTree = Ybc_blog_category_class::getBlogCategoriesTree($id_category);
        $this->context->smarty->assign('blockCategTree', $blockCategTree);
        $this->smarty->assign('branche_tpl_path', _PS_MODULE_DIR_.'ybc_blog/views/templates/hook/category-tree-branch.tpl');
        return $this->display(__FILE__, 'categories_block.tpl');
  }
  public function hookBlogRssCategory()
  {
        $blockCategTree = Ybc_blog_category_class::getBlogCategoriesTree(0);
        $this->context->smarty->assign('blockCategTree', $blockCategTree);
        $this->smarty->assign('branche_tpl_path', _PS_MODULE_DIR_.'ybc_blog/views/templates/hook/rss-category-tree-branch.tpl');
        return $this->display(__FILE__, 'rss_categories_block.tpl');
  }
  public function hookBlogRssAuthor()
  {
        $this->context->smarty->assign(
            Ybc_blog_post_class::getBlogRssAuthor()
        );
        return $this->display(__FILE__,'rss_author_block.tpl');
        
  }
  public function hookBlogTagsBlock()
  {
        if(!Configuration::get('YBC_BLOG_SHOW_TAGS_BLOCK'))
            return;
        $tags = Ybc_blog_post_class::getTags((int)Configuration::get('YBC_BLOG_TAGS_NUMBER') > 0 ? (int)Configuration::get('YBC_BLOG_TAGS_NUMBER') : 20);
        if(is_array($tags) && $tags)
            shuffle($tags);
        $this->smarty->assign(
            array(
                'tags' => $tags
            )
        );
        return $this->display(__FILE__, 'tags_block.tpl');
  }
  public function hookBlogNewsBlock($params)
  {           
        if(isset($params['page']) && $params['page']=='home')
        {
            if(!Configuration::get('YBC_BLOG_SHOW_LATEST_BLOCK_HOME'))
                return '';
            $postCount = (int)Configuration::get('YBC_BLOG_LATEST_POST_NUMBER_HOME') > 0 ? (int)Configuration::get('YBC_BLOG_LATEST_POST_NUMBER_HOME') : 5;
            $this->context->smarty->assign(
                array(
                    'display_desc' => Configuration::get('YBC_BLOG_HOME_DISPLAY_DESC'),
                )
            );
        }
        else
        {
            if(!Configuration::get('YBC_BLOG_SHOW_LATEST_NEWS_BLOCK'))
                return '';
            $this->context->smarty->assign(
                array(
                    'display_desc' => Configuration::get('YBC_BLOG_SIDEBAR_DISPLAY_DESC'),
                )
            );
            $postCount = (int)Configuration::get('YBC_BLOG_LATES_POST_NUMBER') > 0 ? (int)Configuration::get('YBC_BLOG_LATES_POST_NUMBER') : 5;
        }  
        $posts = Ybc_blog_post_class::getPostsWithFilter(' AND p.enabled=1','p.datetime_active DESC,',0,$postCount);
        if($posts)
        {
            foreach($posts as $key => &$post)
            {
                $post['link'] = $this->getLink('blog',array('id_post' => $post['id_post']));
                if($post['thumb'])
                    $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                $post['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.$post['id_post'].' AND approved=1');
                $post['liked'] = $this->isLikedPost($post['id_post']);
                $post['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($post['id_post'],false,true);
                
            }
            unset($key); 
        }                           
        $this->smarty->assign(
            array(
                'posts' => $posts,
                'latest_link' => $this->getLink('blog',array('latest' => 'true')),
                'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false,
                'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                'sidebar_post_type' => Configuration::get('YBC_BLOG_SIDEBAR_POST_TYPE'),
                'date_format' => trim((string)Configuration::get('YBC_BLOG_DATE_FORMAT')),
                'hook' => 'homeblog',
                'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')), 
                'page' => isset($params['page']) && $params['page'] ? $params['page'] : false,
            )
        );
        return $this->display(__FILE__, 'latest_posts_block.tpl');
  }
  public function hookDisplayHome()
  { 
        $homepages=array(
            'homepage_new'=>Configuration::get('YBC_BLOG_SHOW_LATEST_BLOCK_HOME') ? $this->hookBlogNewsBlock(array('page'=>'home')):'',
            'homepage_popular' => Configuration::get('YBC_BLOG_SHOW_POPULAR_BLOCK_HOME') ? $this->hookBlogPopularPostsBlock(array('page'=>'home')):'',
            'homepage_featured' => Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK_HOME') ? $this->hookBlogFeaturedPostsBlock(array('page'=>'home')):'',
            'homepage_categories' => Configuration::get('YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME') ? $this->hookBlogCategoryBlock(array('page'=>'home')):'',
            'homepage_gallery' => Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK_HOME') ? $this->hookBlogGalleryBlock(array('page'=>'home')):'',
        );
        $position_homepages= explode(',',Configuration::get('YBC_BLOG_POSITION_HOMEPAGE') ? Configuration::get('YBC_BLOG_POSITION_HOMEPAGE'):'homepage_new,homepage_popular,homepage_featured,homepage_categories,homepage_gallery');
        $this->context->smarty->assign(
            array(
                'position_homepages' => $position_homepages,
                'homepages'=>$homepages
            )
        );
        return $this->display(__FILE__, 'home_blocks.tpl');
  }
  public function getWidgetVariables($hookName, array $configuration = [])
    {
        $homepages=array(
            'homepage_new'=>Configuration::get('YBC_BLOG_SHOW_LATEST_BLOCK_HOME') ? $this->hookBlogNewsBlock(array('page'=>'home')):'',
            'homepage_popular' => Configuration::get('YBC_BLOG_SHOW_POPULAR_BLOCK_HOME') ? $this->hookBlogPopularPostsBlock(array('page'=>'home')):'',
            'homepage_featured' => Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK_HOME') ? $this->hookBlogFeaturedPostsBlock(array('page'=>'home')):'',
            'homepage_categories' => Configuration::get('YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME') ? $this->hookBlogCategoryBlock(array('page'=>'home')):'',
            'homepage_gallery' => Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK_HOME') ? $this->hookBlogGalleryBlock(array('page'=>'home')):'',
        );
        $position_homepages= explode(',',Configuration::get('YBC_BLOG_POSITION_HOMEPAGE') ? Configuration::get('YBC_BLOG_POSITION_HOMEPAGE'):'homepage_new,homepage_popular,homepage_featured,homepage_categories,homepage_gallery');
        $this->context->smarty->assign(
            array(
                'position_homepages' => $position_homepages,
                'homepages'=>$homepages
            )
        );
        unset($hookName);
        unset($configuration);
        return $this->display(__FILE__, 'home_blocks.tpl');
    }
    
    public function renderWidget($hookName, array $configuration = [])
    {
        if ($hookName == null && isset($configuration['hook'])) {
            $hookName = $configuration['hook'];
        }

        if (preg_match('/^displayNav\d*$/', $hookName)) {
            $template_file = $this->templates['light'];
        } elseif ($hookName == 'displayLeftColumn') {
            $template_file = $this->templates['rich'];
        } else {
            $template_file = $this->templates['default'];
        }

        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));

        return $this->fetch('module:'.$this->name.'/'.$template_file);
    }
  public function hookBlogPopularPostsBlock($params)
  {
        if(isset($params['page']) && $params['page']=='home')
        {
            $postCount = (int)Configuration::get('YBC_BLOG_POPULAR_POST_NUMBER_HOME') > 0 ? (int)Configuration::get('YBC_BLOG_POPULAR_POST_NUMBER_HOME') : 5;
            $this->context->smarty->assign(
                array(
                    'display_desc' => Configuration::get('YBC_BLOG_HOME_DISPLAY_DESC'),
                )
            );
        }
        else
        {
            $this->context->smarty->assign(
                array(
                    'display_desc' => Configuration::get('YBC_BLOG_SIDEBAR_DISPLAY_DESC'),
                )
            );
            $postCount = (int)Configuration::get('YBC_BLOG_PUPULAR_POST_NUMBER') > 0 ? (int)Configuration::get('YBC_BLOG_PUPULAR_POST_NUMBER') : 5;
        }
                                    
        $posts = Ybc_blog_post_class::getPostsWithFilter(' AND p.enabled=1','p.click_number desc,',0,$postCount);
        if($posts)
            foreach($posts as &$post)
            {
                $post['link'] = $this->getLink('blog',array('id_post' => $post['id_post']));
                if($post['thumb'])
                    $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                $post['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.$post['id_post'].' AND approved=1');
                $post['liked'] = $this->isLikedPost($post['id_post']);
                $post['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($post['id_post'],false,true);
            }
        $this->smarty->assign(
            array(
                'posts' => $posts,
                'popular_link' => $this->getLink('blog',array('popular' => 'true')),
                'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false,
                'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                'sidebar_post_type' => Configuration::get('YBC_BLOG_SIDEBAR_POST_TYPE'),
                'date_format' => trim((string)Configuration::get('YBC_BLOG_DATE_FORMAT')),
                'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')), 
                'page' => isset($params['page']) && $params['page'] ? $params['page'] : false,
            )
        );
        return $this->display(__FILE__, 'popular_posts_block.tpl');
  }
  public function hookBlogFeaturedPostsBlock($params)
  {
        if(isset($params['page']) && $params['page']=='home')
        {
            $this->context->smarty->assign(
                array(
                    'display_desc' => Configuration::get('YBC_BLOG_HOME_DISPLAY_DESC'),
                )
            );
            $postCount = (int)Configuration::get('YBC_BLOG_FEATURED_POST_NUMBER_HOME') > 0 ? (int)Configuration::get('YBC_BLOG_FEATURED_POST_NUMBER_HOME') : 5;
        }    
        else
        {
            $this->context->smarty->assign(
                array(
                    'display_desc' => Configuration::get('YBC_BLOG_SIDEBAR_DISPLAY_DESC'),
                )
            );
            $postCount = (int)Configuration::get('YBC_BLOG_FEATURED_POST_NUMBER') > 0 ? (int)Configuration::get('YBC_BLOG_FEATURED_POST_NUMBER') : 5;
        }                 
        $posts = Ybc_blog_post_class::getPostsWithFilter(' AND p.enabled=1 && p.is_featured=1',$this->sort,0,$postCount);
        if($posts)
            foreach($posts as &$post)
            {
                $post['link'] = $this->getLink('blog',array('id_post' => $post['id_post']));
                if($post['thumb'])
                    $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                $post['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.$post['id_post'].' AND approved=1');
                $post['liked'] = $this->isLikedPost($post['id_post']);
                $post['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($post['id_post'],false,true);
            }
        $this->smarty->assign(
            array(
                'posts' => $posts,
                'featured_link' => $this->getLink('blog',array('featured' => 'true')),
                'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false,
                'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                'sidebar_post_type' => Configuration::get('YBC_BLOG_SIDEBAR_POST_TYPE'),
                'date_format' => trim((string)Configuration::get('YBC_BLOG_DATE_FORMAT')),
                'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')), 
                'page' => isset($params['page']) && $params['page'] ? $params['page'] : false,
            )
        );
        return $this->display(__FILE__, 'featured_posts_block.tpl');
  }
  public function hookBlogSlidersBlock()
  {
        if(!Configuration::get('YBC_BLOG_SHOW_SLIDER'))
            return;
        $slides = Ybc_blog_slide_class::getSlidesWithFilter(' AND s.enabled=1','s.sort_order asc, s.id_slide asc,');
        if($slides)
            foreach($slides as &$slide)
            {
                if($slide['image'])
                    $slide['image'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'slide/'.$slide['image']);
            }
        $this->smarty->assign(
            array(
                'loading_img' => $this->_path.'views/img/img/loading.gif',
                'slides' => $slides,
                'nivoTheme' => 'default',
                'nivoAutoPlay' => (int)Configuration::get('YBC_BLOG_SLIDER_AUTO_PLAY') ? true : false,
            )
        );
        return $this->display(__FILE__, 'slider_block.tpl');
  }
  public function hookBlogGalleryBlock($params)
  {                
        if(isset($params['page']) && $params['page']=='home')
        {
            if(!Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK_HOME'))
                return '';
            $postCount = (int)Configuration::get('YBC_BLOG_GALLERY_POST_NUMBER_HOME') > 0 ? (int)Configuration::get('YBC_BLOG_GALLERY_POST_NUMBER_HOME') : 10;
        }    
        else
        {
            if(!Configuration::get('YBC_BLOG_SHOW_GALLERY_BLOCK'))
                return '';
            $postCount = (int)Configuration::get('YBC_BLOG_GALLERY_POST_NUMBER') > 0 ? (int)Configuration::get('YBC_BLOG_GALLERY_POST_NUMBER') : 10;
        }
        $galleries = Ybc_blog_gallery_class::getGalleriesWithFilter(' AND g.enabled=1  AND g.is_featured=1','g.sort_order asc, g.id_gallery asc,',0,$postCount);
        if($galleries)
            foreach($galleries as &$gallery)
            {
                if($gallery['thumb'])
                    $gallery['thumb'] =  $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'gallery/thumb/'.$gallery['thumb']);   
                else
                     $gallery['thumb']= $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'gallery/'.$gallery['image']); 
                if($gallery['image'])
                {                       
                    $gallery['image'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'gallery/'.$gallery['image']);    
                }  
                      
            }      
        $this->smarty->assign(
            array(
                'galleries' => $galleries,
                'gallery_link' => $this->getLink('gallery',array()),                    
                'page' => isset($params['page']) && $params['page'] ? $params['page'] : false,
            )
        );
        return $this->display(__FILE__, 'gallery_block.tpl');
  }
  /**
    * polls
  */
    private function _postPolls()
    {
        /**
        * Change status 
        */
        if(Tools::isSubmit('change_enabled'))
        {
            $status = (int)Tools::getValue('change_enabled') ?  1 : 0;
            $field = Tools::getValue('field');
            $id_polls = (int)Tools::getValue('id_polls');   
            $polls_class = new Ybc_blog_polls_class($id_polls);   
            Hook::exec('actionUpdateBlog', array(
                'id_post' =>(int)$polls_class->id_post,
            ));	      
            if($id_polls && property_exists('Ybc_blog_polls_class', $field))
            {
                Ybc_blog_defines::changeStatus('polls',$field,$id_polls,$status);
                if($status==1)
                    $title = $this->l('Click to mark this as unhelpful');
                else
                    $title = $this->l('Click to mark this as helpful');
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(array(
                        'listId' => $id_polls,
                        'enabled' => $status,
                        'field' => $field,
                        'message' =>  $this->displaySuccessMessage($this->l('The status has been successfully updated')),
                        'messageType'=>'success',
                        'title'=>$title,
                        'href' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=polls&change_enabled='.($status ? '0' : '1').'&field='.$field.'&id_polls='.$id_polls,
                    )));
                }
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=polls&list=true');
            }
        }            
        /**
        * Delete comment 
        */ 
        if(Tools::isSubmit('del'))
        {
            $id_polls = (int)Tools::getValue('id_polls');
            if(($polls_class = new Ybc_blog_polls_class($id_polls)) && Validate::isLoadedObject($polls_class))
            {
                Hook::exec('actionUpdateBlog', array(
                    'id_post' =>(int)$polls_class->id_post,
                ));	 
                if($polls_class->delete())
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=2&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=polls&list=true');
            }                   
        }                  
        /**
        * form send mail
        */
        if(Tools::isSubmit('sendmailform') && ($id_polls= (int)Tools::getValue('id_polls')))
        {
            $polls_class = new Ybc_blog_polls_class($id_polls);
            $this->context->smarty->assign(
                array(
                    'polls_class' => $polls_class,
                    
                )
            );
            if(Tools::isSubmit('ajax'))
            {
                die(
                    json_encode(
                        array(
                            'html_form' => $this->display(__FILE__,'form_send_mail_polls.tpl'),
                        )
                    )
                );   
            }
            return $this->display(__FILE__,'form_send_mail_polls.tpl');
        }
        if(Tools::isSubmit('send_mail_polls') && ($id_polls=(int)Tools::getValue('id_polls')))
        {
            $errors=array();
            if(($message_email = trim(Tools::getValue('message_email')))=='')
            {
                $errors[] = $this->l('Message is required');
            }
            elseif($message_email && !Validate::isCleanHtml($message_email))
                $errors[] = $this->l('Message is not valid');
            if(($subject = trim(Tools::getValue('subject_email')))=='')
                $errors[]=$this->l('Subject is required');
            elseif($subject && !Validate::isCleanHtml($subject))
                $errors[]=$this->l('Subject is not valid');
            if(!$errors)
            {
                $polls_class = new Ybc_blog_polls_class($id_polls);
                $template_customer_vars=array(
                    '{message_email}'  => $message_email,
                    '{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
                    '{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')
                );
                Mail::Send(
        			Context::getContext()->language->id,
        			'reply_polls_customer',
        			$subject,
        			$template_customer_vars,
    		        $polls_class->email,
        			$polls_class->name,
        			null,
        			null,
        			null,
        			null,
        			dirname(__FILE__).'/mails/'
                );
                die(json_encode(
                    array(
                        'message' =>$this->displaySuccessMessage($this->l('Email was sent successfully')),
                        'messageType'=>'success'
                    )
                ));
            }
            else
            {
                die(json_encode(
                    array(
                        'message' =>$this->displayError($errors),
                        'messageType'=>'error'
                    )
                ));
            }
            
        }
    }
   
  /**
   * Comments 
   */
  private function _postComment()
  {
        $errors = array();
        $id_comment = (int)Tools::getValue('id_comment');
        $list = Tools::getValue('list');
        if($list!='true' && ($id_comment && !Validate::isLoadedObject(new Ybc_blog_comment_class($id_comment)) || !$id_comment))
            Tools::redirectAdmin($this->baseAdminPath);
        if(Tools::getValue('submitBulkActionMessage') && ($message_readed =  Tools::getValue('message_readed')) && Ybc_blog::validateArray($message_readed) && $bulk_action_message = Tools::getValue('bulk_action_message'))
        {
            if($bulk_action_message=='delete_selected')
            {
                foreach($message_readed as $id_comment => $value)
                {
                    if($value)
                    {
                        Hook::exec('actionUpdateBlog', array(
                            'id_comment' => (int)$id_comment,
                        ));
                        if(($comment = new Ybc_blog_comment_class($id_comment)) &&  Validate::isLoadedObject($comment))
                            $comment->delete();
                    }
                }
                die(json_encode(
                    array(
                        'url_reload' => $this->context->link->getAdminLink('AdminModules', true).'&conf=2&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=comment&list=true',
                    )
                ));
            }
            else
            {
                if($bulk_action_message=='mark_as_approved')
                {
                    $value_field=1;
                    $field='approved';
                }
                elseif($bulk_action_message=='mark_as_unapproved')
                {
                    $value_field=0;
                    $field='approved';
                }
                elseif($bulk_action_message=='mark_as_read')
                {
                    $value_field=1;
                    $field='viewed';
                }
                else
                {
                    $value_field=0;
                    $field='viewed';
                }
                foreach($message_readed as $id_comment => $value)
                {
                    if($value)
                    {
                        $commentObj = new Ybc_blog_comment_class(($id_comment));
                        if(Validate::isLoadedObject($commentObj))
                        {
                            Hook::exec('actionUpdateBlog', array(
                                'id_comment' => (int)$id_comment,
                            ));

                            $commentObj->{$field} = $value_field;
                            $commentObj->update();
                        }
                    }
                }
                die(json_encode(
                    array(
                        'url_reload' => $this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=comment&list=true',
                    )
                ));
            }
        }
        /**
         * Change status 
         */
         if(Tools::isSubmit('change_enabled'))
         {
            $status = (int)Tools::getValue('change_enabled') ?  1 : 0;
            $field = Tools::getValue('field');
            $id_comment = (int)Tools::getValue('id_comment');   
            $comment = new Ybc_blog_comment_class($id_comment);       
            $post= new Ybc_blog_post_class($comment->id_post);  
            Hook::exec('actionUpdateBlog', array(
                'id_post' => (int)$comment->id_post,
            )); 
            if($field == 'approved' || $field == 'reported' && $id_comment)
            {
                Ybc_blog_defines::changeStatus('comment',$field,$id_comment,$status);
                if($comment->email && Validate::isEmail($comment->email) && ($id_customer = Customer::customerExists($comment->email,true)) && ($customer = new Customer($id_customer)) && Validate::isLoadedObject($customer))
                    $idLang = $customer->id_lang;
                else
                    $idLang = $this->context->language->id;
                if($field=='approved' && $status==1 && ($subject = Ybc_blog_email_template_class::getSubjectByTemplate('approved_comment',$idLang)))
                {
                   Mail::Send(
                        $idLang, 
                        'approved_comment',
                        $subject,
                        array('{customer_name}' => $comment->name, '{email}' => $comment->email,'{rating}' => ' '.($comment->rating != 1 ? $this->l('stars','blog') : $this->l('star','blog')), '{subject}' => $comment->subject, '{comment}'=>$comment->comment,'{post_title}'=>$post->title[$this->context->language->id],'{post_link}' => $this->getLink('blog', array('id_post' => $comment->id_post)),'{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),'{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')),  
                        $comment->email, null, null, null, null, null, 
                        dirname(__FILE__).'/mails/', 
                        false, $this->context->shop->id
                    ); 
                }
                if($field=='approved')
                {
                    if($status==1)
                        $title = $this->l('Click to mark as unapproved');
                    else
                        $title = $this->l('Click to mark as approved');
                }
                else
                {
                    if($status==1)
                        $title = $this->l('Click to mark as unreported');
                    else
                        $title = $this->l('Click to mark as reported');
                }
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(array(
                        'listId' => $id_comment,
                        'enabled' => $status,
                        'field' => $field,
                        'message' => $field == 'approved' ? $this->displaySuccessMessage($this->l('The status has been successfully updated')):$this->displaySuccessMessage($this->l('The status has been successfully updated')),
                        'messageType'=>'success',
                        'title'=>$title,
                        'href' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=comment&change_enabled='.($status ? '0' : '1').'&field='.$field.'&id_comment='.$id_comment,
                    )));
                }
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=comment&list=true');
            }
         }            
        /**
         * Delete comment 
         */ 
         if(Tools::isSubmit('del'))
         {
            $id_comment = (int)Tools::getValue('id_comment');
            Hook::exec('actionUpdateBlog', array(
                'id_comment' => (int)$id_comment,
            ));
            if(!(($comment = new Ybc_blog_comment_class($id_comment)) &&  Validate::isLoadedObject($comment)))
                $errors[] = $this->l('Comment does not exist');
            elseif($comment->delete())
            {                
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=2&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=comment&list=true');
            }                
            else
                $errors[] = $this->l('Could not delete the comment. Please try again');    
         }  
         if(Tools::isSubmit('approve'))
         {
            $id_comment = (int)Tools::getValue('id_comment');
            Hook::exec('actionUpdateBlog', array(
                'id_comment' => (int)$id_comment,
            ));
            if(!(($comment = new Ybc_blog_comment_class($id_comment)) && Validate::isLoadedObject($comment)))
                $errors[] = $this->l('Comment does not exist');
            else
            {
                $comment->approved =1;
                if($comment->update())
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=comment&list=true');
                else
                    $errors[] = $this->l('Could not approve the comment. Please try again');  
            }                 
         }                
        /**
         * Save comment 
         */
        if(Tools::isSubmit('saveComment'))
        {
            if($id_comment && ($comment = new Ybc_blog_comment_class($id_comment)) && Validate::isLoadedObject($comment))
            {
                $post= new Ybc_blog_post_class($comment->id_post);   
                Hook::exec('actionUpdateBlog', array(
                    'id_post' => (int)$comment->id_post,
                ));
                $rating = (int)Tools::getValue('rating');
                $approved = $comment->approved;
                $comment->subject = trim(Tools::getValue('subject',''));
                $comment->comment = trim(Tools::getValue('comment',''));
                $comment->reply = trim(Tools::getValue('reply',''));
                $comment->rating = $rating >=0 && $rating <=5 ? $rating : 0;
                $comment->approved = (int)trim(Tools::getValue('approved',1)) ? 1 : 0;
                $comment->reported = (int)trim(Tools::getValue('reported',0)) ? 1 : 0;
                $comment->replied_by = (int)$this->context->employee->id;
                if(Tools::strlen($comment->subject) < 10)
                    $errors[] = $this->l('Subject needs to be at least 10 characters');
                if(Tools::strlen($comment->subject) >300)
                    $errors[] = $this->l('Subject cannot be longer than 300 characters');
                if(!Validate::isCleanHtml($comment->subject,false))
                    $errors[] = $this->l('Subject needs to be clean HTML');
                if(Tools::strlen($comment->comment) < 20)
                    $errors[] = $this->l('Comment needs to be at least 20 characters');
                if(!Validate::isCleanHtml($comment->comment,false))
                    $errors[] = $this->l('Comment needs to be clean HTML');
                if(Tools::strlen($comment->comment) >2000)
                    $errors[] = $this->l('Comment cannot be longer than 2000 characters');

                if(!Validate::isCleanHtml($comment->reply,false))
                    $errors[] = $this->l('Reply needs to be clean HTML');
                if(Tools::strlen($comment->reply) >2000)
                    $errors[] = $this->l('Reply cannot be longer than 2000 characters');
                if(!$errors)
                {
                    if(!$comment->update())
                    {
                        $errors[] = $this->displayError($this->l('The comment could not be updated.'));
                    }
                    else
                    {
                        if($comment->email && Validate::isEmail($comment->email) && ($id_customer = Customer::customerExists($comment->email)) && ($customer = new Customer($id_customer)) && Validate::isLoadedObject($customer))
                            $id_lang = $customer->id_lang;
                        else
                            $id_lang = $this->context->language->id;
                        if($approved!=$comment->$approved && $comment->approved==1 && ($subject = Ybc_blog_email_template_class::getInstance()->getSubjects('approved_comment',$id_lang)))
                        {
                            Mail::Send(
                                $id_lang,
                                'approved_comment',
                                $subject,
                                array('{customer_name}' => $comment->name, '{email}' => $comment->email,'{rating}' => ' '.($comment->rating != 1 ? $this->l('stars','blog') : $this->l('star','blog')), '{subject}' => $comment->subject, '{comment}'=>$comment->comment,'{post_title}'=>$post->title[$this->context->language->id],'{post_link}' => $this->getLink('blog', array('id_post' => $comment->id_post)),'{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),'{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')),
                                $comment->email, null, null, null, null, null,
                                dirname(__FILE__).'/mails/',
                                false, $this->context->shop->id
                            );
                        }
                    }
                }
            }
            else
            {
                $errors[] = $this->l('Comment does not exist');
            }
         }
         if(Tools::isSubmit('ajax'))
         {
            die(json_encode(
                array(
                    'messageType' => $errors ? 'error' : 'success',
                    'message' => $errors ? $this->displayError($errors) : $this->displayConfirmation($this->l('Comment saved')),
                )
            ));
         }
         if (count($errors))
         {                
            $this->errorMessage = $this->displayError($errors);  
         }
         elseif (Tools::isSubmit('saveComment') && $id_comment)
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_comment='.$id_comment.'&control=comment');
		 elseif (Tools::isSubmit('saveComment'))
         {
            Tools::redirectAdmin($this->baseAdminPath);
         }
   }
   public function renderPollsForm()
   {
        //List 
        $list = trim(Tools::getValue('list'));
        if($list=='true')
        {
            $fields_list = array(
                'id_polls' => array(
                    'title' => $this->l('Vote ID'),
                    'width' => 40,
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true,
                ),
                'name' => array(
                    'title' => $this->l('Name'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true
                ),
                'email' => array(
                    'title' => $this->l('Email'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true
                ),
                'title'=>array(
                    'title'=>$this->l('Blog post'),
                    'type' => 'text',
                    'filter' => true,  
                    'strip_tag'=>false,
                ),
                'feedback'=>array(
                    'title'=>$this->l('Feedback'),
                    'type' => 'text',
                    'filter' => true,
                ),
                'polls' => array(
                    'title' => $this->l('Helpful'),
                    'type' => 'active',
                    'sort' => true,
                    'filter' => true,
                    'strip_tag' => false,
                    'filter_list' => array(
                        'id_option' => 'enabled',
                        'value' => 'title',
                        'list' => array(
                            0 => array(
                                'enabled' => 1,
                                'title' => $this->l('Yes')
                            ),
                            1 => array(
                                'enabled' => 0,
                                'title' => $this->l('No')
                            )
                        )
                    )
                )
            );
            //Filter
            $filter = "";
            $show_reset = false;
            if(($id_polls = trim(Tools::getValue('id_polls')))!='' && Validate::isCleanHtml($id_polls))
            {
                $filter .= " AND po.id_polls = ".(int)$id_polls;
                $show_reset = true;
            }
            if(($feedback = trim(Tools::getValue('feedback')))!='' && Validate::isCleanHtml($feedback))
            {
                $filter .= " AND po.feedback like '%".pSQL($feedback)."%'";
                $show_reset = true;
            }             
            if(($name = trim(Tools::getValue('name')))!='' && Validate::isCleanHtml($name))
            {
                $filter .= " AND po.name like '%".pSQL($name)."%'";
                $show_reset = true;
            }
            if(($polls = trim(Tools::getValue('polls')))!='' && Validate::isCleanHtml($polls))
            {
                $filter .= " AND po.polls = ".(int)$polls;
                $show_reset = true;
            }
            if(($title = trim(Tools::getValue('title')))!='' && Validate::isCleanHtml($title))
            {
                $filter .= " AND pl.title like '%".pSQL($title)."%'";
                $show_reset = true;
            }
            if(($email = trim(Tools::getValue('email')))!='' && Validate::isCleanHtml($email))
            {
                $show_reset = true;
                $filter .= " AND po.email like '%".pSQL($email)."%'";
            }
            //Sort
            $sort_post = Tools::strtolower(Tools::getValue('sort'));
            $sort_type = Tools::strtolower(Tools::getValue('sort_type','desc'));
            if($sort_post && isset($fields_list[$sort_post]))
            {
                $sort = $sort_post." ".($sort_type=='asc' ? ' ASC ' :' DESC ')." , ";
            }
            else
                $sort = 'po.id_polls DESC,';
            //Paggination
            $page = (int)Tools::getValue('page');
            if($page <1)
                $page =1;
            $totalRecords = (int)Ybc_blog_polls_class::countPollsWithFilter($filter,false);
            $paggination = new Ybc_blog_paggination_class();            
            $paggination->total = $totalRecords;
            $paggination->url = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=polls&list=true&page=_page_'.$this->getUrlExtra($fields_list);
            $paggination->limit =  (int)Tools::getValue('paginator_ybc_polls_select_limit',20);
            $paggination->name ='ybc_polls';
            $totalPages = ceil($totalRecords / $paggination->limit);
            if($page > $totalPages)
                $page = $totalPages;
            $paggination->page = $page;
            $start = $paggination->limit * ($page - 1);
            if($start < 0)
                $start = 0;
            $polls = Ybc_blog_polls_class::getPollsWithFilter($filter, $sort, $start, $paggination->limit,false);
            if($polls)
            {
                foreach($polls as &$poll)
                {
                    $poll['title'] = '<a target="_blank" href="'.$this->getLink('blog',array('id_post'=>$poll['id_post'])).'" title="'.$poll['title'].'">'.$poll['title'].'</a>';
                    if($poll['id_user'])
                    {
                        if(version_compare(_PS_VERSION_, '1.7.6', '>='))
                        {
                            $sfContainer = call_user_func(array('\PrestaShop\PrestaShop\Adapter\SymfonyContainer','getInstance'));
                        	if (null !== $sfContainer) {
                        		$sfRouter = $sfContainer->get('router');
                        		$link_customer= $sfRouter->generate(
                        			'admin_customers_view',
                        			array('customerId' => $poll['id_user'])
                        		);
                        	}
                            else
                                $link_customer = $this->context->link->getAdminLink('AdminCustomers').'&id_customer='.(int)$poll['id_user'].'&viewcustomer';
                        }
                        else
                            $link_customer = $this->context->link->getAdminLink('AdminCustomers').'&id_customer='.(int)$poll['id_user'].'&viewcustomer';
                        $poll['link_customer'] = $link_customer;                    
                    }                    
                }
            }
            $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
            $paggination->style_links = $this->l('links');
            $paggination->style_results = $this->l('results');
            $listData = array(
                'name' => 'ybc_polls',
                'actions' => array('edit', 'delete', 'view'),
                'currentIndex' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=polls'.($paggination->limit!=20 ? '&paginator_ybc_polls_select_limit='.$paggination->limit:''),
                'identifier' => 'id_polls',
                'show_toolbar' => true,
                'show_action' => true,
                'title' => $this->l('Polls'),
                'fields_list' => $fields_list,
                'field_values' => $polls,
                'paggination' => $paggination->render(),
                'filter_params' => $this->getFilterParams($fields_list),
                'show_reset' => $show_reset,
                'totalRecords' => $totalRecords,
                'show_add_new' => false,
                'sort'=> $sort_post ? :'id_polls',
                'sort_type'=> $sort_type,
            );            
            return $this->_html .= $this->renderList($listData);      
        }
        //Form
        $id_comment = (int)Tools::getValue('id_comment');
        $fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Manage polls'),				
				),
				'input' => array(					
					array(
						'type' => 'text',
						'label' => $this->l('Subject'),
						'name' => 'subject',    					 
                        'required' => true,
                        'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}',
                        'desc' => $id_comment && ($comment = Ybc_blog_comment_class::getCommentById($id_comment)) ? $this->displayCommentInfo($comment,(int)$comment['id_user'],$this->getLink('blog',array('id_post' => (int)$comment['id_post']))) : '',
					), 
                    array(
    					'type' => 'select',
    					'label' => $this->l('Rating'),
    					'name' => 'rating',
                        'options' => array(
                			 'query' => array(                                
                                    array(
                                        'id_option' => '0', 
                                        'name' => $this->l('No ratings')
                                    ),
                                    array(
                                        'id_option' => '1', 
                                        'name' => '1 '. $this->l('rating')
                                    ),
                                    array(
                                        'id_option' => '2', 
                                        'name' => '2 '. $this->l('ratings')
                                    ),
                                    array(
                                        'id_option' => '3', 
                                        'name' => '3 '. $this->l('ratings')
                                    ),
                                    array(
                                        'id_option' => '4', 
                                        'name' => '4 '. $this->l('ratings')
                                    ),
                                    array(
                                        'id_option' => '5', 
                                        'name' => '5 '. $this->l('ratings')
                                    )
                                ),                             
                             'id' => 'id_option',
                			 'name' => 'name'  
                        )                
    				),
                    array(
						'type' => 'textarea',
						'label' => $this->l('Comment'),
						'name' => 'comment',                            
                        'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}',
                        'required' => true						
					),
                    array(
						'type' => 'switch',
						'label' => $this->l('Approved'),
						'name' => 'approved',
                        'is_bool' => true,
                        'form_group_class' => 'text-center',
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						)					
					),
                    array(
						'type' => 'switch',
						'label' => $this->l('Not reported as abused'),
						'name' => 'reported',
                        'is_bool' => true,
                        'form_group_class' => 'text-center',
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						)					
					),
                    array(
                        'type' => 'hidden', 
                        'name' => 'control'
                    )
                ),
                'submit' => array(
					'title' => $this->l('Save'),
				)
            ),
		);
        $helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'saveComment';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
			'fields_value' => $this->getFieldsValues(Ybc_blog_defines::$commentFields,'id_comment','Ybc_blog_comment_class','saveComment'),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'image_baseurl' => _PS_YBC_BLOG_IMG_,
            'link' => $this->context->link,
            'cancel_url' => $this->baseAdminPath.'&control=comment&list=true'
		);            
        if(Tools::isSubmit('id_comment') && (int)$id_comment)
        {
            
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_comment');                
        }
		$helper->override_folder = '/'; 
        $this->_html .= $helper->generateForm(array($fields_form));			
    }
   public function renderCommentsForm()
   {
        //List 
        $list = Tools::strtolower(Tools::getValue('list'));
        if($list=='true')
        {
            $fields_list = array(
                'id_comment' => array(
                    'title' => $this->l('Id'),
                    'width' => 40,
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true,
                ),
                'subject' => array(
                    'title' => $this->l('Subject'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true,                        
                ),                    
                'rating' => array(
                    'title' => $this->l('Rating'),
                    'type' => 'select',
                    'sort' => true,
                    'filter' => true,
                    'rating_field' => true,
                    'filter_list' => array(
                        'id_option' => 'rating',
                        'value' => 'stars',
                        'list' => array(
                            0 => array(
                                'rating' => 0,
                                'stars' => $this->l('No reviews')
                            ),
                            1 => array(
                                'rating' => 1,
                                'stars' => '1 '.$this->l('star')
                            ),
                            2 => array(
                                'rating' => 2,
                                'stars' => '2 '.$this->l('stars')
                            ),
                            3 => array(
                                'rating' => 3,
                                'stars' => '3 '.$this->l('stars')
                            ),
                            4 => array(
                                'rating' => 4,
                                'stars' => '4 '.$this->l('stars')
                            ),
                            5 => array(
                                'rating' => 5,
                                'stars' => '5 '.$this->l('stars')
                            ),
                        )
                    )
                ),
                'name' => array(
                    'title' => $this->l('Customer'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true
                ),
                'title'=>array(
                    'title'=>$this->l('Blog post'),
                    'type' => 'text',
                    'filter' => true,  
                    'strip_tag'=>false,
                ),
                'count_reply'=>array(
                    'title'=>$this->l('Replies'),
                    'type' => 'text',
                ),
                'approved' => array(
                    'title' => $this->l('Status'),
                    'type' => 'active',
                    'sort' => true,
                    'filter' => true,
                    'strip_tag' => false,
                    'filter_list' => array(
                        'id_option' => 'enabled',
                        'value' => 'title',
                        'list' => array(
                            0 => array(
                                'enabled' => 1,
                                'title' => $this->l('Approved')
                            ),
                            1 => array(
                                'enabled' => 0,
                                'title' => $this->l('Pending')
                            )
                        )
                    )
                ),
                'reported' => array(
                    'title' => $this->l('Not reported as abused'),
                    'type' => 'active',
                    'sort' => true,
                    'filter' => true,
                    'strip_tag' => false,
                    'form_group_class' => 'text-center',
                    'filter_list' => array(
                        'id_option' => 'enabled',
                        'value' => 'title',
                        'list' => array(
                            0 => array(
                                'enabled' => 1,
                                'title' => $this->l('Yes')
                            ),
                            1 => array(
                                'enabled' => 0,
                                'title' => $this->l('No')
                            )
                        )
                    )
                )
            );
            //Filter
            $filter = "";
            $show_reset = false;
            if(($id = trim(Tools::getValue('id_comment')))!='' && Validate::isCleanHtml($id))
            {
                $filter .= " AND bc.id_comment = ".(int)$id;
                $show_reset = true;
            }
            if(($com = trim(Tools::getValue('comment')))!='' && Validate::isCleanHtml($com))
            {
                $filter .= " AND bc.comment like '%".pSQL($com)."%'";
                $show_reset = true;
            }
            if(($subject = trim(Tools::getValue('subject')))!='' && Validate::isCleanHtml($subject))
            {
                $filter .= " AND (bc.subject LIKE '%".pSQL($subject)."%' OR bc.comment LIKE '%".pSQL($subject)."%')";
                $show_reset = true;
            }
            if(($rating = trim(Tools::getValue('rating')))!='' && Validate::isCleanHtml($rating))
            {
                $filter .= " AND bc.rating = ".(int)$rating; 
                $show_reset = true;
            }                   
            if(($name = trim(Tools::getValue('name')))!='' && Validate::isCleanHtml($name))
            {
                $filter .= " AND bc.name like '%".pSQL($name)."%'";
                $show_reset = true;
            }    
            if(($approved = trim(Tools::getValue('approved')))!='' && Validate::isCleanHtml($approved))
            {
                $filter .= " AND bc.approved = ".(int)$approved;
                $show_reset = true;
            }
            if(($reported = trim(Tools::getValue('reported')))!='' && Validate::isCleanHtml($reported))
            {
                $filter .= " AND bc.reported = ".(int)$reported;
                $show_reset = true;
            }
            if(($title = trim(Tools::getValue('title')))!='' && Validate::isCleanHtml($title))
            {
                $filter .= " AND pl.title like '%".pSQL($title)."%'";
                $show_reset = true;
            }    
            //Sort
            $sort_post = Tools::strtolower(Tools::getValue('sort'));
            $sort_type = Tools::strtolower(Tools::getValue('sort_type','desc'));
            if(!in_array($sort_type,array('desc','asc')))
                $sort_type ='desc';
            if($sort_post && isset($fields_list[$sort_post]))
            {
                $sort = $sort_post." ".($sort_type=='asc' ? ' ASC ' :' DESC ')." , ";
            }
            else
                $sort = 'bc.id_comment desc,';
            //Paggination
            $page = (int)Tools::getValue('page');
            if($page <1)
                $page=1;
            $totalRecords = (int)Ybc_blog_comment_class::countCommentsWithFilter($filter,false);
            $paggination = new Ybc_blog_paggination_class();            
            $paggination->total = $totalRecords;
            $paggination->url = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=comment&list=true&page=_page_'.$this->getUrlExtra($fields_list);
            $paggination->limit =  (int)Tools::getValue('paginator_ybc_comment_select_limit',20);
            $paggination->name ='ybc_comment';
            $totalPages = ceil($totalRecords / $paggination->limit);
            if($page > $totalPages)
                $page = $totalPages;
            $paggination->page = $page;
            $start = $paggination->limit * ($page - 1);
            if($start < 0)
                $start = 0;
            $comments = Ybc_blog_comment_class::getCommentsWithFilter($filter, $sort, $start, $paggination->limit,false);
            if($comments)
            {
                foreach($comments as &$comment)
                {
                    $comment['view_url'] = $this->getLink('blog', array('id_post' => $comment['id_post'])).'#blog_comment_line_'.$comment['id_comment'];
                    $comment['view_text'] = $this->l('View in post');
                    $comment['child_view_url'] = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=comment_reply&id_comment='.(int)$comment['id_comment'];
                    $replies = Ybc_blog_reply_class::getTotalRepliesByIDComment($comment['id_comment']);
                    $replies_no_approved = Ybc_blog_reply_class::getTotalRepliesByIDComment($comment['id_comment'],0);
                    if($replies)
                        $comment['count_reply'] = $replies. ($replies_no_approved ? ' ('.$replies_no_approved.' '.$this->l('pending').')':'');
                    else
                        $comment['count_reply']=0;
                    $comment['title'] = '<a target="_blank" href="'.$this->getLink('blog',array('id_post'=>$comment['id_post'])).'" title="'.$comment['title'].'">'.$comment['title'].'</a>';
                }
            }
            $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
            $paggination->style_links = $this->l('links');
            $paggination->style_results = $this->l('results');
            $listData = array(
                'name' => 'ybc_comment',
                'actions' => array('edit','approve' ,'delete'),
                'currentIndex' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=comment'.($paggination->limit!=20 ? '&paginator_ybc_comment_select_limit='.$paggination->limit:''),
                'identifier' => 'id_comment',
                'show_toolbar' => true,
                'show_action' => true,
                'title' => $this->l('Comments'),
                'fields_list' => $fields_list,
                'field_values' => $comments,
                'paggination' => $paggination->render(),
                'filter_params' => $this->getFilterParams($fields_list),
                'show_reset' => $show_reset,
                'totalRecords' => $totalRecords,
                'show_add_new' => false,
                'sort'=> $sort_post ?: 'id_comment',
                'sort_type'=> $sort_type,
            );            
            return $this->_html .= $this->renderList($listData);      
        }
        //Form
        $id_comment = (int)Tools::getValue('id_comment');    
        $fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Manage Comments'),				
				),
				'input' => array(					
					array(
						'type' => 'text',
						'label' => $this->l('Subject'),
						'name' => 'subject',    					 
                        'required' => true,
                        'desc' => $id_comment && ($comment = Ybc_blog_comment_class::getCommentById($id_comment)) ? $this->displayCommentInfo($comment,(int)$comment['id_user'],$this->getLink('blog',array('id_post' => (int)$comment['id_post']))) : '',
					), 
                    array(
    					'type' => 'select',
    					'label' => $this->l('Rating'),
    					'name' => 'rating',
                        'options' => array(
                			 'query' => array(                                
                                    array(
                                        'id_option' => '0', 
                                        'name' => $this->l('No ratings')
                                    ),
                                    array(
                                        'id_option' => '1', 
                                        'name' => '1 '. $this->l('rating')
                                    ),
                                    array(
                                        'id_option' => '2', 
                                        'name' => '2 '. $this->l('ratings')
                                    ),
                                    array(
                                        'id_option' => '3', 
                                        'name' => '3 '. $this->l('ratings')
                                    ),
                                    array(
                                        'id_option' => '4', 
                                        'name' => '4 '. $this->l('ratings')
                                    ),
                                    array(
                                        'id_option' => '5', 
                                        'name' => '5 '. $this->l('ratings')
                                    )
                                ),                             
                             'id' => 'id_option',
                			 'name' => 'name'  
                        )                
    				),
                    array(
						'type' => 'textarea',
						'label' => $this->l('Comment'),
						'name' => 'comment',                            
                        'required' => true						
					),
                    array(
						'type' => 'switch',
						'label' => $this->l('Approved'),
						'name' => 'approved',
                        'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						)					
					),
                    array(
						'type' => 'switch',
						'label' => $this->l('Not reported as abused'),
						'name' => 'reported',
                        'is_bool' => true,
                        'form_group_class' => 'text-center',
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						)					
					),
                    array(
                        'type' => 'hidden', 
                        'name' => 'control'
                    )
                ),
                'submit' => array(
					'title' => $this->l('Save'),
				)
            ),
		);
        $helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'saveComment';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
			'fields_value' => $this->getFieldsValues(Ybc_blog_defines::$commentFields,'id_comment','Ybc_blog_comment_class','saveComment'),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'image_baseurl' => _PS_YBC_BLOG_IMG_,
            'link' => $this->context->link,
            'cancel_url' => $this->baseAdminPath.'&control=comment&list=true'
		);            
        if(Tools::isSubmit('id_comment') && $id_comment)
        {
            
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_comment');                
        }
        
		$helper->override_folder = '/'; 
        $this->_html .= $helper->generateForm(array($fields_form));			
    }
    public function displayCommentInfo($comment, $id_customer, $postLink)
    {
        if($id_customer)
        {
            if(version_compare(_PS_VERSION_, '1.7.6', '>='))
                {
                    $sfContainer = call_user_func(array('\PrestaShop\PrestaShop\Adapter\SymfonyContainer','getInstance'));
                	if (null !== $sfContainer) {
                		$sfRouter = $sfContainer->get('router');
                		$customerLink= $sfRouter->generate(
                			'admin_customers_view',
                			array('customerId' => $id_customer)
                		);
                	}
                    else
                        $customerLink = $this->context->link->getAdminLink('AdminCustomers').'&id_customer='.(int)$id_customer.'&viewcustomer';
                }
                else
                    $customerLink = $this->context->link->getAdminLink('AdminCustomers').'&id_customer='.(int)$id_customer.'&viewcustomer';
        }
        else
            $customerLink='#';
        $this->smarty->assign(array(
            'comment' => $comment,
            'customerLink' => $customerLink,
            'postLink' => $postLink,
        ));
        return $this->display(__FILE__,'comment_info.tpl');
    }
    public function renderAuthorForm()
    {
        return $this->_html .= $this->displayTabAuthor().$this->renderCustomerForm(true).$this->renderEmployeeFrom(true).$this->renderSettingCustomer();
    }
    public function renderCustomerForm($list=false)
    {
        if(!Configuration::get('YBC_BLOG_ALLOW_CUSTOMER_AUTHOR'))
            return false;
        //List 
        $list_post = trim(Tools::getValue('list'));
        if($list_post=='true' || $list)
        {
            $fields_list = array(
                'id_customer' => array(
                    'title' => $this->l('Id'),
                    'width' => 40,
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true
                ),
                'avata' => array(
                    'title' => $this->l('Avatar'),
                    'type' => 'text',
                    'strip_tag' => false,       
                ),                     
                'name' => array(
                    'title' => $this->l('Name'),
                    'type' => 'text',
                    'sort' => true,
                    'strip_tag' => false,
                    'filter' => true
                    
                ), 
                'email' => array(
                    'title' => $this->l('Email'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true
                ), 
                'description' => array(
                    'title' => $this->l('Introduction'),
                    'type' => 'text',
                    'filter'=>true  
                ),
                'has_post'=> array(
                    'title' => $this->l('Have posts'),
                    'type' => 'active',
                    'filter'=>true, 
                    'strip_tag' => false,
                    'filter_list' => array(
                        'id_option' => 'enabled',
                        'value' => 'title',
                        'list' => array(
                            0 => array(
                                'enabled' => '',
                                'title' => '--'
                            ),
                            1 => array(
                                'enabled' => 1,
                                'title' => $this->l('Yes')
                            ),
                            2 => array(
                                'enabled' => 0,
                                'title' => $this->l('No')
                            )
                        )
                    )
                ),
                'total_post'=> array(
                    'title' => $this->l('Total posts'),
                    'sort' => true,
                    'type' => 'int',
                    'filter'=>true, 
                ), 
                'status' => array(
                    'title'=> $this->l('Status'),
                    'type' => 'active',
                    'filter'=>true,
                    'sort' => true,
                    'strip_tag' => false,
                    'filter_list' => array(
                        'id_option' => 'enabled',
                        'value' => 'title',
                        'list' => array(
                            0 => array(
                                'enabled' => 1,
                                'title' => $this->l('Activated')
                            ),
                            1 => array(
                                'enabled' => 0,
                                'title' => $this->l('Suspended')
                            ),
                            2 => array(
                                'enabled' => -1,
                                'title' => $this->l('Suspended and hide posts')
                            )
                        )
                    )
                )              
            );
            //Filter
            $filter = "";
            $sort = "";
            $having='';
            if(($control = Tools::getValue('control')) && $control=='customer')
            {
                if(($id = trim(Tools::getValue('id_customer')))!='' && Validate::isCleanHtml($id))
                    $filter .= " AND c.id_customer = ".(int)$id;
                if(($name = trim(Tools::getValue('name')))!='' && Validate::isCleanHtml($name))
                    $filter .= " AND (CONCAT(c.firstname,' ',c.lastname) like '".pSQL($name)."%' OR be.name like'".pSQL($name)."%')";                
                if(($email = trim(Tools::getValue('email')))!='' && Validate::isCleanHtml($email))
                    $filter .= " AND c.email like '".pSQL($email)."%'";
                if(($desc = trim(Tools::getValue('description')))!='' && Validate::isCleanHtml($desc))
                    $filter .= ' AND bel.description like "%'.pSQL($desc).'%"';
                if(($total_post_min = trim(Tools::getValue('total_post_min')))!='' && Validate::isCleanHtml($total_post_min))
                    $having .= ' AND total_post >="'.(int)$total_post_min.'"';
                if(($total_post_max = trim(Tools::getValue('total_post_max')))!='' && Validate::isCleanHtml($total_post_max))
                    $having .= ' AND total_post <="'.(int)$total_post_max.'"';
                if(Tools::isSubmit('status') && ($status = trim(Tools::getValue('status')))!='' && Validate::isCleanHtml($status))
                    $filter .= " AND (be.status= '".(int)$status."'".((int)$status==1 ? ' or be.status is null':'' )." )";
                //Sort
                $sort_post  = Tools::strtolower(Tools::getValue('sort'));
                $sort_type = Tools::strtolower(Tools::getValue('sort_type','desc'));
                if(!in_array($sort_type,array('desc','asc')))
                    $sort_type ='desc';
                if($sort_post && isset($fields_list[$sort_post]))
                {
                    $sort .= $sort_post." ".($sort_type=='asc' ? ' ASC ' :' DESC ')."";
                }
                else
                    $sort = false;
            }
            $has_post = Tools::getValue('has_post');
            if(!Tools::isSubmit('has_post') || $has_post==1)
                $having .= ' AND total_post >=1';
            elseif(Tools::isSubmit('has_post') && $has_post!='')
                $having .= ' AND total_post <=0';
            $page = (int)Tools::getValue('page');
            if($page <1)
                $page=1;
            $totalRecords = (int)Ybc_blog_post_employee_class::countCustomersFilter($filter,$having);
            $paggination = new Ybc_blog_paggination_class();            
            $paggination->total = $totalRecords;
            $paggination->url = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=customer&list=true&page=_page_'.$this->getUrlExtra($fields_list);
            $paggination->limit =  (int)Tools::getValue('paginator_ybc_blog_customer_select_limit',20);
            $paggination->name ='ybc_blog_customer';
            $totalPages = ceil($totalRecords / $paggination->limit);
            if($page > $totalPages)
                $page = $totalPages;
            $paggination->page = $page;
            $start = $paggination->limit * ($page - 1);
            if($start < 0)
                $start = 0;
            $customers = Ybc_blog_post_employee_class::getCustomersFilter($filter, $sort, $start, $paggination->limit,$having);
            if($customers)
            {
                foreach($customers as &$customer)
                {
                    if(!$customer['name'])
                        $customer['name']=$customer['firstname'].' '.$customer['lastname'];
                    if($customer['avata'])
                        $customer['avata']='<div class="avata_img"><img src="'._PS_YBC_BLOG_IMG_.'avata/'.$customer['avata'].'" style="width:40px;"/></div>';
                    else
                        $customer['avata']='<div class="avata_img"><img src="'._PS_YBC_BLOG_IMG_.'avata/default_customer.png" style="width:40px;"/></div>';
                    
                    $customer['view_post_url'] = $this->getLink('blog',array('id_author'=> $customer['id_customer'],'is_customer'=>1,'alias'=> Tools::link_rewrite($customer['name'],true)));
                    $customer['delete_post_url'] = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=customer&list=true&deleteAllPostCustomer&id_author='.(int)$customer['id_customer'];
                    if($customer['total_post']==0)
                        $customer['has_post']=0;
                    else
                        $customer['has_post']=1;
                    $customer['name'] ='<a href="'.$this->context->link->getAdminLink('AdminCustomers').'&updatecustomer&id_customer='.(int)$customer['id_customer'].'" title="'.$customer['name'].'">'.$customer['name'].'</a>';
                }
            }
            $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
            $paggination->style_links = $this->l('links');
            $paggination->style_results = $this->l('results');
            $listData = array(
                'name' => 'ybc_blog_customer',
                'class' =>'customer',
                'actions' => array('edit', 'view'),
                'currentIndex' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=customer'.($paggination->limit!=20 ? '&paginator_billing_select_limit='.$paggination->limit:''),
                'identifier' => 'id_customer',
                'show_toolbar' => true,
                'show_action' => true,
                'title' => '',
                'fields_list' => $fields_list,
                'field_values' => $customers,
                'paggination' => $paggination->render(),
                'filter_params' => $this->getFilterParams($fields_list),
                'show_reset' => $filter || Tools::isSubmit('total_post_min') || Tools::isSubmit('total_post_max') || Tools::isSubmit('has_post') ? true : false,
                'show_add_new' => false,
                'sort' => $sort ? $sort_post:'',
                'sort_type' => $sort ? $sort_type:'',
            ); 
            if($list)
               return $this->renderList($listData);            
            return $this->_html .= $this->displayTabAuthor().$this->renderList($listData).$this->renderEmployeeFrom(true).$this->renderSettingCustomer();      
        }
        //Form
        $id_customer = (int)Tools::getValue('id_customer');
        $fields_form = array(
			'form' => array(
				'input' => array(					
					array(
						'type' => 'text',
						'label' => $this->l('Name'),
						'name' => 'name',  
                        'required' => true,                
					), 
                    array(
						'type' => 'textarea',
						'label' => $this->l('Introduction'),
						'name' => 'description',
                        'lang'=>true,
                    ),                         
                    array(
						'type' => 'file',
						'label' => $this->l('Avatar photo'),
						'name' => 'avata',
                        'col'=>9,
                        'desc'=> $this->l('Avatar photo should be a square image. Recommended size: ').Configuration::get('YBC_BLOG_IMAGE_AVATA_WIDTH',300).'x'.Configuration::get('YBC_BLOG_IMAGE_AVATA_HEIGHT',300),                 						
					),
                    array(
                        'type'=>'select',
                        'label'=>$this->l('Status'),
                        'name'=>'status',
                        'options' => array(
                            'query' => array( 
                                    array(
                                        'id_option' => 1, 
                                        'name' => $this->l('Activated')
                                    ),        
                                    array(
                                        'id_option' => 0, 
                                        'name' => $this->l('Suspended')
                                    ),
                                    array(
                                        'id_option' => -1, 
                                        'name' => $this->l('Suspended and hide posts')
                                    ),
                                ),                             
                             'id' => 'id_option',
                			 'name' => 'name'  
                        ),
                    ),
                    array(
                        'type' => 'hidden', 
                        'name' => 'control'
                    )
                ),
                'submit' => array(
					'title' => $this->l('Save'),
				)
            ),
		);
        $helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'saveBlogEmployee';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=customer&id_customer='.(int)$id_customer;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
			'fields_value' => $this->getFieldsCustomerValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'image_baseurl' => _PS_YBC_BLOG_IMG_,
            'link' => $this->context->link,
            'post_key' => 'id_customer',
            'cancel_url' => $this->baseAdminPath.'&control=customer&list=true',
            'name_controller' => 'ybc-blog-panel-customer',
		);
        if(Tools::isSubmit('id_customer') && ($id_customer = (int)Tools::getValue('id_customer')))
        {
            
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_customer');
            if(($id = Ybc_blog_post_employee_class::getIdEmployeePostById($id_customer)) && ($blog_employee = new Ybc_blog_post_employee_class($id)) && $blog_employee->avata)
            {             
                $helper->tpl_vars['display_img'] = _PS_YBC_BLOG_IMG_.'avata/'.$blog_employee->avata;
                $helper->tpl_vars['img_del_link'] = $this->baseAdminPath.'&id_customer='.$id_customer.'&delemployeeimage=true&control=customer';                
            }
        }
		$helper->override_folder = '/';      
        $this->_html .= $this->displayTabAuthor().$helper->generateForm(array($fields_form)).$this->renderEmployeeFrom(true).$this->renderSettingCustomer();
    }
    public function renderEmployeeFrom($list=false)
    {
        //List 
        $list_post = Tools::getValue('list');
        if($list_post=='true' || $list)
        {
            $fields_list = array(
                'id_employee' => array(
                    'title' => $this->l('Id'),
                    'width' => 40,
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true
                ),
                'avata' => array(
                    'title' => $this->l('Avatar'),
                    'type' => 'text',
                    'strip_tag' => false,       
                ),                     
                'name' => array(
                    'title' => $this->l('Name'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true,
                    'strip_tag' => false,   
                ), 
                'email' => array(
                    'title' => $this->l('Email'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true
                ), 
                'description' => array(
                    'title' => $this->l('Introduction'),
                    'type' => 'text',
                    'filter'=>true, 
                ),  
                'profile_name'=>array(
                    'title' => $this->l('Profile'),
                    'type' => 'select',
                    'filter'=>true,
                    'filter_list'=>array(
                        'list'=> Profile::getProfiles((int)$this->context->language->id),
                        'id_option' => 'id_profile',
                        'value' => 'name',
                    )
                ),                 
                'profile_employee' =>array(
                    'title'=> $this->l('Accessible tabs'),
                    'width'=>'140',
                    'type'=>'select',
                    'strip_tag' => false,
                    'filter'=> true,
                    'filter_list'=>array(
                        'list'=> array(
                            array(
                                'title'=>$this->l('All tabs'),
                                'id'=>'All tabs'
                            ),
                            array(
                                'title'=>$this->l('Blog posts and blog categories'),
                                'id'=>'Blog posts and blog categories'
                            ),
                            array(
                                'title'=>$this->l('Blog comments'),
                                'id'=>'Blog comments'
                            ),
                            array(
                                'title'=>$this->l('Blog slider'),
                                'id'=>'Blog slider'
                            ),
                            array(
                                'title'=>$this->l('Blog gallery'),
                                'id'=>'Blog gallery'
                            ),
                            array(
                                'title'=>$this->l('Rss feed'),
                                'id'=>'Rss feed'
                            ),
                            array(
                                'title'=>$this->l('Seo'),
                                'id'=>'Seo'
                            ),
                            array(
                                'title'=>$this->l('Socials'),
                                'id'=>'Socials'
                            ),
                            array(
                                'title'=>$this->l('Sitemap'),
                                'id'=>'Sitemap'
                            ),
                            array(
                                'title'=>$this->l('Email'),
                                'id'=>'Email'
                            ),
                            array(
                                'title'=>$this->l('Image'),
                                'id'=>'Image'
                            ),
                            array(
                                'title'=>$this->l('Sidebar'),
                                'id'=>'Sidebar'
                            ),
                            array(
                                'title'=>$this->l('Home page'),
                                'id'=>'Home page'
                            ),
                            array(
                                'title'=>$this->l('Post detail page'),
                                'id'=>'Post detail page'
                            ),
                            array(
                                'title'=>$this->l('Post listing pages'),
                                'id'=>'Post listing pages'
                            ),
                            array(
                                'title'=>$this->l('Category page'),
                                'id'=>'Category page'
                            ),
                            array(
                                'title'=>$this->l('Product detail page'),
                                'id'=>'Product detail page'
                            ),
                            array(
                                'title'=>$this->l('Authors'),
                                'id'=>'Authors'
                            ),
                            array(
                                'title'=>$this->l('Import/Export'),
                                'id'=>'Import/Export'
                            ),
                            array(
                                'title'=>$this->l('Statistics'),
                                'id'=>'Statistics'
                            ),
                            array(
                                'title'=>$this->l('Global settings'),
                                'id'=>'Global settings'
                            ),
                        ),  
                        'id_option' => 'id',
                        'value' => 'title',
                    )  
                ),
                'total_post' =>array(
                    'title'=> $this->l('Total posts'),
                    'width'=>'140',
                    'type'=>'int',
                    'filter'=>true,
                    'sort' => true,
                ),
                'status' => array(
                    'title'=> $this->l('Status'),
                    'type' => 'active',
                    'strip_tag' => false,
                    'filter'=>true,
                    'sort' => true,
                    'filter_list' => array(
                        'id_option' => 'enabled',
                        'value' => 'title',
                        'list' => array(
                            0 => array(
                                'enabled' => 0,
                                'title' => $this->l('Activated')
                            ),
                            1 => array(
                                'enabled' => 1,
                                'title' => $this->l('Suspended')
                            ),
                            2 => array(
                                'enabled' => -1,
                                'title' => $this->l('Suspended and hide posts')
                            )
                        )
                    )
                )
            );
            //Filter
            $filter = "";
            $sort = "";
            $having="";
            if(($control = Tools::getValue('control')) && $control=='employees')
            {
                if(($id = trim(Tools::getValue('id_employee')))!='' && Validate::isCleanHtml($id))
                    $filter .= " AND e.id_employee = ".(int)$id;
                if(($name = trim(Tools::getValue('name')))!='' && Validate::isCleanHtml($name))
                    $filter .= " AND (CONCAT(e.firstname,' ',e.lastname) like '".pSQL($name)."%' OR be.name like'".pSQL($name)."%')";                
                if(($email = trim(Tools::getValue('email')))!='' && Validate::isCleanHtml($email))
                    $filter .= " AND e.email like '%".pSQL($email)."%'";
                if(($description = trim(Tools::getValue('description')))!='' && Validate::isCleanHtml($description))
                    $filter .= " AND bel.description like '%".pSQL($description)."%'";
                if(($id_profile = trim(Tools::getValue('id_profile')))!='' && Validate::isCleanHtml($id_profile))
                    $filter .= " AND pl.id_profile = '".(int)$id_profile."'";
                if(($profile_employee = trim(Tools::getValue('profile_employee')))!='' && Validate::isCleanHtml($profile_employee))
                    $filter .= " AND (be.profile_employee like '%".pSQL($profile_employee)."%' OR p.id_profile=1 or be.profile_employee like '%All tabs%')  ";
                if(($total_post_min = trim(Tools::getValue('total_post_min')))!='' && Validate::isCleanHtml($total_post_min))
                    $having .= ' AND total_post >="'.(int)$total_post_min.'"';
                if(($total_post_max = trim(Tools::getValue('total_post_max')))!='' && Validate::isCleanHtml($total_post_max))
                    $having .= ' AND total_post <="'.(int)$total_post_max.'"';
                if(Tools::isSubmit('status') && ($status = trim(Tools::getValue('status')))!='' && Validate::isCleanHtml($status))
                    $filter .= " AND (be.status= '".(int)$status."'".(!(int)$status ? ' or be.status is null':'' )." )";
                //Sort
                $sort_post = Tools::strtolower(Tools::getValue('sort'));
                $sort_type = Tools::strtolower(Tools::getValue('sort_type'));
                if(!in_array($sort_type,array('desc','asc')))
                    $sort_type = 'desc';
                if($sort_post && isset($fields_list[$sort_post]))
                {
                    
                    $sort .= $sort_post." ".($sort_type=='asc' ? ' ASC ' :' DESC ')."";
                }
                else
                    $sort = false;
            }
            //Paggination
            $page = (int)Tools::getValue('page');
            if($page<1)
                $page =1;
            $totalRecords = (int)Ybc_blog_post_employee_class::countEmployeesFilter($filter,$having);
            $paggination = new Ybc_blog_paggination_class();            
            $paggination->total = $totalRecords;
            $paggination->url = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=employees&list=true&page=_page_'.$this->getUrlExtra($fields_list);
            $paggination->limit =  (int)Tools::getValue('paginator_ybc_blog_employee_select_limit',20);
            $paggination->name ='ybc_blog_employee';
            $totalPages = ceil($totalRecords / $paggination->limit);
            if($page > $totalPages)
                $page = $totalPages;
            $paggination->page = $page;
            $start = $paggination->limit * ($page - 1);
            if($start < 0)
                $start = 0;
            $employees = Ybc_blog_post_employee_class::getEmployeesFilter($filter, $sort, $start, $paggination->limit,$having);
            
            if($employees)
            {
                foreach($employees as &$employee)
                {
                    if(!$employee['name'])
                        $employee['name']=$employee['employee'];
                    if($employee['avata'])
                        $employee['avata']='<div class="avata_img"><img src="'._PS_YBC_BLOG_IMG_.'avata/'.$employee['avata'].'" style="width:40px;"/></div>';
                    else
                        $employee['avata']='<div class="avata_img"><img src="'._PS_YBC_BLOG_IMG_.'avata/default_customer.png" style="width:40px;"/></div>';
                    if($employee['id_profile']==1 || Tools::strpos($employee['profile_employee'],'All tabs')!==false)
                        $employee['profile_employee'] = 'All tabs';
                    else
                        $employee['profile_employee'] = str_replace(',','<br/>',$employee['profile_employee']);
                    $employee['view_post_url'] = $this->getLink('blog',array('id_author'=> $employee['id_employee'],'alias'=> Tools::link_rewrite($employee['name'],true)));
                    $employee['delete_post_url'] = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=employees&list=true&deleteAllPostEmployee&id_author='.(int)$employee['id_employee'];
                    $employee['name'] = '<a href ="'.$this->context->link->getAdminLink('AdminEmployees').'&updateemployee&id_employee='.(int)$employee['id_employee'].'" title="'.$employee['name'].'">'.$employee['name'].'</a>';
                }
            }
            $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
            $paggination->style_links = $this->l('links');
            $paggination->style_results = $this->l('results');                
            $listData = array(
                'name' => 'ybc_blog_employee',
                'actions' => array('edit', 'view'),
                'class' =>'employee',
                'currentIndex' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=employees'.($paggination->limit!=20 ? '&paginator_ybc_blog_employee_select_limit='.$paggination->limit:''),
                'identifier' => 'id_employee',
                'show_toolbar' => true,
                'show_action' => true,
                'title' => '',
                'fields_list' => $fields_list,
                'field_values' => $employees,
                'paggination' => $paggination->render(),
                'filter_params' => $this->getFilterParams($fields_list),
                'show_reset' => $filter || $having ? true : false,
                'show_add_new' => false,
                'sort' => $sort ? $sort_post:'',
                'sort_type' => $sort ? $sort_type:'',
                
            ); 
            if($list)
               return $this->renderList($listData);        
            return $this->_html .= $this->displayTabAuthor().$this->renderList($listData).$this->renderCustomerForm(true).$this->renderSettingCustomer();      
        }
        
        //Form
        $id_employee = (int)Tools::getValue('id_employee');
        $employee_class= new Employee($id_employee);
        $fields_form = array(
			'form' => array(
				'input' => array(					
					array(
						'type' => 'text',
						'label' => $this->l('Name'),
						'name' => 'name',         
                        'required' => true,         
					), 
                    array(
						'type' => 'textarea',
						'label' => $this->l('Introduction'),
						'name' => 'description',
                        'lang'=>true,
                    ),                         
                    array(
						'type' => 'file',
						'label' => $this->l('Avatar photo'),
						'name' => 'avata',
                        'col' => 9,
                        'desc'=> sprintf($this->l('Avatar photo should be a square image. Accepted formats: jpg, jpeg, png, gif. Limit: %dMb. Recommended size: '),Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')).Configuration::get('YBC_BLOG_IMAGE_AVATA_WIDTH',300).'x'.Configuration::get('YBC_BLOG_IMAGE_AVATA_HEIGHT',300),                 						
					),
                    array(
                        'type'=>'select',
                        'label'=>$this->l('Status'),
                        'name'=>'status',
                        'form_group_class'=> 'status'.($employee_class->id_profile==1?' hide':''),
                        'options' => array(
                            'query' => array( 
                                    array(
                                        'id_option' => 1, 
                                        'name' => $this->l('Activated')
                                    ),        
                                    array(
                                        'id_option' => 0, 
                                        'name' => $this->l('Suspended')
                                    ),
                                    array(
                                        'id_option' => -1, 
                                        'name' => $this->l('Suspended and hide posts')
                                    ),
                                ),                             
                             'id' => 'id_option',
                			 'name' => 'name'  
                        ),
                    ),
                    array(
                        'type' => 'profile_employee',
    					'label' => $this->l('Accessible tabs'),
                        'form_group_class'=> 'profile'.($employee_class->id_profile==1?' hide':''),
    					'profiles' => array(
                            array(
                                'title'=>$this->l('All tabs'),
                                'id'=>'All tabs'
                            ),
                            array(
                                'title'=>$this->l('Blog posts and blog categories'),
                                'id'=>'Blog posts and blog categories'
                            ),
                            array(
                                'title'=>$this->l('Blog comments'),
                                'id'=>'Blog comments'
                            ),
                            array(
                                'title'=>$this->l('Blog slider'),
                                'id'=>'Blog slider'
                            ),
                            array(
                                'title'=>$this->l('Blog gallery'),
                                'id'=>'Blog gallery'
                            ),
                            array(
                                'title'=>$this->l('Rss feed'),
                                'id'=>'Rss feed'
                            ),
                            array(
                                'title'=>$this->l('Seo'),
                                'id'=>'Seo'
                            ),
                            array(
                                'title'=>$this->l('Socials'),
                                'id'=>'Socials'
                            ),
                            array(
                                'title'=>$this->l('Sitemap'),
                                'id'=>'Sitemap'
                            ),
                            array(
                                'title'=>$this->l('Email'),
                                'id'=>'Email'
                            ),
                            array(
                                'title'=>$this->l('Image'),
                                'id'=>'Image'
                            ),
                            array(
                                'title'=>$this->l('Sidebar'),
                                'id'=>'Sidebar'
                            ),
                            array(
                                'title'=>$this->l('Home page'),
                                'id'=>'Home page'
                            ),
                            array(
                                'title'=>$this->l('Post detail page'),
                                'id'=>'Post detail page'
                            ),
                            array(
                                'title'=>$this->l('Post listing pages'),
                                'id'=>'Post listing pages'
                            ),
                            array(
                                'title'=>$this->l('Category page'),
                                'id'=>'Category page'
                            ),
                            array(
                                'title'=>$this->l('Product detail page'),
                                'id'=>'Product detail page'
                            ),
                            array(
                                'title'=>$this->l('Authors'),
                                'id'=>'Authors'
                            ),
                            array(
                                'title'=>$this->l('Import/Export'),
                                'id'=>'Import/Export'
                            ),
                            array(
                                'title'=>$this->l('Statistics'),
                                'id'=>'Statistics'
                            ),
                            array(
                                'title'=>$this->l('Global settings'),
                                'id'=>'Global settings'
                            ),
                        ),
    					'name' => 'profile_employee',
                        'selected_profile' => $this->getProfileEmployee($employee_class->id)                                           
    				),
                    array(
                        'type' => 'hidden', 
                        'name' => 'control'
                    )
                ),
                'submit' => array(
					'title' => $this->l('Save'),
				)
            ),
		);
        $helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'saveBlogEmployee';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=employees';
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
			'fields_value' => $this->getFieldsEmployeeValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'image_baseurl' => _PS_YBC_BLOG_IMG_,
            'link' => $this->context->link,
            'post_key' => 'id_employee',
            'cancel_url' => $this->baseAdminPath.'&control=employees&list=true',
            'name_controller' => 'ybc-blog-panel-employee',                        
		);
        
        if(Tools::isSubmit('id_employee') && ($id_employee = (int)Tools::getValue('id_employee')))
        {
            
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_employee');
            if(($id = Ybc_blog_post_employee_class::getIdEmployeePostById($id_employee,false)) && ($blog_employee = new Ybc_blog_post_employee_class($id)) && $blog_employee->avata)
            {             
                $helper->tpl_vars['display_img'] = _PS_YBC_BLOG_IMG_.'avata/'.$blog_employee->avata;
                $helper->tpl_vars['img_del_link'] = $this->baseAdminPath.'&id_employee='.$id_employee.'&delemployeeimage=true&control=employees';                
            }
        }
        
		$helper->override_folder = '/';   
        $this->_html .= $this->displayTabAuthor().$helper->generateForm(array($fields_form)).$this->renderCustomerForm(true).$this->renderSettingCustomer();
    }
    /**
     * Side 
     */
    public function renderSlideForm()
    {
        //List 
        $list = trim(Tools::getValue('list'));
        if($list=='true')
        {
            $fields_list = array(
                'id_slide' => array(
                    'title' => $this->l('Id'),
                    'width' => 40,
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true
                ),
                'image' => array(
                    'title' => $this->l('Image'),
                    'type' => 'text',
                    'filter' => false                       
                ),                     
                'caption' => array(
                    'title' => $this->l('Caption'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true
                ), 
                'sort_order' => array(
                    'title' => $this->l('Sort order'),
                    'type' => 'text',
                    'sort' => true,
                    'drag_handle' => true,
                    'filter' => true,
                    'update_position' => true,
                ),                    
                'enabled' => array(
                    'title' => $this->l('Enabled'),
                    'type' => 'active',
                    'sort' => true,
                    'filter' => true,
                    'strip_tag' => false,
                    'filter_list' => array(
                        'id_option' => 'enabled',
                        'value' => 'title',
                        'list' => array(
                            0 => array(
                                'enabled' => 1,
                                'title' => $this->l('Yes')
                            ),
                            1 => array(
                                'enabled' => 0,
                                'title' => $this->l('No')
                            )
                        )
                    )
                ),
            );
            //Filter
            $filter = "";
            $show_reset = false;
            if(($id = trim(Tools::getValue('id_slide')))!='' && Validate::isCleanHtml($id))
            {
                $filter .= " AND s.id_slide = ".(int)$id;
                $show_reset = true;
            }
            if(($sort_order = trim(Tools::getValue('sort_order')))!='' && Validate::isCleanHtml($sort_order))
            {
                $filter .= " AND s.sort_order = ".(int)$sort_order;
                $show_reset = true;
            }                    
            if(($caption = trim(Tools::getValue('caption')))!='' && Validate::isCleanHtml($caption))
            {
                $filter .= " AND sl.caption like '%".pSQL($caption)."%'";
                $show_reset = true;
            }    
            if(($enabled = trim(Tools::getValue('enabled')))!='' && Validate::isCleanHtml($enabled))
            {
                $filter .= " AND s.enabled =".(int)$enabled;
                $show_reset = true;
            }
            
            //Sort
            $sort = "";
            $sort_post = Tools::strtolower(Tools::getValue('sort'));
            $sort_type = Tools::strtolower(Tools::getValue('sort_type','desc'));
            if(!in_array($sort_type,array('desc','asc')))
                $sort_type = 'desc';
            if($sort_post && isset($fields_list[$sort_post]))
            {
                $sort .= $sort_post." ".($sort_type=='asc' ? ' ASC ' :' DESC ')." , ";
            }
            else
                $sort = 's.sort_order asc, ';
            //Paggination
            $page = (int)Tools::getValue('page');
            if($page <1)
                $page=1;
            $totalRecords = (int)Ybc_blog_slide_class::countSlidesWithFilter($filter);
            $paggination = new Ybc_blog_paggination_class();            
            $paggination->total = $totalRecords;
            $paggination->url = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=slide&list=true&page=_page_'.$this->getUrlExtra($fields_list);
            $paggination->limit =  (int)Tools::getValue('paginator_ybc_slide_select_limit',20);
            $paggination->name ='ybc_slide';
            $totalPages = ceil($totalRecords / $paggination->limit);
            if($page > $totalPages)
                $page = $totalPages;
            $paggination->page = $page;
            $start = $paggination->limit * ($page - 1);
            if($start < 0)
                $start = 0;
            $slides = Ybc_blog_slide_class::getSlidesWithFilter($filter, $sort, $start, $paggination->limit);
            if($slides)
            {
                foreach($slides as &$slide)
                {
                    if($slide['image'] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'slide/'.$slide['image']))
                    {
                        $slide['image'] = array(
                            'image_field' => true,
                            'img_url' => _PS_YBC_BLOG_IMG_.'slide/'.$slide['image'],
                        );
                    }
                    else
                    $slide['image']=array();
                }
            }
            $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
            $paggination->style_links = $this->l('links');
            $paggination->style_results = $this->l('results');
            $listData = array(
                'name' => 'ybc_slide',
                'actions' => array('edit', 'delete', 'view'),
                'currentIndex' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=slide'.($paggination->limit!=20 ? '&paginator_ybc_slide_select_limit='.$paggination->limit:''),
                'identifier' => 'id_slide',
                'show_toolbar' => true,
                'show_action' => true,
                'title' => $this->l('Slider'),
                'fields_list' => $fields_list,
                'field_values' => $slides,
                'paggination' => $paggination->render(),
                'filter_params' => $this->getFilterParams($fields_list),
                'show_reset' => $show_reset,
                'totalRecords' => $totalRecords,
                'sort' => $sort_post ? :'sort_order',
                'sort_type'=> $sort_type,
            );            
            return $this->_html .= $this->renderList($listData);      
        }
        
        //Form
        
        $fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Manage slider'),				
				),
				'input' => array(					
					array(
						'type' => 'text',
						'label' => $this->l('Caption'),
						'name' => 'caption',
						'lang' => true,    
                        'required' => true,                    
					), 
                    array(
						'type' => 'text',
						'label' => $this->l('Url'),
						'name' => 'url',
                        'lang'=>true,
                    ),                         
                    array(
						'type' => 'file_lang',
						'label' => $this->l('Image'),
						'name' => 'image',
                        'required' => true,    
                         'desc' =>sprintf($this->l('Accepted formats: jpg, jpeg, png, gif. Limit: %dMb. Recommended size: %sx%s.'),Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),Configuration::get('YBC_BLOG_IMAGE_SLIDER_WIDTH',null,null,null,800),Configuration::get('YBC_BLOG_IMAGE_SLIDER_HEIGHT',null,null,null,470)),       						
					),
                    array(
						'type' => 'switch',
						'label' => $this->l('Enabled'),
						'name' => 'enabled',
                        'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						)					
					),
                    array(
                        'type' => 'hidden', 
                        'name' => 'control'
                    )
                ),
                'submit' => array(
					'title' => $this->l('Save'),
				)
            ),
		);
        $helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'saveSlide';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
			'fields_value' => $this->getFieldsValues(Ybc_blog_defines::$slideFields,'id_slide','Ybc_blog_slide_class','saveSlide'),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
            'link' => $this->context->link,
            'post_key' => 'id_slide',
            'image_baseurl' => _PS_YBC_BLOG_IMG_.'slide/',
            'image_baseurl_thumb' => _PS_YBC_BLOG_IMG_.'slide/thumb/',
            'addNewUrl' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=slide',
            'cancel_url' => $this->baseAdminPath.'&control=slide&list=true'
		);
        
        if(Tools::isSubmit('id_slide') && ($id_slide = (int)Tools::getValue('id_slide')) && ($slide = new Ybc_blog_slide_class($id_slide)) && Validate::isLoadedObject($slide) )
        {
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_slide');
            if($slide->image)
            {             
                $helper->tpl_vars['img_del_link'] = $this->baseAdminPath.'&id_slide='.$id_slide.'&delslideimage=true&control=slide';                
            }
        }
        
		$helper->override_folder = '/';
        $this->_html .= $helper->generateForm(array($fields_form));			
    }
    public function renderExportForm()
    {
        $this->context->smarty->assign(array(
            'errors'=>$this->errors,
            'import_ok'=>$this->import_ok,
        ));
        $this->_html= $this->display(__FILE__,'export.tpl');
    }
    private function _postSlide()
    {
        $errors = array();
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $id_slide = (int)Tools::getValue('id_slide');
        if($id_slide && !Validate::isLoadedObject(new Ybc_blog_slide_class($id_slide)) && !Tools::isSubmit('list'))
            Tools::redirectAdmin($this->baseAdminPath);
        /**
         * Change status 
         */
         if(Tools::isSubmit('change_enabled'))
         {
            $status = (int)Tools::getValue('change_enabled') ?  1 : 0;
            $field = Tools::getValue('field');
            $id_slide = (int)Tools::getValue('id_slide');     
            Hook::exec('actionUpdateBlog', array(
                'id_slide' =>(int)$id_slide,
            ));       
            if($field == 'enabled' && $id_slide)
            {
                Ybc_blog_defines::changeStatus('slide',$field,$id_slide,$status);
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(array(
                        'listId' => $id_slide,
                        'enabled' => $status,
                        'field' => $field,
                        'message' => $this->displaySuccessMessage($this->l('Successfully updated')),
                        'messageType'=>'success',
                        'href' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=slide&change_enabled='.($status ? '0' : '1').'&field='.$field.'&id_slide='.$id_slide,
                    )));
                }
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=slide&list=true');
            }
         }
        /**
         * Delete image 
         */         
         if($id_slide && ($slide = new Ybc_blog_slide_class($id_slide)) && Validate::isLoadedObject($slide) && Tools::isSubmit('delslideimage'))
         {
            $id_lang = (int)Tools::getValue('id_lang');
            if(isset($slide->image[$id_lang]) && $slide->image[$id_lang] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'slide/'.$slide->image[$id_lang]))
            {
                $oldImage = $slide->image[$id_lang];
                $slide->image[$id_lang] = $slide->image[$id_lang_default];                    
                if($slide->update())
                {
                    if(!in_array($oldImage,$slide->image) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'slide/'.$oldImage))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'slide/'.$oldImage);
                }
                Hook::exec('actionUpdateBlog', array(
                    'id_slide' =>(int)$id_slide,
                )); 
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(
                        array(
                            'messageType' => 'success',
                            'message' => $this->displayConfirmation($this->l('Image deleted')),
                        )
                    ));
                }                     
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_slide='.$id_slide.'&control=slide');
            }
            else
                $errors[] = $this->l('Image does not exist');   
         }
        /**
         * Delete slide 
         */ 
         if(Tools::isSubmit('del'))
         {
            $id_slide = (int)Tools::getValue('id_slide');
            Hook::exec('actionUpdateBlog', array(
                'id_slide' =>(int)$id_slide,
            )); 
            if(!(($slide = new Ybc_blog_slide_class($id_slide)) && Validate::isLoadedObject($slide)) )
                $errors[] = $this->l('Slide does not exist');
            elseif($slide->delete())
            {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=slide&list=true');
            }                
            else
                $errors[] = $this->l('Could not delete the slide. Please try again');    
         }
         if(($action = Tools::getValue('action')) && $action=='updateSliderOrdering' && ($slides=Tools::getValue('slides')) && Ybc_blog::validateArray($slides,'isInt'))
         {
            $page = (int)Tools::getValue('page',1);
            if(Ybc_blog_slide_class::updateSliderOrdering($slides,$page))
            {
                die(
                    json_encode(
                        array(
                            'page'=>$page,
                        )
                    )
                );
            }

        }                  
        /**
         * Save slide 
         */
        if(Tools::isSubmit('saveSlide'))
        {            
            if(!($id_slide && ($slide = new Ybc_blog_slide_class($id_slide)) && Validate::isLoadedObject($slide)))
            {
                $slide = new Ybc_blog_slide_class();
                if(!isset($_FILES['image_'.$id_lang_default]['name']) || isset($_FILES['image_'.$id_lang_default]['name']) && !$_FILES['image_'.$id_lang_default]['name'])
                    $errors[] = $this->l('You need to upload an image');
                $slide->sort_order = 1 + (int)Ybc_blog_slide_class::getMaxSortOrder();
            }                
            $slide->enabled = (int)trim(Tools::getValue('enabled',1)) ? 1 : 0;
            $languages = Language::getLanguages(false);
            $caption_default = trim(Tools::getValue('caption_'.Configuration::get('PS_LANG_DEFAULT')));
            if($caption_default=='')
                $errors[] = $this->l('You need to set caption');  
            elseif($caption_default && !Validate::isCleanHtml($caption_default))
                $errors[] = $this->l('Caption is not valid');
            $url_default =trim(Tools::getValue('url_'.Configuration::get('PS_LANG_DEFAULT')));
            if($url_default && !Validate::isCleanHtml($url_default))
                $errors[] = $this->l('Url is not valid');
            if(!$errors)
            {
                foreach ($languages as $language)
    			{
                    $id_lang = (int)$language['id_lang'];
                    $caption = trim(Tools::getValue('caption_'.$id_lang));
                    if($caption && !Validate::isCleanHtml($caption))
                        $errors[] = sprintf($this->l('Caption in %s is not valid'),$language['name']); 
                    else
    			         $slide->caption[$id_lang] = $caption != '' ? $caption :  $caption_default;
                    $url = trim(Tools::getValue('url_'.$id_lang));
                    if($url && !Validate::isCleanHtml($url))
                        $errors[] = sprintf($this->l('url in %s is not valid'),$language['name']);
                    else  
                        $slide->url[$id_lang] = $url != '' ? $url :  $url_default;                           	
                }
            }
                    
            /**
             * Upload image 
             */  
            $oldImages = array();
            $newImages = array();       
            foreach($languages as $language)
            {
                $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024*1024;
                if(isset($_FILES['image_'.$language['id_lang']]['tmp_name']) && isset($_FILES['image_'.$language['id_lang']]['name']) && $_FILES['image_'.$language['id_lang']]['name'])
                {
                    $_FILES['image_'.$language['id_lang']]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES['image_'.$language['id_lang']]['name']);
                    if(!Validate::isFileName($_FILES['image_'.$language['id_lang']]['name']))
                        $errors[] = sprintf($this->l('Image name is not valid in %s'),$language['iso_code']);
                    elseif($_FILES['image_'.$language['id_lang']]['size'] > $max_file_size)
                        $errors[] = sprintf($this->l('Image file is too large. Limit: %s'),Tools::ps_round($max_file_size/1048576,2).'Mb');
                    else
                    {
                        if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'slide/'.$_FILES['image_'.$language['id_lang']]['name']))
                        {
                            $_FILES['image_'.$language['id_lang']]['name'] = $this->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'slide/',$_FILES['image_'.$language['id_lang']]['name']);
                        }                                            
                        $type = Tools::strtolower(Tools::substr(strrchr($_FILES['image_'.$language['id_lang']]['name'], '.'), 1));
            			$imagesize = @getimagesize($_FILES['image_'.$language['id_lang']]['tmp_name']);
            			if (isset($_FILES['image_'.$language['id_lang']]) &&				
            				!empty($_FILES['image_'.$language['id_lang']]['tmp_name']) &&
            				!empty($imagesize) &&
            				in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
            			)
            			{
            				$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');    				
            				if ($error = ImageManager::validateUpload($_FILES['image_'.$language['id_lang']]))
            					$errors[] = $error;
            				elseif (!$temp_name || !move_uploaded_file($_FILES['image_'.$language['id_lang']]['tmp_name'], $temp_name))
            					$errors[] = $this->l('Cannot upload the file in').' '.$language['iso_code'];
            				elseif (!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'slide/'.$_FILES['image_'.$language['id_lang']]['name'], Configuration::get('YBC_BLOG_IMAGE_SLIDER_WIDTH',null,null,null,800), Configuration::get('YBC_BLOG_IMAGE_SLIDER_HEIGHT',null,null,null,470), $type))
            					$errors[] = $this->displayError($this->l('An error occurred during the image upload process in').' '.$language['iso_code']);
            				if (isset($temp_name)  && file_exists($temp_name))
            					@unlink($temp_name);
                            if($slide->image[$language['id_lang']])
                                $oldImages[$language['id_lang']] = $slide->image[$language['id_lang']];
                            $slide->image[$language['id_lang']] = $_FILES['image_'.$language['id_lang']]['name'];	
                            $newImages[$language['id_lang']] = $slide->image[$language['id_lang']];
                        }
                        else
                            $errors[] = sprintf($this->l('Image is not valid in %s'),$language['iso_code']);
                    }
                }
            }
            foreach($languages as $language)
            {
                if(!$slide->image[$language['id_lang']])
                    $slide->image[$language['id_lang']] = $slide->image[$id_lang_default];
            }			
            foreach($languages as $language)
            {
                if(!$slide->image[$language['id_lang']])
                    $slide->image[$language['id_lang']] = $slide->image[$id_lang_default];
            }
            /**
             * Save 
             */    
             
            if(!$errors)
            {
                if (!$id_slide)
    			{
    				if (!$slide->add())
                    {
                        $errors[] = $this->displayError($this->l('The slide could not be added.'));
                        if($newImages)
                        {
                            foreach($newImages as $newImage)
                                if(file_exists((_PS_YBC_BLOG_IMG_DIR_.'slide/'.$newImage)))
                                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'slide/'.$newImage);
                        }                    
                    }
                    else
                    {
                        Hook::exec('actionUpdateBlogImage', array(
                            'id_slide' =>(int)$slide->id,
                            'image' => $newImages ? $slide->image :false,
                            'thumb' => false,
                        ));
                    }                	                    
    			}				
    			elseif (!$slide->update())
                {
                    if($newImages)
                    {
                        foreach($newImages as $newImage)
                            if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'slide/'.$newImage))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'slide/'.$newImage);
                    } 
                    $errors[] = $this->displayError($this->l('The slide could not be updated.'));
                }
                else
                {
                    if($oldImages)
                    {
                        foreach($oldImages as $oldImage)
                            if(!in_array($oldImage,$slide->image) &&  file_exists(_PS_YBC_BLOG_IMG_DIR_.'slide/'.$oldImage))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'slide/'.$oldImage);
                    } 
                    Hook::exec('actionUpdateBlogImage', array(
                        'id_slide' =>(int)$slide->id,
                        'image' => $newImages ? $slide->image :false,
                        'thumb' => false,
                    ));
                }
    			Hook::exec('actionUpdateBlog', array(
                    'id_slide' =>(int)$slide->id,
                )); 		                
            }
         }
         $changedImages = array();
         if(isset($newImages) && $newImages &&  !$errors && isset($slide)){
            foreach($newImages as $id_lang=>$newImage)
            {
                $changedImages[] = array(
                    'name' => 'image_'.$id_lang,
                    'url' => _PS_YBC_BLOG_IMG_.'slide/'.$newImage,                    
                );
            }
            
         }
         if (count($errors))
         {
            if($newImages)
            {
                foreach($newImages as $newImage)
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'slide/'.$newImage))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'slide/'.$newImage);
            } 
            $this->errorMessage = $this->displayError($errors);  
         }
         if(Tools::isSubmit('ajax'))
         {
            die(json_encode(
                array(
                    'messageType' => $errors ? 'error' : 'success',
                    'message' => $errors ? $this->errorMessage : $this->displaySuccessMessage($this->l('Slider saved'),$this->l('View slider on blog page'),$this->getLink('blog')),
                    'images' => isset($changedImages) && $changedImages ? $changedImages : array(),
                    'postUrl' => !$errors && Tools::isSubmit('saveSlide') && !(int)$id_slide ? $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_slide='.Ybc_blog_defines::getMaxId('slide','id_slide').'&control=slide' : 0,
                    'itemKey' => 'id_slide',
                    'itemId' => !$errors && Tools::isSubmit('saveSlide') && !(int)$id_slide ? Ybc_blog_defines::getMaxId('slide','id_slide') : ((int)$id_slide > 0 ? (int)$id_slide : 0),
                )
            ));
         } 
         if (!$errors && Tools::isSubmit('saveSlide') && Tools::isSubmit('id_slide'))
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_slide='.$id_slide.'&control=slide');
		 elseif (!$errors && Tools::isSubmit('saveSlide'))
         {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=3&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_slide='.Ybc_blog_defines::getMaxId('slide','id_slide').'&control=slide');
         }
    }
    public function _postExport()
    {
        if(Tools::isSubmit('submitExportBlog'))
        {
            $import= new Ybc_Blog_ImportExport();
            $import->generateArchive();
        }
        if(Tools::isSubmit('submitImportBlog'))
        {
            if(!is_dir(_YBC_BLOG_CACHE_DIR_))
                mkdir(_YBC_BLOG_CACHE_DIR_,'0755');
            $import= new Ybc_Blog_ImportExport();
            $data_import = Tools::getValue('data_import');
            $params = array(
                'data_import'=> $data_import && is_array($data_import) && Ybc_blog::validateArray($data_import) ? $data_import : array(),
                'importoverride' => (int)Tools::getValue('importoverride'),
                'keepauthorid' => (int)Tools::getValue('keepauthorid'),
                'keepcommenter' => (int)Tools::getValue('keepcommenter'),
            );
            $this->context->smarty->assign($params);
            $errors =$import->processImport(false,$params);
            if($errors)            
                $this->errors=$errors;
            else
                $this->import_ok=true;                                                                  
        }
        if(Tools::isSubmit('submitImportBlogWP'))
        {
            if(!is_dir(_YBC_BLOG_CACHE_DIR_))
                mkdir(_YBC_BLOG_CACHE_DIR_,'0755');
            $import= new Ybc_Blog_ImportExport();
            $errors =$import->processImportWordPress((int)Tools::getValue('importoverridewp'));
            if($errors)            
                $this->errors=$errors;
            else
                $this->import_ok=true;                                                                  
        }
    }
    
    /**
     * Gallery 
     */
    public function renderGalleryForm()
    {
        //List
        $list = trim(Tools::getValue('list')); 
        if($list=='true')
        {
            $fields_list = array(
                'id_gallery' => array(
                    'title' => $this->l('Id'),
                    'width' => 40,
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true,
                ),
                'thumb' => array(
                    'title' => $this->l('Thumbnail'),
                    'type' => 'text',
                    'required' => true                        
                ), 
                'title' => array(
                    'title' => $this->l('Name'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true
                ),
                'description' => array(
                    'title' => $this->l('Description'),
                    'type' => 'text',
                    'sort' => true,
                    'filter' => true
                ),
                'sort_order' => array(
                    'title' => $this->l('Sort order'),
                    'type' => 'text',                        
                    'sort' => true,
                    'filter' => true,
                    'update_position' => true,                        
                ),  
                'is_featured' => array(
                    'title' => $this->l('Featured'),
                    'type' => 'active',
                    'sort' => true,
                    'filter' => true,
                    'strip_tag' => false,
                    'filter_list' => array(
                        'id_option' => 'enabled',
                        'value' => 'title',
                        'list' => array(
                            0 => array(
                                'enabled' => 1,
                                'title' => $this->l('Yes')
                            ),
                            1 => array(
                                'enabled' => 0,
                                'title' => $this->l('No')
                            )
                        )
                    )
                ),                
                'enabled' => array(
                    'title' => $this->l('Enabled'),
                    'type' => 'active',
                    'sort' => true,
                    'filter' => true,
                    'strip_tag' => false,
                    'filter_list' => array(
                        'id_option' => 'enabled',
                        'value' => 'title',
                        'list' => array(
                            0 => array(
                                'enabled' => 1,
                                'title' => $this->l('Yes')
                            ),
                            1 => array(
                                'enabled' => 0,
                                'title' => $this->l('No')
                            )
                        )
                    )
                ),
            );
            //Filter
            $filter = "";
            if(($id = trim(Tools::getValue('id_gallery')))!='' && Validate::isCleanHtml($id))
                $filter .= " AND g.id_gallery = ".(int)$id;
            if(($sort_order =trim(Tools::getValue('sort_order')))!='' && Validate::isCleanHtml($sort_order))
                $filter .= " AND g.sort_order = ".(int)$sort_order;                
            if(($title = trim(Tools::getValue('title')))!='' && Validate::isCleanHtml($title))
                $filter .= " AND gl.title like '%".pSQL($title)."%'";
            if(($description = trim(Tools::getValue('description')))!='' && Validate::isCleanHtml($description))
                $filter .= " AND gl.description like '%".pSQL($description)."%'";
            if(($enabled = trim(Tools::getValue('enabled')))!='' && Validate::isCleanHtml($enabled))
                $filter .= " AND g.enabled =".(int)$enabled;
            if(($is_featured = trim(Tools::getValue('is_featured')))!='' && Validate::isCleanHtml($is_featured))
                $filter .= " AND g.is_featured =".(int)$is_featured;
            if($filter) 
                $show_reset = true;
            else
                $show_reset = false;
            //Sort
            $sort = "";
            $sort_post = Tools::strtolower(trim(Tools::getValue('sort')));
            $sort_type = Tools::strtolower(Tools::getValue('sort_type','desc'));
            if(!in_array($sort_type,array('desc','asc')))
                $sort_type ='desc';
            if($sort_post && isset($fields_list[$sort_post]))
            {
                $sort .= $sort_post." ".($sort_type=='asc' ? ' ASC ' :' DESC ')." , ";
            }
            else
                $sort = 'g.sort_order asc,';
            
            //Paggination
            $page = (int)Tools::getValue('page');
            if($page<=1)
                $page =1;
            $totalRecords = (int)Ybc_blog_gallery_class::countGalleriesWithFilter($filter);
            $paggination = new Ybc_blog_paggination_class();            
            $paggination->total = $totalRecords;
            $paggination->url = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=gallery&list=true&page=_page_'.$this->getUrlExtra($fields_list);
            $paggination->limit =  (int)Tools::getValue('paginator_ybc_gallery_select_limit',20);
            $paggination->name ='ybc_gallery';
            $totalPages = ceil($totalRecords / $paggination->limit);
            if($page > $totalPages)
                $page = $totalPages;
            $paggination->page = $page;
            $start = $paggination->limit * ($page - 1);
            if($start < 0)
                $start = 0;
            $galleries = Ybc_blog_gallery_class::getGalleriesWithFilter($filter, $sort, $start, $paggination->limit);
            if($galleries)
            {
                foreach($galleries as &$gallery)
                {
                    if($gallery['thumb'] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$gallery['thumb']))
                    {
                        $gallery['thumb'] = array(
                            'image_field' => true,
                            'img_url' =>  _PS_YBC_BLOG_IMG_.'gallery/thumb/'.$gallery['thumb'],
                        );
                    }
                    elseif($gallery['image'] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$gallery['image']))
                    {
                        $gallery['thumb'] = array(
                            'image_field' => true,
                            'img_url' =>  _PS_YBC_BLOG_IMG_.'gallery/'.$gallery['image'],
                        );
                    }
                    else
                        $gallery['thumb']=array();
                }
            }
            $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
            $paggination->style_links = $this->l('links');
            $paggination->style_results = $this->l('results');
            $listData = array(
                'name' => 'ybc_gallery',
                'actions' => array('edit', 'delete', 'view'),
                'currentIndex' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=gallery'.($paggination->limit!=20 ? '&paginator_ybc_gallery_select_limit='.$paggination->limit:''),
                'identifier' => 'id_gallery',
                'show_toolbar' => true,
                'show_action' => true,
                'title' => $this->l('Blog gallery'),
                'fields_list' => $fields_list,
                'field_values' => $galleries,
                'paggination' => $paggination->render(),
                'filter_params' => $this->getFilterParams($fields_list),
                'show_reset' => $show_reset,
                'totalRecords' => $totalRecords,
                'preview_link' => $this->getLink('gallery'),
                'sort' => $sort_post ? : 'sort_order',
                'sort_type'=>$sort_type,
            );            
            return $this->_html .= $this->renderList($listData);      
        }
        //Form
        
        $fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Blog gallery'),				
				),
				'input' => array(					
					array(
						'type' => 'text',
						'label' => $this->l('Name'),
						'name' => 'title',
						'lang' => true,    
                        'required' => true                    
					),    
                    array(
						'type' => 'textarea',
						'label' => $this->l('Caption'),
						'name' => 'description',
						'lang' => true,  
                        'autoload_rte' => true                      
					),  
                    array(
						'type' => 'file_lang',
						'label' => $this->l('Thumbnail image'),
						'name' => 'thumb',
                        'imageType' => 'thumb',
                        'required' => true,
                        'desc' => sprintf($this->l('Accepted formats: jpg, jpeg, png, gif. Limit: %dMb. Recommended size: %sx%s.'),Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),Configuration::get('YBC_BLOG_IMAGE_GALLERY_THUHMB_WIDTH',null,null,null,180),Configuration::get('YBC_BLOG_IMAGE_GALLERY_THUHMB_HEIGHT',null,null,null,180)),						
					),                   
                    array(
						'type' => 'file_lang',
						'label' => $this->l('Large Image'),
						'name' => 'image',
                        'required' => true,
                        'desc' => sprintf($this->l('Accepted formats: jpg, jpeg, png, gif. Limit: %dMb. Recommended size: %sx%s.'),Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),Configuration::get('YBC_BLOG_IMAGE_GALLERY_WIDTH',null,null,null,600),Configuration::get('YBC_BLOG_IMAGE_GALLERY_HEIGHT',null,null,null,600)),                        						
					),
                    array(
						'type' => 'switch',
						'label' => $this->l('Featured'),
						'name' => 'is_featured',
                        'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
                        'desc' => $this->l('Enable if you want to display this image in the featured gallery block on the front office')					
					),
                    array(
						'type' => 'switch',
						'label' => $this->l('Enabled'),
						'name' => 'enabled',
                        'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						)					
					),
                    array(
                        'type' => 'hidden', 
                        'name' => 'control'
                    )
                ),
                'submit' => array(
					'title' => $this->l('Save'),
				)
            ),
		);
        $helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'saveGallery';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
			'fields_value' => $this->getFieldsValues(Ybc_blog_defines::$galleryFields,'id_gallery','Ybc_blog_gallery_class','saveGallery'),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
            'link' => $this->context->link,
            'cancel_url' => $this->baseAdminPath.'&control=gallery&list=true',
            'post_key' => 'id_gallery',
            'image_baseurl' => _PS_YBC_BLOG_IMG_.'gallery/',
            'image_baseurl_thumb' => _PS_YBC_BLOG_IMG_.'gallery/thumb/',
            'addNewUrl' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=gallery',                   
            
		);
        
        if(Tools::isSubmit('id_gallery') && ($id_gallery = (int)Tools::getValue('id_gallery')) && ($gallery = new Ybc_blog_gallery_class($id_gallery)) && Validate::isLoadedObject($gallery) )
        {
            
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_gallery');
            if($gallery->image)
            {             
                $helper->tpl_vars['img_del_link'] = $this->baseAdminPath.'&id_gallery='.$id_gallery.'&delgalleryimage=true&control=gallery';                
            }
            if($gallery->thumb)
            {             
                $helper->tpl_vars['thumb_del_link'] = $this->baseAdminPath.'&id_gallery='.$id_gallery.'&delgallerythumb=true&control=gallery';                
            }
        }
        
		$helper->override_folder = '/';
        $this->_html .= $helper->generateForm(array($fields_form));			
    }
    private function _postGallery()
    {
        $errors = array();
        $id_gallery = (int)Tools::getValue('id_gallery');
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        if($id_gallery && !Validate::isLoadedObject(new Ybc_blog_gallery_class($id_gallery)) && !Tools::isSubmit('list'))
            Tools::redirectAdmin($this->baseAdminPath);
        /**
         * Change status 
         */
         if(Tools::isSubmit('change_enabled'))
         {
            $status = (int)Tools::getValue('change_enabled') ?  1 : 0;
            $field = Tools::getValue('field');
            $id_gallery = (int)Tools::getValue('id_gallery');  
            Hook::exec('actionUpdateBlog', array(
                'id_gallery' =>(int)$id_gallery,
            ));           
            if(($field == 'enabled' || $field=='is_featured') && $id_gallery)
            {
                Ybc_blog_defines::changeStatus('gallery',$field,$id_gallery,$status);
                if($field=='enabled')
                {
                    if($status==1)
                        $title = $this->l('Click to unmark featured');
                    else
                        $title = $this->l('Click to mark as featured');
                }
                else
                {
                    if($status==1)
                        $title = $this->l('Click to unmark disabled');
                    else
                        $title = $this->l('Click to mark as enabled');
                }
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(array(
                        'listId' => $id_gallery,
                        'enabled' => $status,
                        'field' => $field,
                        'message' => $field=='enabled' ? $this->displaySuccessMessage($this->l('The status has been successfully updated')) : $this->displaySuccessMessage($this->l('The feature has been successfully updated')),
                        'messageType'=>'success',
                        'title'=>$title,
                        'href' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=gallery&change_enabled='.($status ? '0' : '1').'&field='.$field.'&id_gallery='.$id_gallery,
                    )));
                }
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=gallery&list=true');
            }
         }
        /**
         * Delete image 
         */         
         if($id_gallery && ($gallery = new Ybc_blog_gallery_class($id_gallery)) && Validate::isLoadedObject($gallery) && Tools::isSubmit('delgalleryimage'))
         {
            $id_lang = (int)Tools::getValue('id_lang');
            Hook::exec('actionUpdateBlog', array(
                'id_gallery' =>(int)$id_gallery,
            ));
            if(isset($gallery->image[$id_lang]) && $gallery->image[$id_lang] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$gallery->image[$id_lang]))
            {
                $oldImage = $gallery->image[$id_lang];
                $gallery->image[$id_lang] = $gallery->image[$id_lang_default];                    
                if($gallery->update())
                {
                    if(!in_array($oldImage,$gallery->image) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$oldImage))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$oldImage);
                        
                }
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(
                        array(
                            'messageType' => 'success',
                            'message' => $this->displayConfirmation($this->l('Image has been deleted')),
                        )
                    ));
                }                 
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_gallery='.$id_gallery.'&control=gallery');
            }
            else
                $errors[] = $this->l('Image is empty'); 
             
         }
        /**
         * Delete gallery 
         */ 
         if(Tools::isSubmit('del'))
         {
            $id_gallery = (int)Tools::getValue('id_gallery');
            Hook::exec('actionUpdateBlog', array(
                'id_gallery' =>(int)$id_gallery,
            )); 
            if(!(($gallery = new Ybc_blog_gallery_class($id_gallery)) &&  Validate::isLoadedObject($gallery)))
                $errors[] = $this->l('Item does not exist');
            elseif($gallery->delete())
            {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=gallery&list=true');
            }                
            else
                $errors[] = $this->l('Could not delete the item. Please try again');    
         }
         // update sort_order
         if(($action = Tools::getValue('action')) && $action=='updateGalleryOrdering' && ($galleries=Tools::getValue('galleries')))
         {
            $page = (int)Tools::getValue('page',1);
            if(Ybc_blog_gallery_class::updateGalleryOrdering($galleries,$page))
            {
                die(
                    json_encode(
                        array(
                            'page'=>$page,
                        )
                    )
                );
            }
         }
        /**
         * Save gallery 
         */
        if(Tools::isSubmit('saveGallery'))
        {            
            if(!($id_gallery && ($gallery = new Ybc_blog_gallery_class($id_gallery)) && Validate::isLoadedObject($gallery)))
            {
                $gallery = new Ybc_blog_gallery_class();  
                $gallery->sort_order = 1 + (int)Ybc_blog_gallery_class::getMaxSortOrder();
            }                
            $gallery->enabled = (int)trim(Tools::getValue('enabled',1)) ? 1 : 0;
            $gallery->is_featured = (int)trim(Tools::getValue('is_featured',1)) ? 1 : 0;
            $languages = Language::getLanguages(false);
            $title_default = trim(Tools::getValue('title_'.Configuration::get('PS_LANG_DEFAULT')));
            if($title_default=='')
                $errors[] = $this->l('Name is required'); 
            elseif($title_default && !Validate::isCleanHtml($title_default))
                 $errors[] = $this->l('Name is not valid');
            $description_default = trim(Tools::getValue('description_'.Configuration::get('PS_LANG_DEFAULT')));
            if($description_default && !Validate::isCleanHtml($description_default,true))
                $errors[] = $this->l('Description is not valid');
            if(!$errors)
            {
                foreach ($languages as $language)
    			{	
                    $title = trim(Tools::getValue('title_'.$language['id_lang']));
                    if($title && !Validate::isCleanHtml($title))
                        $errors[] = sprintf($this->l('Name in %s is not valid'),$language['name']);
                    else
    		          $gallery->title[$language['id_lang']] = $title != '' ? $title : $title_default;
                    $description = trim(Tools::getValue('description_'.$language['id_lang']));
                    if($description && !Validate::isCleanHtml($description,true))
                        $errors[] = sprintf($this->l('Description in %s is not valid'),$language['name']);
                    else
                        $gallery->description[$language['id_lang']] = $description != '' ? $description :  $description_default;
                }
            }           
            /**
             * Upload image 
             */  
            $oldImages = array();
            $newImages = array();       
            $newThumbs = array();
            $oldThumbs = array();
            if(!$id_gallery && (!isset($_FILES['image_'.$id_lang_default]['name']) || !$_FILES['image_'.$id_lang_default]['name']))
                $errors[] = $this->l('Image is required');
            if(!$id_gallery && (!isset($_FILES['thumb_'.$id_lang_default]['name']) || !$_FILES['thumb_'.$id_lang_default]['name']))
                $errors[] = $this->l('Thumbnail is required');
            foreach($languages as $language)
            {
                $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024*1024;
                if(isset($_FILES['image_'.$language['id_lang']]['tmp_name']) && isset($_FILES['image_'.$language['id_lang']]['name']) && $_FILES['image_'.$language['id_lang']]['name'])
                {
                    $_FILES['image_'.$language['id_lang']]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES['image_'.$language['id_lang']]['name']);
                    if(!Validate::isFileName($_FILES['image_'.$language['id_lang']]['name']))
                        $errors[] = sprintf($this->l('Image name is not valid in %s'),$language['iso_code']);
                    elseif($_FILES['image_'.$language['id_lang']]['size'] > $max_file_size)
                        $errors[] = sprintf($this->l('Image file is too large. Limit: %s'),Tools::ps_round($max_file_size/1048576,2).'Mb');
                    else
                    {
                        if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$_FILES['image_'.$language['id_lang']]['name']))
                        {
                            $_FILES['image_'.$language['id_lang']]['name'] = $this->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'gallery/',$_FILES['image_'.$language['id_lang']]['name']);
                        }                    
                        $type = Tools::strtolower(Tools::substr(strrchr($_FILES['image_'.$language['id_lang']]['name'], '.'), 1));
            			$imagesize = @getimagesize($_FILES['image_'.$language['id_lang']]['tmp_name']);
            			if (isset($_FILES['image_'.$language['id_lang']]) &&				
            				!empty($_FILES['image_'.$language['id_lang']]['tmp_name']) &&
            				!empty($imagesize) &&
            				in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
            			)
            			{
            				$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');    				
            				if ($error = ImageManager::validateUpload($_FILES['image_'.$language['id_lang']]))
            					$errors[] = $error;
            				elseif (!$temp_name || !move_uploaded_file($_FILES['image_'.$language['id_lang']]['tmp_name'], $temp_name))
            					$errors[] = $this->l('Cannot upload the file in').' '.$language['iso_code'];
            				elseif(!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'gallery/'.$_FILES['image_'.$language['id_lang']]['name'], Configuration::get('YBC_BLOG_IMAGE_GALLERY_WIDTH',null,null,null,600), Configuration::get('YBC_BLOG_IMAGE_GALLERY_HEIGHT',null,null,null,600), $type))
            					$errors[] = $this->displayError($this->l('An error occurred during the image upload process in').' '.$language['iso_code']);
                            if($gallery->image[$language['id_lang']])
                            {
                                $oldImages[$language['id_lang']] =$gallery->image[$language['id_lang']];
                            }                                
                            $gallery->image[$language['id_lang']] = $_FILES['image_'.$language['id_lang']]['name'];
                            $newImages[$language['id_lang']] = $gallery->image[$language['id_lang']];
                            if (isset($temp_name) && file_exists($temp_name))
            					@unlink($temp_name);		
            			}
                        else
                            $errors[] = sprintf($this->l('Image is not valid in %s'),$language['iso_code']);
                    }
                    
                }			
                if(isset($_FILES['thumb_'.$language['id_lang']]['tmp_name']) && isset($_FILES['thumb_'.$language['id_lang']]['name']) && $_FILES['thumb_'.$language['id_lang']]['name'])
                {
                    $_FILES['thumb_'.$language['id_lang']]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES['thumb_'.$language['id_lang']]['name']);
                    if(!Validate::isFileName($_FILES['thumb_'.$language['id_lang']]['name']))
                        $errors[] = sprintf($this->l('Thumbnail image name is not valid in %s'),$language['iso_code']);
                    elseif($_FILES['thumb_'.$language['id_lang']]['size'] > $max_file_size)
                        $errors[] = sprintf($this->l('Thumbnail image file is too large. Limit: %s'),Tools::ps_round($max_file_size/1048576,2).'Mb');
                    else
                    {
                        if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$_FILES['thumb_'.$language['id_lang']]['name']))
                        {
                            $_FILES['thumb_'.$language['id_lang']]['name'] = $this->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/',$_FILES['thumb_'.$language['id_lang']]['name']);
                        }                    
                        $type = Tools::strtolower(Tools::substr(strrchr($_FILES['thumb_'.$language['id_lang']]['name'], '.'), 1));
            			$imagesize = @getimagesize($_FILES['thumb_'.$language['id_lang']]['tmp_name']);
            			if (isset($_FILES['thumb_'.$language['id_lang']]) &&				
            				!empty($_FILES['thumb_'.$language['id_lang']]['tmp_name']) &&
            				!empty($imagesize) &&
            				in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
            			)
            			{
            				$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');    				
            				if ($error = ImageManager::validateUpload($_FILES['thumb_'.$language['id_lang']]))
            					$errors[] = $error;
            				elseif (!$temp_name || !move_uploaded_file($_FILES['thumb_'.$language['id_lang']]['tmp_name'], $temp_name))
            					$errors[] = $this->l('Cannot upload the file in').' '.$language['iso_code'];
            				elseif(!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$_FILES['thumb_'.$language['id_lang']]['name'], Configuration::get('YBC_BLOG_IMAGE_GALLERY_THUHMB_WIDTH',null,null,null,180), Configuration::get('YBC_BLOG_IMAGE_GALLERY_THUHMB_HEIGHT',null,null,null,180), $type))
            					$errors[] = $this->displayError($this->l('An error occurred during the image thumbnail upload process in').' '.$language['iso_code']);
                            if($gallery->thumb[$language['id_lang']])
                            {
                                $oldThumbs[$language['id_lang']] = $gallery->thumb[$language['id_lang']];
                            }                                
                            $gallery->thumb[$language['id_lang']] = $_FILES['thumb_'.$language['id_lang']]['name'];
                            $newThumbs[$language['id_lang']] = $gallery->thumb[$language['id_lang']];
                            if (isset($temp_name) && file_exists($temp_name))
            					@unlink($temp_name);		
            			}
                        else
                            $errors[] = sprintf($this->l('Thumbnail image is not valid in %s'),$language['iso_code']);
                    }
                    
                }
            }
            foreach($languages as $language)
            {
                if(!$gallery->image[$language['id_lang']])
                    $gallery->image[$language['id_lang']] = $gallery->image[$id_lang_default];
                if(!$gallery->thumb[$language['id_lang']])
                    $gallery->thumb[$language['id_lang']] = $gallery->thumb[$id_lang_default];
            }			
            /**
             * Save 
             */    
             
            if(!$errors)
            {
                if (!$id_gallery)
    			{
    				if (!$gallery->add())
                    {
                        $errors[] = $this->displayError($this->l('The item could not be added.'));
                        if($newImages)
                        {
                            foreach($newImages as $newImage)
                            {
                                if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$newImage))
                                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$newImage);
                            }
                        }  
                        if($newThumbs)
                            foreach($newThumbs as $newThumb)
                            {
                                if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$newThumb))
                                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$newThumb);
                            }
                    } 
                    else
                    {
                        Hook::exec('actionUpdateBlogImage', array(
                            'id_gallery' =>(int)$gallery->id,
                            'image' => $newImages ? $gallery->image :false,
                            'thumb' => $newThumbs ? $gallery->thumb : false,
                        ));
                    }               	                    
    			}				
    			elseif (!$gallery->update())
                {
                    if($newImages)
                    {
                        foreach($newImages as $newImage)
                        {
                            if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$newImage))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$newImage);
                        }
                    }  
                    if($newThumbs)
                    {
                        foreach($newThumbs as $newThumb)
                        {
                            if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$newThumb))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$newThumb);
                        }
                    }
                    $errors[] = $this->displayError($this->l('The item could not be updated.'));
                }
                else
                {
                    if($oldImages)
                    {
                        foreach($oldImages as $oldImage)
                            if(!in_array($oldImage,$gallery->image) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$oldImage))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$oldImage);
                    }  
                    if($oldThumbs)
                        foreach($oldThumbs as $oldThumb)
                            if(!in_array($oldThumb,$gallery->thumb) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$oldThumb))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$oldThumb);
                    Hook::exec('actionUpdateBlogImage', array(
                        'id_gallery' =>(int)$gallery->id,
                        'image' => $newImages ? $gallery->image :false,
                        'thumb' => $newThumbs ? $gallery->thumb : false,
                    ));
                }
    			Hook::exec('actionUpdateBlog', array(
                    'id_gallery' =>(int)$gallery->id,
                ));		                
            }
         }
         $changedImages = array();
         if(isset($newImages) && $newImages && !$errors && isset($gallery)){
            
            foreach($newImages as $id_lang=>$newImage)
            {
                $changedImages[] = array(
                    'name' => 'image_'.$id_lang,
                    'url' => _PS_YBC_BLOG_IMG_.'gallery/'.$newImage,                    
                );
            }
         }
         if(isset($newThumbs) && $newThumbs && !$errors && isset($gallery)){
            foreach($newThumbs as $id_lang=> $newThumb)
            {
                $changedImages[] = array(
                    'name' => 'thumb_'.$id_lang,
                    'url' => _PS_YBC_BLOG_IMG_.'gallery/thumb/'.$newThumb,                    
                );
            }    
         }
         if(Tools::isSubmit('ajax'))
         {
            die(json_encode(
                array(
                    'messageType' => $errors ? 'error' : 'success',
                    'message' => $errors ? $this->displayError($errors) : $this->displaySuccessMessage($this->l('Gallery image saved'),$this->l('View blog gallery'),$this->getLink('gallery')),
                    'images' => isset($changedImages) && $changedImages ? $changedImages : array(),
                    'postUrl' => !$errors && Tools::isSubmit('saveGallery') && !(int)$id_gallery ? $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_gallery='.Ybc_blog_defines::getMaxId('gallery','id_gallery').'&control=gallery' : 0,
                    'itemKey' => 'id_gallery',
                    'itemId' => !$errors && Tools::isSubmit('saveGallery') && !(int)$id_gallery ? Ybc_blog_defines::getMaxId('gallery','id_gallery') : ((int)$id_gallery > 0 ? (int)$id_gallery : 0),
                )
            ));
         } 
         if(count($errors))
         {
            if($newImages)
            {
                foreach($newImages as $newImage)
                {
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$newImage))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$newImage);
                }
            }  
            if($newThumbs)
                foreach($newThumbs as $newThumb)
                {
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$newThumb))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$newThumb);
                }
            $this->errorMessage = $this->displayError($errors);  
         }
         elseif (Tools::isSubmit('saveGallery') && $id_gallery)
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_gallery='.$id_gallery.'&control=gallery');
		 elseif (Tools::isSubmit('saveGallery'))
         {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=3&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id_gallery='.Ybc_blog_defines::getMaxId('gallery','id_gallery').'&control=gallery');
         }
    }
    public function hookModuleRoutes($params) {
        $subfix = (int)Configuration::get('YBC_BLOG_URL_SUBFIX') ? '.html' : '';
        $blogAlias = Configuration::get('YBC_BLOG_ALIAS',$this->context->language->id) ? : Configuration::get('YBC_BLOG_ALIAS',Configuration::get('PS_LANG_DEFAULT'));
        if(!$blogAlias)
            return array();
        $routes = array(
            'authorall' => array(
                'controller' => 'author',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR',$this->context->language->id)) ? $subAlias : 'author'),
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'authorallpage' => array(
                'controller' => 'author',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR',$this->context->language->id)) ? $subAlias : 'author').'/{page}',
                'keywords' => array(
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'ybcblogmainpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'ybcblogfeaturedpostspage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/{page}',
                'keywords' => array(
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),  
            'ybcblogpostcomment' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_POST',$this->context->language->id)) ? $subAlias : 'post').'/{id_post}-{edit_comment}-{url_alias}'.$subfix,
                'keywords' => array(
                    'id_post' =>    array('regexp' => '[0-9]+', 'param' => 'id_post'),
                    'edit_comment' => array('regexp' => '[0-9]+', 'param' => 'edit_comment'),
                    'url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]+','param' => 'url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),  
            'ybcblogpostallcomments' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_POST',$this->context->language->id)) ? $subAlias : 'post').'/allcomments/{id_post}-{url_alias}'.$subfix,
                'keywords' => array(
                    'id_post' =>    array('regexp' => '[0-9]+', 'param' => 'id_post'),
                    'url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]+','param' => 'url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'all_comment'=>1,
                ),
            ),   
            'ybcblogpost2' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_POST',$this->context->language->id)) ? $subAlias : 'post').'/{id_post}-{url_alias}'.$subfix,
                'keywords' => array(
                    'url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]+','param' => 'url_alias'),
                    'id_post' =>    array('regexp' => '[0-9]+', 'param' => 'id_post'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),         
            'ybcblogpost' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_POST',$this->context->language->id)) ? $subAlias : 'post').'/{post_url_alias}'.$subfix,
                'keywords' => array(
                    'post_url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]+','param' => 'post_url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogpostpage2' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORY',$this->context->language->id)) ? $subAlias : 'category').'/{page}/{id_category}-{url_alias}'.$subfix,
                'keywords' => array(
                    'id_category' =>    array('regexp' => '[0-9]+', 'param' => 'id_category'),
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                    'url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]+','param' => 'url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogpostpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORY',$this->context->language->id)) ? $subAlias : 'category').'/{page}/{category_url_alias}'.$subfix,
                'keywords' => array(
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                    'category_url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]+','param' => 'category_url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogpost2' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORY',$this->context->language->id)) ? $subAlias : 'category').'/{id_category}-{url_alias}'.$subfix,
                'keywords' => array(
                    'id_category' =>    array('regexp' => '[0-9]+', 'param' => 'id_category'),
                    'url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]+','param' => 'url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogpost' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORY',$this->context->language->id)) ? $subAlias : 'category').'/{category_url_alias}'.$subfix,
                'keywords' => array(
                    'category_url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]+','param' => 'category_url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'authorblogpostpage2' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR2',$this->context->language->id)) ? $subAlias : 'community-author').'/{page}/{id_author}-{author_name}',
                'keywords' => array(
                    'id_author' =>    array('regexp' => '[0-9]+', 'param' => 'id_author'),
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                    'author_name'       =>   array('regexp' => '(.)+','param' => 'author_name'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'is_customer'=>1,
                ),
            ),
            'authorblogpostpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR',$this->context->language->id)) ? $subAlias : 'author').'/{page}/{id_author}-{author_name}',
                'keywords' => array(
                    'id_author' =>    array('regexp' => '[0-9]+', 'param' => 'id_author'),
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                    'author_name'       =>   array('regexp' => '(.)+','param' => 'author_name'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'authorblogpost2' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR2',$this->context->language->id)) ? $subAlias : 'community-author').'/{id_author}-{author_name}',
                'keywords' => array(
                    'id_author' =>    array('regexp' => '[0-9]+', 'param' => 'id_author'),
                    'author_name'       =>   array('regexp' => '(.)+','param' => 'author_name'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'is_customer'=>1,
                ),
            ),
            'authorblogpost' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR',$this->context->language->id)) ? $subAlias : 'author').'/{id_author}-{author_name}',
                'keywords' => array(
                    'id_author' =>    array('regexp' => '[0-9]+', 'param' => 'id_author'),
                    'author_name'       =>   array('regexp' => '(.)+','param' => 'author_name'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogtagpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/{page}/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_TAG',$this->context->language->id)) ? $subAlias : 'tag').'/{tag}',
                'keywords' => array(
                    'tag'       =>   array('regexp' => '.+','param' => 'tag'),
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogtag' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_TAG',$this->context->language->id)) ? $subAlias : 'tag').'/{tag}',
                'keywords' => array(
                    'tag'       =>   array('regexp' => '.+','param' => 'tag'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categorybloglatestpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_LATEST',$this->context->language->id)) ? $subAlias : 'latest').'/{page}',
                'keywords' => array(                       
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'latest' => 'true'
                ),
            ),
            'categorybloglatest' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_LATEST',$this->context->language->id)) ? $subAlias : 'latest'),
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'latest' => 'true'
                ),
            ),
            'categoryblogpopulartpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_POPULAR',$this->context->language->id)) ? $subAlias : 'popular').'/{page}',
                'keywords' => array(                       
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'popular' => 'true'
                ),
            ),
            'categoryblogpopular' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_POPULAR',$this->context->language->id)) ? $subAlias : 'popular'),
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'popular' => 'true'
                ),
            ),
            'categoryblogfeaturedpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_FEATURED',$this->context->language->id)) ? $subAlias : 'featured').'/{page}',
                'keywords' => array(                       
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'featured' => 'true'
                ),
            ),
            'categoryblogfeatured' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_FEATURED',$this->context->language->id)) ? $subAlias : 'featured'),
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'featured' => 'true'
                ),
            ),
            'categoryblogsearchpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/{page}/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_SEARCH',$this->context->language->id)) ? $subAlias : 'search').'/{search}',
                'keywords' => array(
                    'search'       =>   array('regexp' => '.+','param' => 'search'),
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogsearch' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_SEARCH',$this->context->language->id)) ? $subAlias : 'search').'/{search}',
                'keywords' => array(
                    'search'       =>   array('regexp' => '.+','param' => 'search'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogyearpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_YEARS',$this->context->language->id)) ? $subAlias : 'year').'/{year}/{page}',
                'keywords' => array(
                    'year'       =>   array('regexp' => '[0-9]+','param' => 'year'),
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogyear' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_YEARS',$this->context->language->id)) ? $subAlias : 'year').'/{year}',
                'keywords' => array(
                    'year'       =>   array('regexp' => '[0-9]+','param' => 'year'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogmonthpage' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_MONTHS',$this->context->language->id)) ? $subAlias : 'month').'/{month}/{year}/{page}',
                'keywords' => array(
                    'month'       =>   array('regexp' => '[0-9]+','param' => 'month'),
                    'year'       =>   array('regexp' => '[0-9]+','param' => 'year'),
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogmonth' => array(
                'controller' => 'blog',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_MONTHS',$this->context->language->id)) ? $subAlias : 'month').'/{month}/{year}',
                'keywords' => array(
                    'month'       =>   array('regexp' => '[0-9]+','param' => 'month'),
                    'year'       =>   array('regexp' => '[0-9]+','param' => 'year'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categorybloggallerypage' => array(
                'controller' => 'gallery',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_GALLERY',$this->context->language->id)) ? $subAlias : 'gallery').'/{page}',
                'keywords' => array(
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categorybloggallery' => array(
                'controller' => 'gallery',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_GALLERY',$this->context->language->id)) ? $subAlias : 'gallery'),
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogcommentspage' => array(
                'controller' => 'comment',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_COMMENTS',$this->context->language->id)) ? $subAlias : 'comments').'/{page}',
                'keywords' => array(
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogcategoriespage' => array(
                'controller' => 'category',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORIES',$this->context->language->id)) ? $subAlias : 'categories').'/{page}',
                'keywords' => array(
                    'page' =>    array('regexp' => '[0-9]+', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogcomments' => array(
                'controller' => 'comment',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_COMMENTS',$this->context->language->id)) ? $subAlias : 'comments'),
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogcategories' => array(
                'controller' => 'category',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORIES',$this->context->language->id)) ? $subAlias : 'categories'),
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogrss' => array(
                'controller' => 'rss',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS',$this->context->language->id)) ? $subAlias : 'rss'),
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogrsscategories'=>array(
                'controller' => 'rss',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS',$this->context->language->id)) ? $subAlias : 'rss').'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_CATEGORY',$this->context->language->id)) ? $subAlias : 'category').'/{id_category}-{url_alias}'.$subfix,
                'keywords' => array(
                    'id_category' =>    array('regexp' => '[0-9]+', 'param' => 'id_category'),
                    'url_alias'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]+','param' => 'url_alias'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogrssauthors2'=>array(
                'controller' => 'rss',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS',$this->context->language->id)) ? $subAlias : 'rss').'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR2',$this->context->language->id)) ? $subAlias : 'community-author').'/{id_author}-{author_name}',
                'keywords' => array(
                    'id_author' =>    array('regexp' => '[0-9]+', 'param' => 'id_author'),
                    'author_name'       =>   array('regexp' => '(.)+','param' => 'author_name'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'is_customer' => 1,
                ),
            ),
            'categoryblogrssauthors'=>array(
                'controller' => 'rss',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS',$this->context->language->id)) ? $subAlias : 'rss').'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_AUTHOR',$this->context->language->id)) ? $subAlias : 'author').'/{id_author}-{author_name}',
                'keywords' => array(
                    'id_author' =>    array('regexp' => '[0-9]+', 'param' => 'id_author'),
                    'author_name'       =>   array('regexp' => '(.)+','param' => 'author_name'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                ),
            ),
            'categoryblogrssalatest'=>array(
                'controller' => 'rss',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS',$this->context->language->id)) ? $subAlias : 'rss').'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS_LATEST',$this->context->language->id)) ? $subAlias : 'latest-posts'),
                'keywords' => array(
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'latest' => true,
                ),
            ),
            'categoryblogrsspopular'=>array(
                'controller' => 'rss',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS',$this->context->language->id)) ? $subAlias : 'rss').'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS_POPULAR',$this->context->language->id)) ? $subAlias : 'popular-posts'),
                'keywords' => array(
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'popular' =>    true,
                ),
            ),
            'categoryblogrssfeatured'=>array(
                'controller' => 'rss',
                'rule' => $blogAlias.'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS',$this->context->language->id)) ? $subAlias : 'rss').'/'.(($subAlias = Configuration::get('YBC_BLOG_ALIAS_RSS_FEATURED',$this->context->language->id)) ? $subAlias : 'featured-posts'),
                'keywords' => array(
                
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'ybc_blog',
                    'featured' =>    true,
                ),
            ),
        );
        if(Configuration::get('PS_ROUTE_ybcblogmainpage')) {
            foreach($routes as $key => $r) {
                Configuration::deleteByName('PS_ROUTE_'.$key);
                unset($r);
            }
            
        }

        return $routes;
    }
    public function setMetas()
    {
        $meta = array();
        $module = Tools::getValue('module');
        if($module!='ybc_blog')
            return;
        $id_lang = $this->context->language->id;
        $id_category = (int)Tools::getValue('id_category');
        $id_post = (int)Tools::getValue('id_post');
        $controller = Tools::getValue('controller');
        if(!$id_post && ($post_url_alias = Tools::getValue('post_url_alias')) && Validate::isLinkRewrite($post_url_alias))
        {
            $id_post = (int)Ybc_blog_post_class::getIDPostByUrlAlias($post_url_alias,$id_lang);
        }
        if(!$id_category && ($category_url_alias = Tools::getValue('category_url_alias')) && Validate::isLinkRewrite($category_url_alias))
        {
            $id_category = (int)Ybc_blog_category_class::getIDCategoryByUrlAlias($category_url_alias,$id_lang);
        }
        if($id_category)
        {
            if(($category  = new Ybc_blog_category_class($id_category,$this->context->language->id)) && Validate::isLoadedObject($category) )
            {
                $meta['meta_title'] = trim($category->meta_title) ? $category->meta_title : $category->title;
                if(trim($category->meta_description))
                    $meta['meta_description'] = $category->meta_description;
                else
                    $meta['meta_description'] = trim($category->description) ? Tools::substr(strip_tags($category->description),0,300):'';
                $meta['meta_keywords'] = $category->meta_keywords;
            }
            else
                $meta['meta_title'] = $this->l('Page not found');
                     
        }
        elseif($id_post)
        {
            if(($post = new Ybc_blog_post_class($id_post,$this->context->language->id)) && Validate::isLoadedObject($post) )
            {
                $meta['meta_title'] = trim($post->meta_title) ? $post->meta_title : $post->title;

                if(trim($post->meta_description))
                    $meta['meta_description'] = $post->meta_description;
                else
                    $meta['meta_description'] = trim($post->short_description) ? Tools::substr(strip_tags($post->short_description),0,300):Tools::substr(strip_tags($post->description),0,300);
                $meta['meta_keywords'] = $post->meta_keywords;
            }
            else
                $meta['meta_title'] = $this->l('Page not found');  
        }
        elseif(($tag = Tools::getValue('tag')) && Validate::isCleanHtml($tag))
        {
            $meta['meta_title'] = $this->l('Tag: ').' "'.$tag.'"';
        }  
        elseif(($latest = Tools::getValue('latest')) && Validate::isCleanHtml($latest))
        {
            $meta['meta_title'] = $this->l('Latest posts');
            $meta['meta_description'] = strip_tags(Configuration::get('YBC_BLOG_SEO_LATEST',$id_lang));            
        }
        elseif(($featured = Tools::getValue('featured')) && Validate::isCleanHtml($featured))
        {
            $meta['meta_title'] = $this->l('Featured posts');
            $meta['meta_description'] = strip_tags(Configuration::get('YBC_BLOG_SEO_FEATURED',$id_lang));            
        }
        elseif(($popular = Tools::getValue('popular')) && Validate::isCleanHtml($popular))
        {
            $meta['meta_title'] = $this->l('Popular posts');
            $meta['meta_description'] = strip_tags(Configuration::get('YBC_BLOG_SEO_POPULAR',$id_lang));            
        }
        elseif(($search = Tools::getValue('search')) && Validate::isCleanHtml($search))
        {
            $meta['meta_title'] = $this->l('Search:').' "'.str_replace('+',' ',$search).'"';
            $meta['meta_description'] = strip_tags(Configuration::get('YBC_BLOG_SEO_SEARCH',$id_lang));            
                        
        } 
        elseif(($year = (int)Tools::getValue('year')) && ($month = (int)Tools::getValue('month')))
          $meta['meta_title'] = $this->l('Posted in :').' "'.$year.' - '.$this->getMonthName($month).'"';  
        elseif($year)
          $meta['meta_title'] = $this->l('Posted in :').' "'.$year.'"';  
        elseif($controller=='gallery')
        {
            $meta['meta_title'] = $this->l('Gallery');
            $meta['meta_description'] = strip_tags(Configuration::get('YBC_BLOG_SEO_GALLERY',$id_lang));           
        }
        elseif($controller=='comment')
        {
            $meta['meta_title'] = $this->l('All comments');
        } 
        elseif($id_author = (int)Tools::getValue('id_author'))
        {

            $is_customer = (int)Tools::getValue('is_customer');
            if($employee = Ybc_blog_post_employee_class::getAuthorById($id_author,$is_customer))
            {

                $meta['meta_title'] = $this->l('Author: ').$employee['name'];
                $meta['meta_description'] = strip_tags($employee['description']);
            }
            else
                $meta['meta_title'] = $this->l('Page not found');
                
        } 
        elseif($controller=='author')
        {
            $meta['meta_title'] = $this->l('Authors');
            $meta['meta_description'] = strip_tags(Configuration::get('YBC_BLOG_SEO_AUTHOR',$id_lang));  
        }
        elseif($controller=='category')
        {
            $meta['meta_title'] = $this->l('All categories');
            $meta['meta_description'] = Configuration::get('YBC_BLOG_SEO_CATEGORIES',$id_lang) ? strip_tags(Configuration::get('YBC_BLOG_SEO_CATEGORIES',$id_lang)):'';                        
                        
        }
        elseif($controller=='rss')
        {
            $meta['meta_title']= $this->l('RSS');
        }
        elseif($controller=='managementblog')
        {
            $meta['meta_title'] = $this->l('My blog posts');
        }
        elseif($controller=='managementcomments')
        {
            $meta['meta_title'] = $this->l('My blog comments');
        }
        elseif($controller=='managementmyinfo')
        {
            $meta['meta_title']= $this->l('My blog info');
        }
        elseif($controller=='blog')
        {
            if($id_author = (int)Tools::getValue('id_author'))
            {
                $is_customer = (int)Tools::getValue('is_customer');
                if(($id = Ybc_blog_post_employee_class::getIdEmployeePostById($id_author,$is_customer)) && ($employeePost = new Ybc_blog_post_employee_class($id,$this->context->language->id)) && Validate::isLoadedObject($employeePost))
                {
                    $meta['meta_title'] = $this->l('Author').' '.$employeePost->name;
                    $meta['meta_description'] = $employeePost->description;
                                    
                }                
            }
            else
            {
                $meta['meta_title'] = Configuration::get('YBC_BLOG_META_TITLE',$id_lang);
                $meta['meta_description'] = Configuration::get('YBC_BLOG_META_DESCRIPTION',$id_lang);
                $meta['meta_keywords'] = Configuration::get('YBC_BLOG_META_KEYWORDS',$id_lang);
            }
            
        }
        if(!isset($meta['meta_title']))
            $meta['meta_title']='';
        if(!isset($meta['meta_description']))
            $meta['meta_description']='';
        if(!isset($meta['meta_keywords']))
            $meta['meta_keywords']='';
        if(Configuration::get('YBC_BLOG_RTL_MODE')=='auto' && isset($this->context->language->is_rtl) && $this->context->language->is_rtl || Configuration::get('YBC_BLOG_RTL_MODE')=='rtl')
            $rtl = true;
        else
            $rtl = false;            
        if($this->is17)
        {
            $body_classes = array(
                'lang-'.$this->context->language->iso_code => true,
                'lang-rtl' => (bool) $this->context->language->is_rtl,
                'country-'.$this->context->country->iso_code => true,                                   
                'ybc_blog' => true,
                'ybc_blog_rtl' => $rtl,
            );
            $page = array(
                'title' => '',
                'canonical' => '',
                'meta' => array(
                    'title' => $meta['meta_title'],
                    'description' => $meta['meta_description'],
                    'keywords' => $meta['meta_keywords'],
                    'robots' => 'index',
                ),
                'page_name' => 'ybc_blog_page',
                'body_classes' => $body_classes,
                'admin_notifications' => array(),
            ); 
            $this->context->smarty->assign(array('page' => $page)); 
        }    
        else
        {
            $this->context->smarty->assign($meta);
            if($rtl) 
                $this->context->smarty->assign(array(
                    'body_classes' => array('ybc_blog_rtl'),
                ));
        }                
    }
    public function getBreadCrumb()
    {
        $id_post = (int)Tools::getValue('id_post');
        if(!$id_post && ($post_url_alias = Tools::getValue('post_url_alias')) && Validate::isLinkRewrite($post_url_alias))
        {
            $id_post = (int)Ybc_blog_post_class::getIDPostByUrlAlias($post_url_alias);
        }
        $id_category = (int)Tools::getValue('id_category');
        if(!$id_category && ($category_url_alias= Tools::getValue('category_url_alias')) && Validate::isLinkRewrite($category_url_alias))
        {
            $id_category = (int)Ybc_blog_category_class::getIDCategoryByUrlAlias($category_url_alias);
        }
        $id_author = (int)Tools::getValue('id_author');
        $is_customer= (int)Tools::getValue('is_customer');
        $nodes = array();
        $controller = Tools::getValue('controller');
        $nodes[] = array(
            'title' => $this->l('Home'),
            'url' => $this->context->link->getPageLink('index', true),
        );
        $nodes[] = array(
            'title' => $this->l('Blog'),
            'url' => $this->getLink('blog')
        );
        if($controller=='category')
        {
            $nodes[] = array(
                'title' => $this->l('All categories'),
                'url' => $this->getLink('category')
            );
        }
        if($controller=='comment')
        {
            $nodes[] = array(
                'title' => $this->l('All Comments'),
                'url' => $this->getLink('comment')
            );
        }
        if($id_category && $category = Ybc_blog_category_class::getCategoryById($id_category))
        {
            $nodes[] = array(
                'title' => $category['title'],
                'url' => $this->getLink('blog',array('id_category' => $id_category)),                   
            );
        }
        if($id_author && $author = Ybc_blog_post_employee_class::getAuthorById($id_author,$is_customer))
        {
            $nodes[] = array(
                'title' => $this->l('Authors'),
                'url' => $this->getLink('author'),               
            );
            $nodes[] = array(
                'title' => trim(Tools::ucfirst($author['name'])),
                'url' => $this->getLink('blog',array('id_author' => $id_author)),               
            );
        }
        elseif($controller=='author')
        {
             $nodes[] = array(
                    'title' => $this->l('Authors'),
                    'url' => $this->getLink('author'),               
             );
        }
        if($id_post && ($post = new Ybc_blog_post_class($id_post,$this->context->language->id)) && Validate::isLoadedObject($post))
        {
            if($post->id_category_default)
                $id_category_default= $post->id_category_default;
            else
            {
                $id_category_default = Ybc_blog_post_class::getFirstCategory($post->id);
            }
            if($id_category_default && ($category = new Ybc_blog_category_class($id_category_default,$this->context->language->id)) && Validate::isLoadedObject($category) )
            {
                $nodes[] = array(
                    'title' => $category->title,
                    'url' => $this->getLink('blog',array('id_category' => $category->id)),
                );
            }
            $nodes[] = array(
                'title' => $post->title,
                'url' => $this->getLink('blog',array('id_post' => $id_post)),                   
            );
        }
        if($controller=='rss')
        {
            $nodes[] = array(
                'title' => $this->l('Rss'),
                'url' => $this->getLink('rss'),                   
            );
        }
        if($controller == 'gallery')
        {
            $nodes[] = array(
                'title' => $this->l('Gallery'),
                'url' => $this->getLink('gallery'),                   
            );
        }
        if($controller == 'blog' && ($latest = Tools::getValue('latest')) && Validate::isCleanHtml($latest))
        {
            $nodes[] = array(
                'title' => $this->l('Latest posts'),
                'url' => $this->getLink('blog',array('latest' => true)),                   
            );
        }
        if($controller == 'blog' && ($popular = Tools::getValue('popular')) && Validate::isCleanHtml($popular))
        {
            $nodes[] = array(
                'title' => $this->l('Popular posts'),
                'url' => $this->getLink('blog',array('popular' => true)),                   
            );
        }
        if($controller == 'blog' && ($featured = Tools::getValue('featured')) && Validate::isCleanHtml($featured))
        {
            $nodes[] = array(
                'title' => $this->l('Featured posts'),
                'url' => $this->getLink('blog',array('featured' => true)),                   
            );
        }
        if($controller == 'blog' && ($tag = Tools::getValue('tag')) && Validate::isCleanHtml($tag))
        {
            $nodes[] = array(
                'title' => $this->l('Blog tag').': '.$tag,
                'url' => $this->getLink('blog',array('tag' => $tag)),                    
            );
        }
        if($controller == 'blog' && ($search = Tools::getValue('search')) && Validate::isCleanHtml($search))
        {
            $nodes[] = array(
                'title' => $this->l('Blog search').': '.str_replace('+',' ',$search),
                'url' => $this->getLink('blog',array('search' => $search)),                     
            );
        }
        $year = (int)Tools::getValue('year');
        $month = (int)Tools::getValue('month');
        if($controller == 'blog' && $month && $year)
        {
            $nodes[] = array(
                'title' => $month.'-'.$year,
                'url' => $this->getLink('blog',array('month' => $month,'year'=>$year)),                     
            );
        }
        elseif($controller == 'blog' && $year)
        {
            $nodes[] = array(
                'title' =>$year,
                'url' => $this->getLink('blog',array('year'=>$year)),                     
            );
        }
        if($this->is17)
            return array('links' => $nodes,'count' => count($nodes));
        return $this->displayBreadcrumb($nodes);
    }
    public function displayBreadcrumb($nodes)
    {
        $this->smarty->assign(array('nodes' => $nodes));
        return $this->display(__FILE__, 'nodes.tpl');
    }
    private function _installTabs()
    {
        $languages = Language::getLanguages(false);
        $tab = new Tab();
        $tab->class_name = 'AdminYbcBlog';
        $tab->module = 'ybc_blog';
        $tab->id_parent = 0;            
        foreach($languages as $lang){
                $tab->name[$lang['id_lang']] = ($text_lang = $this->getTextLang('Blog',$lang)) ? $text_lang : $this->l('Blog');
        }
        $tab->save();
        $blogTabId = Tab::getIdFromClassName('AdminYbcBlog');
        if($blogTabId)
        {
            $ybc_defines = new Ybc_blog_defines();
            foreach($ybc_defines->subTabs as $tabArg)
            {
                if(!Tab::getIdFromClassName($tabArg['class_name']))
                {
                    $tab = new Tab();
                    $tab->class_name = $tabArg['class_name'];
                    $tab->module = 'ybc_blog';
                    $tab->id_parent = $blogTabId; 
                    $tab->icon=$tabArg['icon'];             
                    foreach($languages as $lang){
                            $tab->name[$lang['id_lang']] = ($text_lang = $this->getTextLang($tabArg['tabname'],$lang,'ybc_blog_defines')) ? $text_lang : $tabArg['tab_name'];
                    }
                    $tab->save();
                }
            }                
        }            
        return true;
    }
    private function _uninstallTabs()
    {
        $ybc_defines = new Ybc_blog_defines();        
        foreach($ybc_defines->subTabs as $tab)
        {
            if($tabId = Tab::getIdFromClassName($tab['class_name']))
            {
                $tab = new Tab($tabId);
                if($tab)
                    $tab->delete();
            }                
        }
        if($tabId = Tab::getIdFromClassName('AdminYbcBlog'))
        {
            $tab = new Tab($tabId);
            if($tab)
                $tab->delete();
        }
        return true;
    }
    public function getInternalStyles()
    {
        if(!file_exists(dirname(__FILE__).'/views/css/custom.css'))
        {
            $this->refreshCssCustom();
        }
        $this->context->controller->addCSS($this->_path.'views/css/custom.css');
        $id_category = (int)Tools::getValue('id_category');
        $id_post = (int)Tools::getValue('id_post');
        if(!$id_post && ($post_url_alias = Tools::getValue('post_url_alias')) && Validate::isLinkRewrite($post_url_alias))
        {
            $id_post = (int)Ybc_blog_post_class::getIDPostByUrlAlias($post_url_alias,$this->context->language->id);
        }
        if(!$id_category && ($category_url_alias = Tools::getValue('category_url_alias')) && Validate::isLinkRewrite($category_url_alias) )
        {
            $id_category = (int)Ybc_blog_category_class::getIDCategoryByUrlAlias($category_url_alias,$this->context->language->id);
        }
        $fc = Tools::getValue('fc');
        $module = Tools::getValue('module');
        $controller = Tools::getValue('controller');
        if($fc=='module' && $module=='ybc_blog')
        {
            if($id_category)
                $current_link= $this->getLink($controller,array('id_category' => $id_category));
            elseif($id_post)
                $current_link= $this->getLink($controller,array('id_post' => $id_post));
            elseif($id_author=(int)Tools::getValue('id_author'))
                $current_link=$this->getLink($controller,array('id_author'=>$id_author));
            elseif(($tag=Tools::getValue('tag')) && Validate::isCleanHtml($tag))
                $current_link=$this->getLink($controller,array('tag'=>$tag));
            elseif(($search=Tools::getValue('search')) && Validate::isCleanHtml($search))
                $current_link=$this->getLink($controller,array('search'=>$search));
            elseif(($latest=Tools::getValue('latest')) && Validate::isCleanHtml($latest))
                $current_link=$this->getLink($controller,array('latest'=>$latest));
            else
                $current_link=$this->getLink($controller);
        }
        $this->smarty->assign(
            array(
                'link_current'=>isset($current_link)?$current_link:false,
                'baseAdminDir' => __PS_BASE_URI__.'/',
                'url_path' => $this->_path,
                'ybc_blog_product_category' => $id_category,
            )
        );
        if($id_post && $module==$this->name && $controller=='blog')
        {
            $post = $this->getPostById($id_post);
            if($post)
            {
                $is_customer = (int)Tools::getValue('is_customer');
                $post['img_name'] = isset($post['image']) ? $post['image'] : '';
                if($post['image'])
                    $post['image'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/'.$post['image']);                            
                if($post['thumb'])
                    $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                $post['link'] = $this->getLink('blog',array('id_post'=>$post['id_post']));
                $post['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($post['id_post'],false,true);
                $post['products'] =  Ybc_blog_post_class::getRelatedProductByProductsStr($post['id_post'],$post['products'],$post['exclude_products']) ;
                $params = array(); 
                $params['id_author'] = (int)$post['added_by'];
                $params['is_customer'] =(int)$is_customer;
                $employee = Ybc_blog_post_employee_class::getAuthorById($params['id_author'],$is_customer);
                if($employee)
                    $params['alias'] = str_replace(' ','-',trim(Tools::strtolower($employee['firstname'].' '.$employee['lastname']))); 
                $post['author_link'] = $this->getLink('blog', $params);
                $this->context->smarty->assign(
                    array(
                        'blog_post_header'=>$post,
                    )
                );
            }
        }
        $this->context->smarty->assign(
            array(
                'YBC_BLOG_CAPTCHA_TYPE' => Configuration::get('YBC_BLOG_CAPTCHA_TYPE'),
                'YBC_BLOG_CAPTCHA_SITE_KEY' => Configuration::get('YBC_BLOG_CAPTCHA_TYPE')=='google' ? Configuration::get('YBC_BLOG_CAPTCHA_SITE_KEY') : Configuration::get('YBC_BLOG_CAPTCHA_SITE_KEY3'),
            )
        );
        return $this->display(__FILE__, 'head.tpl');;
    } 
    public function ajaxCustomerSearch()
    {
       if(!Tools::isSubmit('ajaxCustomersearch'))
       {
            return '';
       } 
       $query = Tools::getValue('q', false);
       if (!$query OR $query == '' OR (Tools::strlen($query) < 3 AND !Validate::isUnsignedId($query)) OR !Validate::isCleanHtml($query))
        	die();
       $filter ='AND (';
       $filter .= " c.id_customer = ".(int)trim(urldecode($query));
       $filter .= " OR (CONCAT(c.firstname,' ',c.lastname) like '".pSQL($query)."%' OR be.name like'".pSQL($query)."%')";                
       $filter .= " OR c.email like '".pSQL($query)."%'";
       $filter .=')';
       $customers= Ybc_blog_post_employee_class::getCustomersFilter($filter);
        if($customers)
        {
        	foreach ($customers as $customer)
        	{
        	   echo $customer['id_customer'].'|'.($customer['name'] ? $customer['name'] : $customer['customer'] ).'|'.$customer['email'].'|'.$this->context->link->getAdminLink('AdminCustomers').'&id_customer='.(int)$customer['id_customer'].'&updatecustomer'."\n";	
        	}
        }
        die();
    }   
    public function ajaxPostSearch()
    {
        if(!Tools::isSubmit('ajaxpostsearch'))
        {
            return '';
        }
        $query = Tools::getValue('q', false);
        if (!$query OR $query == '' OR (Tools::strlen($query) < 3 AND !Validate::isUnsignedId($query)) OR !Validate::isCleanHtml($query) )
        	die();
        $posts= Ybc_blog_post_class::getPostsWithFilter(' AND ( p.id_post="'.(int)$query.'" OR pl.title like "%'.pSQL($query).'%")');
        if($posts)
        {
        	foreach ($posts as $post)
        	{
        	   echo $post['title'].'|'.$post['id_post'].'|'.($post['thumb'] ?  $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']) :  $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/'.$post['image']) )."\n";	
        	}
        }
        die();
    }    
    public function ajaxProductSearch()
    {
        if(!Tools::isSubmit('ajaxproductsearch'))
            return;
        $query = Tools::getValue('q', false);
        if (!$query OR $query == '' OR Tools::strlen($query) < 1 OR !Validate::isCleanHtml($query))
        	die();        
        /*
         * In the SQL request the "q" param is used entirely to match result in database.
         * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list, 
         * they are no return values just because string:"(ref : #ref_pattern#)" 
         * is not write in the name field of the product.
         * So the ref pattern will be cut for the search request.
         */
        if($pos = strpos($query, ' (ref:'))
        	$query = Tools::substr($query, 0, $pos);
        
        $excludeIds = Tools::getValue('excludeIds', false);
        if($excludeIds && !Validate::isCleanHtml($excludeIds))
            $excludeIds = false;
        $excludeVirtuals = (bool)Tools::getValue('excludeVirtuals', false);
        $exclude_packs = (bool)Tools::getValue('exclude_packs', false);
        $items = Ybc_blog_defines::getProducts($query,$excludeIds,$excludeVirtuals,$exclude_packs);
        $acc = (bool)Tools::isSubmit('excludeIds');
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $type_image= ImageType::getFormattedName('home');
        else
            $type_image= ImageType::getFormatedName('home');
        if ($items && $acc)
        	foreach ($items AS $item)
            {
                $link_product= $this->context->link->getProductLink($item['id_product'],null,null,null,null,null,Product::getDefaultAttribute($item['id_product']));
                echo trim(str_replace('|','-',$item['name'])).(!empty($item['reference']) ? ' (ref: '.str_replace('|','-',$item['reference']).')' : '').'|'.(int)($item['id_product']).'|'.str_replace('http://', Tools::getShopProtocol(), Context::getContext()->link->getImageLink($item['link_rewrite'], $item['id_image'], $type_image)).'|'.$link_product."\n";
            }
        elseif ($items)
        {
        	// packs
        	$results = array();
        	foreach ($items AS $item)
        	{
        		$product = array(
        			'id' => (int)($item['id_product']),
        			'name' => $item['name'],
        			'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
        			'image' => str_replace('http://', Tools::getShopProtocol(), Context::getContext()->link->getImageLink($item['link_rewrite'], $item['id_image'], $type_image)),
        		);
        		array_push($results, $product);
        	}
        	echo json_encode($results);
        }
        else
        	json_encode(new stdClass);
        die;   
   }
   public function getProfileEmployee($id_employee)
   {
        if(($id = (int)Ybc_blog_post_employee_class::getIdEmployeePostById($id_employee,false)) && ($employeePost = new Ybc_blog_post_employee_class($id)) && Validate::isLoadedObject($employeePost))
            return explode(',',$employeePost->profile_employee);
        return array();
   }
   public function getBlogCategoriesDropdown($blogcategories, &$depth_level = -1,$selected_blog_category=0)
   {        
        if($blogcategories)
        {
            $depth_level++;
            foreach($blogcategories as $category)
            {
                if((!$this->depthLevel || $this->depthLevel && (int)$depth_level <= $this->depthLevel))
                {
                    $levelSeparator = '';
                    if($depth_level >= 1)
                    {
                        for($i = 0; $i <= $depth_level-1; $i++)
                        {
                            $levelSeparator .= $this->prefix;
                        }
                    }       
                    if($category['id_category'] >=0)
                        $this->blogCategoryDropDown .= $this->displayBlogOption((int)$selected_blog_category,(int)$category['id_category'],$depth_level,$levelSeparator,$category['title']);
                    if(isset($category['children']) && $category['children'])
                    {                        
                        $this->getBlogCategoriesDropdown($category['children'], $depth_level,$selected_blog_category);
                    }   
                }                                 
            } 
            $depth_level--;           
        }
    }
    public function displayBlogOption($selected_blog_category,$id_category,$depth_level,$levelSeparator,$title)
    {
        $this->context->smarty->assign(array(
            'selected_blog_category' => $selected_blog_category,
            'id_category' => $id_category,
            'depth_level' => $depth_level,
            'levelSeparator' => $levelSeparator,
            'title' => $title,
        ));
        return $this->display(__FILE__,'blogoption.tpl');
    }
    public function checkProfileEmployee($id_employee,$profile)
    {
        $employee = new Employee($id_employee);
        if($employee->id_profile==1)
            return true;
        $id_employee_post=  (int)Ybc_blog_post_employee_class::getIdEmployeePostById($id_employee,false,true);
        if($id_employee_post)
        {
            $employeePost = new Ybc_blog_post_employee_class($id_employee_post);
            if($employeePost->profile_employee)
            {
                $profiles = explode(',',$employeePost->profile_employee);
                if(in_array('All tabs',$profiles) || in_array($profile,$profiles))
                    return true;
                else
                    return false;
            }
        }
        return false;
    }
    public function hookDisplayFooterProduct($params)
    {
        if(!Configuration::get('YBC_BLOG_DISPLAY_PRODUCT_PAGE'))
            return '';
        $id_product = (int)Tools::getValue('id_product');
        $limit = (int)Configuration::get('YBC_BLOG_NUMBER_POST_IN_PRODUCT') > 0 ? (int)Configuration::get('YBC_BLOG_NUMBER_POST_IN_PRODUCT') : 5;
        $posts= Ybc_blog_post_class::getPostsByIdProduct($id_product,$limit);
        if($posts)
        {
            foreach($posts as &$rpost)
                if($rpost['image'])
                {
                    $rpost['image'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/'.$rpost['image']);
                    if($rpost['thumb'])
                        $rpost['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$rpost['thumb']);
                    else
                        $rpost['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/'.$rpost['image']);
                    $rpost['link'] =   $this->getLink('blog',array('id_post'=>$rpost['id_post']));
                    $rpost['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($rpost['id_post'],false,true);
                    $rpost['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.(int)$rpost['id_post'].' AND approved=1');
                    $rpost['liked'] = $this->isLikedPost($rpost['id_post']);                        
                }
                else
                {
                    $rpost['image'] = '';
                    if($rpost['thumb'])
                        $rpost['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$rpost['thumb']);
                    else
                        $rpost['thumb'] = '';
                    $rpost['link'] =   $this->getLink('blog',array('id_post'=>$rpost['id_post']));
                    $rpost['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($rpost['id_post'],false,true);
                    $rpost['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.(int)$rpost['id_post'].' AND approved=1');
                    $rpost['liked'] = $this->isLikedPost($rpost['id_post']);  
                }                        
        }
        $this->context->smarty->assign(
            array(
                'posts'=>$posts,
                'image_folder' => _PS_YBC_BLOG_IMG_,
                'display_desc' => Configuration::get('YBC_BLOG_PRODUCT_PAGE_DISPLAY_DESC'), 
                'allow_rating' => (int)Configuration::get('YBC_BLOG_ALLOW_RATING') ? true : false,
                'show_featured_post' => (int)Configuration::get('YBC_BLOG_SHOW_FEATURED_BLOCK') ? true : false,
                'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                'show_date' => (int)Configuration::get('YBC_BLOG_SHOW_POST_DATE') ? true : false,
                'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                'date_format' => trim((string)Configuration::get('YBC_BLOG_DATE_FORMAT')),
                'show_categories' => (int)Configuration::get('YBC_BLOG_SHOW_POST_CATEGORIES') ? true : false, 
                'blog_layout' => Tools::strtolower(Configuration::get('YBC_BLOG_LAYOUT')),   
                'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')),  
                'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false,
            )
        );
        return $this->display(__FILE__,'product-post.tpl');
    }
   public function displayBlogCategoryTre($blockCategTree,$selected_categories,$name='',$disabled_categories=array())
    {
        if($id_post = (int)Tools::getValue('id_post'))
        {
            $post = new Ybc_blog_post_class($id_post);
            $id_category_default= $post->id_category_default;
        }
        else
            $id_category_default=0;
        $this->context->smarty->assign(
            array(
                'blockCategTree'=> $blockCategTree,
                'branche_tpl_path_input'=> _PS_MODULE_DIR_.'ybc_blog/views/templates/hook/category-tree-blog.tpl',
                'selected_categories'=>$selected_categories,
                'disabled_categories' => $disabled_categories,
                'id_category_default' => (int)Tools::getValue('main_category',$id_category_default) ,
                'name'=>$name ? $name :'blog_categories',
            )
        );
        return $this->display(__FILE__, 'categories_blog.tpl');
    }
    public function hookBlogArchivesBlock()
    {
        $this->context->smarty->assign(
            array(
                'years'=>Ybc_blog_post_class::getBlogArchives(),
            )
        );
        return $this->display(__FILE__,'block_archives.tpl');
    }
    public function getMonthName($month)
    {
        switch ($month) {
            case 1:
                return $this->l('January');
            case 2:
                return $this->l('February');
            case 3:
                return $this->l('March');
            case 4:
                return $this->l('April');
            case 5:
                return $this->l('May');
            case 6:
                return $this->l('June');
            case 7:
                return $this->l('July');
            case 8:
                return $this->l('August');
            case 9:
                return $this->l('September');
            case 10:
                return $this->l('October');
            case 11:
                return $this->l('November');
            case 12:
                return $this->l('December');
        }
    }
    public function _postCustomerSettingAuthor()
    {
        if(Tools::isSubmit('saveCustomerAuthor'))
        {
            $ybc_defines = new Ybc_blog_defines();
            if($this->_saveConfiguration($ybc_defines->customer_settings))
            {
                Hook::exec('actionUpdateBlog', array()); 
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=author&conf=4');
            }
            
        }
    }
    public function _saveConfiguration($configs,$dirImg='',$width_image='',$height_image='')
    {
        $errors = array();
        $languages = Language::getLanguages(false);
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $key_values = array();
        if($configs)
        {
            foreach($configs as $key => $config)
            {
                if(isset($config['lang']) && $config['lang'])
                {
                    $key_lang_default = trim(Tools::getValue($key.'_'.$id_lang_default));
                    if(isset($config['required']) && $config['required'] && $config['type']!='switch' && $key_lang_default=='')
                    {
                        $errors[] = sprintf($this->l('%s is required'),$config['label']);
                    }
                    elseif($key_lang_default && !Validate::isCleanHtml($key_lang_default))
                        $errors[] = sprintf($this->l('%s is not valid'),$config['label']);
                    else
                    {
                        $key_values[$key][$id_lang_default] = $key_lang_default;
                        foreach($languages as $language)
                        {
                            $id_lang = (int)$language['id_lang'];
                            $key_lang = trim(Tools::getValue($key.'_'.$id_lang));
                            if($key_lang && !Validate::isCleanHtml($key_lang))
                                $errors[] = sprintf($this->l('%s in %s is not valid'),$language['name'],$config['label']);
                            else
                                $key_values[$key][$id_lang] = $key_lang;
                        }
                    }                    
                }
                else
                {
                    $key_value = Tools::getValue($key);
                    if(isset($config['required']) && $config['required'] && $config['type']!='switch' && !$key_value)
                    {
                        $errors[] = sprintf($this->l('%s is required'),$config['label']);
                    }
                    if(isset($config['validate']) && method_exists('Validate',$config['validate']))
                    {
                        $validate = $config['validate'];
                        if(!Validate::$validate($key_value))
                            $errors[] = sprintf($this->l('%s is not valid'),$config['label']);
                        unset($validate);
                    }
                    elseif(!is_array($key_value) &&  !Validate::isCleanHtml($key_value))
                    {
                        $errors[] = sprintf($this->l('%s is not valid'),$config['label']);
                    } 
                    $key_values[$key] = $key_value;  
                }                    
            }
        }
        if(!$errors)
        {
            if($configs)
            {
                foreach($configs as $key => $config)
                {
                    if(isset($config['lang']) && $config['lang'])
                    {
                        $valules = array();
                        foreach($languages as $lang)
                        {
                            $id_lang = (int)$lang['id_lang'];
                            if($config['type']=='switch')                                                           
                                $valules[$id_lang] = (int)$key_values[$key][$id_lang] ? 1 : 0;                                
                            else
                                $valules[$id_lang] = $key_values[$key][$id_lang] ? : $key_values[$key][$id_lang_default];
                        }
                        Configuration::updateValue($key,$valules);
                    }
                    else
                    {
                        if($config['type']=='switch')
                        {                           
                            Configuration::updateValue($key,(int)$key_values[$key] ? 1 : 0);
                        }
                        elseif($config['type']=='checkbox' || $config['type']=='blog_categories')
                            Configuration::updateValue($key,implode(',',$key_values[$key]));
                        elseif($config['type']=='file')
                        {      
                            if(isset($_FILES[$key]['tmp_name']) && isset($_FILES[$key]['name']) && $_FILES[$key]['name'])
                            {
                                $_FILES[$key]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES[$key]['name']);
                                if(!Validate::isFileName($_FILES[$key]['name']))
                                {
                                    $errors[] = $this->l('Image is not valid');
                                }
                                else
                                {
                                    if(file_exists($dirImg.$_FILES[$key]['name']))
                                    {
                                        $_FILES[$key]['name'] = $this->createNewFileName($dirImg,$_FILES[$key]['name']);
                                    }
                                    $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'], '.'), 1));
                                    $imagesize = @getimagesize($_FILES[$key]['tmp_name']);
                                    if (isset($_FILES[$key]) &&
                                        !empty($_FILES[$key]['tmp_name']) &&
                                        !empty($imagesize) &&
                                        in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                                    )
                                    {
                                        $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                                        $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE');
                                        if($_FILES[$key]['size'] > $max_file_size*1024*1024)
                                            $errors[] = sprintf($this->l('Image file is too large. Limit: %sMb'),$max_file_size);
                                        elseif (!$temp_name || !move_uploaded_file($_FILES[$key]['tmp_name'], $temp_name))
                                            $errors[] = $this->l('Cannot upload the file');
                                        elseif(!ImageManager::resize($temp_name, $dirImg.$_FILES[$key]['name'], $width_image, $height_image, $type))
                                            $errors[] = $this->l('An error occurred during the image upload process.');
                                        if (isset($temp_name) && file_exists($temp_name))
                                            @unlink($temp_name);
                                        if(Configuration::get($key))
                                        {
                                            if(file_exists($dirImg.Configuration::get($key)))
                                                @unlink($dirImg.Configuration::get($key));
                                        }
                                        Configuration::updateValue($key,$_FILES[$key]['name']);

                                    }
                                    else
                                        $errors[] = $this->l('Image is not valid');
                                }

                                
                            }
                        }
                        else
                            Configuration::updateValue($key,trim($key_values[$key]));   
                    }                        
                }
            }
        }
        if (count($errors))
        {
           $this->errorMessage = $this->displayError($errors);  
           if(!Tools::isSubmit('ajax'))
                return false;
        }
        if(!Tools::isSubmit('ajax'))
            return true;

        if(Tools::isSubmit('ajax'))
        {
            die(json_encode(
                array(
                    'messageType' => $errors ? 'error' : 'success',
                    'message' => $errors ? $this->errorMessage : $this->displayConfirmation($this->l('Configuration saved')),
                    'ybc_link_desc'=>$this->getLink(),
                )
            ));
        }
    }
    public function _postRSS()
    {
        $ybc_defines = new Ybc_blog_defines();
        if(Tools::isSubmit('saveRSS'))
        {
            $this->_saveConfiguration($ybc_defines->rss);
        }
    }
    public function getBaseLink()
    {
        return (Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').$this->context->shop->domain.$this->context->shop->getBaseURI();
    }
    public function displayTabAuthor()
    {
        $filter = "";
        $having="";
        $control = Tools::getValue('control');
        if($control=='employee')
        {
            if(($id_employee = trim(Tools::getValue('id_employee')))!='' && Validate::isCleanHtml($id_employee))
                $filter .= " AND e.id_employee = ".(int)$id_employee;
            if(($name = trim(Tools::getValue('name')))!='' && Validate::isCleanHtml($name))
                $filter .= " AND (CONCAT(e.firstname,' ',e.lastname) like '".pSQL($name)."%' OR be.name like'".pSQL($name)."%')";                
            if(($email = trim(Tools::getValue('email'))) && Validate::isCleanHtml($email))
                $filter .= " AND e.email like '%".pSQL($email)."%'";
            if(($desc = trim(Tools::getValue('description'))) && Validate::isCleanHtml($desc))
                $filter .= " AND bel.description like '%".pSQL($desc)."%'";
            if(($id_profile = trim(Tools::getValue('id_profile'))) && Validate::isCleanHtml($id_profile))
                $filter .= " AND pl.id_profile = '".(int)$id_profile."'";
            if(($profile_employee = trim(Tools::getValue('profile_employee')))!='' && Validate::isCleanHtml($profile_employee))
                $filter .= " AND (be.profile_employee like '".pSQL($profile_employee)."' OR p.id_profile=1)  ";
            if(($total_post_min = trim(Tools::getValue('total_post_min')))!='' && Validate::isCleanHtml($total_post_min))
                $having .= ' AND total_post >="'.(int)$total_post_min.'"';
            if(($total_post_max = trim(Tools::getValue('total_post_max')))!='' && Validate::isCleanHtml($total_post_max))
                $having .= ' AND total_post <="'.(int)$total_post_max.'"'; 
            if(Tools::isSubmit('status') && ($status = trim(Tools::getValue('status')))!='' && Validate::isCleanHtml($status))
                    $filter .= " AND (be.status= '".(int)$status."'".((int)$status==1 ? ' or be.status is null':'' )." )";
        }        
        $totalEmployee = (int)Ybc_blog_post_employee_class::countEmployeesFilter($filter,$having);
        $filter = "";
        $having="";
        if($control=='customer')
        {
            if(($id_customer = trim(Tools::getValue('id_customer')))!='' && Validate::isCleanHtml($id_customer))
                $filter .= " AND c.id_customer = ".(int)$id_customer;
            if(($name = trim(Tools::getValue('name')))!='' && Validate::isCleanHtml($name))
                $filter .= " AND (CONCAT(c.firstname,' ',c.lastname) like '".pSQL($name)."%' OR be.name like'".pSQL($name)."%')";                
            if(($email = trim(Tools::getValue('email')))!='' && Validate::isCleanHtml($email))
                $filter .= " AND c.email like '".pSQL($email)."%'";
            if(($description = trim(Tools::getValue('description')))!='' && Validate::isCleanHtml($description) )
                $filter .= ' AND bel.description like "%'.pSQL($description).'%"';
            if(($total_post_min = trim(Tools::getValue('total_post_min')))!='' && Validate::isCleanHtml($total_post_min))
                $having .= ' AND total_post >="'.(int)$total_post_min.'"';
            if(($total_post_max = trim(Tools::getValue('total_post_max')))!='' && Validate::isCleanHtml($total_post_max))
                $having .= ' AND total_post <="'.(int)$total_post_max.'"'; 
            if(Tools::isSubmit('status') && ($status = trim(Tools::getValue('status')))!='' && Validate::isCleanHtml($status))
                    $filter .= " AND (be.status= '".(int)$status."'".((int)$status==1 ? ' or be.status is null':'' )." )";
        }  
        $has_post = Tools::getValue('has_post');
        if(Tools::isSubmit('has_post') && $has_post==0)
            $having .= ' AND total_post <=0';
        else
            $having .= ' AND total_post >=1';       
        $totalCustomer = (int)Ybc_blog_post_employee_class::countCustomersFilter($filter,$having);
        $this->context->smarty->assign(
            array(
                'totalCustomer' => $totalCustomer,
                'totalEmployee' => $totalEmployee,
                'control' => $control,
                'YBC_BLOG_ALLOW_CUSTOMER_AUTHOR' => Configuration::get('YBC_BLOG_ALLOW_CUSTOMER_AUTHOR'),
            )
        );
        return $this->display(__FILE__,'tab_author.tpl');
    }
    public function hookCustomerAccount($params)
    {
        $this->context->smarty->assign(
            array(
                'author'=> Ybc_blog_post_employee_class::checkGroupAuthor(),
                'path_module'=> $this->_path,
                'link' => $this->context->link,
                'suppened' =>(int)Ybc_blog_post_employee_class::getIdEmployeePostById((int)$this->context->customer->id,true,true) || !(int)Ybc_blog_post_employee_class::getIdEmployeePostById((int)$this->context->customer->id,true) ? false :true,
            )
        );
        if($this->is17)
    	   return $this->display(__FILE__, 'my-account.tpl');
        else
            return $this->display(__FILE__, 'my-account16.tpl');
    }
    public function hookDisplayMyAccountBlock($params)
    {
    	return $this->hookCustomerAccount($params);
    }
    public function hookDisplayLeftFormManagament()
    {
        $left_tabs= array(
            array(
                'title'=> $this->l('My posts'),
                'link'=> $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','list'=>true)),
                'name'=>'post',
            ),
            array(
                'title'=> $this->l('Comments'),
                'link'=> $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'comment','list'=>true)),
                'name'=>'comment',
            ),
        );
        $this->context->smarty->assign(
            array(
                'left_tabs'=>$left_tabs,  
                'tabmanagament' => ($tabmanagament = Tools::getValue('tabmanagament','post')) && Validate::isCleanHtml($tabmanagament) ? $tabmanagament :'post',
            )
        );
        return $this->display(__FILE__,'blog_management_left.tpl');
    }
    public function hookDisplayLeftFormComments()
    {
        $left_tabs= array(
            array(
                'title'=> $this->l('My comments'),
                'link'=> $this->context->link->getModuleLink('ybc_blog','managementcomments',array('tabmanagament'=>'comment_other','list'=>true)),
                'name'=>'comment_other',
            ),
        );
        $this->context->smarty->assign(
            array(
                'left_tabs'=>$left_tabs,  
                'tabmanagament' => ($tabmanagament =  Tools::getValue('tabmanagament','comment_other')) && Validate::isCleanHtml($tabmanagament) ? $tabmanagament :'comment_other',
            )
        );
        return $this->display(__FILE__,'blog_management_left.tpl');
    }
    public function renderCommentOtherListByCustomer()
    {
        if(!(Tools::isSubmit('editcomment') && ($id_comment = (int)Tools::getValue('id_comment'))))
        {
            $fields_list = array(
                'id_comment' => array(
                    'title' => $this->l('Id'),
                    'width' => 40,
                    'type' => 'text',
                    'sort' => $this->context->link->getModuleLink($this->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'id_comment','sort_type'=>'asc')),
                    'sort_desc'=>$this->context->link->getModuleLink($this->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'id_comment','sort_type'=>'desc')),
                    'filter' => true,
                ),
                'subject' => array(
                    'title' => $this->l('Subject'),
                    'type' => 'text',
                    'sort' => $this->context->link->getModuleLink($this->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'subject','sort_type'=>'asc')),
                    'sort_desc'=>$this->context->link->getModuleLink($this->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'subject','sort_type'=>'desc')),
                    'filter' => true,                        
                ),                    
                'rating' => array(
                    'title' => $this->l('Rating'),
                    'type' => 'select',
                    'sort' => $this->context->link->getModuleLink($this->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'rating','sort_type'=>'asc')),
                    'sort_desc'=>$this->context->link->getModuleLink($this->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'rating','sort_type'=>'desc')),
                    'filter' => true,
                    'rating_field' => true,
                    'filter_list' => array(
                        'id_option' => 'rating',
                        'value' => 'stars',
                        'list' => array(
                            0 => array(
                                'rating' => 0,
                                'stars' => $this->l('No reviews')
                            ),
                            1 => array(
                                'rating' => 1,
                                'stars' => '1 '.$this->l('star')
                            ),
                            2 => array(
                                'rating' => 2,
                                'stars' => '2 '.$this->l('stars')
                            ),
                            3 => array(
                                'rating' => 3,
                                'stars' => '3 '.$this->l('stars')
                            ),
                            4 => array(
                                'rating' => 4,
                                'stars' => '4 '.$this->l('stars')
                            ),
                            5 => array(
                                'rating' => 5,
                                'stars' => '5 '.$this->l('stars')
                            ),
                        )
                    )
                ),
                'title' => array(
                    'title' => $this->l('Blog post'),
                    'type' => 'text',
                    'sort' => $this->context->link->getModuleLink($this->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'title','sort_type'=>'asc')),
                    'sort_desc'=>$this->context->link->getModuleLink($this->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'title','sort_type'=>'desc')),
                    'filter' => true,
                    'strip_tag' => false,
                ),
                'approved' => array(
                    'title' => $this->l('Approved'),
                    'type' => 'active',
                    'sort' => $this->context->link->getModuleLink($this->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'approved','sort_type'=>'asc')),
                    'sort_desc'=>$this->context->link->getModuleLink($this->name,'managementcomments',array('tabmanagament'=>'comment_other','sort'=>'approved','sort_type'=>'desc')),
                    'filter' => true,
                    'strip_tag' => false,
                    'filter_list' => array(
                        'id_option' => 'enabled',
                        'value' => 'title',
                        'list' => array(
                            0 => array(
                                'enabled' => 1,
                                'title' => $this->l('Yes')
                            ),
                            1 => array(
                                'enabled' => 0,
                                'title' => $this->l('No')
                            )
                        )
                    )
                )
            );
            //Filter comment
            $filter = " AND bc.id_user ='".(int)$this->context->customer->id."'";
            $tabmanagament = Tools::getValue('tabmanagament');
            if(Tools::isSubmit('ybc_submit_ybc_comment') && $tabmanagament=='comment_other')
            {
                if(($id = trim(Tools::getValue('id_comment')))!='' && Validate::isCleanHtml($id))
                    $filter .= " AND bc.id_comment = ".(int)$id;
                if(($comment_post = trim(Tools::getValue('comment')))!='' && Validate::isCleanHtml($comment_post))
                    $filter .= " AND bc.comment like '%".pSQL($comment_post)."%'";
                if(($subject = trim(Tools::getValue('subject')))!='' && Validate::isCleanHtml($subject))
                    $filter .= " AND (bc.subject like '%".pSQL($subject)."%' OR bc.comment like '%".pSQL($subject)."%' )";
                if(($rating = trim(Tools::getValue('rating')))!='' && Validate::isCleanHtml($rating))
                    $filter .= " AND bc.rating = ".(int)$rating;                
                if(($name = trim(Tools::getValue('name')))!='' && Validate::isCleanHtml($name))
                    $filter .= " AND bc.name like '%".pSQL($name)."%'";
                if(($approved = trim(Tools::getValue('approved')))!='' && Validate::isCleanHtml($approved))
                    $filter .= " AND bc.approved = ".(int)$approved;
                if(($reported = trim(Tools::getValue('reported')))!='' && Validate::isCleanHtml($reported))
                    $filter .= " AND bc.reported = ".(int)$reported;
                if(($title = trim(Tools::getValue('title')))!='' && Validate::isCleanHtml($title))
                    $filter .= " AND pl.title like '%".pSQL($title)."%'";
            }
            //Sort
            $sort = "";
            $sort_post = trim(Tools::getValue('sort'));
            $sort_type = Tools::strtolower(trim(Tools::getValue('sort_type','desc')));
            if(!in_array($sort_type,array('desc','asc')))
                $sort_type='desc';
            if($sort_post && isset($fields_list[$sort_post]))
            {
                $sort .= $sort_post." ".($sort_type=='asc' ? ' ASC ' :' DESC ')." , ";
            }
            else
                $sort = 'bc.id_comment desc,';
            
            //Paggination
            $page = (int)Tools::getValue('page');
            if($page<0)
                $page=1;
            $totalRecords = (int)Ybc_blog_comment_class::countCommentsWithFilter($filter);
            $paggination = new Ybc_blog_paggination_class();            
            $paggination->total = $totalRecords;
            $paggination->url = $this->context->link->getModuleLink('ybc_blog','managementcomments',array('tabmanagament'=>'comment_other','page'=>'_page_',)).$this->getUrlExtraFrontEnd($fields_list,'ybc_submit_ybc_comment');
            $paggination->limit =  (int)Tools::getValue('paginator_ybc_comment_select_limit',20);
            $totalPages = ceil($totalRecords / $paggination->limit);
            if($page > $totalPages)
                $page = $totalPages;
            $paggination->page = $page;
            $start = $paggination->limit * ($page - 1);
            if($start < 0)
                $start = 0;
            $comments = Ybc_blog_comment_class::getCommentsWithFilter($filter, $sort, $start, $paggination->limit);
            if($comments)
            {
                foreach($comments as &$comment)
                {
                    $comment['view_url'] = $this->getLink('blog', array('id_post' => $comment['id_post'])).'#blog_comment_line_'.$comment['id_comment'];
                    $comment['view_text'] = $this->l('View in post');
                    $comment['title'] ='<a href="'.$comment['view_url'].'" title="'.$comment['title'].'">'.$comment['title'].'</a>';
                    if(Ybc_blog_post_employee_class::checkPermisionComment('edit',$comment['id_comment']))
                        $comment['edit_url'] = $this->getLink('blog',array('id_post'=>$comment['id_post'],'edit_comment'=>$comment['id_comment']));
                    if(Ybc_blog_post_employee_class::checkPermisionComment('delete',$comment['id_comment']))
                        $comment['delete_url'] = $this->context->link->getModuleLink($this->name,'managementcomments',array('tabmanagament'=>'comment_other','id_comment'=>$comment['id_comment'],'deletecomment'=>1));
                    
                 }
            }
            $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
            $paggination->style_links = $this->l('links');
            $paggination->style_results = $this->l('results');
            $listData = array(
                'name' => 'ybc_comment',
                'actions' => array('edit', 'delete', 'view'),
                'currentIndex' => $this->context->link->getModuleLink('ybc_blog','managementcomments',array('tabmanagament'=>'comment_other')).($paggination->limit!=20 ? '&paginator_ybc_comment_select_limit='.$paggination->limit:''),
                'identifier' => 'id_comment',
                'show_toolbar' => true,
                'show_action' => true,
                'title' => $this->l('My comments'),
                'fields_list' => $fields_list,
                'field_values' => $comments,
                'paggination' => $paggination->render(),
                'filter_params' => $this->getFilterParamsFontEnd($fields_list,'ybc_submit_ybc_comment'),
                'show_reset' => Tools::isSubmit('ybc_submit_ybc_comment') && isset($comment_post) && ($id_comment!='' || $comment_post!='' || $rating!='' || $subject!='' || $approved!='' || $reported!='' || $title!='') ? true : false,
                'totalRecords' => $totalRecords,
                'show_add_new' => false,
                'sort'=>$sort_post,
                'sort_type'=>$sort_type,
            );            
            return $this->_html .= $this->renderListByCustomer($listData);
        }
        return $this->renderFormCommentByCustomer();
        
    }
    public function sendMailRepyCustomer($id_comment,$replier,$comment_reply=''){
        $comment = new Ybc_blog_comment_class($id_comment);
        if($comment->email && Validate::isEmail($comment->email) && ($id_customer = Customer::customerExists($comment->email,true)) && ($customer = new Customer($id_customer)) && Validate::isLoadedObject($customer))
            $id_lang = $customer->id_lang;
        else
            $id_lang = $this->context->language->id;
        if(($subject = Ybc_blog_email_template_class::getSubjectByTemplate('admin_reply_comment_to_customer',$id_lang)))
        {
            $post = new Ybc_blog_post_class($comment->id_post,$id_lang);
            $reply_comment_text = Tools::getValue('reply_comment_text');
            $template_reply_comment=array(
                '{customer_name}' => $comment->name,
                '{customer_email}' => $comment->email,
                '{comment}' =>$comment->comment,
                '{comment_reply}' => $comment_reply ? $comment_reply : (Validate::isCleanHtml($reply_comment_text) ?  $reply_comment_text :''),
                '{post_link}' => $this->getLink('blog',array('id_post'=>$comment->id_post)),
                '{post_title}'=>$post->title,
                '{replier}' => $replier,
                '{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
                '{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')
            );
            Mail::Send(
    			$id_lang,
    			'admin_reply_comment_to_customer',
    			$subject,
    			$template_reply_comment,
		        $comment->email,
    			$comment->name,
    			null,
    			null,
    			null,
    			null,
    			dirname(__FILE__).'/mails/'
            );
        }
    }
    public function sendMailReplyAdmin($id_comment,$replier,$approved=1,$comment_reply=''){
        $comment = new Ybc_blog_comment_class($id_comment);
        $post_class = new Ybc_blog_post_class($comment->id_post);
        if($post_class->is_customer && ($id_customer= $post_class->added_by))
        {
            $author= new Customer($id_customer);
            $link_view_comment= $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'comment','list'=>1));
        }
        else
        {
            $author = new Employee($post_class->added_by);
            $link_view_comment= $this->getBaseLink().Configuration::get('YBC_BLOG_ADMIN_FORDER');
        }
        $id_lang = $author->id_lang;
        if(($subject = Ybc_blog_email_template_class::getSubjectByTemplate('customer_reply_comment_to_admin_'.$approved,$id_lang)))
        {
            $post_class = new Ybc_blog_post_class($comment->id_post,$id_lang);
            $reply_comment_text = Tools::getValue('reply_comment_text');
            $template_reply_comment=array(
                '{customer_name}' => $comment->name, 
                '{customer_email}' => $comment->email,
                '{comment}' =>$comment->comment,
                '{comment_reply}' => $comment_reply ? $comment_reply : (Validate::isCleanHtml($reply_comment_text) ? $reply_comment_text :''),
                '{post_title}' => $post_class->title,
                '{replier}'=>$replier,
                '{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
                '{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER'),
                '{post_link}' => $this->getLink('blog',array('id_post'=>$post_class->id)),
            );
            
            if($author->id)
            {
                $template_reply_comment['{author_name}'] = $author->firstname.' '.$author->lastname;
                $template_reply_comment['{link_view_comment}'] = $link_view_comment;
                Mail::Send(
        			$id_lang,
        			'customer_reply_comment_to_admin_'.$approved,
        			str_replace('[post_title]',$post_class->title,$subject),
        			$template_reply_comment,
    		        $author->email,
        			$author->firstname.' '.$author->lastname,
        			null,
        			null,
        			null,
        			null,
        			dirname(__FILE__).'/mails/'
                );
            }
            if($emails= explode(',',Configuration::get('YBC_BLOG_ALERT_EMAILS')))
            {
                $link_view_comment= $this->getBaseLink().Configuration::get('YBC_BLOG_ADMIN_FORDER');
                foreach($emails as $email)
                {
                    $template_reply_comment['{author_name}'] = Configuration::get('PS_SHOP_NAME');
                    $template_reply_comment['{link_view_comment}'] = $link_view_comment;
                    if(Validate::isEmail($email))
                        Mail::Send(
            			Context::getContext()->language->id,
            			'customer_reply_comment_to_admin_'.$approved,
            			$this->l('A customer has replied to a comment on ').$post_class->title,
            			$template_reply_comment,
        		        $email,
            			Configuration::get('PS_SHOP_NAME'),
            			null,
            			null,
            			null,
            			null,
            			dirname(__FILE__).'/mails/'
                    );
                }
            }
        }
    }
    public function renderCommentListByCustomer()
    {
        if(Tools::isSubmit('viewcomment') && ($id_comment=(int)Tools::getValue('id_comment')) && ($comment= new Ybc_blog_comment_class($id_comment)) && Validate::isLoadedObject($comment) )
        {
            $errors =array();
            if(Tools::isSubmit('change_approved_comment'))
            {
                if(Ybc_blog_post_employee_class::checkPermisionComment('edit',$comment->id))
                {
                    $approved = (int)Tools::getValue('approved');
                    $comment->approved = $approved;
                    if($comment->update())
                        Tools::redirectLink($this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment->id,'viewcomment'=>1,'updatedComment'=> 1)));
                    else
                        $errors[] = $this->l('Update failed');
                }
                else    
                    $errors[]=  $this->l('Sorry, you do not have permission');
            }
            if(Tools::isSubmit('addReplyComment'))
            {
                if(Ybc_blog_post_employee_class::checkPermisionComment('reply',$id_comment))
                {
                    $reply_comment_text = Tools::getValue('reply_comment_text');
                    if(Tools::strlen($reply_comment_text) < 20)
                        $errors[] = $this->l('Reply needs to be at least 20 characters');
                    if(!Validate::isCleanHtml($reply_comment_text,false))
                        $errors[] = $this->l('Reply needs to be clean HTML');
                    if(Tools::strlen($reply_comment_text) >2000)
                        $errors[] = $this->l('Reply cannot be longer than 2000 characters');
                    if(!$errors)
                    {
                        $replyObj = new Ybc_blog_reply_class();
                        $replyObj->id_comment = (int)$comment->id;
                        $replyObj->id_user = (int)$this->context->customer->id;
                        $replyObj->name = $this->context->customer->firstname.' '.$this->context->customer->lastname;
                        $replyObj->email = $this->context->customer->email;
                        $replyObj->reply = $reply_comment_text;
                        $replyObj->datetime_added = date('Y-m-d H:i:s');
                        $replyObj->datetime_updated = date('Y-m-d H:i:s');
                        if($replyObj->add())
                        {
                            $this->sendMailRepyCustomer($id_comment,$replyObj->name);
                            Tools::redirectLink($this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment->id,'viewcomment'=>1,'addedReply'=> 1)));
                        }
                        else
                            $errors[] = $this->l('Add reply failed');
                    }
                    if($errors)
                    {
                        $this->context->smarty->assign(
                            array(
                                'replyCommentsave' => $id_comment,
                                'reply_comment_text' => $reply_comment_text,
                            )
                        );
                    }
                }
                else
                    $errors[]=$this->l('Sorry, you do not have permission');
                
            }
            if(Tools::isSubmit('delete_reply') && ($id_reply = (int)Tools::getValue('delete_reply')) && ($replyObj = new Ybc_blog_reply_class($id_reply)) && Validate::isLoadedObject($replyObj) )
            {
                if(Ybc_blog_post_employee_class::checkPermisionComment('delete',$id_comment))
                {
                    if($replyObj->delete())
                        Tools::redirectLink($this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment->id,'viewcomment'=>1,'deleteddReply'=> 1)));
                    else
                        $errors[]=$this->l('Delete reply failed');
                }
                else
                    $errors[]=$this->l('Sorry, you do not have permission');
            }
            if(Tools::isSubmit('change_approved_reply') && ($id_reply= (int)Tools::getValue('change_approved_reply')) && ($replyObj = new Ybc_blog_reply_class($id_reply)) && Validate::isLoadedObject($replyObj) )
            {
                if(Ybc_blog_post_employee_class::checkPermisionComment('edit',$id_comment))
                {
                    $approved = (int)Tools::getValue('approved');
                    $status_old = $replyObj->approved;
                    $replyObj->approved = $approved;
                    if($replyObj->update())
                    {
                        if($status_old!=$approved && $approved==1)
                        {
                            $this->sendMailRepyCustomer($id_comment,$replyObj->name,$replyObj->reply);
                        }
                        Tools::redirectLink($this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment->id,'viewcomment'=>1,'updatedReply'=> 1)));
                    }
                    else
                        $errors[]=$this->l('Update reply failed');
                }
                else
                    $errors[]=$this->l('Sorry, you do not have permission');
            }
            $replies= Ybc_blog_comment_class::getRepliesByIdComment($comment->id);
            if($replies)
            {
                foreach($replies as &$reply)
                {
                    if(Ybc_blog_post_employee_class::checkPermisionComment('edit',$comment->id))
                        $reply['link_approved'] = $this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment->id,'viewcomment'=>1,'change_approved_reply'=> $reply['id_reply'],'approved' => $reply['approved'] ? 0 :1));
                    if(Ybc_blog_post_employee_class::checkPermisionComment('delete',$comment->id))
                        $reply['link_delete'] = $this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment->id,'viewcomment'=>1,'delete_reply'=> $reply['id_reply']));
                    $reply['reply'] = str_replace("\n",'<'.'b'.'r/'.'>',$reply['reply']);
                    if($reply['id_employee'])
                    {
                        if(($id = Ybc_blog_post_employee_class::getIdEmployeePostById($reply['id_employee'],false)) && ($employeePost = new Ybc_blog_post_employee_class($id)) && $employeePost->name )
                            $reply['name']= $employeePost->name;
                        elseif(($employee = new Employee($reply['id_employee'])) && Validate::isLoadedObject($employee))
                            $reply['name']= $employee->firstname.' '.$employee->lastname;
                    }
                    if($reply['id_user'])
                    {
                        if(($id = Ybc_blog_post_employee_class::getIdEmployeePostById($reply['id_user'],true)) && ($employeePost = new Ybc_blog_post_employee_class($id)) && $employeePost->name )
                            $reply['name']= $employeePost->name;
                        elseif(($customer = new Customer($reply['id_user'])) && Validate::isLoadedObject($customer))
                            $reply['name']= $customer->firstname.' '.$customer->lastname;
                    }
                }
            }
            $comment->comment = str_replace("\n",'<'.'b'.'r/'.'>',$comment->comment);
            $this->context->smarty->assign(
                array(
                    'comment'=>$comment,
                    'replies'=>$replies,
                    'post_link' => $this->getLink('blog',array('id_post'=>$comment->id_post)),
                    'link_delete' => Ybc_blog_post_employee_class::checkPermisionComment('delete',$comment->id) ? $this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','deletecomment'=>1,'id_comment'=>$comment->id)):'',
                    'link_approved' => Ybc_blog_post_employee_class::checkPermisionComment('edit',$comment->id) ? $this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment->id,'viewcomment'=>1,'change_approved_comment'=>1,'approved' => $comment->approved ? 0 :1)):'',
                    'post_class' => new Ybc_blog_post_class($comment->id_post,$this->context->language->id),
                    'link_back'=> $this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment')),
                )
            );
            return $this->_html .=($errors ? $this->displayError($errors): '' ).$this->display(__FILE__,'author_reply_comment.tpl');
        }
        $id_comment = (int)Tools::getValue('id_comment');
        if(!(Tools::isSubmit('editcomment') && $id_comment))
        {
            $fields_list = array(
                'id_comment' => array(
                    'title' => $this->l('Id'),
                    'width' => 40,
                    'type' => 'text',
                    'sort' => $this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'id_comment','sort_type'=>'asc')),
                    'sort_desc'=>$this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'id_comment','sort_type'=>'desc')),
                    'filter' => true,
                ),
                'subject' => array(
                    'title' => $this->l('Subject'),
                    'type' => 'text',
                    'sort' => $this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'subject','sort_type'=>'asc')),
                    'sort_desc'=>$this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'subject','sort_type'=>'desc')),
                    'filter' => true,                        
                ),
                'title' => array(
                    'title' => $this->l('Blog post'),
                    'type' => 'text',
                    'sort' => $this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'title','sort_type'=>'asc')),
                    'sort_desc'=>$this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'title','sort_type'=>'desc')),
                    'filter' => true, 
                    'strip_tag'=>false,                       
                ),                      
                'rating' => array(
                    'title' => $this->l('Rating'),
                    'type' => 'select',
                    'sort' => $this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'rating','sort_type'=>'asc')),
                    'sort_desc'=>$this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'rating','sort_type'=>'desc')),
                    'filter' => true,
                    'rating_field' => true,
                    'filter_list' => array(
                        'id_option' => 'rating',
                        'value' => 'stars',
                        'list' => array(
                            0 => array(
                                'rating' => 0,
                                'stars' => $this->l('No reviews')
                            ),
                            1 => array(
                                'rating' => 1,
                                'stars' => '1 '.$this->l('star')
                            ),
                            2 => array(
                                'rating' => 2,
                                'stars' => '2 '.$this->l('stars')
                            ),
                            3 => array(
                                'rating' => 3,
                                'stars' => '3 '.$this->l('stars')
                            ),
                            4 => array(
                                'rating' => 4,
                                'stars' => '4 '.$this->l('stars')
                            ),
                            5 => array(
                                'rating' => 5,
                                'stars' => '5 '.$this->l('stars')
                            ),
                        )
                    )
                ),
                'name' => array(
                    'title' => $this->l('Customer'),
                    'type' => 'text',
                    'sort' => $this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'name','sort_type'=>'asc')),
                    'sort_desc'=>$this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'name','sort_type'=>'desc')),
                    'filter' => true
                ),
                'approved' => array(
                    'title' => $this->l('Approved'),
                    'type' => 'active',
                    'sort' => $this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'approved','sort_type'=>'asc')),
                    'sort_desc'=>$this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','sort'=>'approved','sort_type'=>'desc')),
                    'filter' => true,
                    'strip_tag' => false,
                    'filter_list' => array(
                        'id_option' => 'enabled',
                        'value' => 'title',
                        'list' => array(
                            0 => array(
                                'enabled' => 1,
                                'title' => $this->l('Yes')
                            ),
                            1 => array(
                                'enabled' => 0,
                                'title' => $this->l('No')
                            )
                        )
                    )
                )
                
            );
            //Filter
            $filter = " AND p.added_by ='".(int)$this->context->customer->id."' AND p.is_customer=1";
            if(($id_comment = trim(Tools::getValue('id_comment')))!='' && Validate::isCleanHtml($id_comment))
                $filter .= " AND bc.id_comment = ".(int)$id_comment;
            if(($comment = trim(Tools::getValue('comment')))!='' && Validate::isCleanHtml($comment) )
                $filter .= " AND bc.comment like '%".pSQL($comment)."%'";
            if(($subject =  trim(Tools::getValue('subject')))!='' && Validate::isCleanHtml($subject))
                $filter .= " AND (bc.subject like '%".pSQL($subject)."%' OR bc.comment like '%".pSQL($subject)."%' )";
            if(($rating = trim(Tools::getValue('rating')))!='' && Validate::isCleanHtml($rating))
                $filter .= " AND bc.rating = ".(int)$rating;                
            if(($name = trim(Tools::getValue('name')))!='' && Validate::isCleanHtml($name))
                $filter .= " AND bc.name like '%".pSQL($name)."%'";
            if(($approved = trim(Tools::getValue('approved')))!='' && Validate::isCleanHtml($approved))
                $filter .= " AND bc.approved = ".(int)$approved;
            if(($reported =  trim(Tools::getValue('reported')))!='' && Validate::isCleanHtml($reported))
                $filter .= " AND bc.reported = ".(int)$reported;
             if(($title =  trim(Tools::getValue('title')))!='' && Validate::isCleanHtml($title))
                $filter .= " AND pl.title like '%".pSQL($title)."%'";
            //Sort
            $sort_post = Tools::strtolower(Tools::getValue('sort','id_comment'));
            if(!isset($fields_list[$sort_post]))
                $sort_post = 'id_comment';
            $sort_type = Tools::strtolower(trim(Tools::getValue('sort_type','desc')));
            if(!in_array($sort_type,array('asc','desc')))
                $sort_type = 'desc';
            $sort = "";
            if(trim($sort_post) && isset($fields_list[$sort_post]))
            {
                $sort .= trim($sort_post)." ".($sort_type=='asc' ? ' ASC ' :' DESC ')." , ";
            }
            else
                $sort = 'bc.id_comment desc,';
            
            //Paggination
            $page = (int)Tools::getValue('page');
            if($page <0 )
                $page =1;
            $totalRecords = (int)Ybc_blog_comment_class::countCommentsWithFilter($filter);
            $paggination = new Ybc_blog_paggination_class();            
            $paggination->total = $totalRecords;
            $paggination->url = $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'comment','page'=>'_page_',)).$this->getUrlExtraFrontEnd($fields_list,'ybc_submit_ybc_comment');
            $paggination->limit =  (int)Tools::getValue('paginator_ybc_comment_select_limit',20);
            $totalPages = ceil($totalRecords / $paggination->limit);
            if($page > $totalPages)
                $page = $totalPages;
            $paggination->page = $page;
            $start = $paggination->limit * ($page - 1);
            if($start < 0)
                $start = 0;
            $comments = Ybc_blog_comment_class::getCommentsWithFilter($filter, $sort, $start, $paggination->limit);
            if($comments)
            {
                foreach($comments as &$comment_val)
                {
                    $comment_val['child_view_url']=$this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment_val['id_comment'],'viewcomment'=>1));
                    $comment_val['view_url'] = $this->getLink('blog', array('id_post' => $comment_val['id_post'])).'#blog_comment_line_'.$comment_val['id_comment'];
                    $comment_val['title'] ='<a href="'.$comment_val['view_url'].'" title="'.$comment_val['title'].'">'.$comment_val['title'].'</a>';
                    $comment_val['view_text'] = $this->l('View in post');
                    if(($privileges= explode(',',Configuration::get('YBC_BLOG_AUTHOR_PRIVILEGES'))) && in_array('manage_comments',$privileges))
                    {
                        $comment_val['edit_url'] = $this->getLink('blog',array('id_post'=>$comment_val['id_post'],'edit_comment'=>$comment_val['id_comment']));
                        $comment_val['delete_url'] = $this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment_val['id_comment'],'deletecomment'=>1));
                        $comment_val['edit_approved'] = $this->context->link->getModuleLink($this->name,'managementblog',array('tabmanagament'=>'comment','id_comment'=>$comment_val['id_comment'],'commentapproved'=>!$comment_val['approved']));
                    }
                 }
                 unset($comment_val);
            }
            $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
            $paggination->style_links = $this->l('links');
            $paggination->style_results = $this->l('results');
            $listData = array(
                'name' => 'ybc_comment',
                'actions' => array('edit', 'delete', 'view'),
                'currentIndex' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'comment')).($paggination->limit!=20 ? '&paginator_ybc_comment_select_limit='.$paggination->limit:''),
                'identifier' => 'id_comment',
                'show_toolbar' => true,
                'show_action' => true,
                'title' => $this->l('Customer comments'),
                'fields_list' => $fields_list,
                'field_values' => $comments,
                'paggination' => $paggination->render(),
                'filter_params' => $this->getFilterParamsFontEnd($fields_list,'ybc_submit_ybc_comment'),
                'show_reset' => $id_comment!='' || $comment!='' || $rating !='' || $subject !=''  || $approved !='' || $reported !='' || $title!='' ? true : false,
                'totalRecords' => $totalRecords,
                'show_add_new' => false,
                'sort'=> $sort_post,
                'sort_type'=>$sort_type,
            );            
            return $this->_html .= $this->renderListByCustomer($listData);
        }
        return $this->renderFormCommentByCustomer();
    }
    public function hookDisplayRightFormManagament()
    {
        $tabmanagament=Tools::getValue('tabmanagament');
        switch ($tabmanagament) {
            case 'post':
                $content_html_right = $this->renderPostListByCustomer();
                break;
            case 'comment':
                $content_html_right = $this->renderCommentListByCustomer();
                break;
            default:
                $content_html_right= $this->renderPostListByCustomer();
        } 
        $this->context->smarty->assign(
            array(
                'content_html_right'=>$content_html_right,
            )
        );  
        return $this->display(__FILE__,'blog_management_right.tpl');
    }
    public function hookDisplayRightFormComments()
    {
        $this->context->smarty->assign(
            array(
                'content_html_right'=>$this->renderCommentOtherListByCustomer(),
            )
        );  
        return $this->display(__FILE__,'blog_management_right.tpl');
    }
    public function renderFormAuthorInformation(){
        $id_employee_post = (int)Ybc_blog_post_employee_class::getIdEmployeePostById($this->context->customer->id);
        if($id_employee_post)
            $imployeePost = new Ybc_blog_post_employee_class($id_employee_post,$this->context->language->id);
        $this->context->smarty->assign(
            array(
                'name_author'=> isset($imployeePost) && $imployeePost->name ? $imployeePost->name : $this->context->customer->firstname.' '.$this->context->customer->lastname,
                'author_description' => isset($imployeePost) && $imployeePost->description ? $imployeePost->description :'',
                'author_avata' => isset($imployeePost->avata) && $imployeePost->avata ? $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.$imployeePost->avata) :'',
                'avata_default' => $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'avata/'.(Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT')? Configuration::get('YBC_BLOG_IMAGE_AVATA_DEFAULT') : 'default_customer.png')),
                'link_delete_image' => $this->context->link->getModuleLink('ybc_blog','managementmyinfo',array('delemployeeimage'=>1)),
                'action_link' => $this->context->link->getModuleLink($this->name,'managementmyinfo'),
            )
        );
        return $this->display(__FILE__,'form_author.tpl');
    }
    public function displayFormBlog()
    {
        if(($id_post=(int)Tools::getValue('id_post')))
        {
            if(!Ybc_blog_post_employee_class::checkPermistionPost($id_post,'edit_blog'))
            {
                return $this->displayError($this->l('Sorry, you do not have permission'));
            }
            $ybc_post= new Ybc_blog_post_class($id_post,$this->context->language->id);
            $this->context->smarty->assign(
                array(
                    'link_delete_thumb' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','editpost'=>1,'deletethumb'=>1,'id_post'=>$id_post)),
                    'link_delete_image' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post','editpost'=>1,'deleteimage'=>1,'id_post'=>$id_post)),
                    'link_post' => $this->getLink('blog',array('id_post'=>$id_post)),
                )
            ); 
        }  
        else
        {
            if(!Ybc_blog_post_employee_class::checkPermistionPost(0,'add_new'))
                return $this->displayError($this->l('Sorry, you do not have permission'));
            $ybc_post = new Ybc_blog_post_class();
        }
        $params = array(
            'tabmanagament' => 'post',
        );
        if(Tools::isSubmit('editpost'))
        {
            $params['editpost']=1;
        }
        else
            $params['addpost']=1;
        $this->context->smarty->assign(
            array(
                'ybc_post'=>$ybc_post,
                'link'=> $this->context->link,
                'link_back_list' => $this->context->link->getModuleLink('ybc_blog','managementblog',array('tabmanagament'=>'post')),
                'dir_img' => _PS_YBC_BLOG_IMG_,
                'action' => $this->context->link->getModuleLink('ybc_blog','managementblog',$params),
                'html_content_category_block' =>$this->displayBlogCategoryTre(Ybc_blog_category_class::getBlogCategoriesTreeFontEnd(0),$this->getSelectedCategories((int)$id_post),'',Ybc_blog_category_class::getCategoriesDisabled()),
            )
        );
        return $this->display(__FILE__,'form_blog.tpl');
    }
    public function renderFormCommentByCustomer()
    {
        $id_comment = (int)Tools::getValue('id_comment');
        $tabmanagament = Tools::getValue('tabmanagament','comment_other');
        if(!Validate::isCleanHtml($tabmanagament))
            $tabmanagament = 'comment_other';
        if(!Ybc_blog_post_employee_class::checkPermisionComment('',$id_comment,$tabmanagament))
            return $this->displayError($this->l('Sorry, you do not have permission'));
        else
        {
            $ybc_comment= new Ybc_blog_comment_class($id_comment);
            $this->context->smarty->assign(
                array(
                    'ybc_comment'=> $ybc_comment,
                    'link_back_list' => $this->context->link->getModuleLink($this->name,'managementcomments',array('tabmanagament'=>$tabmanagament)),
                    'edit_approved' => $ybc_comment->id_user!=$this->context->customer->id,
                )
            );
            return $this->display(__FILE__,'form_comment_customer.tpl');
        }  
    }
    public function getThumbCategory($id_category,&$thumb,&$lever)
    {
        $category = new Ybc_blog_category_class($id_category,$this->context->language->id);
        if($lever>=1)
            $thumb = ' > '.'<a href="'.$this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=category&list=true&id_parent='.(int)$category->id_category.'">'.$category->title.'</a>'.$thumb;
        else
            $thumb = ' > '.$category->title.$thumb;
        $lever++;
        if($category->id_parent)
            $this->getThumbCategory($category->id_parent,$thumb,$lever);
        return $thumb;
    }
    public function hookBlogCategoryBlock($params)
    {

        if(Configuration::get('YBC_BLOG_SHOW_CATEGORY_BLOCK_HOME'))
        {

            if(($ids = Configuration::get('YBC_BLOG_SHOW_CATEGORIES_BLOCK_HOME')) && $limit=(int)Configuration::get('YBC_BLOG_CATEGORY_POST_NUMBER_HOME'))
            {
                $categoires = Ybc_blog_category_class::getCategoriesWithFilter(' AND c.id_category IN ('.implode(',',array_map('intval',explode(',',$ids))).')',false,false,false,false);
                if($categoires)
                {
                    foreach($categoires as &$category)
                    {
                        if(!Configuration::get('YBC_BLOG_POST_SORT_BY'))
                            $sort = 'p.datetime_added DESC, ';
                        else
                        {
                            if(Configuration::get('YBC_BLOG_POST_SORT_BY')=='sort_order')
                                $sort = 'pc.position ASC, ';
                            elseif(Configuration::get('YBC_BLOG_POST_SORT_BY')=='id_post')
                                $sort = 'p.datetime_added DESC, ';
                            else
                                $sort = 'p.'.Configuration::get('YBC_BLOG_POST_SORT_BY').' DESC, ';
                        }
                        $posts= Ybc_blog_post_class::getPostsWithFilter(" AND p.enabled=1 AND pc.id_category= '".(int)$category['id_category']."'",$sort,0,$limit);
                        if($posts)
                        {
                            foreach($posts as $key => &$post)
                            {
                                $post['link'] = $this->getLink('blog',array('id_post' => $post['id_post']));
                                if($post['thumb'])
                                    $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                                $post['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.$post['id_post'].' AND approved=1');
                                $post['liked'] = $this->isLikedPost($post['id_post']);
                                $post['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($post['id_post'],false,true);
                                
                            }
                            unset($key); 
                        }
                        $category['posts'] = $posts;
                        $category['link_all'] = $this->getLink('blog',array('id_category'=>$category['id_category']));
                    }
                }
                if($categoires)
                {                                  
                    $this->smarty->assign(
                        array(
                            'posts' => $posts,
                            'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false,
                            'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                            'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                            'sidebar_post_type' => Configuration::get('YBC_BLOG_SIDEBAR_POST_TYPE'),
                            'date_format' => trim((string)Configuration::get('YBC_BLOG_DATE_FORMAT')),
                            'hook' => 'homeblog',
                            'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')), 
                            'page' => isset($params['page']) && $params['page'] ? $params['page'] : false,
                            'categoires' => $categoires,
                        )
                    );
                    return $this->display(__FILE__,'categories_home_block.tpl');
                }
            }
        }
        return '';
    }
    public function getDevice()
    {
      return ($userAgent = new Ybc_browser())? $userAgent->getBrowser().' '.$userAgent->getVersion().' '.$userAgent->getPlatform() : $this->l('Unknown');
    }
    public function isLikedPost($id_post)
    {
        if($this->context->customer->logged)
        {
            if(Ybc_blog_comment_class::checkCustomerIsLikePost($id_post))
            {
                return true;
            }
        }
        if(!$this->context->cookie->liked_posts)
            $likedPosts = array();
        else
            $likedPosts = @unserialize($this->context->cookie->liked_posts);
        
        if(is_array($likedPosts) && in_array($id_post, $likedPosts))
            $likedPost = true;
        else
            $likedPost = false;
        return $likedPost;
    }
    public function hookDisplayFooterCategory()
    {
        $id_category = (int)Tools::getValue('id_category');
        return $this->displayPostRelatedCategories($id_category);
    }
    public function displayPostRelatedCategories($id_category)
    {
        if(!Configuration::get('YBC_BLOG_DISPLAY_CATEGORY_PAGE') || !Configuration::get('YBC_BLOG_NUMBER_POST_IN_CATEGORY') || !$id_category)
            return '';         
        $posts= Ybc_blog_post_class::getPostsWithFilter(' AND p.enabled=1 AND rpc.id_category='.(int)$id_category,$this->sort,0,Configuration::get('YBC_BLOG_NUMBER_POST_IN_CATEGORY',8),false);
        if($posts)
            foreach($posts as &$post)
            {
                $post['link'] = $this->getLink('blog',array('id_post' => $post['id_post']));
                if($post['thumb'])
                    $post['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$post['thumb']);
                $post['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post='.$post['id_post'].' AND approved=1');
                $post['liked'] = $this->isLikedPost($post['id_post']);
                $post['categories'] = Ybc_blog_category_class::getCategoriesByIdPost($post['id_post'],false,true);
            }
        $this->smarty->assign(
            array(
                'posts' => $posts,
                'display_desc' => Configuration::get('YBC_BLOG_CATEGORY_PAGE_DISPLAY_DESC'),
                'allowComments' => (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false,
                'show_views' => (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false,
                'allow_like' => (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false,
                'sidebar_post_type' => Configuration::get('YBC_BLOG_CATEGORY_POST_TYPE'),
                'date_format' => trim((string)Configuration::get('YBC_BLOG_DATE_FORMAT')),
                'blog_skin' => Tools::strtolower(Configuration::get('YBC_BLOG_SKIN')), 
                'page' => 'home',
            )
        );
        return $this->display(__FILE__, 'related_posts_category.tpl');
    }
    public function displayReplyComment()
    {
        $id_comment = (int)Tools::getValue('id_comment');
        $control = Tools::getValue('control');
        if(!in_array($control,$this->controls))
            $control ='comment';
        if($id_comment)
        {
            $comment= new Ybc_blog_comment_class($id_comment);
            if(!Validate::isLoadedObject($comment))
            {
                $this->_html .= $this->displayWarning($this->l('Comment not exists'));
                return '';
            }
            else
            {
                $comment->viewed=1;
                $comment->update();
                $comment->comment = str_replace("\n",'<'.'b'.'r/'.'>',$comment->comment);
                $replies= Ybc_blog_comment_class::getRepliesByIdComment($id_comment);
                if($replies)
                {
                    foreach($replies as &$reply)
                    {
                        $reply['reply'] = str_replace("\n",'<'.'b'.'r/'.'>',$reply['reply']);
                        if($reply['id_employee'])
                        {
                            if(($id = Ybc_blog_post_employee_class::getIdEmployeePostById($reply['id_employee'],false)) && ($employeePost = new Ybc_blog_post_employee_class($id)) && $employeePost->name  )
                                $reply['name']= $employeePost->name;
                            elseif(($employee = new Employee($reply['id_employee'])) && Validate::isLoadedObject($employee))
                                $reply['name']= $employee->firstname.' '.$employee->lastname;
                        }
                        if($reply['id_user'])
                        {
                            if(($id = Ybc_blog_post_employee_class::getIdEmployeePostById((int)$reply['id_user'])) && ($employeePost = new Ybc_blog_post_employee_class($id)) && $employeePost->name)
                                $reply['name']= $employeePost->name;
                            elseif(($customer = new Customer($reply['id_user'])) && Validate::isLoadedObject($customer)  && Validate::isLoadedObject($customer))
                                $reply['name']= $customer->firstname.' '.$customer->lastname;
                        }
                    }    
                }
                $this->context->smarty->assign(
                    array(
                        'comment'=>$comment,
                        'replies'=>$replies,
                        'post_class' => new Ybc_blog_post_class($comment->id_post,$this->context->language->id),
                        'curenturl' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control='.$control.'&id_comment='.(int)$id_comment,
                        'link_back'=> $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=comment&list=true',
                        'post_link' => $this->getLink('blog',array('id_post'=>$comment->id_post)),
                        'link_delete' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=comment&id_comment='.(int)$id_comment.'&del=1',
                    )
                );
            }
            
        }
        $this->_html .= $this->display(__FILE__,'reply_comment.tpl');
    }
    public function _posstReply()
    {   
        $id_comment = (int)Tools::getValue('id_comment');
        $control = Tools::getValue('control');
        if(!in_array($control,$this->controls))
            $control='post';
        $errors=array();
        if(Tools::isSubmit('submitBulkActionReply') && ($reply_readed = Tools::getValue('reply_readed')) && Ybc_blog::validateArray($reply_readed) && ($bulk_action_reply =Tools::getValue('bulk_action_reply')) )
        {
            Hook::exec('actionUpdateBlog', array(
                'id_comment' => (int)$id_comment,
            ));
            if($bulk_action_reply=='delete_selected')
            {
                foreach($reply_readed as $id_reply => $value)
                {
                    if($value)
                    {
                        Ybc_blog_comment_class::deleteReply($id_reply);
                    }
                }
                die(json_encode(
                    array(
                        'url_reload' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control='.$control.'&id_comment='.(int)$id_comment.'&conf=2',
                    )
                ));
            }
            else
            {
                if($bulk_action_reply=='mark_as_approved')
                {
                    $approved=1;
                }
                else
                {
                    $approved=0;
                }
                foreach($reply_readed as $id_reply => $value)
                {
                    if($value)
                    {
                        Ybc_blog_comment_class::updateApprovedReply($id_reply,$approved);
                    }
                }
                die(json_encode(
                    array(
                        'url_reload' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control='.$control.'&id_comment='.(int)$id_comment.'&conf=4',
                    )
                ));
            }
        }
        if(Tools::isSubmit('change_approved') && ($id_reply=(int)Tools::getValue('id_reply')) && ($replyObj = new Ybc_blog_reply_class($id_reply)) && Validate::isLoadedObject($replyObj))
        {
            $change_approved = (int)Tools::getValue('change_approved');
            Hook::exec('actionUpdateBlog', array(
                'id_comment' => (int)$id_comment,
            ));
            $approved = $replyObj->approved;
            $replyObj->approved = (int)$change_approved;
            if($replyObj->update())
            {
                if($change_approved)
                    $title = $this->l('Click to mark as unapproved');
                else
                    $title = $this->l('Click to mark as approved');
                if($approved!=$change_approved && $change_approved==1)
                {
                    $this->sendMailRepyCustomer($replyObj->id_comment,$replyObj->name,$replyObj->reply);
                }
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(array(
                        'listId' => $id_reply,
                        'enabled' => $change_approved,
                        'field' => 'approved',
                        'message' => $this->displaySuccessMessage($this->l('The status has been successfully updated')) ,
                        'messageType'=>'success',
                        'title'=>$title,
                        'href' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control='.$control.'&id_comment='.(int)$id_comment.'&change_approved='.($change_approved ? '0' : '1').'&id_reply='.(int)$id_reply,
                    )));
                }
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=post&list=true');
            }
        } 
        if(Tools::isSubmit('change_comment_approved') && $id_comment && ($comment = new Ybc_blog_comment_class(($id_comment))) && Validate::isLoadedObject($comment) )
        {
            $change_comment_approved = (int)Tools::getValue('change_comment_approved');
            Hook::exec('actionUpdateBlog', array(
                'id_comment' => (int)$id_comment,
            ));
            $comment->approved = (int)$change_comment_approved;
            if($comment->update())
            {
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(array(
                        'listId' => $id_reply,
                        'enabled' => $change_comment_approved,
                        'field' => 'approved',
                        'href' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control='.$control.'&id_comment='.(int)$id_comment.'&change_comment_approved='.($change_comment_approved ? '0' : '1'),
                    )));
                }
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=5&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control='.$control.'&id_comment='.(int)$id_comment);
            }
        }
        if(Tools::isSubmit('delreply') && ($id_reply=(int)Tools::getValue('id_reply')) && ($replyObj = new Ybc_blog_reply_class($id_reply)) && Validate::isLoadedObject($replyObj) && $replyObj->delete())
        {
            Hook::exec('actionUpdateBlog', array(
                'id_comment' => (int)$id_comment,
            ));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control='.$control.'&id_comment='.(int)$id_comment.'&conf=2');
        } 
        if(Tools::isSubmit('addReplyComment') && $id_comment && ($comment = new Ybc_blog_comment_class($id_comment)) && Validate::isLoadedObject($comment))
        {
            Hook::exec('actionUpdateBlog', array(
                'id_comment' => $id_comment,
            ));
            $reply_comment_text = Tools::getValue('reply_comment_text');
            if(Tools::strlen($reply_comment_text) < 20)
                $errors[] = $this->l('Reply needs to be at least 20 characters');
            if(!Validate::isCleanHtml($reply_comment_text,false))
                $errors[] = $this->l('Reply needs to be clean HTML');
            if(Tools::strlen($reply_comment_text) >2000)
                $errors[] = $this->l('Reply cannot be longer than 2000 characters');
            if(!$errors)
            {
                $replyObj = new Ybc_blog_reply_class();
                $replyObj->id_comment = $comment->id;
                $replyObj->id_user =0;
                $replyObj->name = $this->context->employee->firstname.' '.$this->context->employee->lastname;
                $replyObj->email = $this->context->employee->email;
                $replyObj->id_employee = $this->context->employee->id;
                $replyObj->approved =1;
                $replyObj->reply = $reply_comment_text;
                $replyObj->datetime_added = date('Y-m-d H:i:s');
                $replyObj->datetime_updated = date('Y-m-d H:i:s');
                if($replyObj->add())
                {
                    $this->sendMailRepyCustomer($id_comment,$this->context->employee->firstname.' '.$this->context->employee->lastname);
                    $this->sendMailReplyAdmin($id_comment,$this->context->employee->firstname.' '.$this->context->employee->lastname,1,$reply_comment_text);
                    $this->_html .= $this->displaySuccessMessage($this->l('Reply has been submitted'));
                }
                else
                    $errors[] = $this->l('Add reply failed');
            }
            if($errors)
            {
                $this->context->smarty->assign(
                    array(
                        'replyCommentsave' => $id_comment,
                        'reply_comment_text' => $reply_comment_text,
                    )
                );
                $this->_html .= $this->displayError($errors);
            }
        }
    }
    public function displayError($error)
    {
        $output = '
        <div class="bootstrap">
        <div class="module_error alert alert-danger" >
            <button type="button" class="close" data-dismiss="alert">&times;</button>';

        if (is_array($error)) {
            $output .= '<ul>';
            foreach ($error as $msg) {
                $output .= '<li>'.$msg.'</li>';
            }
            $output .= '</ul>';
        } else {
            $output .= $error;
        }

        // Close div openned previously
        $output .= '</div></div>';

        $this->error = true;
        if($error)
        {
            $this->context->smarty->assign(
                array(
                    'errors_blog'=>$error
                )
            );
            return $this->display(__FILE__,'errors.tpl');
        }
        return '';
    }
    public function hookDisplayBackOfficeFooter()
    {
        if(version_compare(_PS_VERSION_, '1.6', '<'))
            return '';
        $this->context->smarty->assign(
            array(
                'link_ajax' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name,
            )
        );
        return $this->display(__FILE__,'admin_footer.tpl');
    }
    
    public function hookDisplayFooterYourAccount(){
        $this->context->smarty->assign(
            array(
                'is_17' => $this->is17,
                'my_account_link' => $this->context->link->getPageLink('my-account',Configuration::get('PS_SSL_ENABLED'),$this->context->language->id),
                'home_link' => $this->context->link->getPageLink('index',Configuration::get('PS_SSL_ENABLED'),$this->context->language->id),
            )
        );
        return $this->display(__FILE__,'your_account_footer.tpl');
    }
    public function redirect($url)
    {
        Tools::redirect($url);
    }
    public static function checkIframeHTML($content)
    {
        if(!Configuration::get('PS_ALLOW_HTML_IFRAME') && (Tools::strpos($content,'<iframe')!==false || Tools::strpos($content,'<source')!==false) )
            return false;
        else
            return true;
    }
    public function displayErrorIframe()
    {
        $this->context->smarty->assign(
            array(
                'link' => $this->context->link,
            )
        );
        return $this->display(__FILE__,'iframe.tpl');
    }
    public static function checkIsLinkRewrite($link)
    {
        if (Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')) {
            return preg_match(Tools::cleanNonUnicodeSupport('/^[_a-zA-Z\x{0600}-\x{06FF}\pL\pS-]{1}[_a-zA-Z0-9\x{0600}-\x{06FF}\pL\pS-]+$/u'), $link);
        }
        return preg_match('/^[_a-zA-Z\-]{1}[_a-zA-Z0-9\-]+$/', $link);
    }
    public function hookActionObjectLanguageAddAfter()
    {
       Ybc_blog_defines::duplicateRowsFromDefaultShopLang(_DB_PREFIX_.'ybc_blog_category_lang',$this->context->shop->id,'id_category');
       Ybc_blog_defines::duplicateRowsFromDefaultShopLang(_DB_PREFIX_.'ybc_blog_employee_lang',$this->context->shop->id,'id_employee_post');
       Ybc_blog_defines::duplicateRowsFromDefaultShopLang(_DB_PREFIX_.'ybc_blog_gallery_lang',$this->context->shop->id,'id_gallery');
       Ybc_blog_defines::duplicateRowsFromDefaultShopLang(_DB_PREFIX_.'ybc_blog_post_lang',$this->context->shop->id,'id_post');
       Ybc_blog_defines::duplicateRowsFromDefaultShopLang(_DB_PREFIX_.'ybc_blog_slide_lang',$this->context->shop->id,'id_slide');
       Ybc_blog_defines::duplicateRowsFromDefaultShopLang(_DB_PREFIX_.'ybc_blog_email_template_lang',$this->context->shop->id,'id_ybc_blog_email_template');
       $this->_copyForderMail();
    }
    public function createNewFileName($dir,$name)
    {
        $i=1;
        $file_name = $name;
        while(file_exists($dir.$file_name))
        {
            $file_name =$i.'-'.$name;
            $i++;
        }
        return $file_name;
    }
    public function getTextLang($text, $lang, $file = '')
    {
        $modulePath = rtrim(_PS_MODULE_DIR_, '/') . '/' . $this->name;
        $fileTransDir = $modulePath . '/translations/' . $lang['iso_code'] . '.' . 'php';
        if (!@file_exists($fileTransDir)) {
            return $text;
        }
        $fileContent = Tools::file_get_contents($fileTransDir);
        $strMd5 = md5($text);
        $keyMd5 = '<{' . $this->name . '}prestashop>' . ($file ?: $this->name) . '_' . $strMd5;
        preg_match('/(\$_MODULE\[\'' . preg_quote($keyMd5) . '\'\]\s*=\s*\')(.*)(\';)/', $fileContent, $matches);
        if ($matches && isset($matches[2])) {
            return $matches[2];
        }
        return $text;
    }
    public function getLanguageLink($idLang, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $controller = Dispatcher::getInstance()->getController();
        if (!empty($context->controller->php_self)) {
            $controller = $context->controller->php_self;
        }
        $params = Tools::getAllValues();
        if(isset($params['controller']))
            unset($params['controller']);
        if(isset($params['id_lang']))
            unset($params['id_lang']);
        $id_post = (int)Tools::getValue('id_post');
        if(!$id_post && ($post_url_alias = Tools::getValue('post_url_alias')) && Validate::isLinkRewrite($post_url_alias))
        {
            $id_post = (int)Ybc_blog_post_class::getIDPostByUrlAlias($post_url_alias,$context->language->id);
        }
        if($id_post)
            $params['id_post'] = $id_post;
        $id_category = (int)trim(Tools::getValue('id_category'));
        if(!$id_category && ($category_url_alias = Tools::getValue('category_url_alias')) && Validate::isLinkRewrite($category_url_alias))
        {
            $id_category = (int)Ybc_blog_category_class::getIDCategoryByUrlAlias($category_url_alias,$context->language->id);
        }
        if($id_category)
            $params['id_category'] = $id_category;
        return $this->getLink($controller,$params,$idLang);
    }
    public static function validateArray($array,$validate='isCleanHtml')
    {
        if($array)
        {
            if(!is_array($array))
            return false;
            if(method_exists('Validate',$validate))
            {
                if($array && is_array($array))
                {
                    $ok= true;
                    foreach($array as $val)
                    {
                        if(!is_array($val))
                        {
                            if($val && !Validate::$validate($val))
                            {
                                $ok= false;
                                break;
                            }
                        }
                        else
                            $ok = self::validateArray($val,$validate);
                    }
                    return $ok;
                }
            }
        }
        return true;
    }
    public function initEmailTemplate($default=true)
    {
         return Ybc_blog_email_template_class::getInstance()-> initEmailTemplate($default);
    }
    public function submitSaveEamilTemplate()
    {
        if(Tools::isSubmit('change_enabled'))
        {
            $status = (int)Tools::getValue('change_enabled') ?  1 : 0;
            $field = Tools::getValue('field');    
            if(($id_ybc_blog_email_template = (int)Tools::getValue('id_ybc_blog_email_template')) && ($email_template = new Ybc_blog_email_template_class($id_ybc_blog_email_template)) && Validate::isLoadedObject($email_template))
            {
                $email_template->active = $status;
                if($email_template->update())
                {
                    if($status==1)
                        $title= $this->l('Click to disabled');
                    else
                        $title=$this->l('Click to enabled');
                    if(Tools::isSubmit('ajax'))
                    {
                        die(json_encode(array(
                            'listId' => $id_ybc_blog_email_template,
                            'enabled' => $status,
                            'field' => $field,
                            'message' => $this->displaySuccessMessage($this->l('The status has been successfully updated')) ,
                            'messageType'=>'success',
                            'title'=>$title,
                            'href' => $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control=email&change_enabled='.($status ? '0' : '1').'&field='.$field.'&id_ybc_blog_email_template='.$id_ybc_blog_email_template,
                        )));
                    }
                }
                else
                {
                    die(json_encode(array(
                        'message' => $this->displaySuccessMessage($this->l('Update status failed')) ,
                        'messageType'=>'error',
                    )));
                }
            }
            else
            {
                die(json_encode(array(
                    'message' => $this->displaySuccessMessage($this->l('Email template is not valid')) ,
                    'messageType'=>'error',
                )));
            }
        }
        else
        {
            $errors = array();
            if(($id_ybc_blog_email_template = (int)Tools::getValue('id_ybc_blog_email_template')) && ($email_template = new Ybc_blog_email_template_class($id_ybc_blog_email_template)) && Validate::isLoadedObject($email_template))
            {
                $this->submitSaveMailTemplate($email_template,$errors);
            }
            else
                $errors[] = $this->l("Email template is not valid");
            if($errors)
                $this->errorMessage = $this->displayError($errors);
            else
                Tools::redirectAdmin($this->baseAdminPath.'&control=email&conf=4');
        }  
    }
    public function submitSaveMailTemplate($emailTemplate,&$errors)
    {
        /** @var Ybc_blog_email_template_class  $emailTemplate */
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages();
        $subject = trim(Tools::getValue('subject_'.$id_lang_default));
        $content_html = trim(Tools::getValue('content_html_'.$id_lang_default));
        $content_txt = trim(Tools::getValue('content_txt_'.$id_lang_default));
        $active = (int)Tools::getValue('active');
        if(!$subject)
            $errors[] = $this->l('Subject is required');
        elseif($subject && !Validate::isMailSubject($subject))
            $errors[] = $this->l('Subject is not valid');
        if(!$content_html)
            $errors[] = $this->l('Content in HTML form is required');
        elseif($content_html && !Validate::isCleanHtml($content_html,true))
            $errors[] = $this->l('Content in HTML form is not valid');
        if(!$content_txt)
            $errors[] = $this->l('Content in TXT form is required');
        elseif($content_txt && !Validate::isCleanHtml($content_txt))
            $errors[] = $this->l('Content in TXT form is not valid');
        $content_htmls = array();
        $content_txts = array();
        if(!$errors)
        {
            $emailTemplate->active = $active;
            if($languages)
            {
                foreach($languages as $language)
                {
                    $id_lang = (int)$language['id_lang'];
                    if($id_lang !=$id_lang_default)
                    {
                        $subject_lang = trim(Tools::getValue('subject_'.$id_lang));
                        if($subject_lang && !Validate::isMailSubject($subject_lang))
                            $errors[] = sprintf($this->l('Subject is not valid in %s'),$language['iso_code']);
                        else
                            $emailTemplate->subject[$id_lang] = $subject_lang ? : $subject;
                        $content_html_lang = trim(Tools::getValue('content_html_'.$id_lang));
                        if($content_html_lang && !Validate::isCleanHtml($content_html_lang,true))
                            $errors[] = sprintf($this->l('Content in HTML form is not valid in %s'),$language['iso_code']);
                        else
                            $content_htmls[$id_lang] = $content_html_lang ? : $content_html;
                        $content_txt_lang = trim(Tools::getValue('content_txt_'.$id_lang));
                        if($content_txt_lang && !Validate::isCleanHtml($content_txt_lang))
                            $errors[] = sprintf($this->l('Content in HTML form is not valid in %s'),$language['iso_code']);
                        else
                            $content_txts[$id_lang] = $content_txt_lang ? : $content_txt;
                    }
                    else
                    {
                        $emailTemplate->subject[$id_lang] = $subject;
                        $content_htmls[$id_lang] = $content_html;
                        $content_txts[$id_lang] = $content_txt;
                    }
                }
            }
        }
        if(!$errors)
        {
            if($emailTemplate->update())
            {
                if ($languages) {
                    $base_dir = _PS_ROOT_DIR_ . '/themes/' . ($this->is17 ? Context::getContext()->shop->theme->getName() : Context::getContext()->shop->getTheme()) . '/modules/' . $this->name . '/mails/';
                    if (!is_dir($base_dir))
                        mkdir($base_dir, 0755, true);

                    foreach ($languages as $l) {
                        $id_lang = (int)$l['id_lang'];

                        $iso_path = $base_dir . $l['iso_code'] . '/';
                        if (!is_dir($iso_path))
                            mkdir($iso_path, 0755, true);
                        @file_put_contents($iso_path . $emailTemplate->template . '.html', $content_htmls[$id_lang]);
                        @file_put_contents($iso_path . $emailTemplate->template . '.txt', $content_txts[$id_lang]);
                    }
                }
            }
            else
                $errors[] = $this->l("Update failed");
        }
    }
    public function displayText($content=null,$tag,$class=null,$id=null,$href=null,$blank=false,$src = null,$name = null,$value = null,$type = null,$data_id_product = null,$rel = null,$attr_datas=null)
    {
        $this->smarty->assign(
            array(
                'content' =>$content,
                'tag' => $tag,
                'tag_class'=> $class,
                'tag_id' => $id,
                'href' => $href,
                'blank' => $blank,
                'src' => $src,
                'attr_name' => $name,
                'value' => $value,
                'type' => $type,
                'data_id_product' => $data_id_product,
                'attr_datas' => $attr_datas,
                'rel' => $rel,
            )
        );
        return $this->display(__FILE__,'html.tpl');
    }
    public function displayPaggination($limit,$name)
    {
        if($name)
        {
            $this->context->smarty->assign(
                array(
                    'limit' => $limit,
                    'pageName' => $name,
                )
            );
            return $this->display(__FILE__,'limit.tpl');
        }
    }
    public function getFieldsMailTemplateValues($template)
    {
        $languages = Language::getLanguages();
        $subject = array();
        $content_html = array();
        $content_txt = array();
        $theme = (version_compare(_PS_VERSION_, '1.7', '>=') ? Context::getContext()->shop->theme->getName() : Context::getContext()->shop->getTheme());
        $basePathList = array(
            _PS_ROOT_DIR_ . '/themes/' . $theme . '/modules/ybc_blog/mails/',
            $this->getLocalPath() . 'mails/',
        );
        foreach($languages as $language)
        {
            $id_lang = (int)$language['id_lang'];
            $subject[$id_lang] =  isset($template->subject[$id_lang]) ? $template->subject[$id_lang]:'';
            foreach ($basePathList as $path) {
                $flag = false;
                $iso_path = $path . $language['iso_code'] . '/' . $template->template;
                if (@file_exists($iso_path . '.html')) {
                    $content_html[$id_lang] = Tools::getValue('content_html_'.$id_lang,Tools::file_get_contents($iso_path . '.html')) ;
                    $flag = true;
                }
                if (@file_exists($iso_path . '.txt')) {
                    $content_txt[$id_lang] = Tools::getValue('content_txt_'.$id_lang,Tools::file_get_contents($iso_path . '.txt'));
                }
                if ($flag)
                    break;
            }
        }
        $fields = array();
        $fields['subject'] = $subject;
        $fields['active'] = $template->active;
        $fields['id_ybc_blog_email_template'] = $template->id;
        $fields['content_html'] = $content_html;
        $fields['content_txt'] = $content_txt;
        return $fields;
    }

}