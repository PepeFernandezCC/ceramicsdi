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

if (!defined('_PS_VERSION_')) {
    exit;
}

class Dbredirects extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        require_once(dirname(__FILE__).'/classes/DbRedirect.php');
        if(file_exists(dirname(__FILE__).'/premium/DbPremium.php')){
            require_once(dirname(__FILE__).'/premium/DbPremium.php');
            $this->premium = 1;
        } else {
            $this->premium = 0;
        }

        $this->name = 'dbredirects';
        $this->tab = 'administration';
        $this->version = '1.2.0';
        $this->author = 'DevBlinders';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('DB Redirects');
        $this->description = $this->l('Redirecciones 301 y bloqueos 410');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        include _PS_MODULE_DIR_ . 'dbredirects/sql/install.php';
        $this->createTabs();
        return parent::install();
    }

    public function uninstall()
    {
        include _PS_MODULE_DIR_ . 'dbredirects/sql/uninstall.php';
        $this->deleteTabs();
        return parent::uninstall();
    }


    /**
     * Create Tabs
     */
    public function createTabs()
    {
        // Tabs
        $idTabs = array();
        $idTabs[] = Tab::getIdFromClassName('AdminDbRedirects');
        $idTabs[] = Tab::getIdFromClassName('AdminDbRedirects301');
        $idTabs[] = Tab::getIdFromClassName('AdminDbRedirects410');

        foreach ($idTabs as $idTab) {
            if ($idTab) {
                $tab = new Tab($idTab);
                $tab->delete();
            }
        }

        // Tabs
        if (!Tab::getIdFromClassName('AdminDevBlinders')) {
            $parent_tab = new Tab();
            $parent_tab->name = array();
            foreach (Language::getLanguages(true) as $lang)
                $parent_tab->name[$lang['id_lang']] = $this->l('DevBlinders');

            $parent_tab->class_name = 'AdminDevBlinders';
            $parent_tab->id_parent = 0;
            $parent_tab->module = $this->name;
            $parent_tab->add();

            $id_full_parent = $parent_tab->id;
        } else {
            $id_full_parent = Tab::getIdFromClassName('AdminDevBlinders');
        }

        $parent = new Tab();
        $parent->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $parent->name[$lang['id_lang']] = $this->l('Redirecciones');

        $parent->class_name = 'AdminDbRedirects';
        $parent->id_parent = $id_full_parent;
        $parent->module = $this->name;
        $parent->icon = 'compare_arrows';
        $parent->add();

        // 301
        $tab_config = new Tab();
        $tab_config->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab_config->name[$lang['id_lang']] = $this->l('301');

        $tab_config->class_name = 'AdminDbRedirects301';
        $tab_config->id_parent = $parent->id;
        $tab_config->module = $this->name;
        $tab_config->add();

        // 410
        $tab_config = new Tab();
        $tab_config->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab_config->name[$lang['id_lang']] = $this->l('410');

        $tab_config->class_name = 'AdminDbRedirects410';
        $tab_config->id_parent = $parent->id;
        $tab_config->module = $this->name;
        $tab_config->add();

    }

    /**
     * Delete Tabs
     */
    public function deleteTabs()
    {
        // Tabs
        $idTabs = array();
        $idTabs[] = Tab::getIdFromClassName('AdminDbRedirects');
        $idTabs[] = Tab::getIdFromClassName('AdminDbRedirects301');
        $idTabs[] = Tab::getIdFromClassName('AdminDbRedirects410');

        foreach ($idTabs as $idTab) {
            if ($idTab) {
                $tab = new Tab($idTab);
                $tab->delete();
            }
        }
    }

}
