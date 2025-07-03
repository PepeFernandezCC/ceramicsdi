<?php

/**
 * @author Julio Colás
 */
class OrderHistory extends OrderHistoryCore {
	
	/**
	 * @param Order $order
	 * @param array|false $template_vars
	 *
	 * @return bool
	 */
	public function sendEmail($order, $template_vars = false)
	{
		$result = Db::getInstance()->getRow('
            SELECT osl.`template`, c.`lastname`, c.`firstname`, osl.`name` AS osname, c.`email`, os.`module_name`, os.`id_order_state`, os.`pdf_invoice`, os.`pdf_delivery`
            FROM `' . _DB_PREFIX_ . 'order_history` oh
                LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON oh.`id_order` = o.`id_order`
                LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON o.`id_customer` = c.`id_customer`
                LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON oh.`id_order_state` = os.`id_order_state`
                LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = o.`id_lang`)
            WHERE oh.`id_order_history` = ' . (int) $this->id . ' AND os.`send_email` = 1');
		if (isset($result['template']) && Validate::isEmail($result['email'])) {
			ShopUrl::cacheMainDomainForShop($order->id_shop);
			
			$topic = $result['osname'];
			$carrierUrl = '';
			if (Validate::isLoadedObject($carrier = new Carrier((int) $order->id_carrier, $order->id_lang))) {
				$carrierUrl = $carrier->url;
			}
			/**
			 * PLANATEC
			 */
			$delivery = new Address((int) $order->id_address_delivery);
			$addressFormated = AddressFormat::generateAddress($delivery, ['avoid' => []], '<br />', ' ', [
				'firstname' => '<span style="font-weight:bold;">%s</span>',
				'lastname' => '<span style="font-weight:bold;">%s</span>',
			]);
			$addressFormatedTxt = AddressFormat::generateAddress($delivery, ['avoid' => []], '\n', ' ');
			
			$invoice = new Address($order->id_address_invoice);
			$invoiceAddressFormated = AddressFormat::generateAddress($invoice, ['avoid' => []], '<br />', ' ', [
				'firstname' => '<span style="font-weight:bold;">%s</span>',
				'lastname' => '<span style="font-weight:bold;">%s</span>',
			]);
			$invoiceAddressFormatedTxt = AddressFormat::generateAddress($invoice, ['avoid' => []], '\n', ' ');
			
			$context = Context::getContext();
			
			$productTemplateList = $this->getProductList($order, $context);
			$productListTxt = $this->customRender($context, 'order_conf_product_list.txt', $context->language, $productTemplateList);
			$productListHtml = $this->customRender($context,'order_conf_product_list.tpl', $context->language, $productTemplateList);
			
			$cartRulesList[] = [
				'voucher_name' => 'Promo code',
				'voucher_reduction' => '-' . Tools::getContextLocale($context)->formatPrice(5, $context->currency->iso_code),
			];
			$cartRulesListTxt = $this->customRender($context, 'order_conf_cart_rules.txt', $context->language, $cartRulesList);
			$cartRulesListHtml = $this->customRender($context,'order_conf_cart_rules.tpl', $context->language, $cartRulesList);
			
			
			/**
			 * END PLANATEC
			 */
			$data = [
				'{lastname}' => $result['lastname'],
				'{firstname}' => $result['firstname'],
				'{id_order}' => (int) $this->id_order,
				'{order_name}' => $order->getUniqReference(),
				'{followup}' => str_replace('@', $order->getWsShippingNumber(), $carrierUrl),
				'{shipping_number}' => $order->getWsShippingNumber(),
				// PLANATEC
				'{date}' => Tools::displayDate($order->date_add, null, 1),
				'{payment}' => Tools::substr($order->payment, 0, 255),
				'{total_products}' => count($order->getProducts()),
				'{total_discounts}' => Tools::getContextLocale($context)->formatPrice($order->total_discounts, $context->currency->iso_code),
				'{total_wrapping}' => Tools::getContextLocale($context)->formatPrice($order->total_wrapping, $context->currency->iso_code),
				'{total_shipping}' => Tools::getContextLocale($context)->formatPrice($order->total_shipping, $context->currency->iso_code),
				'{total_shipping_tax_excl}' => Tools::getContextLocale($context)->formatPrice($order->total_shipping_tax_excl, $context->currency->iso_code),
				'{total_shipping_tax_incl}' => Tools::getContextLocale($context)->formatPrice($order->total_shipping_tax_incl, $context->currency->iso_code),
				'{total_tax_paid}' => Tools::getContextLocale($context)->formatPrice(($order->total_products_wt - $order->total_products) + ($order->total_shipping_tax_incl - $order->total_shipping_tax_excl), $context->currency->iso_code),
				'{total_paid}' => Tools::getContextLocale($context)->formatPrice($order->total_paid, $context->currency->iso_code),
				'{carrier}' => $carrier->name,
				'{delivery_block_txt}' => $addressFormatedTxt,
				'{invoice_block_txt}' => $invoiceAddressFormatedTxt,
				'{delivery_block_html}' => $addressFormated,
				'{invoice_block_html}' => $invoiceAddressFormated,
				'{products}' => $productListHtml,
				'{products_txt}' => $productListTxt,
				'{discounts}' => $cartRulesListHtml,
				'{discounts_txt}' => $cartRulesListTxt,
				// END PLANATEC
				];
			
			if ($result['module_name']) {
				$module = Module::getInstanceByName($result['module_name']);
				if (Validate::isLoadedObject($module) && isset($module->extra_mail_vars) && is_array($module->extra_mail_vars)) {
					$data = array_merge($data, $module->extra_mail_vars);
				}
			}
			
			if (is_array($template_vars)) {
				$data = array_merge($data, $template_vars);
			}
			
			//$context = Context::getContext();
			$data['{total_paid}'] = Tools::getContextLocale($context)->formatPrice((float) $order->total_paid, Currency::getIsoCodeById((int) $order->id_currency));
			
			if (Validate::isLoadedObject($order)) {
				// Attach invoice and / or delivery-slip if they exists and status is set to attach them
				if (($result['pdf_invoice'] || $result['pdf_delivery'])) {
					$currentLanguage = $context->language;
					$orderLanguage = new Language((int) $order->id_lang);
					$context->language = $orderLanguage;
					$context->getTranslator()->setLocale($orderLanguage->locale);
					$invoice = $order->getInvoicesCollection();
					$file_attachement = [];
					
					if ($result['pdf_invoice'] && (int) Configuration::get('PS_INVOICE') && $order->invoice_number) {
						Hook::exec('actionPDFInvoiceRender', ['order_invoice_list' => $invoice]);
						$pdf = new PDF($invoice, PDF::TEMPLATE_INVOICE, $context->smarty);
						$file_attachement['invoice']['content'] = $pdf->render(false);
						$file_attachement['invoice']['name'] = Configuration::get('PS_INVOICE_PREFIX', (int) $order->id_lang, null, $order->id_shop) . sprintf('%06d', $order->invoice_number) . '.pdf';
						$file_attachement['invoice']['mime'] = 'application/pdf';
					}
					if ($result['pdf_delivery'] && $order->delivery_number) {
						$pdf = new PDF($invoice, PDF::TEMPLATE_DELIVERY_SLIP, $context->smarty);
						$file_attachement['delivery']['content'] = $pdf->render(false);
						$file_attachement['delivery']['name'] = Configuration::get('PS_DELIVERY_PREFIX', (int) $order->id_lang, null, $order->id_shop) . sprintf('%06d', $order->delivery_number) . '.pdf';
						$file_attachement['delivery']['mime'] = 'application/pdf';
					}
					
					$context->language = $currentLanguage;
					$context->getTranslator()->setLocale($currentLanguage->locale);
				} else {
					$file_attachement = null;
				}
				
				if (!Mail::Send(
					(int) $order->id_lang,
					$result['template'],
					$topic,
					$data,
					$result['email'],
					$result['firstname'] . ' ' . $result['lastname'],
					null,
					null,
					$file_attachement,
					null,
					_PS_MAIL_DIR_,
					false,
					(int) $order->id_shop
				)) {
					return false;
				}
			}
			
			ShopUrl::resetMainDomainCache();
		}
		
		return true;
	}
	
