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

class UpdateCatalog extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'updatecatalog';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'José Fernández';
        $this->need_instance = 1;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Actualizar Catálogo de Precios');
        $this->description = $this->l('Actualiza el catálogo de precios');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        if (!parent::install()) {
            return false;
        }
    
        // Crear la tabla en la base de datos
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'updatecatalog` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NULL,
            `position` INT(4) NULL,
            `active` BOOLEAN NOT NULL DEFAULT 1,
            PRIMARY KEY (`id`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
    
        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
    
        // Registrar hooks necesarios
        return $this->registerHook('displayBackOfficeHeader') &&
               $this->registerHook('header');
    }
    

    public function uninstall()
    {
        return parent::uninstall();
    }


    // Crear el formulario con el botón "Actualizar Colecciones"
    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->submit_action = 'submitUpdateCatalogModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        return $helper->generateForm(array(
            array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Listado de Colecciones'),
                        'icon' => 'icon-cogs',
                    ),
                    'submit' => array(
                        'title' => $this->l('Actualizar Colecciones'),
                        'class' => 'btn btn-primary'
                    )
                ),
            )
        ));
    }

    // Crear la tabla para mostrar las colecciones
    protected function renderTable()
    {
        // Definir las columnas de la tabla
        $fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'width' => 100,
                'align' => 'center',
                'orderby' => true
            ),
            'name' => array(
                'title' => $this->l('Nombre'),
                'width' => 150,
                'orderby' => true
            ),
            'position' => array(
                'title' => $this->l('Posición'),
                'width' => 80,
                'align' => 'center',
                'orderby' => true
            ),
            'active' => array(
                'title' => $this->l('Activa'),
                'width' => 50,
                'align' => 'center',
                'type' => 'bool',
                'orderby' => true
            ),
        );
    
        // Capturar el orden y dirección desde la URL
        $orderBy = Tools::getValue('updatecatalogOrderby'); 
        $orderWay = Tools::getValue('updatecatalogOrderway'); 
            // Capturar los valores de los filtros
        $searchId = Tools::getValue('updatecatalogFilter_id');
        $searchName = Tools::getValue('updatecatalogFilter_name');
        $searchPosition = Tools::getValue('updatecatalogFilter_position');
        $searchActive = Tools::getValue('updatecatalogFilter_active');

        // Validar que los valores sean seguros
        $allowedFields = ['id', 'name', 'position', 'active'];
        if (!in_array($orderBy, $allowedFields)) {
            $orderBy = 'id';
        }
        if ($orderWay !== 'asc' && $orderWay !== 'desc') {
            $orderWay = 'ASC';
        }

        // Modificar la consulta SQL con ordenación y filtros
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'updatecatalog` WHERE 1';

        // Aplicar los filtros
        if ($searchId) {
            $sql .= ' AND `id` LIKE "%' . pSQL($searchId) . '%"';
        }
        if ($searchName) {
            $sql .= ' AND `name` LIKE "%' . pSQL($searchName) . '%"';
        }
        if ($searchPosition) {
            $sql .= ' AND `position` LIKE "%' . pSQL($searchPosition) . '%"';
        }
        if ($searchActive !== '') {
            $sql .= ' AND `active` = ' . (int)$searchActive;
        }

        // Modificar la consulta SQL con ordenación dinámica
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'updatecatalog` ORDER BY `' . $orderBy . '` ' . $orderWay .'';

       
        $collections = Db::getInstance()->executeS($sql);
        
        // Crear el helper para la tabla
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id';
        $helper->table = 'updatecatalog';
        $helper->list_id = 'updatecatalog';
        $helper->title = $this->l('Listado de Colecciones');
        $helper->actions = ['edit']; // Agregar botón de edición
        $helper->module = $this;
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->orderBy = $orderBy;
        $helper->orderWay = $orderWay;
    
        // Generar la tabla
        return $helper->generateList($collections, $fields_list);
    }
    
    public function getContent()
    {
        $output = '<h2>' . $this->l('Listado de Colecciones') . '</h2>';
    
        // Verificar si se está editando una colección
        if (Tools::getValue('id')) {
            if (Tools::isSubmit('submitEditCollection')) {
                $output .= $this->processEditCollection((int)Tools::getValue('id'));
            }
            $output .= $this->renderEditForm((int)Tools::getValue('id'));
        } else {
            // Mostrar el listado y el botón de actualización
            if (Tools::isSubmit('update_collections')) {
                $output .= $this->processUpdateCollections(); 
            }
            $output .= '<form method="post" action="">
                            <button type="submit" name="update_collections" class="btn btn-primary">
                                ' . $this->l('Actualizar Colecciones') . '
                            </button>
                        </form><br>';
            $output .= $this->renderTable();
        }
    
        return $output;
    }
    
    

    public function processUpdateCollections()
    {
        $sql = 'SELECT fv.`id_feature_value`, `value`
                FROM `' . _DB_PREFIX_ . 'feature_value` fv
                JOIN `' . _DB_PREFIX_ . 'feature_value_lang` fvl ON fv.`id_feature_value` = fvl.`id_feature_value`
                WHERE fv.`id_feature` = 57 AND fvl.`id_lang` = 1';
        
        $collections = Db::getInstance()->executeS($sql);

        if (!$collections) {
            return $this->displayError($this->l('No se encontraron colecciones.'));
        }

        foreach ($collections as $collection) {
            $idFeatureValue = (int) $collection['id_feature_value'];
            $name = pSQL($collection['value']);

            // Comprobar si ya existe en updatecatalog
            $exists = Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'updatecatalog` WHERE `id` = ' . $idFeatureValue);

            if ($exists) {
                // Si existe, actualizar el nombre
                Db::getInstance()->update('updatecatalog', [
                    'name' => $name,
                ], '`id` = ' . $idFeatureValue);
            } else {
                // Si no existe, insertarlo
                Db::getInstance()->insert('updatecatalog', [
                    'id' => $idFeatureValue,
                    'name' => $name,
                    'position' => 1,
                    'active' => 1
                ]);
            }
        }

        // Redirigir para actualizar la página
        Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'));
    }

    public function renderEditForm($id)
    {
        // Obtener los datos de la colección a editar
        $collection = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'updatecatalog` WHERE `id` = ' . (int)$id);
        
        if (!$collection) {
            return $this->displayError($this->l('Colección no encontrada.'));
        }

 
        // Enlace para volver al listado con token
        $back_url = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules');



        // Crear el formulario de edición
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->submit_action = 'submitEditCollection';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        
        $helper->fields_value['id'] = $collection['id'];
        $helper->fields_value['name'] = $collection['name'];  // No editable
        $helper->fields_value['position'] = $collection['position'];
        $helper->fields_value['active'] = $collection['active'];

        return $helper->generateForm(array(
            array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Editar Colección'),
                        'icon' => 'icon-cogs',
                    ),
                    'input' => array(
                        array(
                            'type' => 'text',
                            'label' => $this->l('ID'),
                            'name' => 'id',
                            'readonly' => true,
                            'value' => $collection['id'],
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Nombre'),
                            'name' => 'name',
                            'readonly' => true,
                            'value' => $collection['name'],
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Posición'),
                            'name' => 'position',
                            'value' => $collection['position'],
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Activa'),
                            'name' => 'active',
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Sí')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                ),
                            ),
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Guardar cambios'),
                        'class' => 'btn btn-primary',
                    ),
                    'buttons' => array(
                        'back' => array(
                            'title' => $this->l('Volver al listado'),
                            'href' => $back_url,
                            'class' => 'btn btn-default',
                            'icon' => 'process-icon-back'
                        )
                    )
                ),
            )
        ));
    }

    public function processEditCollection($id)
    {
        // Recuperar los valores enviados desde el formulario
        $position = (int)Tools::getValue('position');
        $active = (int)Tools::getValue('active');

    
        // Actualizar los datos en la base de datos
        $result = Db::getInstance()->update('updatecatalog', [
            'position' => $position,
            'active' => $active,
        ], '`id` = ' . (int)$id);
    
        if ($result) {
            // Mostrar mensaje de éxito
            return $this->displayConfirmation($this->l('Colección actualizada correctamente.'));
        } else {
            // Mostrar mensaje de error
            return $this->displayError($this->l('Error al actualizar la colección.'));
        }
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


}
