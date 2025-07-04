<?php
/**
 * 2007-2017 PrestaShop
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
 *  @copyright 2007-2017 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class ValidateVatNumber extends Module
{
    /**
     * PrestaShop Addons Developer ID.
     *
     * @var int
     */
    const DEVELOPER_ID = 156845;

    /**
     * Module configuration keys and
     * their default values.
     *
     * @var array
     */
    public $configs = array(
        'VALIDATEVATNUMBER_MANUAL_MODE' => 0,
        'VALIDATEVATNUMBER_VAT_REQUIRED' => 0,
        'VALIDATEVATNUMBER_INCORRECT_VAT_ACCEPTED' => 0,
        'VALIDATEVATNUMBER_CHANGE_VALIDATION_METHOD' => 0,
        'VALIDATEVATNUMBER_DEFAULT_GROUP' => 3,
        'VALIDATEVATNUMBER_COUNTRY' => 0,
        'VALIDATEVATNUMBER_COUNTRY_ID' => 0,
        'VALIDATEVATNUMBER_ACCEPTED_GROUP' => '',
        'VALIDATEVATNUMBER_ADMINNOTIFY' => 0,
        'VALIDATEVATNUMBER_ADMINMAILS' => '',
        'VALIDATEVATNUMBER_USERSNOTIFY' => 1,
        'VALIDATEVATNUMBER_DEFAULT_CURL_TIMEOUT' => 15,
    );

    /**
     * Current request data.
     *
     * @var array
     */
    public $request = array();

    /**
     * Current request errors.
     *
     * @var array
     */
    public $errors = array();

    /**
     * Current request confirmation.
     *
     * @var string
     */
    public $confirmation = '';

    /**
     * Hooks used by the module.
     *
     * @var array
     */
    public $hooks = array(
        'actionObjectCustomerAddAfter',
        'actionDispatcher',
    );

    /**
     * Hooks used by the module.
     *
     * @var array
     */
    public $hooks17 = array(
        'actionObjectAddressUpdateAfter',
        'actionValidateCustomerAddressForm',
    );


    /**
     * Unique database instance.
     *
     * @var Db
     */
    public $db;

    /**
     * Database prefix.
     *
     * @var string
     */
    public $pr = _DB_PREFIX_;

    /**
     * Create a new PrestaShop module instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->name = 'validatevatnumber';
        $this->author = 'ActiveDesign';
        $this->version = '2.4.0';
        $this->bootstrap = true;
        $this->module_key = '7f6ab33de18b8009b1927a87b1e2a0b5';
        parent::__construct();

        $this->displayName = $this->l('Validate VAT Number');
        $this->description = $this->l('Check and validate your custommers VAT number');

        $this->ps_version_compliancy = array('min' => '1.6.0', 'max' => _PS_VERSION_);
        $this->tab = 'billing_invoicing';
        $this->db = Db::getInstance();

        //Back controllers list
        $this->secondaryBackControllers = array(
            'AdminValidateVatNumberList' => $this->l('VAT Numbers'),
        );
    }

    /**
     * Create BackOffice controllers
     *
     * @return bool
     */
    public function addBackOfficeControllers()
    {

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'SaveCountryGroup';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'SaveCountryGroup';
        }
        $tab->id_parent = -1;
        $tab->module = $this->name;
        $tab->add();

        $primaryTabId = Tab::getIdFromClassName('AdminParentCustomer');
        if ($primaryTabId) {
            foreach ($this->secondaryBackControllers as $class_name => $name) {
                $tab = new Tab;

                $tab->class_name = $class_name;
                $tab->id_parent = $primaryTabId;
                $tab->module = $this->name;
                $tab->name[(int)(Configuration::get('PS_LANG_DEFAULT'))] = $name;
                if (!$tab->add()) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Delete backoffice controllers
     * @return bool
     */
    public function deleteBackOfficeTabs()
    {
        foreach (array_keys($this->secondaryBackControllers) as $class_name) {
            $tab = new Tab(Tab::getIdFromClassName($class_name));
            $tab->delete();
        }
        return true;
    }

    /**
     * Install the module.
     *
     * @return bool
     */
    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');
        return parent::install() &&
            $this->createConfigs() &&
            $this->registerHooks() &&
            $this->addBackOfficeControllers() &&
            $this->addNewCustomerGroup();
    }

    /**
     * Uninstall the module.
     *
     * @return bool
     */
    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');
        return parent::uninstall() &&
            $this->unregisterHooks() &&
            $this->deleteBackOfficeTabs() &&
            $this->removeNewCustomerGroup() &&
            $this->deleteConfigs();
    }

    /**
     * Create the necessary module configurations.
     *
     * @return bool
     */
    public function createConfigs()
    {
        foreach ($this->configs as $key => $value) {
            if (! Configuration::updateValue($key, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Delete the module configurations
     *
     * @return bool
     */
    public function deleteConfigs()
    {
        foreach (array_keys($this->configs) as $key) {
            if (! Configuration::deleteByName($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Register the module hooks.
     *
     * @return bool
     */
    public function registerHooks()
    {
        $BOHeader = 'backOfficeHeader';
        if (Tools::substr(_PS_VERSION_, 0, 3) >= '1.7') {
            $BOHeader = 'displayBackOfficeHeader';
            foreach ($this->hooks17 as $hook) {
                if (!$this->registerHook($hook)) {
                    return false;
                }
            }
        }
        $this->hooks[] = $BOHeader;
        foreach ($this->hooks as $hook) {
            if (! $this->registerHook($hook)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Unregister the module hooks.
     *
     * @return bool
     */
    public function unregisterHooks()
    {
        $BOHeader = 'backOfficeHeader';
        if (Tools::substr(_PS_VERSION_, 0, 3) == '1.7') {
            $BOHeader = 'displayBackOfficeHeader';
            foreach ($this->hooks17 as $hook) {
                if (!$this->unregisterHook($hook)) {
                    return false;
                }
            }
        }
        $this->hooks[] = $BOHeader;
        foreach ($this->hooks as $hook) {
            if (! $this->unregisterHook($hook)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the module settings form.
     *
     * @return string
     */
    public function getContent()
    {
        $this->postProcess();

        $helper = $this->getHelperForm();

        $helper->fields_value = $this->getSettingsFormValues();

        $settingsForm = $helper->generateForm(array(
            array('form' => $this->getSettingsForm())
        ));
        $settingsForm2 = $helper->generateForm(array(
            array('form' => $this->getSettingsForm2())
        ));
        $get_all_countries = self::getAllCountries($this->context->language->id);

        $this->context->smarty->assign(array(
            'errors' => $this->errors,
            'confirmation' => $this->confirmation,
            'settingsForm' => $settingsForm,
            'settingsForm2' => $settingsForm2,
            'ourProducts' => $this->getOurProductsLink(),
            'vatNumberListController' => $this->context->link->getAdminLink('AdminValidateVatNumberList'),
            'form_action' => $this->context->link->getAdminLink("AdminModules")
                . "&module_name=" . $this->name.
                "&configure=validatevatnumber&tab_module=billing_invoicing",
            'country_list' => $get_all_countries,
            'customer_groups' => $this->getCustomersGroups(),
        ));

        return $this->context->smarty->fetch($this->getLocalPath().'views/templates/admin/configure.tpl');
    }

    /**
     * Get the module admin link.
     *
     * @return string
     */
    public function getModuleAdminLink()
    {
        return $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name;
    }

    /**
     * Get settings form configuration values.
     *
     * @return array
     */
    public function getSettingsFormValues()
    {
        $values = array();

        foreach (array_keys($this->configs) as $key) {
            $values[$key] = Configuration::get($key);
        }

        return $values;
    }

    /**
     * Get settings form definitions.
     *
     * @return array
     */
    public function getSettingsForm()
    {
        return array(
            'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-gear',
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->getInputLabel('VALIDATEVATNUMBER_MANUAL_MODE'),
                    'name' => 'VALIDATEVATNUMBER_MANUAL_MODE',
                    'is_bool' => true,
                    'desc' => $this->l('If you activate "Manual Mode", all the VAT numbers must be validated manually otherwise they will be validated automatically'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->getInputLabel('VALIDATEVATNUMBER_VAT_REQUIRED'),
                    'name' => 'VALIDATEVATNUMBER_VAT_REQUIRED',
                    'is_bool' => true,
                    'desc' => $this->l('Make VAT Number field required'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->getInputLabel('VALIDATEVATNUMBER_INCORRECT_VAT_ACCEPTED'),
                    'name' => 'VALIDATEVATNUMBER_INCORRECT_VAT_ACCEPTED',
                    'is_bool' => true,
                    'desc' => $this->l('Accept VAT field despite the number being wrong'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->getInputLabel('VALIDATEVATNUMBER_CHANGE_VALIDATION_METHOD'),
                    'name' => 'VALIDATEVATNUMBER_CHANGE_VALIDATION_METHOD',
                    'is_bool' => true,
                    'desc' => $this->l('Do you wish to change the validation from VIES?'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->getInputLabel('VALIDATEVATNUMBER_DEFAULT_GROUP'),
                    'desc' => $this->l('Choose the default customer group while awaiting moderation'),
                    'name' => 'VALIDATEVATNUMBER_DEFAULT_GROUP',
                    'required' => false,
                    'options' => array(
                        'query' => $this->getCustomersGroups(),
                        'id' => 'id_group',
                        'name' => 'name',
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->getInputLabel('VALIDATEVATNUMBER_ACCEPTED_GROUP'),
                    'desc' => $this->l('Choose the default customer group after validation'),
                    'name' => 'VALIDATEVATNUMBER_ACCEPTED_GROUP',
                    'required' => false,
                    'options' => array(
                        'query' => $this->getCustomersGroups(),
                        'id' => 'id_group',
                        'name' => 'name',
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->getInputLabel('VALIDATEVATNUMBER_COUNTRY_ID'),
                    'desc' => $this->l('In which country the goods will always be taxed?'),
                    'name' => 'VALIDATEVATNUMBER_COUNTRY_ID',
                    'required' => false,
                    'options' => array(
                        'query' => $this->getCountries(),
                        'id' => 'id_country',
                        'name' => 'name',
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->getInputLabel('VALIDATEVATNUMBER_COUNTRY'),
                    'name' => 'VALIDATEVATNUMBER_COUNTRY',
                    'is_bool' => true,
                    'desc' => $this->l('Select VAT number option for each country'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'saveSettings',
            ),
        );
    }

    /**
     * Get settings form definitions.
     *
     * @return array
     */
    public function getSettingsForm2()
    {
        return array(
            'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-gear',
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->getInputLabel('VALIDATEVATNUMBER_ADMINNOTIFY'),
                    'name' => 'VALIDATEVATNUMBER_ADMINNOTIFY',
                    'is_bool' => true,
                    'desc' => $this->l('Notify Admin by e-mail about new VAT numbers need to be valdidated when "Manual Mode" is active'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'text',
                    'class' => 't',
                    'col' => 3,
                    'label' => $this->getInputLabel('VALIDATEVATNUMBER_DEFAULT_CURL_TIMEOUT'),
                    'name' => 'VALIDATEVATNUMBER_DEFAULT_CURL_TIMEOUT',
                    'desc' => $this->l('The time it takes before the VIES check times out.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->getInputLabel('VALIDATEVATNUMBER_ADMINMAILS'),
                    'name' => 'VALIDATEVATNUMBER_ADMINMAILS',
                    'col' => 3,
                    'desc' => $this->l('Admin e-mails separated by ;'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->getInputLabel('VALIDATEVATNUMBER_USERSNOTIFY'),
                    'name' => 'VALIDATEVATNUMBER_USERSNOTIFY',
                    'is_bool' => true,
                    'desc' => $this->l('Notify users by e-mail when VAT number is verified manually'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'saveSettings',
            ),
        );
    }

    /**
     * Get yes or no switch values.
     *
     * @return array
     */
    public function getYesOrNoValues()
    {
        return array(
            array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Yes')),
            array('id' => 'active_off', 'value' => 0, 'label' => $this->l('No')),
        );
    }

    /**
     * Get helper form instance.
     *
     * @return HelperForm
     */
    public function getHelperForm()
    {
        $helper = new HelperForm();

        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->currentIndex = $this->getModuleAdminLink();
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars['languages'] = $this->context->controller->getLanguages();
        $helper->tpl_vars['id_language'] = $this->context->language->id;

        return $helper;
    }

    /**
     * Process any form request.
     *
     * @return void
     */
    public function postProcess()
    {
        if (Tools::isSubmit('saveSettings')) {
            $this->processSettingsForm();
        }
    }

    /**
     * Process the settings form.
     *
     * @return void
     */
    public function processSettingsForm()
    {
        $this->setRequestData();

        foreach ($this->request as $key => $value) {
            if ($key == 'VALIDATEVATNUMBER_VAT_REQUIRED') {
                if (Tools::getValue($key) == 1) {
                    $isRequired = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `field_name` FROM '._DB_PREFIX_.'required_field WHERE `field_name`="'.pSQL('vat_number').'"');
                    $presta_version_address_format = '';
                    if (Tools::substr(_PS_VERSION_, 0, 3) == '1.6') {
                        $presta_version_address_format = 'Address';
                    } elseif (Tools::substr(_PS_VERSION_, 0, 3) == '1.7') {
                        $presta_version_address_format = 'CustomerAddress';
                    }
                    if (!$isRequired) {
                        Db::getInstance()->insert('required_field', array(
                            'object_name' => pSQL($presta_version_address_format),
                            'field_name' => pSQL('vat_number'),
                        ));
                        Db::getInstance()->insert('required_field', array(
                            'object_name' => pSQL($presta_version_address_format),
                            'field_name' => pSQL('company'),
                        ));
                    }
                } else if (Tools::getValue($key) == 0) {
                    Db::getInstance()->delete('required_field', 'field_name = "vat_number"');
                    Db::getInstance()->delete('required_field', 'field_name = "company"');
                }
                Configuration::updateValue($key, $value);
            } else if (! Configuration::updateValue($key, $value)) {
                $this->errors[] = $this->l('Could not save setting')
                    .sprintf(': "%s".', $this->getInputLabel($key));
            }
        }

        if (count($this->errors) == 0) {
            $this->confirmation = $this->l('Settings saved successfully.');
        }
    }

    /**
     * Set the current request data.
     *
     * @return void
     */
    public function setRequestData()
    {
        foreach (array_keys($this->configs) as $key) {
            if (Tools::getValue($key) !== false) {
                $this->request[$key] = Tools::getValue($key);
            }
        }
    }

    /**
     * Get input label by key.
     *
     * @param  string $key
     * @return string
     */
    public function getInputLabel($key)
    {
        switch ($key) {
            case 'VALIDATEVATNUMBER_MANUAL_MODE':
                return $this->l('Manual Mode');
            case 'VALIDATEVATNUMBER_DEFAULT_CURL_TIMEOUT':
                return $this->l('VIES timeout timer');
            case 'VALIDATEVATNUMBER_VAT_REQUIRED':
                return $this->l('VAT number required');
            case 'VALIDATEVATNUMBER_INCORRECT_VAT_ACCEPTED':
                return $this->l('Incorrect VAT number');
            case 'VALIDATEVATNUMBER_CHANGE_VALIDATION_METHOD':
                return $this->l('Change the validation method');
            case 'VALIDATEVATNUMBER_DEFAULT_GROUP':
                return $this->l('Default customer group');
            case 'VALIDATEVATNUMBER_ACCEPTED_GROUP':
                return $this->l('Validated customers group');
            case 'VALIDATEVATNUMBER_COUNTRY_ID':
                return $this->l('Taxed country');
            case 'VALIDATEVATNUMBER_COUNTRY':
                return $this->l('Validate Country');
            case 'VALIDATEVATNUMBER_ADMINNOTIFY':
                return $this->l('Notify Admin');
            case 'VALIDATEVATNUMBER_ADMINMAILS':
                return $this->l('Admin E-mails');
            case 'VALIDATEVATNUMBER_USERSNOTIFY':
                return $this->l('Notify Users');
        }

        return '';
    }

    /**
     * Get PrestaShop Addons products/modules link.
     *
     * @return string
     */
    public function getOurProductsLink()
    {
        return 'https://addons.prestashop.com/en/2_community-developer?contributor='
            .static::DEVELOPER_ID;
    }

    /**
     * Display content in back office
     *
     * @return  string
     */
    public function hookBackOfficeHeader()
    {
        if ((Tools::getValue('controller') == 'AdminModules' &&
            (Tools::getValue('configure') == $this->name ||
                Tools::getValue('module_name') == $this->name))) {
            Media::addJsDef(array(
                'save_country_group' => $this->context->link->getAdminLink("SaveCountryGroup"),
                'ajax_ok_message' => $this->l('Change saved'),
                'ajax_not_ok_message' => $this->l('Please select a group'),
            ));
            $this->context->controller->addJQuery();
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
            $this->context->controller->addJS($this->_path . 'views/js/back.js');

            return '';
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        return $this->hookBackOfficeHeader();
    }

    /**
     * Adds/removes special group to the validated. This special group needs to be double-checked.
     */
    public function hookActionDispatcher()
    {
        if (is_object(Context::getContext()->customer)) {
            $id_cart = $this->context->cookie->id_cart;
            $cart = new Cart($id_cart);
            $idAddress = $cart->id_address_invoice;
            $vatValid = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `vat_number_status` FROM '._DB_PREFIX_.'validatevatnumber WHERE `id_address`="'.(int)$idAddress.'"');
            $vatAccGroup = Configuration::get('VALIDATEVATNUMBER_ACCEPTED_GROUP');
            $addressData = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_customer` FROM `'._DB_PREFIX_.'address` WHERE `id_address`="'.(int)$idAddress.'"');

            if ($vatValid && (Address::getCountryAndState($idAddress)['id_country'] !== Configuration::get('VALIDATEVATNUMBER_COUNTRY_ID'))) {
                Db::getInstance()->insert('customer_group', array(
                    'id_customer' => (int)$addressData,
                    'id_group' => pSQL($vatAccGroup),
                ), false, false, Db::REPLACE);
            } else {
                Db::getInstance()->delete(
                    'customer_group',
                    'id_customer = "' . (int)$addressData . '" AND
                    `id_group` = "'.$vatAccGroup.'"'
                );
            }
        }
    }

    /**
     * Hook to set customer's default group the one selected in the back
     * @param $params
     */
    public function hookActionObjectCustomerAddAfter($params)
    {
        $params['object']->id_default_group = Configuration::get('VALIDATEVATNUMBER_DEFAULT_GROUP');
    }

    /**
     * Hook used to verify the vat number at address add (only used in  presta >1.7)
     * @param $params
     * @return bool|string
     */
    public function hookActionValidateCustomerAddressForm($params)
    {
        $is_valid = true;
        $form = $params['form'];
        if ((int)Configuration::get('VALIDATEVATNUMBER_MANUAL_MODE') == 0) {
            if ($this->verifyCountryAndVat(Tools::getValue('id_country'), Tools::getValue('vat_number')) === true && !empty(Tools::getValue('vat_number'))) {
                if (Tools::getValue('vat_number') && ($vat_number = $form->getField('vat_number'))) {
                    if ($this->verifyVatNumberOnline(Tools::getValue('vat_number')) == true) {
                        $is_valid = '';
                    } else if ($this->verifyVatNumberOnline(Tools::getValue('vat_number')) == "Your VAT number format is incorrect!") {
                        $vat_number->addError($this->l('Your VAT number format is incorrect!'));
                        $is_valid = '0';
                    } else {
                        $vat_number->addError($this->l('Your VAT number is invalid!'));
                        $is_valid = '0';
                    }
                }
                return $is_valid;
            } elseif (!empty(Tools::getValue('vat_number'))) {
                $vat_number = $form->getField('vat_number');
                $vat_number->addError($this->l('Your VAT number country does not match the address country!'));
            }
        }
    }

    /**
     * Hook used to insert into db the valid vat on address update (used in presta > 1.7)
     * @param $params
     */
    public function hookActionObjectAddressUpdateAfter($params)
    {
        if ((int)Configuration::get('VALIDATEVATNUMBER_MANUAL_MODE') == 1) {
            Db::getInstance()->insert('validatevatnumber', array('vat_number_status'=>(int)2, 'id_address' => $params['object']->id), false, true, Db::REPLACE);
        } else {
            Db::getInstance()->insert('validatevatnumber', array('vat_number_status'=>(int)$this->verifyVatNumberOnline($params['object']->vat_number), 'id_address' => $params['object']->id), false, true, Db::REPLACE);
        }
    }

    /**
     * Get customer groups function
     * @return array
     */
    public function getCustomersGroups()
    {
        $customers_groups = Group::getGroups($this->context->language->id, true);
        $customers_groups_array = array();

        foreach ($customers_groups as $customers_group) {
            $customers_groups_array[] = array(
                "id_group" => (int)$customers_group['id_group'],
                "name" => $customers_group['name'],
            );
        }
        return $customers_groups_array;
    }

    /**
     * Get countries function
     * @return array
     */
    public function getCountries()
    {
        $countries = Country::getCountries($this->context->language->id);
        $countries = array_merge(array(array('id_country' => 0, 'name' => $this->l('- None -'))), $countries);

        return $countries;
    }

    /**
     * Get all countries(function)
     * @param $id_lang
     * @param bool $active
     * @param bool $contain_states
     * @param bool $list_states
     * @return array
     */
    public static function getAllCountries($id_lang, $active = false, $contain_states = false, $list_states = true)
    {
        $countries = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT vc.`id_customer_group`, cl.*,c.*, cl.`name` country, z.`name` zone
		FROM `'._DB_PREFIX_.'country` c '.Shop::addSqlAssociation('country', 'c').'
		LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'zone` z ON (z.`id_zone` = c.`id_zone`)
        LEFT JOIN `'._DB_PREFIX_.'validatevatnumber_country` vc ON (c.`id_country` = vc.`id_country`)
		WHERE 1'.($active ? ' AND c.active = 1' : '').($contain_states ? ' AND c.`contains_states` = '.(int)$contain_states : '').'
		ORDER BY cl.name ASC');
        foreach ($result as $row) {
            $countries[$row['id_country']] = $row;
        }

        if ($list_states) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'state` ORDER BY `name` ASC');
            foreach ($result as $row) {
                if (isset($countries[$row['id_country']]) && $row['active'] == 1) { /* Does not keep the state if its country has been disabled and not selected */
                    $countries[$row['id_country']]['states'][] = $row;
                }
            }
        }
        return $countries;
    }


    /**
     * Get all groups selected by each country
     * @param $id_country
     * @return int
     */
    public function getSelectedCountryDefaultGroup($id_country)
    {
        $sql = 'SELECT id_customer_group FROM '._DB_PREFIX_.'validatevatnumber_country WHERE id_country = '. (int)$id_country .'';
        $result = Db::getInstance()->getValue($sql);

        return (int)$result;
    }

    /**
     * Verify vat validation online
     * @param $vat_number
     * @return bool
     */
    public function verifyVatNumberOnline($vat_number)
    {
        $vat_number = $this->cleanVAT($vat_number);
        $vat_number_prefix = str_split($vat_number, 2);
        $vat_number_number = str_replace($vat_number_prefix[0], "", $vat_number);
        $timer = (Configuration::get('VALIDATEVATNUMBER_DEFAULT_CURL_TIMEOUT') ? Configuration::get('VALIDATEVATNUMBER_DEFAULT_CURL_TIMEOUT') : 15);
        if (Configuration::get('VALIDATEVATNUMBER_CHANGE_VALIDATION_METHOD')) {

        }
        if ($vat_number) {
            $url = 'https://ec.europa.eu/taxation_customs/vies/rest-api/ms/'.urlencode($vat_number_prefix[0]).'/vat/'.urlencode($vat_number_number);
            $encoded = self::curlCall($url, $timer);
            $result = json_decode($encoded);
            if ($result->isValid  && $result->userError == 'VALID') {
                return true;
            } elseif ($result->isValid === false && $result->userError == 'INVALID') {
                return (Tools::substr(_PS_VERSION_, 0, 3) == '1.6' ? Tools::displayError($this->l('Your VAT number format is incorrect!')) : $this->l('Your VAT number format is incorrect!'));
            } else {
                return (Tools::substr(_PS_VERSION_, 0, 3) == '1.6' ? Tools::displayError($this->l('VIES system in currently unavailable for the selected country, please try again later.')) : $this->l('VIES system in currently unavailable for the selected country, please try again later.'));
            }
        }
    }

    /**
     * Check if country selected matches vat country entered
     * @return array|mixed|null|string
     */
    public function verifyCountryAndVat($country, $vat_number)
    {
        if (!empty($vat_number)) {
            $return = false;
            $countryISO = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `iso_code` FROM ' . _DB_PREFIX_ . 'country WHERE `id_country`="' . pSQL($country) . '"');
            $vat_number_prefix = str_split($vat_number, 2);
            if ($countryISO == 'GR' && 'EL' === $vat_number_prefix[0]) {
                $return = true;
            }
            if ($countryISO === $vat_number_prefix[0]) {
                $return = true;
            }
            if (!$return) {
                return (Tools::substr(_PS_VERSION_, 0, 3) == '1.6' ? Tools::displayError($this->l('Your VAT number country does not match the address country!')) : $this->l('Your VAT number country does not match the address country!'));
            }
            return true;
        }
    }

    /**
     * Notify administrators when new vat needs validation. Works only when "Manual Mode" is active
     * @param $administrator
     * @param $firstname
     * @param $lastname
     * @param $email
     * @param $vatnr
     * @return mixed
     */
    public function sendNotificationAdmin($administrator, $firstname, $lastname, $vatnr)
    {
        $mailParams = array(
            '{firstname}' => $firstname,
            '{lastname}' => $lastname,
            '{vatnr}' => $vatnr,
            '{administrator}' => $administrator,
        );
        $sent = Mail::Send(
            $this->context->language->id,
            "newvat",
            "New vat submitted",
            $mailParams,
            $administrator,
            $administrator,
            null,
            null,
            null,
            null,
            dirname(__FILE__) . "/mails/"
        );
        return $sent;
    }

    /**
     * Create validated customers group and update default value for Verified input in backoffice
     * @return mixed
     */
    public function addNewCustomerGroup()
    {
        $new_group = new Group();

        foreach (Language::getLanguages(false) as $lang) {
            $new_group->name[(int) $lang['id_lang']] = 'Verified Customers';
        }

        $new_group->price_display_method = '1';

        $add = $new_group->add();
        if ($add) {
            $auth_modules_sql = Db::getInstance()->executeS('SELECT `id_module` FROM `'._DB_PREFIX_.'module`');
            $auth_modules = array();
            foreach ($auth_modules_sql as $row) {
                $auth_modules[] = $row['id_module'];
            }
            $shops = Shop::getShops(true, null, true);
            if (is_array($auth_modules)) {
                $add &= Group::addModulesRestrictions($new_group->id, $auth_modules, $shops);
            }
            Configuration::updateValue('VALIDATEVATNUMBER_ACCEPTED_GROUP', $new_group->id);
        }
        return $add;
    }

    /**
     *
     * Delete the validated customers group at uninstall and set all customers without group to group id:3
     */
    public function removeNewCustomerGroup()
    {
        $groupID = (int)Configuration::get('VALIDATEVATNUMBER_ACCEPTED_GROUP');
        $newGroup = new Group($groupID);
        if (Validate::isLoadedObject($newGroup)) {
            $newGroup->delete();
        }
        return true;
    }

    /**
     *
     * Remove special characters from VAT
     */
    public function cleanVAT($string)
    {
        $string = str_replace(' ', '', $string); // Removes all spaces

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    public static function curlCall($url, $timeout)
    {
        $curl_call = curl_init($url);
        curl_setopt($curl_call, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($curl_call, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl_call, CURLOPT_RETURNTRANSFER, true);
        $curl_result = curl_exec($curl_call);
        curl_close($curl_call);

        return $curl_result;
    }
}
