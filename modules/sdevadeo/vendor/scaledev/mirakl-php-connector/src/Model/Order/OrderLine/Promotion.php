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
use Scaledev\MiraklPhpConnector\Model\Order\OrderLine\Promotion\Configuration;

/**
 * Class Promotion
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class Promotion extends AbstractModel
{
    /**
     * @var boolean
     */
    private $apportioned;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var number
     */
    private $deduced_amount;

    /**
     * @var string
     */
    private $id;

    /**
     * @var integer
     */
    private $offered_quantity;

    /**
     * @return bool
     */
    public function isApportioned()
    {
        return $this->apportioned;
    }

    /**
     * @param bool $apportioned
     * @return $this
     */
    public function setApportioned($apportioned)
    {
        $this->apportioned = $apportioned;
        return $this;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param Configuration $configuration
     * @return $this
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * @return number
     */
    public function getDeducedAmount()
    {
        return $this->deduced_amount;
    }

    /**
     * @param number $deduced_amount
     * @return $this
     */
    public function setDeducedAmount($deduced_amount)
    {
        $this->deduced_amount = $deduced_amount;
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
    public function getOfferedQuantity()
    {
        return $this->offered_quantity;
    }

    /**
     * @param int $offered_quantity
     * @return $this
     */
    public function setOfferedQuantity($offered_quantity)
    {
        $this->offered_quantity = $offered_quantity;
        return $this;
    }
}