	/**
	 * PLANATEC
	 */
	/**
	 * @param Order   $order
	 * @param Context $context
	 *
	 * @return array
	 *
	 * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
	 */
	private function getProductList(Order $order, Context $context)
	{
		$cart = new Cart($order->id_cart);
		$packageList = $cart->getPackageList();
		$package = current(current($packageList));
		$productList = $package['product_list'];
		
		$productTemplateList = [];
		foreach ($productList as $product) {
			$price = Product::getPriceStatic((int) $product['id_product'], false, ($product['id_product_attribute'] ? (int) $product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int) $order->id_customer, (int) $order->id_cart, (int) $order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}, $specific_price, true, true, null, true, $product['id_customization']);
			$priceWithTax = Product::getPriceStatic((int) $product['id_product'], true, ($product['id_product_attribute'] ? (int) $product['id_product_attribute'] : null), 2, null, false, true, $product['cart_quantity'], false, (int) $order->id_customer, (int) $order->id_cart, (int) $order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}, $specific_price, true, true, null, true, $product['id_customization']);
			
			$productPrice = Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($price, 2) : $priceWithTax;
			
			$deliveryInStock = Product::getDeliveryInStock($context->language->getId(), $product['id_product']);
			if (!empty($deliveryInStock) and !is_null($deliveryInStock)) {
				$deliveryInStock = '<br><br>' . $deliveryInStock[0]['delivery_in_stock'];
			}
			
