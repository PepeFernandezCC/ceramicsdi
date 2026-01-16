<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/controllers/admin/AdminManoManoOrdersController.php';
require_once __DIR__ . '/classes/ManoManoImportPayment.php';

class ManoManoOrders extends Module
{
    public function __construct()
    {
        $this->name = 'manomanoorders';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'José Fernández';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('ManoMano Orders Importer');
        $this->description = $this->l('Importa pedidos de ManoMano a tu Prestashop.');
        $this->confirmUninstall = $this->l('¿Estás seguro de desinstalar?');
    }

    public function install()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'mm_orders_imported` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `order_reference` VARCHAR(50) NOT NULL,
            `date_add` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `order_reference` (`order_reference`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        return parent::install()
            && $this->registerHook('displayBackOfficeHeader')
            && $this->installTab()
            && Db::getInstance()->execute($sql);
    }

    public function uninstall()
    {
        Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'mm_orders_imported`');
        return parent::uninstall() && $this->uninstallTab();
    }

    private function installTab()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminManoManoOrders';
        $tab->module = $this->name;
        $tab->id_parent = (int) Tab::getIdFromClassName('SELL');
        if (!$tab->id_parent) {
            $tab->id_parent = 1;
        }
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Pedidos ManoMano';
        }
        return $tab->add();
    }

    private function uninstallTab()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminManoManoOrders');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return true;
    }

    public function getContent()
    {
        if (Tools::isSubmit('submit'.$this->name)) {
            Configuration::updateValue('MM_API_KEY', Tools::getValue('MM_API_KEY'));
            //Configuration::updateValue('MM_SELLER_ID', Tools::getValue('MM_SELLER_ID'));
        }

        return $this->renderForm();
    }
    //SELLER ID ES UN ARRAY EN EL CONTROLADOR
    private function renderForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Configuración API ManoMano'),
                    'icon' => 'icon-cogs'
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('API Key'),
                        'name' => 'MM_API_KEY',
                        'required' => true
                    ]
                    /*
                    ,[
                        'type' => 'text',
                        'label' => $this->l('Seller Contract ID'),
                        'name' => 'MM_SELLER_ID',
                        'required' => true
                    ]
                    */
                ],
                'submit' => [
                    'title' => $this->l('Guardar'),
                ]
            ]
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit'.$this->name;
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->fields_value['MM_API_KEY'] = Configuration::get('MM_API_KEY');
        //$helper->fields_value['MM_SELLER_ID'] = Configuration::get('MM_SELLER_ID');

        return $helper->generateForm([$fields_form]);
    }
}
