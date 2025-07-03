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

class RefundppwfController extends ModuleAdminController
{
    public $currency;
    public $decimals;

    public function __construct()
    {
        parent::__construct();
    }

    public function init()
    {
        parent::init();

        if (Tools::isSubmit('ppwf_refund')) {
            $id_order = Tools::getValue('id_order');
            $order = new Order($id_order);
            $currency = new Currency((int) $order->id_currency);
            $amount = 0.00;
            $amount_init = 0.00;

            $currency_decimals = is_array($currency) ? (int) $currency['decimals'] : (int) $currency->decimals;
            $this->decimals = $currency_decimals * _PS_PRICE_DISPLAY_PRECISION_;

            if ($this->decimals > 2) {
                $this->decimals = 2;
            }

            $transaction_id = PaypalRefund::getTransactionID($id_order);

            $refund_type = Tools::getValue('refund');
            if ($refund_type == 0) {
                $amount_init = Tools::getValue('ppwf_refund_amount');
            }

            if (empty($transaction_id) || $transaction_id == '-') {
                Tools::redirectAdmin('index.php?tab=AdminOrders&id_order=' . $id_order . '&messageppwf=ppwf1&vieworder'
                    . '&token=' . Tools::getAdminTokenLite('AdminOrders') . '#paypalwithfee_refund');
            }
			if (isset($amount_init)) {
				if ((!preg_match('/-?^[0-9]{1,10}+(?:\.[0-9]{1,2})?$/',
							$amount_init) && !preg_match('/-?^[0-9]{1,10}+(?:\,[0-9]{1,2})?$/',
							$amount_init)) && $refund_type != 1) {
					Tools::redirectAdmin('index.php?tab=AdminOrders&id_order=' . $id_order
						. '&messageppwf=ppwf2&vieworder' . '&token=' . Tools::getAdminTokenLite('AdminOrders')
						. '#paypalwithfee_refund');
				} else {
					if ($refund_type == 0) {
						$amount_init = str_replace(',', '.', $amount_init);
						$amount = number_format(Tools::convertPrice($amount_init), $this->decimals);
					}
				}
			}

            $refund_type = $refund_type == 1 ? 'Full' : 'Partial';
            $user = Configuration::get('PPAL_FEE_USER');
            $password = Configuration::get('PPAL_FEE_PASS');

            $paypal = new Paypalwf($user, $password);

            //Inflate the request
            $request = $paypal->createPaymentRefund(
                '',
                $order->reference,
                $currency->iso_code,
                $transaction_id,
                $refund_type == 'Full' ? 0 : $amount,
                ($refund_type == 'Full')
            );

            $response = $paypal->executeRequest($request);

            //
            if ($response['result'] != 'ok') {
                $paypal->logError($id_order, $request, $response['data']);
                Tools::redirectAdmin('index.php?tab=AdminOrders&id_order=' . $id_order . '&messageppwf=error&vieworder'
                    . '&token=' . Tools::getAdminTokenLite('AdminOrders') . '#paypalwithfee_refund');
            } else {
                if ($response['data']->statusCode != 201) {
                    $paypal->logError($id_order, $request, $response['data']);
                    Tools::redirectAdmin('index.php?tab=AdminOrders&id_order=' . $id_order .
                        '&messageppwf=error&vieworder' . '&token=' . Tools::getAdminTokenLite('AdminOrders') .
                        '#paypalwithfee_refund');
                }
            }

            if ($response) {
                $paypal_refund = new PaypalRefund();
                $paypal_refund->id_ppwf = PaypalRefund::getPpwfID($id_order);
                $paypal_refund->id_order = $id_order;
                $paypal_refund->amount = $refund_type == 'Full' ? $order->getTotalPaid() : $amount;
                $paypal_refund->transaction_id = $response['data']->result->id;
                $paypal_refund->date = date('Y-m-d H:i:s');
                $paypal_refund->add();
                Tools::redirectAdmin('index.php?tab=AdminOrders&id_order=' . $id_order . '&messageppwf=ok&vieworder' .
                    '&token=' . Tools::getAdminTokenLite('AdminOrders') . '#paypalwithfee_refund');
            }
        } elseif (Tools::getValue('submitAction') == 'ppwf_pdf') {
            $id_order = Tools::getValue('id_order');
            $order = new Order($id_order);
            $id_order_invoice = $order->invoice_number;
            $invoice = $this->getInvoiceByNumber($id_order, $id_order_invoice);
            $html_template_invoice = new HTMLTemplateInvoice($invoice, Context::getContext()->smarty);
            $id_lang = Context::getContext()->language->id;
            $html_template_invoice->title = $invoice->getInvoiceNumberFormatted($id_lang, (int) $order->id_shop);
            $template = $html_template_invoice;
            $pdf = new PDFGenerator((bool) Configuration::get('PS_PDF_USE_CACHE'), 'P');

            $fee = $this->getFeeData($id_order);

            $content = $this->getPdfData($html_template_invoice, $order, $fee, $id_order);

            $pdf->createHeader($template->getHeader());
            $pdf->createContent($content);
            $pdf->createFooter($template->getFooter());
            $pdf->writePage();

            unset($template);
            return $pdf->render($id_order_invoice . '.pdf', true);
        } else {
            Tools::redirectAdmin('AdminDashboard');
        }
    }


