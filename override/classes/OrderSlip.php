<?php

class OrderSlip extends OrderSlipCore {
	
	/**
	 * @param int $orderSlipId
	 * @param Order $order
	 *
	 * @return array
	 */
	public static function getOrdersSlipProducts($orderSlipId, $order)
	{
		$productsRet = OrderSlip::getOrdersSlipDetail($orderSlipId);
		$order_details = $order->getProductsDetail();
		
		$slip_quantity = [];
		foreach ($productsRet as $slip_detail) {
			$slip_quantity[$slip_detail['id_order_detail']] = $slip_detail;
		}
		
		$products = [];
		foreach ($order_details as $key => $product) {
			if (isset($slip_quantity[$product['id_order_detail']]) && $slip_quantity[$product['id_order_detail']]['product_quantity']) {
				$products[$key] = $product;
				$products[$key] = array_merge($products[$key], $slip_quantity[$product['id_order_detail']]);
			}
		}
		
		/**
		 * PLANATEC
		 */
		if (empty($products)) {
			return $products;
		}
		/**
		 * END PLANATEC
		 */
		
		return $order->getProducts($products);
	}
}