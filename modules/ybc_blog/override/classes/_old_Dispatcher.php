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

class Dispatcher extends DispatcherCore
{
    public function getController($id_shop = null)
    {
        parent::getController($id_shop);
        $controller = $this->controller;
        if ($controller == '404' || $controller == 'pagenotfound' || $controller == 'sitemap') {
            if (Configuration::get('YBC_BLOG_ENABLE_SITEMAP') && preg_match("/modules\/ybc_blog\/sitemap(\/(\w+(\/(\w+)|))|)\.xml$/", $this->request_uri)) {
                $_GET['module'] = 'ybc_blog';
                $this->controller = 'sitemap';
                $_GET['fc'] = 'module';
                $this->front_controller = self::FC_MODULE;
            }
        }
        return $this->controller;
    }   
}