    public function getPdfData($html_template_invoice, $order, $fee, $id_order)
    {
        $invoiceAddressPatternRules = json_decode(Configuration::get('PS_INVCE_INVOICE_ADDR_RULES'), true);
        $deliveryAddressPatternRules = json_decode(Configuration::get('PS_INVCE_DELIVERY_ADDR_RULES'), true);

        $invoice_address = new Address((int) $order->id_address_invoice);
        $formatted_invoice_address = AddressFormat::generateAddress(
            $invoice_address,
            $invoiceAddressPatternRules,
            '<br />',
            ' '
        );

        $delivery_address = null;
        if (isset($order->id_address_delivery) && $order->id_address_delivery) {
            $delivery_address = new Address((int) $order->id_address_delivery);
            $formatted_delivery_address = AddressFormat::generateAddress(
                $delivery_address,
                $deliveryAddressPatternRules,
                '<br />',
                ' '
            );
        }

        $customer = new Customer((int) $order->id_customer);
        $carrier = new Carrier((int) $order->id_carrier);

        $id_order_invoice = $order->invoice_number;
        $invoice = $this->getInvoiceByNumber($id_order, $id_order_invoice);

        $order_details = $invoice->getProducts();

        $has_discount = false;
        foreach ($order_details as $id => &$order_detail) {
            if ($order_detail['reduction_amount_tax_excl'] > 0) {
                $has_discount = true;
                $order_detail['unit_price_tax_excl_before_specific_price'] =
                    $order_detail['unit_price_tax_excl_including_ecotax'] + $order_detail['reduction_amount_tax_excl'];
            } elseif ($order_detail['reduction_percent'] > 0) {
                $has_discount = true;
                $order_detail['unit_price_tax_excl_before_specific_price'] =
                    (100 * $order_detail['unit_price_tax_excl_including_ecotax']) /
                    (100 - $order_detail['reduction_percent']);
            }

            $taxes = OrderDetail::getTaxListStatic($id);
            $tax_temp = array();
            foreach ($taxes as $tax) {
                $obj = new Tax($tax['id_tax']);
                $tax_temp[] = sprintf($this->l('%1$s%2$s%%'), ($obj->rate + 0), '&nbsp;');
            }

            $order_detail['order_detail_tax'] = $taxes;
            $order_detail['order_detail_tax_label'] = implode(', ', $tax_temp);
        }
        unset($tax_temp);
        unset($order_detail);

        if (Configuration::get('PS_PDF_IMG_INVOICE')) {
            foreach ($order_details as &$order_detail) {
                if ($order_detail['image'] != null) {
                    $name = 'product_mini_' . (int) $order_detail['product_id'] .
                        (isset($order_detail['product_attribute_id']) ? '_' .
                        (int) $order_detail['product_attribute_id'] : '') . '.jpg';
                    $path = _PS_PROD_IMG_DIR_ . $order_detail['image']->getExistingImgPath() . '.jpg';

                    $order_detail['image_tag'] = preg_replace(
                        '/\.*' . preg_quote(__PS_BASE_URI__, '/') . '/',
                        _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR,
                        ImageManager::thumbnail($path, $name, 45, 'jpg', false),
                        1
                    );

                    if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                        $order_detail['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                    } else {
                        $order_detail['image_size'] = false;
                    }
                }
            }
            unset($order_detail);
        }

        $cart_rules = $order->getCartRules($invoice->id);
        $free_shipping = false;
        foreach ($cart_rules as $key => $cart_rule) {
            if ($cart_rule['free_shipping']) {
                $free_shipping = true;

                $cart_rules[$key]['value_tax_excl'] -= $invoice->total_shipping_tax_excl;
                $cart_rules[$key]['value'] -= $invoice->total_shipping_tax_incl;

                if ($cart_rules[$key]['value'] == 0) {
                    unset($cart_rules[$key]);
                }
            }
        }

        $product_taxes = 0;
        foreach ($invoice->getProductTaxesBreakdown($order) as $details) {
            $product_taxes += $details['total_amount'];
        }

        $product_discounts_tax_excl = $invoice->total_discount_tax_excl;
        $product_discounts_tax_incl = $invoice->total_discount_tax_incl;

        $products_after_discounts_tax_excl = $invoice->total_products - $product_discounts_tax_excl;
        $products_after_discounts_tax_incl = $invoice->total_products_wt - $product_discounts_tax_incl;

        $shipping_tax_excl = $free_shipping ? 0 : $invoice->total_shipping_tax_excl;
        $shipping_tax_incl = $free_shipping ? 0 : $invoice->total_shipping_tax_incl;
        $shipping_taxes = $shipping_tax_incl - $shipping_tax_excl;

        $wrapping_taxes = $invoice->total_wrapping_tax_incl - $invoice->total_wrapping_tax_excl;

        $total_taxes = $invoice->total_paid_tax_incl - $invoice->total_paid_tax_excl;
        //$currency = new Currency($order->id_currency);
        $footer = array(
            'products_before_discounts_tax_excl' => $invoice->total_products,
            'product_discounts_tax_excl' => $product_discounts_tax_excl,
            'products_after_discounts_tax_excl' => $products_after_discounts_tax_excl,
            'products_before_discounts_tax_incl' => $invoice->total_products_wt,
            'product_discounts_tax_incl' => $product_discounts_tax_incl,
            'products_after_discounts_tax_incl' => $products_after_discounts_tax_incl,
            'product_taxes' => $product_taxes,
            'shipping_tax_excl' => $shipping_tax_excl,
            'shipping_taxes' => $shipping_taxes,
            'shipping_tax_incl' => $shipping_tax_incl,
            'wrapping_tax_excl' => $invoice->total_wrapping_tax_excl,
            'wrapping_taxes' => $wrapping_taxes,
            'wrapping_tax_incl' => $invoice->total_wrapping_tax_incl,
            'ecotax_taxes' => $total_taxes - $product_taxes - $wrapping_taxes - $shipping_taxes,
            'total_taxes' => $total_taxes,
            'total_paid_tax_excl' => $invoice->total_paid_tax_excl,
            'total_paid_tax_incl' => $invoice->total_paid_tax_incl,
            'fee' => $fee,
        );


        foreach ($footer as $key => $value) {
            $footer[$key] = Tools::ps_round($value, _PS_PRICE_COMPUTE_PRECISION_, $order->round_mode);
        }

        $round_type = null;
        switch ($order->round_type) {
            case Order::ROUND_TOTAL:
                $round_type = 'total';
                break;
            case Order::ROUND_LINE:
                $round_type = 'line';
                break;
            case Order::ROUND_ITEM:
                $round_type = 'item';
                break;
            default:
                $round_type = 'line';
                break;
        }

        $display_product_images = Configuration::get('PS_PDF_IMG_INVOICE');
        $tax_excluded_display = Group::getPriceDisplayMethod($customer->id_default_group);

        $layout = $this->computeLayout(array('has_discount' => $has_discount));

        $legal_free_text = Hook::exec('displayInvoiceLegalFreeText', array('order' => $order));
        if (!$legal_free_text) {
            $legal_free_text = Configuration::get(
                'PS_INVOICE_LEGAL_FREE_TEXT',
                (int) Context::getContext()->language->id,
                null,
                (int) $order->id_shop
            );
        }

        $data = array(
            'title' => $invoice->getInvoiceNumberFormatted(Context::getContext()->language->id),
            'order' => $order,
            'order_invoice' => $invoice,
            'order_details' => $order_details,
            'carrier' => $carrier,
            'cart_rules' => $cart_rules,
            'delivery_address' => $formatted_delivery_address,
            'invoice_address' => $formatted_invoice_address,
            'addresses' => array('invoice' => $invoice_address, 'delivery' => $delivery_address),
            'tax_excluded_display' => $tax_excluded_display,
            'display_product_images' => $display_product_images,
            'layout' => $layout,
            'tax_tab' => $this->getTaxTabContent($order, $invoice, $html_template_invoice, $fee),
            'customer' => $customer,
            'footer' => $footer,
            'ps_price_compute_precision' => _PS_PRICE_COMPUTE_PRECISION_,
            'round_type' => $round_type,
            'legal_free_text' => $legal_free_text,
        );

        if (Tools::getValue('debug')) {
            die(json_encode($data));
        }

        $html_template_invoice->smarty->assign($data);

        $this->registerSmartyFunctions($html_template_invoice->smarty);

        $tpls = array(
            'style_tab' => $html_template_invoice->smarty->fetch(
                _PS_MODULE_DIR_ . '/paypalwithfee/views/templates/admin/invoice.style-tab.tpl'
            ),
            'addresses_tab' => $html_template_invoice->smarty->fetch(
                _PS_MODULE_DIR_ . '/paypalwithfee/views/templates/admin/invoice.addresses-tab.tpl'
            ),
            'summary_tab' => $html_template_invoice->smarty->fetch(
                _PS_MODULE_DIR_ . '/paypalwithfee/views/templates/admin/invoice.summary-tab.tpl'
            ),
            'product_tab' => $html_template_invoice->smarty->fetch(
                _PS_MODULE_DIR_ . '/paypalwithfee/views/templates/admin/invoice.product-tab.tpl'
            ),
            'tax_tab' => $this->getTaxTabContent($order, $invoice, $html_template_invoice, $fee),
            'payment_tab' => $html_template_invoice->smarty->fetch(
                _PS_MODULE_DIR_ . '/paypalwithfee/views/templates/admin/invoice.payment-tab.tpl'
            ),
            'note_tab' => $html_template_invoice->smarty->fetch(
                _PS_MODULE_DIR_ . '/paypalwithfee/views/templates/admin/invoice.note-tab.tpl'
            ),
            'total_tab' => $html_template_invoice->smarty->fetch(
                _PS_MODULE_DIR_ . '/paypalwithfee/views/templates/admin/invoice.total-tab.tpl'
            ),
            'shipping_tab' => $html_template_invoice->smarty->fetch(
                _PS_MODULE_DIR_ . '/paypalwithfee/views/templates/admin/invoice.shipping-tab.tpl'
            ),
        );
        $html_template_invoice->smarty->assign($tpls);

        return $html_template_invoice->smarty->fetch(
            _PS_MODULE_DIR_ . '/paypalwithfee/views/templates/admin/invoice.tpl'
        );
    }

