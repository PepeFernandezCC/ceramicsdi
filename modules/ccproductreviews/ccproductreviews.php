<?php
if (!defined('_PS_VERSION_')) exit;

class CcProductReviews extends Module
{
    public function __construct()
    {
        $this->name = 'ccproductreviews';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'CERAMIC CONNECTION';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('CC Product Reviews');
        $this->description = $this->l('Reseñas con estrellas, comentario y fotos para productos.');
    }

    public function install()
    {
        return parent::install()
            && $this->installDb()
            && $this->registerHook('header')
            && $this->registerHook('displayProductAdditionalInfo')
            && $this->registerHook('displayCcProductReviews')
            && $this->registerHook('actionOrderStatusPostUpdate')
            && $this->installTab()
            && Configuration::updateValue('CCPR_DELIVERED_STATE', (int)Configuration::get('PS_OS_DELIVERED'));
    }

    public function uninstall()
    {
        return $this->uninstallTab()
            && $this->uninstallDb()
            && Configuration::deleteByName('CCPR_DELIVERED_STATE')
            && parent::uninstall();
    }

    private function installDb()
    {
        return Db::getInstance()->execute(file_get_contents(__DIR__.'/config/install.sql'));
    }

    private function uninstallDb()
    {
        return Db::getInstance()->execute(file_get_contents(__DIR__.'/config/uninstall.sql'));
    }

    public function hookDisplayCcProductReviews($params)
    {
        $idProduct = (int)($params['product']['id_product'] ?? 0);
        if (!$idProduct) { return ''; }

        require_once __DIR__.'/classes/Review.php';

        $idCustomer = (int)$this->context->customer->id;
        
        $canReview = $idCustomer ? Review::customerCanReview($idCustomer, $idProduct) : false;

        $reviews = Review::getByProduct($idProduct);
        $avg = Review::getAverageByProduct($idProduct);
        $count = Review::getCountByProduct($idProduct);

        Media::addJsDef([
            'ccpr' => [
                'max_files' => 3,
                'token' => Tools::getToken(false),
            ]
         ]);

        $this->context->smarty->assign([
            'clientId' => $idCustomer,
            'ccpr_id_product' => $idProduct,
            'ccpr_reviews' => $reviews,
            'ccpr_img_base' => $this->context->link->getMediaLink(_PS_IMG_).'ccproductreviews/uploads/',
            'ccpr_avg' => $avg,
            'ccpr_count' => $count,
            'ccpr_can_review' => $canReview,
            'ccpr_max_files' => 3,
            'ccpr_token' => Tools::getToken(false),
            'ccpr_submit_url' => $this->context->link->getModuleLink($this->name, 'submit', [], true),
        ]);

        return $this->fetch('module:'.$this->name.'/views/templates/hook/product_reviews.tpl');
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        $newStatus = (int)$params['newOrderStatus']->id;
        $delivered = (int)Configuration::get('CCPR_DELIVERED_STATE');

        if ($newStatus !== $delivered) {
            return;
        }

        $order = new Order((int)$params['id_order']);
        $customer = new Customer((int)$order->id_customer);

        $products = $order->getProducts();

        $productListHtml = '';
        $productListTxt  = '';

        foreach ($products as $p) {

            $productUrl = $this->context->link->getProductLink($p['product_id']);

            // Construimos HTML
            $productListHtml .= '
            <tr>
                <td style="padding:15px 0;border-bottom:1px solid #eee;">
                    <strong>'.htmlspecialchars($p['product_name']).'</strong><br><br>
                    <a href="'.$productUrl.'"
                    style="display:inline-block;padding:8px 16px;background:#111;color:#fff;text-decoration:none;border-radius:4px;font-size:13px;">
                        Escribir reseña
                    </a>
                </td>
            </tr>';

            // Construimos TXT
            $productListTxt .=
                $p['product_name']."\n".
                $productUrl."\n\n";
        }

        $templateVars = [
            '{firstname}'    => $customer->firstname,
            '{lastname}'     => $customer->lastname,
            '{shop_name}'    => Configuration::get('PS_SHOP_NAME'),
            '{product_list}' => $productListHtml,
        ];

        Mail::Send(
            (int)$order->id_lang,
            'review_request',
            '¿Qué te han parecido tus productos?',
            $templateVars,
            $customer->email,
            $customer->firstname.' '.$customer->lastname,
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_.$this->name.'/mails/'
        );
    }

    private function installTab()
    {
        $idParent = (int)Tab::getIdFromClassName('AdminCatalog'); // o AdminParentCustomer, según prefieras
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminCcProductReviews';
        $tab->name = [];
        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[$lang['id_lang']] = 'Reseñas productos';
        }
        $tab->id_parent = $idParent;
        $tab->module = $this->name;
        return (bool)$tab->add();
    }

    private function uninstallTab()
    {
        $idTab = (int)Tab::getIdFromClassName('AdminCcProductReviews');
        if ($idTab) {
            $tab = new Tab($idTab);
            return (bool)$tab->delete();
        }
        return true;
    }

    public function hookHeader()
    {
        if ($this->context->controller->php_self !== 'product') {
            return;
        }

        $this->context->controller->registerStylesheet(
            'ccpr-front-css',
            'modules/'.$this->name.'/views/css/front.css',
            [
                'media' => 'all',
                'priority' => 150,
            ]
        );

        $this->context->controller->registerJavascript(
            'ccpr-front-js',
            'modules/'.$this->name.'/views/js/front.js',
            [
                'position' => 'bottom',
                'priority' => 150,
            ]
        );
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
        $idProduct = (int)($params['product']['id_product'] ?? 0);
        if (!$idProduct) {
            return '';
        }

        require_once __DIR__.'/classes/Review.php';

        $avg = Review::getAverageByProduct($idProduct);
        $count = Review::getCountByProduct($idProduct);

        $this->context->smarty->assign([
            'ccpr_avg' => $avg,
            'ccpr_count' => $count,
        ]);

        return $this->fetch('module:'.$this->name.'/views/templates/hook/product_rating.tpl');
    }
}