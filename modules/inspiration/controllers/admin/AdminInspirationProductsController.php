<?php
class AdminInspirationProductsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table      = 'inspiration_category_product';
        $this->identifier = 'id_inspiration'; // PK real
        $this->className  = 'stdClass';       // SIN ObjectModel (bypass loadObject)
        $this->bootstrap  = true;

        parent::__construct();

        // SELECT base sin filtrar por categoría todavía
        $this->_select = '
            a.id_inspiration AS id_inspiration,
            a.id_product AS ap_id_product,
            pl.name,
            a.id_image,
            a.position
        ';
        $this->_join = '
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                ON (pl.id_product = a.id_product AND pl.id_lang = 1)
        ';
        $this->_orderBy = 'a.id_product';

        // Definición de columnas
        $this->fields_list = [
            'ap_id_product' => [
                'title' => $this->l('ID Producto'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title'      => $this->l('Nombre'),
                'filter_key' => 'pl!name',
            ],
            'position' => [
                'title' => $this->l('Imagen inspiracional (posición ps_image)'),
            ],
        ];

        // Acciones por fila
        $this->actions = ['view', 'delete'];
    }

    public function initPageHeaderToolbar()
    {
        // Botón "Volver atrás" → regresa a categorías
        $this->page_header_toolbar_btn['back_to_categories'] = [
            'short' => 'Back',
            'href'  => $this->context->link->getAdminLink('AdminInspirationCategories'),
            'desc'  => $this->l('Volver a categorías'),
            'icon'  => 'process-icon-back'
        ];

        parent::initPageHeaderToolbar();
    }



    public function renderList()
    {
        $id_category = (int)Tools::getValue('id_category');

        // Validar categoría (solo aquí, NO en el constructor)
        if (!$id_category) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminInspirationCategories'));
        }

            // 🔹 Obtener el nombre de la categoría (para el título o mostrar en el tpl)
        $category_name = Db::getInstance()->getValue('
            SELECT name
            FROM '._DB_PREFIX_.'category_lang
            WHERE id_category = '.(int)$id_category.' AND id_lang = 1
        ');

        // Filtro para la lista de productos de esta categoría
        $this->_where = ' AND a.id_category='.(int)$id_category.' AND a.id_product<>0 ';

        // Productos disponibles para agregar (para el selector superior)
        $products = Db::getInstance()->executeS('
            SELECT p.id_product, pl.name
            FROM '._DB_PREFIX_.'category_product cp
            INNER JOIN '._DB_PREFIX_.'product p ON (p.id_product = cp.id_product)
            LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product AND pl.id_lang = 1)
            LEFT JOIN '._DB_PREFIX_.'inspiration_category_product icp
                ON (icp.id_category = '.(int)$id_category.' AND icp.id_product = p.id_product)
            WHERE cp.id_category = '.(int)$id_category.'
            AND icp.id_product IS NULL              -- 🔥 excluye los ya añadidos
            ORDER BY pl.name ASC
        ');

        $this->context->smarty->assign([
            'id_category'          => $id_category,
            'category_name'        => $category_name,
            'products'             => $products,
            'add_product_ajax_url' => $this->context->link->getAdminLink('AdminInspirationProducts').'&ajax=1&action=addProduct&id_category='.$id_category,
        ]);

        return parent::renderList()
             .$this->context->smarty->fetch(_PS_MODULE_DIR_.'inspiration/views/templates/admin/products.tpl');
    }

    /**
     * Bypass: devuelve un objeto "válido" para que el core llame a renderView()
     */
    protected function loadObject($opt = false)
    {
        if ($this->object) {
            return $this->object;
        }
        $id = (int)Tools::getValue($this->identifier); // id_inspiration
        if ($id > 0) {
            $o = new stdClass();
            $o->id = $id; // necesario para Validate::isLoadedObject()
            $this->object = $o;
            return $this->object;
        }
        return parent::loadObject($opt);
    }

    /**
     * Acción "Ver": lleva a la pantalla de imágenes del producto
     */
    public function renderView()
    {
        $id_inspiration = (int)Tools::getValue($this->identifier);
        if (!$id_inspiration) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminInspirationCategories'));
        }

        $row = Db::getInstance()->getRow('
            SELECT id_category, id_product
            FROM '._DB_PREFIX_.'inspiration_category_product
            WHERE id_inspiration='.(int)$id_inspiration.'
        ');

        if ($row && (int)$row['id_category'] && (int)$row['id_product']) {
            // 1) Saca el enlace con token
            $url = $this->context->link->getAdminLink('AdminInspirationImages', true);
            // 2) Añade los parámetros manualmente
            $url .= '&id_category='.(int)$row['id_category'].'&id_product='.(int)$row['id_product'];
            Tools::redirectAdmin($url);
        } else {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminInspirationCategories'));
        }

        return '';
    }

    public function postProcess()
    {
        // AJAX: agregar producto a la categoría
        if (Tools::isSubmit('ajax') && Tools::getValue('action') === 'addProduct') {
            $id_category = (int)Tools::getValue('id_category');
            $id_product  = (int)Tools::getValue('id_product');
            if ($id_category && $id_product) {
                $exists = (int)Db::getInstance()->getValue(sprintf(
                    'SELECT COUNT(*) FROM `%sinspiration_category_product`
                     WHERE id_category=%d AND id_product=%d',
                    pSQL(_DB_PREFIX_), (int)$id_category, (int)$id_product
                ));
                if (!$exists) {
                    Db::getInstance()->insert('inspiration_category_product', [
                        'id_category' => (int)$id_category,
                        'id_product'  => (int)$id_product,
                        'id_image'    => null,
                        'position'    => null,
                    ]);
                }
            }
            die(json_encode(['ok'=>true]));
        }

        // Eliminar producto de la inspiración
        if (Tools::isSubmit('delete'.$this->table)) {
            $id_inspiration = (int)Tools::getValue($this->identifier);
            $id_category = (int)Db::getInstance()->getValue(sprintf(
                'SELECT id_category FROM `%sinspiration_category_product` WHERE id_inspiration=%d',
                pSQL(_DB_PREFIX_), (int)$id_inspiration
            ));
            if ($id_inspiration) {
                Db::getInstance()->delete('inspiration_category_product', 'id_inspiration='.(int)$id_inspiration);
            }
            Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&id_category='.(int)$id_category);
        }

        return parent::postProcess();
    }
}
