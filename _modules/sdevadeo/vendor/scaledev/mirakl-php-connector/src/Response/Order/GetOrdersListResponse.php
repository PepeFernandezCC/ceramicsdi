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

namespace Scaledev\MiraklPhpConnector\Response\Order;

use Scaledev\MiraklPhpConnector\Builder\Order\GetOrdersListBuilder;
use Scaledev\MiraklPhpConnector\Collection\OrderCollection;
use Scaledev\MiraklPhpConnector\Core\Response\AbstractResponse;

/**
 * Class GetOrdersListResponse
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class GetOrdersListResponse extends AbstractResponse
{
    /**
     * Defines the request class to use.
     */
    const BUILDER_CLASS = GetOrdersListBuilder::class;

    /**
     * Total number of orders
     *
     * @var integer $total_count
     */
    private $total_count;

    /**
     * List of the orders
     *
     * @var OrderCollection $orderCollection
     */
    private $orderCollection;

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->total_count;
    }

    /**
     * @param int $total_count
     * @return $this
     */
    public function setTotalCount($total_count)
    {
        $this->total_count = $total_count;
        return $this;
    }

    /**
     * @return OrderCollection
     */
    public function getOrderCollection()
    {
        return $this->orderCollection;
    }

    /**
     * @param OrderCollection $orderCollection
     * @return $this
     */
    public function setOrderCollection($orderCollection)
    {
        $this->orderCollection = $orderCollection;
        return $this;
    }

    /**
     * @param array $resultRequest
     * @return $this
     */
    public function __construct($resultRequest)
    {
        $builder = self::BUILDER_CLASS;
        $this->setTotalCount($resultRequest['total_count']);
        $this->setOrderCollection(
            (new $builder(
                $resultRequest['orders']
            ))->getBuilt()
        );

        return $this;
    }
}
