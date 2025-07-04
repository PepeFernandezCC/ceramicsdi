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

if (!defined('_PS_VERSION_'))
	exit;
class Ybc_blogCategoryModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;
    public $display_column_right = false;
    public function __construct()
	{
		parent::__construct();
        if(Configuration::get('YBC_BLOG_SIDEBAR_POSITION')=='right')
            $this->display_column_right=true;
        if(Configuration::get('YBC_BLOG_SIDEBAR_POSITION')=='left')
            $this->display_column_left =true;
		$this->context = Context::getContext();
        $this->module= new Ybc_blog();
	}
	public function init()
	{
		parent::init();
	}
    public function getAlternativeLangsUrl()
    {
        $alternativeLangs = array();
        $languages = Language::getLanguages(true, $this->context->shop->id);

        if ($languages < 2) {
            // No need to display alternative lang if there is only one enabled
            return $alternativeLangs;
        }

        foreach ($languages as $lang) {
            $alternativeLangs[$lang['language_code']] = $this->module->getLanguageLink($lang['id_lang']);
        }
        return $alternativeLangs;
    }
	public function initContent()
	{
		parent::initContent();
        $this->module->setMetas();
        $categoryData = $this->getCategories();
        if(isset($categoryData['categories']) && $categoryData['categories'])
        {
            foreach($categoryData['categories'] as &$category)
            {
                $category['link'] = $this->module->getLink('blog',array('id_category'=>$category['id_category']));
                if($category['image'])
                     $category['image'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'category/'.$category['image']);  
                if($category['thumb'])
                    $category['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'category/thumb/'.$category['thumb']);
                $category['count_posts'] = (int)Ybc_blog_post_class::getCountPostByIDCategory($category['id_category']);
                $category['sub_categogires'] = Ybc_blog_category_class::getCategoriesWithFilter(' AND c.enabled=1',false,false,false,$category['id_category']);
                if($category['sub_categogires'])
                {
                    foreach($category['sub_categogires'] as &$sub)
                    {
                        $sub['link'] = $this->module->getLink('blog',array('id_category'=>$sub['id_category']));
                    }
                }    
            }
        }
        $this->context->smarty->assign(
            array(
                'blog_categories' => $categoryData['categories'],
                'blog_paggination' => $categoryData['paggination'],
                'path' => $this->module->getBreadCrumb(),
                'blog_layout' => Tools::strtolower(Configuration::get('YBC_BLOG_LAYOUT')),                 
                'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false,
                'show_date' => (int)Configuration::get('YBC_BLOG_SHOW_POST_DATE') ? true : false,
                'date_format' => trim((string)Configuration::get('YBC_BLOG_DATE_FORMAT')),
                'image_folder' => _PS_YBC_BLOG_IMG_.'category/',
            )
        );
        if(Tools::isSubmit('loadajax'))
        {
            $this->module->loadMoreCategories($categoryData);
        }
        if($this->module->is17)
            $this->setTemplate('module:ybc_blog/views/templates/front/list-category.tpl');      
        else  
            $this->setTemplate('list-category-16.tpl');                
	}    
    public function getCategories()
    {
        $filter = ' AND c.enabled = 1 AND id_parent=0';            
        $sort = ' c.sort_order asc, c.id_category asc, ';
        $module = new Ybc_blog();
        
        $page = (int)Tools::getValue('page');
        if($page < 1)
            $page = 1;
        $totalRecords = (int)Ybc_blog_category_class::countCategoriesWithFilter($filter);
        $paggination = new Ybc_blog_paggination_class();            
        $paggination->total = $totalRecords;
        $paggination->url = $module->getLink('category', array('page'=>"_page_"));
        $paggination->limit =  8;
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $categories = Ybc_blog_category_class::getCategoriesWithFilter($filter, $sort, $start, $paggination->limit);
        return array(
            'categories' => $categories , 
            'paggination' => $paggination->render()
        );
    }
}