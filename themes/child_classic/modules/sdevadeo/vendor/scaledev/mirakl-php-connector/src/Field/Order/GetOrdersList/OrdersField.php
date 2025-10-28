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

namespace Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList;

use Scaledev\MiraklPhpConnector\Collection\OrderCollection;
use Scaledev\MiraklPhpConnector\Core\Field\AbstractField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\AcceptanceDecisionDateField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CanCancelField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ChannelField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CommercialIdField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CreatedDateField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CurrencyIsoCodeField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerDebitedDateField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerDirectlyPaysSellerField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerNotificationEmailField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\DeliveryDateField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\FulfillmentField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\HasCustomerMessageField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\HasIncidentField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\HasInvoiceField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\InvoiceDetailsField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\LastUpdatedDateField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderAdditionalFieldsField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderIdField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderStateField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderStateReasonCodeField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderStateReasonLabelField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderTaxModeField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\PaymentDurationField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\PaymentTypeField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\PaymentWorkflowField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\PriceField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\PromotionsField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\QuoteIdField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ReferencesField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingCarrierCodeField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingCompanyField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingDeadlineField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingFromField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingPriceField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingPudoIdField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingTrackingField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingTrackingUrlField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingTypeCodeField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingTypeLabelField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingZoneCodeField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingZoneLabelField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\TotalCommissionField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\TotalPriceField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\TransactionDateField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\TransactionNumberField;
use Scaledev\MiraklPhpConnector\Validator\RequiredValidator;
use Scaledev\MiraklPhpConnector\Validator\Type\ArrayTypeValidator;

/**
 * Class OrdersField
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class OrdersField extends AbstractField
{
    /**
     * @inheritdoc
     */
    const CONSTRAINTS =  array(
        RequiredValidator::class,
        ArrayTypeValidator::class
    );

    /**
     * @inheritdoc
     */
    const TYPE = OrderCollection::class;

    /**
     * @inheritdoc
     */
    const CHILD_FIELD = array(
        AcceptanceDecisionDateField::class,
        CanCancelField::class,
        ChannelField::class,
        CommercialIdField::class,
        CreatedDateField::class,
        CurrencyIsoCodeField::class,
        CustomerDebitedDateField::class,
        CustomerDirectlyPaysSellerField::class,
        CustomerField::class,
        CustomerNotificationEmailField::class,
        DeliveryDateField::class,
        FulfillmentField::class,
        HasCustomerMessageField::class,
        HasIncidentField::class,
        HasInvoiceField::class,
        InvoiceDetailsField::class,
        LastUpdatedDateField::class,
        OrderAdditionalFieldsField::class,
        OrderIdField::class,
        OrderLinesField::class,
        OrderStateField::class,
        OrderStateReasonCodeField::class,
        OrderStateReasonLabelField::class,
        OrderTaxModeField::class,
        PaymentDurationField::class,
        PaymentTypeField::class,
        PaymentWorkflowField::class,
        PriceField::class,
        PromotionsField::class,
        QuoteIdField::class,
        ReferencesField::class,
        ShippingCarrierCodeField::class,
        ShippingCompanyField::class,
        ShippingDeadlineField::class,
        ShippingFromField::class,
        ShippingPriceField::class,
        ShippingPudoIdField::class,
        ShippingTrackingField::class,
        ShippingTrackingUrlField::class,
        ShippingTypeCodeField::class,
        ShippingTypeLabelField::class,
        ShippingZoneCodeField::class,
        ShippingZoneLabelField::class,
        TotalCommissionField::class,
        TotalPriceField::class,
        TransactionDateField::class,
        TransactionNumberField::class,
    );
}
