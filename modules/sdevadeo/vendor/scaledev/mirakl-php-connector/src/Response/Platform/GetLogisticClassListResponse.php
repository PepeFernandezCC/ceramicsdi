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

namespace Scaledev\MiraklPhpConnector\Response\Platform;

use Scaledev\MiraklPhpConnector\Builder\Platform\GetLogisticClassBuilder;
use Scaledev\MiraklPhpConnector\Collection\LogisticClassCollection;
use \Scaledev\MiraklPhpConnector\Core\Response\AbstractResponse;

/**
 * Class GetLogisticClassListResponse
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class GetLogisticClassListResponse extends AbstractResponse
{
    /**
     * Defines the builder class to use.
     */
    const BUILDER_CLASS = GetLogisticClassBuilder::class;

    /**
     * @var logisticClassCollection
     */
    private $logisticClassCollection;

    /**
     * @param array $resultRequest
     * @return $this
     */
    public function __construct($resultRequest)
    {
        $builder = self::BUILDER_CLASS;
        $this->setLogisticClassCollection(
            (new $builder(
                $resultRequest
            ))->getBuilt()
        );

        return $this;
    }

    /**
     * @return LogisticClassCollection
     */
    public function getLogisticClassCollection()
    {
        return $this->logisticClassCollection;
    }

    /**
     * @param LogisticClassCollection $logisticClassCollection
     * @return $this
     */
    public function setLogisticClassCollection($logisticClassCollection)
    {
        $this->logisticClassCollection = $logisticClassCollection;
        return $this;
    }
}
