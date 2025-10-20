<?php
if (!defined('_PS_VERSION_')) { exit; }

class Inspiration extends Module
{
    public function __construct()
    {
        $this->name = 'inspiration';
        $this->tab = 'administration';
        $this->version = '1.2.1';
        $this->author = 'José Fernández';
        $this->need_instance = 0;
        $this->is_configurable = 1;

        parent::__construct();

        $this->displayName = $this->l('Inspiraciones');
        $this->description = $this->l('Gestión de inspiraciones por categorías, productos e imágenes.');
    }

    public function install()
    {
        return parent::install()
            && $this->installSql()
            && $this->installTabs()
            // Hook del carrusel (lo llamas desde el tema)
            && $this->registerHook('displayInspirationCarousel')
            // Opcional: para cargar CSS/JS del carrusel
            && $this->registerHook('header');
    }

    public function uninstall()
    {
        return $this->uninstallTabs()
            && $this->uninstallSql()
            && parent::uninstall();
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminInspirationCategories'));
        return '';
    }

    private function installSql()
    {
        $sql = str_replace('PREFIX_', _DB_PREFIX_, file_get_contents(__DIR__.'/sql/install.sql'));
        foreach (array_filter(array_map('trim', preg_split('/;\s*\n/', $sql))) as $statement) {
            if ($statement && !Db::getInstance()->execute($statement)) {
                return false;
            }
        }
        return true;
    }

    private function uninstallSql()
    {
        $sql = str_replace('PREFIX_', _DB_PREFIX_, file_get_contents(__DIR__.'/sql/uninstall.sql'));
        foreach (array_filter(array_map('trim', preg_split('/;\s*\n/', $sql))) as $statement) {
            if ($statement && !Db::getInstance()->execute($statement)) {
                return false;
            }
        }
        return true;
    }

    private function installTabs()
    {
        $id_parent = (int)Tab::getIdFromClassName('AdminParentCatalog');

        $tab1 = new Tab();
        $tab1->active = 1;
        $tab1->class_name = 'AdminInspirationCategories';
        foreach (Language::getIDs(false) as $id_lang) {
            $tab1->name[$id_lang] = 'Inspiraciones';
        }
        $tab1->id_parent = $id_parent;
        $tab1->module = $this->name;

        $tab2 = new Tab();
        $tab2->active = 0;
        $tab2->class_name = 'AdminInspirationProducts';
        foreach (Language::getIDs(false) as $id_lang) {
            $tab2->name[$id_lang] = 'Insp: Productos';
        }
        $tab2->id_parent = -1;
        $tab2->module = $this->name;

        $tab3 = new Tab();
        $tab3->active = 0;
        $tab3->class_name = 'AdminInspirationImages';
        foreach (Language::getIDs(false) as $id_lang) {
            $tab3->name[$id_lang] = 'Insp: Imágenes';
        }
        $tab3->id_parent = -1;
        $tab3->module = $this->name;

        return $tab1->add() && $tab2->add() && $tab3->add();
    }

    private function uninstallTabs()
    {
        foreach (['AdminInspirationImages','AdminInspirationProducts','AdminInspirationCategories'] as $cn) {
            if ($id = (int)Tab::getIdFromClassName($cn)) {
                $t = new Tab($id);
                $t->delete();
            }
        }
        return true;
    }

    /**
     * (Opcional) Cargar CSS/JS del carrusel en páginas de categoría.
     * Elimina/ajusta si no usas Owl u otro carrusel.
     */
    public function hookHeader($params)
    {
        if (Tools::getValue('controller') !== 'category') {
            return;
        }

        $this->context->controller->registerStylesheet(
            'inspiration-carousel',
            'modules/'.$this->name.'/views/css/inspiration_carousel.css',
            ['media' => 'all', 'priority' => 150]
        );

        $this->context->controller->registerJavascript(
            'inspiration-carousel',
            'modules/'.$this->name.'/views/js/inspiration_carousel.js',
            ['position' => 'bottom', 'priority' => 150]
        );
    }


