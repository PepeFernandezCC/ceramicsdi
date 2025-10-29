<?php
/**
 * Copyright since 2022 Trusted shops
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to tech@202-ecommerce.com so we can send you a copy immediately.
 *
 * @author 202 ecommerce <tech@202-ecommerce.com>
 * @copyright 2022 Trusted shops
 * @license https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 *
 * This source file is loading the components connector.umd.js and eventsLib.js
 * (itself subject to the Trusted Shops EULA https://policies.etrusted.com/IE/en/plugin-licence.html)  to connect to Trusted Shops. For these components, you will find below a list of the open source libraries we use for our Services.
 * Please note that the following list may be subject to amendments and modifications, and does not thus claim (perpetual) exhaustiveness. You can always refer to the following website for up-to-date information on the open source software Trusted Shops uses:
 * https://policies.etrusted.com/IE/en/plugin-licence.html
 *
 * Name                Licence         Copyright Disclaimer
 * axios               MIT     Copyright (c) 2014-present (Matt Zabriskie)
 * babel               MIT     Copyright (c) 2014-present (Sebastian McKenzie and other Contributors)
 * follow-redirects    MIT     Copyright (c) 2014–present (Olivier Lalonde, James Talmage, Ruben Verborgh)
 * history             MIT     Copyright (c) 2016-2020 (React Training), Copyright (c) 2020-2021 (Remix Software)
 * hookform/resolvers  MIT     Copyright (c) 2019-present (Beier(Bill) Luo)
 * inherits            ISC     Copyright (c) 2011-2022 (Isaac Z. Schlueter)
 * js-tokens           MIT     Copyright (c) 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021 (Simon Lydell)
 * lodash              MIT     Copyright (c) (OpenJS Foundation and other contributors (https://openjsf.org/)
 * lodash-es           MIT     Copyright (c) (OpenJS Foundation and other contributors (https://openjsf.org/)
 * loose-envify        MIT     Copyright (c) 2015 (Andreas Suarez)
 * nanoclone           MIT     Copyright (c) 2017 (Anton Kosykh)
 * path                MIT     Copyright (c) (Joyent, Inc. and other Node contributors.)
 * preact              MIT     Copyright (c) 2015-present (Jason Miller)
 * preact-router       MIT     Copyright (c) 2015 (Jason Miller)
 * process             MIT     Copyright (c) 2013 (Roman Shtylman)
 * property-expr       MIT     Copyright (c) 2014 (Jason Quense)
 * react-hook-form     MIT     Copyright (c) 2019-present (Beier(Bill) Luo)
 * regenerator-runtime MIT     Copyright (c) 2014-present (Facebook, Inc.)
 * resolve-pathname    MIT     Copyright (c) 2016-2018 (Michael Jackson)
 * tiny-invariant      MIT     Copyright (c) 2019 (Alexander Reardon)
 * tiny-warning        MIT     Copyright (c) 2019 (Alexander Reardon)
 * toposort            MIT     Copyright (c) 2012 (Marcel Klehr)
 * types/lodash        MIT     (none)
 * util                MIT     Copyright (c) (Joyent, Inc. and other Node contributors)
 * value-equal         MIT     Copyright (c) 2016-2018 (Michael Jackson)
 * yup                 MIT     Copyright (c) 2014 (Jason Quense)
 * zustand             MIT     Copyright (c) 2019 (Paul Henschel)
 */

namespace TrustedshopsAddon\API;

if (!defined('_PS_VERSION_')) {
    exit;
}

use InvalidArgumentException;
use RuntimeException;
use TrustedshopsAddon\API\Exception\RequestException;
use TrustedshopsAddon\API\Logger\ApiLogger;
use TrustedshopsAddon\API\Request\AbstractRequest;
use TrustedshopsAddon\API\Response\AbstractResponse;
use TrustedshopsAddon\API\Response\DefaultResponse;
use TrustedshopsAddon\API\Response\ErrorResponse;
use TrustedshopsAddon\API\Response\ResponseBuilder;
use UnexpectedValueException;

/**
 * API client
 */
class Client
{
    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * cURL handler
     *
     * @var resource
     */
    protected $ch;

    /**
     * cURL options array
     *
     * @var array<mixed>
     */
    protected $options;

    /**
     * Maximum request body size
     *
     * @var int
     */
    protected static $MAX_BODY_SIZE;

    protected $lastRequest = null;

    /**
     * @var ApiLogger
     */
    protected $apiLogger;

    /**
     * Create new cURL http client object
     */
    public function __construct()
    {
        self::$MAX_BODY_SIZE = 1024 * 1024;
        $this->apiLogger = ApiLogger::getInstance();
    }

    /**
     * Set credentials
     *
     * @param string $clientId api client key
     * @param string $clientSecret api client secret
     *
     * @return static
     */
    public function setCredential($clientId, $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * Get Oauth token
     *
     * @return false|string
     */
    private function getToken()
    {
        $data['grant_type'] = 'client_credentials';
        $data['client_id'] = $this->clientId;
        $data['client_secret'] = $this->clientSecret;
        $data['audience'] = 'https://' . TS_API_URL;

        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $headers[] = 'Accept-Encoding: gzip, deflate, br';
        $headers[] = 'Cache-control: no-cache';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, TS_API_TOKEN_URL);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);

