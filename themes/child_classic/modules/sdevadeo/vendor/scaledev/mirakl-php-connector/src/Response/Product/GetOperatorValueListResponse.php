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

namespace Scaledev\MiraklPhpConnector\Response\Product;

use Scaledev\MiraklPhpConnector\Builder\Product\GetOperatorValueListBuilder;
use Scaledev\MiraklPhpConnector\Collection\OperatorValueCollection;
use Scaledev\MiraklPhpConnector\Core\Response\AbstractResponse;

/**
 * Class GetOperatorValueListResponse
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class GetOperatorValueListResponse extends AbstractResponse
{
    /**
     * Defines the builder class to use.
     */
    const BUILDER_CLASS = GetOperatorValueListBuilder::class;

    /**
     * @var OperatorValueCollection
     */
    private $operatorValueCollection;

    /**
     * @var array
     */
    private $operatorValueList;

    public function getOperatorValueListLength()
    {
        return count($this->operatorValueList);
    }

    /**
     * @return array
     */
    public function getOperatorValueList($offset, $limit)
    {
        return array_slice($this->operatorValueList, $offset, $limit);
    }

    /**
     * @param array $operatorValueList
     * @return $this
     */
    public function setOperatorValueList($operatorValueList)
    {
        $this->operatorValueList = $operatorValueList;
        return $this;
    }

    /**
     * @return OperatorValueCollection
     */
    public function getOperatorValueCollection()
    {
        return $this->operatorValueCollection;
    }

    /**
     * @param OperatorValueCollection $operatorValueCollection
     * @return $this
     */
    public function setOperatorValueCollection($operatorValueCollection)
    {
        $this->operatorValueCollection = $operatorValueCollection;
        return $this;
    }

    /**
     * @param array $resultRequest
     * @return $this
     */
    public function __construct($resultRequest)
    {
        $this->setOperatorValueList($resultRequest);
        return $this;
    }
}
