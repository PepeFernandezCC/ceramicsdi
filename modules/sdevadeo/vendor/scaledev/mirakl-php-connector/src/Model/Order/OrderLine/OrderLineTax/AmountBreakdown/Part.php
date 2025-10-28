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

namespace Scaledev\MiraklPhpConnector\Model\Order\OrderLine\OrderLineTax;

use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;

/**
 * Class CancelationsAmoutBreakdown
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class Part extends AbstractModel
{
    /**
     * Part's amount. The sum of each part's amount is equal to the total amount.
     *
     * @var number
     */
    private $amount;

    /**
     * Should this amount be applied to the sellers commissions calculation.
     *
     * @var boolean
     */
    private $commissionable;

    /**
     * Should this amount be debited to the customer.
     * If true, the amount is taken into account when generating the debit file.
     * If false, the amount is not taken into account when generating the debit file.
     *
     * @var boolean
     */
    private $debitable_from_customer;

    /**
     * Should this amount be paid to the shop.
     * If true, the amount is taken into account when generating the shop payment voucher.
     * If false, the amount is not taken into account when generating the shop payment voucher.
     *
     * @var boolean
     */
    private $payable_to_shop;

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
     * @return bool
     */
    public function isCommissionable()
    {
        return $this->commissionable;
    }

    /**
     * @param bool $commissionable
     * @return $this
     */
    public function setCommissionable($commissionable)
    {
        $this->commissionable = $commissionable;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDebitableFromCustomer()
    {
        return $this->debitable_from_customer;
    }

    /**
     * @param bool $debitable_from_customer
     * @return $this
     */
    public function setDebitableFromCustomer($debitable_from_customer)
    {
        $this->debitable_from_customer = $debitable_from_customer;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPayableToShop()
    {
        return $this->payable_to_shop;
    }

    /**
     * @param bool $payable_to_shop
     * @return $this
     */
    public function setPayableToShop($payable_to_shop)
    {
        $this->payable_to_shop = $payable_to_shop;
        return $this;
    }
}
