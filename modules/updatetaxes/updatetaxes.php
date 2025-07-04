<?php
/**
* 2007-2024 PrestaShop
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
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class UpdateTaxes extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'updatetaxes';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'José Fernández';
        $this->need_instance = 1;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Actualizar Impuestos');
        $this->description = $this->l('Actualiza Impuestos de Fras. Adeo');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('UPDATETAXES_LIVE_MODE', false);

        //include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayProductExtraInfo') &&
            $this->registerHook('actionCartSave');
    }

    public function uninstall()
    {
        Configuration::deleteByName('UPDATETAXES_LIVE_MODE');

        //include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    
    public function getContent()
    {
        
        if (((bool)Tools::isSubmit('submitUpdateTaxesModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        return $this->renderForm();
    }
    

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitUpdateTaxesModule';
        
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        //$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
       // $helper->token = Tools::getAdminTokenLite('UpdateTaxes');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Parámetros'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'desc' => $this->l('Id del pedido'),
                        'name' => 'UPDATETAXES_ORDER_ID',
                        'label' => $this->l('ID Pedido'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Selector de Impuesto'),
                        'name' => 'UPDATETAXES_TAX_ID',
                        'desc' => $this->l('Selecciona el impuesto que deseas aplicar.'),
                        'options' => array(
                            'query' => array(
                                array('id_option' => 37, 'name' => 'SIN IMPUESTOS'),
                                array('id_option' => 1, 'name' => 'ESPAÑA 21%'),
                                array('id_option' => 5, 'name' => 'BELGICA 21%'),
                                array('id_option' => 13, 'name' => 'FRANCIA 20%'),
                                array('id_option' => 19, 'name' => 'ITALIA 22%'),
                                array('id_option' => 14, 'name' => 'INGLATERRA 20%'),
                                array('id_option' => 26, 'name' => 'PORTUGAL 23%'),
                                array('id_option' => 27, 'name' => 'RUMANIA 19%'),
                            ),
                            'id' => 'id_option',
                            'name' => 'name',
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Actualizar'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'UPDATETAXES_ORDER_ID' => Tools::getValue('UPDATETAXES_ORDER_ID', ''), // Valor predeterminado vacío
            'UPDATETAXES_TAX_ID' => Tools::getValue('UPDATETAXES_TAX_ID', 13), // Impuesto predeterminado: 13
        );
    }

    /**
     * Save form data.
     */
/**
 * Procesa el formulario y actualiza los impuestos.
 */
public function postProcess()
{
    // Verifica si se envió el formulario
    if (Tools::isSubmit('submitUpdateTaxesModule')) {

        // Captura los valores enviados desde el formulario
        $idOrder = (int)Tools::getValue('UPDATETAXES_ORDER_ID');
        $taxId = (int)Tools::getValue('UPDATETAXES_TAX_ID');

        // Verificación básica de los parámetros
        if (!$idOrder || !$taxId) {
            $this->context->controller->errors[] = $this->l('Faltan parámetros: ID del pedido o ID del impuesto.');
            return;
        }

        // Lógica para asignar un grupo de reglas de impuestos
        $idTaxRulesGroup = 33; 
        $taxName = $this->getTaxNameById($taxId); 

        try {
            if ($taxId == 37) { //QUITAR IMPUESTOS
                $idTaxRulesGroup = 0;   
                $taxName = '';
                // 1. Actualizar impuesto de transporte a 0
                $query_ps_order = 'UPDATE `'._DB_PREFIX_.'orders` 
                        SET `carrier_tax_rate` = "0.000",
                        `total_shipping_tax_incl` = `total_shipping_tax_excl`
                        WHERE `id_order` = '.(int)$idOrder;
                Db::getInstance()->execute($query_ps_order);

            }
            // 1. Actualizar ps_order_detail
            $query_ps_order_detail = 'UPDATE `'._DB_PREFIX_.'order_detail` 
                     SET `id_tax_rules_group` = '.(int)$idTaxRulesGroup.',
                         `tax_name` = "'.$taxName.'"
                     WHERE `id_order` = '.(int)$idOrder;
            Db::getInstance()->execute($query_ps_order_detail);

            // 2. Obtener los id_order_detail
            $query = 'SELECT `id_order_detail` 
                     FROM `'._DB_PREFIX_.'order_detail`
                     WHERE `id_order` = '.(int)$idOrder;
            $orderDetails = Db::getInstance()->executeS($query);

            // 3. Actualizar ps_order_detail_tax
            foreach ($orderDetails as $orderDetail) {
                $idOrderDetail = (int)$orderDetail['id_order_detail'];
                $query_detail_tax = 'UPDATE `'._DB_PREFIX_.'order_detail_tax`
                         SET `id_tax` = '.(int)$taxId.'
                         WHERE `id_order_detail` = '.(int)$idOrderDetail;
                Db::getInstance()->execute($query_detail_tax);
            }

            // Mensaje de éxito
            $this->context->controller->confirmations[] = $this->l('Impuesto actualizado correctamente.');
        } catch (Exception $e) {
            $this->context->controller->errors[] = $this->l('Error al actualizar los impuestos: ').$e->getMessage();
        }
    }
}

/**
 * Función auxiliar para obtener el nombre del impuesto por su ID.
 */
private function getTaxNameById($taxId)
{
    $taxes = array(
        1 => 'IVA ES 21%',
        5 => 'TVA BE 21%',
        13 => 'TVA FR 20%',
        19 => 'IVA IT 22%',
        14 => 'VAT UK 20%',
        26 => 'IVA PT 23%',
        27 => 'TVA RO 19%',
        37 => 'VAT MT 0%'
    );

    return isset($taxes[$taxId]) ? $taxes[$taxId] : 'Tax';
}

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookActionCartSave()
    {
        /* Place your code here. */
    }

}
