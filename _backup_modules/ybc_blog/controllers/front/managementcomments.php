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
class Ybc_blogManagementcommentsModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;
    public $display_column_right = false;
    public $_errros= array();
    public $sussecfull;
    public function __construct()
	{
		parent::__construct();
        $this->display_column_right=false;
        $this->display_column_left =false;
		$this->context = Context::getContext();
        $this->module= new Ybc_blog();
        
	}
	public function init()
	{
		parent::init();
        if (!$this->context->customer->isLogged())
        {
            Tools::redirect('index.php?controller=authentication');
        }
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
        if (!$this->context->customer->isLogged())
		{
                
            Tools::redirect('index.php?controller=authentication');
        }
        $tabmanagament = Tools::getValue('tabmanagament');
        if($tabmanagament && !Validate::isCleanHtml($tabmanagament))
            $tabmanagament ='comment';
        $form_html_post ='';
        if(Tools::isSubmit('submitComment') || Tools::isSubmit('submitCommentStay'))
            $this->_saveComment();
        if(Tools::isSubmit('commentapproved') && ($id_comment=(int)Tools::getValue('id_comment')))
        {
               if(Ybc_blog_post_employee_class::checkPermisionComment('',$id_comment,$tabmanagament) && ($comment = new Ybc_blog_comment_class($id_comment)) && Validate::isLoadedObject($comment))
               {
                    $commentapproved = (int)Tools::getValue('commentapproved');
                    $comment->approved = (int)$commentapproved;
                    if($comment->update())
                    {
                        Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'managementcomments',array('tabmanagament'=>$tabmanagament,'updateComment'=>1)));
                    }
               }
               else
               {
                    if(Tools::isSubmit('ajax'))
                    {
                        die(
                            json_encode(
                                array(
                                    'error' => $this->module->l('Sorry, you do not have permission','managementcomments'),
                                )
                            )
                        );
                    }
                    else
                        $this->_errros[]=$this->module->l('Sorry, you do not have permission','managementcomments');
               }
                    
        }
        if(Tools::isSubmit('deletecomment') && $id_comment=(int)Tools::getValue('id_comment'))
        {
            if(Ybc_blog_post_employee_class::checkPermisionComment('delete',$id_comment) && ($comment = new Ybc_blog_comment_class($id_comment)) && Validate::isLoadedObject($comment))
            {
                if($comment->delete())
                {
                    if(Tools::isSubmit('ajax'))
                    {
                        die(
                            json_encode(
                                array(
                                    'success' => $this->module->l('You have just deleted the comment successfully','managementcomments'),
                                )
                            )
                        );
                    }
                    else
                        Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'managementcomments',array('tabmanagament'=>$tabmanagament,'deletedcomment'=>1)));
                }

            }
            else
            {
                if(Tools::isSubmit('ajax'))
                {
                    die(
                        json_encode(
                            array(
                                'error' => $this->module->l('Sorry, you do not have permission','managementcomments'),
                            )
                        )
                    );
                }
                else
                    $this->_errros[]=$this->module->l('Sorry, you do not have permission','managementcomments');
            }
               
        }   
        if(Tools::isSubmit('deletedcomment'))
            $this->sussecfull = $this->module->l('You have just deleted the comment successfully','managementcomments');
        if(Tools::isSubmit('updateComment'))
            $this->sussecfull = $this->module->l('Comment updated','managementcomments');
        $this->context->smarty->assign(
            array(
                'errors_html'=>$this->_errros ? $this->module->displayError($this->_errros) : false,
                'form_html_post'=>$form_html_post,
                'sucsecfull_html' => $this->sussecfull ? $this->module->displaySuccessMessage($this->sussecfull):'',
                'breadcrumb' => $this->module->is17 ? $this->getBreadCrumb() : false, 
                'path' => $this->getBreadCrumb(),
            )
        );
        if($this->module->is17)
            $this->setTemplate('module:ybc_blog/views/templates/front/management_comments.tpl');      
        else         
            $this->setTemplate('management_comments16.tpl');  
    }
    public function _saveComment()
    {
        $id_comment = (int)Tools::getValue('id_comment');
        $tabmanagament = Tools::getValue('tabmanagament');
        if($tabmanagament && !Validate::isCleanHtml($tabmanagament))
            $tabmanagament ='comment';
        if(Ybc_blog_post_employee_class::checkPermisionComment('',$id_comment,$tabmanagament))
        {
            $ybc_comment= new Ybc_blog_comment_class($id_comment);
            if(!($subject = Tools::getValue('subject')))
                $this->_errros[]= $this->module->l('Subject is required','managementcomments');
            elseif(!Validate::isCleanHtml($subject))
                $this->_errros[]= $this->module->l('Subject is not valid','managementcomments');
            else
                $ybc_comment->subject = $subject;
            if(!($comment = Tools::getValue('comment')))
                $this->_errros[] = $this->module->l('Comment is requied','managementcomments');
            elseif(Tools::strlen($comment)<20)
                $this->_errros[]=$this->module->l('Comment needs to be at least 20 characters','managementcomments');
            elseif(!Validate::isCleanHtml($comment,true))
                $this->_errros[] = $this->module->l('Comment is not valid','managementcomments');
            else
                $ybc_comment->comment = Tools::getValue('comment');
            if(Tools::isSubmit('reply'))
            {
                $reply = Tools::getValue('reply');
                if($reply && !Validate::isCleanHtml($reply,true))
                    $this->_errros[] = $this->module->l('Reply is not valid','managementcomments');
                else
                {
                    $ybc_comment->reply = $reply;
                    if($reply)
                    {
                        $ybc_comment->replied_by = $this->context->customer->id;
                        $ybc_comment->customer_reply=1;
                    }
                    else
                        $ybc_comment->customer_reply=0;
                }
                
            }
            if(Tools::isSubmit('approved'))
            {
                $approved = (int)Tools::getValue('approved');
                $ybc_comment->approved = $approved;
            }
            $tabmanagament = Tools::getValue('tabmanagament');
            if($tabmanagament && !Validate::isCleanHtml($tabmanagament))
                $tabmanagament ='post';
            if(!$this->_errros)
            {
                $ybc_comment->update();
                if(Tools::isSubmit('submitComment'))
                    Tools::redirectLink($this->context->link->getModuleLink($this->module->name,'managementcomments',array('tabmanagament'=>$tabmanagament,'updateComment'=>1)));
                else
                    $this->sussecfull = $this->module->l('Comment updated','managementcomments');
            }
                
        }
    }
    public function getBreadCrumb()
    {
        $nodes=array();
        $nodes[] = array(
            'title' => $this->module->l('Home','managementcomments'),
            'url' => $this->context->link->getPageLink('index', true),
        );
        $nodes[] = array(
            'title' => $this->module->l('Your account','managementcomments'),
            'url' => $this->context->link->getPageLink('my-account'),
        );
        $nodes[] = array(
            'title' => $this->module->l('My blog comments','managementcomments'),
            'url' => $this->context->link->getModuleLink('ybc_blog','managementcomments'),
        );
        if($this->module->is17)
                return array('links' => $nodes,'count' => count($nodes));
        return $this->module->displayBreadcrumb($nodes);
    }
}