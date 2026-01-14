<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Pslanding extends Module
{
    public function __construct()
    {
        $this->name = 'pslanding';
        $this->tab = 'front_office_features';
        $this->version = '1.0.2';
        $this->author = 'Jose Fernández';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Landing Pages');
        $this->description = $this->l('Genera landing pages con URLs amigables y contenido multidioma.');
    }

    public function install()
    {
        return parent::install()
            && $this->installDb()
            && $this->registerHook('moduleRoutes')   // 👈 IMPORTANTE
            && $this->registerHook('header')  // opcional, si metes css
            && $this->registerHook('displayBackOfficeHeader')
            && $this->installTab();
    }

    public function uninstall()
    {
        $this->uninstallTab();
        return parent::uninstall();
    }

    protected function installDb()
    {
        $sql_file = dirname(__FILE__).'/sql/install.sql';
        if (!file_exists($sql_file)) {
            return false;
        }

        $sql = Tools::file_get_contents($sql_file);
        $queries = preg_split("/;\s*[\r\n]+/", $sql);

        foreach ($queries as $query) {
            $query = trim($query);
            if (empty($query)) {
                continue;
            }
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    protected function uninstallDb()
    {
        $sql = [];
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'pslanding_characteristic_lang`';
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'pslanding_characteristic`';
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'pslanding_lang`';
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'pslanding`';

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }
    protected function installTab()
    {
        if (Tab::getIdFromClassName('AdminPsLanding')) {
            return true;
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminPsLanding';
        $tab->name = [];

        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[$lang['id_lang']] = 'Landing Pages';
        }

        $parents = ['IMPROVE', 'SELL', 'AdminParentModulesSf', 'AdminCatalog'];
        $parent_id = 0;

        foreach ($parents as $parent) {
            $id = (int) Tab::getIdFromClassName($parent);
            if ($id > 0) {
                $parent_id = $id;
                break;
            }
        }

        if ($parent_id == 0) {
            $parent_id = -1;
        }

        $tab->id_parent = $parent_id;
        $tab->module = $this->name;

        return (bool) $tab->add();
    }

    protected function uninstallTab()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminPsLanding');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return (bool) $tab->delete();
        }
        return true;
    }

    public function hookModuleRoutes($params)
    {
        return [
            // /landing
            'module-pslanding-landing-home' => [
                'controller' => 'landing',
                'rule'       => 'landing',
                'keywords'   => [],
                'params'     => [
                    'fc'     => 'module',
                    'module' => $this->name,
                ],
            ],

            // /landing/{slug}
            'module-pslanding-landing' => [
                'controller' => 'landing',
                'rule'       => 'landing/{slug}',
                'keywords'   => [
                    'slug' => ['regexp' => '[_a-zA-Z0-9\\-]+', 'param' => 'slug'],
                ],
                'params'     => [
                    'fc'     => 'module',
                    'module' => $this->name,
                ],
            ],
        ];
    }


    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path.'/views/assets/css/front.css');
        $this->context->controller->registerJavascript(
            'landing-frontend',
            'modules/'.$this->name.'/views/assets/js/front.js',
            ['position' => 'bottom', 'priority' => 150]
        );;
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/assets/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/assets/css/back.css');
        }
    }


}