    /**
     * Hook que pinta el carrusel en el tema:
     * {hook h='displayInspirationCarousel' id_category=$category.id}
     */
    public function hookDisplayInspirationCarousel($params)
    {
        $ctx = $this->context;
        $idLang = (int)$ctx->language->id;

        // id_category viene desde el tpl del tema
        $idCategory = isset($params['id_category']) ? (int)$params['id_category'] : 0;
        if ($idCategory <= 0) {
            return '';
        }

        // ¿La categoría está activada en el módulo?
        $isInspirational = (bool)Db::getInstance()->getValue('
            SELECT 1
            FROM '._DB_PREFIX_.'inspiration_category
            WHERE id_category = '.(int)$idCategory.' AND active = 1
        ');
        if (!$isInspirational) {
            return '';
        }

        // Productos seleccionados en la tabla del módulo
        $products = self::getInspirationalProducts($idCategory, $idLang, 10);
        if (empty($products)) {
            return '';
        }

        // Nombre de categoría para el título del bloque
        $rawName = Db::getInstance()->getValue('
            SELECT meta_title
            FROM '._DB_PREFIX_.'category_lang
            WHERE id_category='.(int)$idCategory.' AND id_lang='.(int)$idLang
        );

        $categoryName = trim(preg_replace('/\|.*/', '', $rawName));

        $ctx->smarty->assign([
            'inspirationalProducts' => $products,
            'categoryName'          => $categoryName,
        ]);

        return $this->fetch('module:'.$this->name.'/views/templates/hook/inspiration_carousel.tpl');
    }

    /**
     * Devuelve hasta $limit productos de la categoría en la tabla del módulo
     * con url del producto e imagen (por id_image guardado o por position).
     */
    public static function getInspirationalProducts($idCategory, $idLang = 1, $limit = 10)
    {
        $idCategory = (int)$idCategory;
        if ($idCategory <= 0) return [];

        $sql = '
            SELECT icp.id_product, icp.id_image, icp.position, pl.link_rewrite
            FROM '._DB_PREFIX_.'inspiration_category_product icp
            INNER JOIN '._DB_PREFIX_.'product_lang pl
                ON (pl.id_product = icp.id_product AND pl.id_lang = '.(int)$idLang.')
            WHERE icp.id_category = '.(int)$idCategory.'
            ORDER BY
                CASE WHEN icp.position IS NULL THEN 999999 ELSE icp.position END ASC,
                icp.id_inspiration DESC
            LIMIT '.(int)$limit;

        $rows = Db::getInstance()->executeS($sql);
        if (empty($rows)) return [];

        $ctx  = Context::getContext();
        $link = $ctx->link;
        $out  = [];

        foreach ($rows as $r) {
            $idProduct   = (int)$r['id_product'];
            $rewrite     = $r['link_rewrite'];

            // URL del producto (por si la quieres en el futuro, pero no la usaremos para click)
            $urlProduct = $link->getProductLink($idProduct, $rewrite, null, null, (int)$idLang);

            // Resolver id_image final: preferimos el id_image guardado; si no, por position; si no, cover
            $idImage = (int)$r['id_image'];
            if ($idImage <= 0) {
                $idImage = (int)Db::getInstance()->getValue('
                    SELECT id_image FROM '._DB_PREFIX_.'image
                    WHERE id_product='.(int)$idProduct.' AND position='.(int)$r['position'].'
                    ORDER BY id_image ASC
                ');
            }
            if ($idImage <= 0) {
                $idImage = (int)Db::getInstance()->getValue('
                    SELECT id_image FROM '._DB_PREFIX_.'image
                    WHERE id_product='.(int)$idProduct.' AND cover=1
                ');
            }
            if ($idImage <= 0) {
                continue; // sin imagen que mostrar
            }

            // Generar URLs mini y grande (ajusta tipos si necesitas otros)
            $thumb = self::getImageUrlByIdImage($idImage, $rewrite, 'home_default');
            $full  = self::getImageUrlByIdImage($idImage, $rewrite, 'large_default');

            if ($thumb && $full) {
                $out[] = [
                    'urlProduct'    => $urlProduct,     // no se usará en el click ahora
                    'urlImageThumb' => $thumb,
                    'urlImageFull'  => $full,
                ];
            }
        }

        return $out;
    }


    protected static function getImageUrlByIdImage($idImage, $linkRewrite, $type = 'home_default')
    {
        return Context::getContext()->link->getImageLink($linkRewrite, (int)$idImage, $type);
    }

    protected static function getImageUrlByPosition($idProduct, $position, $linkRewrite, $type = 'home_default')
    {
        if ($position <= 0) $position = 0;
        $idImage = (int)Db::getInstance()->getValue('
            SELECT id_image FROM '._DB_PREFIX_.'image
            WHERE id_product='.(int)$idProduct.' AND position='.(int)$position.'
            ORDER BY id_image ASC
        ');
        return $idImage ? self::getImageUrlByIdImage($idImage, $linkRewrite, $type) : '';
    }

    protected static function getCoverUrl($idProduct, $linkRewrite, $type = 'home_default')
    {
        $idImage = (int)Db::getInstance()->getValue('
            SELECT id_image FROM '._DB_PREFIX_.'image
            WHERE id_product='.(int)$idProduct.' AND cover=1
        ');
        return $idImage ? self::getImageUrlByIdImage($idImage, $linkRewrite, $type) : '';
    }
}
