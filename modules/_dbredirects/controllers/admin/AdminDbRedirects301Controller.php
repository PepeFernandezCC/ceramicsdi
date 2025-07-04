<?php
/**
 * 2007-2020 PrestaShop
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
 *  @author    DevBlinders <info@devblinders.com>
 *  @copyright 2007-2020 DevBlinders
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */


class AdminDbRedirects301Controller extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'dbredirects';
        $this->className = 'DbRedirect';
        $this->lang = false;
        $this->multishop_context = Shop::CONTEXT_ALL;

        parent::__construct();

        $this->toolbar_title = $this->l('Redirecciones 301');
        $this->fields_list = array(
            'id_dbredirects' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'width' => 30
            ),
            'url_antigua' => array(
                'title' => $this->trans('Url antigua', array(), 'Admin.Global'),
            ),
            'url_nueva' => array(
                'title' => $this->trans('Url nueva', array(), 'Admin.Global'),
            ),
            'date_add' => array(
                'title' => $this->trans('Fecha', array(), 'Admin.Global'),
            ),
            'active' => array(
                'title' => 'Activo',
                'active' => 'status',
                'type' => 'bool',
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'ajax' => true,
                'orderby' => false,
                'search' => true,
                'width' => 25,
            ),
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
    }

    public function initProcess()
    {
        if (Tools::getIsset('status'.$this->table))
        {
            DbRedirect::isToggleStatus((int)Tools::getValue('id_dbredirects'));
            return;
        }

        if(Tools::getIsset('import301')){
            $this->import301();
        }

        return parent::initProcess();
    }

    public function renderList()
    {
        $this->_where = 'AND a.`type` = 1';

        // removes links on rows
        $this->list_no_link = true;

        // adds actions on rows
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $list = parent::renderList();
        if($this->module->premium == 0){
            return $list;
        } else {
            $form_csv = DbRedirectsPremium::renderFormCSV301();
            return $list.$form_csv;
        }

    }

    public function renderView()
    {
        // adds actions on rows
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm()
    {

        // Sets the title of the toolbar
        $this->toolbar_title = $this->l('Redirección 301');

        $this->fields_value = array(
            'type' => 1,
        );

        // Sets the fields of the form
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Redirección 301'),
                'icon' => 'icon-pencil'
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_dbredirects'
                ),

                array(
                    'type' => 'hidden',
                    'name' => 'type'
                ),

                array(
                    'type' => 'text',
                    'label' => $this->l('Url antigua'),
                    'desc' => 'Debe ser la url sin el dominio, por ejemplo: /ruta-antigua-redireccion',
                    'name' => 'url_antigua',
                    'required' => true,
                    'lang' => false,
                ),

                array(
                    'type' => 'text',
                    'label' => $this->l('Url nueva'),
                    'desc' => 'Debe ser la url con el dominio, por ejemplo: https://www.midominio.com/ruta-antigua-redireccion',
                    'name' => 'url_nueva',
                    'required' => true,
                    'lang' => false,
                ),

                array(
                    'type' => 'switch',
                    'label' => $this->l('Activo'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                ),

            ),
        );


        $this->fields_form['submit'] = array(
            'title' => $this->trans('Save', array(), 'Admin.Actions'),
        );

        return parent::renderForm();
    }

    public function processAdd()
    {
        if (!($object = $this->loadObject(true))) {
            return;
        }

        $_POST['date_add'] = date('Y-m-d H:i:s');
        $url_antigua = Tools::getValue('url_antigua');
        $exists = DbRedirect::isRedirect(trim($url_antigua));
        if((int)$exists['id_dbredirects'] == 0) {
            parent::processAdd();
        } else {
            $this->context->controller->errors[] = $this->l( sprintf('La URL ya tiene un registro creado: %s', $url_antigua) );
        }

    }

    public function import301()
    {
        $num = 0;
        $file = $_FILES['file']['tmp_name'];
        $handle = false;
        if (is_file($file) && is_readable($file)) {
            $handle = fopen($file, 'r');
        }

        if (!$handle) {
            $this->errors[] = Tools::displayError('No se ha podido leer el fichero');
        } else {
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            if($ext != 'csv' && $ext != 'CSV') {
                $this->errors[] = Tools::displayError('El fichero no es un CSV');
            }
        }

        while (($line = fgetcsv($handle, 10000, ";")) !== FALSE) {

            if($num > 0) {

                // Comprobamos que hay datos en todos los campos
                $url_antigua = $line[0];
                $url_nueva = $line[1];
                $active = $line[2];
                if(empty($url_antigua) || empty($url_nueva) || empty($active)) {
                    $this->context->controller->errors[] = $this->l( sprintf('Error en el contenido de la fila %s, hay algun campo sin contenido o formato incorrecto', $num + 2) );
                    continue;
                }

                // Comprobamos la longitud de los campos
                if(strlen($url_antigua) > 512) {
                    $this->context->controller->errors[] = $this->l( sprintf('La URL antigua: %s es demasiado larga, no puede superar 512 caracteres', $url_antigua) );
                    continue;
                }
                if(strlen($url_nueva) > 512) {
                    $this->context->controller->errors[] = $this->l( sprintf('La URL nueva: %s es demasiado larga, no puede superar 512 caracteres', $url_nueva) );
                    continue;
                }

                $exists = DbRedirect::isRedirect(trim($url_antigua));
                if(isset($exists['id_dbredirects']) && $exists['id_dbredirects'] > 0) {
                    $this->context->controller->errors[] = $this->l( sprintf('La URL ya tiene un registro creado: %s', $url_antigua) );
                } else {
                    $redirect = new DbRedirect();
                    $redirect->type = 1;
                    $redirect->url_antigua = trim($url_antigua);
                    $redirect->url_nueva = trim($url_nueva);
                    $redirect->active = trim($active);
                    $redirect->add();
                }
            }
            $num++;
        }

    }

}