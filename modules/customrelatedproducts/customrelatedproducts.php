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

class CustomRelatedProducts extends Module
{
    protected $config_form = false;

    public function __construct() {

        $this->name = 'customrelatedproducts';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'José Fernández';
        $this->need_instance = 1;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Actualizar Productos Relacionados');
        $this->description = $this->l('Actualiza Los Productos Relacionados');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install() {

        if (!parent::install()) {
            return false;
        }
    
        // Crear la tabla en la base de datos
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'customrelatedproducts` (
            `id_product` INT(10) UNSIGNED NOT NULL,
            `id_product_related` INT(10) UNSIGNED NOT NULL,
            `position` INT(4) NULL DEFAULT 1,
            `active` BOOLEAN NOT NULL DEFAULT 1,
            PRIMARY KEY (`id_product`, `id_product_related`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        /*
        Db::getInstance()->insert('configuration', [
            'name' => 'PS_QUANTITY_RELATED_PRODUCTS',
            'value' => '10',
            'date_add' => date('Y-m-d H:i:s'),
            'date_upd' => date('Y-m-d H:i:s'),
        ]);
        */

        Configuration::updateValue('PS_QUANTITY_RELATED_PRODUCTS', 10); //agregar variable configuración optimizada
    
        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
    
        // Registrar hooks necesarios
        return $this->registerHook('displayBackOfficeHeader') &&
               $this->registerHook('header');
    }
    

    public function uninstall() {

        return parent::uninstall();
    }


    // Crear el formulario con el botón "Actualizar Colecciones"
    protected function renderForm() 
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->submit_action = 'submitCustomRelatedProducts';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        return $helper->generateForm(array(
            array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Listado de Productos'),
                        'icon' => 'icon-cogs',
                    ),
                    'submit' => array(
                        'title' => $this->l('Actualizar Productos'),
                        'class' => 'btn btn-primary'
                    )
                ),
            )
        ));
    }

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
            'reference' => array(
                'title' => $this->l('Referencia'),
                'width' => 80,
                'align' => 'center',
                'orderby' => true
            ),
            'name' => array(
                'title' => $this->l('Nombre'),
                'width' => 150,
                'orderby' => true
            )
        );

        // Capturar orden
        $orderBy = Tools::getValue('customrelatedproductsOrderby', 'id_product');
        $orderWay = Tools::getValue('customrelatedproductsOrderway', 'ASC');

        // Validar orden
        $allowedFields = ['id_product', 'name', 'reference', 'active'];
        if (!in_array($orderBy, $allowedFields)) {
            $orderBy = 'id_product';
        }
        if (!in_array(strtoupper($orderWay), ['ASC', 'DESC'])) {
            $orderWay = 'ASC';
        }

        // Capturar filtros
        $searchId = Tools::getValue('customrelatedproductsFilter_id_product');
        $searchName = Tools::getValue('customrelatedproductsFilter_name');
        $searchReference = Tools::getValue('customrelatedproductsFilter_reference');

        // Construir consulta
        $sql = '
            SELECT DISTINCT cr.id_product, p.reference, pl.name
            FROM ' . _DB_PREFIX_ . 'customrelatedproducts cr
            JOIN ' . _DB_PREFIX_ . 'product p ON cr.id_product = p.id_product
            JOIN ' . _DB_PREFIX_ . 'product_lang pl ON cr.id_product = pl.id_product
            WHERE pl.id_lang = ' . (int)$this->context->language->id;

        if ($searchId) {
            $sql .= ' AND cr.id_product LIKE "%' . pSQL($searchId) . '%"';
        }
        if ($searchName) {
            $sql .= ' AND pl.name LIKE "%' . pSQL($searchName) . '%"';
        }
        if ($searchReference) {
            $sql .= ' AND p.reference LIKE "%' . pSQL($searchReference) . '%"';
        }

        $sql .= ' ORDER BY ' . bqSQL($orderBy) . ' ' . pSQL($orderWay);

        // Obtener resultados
        $collections = Db::getInstance()->executeS($sql);

        // HelperList
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_product'; // Debe estar presente en los resultados
        $helper->table = 'customrelatedproducts';
        $helper->list_id = 'customrelatedproducts';
        $helper->title = $this->l('Listado de Productos Relacionados');
        $helper->actions = ['edit'];
        $helper->module = $this;
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->orderBy = $orderBy;
        $helper->orderWay = $orderWay;

        return $helper->generateList($collections, $fields_list);
    }

    public function getContent()
    {
        $output = '<h2>' . $this->l('Listado de Productos Relacionados') . '</h2>';

        // Detectar si venimos del listado con la acción editar (usado por HelperList)
        $idToEdit = Tools::getValue('id_product');

        if ($idToEdit) {
            if (Tools::isSubmit('submitUpdateRelatedProduct')) {
                $output .= $this->postProcess();
            }
            $output .= $this->renderEditForm((int)$idToEdit);
        } else {
            // Botón para actualizar relaciones automáticamente
            if (Tools::isSubmit('update_relateds')) {
                $output .= $this->processUpdateRelateds(); 
            }

            // Mostrar botón de actualización y tabla
            $output .= '<form method="post" action="">
                            <button type="submit" name="update_relateds" class="btn btn-primary">
                                ' . $this->l('Actualizar Productos Relacionados') . '
                            </button>
                        </form><br>';

            $output .= $this->renderTable();
        }

        // Mensajes de confirmación o error
        if (Tools::getValue('success')) {
            $output = $this->displayConfirmation($this->l('Colección actualizada correctamente.')) . $output;
        }
        if (Tools::getValue('error')) {
            $output = $this->displayError($this->l('Error al actualizar la colección.')) . $output;
        }

        return $output;
    }

    public function processUpdateRelateds() 
    {

        // Obtener todos los productos activos
        $productList = Db::getInstance()->executeS('SELECT id_product FROM `' . _DB_PREFIX_ . 'product` WHERE active = 1');
        $allProductIds = array_column($productList, 'id_product');

        // Productos que ya tienen relaciones
        $existingRelated = Db::getInstance()->executeS('SELECT DISTINCT id_product FROM `' . _DB_PREFIX_ . 'customrelatedproducts`');
        $existingIds = array_column($existingRelated, 'id_product');
        
        foreach ($allProductIds as $idProduct) {
            if (in_array($idProduct, $existingIds)) {
                continue;
            }

            $relatedIds = $this->findRelatedProducts($idProduct, $allProductIds);

            foreach ($relatedIds as $pos => $relatedId) {
                Db::getInstance()->insert('customrelatedproducts', [
                    'id_product' => $idProduct,
                    'id_product_related' => $relatedId,
                    'position' => $pos + 1,
                    'active' => 1,
                ]);
            }
        }

        Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'));
    }

    public function findRelatedProducts($idProduct, $allProductIds)
    {
        $idProduct = (int) $idProduct;
        $limit = (int) Configuration::get('PS_QUANTITY_RELATED_PRODUCTS');

        if ($limit <= 0) {
            return [];
        }

        $relatedIds = [];

        /*
        * 1. Productos relacionados por colección (feature = 57)
        *    Solo productos activos (p.active = 1)
        */
        $sqlCollection = '
            SELECT fp2.id_product 
            FROM ' . _DB_PREFIX_ . 'feature_product fp1
            INNER JOIN ' . _DB_PREFIX_ . 'feature_product fp2 
                ON fp1.id_feature_value = fp2.id_feature_value
            INNER JOIN ' . _DB_PREFIX_ . 'product p
                ON p.id_product = fp2.id_product
            WHERE fp1.id_feature = 57
                AND fp1.id_product = ' . $idProduct . '
                AND fp2.id_product != ' . $idProduct . '
                AND p.active = 1
            GROUP BY fp2.id_product
            LIMIT ' . $limit;

        $collectionProducts = Db::getInstance()->executeS($sqlCollection);

        foreach ($collectionProducts as $p) {
            if (count($relatedIds) >= $limit) {
                break;
            }

            $relatedId = (int) $p['id_product'];

            if (!in_array($relatedId, $relatedIds)) {
                $relatedIds[] = $relatedId;
            }
        }

        /*
        * 2. Productos TOP (feature = 69, value = 146347)
        *    Solo productos activos (p.active = 1)
        */
        if (count($relatedIds) < $limit) {
            $remaining = $limit - count($relatedIds);

            $sqlTop = '
                SELECT DISTINCT fp.id_product 
                FROM ' . _DB_PREFIX_ . 'feature_product fp
                INNER JOIN ' . _DB_PREFIX_ . 'product p
                    ON p.id_product = fp.id_product
                WHERE fp.id_feature = 69
                    AND fp.id_feature_value = 146347
                    AND fp.id_product != ' . $idProduct . '
                    AND p.active = 1
                LIMIT ' . (int) $remaining;

            $topProducts = Db::getInstance()->executeS($sqlTop);

            foreach ($topProducts as $p) {
                if (count($relatedIds) >= $limit) {
                    break;
                }

                $relatedId = (int) $p['id_product'];

                if (!in_array($relatedId, $relatedIds)) {
                    $relatedIds[] = $relatedId;
                }
            }
        }

        /*
        * 3. Productos aleatorios a partir de $allProductIds
        *    Filtrados por active = 1 y excluyendo ya seleccionados
        */
        if (count($relatedIds) < $limit && !empty($allProductIds)) {
            $remaining = $limit - count($relatedIds);

            // Normalizamos y limpiamos IDs
            $candidateIds = array_map('intval', (array) $allProductIds);

            // Quitamos el propio producto
            $candidateIds = array_diff($candidateIds, [$idProduct]);

            if (!empty($candidateIds)) {
                $inIds = implode(',', $candidateIds);

                $sqlRandom = '
                    SELECT p.id_product
                    FROM ' . _DB_PREFIX_ . 'product p
                    WHERE p.active = 1
                        AND p.id_product IN (' . $inIds . ')';

                // Evitamos repetir los que ya están en $relatedIds
                if (!empty($relatedIds)) {
                    $sqlRandom .= ' AND p.id_product NOT IN (' . implode(',', array_map('intval', $relatedIds)) . ')';
                }

                $sqlRandom .= '
                    ORDER BY RAND()
                    LIMIT ' . (int) $remaining;

                $randomProducts = Db::getInstance()->executeS($sqlRandom);

                foreach ($randomProducts as $p) {
                    if (count($relatedIds) >= $limit) {
                        break;
                    }

                    $relatedId = (int) $p['id_product'];

                    if (!in_array($relatedId, $relatedIds)) {
                        $relatedIds[] = $relatedId;
                    }
                }
            }
        }

        return $relatedIds;
    }

    public function renderEditForm($id)
    {
        // Obtener datos del producto
        $product = Db::getInstance()->getRow('
            SELECT p.id_product, p.reference, pl.name 
            FROM ' . _DB_PREFIX_ . 'product p 
            JOIN ' . _DB_PREFIX_ . 'product_lang pl ON p.id_product = pl.id_product 
            WHERE pl.id_lang = 1 
            AND p.id_product = ' . (int)$id
        );

        if (!$product) {
            return $this->displayError($this->l('Producto no encontrado.'));
        }

        // Obtener productos relacionados (incluyendo posición)
        $relatedProducts = Db::getInstance()->executeS('
            SELECT crp.id_product_related, crp.position
            FROM ' . _DB_PREFIX_ . 'customrelatedproducts crp 
            WHERE crp.id_product = ' . (int)$id . ' 
            ORDER BY crp.position ASC
        ');

        // Obtener todos los productos para el selector
        $allProducts = Db::getInstance()->executeS('
            SELECT p.id_product, p.reference, pl.name
            FROM ' . _DB_PREFIX_ . 'product p 
            JOIN ' . _DB_PREFIX_ . 'product_lang pl ON p.id_product = pl.id_product 
            WHERE pl.id_lang = 1
            ORDER BY pl.name ASC
        ');

        $productOptions = [];
        foreach ($allProducts as $prod) {
            if ($prod['id_product'] == $id) continue; // No autoseleccionar el mismo
            $productOptions[] = [
                'id' => $prod['id_product'],
                'name' => sprintf('[%d] %s', $prod['reference'], $prod['name']),
            ];
        }

        $inputs = [
            [
                'type' => 'text',
                'label' => $this->l('ID'),
                'name' => 'id_product',
                'readonly' => true,
                'class' => 'fixed-width-xxl', // pequeño ancho
            ],
            [
                'type' => 'text',
                'label' => $this->l('Referencia'),
                'name' => 'reference',
                'readonly' => true,
                'class' => 'fixed-width-xxl', // pequeño ancho
            ],
            [
                'type' => 'text',
                'label' => $this->l('Nombre'),
                'name' => 'name',
                'readonly' => true,
                'class' => 'fixed-width-xxl', // ancho moderado
            ],
        ];


        foreach ($relatedProducts as $index => $related) {
            $selectName = 'related_' . $index;
            $positionName = 'position_' . $index;

            // Construir HTML manual para que estén en línea
            $selectHtml = '<label>' . $this->l('Producto relacionado #' . ($index + 1)) . '</label>
            <div style="max-width:500px;display: flex; gap: 10px; align-items: center;">
                <select name="' . $selectName . '" class="chosen" style="width: 300px;">';

            foreach ($productOptions as $option) {
                $selected = $option['id'] == $related['id_product_related'] ? 'selected' : '';
                $selectHtml .= '<option value="' . (int)$option['id'] . '" ' . $selected . '>' . Tools::safeOutput($option['name']) . '</option>';
            }

            $selectHtml .= '</select>
                <input type="text" name="' . $positionName . '" value="' . (int)$related['position'] . '" class="fixed-width-sm" placeholder="' . $this->l('Pos.') . '"/>
            </div>';

            $inputs[] = [
                'type' => 'html',
                'name' => 'group_' . $index,
                'html_content' => $selectHtml,
            ];
        }


        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->submit_action = 'submitUpdateRelatedProduct';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        // Valores del formulario
        $helper->fields_value = [
            'id_product' => $product['id_product'],
            'reference' => $product['reference'],
            'name' => $product['name'],
        ];

        foreach ($relatedProducts as $index => $related) {
            $helper->fields_value['related_' . $index] = $related['id_product_related'];
            $helper->fields_value['position_' . $index] = $related['position'];
        }

        // Botón de volver
        $back_url = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules');

        return $helper->generateForm([[
            'form' => [
                'legend' => [
                    'title' => $this->l('Editar productos relacionados'),
                    'icon' => 'icon-link',
                ],
                'input' => $inputs,
                'submit' => [
                    'title' => $this->l('Guardar cambios'),
                    'class' => 'btn btn-primary',
                ],
                'buttons' => [
                    'back' => [
                        'title' => $this->l('Volver'),
                        'href' => $back_url,
                        'class' => 'btn btn-default',
                        'icon' => 'process-icon-back'
                    ]
                ]
            ]
        ]]);
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitUpdateRelatedProduct')) {
            $idProduct = (int)Tools::getValue('id_product');

            if (!$idProduct) {
                Tools::redirectAdmin(
                    $this->context->link->getAdminLink('AdminModules', false)
                    . '&configure=' . $this->name
                    . '&error=1&token=' . Tools::getAdminTokenLite('AdminModules')
                );
            }

            // Eliminar relaciones existentes
            Db::getInstance()->delete('customrelatedproducts', 'id_product = ' . $idProduct);

            $relatedIds = [];
            $result = true;

            foreach ($_POST as $key => $value) {
                if (strpos($key, 'related_') === 0) {
                    $index = (int)str_replace('related_', '', $key);
                    $relatedId = (int)$value;
                    $position = (int)Tools::getValue('position_' . $index);

                    // Evitar duplicados y autoselección
                    if ($relatedId && $relatedId !== $idProduct && !in_array($relatedId, $relatedIds)) {
                        $relatedIds[] = $relatedId;

                        $insert = Db::getInstance()->insert('customrelatedproducts', [
                            'id_product' => $idProduct,
                            'id_product_related' => $relatedId,
                            'position' => $position ?: ($index + 1),
                            'active' => 1,
                        ]);

                        if (!$insert) {
                            $result = false;
                            break;
                        }
                    }
                }
            }

            $flag = $result ? 'success=1' : 'error=1';

            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminModules', false)
                . '&configure=' . $this->name
                . '&' . $flag
                . '&token=' . Tools::getAdminTokenLite('AdminModules')
            );
        }
    }


    private static function getIfNormalSell(int $productId) {

        $categoriasProducto = Product::getProductCategories($productId);
        
        // Determinar si es una venta normal
        $CATEGORY_INSTALACION_ID = '36';
        $CATEGORY_MANTENIMIENTO_ID = '67';
        $CATEGORY_ARTICULATIONS = '94';
        $normalSell = in_array($CATEGORY_INSTALACION_ID, $categoriasProducto) ||
                      in_array($CATEGORY_MANTENIMIENTO_ID, $categoriasProducto) ||
                      in_array($CATEGORY_ARTICULATIONS, $categoriasProducto);

        return $normalSell;

    }

    public function getRelatedProductsArray($product) {

        $idLang = (int)$this->context->language->id;
        $idProduct = $product->getId();

        $relatedArray = [];

        if (self::getIfNormalSell($idProduct)) {

            $sql = 'SELECT
                    crp.id_product_related AS id_product,
                    pl.name,
                    crp.position
                FROM ps_customrelatedproducts crp
                LEFT JOIN ps_product_lang pl ON pl.id_product = crp.id_product_related AND pl.id_lang = '.$idLang.'
                WHERE crp.id_product = '.$idProduct.'
                ORDER BY crp.position';

        }else{
            
            $sql = 'SELECT
                    crp.id_product_related AS id_product,
                    pl.name,
                    formato.value AS formato,
                    material.value AS material,
                    crp.position
                FROM ps_customrelatedproducts crp
                LEFT JOIN ps_product_lang pl 
                    ON pl.id_product = crp.id_product_related AND pl.id_lang = '.$idLang.'
                LEFT JOIN ps_feature_product fp_formato 
                    ON fp_formato.id_product = crp.id_product_related AND fp_formato.id_feature = 4
                LEFT JOIN ps_feature_value_lang formato 
                    ON formato.id_feature_value = fp_formato.id_feature_value AND formato.id_lang = '.$idLang.'
                LEFT JOIN ps_feature_product fp_material 
                    ON fp_material.id_product = crp.id_product_related AND fp_material.id_feature = 45
                LEFT JOIN ps_feature_value_lang material 
                    ON material.id_feature_value = fp_material.id_feature_value AND material.id_lang = '.$idLang.'
                WHERE crp.id_product = '.$idProduct.'
                ORDER BY crp.position';
        }
        // Obtener resultados
        $dataCollection = Db::getInstance()->executeS($sql);
        // Crear instancia de Link
        $link = new Link();

        foreach ( $dataCollection as $data ) {
            $relatedArray[] = [
                'id' => $data['id_product'],
                'name' => $data['name'],
                'portada' => Product::getImageByPosition(1, $data['id_product']),
                'image' => Product::getImageByPosition(2, $data['id_product']),
                'formato' => self::getIfNormalSell($idProduct) ? '' : $data['formato'],
                'url' => $link->getProductLink($data['id_product'], null, null, null, $idLang),
                'position' => $data['position']

            ];
        }


        return $relatedArray;

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

    public function hookDisplayRelatedProducts($params)
    {
        
        $this->smarty->assign(array(
            'relatedproducts' => $this->getRelatedProductsArray($params['product']),
            'idLang' => (int)$this->context->language->id,
        ));
        return $this->display(__file__, 'views/templates/hook/relatedProductTemplate.tpl');
    }


}
