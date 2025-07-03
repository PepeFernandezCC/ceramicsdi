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
 * @package Scaledev\MiraklPhpConnector
 * Support: support@scaledev.fr
 */

namespace Scaledev\MiraklPhpConnector\Model\Order;

use Scaledev\MiraklPhpConnector\Collection\AdditionalFieldCollection;
use Scaledev\MiraklPhpConnector\Collection\Component\Order\OrderLine\CommissionTaxCollection;
use Scaledev\MiraklPhpConnector\Collection\Component\Order\OrderLine\OrderLineTaxCollection;
use Scaledev\MiraklPhpConnector\Collection\Component\Order\OrderLine\ProductMediaCollection;
use Scaledev\MiraklPhpConnector\Collection\Component\Order\OrderLine\PromotionCollection;
use Scaledev\MiraklPhpConnector\Collection\Component\Order\OrderLine\RefundCollection;
use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;
use Scaledev\MiraklPhpConnector\Model\Order\OrderLine\Address;
use Scaledev\MiraklPhpConnector\Model\Order\OrderLine\Cancelation;
use Scaledev\MiraklPhpConnector\Model\Order\OrderLine\Measurement;
use Scaledev\MiraklPhpConnector\Model\Order\OrderLine\OrderLineTax\AmountBreakdown;

