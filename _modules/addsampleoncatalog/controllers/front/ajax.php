<?php
class AddsampleoncatalogAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $this->addSampleAction();

    }

    public function addSampleAction() {

        header('Content-Type: application/json');

        $response = [
            'success' => true, 
            'msg' => 'Estoy Dentro'
        ];

        return json_encode($response);

        /*
        if (Tools::getValue('action') === 'addToCart') {
            $productId = (int) Tools::getValue('id_product');
            $cartId = (int) Tools::getValue('id_cart');

            // Lógica para agregar el producto al carrito

            // Ejemplo:
            $cart = new Cart($cartId);
            $cart->updateQty(1, $productId);

            if ($cart->update()) {
                return (json_encode(['success' => true]));
            } else {
                return (json_encode(['success' => false, 'error' => 'Error al agregar el producto al carrito']));
            }
        }
        */       
    }
}