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

namespace Scaledev\MiraklPhpConnector\Request\Platform;

use Scaledev\MiraklPhpConnector\Core\Request\AbstractRequest;
use Scaledev\MiraklPhpConnector\Response\Platform\GetLogisticClassListResponse;

/**
 * Class GetLogisticClassListRequest
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class GetLogisticClassListRequest extends AbstractRequest
{
    /**
     * Defines the response class to use.
     */
    const RESPONSE_CLASS = GetLogisticClassListResponse::class;

    /**
     * Defines the method to use for the API call.
     */
    const METHOD = 'GET';

    /**
     * Initialization of the Response object
     *
     * @return $this
     */
    protected function initResponse()
    {
        $responseClassName = self::RESPONSE_CLASS;
        $this->setResponse(
            new $responseClassName(
                json_decode($this->clientResult,1)['logistic_classes']
            )
        );

        return $this;
    }
}