/**
 * Class OrderLine
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class OrderLine extends AbstractModel
{
    /**
     * Indicate whether the order line full amount can be refunded
     *
     * @var boolean
     */
    private $can_refund;

    /**
     * @var Cancelation
     */
    private $cancelations;

    /**
     * @var string
     */
    private $category_code;

    /**
     * @var string
     */
    private $category_label;

    /**
     * @var number
     */
    private $commission_fee;

    /**
     * @var CommissionTaxCollection
     */
    private $commission_taxes;

    /**
     * @var string
     */
    private $created_date;

    /**
     * @var string
     */
    private $debited_date;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $last_updated_date;

    /**
     * @var Measurement
     */
    private $measurement;

    /**
     * @var string
     */
    private $offer_id;

    /**
     * @var string
     */
    private $offer_sku;

    /**
     * @var string
     */
    private $offer_state_code;

    /**
     * @var AdditionalFieldCollection
     */
    private $order_line_additional_fields;

    /**
     * @var string
     */
    private $order_line_id;

    /**
     * @var integer
     */
    private $order_line_index;

    /**
     * @var string
     */
    private $order_line_state;

    /**
     * @var string
     */
    private $order_line_state_reason_code;

    /**
     * @var string
     */
    private $order_line_state_reason_label;

    /**
     * @var number
     */
    private $price;

    /**
     * @var string
     */
    private $price_additional_info;

    /**
     * @var AmountBreakdown
     */
    private $price_amount_breakdown;

    /**
     * @var number
     */
    private $price_unit;

    /**
     * @var ProductMediaCollection
     */
    private $product_medias;

    /**
     * @var string
     */
    private $product_sku;

    /**
     * @var string
     */
    private $product_title;

    /**
     * @var PromotionCollection
     */
    private $promotions;

    /**
     * @var integer
     */
    private $quantity;

    /**
     * @var string
     */
    private $received_date;

    /**
     * @var RefundCollection
     */
    private $refunds;

    /**
     * @var string
     */
    private $shipped_date;

    /**
     * @var array
     */
    private $shipping_from;

    /**
     * @var number
     */
    private $shipping_price;

    /**
     * @var AmountBreakdown
     */
    private $shipping_price_amount_breakdown;

    /**
     * @var OrderLineTaxCollection
     */
    private $shipping_taxes;

    /**
     * @var OrderLineTaxCollection
     */
    private $taxes;

    /**
     * @var number
     */
    private $total_commission;

    /**
     * @var number
     */
    private $total_price;

    /**
     * @return bool
     */
    public function isCanRefund()
    {
        return $this->can_refund;
    }

    /**
     * @param bool $can_refund
     * @return $this
     */
    public function setCanRefund($can_refund)
    {
        $this->can_refund = $can_refund;
        return $this;
    }

    /**
     * @return Cancelation
     */
    public function getCancelations()
    {
        return $this->cancelations;
    }

    /**
     * @param Cancelation $cancelations
     * @return $this
     */
    public function setCancelations($cancelations)
    {
        $this->cancelations = $cancelations;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategoryCode()
    {
        return $this->category_code;
    }

    /**
     * @param string $category_code
     * @return $this
     */
    public function setCategoryCode($category_code)
    {
        $this->category_code = $category_code;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategoryLabel()
    {
        return $this->category_label;
    }

    /**
     * @param string $category_label
     * @return $this
     */
    public function setCategoryLabel($category_label)
    {
        $this->category_label = $category_label;
        return $this;
    }

    /**
     * @return number
     */
    public function getCommissionFee()
    {
        return $this->commission_fee;
    }

    /**
     * @param number $commission_fee
     * @return $this
     */
    public function setCommissionFee($commission_fee)
    {
        $this->commission_fee = $commission_fee;
        return $this;
    }

    /**
     * @return CommissionTaxCollection
     */
    public function getCommissionTaxes()
    {
        return $this->commission_taxes;
    }

    /**
     * @param CommissionTaxCollection $commission_taxes
     * @return $this
     */
    public function setCommissionTaxes($commission_taxes)
    {
        $this->commission_taxes = $commission_taxes;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedDate()
    {
        return $this->created_date;
    }

    /**
     * @param string $created_date
     * @return $this
     */
    public function setCreatedDate($created_date)
    {
        $this->created_date = $created_date;
        return $this;
    }

    /**
     * @return string
     */
    public function getDebitedDate()
    {
        return $this->debited_date;
    }

    /**
     * @param string $debited_date
     * @return $this
     */
    public function setDebitedDate($debited_date)
    {
        $this->debited_date = $debited_date;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastUpdatedDate()
    {
        return $this->last_updated_date;
    }

    /**
     * @param string $last_updated_date
     * @return $this
     */
    public function setLastUpdatedDate($last_updated_date)
    {
        $this->last_updated_date = $last_updated_date;
        return $this;
    }

    /**
     * @return Measurement
     */
    public function getMeasurement()
    {
        return $this->measurement;
    }

    /**
     * @param Measurement $measurement
     * @return $this
     */
    public function setMeasurement($measurement)
    {
        $this->measurement = $measurement;
        return $this;
    }

    /**
     * @return string
     */
    public function getOfferId()
    {
        return $this->offer_id;
    }

    /**
     * @param string $offer_id
     * @return $this
     */
    public function setOfferId($offer_id)
    {
        $this->offer_id = $offer_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getOfferSku()
    {
        return $this->offer_sku;
    }

    /**
     * @param string $offer_sku
     * @return $this
     */
    public function setOfferSku($offer_sku)
    {
        $this->offer_sku = $offer_sku;
        return $this;
    }

    /**
     * @return string
     */
    public function getOfferStateCode()
    {
        return $this->offer_state_code;
    }

    /**
     * @param string $offer_state_code
     * @return $this
     */
    public function setOfferStateCode($offer_state_code)
    {
        $this->offer_state_code = $offer_state_code;
        return $this;
    }

    /**
     * @return AdditionalFieldCollection
     */
    public function getOrderLineAdditionalFields()
    {
        return $this->order_line_additional_fields;
    }

    /**
     * @param AdditionalFieldCollection $order_line_additional_fields
     * @return $this
     */
    public function setOrderLineAdditionalFields($order_line_additional_fields)
    {
        $this->order_line_additional_fields = $order_line_additional_fields;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderLineId()
    {
        return $this->order_line_id;
    }

    /**
     * @param string $order_line_id
     * @return $this
     */
    public function setOrderLineId($order_line_id)
    {
        $this->order_line_id = $order_line_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrderLineIndex()
    {
        return $this->order_line_index;
    }

    /**
     * @param int $order_line_index
     * @return $this
     */
    public function setOrderLineIndex($order_line_index)
    {
        $this->order_line_index = $order_line_index;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderLineState()
    {
        return $this->order_line_state;
    }

    /**
     * @param string $order_line_state
     * @return $this
     */
    public function setOrderLineState($order_line_state)
    {
        $this->order_line_state = $order_line_state;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderLineStateReasonCode()
    {
        return $this->order_line_state_reason_code;
    }

    /**
     * @param string $order_line_state_reason_code
     * @return $this
     */
    public function setOrderLineStateReasonCode($order_line_state_reason_code)
    {
        $this->order_line_state_reason_code = $order_line_state_reason_code;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderLineStateReasonLabel()
    {
        return $this->order_line_state_reason_label;
    }

    /**
     * @param string $order_line_state_reason_label
     * @return $this
     */
    public function setOrderLineStateReasonLabel($order_line_state_reason_label)
    {
        $this->order_line_state_reason_label = $order_line_state_reason_label;
        return $this;
    }

    /**
     * @return number
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param number $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return string
     */
    public function getPriceAdditionalInfo()
    {
        return $this->price_additional_info;
    }

    /**
     * @param string $price_additional_info
     * @return $this
     */
    public function setPriceAdditionalInfo($price_additional_info)
    {
        $this->price_additional_info = $price_additional_info;
        return $this;
    }

    /**
     * @return AmountBreakdown
     */
    public function getPriceAmountBreakdown()
    {
        return $this->price_amount_breakdown;
    }

    /**
     * @param AmountBreakdown $price_amount_breakdown
     * @return $this
     */
    public function setPriceAmountBreakdown($price_amount_breakdown)
    {
        $this->price_amount_breakdown = $price_amount_breakdown;
        return $this;
    }

    /**
     * @return number
     */
    public function getPriceUnit()
    {
        return $this->price_unit;
    }

    /**
     * @param number $price_unit
     * @return $this
     */
    public function setPriceUnit($price_unit)
    {
        $this->price_unit = $price_unit;
        return $this;
    }

    /**
     * @return ProductMediaCollection
     */
    public function getProductMedias()
    {
        return $this->product_medias;
    }

    /**
     * @param ProductMediaCollection $product_medias
     * @return $this
     */
    public function setProductMedias($product_medias)
    {
        $this->product_medias = $product_medias;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductSku()
    {
        return $this->product_sku;
    }

    /**
     * @param string $product_sku
     * @return $this
     */
    public function setProductSku($product_sku)
    {
        $this->product_sku = $product_sku;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductTitle()
    {
        return $this->product_title;
    }

    /**
     * @param string $product_title
     * @return $this
     */
    public function setProductTitle($product_title)
    {
        $this->product_title = $product_title;
        return $this;
    }

    /**
     * @return PromotionCollection
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    /**
     * @param PromotionCollection $promotions
     * @return $this
     */
    public function setPromotions($promotions)
    {
        $this->promotions = $promotions;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return string
     */
    public function getReceivedDate()
    {
        return $this->received_date;
    }

    /**
     * @param string $received_date
     * @return $this
     */
    public function setReceivedDate($received_date)
    {
        $this->received_date = $received_date;
        return $this;
    }

    /**
     * @return RefundCollection
     */
    public function getRefunds()
    {
        return $this->refunds;
    }

    /**
     * @param RefundCollection $refunds
     * @return $this
     */
    public function setRefunds($refunds)
    {
        $this->refunds = $refunds;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippedDate()
    {
        return $this->shipped_date;
    }

    /**
     * @param string $shipped_date
     * @return $this
     */
    public function setShippedDate($shipped_date)
    {
        $this->shipped_date = $shipped_date;
        return $this;
    }

    /**
     * @return array
     */
    public function getShippingFrom()
    {
        return $this->shipping_from;
    }

    /**
     * @param array $shipping_from
     * @return $this
     */
    public function setShippingFrom($shipping_from)
    {
        $this->shipping_from = $shipping_from;
        return $this;
    }

    /**
     * @return number
     */
    public function getShippingPrice()
    {
        return $this->shipping_price;
    }

    /**
     * @param number $shipping_price
     * @return $this
     */
    public function setShippingPrice($shipping_price)
    {
        $this->shipping_price = $shipping_price;
        return $this;
    }

    /**
     * @return AmountBreakdown
     */
    public function getShippingPriceAmountBreakdown()
    {
        return $this->shipping_price_amount_breakdown;
    }

    /**
     * @param AmountBreakdown $shipping_price_amount_breakdown
     * @return $this
     */
    public function setShippingPriceAmountBreakdown($shipping_price_amount_breakdown)
    {
        $this->shipping_price_amount_breakdown = $shipping_price_amount_breakdown;
        return $this;
    }

    /**
     * @return OrderLineTaxCollection
     */
    public function getShippingTaxes()
    {
        return $this->shipping_taxes;
    }

    /**
     * @param OrderLineTaxCollection $shipping_taxes
     * @return $this
     */
    public function setShippingTaxes($shipping_taxes)
    {
        $this->shipping_taxes = $shipping_taxes;
        return $this;
    }

    /**
     * @return OrderLineTaxCollection
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    /**
     * @param OrderLineTaxCollection $taxes
     * @return $this
     */
    public function setTaxes($taxes)
    {
        $this->taxes = $taxes;
        return $this;
    }

    /**
     * @return number
     */
    public function getTotalCommission()
    {
        return $this->total_commission;
    }

    /**
     * @param number $total_commission
     * @return $this
     */
    public function setTotalCommission($total_commission)
    {
        $this->total_commission = $total_commission;
        return $this;
    }

    /**
     * @return number
     */
    public function getTotalPrice()
    {
        return $this->total_price;
    }

    /**
     * @param number $total_price
     * @return $this
     */
    public function setTotalPrice($total_price)
    {
        $this->total_price = $total_price;
        return $this;
    }
}
