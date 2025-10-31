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
 * Class AdminYbcBlogSliderController
 * @property Ybc_blog $module;
 */
class AdminYbcBlogSliderController extends ModuleAdminController
{
    public $baseLink;
    public $_html='';
    protected $checked = false;
    public function init()
    {
        parent::init();
        $this->baseLink = $this->context->link->getAdminLink('AdminYbcBlogSlider');
        $this->bootstrap = true;
        if($this->module->checkProfileEmployee($this->context->employee->id,'Blog slider'))
        {
            $this->checked = true;
            $this->_postSlide();
        }

    }
    public function renderList()
    {
        if(!$this->checked)
            return $this->module->display($this->module->getLocalPath(),'error_access.tpl');
        $this->context->smarty->assign(array_merge($this->module->getAssign('slide'), array('ybc_blog_body_html' => $this->_getContent())));
        return $this->_html.$this->module->display(_PS_MODULE_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . $this->module->name . '.php', 'admin.tpl');
    }
    public function _getcontent()
    {
        if(Tools::isSubmit('addNew') || Tools::isSubmit('editybc_slide'))
        {
            return $this->renderSlideForm();
        }
        else
            return $this->renderListSlides();
    }
    private function renderListSlides()
    {
        $fields_list = array(
            'id_slide' => array(
                'title' =>$this->module->l('ID', 'AdminYbcBlogSliderController'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true
            ),
            'image' => array(
                'title' =>$this->module->l('Image', 'AdminYbcBlogSliderController'),
                'type' => 'text',
                'filter' => false
            ),
            'caption' => array(
                'title' =>$this->module->l('Caption', 'AdminYbcBlogSliderController'),
                'type' => 'text',
                'sort' => true,
                'filter' => true
            ),
            'sort_order' => array(
                'title' =>$this->module->l('Sort order', 'AdminYbcBlogSliderController'),
                'type' => 'text',
                'sort' => true,
                'drag_handle' => true,
                'filter' => true,
                'update_position' => true,
            ),
            'enabled' => array(
                'title' =>$this->module->l('Enabled', 'AdminYbcBlogSliderController'),
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
                            'title' =>$this->module->l('Yes', 'AdminYbcBlogSliderController')
                        ),
                        1 => array(
                            'enabled' => 0,
                            'title' =>$this->module->l('No', 'AdminYbcBlogSliderController')
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
        if($page < 1)
            $page=1;
        $totalRecords = (int)Ybc_blog_slide_class::countSlidesWithFilter($this->context, $filter);
        $paggination = new Ybc_blog_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->baseLink.'&page=_page_'.$this->module->getUrlExtra($fields_list);
        $paggination->limit =  (int)Tools::getValue('paginator_ybc_slide_select_limit',20);
        $paggination->name ='ybc_slide';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $slides = Ybc_blog_slide_class::getSlidesWithFilter($this->context, $filter, $sort, $start, $paggination->limit);
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
        $paggination->text = $this->module->l('Showing {start} to {end} of {total} ({pages} Pages)', 'AdminYbcBlogSliderController');
        $paggination->style_links =$this->module->l('links', 'AdminYbcBlogSliderController');
        $paggination->style_results =$this->module->l('results', 'AdminYbcBlogSliderController');
        $listData = array(
            'name' => 'ybc_slide',
            'actions' => array('edit', 'delete', 'view'),
            'currentIndex' => $this->baseLink.($paggination->limit!=20 ? '&paginator_ybc_slide_select_limit='.$paggination->limit:''),
            'identifier' => 'id_slide',
            'show_toolbar' => true,
            'show_action' => true,
            'title' =>$this->module->l('Slider', 'AdminYbcBlogSliderController'),
            'fields_list' => $fields_list,
            'field_values' => $slides,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list),
            'show_reset' => $show_reset,
            'totalRecords' => $totalRecords,
            'sort' => $sort_post ? :'sort_order',
            'sort_type'=> $sort_type,
        );
        return $this->module->renderList($listData);
    }
    private function _postSlide()
    {
        $errors = array();
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $id_slide = (int)Tools::getValue('id_slide');
        if(Tools::isSubmit('editybc_slide') && $id_slide && !Validate::isLoadedObject(new Ybc_blog_slide_class($id_slide)))
            Tools::redirectAdmin($this->baseLink);
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
                        'message' => $this->module->displaySuccessMessage($this->module->l('Successfully updated', 'AdminYbcBlogSliderController')),
                        'messageType'=>'success',
                        'href' => $this->baseLink.'&change_enabled='.($status ? '0' : '1').'&field='.$field.'&id_slide='.$id_slide,
                    )));
                }
                Tools::redirectAdmin($this->baseLink.'&conf=4');
            }
        }
        /**
         * Delete image
         */
        if($id_slide && Tools::isSubmit('delslideimage'))
        {
            $slide = new Ybc_blog_slide_class($id_slide);
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
                            'message' => $this->module->displayConfirmation($this->module->l('Image deleted', 'AdminYbcBlogSliderController')),
                        )
                    ));
                }
                Tools::redirectAdmin($this->baseLink.'&conf=4&editybc_slide&id_slide='.$id_slide.'&control=slide');
            }
            else
                $errors[] =$this->module->l('Image does not exist', 'AdminYbcBlogSliderController');
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
            $slide = new Ybc_blog_slide_class($id_slide);
            if(!Validate::isLoadedObject($slide))
                $errors[] =$this->module->l('Slide does not exist', 'AdminYbcBlogSliderController');
            elseif($slide->delete())
            {
                Tools::redirectAdmin($this->baseLink.'&conf=4');
            }
            else
                $errors[] =$this->module->l('Could not delete the slide. Please try again', 'AdminYbcBlogSliderController');
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
            $slide = new Ybc_blog_slide_class($id_slide);
            if(!($id_slide && Validate::isLoadedObject($slide)))
            {
                $slide = new Ybc_blog_slide_class(null, null, $this->context->shop->id);
                if(!isset($_FILES['image_'.$id_lang_default]['name']) || isset($_FILES['image_'.$id_lang_default]['name']) && !$_FILES['image_'.$id_lang_default]['name'])
                    $errors[] =$this->module->l('You need to upload an image', 'AdminYbcBlogSliderController');
                $slide->sort_order = 1 + (int)Ybc_blog_slide_class::getMaxSortOrder($this->context->shop->id);
            }
            $slide->enabled = (int)trim(Tools::getValue('enabled',1)) ? 1 : 0;
            $languages = Language::getLanguages(false);
            $caption_default = trim(Tools::getValue('caption_'.Configuration::get('PS_LANG_DEFAULT')));
            if($caption_default=='')
                $errors[] =$this->module->l('You need to set caption', 'AdminYbcBlogSliderController');
            elseif($caption_default && !Validate::isCleanHtml($caption_default))
                $errors[] =$this->module->l('Caption is not valid', 'AdminYbcBlogSliderController');
            $url_default =trim(Tools::getValue('url_'.Configuration::get('PS_LANG_DEFAULT')));
            if($url_default && !Validate::isCleanHtml($url_default))
                $errors[] =$this->module->l('Url is not valid', 'AdminYbcBlogSliderController');
            if(!$errors)
            {
                foreach ($languages as $language)
                {
                    $id_lang = (int)$language['id_lang'];
                    $caption = trim(Tools::getValue('caption_'.$id_lang));
                    if($caption && !Validate::isCleanHtml($caption))
                        $errors[] = sprintf($this->module->l('Caption in %s is not valid', 'AdminYbcBlogSliderController'),$language['name']);
                    else
                        $slide->caption[$id_lang] = $caption != '' ? $caption :  $caption_default;
                    $url = trim(Tools::getValue('url_'.$id_lang));
                    if($url && !Validate::isCleanHtml($url))
                        $errors[] = sprintf($this->module->l('url in %s is not valid', 'AdminYbcBlogSliderController'),$language['name']);
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
                        $errors[] = sprintf($this->module->l('Image name is not valid in %s', 'AdminYbcBlogSliderController'),$language['iso_code']);
                    elseif($_FILES['image_'.$language['id_lang']]['size'] > $max_file_size)
                        $errors[] = sprintf($this->module->l('Image file is too large. Limit: %s', 'AdminYbcBlogSliderController'),Tools::ps_round($max_file_size/1048576,2).'Mb');
                    else
                    {
                        if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'slide/'.$_FILES['image_'.$language['id_lang']]['name']))
                        {
                            $_FILES['image_'.$language['id_lang']]['name'] = $this->module->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'slide/',$_FILES['image_'.$language['id_lang']]['name']);
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
                                $errors[] =$this->module->l('Cannot upload the file in', 'AdminYbcBlogSliderController').' '.$language['iso_code'];
                            elseif (!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'slide/'.$_FILES['image_'.$language['id_lang']]['name'], (int)Configuration::get('YBC_BLOG_IMAGE_SLIDER_WIDTH',null,null,null,800), (int)Configuration::get('YBC_BLOG_IMAGE_SLIDER_HEIGHT',null,null,null,470), $type))
                                $errors[] =$this->module->l('An error occurred during the image upload process in', 'AdminYbcBlogSliderController').' '.$language['iso_code'];
                            if (file_exists($temp_name))
                                @unlink($temp_name);
                            if($slide->image && isset($slide->image[$language['id_lang']]) && $slide->image[$language['id_lang']])
                                $oldImages[$language['id_lang']] = $slide->image[$language['id_lang']];
                            $slide->image[$language['id_lang']] = $_FILES['image_'.$language['id_lang']]['name'];
                            $newImages[$language['id_lang']] = $slide->image[$language['id_lang']];
                        }
                        else
                            $errors[] = sprintf($this->module->l('Image is not valid in %s', 'AdminYbcBlogSliderController'),$language['iso_code']);
                    }
                }
            }
            foreach($languages as $language)
            {
                if(!(isset($slide->image[$language['id_lang']]) && $slide->image[$language['id_lang']]) && isset($slide->image[$id_lang_default]) && $slide->image[$id_lang_default])
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
                        $errors[] =$this->module->l('The slide could not be added.', 'AdminYbcBlogSliderController');
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
                    $errors[] =$this->module->l('The slide could not be updated.', 'AdminYbcBlogSliderController');
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
            if(!empty($newImages))
            {
                foreach($newImages as $newImage)
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'slide/'.$newImage))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'slide/'.$newImage);
            }
            $this->module->errorMessage = $this->module->displayError($errors);
        }
        if(Tools::isSubmit('ajax'))
        {
            die(json_encode(
                array(
                    'messageType' => $errors ? 'error' : 'success',
                    'message' => $errors ? $this->module->errorMessage : $this->module->displaySuccessMessage($this->module->l('Slider saved', 'AdminYbcBlogSliderController'),$this->module->l('View slider on blog page', 'AdminYbcBlogSliderController'),$this->module->getLink('blog')),
                    'images' =>  $changedImages ? : array(),
                    'postUrl' => !$errors && Tools::isSubmit('saveSlide') && !(int)$id_slide ? $this->baseLink.'&editybc_slide&id_slide='.Ybc_blog_defines::getMaxId('slide','id_slide') : 0,
                    'itemKey' => 'id_slide',
                    'itemId' => !$errors && Tools::isSubmit('saveSlide') && !(int)$id_slide ? Ybc_blog_defines::getMaxId('slide','id_slide') : ((int)$id_slide > 0 ? (int)$id_slide : 0),
                )
            ));
        }
        if (!$errors && Tools::isSubmit('saveSlide') && Tools::isSubmit('id_slide'))
            Tools::redirectAdmin($this->baseLink.'&conf=4&editybc_slide&id_slide='.$id_slide);
        elseif (!$errors && Tools::isSubmit('saveSlide'))
        {
            Tools::redirectAdmin($this->baseLink.'&conf=3editybc_slide&id_slide='.Ybc_blog_defines::getMaxId('slide','id_slide').'&control=slide');
        }
    }
    public function renderSlideForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' =>$this->module->l('Manage slider', 'AdminYbcBlogSliderController'),
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' =>$this->module->l('Caption', 'AdminYbcBlogSliderController'),
                        'name' => 'caption',
                        'lang' => true,
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' =>$this->module->l('Url', 'AdminYbcBlogSliderController'),
                        'name' => 'url',
                        'lang'=>true,
                    ),
                    array(
                        'type' => 'file_lang',
                        'label' =>$this->module->l('Image', 'AdminYbcBlogSliderController'),
                        'name' => 'image',
                        'required' => true,
                        'desc' =>sprintf($this->module->l('Accepted formats: jpg, jpeg, png, gif. Limit: %dMb. Recommended size: %sx%s.', 'AdminYbcBlogSliderController'),Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),Configuration::get('YBC_BLOG_IMAGE_SLIDER_WIDTH',null,null,null,800),Configuration::get('YBC_BLOG_IMAGE_SLIDER_HEIGHT',null,null,null,470)),
                    ),
                    array(
                        'type' => 'switch',
                        'label' =>$this->module->l('Enabled', 'AdminYbcBlogSliderController'),
                        'name' => 'enabled',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' =>$this->module->l('Yes', 'AdminYbcBlogSliderController')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' =>$this->module->l('No', 'AdminYbcBlogSliderController')
                            )
                        )
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'control'
                    )
                ),
                'submit' => array(
                    'title' =>$this->module->l('Save', 'AdminYbcBlogSliderController'),
                )
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = 'module';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this->module;
        $helper->identifier = 'id_slide';
        $helper->submit_action = 'saveSlide';
        $helper->currentIndex = $this->baseLink;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
            'fields_value' => $this->module->getFieldsValues(Ybc_blog_defines::getSlideField(),'id_slide','Ybc_blog_slide_class','saveSlide'),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'link' => $this->context->link,
            'post_key' => 'id_slide',
            'image_baseurl' => _PS_YBC_BLOG_IMG_.'slide/',
            'image_baseurl_thumb' => _PS_YBC_BLOG_IMG_.'slide/thumb/',
            'addNewUrl' => $this->baseLink,
            'cancel_url' => $this->baseLink
        );
        $id_slide = (int)Tools::getValue('id_slide');
        $slide = new Ybc_blog_slide_class($id_slide);
        if($id_slide && Validate::isLoadedObject($slide))
        {
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_slide');
            if($slide->image)
            {
                $helper->tpl_vars['img_del_link'] = $this->baseLink.'&id_slide='.$id_slide.'&delslideimage=true';
            }
        }
        return $helper->generateForm(array($fields_form));
    }
}
