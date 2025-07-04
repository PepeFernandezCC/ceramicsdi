<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SAS Comptoir du Code
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SAS Comptoir du Code is strictly forbidden.
 * In order to obtain a license, please contact us: contact@comptoirducode.com
 *
 * @author    Vincent - Comptoir du Code
 * @copyright Copyright(c) 2015-2022 SAS Comptoir du Code
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * @package   cdc_googletagmanager
 */

if (!defined('_CDCGTM_DIR_'))
    define('_CDCGTM_DIR_', dirname(__FILE__).'/..');

include_once(_CDCGTM_DIR_.'/classes/gtm/Ecommerce.php');
include_once(_CDCGTM_DIR_.'/classes/gtm/DataLayerProduct.php');
include_once(_CDCGTM_DIR_.'/classes/gtm/Refund.php');
include_once(_CDCGTM_DIR_.'/classes/gtm/GoogleTagParams.php');

include_once(_CDCGTM_DIR_.'/classes/gtm_ga4/DataLayerItem.php');

/**
 * Represent GTM Datalayer
 */
class Gtm_DataLayer
{
    private $format; // eec|ga4|all
    private $gtm;

    public $pageCategory;
    public $event;
    public $eventCallback;
    public $eventTimeout;
    public $ecommerce;

    public function __construct($gtm, $format = 'eec', $context = null) {
        $this->gtm = $gtm;
        $this->format = $format;
        $context = $context ?: Context::getContext();
        $currency = !empty($context->currency->iso_code) ? $context->currency->iso_code : null;

        $this->ecommerce = new Gtm_Ecommerce($this, $currency);
    }

    public function eeEnabled() {
        return $this->format == 'eec' || $this->format == 'all';
    }

    public function ga4Enabled() {
        return $this->format == 'ga4' || $this->format == 'all';
    }

    /**
     * @param ProductCore $product
     * @param array|null $list
     */
    public function addItem($product, $type, $list = null) {
        if($this->eeEnabled()) {
            $this->addItemEe($product, $type, $list);
        }

        if($this->ga4Enabled()) {
            $this->addItemGa4($product, $list);
        }

        // use type as default event
        if(empty($this->event)) {
            $this->event = $type;
        }
    }

    protected function addItemGa4($product, $list) {
        $dataLayerProduct = new Gtm_DataLayerItem($this->gtm, $product, $list);
        $dataLayerProduct->removeNull();

        if(empty($this->ecommerce->items)) {
            $this->ecommerce->items = array();
        }
        $this->ecommerce->items[] = $dataLayerProduct;
    }

    protected function addItemEe($product, $type, $list = null) {
        $dataLayerProduct = new Gtm_DataLayerProduct($this->gtm, $product, $list);
        $dataLayerProduct->removeNull();

        if($type == 'view_item') {
            // product detail
            if(empty($this->ecommerce->detail->products)) {
                $this->ecommerce->detail = new stdClass();
            }
            $this->ecommerce->detail->products = array($dataLayerProduct);
        } elseif($type == 'view_item_list' || $type == 'search_results') {
            // products list
            if(empty($this->ecommerce->impressions)) {
                $this->ecommerce->impressions = array();
            }
            $this->ecommerce->impressions[] = $dataLayerProduct;
        } elseif($type == 'view_cart') {
            // products list
            if(empty($this->ecommerce->checkout)) {
                $this->ecommerce->checkout = array();
            }
            $this->ecommerce->checkout[] = $dataLayerProduct;
        } elseif($type == 'begin_checkout') {
            // checkout
            if(empty($this->ecommerce->checkout->products)) {
                $this->ecommerce->checkout = new stdClass();
                $this->ecommerce->checkout->products = array();
            }
            $this->ecommerce->checkout->products[] = $dataLayerProduct;
        } elseif($type == 'purchase') {
            // checkout
            if(empty($this->ecommerce->purchase->products)) {
                $this->ecommerce->purchase = new stdClass();
                $this->ecommerce->purchase->products = array();
            }
            $this->ecommerce->purchase->products[] = $dataLayerProduct;
        } elseif($type == 'add_to_cart') {
            // add product to cart
            if(empty($this->ecommerce->add->products)) {
                $this->ecommerce->add = new stdClass();
            }
            $this->ecommerce->add->products = array($dataLayerProduct);
            $this->event = empty($this->event) ? 'addToCart' : $this->event;
        } elseif($type == 'remove_from_cart') {
            // remove product from cart
            if(empty($this->ecommerce->remove->products)) {
                $this->ecommerce->remove = new stdClass();
            }
            $this->ecommerce->remove->products = array($dataLayerProduct);
            $this->event = empty($this->event) ? 'removeFromCart' : $this->event;
        } elseif($type == 'refund') {
            // refund
            if(empty($this->ecommerce->refund->products)) {
                $this->ecommerce->refund = new stdClass();
                $this->ecommerce->refund->products = array();
            }
            $this->ecommerce->refund->products[] = $dataLayerProduct;
        }
    }


