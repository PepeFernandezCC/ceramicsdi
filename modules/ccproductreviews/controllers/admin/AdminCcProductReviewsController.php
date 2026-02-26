<?php


if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminCcProductReviewsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = false;

        $this->table = 'product_review';
        $this->identifier = 'id_review';

        $this->_defaultOrderBy = 'date_add';
        $this->_defaultOrderWay = 'DESC';

        $idLang = (int) Context::getContext()->language->id;
        $idShop = (int) Context::getContext()->shop->id;

        // Add product name to list
        $this->_select = 'pl.name AS product_name';
        $this->_join = '
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
              ON (pl.id_product = a.id_product
              AND pl.id_lang = '.$idLang.'
              AND pl.id_shop = '.$idShop.')
        ';

        $this->fields_list = [
            'id_review' => [
                'title' => 'ID',
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'product_name' => [
                'title' => 'Producto',
                'filter_key' => 'pl!name',
                'havingFilter' => true,
            ],
            'customer_name' => [
                'title' => 'Cliente',
            ],
            'rating' => [
                'title' => 'Puntuación',
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'type' => 'int',
            ],
            'active' => [
                'title' => 'Visible',
                'align' => 'center',
                'active' => 'status', // enables toggle action in list
                'type' => 'bool',
                'class' => 'fixed-width-sm',
                'orderby' => true,
            ],
            'date_add' => [
                'title' => 'Fecha',
                'type' => 'datetime',
            ],
        ];

        parent::__construct();

        // Row actions
        $this->addRowAction('view');
        $this->addRowAction('delete');

        // Bulk delete
        $this->bulk_actions = [
            'delete' => [
                'text' => 'Borrar seleccionado',
                'confirm' => 'Borrar las reviews seleccionadas?',
                'icon' => 'icon-trash',
            ],
        ];
    }


    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addCSS($this->module->getPathUri().'views/css/admin.css');
        $this->addJS($this->module->getPathUri().'views/js/admin.js');
    }


    public function processStatus()
    {
        $idReview = (int) Tools::getValue($this->identifier);
        if ($idReview <= 0) {
            $this->errors[] = $this->l('Invalid review ID.');
            return false;
        }

        $current = (int) Db::getInstance()->getValue('
            SELECT `active`
            FROM `'._DB_PREFIX_.'product_review`
            WHERE `id_review`='.(int) $idReview
        );

        $newValue = (int) !((bool) $current);

        $ok = Db::getInstance()->update(
            'product_review',
            ['active' => $newValue],
            '`id_review`='.(int) $idReview
        );

        if (!$ok) {
            $this->errors[] = $this->l('Could not update visibility.');
            return false;
        }

        // conf=5 => status updated (standard PS message)
        Tools::redirectAdmin(self::$currentIndex.'&conf=5&token='.$this->token);
        return true;
    }


    public function processDelete()
    {
        $idReview = (int) Tools::getValue($this->identifier);
        if ($idReview <= 0) {
            $this->errors[] = $this->l('Invalid review ID.');
            return false;
        }

        $this->deleteReviewAssets($idReview);

        $ok = Db::getInstance()->delete('product_review', '`id_review`='.(int) $idReview);
        if (!$ok) {
            $this->errors[] = $this->l('Could not delete review.');
            return false;
        }

        // conf=1 => deleted (standard PS message)
        Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.$this->token);
        return true;
    }


    public function processBulkDelete()
    {
        $ids = Tools::getValue($this->table.'Box');
        if (!is_array($ids) || empty($ids)) {
            $this->errors[] = $this->l('No items selected.');
            return false;
        }

        foreach ($ids as $idReview) {
            $idReview = (int) $idReview;
            if ($idReview <= 0) {
                continue;
            }
            $this->deleteReviewAssets($idReview);
            Db::getInstance()->delete('product_review', '`id_review`='.(int) $idReview);
        }

        // conf=2 => bulk delete (standard PS message)
        Tools::redirectAdmin(self::$currentIndex.'&conf=2&token='.$this->token);
        return true;
    }


    public function renderView()
    {
        $idReview = (int) Tools::getValue($this->identifier);
        if ($idReview <= 0) {
            return parent::renderView();
        }

        $row = Db::getInstance()->getRow('
            SELECT r.*, pl.name AS product_name
            FROM `'._DB_PREFIX_.'product_review` r
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
              ON (pl.id_product = r.id_product
              AND pl.id_lang='.(int) $this->context->language->id.'
              AND pl.id_shop='.(int) $this->context->shop->id.')
            WHERE r.id_review='.(int) $idReview.'
        ');

        if (!$row) {
            $this->errors[] = $this->l('Review not found.');
            return parent::renderView();
        }

        $images = Db::getInstance()->executeS('
            SELECT file_name
            FROM `'._DB_PREFIX_.'product_review_image`
            WHERE id_review='.(int) $idReview.'
            ORDER BY id_image ASC
        ');

        // Use media base URL so it works under http/https and different domains
        $imgBase = $this->context->link->getMediaLink(_PS_IMG_).'ccproductreviews/uploads/'.(int) $idReview.'/';

        $this->context->smarty->assign([
            'review' => $row,
            'images' => $images,
            'img_base' => $imgBase,
            'back_url' => self::$currentIndex.'&token='.$this->token,
        ]);

        return $this->module->fetch(
            'module:'.$this->module->name.'/views/templates/admin/view.tpl'
        );
    }


    private function deleteReviewAssets($idReview)
    {
        Db::getInstance()->delete('product_review_image', '`id_review`='.(int) $idReview);

        $dir = _PS_IMG_DIR_.'ccproductreviews/uploads/'.(int) $idReview.'/';
        if (is_dir($dir)) {
            Tools::deleteDirectory($dir, true);
        }
    }
}