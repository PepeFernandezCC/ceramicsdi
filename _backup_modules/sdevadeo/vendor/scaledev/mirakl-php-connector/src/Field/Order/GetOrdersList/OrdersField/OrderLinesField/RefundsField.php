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

namespace Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField;

use Scaledev\MiraklPhpConnector\Collection\Component\Order\OrderLine\RefundCollection;
use Scaledev\MiraklPhpConnector\Core\Field\AbstractField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\RefundField\AmountBreakdownField as RefundAmountBreakdown;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\RefundField\AmountField as RefundAmount;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\RefundField\CommissionAmountField as RefundCommissionAmount;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\RefundField\CommissionTaxesField as RefundCommissionTaxes;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\RefundField\CommissionTotalAmountField as RefundCommissionTotalAmount;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\RefundField\CreatedDateField as RefundCreatedDate;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\RefundField\IdField as RefundId;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\RefundField\ReasonCodeField as RefundReasonCode;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\RefundField\ShippingAmountBreakdownField as RefundShippingAmountBreakdown;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\RefundField\ShippingAmountField as RefundShippingAmount;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\RefundField\ShippingTaxesField as RefundShippingTaxes;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\RefundField\StateField as RefundState;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\RefundField\TaxesField as RefundTaxes;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\RefundField\TransactionDateField as RefundTransactionDate;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\RefundField\TransactionNumberField as RefundTransactionNumber;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\RefundField\QuantityField as RefundQuantity;
use Scaledev\MiraklPhpConnector\Validator\RequiredValidator;
use Scaledev\MiraklPhpConnector\Validator\Type\ArrayTypeValidator;

/**
 * Class RefundsField
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class RefundsField extends AbstractField
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
    const TYPE = RefundCollection::class;

    /**
     * @inheritdoc
     */
    const CHILD_FIELD = array(
        RefundAmountBreakdown::class,
        RefundAmount::class,
        RefundCommissionAmount::class,
        RefundCommissionTaxes::class,
        RefundCommissionTotalAmount::class,
        RefundCreatedDate::class,
        RefundId::class,
        RefundQuantity::class,
        RefundReasonCode::class,
        RefundShippingAmountBreakdown::class,
        RefundShippingAmount::class,
        RefundShippingTaxes::class,
        RefundState::class,
        RefundTaxes::class,
        RefundTransactionDate::class,
        RefundTransactionNumber::class
    );
}