    /**
     * Return JSON representation
     * of this datalayer, for the webpage
     *
     * @param false $debug
     * @return string Json datalayer
     */
    public function toJson($debug = false) {
        $dataLayerJs = "";

        if(count((array)$this)) {
            $eventCallback = false;
            if(!empty($this->eventCallback)) {
                // remove double quotes before encoding
                $this->eventCallback = str_replace('"', '@@', $this->eventCallback);
                // add function delimiter
                $this->eventCallback = "!##".$this->eventCallback."##!";
                $eventCallback = true;
                if(empty($this->event)) {
                    // create random event to fire callback
                    $this->event = "eventForCallback";
                }
                if(empty($this->eventTimeout)) {
                    $this->eventTimeout = 2000;
                }
            } else {
                // if no callback, remove it from datalayer to avoid GTM errors
                unset($this->eventCallback);
                unset($this->eventTimeout);
            }

            // encode datalayer
            $json_options = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
            if($debug) {
                $json_options = $json_options | JSON_PRETTY_PRINT;
            }
            $dataLayerJs = json_encode($this, $json_options);

            if($eventCallback) {
                // remove double quotes
                $dataLayerJs = str_replace('##!"','', str_replace('"!##','', $dataLayerJs));
                // add double quote back
                $dataLayerJs = str_replace('@@', '"', $dataLayerJs);
            }
        }

        return $dataLayerJs;
    }

    /**
     * Return the list of items
     * @return array
     */
    protected function getItems() {
        $items = array();

        if($this->ga4Enabled()) {
            $items = $this->ecommerce->items;
        } else if($this->eeEnabled()) {
            if(!empty($this->ecommerce->detail->products)) {
                $items = $this->ecommerce->detail->products;
            } elseif(!empty($this->ecommerce->impressions)) {
                $items = $this->ecommerce->impressions;
            } elseif(!empty($this->ecommerce->checkout)) {
                $items = $this->ecommerce->checkout;
            }
        }

        return $items;
    }


    /**
     * Add a list of products to datalayer
     * @param $products : product list
     * @param array $list : information about the page list
     * @param int|null $max_products
     * @param $type: type of list, only used fo Enhanced Ecommerce Datalayer
     */
    public function addProductList($products, $list = null, $max_products = null, $type = null) {
        $position = 1;
        if(is_array($products) && !empty($products)) {
            foreach ($products as $p) {
                if(is_array($list)) {
                    $list['index'] = $position++;
                }

                $this->addItem($p, $type, $list);

                // stop loop when max_products_datalayer is reached
                if($max_products && $position > $max_products) {
                    break;
                }
            }
        }
    }