        $result = curl_exec($ch);
        if (curl_errno($ch) !== 0) {
            $info = curl_getinfo($ch);

            return false;
        }
        curl_close($ch);
        $output = json_decode((string) $result, true);
        if (empty($output['access_token']) === true) {
            return false;
        }

        return $output['access_token'];
    }

    /**
     * Send a CURL Request
     *
     * @param AbstractRequest $request
     *
     * @return AbstractResponse
     *
     * @throws RequestException Invalid request
     * @throws InvalidArgumentException Invalid header names and/or values
     * @throws RuntimeException Failure to create stream
     */
    public function sendRequest(AbstractRequest $request)
    {
        $this->lastRequest = null;
        $token = $this->getToken();
        if ($token === false) {
            return new ErrorResponse(401);
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ];

        $this->apiLogger->logInfo('Request to : ' . $request->getUri());

        $request->setHeaders($headers);

        $ch = curl_init();

        $response = $this->createResponse($request);
        $options = $this->createOptions($request, $response);

        curl_setopt_array($ch, $options);

        // Execute the request
        $result = curl_exec($ch);

        $infos = curl_getinfo($ch);
        // Check for any request errors
        switch (curl_errno($ch)) {
            case CURLE_OK:
                break;
            case CURLE_COULDNT_RESOLVE_PROXY:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_COULDNT_CONNECT:
            case CURLE_OPERATION_TIMEOUTED:
            case CURLE_SSL_CONNECT_ERROR:
                throw new RequestException('curl error ' . curl_error($ch), $request);
            default:
                throw new RequestException('curl error: network error', $request);
        }
        curl_close($ch);

        // Get the response
        $response->getResponse()->setBody($result);

        $this->apiLogger->logInfo('Response : ' . $result);

        return $response->getResponse();
    }

    /**
     * Create a new http response
     *
     * @param AbstractRequest $request
     *
     * @return ResponseBuilder
     *
     * @throws RuntimeException Failure to create stream
     */
    protected function createResponse($request)
    {
        $responseObject = $request->getResponseObject();
        $message = DefaultResponse::getInstance($responseObject)->withBody(null);

        return new ResponseBuilder(
            $message
        );
    }

    /**
     * Create array of headers to pass to CURLOPT_HTTPHEADER
     *
     * @param AbstractRequest $request Request object
     * @param array<mixed> $options cURL options
     *
     * @return array<mixed> Array of http header lines
     */
    protected function createHeaders(AbstractRequest $request, array $options)
    {
        $headers = [];
        $requestHeaders = $request->getHeaders();

        foreach ($requestHeaders as $name => $values) {
            $header = strtoupper($name);

            // cURL does not support 'Expect-Continue', skip all 'EXPECT' headers
            if ($header === 'EXPECT') {
                continue;
            }

            if ($header === 'CONTENT-LENGTH') {
                if (array_key_exists(CURLOPT_POSTFIELDS, $options)) {
                    $values = [strlen($options[CURLOPT_POSTFIELDS])];
                } // Force content length to '0' if body is empty
                elseif (!array_key_exists(CURLOPT_READFUNCTION, $options)) {
                    $values = [0];
                }
            }

            foreach ($values as $value) {
                $headers[] = $name . ': ' . $value;
            }
        }

        // Although cURL does not support 'Expect-Continue', it adds the 'Expect'
        // header by default, so we need to force 'Expect' to empty.
        $headers[] = 'Expect:';

        return $headers;
    }

    /**
     * Create cURL request options
     *
     * @param AbstractRequest $request
     * @param ResponseBuilder $response
     *
     * @return array<mixed> cURL options
     *
     * @throws RequestException Invalid request
     * @throws InvalidArgumentException Invalid header names and/or values
     * @throws RuntimeException Unable to read request body
     */
    protected function createOptions(AbstractRequest $request, ResponseBuilder $response)
    {
        $options = $this->options;

        // These options default to false and cannot be changed on set up.
        // The options should be provided with the request instead.
        $options[CURLOPT_FOLLOWLOCATION] = false;
        $options[CURLOPT_HEADER] = false;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_SSLVERSION] = CURL_SSLVERSION_TLSv1_2;
        $options[CURLOPT_URL] = (string) $request->getUri();
        $options[CURLOPT_HTTPHEADER] = $this->createHeaders($request, $options);
        $options[CURLOPT_URL] = (string) $request->getUri();
        $options[CURLOPT_POSTFIELDS] = (string) $request->getBody();

        if ($request->getUri()->getUserInfo()) {
            $options[CURLOPT_USERPWD] = $request->getUri()->getUserInfo();
        }

        return $options;
    }

    /**
     * Get cURL constant for request http protocol version
     *
     * @param string $requestProtocolVersion Request http protocol version
     *
     * @return int cURL constant for request http protocol version
     *
     * @throws UnexpectedValueException Unsupported cURL http protocol version
     */
    protected function getProtocolVersion($requestProtocolVersion)
    {
        switch ($requestProtocolVersion) {
            case '1.0':
                return CURL_HTTP_VERSION_1_0;
            case '1.1':
                return CURL_HTTP_VERSION_1_1;
            case '2.0':
                if (defined('CURL_HTTP_VERSION_2_0')) {
                    return CURL_HTTP_VERSION_2_0;
                }

                throw new UnexpectedValueException('libcurl 7.33 required for HTTP 2.0');
        }

        return CURL_HTTP_VERSION_NONE;
    }
}
