<?php
class AdminInspirationCategoriesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table      = 'inspiration_category';
        $this->identifier = 'id_inspiration_category'; // PK real
        $this->className  = 'stdClass';                // SIN ObjectModel (bypass loadObject)
        $this->bootstrap  = true;
        parent::__construct();

        // IMPORTANTE: selecciona la PK con el MISMO alias que identifier
        $this->_select = '
            a.id_inspiration_category AS id_inspiration_category,
            a.id_category AS cat_id,
            cl.name
        ';
        $this->_join = '
            INNER JOIN `'._DB_PREFIX_.'category_lang` cl
              ON (cl.id_category = a.id_category AND cl.id_lang = 1)
        ';
        $this->_orderBy = 'cl.name';

        $this->fields_list = [
            'cat_id' => [
                'title' => $this->l('ID Categoría'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ],
            'name' => [
                'title' => $this->l('Nombre')
            ],
        ];

        // Botones: Ver primero (view) y Eliminar
        $this->actions = ['view', 'delete'];
    }

    public function initPageHeaderToolbar()
    {
        // No añadimos botones personalizados
        $this->page_header_toolbar_btn = [];
        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        $this->toolbar_btn = [];

        // Construir breadcrumb de categorías disponibles (no añadidas aún al módulo)
        $rootId = (int)Configuration::get('PS_ROOT_CATEGORY');
        $langId = 1;

        $categories = Db::getInstance()->executeS('
            SELECT
                c.id_category,
                GROUP_CONCAT(cl2.name ORDER BY c2.nleft SEPARATOR " > ") AS path
            FROM '._DB_PREFIX_.'category c
            INNER JOIN '._DB_PREFIX_.'category c2
                ON (c2.nleft <= c.nleft AND c2.nright >= c.nright)
            INNER JOIN '._DB_PREFIX_.'category_lang cl2
                ON (cl2.id_category = c2.id_category AND cl2.id_lang = '.(int)$langId.')
            WHERE c.active = 1
            AND c.id_category <> '.(int)$rootId.'
            AND c2.id_category <> '.(int)$rootId.'
            AND NOT EXISTS (
                SELECT 1 FROM '._DB_PREFIX_.'inspiration_category ic
                WHERE ic.id_category = c.id_category
            )
            GROUP BY c.id_category
            ORDER BY path ASC
        ');

        $this->context->smarty->assign([
            'add_category_ajax_url' => $this->context->link->getAdminLink('AdminInspirationCategories').'&ajax=1&action=addCategory',
            'categories'            => is_array($categories) ? $categories : [], // <- siempre asignamos un array
        ]);

        // Render listado + panel con selector
        return parent::renderList()
            .$this->context->smarty->fetch(_PS_MODULE_DIR_.'inspiration/views/templates/admin/categories.tpl');
    }

    /**
     * BYPASS: devolvemos objeto "válido" con ->id para que el core permita renderView()
     */
    protected function loadObject($opt = false)
    {
        if ($this->object) {
            return $this->object;
        }
        $id = (int)Tools::getValue($this->identifier); // id_inspiration_category
        if ($id > 0) {
            $o = new stdClass();
            $o->id = $id; // Validate::isLoadedObject() necesita ->id
            $this->object = $o;
            return $this->object;
        }
        return parent::loadObject($opt);
    }

    public function renderView()
    {
        $id_insp_cat = (int)Tools::getValue($this->identifier);
        if (!$id_insp_cat) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminInspirationCategories'));
        }

        $id_category = (int)Db::getInstance()->getValue('
            SELECT id_category
            FROM '._DB_PREFIX_.'inspiration_category
            WHERE id_inspiration_category='.(int)$id_insp_cat.'
        ');

        if ($id_category) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminInspirationProducts', true, [], [
                'id_category' => $id_category
            ]));
        } else {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminInspirationCategories'));
        }
        return '';
    }

    public function displayAjaxAddCategory()
    {
        $id_category = (int)Tools::getValue('id_category');
        if ($id_category > 0) {
            Db::getInstance()->insert('inspiration_category', [
                'id_category' => (int)$id_category,
                'active'      => 1,
                'date_add'    => date('Y-m-d H:i:s'),
                'date_upd'    => date('Y-m-d H:i:s'),
            ], false, true, Db::INSERT_IGNORE);
            die(json_encode(['ok'=>true]));
        }
        die(json_encode(['ok'=>false]));
    }

    public function processDelete()
    {
        $id_inspiration_category = (int)Tools::getValue('id_inspiration_category');
        if ($id_inspiration_category) {
            $id_category = (int)Db::getInstance()->getValue('
                SELECT id_category FROM '._DB_PREFIX_.'inspiration_category
                WHERE id_inspiration_category='.(int)$id_inspiration_category
            );
            if ($id_category) {
                Db::getInstance()->delete('inspiration_category_product', 'id_category='.(int)$id_category);
            }
            Db::getInstance()->delete('inspiration_category', 'id_inspiration_category='.(int)$id_inspiration_category);
        }
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    public function processDetails() {
        $id_inspiration_category = (int)Tools::getValue('id_inspiration_category');
        $id_category = (int)Db::getInstance()->getValue('
            SELECT id_category FROM '._DB_PREFIX_.'inspiration_category
            WHERE id_inspiration_category='.(int)$id_inspiration_category
        );
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminInspirationProducts', true, [], [
            'id_category' => $id_category
        ]));
    }

}
