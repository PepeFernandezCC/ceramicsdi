<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from ScaleDEV.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from ScaleDEV is strictly forbidden.
 * In order to obtain a license, please contact us: contact@scaledev.fr
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise à une licence commerciale
 * concédée par la société ScaleDEV.
 * Toute utilisation, reproduction, modification ou distribution du présent
 * fichier source sans contrat de licence écrit de la part de ScaleDEV est
 * expressément interdite.
 * Pour obtenir une licence, veuillez nous contacter : contact@scaledev.fr
 * ...........................................................................
 * @author ScaleDEV <contact@scaledev.fr>
 * @copyright Copyright (c) ScaleDEV - 12 RUE CHARLES MORET - 10120 SAINT-ANDRE-LES-VERGERS - FRANCE
 * @license Commercial license
 * @package Scaledev\Adeo
 * Support: support@scaledev.fr
 */

namespace Scaledev\Adeo\Action;

use Context;
use Customer;
use Db;
use Group;
use Scaledev\Adeo\Component\Configuration;
use Scaledev\Adeo\Component\Address;
use Scaledev\Adeo\Core\Action\AbstractAction;
use Scaledev\Adeo\Core\Component\Logger;
use Scaledev\Adeo\Core\Module;
use Scaledev\Adeo\Core\Tools;
use Validate;

