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
 * Class Ybc_blogLikeModuleFrontController
 * @property Ybc_blog $module;
 */
class Ybc_blogLikeModuleFrontController extends ModuleFrontController
{
    public function init()
	{
	    parent::init();
        $json = array();
        $id_post = (int)Tools::getValue('id_post');
        $post = new Ybc_blog_post_class($id_post);
        if(!(Validate::isLoadedObject($post)))
        {
            $json['error'] = $this->module->l('This post does not exist','like');
            die(json_encode($json));
        }
        if(!(int)Configuration::get('YBC_BLOG_ALLOW_LIKE'))
        {
            $json['error'] = $this->module->l('You are not allowed to like the post','like');
            die(json_encode($json));
        }
        if(!(int)Configuration::get('YBC_BLOG_GUEST_LIKE') && !$this->context->customer->id)
        {
            $json['error'] = $this->module->l('You need to log in to like the post','like');
            die(json_encode($json));
        }
        $browser= $this->module->getDevice();
        $likeds = $this->context->cookie->__get('liked_posts');
        if(!$this->module->isLikedPost($id_post))
        {
            if($likeds && Validate::isJson($likeds))
            {
                $likedPosts = json_decode($likeds,true);
                $likedPosts[]=$id_post;
                $this->context->cookie->__set('liked_posts', json_encode($likedPosts));
            }
            else
            {
                $likedPosts=array();
                $likedPosts[]=$id_post;
                $this->context->cookie->__set('liked_posts', json_encode($likedPosts));
            }
            $likes = $post->addLike($browser, $this->context->customer->id);
            $json['likes'] = $likes;
            $json['success'] = $this->module->l('Successfully liked the post','like');
            $json['liked']=true;
            die(json_encode($json));
        }
        else
        {
            if($likeds && Validate::isJson($likeds))
            {
                $likedPosts = json_decode($likeds,true);
                foreach($likedPosts as $key=>$val)
                {
                    if($val==$id_post)
                        unset($likedPosts[$key]);
                }
                $this->context->cookie->__set('liked_posts', json_encode($likedPosts));
            }
            $json['likes'] = $post->unLike( $this->context->customer->id);
            $json['success'] = $this->module->l('Successfully unliked the post','like');
            $json['liked']=false;
        }
        die(json_encode($json));
	}
}