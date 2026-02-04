<?php


class CartController extends CartControllerCore
{
    public const MUESTRAS_CATEGORY_ID = '1751';

    public function initContent()
    {
        $this->sanitizeMuestrasCart($this->context->cart);
        parent::initContent();
    }
    
    protected function sanitizeMuestrasCart(Cart $cart)
    {
        if (!(int)$cart->id) {
            return;
        }

        $products = $cart->getProducts();
        if (empty($products)) {
            return;
        }

        foreach ($products as $p) {
            $qty = (int)$p['cart_quantity'];
            if ($qty <= 1) {
                continue;
            }

            $idProduct = (int)$p['id_product'];

            // comprobar si pertenece a la categoría "muestras"
            $cats = Product::getProductCategories($idProduct);
            if (empty($cats) || !in_array(self::MUESTRAS_CATEGORY_ID, $cats)) {
                continue;
            }

            $idProductAttribute = (int)$p['id_product_attribute'];
            $idCustomization = isset($p['id_customization']) ? (int)$p['id_customization'] : 0;
            $idAddressDelivery = isset($p['id_address_delivery']) ? (int)$p['id_address_delivery'] : 0;

            // bajar a 1 => resta (qty - 1)
            $diff = $qty - 1;

            $cart->updateQty(
                $diff,
                $idProduct,
                $idProductAttribute,
                $idCustomization,
                'down',
                $idAddressDelivery
            );
        }

        $cart->save();
    }

    public function displayAjaxRefresh()
    {
        if (Configuration::isCatalogMode()) {
            return;
        }

        ob_end_clean();
        header('Content-Type: application/json');
        $this->ajaxRender(Tools::jsonEncode([
            'cart_detailed' => $this->render('checkout/_partials/cart-detailed'),
            'cart_detailed_totals' => $this->render('checkout/_partials/cart-detailed-totals'),
            'cart_summary_items_subtotal' => $this->render('checkout/_partials/cart-summary-items-subtotal'),
            'cart_summary_products' => $this->render('checkout/_partials/cart-summary-products'),
            'cart_summary_subtotals_container' => $this->render('checkout/_partials/cart-summary-subtotals'),
            'cart_summary_totals' => $this->render('checkout/_partials/cart-summary-totals'),
            'cart_detailed_actions' => $this->render('checkout/_partials/cart-detailed-actions'),
            'cart_voucher' => $this->render('checkout/_partials/cart-voucher'),
            'cart_summary_top' => $this->render('checkout/_partials/cart-summary-top'),
        ]));
    }

}