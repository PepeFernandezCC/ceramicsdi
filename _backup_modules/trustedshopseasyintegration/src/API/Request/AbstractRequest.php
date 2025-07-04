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

namespace TrustedshopsAddon\API\Request;

use JsonSerializable;
use TrustedshopsAddon\API\Constant\APIType;
use TrustedshopsAddon\API\Model\AbstractModel;
use TrustedshopsAddon\API\Uri\ApiUri;

abstract class AbstractRequest implements JsonSerializable
{
    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    protected $response;

    /** @var ApiUri */
    protected $uri;

    /** @var array<mixed> Map of all registered headers, as original name => array of values */
    protected $headers = [];

    /** @var array<string> Map of lowercase header name => original name at registration */
    protected $headerNames = [];

    /** @var string */
    protected $protocol = '1.1';

    /** @var string|null */
    protected $requestTarget;

    public function __construct()
    {
        $this->uri = new ApiUri();
        $this->uri = $this->uri->withPath($this->requestTarget);
        $this->getQueryParameters();
    }

    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Generate query parameters for the request
     * Add Client system name & version
     * Add Connector module name and version
     */
    private function getQueryParameters()
    {
        $query = $this->uri->getQuery();
        if ($query !== '') {
            $query .= '&';
        }
        $query .= $this->getSystemInfo(APIType::QUERY_SYSTEM);
        $query .= '&' . $this->getSystemInfo(APIType::QUERY_SYSTEM_VERSION);
        $query .= '&' . $this->getSystemInfo(APIType::QUERY_CONNECTOR);
        $query .= '&' . $this->getSystemInfo(APIType::QUERY_CONNECTOR_VERSION);
        $this->uri = $this->uri->withQuery($query);
    }

    private function getSystemInfo($type)
    {
        $result = '';
        try {
            switch ($type) {
                case APIType::QUERY_SYSTEM:
                    $result = 'PrestaShop';
                    break;
                case APIType::QUERY_SYSTEM_VERSION:
                    $result = _PS_VERSION_;
                    break;
                case APIType::QUERY_CONNECTOR:
                    $result = 'NewGen-connector';
                    break;
                case APIType::QUERY_CONNECTOR_VERSION:
                    $result = \Module::getInstanceByName('trustedshopseasyintegration')->version;
                    break;
            }

            return 'Trustedshops-' . $type . '=' . urlencode($result);
        } catch (\Exception $e) {
            $result = 'Unknown';
        }

        return 'Trustedshops-' . $type . '=' . $result;
    }

    /**
     * Set Body From Model
     *
     * @param AbstractModel $body
     *
     * @return self
     */
    public function setModel(AbstractModel $body)
    {
        $json = json_encode($body->jsonSerialize(), JSON_PRETTY_PRINT);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException('json_encode error: ' . json_last_error_msg());
        }
        $new = clone $this;
        $new->body = $json;

        return $new;
    }

    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set Body From Model
     *
     * @return string
     */
    public function getResponseObject()
    {
        return $this->response;
    }

    /**
     * @inherit
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set headers
     *
     * @param array<mixed> $headers
     *
     * @return void
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as $header => $value) {
            if (\is_int($header)) {
                // If a header name was set to a numeric string, PHP will cast the key to an int.
                // We must cast it back to a string in order to comply with validation.
                $header = (string) $header;
            }
            $value = $this->validateAndTrimHeader($header, $value);
            $normalized = \strtr($header, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
            if (isset($this->headerNames[$normalized])) {
                $header = $this->headerNames[$normalized];
                $this->headers[$header] = \array_merge($this->headers[$header], $value);
            } else {
                $this->headerNames[$normalized] = $header;
                $this->headers[$header] = $value;
            }
        }
    }

    /**
     * Make sure the header complies with RFC 7230.
     *
     * Header names must be a non-empty string consisting of token characters.
     *
     * Header values must be strings consisting of visible characters with all optional
     * leading and trailing whitespace stripped. This method will always strip such
     * optional whitespace. Note that the method does not allow folding whitespace within
     * the values as this was deprecated for almost all instances by the RFC.
     *
     * header-field = field-name ":" OWS field-value OWS
     * field-name   = 1*( "!" / "#" / "$" / "%" / "&" / "'" / "*" / "+" / "-" / "." / "^"
     *              / "_" / "`" / "|" / "~" / %x30-39 / ( %x41-5A / %x61-7A ) )
     * OWS          = *( SP / HTAB )
     * field-value  = *( ( %x21-7E / %x80-FF ) [ 1*( SP / HTAB ) ( %x21-7E / %x80-FF ) ] )
     *
     * @see https://tools.ietf.org/html/rfc7230#section-3.2.4
     *
     * @param mixed $header
     * @param mixed $values
     *
     * @return array<string>
     */
    protected function validateAndTrimHeader($header, $values)
    {
        if (!\is_string($header) || 1 !== \preg_match("@^[!#$%&'*+.^_`|~0-9A-Za-z-]+$@", $header)) {
            throw new \InvalidArgumentException('Header name must be an RFC 7230 compatible string.');
        }

        if (!\is_array($values)) {
            // This is simple, just one value.
            if ((!\is_numeric($values) && !\is_string($values)) || 1 !== \preg_match("@^[ \t\x21-\x7E\x80-\xFF]*$@", (string) $values)) {
                throw new \InvalidArgumentException('Header values must be RFC 7230 compatible strings.');
            }

            return [\trim((string) $values, " \t")];
        }

        if (empty($values)) {
            throw new \InvalidArgumentException('Header values must be a string or an array of strings, empty array given.');
        }

        // Assert Non empty array
        $returnValues = [];
        foreach ($values as $v) {
            if ((!\is_numeric($v) && !\is_string($v)) || 1 !== \preg_match("@^[ \t\x21-\x7E\x80-\xFF]*$@", (string) $v)) {
                throw new \InvalidArgumentException('Header values must be RFC 7230 compatible strings.');
            }

            $returnValues[] = \trim((string) $v, " \t");
        }

        return $returnValues;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
