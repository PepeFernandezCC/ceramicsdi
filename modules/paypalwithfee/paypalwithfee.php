<?php

/**

 * 2025 4webs

 *

 * DEVELOPED By 4webs.es Prestashop Platinum Partner

 *

 * @author    4webs

 * @copyright 4webs 2025

 * @license   4webs

 * @version 5.4.12

 * @category payment_gateways

 */



use PrestaShop\PrestaShop\Core\Payment\PaymentOption;



include(_PS_MODULE_DIR_ . 'paypalwithfee' . DIRECTORY_SEPARATOR . 'classes/PaypalOrder.php');

include(_PS_MODULE_DIR_ . 'paypalwithfee' . DIRECTORY_SEPARATOR . 'classes/PaypalRefund.php');



if (!defined('_PS_VERSION_')) {

    exit;

}



class Paypalwithfee extends PaymentModule

{

    protected $_html = '';

    protected $_postErrors = array();



    public function __construct()

    {

        $this->name = 'paypalwithfee';

        $this->tab = 'payments_gateways';

        $this->version = '5.4.12';

        $this->author = '4webs.es';

        $this->module_key = 'a919c7484a7ecf71b1665b04f2851680';

        $this->author_address = '0xF2f66881B34D8497784cD8B138bd0dE65734b84b';

        $this->bootstrap = true;

        parent::__construct();

        $this->need_instance = 0;

        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

        $this->ajax = true;



        $this->displayName = $this->l('PayPal with fee');

        $this->description = $this->l('Allows to your customers Pay by PayPal with an extra fee.');

        $this->confirmUninstall = $this->l('Are you sure about removing these details?');

    }



    public function install()

    {

        //Return the result

        if (version_compare(_PS_VERSION_, '8', '>=')) {

            return parent::install() &&

                $this->installDB() &&

                $this->installTab() &&

                $this->registerHook('paymentOptions') &&

                $this->registerHook('ActionEmailAddAfterContent') &&

                $this->registerHook('DisplayAdminOrderTabShip') &&

                $this->registerHook('DisplayAdminOrderContentShip') &&

                $this->registerHook('displayAdminOrderTabLink') &&

                $this->registerHook('displayAdminOrderTabContent') &&

                $this->registerHook('DisplayOrderConfirmation') &&

                $this->registerHook('DisplayOrderConfirmation1') &&

                $this->registerHook('ActionValidateOrder') &&

                $this->registerHook('sendMailAlterTemplateVars') &&

                $this->registerHook('DisplayOrderDetail') &&

                $this->registerHook('displayProductAdditionalInfo') &&

                $this->registerHook('actionObjectTaxRulesGroupUpdateAfter') &&

                $this->registerHook('displayBackOfficeHeader') &&

                $this->registerHook('displayHeader') &&

                $this->initialize();

        } else {

            return parent::install() &&

                $this->installDB() &&

                $this->installTab() &&

                $this->registerHook('paymentOptions') &&

                $this->registerHook('ActionEmailAddAfterContent') &&

                $this->registerHook('DisplayAdminOrderTabShip') &&

                $this->registerHook('DisplayAdminOrderContentShip') &&

                $this->registerHook('displayAdminOrderTabLink') &&

                $this->registerHook('displayAdminOrderTabContent') &&

                $this->registerHook('DisplayOrderConfirmation') &&

                $this->registerHook('DisplayOrderConfirmation1') &&

                $this->registerHook('ActionValidateOrder') &&

                $this->registerHook('sendMailAlterTemplateVars') &&

                $this->registerHook('DisplayOrderDetail') &&

                $this->registerHook('displayProductAdditionalInfo') &&

                $this->registerHook('actionObjectTaxRulesGroupUpdateAfter') &&

                $this->registerHook('backOfficeHeader') &&

                $this->registerHook('header') &&

                $this->initialize();

        }

    }



    public function installDB()

    {

        $return = true;

        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ppwf_order` ('

            . ' `id_ppwf` INT(9) NOT NULL AUTO_INCREMENT,'

            . ' `id_cart` INT(9) NOT NULL,'

            . ' `id_order` INT(9) NOT NULL,'

            . ' `total_amount` DECIMAL(20,6) NOT NULL,'

            . ' `tax_rate` DECIMAL(10,2),'

            . ' `fee` DECIMAL(20,6) NOT NULL,'

            . ' `transaction_id` VARCHAR(50) NOT NULL,'

            . ' `payer_id` VARCHAR(128) NOT NULL,'

            . ' `seller_protection` INT(1) NOT NULL,'

            . ' `id_shop` INT(2) NOT NULL,'

            . ' `customer_data` LONGTEXT NULL,'

            . ' PRIMARY KEY (`id_ppwf`)'

            . ' ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8;');



        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ppwf_order_refund` ('

            . '`id_refund` INT(9) NOT NULL AUTO_INCREMENT,'

            . '`id_ppwf` INT(9) NOT NULL,'

            . '`id_order` INT(9) NOT NULL,'

            . '`amount` DECIMAL(20,6) NOT NULL,'

            . '`transaction_id` VARCHAR(50) NOT NULL,'

            . '`date` DATETIME NOT NULL,'

            . 'PRIMARY KEY (`id_refund`)'

            . ') ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8;');



        return $return;

    }



    protected function uninstallDB()

    {

        $sql = array();



        $sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'ppwf_order';

        $sql[] = 'DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'ppwf_order_refund';



        foreach ($sql as $query) {

            if (Db::getInstance()->execute($query) == false) {

                return false;

            }

        }

    }



    public function uninstall()

    {

        $this->uninstallTab();



        $ppalstatus = new OrderState(Configuration::get('PPAL_FEE_PENDINGSTATE'));



        if (Validate::isLoadedObject($ppalstatus)) {

            $ppalstatus->delete();

        }



        /*if (version_compare(_PS_VERSION_, '8', '>=')) {

            $versionCompare = $this->unregisterHook('displayBackOfficeHeader') && $versionCompare = $this->unregisterHook('displayHeader');

        } else {

            $versionCompare = $this->unregisterHook('backOfficeHeader') && $versionCompare = $this->unregisterHook('header');

        }*/

        $this->uninstallDB();



        //Unregister the hooks

        if (version_compare(_PS_VERSION_, '8', '>=')) {

            $this->unregisterHook('paymentOptions');

            $this->unregisterHook('ActionEmailAddAfterContent');

            $this->unregisterHook('DisplayAdminOrderTabShip');

            $this->unregisterHook('DisplayAdminOrderContentShip');

            $this->unregisterHook('displayAdminOrderTabLink');

            $this->unregisterHook('displayAdminOrderTabContent');

            $this->unregisterHook('DisplayOrderConfirmation');

            $this->unregisterHook('DisplayOrderConfirmation1');

            $this->unregisterHook('ActionValidateOrder');

            $this->unregisterHook('sendMailAlterTemplateVars');

            $this->unregisterHook('DisplayOrderDetail');

            $this->unregisterHook('actionObjectTaxRulesGroupUpdateAfter');

            $this->unregisterHook('displayBackOfficeHeader');

            $this->unregisterHook('displayHeader');

        } else {

            $this->unregisterHook('paymentOptions');

            $this->unregisterHook('ActionEmailAddAfterContent');

            $this->unregisterHook('DisplayAdminOrderTabShip');

            $this->unregisterHook('DisplayAdminOrderContentShip');

            $this->unregisterHook('displayAdminOrderTabLink');

            $this->unregisterHook('displayAdminOrderTabContent');

            $this->unregisterHook('DisplayOrderConfirmation');

            $this->unregisterHook('DisplayOrderConfirmation1');

            $this->unregisterHook('ActionValidateOrder');

            $this->unregisterHook('sendMailAlterTemplateVars');

            $this->unregisterHook('DisplayOrderDetail');

            $this->unregisterHook('actionObjectTaxRulesGroupUpdateAfter');

            $this->unregisterHook('backOfficeHeader');

            $this->unregisterHook('header');

        }



        return Configuration::deleteByName('PPAL_FEE_USER') && Configuration::deleteByName('PPAL_FEE_PASS')

            && Configuration::deleteByName('PPAL_FEE_TEST') && Configuration::deleteByName('PPAL_FEE_PERCENTAGE')

            && Configuration::deleteByName('PPAL_FEE_FIXEDFEE') && Configuration::deleteByName('PPAL_FEE_LIMIT') &&

            Configuration::deleteByName('PPAL_FEE_DISABLECAR') && Configuration::deleteByName('PPAL_FEE_DISABLEMAN')

            && Configuration::deleteByName('PPAL_FEE_DISABLECAT') && Configuration::deleteByName('PPAL_FEE_DISABLEPRO')

            && Configuration::deleteByName('PPAL_FEE_DISABLECAT') &&

            Configuration::deleteByName('PPAL_TAX_FEE') && Configuration::deleteByName('PPAL_SEND_EMAIL_ON_ADDR_UPDATE')

            && Configuration::deleteByName('PPAL_FEE_PENDINGSTATE') && parent::uninstall();

    }



    private function installTab()

    {

        $tab = new Tab();

        $tab->active = 1;

        $tab->class_name = 'Refundppwf';

        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang) {

            $tab->name[$lang['id_lang']] = 'Refund';

        }

        $tab->id_parent = -1;

        $tab->module = $this->name;



        if ($tab->add()) {

            return true;

        }

