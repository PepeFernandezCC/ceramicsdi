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

/**
 * Class Shipping
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class Shipping extends AbstractModel
{
    /**
     * List of custom shipping fields for this shipping zone and shipping method
     * API data: additional_fields
     *
     * @var AdditionalFieldCollection
     */
    private $additional_fields;

    /**
     * Minimal amount for free shipping
     * API data: free_amount
     *
     * @var number
     */
    private $free_amount;

    /**
     * The code of the shipping type
     * API data: type_code
     *
     * @required
     * @var string
     */
    private $type_code;

    /**
     * The label of the shipping type
     * API data: type_label
     *
     * @required
     * @var string
     */
    private $type_label;

    /**
     * The code of the shipping zone
     * API data: zone_code
     *
     * @required
     * @var string
     */
    private $zone_code;

    /**
     * The label of the shipping zone
     * API data: zone_label
     *
     * @required
     * @var string
     */
    private $zone_label;

    /**
     * @return AdditionalFieldCollection
     */
    public function getAdditionalFields()
    {
        return $this->additional_fields;
    }

    /**
     * @param AdditionalFieldCollection $additional_fields
     * @return $this
     */
    public function setAdditionalFields($additional_fields)
    {
        $this->additional_fields = $additional_fields;
        return $this;
    }

    /**
     * @return number
     */
    public function getFreeAmount()
    {
        return $this->free_amount;
    }

    /**
     * @param number $free_amount
     * @return $this
     */
    public function setFreeAmount($free_amount)
    {
        $this->free_amount = $free_amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingTypeCode()
    {
        return $this->type_code;
    }

    /**
     * @param string $type_code
     * @return $this
     */
    public function setShippingTypeCode($type_code)
    {
        $this->type_code = $type_code;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingTypeLabel()
    {
        return $this->type_label;
    }

    /**
     * @param string $type_label
     * @return $this
     */
    public function setShippingTypeLabel($type_label)
    {
        $this->type_label = $type_label;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingZoneCode()
    {
        return $this->zone_code;
    }

    /**
     * @param string $zone_code
     * @return $this
     */
    public function setShippingZoneCode($zone_code)
    {
        $this->zone_code = $zone_code;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingZoneLabel()
    {
        return $this->zone_label;
    }

    /**
     * @param string $zone_label
     * @return $this
     */
    public function setShippingZoneLabel($zone_label)
    {
        $this->zone_label = $zone_label;
        return $this;
    }
}
