<?php
/**
 * 2016-2017 ZSolutions
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    Eugene Zubkov <magrabota@gmail.com>
 * @copyright 2017 ZLab Solutions
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Property of ZLab Solutions
 */

class AdminoutvioController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->setup();
    }

    private function setup()
    {
        if (Shop::isFeatureActive()) Shop::setContext(Shop::CONTEXT_ALL);

        $this->bootstrap = true;
        $this->html = '';
        $this->display = 'view';
        $this->meta_title = $this->l('Mass Actions');

        $context = Context::getContext();

        // ajax
        if ((Tools::getValue('outvio_ajax') == 1) && isset($context->employee) && ($context->employee->id > 0)) {
            $api_key = Tools::getValue('api_key');
            if ($api_key) {
                Configuration::updateGlobalValue('OUTVIO_API_KEY', $api_key);
                echo 'true';
            } else {
                echo 'false';
            }

            die();
        }

        // regular redirect
        if (isset($context->employee) && ($context->employee->id > 0)) {
            $id_employee = $context->employee->id;
            $token = self::getAdminToken($id_employee);
            Tools::redirectAdmin("index.php?controller=AdminModules&token=$token&configure=outvio");
        } else {
            die();
        }
    }

    public function renderView()
    {
        $context = Context::getContext();

        if (isset($context->employee) && ($context->employee->id > 0)) {
            $id_employee = $context->employee->id;
            $token = self::getAdminToken($id_employee);
            Tools::redirectAdmin("index.php?controller=AdminModules&token=$token&configure=outvio");
        } else {
            die();
        }
    }

    public static function getAdminToken($id_employee)
    {
        $tab = 'AdminModules';
        if (_PS_VERSION_ >= '8.0.0') $tab_id = SymfonyContainer::getInstance()
            ->get('prestashop.core.admin.tab.repository')
            ->findOneIdByClassName($tab);
        else $tab_id = (int)Tab::getIdFromClassName($tab);
        return Tools::getAdminToken($tab . $tab_id . (int)$id_employee);
    }
}
