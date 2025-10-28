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

namespace Scaledev\MiraklPhpConnector\Model\Order\OrderLine;

use Scaledev\MiraklPhpConnector\Collection\Component\Order\OrderLine\CancelationCollection\CommissionTaxCollection;
use Scaledev\MiraklPhpConnector\Collection\Component\Order\OrderLine\CancelationCollection\CancelationTaxCollection;
use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;
use Scaledev\MiraklPhpConnector\Model\Order\OrderLine\Cancelation\AmountBreakdown;

/**
 * Class OrderLinesCancelations
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class Cancelation extends AbstractModel
{
    /**
     * Cancellation's amount
     *
     * @var number
     */
    private $amount;

    /**
     * The breakdown of the cancellation's amount, only available when advanced features are enabled
     *
     * @var AmountBreakdown
     */
    private $amount_breakdown;

    /**
     * Cancellation's commission amount
     *
     * @var number
     */
    private $commission_amount;

    /**
     * Cancellation's commission taxes
     *
     * @var CommissionTaxCollection
     */
    private $commission_taxes;

    /**
     * The total commission amount of the cancellation (commission amount + commission taxes)
     *
     * @var number
     */
    private $commission_total_amount;

    /**
     * Cancellation's creation date
     *
     * @var string
     */
    private $created_date;

    /**
     * Cancellation's ID
     *
     * @var string
     */
    private $id;

    /**
     * The quantity of products canceled (This quantity is informative only)
     *
     * @var integer
     */
    private $quantity;

    /**
     * Cancellation reason's code
     *
     * @var string
     */
    private $reason_code;

    /**
     * Cancellation's shipping amount
     *
     * @var number
     */
    private $shipping_amount;

    /**
     * The breakdown of the cancellation's shipping amount, only available when advanced features are enabled
     *
     * @var AmountBreakdown
     */
    private $shipping_amount_breakdown;

    /**
     * The taxes on the shipping price
     *
     * @var CancelationTaxCollection
     */
    private $shipping_taxes;

    /**
     * The taxes on the price
     *
     * @var CancelationTaxCollection
     */
    private $taxes;

    /**
     * @return number
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param number $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return AmountBreakdown
     */
    public function getAmountBreakdown()
    {
        return $this->amount_breakdown;
    }

    /**
     * @param AmountBreakdown $amount_breakdown
     * @return $this
     */
    public function setAmountBreakdown($amount_breakdown)
    {
        $this->amount_breakdown = $amount_breakdown;
        return $this;
    }

    /**
     * @return number
     */
    public function getCommissionAmount()
    {
        return $this->commission_amount;
    }

    /**
     * @param number $commission_amount
     * @return $this
     */
    public function setCommissionAmount($commission_amount)
    {
        $this->commission_amount = $commission_amount;
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
     * @return number
     */
    public function getCommissionTotalAmount()
    {
        return $this->commission_total_amount;
    }

    /**
     * @param number $commission_total_amount
     * @return $this
     */
    public function setCommissionTotalAmount($commission_total_amount)
    {
        $this->commission_total_amount = $commission_total_amount;
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getReasonCode()
    {
        return $this->reason_code;
    }

    /**
     * @param string $reason_code
     * @return $this
     */
    public function setReasonCode($reason_code)
    {
        $this->reason_code = $reason_code;
        return $this;
    }

    /**
     * @return number
     */
    public function getShippingAmount()
    {
        return $this->shipping_amount;
    }

    /**
     * @param number $shipping_amount
     * @return $this
     */
    public function setShippingAmount($shipping_amount)
    {
        $this->shipping_amount = $shipping_amount;
        return $this;
    }

    /**
     * @return AmountBreakdown
     */
    public function getShippingAmountBreakdown()
    {
        return $this->shipping_amount_breakdown;
    }

    /**
     * @param AmountBreakdown $shipping_amount_breakdown
     * @return $this
     */
    public function setShippingAmountBreakdown($shipping_amount_breakdown)
    {
        $this->shipping_amount_breakdown = $shipping_amount_breakdown;
        return $this;
    }

    /**
     * @return CancelationTaxCollection
     */
    public function getShippingTaxes()
    {
        return $this->shipping_taxes;
    }

    /**
     * @param CancelationTaxCollection $shipping_taxes
     * @return $this
     */
    public function setShippingTaxes($shipping_taxes)
    {
        $this->shipping_taxes = $shipping_taxes;
        return $this;
    }

    /**
     * @return CancelationTaxCollection
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    /**
     * @param CancelationTaxCollection $taxes
     * @return $this
     */
    public function setTaxes($taxes)
    {
        $this->taxes = $taxes;
        return $this;
    }
}