			$productTemplate = [
				'id_product' => $product['id_product'],
				'id_product_attribute' => $product['id_product_attribute'],
				'reference' => $product['reference'],
				'name' => $product['name'] . (isset($product['attributes']) ? ' - ' . $product['attributes'] : ''),
				'price' => Tools::getContextLocale($context)->formatPrice($productPrice * $product['quantity'], $context->currency->iso_code),
				'quantity' => $product['quantity'],
				'customization' => [],
				'delivery_in_stock' => $deliveryInStock
			];
			
			if (isset($product['price']) && $product['price']) {
				$productTemplate['unit_price'] = Tools::getContextLocale($context)->formatPrice($productPrice, $context->currency->iso_code);
				$productTemplate['unit_price_full'] = Tools::getContextLocale($context)->formatPrice($productPrice, $context->currency->iso_code)
					. ' ' . $product['unity'];
			} else {
				$productTemplate['unit_price'] = $productTemplate['unit_price_full'] = '';
			}
			
			$customizedDatas = Product::getAllCustomizedDatas((int) $order->id_cart, null, true, null, (int) $product['id_customization']);
			if (isset($customizedDatas[$product['id_product']][$product['id_product_attribute']])) {
				$productTemplate['customization'] = [];
				foreach ($customizedDatas[$product['id_product']][$product['id_product_attribute']][$order->id_address_delivery] as $customization) {
					$customizationText = '';
					if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD])) {
						foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text) {
							$customizationText .= '<strong>' . $text['name'] . '</strong>: ' . $text['value'] . '<br />';
						}
					}
					
					if (isset($customization['datas'][Product::CUSTOMIZE_FILE])) {
						$customizationText .= $this->trans('%d image(s)', [count($customization['datas'][Product::CUSTOMIZE_FILE])], 'Admin.Payment.Notification') . '<br />';
					}
					
					$customizationQuantity = (int) $customization['quantity'];
					
					$productTemplate['customization'][] = [
						'customization_text' => $customizationText,
						'customization_quantity' => $customizationQuantity,
						'quantity' => Tools::getContextLocale($context)->formatPrice($customizationQuantity * $productPrice, $context->currency->iso_code),
					];
				}
			}
			$productTemplateList[] = $productTemplate;
		}
		
		return $productTemplateList;
	}
	
	private function customRender(Context $context, $partialTemplateName, \PrestaShop\PrestaShop\Core\Language\LanguageInterface $language, array $variables = [], $cleanComments = false) {
		$smarty = $context->smarty;
	
		$potentialPaths = [
			_PS_THEME_DIR_ . 'mails' . DIRECTORY_SEPARATOR . $language->getIsoCode() . DIRECTORY_SEPARATOR . $partialTemplateName,
			_PS_MAIL_DIR_ . $language->getIsoCode() . DIRECTORY_SEPARATOR . $partialTemplateName,
			_PS_THEME_DIR_ . 'mails' . DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR . $partialTemplateName,
			_PS_MAIL_DIR_ . 'en' . DIRECTORY_SEPARATOR . $partialTemplateName,
			_PS_MAIL_DIR_ . '_partials' . DIRECTORY_SEPARATOR . $partialTemplateName,
		];
		
		foreach ($potentialPaths as $path) {
			if (Tools::file_exists_cache($path)) {
				$smarty->assign('list', $variables);
				$content = $smarty->fetch($path);
				if ($cleanComments) {
					$content = preg_replace('/\s?<!--.*?-->\s?/s', '', $content);
				}
				
				return $content;
			}
		}
		
		return '';
	}
	/**
	 * END PLANATEC
	 */
}