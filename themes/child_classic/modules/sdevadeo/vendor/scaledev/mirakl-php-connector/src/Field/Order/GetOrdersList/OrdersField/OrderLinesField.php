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

namespace Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField;

use Scaledev\MiraklPhpConnector\Collection\Component\Order\OrderLineCollection;
use Scaledev\MiraklPhpConnector\Core\Field\AbstractField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\CancelationsField as OrderLineCancelations;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\CanRefundField as OrderLineCanRefund;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\CategoryCodeField as OrderLineCategoryCode;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\CategoryLabelField as OrderLineCategoryLabel;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\CommissionFeeField as OrderLineCommissionFee;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\CommissionTaxesField as OrderLineCommissionTaxes;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\DebitedDateField as OrderLineDebitedDate;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\DescriptionField as OrderLineDescription;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\MeasurementField as OrderLineMeasurement;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\OfferIdField as OrderLineOfferId;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\OfferSkuField as OrderLineOfferSku;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\OfferStateCodeField as OrderLineOfferStateCode;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\OrderLineAdditionalFieldsField as OrderLineOrderLineAdditionalFields;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\OrderLineIdField as OrderLineOrderLineId;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\OrderLineIndexField as OrderLineOrderLineIndex;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\OrderLineStateField as OrderLineOrderLineState;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\OrderLineStateReasonCodeField as OrderLineOrderLineStateReasonCode;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\OrderLineStateReasonLabelField as OrderLineOrderLineStateReasonLabel;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\PriceAdditionalInfoField as OrderLinePriceAdditionalInfo;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\PriceAmountBreakdownField as OrderLinePriceAmountBreakdown;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\PriceUnitField as OrderLinePriceUnit;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\ProductMediasField as OrderLineProductMedias;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\ProductSkuField as OrderLineProductSku;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\ProductTitleField as OrderLineProductTitle;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\QuantityField as OrderLineQuantity;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\ReceivedDateField as OrderLineReceivedDate;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\RefundsField as OrderLineRefunds;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\ShippedDateField as OrderLineShippedDate;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\ShippingTaxesField as OrderLineShippingTaxes;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\TaxesField as OrderLineTaxes;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\CreatedDateField as OrderLineCreatedDate;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\LastUpdatedDateField as OrderLineLastUpdatedDate;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\PriceField as OrderLinePrice;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\PromotionsField as OrderLinePromotions;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\ShippingFromField as OrderLineShippingFrom;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\ShippingPriceField as OrderLineShippingPrice;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\TotalCommissionField as OrderLineTotalCommission;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\TotalPriceField as OrderLineTotalPrice;
use Scaledev\MiraklPhpConnector\Validator\RequiredValidator;
use Scaledev\MiraklPhpConnector\Validator\Type\ArrayTypeValidator;

/**
 * Class OrderLinesField
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class OrderLinesField extends AbstractField
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
    const TYPE = OrderLineCollection::class;

    /**
     * @inheritdoc
     */
    const CHILD_FIELD = array(
        OrderLineCancelations::class,
        OrderLineCanRefund::class,
        OrderLineCategoryCode::class,
        OrderLineCategoryLabel::class,
        OrderLineCommissionFee::class,
        OrderLineCommissionTaxes::class,
        OrderLineCreatedDate::class,
        OrderLineDebitedDate::class,
        OrderLineDescription::class,
        OrderLineLastUpdatedDate::class,
        OrderLineMeasurement::class,
        OrderLineOfferId::class,
        OrderLineOfferSku::class,
        OrderLineOfferStateCode::class,
        OrderLineOrderLineAdditionalFields::class,
        OrderLineOrderLineId::class,
        OrderLineOrderLineIndex::class,
        OrderLineOrderLineState::class,
        OrderLineOrderLineStateReasonCode::class,
        OrderLineOrderLineStateReasonLabel::class,
        OrderLinePriceAdditionalInfo::class,
        OrderLinePriceAmountBreakdown::class,
        OrderLinePrice::class,
        OrderLinePriceUnit::class,
        OrderLineProductMedias::class,
        OrderLineProductSku::class,
        OrderLineProductTitle::class,
        OrderLinePromotions::class,
        OrderLineQuantity::class,
        OrderLineReceivedDate::class,
        OrderLineRefunds::class,
        OrderLineShippedDate::class,
        OrderLineShippingFrom::class,
        OrderLineShippingPrice::class,
        OrderLineShippingTaxes::class,
        OrderLineTaxes::class,
        OrderLineTotalCommission::class,
        OrderLineTotalPrice::class,
    );
}
