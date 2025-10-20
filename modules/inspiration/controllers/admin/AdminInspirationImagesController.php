<?php
class AdminInspirationImagesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initPageHeaderToolbar()
    {
        $id_category = (int)Tools::getValue('id_category');

        $this->page_header_toolbar_btn['back_to_products'] = [
            'short' => 'Back',
            'href'  => $this->context->link->getAdminLink('AdminInspirationProducts', true, [], [
                'id_category' => $id_category
            ]),
            'desc'  => $this->l('Volver a productos'),
            'icon'  => 'process-icon-back'
        ];

        parent::initPageHeaderToolbar();
    }


    public function postProcess()
    {
        if (Tools::isSubmit('saveImage')) {
            $id_category = (int)Tools::getValue('id_category');
            $id_product  = (int)Tools::getValue('id_product');
            $id_image    = (int)Tools::getValue('id_image');

            if ($id_category && $id_product && $id_image) {
                $position = (int)Db::getInstance()->getValue('
                    SELECT `position` FROM '._DB_PREFIX_.'image
                    WHERE id_image='.(int)$id_image.'
                ');
                Db::getInstance()->update('inspiration_category_product', [
                    'id_image' => $id_image,
                    'position' => $position,
                ], 'id_category='.(int)$id_category.' AND id_product='.(int)$id_product);
            }
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminInspirationProducts', true, [], [
                'id_category' => (int)Tools::getValue('id_category')
            ]));
        }
        parent::postProcess();
    }

    public function renderList()
    {
        $id_category = (int)Tools::getValue('id_category');
        $id_product  = (int)Tools::getValue('id_product');

        if (!$id_category || !$id_product) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminInspirationCategories'));
        }

        $images = Db::getInstance()->executeS('
            SELECT i.id_image, i.position
            FROM '._DB_PREFIX_.'image i
            WHERE i.id_product='.(int)$id_product.'
            ORDER BY i.position ASC
        ');

        $current = Db::getInstance()->getRow('
            SELECT id_image, position
            FROM '._DB_PREFIX_.'inspiration_category_product
            WHERE id_category='.(int)$id_category.' AND id_product='.(int)$id_product.'
        ');

        $thumbnails = [];
        foreach ($images as $img) {
            $imageObj = new Image((int)$img['id_image']);
            $path = _PS_IMG_.'p/'.$imageObj->getExistingImgPath().'-small_default.jpg';
            $thumbnails[] = [
                'id_image' => (int)$img['id_image'],
                'position' => (int)$img['position'],
                'src'      => $path,
                'selected' => (isset($current['id_image']) && (int)$current['id_image'] === (int)$img['id_image']),
            ];
        }

        $this->context->smarty->assign([
            'id_category' => $id_category,
            'id_product'  => $id_product,
            'thumbnails'  => $thumbnails,
            'save_url'    => $this->context->link->getAdminLink('AdminInspirationImages'),
        ]);

        return $this->context->smarty->fetch(_PS_MODULE_DIR_.'inspiration/views/templates/admin/images.tpl');
    }
}
