<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code.
 *
 *  @author    Active Design <office@activedesign.ro>
 *  @copyright 2018 Active Design
 *  @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminValidateVatNumberListController extends AdminController
{
    public $prefix = '';
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'address';
        $this->list_id = 'id_address';
        $this->addRowAction('view');
        $this->prefix = $this->list_id . 'Filter_';
        parent::__construct();
    }

    /**
     * Current request confirmations.
     *
     * @var array
     */
    public $vatConfirmation = array();

    /**
     * Current request errors.
     *
     * @var array
     */
    public $vatErrors = array();

    public function setRedirectAfter($url)
    {
        // Workaround for unwanted redirect when filtering
        if ((int)Tools::getValue('submitFilter' . $this->list_id) ||
            Tools::isSubmit('submitReset' . $this->list_id)) {
            return;
        }

        parent::setRedirectAfter($url);
    }

    public function init()
    {
        $this->context->controller->addCSS(_PS_MODULE_DIR_ . 'validatevatnumber/views/css/back.css');
        $url = $this->context->link->getAdminLink('AdminModules').'&configure=validatevatnumber&module=validatevatnumber';
        $this->context->smarty->assign(array(
            'vatNumberListController' => $this->context->link->getAdminLink('AdminValidateVatNumberList'),
            'vatNumberLink' => $url,
        ));
        $redirect = $this->context->link->getAdminLink('AdminValidateVatNumberList').'&conf=4';
        $this->module = Module::getInstanceByName('validatevatnumber');
        if (!empty(Tools::getValue('id_address'))) {
            $this->renderView();
        }

        if (!empty(Tools::getValue('reqstatus')) && Tools::getValue('reqstatus') == 1) {
            $id_address = Tools::getValue('id_address');
            $customerId = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_customer` FROM '._DB_PREFIX_.'address WHERE `id_address`="'.(int)$id_address.'"');
            $id_accepted_group = Configuration::get('VALIDATEVATNUMBER_ACCEPTED_GROUP');
            $customerGroup = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_customer` FROM '._DB_PREFIX_.'customer_group WHERE `id_group`="'.(int)$id_accepted_group.'"');

            Db::getInstance()->update('validatevatnumber', array(
                'vat_number_status' => (int)1,
            ), '`id_address` = "' . (int)$id_address . '"');
            if (!$customerGroup) {
                Db::getInstance()->insert('customer_group', array(
                    'id_customer' => (int)$customerId,
                    'id_group' => (int)$id_accepted_group,
                ));
            }
            /** No longer needed to set the default group */
//            Db::getInstance()->update('customer', array(
//                'id_default_group' => (int)$id_accepted_group,
//            ), '`id_customer` = "' . (int)$customerId . '"');
            /** Since v2.2.2 */
            $customerData = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'customer` WHERE `id_customer`='.$customerId);
            $vatnr = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `vat_number` FROM '._DB_PREFIX_.'address WHERE `id_address`="'.(int)$id_address.'"');
            if ($customerId && ((int)Configuration::get('VALIDATEVATNUMBER_USERSNOTIFY') == 1)) {
                $this->sendNotificationUser($customerData[0]['email'], $customerData[0]['firstname'], $customerData[0]['lastname'], $vatnr, 'vataccepted');
            }
            Tools::redirectAdmin($redirect);
        } else if (!empty(Tools::getValue('reqstatus')) && Tools::getValue('reqstatus') == 2) {
            $id_address = Tools::getValue('id_address');
            Db::getInstance()->update('validatevatnumber', array(
                'vat_number_status' => (int)0,
            ), '`id_address` = "' . (int)$id_address . '"');
            $customerId = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_customer` FROM '._DB_PREFIX_.'address WHERE `id_address`="'.(int)$id_address.'"');
            $customerData = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'customer` WHERE `id_customer`='.(int)$customerId);
            $vatnr = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `vat_number` FROM '._DB_PREFIX_.'address WHERE `id_address`="'.(int)$id_address.'"');
            if ($customerId && ((int)Configuration::get('VALIDATEVATNUMBER_USERSNOTIFY') == 1)) {
                $this->sendNotificationUser($customerData[0]['email'], $customerData[0]['firstname'], $customerData[0]['lastname'], $vatnr, 'vatdenied');
            }
            Tools::redirectAdmin($redirect);
        }
        if (!empty(Tools::getValue('updateUsersVAT')) && Tools::getValue('updateUsersVAT')) {
            $customersVats = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT DISTINCT `vat_number` FROM `'._DB_PREFIX_.'address`
                                                                                        WHERE `vat_number` <> "" AND  `id_address` NOT IN (SELECT `id_address` FROM `'._DB_PREFIX_.'validatevatnumber` )');
            $customersVats = array_map(function ($row) {
                return $row['vat_number'];
            }, $customersVats);
            $customersIDs = array();
            $errors = array();
            $validatedVats = array();
            $deniedVats = array();
            foreach ($customersVats as $customerVat) {
                $vat_number_error = Module::getInstanceByName('validatevatnumber')->verifyVatNumberOnline($customerVat);
                if ($vat_number_error === true) {
                    $errors[$customerVat] = $vat_number_error;
                    if ($errors[$customerVat] === true) {
                        $customersAddresses = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `id_address` FROM `' . _DB_PREFIX_ . 'address` WHERE `vat_number`="'.pSQL($customerVat).'"');

                        foreach ($customersAddresses as $addr) {
                            $customersIDs[] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'address` WHERE `id_address`=' . (int)$addr['id_address']);
                        }

                        foreach ($customersIDs as $customerID) {
                            Db::getInstance()->update('customer', array(
                                'id_default_group' => Configuration::get('VALIDATEVATNUMBER_ACCEPTED_GROUP'),
                            ), '`id_customer` = "' . pSQL($customerID) . '"');
                            $validatedVats[] = $customerVat;
                        }
                        $this->vatConfirmation[] = $this->l(sprintf('VAT: %s has been validated', $customerVat));
                    }
                } else {
                    $deniedVats[] = $customerVat;
                    $this->vatErrors[] = $this->l(sprintf('VAT: %s has been denied', $customerVat));
                }
            }
            foreach ($validatedVats as $validVat) {
                $validVatAddr = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT `id_address` FROM `' . _DB_PREFIX_ . 'address` WHERE `vat_number`="'.pSQL($validVat).'"');
                foreach ($validVatAddr as $va) {
                    $checkVat = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_address` FROM `' . _DB_PREFIX_ . 'validatevatnumber` WHERE `id_address`=' . $va['id_address']);
                    if (!$checkVat) {
                        Db::getInstance()->insert('validatevatnumber', array(
                            'id_address' => $va['id_address'],
                            'vat_number_status' => (int)1,
                        ));
                    }
                }
            }
            foreach ($deniedVats as $deniedVat) {
                $deniedVatAddr = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT `id_address` FROM `' . _DB_PREFIX_ . 'address` WHERE `vat_number`="'.pSQL($deniedVat).'"');
                foreach ($deniedVatAddr as $da) {
                    $checkVatD = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_address` FROM `' . _DB_PREFIX_ . 'validatevatnumber` WHERE `id_address`=' . (int)$da['id_address']);
                    if (!$checkVatD) {
                        Db::getInstance()->insert('validatevatnumber', array(
                            'id_address' => (int)$da['id_address'],
                            'vat_number_status' => (int)0,
                        ));
                    }
                }
            }

            if (empty($this->vatConfirmation)) {
                $this->vatConfirmation[] = $this->l('All VATs validated already ');
            }
            $this->context->smarty->assign(array(
                'vatConfirmation' => $this->vatConfirmation,
                'vatErrors' => $this->vatErrors,
            ));
        }
        parent::init();
    }

    public function renderList()
    {
        $this->setListFields();
        $this->setListQuery();
        $display = parent::renderList();
        $tabs = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'validatevatnumber/views/templates/admin/configureController.tpl');
        return $tabs . $display;
    }

    public function renderView()
    {
        $id_address = Tools::getValue('id_address');
        $id_lang = Context::getContext()->language->id;
        $address = new Address($id_address);

        $check_vat_number_list = Db::getInstance()->getRow('
                        SELECT * FROM `'._DB_PREFIX_.'validatevatnumber` a
                        LEFT JOIN `'._DB_PREFIX_.'address` b ON a.id_address = b.id_address WHERE a.id_address = '. (int)$id_address);

        $tpl = $this->context->smarty->createTemplate(dirname(__FILE__).'/../../views/templates/admin/validatevatnumberlist/helpers/view/view.tpl');
        $tpl->assign(array(
            'address' => $address,
            'vat_number_status' => (int)$check_vat_number_list['vat_number_status'],
            'id_lang' => (int)$id_lang,
        ));
        return $tpl->fetch();
    }

    public function setListFields()
    {
        $this->fields_list = array(
            'id_address' => array(
                'title' => $this->module->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'search' => true,
                'filter_type' => 'int',
            ),
            'firstname' => array(
                'title' => $this->module->l('Firstname'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'havingFilter' => false,
            ),
            'lastname' => array(
                'title' => $this->module->l('Lastname'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'havingFilter' => false,
            ),
            'alias' => array(
                'title' => $this->module->l('Address Alias'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'havingFilter' => false,
            ),
            'company' => array(
                'title' => $this->module->l('Company Name'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'havingFilter' => false,
            ),
            'vat_number' => array(
                'title' => $this->module->l('VAT Number'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'havingFilter' => false,
            ),
            'vat_number_status' => array(
                'title' => $this->module->l('Status'),
                'align' => 'text-right',
                'callback' => 'updateStatus',
                'hint' => $this->module->l('VAT Number verified/unverified.'),
//                'filter' => false,
            ),
        );
    }

    protected function setListQuery()
    {
        $this->identifier = 'id_address';
        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'validatevatnumber` ad ON (ad.`id_address` = a.`id_address`)';
        $this->_select = '
            a.`firstname`, a.`id_customer`, a.`alias` , a.`lastname`, a.`vat_number`, ad.`vat_number_status`
        ';
        $this->_where = 'AND a.`vat_number` <> ""';
        $this->addListQueryFilters();
    }

    protected function addListQueryFilters()
    {
        if (!Tools::isSubmit('submitFilter')) {
            return;
        }
        $prefix = $this->prefix;

        $fields = array_map(function ($field) use ($prefix) {
            return $prefix . $field;
        }, array_keys($this->fields_list));
        foreach ($fields as $field) {
            if (($value = Tools::getValue($field)) == '') {
                continue;
            }
            switch ($field) {
                case $this->prefix.'id_address':
                    $this->_where .= ' and a.`id_address` = '. (int)$value;
                    break;
                case $this->prefix . 'firstname':
                    $this->_where .= ' and a.`firstname` like "%' . pSQL($value) . '%"';
                    break;
                case $this->prefix . 'lastname':
                    $this->_where .= ' and a.`lastname` like "%' . pSQL($value) . '%"';
                    break;
                case $this->prefix . 'alias':
                    $this->_where .= ' and a.`alias` like "%' . pSQL($value) . '%"';
                    break;
                case $this->prefix . 'company':
                    $this->_where .= ' and a.`company` like "%' . pSQL($value) . '%"';
                    break;
                case $this->prefix . 'vat_number':
                    $this->_where .= ' and a.`vat_number` like "%' . pSQL($value) . '%"';
                    break;
                case $this->prefix . 'newsletter_date_add':
                    $this->_where .= !is_array($value) || $value[0] == '' || $value[1] == ''
                        ? '' : ' and o.date_add between "' . pSQL($value[0]) . '" and "' . pSQL($value[1]) . '"';
                    break;
            }
        }
    }

    /**
     * Display buttons in renderList
     * @param $id
     * @param $row
     * @return mixed
     */
    public function updateStatus($id, $row)
    {
        static $links = array();

        $id_address = (int)$row['id_address'];
        $status = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `vat_number_status` FROM '._DB_PREFIX_.'validatevatnumber WHERE `id_address`="'.(int)$id_address.'"');
        $link = isset($links[$id_address])
            ? $links[$id_address]
            : $links[$id_address] =  $this->context->link->getAdminLink('AdminValidateVatNumberList', true)
                .'&id_address='.$id_address.'&reqstatus=1';
        $deny_request = $this->context->link->getAdminLink('AdminValidateVatNumberList', true)
            .'&id_address='.$id_address.'&reqstatus=2';
        $this->context->smarty->assign(array(
            'id_address' => $id,
            'accept_request' => $link,
            'deny_request' => $deny_request,
            'status' => $status
        ));
        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'/validatevatnumber/views/templates/admin/status.tpl'
        );
    }

    /**
     * Notify users when vat has been verified
     * @param $email
     * @param $firstname
     * @param $lastname
     * @return mixed
     */
    public function sendNotificationUser($email, $firstname, $lastname, $vatnr, $template)
    {
        $mailParams = array(
            '{firstname}' => $firstname,
            '{lastname}' => $lastname,
            '{email}' => $email,
            '{vatnr}' => $vatnr,
        );
        $sent = Mail::Send(
            $this->context->language->id,
            $template,
            "Your address's VAT number has been verified",
            $mailParams,
            $email,
            $firstname,
            null,
            null,
            null,
            null,
            dirname(__FILE__) . "/../../mails/"
        );
        return $sent;
    }
}
