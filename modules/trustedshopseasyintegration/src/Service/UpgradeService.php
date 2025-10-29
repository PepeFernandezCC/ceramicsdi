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

namespace TrustedshopsAddon\Service;

if (!defined('_PS_VERSION_')) {
    exit;
}

use TrustedshopsAddon\API\Exception\RequestException;
use TrustedshopsAddon\API\Logger\ApiLogger;
use TrustedshopsAddon\API\Request\AbstractRequest;
use TrustedshopsAddon\API\Request\CustomRequest;
use TrustedshopsAddon\API\Response\AbstractResponse;
use TrustedshopsAddon\API\Response\ArrayResponse;
use TrustedshopsAddon\API\Response\DefaultResponse;
use TrustedshopsAddon\API\Response\ResponseBuilder;
use TrustedshopsAddon\Model\OrderStatusEvents\OrderStatusEventsModel;
use TrustedshopsAddon\Utils\ServiceContainer;
use Trustedshopseasyintegration;

class UpgradeService
{
    /**
     * @var ChannelService
     */
    protected $channelService;

    /**
     * @var CredentialsService
     */
    protected $credentialsService;

    /**
     * @var ApiLogger
     */
    protected $apiLogger;

    /**
     * @var bool|int
     */
    protected $idEvent = false;

    /**
     * @var bool
     */
    protected $orderShippedFound;

    /**
     * @var bool
     */
    protected $orderShippedActive;

    /**
     * @var mixed
     */
    protected $eventsOrderShippedActivated;

    /**
     * @var string
     */
    protected $token;

    public function __construct()
    {
        $this->apiLogger = ApiLogger::getInstance();
    }

    public function updateNewStatus()
    {
        $isUpgradeAlreadyDone = (bool) \Configuration::get(Trustedshopseasyintegration::UPGRADE_NEW_ORDER_STATUS);

        if ($isUpgradeAlreadyDone === true) {
            $this->apiLogger->logInfo('Upgrade already done.');

            return true;
        }

        if ($this->checkCredentials() === false) {
            // error in credentials (not set or not valid token)
            return true;
        }

        $this->channelService = ServiceContainer::getInstance()->get(ChannelService::class);

        if ($this->checkEventType() === false) {
            // event already exist
            return true;
        }

        if ($this->orderShippedFound === false) {
            // Order shipped not present : nothing to do
            $this->apiLogger->logInfo('Upgrade order_shipped - ignored becaused event type not found');

            return true;
        }

        if ($this->idEvent === false) {
            $request = (new CustomRequest('event-types', 'POST'));
            $body = json_encode([
                'name' => 'order_status_from_prestashop',
                'active' => true,
            ]);
            $response = $this->sendCurlRequest($request, $body);

            $this->idEvent = isset($response['id']) ? $response['id'] : false;

            if ($this->idEvent === false) {
                // Error creating event order_status_from_prestashop
                $this->apiLogger->logInfo('Error creating event order_status_from_prestashop');

                return true;
            }
            $this->apiLogger->logInfo('New event id created : ' . $this->idEvent);
        }

        $request = (new CustomRequest('invite-settings', 'PATCH'));
        $body = json_encode([
            'enabled' => true,
            'productInviteConfiguration' => [
                'enabled' => false,
                'sendingDelayInDays' => 5,
            ],
            'serviceInviteConfiguration' => [
                'enabled' => false,
                'sendingDelayInDays' => 5,
            ],
        ]);
        $moreParams = ['event-type-id' => $this->idEvent];
        $this->apiLogger->logInfo('Patching global invite settings');
        $response = $this->sendCurlRequest($request, $body, $moreParams);
        $try = 1;
        $isRequestOk = isset($response['ErrorDescription']) === false
            && count($response) > 0
            && isset($response[0]['id']);
        while ($isRequestOk === false && $try < 8) {
            sleep(2);
            $this->apiLogger->logInfo('Error description or no response trying again, try N°' . ($try + 1));
            $response = $this->sendCurlRequest($request, $body, $moreParams);
            $isRequestOk = isset($response['ErrorDescription']) === false
                && count($response) > 0
                && isset($response[0]['id']);
            ++$try;
        }

        $channels = $this->channelService->getMappedChannels()->getMappedChannels();
        foreach ($channels as $oneChannel) {
            $ref = $oneChannel->getETrustedChannelRef();
            $salesChannel = $oneChannel->getSalesChannelRef();
            /** @var OrderStatusEventsModel $orderStatusEvent */
            $orderStatusEvent = $this->channelService->getOrderStatusEventsForChannel($ref, $salesChannel);
            if ((bool) $orderStatusEvent->isOrderStatusEventsActivated() === true) {
                $request = (new CustomRequest('invite-settings', 'GET'));
                $moreParams = ['channel-id' => $ref];
                $response = $this->sendCurlRequest($request, '', $moreParams);
                $idInviteSetting = false;
                foreach ($response as $oneInviteSetting) {
                    if ($oneInviteSetting['eventTypeId'] === $this->idEvent) {
                        $idInviteSetting = $oneInviteSetting['id'];
                    }
                }
                if ($idInviteSetting !== false) {
                    $this->apiLogger->logInfo('Activating event order_status_from_prestashop for channel : ' . $ref);
                    $request = (new CustomRequest('invite-settings/' . $idInviteSetting, 'PATCH'));
                    $body = json_encode([
                        'enabled' => true,
                        'productInviteConfiguration' => [
                            'enabled' => true,
                        ],
                        'serviceInviteConfiguration' => [
                            'enabled' => true,
                        ],
                    ]);
                    $response = $this->sendCurlRequest($request, $body);
                } else {
                    $this->apiLogger->logInfo('No event type found to patch this channel on GET response of invite-settings with "channel-id" = ' . $ref);
                }
            } else {
                $this->apiLogger->logInfo('Channel ' . $ref . ' do not have order_status_event order shipped enabled before, channel not activated with order_status_from_prestashop.');
            }
        }

        return \Configuration::updateValue(Trustedshopseasyintegration::UPGRADE_NEW_ORDER_STATUS, 1);
    }

