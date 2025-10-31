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

if (!defined('_PS_VERSION_')) { exit; }

/**
 * Class AdminYbcBlogPostController
 * @property Ybc_blog $module;
 */
class AdminYbcBlogPostController extends ModuleAdminController
{
    public $errorMessage;
    public $baseLink;
    protected $checked = false;
    public function init()
    {
        parent::init();
        $this->baseLink = $this->context->link->getAdminLink('AdminYbcBlogPost');
        $this->bootstrap = true;
        if($this->module->checkProfileEmployee($this->context->employee->id,'Blog posts and blog categories'))
        {
            $this->checked = true;
            $this->_postPost();
            if(Tools::isSubmit('chatGPT'))
            {
                Ybc_chatgpt::getInstance()->chatGPT();
            }
            if(Tools::isSubmit('ajaxproductsearch'))
                $this->ajaxProductSearch();
            if(Tools::isSubmit('ajaxCustomersearch'))
                $this->ajaxCustomerSearch();
        }
        if(Tools::isSubmit('searchPostByQuery') && ($query = (string)Tools::getValue('q')) && Validate::isCleanHtml($query))
        {
            $posts = Ybc_blog_post_class::getPostsByQuery($this->context, $query);
            if ($posts)
            {
                foreach($posts as &$rpost)
                {
                    if($rpost['image']) {
                        $rpost['image'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_ . 'post/' . $rpost['image']);
                        if ($rpost['thumb'])
                            $rpost['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_ . 'post/thumb/' . $rpost['thumb']);
                        else
                            $rpost['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_ . 'post/' . $rpost['image']);
                        $rpost['link'] = $this->module->getLink('blog', array('id_post' => $rpost['id_post']));
                    }
                    else
                    {
                        $rpost['image'] = '';
                        if($rpost['thumb'])
                            $rpost['thumb'] = $this->context->link->getMediaLink(_PS_YBC_BLOG_IMG_.'post/thumb/'.$rpost['thumb']);
                        else
                            $rpost['thumb'] = '';
                        $rpost['link'] =   $this->module->getLink('blog',array('id_post'=>$rpost['id_post']));
                    }
                }
            }
            die(
                json_encode(
                    array(
                        'posts' => $posts,
                    )
                )
            );
        }
        if(Tools::isSubmit('submitAddPostRelatedProduct') && ($id_post = (Int)Tools::getValue('id_post')) && ($id_product = Tools::getValue('id_product')))
        {
            if(Ybc_blog_post_class::addPostRelatedProduct($id_post,$id_product))
            {
                die(
                    json_encode(
                        array(
                            'success' =>$this->module->l('Added successfully', 'AdminYbcBlogPostController'),
                        )
                    )
                );
            }
            else
            {
                die(
                    json_encode(
                        array(
                            'errors' =>$this->module->l('Post added', 'AdminYbcBlogPostController'),
                        )
                    )
                );
            }
        }
        if(Tools::isSubmit('submitDeletePostProduct') && ($id_post = (Int)Tools::getValue('id_post')) && ($id_product = Tools::getValue('id_product')))
        {
            Ybc_blog_post_class::deletePostProduct($id_post,$id_product);
            die(
                json_encode(
                    array(
                        'success' =>$this->module->l('Deleted successfully', 'AdminYbcBlogPostController'),
                    )
                )
            );
        }
    }
    public function renderList()
    {
        if(!$this->checked)
            return $this->module->display($this->module->getLocalPath(),'error_access.tpl');
        $this->context->smarty->assign(array_merge($this->module->getAssign('post'), array('ybc_blog_body_html' => $this->_getContent())));
        return $this->module->display(_PS_MODULE_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . $this->module->name . '.php', 'admin.tpl');
    }

    public function _getContent()
    {
        if (Tools::isSubmit('addNew') || Tools::isSubmit('editybc_post'))
            return $this->renderPostForm();
        else
            return $this->renderListPost();
    }
    public function ajaxCustomerSearch()
    {
        $query = Tools::getValue('q', false);
        if (!$query OR $query == '' OR (Tools::strlen($query) < 3 AND !Validate::isUnsignedId($query)) OR !Validate::isCleanHtml($query))
            die();
        $filter ='AND (';
        $filter .= " c.id_customer = ".(int)trim(urldecode($query));
        $filter .= " OR (CONCAT(c.firstname,' ',c.lastname) like '".pSQL($query)."%' OR be.name like'".pSQL($query)."%')";
        $filter .= " OR c.email like '".pSQL($query)."%'";
        $filter .=')';
        $customers= Ybc_blog_post_employee_class::getCustomersFilter($this->context, $filter);
        if($customers)
        {
            foreach ($customers as $customer)
            {
                echo $customer['id_customer'].'|'.($customer['name'] ? $customer['name'] : $customer['customer'] ).'|'.$customer['email'].'|'.$this->context->link->getAdminLink('AdminCustomers').'&id_customer='.(int)$customer['id_customer'].'&updatecustomer'."\n";
            }
        }
        die();
    }
    public function ajaxProductSearch()
    {
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
        $items = Ybc_blog_defines::getProducts($this->context->language->id, $query,$excludeIds,$excludeVirtuals,$exclude_packs);
        $acc = (bool)Tools::isSubmit('excludeIds');
        $type_image= Ybc_blog::getFormattedName('home', $this->context);
        if ($items && $acc)
            foreach ($items AS $item)
            {
                $link_product= $this->context->link->getProductLink($item['id_product'],null,null,null,null,null,Product::getDefaultAttribute($item['id_product']));
                echo trim(str_replace('|','-',$item['name'])).(!empty($item['reference']) ? ' (ref: '.str_replace('|','-',$item['reference']).')' : '').'|'.(int)($item['id_product']).'|'.str_replace('http://', Tools::getShopProtocol(), $this->context->link->getImageLink($item['link_rewrite'], $item['id_image'], $type_image)).'|'.$link_product."\n";
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
                    'image' => str_replace('http://', Tools::getShopProtocol(), $this->context->link->getImageLink($item['link_rewrite'], $item['id_image'], $type_image)),
                );
                array_push($results, $product);
            }
            echo json_encode($results);
        }
        else
            json_encode(new stdClass);
        die;
    }
    public function renderListPost()
    {
        $show_reset = false;
        $fields_list = array(
            'input_box' => array(
                'title' =>'',
                'type'=>'text',
            ),
            'id_post' => array(
                'title' =>$this->module->l('ID', 'AdminYbcBlogPostController'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'thumb_link' => array(
                'title' =>$this->module->l('Image', 'AdminYbcBlogPostController'),
                'type' => 'text',
                'strip_tag' => false,
            ),
            'title' => array(
                'title' =>$this->module->l('Title', 'AdminYbcBlogPostController'),
                'type' => 'text',
                'sort' => true,
                'filter' => true
            ),
            'id_category' => array(
                'title' =>$this->module->l('Categories', 'AdminYbcBlogPostController'),
                'type' => 'select',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'id_option' => 'id_category',
                    'value' => 'title',
                    'list' => Ybc_blog_category_class::getCategories($this->context)
                )
            ),
            'name_author' => (
            array(
                'title' =>$this->module->l('Author', 'AdminYbcBlogPostController'),
                'type' => 'text',
                'filter' => true,
                'strip_tag' => false,
            )
            ),
            'sort_order' => array(
                'title' =>$this->module->l('Sort order', 'AdminYbcBlogPostController'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'update_position' => true,
            ),
            'position' => array(
                'title' =>$this->module->l('Sort order', 'AdminYbcBlogPostController'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'update_position' => true,
            ),
            'click_number' => array(
                'title' =>$this->module->l('Views', 'AdminYbcBlogPostController'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'likes' => array(
                'title' =>$this->module->l('Likes', 'AdminYbcBlogPostController'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'enabled' => array(
                'title' =>$this->module->l('Status', 'AdminYbcBlogPostController'),
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
                            'title' =>$this->module->l('Published', 'AdminYbcBlogPostController')
                        ),
                        1 => array(
                            'enabled' => -1,
                            'title' =>$this->module->l('Pending', 'AdminYbcBlogPostController')
                        ),
                        2 => array(
                            'enabled' => 0,
                            'title' =>$this->module->l('Disabled', 'AdminYbcBlogPostController')
                        ),
                        3 => array(
                            'enabled' => -2,
                            'title' =>$this->module->l('Preview', 'AdminYbcBlogPostController')
                        ),
                        4 => array(
                            'enabled' => 2,
                            'title' =>$this->module->l('Schedule publish date', 'AdminYbcBlogPostController')
                        )
                    )
                )
            ),
            'is_featured' => array(
                'title' =>$this->module->l('Featured', 'AdminYbcBlogPostController'),
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
                            'title' =>$this->module->l('Yes', 'AdminYbcBlogPostController')
                        ),
                        1 => array(
                            'is_featured' => 0,
                            'title' =>$this->module->l('No', 'AdminYbcBlogPostController')
                        )
                    )
                )
            ),
        );
        if (($idCategory = Tools::getValue('id_category')) != '' && Validate::isInt($idCategory))
            unset($fields_list['sort_order']);
        else
            unset($fields_list['position']);
        //Filter
        $filter = '';
        if (($idPost = trim(Tools::getValue('id_post'))) != '' && Validate::isCleanHtml($idPost))
            $filter .= " AND p.id_post = " . (int)$idPost;
        if (($sort_order = trim(Tools::getValue('sort_order'))) != '' && Validate::isCleanHtml($sort_order))
            $filter .= " AND p.sort_order = " . (int)$sort_order;
        if (($click_number = trim(Tools::getValue('click_number'))) != '' && Validate::isCleanHtml($click_number))
            $filter .= " AND p.click_number = " . (int)$click_number;
        if (($likes = trim(Tools::getValue('likes'))) != '' && Validate::isCleanHtml($likes))
            $filter .= " AND p.likes = " . (int)$likes;
        if (($title = trim(Tools::getValue('title'))) != '' && Validate::isCleanHtml($title))
            $filter .= " AND pl.title like '%" . pSQL($title) . "%'";
        if (($description = trim(Tools::getValue('description'))) != '' && Validate::isCleanHtml($description))
            $filter .= " AND pl.description like '%" . pSQL($description) . "%'";
        if (($id_category = trim(Tools::getValue('id_category'))) != '' && Validate::isCleanHtml($id_category))
            $filter .= " AND p.id_post IN (SELECT id_post FROM `" . _DB_PREFIX_ . "ybc_blog_post_category` WHERE id_category = " . (int)$id_category . ") ";
        if (($enabled = trim(Tools::getValue('enabled'))) != '' && Validate::isCleanHtml($enabled))
            $filter .= " AND p.enabled = " . (int)$enabled;
        if (($is_featured = trim(Tools::getValue('is_featured'))) != '' && Validate::isCleanHtml($is_featured))
            $filter .= " AND p.is_featured = " . (int)$is_featured;
        if (($name_author = trim(Tools::getValue('name_author'))) != '' && Validate::isCleanHtml($name_author))
            $filter .= " AND (CONCAT(e.firstname,' ', e.lastname) like '%" . pSQL($name_author) . "%' OR CONCAT(c.firstname,' ', c.lastname) like '%" . pSQL($name_author) . "%')";
        //Sort
        $sort = 'p.id_post DESC,';
        $sort_post = Tools::strtolower(trim(Tools::getValue('sort')));
        $sort_type = Tools::strtolower(Tools::getValue('sort_type', 'desc'));
        if ($sort_post && isset($fields_list[$sort_post])) {
            $sort = $sort_post . " " . ($sort_type == 'asc' ? ' ASC ' : ' DESC ') . " , ";
        }
        if ($filter)
            $show_reset = true;

        //Paggination
        $page = (int)Tools::getValue('page');
        if ($page <= 1)
            $page = 1;
        $totalRecords = (int)Ybc_blog_post_class::countPostsWithFilter($this->context, $filter, false);
        $paggination = new Ybc_blog_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->baseLink . '&page=_page_' . $this->module->getUrlExtra($fields_list);
        $paggination->limit = (int)Tools::getValue('paginator_ybc_post_select_limit', 20);
        $paggination->name = 'ybc_post';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if ($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if ($start < 0)
            $start = 0;
        $posts = Ybc_blog_post_class::getPostsWithFilter($this->context, $filter, $sort, $start, $paggination->limit, false);
        if ($posts) {
            foreach ($posts as &$post) {
                $post['id_category'] = $this->module->getCategoriesStrByIdPost($post['id_post']);
                $url = $this->module->getLink('blog', array('id_post' => $post['id_post']));
                if ($post['enabled'] == -2) {
                    if (Tools::strpos('?', $url) !== false)
                        $url .= '&preview=1';
                    else
                        $url .= '?preview=1';
                }
                $post['view_url'] = $url;
            }
        }
        $paggination->text =$this->module->l('Showing {start} to {end} of {total} ({pages} Pages)', 'AdminYbcBlogPostController');
        $paggination->style_links =$this->module->l('links', 'AdminYbcBlogPostController');
        $paggination->style_results =$this->module->l('results', 'AdminYbcBlogPostController');

        $listData = array(
            'name' => 'ybc_post',
            'actions' => array('edit', 'delete', 'view'),
            'currentIndex' => $this->baseLink . ($paggination->limit != 20 ? '&paginator_ybc_post_select_limit=' . $paggination->limit : ''),
            'identifier' => 'id_post',
            'show_toolbar' => true,
            'show_action' => true,
            'title' =>$this->module->l('Posts', 'AdminYbcBlogPostController'),
            'fields_list' => $fields_list,
            'field_values' => $posts,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list),
            'show_reset' => $show_reset,
            'totalRecords' => $totalRecords,
            'preview_link' => $this->module->getLink('blog'),
            'sort' => $sort_post ?: 'id_post',
            'sort_type' => $sort_type,
            'show_bulk_action' => true,
            'bulk_actions' =>$this->getBulkActions(),
        );
        return $this->module->renderList($listData);
    }
    public function getBulkActions(){
        return array(
            array(
                'action'=>'Enable',
                'title' =>$this->module->l('Enable', 'AdminYbcBlogPostController'),
                'confirm' =>$this->module->l('Do you want to enable selected items?', 'AdminYbcBlogPostController'),
            ),
            array(
                'action'=>'Disable',
                'title' =>$this->module->l('Disable', 'AdminYbcBlogPostController'),
                'confirm' =>$this->module->l('Do you want to disable selected items?', 'AdminYbcBlogPostController'),
            ),
            array(
                'action'=>'MarkAsFeature',
                'title' =>$this->module->l('Mark as featured', 'AdminYbcBlogPostController'),
                'confirm' =>$this->module->l('Do you want to mark selected items as featured?', 'AdminYbcBlogPostController')
            ),
            array(
                'action'=>'UnMarkAsFeature',
                'title' =>$this->module->l('Unmark as featured', 'AdminYbcBlogPostController'),
                'confirm' =>$this->module->l('Do you want to unmark featured items?', 'AdminYbcBlogPostController')
            ),
            array(
                'action'=>'Delete',
                'title' =>$this->module->l('Delete', 'AdminYbcBlogPostController'),
                'confirm' =>$this->module->l('Do you want to delete selected items?', 'AdminYbcBlogPostController')
            ),
        );
    }
    public function renderPostForm()
    {
        $id_post = (int)Tools::getValue('id_post');
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' =>$this->module->l('Manage posts', 'AdminYbcBlogPostController'),
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' =>$this->module->l('Post title', 'AdminYbcBlogPostController'),
                        'name' => 'title',
                        'lang' => true,
                        'required' => true,
                        'tab' => 'basic',
                        'class' => 'title',
                    ),
                    array(
                        'type' => 'text',
                        'label' =>$this->module->l('Meta title', 'AdminYbcBlogPostController'),
                        'name' => 'meta_title',
                        'lang' => true,
                        'tab' => 'seo',
                        'desc' =>$this->module->l('Should contain your focus keyword and be attractive', 'AdminYbcBlogPostController'),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' =>$this->module->l('Meta description', 'AdminYbcBlogPostController'),
                        'name' => 'meta_description',
                        'lang' => true,
                        'tab' => 'seo',
                        'desc' =>$this->module->l('Should contain your focus keyword and be attractive. Meta description should be less than 300 characters.', 'AdminYbcBlogPostController'),
                    ),
                    array(
                        'type' => 'tags',
                        'label' =>$this->module->l('Meta keywords', 'AdminYbcBlogPostController'),
                        'name' => 'meta_keywords',
                        'lang' => true,
                        'tab' => 'seo',
                        'hint' => array(
                           $this->module->l('To add "keywords" click in the field, write something, and then press "Enter."', 'AdminYbcBlogPostController'),
                        ),
                        'desc' =>$this->module->l('Enter your focus keywords and minor keywords', 'AdminYbcBlogPostController'),
                    ),
                    array(
                        'type' => 'text',
                        'label' =>$this->module->l('Url alias', 'AdminYbcBlogPostController'),
                        'name' => 'url_alias',
                        'required' => true,
                        'lang' => true,
                        'tab' => 'seo',
                        'desc' =>$this->module->l('Should be as short as possible and contain your focus keyword. ', 'AdminYbcBlogPostController') . ($id_post ? $this->module->displayText($this->module->l(' View post', 'AdminYbcBlogPostController'), 'a', 'ybc_link_view', null, $this->module->getLink('blog', array('id_post' => $id_post)), true) : ''),
                    ),
                    array(
                        'type' => 'tags',
                        'label' =>$this->module->l('Tags', 'AdminYbcBlogPostController'),
                        'name' => 'tags',
                        'lang' => true,
                        'tab' => 'option',
                        'hint' => array(
                           $this->module->l('To add "tags" click in the field, write something, and then press "Enter."', 'AdminYbcBlogPostController'),
                        ),
                        'desc' =>$this->module->l('Tags are separated by a comma. Related posts are the posts in the same tag or in the same post categories.', 'AdminYbcBlogPostController'),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' =>$this->module->l('Short description', 'AdminYbcBlogPostController'),
                        'name' => 'short_description',
                        'lang' => true,
                        'required' => true,
                        'autoload_rte' => true,
                        'tab' => 'basic',
                        'desc' =>$this->module->l('Short description is displayed in post listing pages', 'AdminYbcBlogPostController'),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' =>$this->module->l('Post content', 'AdminYbcBlogPostController'),
                        'name' => 'description',
                        'lang' => true,
                        'autoload_rte' => true,
                        'required' => true,
                        'tab' => 'basic',
                        'desc' =>$this->module->l('Post content is displayed in post details page (single page).', 'AdminYbcBlogPostController'),
                    ),
                    array(
                        'type' => 'file_lang',
                        'label' =>$this->module->l('Post thumbnail', 'AdminYbcBlogPostController'),
                        'name' => 'thumb',
                        'imageType' => 'thumb',
                        'required' => true,
                        'tab' => 'basic',
                        'desc' => sprintf($this->module->l('Accepted formats: jpg, jpeg, png, gif. Limit: %dMb. Recommended size: %sx%s. Post thumbnail image is required. You should adjust your image to the recommended size before uploading it.', 'AdminYbcBlogPostController'), Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'), Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_WIDTH', null, null, null, 260), Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_HEIGHT', null, null, null, 180)),
                    ),
                    array(
                        'type' => 'file_lang',
                        'label' =>$this->module->l('Blog post main image', 'AdminYbcBlogPostController'),
                        'name' => 'image',
                        'tab' => 'basic',
                        'desc' => sprintf($this->module->l('Accepted formats: jpg, jpeg, png, gif. Limit: %dMb. Recommended size: %sx%s.', 'AdminYbcBlogPostController'), Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'), Configuration::get('YBC_BLOG_IMAGE_BLOG_WIDTH', null, null, null, 1920), Configuration::get('YBC_BLOG_IMAGE_BLOG_HEIGHT', null, null, null, 750))
                    ),
                    array(
                        'type' => 'blog_categories',
                        'label' =>$this->module->l('Post categories', 'AdminYbcBlogPostController'),
                        'html_content' => $this->module->displayBlogCategoryTre(Ybc_blog_category_class::getBlogCategoriesTree($this->context, 0), $this->module->getSelectedCategories($id_post)),
                        'categories' => Ybc_blog_category_class::getBlogCategoriesTree($this->context, 0),
                        'name' => 'categories',
                        'required' => true,
                        'tab' => 'basic',
                        'selected_categories' => $this->module->getSelectedCategories($id_post)
                    ),
                    array(
                        'type' => 'categories2',
                        'label' =>$this->module->l('Select product categories for related items', 'AdminYbcBlogPostController'),
                        'name' => 'product_categories',
                        'tab' => 'option',
                        'tree' => array(
                            'id' => 'product-categories-tree',
                            'selected_categories' => Ybc_blog_category_class::getSelectedRelatedProductCategories($id_post),
                            'use_search' => true,
                            'use_checkbox' => true,
                        ),
                        'desc' =>$this->module->l('Choose the product categories to display related products below this blog post. These products will appear in a dedicated block on the front office.', 'AdminYbcBlogPostController')
                    ),
                    array(
                        'type' => 'products_search',
                        'label' =>$this->module->l('Include products', 'AdminYbcBlogPostController'),
                        'name' => 'products',
                        'selected_products' => $this->getSelectedProducts($id_post),
                        'tab' => 'option',
                    ),
                    array(
                        'type' => 'exclude_products',
                        'label' =>$this->module->l('Exclude products', 'AdminYbcBlogPostController'),
                        'name' => 'exclude_products',
                        'selected_products' => $this->getExcludeProducts($id_post),
                        'tab' => 'option',
                    ),
                    array(
                        'type' => 'categories',
                        'label' =>$this->module->l('Related product categories', 'AdminYbcBlogPostController'),
                        'name' => 'related_categories',
                        'tab' => 'option',
                        'tree' => array(
                            'id' => 'categories-tree',
                            'selected_categories' => Ybc_blog_category_class::getSelectedRelatedCategories($id_post),
                            'use_search' => true,
                            'use_checkbox' => true,
                        ),
                        'showRequired' => true,
                        'desc' =>$this->module->l('Check on product categories that you want to display this post on their "Related posts" section on the front office', 'AdminYbcBlogPostController'),
                    ),
                    array(
                        'type' => 'text',
                        'label' =>$this->module->l('Views', 'AdminYbcBlogPostController'),
                        'name' => 'click_number',
                        'required' => true,
                        'tab' => 'option',
                        'desc' =>$this->module->l('The number of post view will be increased from this number', 'AdminYbcBlogPostController'),
                    ),
                    array(
                        'type' => 'text',
                        'label' =>$this->module->l('Likes', 'AdminYbcBlogPostController'),
                        'name' => 'likes',
                        'required' => true,
                        'tab' => 'option',
                        'desc' =>$this->module->l('The number of post likes will be increased from this number', 'AdminYbcBlogPostController'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' =>$this->module->l('Is featured post', 'AdminYbcBlogPostController'),
                        'name' => 'is_featured',
                        'is_bool' => true,
                        'tab' => 'option',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' =>$this->module->l('Yes', 'AdminYbcBlogPostController')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' =>$this->module->l('No', 'AdminYbcBlogPostController')
                            )
                        ),
                        'desc' =>$this->module->l('Enable this if you want to display this post in "Featured posts" section on the front office', 'AdminYbcBlogPostController')
                    ),
                    array(
                        'type' => 'select',
                        'label' =>$this->module->l('Status', 'AdminYbcBlogPostController'),
                        'name' => 'enabled',
                        'tab' => 'basic',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 1,
                                    'name' =>$this->module->l('Published', 'AdminYbcBlogPostController')
                                ),
                                array(
                                    'id_option' => -1,
                                    'name' =>$this->module->l('Pending', 'AdminYbcBlogPostController')
                                ),
                                array(
                                    'id_option' => -2,
                                    'name' =>$this->module->l('Preview', 'AdminYbcBlogPostController'),
                                ),
                                array(
                                    'id_option' => 0,
                                    'name' =>$this->module->l('Disabled', 'AdminYbcBlogPostController')
                                ),
                                array(
                                    'id_option' => 2,
                                    'name' =>$this->module->l('Schedule publish date', 'AdminYbcBlogPostController')
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'datetime',
                        'label' =>$this->module->l('Publish date', 'AdminYbcBlogPostController'),
                        'name' => 'datetime_added',
                        'tab' => 'basic',
                        'form_group_class' =>'setting_customer_author',
                    ),
                    array(
                        'type' => 'date',
                        'label' =>$this->module->l('Schedule publish date', 'AdminYbcBlogPostController'),
                        'name' => 'datetime_active',
                        'tab' => 'basic',
                        'required2' => true,
                        'desc' =>$this->module->l('You can select the time to automatically publish this post. Leave blank to save this post as draft', 'AdminYbcBlogPostController'),
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'control'
                    )
                ),
                'submit' => array(
                    'title' =>$this->module->l('Save', 'AdminYbcBlogPostController'),
                ),
                'buttons' => array(
                    array(
                        'type' => 'submit',
                        'name' => 'submitSaveAndPreview',
                        'title' =>$this->module->l('Save and preview', 'AdminYbcBlogPostController'),
                        'class' => 'pull-right',
                        'icon' => 'process-icon-save',
                    )
                ),
            ),
        );
        if (version_compare(_PS_VERSION_, '8.0.4', '>=')) {
            $smarty = $this->context->smarty->_getSmartyObj();
            if (!isset($smarty->registered_plugins['modifier']['implode']))
                $this->context->smarty->registerPlugin('modifier', 'implode', 'implode');
        }
        foreach ($fields_form['form']['input'] as &$params) {
            if ($params['type'] == 'categories2') {
                $tree = new HelperTreeCategories($params['tree']['id'], null);
                $tree->setInputName($params['name']);
                $tree->setUseSearch($params['tree']['use_search']);
                $tree->setUseCheckBox($params['tree']['use_checkbox']);
                $this->context->smarty->assign('categories_tree2', $tree->render());
                break;
            }
        }
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = 'module';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->module = $this->module;
        $helper->identifier = 'id_post';
        $helper->submit_action = 'savePost';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminYbcBlogPost', false);
        $helper->token = $this->context->employee->id ? Tools::getAdminTokenLite('AdminYbcBlogPost') : false;
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $chatgpt = Configuration::get('YBC_BLOG_ENABLED_GPT');
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
            'fields_value' => $this->module->getFieldsValues(Ybc_blog_defines::getPostField(), 'id_post', 'Ybc_blog_post_class', 'savePost'),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'link' => $this->context->link,
            'post_key' => 'id_post',
            'tab_post' => true,
            'YBC_BLOG_ENABLED_GPT' => $chatgpt,
            'check_suspend' => Ybc_blog_post_class::checkPostSuspend($id_post),
            'form_author_post' => $this->getFormAuthorPost($id_post),
            'cancel_url' => $this->baseLink,
            'image_baseurl' => _PS_YBC_BLOG_IMG_ . 'post/',
            'image_baseurl_thumb' => _PS_YBC_BLOG_IMG_ . 'post/thumb/',
            'addNewUrl' => $this->baseLink . '&addNew=1',
            'preview_link' => $id_post ? $this->module->getLink('blog', array('id_post' => $id_post)) : '',
        );
        $post = new Ybc_blog_post_class((int)$id_post);
        if (Validate::isLoadedObject($post)) {
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_post');
            if ($post->image) {
                $helper->tpl_vars['img_del_link'] = $this->baseLink . '&id_post=' . $id_post . '&delpostimage=true&editybc_post=1';
            }
            if ($post->thumb) {
                $helper->tpl_vars['thumb_del_link'] = $this->baseLink . '&id_post=' . $id_post . '&delpostthumb=true&editybc_post=1';
            }
        }
        return $helper->generateForm(array($fields_form));
    }

    public function getSelectedProducts($id_post)
    {
        $products = array();
        $inputAccessories = Tools::getValue('inputAccessories');
        if (Tools::isSubmit('inputAccessories') && trim(trim($inputAccessories), ',') && Validate::isCleanHtml($inputAccessories)) {
            $products = explode('-', trim(trim($inputAccessories), '-'));
        } elseif ($id_post) {
            $products = Ybc_blog_post_class::getRelatedProducts($id_post);
            $products = explode(',', trim($products, '-'));
        }
        if ($products) {
            foreach ($products as $key => &$product) {
                $product = (int)$product;
            }
            return Ybc_blog_post_class::getProductsByIDs($this->context, $products);
        }
        return false;
    }

    public function getExcludeProducts($id_post)
    {
        $products = array();
        $inputAccessories = Tools::getValue('exclude_products');
        if (Tools::isSubmit('exclude_products') && trim(trim($inputAccessories), ',') && Validate::isCleanHtml($inputAccessories)) {
            $products = explode('-', trim(trim($inputAccessories), '-'));
        } elseif ($id_post) {
            $post = new Ybc_blog_post_class($id_post);
            $products = explode('-', trim($post->exclude_products, '-'));
        }
        if ($products) {
            foreach ($products as $key => &$product) {
                $product = (int)$product;
            }
            return Ybc_blog_post_class::getProductsByIDs($this->context, $products);
        }
        return false;
    }

    public function getFormAuthorPost($id_post)
    {
        if (!$this->module->isCached('form_author_post.tpl', $this->module->_getCacheId($id_post))) {
            $post = new Ybc_blog_post_class($id_post);
            if (Validate::isLoadedObject($post)) {
                $this->context->smarty->assign(
                    array(
                        'YBC_BLOG_ALLOW_CUSTOMER_AUTHOR' => Configuration::get('YBC_BLOG_ALLOW_CUSTOMER_AUTHOR') && Ybc_blog_post_employee_class::countCustomersFilter($this->context, false),
                        'admin_authors' => Ybc_blog_post_employee_class::getAuthors($this->context),
                        'post' => Ybc_blog_post_class::getPostByID($id_post),
                        'author' => Ybc_blog_post_class::getAuthorByIdPost($this->context, $id_post)
                    )
                );
            }
            else
                return '';
        }
        return $this->module->display($this->module->getLocalPath(), 'form_author_post.tpl', $this->module->_getCacheId($id_post));
    }

    public function actionChangeStatus()
    {
        $status = (int)Tools::getValue('change_enabled') ? 1 : 0;
        $field = Tools::getValue('field');
        $id_post = (int)Tools::getValue('id_post');
        Hook::exec('actionUpdateBlog', array(
            'id_post' => (int)$id_post,
        ));
        if (($field == 'enabled' || $field == 'is_featured') && $id_post) {
            $post_class = new Ybc_blog_post_class($id_post);
            Hook::exec('actionUpdateBlog', array(
                'id_post' => (int)$id_post,
            ));
            Ybc_blog_defines::changeStatus('post', $field, $id_post, $status);
            $customer = new Customer($post_class->added_by);
            if (($subject = Ybc_blog_email_template_class::getSubjectByTemplate('approved_blog_customer', $customer->id_lang)) && $field == 'enabled' && $status == 1 && $post_class->is_customer) {
                $template_customer_vars = array(
                    '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                    '{post_title}' => $post_class->title[$this->context->language->id],
                    '{post_link}' => $this->module->getLink('blog', array('id_post' => $post_class->id)),
                    '{color_main}' => Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
                    '{color_hover}' => Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')
                );
                Mail::Send(
                    $customer->id_lang,
                    'approved_blog_customer',
                    $subject,
                    $template_customer_vars,
                    $customer->email,
                    $customer->firstname . ' ' . $customer->lastname,
                    null,
                    null,
                    null,
                    null,
                    _PS_MODULE_DIR_.'ybc_blog'.'/mails/'
                );
            }
            if ($field == 'enabled') {
                if ($status == 1)
                    $title =$this->module->l('Click to mark as draft', 'AdminYbcBlogPostController');
                else
                    $title =$this->module->l('Click to mark as published', 'AdminYbcBlogPostController');
            } else {
                if ($status == 1)
                    $title =$this->module->l('Click to unmark featured post', 'AdminYbcBlogPostController');
                else
                    $title =$this->module->l('Click to mark as featured', 'AdminYbcBlogPostController');
            }
            if (Tools::isSubmit('ajax')) {
                die(json_encode(array(
                    'listId' => $id_post,
                    'enabled' => $status,
                    'field' => $field,
                    'message' => $field == 'enabled' ? $this->module->displaySuccessMessage($this->module->l('The status has been successfully updated', 'AdminYbcBlogPostController')) : $this->module->displaySuccessMessage($this->module->l('The featured post has been successfully updated', 'AdminYbcBlogPostController')),
                    'messageType' => 'success',
                    'title' => $title,
                    'href' => $this->baseLink . '&change_enabled=' . ($status ? '0' : '1') . '&field=' . $field . '&id_post=' . $id_post,
                )));
            }
            Tools::redirectAdmin($this->baseLink . '&conf=4');
        }
    }

    private function actionUpdatePostOrdering($posts)
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
     * @param Ybc_blog_post_class $post
     * @return void
     * @throws PrestaShopException
     */
    public function actionDeleteImagePost($post)
    {
        if(Validate::isLoadedObject($post))
        {
            $post->datetime_modified = date('Y-m-d H:i:s');
            $post->modified_by = (int)$this->context->employee->id;
            Hook::exec('actionUpdateBlog', array(
                'id_post' =>(int)$post->id ,
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
                                'message' => $this->module->displayConfirmation($this->module->l('Thumbnail image deleted', 'AdminYbcBlogPostController')),
                            )
                        ));
                    }
                    Tools::redirectAdmin($this->baseLink.'editybc_post&id_post='.$post->id.'&control=post');
                }
                else
                {
                    $errors[] =$this->module->l('Thumbnail image does not exist', 'AdminYbcBlogPostController');
                    if(Tools::isSubmit('ajax'))
                    {
                        die(json_encode(
                            array(
                                'messageType' => 'error',
                                'message' => $this->module->displayError($errors),
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
                        'id_post' =>(int)$post->id,
                    ));
                    if(Tools::isSubmit('ajax'))
                    {
                        die(json_encode(
                            array(
                                'messageType' => 'success',
                                'message' => $this->module->displayConfirmation($this->module->l('Image deleted', 'AdminYbcBlogPostController')),
                            )
                        ));
                    }
                    Tools::redirectAdmin($this->baseLink.'editybc_post&id_post='.$post->id);
                }
                else
                {
                    $errors[] =$this->module->l('Image does not exist', 'AdminYbcBlogPostController');
                    if(Tools::isSubmit('ajax'))
                    {
                        die(json_encode(
                            array(
                                'messageType' => 'error',
                                'message' => $this->module->displayError($errors),
                            )
                        ));
                    }
                }
            }
        }

    }
    public function submitDeletePost()
    {
        $id_post = (int)Tools::getValue('id_post');
        Hook::exec('actionUpdateBlog', array(
            'id_post' =>(int)$id_post,
        ));
        if(!Validate::isLoadedObject(new Ybc_blog_post_class($id_post)))
            $this->errors[] =$this->module->l('Post does not exist', 'AdminYbcBlogPostController');
        elseif(Ybc_blog_post_class::_deletePost( $id_post))
        {

            Tools::redirectAdmin($this->baseLink.'&conf=1');
        }
        else
            $this->errors[] =$this->module->l('Could not delete the post. Please try again', 'AdminYbcBlogPostController');
    }
    public function submitSavePost()
    {
        $errors = array();
        $id_post = (int)Tools::getValue('id_post');
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $datetime_added = Tools::getValue('datetime_added');
        $post = new Ybc_blog_post_class($id_post);
        if(Validate::isLoadedObject($post))
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
                    $errors[]= $this->module->l('Community - Authors is required', 'AdminYbcBlogPostController');
                else
                    $post->added_by = (int)$customer_author;
            }
            else
            {
                $admin_author = (int)Tools::getValue('admin_author');
                if(!$admin_author)
                    $errors[]= $this->module->l('Administrator - Author is required', 'AdminYbcBlogPostController');
                else
                    $post->added_by = (int)$admin_author;
            }

        }
        else
        {
            $post = new Ybc_blog_post_class(null, null, $this->context->shop->id);
            $post->datetime_added = $datetime_added && Validate::isDate($datetime_added) ? $datetime_added : date('Y-m-d H:i:s');
            $post->datetime_modified = $datetime_added && Validate::isDate($datetime_added) ? $datetime_added : date('Y-m-d H:i:s');
            $post->modified_by = (int)$this->context->employee->id;
            $post->added_by = (int)$this->context->employee->id;
            $post->is_customer=0;
            $post->sort_order =1 + (int)Ybc_blog_post_class::getMaxOrder($this->context->shop->id);
        }
        $inputAccessories = trim(trim(Tools::getValue('inputAccessories','')),'-');
        if($inputAccessories && Validate::isCleanHtml($inputAccessories))
            $products = $inputAccessories;
        else
            $products = '';
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
                $errors[]=$this->module->l('Publish date is required', 'AdminYbcBlogPostController');
            elseif($datetime_active=='0000-00-00' || !Validate::isDate($datetime_active))
                $errors[] =$this->module->l('Publish date is not valid', 'AdminYbcBlogPostController');
            else
                $post->datetime_active = $datetime_active;
        }
        elseif(!$post->id)
            $post->datetime_active = date('Y-m-d');
        $languages = Language::getLanguages(false);
        $post->click_number = (int)Tools::getValue('click_number');
        $post->likes = (int)Tools::getValue('likes');
        $tags = array();
        $categories = Tools::getValue('blog_categories',array());
        $title_default = trim(Tools::getValue('title_'.$id_lang_default));
        if($title_default=='')
            $errors[] =$this->module->l('You need to set blog post title', 'AdminYbcBlogPostController');
        elseif(!Validate::isCatalogName($title_default))
            $errors[] =$this->module->l('Blog post title is not valid', 'AdminYbcBlogPostController');
        $short_description_default = trim(Tools::getValue('short_description_'.$id_lang_default));
        if($short_description_default=='')
            $errors[] =$this->module->l('You need to set blog post short description', 'AdminYbcBlogPostController');
        elseif(!Validate::isCleanHtml($short_description_default,true))
            $errors[] =$this->module->l('Blog post short description is not valid', 'AdminYbcBlogPostController');
        $description_default = trim(Tools::getValue('description_'.$id_lang_default));
        if($description_default=='')
            $errors[] =$this->module->l('You need to set blog post content', 'AdminYbcBlogPostController');
        elseif(!Validate::isCleanHtml($description_default,true))
            $errors[] =$this->module->l('Blog post content is not valid', 'AdminYbcBlogPostController');
        $url_alias_default = Tools::getValue('url_alias_'.$id_lang_default);
        if($url_alias_default=='')
            $errors[] =$this->module->l('Url alias is required', 'AdminYbcBlogPostController');
        elseif(!Validate::isLinkRewrite($url_alias_default))
            $errors[] =$this->module->l('Url alias is not valid', 'AdminYbcBlogPostController');
        if(!$categories || !is_array($categories))
            $errors[] =$this->module->l('You need to choose at least 1 category', 'AdminYbcBlogPostController');
        elseif(!Ybc_blog::validateArray($categories))
            $errors[] =$this->module->l('Categories is not valid', 'AdminYbcBlogPostController');
        $main_category = (int)Tools::getValue('main_category');
        if(!$main_category)
            $errors[] =$this->module->l('Main category is required', 'AdminYbcBlogPostController');
        elseif(!in_array($main_category,$categories))
            $errors[] =$this->module->l('Main category is not valid', 'AdminYbcBlogPostController');
        else
            $post->id_category_default = (int)$main_category;
        $click_number = Tools::getValue('click_number');
        if($click_number=='')
            $errors[] =$this->module->l('Views are required', 'AdminYbcBlogPostController');
        elseif(!Validate::isUnsignedInt($click_number))
            $errors[] =$this->module->l('Views are not valid', 'AdminYbcBlogPostController');
        $likes = Tools::getValue('likes');
        if($likes=='')
            $errors[] =$this->module->l('Likes are required', 'AdminYbcBlogPostController');
        elseif(!Validate::isUnsignedInt($likes))
            $errors[] =$this->module->l('Likes are not valid', 'AdminYbcBlogPostController');
        if(!($post->thumb && isset($post->thumb[$id_lang_default]) && $post->thumb[$id_lang_default]) && !(isset($_FILES['thumb_'.$id_lang_default]['tmp_name']) && isset($_FILES['thumb_'.$id_lang_default]['name']) && $_FILES['thumb_'.$id_lang_default]['name']))
            $errors[]=$this->module->l('Post thumbnail image is required', 'AdminYbcBlogPostController');
        $meta_title_default = trim(Tools::getValue('meta_title_'.$id_lang_default));
        if($meta_title_default && !Validate::isGenericName($meta_title_default))
            $errors[] =$this->module->l('Meta title is not valid', 'AdminYbcBlogPostController');
        $meta_description_default = trim(Tools::getValue('meta_description_'.$id_lang_default));
        if($meta_description_default && !Validate::isGenericName($meta_description_default))
            $errors[] =$this->module->l('Meta description is not valid', 'AdminYbcBlogPostController');
        $meta_keywords_default = trim(Tools::getValue('meta_keywords_'.$id_lang_default));
        if($meta_keywords_default && !Validate::isTagsList($meta_keywords_default))
            $errors[] =$this->module->l('Meta keyword is not valid', 'AdminYbcBlogPostController');
        if(!$errors)
        {
            foreach ($languages as $language)
            {
                $title = trim(Tools::getValue('title_'.$language['id_lang']));
                $meta_title = trim(Tools::getValue('meta_title_'.$language['id_lang']));
                $url_alias = trim(Tools::getValue('url_alias_'.$language['id_lang']));
                if($title && !Validate::isCatalogName($title))
                    $errors[] = sprintf($this->module->l('Title in %s is not valid', 'AdminYbcBlogPostController'),$language['name']);
                else
                    $post->title[$language['id_lang']] = $title ? $title : $title_default;
                if($meta_title && !Validate::isGenericName($meta_title))
                    $errors[] = sprintf($this->module->l('Meta title in %s is not valid', 'AdminYbcBlogPostController'),$language['name']);
                elseif($meta_title && Tools::strlen($meta_title) >700)
                    $errors[] = sprintf($this->module->l('Meta title in %s is too long. Length should not exceed 700 characters', 'AdminYbcBlogPostController'),$language['name']);
                else
                    $post->meta_title[$language['id_lang']] = $meta_title ? $meta_title: $meta_title_default;
                if($url_alias && str_replace(array('0','1','2','3','4','5','6','7','8','9'),'',Tools::substr($url_alias,0,1))=='')
                    $errors[] = sprintf($this->module->l('Post alias in %s cannot have number on the start position because it will cause error when you enable "Remove post ID" option', 'AdminYbcBlogPostController'),$language['name']);
                elseif($url_alias && !Ybc_blog::checkIsLinkRewrite($url_alias))
                    $errors[] = sprintf($this->module->l('Url alias in %s is not valid', 'AdminYbcBlogPostController'),$language['name']);
                elseif($url_alias && Ybc_blog_post_class::checkUrlAliasExists($url_alias,$post->id, $this->context->shop->id))
                    $errors[] = sprintf($this->module->l('Url alias in %s has already existed', 'AdminYbcBlogPostController'),$language['name']);
                else
                    $post->url_alias[$language['id_lang']]= $url_alias ? $url_alias : $url_alias_default;
                $meta_description = trim(Tools::getValue('meta_description_'.$language['id_lang']));
                if($meta_description && !Validate::isGenericName($meta_description))
                    $errors[] = sprintf($this->module->l('Meta description in %s is not valid', 'AdminYbcBlogPostController'),$language['name']);
                else
                    $post->meta_description[$language['id_lang']] = $meta_description ? $meta_description :  $meta_description_default;
                $meta_keywords = trim(Tools::getValue('meta_keywords_'.$language['id_lang']));
                if($meta_keywords && !Validate::isTagsList($meta_keywords))
                    $errors[] = sprintf($this->module->l('Meta keywords in %s are not valid', 'AdminYbcBlogPostController'),$language['name']);
                else
                    $post->meta_keywords[$language['id_lang']] = $meta_keywords != '' ? $meta_keywords :  $meta_keywords_default;
                $short_description = trim(Tools::getValue('short_description_'.$language['id_lang']));
                if($short_description && !Validate::isCleanHtml($short_description, true) )
                    $errors[] = sprintf($this->module->l('Short description in %s is not valid', 'AdminYbcBlogPostController'),$language['name']);
                elseif($short_description && !Ybc_blog::checkIframeHTML($short_description))
                    $errors[] = sprintf($this->module->l('Short description in %s is not valid', 'AdminYbcBlogPostController'),$language['name']).$this->displayErrorIframe();
                else
                    $post->short_description[$language['id_lang']] = $short_description != '' ? $short_description :  $short_description_default;
                $description = Tools::getValue('description_'.$language['id_lang']);
                if(trim($description) && !Validate::isCleanHtml($description, true))
                    $errors[] = sprintf($this->module->l('Description in %s is not valid', 'AdminYbcBlogPostController'),$language['name']);
                elseif($description && !Ybc_blog::checkIframeHTML($description))
                    $errors[] = sprintf($this->module->l('Description in %s is not valid.', 'AdminYbcBlogPostController'),$language['name']).' '.$this->displayErrorIframe();
                else
                    $post->description[$language['id_lang']] = $description != '' ? $description :  $description_default;
                if($products && !preg_match('/^[0-9]+([\-0-9])*$/', $products))
                {
                    $errors[] =$this->module->l('Products are not valid', 'AdminYbcBlogPostController');
                }
                $tagStr = trim(Tools::getValue('tags_'.$language['id_lang']));
                if($tagStr && Validate::isTagsList($tagStr))
                    $tags[$language['id_lang']] = explode(',',$tagStr);
                elseif($tagStr && !Validate::isTagsList($tagStr))
                {
                    $tags[$language['id_lang']] = array();
                    $errors[] =$this->module->l('Tags in '.$language['name'].' are not valid');
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
                    $errors[] = sprintf($this->module->l('Image name is not valid in %s', 'AdminYbcBlogPostController'),$language['iso_code']);
                elseif($_FILES['image_'.$language['id_lang']]['size'] > $max_file_size)
                    $errors[] = sprintf($this->module->l('Image file is too large. Limit: %s', 'AdminYbcBlogPostController'),Tools::ps_round($max_file_size/1048576,2).'Mb');
                else
                {
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/'.$_FILES['image_'.$language['id_lang']]['name']))
                    {
                        $_FILES['image_'.$language['id_lang']]['name'] = $this->module->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'post/',$_FILES['image_'.$language['id_lang']]['name']);
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
                            $errors[] =$this->module->l('Cannot upload the file in', 'AdminYbcBlogPostController').' '.$language['iso_code'];
                        elseif (!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'post/'.$_FILES['image_'.$language['id_lang']]['name'], (int)Configuration::get('YBC_BLOG_IMAGE_BLOG_WIDTH',null,null,null,1920), (int)Configuration::get('YBC_BLOG_IMAGE_BLOG_HEIGHT',null,null,null,750), $type))
                            $errors[] =$this->module->l('An error occurred during the image upload process in', 'AdminYbcBlogPostController').' '.$language['iso_code'];
                        if (file_exists($temp_name))
                            @unlink($temp_name);
                        if( $post->image && isset($post->image[$language['id_lang']]) && $post->image[$language['id_lang']])
                            $oldImages[$language['id_lang']] = $post->image[$language['id_lang']];
                        $post->image[$language['id_lang']] = $_FILES['image_'.$language['id_lang']]['name'];
                        $newImages[$language['id_lang']] = $post->image[$language['id_lang']];
                    }
                    elseif(isset($_FILES['image_'.$language['id_lang']]) &&
                        !empty($_FILES['image_'.$language['id_lang']]['tmp_name']) &&
                        !empty($imagesize) &&
                        in_array($type, array('jpg', 'gif', 'jpeg', 'png')
                        ))
                        $errors[] =$this->module->l('Image is invalid in', 'AdminYbcBlogPostController').' '.$language['iso_code'];
                }
            }


            /**
             * Upload thumbnail
             */

            if(isset($_FILES['thumb_'.$language['id_lang']]['tmp_name']) && isset($_FILES['thumb_'.$language['id_lang']]['name']) && $_FILES['thumb_'.$language['id_lang']]['name'])
            {
                $_FILES['thumb_'.$language['id_lang']]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES['thumb_'.$language['id_lang']]['name']);
                if(!Validate::isFileName($_FILES['thumb_'.$language['id_lang']]['name']))
                    $errors[] = sprintf($this->module->l('Thumbnail image name is not valid in %s', 'AdminYbcBlogPostController'),$language['iso_code']);
                elseif($_FILES['thumb_'.$language['id_lang']]['size'] > $max_file_size)
                    $errors[] = sprintf($this->module->l('Thumbnail image file is too large. Limit: %s', 'AdminYbcBlogPostController'),Tools::ps_round($max_file_size/1048576,2).'Mb');
                else
                {
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$_FILES['thumb_'.$language['id_lang']]['name']))
                    {
                        $_FILES['thumb_'.$language['id_lang']]['name'] = $this->module->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/',$_FILES['thumb_'.$language['id_lang']]['name']);
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
                            $errors[] =$this->module->l('Cannot upload the file in', 'AdminYbcBlogPostController').' '.$language['iso_code'];
                        elseif (!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$_FILES['thumb_'.$language['id_lang']]['name'], (int)Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_WIDTH',null,null,null,260), (int)Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_HEIGHT',null,null,null,180), $type))
                            $errors[] =$this->module->l('An error occurred during the thumbnail upload process in', 'AdminYbcBlogPostController').' '.$language['iso_code'];
                        if (file_exists($temp_name))
                            @unlink($temp_name);
                        if($post->thumb && isset($post->thumb[$language['id_lang']]) && $post->thumb[$language['id_lang']])
                            $oldThumbs[$language['id_lang']] = $post->thumb[$language['id_lang']];
                        $post->thumb[$language['id_lang']] = $_FILES['thumb_'.$language['id_lang']]['name'];
                        $newThumbs[$language['id_lang']] = $post->thumb[$language['id_lang']];
                    }
                    elseif(isset($_FILES['thumb_'.$language['id_lang']]) &&
                        !in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                    )
                        $errors[] =$this->module->l('Thumbnail image is invalid in', 'AdminYbcBlogPostController').' '.$language['iso_code'];
                }
            }
        }
        foreach($languages as $language)
        {
            if(!(isset($post->thumb[$language['id_lang']]) && $post->thumb[$language['id_lang']]) && (isset($post->thumb[$id_lang_default]) && $post->thumb[$id_lang_default]))
                $post->thumb[$language['id_lang']] = $post->thumb[$id_lang_default];
            if(!(isset($post->image[$language['id_lang']]) && $post->image[$language['id_lang']]) &&  isset($post->image[$id_lang_default]) && $post->image[$id_lang_default])
                $post->image[$language['id_lang']] = $post->image[$id_lang_default];
        }
        $changedImages = array();
        if(!$errors)
        {
            if (!$id_post)
            {
                if (!$post->add())
                {
                    $errors[] = $this->module->displayError($this->module->l('The post could not be added.', 'AdminYbcBlogPostController'));
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
                    $id_post = $post->id;
                    Ybc_blog_post_class::updateCategories($categories, $id_post);
                    $relatedCategories= Tools::getValue('related_categories');
                    if(!empty($products))
                        Ybc_blog_post_class::updateRelatedProducts($products,$id_post);
                    if(Ybc_blog::validateArray($relatedCategories))
                        Ybc_blog_post_class::updateRelatedCategories($relatedCategories,$id_post);
                    $product_categories = Tools::getValue('product_categories');
                    if(Ybc_blog::validateArray($product_categories))
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
                $errors[] = $this->module->displayError($this->module->l('The post could not be updated.', 'AdminYbcBlogPostController'));
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
                if(!empty($products))
                    Ybc_blog_post_class::updateRelatedProducts($products,$id_post);
                $product_categories = Tools::getValue('product_categories');
                if(Ybc_blog::validateArray($product_categories))
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
                        '{post_link}'=> $this->module->getLink('blog',array('id_post'=>$post->id)),
                        '{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
                        '{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')
                    );
                    Mail::Send(
                        $customer->id_lang,
                        'approved_blog_customer',
                       $this->module->l('Your post has been approved', 'AdminYbcBlogPostController'),
                        $template_customer_vars,
                        $customer->email,
                        $customer->firstname .' '.$customer->lastname,
                        null,
                        null,
                        null,
                        null,
                        _PS_MODULE_DIR_.'ybc_blog'.'/mails/'
                    );
                }
            }
            Hook::exec('actionUpdateBlog', array(
                'id_post' =>(int)$post->id,
            ));
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
            $this->module->errorMessage = $this->module->displayError($errors);
        }
        if($newThumbs && !$errors)
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
        if($newImages && !$errors){
            foreach($languages as $language)
            {
                $changedImages[] = array(
                    'name' => 'image_'.$language['id_lang'],
                    'url' => _PS_YBC_BLOG_IMG_.'post/'.$post->image[$language['id_lang']],
                    'delete_url' => $this->baseLink.'&id_post='.$id_post.'&delpostimage=true&id_lang='.$language['id_lang'],
                );
            }

        }
        if(Tools::isSubmit('ajax'))
        {
            $itemId= !$errors && Tools::isSubmit('savePost') && !(int)$id_post ? Ybc_blog_defines::getMaxId('post','id_post') : ((int)$id_post > 0 ? (int)$id_post : 0);
            $array = array(
                'messageType' => $errors ? 'error' : 'success',
                'message' => $errors ? $this->module->errorMessage : (isset($id_post) && $id_post ? $this->module->displaySuccessMessage($this->module->l('Post has been saved', 'AdminYbcBlogPostController'),$this->module->l('View this post', 'AdminYbcBlogPostController'),$this->module->getLink('blog',array('id_post'=>$id_post))) : $this->module->displayConfirmation($this->module->l('Post saved', 'AdminYbcBlogPostController'))),
                'images' => $changedImages ? : array(),
                'postUrl' => !$errors && Tools::isSubmit('savePost') && !(int)$id_post ? $this->baseLink.'&id_post='.Ybc_blog_defines::getMaxId('post','id_post') : 0,
                'itemKey' => 'id_post',
                'itemId' => $itemId,
                'link_preview'=> Tools::isSubmit('submitSaveAndPreview') && !$errors  ? $this->module->getLink('blog',array('id_post'=>$post->id,'preview'=>1)):'',
            );
            if(!$errors)
                $array['form_author_post']= $this->getFormAuthorPost($itemId);
            die(json_encode(
                $array
            ));
        }
        if(!$errors)
        {
            if ($id_post)
                Tools::redirectAdmin($this->baseLink.'&conf=4&editybc_post&id_post='.$id_post.'&control=post');
            else
            {
                Tools::redirectAdmin($this->baseLink.'&conf=3&editybc_post&id_post='.Ybc_blog_defines::getMaxId('post','id_post'));
            }
        }
    }
    private function _postPost()
    {
        /**
         * Change status
         */
        if(Tools::isSubmit('change_enabled'))
        {
            $this->actionChangeStatus();
        }
        if(($action = Tools::getValue('action')) && $action=='updatePostOrdering' && ($posts=Tools::getValue('posts')) && Ybc_blog::validateArray($posts,'isInt'))
        {
            $this->actionUpdatePostOrdering($posts);
        }

        /**
         * Delete image
         */
        if(($id_post =(int)Tools::getValue('id_post')) && (Tools::isSubmit('delpostimage') || Tools::isSubmit('delpostthumb')))
        {
            $post = new Ybc_blog_post_class($id_post);
            if(Validate::isLoadedObject($post))
            {
                $this->actionDeleteImagePost($post);
            }

        }
        /**
         * Delete post
         */
        if(Tools::isSubmit('del'))
        {
            $this->submitDeletePost();
        }
        if(Tools::isSubmit('bulkActionSubmitDelete'))
            $this->bulkActionSubmitDelete();
        if(Tools::isSubmit('savePost'))
            $this->submitSavePost();
        if(Tools::isSubmit('bulkActionSubmitEnable'))
            $this->bulkActionSubmitChangeStatus(1);
        if(Tools::isSubmit('bulkActionSubmitDisable'))
            $this->bulkActionSubmitChangeStatus(0);
        if(Tools::isSubmit('bulkActionSubmitMarkAsFeature'))
            $this->bulkActionChangeMarkasFeature(1);
        if(Tools::isSubmit('bulkActionSubmitUnMarkAsFeature'))
        {
            $this->bulkActionChangeMarkasFeature(0);
        }
    }
    public function bulkActionSubmitDelete(){
        $ids = Tools::getValue('bulk_ybc_post');
        if($ids && ($ids = array_keys($ids)) && Ybc_blog::validateArray($ids,'isInt'))
        {
            foreach($ids as $id)
            {
                Ybc_blog_post_class::_deletePost($id);
            }
            Hook::exec('actionUpdateBlog', array());
            Tools::redirect($this->baseLink.'&conf=1');
        }
    }
    public function bulkActionChangeMarkasFeature($active){
        $ids = Tools::getValue('bulk_ybc_post');
        if($ids && ($ids = array_keys($ids)) && Ybc_blog::validateArray($ids,'isInt'))
        {
            if(Ybc_blog_post_class::bulkActionSubmitChangeMarkasFeature($ids,$active))
            {
                Hook::exec('actionUpdateBlog', array());
                Tools::redirect($this->baseLink.'&conf=4');
            }
        }
    }
    public function bulkActionSubmitChangeStatus($active){
        $ids = Tools::getValue('bulk_ybc_post');
        if($ids && ($ids = array_keys($ids)) && Ybc_blog::validateArray($ids,'isInt'))
        {
            if(Ybc_blog_post_class::bulkActionSubmitChangeStatus($ids,$active))
            {
                Hook::exec('actionUpdateBlog', array());
                Tools::redirect($this->baseLink.'&conf=4');
            }
        }
    }
    public function displayErrorIframe()
    {
        if(!$this->module->isCached('iframe.tpl',$this->module->_getCacheId()))
        {
            $this->context->smarty->assign(
                array(
                    'link' => $this->context->link,
                )
            );
        }
        return $this->module->display($this->module->getLocalPath(),'iframe.tpl',$this->module->_getCacheId());
    }
}
