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

use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;

/**
 * Class Measurement
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class Measurement extends AbstractModel
{
    /**
     * @var double
     */
    private $actual_measurement;

    /**
     * @var double
     */
    private $adjustement_limit;

    /**
     * @var string
     */
    private $measurement_unit;

    /**
     * @var double
     */
    private $ordered_measurement;

    /**
     * @return float
     */
    public function getActualMeasurement()
    {
        return $this->actual_measurement;
    }

    /**
     * @param float $actual_measurement
     * @return $this
     */
    public function setActualMeasurement($actual_measurement)
    {
        $this->actual_measurement = $actual_measurement;
        return $this;
    }

    /**
     * @return float
     */
    public function getAdjustementLimit()
    {
        return $this->adjustement_limit;
    }

    /**
     * @param float $adjustement_limit
     * @return $this
     */
    public function setAdjustementLimit($adjustement_limit)
    {
        $this->adjustement_limit = $adjustement_limit;
        return $this;
    }

    /**
     * @return string
     */
    public function getMeasurementUnit()
    {
        return $this->measurement_unit;
    }

    /**
     * @param string $measurement_unit
     * @return $this
     */
    public function setMeasurementUnit($measurement_unit)
    {
        $this->measurement_unit = $measurement_unit;
        return $this;
    }

    /**
     * @return float
     */
    public function getOrderedMeasurement()
    {
        return $this->ordered_measurement;
    }

    /**
     * @param float $ordered_measurement
     * @return $this
     */
    public function setOrderedMeasurement($ordered_measurement)
    {
        $this->ordered_measurement = $ordered_measurement;
        return $this;
    }
}