    private function checkCredentials()
    {
        $this->credentialsService = ServiceContainer::getInstance()->get(CredentialsService::class);
        $credentials = $this->credentialsService->getCredentials();
        if ($credentials->getClientId() === '' || $credentials->getClientSecret() === '') {
            $this->apiLogger->logInfo('No credentials saved.');

            return true;
        }

        $this->token = $this->getToken($credentials->getClientId(), $credentials->getClientSecret());
        if ($this->token === false) {
            $this->apiLogger->logInfo('Token generation error - wrong token - please check credentials.');

            return true;
        }
    }

    private function checkEventType()
    {
        $request = (new CustomRequest('event-types', 'GET'));
        $response = $this->sendCurlRequest($request);

        $this->orderShippedFound = false;
        $this->idEvent = false;
        $this->orderShippedActive = false;
        foreach ($response as $oneEventType) {
            $name = isset($oneEventType['name']) ? $oneEventType['name'] : '';
            $active = isset($oneEventType['active']) ? $oneEventType['active'] : false;
            if ($name === 'order_shipped') {
                if ($active === false) {
                    // Order shipped not found : nothing to do
                    $this->apiLogger->logInfo('Upgrade order_shipped - ignored becaused not activated');

                    $this->orderShippedActive = true;
                } else {
                    $this->eventsOrderShippedActivated[] = $oneEventType['id'];
                }
                $this->orderShippedFound = true;
            }
            if ($name === 'order_status_from_prestashop') {
                // Already created : nothing to do !
                $this->apiLogger->logInfo('Upgrade order_status_from_prestashop - event type already created');
                $this->idEvent = $oneEventType['id'];

                return false;
            }
        }
    }

    public function deleteEventTypePrestaShop()
    {
        if ($this->checkCredentials() === false) {
            // error in credentials (not set or not valid token)
            return true;
        }

        $this->checkEventType();

        if ($this->idEvent === false) {
            // already created : do not try to delete it
            return true;
        }

        $request = (new CustomRequest('event-types/' . $this->idEvent, 'DELETE'));
        $response = $this->sendCurlRequest($request);

        return \Configuration::updateValue(Trustedshopseasyintegration::UPGRADE_NEW_ORDER_STATUS, 0);
    }

    private function sendCurlRequest($request, $body = '', $moreParams = [])
    {
        $url = $request->getUri();
        if (empty($moreParams) === false) {
            foreach ($moreParams as $key => $value) {
                $url .= '&' . $key . '=' . $value;
            }
        }
        $this->apiLogger->logInfo('Request : ' . json_encode($request));
        if ($body !== '') {
            $this->apiLogger->logInfo('Body : ' . $body);
        }
        /** @var ArrayResponse $response */
        $response = $this->sendRequest($request, $url, $body);
        $this->apiLogger->logInfo('Response : ' . json_encode($response));

        return $response->getBody();
    }

    /**
     * Create cURL request options
     *
     * @param CustomRequest $request
     *
     * @return array<mixed> cURL options
     */
    protected function createOptions(CustomRequest $request, $url, $body)
    {
        $options = [];

        // These options default to false and cannot be changed on set up.
        // The options should be provided with the request instead.
        $options[CURLOPT_FOLLOWLOCATION] = false;
        $options[CURLOPT_HEADER] = false;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_SSLVERSION] = CURL_SSLVERSION_TLSv1_2;
        $options[CURLOPT_URL] = (string) $url;
        $options[CURLOPT_HTTPHEADER] = $this->createHeaders($request, $options);
        if ($request->getMethod() !== 'GET') {
            $options[CURLOPT_CUSTOMREQUEST] = (string) \strtoupper($request->getMethod());
            $options[CURLOPT_POSTFIELDS] = (string) $body;
        }

        return $options;
    }

    /**
     * Send a CURL Request
     *
     * @param AbstractRequest $request
     *
     * @return AbstractResponse
     */
    public function sendRequest(AbstractRequest $request, $url, $body = '')
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ];

        $this->apiLogger->logInfo('Request to : ' . $url);

        $request->setHeaders($headers);

        $ch = curl_init();

        $response = $this->createResponse($request);
        $options = $this->createOptions($request, $url, $body);

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

        return $response->getResponse();
    }

    /**
     * Create a new http response
     *
     * @param AbstractRequest $request
     *
     * @return ResponseBuilder
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
     * Get Oauth token
     *
     * @return false|string
     */
    private function getToken($clientId, $clientSecret)
    {
        $data['grant_type'] = 'client_credentials';
        $data['client_id'] = $clientId;
        $data['client_secret'] = $clientSecret;
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
}
