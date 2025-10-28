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

namespace Scaledev\MiraklPhpConnector\Core\Request;

use Scaledev\Adeo\Core\Component\Logger;
use Scaledev\Adeo\Core\Module;
use Scaledev\MiraklPhpConnector\Api;
use Scaledev\MiraklPhpConnector\Core\Response\ResponseInterface;
use Scaledev\MiraklPhpConnector\Exception\BadRequestException;
use Scaledev\MiraklPhpConnector\Exception\ForbiddenException;
use Scaledev\MiraklPhpConnector\Exception\GoneException;
use Scaledev\MiraklPhpConnector\Exception\InternalServerErrorException;
use Scaledev\MiraklPhpConnector\Exception\MethodNotAllowedException;
use Scaledev\MiraklPhpConnector\Exception\NotAcceptableException;
use Scaledev\MiraklPhpConnector\Exception\NotFoundException;
use Scaledev\MiraklPhpConnector\Exception\TooManyRequestsException;
use Scaledev\MiraklPhpConnector\Exception\UnauthorizedException;
use Scaledev\MiraklPhpConnector\Exception\UnsupportedMediaTypeException;
use Scaledev\MiraklPhpConnector\Client\HttpClient;

/**
 * Class AbstractRequest
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Pascal Fischer <contact@scaledev.fr>
 */
abstract class AbstractRequest implements RequestInterface
{
    /**
     * Defines the response class to use.
     */
    const RESPONSE_CLASS = null;

    /**
     * Defines the method to use for the API call.
     */
    const METHOD = null;

    /**
     * Response of the request
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * API return of the request
     *
     * @var string
     */
    protected $clientResult;

    /**
     * Parameter to pass to GET request
     *
     * @var string|array
     */
    protected $requestParameter = null;

    /** @var array */
    protected $putFields = array();

    /** @var int */
    protected $httpCode;

    /**
     * @return string
     */
    public function getRequestParameter()
    {
        return $this->requestParameter;
    }

    /**
     * @param string $requestParameter
     * @return $this
     */
    public function setRequestParameter($requestParameter)
    {
        $this->requestParameter = $requestParameter;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     * @return $this
     */
    protected function setResponse(ResponseInterface $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientResult()
    {
        return $this->clientResult;
    }

    /**
     * @param string $clientResult
     * @return $this
     */
    public function setClientResult($clientResult)
    {
        $this->clientResult = $clientResult;
        return $this;
    }

    /**
     * @return array
     */
    public function getPutFields()
    {
        return $this->putFields;
    }

    /**
     * @param array $putFields
     * @return $this
     */
    public function setPutFields($putFields)
    {
        $this->putFields = $putFields;
        return $this;
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @param int $httpCode
     * @return $this
     */
    public function setHttpCode($httpCode)
    {
        $this->httpCode = $httpCode;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function execute($apiKey, $isTestMode = false, $queryParameter = array())
    {
        $getParameter = array();
        if ($this->requestParameter != null) {
            $getParameter['placeholder'] = $this->requestParameter;
        }
        if (!empty($queryParameter)) {
            foreach ($queryParameter as $key => $value) {
                $getParameter[$key] = $value;
            }
        }

        $this
            ->callToApi($apiKey, $isTestMode, $getParameter)
            ->checkStatus()
            ->initResponse();

        return $this;
    }

    /**
     * Build the client, call the API and stock the result.
     *
     * @param string $apiKey
     * @param bool $isTestMode
     * @param array $postFields
     * @return $this
     */
    protected function callToApi($apiKey, $isTestMode = false, $getParameter = null, $postFields = array())
    {
        $client = (new HttpClient())
            ->setMethod(static::METHOD)
            ->setUrl(Api::getUrl(static::class, $isTestMode, $getParameter))
            ->setHeaders(self::buildHeader($apiKey, $postFields))
            ->setPostFields($postFields)
            ->setPutFields($this->putFields)
            ->executeRequest()
        ;

        $this->setClientResult(
            $client->getResult()
        );

        $this->setHttpCode(
            $client->getHttpCode()
        );

        return $this;
    }

    private static function buildHeader($apiKey, $postFields = [])
    {
        $header = [
            "Authorization: $apiKey",
            "Accept: application/json"
        ];
        $header[] = empty($postFields)
            ? "Content-Type: application/json"
            : "Content-Type: multipart/form-data";
        return $header;
    }

    /**
     * @return $this
     */
    abstract protected function initResponse();

    /**
     * @throws TooManyRequestsException
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @throws BadRequestException
     * @throws NotAcceptableException
     * @throws GoneException
     * @throws InternalServerErrorException
     * @throws UnauthorizedException
     * @throws UnsupportedMediaTypeException
     * @throws ForbiddenException
     */
    private function checkStatus()
    {
        $module = \Module::getInstanceByName(Module::NAME);

        if ($this->httpCode < 300) {
            return $this;
        }

        (new Logger('api_error'))->addLog([$this->requestParameter, $this->putFields]);

        switch ($this->httpCode) {
            case BadRequestException::CODE:
                throw new BadRequestException($module->l('Parameter errors or bad method usage.'));

            case ForbiddenException::CODE:
                throw new ForbiddenException($module->l('Access to the resource is denied.'));

            case GoneException::CODE:
                throw new GoneException($module->l('The resource is permanently gone.'));

            case InternalServerErrorException::CODE:
                throw new InternalServerErrorException($module->l('The server encountered an unexpected error.'));

            case MethodNotAllowedException::CODE:
                throw new MethodNotAllowedException($module->l('The HTTP method is not allowed for this resource.'));

            case NotAcceptableException::CODE:
                throw new NotAcceptableException($module->l('The requested response content type is not available for this resource.'));

            case NotFoundException::CODE:
                throw new NotFoundException($module->l('The resource does not exist.'));

            case TooManyRequestsException::CODE:
                throw new TooManyRequestsException($module->l('Rate limits are exceeded.'));

            case UnauthorizedException::CODE:
                throw new UnauthorizedException($module->l('API call without authentication.'));

            case UnsupportedMediaTypeException::CODE:
                throw new UnsupportedMediaTypeException($module->l('The entity content type sent to the server is not supported.'));

            default:
                return $this;
        }
    }
}