        return false;

    }



    private function uninstallTab()

    {

        $id_tab = (int)Tab::getIdFromClassName('Refundppwf');

        if ($id_tab) {

            $tab = new Tab($id_tab);

            $tab->delete();

        }

    }



    public function initialize()

    {

        // Installing the "Pending" status

        $paypalStatus = _PS_OS_PAYPAL_;



        if (!Configuration::get('PPAL_FEE_PENDINGSTATE')) {

            $paypalStatus = new OrderState();

            $languages = Language::getLanguages(false);

            foreach ($languages as $language) {

                $paypalStatus->name[$language['id_lang']] = 'PayPal Payment Pending';

            }

            $paypalStatus->logable = true;

            $paypalStatus->color = '#4169E1';

            $paypalStatus->add();

            Configuration::updateValue('PPAL_FEE_PENDINGSTATE', $paypalStatus->id);

        }



        Configuration::updateValue('PPAL_FEE_USER', '');

        Configuration::updateValue('PPAL_FEE_PASS', '');

        Configuration::updateValue('PPAL_FEE_TEST', 0);

        Configuration::updateValue('PPAL_FEE_PERCENTAGE', '3.4');

        Configuration::updateValue('PPAL_FEE_FIXEDFEE', '0');

        Configuration::updateValue('PPAL_FEE_LIMIT', '0');



        Configuration::updateValue('PPAL_FEE_MIN', '0');

        Configuration::updateValue('PPAL_FEE_MAX', '0');

        // Restrictions

        Configuration::updateValue('PPAL_FEE_DISABLECAR', '');

        Configuration::updateValue('PPAL_FEE_DISABLEMAN', '');

        Configuration::updateValue('PPAL_FEE_DISABLECAT', '');

        Configuration::updateValue('PPAL_FEE_DISABLEPRO', '');



        Configuration::updateValue('PPAL_TAX_FEE', (int)Carrier::getIdTaxRulesGroupByIdCarrier(

            (int)Configuration::get('PS_CARRIER_DEFAULT')

        ));

        Configuration::updateValue('PPAL_ROUND_MODE', 0);

        Configuration::updateValue('PPAL_SEND_EMAIL_ON_ADDR_UPDATE', 0);

        Configuration::updateValue('PPAL_TAX_INCREASE', 0);

        Configuration::updateValue('PPAL_PAY_LATER', 0);

        return true;

    }



    public function get4webs()

    {

        $idlang = $this->context->language->id;

        $lang = Language::getIsoById($idlang);



        switch ($lang) {

            case 'es':

                $lang = 'es';

                break;

            default:

                $lang = 'en';

                break;

        }

        $this->context->smarty->assign(array(

            'module_name' => $this->name,

            'module_path' => $this->_path,

            'module_lang' => $lang,

        ));



        $this->context->controller->addCSS($this->_path . 'views/css/4webs_back.css');



        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'paypalwithfee/views/templates/admin/module_head.tpl');

    }



    public function getContent()

    {

        $this->_html = '';

        if (Tools::isSubmit("submitPaypal")) {

            $selected_tab_input = Tools::getValue('selected_tab_input');

            $this->context->smarty->assign('selected_tab_input', $selected_tab_input);

            $this->_postValidation();

            if (!count($this->_postErrors)) {

                $this->_postProcess();

            } else {

                foreach ($this->_postErrors as $error) {

                    $this->_html .= $this->displayError($error);

                }

            }

        }



        $this->_html .= $this->get4webs() . $this->renderForm();

        return $this->_html;

    }



    public function hookActionObjectTaxRulesGroupUpdateAfter($params)

    {

        $actual_id = Configuration::get('PPAL_TAX_FEE');

        $isDeleted = (boolean)Db::getInstance()->getValue('SELECT `deleted`

		FROM `' . _DB_PREFIX_ . 'tax_rules_group`

		WHERE `id_tax_rules_group` = ' . (int)$actual_id);



        if ($isDeleted) {

            Configuration::updateValue('PPAL_TAX_FEE', $params['object']->id);

        }

    }



    public function renderForm()

    {

        // List of carriers

        $carriers_ = Carrier::getCarriers(

            $this->context->language->id,

            false,

            false,

            false,

            null,

            Carrier::ALL_CARRIERS

        );





        $carriers = array();

        foreach ($carriers_ as $key => $carrier) {

            $carriers[$key]['id'] = $carrier['id_carrier'];

            $carriers[$key]['name'] = $carrier['name'];

            $carriers[$key]['val'] = $carrier['id_reference'];

        }



        // List of manufacturers

        $manufacturers_ = Manufacturer::getManufacturers();

        $manufacturers = array();

        foreach ($manufacturers_ as $key => $manufacturer) {

            $manufacturers[$key]['id'] = $manufacturer['id_manufacturer'];

            $manufacturers[$key]['name'] = $manufacturer['name'];

            $manufacturers[$key]['val'] = $manufacturer['id_manufacturer'];

        }



        // List of categories

        //$categoriesWithParentInfos = Category::getCategories($this->context->language->id);

        $categories = new HelperTreeCategories(

            'associated-categories-tree',

            $this->l('Category'),

            $this->context->language->id

        );

        $categories->setUseCheckBox(1)

            ->setUseSearch(1);

        $categories->setInputName("categories");

        $categories->setSelectedCategories(explode(',', Configuration::get('PPAL_FEE_DISABLECAT')));

        //List of products

        $products = array();

        $disabled_products = Configuration::get('PPAL_FEE_DISABLEPRO');

        if ($disabled_products) {

            $disabled_products = explode(',', $disabled_products);

            foreach ($disabled_products as $disprod) {

                $products[$disprod]['id_product'] = $disprod;

                $products[$disprod]['name'] = Product::getProductName(

                    $disprod,

                    null,

                    $this->context->language->id

                );

            }

        }



        $ajax_url_ng = Context::getContext()->link->getModuleLink(

            $this->name,

            'ajaxppwf',

            [

                'token' => Tools::getAdminToken($this->name),

            ]

        );



        //tax

        $tax = TaxRulesGroup::getTaxRulesGroups(true);

        $tax[] = array('id_tax_rules_group' => 0, 'name' => $this->l('None'));

        $tax_id = array();

        foreach ($tax as $key => $row) {

            $tax_id[$key] = $row['id_tax_rules_group'];

        }

        array_multisort($tax_id, SORT_ASC, $tax);



        $fields_form = array(



            'form' => array(

                'tabs' => array(

                    'paypal_credentials' => $this->l('Paypal credentials'),

                    'extra_fee_limits' => $this->l('Extra fee and limits'),

                    'more_options' => $this->l('More options'),

                ),

                'legend' => array(

                    'title' => $this->displayName,

                    'desc' => $this->l('Paypal configuration'),

                    'icon' => 'icon-wrench',

                ),

                'input' => array(

                    array(

                        'type' => 'radio',

                        'label' => $this->l('Environment'),

                        'name' => 'PPAL_FEE_TEST',

                        'is_bool' => true,

                        'required' => true,

                        'tab' => 'paypal_credentials',

                        'values' => array(

                            array(

                                'id' => 'active_real',

                                'value' => 0,

                                'label' => $this->l('Real (ready to recieve payments).'),

                            ),

                            array(

                                'id' => 'active_test',

                                'value' => 1,

                                'label' => $this->l('Test (only sandbox test accounts).'),

                            ),

                        ),

                    ),

                    array(

                        'type' => 'text',

                        'label' => $this->l('Client ID'),

                        'name' => 'PPAL_FEE_USER',

                        'tab' => 'paypal_credentials',

                        'required' => true,

                        'hint' => $this->l('Please enter the client ID from Paypal app.'),

                    ),

                    array(

                        'type' => 'text',

                        'label' => $this->l('Secret'),

                        'name' => 'PPAL_FEE_PASS',

                        'tab' => 'paypal_credentials',

                        'required' => true,

                        'hint' => $this->l('Please enter the secret from Paypal app.'),

                    ),

                    array(

                        'type' => 'text',

                        'label' => $this->l('Percentage of the fee'),

                        'name' => 'PPAL_FEE_PERCENTAGE',

                        'tab' => 'extra_fee_limits',

                        'hint' => $this->l('Example: 5% amount. Set 0 to disable.'),

                        'col' => 3,

                    ),

                    array(

                        'type' => 'text',

                        'label' => $this->l('Fixed fee (optional)'),

                        'name' => 'PPAL_FEE_FIXEDFEE',

                        'tab' => 'extra_fee_limits',

                        'hint' => $this->l('Example: 10€ amount. Set 0 to disable.'),

                        'col' => 3,

                    ),

                    array(

                        'type' => 'text',

                        'label' => $this->l('Limit payment to'),

                        'name' => 'PPAL_FEE_LIMIT',

                        'tab' => 'extra_fee_limits',

                        'hint' => $this->l('Maximum payment for Paypal in store. Set 0 to disable.'),

                        'col' => 3,

                    ),

                    /* PPAL_FEE_MIN*/

                    array(

                        'type' => 'text',

                        'label' => $this->l('Minimum Fee Value'),

                        'name' => 'PPAL_FEE_MIN',

                        'tab' => 'extra_fee_limits',

                        'hint' => $this->l('Minimum value of the fee. Set 0 to disable.'),

                        'col' => 3,

                    ),

                    //PPAL_FEE_MAX

                    array(

                        'type' => 'text',

                        'label' => $this->l('Maximum Fee Value'),

                        'name' => 'PPAL_FEE_MAX',

                        'tab' => 'extra_fee_limits',

                        'hint' => $this->l('Maximum value of the fee. Set 0 to disable.'),

                        'col' => 3,

                    ),

                    array(

                        'type' => 'html',

                        'name' => 'PPAL_HTML1',

                        'tab' => 'extra_fee_limits',

                        'html_content' => $this->l('Disable this payment method for:'),

                    ),

                    array(

                        'type' => 'checkbox',

                        'label' => $this->l('Carriers'),

                        'hidden_label' => true,

                        'name' => 'carriers[]',

                        'class' => 't',

                        'multiple' => true,

                        'title' => $this->l('Carrier'),

                        'tab' => 'extra_fee_limits',

                        'form_group_class' => 'col-lg-4',

                        'col' => 12,

                        'values' => array(

                            'query' => $carriers,

                            'id' => 'id',

                            'name' => 'name',

                        ),

                        'expand' => array(

                            'print_total' => count($carriers),

                            'default' => 'hide',

                            'show' => array('text' => $this->l('show'), 'icon' => 'plus-sign-alt'),

                            'hide' => array('text' => $this->l('hide'), 'icon' => 'minus-sign-alt')

                        ),

                        //'hint' => $this->l('Show this fields in search results.'),

                    ),

                    array(

                        'type' => 'checkbox',

                        'label' => $this->l('Manufacturers'),

                        'hidden_label' => true,

                        'name' => 'manufacturers[]',

                        'class' => 't',

                        'multiple' => true,

                        'title' => $this->l('Manufacturer'),

                        'tab' => 'extra_fee_limits',

                        'form_group_class' => 'col-lg-4',

                        'col' => 12,

                        'values' => array(

                            'query' => $manufacturers,

                            'id' => 'id',

                            'name' => 'name',

                        ),

                        'expand' => array(

                            'print_total' => count($manufacturers),

                            'default' => 'hide',

                            'show' => array('text' => $this->l('show'), 'icon' => 'plus-sign-alt'),

                            'hide' => array('text' => $this->l('hide'), 'icon' => 'minus-sign-alt')

                        ),

                        //'hint' => $this->l('Show this fields in search results.'),

                    ),

                    array(

                        'type' => 'search_products',

                        'label' => $this->l('Products'),

                        'hidden_label' => true,

                        'name' => 'products[]',

                        'title' => $this->l('Product'),

                        'tab' => 'extra_fee_limits',

                        'form_group_class' => 'col-lg-4',

                        'col' => 12,

                        'ajax_url' => $ajax_url_ng,

                        'ajax_token' => '?token=' . Tools::getAdminToken('paypalwithfee'),

                        'values' => array(

                            'query' => $products,

                            'id' => 'id_product',

                            'name' => 'name',

                        ),

                        //'hint' => $this->l('Show this fields in search results.'),

                    ),

                    array(

                        'type' => 'search_categories',

                        'label' => $this->l('Categories'),

                        'hidden_label' => true,

                        'name' => 'categories[]',

                        'title' => $this->l('Categories'),

                        'tab' => 'extra_fee_limits',

                        'col' => 12,

                        'render' => $categories,

                        'form_group_class' => 'col-lg-12',

                        //'hint' => $this->l('Show this fields in search results.'),

                    ),

                    array(

                        'type' => 'switch',

                        'label' => $this->l('Round mode compatibility'),

                        'name' => 'PPAL_ROUND_MODE',

                        'tab' => 'more_options',

                        'col' => 0,

                        'class' => 't',

                        'hint' => $this->l('If you have problems with paypal rounding mode. Active it to rounding total. If you use in prestashop product price with more than 2 decimals.'),

                        'required' => false,

                        'is_bool' => true,

                        'values' => array(

                            array(

                                'id' => 'roundmode_enabled',

                                'value' => 1,

                                'label' => $this->l('Enabled')

                            ),

                            array(

                                'id' => 'roundmode_disabled',

                                'value' => 0,

                                'label' => $this->l('Disabled')

                            )

                        ),

                    ),

                    array(

                        'type' => 'select',

                        'label' => $this->l('Breaking TAX into invoice'),

                        'name' => 'PPAL_TAX_FEE',

                        'tab' => 'more_options',

                        'default' => 'none',

                        'hint' => $this->l('Select if fee use tax. The tax base will be calculated on the total amount of the commission.'),

                        'col' => 3,

                        //'disabled' => Configuration::get('PPAL_CUSTOM_INVOICE') == 0 ? true : false,

                        'options' => array(

                            'query' => $tax,

                            'id' => 'id_tax_rules_group',

                            'name' => 'name',

                        )

                    ),

                    array(

                        'type' => 'switch',

                        'label' => $this->l('Add taxes to fees'),

                        'name' => 'PPAL_TAX_INCREASE',

                        'tab' => 'more_options',

                        'col' => 0,

                        'class' => 't',

                        'hint' => $this->l(

                            'To use this option, you must have the Break down VAT on invoice option activated.'

                        ),

                        'required' => false,

                        'is_bool' => true,

                        'values' => array(

                            array(

                                'id' => 'tax_increase_enabled',

                                'value' => 1,

                                'label' => $this->l('Enabled')

                            ),

                            array(

                                'id' => 'tax_increase_disabled',

                                'value' => 0,

                                'label' => $this->l('Disabled')

                            )

                        ),

                    ),

                    array(

                        'type' => 'switch',

                        'label' => $this->l('Send email when update address'),

                        'name' => 'PPAL_SEND_EMAIL_ON_ADDR_UPDATE',

                        'tab' => 'more_options',

                        'col' => 0,

                        'class' => 't',

                        'hint' => $this->l('Check this if you want to send an email when the shipping address is changed'),

                        'required' => false,

                        'is_bool' => true,

                        'values' => array(

                            array(

                                'id' => 'sendemail_enabled',

                                'value' => 1,

                                'label' => $this->l('Enabled')

                            ),

                            array(

                                'id' => 'sendemail_enabled',

                                'value' => 0,

                                'label' => $this->l('Disabled')

                            )

                        ),

                    ),

                    array(

                        'type' => 'switch',

                        'label' => $this->l('Paypal pay later'),

                        'name' => 'PPAL_PAY_LATER',

                        'tab' => 'more_options',

                        'default' => 'true',

                        'desc' => $this->l('Show or hide the view of the "pay later" option in products and in the payment process.'),

                        'is_bool' => true,

                        'values' => array(

                            array(

                                'id' => 'pay_later_enabled',

                                'value' => 1,

                                'label' => $this->l('Enabled')

                            ),

                            array(

                                'id' => 'pay_later_disabled',

                                'value' => 0,

                                'label' => $this->l('Disabled')

                            )

                        ),

                    ),

                ),

                'submit' => array(

                    'title' => $this->l('Save'),

                    'name' => 'submitPaypal',

                )

            ),

        );



        $helper = new HelperForm();

        $helper->base_folder = _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/';

        $helper->base_tpl = 'configure.tpl';

        $helper->show_toolbar = false;

        $helper->identifier = $this->identifier;

        $helper->submit_action = 'submit';

        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' .

            $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;

        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(

            'fields_value' => $this->getConfigFieldsValues(),

            'languages' => $this->context->controller->getLanguages(),

            'id_language' => $this->context->language->id

        );



        $this->fields_form = array();



        return $helper->generateForm(array($fields_form));

    }





    public function getConfigFieldsValues()

    {

        $configvalues = array();



        if (Tools::getValue('carriers')) {

            $carriers = Tools::getValue('carriers');

        } else {

            $carriers = explode(',', Configuration::get('PPAL_FEE_DISABLECAR'));

        }



        foreach ($carriers as $carrier) {

            $configvalues['carriers[]_' . $carrier] = true;

        }



        if (Tools::getValue('manufacturers')) {

            $manufacturers = Tools::getValue('manufacturers');

        } else {

            $manufacturers = explode(',', Configuration::get('PPAL_FEE_DISABLEMAN'));

        }



        foreach ($manufacturers as $manufacturer) {

            $configvalues['manufacturers[]_' . $manufacturer] = true;

        }



        if (Tools::getValue('categories')) {

            $categories = Tools::getValue('categories');

        } else {

            $categories = explode(',', Configuration::get('PPAL_FEE_DISABLECAT'));

        }



        foreach ($categories as $category) {

            $configvalues['categories[]_' . $category] = true;

        }



        if (Tools::getValue('products')) {

            $products = Tools::getValue('products');

        } else {

            $products = explode(',', Configuration::get('PPAL_FEE_DISABLEPRO'));

        }



        foreach ($products as $product) {

            $configvalues['products']['id'] = $product;

            $configvalues['products']['name'] = Product::getProductName($product, null, $this->context->language->id);

        }



        $configvalues['PPAL_FEE_USER'] = Tools::getValue('PPAL_FEE_USER', Configuration::get('PPAL_FEE_USER'));

        $configvalues['PPAL_FEE_TEST'] = Tools::getValue('PPAL_FEE_TEST', Configuration::get('PPAL_FEE_TEST'));

        $configvalues['PPAL_PAY_LATER'] = Tools::getValue('PPAL_PAY_LATER', Configuration::get('PPAL_PAY_LATER'));

        $configvalues['PPAL_FEE_PASS'] = Tools::getValue('PPAL_FEE_PASS', Configuration::get('PPAL_FEE_PASS'));

        $configvalues['PPAL_FEE_PERCENTAGE'] = Tools::getValue(

            'PPAL_FEE_PERCENTAGE',

            Configuration::get('PPAL_FEE_PERCENTAGE')

        );

        $configvalues['PPAL_FEE_FIXEDFEE'] = Tools::getValue(

            'PPAL_FEE_FIXEDFEE',

            Configuration::get('PPAL_FEE_FIXEDFEE')

        );

        $configvalues['PPAL_FEE_LIMIT'] = Tools::getValue('PPAL_FEE_LIMIT', Configuration::get('PPAL_FEE_LIMIT'));



        $configvalues['PPAL_FEE_MIN'] = Tools::getValue('PPAL_FEE_MIN', Configuration::get('PPAL_FEE_MIN'));

        $configvalues['PPAL_FEE_MAX'] = Tools::getValue('PPAL_FEE_MAX', Configuration::get('PPAL_FEE_MAX'));



        $configvalues['PPAL_FEE_DISABLECAT'] = Tools::getValue(

            'PPAL_FEE_DISABLECAT',

            Configuration::get('PPAL_FEE_DISABLECAT')

        );

        $configvalues['PPAL_TAX_FEE'] = Tools::getValue('PPAL_TAX_FEE', Configuration::get('PPAL_TAX_FEE'));

        $configvalues['PPAL_SEND_EMAIL_ON_ADDR_UPDATE'] = Tools::getValue(

            'PPAL_SEND_EMAIL_ON_ADDR_UPDATE',

            Configuration::get('PPAL_SEND_EMAIL_ON_ADDR_UPDATE')

        );

        $configvalues['PPAL_ROUND_MODE'] = Tools::getValue('PPAL_ROUND_MODE', Configuration::get('PPAL_ROUND_MODE'));

        $configvalues['PPAL_TAX_INCREASE'] = Tools::getValue('PPAL_TAX_INCREASE',

            Configuration::get('PPAL_TAX_INCREASE'));



        return $configvalues;

    }



    protected function _postValidation()

    {

        if (trim(Tools::getValue('PPAL_FEE_USER')) == '') {

            $this->_postErrors[] = $this->l('Username API is required.');

        }//$this->trans('Username API is required.', array(), 'Modules.Paypalwithfee.Admin');

        if (Tools::getValue('PPAL_FEE_PASS') == '') {

            $this->_postErrors[] = $this->l('Password API is required.');

        }//$this->trans('Password API is required.', array(), 'Modules.Paypalwithfee.Admin');

        if (trim(Tools::getValue('PPAL_FEE_LIMIT')) == '') {

            $this->_postErrors[] = $this->l('Limit payment is invalid. Set 0 to disable.');

        }//$this->trans('Limit payment is invalid. Set 0 to disable.', array(), 'Modules.Paypalwithfee.Admin');

        if (trim(Tools::getValue('PPAL_FEE_PERCENTAGE')) == '') {

            $this->_postErrors[] = $this->l('Percentage is invalid. Set 0 to disable.');

        }//$this->trans('Percentage is invalid. Set 0 to disable.', array(), 'Modules.Paypalwithfee.Admin');

        if (trim(Tools::getValue('PPAL_FEE_FIXEDFEE')) == '') {

            $this->_postErrors[] = $this->l('Fixed fee is invalid. Set 0 to disable.');

        }//$this->trans('Fixed fee is invalid. Set 0 to disable.', array(), 'Modules.Paypalwithfee.Admin');

        if (Tools::getValue('PPAL_FEE_LIMIT')) {

            if (!preg_match('/-?^[0-9]{1,10}+(?:\.[0-9]{1,2})?$/', Tools::getValue('PPAL_FEE_LIMIT'))) {

                $this->_postErrors[] = $this->l('Limit payment not set correctly. (Example: 12.99).');

            }

        }//$this->trans('Limit payment not set correctly. (Example: 12.99).', array(), 'Modules.Paypalwithfee.Admin');

        if (Tools::getValue('PPAL_FEE_PERCENTAGE')) {

            if (!preg_match('/-?^[0-9]{1,10}+(?:\.[0-9]{1,2})?$/', Tools::getValue('PPAL_FEE_PERCENTAGE'))) {

                $this->_postErrors[] = $this->l('Percentage not set correctly. (Example: 3.4).');

            }

        }//$this->trans('Percentage not set correctly. (Example: 3.4).', array(), 'Modules.Paypalwithfee.Admin');

        if (Tools::getValue('PPAL_FEE_FIXEDFEE')) {

            if (!preg_match('/-?^[0-9]{1,10}+(?:\.[0-9]{1,2})?$/', Tools::getValue('PPAL_FEE_FIXEDFEE'))) {

                $this->_postErrors[] = $this->l('Fixed Fee not set correctly. (Example: 5.99).');

            }

        }//$this->trans('Fixed Fee not set correctly. (Example: 5.99).', array(), 'Modules.Paypalwithfee.Admin');

        if (Tools::getValue('PPAL_FEE_MIN')) {

            if (!preg_match('/-?^[0-9]{1,10}+(?:\.[0-9]{1,2})?$/', Tools::getValue('PPAL_FEE_MIN'))) {

                $this->_postErrors[] = $this->l('Minimum Fee not set correctly. (Example: 19.99).');

            }

        }

        if (Tools::getValue('PPAL_FEE_MIN')) {

            if (!preg_match('/-?^[0-9]{1,10}+(?:\.[0-9]{1,2})?$/', Tools::getValue('PPAL_FEE_MIN'))) {

                $this->_postErrors[] = $this->l('Maximum Fee not set correctly. (Example: 99.99).');

            }

        }

        if (trim(Tools::getValue('PPAL_FEE_MIN')) && trim(Tools::getValue('PPAL_FEE_MAX'))) {

            if (trim(Tools::getValue('PPAL_FEE_MIN')) > trim(Tools::getValue('PPAL_FEE_MAX'))) {

                $this->_postErrors[] = $this->l('The minimun fee value can not be higher than maximum fee value.');

            }

        }

    }



    protected function _postProcess()

    {

        if (Tools::isSubmit('submitPaypal')) {

            $carriers = Tools::getValue('carriers') ? implode(',', Tools::getValue('carriers')) : '';

            $manufacturers = Tools::getValue('manufacturers') ? implode(',', Tools::getValue('manufacturers')) : '';

            $categories = Tools::getValue('categories') ? implode(',', Tools::getValue('categories')) : '';

            $products = Tools::getValue('products') ? implode(',', Tools::getValue('products')) : '';



            Configuration::updateValue('PPAL_FEE_DISABLECAR', $carriers);

            Configuration::updateValue('PPAL_FEE_DISABLEMAN', $manufacturers);

            Configuration::updateValue('PPAL_FEE_DISABLECAT', $categories);

            Configuration::updateValue('PPAL_FEE_DISABLEPRO', $products);



            Configuration::updateValue('PPAL_FEE_USER', trim(Tools::getValue('PPAL_FEE_USER')));

            Configuration::updateValue('PPAL_FEE_PASS', trim(Tools::getValue('PPAL_FEE_PASS')));

            Configuration::updateValue('PPAL_PAY_LATER', Tools::getValue('PPAL_PAY_LATER'));

            Configuration::updateValue('PPAL_FEE_PERCENTAGE', trim(Tools::getValue('PPAL_FEE_PERCENTAGE')));

            Configuration::updateValue('PPAL_FEE_FIXEDFEE', trim(Tools::getValue('PPAL_FEE_FIXEDFEE')));

            Configuration::updateValue('PPAL_FEE_TEST', Tools::getValue('PPAL_FEE_TEST'));

            Configuration::updateValue('PPAL_FEE_LIMIT', trim(Tools::getValue('PPAL_FEE_LIMIT')));



            Configuration::updateValue('PPAL_FEE_MIN', (Tools::getValue('PPAL_FEE_MIN')));

            Configuration::updateValue('PPAL_FEE_MAX', (Tools::getValue('PPAL_FEE_MAX')));



            Configuration::updateValue('PPAL_TAX_FEE', (Tools::getValue('PPAL_TAX_FEE')));

            Configuration::updateValue(

                'PPAL_SEND_EMAIL_ON_ADDR_UPDATE',

                (Tools::getValue('PPAL_SEND_EMAIL_ON_ADDR_UPDATE'))

            );

            Configuration::updateValue('PPAL_ROUND_MODE', (Tools::getValue('PPAL_ROUND_MODE')));

            Configuration::updateValue('PPAL_TAX_INCREASE', (Tools::getValue('PPAL_TAX_INCREASE')));

            $this->_html .= $this->displayConfirmation($this->l('Settings updated'));

        }

    }



    public function checkCurrency($cart)

    {

        $currency_order = new Currency($cart->id_currency);

        $currencies_module = $this->getCurrency($cart->id_currency);



        if (is_array($currencies_module)) {

            foreach ($currencies_module as $currency_module) {

                if ($currency_order->id == $currency_module['id_currency']) {

                    return true;

                }

            }

        }

        return false;

    }



    public function hookPaymentOptions($params)

    {

        if (!$this->active) {

            return;

        }



        if (!$this->checkCurrency($params['cart'])) {

            return;

        }



        $blockme = false;

        $categories_search = array();



        $limited_carriers = explode(",", Configuration::get('PPAL_FEE_DISABLECAR'));



        $limited_manufacturers = explode(",", Configuration::get('PPAL_FEE_DISABLEMAN'));



        $limited_categories = explode(",", Configuration::get('PPAL_FEE_DISABLECAT'));



        $limited_products = explode(",", Configuration::get('PPAL_FEE_DISABLEPRO'));



        $cart_products = $params['cart']->getProducts(true);



        $ppal_fee_limit = Configuration::get('PPAL_FEE_LIMIT');



        if ($params['cart']->id_currency != Configuration::get('PS_CURRENCY_DEFAULT')) {

            $current_currency = new Currency($params['cart']->id_currency);

            $conversion_rate = $current_currency->conversion_rate;

            $ppal_fee_limit = round($ppal_fee_limit * $conversion_rate, 6);

        }



        $all_is_virtual = true;



        foreach ($cart_products as $product) {

            $categories_search[] = $product['id_category_default'];



            // check manufacturers

            if ($product['id_manufacturer'] != null) {

                if (in_array($product['id_manufacturer'], $limited_manufacturers)) {

                    $blockme = true;

                }

            }

            // check products

            if (in_array($product['id_product'], $limited_products)) {

                $blockme = true;

            }



            foreach ($this->getCategoriesByProduct($product['id_product']) as $category) {

                $categories_search[] = $category['id_category'];

            }



            $all_is_virtual &= $product['is_virtual'];

        }



        $delivery_option = $params['cart']->getDeliveryOption();

        if (!empty($delivery_option) && reset($delivery_option)) {

            $carriers = explode(",", reset($delivery_option));

            $carriers = array_values(array_diff($carriers, array('')));

        } else {

            $carriers[] = $params['cart']->id_carrier;

        }



        foreach ($carriers as $id_carrier) {

            $carrier = new Carrier($id_carrier);

            // check id_reference of the carrier

            if (in_array($carrier->id_reference, $limited_carriers) && count($limited_carriers) > 0) {

                //if all products are virtual -> no block because carrier is not needed.

                if (!$all_is_virtual) {

                    $blockme = true;

                }

            }

        }



        foreach ($limited_categories as $value) {

            if (in_array($value, $categories_search)) {

                $blockme = true;

            }

        }



        if ($ppal_fee_limit >= 0 && !$blockme) {

            $total_compare = $this->context->cart->getOrderTotal(true);



            if ($total_compare <= $ppal_fee_limit || $ppal_fee_limit == 0) {

                $link = new Link();

                $path_controller = Configuration::get('PS_SSL_ENABLED') == 0 ?

                    $this->context->link->getModuleLink('paypalwithfee', 'payment') :

                    $this->context->link->getModuleLink('paypalwithfee', 'payment', array(), true);

                $return_url = Configuration::get('PS_SSL_ENABLED') == 0 ?

                    $this->context->link->getModuleLink('paypalwithfee', 'validation') :

                    $this->context->link->getModuleLink('paypalwithfee', 'validation', array(), true);

                $cancel_url = Configuration::get('PS_SSL_ENABLED') == 0 ?

                    $link->getPageLink('order') : $link->getPageLink('order', true);



                $finalFee = $this->getFee($this->context->cart);



                $this->context->smarty->assign(array(

                    'limitpay' => $ppal_fee_limit,

                    'returnURL' => $return_url,

                    'cancelURL' => $cancel_url,

                    'path_controller' => $path_controller,

                    'module_path' => $this->_path,

                    'fee_real' => $this->getFee($this->context->cart),

                    'fee' => Tools::displayPrice($finalFee['fee_with_tax']),

                    'total_amount' => Tools::displayPrice($finalFee['total_order_no_fee'] + $finalFee['fee_with_tax']),

                    'client_id' => Configuration::get('PPAL_FEE_USER'),

                    'ppfw_currency' => Context::getContext()->currency->iso_code,

                ));



                $externalOption = new PaymentOption();
                $percentage = Configuration::get('PPAL_FEE_PERCENTAGE');

                $externalOption->setCallToActionText($finalFee['fee_with_tax'] > 0 
                
                ? $this->l('Pay with Paypal') . ' <span style="color:gray; margin-left:15px"> (+ ' . $percentage . '%)</span>'
                : $this->l('Pay with Paypal'))

                    ->setAction($path_controller)

                    ->setInputs([

                        'cancelURL' => [

                            'name' => 'cancelURL',

                            'type' => 'hidden',

                            'value' => $cancel_url,

                        ],

                        'returnURL' => [

                            'name' => 'returnURL',

                            'type' => 'hidden',

                            'value' => $return_url,

                        ],

                    ])

                    ->setAdditionalInformation($this->context->smarty->fetch(

                        'module:paypalwithfee/views/templates/front/payment_infos.tpl'

                    ))

                    ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/paypal_logo.jpg'))

                    ->setModuleName($this->name);



                $active = Configuration::get('PPAL_PAY_LATER');



                if ($active && ($finalFee['total_order_no_fee'] + $finalFee['fee_with_tax'] >= 30)) {

                    $externalOptionPaymentLater = new PaymentOption();
                    $percentage = Configuration::get('PPAL_FEE_PERCENTAGE');
                    $paymentString = $this->getTranslator()->trans(
                        'Pay later with Paypal',
                        [],
                        'Modules.Paypalwithfee'
                    );

                    $externalOptionPaymentLater->setCallToActionText($finalFee['fee_with_tax'] > 0 
                    
                    ? $paymentString . '  <span style="color:gray; margin-left:15px"> (+ ' . $percentage . '%)</span>'
                    : $this->l('Pay later with Paypal'))

                        ->setAction($path_controller)

                        ->setInputs([

                            'cancelURL' => [

                                'name' => 'cancelURL',

                                'type' => 'hidden',

                                'value' => $cancel_url,

                            ],

                            'returnURL' => [

                                'name' => 'returnURL',

                                'type' => 'hidden',

                                'value' => $return_url,

                            ], 'paylater' => [

                                'name' => 'paylater',

                                'type' => 'hidden',

                                'value' => 1,

                            ],

                        ])

                        ->setAdditionalInformation($this->context->smarty->fetch(

                            'module:paypalwithfee/views/templates/front/payment_infos_payment_later.tpl'

                        ))

                        ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/paypal_logo.jpg'))

                        ->setModuleName($this->name);



                    return [$externalOption, $externalOptionPaymentLater];

                } else {

                    return [$externalOption];

                }





            }

        }

    }



    public function hookDisplayProductAdditionalInfo($params)

    {

        $active = Configuration::get('PPAL_PAY_LATER');



        if ($active) {

            $fixedfee = Configuration::get('PPAL_FEE_FIXEDFEE');

            $percentage = Configuration::get('PPAL_FEE_PERCENTAGE');

            $totalWithPaypal = Product::getPriceStatic($params['product']->id, true, $params['product']->id_product_attribute);

            if ($percentage != 0) {

                $totalWithPaypal *= ($percentage / 100) + 1;

            }



            if ($fixedfee != 0) {

                $totalWithPaypal += $fixedfee;

            }

            $this->context->smarty->assign(

                array(

                    'client_id' => Configuration::get('PPAL_FEE_USER'),

                    'currency' => Context::getContext()->currency->iso_code,

                    'product_price' => $totalWithPaypal

                )

            );



            return $this->display(__FILE__, 'views/templates/hook/message_pp.tpl');

        }

    }



    public function hookActionValidateOrder($params)

    {

        if (version_compare(_PS_VERSION_, '1.7.8', '>=')) {

            $versionCompare = (Tools::getValue('controller') != 'validation' && !Tools::getIsset('module'));

        } else {

            $versionCompare = Tools::getValue('controller') == 'AdminOrders';

        }



        //manual order

        if (

            $versionCompare && ((Tools::getIsset('module') && Tools::getValue('module') == $this->name)

                || (Tools::getIsset('payment_module_name') && Tools::getValue('payment_module_name') == $this->name)

                || (Tools::getIsset('cart_summary') && Tools::getValue('cart_summary')['payment_module'] == $this->name))

        ) {

            $order = $params['order'];

            $fee = $this->getFee($params['cart']);



            $productList = $order->product_list;



            if ($fee['fee_with_tax'] > 0) {

                if (version_compare(_PS_VERSION_, '8', '>=')) {

                    foreach (OrderDetail::getList($order->id) as $orderD) {

                        (new OrderDetail($orderD['id_order_detail']))->delete();

                    }

                }

                // Fee as product

                $order_detail_fee = new OrderDetail();

                $order_detail_fee->id_order = (int)$order->id;

                $order_detail_fee->product_id = 999999999;

                $order_detail_fee->id_shop = $order->id_shop;

                $order_detail_fee->product_attribute_id = 0;

                $order_detail_fee->product_name = $this->l('PayPal Fee');

                $order_detail_fee->product_quantity = 1;

                $order_detail_fee->product_price = $fee['fee_without_tax'];

                $order_detail_fee->original_product_price = $fee['fee_with_tax'];



                $order_detail_fee->unit_price_tax_incl = $fee['fee_with_tax'];

                $order_detail_fee->unit_price_tax_excl = $fee['fee_without_tax'];



                $order_detail_fee->total_price_tax_incl = $fee['fee_with_tax'];

                $order_detail_fee->total_price_tax_excl = $fee['fee_without_tax'];

                $order_detail_fee->product_reference = $this->l('PPWF');

                $order_detail_fee->product_supplier_reference = 'PPWF';



                $order_detail_fee->id_tax_rules_group = Configuration::get('PPAL_TAX_FEE');

                $order_detail_fee->id_warehouse = 0;



                if ($order_detail_fee->save()) {

                    if ($order_detail_fee->id_tax_rules_group > 0) {

                        //id_tax of id_tax_rules_group

                        $vat_address = new Address($order->id_address_invoice);

                        $tax_manager_order_detail_fee = TaxManagerFactory::getManager(

                            $vat_address,

                            Configuration::get('PPAL_TAX_FEE'),

                            $this->context

                        );

                        $tax_calculator = $tax_manager_order_detail_fee->getTaxCalculator();



                        if (count($tax_calculator->taxes) > 0) {

                            $round_mode = Configuration::get('PS_PRICE_ROUND_MODE');

                            $round_type = Configuration::get('PS_ROUND_TYPE');



                            foreach ($tax_calculator->getTaxesAmount(

                                $order_detail_fee->unit_price_tax_excl

                            ) as $id_tax => $amount) {

                                switch ($round_type) {

                                    case 1:

                                        $total_amount = 1 * Tools::ps_round(

                                                $amount,

                                                _PS_PRICE_COMPUTE_PRECISION_,

                                                $round_mode

                                            );

                                        break;

                                    case 2:

                                        $total_amount = Tools::ps_round(

                                            1 * $amount,

                                            _PS_PRICE_COMPUTE_PRECISION_,

                                            $round_mode

                                        );

                                        break;

                                    case 3:

                                        //$total_tax_base = $quantity * $discounted_price_tax_excl;

                                        $total_amount = 1 * $amount;

                                        break;

                                }



                                $sql_order_detail_tax = 'INSERT INTO `' .

                                    _DB_PREFIX_ . 'order_detail_tax` (id_order_detail, id_tax, unit_amount, total_amount)

                                VALUES (' . (int)$order_detail_fee->id . ',' . (int)$id_tax . ',' .

                                    (float)$amount . ',' . (float)$total_amount . ')';



                                Db::getInstance()->execute($sql_order_detail_tax);

                            }

                        }

                    }



                } else {

                    //error saving fee

                }

                // end mod



                $order->total_shipping_tax_excl = $order->total_shipping_tax_excl;

                $order->total_shipping_tax_incl = $order->total_shipping_tax_incl;



                $order->total_paid_tax_incl = $order->total_paid_tax_incl + $fee['fee_with_tax'];

                $order->total_paid_tax_excl = $order->total_paid_tax_excl + $fee['fee_without_tax'];



                $order->update();



                if (version_compare(_PS_VERSION_, '8', '>=')) {

                    $order_detail = new OrderDetail(null, null, $this->context);

                    $order_detail->createList(

                        $order,

                        $params['cart'],

                        $params['orderStatus']->id,

                        $productList

                    );

                }

            }



            if ($fee['fee_with_tax'] > 0) {

                // OrderPayments

                $orderPayments = $order->getOrderPayments();



                foreach ($orderPayments as $op) {

                    if ($op->payment_method === 'Paypal') {

                        $op->amount = $fee['total_order_no_fee'] + $fee['fee_with_tax'];

                        $op->update();

                    }

                }

            }



            //create order paypal.

            $message = '';

            $id_cart = $order->id_cart;

            $id_order = $order->id;



            $amount = $fee['total_order_no_fee'] + $fee['fee_with_tax'];

            $id_tax = Configuration::get('PPAL_TAX_FEE');





            if ($id_tax) {

                $invoiceAddress = new Address($order->id_address_invoice);

                $tax_manager = TaxManagerFactory::getManager($invoiceAddress, $id_tax);

                $tax_calculator = $tax_manager->getTaxCalculator();

                $tax_rate = $tax_calculator->getTotalRate();

            } else {

                $tax_rate = $id_tax;

            }



            $fee_amount = $fee['fee_with_tax'];

            if (!empty($message)) {

                $transaction_idppwf = $message;

            } else {

                $transaction_idppwf = '-';

            }



            $paypal_order = new PaypalOrderx();

            $paypal_order->id_cart = $id_cart;

            $paypal_order->id_order = $id_order;

            $paypal_order->total_amount = $amount;

            $paypal_order->tax_rate = (float)$tax_rate;

            $paypal_order->fee = $fee_amount;

            $paypal_order->transaction_id = $transaction_idppwf;

            $paypal_order->id_shop = $this->context->cart->id_shop;



            $paypal_order->add();

        }

    }



    public function hookSendMailAlterTemplateVars($params)

    {

        $ppwf = false;





        if (!isset($params['template']) && !isset($params['{products}'])) {

            return;

        }



        //$params['template'] == 'payment' ||

        if ($params['template'] == 'order_conf') {

            if ($params['template_vars']['{order_name}']) {

                $reference = $params['template_vars']['{order_name}'];

                $orders = Order::getByReference($reference);



                foreach ($orders as $order) {

                    if ($order->module == $this->name) {

                        $ppwf = true;

                    }

                }

            }



            if ($ppwf) {

                $fee = $this->getFee($params['cart']);



                $products = array();

                $products[] = array(

                    'id_product' => '999999999',

                    'reference' => 'PPWF',

                    'name' => $this->l('PayPal Fee'),

                    'price' => Tools::displayPrice($fee['fee_with_tax'], $this->context->currency, false),

                    'quantity' => '1',

                    'customization' => array(),

                    'unit_price' => Tools::displayPrice($fee['fee_without_tax'], $this->context->currency, false),

                    'unit_price_full' => Tools::displayPrice($fee['fee_without_tax'], $this->context->currency, false),

                );



                $product_list_txt = $this->getEmailTemplateContent(

                    'order_conf_product_list.txt',

                    Mail::TYPE_TEXT,

                    $products

                );

                $product_list_html = $this->getEmailTemplateContent(

                    'order_conf_product_list.tpl',

                    Mail::TYPE_HTML,

                    $products

                );



                $params['template_vars']['{products}'] .= $product_list_html;

                $params['template_vars']['{products_txt}'] .= $product_list_txt;

            }

        }

    }



    public function hookDisplayOrderDetail($params)

    {

        $order = $params['order'];

        if ($order->module == 'paypalwithfee') {

            $fee_data = PaypalOrderx::getFeeData($order->id);



            if ($fee_data['fee'] > 0) {

                $this->context->smarty->assign(

                    'fee',

                    Tools::displayPrice($fee_data['fee'], $this->context->currency, false)

                );

                return $this->display(__FILE__, '/views/templates/hook/order_detail.tpl');

            }

        }

    }



    public function hookActionEmailAddAfterContent($params)

    {

        $tpl_name = (string)$params['template'];

        $context = new Context();

        $context = $context->getContext();

        if ($tpl_name == 'order_conf') {

            if ($id_order = Order::getOrderByCartId($context->cart->id)) {

                if ($fee = PaypalOrderx::getFeeDB($id_order)) {

                    $order = new Order($id_order);

                    $this->context->smarty->assign(array(

                        'fee' => Tools::displayPrice(

                            Tools::convertPrice(

                                $fee,

                                new Currency($order->id_currency)

                            ),

                            new Currency($order->id_currency)

                        )

                    ));

                    $params['template_html'] .= $this->display(__FILE__, 'views/templates/hook/email.tpl');

                }

            }

        }

    }



    /*

     * Returns the hookDisplayAdminOrderTabShip hook for prestashop 1.7.7 and newer

     */

    public function hookDisplayAdminOrderTabLink($params)

    {

        return $this->hookDisplayAdminOrderTabShip($params['id_order']);

    }



    /*

     * @Deprecated since 2020-06-22

     *

     * This hook isn't supported anymore by prestahsop 1.7.7 and newer

     */

    public function hookDisplayAdminOrderTabShip($idOrderFromParams = null)

    {

        $id_order = Tools::getValue('id_order');

        if (!is_numeric($id_order)) {

            $id_order = $idOrderFromParams;

        }

        $order = new Order($id_order);



        //Return different layouts based on the current prestashop version

        if ($order->module == $this->name) {

            if (version_compare(_PS_VERSION_, '1.7.6.99', '>=')) {

                return $this->display(__FILE__, 'views/templates/hook/admin_order_tab_ship_ps177.tpl');

            } else {

                return $this->display(__FILE__, 'views/templates/hook/admin_order_tab_ship.tpl');

            }

        }

    }



    /**

     * Returns the hookDisplayAdminOrderContentShip hook for prestahsop 1.7.7 and newer

     */

    public function hookDisplayAdminOrderTabContent($params)

    {

        return $this->hookDisplayAdminOrderContentShip($params['id_order']);

    }



    /*

     * @Deprecated since 2020-06-22

     *

     * This hook isn't supported anymore by prestahsop 1.7.7 and newer

     */

    public function hookDisplayAdminOrderContentShip($idOrderFromParams)

    {

        $id_order = Tools::getValue('id_order');

        if (!is_numeric($id_order)) {

            $id_order = $idOrderFromParams;

        }



        $order = new Order((int)$id_order);

        $hasFee = false;



        $address = new Address($order->id_address_delivery);

        if ($address->id_state == 0) {

            $not_state = true;

        } else {

            $not_state = false;

        }



        foreach (OrderDetail::getList($id_order) as $value) {

            if ($value['product_reference'] === 'PPWF') {

                $hasFee = true;

            }

        }



        if ($order->module == $this->name) {

            if (Tools::getValue('messageppwf')) {

                if (Tools::getValue('messageppwf') == 'ok') {

                    $this->context->smarty->assign('ppwfmessage_ok', $this->l('Refund complete'));

                } else {

                    switch (Tools::getValue('messageppwf')) {

                        case 'ppwf1':

                            $this->context->smarty->assign(

                                'ppwfmessage_error',

                                $this->l('Refund error - Transaction ID is empty.')

                            );

                            break;

                        case 'ppwf2':

                            $this->context->smarty->assign(

                                'ppwfmessage_error',

                                $this->l('Refund error - The amount not set correctly. (Example: 12.99)')

                            );

                            break;

                        default:

                            $this->context->smarty->assign(

                                'ppwfmessage_error',

                                $this->l('Refund error')

                            );

                            break;

                    }

                }

            }



            $generatepdfurl = null;



            if (!$hasFee) {

                $link = new Link();

                $params = array('id_order' => $id_order);

                $generatepdfurl = $link->getModuleLink($this->name, 'generatepdf', $params);

            }



            $fee = PaypalOrderx::getFeeData($order->id);

            //Try to decode the customer address

            try {

                if ($fee['customer_data'] != null) {

                    if (Tools::strlen($fee['customer_data'])) {

                        $fee['customer_data'] = json_decode($fee['customer_data']);

                    }

                }

            } catch (Exception $ex) {

                echo $ex->getMessage();

            }



            $fee['price_parsed'] = Tools::displayPrice($fee['fee'], $this->context->currency->id);



            $this->context->smarty->assign(array(

                'form_go_ppwf_generatepdf' => $generatepdfurl ? $generatepdfurl : null,

                'form_go_ppwf_refund' => 'index.php?tab=Refundppwf&id_order=' .

                    $id_order . '&ppwfr' . '&token=' . Tools::getAdminTokenLite('AdminOrders'),

                'invoice_number_' => $order->invoice_number,

                'invoices_collection_' => $order->getInvoicesCollection(),

                'id_currency' => $order->id_currency,

                'refund' => PaypalRefund::getRefundData($id_order),

                'max_refund' => PaypalRefund::getMaxRefundAmount($id_order),

                'not_state' => $not_state,

                'image_endpoint' => _PS_BASE_URL_ . __PS_BASE_URI__ . '/modules/paypalwithfee/views/img/',

                'seller_protection' => $this->l('This order transaction is covered by the Paypal seller protection'),

                'seller_protection_no' => $this->l('WARNING! This order transaction is NOT covered by the Paypal seller protection'),

                'fee' => $fee,

                'internal_seller_protection' => $this->checkIfSellerProtection($fee),

                'change_address_endpoint' => $this->context->link->getModuleLink($this->name, 'addressajax'),

                'ppwf_ajax_token' => Tools::getAdminToken($this->name)

            ));



            //Return different layouts based on the current prestashop version

            if ($order->module == $this->name) {

                if (version_compare(_PS_VERSION_, '1.7.8', '>=')) {

                    return $this->display(__FILE__, 'views/templates/hook/admin_order_content_ship_ps177.tpl');

                } else {

                    return $this->display(__FILE__, 'views/templates/hook/admin_order_content_ship.tpl');

                }

            }

        }

    }



    /**

     * This function will check if the seller protection is engaged.

     */

    protected function checkIfSellerProtection($fee)

    {

        //Now load the order

        $order = new Order((int)$fee['id_order']);



        //Decode the customer data

        $cData = $fee['customer_data'];



        //Try to match the address parts into states and countries id's from prestashop

        $parts = $this->matchParts(

            $cData->address->admin_area_1,

            $cData->address->admin_area_2,

            $cData->address->country_code

        );

        $orderAddress = new Address((int)$order->id_address_delivery);

        $orderAddress->stateObj = ($orderAddress->id_state != 0 ? new State((int)$orderAddress->id_state) : null);

        $orderAddress->countryObj = new Country((int)$orderAddress->id_country);

        //Now compare the addresses.



        //Try to match

        $cityTwoCheck = Tools::strtoupper($orderAddress->city) == Tools::strtoupper($cData->address->admin_area_1);

        $cityOneCheck = Tools::strtoupper($orderAddress->id_state) == Tools::strtoupper(State::getIdByName($cData->address->admin_area_2));

        $countryCheck = $parts['countryCode'] == $orderAddress->id_country;

        $postCodeCheck = $cData->address->postal_code == $orderAddress->postcode;

        $addressCheck = Tools::strtoupper($orderAddress->address1) ==

            Tools::strtoupper($cData->address->address_line_1);

        $address2Check = Tools::strtoupper($orderAddress->address2) ==

            Tools::strtoupper($cData->address->address_line_2);



        return [

            'allOk' => (

                $cityOneCheck &&

                $cityTwoCheck &&

                $countryCheck &&

                $postCodeCheck &&

                $addressCheck &&

                $address2Check

            ),

            'cityOneCheck' => $cityOneCheck,

            'cityTwoCheck' => $cityTwoCheck,

            'countryCheck' => $countryCheck,

            'postCodeCheck' => $postCodeCheck,

            'addressCheck' => $addressCheck,

            'address2Check' => $address2Check,

            'paypalAddress' => $cData->address,

            'orderAddress' => $orderAddress,

        ];

    }





    /**

     * Tries to match the city names and the countries code

     */

    public function matchParts($cityOne, $cityTwo, $countryCode)

    {

        $parts = [

            'cityOne' => $cityOne,

            'cityTwo' => 0,

            'countryCode' => 0

        ];



        //First get the states and iterate them

        $states = Db::getInstance()

            ->ExecuteS(

                '

                SELECT * 

                FROM `' . _DB_PREFIX_ . 'state`

                '

            );

        foreach ($states as $state) {

            if (Tools::strtoupper($state['name']) == Tools::strtoupper($cityTwo)) {

                $parts['cityTwo'] = $state['id_state'];

            }

        }



        //Now, the same with countries.

        $countries = Db::getInstance()

            ->ExecuteS(

                '

                SELECT * 

                FROM `' . _DB_PREFIX_ . 'country`

                '

            );

        foreach ($countries as $country) {

            if (Tools::strtoupper($country['iso_code']) == Tools::strtoupper($countryCode)) {

                $parts['countryCode'] = $country['id_country'];

            }

        }



        return $parts;

    }



    public function hookDisplayOrderConfirmation($params)

    {

        $this->context->controller->addJS(_PS_MODULE_DIR_ . $this->name . '/views/js/paypalwithfeePdfConfirmation.js');

    }



    public function hookDisplayOrderConfirmation1()

    {

        $id_order = Tools::getValue('id_order');

        $order = new order($id_order);

        if ($order->module == $this->name) {

            $this->context->smarty->assign(array(

                'fee' => Tools::displayPrice(

                    Tools::convertPrice(

                        PaypalOrderx::getFeeDB($order->id),

                        new Currency($order->id_currency)

                    ),

                    new Currency($order->id_currency)

                ),

                'id_order' => $id_order,

            ));



            return $this->display(__FILE__, 'views/templates/hook/confirmation.tpl');

        }

    }



    public function hookHeader()

    {

        if ($this->context->controller->php_self == 'order') {

            $this->context->controller->addJS(_PS_MODULE_DIR_ . $this->name . '/views/js/paypalwithfeeFront.js');

        }



        if ($this->context->controller->php_self == 'history' ||

            $this->context->controller->php_self == 'order-detail' ||

            $this->context->controller->php_self == 'order-confirmation') {

            if (Configuration::get('PS_INVOICE')) {

                Media::addJsDef(array(

                    'ppwf_ajax_url' => $this->context->link->getModuleLink($this->name, 'generatepdf')

                ));

                $this->context->controller->addJS(

                    _PS_MODULE_DIR_ . $this->name . '/views/js/paypalwithfeePdfHistory.js'

                );

            }

        }

        if ($this->context->controller->php_self == 'product' || $this->context->controller->php_self == 'order') {

            $this->context->smarty->assign(

                array(

                    'client_id' => Configuration::get('PPAL_FEE_USER'),

                    'ppfw_currency' => Context::getContext()->currency->iso_code,

                )

            );

            return $this->display(__FILE__, 'views/templates/hook/script.tpl');

        }

    }



    public function hookDisplayHeader()

    {

        if ($this->context->controller->php_self == 'order') {

            $this->context->controller->addJS(_PS_MODULE_DIR_ . $this->name . '/views/js/paypalwithfeeFront.js');

        }



        if ($this->context->controller->php_self == 'history' ||

            $this->context->controller->php_self == 'order-detail' ||

            $this->context->controller->php_self == 'order-confirmation') {

            if (Configuration::get('PS_INVOICE')) {

                Media::addJsDef(array(

                    'ppwf_ajax_url' => $this->context->link->getModuleLink($this->name, 'generatepdf')

                ));

                $this->context->controller->addJS(

                    _PS_MODULE_DIR_ . $this->name . '/views/js/paypalwithfeePdfHistory.js'

                );

            }

        }

        if ($this->context->controller->php_self == 'product' || $this->context->controller->php_self == 'order') {

            $this->context->smarty->assign(

                array(

                    'client_id' => Configuration::get('PPAL_FEE_USER'),

                    'ppfw_currency' => Context::getContext()->currency->iso_code,

                )

            );

            return $this->display(__FILE__, 'views/templates/hook/script.tpl');

        }

    }



    /**

     * Add the CSS & JavaScript files you want to be loaded in the BO.

     */

    public function hookBackOfficeHeader()

    {

        if (Tools::getValue('configure') == $this->name || Tools::getValue('controller') == 'AdminProducts') {

            $this->context->controller->addJquery();

            $this->context->controller->addCSS($this->_path . 'views/css/4webs_back.css');

            $this->context->controller->addCSS($this->_path . 'views/css/fw_paypalwithfee.css');

        }

    }



    public function hookDisplayBackOfficeHeader()

    {

        if (Tools::getValue('configure') == $this->name || Tools::getValue('controller') == 'AdminProducts') {

            $this->context->controller->addJquery();

            $this->context->controller->addCSS($this->_path . 'views/css/4webs_back.css');

            $this->context->controller->addCSS($this->_path . 'views/css/fw_paypalwithfee.css');

        }

    }



    protected function getCategoriesByProduct($id_product)

    {

        $sql = 'SELECT `id_category` FROM `' . _DB_PREFIX_ .

            'category_product` WHERE `id_product`=' . (int)$id_product;

        return Db::getInstance()->executeS($sql);

    }



    public function getFee($cart)

    {

        $fee_content = array();



        $currency = new Currency((int)$cart->id_currency);

        $currency_decimals = is_array($currency) ? (int)$currency['decimals'] : (int)$currency->decimals;

        $decimals = $currency_decimals * _PS_PRICE_DISPLAY_PRECISION_;



        $fixedfee = Configuration::get('PPAL_FEE_FIXEDFEE');

        $percentage = Configuration::get('PPAL_FEE_PERCENTAGE');



        $minFee = Configuration::get('PPAL_FEE_MIN');

        $maxFee = Configuration::get('PPAL_FEE_MAX');



        $totalttc = (float)($cart->getOrderTotal(true, Cart::BOTH));

        if (!empty($totalttc)) {

            // total with tax

            $fee_with_tax = Tools::ps_round((($percentage / 100) * $totalttc) + $fixedfee, $decimals);

        } else {

            $fee_with_tax = 0;

        }



        if ($maxFee && $fee_with_tax > $maxFee) {

            $fee_with_tax = $maxFee;

        } elseif ($minFee && $fee_with_tax < $minFee) {

            $fee_with_tax = $minFee;

        }



        $id_tax = Configuration::get('PPAL_TAX_FEE');



        if ($id_tax && Configuration::get('PPAL_TAX_FEE')) {

            //search tax rule group

            if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery') {

                $id_address = $cart->id_address_delivery;

            } else {

                $id_address = $cart->id_address_invoice;

            }



            $address = new Address($id_address);

            $tax_manager = TaxManagerFactory::getManager($address, $id_tax);

            $tax_calculator = $tax_manager->getTaxCalculator();

            $tax_rate = $tax_calculator->getTotalRate();



            if ($tax_rate > 0) {

                if (Configuration::get('PPAL_TAX_INCREASE')) {

                    $fee_without_tax = $fee_with_tax;

                    $fee_with_tax = Tools::ps_round($fee_with_tax * (1 + (0.01 * $tax_rate)), $decimals);

                } else {

                    $fee_without_tax = Tools::ps_round($fee_with_tax / (1 + (0.01 * $tax_rate)), $decimals);

                }

            } else {

                $fee_without_tax = $fee_with_tax;

            }

        } else {

            $fee_without_tax = $fee_with_tax;

        }



        $fee_content['fee_with_tax'] = $fee_with_tax;

        $fee_content['fee_without_tax'] = $fee_without_tax;

        $fee_content['total_order_no_fee'] = $totalttc;



        return $fee_content;

    }



    public function validateOrder4webs(

        $id_cart,

        $id_order_state,

        $amount_paid,

        $payment_method = 'Unknown',

        $message = null,

        $payerID = null,

        $sellerProtection = false,

        $extra_vars = array(),

        $currency_special = null,

        $dont_touch_amount = false,

        $secure_key = false,

        Shop $shop = null,

        $paypalAddress = null

    )

    {



        if (self::DEBUG_MODE) {

            PrestaShopLogger::addLog(

                'PaymentModule::validateOrder - Function called',

                1,

                null,

                'Cart',

                (int)$id_cart,

                true

            );

        }



        if (!isset($this->context)) {

            $this->context = Context::getContext();

        }

        $this->context->cart = new Cart((int)$id_cart);

        $completeFee = $this->getFee($this->context->cart);



        $this->context->customer = new Customer((int)$this->context->cart->id_customer);

        // The tax cart is loaded before the customer so re-cache the tax calculation method

        $this->context->cart->setTaxCalculationMethod();



        $this->context->language = new Language((int)$this->context->cart->id_lang);

        $this->context->shop = ($shop ? $shop : new Shop((int)$this->context->cart->id_shop));

        ShopUrl::resetMainDomainCache();

        $id_currency = $currency_special ? (int)$currency_special : (int)$this->context->cart->id_currency;

        $this->context->currency = new Currency((int)$id_currency, null, (int)$this->context->shop->id);

        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery') {

            $context_country = $this->context->country;

        }



        $order_status = new OrderState((int)$id_order_state, (int)$this->context->language->id);

        if (!Validate::isLoadedObject($order_status)) {

            PrestaShopLogger::addLog(

                'PaymentModule::validateOrder - Order Status cannot be loaded',

                3,

                null,

                'Cart',

                (int)$id_cart,

                true

            );

            throw new PrestaShopException('Can\'t load Order status');

        }



        if (!$this->active) {

            PrestaShopLogger::addLog(

                'PaymentModule::validateOrder - Module is not active',

                3,

                null,

                'Cart',

                (int)$id_cart,

                true

            );

            die(Tools::displayError());

        }



        // Does order already exists ?

        if (Validate::isLoadedObject($this->context->cart) && $this->context->cart->OrderExists() == false) {

            if ($secure_key !== false && $secure_key != $this->context->cart->secure_key) {

                PrestaShopLogger::addLog(

                    'PaymentModule::validateOrder - Secure key does not match',

                    3,

                    null,

                    'Cart',

                    (int)$id_cart,

                    true

                );

                die(Tools::displayError());

            }



            // For each package, generate an order

            $delivery_option_list = $this->context->cart->getDeliveryOptionList(null, true);

            $package_list = $this->context->cart->getPackageList(true);

            $cart_delivery_option = $this->context->cart->getDeliveryOption(null, true);



            // If some delivery options are not defined, or not valid, use the first valid option

            foreach ($delivery_option_list as $id_address => $package) {

                if (!isset($cart_delivery_option[$id_address]) ||

                    !array_key_exists($cart_delivery_option[$id_address], $package)) {

                    foreach ($package as $key => $val) {

                        unset($val);

                        $cart_delivery_option[$id_address] = $key;

                        break;

                    }

                }

            }



            $order_list = array();

            $order_detail_list = array();



            do {

                $reference = Order::generateReference();

            } while (Order::getByReference($reference)->count());



            $this->currentOrderReference = $reference;



            $cart_total_paid = (float)Tools::ps_round((float)$this->context->cart->getOrderTotal(true, Cart::BOTH), 2);

            if (!empty($completeFee['fee_with_tax'])) {

                $cart_total_paid += $completeFee['fee_with_tax'];

            }



            foreach ($cart_delivery_option as $id_address => $key_carriers) {

                foreach ($delivery_option_list[$id_address][$key_carriers]['carrier_list'] as $id_carrier => $data) {

                    foreach ($data['package_list'] as $id_package) {

                        // Rewrite the id_warehouse

                        $package_list[$id_address][$id_package]['id_warehouse'] = (int)

                        $this->context->cart->getPackageIdWarehouse(

                            $package_list[$id_address][$id_package],

                            (int)$id_carrier

                        );

                        $package_list[$id_address][$id_package]['id_carrier'] = $id_carrier;

                    }

                }

            }

            // Make sure CartRule caches are empty

            CartRule::cleanCache();

            $cart_rules = $this->context->cart->getCartRules();

            foreach ($cart_rules as $cart_rule) {

                if (($rule = new CartRule((int)$cart_rule['obj']->id)) && Validate::isLoadedObject($rule)) {

                    if ($error = $rule->checkValidity($this->context, true, true)) {

                        $this->context->cart->removeCartRule((int)$rule->id);

                        if (isset($this->context->cookie) &&

                            isset($this->context->cookie->id_customer) &&

                            $this->context->cookie->id_customer && !empty($rule->code)) {

                            Tools::redirect(

                                'index.php?controller=order&submitAddDiscount=1&discount_name=' .

                                urlencode($rule->code)

                            );

                        } else {

                            $rule_name = isset($rule->name[(int)$this->context->cart->id_lang]) ?

                                $rule->name[(int)$this->context->cart->id_lang] : $rule->code;

                            $error = sprintf(

                                Tools::displayError(

                                    'CartRule ID %1s (%2s) used in this cart is not valid and has been withdrawn'

                                ),

                                (int)$rule->id,

                                $rule_name

                            );

                            PrestaShopLogger::addLog($error, 3, '0000002', 'Cart', (int)$this->context->cart->id);

                        }

                    }

                }

            }



            $multiple_carrier = 0;

            foreach ($package_list as $id_address => $packageByAddress) {

                foreach ($packageByAddress as $id_package => $package) {

                    /** @var Order $order */

                    //if has multiple carrier for different products // assign fee to one order.

                    if ($multiple_carrier > 0) {

                        $completeFee = 0;

                    }



                    $order = new Order();

                    $order->product_list = $package['product_list'];



                    if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery') {

                        $address = new Address((int)$id_address);

                        $this->context->country = new Country(

                            (int)$address->id_country,

                            (int)$this->context->cart->id_lang

                        );

                        if (!$this->context->country->active) {

                            throw new PrestaShopException('The delivery address country is not active.');

                        }

                    }



                    $carrier = null;

                    if (!$this->context->cart->isVirtualCart() && isset($package['id_carrier'])) {

                        $carrier = new Carrier((int)$package['id_carrier'], (int)$this->context->cart->id_lang);

                        $order->id_carrier = (int)$carrier->id;

                        $id_carrier = (int)$carrier->id;

                    } else {

                        $order->id_carrier = 0;

                        $id_carrier = 0;

                    }



                    $order->id_customer = (int)$this->context->cart->id_customer;

                    $order->id_address_invoice = (int)$this->context->cart->id_address_invoice;

                    $order->id_address_delivery = (int)$id_address;

                    $order->id_currency = $this->context->currency->id;

                    $order->id_lang = (int)$this->context->cart->id_lang;

                    $order->id_cart = (int)$this->context->cart->id;

                    $order->reference = $reference;

                    $order->id_shop = (int)$this->context->shop->id;

                    $order->id_shop_group = (int)$this->context->shop->id_shop_group;



                    $order->secure_key = ($secure_key ? pSQL($secure_key) : pSQL($this->context->customer->secure_key));

                    $order->payment = $payment_method;

                    if (isset($this->name)) {

                        $order->module = $this->name;

                    }

                    $order->recyclable = $this->context->cart->recyclable;

                    $order->gift = (int)$this->context->cart->gift;

                    $order->gift_message = $this->context->cart->gift_message;

                    $order->mobile_theme = $this->context->cart->mobile_theme;

                    $order->conversion_rate = $this->context->currency->conversion_rate;

                    $amount_paid = !$dont_touch_amount ? Tools::ps_round((float)$amount_paid, 2) : $amount_paid;



                    // fee

                    // total_paid_real only add fee, saves two sum

                    $order->total_paid_real = (float)(Tools::ps_round((float)$completeFee['fee_with_tax'], 2));



                    $order->total_products = (float)$this->context->cart->getOrderTotal(

                            false,

                            Cart::ONLY_PRODUCTS,

                            $order->product_list,

                            $id_carrier

                        ) + $completeFee['fee_without_tax'];

                    $order->total_products_wt = (float)$this->context->cart->getOrderTotal(

                            true,

                            Cart::ONLY_PRODUCTS,

                            $order->product_list,

                            $id_carrier

                        ) + $completeFee['fee_with_tax'];

                    $order->total_discounts_tax_excl = (float)abs($this->context->cart->getOrderTotal(

                        false,

                        Cart::ONLY_DISCOUNTS,

                        $order->product_list,

                        $id_carrier

                    ));

                    $order->total_discounts_tax_incl = (float)abs($this->context->cart->getOrderTotal(

                        true,

                        Cart::ONLY_DISCOUNTS,

                        $order->product_list,

                        $id_carrier

                    ));

                    $order->total_discounts = $order->total_discounts_tax_incl;





                    // fee

                    $order->total_shipping_tax_excl = (float)$this->context->cart->getPackageShippingCost(

                        (int)$id_carrier,

                        false,

                        null,

                        $order->product_list

                    );

                    // fee

                    $order->total_shipping_tax_incl = (float)$this->context->cart->getPackageShippingCost(

                        (int)$id_carrier,

                        true,

                        null,

                        $order->product_list

                    );

                    $order->total_shipping = $order->total_shipping_tax_incl;



                    if (!is_null($carrier) && Validate::isLoadedObject($carrier)) {

                        $order->carrier_tax_rate = $carrier->getTaxesRate(

                            new Address((int)$this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')})

                        );

                    }



                    $order->total_wrapping_tax_excl = (float)abs($this->context->cart->getOrderTotal(

                        false,

                        Cart::ONLY_WRAPPING,

                        $order->product_list,

                        $id_carrier

                    ));

                    $order->total_wrapping_tax_incl = (float)abs($this->context->cart->getOrderTotal(

                        true,

                        Cart::ONLY_WRAPPING,

                        $order->product_list,

                        $id_carrier

                    ));

                    $order->total_wrapping = $order->total_wrapping_tax_incl;



                    $order->total_paid_tax_excl = (float)Tools::ps_round(

                            (float)$this->context->cart->getOrderTotal(

                                false,

                                Cart::BOTH,

                                $order->product_list,

                                $id_carrier

                            ),

                            _PS_PRICE_COMPUTE_PRECISION_

                        ) + $completeFee['fee_without_tax'];



                    // fee

                    $order->total_paid_tax_incl = (float)Tools::ps_round(

                            (float)$this->context->cart->getOrderTotal(

                                true,

                                Cart::BOTH,

                                $order->product_list,

                                $id_carrier

                            ),

                            _PS_PRICE_COMPUTE_PRECISION_

                        ) + $completeFee['fee_with_tax'];

                    $order->total_paid = $order->total_paid_tax_incl;

                    $order->round_mode = Configuration::get('PS_PRICE_ROUND_MODE');

                    $order->round_type = Configuration::get('PS_ROUND_TYPE');



                    $order->invoice_date = '0000-00-00 00:00:00';

                    $order->delivery_date = '0000-00-00 00:00:00';



                    if (self::DEBUG_MODE) {

                        PrestaShopLogger::addLog(

                            'PaymentModule::validateOrder - Order is about to be added',

                            1,

                            null,

                            'Cart',

                            (int)$id_cart,

                            true

                        );

                    }



                    // Creating order

                    $result = $order->add();



                    if (!$result) {

                        PrestaShopLogger::addLog(

                            'PaymentModule::validateOrder - Order cannot be created',

                            3,

                            null,

                            'Cart',

                            (int)$id_cart,

                            true

                        );

                        throw new PrestaShopException('Can\'t save Order');

                    }



                    // Amount paid by customer is not the right one -> Status = payment error

                    // if ($order->total_paid != $order->total_paid_real)

                    // We use number_format in order to compare two string

                    if ($order_status->logable && number_format($cart_total_paid, _PS_PRICE_COMPUTE_PRECISION_)

                        != number_format($amount_paid, _PS_PRICE_COMPUTE_PRECISION_)) {

                        // Calculate the difference between the two values

                        $difference = abs(number_format($cart_total_paid, _PS_PRICE_COMPUTEPRECISION) - number_format($amount_paid, _PS_PRICE_COMPUTEPRECISION));



                        // If the difference is greater than 0.02, it is a payment error

                        if ($difference > 0.02) {

                            $id_order_state = Configuration::get('PS_OS_ERROR');

                        }

                    }



                    $order_list[] = $order;



                    if (self::DEBUG_MODE) {

                        PrestaShopLogger::addLog(

                            'PaymentModule::validateOrder - OrderDetail is about to be added',

                            1,

                            null,

                            'Cart',

                            (int)$id_cart,

                            true

                        );

                    }



                    // Insert new Order detail list using cart for the current order

                    if (version_compare(_PS_VERSION_, '8', '<')) {

                        $order_detail = new OrderDetail(null, null, $this->context);

                        $order_detail->createList(

                            $order,

                            $this->context->cart,

                            $id_order_state,

                            $order->product_list,

                            0,

                            true,

                            $package_list[$id_address][$id_package]['id_warehouse']

                        );

                        $order_detail_list[] = $order_detail;

                    }



                    if ($completeFee['fee_with_tax'] > 0) {

                        // Fee as product

                        $order_detail_fee = new OrderDetail();

                        $order_detail_fee->id_order = (int)$order->id;

                        $order_detail_fee->product_id = 999999999;

                        $order_detail_fee->id_shop = $order->id_shop;

                        $order_detail_fee->product_attribute_id = 0;

                        $order_detail_fee->product_name = chr(124) . $this->l('PayPal Fee');

                        $order_detail_fee->product_quantity = 1;

                        $order_detail_fee->product_price = $completeFee['fee_without_tax'];

                        $order_detail_fee->original_product_price = $completeFee['fee_with_tax'];



                        $order_detail_fee->unit_price_tax_incl = $completeFee['fee_with_tax'];

                        $order_detail_fee->unit_price_tax_excl = $completeFee['fee_without_tax'];



                        $order_detail_fee->total_price_tax_incl = $completeFee['fee_with_tax'];

                        $order_detail_fee->total_price_tax_excl = $completeFee['fee_without_tax'];

                        $order_detail_fee->product_reference = $this->l('PPWF');

                        $order_detail_fee->product_supplier_reference = 'PPWF';



                        $order_detail_fee->id_tax_rules_group = Configuration::get('PPAL_TAX_FEE');

                        $order_detail_fee->id_warehouse = 0;



                        if ($order_detail_fee->save()) {

                            if ($order_detail_fee->id_tax_rules_group > 0) {

                                //id_tax of id_tax_rules_group

                                $vat_address = new Address($order->id_address_invoice);

                                $tax_manager_order_detail_fee = TaxManagerFactory::getManager(

                                    $vat_address,

                                    Configuration::get('PPAL_TAX_FEE'),

                                    $this->context

                                );

                                $tax_calculator = $tax_manager_order_detail_fee->getTaxCalculator();



                                if (count($tax_calculator->taxes) > 0) {

                                    $round_mode = Configuration::get('PS_PRICE_ROUND_MODE');

                                    $round_type = Configuration::get('PS_ROUND_TYPE');



                                    foreach ($tax_calculator->getTaxesAmount(

                                        $order_detail_fee->unit_price_tax_excl

                                    ) as $id_tax => $amount) {

                                        switch ($round_type) {

                                            case 1:

                                                //$total_tax_base = $quantity * Tools::ps_round($discounted_price_tax_excl, _PS_PRICE_COMPUTE_PRECISION_, $this->round_mode);

                                                $total_amount = 1 * Tools::ps_round(

                                                        $amount,

                                                        _PS_PRICE_COMPUTE_PRECISION_,

                                                        $round_mode

                                                    );

                                                break;

                                            case 2:

                                                //$total_tax_base = Tools::ps_round($quantity * $discounted_price_tax_excl, _PS_PRICE_COMPUTE_PRECISION_, $this->round_mode);

                                                $total_amount = Tools::ps_round(

                                                    1 * $amount,

                                                    _PS_PRICE_COMPUTE_PRECISION_,

                                                    $round_mode

                                                );

                                                break;

                                            case 3:

                                                //$total_tax_base = $quantity * $discounted_price_tax_excl;

                                                $total_amount = 1 * $amount;

                                                break;

                                        }



                                        $sql_order_detail_tax = 'INSERT INTO `' .

                                            _DB_PREFIX_ . 'order_detail_tax` (id_order_detail, id_tax, unit_amount, total_amount)

                                    VALUES (' . (int)$order_detail_fee->id . ',' . (int)$id_tax . ',' . (float)$amount .

                                            ',' . (float)$total_amount . ')';



                                        Db::getInstance()->execute($sql_order_detail_tax);

                                    }

                                }

                            }

                        } else {

                            //error saving fee

                        }

                    }



                    if (version_compare(_PS_VERSION_, '8', '>=')) {

                        $order_detail = new OrderDetail(null, null, $this->context);

                        $order_detail->createList(

                            $order,

                            $this->context->cart,

                            $id_order_state,

                            $order->product_list,

                            0,

                            true,

                            $package_list[$id_address][$id_package]['id_warehouse']

                        );

                        $order_detail_list[] = $order_detail;

                    }





                    //end mod



                    if (self::DEBUG_MODE) {

                        PrestaShopLogger::addLog(

                            'PaymentModule::validateOrder - OrderCarrier is about to be added',

                            1,

                            null,

                            'Cart',

                            (int)$id_cart,

                            true

                        );

                    }



                    // Adding an entry in order_carrier table

                    if (!is_null($carrier)) {

                        $order_carrier = new OrderCarrier();

                        $order_carrier->id_order = (int)$order->id;

                        $order_carrier->id_carrier = (int)$id_carrier;

                        $order_carrier->weight = (float)$order->getTotalWeight();

                        $order_carrier->shipping_cost_tax_excl = (float)$order->total_shipping_tax_excl;

                        $order_carrier->shipping_cost_tax_incl = (float)$order->total_shipping_tax_incl;

                        $order_carrier->add();

                    }

                    $multiple_carrier += 1;

                    //if has multiple carrier for different products // assign fee to one order.

                }

            }



            // The country can only change if the address used for the calculation is the delivery address, and if multi-shipping is activated

            if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery') {

                $this->context->country = $context_country;

            }



            if (!$this->context->country->active) {

                PrestaShopLogger::addLog(

                    'PaymentModule::validateOrder - Country is not active',

                    3,

                    null,

                    'Cart',

                    (int)$id_cart,

                    true

                );

                throw new PrestaShopException('The order address country is not active.');

            }



            if (self::DEBUG_MODE) {

                PrestaShopLogger::addLog(

                    'PaymentModule::validateOrder - Payment is about to be added',

                    1,

                    null,

                    'Cart',

                    (int)$id_cart,

                    true

                );

            }



            // Register Payment only if the order status validate the order

            if ($order_status->logable) {

                // $order is the last order loop in the foreach

                // The method addOrderPayment of the class Order make a create a paymentOrder

                // linked to the order reference and not to the order id

                if (isset($message)) {

                    $transaction_id = $message;

                } else {

                    $transaction_id = null;

                }



                if (!$order->addOrderPayment($amount_paid, null, $transaction_id)) {

                    PrestaShopLogger::addLog(

                        'PaymentModule::validateOrder - Cannot save Order Payment',

                        3,

                        null,

                        'Cart',

                        (int)$id_cart,

                        true

                    );

                    throw new PrestaShopException('Can\'t save Order Payment');

                }

            }



            //create order paypal whatever status.

            $id_cart = $order->id_cart;

            $id_order = $order->id;

            $amount = $order->total_paid;

            $id_tax = Configuration::get('PPAL_TAX_FEE');

            if ($id_tax != 0) {

                $invoiceAddress = new Address($order->id_address_invoice);

                $tax_manager = TaxManagerFactory::getManager($invoiceAddress, $id_tax);

                $tax_calculator = $tax_manager->getTaxCalculator();

                $tax_rate = $tax_calculator->getTotalRate();

            } else {

                $tax_rate = $id_tax;

            }

            $fee_amount = $completeFee['fee_with_tax'];

            if (!empty($message)) {

                $transaction_idppwf = $message;

            } else {

                $transaction_idppwf = '-';

            }



            $city = $paypalAddress->address->admin_area_1;

            $state = $paypalAddress->address->admin_area_2;

            $paypalAddress->address->admin_area_1 = $state;

            $paypalAddress->address->admin_area_2 = $city;



            $paypal_order = new PaypalOrderx();

            $paypal_order->id_cart = $id_cart;

            $paypal_order->id_order = $id_order;

            $paypal_order->total_amount = $amount;

            $paypal_order->tax_rate = (float)$tax_rate;

            $paypal_order->fee = $fee_amount;

            $paypal_order->transaction_id = $transaction_idppwf;

            $paypal_order->payer_id = $payerID;

            $paypal_order->seller_protection = ($sellerProtection ? 1 : 0);

            $paypal_order->id_shop = $this->context->cart->id_shop;

            $paypal_order->customer_data = json_encode($paypalAddress);



            $paypal_order->add();

            //end paypal order

            // Next !

            $cart_rule_used = array();



            // Make sure CartRule caches are empty

            CartRule::cleanCache();

            foreach ($order_detail_list as $key => $order_detail) {

                /** @var OrderDetail $order_detail */

                $order = $order_list[$key];

                if (isset($order->id)) {

                    if (!$secure_key) {

                        $message .= '' . Tools::displayError(

                                'Warning: the secure key is empty, check your payment account before validation'

                            );

                    }

                    // Optional message to attach to this order

                    if (isset($message) & !empty($message)) {

                        $msg = new Message();

                        $message = strip_tags($message, '<br>');

                        if (Validate::isCleanHtml($message)) {

                            if (self::DEBUG_MODE) {

                                PrestaShopLogger::addLog(

                                    'PaymentModule::validateOrder - Message is about to be added',

                                    1,

                                    null,

                                    'Cart',

                                    (int)$id_cart,

                                    true

                                );

                            }

                            $msg->message = 'Paypal Transaction ID:' . $message;;

                            $msg->id_cart = (int)$id_cart;

                            $msg->id_customer = (int)($order->id_customer);

                            $msg->id_order = (int)$order->id;

                            $msg->private = 1;

                            $msg->add();

                        }

                    }



                    // Insert new Order detail list using cart for the current order

                    //$orderDetail = new OrderDetail(null, null, $this->context);

                    //$orderDetail->createList($order, $this->context->cart, $id_order_state);

                    // Construct order detail table for the email

                    $virtual_product = true;



                    $psTaxAddressType = (int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')};

                    $specific_price_output = null;



                    $product_var_tpl_list = array();

                    foreach ($order->product_list as $product) {

                        $price = Product::getPriceStatic(

                            (int)$product['id_product'],

                            false,

                            ($product['id_product_attribute'] ?

                                (int)$product['id_product_attribute'] : null),

                            6,

                            null,

                            false,

                            true,

                            $product['cart_quantity'],

                            false,

                            (int)$order->id_customer,

                            (int)$order->id_cart,

                            $psTaxAddressType,

                            $specific_price_output,

                            true,

                            true,

                            null,

                            true,

                            $product['id_customization']

                        );

                        $price_wt = Product::getPriceStatic(

                            (int)$product['id_product'],

                            true,

                            ($product['id_product_attribute'] ? (int)$product['id_product_attribute'] : null),

                            2,

                            null,

                            false,

                            true,

                            $product['cart_quantity'],

                            false,

                            (int)$order->id_customer,

                            (int)$order->id_cart,

                            $psTaxAddressType,

                            $specific_price_output,

                            true,

                            true,

                            null,

                            true,

                            $product['id_customization']

                        );



                        $product_price = Product::getTaxCalculationMethod() == PS_TAX_EXC ?

                            Tools::ps_round($price, 2) : $price_wt;



                        $product_var_tpl = array(

                            'id_product' => $product['id_product'],

                            'reference' => $product['reference'],

                            'name' => $product['name'] . (isset($product['attributes']) ?

                                    ' - ' . $product['attributes'] : ''),

                            'price' => Tools::displayPrice(

                                $product_price * $product['quantity'],

                                $this->context->currency,

                                false

                            ),

                            'quantity' => $product['quantity'],

                            'customization' => array()

                        );



                        if (isset($product['price']) && $product['price']) {

                            $product_var_tpl['unit_price'] = Tools::displayPrice(

                                $product['price'],

                                $this->context->currency,

                                false

                            );

                            $product_var_tpl['unit_price_full'] = Tools::displayPrice(

                                    $product['price'],

                                    $this->context->currency,

                                    false

                                )

                                . ' ' . $product['unity'];

                        } else {

                            $product_var_tpl['unit_price'] = $product_var_tpl['unit_price_full'] = '';

                        }



                        $customized_datas = Product::getAllCustomizedDatas(

                            (int)$order->id_cart,

                            null,

                            true,

                            null,

                            (int)$product['id_customization']

                        );

                        if (isset($customized_datas[$product['id_product']][$product['id_product_attribute']])) {

                            $product_var_tpl['customization'] = array();

                            foreach ($customized_datas[$product['id_product']][$product['id_product_attribute']][$order->id_address_delivery] as $customization) {

                                $customization_text = '';

                                if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD])) {

                                    foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text) {

                                        $customization_text .= $text['name'] .

                                            $text['value'] . '';

                                    }

                                }



                                if (isset($customization['datas'][Product::CUSTOMIZE_FILE])) {

                                    $customization_text .= sprintf(

                                            Tools::displayError('%d image(s)'),

                                            count($customization['datas'][Product::CUSTOMIZE_FILE])

                                        ) . '';

                                }



                                $customization_quantity = (int)$customization['quantity'];



                                $product_var_tpl['customization'][] = array(

                                    'customization_text' => $customization_text,

                                    'customization_quantity' => $customization_quantity,

                                    'quantity' => Tools::displayPrice(

                                        $customization_quantity * $product_price,

                                        $this->context->currency,

                                        false

                                    )

                                );

                            }

                        }



                        $product_var_tpl_list[] = $product_var_tpl;

                        // Check if is not a virutal product for the displaying of shipping

                        if (!$product['is_virtual']) {

                            $virtual_product &= false;

                        }

                    } // end foreach ($products)



                    $product_list_txt = '';

                    $product_list_html = '';

                    if (count($product_var_tpl_list) > 0) {

                        $product_list_txt = $this->getEmailTemplateContent(

                            'order_conf_product_list.txt',

                            Mail::TYPE_TEXT,

                            $product_var_tpl_list

                        );

                        $product_list_html = $this->getEmailTemplateContent(

                            'order_conf_product_list.tpl',

                            Mail::TYPE_HTML,

                            $product_var_tpl_list

                        );

                    }



                    $cart_rules_list = array();

                    $total_reduction_value_ti = 0;

                    $total_reduction_value_tex = 0;

                    foreach ($cart_rules as $cart_rule) {

                        $package = array(

                            'id_carrier' => $order->id_carrier,

                            'id_address' => $order->id_address_delivery,

                            'products' => $order->product_list

                        );

                        $values = array(

                            'tax_incl' => $cart_rule['obj']->getContextualValue(

                                true,

                                $this->context,

                                CartRule::FILTER_ACTION_ALL_NOCAP,

                                $package

                            ),

                            'tax_excl' => $cart_rule['obj']->getContextualValue(

                                false,

                                $this->context,

                                CartRule::FILTER_ACTION_ALL_NOCAP,

                                $package

                            )

                        );



                        // If the reduction is not applicable to this order, then continue with the next one

                        if (!$values['tax_excl']) {

                            continue;

                        }



                        // IF

                        //  This is not multi-shipping

                        //  The value of the voucher is greater than the total of the order

                        //  Partial use is allowed

                        //  This is an "amount" reduction, not a reduction in % or a gift

                        // THEN

                        //  The voucher is cloned with a new value corresponding to the remainder

                        if (count($order_list) == 1 && $values['tax_incl'] >

                            ($order->total_products_wt - $total_reduction_value_ti) &&

                            $cart_rule['obj']->partial_use == 1 && $cart_rule['obj']->reduction_amount > 0) {

                            // Create a new voucher from the original

                            $voucher = new CartRule((int)$cart_rule['obj']->id); // We need to instantiate the CartRule without lang parameter to allow saving it

                            unset($voucher->id);



                            // Set a new voucher code

                            $voucher->code = empty($voucher->code) ? Tools::substr(md5($order->id . '-' .

                                $order->id_customer . '-' . $cart_rule['obj']->id), 0, 16) : $voucher->code . '-2';

                            if (preg_match('/\-([0-9]{1,2})\-([0-9]{1,2})$/', $voucher->code, $matches) &&

                                $matches[1] == $matches[2]) {

                                $voucher->code = preg_replace(

                                    '/' . $matches[0] . '$/',

                                    '-' . ((int)$matches[1] + 1),

                                    $voucher->code

                                );

                            }



                            // Set the new voucher value

                            if ($voucher->reduction_tax) {

                                $voucher->reduction_amount = ($total_reduction_value_ti + $values['tax_incl'])

                                    - $order->total_products_wt;



                                // Add total shipping amout only if reduction amount > total shipping

                                if ($voucher->free_shipping == 1 &&

                                    $voucher->reduction_amount >= $order->total_shipping_tax_incl) {

                                    $voucher->reduction_amount -= $order->total_shipping_tax_incl;

                                }

                            } else {

                                $voucher->reduction_amount = ($total_reduction_value_tex +

                                        $values['tax_excl']) - $order->total_products;



                                // Add total shipping amout only if reduction amount > total shipping

                                if ($voucher->free_shipping == 1 &&

                                    $voucher->reduction_amount >= $order->total_shipping_tax_excl) {

                                    $voucher->reduction_amount -= $order->total_shipping_tax_excl;

                                }

                            }

                            if ($voucher->reduction_amount <= 0) {

                                continue;

                            }



                            if ($this->context->customer->isGuest()) {

                                $voucher->id_customer = 0;

                            } else {

                                $voucher->id_customer = $order->id_customer;

                            }



                            $voucher->quantity = 1;

                            $voucher->reduction_currency = $order->id_currency;

                            $voucher->quantity_per_user = 1;

                            $voucher->free_shipping = 0;

                            if ($voucher->add()) {

                                // If the voucher has conditions, they are now copied to the new voucher

                                CartRule::copyConditions($cart_rule['obj']->id, $voucher->id);

                                $orderLanguage = new Language((int)$order->id_lang);



                                $params = array(

                                    '{voucher_amount}' => Tools::displayPrice(

                                        $voucher->reduction_amount,

                                        $this->context->currency,

                                        false

                                    ),

                                    '{voucher_num}' => $voucher->code,

                                    '{firstname}' => $this->context->customer->firstname,

                                    '{lastname}' => $this->context->customer->lastname,

                                    '{id_order}' => $order->reference,

                                    '{order_name}' => $order->getUniqReference()

                                );

                                Mail::Send(

                                    (int)$order->id_lang,

                                    'voucher',

                                    Context::getContext()->getTranslator()->trans(

                                        'New voucher for your order %s',

                                        array($order->reference),

                                        'Emails.Subject',

                                        $orderLanguage->locale

                                    ),

                                    $params,

                                    $this->context->customer->email,

                                    $this->context->customer->firstname . ' ' . $this->context->customer->lastname,

                                    null,

                                    null,

                                    null,

                                    null,

                                    _PS_MAIL_DIR_,

                                    false,

                                    (int)$order->id_shop

                                );

                            }



                            $values['tax_incl'] = $order->total_products_wt - $total_reduction_value_ti;

                            $values['tax_excl'] = $order->total_products - $total_reduction_value_tex;

                        }

                        $total_reduction_value_ti += $values['tax_incl'];

                        $total_reduction_value_tex += $values['tax_excl'];



                        $order->addCartRule(

                            $cart_rule['obj']->id,

                            $cart_rule['obj']->name,

                            $values,

                            0,

                            $cart_rule['obj']->free_shipping

                        );



                        if ($id_order_state != Configuration::get('PS_OS_ERROR') &&

                            $id_order_state != Configuration::get('PS_OS_CANCELED') &&

                            !in_array($cart_rule['obj']->id, $cart_rule_used)) {

                            $cart_rule_used[] = $cart_rule['obj']->id;



                            // Create a new instance of Cart Rule without id_lang, in order to update its quantity

                            $cart_rule_to_update = new CartRule((int)$cart_rule['obj']->id);

                            $cart_rule_to_update->quantity = max(0, $cart_rule_to_update->quantity - 1);

                            $cart_rule_to_update->update();

                        }



                        $cart_rules_list[] = array(

                            'voucher_name' => $cart_rule['obj']->name,

                            'voucher_reduction' => ($values['tax_incl'] != 0.00 ? '-' : '') .

                                Tools::displayPrice($values['tax_incl'], $this->context->currency, false)

                        );

                    }



                    $cart_rules_list_txt = '';

                    $cart_rules_list_html = '';

                    if (count($cart_rules_list) > 0) {

                        $cart_rules_list_txt = $this->getEmailTemplateContent(

                            'order_conf_cart_rules.txt',

                            Mail::TYPE_TEXT,

                            $cart_rules_list

                        );

                        $cart_rules_list_html = $this->getEmailTemplateContent(

                            'order_conf_cart_rules.tpl',

                            Mail::TYPE_HTML,

                            $cart_rules_list

                        );

                    }



                    // Specify order id for message

                    $old_message = Message::getMessageByCartId((int)$this->context->cart->id);

                    if ($old_message && !$old_message['private']) {

                        $update_message = new Message((int)$old_message['id_message']);

                        $update_message->id_order = (int)$order->id;

                        $update_message->update();



                        // Add this message in the customer thread

                        $customer_thread = new CustomerThread();

                        $customer_thread->id_contact = 0;

                        $customer_thread->id_customer = (int)$order->id_customer;

                        $customer_thread->id_shop = (int)$this->context->shop->id;

                        $customer_thread->id_order = (int)$order->id;

                        $customer_thread->id_lang = (int)$this->context->language->id;

                        $customer_thread->email = $this->context->customer->email;

                        $customer_thread->status = 'open';

                        $customer_thread->token = Tools::passwdGen(12);

                        $customer_thread->add();



                        $customer_message = new CustomerMessage();

                        $customer_message->id_customer_thread = $customer_thread->id;

                        $customer_message->id_employee = 0;

                        $customer_message->message = $update_message->message;

                        $customer_message->private = 1;



                        if (!$customer_message->add()) {

                            $this->errors[] = Tools::displayError('An error occurred while saving message');

                        }

                    }



                    if (self::DEBUG_MODE) {

                        PrestaShopLogger::addLog(

                            'PaymentModule::validateOrder - Hook validateOrder is about to be called',

                            1,

                            null,

                            'Cart',

                            (int)$id_cart,

                            true

                        );

                    }



                    // Hook validate order



                    Hook::exec('actionValidateOrder', array(

                        'cart' => $this->context->cart,

                        'order' => $order,

                        'customer' => $this->context->customer,

                        'currency' => $this->context->currency,

                        'orderStatus' => $order_status

                    ));



                    if ($completeFee['fee_with_tax'] > 0) {

                        // OrderPayments

                        $orderPayments = $order->getOrderPayments();

                        foreach ($orderPayments as $op) {

                            if ($op->transaction_id === $message) {

                                $op->amount = $completeFee['total_order_no_fee'] + $completeFee['fee_with_tax'];

                                $op->update();

                            }

                        }

                    }



                    foreach ($this->context->cart->getProducts() as $product) {

                        if ($order_status->logable) {

                            ProductSale::addProductSale((int)$product['id_product'], (int)$product['cart_quantity']);

                        }

                    }



                    if (self::DEBUG_MODE) {

                        PrestaShopLogger::addLog(

                            'PaymentModule::validateOrder - Order Status is about to be added',

                            1,

                            null,

                            'Cart',

                            (int)$id_cart,

                            true

                        );

                    }



                    // Set the order status

                    $new_history = new OrderHistory();

                    $new_history->id_order = (int)$order->id;

                    $new_history->changeIdOrderState((int)$id_order_state, $order, true);

                    $new_history->addWithemail(true, $extra_vars);



                    // Switch to back order if needed

                    if (Configuration::get('PS_STOCK_MANAGEMENT')

                        && ($order_detail->getStockState() || $order_detail->product_quantity_in_stock < 0)) {

                        $history = new OrderHistory();

                        $history->id_order = (int)$order->id;

                        $history->changeIdOrderState(Configuration::get($order->valid ?

                            'PS_OS_OUTOFSTOCK_PAID' : 'PS_OS_OUTOFSTOCK_UNPAID'), $order, true);

                        $history->addWithemail();

                    }



                    unset($order_detail);



                    // Order is reloaded because the status just changed

                    $order = new Order((int)$order->id);



                    // Send an e-mail to customer (one order = one email)

                    if ($id_order_state != Configuration::get('PS_OS_ERROR') &&

                        $id_order_state != Configuration::get('PS_OS_CANCELED') && $this->context->customer->id) {

                        $invoice = new Address((int)$order->id_address_invoice);

                        $delivery = new Address((int)$order->id_address_delivery);

                        $delivery_state = $delivery->id_state ? new State((int)$delivery->id_state) : false;

                        $invoice_state = $invoice->id_state ? new State((int)$invoice->id_state) : false;



                        $data = array(

                            '{firstname}' => $this->context->customer->firstname,

                            '{lastname}' => $this->context->customer->lastname,

                            '{email}' => $this->context->customer->email,

                            '{delivery_block_txt}' => $this->_getFormatedAddress($delivery, "\n"),

                            '{invoice_block_txt}' => $this->_getFormatedAddress($invoice, "\n"),

                            '{delivery_block_html}' => $this->_getFormatedAddress($delivery, '', array(

                                'firstname' => '%s',

                                'lastname' => '%s'

                            )),

                            '{invoice_block_html}' => $this->_getFormatedAddress($invoice, '', array(

                                'firstname' => '%s',

                                'lastname' => '%s'

                            )),

                            '{delivery_company}' => $delivery->company,

                            '{delivery_firstname}' => $delivery->firstname,

                            '{delivery_lastname}' => $delivery->lastname,

                            '{delivery_address1}' => $delivery->address1,

                            '{delivery_address2}' => $delivery->address2,

                            '{delivery_city}' => $delivery->city,

                            '{delivery_postal_code}' => $delivery->postcode,

                            '{delivery_country}' => $delivery->country,

                            '{delivery_state}' => $delivery->id_state ? $delivery_state->name : '',

                            '{delivery_phone}' => ($delivery->phone) ? $delivery->phone : $delivery->phone_mobile,

                            '{delivery_other}' => $delivery->other,

                            '{invoice_company}' => $invoice->company,

                            '{invoice_vat_number}' => $invoice->vat_number,

                            '{invoice_firstname}' => $invoice->firstname,

                            '{invoice_lastname}' => $invoice->lastname,

                            '{invoice_address2}' => $invoice->address2,

                            '{invoice_address1}' => $invoice->address1,

                            '{invoice_city}' => $invoice->city,

                            '{invoice_postal_code}' => $invoice->postcode,

                            '{invoice_country}' => $invoice->country,

                            '{invoice_state}' => $invoice->id_state ? $invoice_state->name : '',

                            '{invoice_phone}' => ($invoice->phone) ? $invoice->phone : $invoice->phone_mobile,

                            '{invoice_other}' => $invoice->other,

                            '{order_name}' => $order->getUniqReference(),

                            '{date}' => Tools::displayDate(date('Y-m-d H:i:s'), null, 1),

                            '{carrier}' => ($virtual_product || !isset($carrier->name)) ?

                                Tools::displayError('No carrier') : $carrier->name,

                            '{payment}' => Tools::substr($order->payment, 0, 32),

                            '{products}' => $product_list_html,

                            '{products_txt}' => $product_list_txt,

                            '{discounts}' => $cart_rules_list_html,

                            '{discounts_txt}' => $cart_rules_list_txt,

                            '{total_paid}' => Tools::displayPrice($order->total_paid, $this->context->currency, false),

                            '{total_products}' => Tools::displayPrice(

                                Product::getTaxCalculationMethod() == PS_TAX_EXC ?

                                    $order->total_products : $order->total_products_wt,

                                $this->context->currency,

                                false

                            ),

                            '{total_discounts}' => Tools::displayPrice(

                                $order->total_discounts,

                                $this->context->currency,

                                false

                            ),

                            '{total_shipping}' => Tools::displayPrice(

                                $order->total_shipping,

                                $this->context->currency,

                                false

                            ),

                            '{total_wrapping}' => Tools::displayPrice(

                                $order->total_wrapping,

                                $this->context->currency,

                                false

                            ),

                            '{total_tax_paid}' => Tools::displayPrice(

                                ($order->total_products_wt - $order->total_products) +

                                ($order->total_shipping_tax_incl - $order->total_shipping_tax_excl),

                                $this->context->currency,

                                false

                            )

                        );



                        if (is_array($extra_vars)) {

                            $data = array_merge($data, $extra_vars);

                        }



                        // Join PDF invoice

                        if ((int)Configuration::get('PS_INVOICE') &&

                            $order_status->invoice && $order->invoice_number) {

                            $order_invoice_list = $order->getInvoicesCollection();

                            Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => $order_invoice_list));

                            $pdf = new PDF($order_invoice_list, PDF::TEMPLATE_INVOICE, $this->context->smarty);

                            $file_attachement = [];

                            $file_attachement['content'] = $pdf->render(false);

                            $file_attachement['name'] = Configuration::get(

                                    'PS_INVOICE_PREFIX',

                                    (int)$order->id_lang,

                                    null,

                                    $order->id_shop

                                ) . sprintf('%06d', $order->invoice_number) . '.pdf';

                            $file_attachement['mime'] = 'application/pdf';

                        } else {

                            $file_attachement = null;

                        }



                        if (self::DEBUG_MODE) {

                            PrestaShopLogger::addLog(

                                'PaymentModule::validateOrder - Mail is about to be sent',

                                1,

                                null,

                                'Cart',

                                (int)$id_cart,

                                true

                            );

                        }



                        $orderLanguage = new Language((int)$order->id_lang);



                        if (Validate::isEmail($this->context->customer->email)) {

                            Mail::Send(

                                (int)$order->id_lang,

                                'order_conf',

                                Context::getContext()->getTranslator()->trans(

                                    'Order confirmation',

                                    array(),

                                    'Emails.Subject',

                                    $orderLanguage->locale

                                ),

                                $data,

                                $this->context->customer->email,

                                $this->context->customer->firstname . ' ' . $this->context->customer->lastname,

                                null,

                                null,

                                $file_attachement,

                                null,

                                _PS_MAIL_DIR_,

                                false,

                                (int)$order->id_shop

                            );

                        }

                    }



                    // updates stock in shops

                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {

                        $product_list = $order->getProducts();

                        foreach ($product_list as $product) {

                            // if the available quantities depends on the physical stock

                            if (StockAvailable::dependsOnStock($product['product_id'])) {

                                // synchronizes

                                StockAvailable::synchronize($product['product_id'], $order->id_shop);

                            }

                        }

                    }



                    $order->updateOrderDetailTax();

                } else {

                    $error = Tools::displayError('Order creation failed');

                    PrestaShopLogger::addLog($error, 4, '0000002', 'Cart', (int)$order->id_cart);

                    die($error);

                }

            } // End foreach $order_detail_list

            // Use the last order as currentOrder

            if (isset($order) && $order->id) {

                $this->currentOrder = (int)$order->id;

            }



            if (self::DEBUG_MODE) {

                PrestaShopLogger::addLog(

                    'PaymentModule::validateOrder - End of validateOrder',

                    1,

                    null,

                    'Cart',

                    (int)$id_cart,

                    true

                );

            }



            return true;

        } else {

            $error = Tools::displayError('Cart cannot be loaded or an order has already been placed using this cart');

            PrestaShopLogger::addLog($error, 4, '0000001', 'Cart', (int)$this->context->cart->id);

            die($error);

        }

    }



    /**

     * This function will check if the given token is still valid

     *

     * @param $token - the token to check

     * @return bool - True if token is valid, false if isn't

     */

    public function checkToken($token)

    {

        return Tools::getAdminToken($this->name) == $token;

    }



    /**

     * Generates a unique hash based on the cart's contents and details.

     *

     * This function creates a hash by combining various pieces of information from the cart, including:

     * - Product IDs, attributes, and quantities for each product in the cart

     * - The carrier ID (if available)

     * - The cart ID

     * - The total order amount (after tax and shipping)

     *

     * The function assembles these values into a key, and then generates an MD5 hash of this key to create a unique identifier for the cart.

     * This hash can be used for tracking or comparison purposes, ensuring that the cart's state can be reliably identified.

     *

     * @param Cart $cart The current cart object.

     *

     * @return string The generated MD5 hash representing the cart's unique details.

     */



    public function generateCartHash($cart)

    {

        $hash = [];

        if (!empty($products = $cart->getProducts())) {

            foreach ($products as $product) {

                $hash[] = implode(

                    '-',

                    [

                        $product['id_product'],

                        $product['id_product_attribute'],

                        $product['quantity'],

                    ]

                );

            }

        }



        $hash[] = $cart->id_carrier;

        $hash[] = $cart->id;

        $hash[] = $cart->getOrderTotal(true, Cart::BOTH);



        return md5(implode('_', $hash));

    }



    /**

     * Generates and stores a unique cart hash in the user's cookie.

     *

     * If no cart is provided, the current cart from the context is used.

     * The generated hash is stored as `ppwf_cart_hash` in the cookie.

     *

     * @param Cart|null $cart The cart to generate the hash for. Defaults to the current cart if null.

     */

    public function updateCartHash($cart = null)

    {

        if (empty($cart)) {

            $cart = Context::getContext()->cart;

        }

        $cart_hash = $this->generateCartHash($cart);

        Context::getContext()->cookie->ppwf_cart_hash = $cart_hash;

        Context::getContext()->cookie->write();

    }



    /**

     * Retrieves the cart hash from the user's cookie.

     *

     * Returns the stored cart hash if it exists, or an empty string if not.

     *

     * @return string The cart hash or an empty string if not set.

     */



    public function getCartHash()

    {

        return (string)Context::getContext()->cookie->ppwf_cart_hash ?? '';

    }



    /**

     * Compares the stored cart hash with the generated cart hash.

     *

     * Checks if the cart's current hash matches the stored hash in the cookie,

     * indicating that the cart has not been altered.

     *

     * @param Cart $cart The cart to compare the hash with.

     *

     * @return bool True if the hashes match, false otherwise.

     */

    public function isValidHash($cart)

    {

        return $this->getCartHash() == $this->generateCartHash($cart);

    }

}

