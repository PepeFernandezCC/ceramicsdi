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

use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;

/**
 * Class ShippingMethod
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class ShippingMethod extends AbstractModel
{
    /**
     * Shipping type corresponds to “click&collect”
     * API data: click_and_collect
     *
     * @var boolean
     */
    private $click_and_collect;

    /**
     * Shipping type code
     * API data: code
     *
     * @var string
     */
    private $code;

    /**
     * Shipping type is managed by operator on behalf of shops
     * API data: delivery_by_operator
     *
     * @var boolean
     */
    private $delivery_by_operator;

    /**
     * Shipping type description
     * API data: description
     *
     * @var string
     */
    private $description;

    /**
     * Shipping type label
     * API data: label
     *
     * @var string
     */
    private $label;

    /**
     * Shops must provide tracking details before confirming shipment for a given shipping type
     * API data: mandatory_tracking
     *
     * @var boolean
     */
    private $mandatory_tracking;

    /**
     * @return bool
     */
    public function isClickAndCollect()
    {
        return $this->click_and_collect;
    }

    /**
     * @param bool $click_and_collect
     * @return $this
     */
    public function setClickAndCollect($click_and_collect)
    {
        $this->click_and_collect = $click_and_collect;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDeliveryByOperator()
    {
        return $this->delivery_by_operator;
    }

    /**
     * @param bool $delivery_by_operator
     * @return $this
     */
    public function setDeliveryByOperator($delivery_by_operator)
    {
        $this->delivery_by_operator = $delivery_by_operator;
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
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMandatoryTracking()
    {
        return $this->mandatory_tracking;
    }

    /**
     * @param bool $mandatory_tracking
     * @return $this
     */
    public function setMandatoryTracking($mandatory_tracking)
    {
        $this->mandatory_tracking = $mandatory_tracking;
        return $this;
    }
}
