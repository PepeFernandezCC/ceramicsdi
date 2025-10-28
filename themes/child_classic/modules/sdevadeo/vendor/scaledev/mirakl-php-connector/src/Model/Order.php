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

namespace Scaledev\MiraklPhpConnector\Model;

use Scaledev\MiraklPhpConnector\Collection\AdditionalFieldCollection;
use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;
use Scaledev\MiraklPhpConnector\Model\Order\OrderLine;
use Scaledev\MiraklPhpConnector\Model\Order\Promotions;

/**
 * Class Order
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class Order extends AbstractModel
{
    /** @var string */
    private $acceptance_decision_date;

    /** @var bool */
    private $can_cancel;

    /** @var array */
    private $channel;

    /** @var string */
    private $commercial_id;

    /** @var string */
    private $created_date;

    /** @var string */
    private $currency_iso_code;

    /** @var string */
    private $customer_debited_date;

    /** @var string */
    private $customer_directly_pays_seller;

    /** @var Customer */
    private $customer;

    /** @var string */
    private $customer_notification_email;

    /** @var array */
    private $delivery_date;

    /** @var array */
    private $fulfillment;

    /** @var bool */
    private $has_customer_message;

    /** @var bool */
    private $has_incident;

    /** @var bool */
    private $has_invoice;

    /** @var array */
    private $invoice_details;

    /** @var string */
    private $last_updated_date;

    /** @var AdditionalFieldCollection */
    private $order_additional_fields;

    /** @var string */
    private $order_id;

    /** @var OrderLine */
    private $order_lines;

    /** @var string */
    private $order_state;

    /** @var string */
    private $order_state_reason_code;

    /** @var string */
    private $order_state_reason_label;

    /** @var string */
    private $order_tax_mode;

    /** @var integer */
    private $payment_duration;

    /** @var string */
    private $payment_type;

    /** @var string */
    private $payment_workflow;

    /** @var float */
    private $price;

    /** @var Promotions */
    private $promotions;

    /** @var string */
    private $quote_id;

    /** @var array */
    private $references;

    /** @var string */
    private $shipping_carrier_code;

    /** @var string */
    private $shipping_company;

    /** @var string */
    private $shipping_deadline;

    /** @var string */
    private $shipping_from;

    /** @var float */
    private $shipping_price;

    /** @var string */
    private $shipping_pudo_id;

    /** @var string */
    private $shipping_tracking;

    /** @var string */
    private $shipping_tracking_url;

    /** @var string */
    private $shipping_type_code;

    /** @var string */
    private $shipping_type_label;

    /** @var string */
    private $shipping_zone_code;

    /** @var string */
    private $shipping_zone_label;

    /** @var float */
    private $total_commission;

    /** @var float */
    private $total_price;

    /** @var string */
    private $transaction_date;

    /** @var string */
    private $transaction_number;

    /**
     * @return string
     */
    public function getAcceptanceDecisionDate()
    {
        return $this->acceptance_decision_date;
    }

    /**
     * @param string $acceptance_decision_date
     * @return $this
     */
    public function setAcceptanceDecisionDate($acceptance_decision_date)
    {
        $this->acceptance_decision_date = $acceptance_decision_date;
        return $this;
    }

    /**
     * @return array
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param array $channel
     * @return $this
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
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
    public function getCurrencyIsoCode()
    {
        return $this->currency_iso_code;
    }

    /**
     * @param string $currency_iso_code
     * @return $this
     */
    public function setCurrencyIsoCode($currency_iso_code)
    {
        $this->currency_iso_code = $currency_iso_code;
        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     * @return $this
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return AdditionalFieldCollection
     */
    public function getOrderAdditionalFields()
    {
        return $this->order_additional_fields;
    }

    /**
     * @param AdditionalFieldCollection $order_additional_fields
     * @return $this
     */
    public function setOrderAdditionalFields($order_additional_fields)
    {
        $this->order_additional_fields = $order_additional_fields;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * @param string $order_id
     * @return $this
     */
    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderState()
    {
        return $this->order_state;
    }

    /**
     * @param string $order_state
     * @return $this
     */
    public function setOrderState($order_state)
    {
        $this->order_state = $order_state;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderTaxMode()
    {
        return $this->order_tax_mode;
    }

    /**
     * @param string $order_tax_mode
     * @return $this
     */
    public function setOrderTaxMode($order_tax_mode)
    {
        $this->order_tax_mode = $order_tax_mode;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return Promotions
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    /**
     * @param Promotions $promotions
     * @return $this
     */
    public function setPromotions($promotions)
    {
        $this->promotions = $promotions;
        return $this;
    }

    /**
     * @return array
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * @param array $references
     * @return $this
     */
    public function setReferences($references)
    {
        $this->references = $references;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingCarrierCode()
    {
        return $this->shipping_carrier_code;
    }

    /**
     * @param string $shipping_carrier_code
     * @return $this
     */
    public function setShippingCarrierCode($shipping_carrier_code)
    {
        $this->shipping_carrier_code = $shipping_carrier_code;
        return $this;
    }

    /**
     * @return float
     */
    public function getShippingPrice()
    {
        return $this->shipping_price;
    }

    /**
     * @param float $shipping_price
     * @return $this
     */
    public function setShippingPrice($shipping_price)
    {
        $this->shipping_price = $shipping_price;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingPudoId()
    {
        return $this->shipping_pudo_id;
    }

    /**
     * @param string $shipping_pudo_id
     * @return $this
     */
    public function setShippingPudoId($shipping_pudo_id)
    {
        $this->shipping_pudo_id = $shipping_pudo_id;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalCommission()
    {
        return $this->total_commission;
    }

    /**
     * @param float $total_commission
     * @return $this
     */
    public function setTotalCommission($total_commission)
    {
        $this->total_commission = $total_commission;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalPrice()
    {
        return $this->total_price;
    }

    /**
     * @param float $total_price
     * @return $this
     */
    public function setTotalPrice($total_price)
    {
        $this->total_price = $total_price;
        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionDate()
    {
        return $this->transaction_date;
    }

    /**
     * @param string $transaction_date
     * @return $this
     */
    public function setTransactionDate($transaction_date)
    {
        $this->transaction_date = $transaction_date;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCanCancel()
    {
        return $this->can_cancel;
    }

    /**
     * @param bool $can_cancel
     * @return $this
     */
    public function setCanCancel($can_cancel)
    {
        $this->can_cancel = $can_cancel;
        return $this;
    }

    /**
     * @return string
     */
    public function getCommercialId()
    {
        return $this->commercial_id;
    }

    /**
     * @param string $commercial_id
     * @return $this
     */
    public function setCommercialId($commercial_id)
    {
        $this->commercial_id = $commercial_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerDebitedDate()
    {
        return $this->customer_debited_date;
    }

    /**
     * @param string $customer_debited_date
     * @return $this
     */
    public function setCustomerDebitedDate($customer_debited_date)
    {
        $this->customer_debited_date = $customer_debited_date;
        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerDirectlyPaysSeller()
    {
        return $this->customer_directly_pays_seller;
    }

    /**
     * @param string $customer_directly_pays_seller
     * @return $this
     */
    public function setCustomerDirectlyPaysSeller($customer_directly_pays_seller)
    {
        $this->customer_directly_pays_seller = $customer_directly_pays_seller;
        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerNotificationEmail()
    {
        return $this->customer_notification_email;
    }

    /**
     * @param string $customer_notification_email
     * @return $this
     */
    public function setCustomerNotificationEmail($customer_notification_email)
    {
        $this->customer_notification_email = $customer_notification_email;
        return $this;
    }

    /**
     * @return array
     */
    public function getDeliveryDate()
    {
        return $this->delivery_date;
    }

    /**
     * @param array $delivery_date
     * @return $this
     */
    public function setDeliveryDate($delivery_date)
    {
        $this->delivery_date = $delivery_date;
        return $this;
    }

    /**
     * @return array
     */
    public function getFulfillment()
    {
        return $this->fulfillment;
    }

    /**
     * @param array $fulfillment
     * @return $this
     */
    public function setFulfillment($fulfillment)
    {
        $this->fulfillment = $fulfillment;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHasCustomerMessage()
    {
        return $this->has_customer_message;
    }

    /**
     * @param bool $has_customer_message
     * @return $this
     */
    public function setHasCustomerMessage($has_customer_message)
    {
        $this->has_customer_message = $has_customer_message;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHasIncident()
    {
        return $this->has_incident;
    }

    /**
     * @param bool $has_incident
     * @return $this
     */
    public function setHasIncident($has_incident)
    {
        $this->has_incident = $has_incident;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHasInvoice()
    {
        return $this->has_invoice;
    }

    /**
     * @param bool $has_invoice
     * @return $this
     */
    public function setHasInvoice($has_invoice)
    {
        $this->has_invoice = $has_invoice;
        return $this;
    }

    /**
     * @return array
     */
    public function getInvoiceDetails()
    {
        return $this->invoice_details;
    }

    /**
     * @param array $invoice_details
     * @return $this
     */
    public function setInvoiceDetails($invoice_details)
    {
        $this->invoice_details = $invoice_details;
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
     * @return OrderLine
     */
    public function getOrderLines()
    {
        return $this->order_lines;
    }

    /**
     * @param OrderLine $order_lines
     * @return $this
     */
    public function setOrderLines($order_lines)
    {
        $this->order_lines = $order_lines;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderStateReasonCode()
    {
        return $this->order_state_reason_code;
    }

    /**
     * @param string $order_state_reason_code
     * @return $this
     */
    public function setOrderStateReasonCode($order_state_reason_code)
    {
        $this->order_state_reason_code = $order_state_reason_code;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderStateReasonLabel()
    {
        return $this->order_state_reason_label;
    }

    /**
     * @param string $order_state_reason_label
     * @return $this
     */
    public function setOrderStateReasonLabel($order_state_reason_label)
    {
        $this->order_state_reason_label = $order_state_reason_label;
        return $this;
    }

    /**
     * @return int
     */
    public function getPaymentDuration()
    {
        return $this->payment_duration;
    }

    /**
     * @param int $payment_duration
     * @return $this
     */
    public function setPaymentDuration($payment_duration)
    {
        $this->payment_duration = $payment_duration;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentType()
    {
        return $this->payment_type;
    }

    /**
     * @param string $payment_type
     * @return $this
     */
    public function setPaymentType($payment_type)
    {
        $this->payment_type = $payment_type;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentWorkflow()
    {
        return $this->payment_workflow;
    }

    /**
     * @param string $payment_workflow
     * @return $this
     */
    public function setPaymentWorkflow($payment_workflow)
    {
        $this->payment_workflow = $payment_workflow;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuoteId()
    {
        return $this->quote_id;
    }

    /**
     * @param string $quote_id
     * @return $this
     */
    public function setQuoteId($quote_id)
    {
        $this->quote_id = $quote_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingCompany()
    {
        return $this->shipping_company;
    }

    /**
     * @param string $shipping_company
     * @return $this
     */
    public function setShippingCompany($shipping_company)
    {
        $this->shipping_company = $shipping_company;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingDeadline()
    {
        return $this->shipping_deadline;
    }

    /**
     * @param string $shipping_deadline
     * @return $this
     */
    public function setShippingDeadline($shipping_deadline)
    {
        $this->shipping_deadline = $shipping_deadline;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingFrom()
    {
        return $this->shipping_from;
    }

    /**
     * @param string $shipping_from
     * @return $this
     */
    public function setShippingFrom($shipping_from)
    {
        $this->shipping_from = $shipping_from;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingTracking()
    {
        return $this->shipping_tracking;
    }

    /**
     * @param string $shipping_tracking
     * @return $this
     */
    public function setShippingTracking($shipping_tracking)
    {
        $this->shipping_tracking = $shipping_tracking;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingTrackingUrl()
    {
        return $this->shipping_tracking_url;
    }

    /**
     * @param string $shipping_tracking_url
     * @return $this
     */
    public function setShippingTrackingUrl($shipping_tracking_url)
    {
        $this->shipping_tracking_url = $shipping_tracking_url;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingTypeCode()
    {
        return $this->shipping_type_code;
    }

    /**
     * @param string $shipping_type_code
     * @return $this
     */
    public function setShippingTypeCode($shipping_type_code)
    {
        $this->shipping_type_code = $shipping_type_code;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingTypeLabel()
    {
        return $this->shipping_type_label;
    }

    /**
     * @param string $shipping_type_label
     * @return $this
     */
    public function setShippingTypeLabel($shipping_type_label)
    {
        $this->shipping_type_label = $shipping_type_label;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingZoneCode()
    {
        return $this->shipping_zone_code;
    }

    /**
     * @param string $shipping_zone_code
     * @return $this
     */
    public function setShippingZoneCode($shipping_zone_code)
    {
        $this->shipping_zone_code = $shipping_zone_code;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingZoneLabel()
    {
        return $this->shipping_zone_label;
    }

    /**
     * @param string $shipping_zone_label
     * @return $this
     */
    public function setShippingZoneLabel($shipping_zone_label)
    {
        $this->shipping_zone_label = $shipping_zone_label;
        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionNumber()
    {
        return $this->transaction_number;
    }

    /**
     * @param string $transaction_number
     * @return $this
     */
    public function setTransactionNumber($transaction_number)
    {
        $this->transaction_number = $transaction_number;
        return $this;
    }
}
