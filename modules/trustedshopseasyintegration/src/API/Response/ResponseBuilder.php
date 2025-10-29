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

namespace TrustedshopsAddon\API\Response;

if (!defined('_PS_VERSION_')) {
    exit;
}

use InvalidArgumentException;

/**
 * Response Builder
 *
 * Build a PSR-7 Response object
 */
class ResponseBuilder
{
    /**
     * PSR-7 Response
     *
     * @var AbstractResponse
     */
    protected $response;

    /**
     * Create a Response Builder
     *
     * @param AbstractResponse $response
     */
    public function __construct(AbstractResponse $response)
    {
        $this->response = $response;
    }

    /**
     * Return the response
     *
     * @return AbstractResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set the response
     *
     * @param AbstractResponse $response Response object
     *
     * @return void
     */
    public function setResponse(AbstractResponse $response)
    {
        $this->response = $response;
    }

    /**
     * Add response header from header line string
     *
     * @param string $headerLine Response header line string
     *
     * @return self $this
     *
     * @throws InvalidArgumentException Invalid header line argument
     */
    public function addHeader($headerLine)
    {
        $headerParts = explode(':', $headerLine, 2);

        if (count($headerParts) !== 2) {
            throw new InvalidArgumentException("'$headerLine' is not a valid HTTP header line");
        }

        $headerName = trim($headerParts[0]);
        $headerValue = trim($headerParts[1]);

        if ($this->response->hasHeader($headerName)) {
            $this->response = $this->response->withAddedHeader($headerName, $headerValue);
        } else {
            $this->response = $this->response->withHeader($headerName, $headerValue);
        }

        return $this;
    }

    /**
     * Set response headers from header line array
     *
     * @param array<string> $headers Array of header lines
     *
     * @return self $this
     *
     * @throws InvalidArgumentException Invalid status code argument value
     */
    public function setHeadersFromArray(array $headers)
    {
        $status = (string) array_shift($headers);

        $this->setStatus($status);

        foreach ($headers as $header) {
            $header_line = trim($header);

            if ($header_line === '') {
                continue;
            }

            $this->addHeader($header_line);
        }

        return $this;
    }

    /**
     * Set reponse status
     *
     * @param string $statusLine Response status line string
     *
     * @return self $this
     *
     * @throws InvalidArgumentException Invalid status line argument
     */
    public function setStatus($statusLine)
    {
        $statusParts = explode(' ', $statusLine, 3);
        $partsCount = count($statusParts);

        if ($partsCount < 2 || strpos(strtoupper($statusParts[0]), 'HTTP/') !== 0) {
            throw new InvalidArgumentException("'$statusLine' is not a valid HTTP status line");
        }

        $reasonPhrase = ($partsCount > 2 ? $statusParts[2] : '');

        $this->response = $this->response
            ->withStatus((int) $statusParts[1], $reasonPhrase)
            ->withProtocolVersion(substr($statusParts[0], 5));

        return $this;
    }
}
