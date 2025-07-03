<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

class Order extends OrderCore
{

    public static function getLastInvoiceNumber()
    {
        $sql = 'SELECT MAX(`number`) FROM `' . _DB_PREFIX_ . 'order_invoice` WHERE `id_order_invoice` > 630';


        return Db::getInstance()->getValue($sql);
    }

    public static function setLastInvoiceNumber($order_invoice_id, $id_shop)
    {
        if (!$order_invoice_id) {
            return false;
        }

        $number = Configuration::get('PS_INVOICE_START_NUMBER', null, null, $id_shop);
        // If invoice start number has been set, you clean the value of this configuration
        if ($number) {
            Configuration::updateValue('PS_INVOICE_START_NUMBER', false, false, null, $id_shop);
        }

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'order_invoice` SET number =';

        if (!$number) {

            $getNumberSql = 'SELECT MAX(`number`) FROM `' . _DB_PREFIX_ . 'order_invoice` WHERE `id_order_invoice` > 630';
            $lastNumber = Db::getInstance()->getValue($getNumberSql);
            $number = $lastNumber +1;
        } 

        $sql .= (int) $number;

        $sql .= ' WHERE `id_order_invoice` = ' . (int) $order_invoice_id;

        return Db::getInstance()->execute($sql);
    }


    /**
     * By default this function was made for invoice, to compute tax amounts and balance delta (because of computation made on round values).
     * If you provide $limitToOrderDetails, only these item will be taken into account. This option is useful for order slip for example,
     * where only sublist of the order is refunded.
     *
     * @param $limitToOrderDetails Optional array of OrderDetails to take into account. False by default to take all OrderDetails from the current Order.
     *
     * @return array a list of tax rows applied to the given OrderDetails (or all OrderDetails linked to the current Order)
     */
    public function getProductTaxesDetails($limitToOrderDetails = false)
    {
        $round_type = $this->round_type;
        if ($round_type == 0) {
            // if this is 0, it means the field did not exist
            // at the time the order was made.
            // Set it to old type, which was closest to line.
            $round_type = Order::ROUND_LINE;
        }

        // compute products discount
        $order_discount_tax_excl = $this->total_discounts_tax_excl;

        $free_shipping_tax = 0;
        $product_specific_discounts = [];

        $expected_total_base = $this->total_products - $this->total_discounts_tax_excl;

        foreach ($this->getCartRules() as $order_cart_rule) {
            if ($order_cart_rule['free_shipping'] && $free_shipping_tax === 0) {
                $free_shipping_tax = $this->total_shipping_tax_incl - $this->total_shipping_tax_excl;
                $order_discount_tax_excl -= $this->total_shipping_tax_excl;
                $expected_total_base += $this->total_shipping_tax_excl;
            }

            $cart_rule = new CartRule($order_cart_rule['id_cart_rule']);
            if ($cart_rule->reduction_product > 0) {
                if (empty($product_specific_discounts[$cart_rule->reduction_product])) {
                    $product_specific_discounts[$cart_rule->reduction_product] = 0;
                }

                $product_specific_discounts[$cart_rule->reduction_product] += $order_cart_rule['value_tax_excl'];
                $order_discount_tax_excl -= $order_cart_rule['value_tax_excl'];
            }
        }

        $products_tax = $this->total_products_wt - $this->total_products;
        $discounts_tax = $this->total_discounts_tax_incl - $this->total_discounts_tax_excl;

        // We add $free_shipping_tax because when there is free shipping, the tax that would
        // be paid if there wasn't is included in $discounts_tax.
        $expected_total_tax = $products_tax - $discounts_tax + $free_shipping_tax;
        $actual_total_tax = 0;
        $actual_total_base = 0;

        $order_detail_tax_rows = [];

        $breakdown = [];

        // Get order_details
        $order_details = $limitToOrderDetails ? $limitToOrderDetails : $this->getOrderDetailList();

        $order_ecotax_tax = 0;

        $tax_rates = [];

        foreach ($order_details as $order_detail) {
            $id_order_detail = $order_detail['id_order_detail'];
            $tax_calculator = OrderDetail::getTaxCalculatorStatic($id_order_detail);

            // TODO: probably need to make an ecotax tax breakdown here instead,
            // but it seems unlikely there will be different tax rates applied to the
            // ecotax in the same order in the real world
            $unit_ecotax_tax = $order_detail['ecotax'] * $order_detail['ecotax_tax_rate'] / 100;
            $order_ecotax_tax += $order_detail['product_quantity'] * $unit_ecotax_tax;

            $discount_ratio = 0;

            if ($this->total_products > 0) {
                $discount_ratio = ($order_detail['unit_price_tax_excl'] + $order_detail['ecotax']) / $this->total_products;
            }

            // share of global discount
            $discounted_price_tax_excl = $order_detail['unit_price_tax_excl'] - $discount_ratio * $order_discount_tax_excl;
            // specific discount
            if (!empty($product_specific_discounts[$order_detail['product_id']])) {
                $discounted_price_tax_excl -= $product_specific_discounts[$order_detail['product_id']];
            }

            $quantity = $order_detail['product_quantity'];

            foreach ($tax_calculator->taxes as $tax) {
                $tax_rates[$tax->id] = $tax->rate;
            }

            foreach ($tax_calculator->getTaxesAmount($discounted_price_tax_excl) as $id_tax => $unit_amount) {
                $total_tax_base = 0;
                switch ($round_type) {
                    
                    case Order::ROUND_ITEM:
                        $total_tax_base = $quantity * Tools::ps_round($discounted_price_tax_excl, 2, $this->round_mode);
                        $total_amount = $quantity * Tools::ps_round($unit_amount, 2, $this->round_mode);
                        break;

                    case Order::ROUND_LINE:
                        $total_tax_base = Tools::ps_round($quantity * $discounted_price_tax_excl, 2, $this->round_mode);
                        $total_amount = Tools::ps_round($quantity * $unit_amount, 2, $this->round_mode);
                        break;

                    case Order::ROUND_TOTAL:
                        $total_tax_base = $quantity * $discounted_price_tax_excl;
                        $total_amount = $quantity * $unit_amount;
                        break;
                }

                if (!isset($breakdown[$id_tax])) {
                    $breakdown[$id_tax] = ['tax_base' => 0, 'tax_amount' => 0];
                }

                $breakdown[$id_tax]['tax_base'] += $total_tax_base;
                $breakdown[$id_tax]['tax_amount'] += $total_amount;

                $order_detail_tax_rows[] = [
                    'id_order_detail' => $id_order_detail,
                    'id_tax' => $id_tax,
                    'tax_rate' => $tax_rates[$id_tax],
                    'unit_tax_base' => $discounted_price_tax_excl,
                    'total_tax_base' => $total_tax_base,
                    'unit_amount' => $unit_amount,
                    'total_amount' => $total_amount,
                    'id_order_invoice' => $order_detail['id_order_invoice'],
                ];
            }
        }

        if (!empty($order_detail_tax_rows)) {
            foreach ($breakdown as $data) {
                $actual_total_tax += Tools::ps_round($data['tax_amount'], 2, $this->round_mode);
                $actual_total_base += Tools::ps_round($data['tax_base'], 2, $this->round_mode);
            }

            $order_ecotax_tax = Tools::ps_round($order_ecotax_tax, 2, $this->round_mode);

            $tax_rounding_error = $expected_total_tax - $actual_total_tax - $order_ecotax_tax;
            if ($tax_rounding_error !== 0) {
                Tools::spreadAmount($tax_rounding_error, 2, $order_detail_tax_rows, 'total_amount');
            }

            $base_rounding_error = $expected_total_base - $actual_total_base;
            if ($base_rounding_error !== 0) {
                Tools::spreadAmount($base_rounding_error, 2, $order_detail_tax_rows, 'total_tax_base');
            }
        }

        return $order_detail_tax_rows;
    }

    public function getUniqIdReference()
    {
        $query = new DbQuery();
        $query->select('MIN(id_order) as min, MAX(id_order) as max');
        $query->from('orders');
        $query->where('id_cart = ' . (int) $this->id_cart);

        $order = Db::getInstance()->getRow($query);

        if ($order['min'] == $order['max']) {
            return $this->id;
        } else {
            return $this->id . '#' . ($this->id + 1 - $order['min']);
        }
    }

}
