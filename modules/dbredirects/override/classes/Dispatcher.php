<?php
/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2021 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class Dispatcher extends DispatcherCore
{
    public function dispatch()
    {
        $module = Module::getInstanceByName('dbredirects');
        if ( is_object($module) && $module->active ) {
            $uri_var = $_SERVER['REQUEST_URI'];
            $redirect = DbRedirect::isRedirect($uri_var);
            if (isset($redirect['url_antigua']) && $uri_var == $redirect['url_antigua']) {
                switch ($redirect['type']) {
                    case '1':
                        Tools::redirect($redirect['url_nueva'], __PS_BASE_URI__, null, 'HTTP/1.1 301 Moved Permanently');
                        break;
                    case '2':
                        header("HTTP/1.1 410 Gone");
                        exit;
                        break;
                }
            }
        }
        parent::dispatch();
    }
}