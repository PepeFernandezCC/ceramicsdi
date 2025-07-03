<?php
/**
 * 2020 4webs
 *
 * DEVELOPED By 4webs.es Prestashop Platinum Partner
 *
 * @author    4webs
 * @copyright 4webs 2019
 * @license   4webs
 * @version 5.1.4
 * @category payment_gateways
 */

if (!defined('_PS_VERSION_')) {
	exit;
}

include(_PS_MODULE_DIR_ . 'paypalwithfee' . DIRECTORY_SEPARATOR . 'api/Paypalwf.php');

use Fourwebs\PaypalWithFee\Paypalwf;

class PayPalwithFeePaymentModuleFrontController extends ModuleFrontController
{
	public $php_self = 'paymentppwf';
	public $ssl = true;
	public $display_column_left = false;
	public $currency;
	public $decimals;
	public $round_active;



	public function initContent()
	{
		parent::initContent();

		$this->currency = new Currency((int) $this->context->cart->id_currency);
		$this->round_active = Configuration::get('PPAL_ROUND_MODE');
		$this->decimals = _PS_PRICE_DISPLAY_PRECISION_;
		//always two decimals to paypal
		if ($this->decimals > 2) {
			$this->decimals = 2;
		}


		$gift_wrapping_fee = array();
		$gift_wrapping = $this->context->cart->gift;

		if ($gift_wrapping == 1) {
			$gift_wrapping_fee['gift_amount'] = Tools::convertPrice(
				Tools::ps_round(
					$this->context->cart->getGiftWrappingPrice(true),
					$this->decimals
				),
				Currency::getCurrencyInstance((int) $this->context->cookie->id_currency)
			);
			$gift_wrapping_fee['gift_amount_no_tax'] = Tools::convertPrice(
				Tools::ps_round(
					$this->context->cart->getGiftWrappingPrice(false),
					$this->decimals
				),
				Currency::getCurrencyInstance((int) $this->context->cookie->id_currency)
			);
			$gift_wrapping_fee['gift_name'] = $this->module->l('Gift-wrapping', 'payment');
			$gift_wrapping_fee['gift_description'] = $this->module->l('Gift-wrapping cost', 'payment');
		} else {
			$gift_wrapping_fee = 0;
		}

		//discounts + gift products + free shipping
		$discounts = array();
		//FILTER_ACTION_REDUCTION
		//FILTER_ACTION_ALL_NOCAP
		$array_discount = $this->context->cart->getCartRules(CartRule::FILTER_ACTION_ALL_NOCAP);

		$position = 0;

		if (count($array_discount) > 0) {
			foreach ($array_discount as $value) {
				$position += 1;

				$discounts[$position]['total'] = Tools::ps_round($value['value_real'], $this->decimals);
				$discounts[$position]['total_no_tax'] = Tools::ps_round($value['value_tax_exc'], $this->decimals);
				$discounts[$position]['name'] = $this->module->l('Discount', 'payment');
				$discounts[$position]['desc'] = $value['code'];
			}
		}

		//apply amount of products always 2 decimals.
		$product_list = $this->context->cart->getProducts(true);

		$products_total_amount = $this->round_active ? $this->context->cart->getOrderTotal(
			true,
			Cart::ONLY_PRODUCTS
		) : $this->getTotalAmount($product_list);
		$products_total_amount_no_tax = $this->round_active ? $this->context->cart->getOrderTotal(
			false,
			Cart::ONLY_PRODUCTS
		) : $this->getTotalAmount($product_list, false);
		$discounts_amount = $this->getTotalDiscounts($discounts);
		$discounts_amount_no_tax = $this->getTotalDiscounts($discounts, false);
		$shipping_amount = Tools::ps_round($this->context->cart->getTotalShippingCost(), $this->decimals);
		$shipping_amount_no_tax = Tools::ps_round(
			$this->context->cart->getTotalShippingCost(null, false),
			$this->decimals
		);

		if ($gift_wrapping_fee) {
			$total_amount_final = ($products_total_amount + $shipping_amount + $gift_wrapping_fee['gift_amount']) - $discounts_amount;
			$total_amount_final_no_tax = ($products_total_amount_no_tax + $shipping_amount_no_tax + $gift_wrapping_fee['gift_amount_no_tax']) - $discounts_amount_no_tax;
		} else {
			$total_amount_final = ($products_total_amount + $shipping_amount) - $discounts_amount;
			$total_amount_final_no_tax = ($products_total_amount_no_tax + $shipping_amount_no_tax) - $discounts_amount_no_tax;
		}

		$user = Configuration::get('PPAL_FEE_USER');
		$password = Configuration::get('PPAL_FEE_PASS');

		if (Tools::getIsset('ajax')) {
			$json_data = Tools::file_get_contents('php://input');
			$data = json_decode($json_data, true);
			$returnURL = $data['returnURL'];
			$cancelURL = $data['cancelURL'];
		} else {
			$returnURL = Tools::getValue('returnURL');
			$cancelURL = Tools::getValue('cancelURL');
		}


		$paypalwithfee = new Paypalwithfee();
		$fee_data = $paypalwithfee->getFee($this->context->cart);
		$fee_amount = $fee_data['fee_with_tax'];
		$fee_amount_no_tax = $fee_data['fee_without_tax'];
		$total_tax = $total_amount_final - $total_amount_final_no_tax + ($fee_amount - $fee_amount_no_tax);

		$paypal = new Paypalwf($user, $password);

		$purchaseUnits = [
			'item_total' => 0.00,
			'real_paypal' => 0.00,
			'tax_total' => 0.00,
			'shipping' => 0.00,
			'handling' => 0.00,
			'shipping_discount' => 0.00,
			'discount' => 0.00
		];
		$purchaseProducts = array();

		//FEE
		if ($fee_amount > 0) {
			$purchaseUnits['item_total'] += $this->round_active ? $fee_amount_no_tax : $fee_amount;
			//Add only taxes if round mode == 0
			if ($this->round_active) {
				$purchaseUnits['tax_total'] += ($fee_amount - $fee_amount_no_tax);
			}

			//Add the to the array object
			$purchaseProducts[] = [
				'name' => $this->setMaxStrlen($this->module->l('Fee', 'payment')),
				'quantity' => 1,
				'unit_amount' => [
					'currency_code' => $this->currency->iso_code,
					'value' => number_format($this->round_active ? $fee_amount_no_tax : $fee_amount, 2, '.', '')
				],
				'description' => $this->setMaxStrlen($this->module->l('Paypal Fee', 'payment'))
			];

			$purchaseUnits['real_paypal'] += $this->round_active ? $fee_amount_no_tax : $fee_amount;
		}

		//SHIPPING
		if ($shipping_amount > 0) {
			$purchaseUnits['shipping'] += $this->round_active ? $shipping_amount_no_tax : $shipping_amount;
			//Add only taxes if round mode == 0
			if ($this->round_active) {
				$purchaseUnits['tax_total'] += ($shipping_amount - $shipping_amount_no_tax);
			}
		}


		//PRODUCTS
		foreach ($product_list as $product) {
			$purchaseUnits['item_total'] += $this->round_active ? number_format(
				$product['total'], $this->decimals,
				'.',
				''
			) : Tools::ps_round($product['total_wt'], $this->decimals);
			//Add only taxes if round mode == 0
			if ($this->round_active) {
				$purchaseUnits['tax_total'] += number_format(
					($product['total_wt'] - $product['total']),
					$this->decimals,
					'.',
					''
				);
			}

			//Add the to the array object
			$purchaseProducts[] = [
				'name' => $this->setMaxStrlen(
					$this->round_active ? $product['quantity'] . ' x ' . $product['name'] : $product['name']
				),
				'quantity' => $this->round_active ? 1 : $product['quantity'],
				'unit_amount' => [
					'currency_code' => $this->currency->iso_code,
					'value' => $this->round_active ? str_replace(
						',',
						'',
						number_format($product['total'], $this->decimals)
					) : str_replace(
						',',
						'',
						Tools::ps_round($product['price_wt'], $this->decimals, '.', '')
					)
				],
				'description' => $this->setMaxStrlen($product['description_short'])
			];

			$purchaseUnits['real_paypal'] += $this->round_active ? str_replace(
				',',
				'',
				number_format($product['total'], $this->decimals)
			) : (
					str_replace(
						',',
						'',
						Tools::ps_round($product['price_wt'], $this->decimals, '.', '') * $product['quantity']
					)
				);
		}

		//WRAPPING FEES
		if (is_array($gift_wrapping_fee) && count($gift_wrapping_fee) > 0) {
			$purchaseUnits['handling'] += $this->round_active ?
				$gift_wrapping_fee['gift_amount_no_tax'] : $gift_wrapping_fee['gift_amount'];
			//Add only taxes if round mode == 0
			if ($this->round_active) {
				$purchaseUnits['tax_total'] +=
					($gift_wrapping_fee['gift_amount'] - $gift_wrapping_fee['gift_amount_no_tax']);
			}
		}

		//GLOBAL ORDER DISCOUNTS
		if (is_array($discounts) && count($discounts) > 0) {
			foreach ($discounts as $discount) {
				$purchaseUnits['discount'] += $this->round_active ?
					$discount['total_no_tax'] : $discount['total'];
			}
		}

		//Build the final request object
		foreach ($purchaseUnits as &$pItem) {
			$pItem = number_format($pItem, 2, '.', '');
		}

		//Calculate the total
		//first compare totals paypal and prestashop if not match
		if ($purchaseUnits['item_total'] != $purchaseUnits['real_paypal']) {
			$purchaseUnits['item_total'] = $purchaseUnits['real_paypal'];
		}

		if ($this->round_active) {
			if ($purchaseUnits['tax_total'] != $total_tax) {
				$purchaseUnits['tax_total'] = number_format($total_tax, $this->decimals, '.', '');
			}
		}

		$total = Tools::ps_round($purchaseUnits['item_total'] +
			$purchaseUnits['tax_total'] +
			$purchaseUnits['shipping'] +
			$purchaseUnits['handling'] -
			$purchaseUnits['shipping_discount'] -
			$purchaseUnits['discount'], $this->decimals);

		//check if the discount exceeds the total payment to fix it.
		if ($total < 0) {
			$totalOrderWithAll = $this->context->cart->getOrderTotal(true);

			$newDiscount = (float) $purchaseUnits['discount'] + $total - $totalOrderWithAll - $fee_amount;
			$purchaseUnits['discount'] = number_format(Tools::ps_round($newDiscount, $this->decimals), 2, '.', '');

			//Recalculate the totals
			$total = Tools::ps_round(
				$purchaseUnits['item_total'] + $purchaseUnits['shipping'] + $purchaseUnits['handling'] - $purchaseUnits['shipping_discount'] - $purchaseUnits['discount'],
				$this->decimals
			);

			//Recalculate the totals
			$total = Tools::ps_round(
				$purchaseUnits['item_total'] + $purchaseUnits['shipping'] + $purchaseUnits['handling'] - $purchaseUnits['shipping_discount'] - $purchaseUnits['discount'],
				$this->decimals
			);
		}

		foreach ($purchaseProducts as $key => $value) {
			if ($value['name'] != 'Recargo') {
				$amount = $value['unit_amount']['value'];
				$amount_replace = str_replace(',', '', $amount);

				//$purchaseProducts[$key]['unit_amount']['value'] = $amount);
				$purchaseProducts[$key]['unit_amount']['value'] = $amount_replace;
			}
		}

		//Build the request
		if (Tools::getIsset('ajax') || Tools::getIsset('paylater')) {
			$request = $paypal->createOrder(
				[
					[
						'items' => $purchaseProducts,
						'payment_source' => array(
							'paypal' => array(
								'experience_context' => array(
									'payment_method_selected' => 'PAYPAL_PAYLATER',
									'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED'
								),
							),
						),
						'amount' => [
							'value' => number_format($total, 2, '.', ''),
							'currency_code' => $this->currency->iso_code,
							'breakdown' =>
							[
								"item_total" => [
									"currency_code" => $this->currency->iso_code,
									"value" => $purchaseUnits['item_total']
								],
								"tax_total" => [
									"currency_code" => $this->currency->iso_code,
									"value" => $purchaseUnits['tax_total']
								],
								"shipping" => [
									"currency_code" => $this->currency->iso_code,
									"value" => $purchaseUnits['shipping']
								],
								"handling" => [
									"currency_code" => $this->currency->iso_code,
									"value" => $purchaseUnits['handling']
								],
								"shipping_discount" => [
									"currency_code" => $this->currency->iso_code,
									"value" => $purchaseUnits['shipping_discount']
								],
								"discount" => [
									"currency_code" => $this->currency->iso_code,
									"value" => $purchaseUnits['discount']
								]
							]
						]
					]
				],
				[
					"cancel_url" => $cancelURL,
					"return_url" => $returnURL
				]
			);
		} else {
			$request = $paypal->createOrder(
				[
					[
						'items' => $purchaseProducts,
						'amount' => [
							'value' => number_format($total, 2, '.', ''),
							'currency_code' => $this->currency->iso_code,
							'breakdown' =>
							[
								"item_total" => [
									"currency_code" => $this->currency->iso_code,
									"value" => $purchaseUnits['item_total']
								],
								"tax_total" => [
									"currency_code" => $this->currency->iso_code,
									"value" => $purchaseUnits['tax_total']
								],
								"shipping" => [
									"currency_code" => $this->currency->iso_code,
									"value" => $purchaseUnits['shipping']
								],
								"handling" => [
									"currency_code" => $this->currency->iso_code,
									"value" => $purchaseUnits['handling']
								],
								"shipping_discount" => [
									"currency_code" => $this->currency->iso_code,
									"value" => $purchaseUnits['shipping_discount']
								],
								"discount" => [
									"currency_code" => $this->currency->iso_code,
									"value" => $purchaseUnits['discount']
								]
							]
						]
					]
				],
				[
					"cancel_url" => $cancelURL,
					"return_url" => $returnURL
				]
			);
		}

		//now, send the request to the paypal servers
		$response = $paypal->executeRequest($request);

		if ($response['result'] == 'ok') {
			if ($response['data']->statusCode == '201') {
				if (Tools::getIsset('ajax')) {
					die(json_encode($response['data']->result));
				}
				foreach ($response['data']->result->links as $link) {
					if ($link->rel == 'approve') {
                        $this->module->updateCartHash();
						$this->redirectPaypal($link->href);
						break;
					}
				}
			} else {
				return $this->error($paypal, $response, $request);
			}
		} else {
			return $this->error($paypal, $response, $request);
		}
	}

