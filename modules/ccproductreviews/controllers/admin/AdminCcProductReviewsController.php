<?php
require_once _PS_MODULE_DIR_.'ccproductreviews/classes/Review.php';
require_once _PS_MODULE_DIR_.'ccproductreviews/classes/ReviewImage.php';

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
        $this->className = 'Review';
        
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

    public function postProcess()
    {
        // 1) Toggle desde la vista detalle
        if (Tools::isSubmit('ccpr_toggle_view')) {
            $this->processToggleFromView();
            return;
        }

        // 2) Enviar email desde la vista detalle
        if (Tools::isSubmit('ccpr_send_email')) {
            $this->processSendEmailFromView();
            return;
        }

        parent::postProcess();
    }


    public function processStatus()
    {
        $idReview = (int) Tools::getValue($this->identifier);
        if ($idReview <= 0) {
            $this->errors[] = $this->l('Invalid review ID.');
            return false;
        }
        $review = new Review($idReview);
        $review->active = (int)!((bool)$review->active);

        if (!$review->update()) {
            $this->errors[] = $this->l('Could not update review.');
            return;
        }

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

        // 1) filesystem (inevitable iterar)
        foreach ($ids as $idReview) {
            $idReview = (int)$idReview;
            if ($idReview > 0) {
                $this->deleteReviewAssets($idReview);
            }
        }

        // 2) BD en bloque (evita N+1)
        if (!Review::bulkDelete($ids)) {
            $this->errors[] = $this->l('Could not delete selected reviews.');
            return false;
        }

        Tools::redirectAdmin(self::$currentIndex.'&conf=2&token='.$this->token);
        return true;
    }

    public function renderView()
    {
        $idReview = (int)Tools::getValue($this->identifier);
        if ($idReview <= 0) {
            return parent::renderView();
        }

        // ✅ Review ObjectModel
        $review = new Review($idReview);
        if (!Validate::isLoadedObject($review)) {
            $this->errors[] = $this->l('Review not found.');
            return parent::renderView();
        }

        if ((int)Tools::getValue('ccpr_mail_sent')) {
            $this->confirmations[] = $this->l('Email Enviado correctamente.');
        }

        // ✅ Nombre de producto sin SQL manual
        $productName = Product::getProductName((int)$review->id_product, null, (int)$this->context->language->id);
        if (!$productName) {
            $productName = '#'.(int)$review->id_product;
        }

        // Convertimos a array para tu tpl actual (que espera $review como array)
        $row = [
            'id_review' => (int)$review->id_review,
            'id_product' => (int)$review->id_product,
            'id_customer' => (int)$review->id_customer,
            'customer_name' => (string)$review->customer_name,
            'rating' => (int)$review->rating,
            'comment' => (string)$review->comment,
            'active' => (int)$review->active,
            'date_add' => (string)$review->date_add,
            'product_name' => (string)$productName,
        ];

        // ✅ Imágenes vía clase
        $imagesRows = ReviewImage::getByReview($idReview);

        // Mantener estructura que tu tpl usa: [{file_name: "..."}]
        $images = [];
        foreach ($imagesRows as $img) {
            $images[] = ['file_name' => $img['file_name']];
        }

        $imgBase = $this->context->link->getMediaLink(_PS_IMG_).'ccproductreviews/uploads/'.(int)$idReview.'/';

        $this->context->smarty->assign([
            'review' => $row,
            'images' => $images,
            'img_base' => $imgBase,
            'back_url' => self::$currentIndex.'&token='.$this->token,
            'action_url' => self::$currentIndex.'&token='.$this->token.'&view'.$this->table.'=&'.$this->identifier.'='.(int)$idReview,
        ]);

        return $this->module->fetch('module:'.$this->module->name.'/views/templates/admin/view.tpl');
    }


    private function deleteReviewAssets($idReview)
    {
        Db::getInstance()->delete('product_review_image', '`id_review`='.(int) $idReview);

        $dir = _PS_IMG_DIR_.'ccproductreviews/uploads/'.(int) $idReview.'/';
        if (is_dir($dir)) {
            Tools::deleteDirectory($dir, true);
        }
    }

    private function processToggleFromView()
    {
        $idReview = (int) Tools::getValue($this->identifier);
        if ($idReview <= 0) {
            $this->errors[] = 'Invalid review ID.';
            return;
        }

        $review = new Review((int)$idReview);

        if (!Validate::isLoadedObject($review)) {
            $this->errors[] = 'Review not found.';
            return;
        }

        $review->active = (int)!((bool)$review->active);

        if (!$review->update()) {
            $this->errors[] = 'Could not update review.';
            return;
        }

        // Volver a la vista detalle
        Tools::redirectAdmin(
            self::$currentIndex.'&token='.$this->token.'&view'.$this->table.'=&'.$this->identifier.'='.(int)$idReview.'&conf=5'
        );
    }

    private function processSendEmailFromView()
    {
        $idReview = (int)Tools::getValue($this->identifier);
        $message  = trim((string)Tools::getValue('ccpr_email_message'));

        if ($idReview <= 0) {
            $this->errors[] = 'Invalid review ID.';
            return;
        }

        if ($message === '') {
            $this->errors[] = 'Message cannot be empty.';
            return;
        }

        if (Tools::strlen($message) > 2000) {
            $this->errors[] = 'Message is too long (max 2000 chars).';
            return;
        }

        // ✅ Cargar reseña con ObjectModel
        $review = new Review($idReview);
        if (!Validate::isLoadedObject($review)) {
            $this->errors[] = 'Review not found.';
            return;
        }

        // ✅ Cliente
        $customer = new Customer((int)$review->id_customer);
        if (!Validate::isLoadedObject($customer) || !Validate::isEmail($customer->email)) {
            $this->errors[] = 'Customer email not found.';
            return;
        }

        // Idioma destino del email
        $idLang = (int)$customer->id_lang;
        if ($idLang <= 0) {
            $idLang = (int)Configuration::get('PS_LANG_DEFAULT');
        }

        // ✅ Nombre de producto (sin SQL manual)
        // Nota: Product::getProductName existe en PS 1.7.x
        $productName = Product::getProductName((int)$review->id_product, null, (int)$idLang);
        $productUrl = $this->context->link->getProductLink((int)$review->id_product, null, null, null, (int)$idLang);
        if (!$productName) {
            $productName = '#'.(int)$review->id_product;
        }

        $vars = [
            '{firstname}'    => $customer->firstname,
            '{lastname}'     => $customer->lastname,
            '{shop_name}'    => Configuration::get('PS_SHOP_NAME'),
            '{product_name}' => $productName,
            '{product_url}' => $productUrl,
            '{message}'      => nl2br(Tools::safeOutput($message)),
        ];

        $subject = sprintf($this->l('Message about your review: %s'), $productName);

        $sent = Mail::Send(
            $idLang,
            'ccpr_admin_message',
            $subject,
            $vars,
            $customer->email,
            $customer->firstname.' '.$customer->lastname,
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_.$this->module->name.'/mails/'
        );

        if (!$sent) {
            $this->errors[] = $this->l('Email could not be sent.');
            return;
        }

        Tools::redirectAdmin(
            self::$currentIndex.'&token='.$this->token.'&view'.$this->table.'=&'.$this->identifier.'='.(int)$idReview.'&ccpr_mail_sent=1'
        );
    }

}