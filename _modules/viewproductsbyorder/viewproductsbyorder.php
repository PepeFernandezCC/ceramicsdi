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

class viewproductsbyorder extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'viewproductsbyorder';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'José Fernández';
        $this->need_instance = 1;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Ver Productos por Pedidos');
        $this->description = $this->l('Muestra por producto cuantos se han vendido por número de pedidos');

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
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'viewproductsbyorder` (
            `id_product` INT(11) NOT NULL,
            `id_combination` INT(11) NOT NULL,
            `reference` VARCHAR(255) NULL,
            `name` VARCHAR(255) NULL,
            `sample` BOOLEAN NOT NULL DEFAULT 0,
            `orders` INT(5) NULL,
            `quantity` INT(5) NULL,
            `media` INT(5) NULL

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
        $helper->submit_action = 'submitViewProductsByOrderModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        return $helper->generateForm(array(
            array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Listado de Productos por Pedidos'),
                        'icon' => 'icon-cogs',
                    ),
                    'submit' => array(
                        'title' => $this->l('Actualizar Listado'),
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
            'id_product' => array(
                'title' => $this->l('ID'),
                'width' => 100,
                'align' => 'center',
                'orderby' => true
            ),
            'id_combination' => array(
                'title' => $this->l('Combinación'),
                'width' => 100,
                'align' => 'center',
                'orderby' => true
            ),
            'reference' => array(
                'title' => $this->l('Referencia'),
                'width' => 150,
                'orderby' => true
            ),
            'name' => array(
                'title' => $this->l('Nombre'),
                'width' => 150,
                'orderby' => true
            ),
            'sample' => array(
                'title' => $this->l('Muestra'),
                'width' => 50,
                'align' => 'center',
                'type' => 'bool',
                'orderby' => true
            ),
            'orders' => array(
                'title' => $this->l('Pedidos'),
                'width' => 80,
                'align' => 'center',
                'orderby' => true
            ),
            'quantity' => array(
                'title' => $this->l('Cantidad'),
                'width' => 80,
                'align' => 'center',
                'orderby' => true
            ),
            'media' => array(
                'title' => $this->l('Media'),
                'width' => 80,
                'align' => 'center',
                'orderby' => true
            ),
        );
    
        // Capturar el orden y dirección desde la URL
        $orderBy = Tools::getValue('viewproductsbyorderOrderby'); 
        $orderWay = Tools::getValue('viewproductsbyorderOrderway'); 

            // Capturar los valores de los filtros
        $searchId = Tools::getValue('viewproductsbyorderFilter_id_product');
        $searchCombination = Tools::getValue('viewproductsbyorderFilter_id_combination');
        $searchName = Tools::getValue('viewproductsbyorderFilter_name');
        $searchReference = Tools::getValue('viewproductsbyorderFilter_reference');
        $searchOrders = Tools::getValue('viewproductsbyorderFilter_orders');
        $searchQuantity = Tools::getValue('viewproductsbyorderFilter_quantity');
        $searchSample = Tools::getValue('viewproductsbyorderFilter_sample');
        $searchMedia = Tools::getValue('viewproductsbyorderFilter_media');

        // Validar que los valores sean seguros
        $allowedFields = ['id_product', 'id_combination', 'name', 'reference', 'orders', 'quantity', 'sample', 'media'];
        if (!in_array($orderBy, $allowedFields)) {
            $orderBy = 'id_product';
        }
        if ($orderWay !== 'asc' && $orderWay !== 'desc') {
            $orderWay = 'ASC';
        }

        // Modificar la consulta SQL con ordenación y filtros
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'viewproductsbyorder` WHERE 1';

        // Aplicar los filtros
        if ($searchId) {
            $sql .= ' AND `id_product` LIKE "%' . pSQL($searchId) . '%"';
        }
        if ($searchCombination) {
            $sql .= ' AND `id_combination` LIKE "%' . pSQL($searchCombination) . '%"';
        }
        if ($searchReference) {
            $sql .= ' AND `reference` LIKE "%' . pSQL($searchReference) . '%"';
        }
        if ($searchName) {
            $sql .= ' AND `name` LIKE "%' . pSQL($searchName) . '%"';
        }
        if ($searchQuantity) {
            $sql .= ' AND `quantity` LIKE "%' . pSQL($searchQuantity) . '%"';
        }
        if ($searchOrders) {
            $sql .= ' AND `orders` LIKE "%' . pSQL($searchOrders) . '%"';
        }
        if ($searchMedia) {
            $sql .= ' AND `media` LIKE "%' . pSQL($searchMedia) . '%"';
        }
        if ($searchSample !== '') {
            $sql .= ' AND `sample` = ' . (int)$searchSample;
        }

        // Modificar la consulta SQL con ordenación dinámica
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'viewproductsbyorder` ORDER BY `' . $orderBy . '` ' . $orderWay .'';

       
        $collections = Db::getInstance()->executeS($sql);
        
        // Crear el helper para la tabla
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_product';
        $helper->table = 'viewproductsbyorder';
        $helper->list_id = 'viewproductsbyorder';
        $helper->title = $this->l('Listado de Productos en Pedidos');
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
        $output = '<h2>' . $this->l('Listado de Productos Vendidos') . '</h2>';
    
        // Mostrar el formulario de fecha
        $output .= '<form method="post" action="">
                        <div>
                            <div style="margin-top:25px; margin-bottom: 20px">
                                <label style="margin-left: 18px" for="fecha">VER VENTAS DESDE:</label>
                                <input type="date" class="custom-date-input" id="fecha" name="fecha_inicio" value="2024-01-01">
                            </div>
                            <div>
                                <button type="submit" name="update_table" class="btn btn-primary">' . $this->l('Actualizar Listado') . '</button>
                                <button type="submit" name="export_csv" class="btn btn-success">' . $this->l('Exportar a CSV') . '</button>
                            </div>
                        <div>
                    </form><br>';
    
        // Si se presiona el botón de actualizar tabla, procesar la actualización
        if (Tools::isSubmit('update_table')) {
            $output .= $this->processUpdateTable();
        }
    
        // Si el botón de exportar es presionado, exportar los datos
        if (Tools::isSubmit('export_csv')) {
            $this->exportCSV();
        }
    
        // Mostrar la tabla de datos
        $output .= $this->renderTable();
    
        return $output;
    }
    
    public function exportCSV()
    {
        // Obtener los datos de la tabla
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'viewproductsbyorder`';
        $collections = Db::getInstance()->executeS($sql);
    
        // Verificar si se obtuvieron datos
        if (empty($collections)) {
            die('No se encontraron datos para exportar.');
        }
    
        // Definir las cabeceras del archivo CSV
        $headers = ['ID Producto', 'ID Combinación', 'Referencia', 'Nombre', 'Muestra', 'Pedidos', 'Cantidad', 'Media'];
    
        // Iniciar el output del archivo CSV
        $csvData = '';
    
        // Añadir BOM para UTF-8
        $csvData .= "\xEF\xBB\xBF";  // Esto es el BOM para UTF-8
    
        // Añadir las cabeceras al archivo CSV
        $csvData .= implode(';', $headers) . "\n";
    
        // Añadir los datos de la tabla al archivo CSV
        foreach ($collections as $row) {
            $data = [
                $row['id_product'],
                $row['id_combination'],
                $row['reference'],
                $row['name'],
                $row['sample'] == 1 ? 'Sí' : 'No',  // Convertir el booleano de muestra en texto
                $row['orders'],
                $row['quantity'],
                $row['media']
            ];
    
            // No es necesario utilizar utf8_encode
            $csvData .= implode(';', $data) . "\n";
        }
    
        // Forzar la descarga del archivo CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="productos_pedidos_' . date('Y-m-d_H-i-s') . '.csv"');
        
        // Enviar los datos CSV
        echo $csvData;
    
        // Finalizar el script
        exit();
    }
    
    

    private function getProductsSpeciAlarray($fechaInicio)
    {
        // Modificar la consulta para usar la fecha proporcionada por el usuario
        $sql = '
            SELECT 
                p.id_product, 
                p.reference AS main_reference, 
                pl.name AS product_name, 
                pa.id_product_attribute, 
                pa.reference AS combination_reference, 
                pa.default_on,
                COALESCE(COUNT(DISTINCT od.id_order), 0) AS n_pedidos,
                COALESCE(SUM(od.product_quantity), 0) AS cantidad,
                -- Redondeamos la media a un número entero
                CASE 
                    WHEN COUNT(DISTINCT od.id_order) > 0 THEN ROUND(SUM(od.product_quantity) / COUNT(DISTINCT od.id_order))
                    ELSE 0
                END AS media
            FROM `' . _DB_PREFIX_ . 'product` p
            INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl 
                ON p.id_product = pl.id_product AND pl.id_lang = 1
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa 
                ON p.id_product = pa.id_product
            LEFT JOIN `' . _DB_PREFIX_ . 'order_detail` od 
                ON (pa.id_product_attribute IS NOT NULL AND od.product_attribute_id = pa.id_product_attribute) 
                OR (pa.id_product_attribute IS NULL AND od.product_id = p.id_product)
            LEFT JOIN `' . _DB_PREFIX_ . 'orders` o 
                ON od.id_order = o.id_order
            WHERE p.active = 1
            AND o.date_add BETWEEN "' . pSQL($fechaInicio) . ' 00:00:00" AND NOW()
            GROUP BY p.id_product, pa.id_product_attribute
        ';
        
        $products = Db::getInstance()->executeS($sql);
    
        if (!$products) {
            return $this->displayError($this->l('No se encontraron productos activos.'));
        }
    
        $products_array = [];
    
        foreach ($products as $product) {
            $products_array[] = [
                'id_producto' => $product['id_product'],
                'id_combinacion' => $product['id_product_attribute'] ?? null,
                'referencia' => $product['id_product_attribute'] ? $product['combination_reference'] : $product['main_reference'],
                'nombre' => $product['product_name'],
                'muestra' => isset($product['default_on']) && $product['default_on'] == 1 ? 0 : 1,
                'cantidad' => (int) $product['cantidad'],
                'n_pedidos' => (int) $product['n_pedidos'],
                'media' => (int) $product['media'] // Redondeamos la media a entero
            ];
        }
    
        return $products_array;
    }
    
    
    private function insertProducts($productArray) {
        foreach ($productArray as $product) {
            $id_product = (int) $product['id_producto'];
            $id_combination = isset($product['id_combinacion']) ? (int) $product['id_combinacion'] : 0;
            $reference = pSQL($product['referencia']);
            $name = pSQL($product['nombre']);
            $sample = (int) $product['muestra'];
            $orders = (int) $product['n_pedidos'];
            $quantity = (int) $product['cantidad'];
            $media = (int) $product['media']; // Aseguramos que la media es un entero
    
            Db::getInstance()->insert('viewproductsbyorder', [
                'id_product' => $id_product,
                'id_combination' => $id_combination,
                'reference' => $reference,
                'name' => $name,
                'sample' => $sample,
                'orders' => $orders,
                'quantity' => $quantity,
                'media' => $media // Guardamos la media como entero
            ]);
            
        }
    }
    
    public function processUpdateTable()
    {
        // Obtener la fecha seleccionada por el usuario
        $fechaInicio = Tools::getValue('fecha_inicio', '2024-01-01'); // Si no se selecciona, usar la fecha por defecto
    
        // Borrar todos los registros de la tabla antes de insertar los nuevos
        $sqlDelete = 'DELETE FROM `' . _DB_PREFIX_ . 'viewproductsbyorder`';
        Db::getInstance()->execute($sqlDelete);
    
        // Obtener los nuevos datos basados en la fecha seleccionada
        $productsArray = $this->getProductsSpeciAlarray($fechaInicio);
    
        // Insertar los nuevos productos
        $this->insertProducts($productsArray);
    
        // Redirigir para actualizar la página
        Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'));
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