require_once(dirname(__FILE__).'/../../autoload.php');

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class ImportOrdersAction
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class ImportOrdersAction extends AbstractAction
{
    /** @var object order proceeding */
    private $order;
    private $module;
    /** @var Logger */
    private $logger;

    public function __construct()
    {
        parent::__construct();
        $this->logger = new Logger('order_import');
    }

    /**
     * @param object $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $this->module = \Module::getInstanceByName(Module::NAME);
        $orderId = Tools::getIdByMpOrderId($this->order['order_id']);
        \Context::getContext()->currency = new \Currency(\Currency::getIdByIsoCode($this->order['currency_iso_code']));

        if ($orderId) {
            return array(
                'error' => 'duplicateId',
                'id' => $this->order['order_id']
            );
        }
        $order = $this->testBeforeImport($this->order);
        $customer = new Customer();
        $customer = $customer->getByEmail($order['customer_notification_email']);

        if (!$customer || !$customer->id) {
            // Create customer
            $customer = new Customer();
            $customer->firstname = Tools::cleanName($order['customer']['firstname']);
            $customer->lastname = Tools::cleanName($order['customer']['lastname']);
            $customer->passwd = md5(Tools::passwdGen());
            $customer->email = $order['customer_notification_email'];
            $group = new Group(null, Context::getContext()->language->id);
            if(!$group->searchByName('AdeoCustomer')){
                $group->name = "AdeoCustomer";
                $group->price_display_method = 1;
                $group->add();
            }
            $group_id = $group->searchByName('AdeoCustomer')["id_group"];
            $customer->id_default_group = $group_id;

                $customer->add();

            if (!$customer->id) {
                return array('error' => 'customerError');
            }
        }
        \Context::getContext()->customer = $customer;

        // Check if DNI is set
        $dni = null;
        if (!empty($order['order_additional_fields']['list'])) {
            foreach ($order['order_additional_fields']['list'] as $additional_field) {
                if (
                    !isset($additional_field['code'])
                    || $additional_field['code'] != 'administrative-tax-code'
                ) {
                    continue;
                }
                $dni = $additional_field['value'];
            }
        }

        // Invoice address
        $invoice_address = new Address();
        $order['customer']['billing_address']['other'] = null;
        $id_address_invoice = $invoice_address->createOrGetAddress($order['customer']['billing_address'], $customer->id, $dni, false);
        if (!$id_address_invoice) {
            return array('error' => 'addressBillingError');
        }

        // Shipping address
        $shipping_address = new Address();
        // $order['shipping_address']['other'] = null;
        $id_address_shipping = $shipping_address->createOrGetAddress($order['customer']['shipping_address'], $customer->id, $dni, false);
        if (!$id_address_shipping) {
            return array('error' => 'addressShippingError');
        }

        $carrier = new \Carrier((int)\SdevAdeoCarrierRule::findIdCarrierByMpReference($order['shipping_type_code']));
        $id_carrier = (\Validate::isLoadedObject($carrier)
            ? $carrier->id
            : false
        );

        if (!$id_carrier) {
            return array(
                'error' => 'shippingError',
                'shipping_code' => $order['shipping_type_code']
            );
        }

        // Create cart
        $cart = new \Cart();
        $cart->id_customer = $customer->id;
        $cart->id_carrier = null;
        $cart->id_currency = \Currency::getIdByIsoCode($order['currency_iso_code']);
        $cart->id_address_invoice = $id_address_invoice;
        $cart->id_address_delivery = $id_address_shipping;
        $cart->add();

        if (!\Validate::isLoadedObject($cart)) {
            return array('error' => 'cartError');
        }

        $total_products_without_taxes = 0;
        $total_products_with_taxes = 0;

        // Add products to cart
        $sellerProductsID = array();
        foreach ($order['order_lines']['list'] as $key => &$prod) {
            // Get product IDs.
            $productIDs = array();
            if (array_key_exists('offer_sku', $prod)) {
                $productIDs['id_product_attribute'] = 0;
                if (!($id_product = Tools::getIdByReference($prod['offer_sku']))) {
                    $result = \Db::getInstance()->executeS((new \DbQuery())
                        ->select('id_product, id_product_attribute')
                        ->from('product_attribute')
                        ->where('reference = \'' . pSQL($prod['offer_sku']) . '\'')
                    );
                    if (empty($result) || !array_key_exists('id_product', $result[0])) {
                        return array('error' => 'WrongIdentifierError', 'offer_sku' => $prod['offer_sku']);
                    }
                    $id_product = $result[0]['id_product'];
                    $productIDs['id_product_attribute'] = $result[0]['id_product_attribute'];
                }
                $productIDs['id_product'] = $id_product;
            } else {
                return array('error' => 'NoIdentifierError', 'offer_sku' => $prod['offer_sku']);
            }

            $id_product = (int)$productIDs['id_product'];
            $qSelectProduct = 'SELECT *
                    FROM `' . _DB_PREFIX_ . 'product`
                    WHERE `id_product` = ' . (int)$id_product;
            $product = Db::getInstance()->getRow($qSelectProduct);
            if (!$product) {
                return array('error' => 'productNotFoundError', 'offer_sku' => $prod['offer_sku']);
            }

            $id_product_attribute = (int)$productIDs['id_product_attribute'];
            if ($id_product_attribute) {
                $qSelectProductAttribute = 'SELECT *
                        FROM `' . _DB_PREFIX_ . 'product_attribute`
                        WHERE id_product_attribute = ' . (int)$id_product_attribute;
                $product_attribute = Db::getInstance()->getRow($qSelectProductAttribute);
                if (!$product_attribute || $product_attribute['id_product'] != $id_product) {
                    return array('error' => 'productAttributeNotFoundError', 'offer_sku' => $prod['offer_sku']);
                }

                $prod['reference'] = $product_attribute['reference'];
                $prod['ean13'] = $product_attribute['ean13'];
                $prod['weight'] = $product['weight'] + $product_attribute['weight'];
                $prod['ecotax'] = $product_attribute['ecotax'];
            } else {
                $prod['reference'] = $product['reference'];
                $prod['ean13'] = $product['ean13'];
                $prod['weight'] = $product['weight'];
                $prod['ecotax'] = $product['ecotax'];
            }

            $prod['supplier_reference'] = \ProductSupplier::getProductSupplierReference($id_product, $id_product_attribute, $product['id_supplier']);

            if (!$cart->updateQty($prod['quantity'], $id_product, $id_product_attribute ?: null)) {
                return array('error' => 'addProductToCartError', 'offer_sku' => $prod['offer_sku']);
            }

            // Calc unit price with tax rate
            $tax_rate = \Tax::getProductTaxRate($id_product, $id_address_shipping);

            $prod['id_product'] = $id_product;
            $prod['id_product_attribute'] = $id_product_attribute;
            $prod['tax_rate'] = $tax_rate;

            $total_products_without_taxes += ((($prod['price'] / $prod['quantity']) / (1 + ($prod['tax_rate'] / 100))) * $prod['quantity']);
            $total_products_with_taxes += $prod['price'];

            $sellerProductsID[] = $prod['offer_sku'];
        }

        $total_products_without_taxes = round($total_products_without_taxes, 6);

        try {
            // Make order
            $newOrder = new \Order();
            $newOrder->date_add = $order['created_date'];
            $newOrder->date_upd = date('Y-m-d H:i:s');
            $newOrder->id_shop_group = \Context::getContext()->shop->id_shop_group;
            $newOrder->id_shop = \Context::getContext()->shop->id;
            $newOrder->id_address_delivery = $id_address_shipping;
            $newOrder->id_address_invoice = $id_address_invoice;
            $newOrder->id_carrier = $id_carrier;
            $newOrder->id_customer = $customer->id;
            $newOrder->id_currency = \Currency::getIdByIsoCode($order['currency_iso_code']);
            $newOrder->id_lang = \Context::getContext()->language->id;
            $newOrder->id_cart = $cart->id;

            $newOrder->secure_key = $customer->secure_key;
            if (!$newOrder->secure_key) {
                $newOrder->secure_key = md5(time());
            }

            $newOrder->payment = 'Adeo';

            $newOrder->module = $this->module->name;
            $newOrder->recyclable = (bool)\Configuration::get('PS_RECYCLABLE_PACK');
            $newOrder->total_products_wt = (float)round($total_products_with_taxes, 6);
            $newOrder->total_products = (float)round($total_products_without_taxes, 6);
            $newOrder->total_discounts = $order['promotions']['total_deduced_amount'];

            $Carrier = new \Carrier($id_carrier);
            $Address = new \Address($id_address_shipping);
            $carrier_tax_rate = (float)$Carrier->getTaxesRate($Address);

            $newOrder->total_shipping = (float)$order['shipping_price'];
            $newOrder->total_shipping_tax_excl = round($newOrder->total_shipping / (1 + ($carrier_tax_rate / 100)), 6);
            $newOrder->total_shipping_tax_incl = $newOrder->total_shipping;
            $newOrder->total_wrapping = 0;
            $newOrder->total_wrapping_tax_excl = 0;
            $newOrder->total_wrapping_tax_incl = 0;
            $newOrder->total_paid = (float)($newOrder->total_products_wt + $newOrder->total_shipping_tax_incl);
            $newOrder->total_paid_real = 0;
            $newOrder->total_paid_tax_excl = round($newOrder->total_products + $newOrder->total_shipping_tax_excl, 6);
            $newOrder->total_paid_tax_incl = $newOrder->total_paid;
            if (round($order['total_price'], 2) !== round($newOrder->total_paid, 2)) {
                return array('error' => 'totalPaidAmountError');
            }

            $newOrder->carrier_tax_rate = $carrier_tax_rate;
            $newOrder->invoice_date = '0000-00-00 00:00:00';
            $newOrder->delivery_date = '0000-00-00 00:00:00';

            $reference = \Order::generateReference();
            $newOrder->reference = $reference;
            $newOrder->mp_order_id = $order['order_id'];
            $newOrder->current_state = Configuration::getValue(Configuration::IMPORTED_STATE);

            $Currency = new \Currency(\Currency::getIdByIsoCode($order['currency_iso_code']));
            $newOrder->conversion_rate = $Currency->conversion_rate;

            $newOrder->add(true, false);
            if (!\Validate::isLoadedObject($newOrder)) {
                return array('error' => 'orderCreateError');
            }

            \Db::getInstance()->update('orders', array('mp_order_id' => $order['order_id']), 'id_order = ' . (int)$newOrder->id);
            $id_warehouse = (int)Configuration::getValue('WAREHOUSE_ID');

            foreach ($order['order_lines']['list'] as $product) {
                $product_id = Tools::getProductIdByReference($product['offer_sku']);
                if (array_key_exists('id_product_attribute', $product_id) && $product_id['id_product_attribute']) {
                    $combination = new \Combination($product_id['id_product_attribute']);
                }
                $product_quantity = \Product::getRealQuantity($product_id['id_product'], $product_id['id_product_attribute'] ?: 0, $id_warehouse);
                $quantity_in_stock = $product_quantity - $product['quantity'];

                $product_tax_coef = 1 + ($product['tax_rate'] / 100);
                $unit_price_tax_excl = round(($product['price'] / $product['quantity']) / $product_tax_coef, 6);
                $unit_price_tax_incl = $product['price'] / $product['quantity'];
                $total_price_tax_excl = $unit_price_tax_excl * $product['quantity'];
                $total_price_tax_incl = $unit_price_tax_incl * $product['quantity'];

                $OrderDetail = new \OrderDetail();
                $OrderDetail->id_order = (int)$newOrder->id;
                $OrderDetail->product_name = str_replace('=', ':', $product['product_title']);
                $OrderDetail->product_id = (int)$product_id['id_product'];
                $OrderDetail->product_attribute_id = $product['id_product_attribute'] ? (int)$product['id_product_attribute'] : 0;
                $OrderDetail->product_quantity = (int)$product['quantity'];
                $OrderDetail->product_quantity_in_stock = (int)$quantity_in_stock;
                $OrderDetail->product_price = $unit_price_tax_excl;
                $OrderDetail->product_ean13 = $product['ean13'];
                $OrderDetail->product_reference = $product['reference'];
                $OrderDetail->product_supplier_reference = $product['supplier_reference'];
                $OrderDetail->product_weight = $product_id['id_product_attribute'] ? $combination->weight : (new \Product($product_id))->weight;

                $tax_name = null;
                $tax_id = 0;

                if (!\Tax::excludeTaxeOption()) {
                    $language = \Language::getIdByIso($order['customer']['shipping_address']['country']);
                    foreach (\Tax::getTaxes($language) as $tax) {
                        $tax = new \Tax($tax);
                        if ($tax->rate == $product['tax_rate']) {
                            $tax_name = ($tax->name)[$language];
                            $tax_id = $tax->id;
                        }
                    }
                }

                $OrderDetail->tax_name = $tax_name;
                $OrderDetail->tax_rate = $product['tax_rate'];
                $OrderDetail->ecotax = $product['ecotax'];
                $OrderDetail->download_deadline = '0000-00-00 00:00:00';
                $OrderDetail->total_price_tax_incl = $total_price_tax_incl;
                $OrderDetail->total_price_tax_excl = $total_price_tax_excl;
                $OrderDetail->unit_price_tax_incl = $unit_price_tax_incl;
                $OrderDetail->unit_price_tax_excl = $unit_price_tax_excl;
                $OrderDetail->id_shop = (int)\Context::getContext()->shop->id;
                $OrderDetail->id_warehouse = $id_warehouse;
                $OrderDetail->add();

                if (!\Validate::isLoadedObject($OrderDetail)) {
                    $newOrder->delete();
                    return array('error' => 'orderDetailError');
                }

                // order_detail_tax
                $order_detail_tax = array(
                    'id_order_detail' => (int)$OrderDetail->id,
                    'id_tax' => (int)$tax_id,
                    'unit_amount' => (float)$total_price_tax_excl,
                    'total_amount' => (float)($total_price_tax_incl - $total_price_tax_excl)
                );

                \Db::getInstance()->insert('order_detail_tax', $order_detail_tax);
            }

            if ($newOrder->id_carrier) {
                $OrderCarrier = new \OrderCarrier();
                $OrderCarrier->id_order = (int)$newOrder->id;
                $OrderCarrier->id_carrier = (int)$newOrder->id_carrier;
                $OrderCarrier->weight = (float)$newOrder->getTotalWeight();
                $OrderCarrier->shipping_cost_tax_excl = $newOrder->total_shipping_tax_excl;
                $OrderCarrier->shipping_cost_tax_incl = $newOrder->total_shipping_tax_incl;
                $OrderCarrier->add();
            }

            if (($id_order_state = Configuration::getValue(Configuration::IMPORTED_STATE)) === false) {
                $newOrder->delete();
                return array('error' => 'orderStateNotSetError');
            }

            $OrderState = new \OrderState((int)$id_order_state);
            if (!\Validate::isLoadedObject($OrderState)) {
                $newOrder->delete();
                return array('error' => 'orderStateNotFoundError');
            }

            foreach ($order['order_lines']['list'] as $product) {
                if ($OrderState->logable) {
                    \ProductSale::addProductSale((int)$product['id_product'], (int)$product['quantity']);
                }
            }

            \Hook::exec('actionValidateOrder', array(
                'cart' => $cart,
                'order' => $newOrder,
                'customer' => $customer,
                'currency' => $Currency,
                'orderStatus' => $OrderState,
            ));

            $OrderHistory = new \OrderHistory();
            $OrderHistory->id_order = (int)$newOrder->id;
            $OrderHistory->id_employee = 1;
            $OrderHistory->changeIdOrderState($id_order_state, $newOrder->id);
            $OrderHistory->add();

            return $newOrder;
        } catch (\Exception $e) {
            if (
                isset($newOrder)
                && Validate::isLoadedObject($newOrder)
            ) {
                $newOrder->delete();
            }
            $this->logger->addLog($this->order['order_id']);
            $this->logger->addLog($e->getMessage());
            $this->logger->addLog('_________________');
            return array('error' => $e->getMessage());
        }
    }

    public function testBeforeImport($order)
    {
        $order['errors'] = array();

        // Test if customer account has issues
        $customer = new Customer();
        $customer->firstname = $order['customer']['billing_address']['firstname'];
        $customer->lastname = $order['customer']['billing_address']['lastname'];
        $customer->passwd = md5(Tools::passwdGen());
        if ($order['customer_notification_email']) {
            $customer->email = $order['customer_notification_email'];
        }

        foreach ($customer->validateController() as $key => $error) {
            $order['errors'][$key] = $error;
        }

        if (!isset($order['customer']['billing_address']['phone'])) {
            $order['customer']['billing_address']['phone'] = $order['customer']['shipping_address']['phone'];
        }
        if (!isset($order['customer']['billing_address']['phone_secondary'])) {
            $order['customer']['billing_address']['phone_secondary'] = $order['customer']['shipping_address']['phone_secondary'];
        }

        if (!isset($order['shipping_address']['phone'])) {
            $order['customer']['shipping_address']['phone'] = $order['customer']['billing_address']['phone'];
        }
        if (!isset($order['customer']['shipping_address']['phone_secondary'])) {
            $order['customer']['shipping_address']['phone_secondary'] = $order['customer']['billing_address']['phone_secondary'];
        }

        if (!isset($order['customer']['shipping_address']['other']) && $order['shipping_pudo_id']) {
            $order['customer']['shipping_address']['other'] = $order['shipping_pudo_id'];

            // Set the company name from the shipping address by the given first name and company name if present.
            if (!array_key_exists('company_name', $order['customer']['shipping_address']) || !$order['customer']['shipping_address']['company_name']) {
                $order['customer']['shipping_address']['company_name'] = $order['customer']['shipping_address']['firstname'];
            } elseif (
                isset($order['shipping_address']['firstname'])
                && $order['shipping_address']['firstname'] != $order['customer']['billing_address']['firstname']
            ) {
                $order['customer']['shipping_address']['company_name'] .= ' '.$order['customer']['shipping_address']['firstname'];
            }

            if (!isset($order['customer']['shipping_address']['address1'])) {
                $order['customer']['shipping_address']['address1'] = $order['customer']['shipping_address']['street1'];
            }
            if (!isset($order['customer']['shipping_address']['address2'])) {
                $order['customer']['shipping_address']['address2'] = $order['customer']['shipping_address']['street2'];
            }

            // Set the customer name from billing address to the shipping address.
            $order['customer']['shipping_address']['firstname'] = $order['customer']['billing_address']['firstname'];
            $order['customer']['shipping_address']['lastname'] = $order['customer']['billing_address']['lastname'];
        } else {
            $order['customer']['shipping_address']['other'] = null;
        }

        // Test si problème avec l'adresse de facturation
        $billingAddressErrors = (new Address())->createOrGetAddress($order['customer']['billing_address'], 0);
        foreach ($billingAddressErrors as $key => $error) {
            $order['errors']['billing_address'][$key] = $error;
        }

        // Test si problème avec l'adresse de livraison
        $shipping_addressErrors = (new Address())->createOrGetAddress($order['customer']['shipping_address'], 0);
        foreach ($shipping_addressErrors as $key => $error) {
            $order['errors']['shipping_address'][$key] = $error;
        }

        return $order;
    }
}
