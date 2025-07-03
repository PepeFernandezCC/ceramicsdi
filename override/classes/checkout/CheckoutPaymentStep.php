<?php

/**
 * @author Julio Colás
 */
class CheckoutPaymentStep extends CheckoutPaymentStepCore {
	
	public function handleRequest(array $requestParams = [])
	{
		$cart = $this->getCheckoutSession()->getCart();
		
		/* PLANATEC 
		if (count($cart->getProducts()) > 10) {
			$cartShowUrl = $this->context->link->getPageLink(
				'cart',
				null,
				$this->context->language->id,
				[
					'action' => 'show',
				],
				false,
				null,
				false
			);
			Tools::redirect($cartShowUrl);
		}
		 END PLANATEC */
		
		$allProductsInStock = $cart->isAllProductsInStock();
		$allProductsExist = $cart->checkAllProductsAreStillAvailableInThisState();
		$allProductsHaveMinimalQuantity = $cart->checkAllProductsHaveMinimalQuantities();
		
		if ($allProductsInStock !== true || $allProductsExist !== true || $allProductsHaveMinimalQuantity !== true) {
			$cartShowUrl = $this->context->link->getPageLink(
				'cart',
				null,
				$this->context->language->id,
				[
					'action' => 'show',
				],
				false,
				null,
				false
			);
			Tools::redirect($cartShowUrl);
		}
		
		if (isset($requestParams['select_payment_option'])) {
			$this->selected_payment_option = $requestParams['select_payment_option'];
		}
		
		$this->setTitle(
			$this->getTranslator()->trans(
				'Payment',
				[],
				'Shop.Theme.Checkout'
			)
		);
	}
}