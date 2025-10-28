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

namespace Scaledev\MiraklPhpConnector\Model\Order\Promotion\AppliedPromotion;

use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;

/**
 * Class Configuration
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class Configuration extends AbstractModel
{
    /**
     * @var number
     */
    private $amount_off;

    /**
     * @var integer
     */
    private $free_items_quantity;

    /**
     * @var string
     */
    private $internal_description;

    /**
     * @var number
     */
    private $percentage_off;

    /**
     * @var string
     */
    private $type;

    /**
     * @return number
     */
    public function getAmountOff()
    {
        return $this->amount_off;
    }

    /**
     * @param number $amount_off
     * @return $this
     */
    public function setAmountOff($amount_off)
    {
        $this->amount_off = $amount_off;
        return $this;
    }

    /**
     * @return int
     */
    public function getFreeItemsQuantity()
    {
        return $this->free_items_quantity;
    }

    /**
     * @param int $free_items_quantity
     * @return $this
     */
    public function setFreeItemsQuantity($free_items_quantity)
    {
        $this->free_items_quantity = $free_items_quantity;
        return $this;
    }

    /**
     * @return string
     */
    public function getInternalDescription()
    {
        return $this->internal_description;
    }

    /**
     * @param string $internal_description
     * @return $this
     */
    public function setInternalDescription($internal_description)
    {
        $this->internal_description = $internal_description;
        return $this;
    }

    /**
     * @return number
     */
    public function getPercentageOff()
    {
        return $this->percentage_off;
    }

    /**
     * @param number $percentage_off
     * @return $this
     */
    public function setPercentageOff($percentage_off)
    {
        $this->percentage_off = $percentage_off;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}