    protected function getTaxTabContent($order, $invoice, $html_template_invoice, $fee)
    {
        $debug = Tools::getValue('debug');

        $address = new Address((int) $order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $tax_exempt = Configuration::get(
            'VATNUMBER_MANAGEMENT'
        ) && !empty($address->vat_number) &&
            $address->id_country != Configuration::get(
                'VATNUMBER_COUNTRY'
            );
        $carrier = new Carrier($order->id_carrier);

        $tax_breakdowns = $this->getTaxBreakdown($order, $invoice);

        $data = array(
            'tax_exempt' => $tax_exempt,
            'use_one_after_another_method' => $invoice->useOneAfterAnotherTaxComputationMethod(),
            'display_tax_bases_in_breakdowns' => $invoice->displayTaxBasesInProductTaxesBreakdown(),
            'product_tax_breakdown' => $invoice->getProductTaxesBreakdown($order),
            'shipping_tax_breakdown' => $invoice->getShippingTaxesBreakdown($order),
            'ecotax_tax_breakdown' => $invoice->getEcoTaxTaxesBreakdown(),
            'wrapping_tax_breakdown' => $invoice->getWrappingTaxesBreakdown(),
            'tax_breakdowns' => $tax_breakdowns,
            'order' => $debug ? null : $order,
            'order_invoice' => $debug ? null : $invoice,
            'carrier' => $debug ? null : $carrier,
            'fee' => $fee
        );

        if ($debug) {
            return $data;
        }

        $html_template_invoice->smarty->assign($data);

        $this->registerSmartyFunctions($html_template_invoice->smarty);

        return $html_template_invoice->smarty->fetch(_PS_MODULE_DIR_ .
            '/paypalwithfee/views/templates/admin/invoice.tax-tab.tpl');
    }

    protected function getTaxBreakdown($order, $invoice)
    {
        $breakdowns = array(
            'product_tax' => $invoice->getProductTaxesBreakdown($order),
            'shipping_tax' => $invoice->getShippingTaxesBreakdown($order),
            'ecotax_tax' => $invoice->getEcoTaxTaxesBreakdown(),
            'wrapping_tax' => $invoice->getWrappingTaxesBreakdown(),
        );

        foreach ($breakdowns as $type => $bd) {
            if (empty($bd)) {
                unset($breakdowns[$type]);
            }
        }

        if (empty($breakdowns)) {
            $breakdowns = false;
        }

        if (isset($breakdowns['product_tax'])) {
            foreach ($breakdowns['product_tax'] as &$bd) {
                $bd['total_tax_excl'] = $bd['total_price_tax_excl'];
            }
        }

        if (isset($breakdowns['ecotax_tax'])) {
            foreach ($breakdowns['ecotax_tax'] as &$bd) {
                $bd['total_tax_excl'] = $bd['ecotax_tax_excl'];
                $bd['total_amount'] = $bd['ecotax_tax_incl'] - $bd['ecotax_tax_excl'];
            }
        }

        return $breakdowns;
    }

    protected function computeLayout($params)
    {
        $layout = array(
            'reference' => array(
                'width' => 15,
            ),
            'product' => array(
                'width' => 40,
            ),
            'quantity' => array(
                'width' => 8,
            ),
            'tax_code' => array(
                'width' => 8,
            ),
            'unit_price_tax_excl' => array(
                'width' => 0,
            ),
            'total_tax_excl' => array(
                'width' => 0,
            )
        );

        if (isset($params['has_discount']) && $params['has_discount']) {
            $layout['before_discount'] = array('width' => 0);
            $layout['product']['width'] -= 7;
            $layout['reference']['width'] -= 3;
        }

        $total_width = 0;
        $free_columns_count = 0;
        foreach ($layout as $data) {
            if ($data['width'] === 0) {
                ++$free_columns_count;
            }

            $total_width += $data['width'];
        }

        $delta = 100 - $total_width;

        foreach ($layout as $row => $data) {
            if ($data['width'] === 0) {
                $layout[$row]['width'] = $delta / $free_columns_count;
            }
        }

        $layout['_colCount'] = count($layout);

        return $layout;
    }

    protected function registerSmartyFunctions($smarty)
    {
        smartyRegisterFunction($smarty, 'function', 'displayPrice', array('Tools', 'displayPriceSmarty'));
    }

    protected function getFeeData($id_order)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'ppwf_order` WHERE `id_order`=' . (int) $id_order;
        return Db::getInstance()->getRow($sql);
    }
    
    private function getInvoiceByNumber($id_order, $id_invoice)
    {
        if (is_numeric($id_invoice)) {
            $id_invoice = (int)$id_invoice;
        } elseif (is_string($id_invoice)) {
            $matches = array();
            if (preg_match(
                '/^(?:'.Configuration::get(
                    'PS_INVOICE_PREFIX',
                    Context::getContext()->language->id
                )
                    .')\s*([0-9]+)$/i',
                $id_invoice,
                $matches
            )
                ) {
                $id_invoice = $matches[1];
            }
        }
        if (!$id_invoice) {
            return false;
        }

        $id_order_invoice = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT `id_order_invoice` FROM `'._DB_PREFIX_.'order_invoice` WHERE number = '.
                (int)$id_invoice.' AND `id_order`='.(int)$id_order
        );


        return ($id_order_invoice ? new OrderInvoice($id_order_invoice) : false);
    }
}
