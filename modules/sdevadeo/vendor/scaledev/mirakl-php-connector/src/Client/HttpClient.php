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

namespace Scaledev\MiraklPhpConnector\Client;

use Scaledev\MiraklPhpConnector\Core\HttpClient\HttpClientInterface;

/**
 * Class HttpClient
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
class HttpClient implements HttpClientInterface
{
    private $url = '';
    private $headers = array();
    private $method ='';
    private $result = '';
    private $postFields = array();
    private $putFields = array();
    private $httpCode;

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Return the result of the request as string
     *
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return array
     */
    public function getPostFields()
    {
        return $this->postFields;
    }

    /**
     * @param array $postFields
     * @return $this
     */
    public function setPostFields($postFields)
    {
        $this->postFields = $postFields;
        return $this;
    }

    /**
     * @param mixed $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
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
     * @return mixed
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @param mixed $httpCode
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
    public function executeRequest()
    {
        $client = curl_init($this->url);

        curl_setopt_array($client, array(
            CURLOPT_HTTPHEADER => $this->headers,
            CURLOPT_RETURNTRANSFER => true,
        ));

        switch ($this->method) {
            case 'POST':
                curl_setopt($client, CURLOPT_POST, true);
                break;
            case 'DELETE':
                curl_setopt($client, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case 'PUT':
                curl_setopt($client, CURLOPT_CUSTOMREQUEST, 'PUT');
                break;
        }

        if ($this->method != 'GET') {
            if (!empty($this->postFields)) {
                curl_setopt($client, CURLOPT_POSTFIELDS, $this->postFields);
            } else if (!empty($this->putFields)) {
                curl_setopt($client, CURLOPT_POSTFIELDS, json_encode($this->putFields));
            } else {
                curl_setopt($client, CURLOPT_POSTFIELDS, json_encode(array()));
            }
        }

        $this->result = curl_exec($client);

        if (!curl_errno($client)) {
            $this->setHttpCode(curl_getinfo($client, CURLINFO_HTTP_CODE));
        }

        curl_close($client);

        return $this;
    }
}