	protected function error($paypal, $response, $request)
	{
		$this->context->smarty->assign(
			array(
				'error_paypal' => $response['data'],
				'this_path' => $this->module->getPathUri(),
				'this_path_check' => $this->module->getPathUri(),
				'this_path_ssl' => Tools::getShopDomainSsl(true, true) .
				__PS_BASE_URI__ . 'modules/' . $this->module->name . '/'
			)
		);
		$paypal->logError($this->context->cart, $request, $response['data']);
		return $this->setTemplate('module:paypalwithfee/views/templates/front/error.tpl');
	}

	public static function setMaxStrlen($str)
	{
		if (Tools::strlen($str) > 125) {
			return strip_tags(Tools::substr($str, 0, 125));
		}
		return strip_tags($str);
	}

	protected function redirectPaypal($url)
	{
		if (Tools::getIsset('paylater')) {
			$url .= '&fundingSource=paylater';
		}

		$this->context->smarty->assign(
			array(
				'ruta' => $url
			)
		);

		Tools::redirect($url);
	}

	public function getTotalAmount($products, $tax = true)
	{
		$total = 0;
		if ($tax) {
			foreach ($products as $product) {
				$total = $total + (Tools::ps_round($product['price_wt'], $this->decimals) * $product['quantity']);
			}
		} else {
			foreach ($products as $product) {
				$total = $total + (Tools::ps_round($product['price'], $this->decimals) * $product['quantity']);
			}
		}

		return $total;
	}

	public function getTotalDiscounts($discounts, $tax = true)
	{
		$total = 0;
		if ($tax) {
			if (count($discounts) > 0) {
				foreach ($discounts as $discount) {
					$total = $total + $discount['total'];
				}
			}
		} else {
			if (count($discounts) > 0) {
				foreach ($discounts as $discount) {
					$total = $total + $discount['total_no_tax'];
				}
			}
		}
		return $total;
	}
}