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

class OrderInvoice extends OrderInvoiceCore
{

    public function getProductTaxesBreakdown($order = null)
    {
        if (!$order) {
            $order = $this->getOrder();
        }
    
        $breakdown = [];
        $details = $order->getProductTaxesDetails();
        $sum_composite_taxes = !$this->useOneAfterAnotherTaxComputationMethod();
    
        if ($sum_composite_taxes) {
            $grouped_details = [];
            foreach ($details as $row) {
                if ($this->id !== (int) $row['id_order_invoice']) {
                    continue;
                }
                if (!isset($grouped_details[$row['id_order_detail']])) {
                    $grouped_details[$row['id_order_detail']] = [
                        'tax_rate' => 0,
                        'total_tax_base' => 0,
                        'total_amount' => 0,
                        'id_tax' => $row['id_tax'],
                    ];
                }
    
                $grouped_details[$row['id_order_detail']]['tax_rate'] += $row['tax_rate'];
                $grouped_details[$row['id_order_detail']]['total_tax_base'] += $row['total_tax_base'];
                $grouped_details[$row['id_order_detail']]['total_amount'] += $row['total_amount'];
            }
    
            $details = $grouped_details;
        }
    
        foreach ($details as $row) {
            $rate = sprintf('%.3f', $row['tax_rate']);
            if (!isset($breakdown[$rate])) {
                $breakdown[$rate] = [
                    'total_price_tax_excl' => 0,
                    'total_amount' => 0,
                    'id_tax' => $row['id_tax'],
                    'rate' => $rate,
                ];
            }
    
            $breakdown[$rate]['total_price_tax_excl'] += $row['total_tax_base'];
            $breakdown[$rate]['total_amount'] += $row['total_amount'];
        }
    
        // Asegurar redondeo consistente
        foreach ($breakdown as $rate => $data) {
            $breakdown[$rate]['total_price_tax_excl'] = Tools::ps_round($data['total_price_tax_excl'], 2, $order->round_mode);
            $breakdown[$rate]['total_amount'] = Tools::ps_round($data['total_amount'], 2, $order->round_mode);
        }
    
        ksort($breakdown);
    
        return $breakdown;
    }

    public static function checkInvoiceDate(int $orderId) {

        $invoice = OrderInvoice::getInvoiceByOrderId($orderId);

        if ($invoice !== NULL) {
            OrderInvoice::updateInvoiceDate($invoice);
        }
        
    }

    public static function updateInvoiceDate($invoice) {

        $dateNow = date('Y-m-d H:i:s');
        Db::getInstance()->execute(
                'UPDATE `ps_order_invoice` SET `date_add` = "'.$dateNow.'" WHERE `ps_order_invoice`.`id_order_invoice` = '.$invoice->id_order.''
            );
    }

    public static function getInvoiceByOrderId(int $orderId) {
        if (is_numeric($orderId)) {
            $orderId = (int) $orderId;
        }
        if (!$orderId) {
            return false;
        }

        $id_order_invoice = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT `id_order_invoice`
            FROM `' . _DB_PREFIX_ . 'order_invoice`
            WHERE id_order = ' . (int) $orderId
        );

        return $id_order_invoice ? new OrderInvoice($id_order_invoice) : false;
    }
    
}
