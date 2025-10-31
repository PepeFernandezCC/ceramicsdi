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
class Ybc_blogCapchaModuleFrontController extends ModuleFrontController
{
    public function init()
	{
		$this->create_image();
        die;
	}
    public function create_image()
    {
        $type = Tools::getValue('type','comment');
        if($type=='comment')
        {
            if(Tools::isSubmit('reset'))
            {
                $security_code = Tools::passwdGen(5);
                $this->context->cookie->__set('ybc_security_captcha_code', $security_code);
            }
            else
                $security_code = $this->context->cookie->__get('ybc_security_captcha_code');
        }
        else
        {
            if(Tools::isSubmit('reset'))
            {
                $security_code = Tools::passwdGen(5);
                $this->context->cookie->__set('security_polls_captcha_code', $security_code);
            }
            else
                $security_code = $this->context->cookie->__get('security_polls_captcha_code');
        }
        require_once(_PS_MODULE_DIR_.'ybc_blog/classes/OverridUtitl');
        $class= 'Ybc_blog_overrideUtil';
        $method = 'createImage';
        call_user_func_array(array($class, $method),array($security_code));
        exit();
    }
}