    /**
     * Set subtotals to the datalayer from a cart
     * @param $cart
     */
    public function setSubtotalsFromCart($cart) {
        if(Validate::isLoadedObject($cart)) {
            $this->ecommerce->value = (float) round($cart->getOrderTotal(), _CDCGTM_PRICE_DECIMAL_);
            $this->ecommerce->total_tax_exc = (float) round($cart->getOrderTotal(false), _CDCGTM_PRICE_DECIMAL_);
            $this->ecommerce->tax = (float) round($cart->getOrderTotal() - $cart->getOrderTotal(false), _CDCGTM_PRICE_DECIMAL_);
            $this->ecommerce->products = (float) round($cart->getOrderTotal(true, Cart::ONLY_PRODUCTS), _CDCGTM_PRICE_DECIMAL_);
            $this->ecommerce->products_tax_exc = (float) round($cart->getOrderTotal(false, Cart::ONLY_PRODUCTS), _CDCGTM_PRICE_DECIMAL_);
            $this->ecommerce->shipping = (float) round($cart->getOrderTotal(true, Cart::ONLY_SHIPPING), _CDCGTM_PRICE_DECIMAL_);
            $this->ecommerce->shipping_tax_exc = (float) round($cart->getOrderTotal(false, Cart::ONLY_SHIPPING), _CDCGTM_PRICE_DECIMAL_);
            $this->ecommerce->discounts = (float) round($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS), _CDCGTM_PRICE_DECIMAL_);
            $this->ecommerce->discounts_tax_exc = (float) round($cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS), _CDCGTM_PRICE_DECIMAL_);
        }
    }

    /**
     * Add subtotals to the datalayer from an order
     * @param $order
     */
    public function addSubtotalsFromOrder($ecommerceObj, $order) {
        $revenue = (float) round($order->total_paid_tax_incl, _CDCGTM_PRICE_DECIMAL_); // total transaction inc tax and shipping
        $total_tax_exc = (float) round($order->total_paid_tax_excl, _CDCGTM_PRICE_DECIMAL_);
        $tax = (float) round($order->total_paid_tax_incl - $order->total_paid_tax_excl, _CDCGTM_PRICE_DECIMAL_);
        $products = (float) round($order->total_products_wt, _CDCGTM_PRICE_DECIMAL_);
        $products_tax_exc = (float) round($order->total_products, _CDCGTM_PRICE_DECIMAL_);
        $shipping = (float) round($order->total_shipping_tax_incl, _CDCGTM_PRICE_DECIMAL_);
        $shipping_tax_exc = (float) round($order->total_shipping_tax_excl, _CDCGTM_PRICE_DECIMAL_);
        $discounts = (float) round($order->total_discounts_tax_incl, _CDCGTM_PRICE_DECIMAL_);
        $discounts_tax_exc = (float) round($order->total_discounts_tax_excl, _CDCGTM_PRICE_DECIMAL_);

        // add infos to Ecommerce Object
        if($this->eeEnabled()) {
            $ecommerceObj->revenue = round(empty($ecommerceObj->revenue) ? $revenue : $ecommerceObj->revenue + $revenue, _CDCGTM_PRICE_DECIMAL_);
        }
        if($this->ga4Enabled()) {
            $ecommerceObj->value = round(empty($ecommerceObj->value) ? $revenue : $ecommerceObj->value + $revenue, _CDCGTM_PRICE_DECIMAL_);
        }

        $ecommerceObj->total_tax_exc = round(empty($ecommerceObj->total_tax_exc) ? $total_tax_exc : $ecommerceObj->total_tax_exc + $total_tax_exc, _CDCGTM_PRICE_DECIMAL_);
        $ecommerceObj->tax = round(empty($ecommerceObj->tax) ? $tax : $ecommerceObj->tax + $tax, _CDCGTM_PRICE_DECIMAL_);
        $ecommerceObj->products = round(empty($ecommerceObj->products) ? $products : $ecommerceObj->products + $products, _CDCGTM_PRICE_DECIMAL_);
        $ecommerceObj->products_tax_exc = round(empty($ecommerceObj->products_tax_exc) ? $products_tax_exc : $ecommerceObj->products_tax_exc + $products_tax_exc, _CDCGTM_PRICE_DECIMAL_);
        $ecommerceObj->shipping = round(empty($ecommerceObj->shipping) ? $shipping : $ecommerceObj->shipping + $shipping, _CDCGTM_PRICE_DECIMAL_);
        $ecommerceObj->shipping_tax_exc = round(empty($ecommerceObj->shipping_tax_exc) ? $shipping_tax_exc : $ecommerceObj->shipping_tax_exc + $shipping_tax_exc, _CDCGTM_PRICE_DECIMAL_);
        $ecommerceObj->discounts = round(empty($ecommerceObj->discounts) ? $discounts : $ecommerceObj->discounts + $discounts, _CDCGTM_PRICE_DECIMAL_);
        $ecommerceObj->discounts_tax_exc = round(empty($ecommerceObj->discounts_tax_exc) ? $discounts_tax_exc : $ecommerceObj->discounts_tax_exc + $discounts_tax_exc, _CDCGTM_PRICE_DECIMAL_);

        // add discount code - only one
        if(empty($ecommerceObj->coupon)) {
            $this->addCartRulesToEcommerceObject($order->getCartRules(), $ecommerceObj);
        }
    }

    /**
     * Add cart rules to the datalayer from cart
     * @param $cart
     */
    public function getCartRulesFromCart($cart) {
        if(Validate::isLoadedObject($cart)) {
            $this->addCartRulesToEcommerceObject($cart->getCartRules(), $this->ecommerce);
        }
    }

    /**
     * Given a cartRules array, add it to the Datalayer Ecommerce object
     * @param array $cartRules
     * @param object $ecommerce
     */
    protected function addCartRulesToEcommerceObject($cartRules, $ecommerce) {
        if(is_array($cartRules) && count($cartRules) > 0) {
            $ecommerce->coupon = $cartRules[0]['name'];
            $ecommerce->coupon_id = $cartRules[0]['id_cart_rule'];
            if(isset($cartRules[0]['code'])) {
                $ecommerce->coupon_code = $cartRules[0]['code'];
            } else {
                $cartRule = new CartRule($cartRules[0]['id_cart_rule']);
                if(Validate::isLoadedObject($cartRule)) {
                    $ecommerce->coupon_code = $cartRule->code;
                }
            }
        }
    }


    /**
     * Generate datalayer for order confirmation
     * @param $id_cart
     * @param false $force_resend
     */
    public function generateOrderConfirmationFromCart($id_cart, $force_resend = false) {
        $orders = PrestashopUtils::getOrdersByCartId($id_cart);

        // create action field (ecommerce infos)
        $ecommerce = new stdClass();
        $transactionId = null;

        // store last handled order
        $lastOrder = null;

        $orders_list = array();
        foreach ($orders as $id_order) {
            $id_order = $id_order['id_order'];
            $order = new Order($id_order);

            if(Validate::isLoadedObject($order)) {
                // test if order is not in error and not already sent ?
                if($order->current_state != Configuration::get('PS_OS_ERROR')
                    && ($force_resend || !CdcGtmOrderLog::isSent($id_order, $order->id_shop))) {
                    $lastOrder = $order;

                    $orders_list[] = $id_order;
                    // create log
                    $this->gtm->createGtmOrderLog($id_order, $order->id_shop);
                    $this->gtm->orderValidationDetails[] = array('id_order' => $id_order);

                    // build id if multi order
                    $order_id_field = $this->gtm->order_id_field;
                    $current_ref_order = $order->$order_id_field;
                    if(empty($transactionId)) {
                        // reference empty, set reference
                        $transactionId = (string) $current_ref_order;
                    } else {
                        // reference not empty, add it if not yet added
                        $array_ids = explode(',', $transactionId);
                        if(!in_array($current_ref_order, $array_ids)) {
                            $array_ids[] = $current_ref_order;
                            $transactionId = implode(',', $array_ids);
                        }
                    }

                    $this->addSubtotalsFromOrder($ecommerce, $order);

                    // products
                    $products = $order->getProducts();
                    $this->addProductList($products, null, $this->gtm->max_products_datalayer, 'purchase');
                }
            }
        }

        // add customer informations
        if(Validate::isLoadedObject($lastOrder)) {
            $this->customer = $this->getCustomerDataFromOrder($lastOrder);
        }

        // orders found, add infos to DL
        if(count($orders_list) > 0) {
            // set transaction ID
            if($this->eeEnabled()) {
                $ecommerce->id = $transactionId;
            }
            if($this->ga4Enabled()) {
                $ecommerce->transaction_id = $transactionId;
            }

            // add informations to main datalayer
            if($this->eeEnabled()) {
                $this->ecommerce->purchase->actionField = $ecommerce;
                $this->event = "order_confirmation";
            }

            if($this->ga4Enabled()) {
                $this->ecommerce->mergeObject($ecommerce);
                $this->event = "purchase";
            }

            $callback_name = $force_resend ? 'orderresend' : 'orderconfirmation';
            $this->eventCallback = $this->gtm->getCallback($callback_name, array(
                'orders' => $orders_list,
                'id_shop' => $this->gtm->shop_id
            ));
        }
    }

    /**
     * Return customer data from order
     * @param $order
     */
    protected function getCustomerDataFromOrder($order) {
        return $this->getCustomerData($order->id_customer, $order->id_address_delivery, $order->reference);
    }

    /**
     * Get customer data
     * @param $id_customer
     * @param null $id_address_delivery
     * @param null $exclude_order_reference
     * @return stdClass
     */
    public function getCustomerData($id_customer, $id_address_delivery = null, $exclude_order_reference = null) {
        $customerData = new stdClass();

        $customer = new Customer($id_customer);
        if(Validate::isLoadedObject($customer)) {
            $customerData->email = $customer->email;
        }

        if(!empty($id_address_delivery)) {
            $address = new Address($id_address_delivery);
            if (Validate::isLoadedObject($address)) {
                $phone = !empty($address->phone_mobile) ? $address->phone_mobile : $address->phone;
                if (!empty($phone)) {
                    $customerData->phone_number = $phone;
                }

                $addressData = new stdClass();
                $addressData->first_name = $address->firstname;
                $addressData->last_name = $address->lastname;
                $addressData->street = $address->address1 . (!empty($address->address2) ? ' ' . $address->address2 : '');
                $addressData->city = $address->city;
                //$addressData->region = '';
                $addressData->postal_code = $address->postcode;

                $country = new Country($address->id_country);
                if (Validate::isLoadedObject($country)) {
                    $addressData->country = $country->iso_code;
                }

                $customerData->address = $addressData;
            }
        }

        $past_orders = PrestashopUtils::countCustomerOtherOrders($id_customer, $exclude_order_reference);
        $customerData->new = $past_orders > 0 ? 0 : 1;
        $customerData->past_orders = $past_orders;

        return $customerData;
    }

    /**
     * @param $order_id
     * @param all|array|null $products
     */
    public function generateRefund($order_id, $products = null) {
        $order = new Order($order_id);
        if(!Validate::isLoadedObject($order)) {
            return false;
        }

        $ecommerce = new stdClass();
        if($this->eeEnabled()) {
            $ecommerce->id = (string) $order->id;
        }
        if($this->ga4Enabled()) {
            $ecommerce->transaction_id = (string) $order->id;
        }
        $this->addSubtotalsFromOrder($ecommerce, $order);

        // add products in case of partial refund
        if($products !== "all" && is_array($products) && count($products)) {
            foreach ($products as $product_full_identifier => $qtity) {
                $product_identifier = explode(',', $product_full_identifier);
                $product = new Product($product_identifier[0]);
                $product->id_product_attribute = $product_identifier[1];
                $product->quantity = $qtity;
                $this->addItem($product, 'refund');
            }
        }

        $this->eventCallback = $this->gtm->getCallback("orderrefund", array('order' => $order->id, 'id_shop' => $order->id_shop));

        if($this->eeEnabled()) {
            if(!isset($this->ecommerce->refund)) {
                $this->ecommerce->refund = new Gtm_Refund();
            }
            $this->ecommerce->refund->actionField = $ecommerce;
            $this->event = "order_refund";
        }

        if($this->ga4Enabled()) {
            $this->ecommerce->mergeObject($ecommerce);
            $this->event = "refund";
        }
    }

}
