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

namespace TrustedshopsAddon\API\Uri;

abstract class AbstractUri
{
    /** @var array<string,int> SCHEMES. */
    private static $SCHEMES = ['http' => 80, 'https' => 443];

    /** @var string CHAR_UNRESERVED. */
    private static $CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~';

    /** @var string CHAR_SUB_DELIMS. */
    private static $CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';

    /** @var string Uri scheme. */
    protected $scheme = 'https';

    /** @var string Uri user info. */
    protected $userInfo = '';

    /** @var string Uri host. */
    protected $host = '';

    /** @var int|null Uri port. */
    protected $port;

    /** @var string Uri path. */
    protected $path = '';

    /** @var string Uri query string. */
    protected $query = '';

    /** @var string Uri fragment. */
    protected $fragment = '';

    /**
     * Constructor
     *
     * @param string $uri uri
     */
    public function __construct($uri = '')
    {
        if ('' !== $uri) {
            if (false === $parts = \parse_url($uri)) {
                throw new \InvalidArgumentException(\sprintf('Unable to parse URI: "%s"', $uri));
            }

            // Apply parse_url parts to a URI.
            $this->scheme = isset($parts['scheme']) ? \strtr($parts['scheme'], 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz') : '';
            $this->userInfo = empty($parts['user']) ? '' : $parts['user'];
            $this->host = isset($parts['host']) ? \strtr($parts['host'], 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz') : '';
            $this->port = isset($parts['port']) ? $this->filterPort($parts['port']) : null;
            $this->path = isset($parts['path']) ? $this->filterPath($parts['path']) : '';
            $this->query = isset($parts['query']) ? $this->filterQueryAndFragment($parts['query']) : '';
            $this->fragment = isset($parts['fragment']) ? $this->filterQueryAndFragment($parts['fragment']) : '';
            if (isset($parts['pass'])) {
                $this->userInfo .= ':' . $parts['pass'];
            }
        }
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return self::createUriString($this->scheme, $this->getAuthority(), $this->path, $this->query, $this->fragment);
    }

    /**
     * Get Scheme
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @inherit
     */
    public function getAuthority()
    {
        if ('' === $this->host) {
            return '';
        }

        $authority = $this->host;
        if ('' !== $this->userInfo) {
            $authority = $this->userInfo . '@' . $authority;
        }

        if (null !== $this->port) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    /**
     * @inherit
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * @inherit
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @inherit
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @inherit
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @inherit
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @inherit
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @inherit
     */
    public function withScheme($scheme)
    {
        if (!\is_string($scheme)) {
            throw new \InvalidArgumentException('Scheme must be a string');
        }

        if ($this->scheme === $scheme = \strtr($scheme, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')) {
            return $this;
        }

        $new = clone $this;
        $new->scheme = $scheme;
        $new->port = $new->filterPort($new->port);

        return $new;
    }

    /**
     * @inherit
     */
    public function withUserInfo($user, $password = null)
    {
        $info = $user;
        if (null !== $password && '' !== $password) {
            $info .= ':' . $password;
        }

        if ($this->userInfo === $info) {
            return $this;
        }

        $new = clone $this;
        $new->userInfo = $info;

        return $new;
    }

    /**
     * @inherit
     */
    public function withHost($host)
    {
        if (!\is_string($host)) {
            throw new \InvalidArgumentException('Host must be a string');
        }

        if ($this->host === $host = \strtr($host, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')) {
            return $this;
        }

        $new = clone $this;
        $new->host = $host;

        return $new;
    }

    /**
     * @inherit
     */
    public function withPort($port)
    {
        if ($this->port === $port = $this->filterPort($port)) {
            return $this;
        }

        $new = clone $this;
        $new->port = $port;

        return $new;
    }

    /**
     * @inherit
     */
    public function withPath($path)
    {
        if ($this->path === $path = $this->filterPath($path)) {
            return $this;
        }

        $new = clone $this;
        $new->path = $path;

        return $new;
    }

    /**
     * @inherit
     */
    public function withQuery($query)
    {
        if ($this->query === $query = $this->filterQueryAndFragment($query)) {
            return $this;
        }

        $new = clone $this;
        $new->query = $query;

        return $new;
    }

    /**
     * @inherit
     */
    public function withFragment($fragment)
    {
        if ($this->fragment === $fragment = $this->filterQueryAndFragment($fragment)) {
            return $this;
        }

        $new = clone $this;
        $new->fragment = $fragment;

        return $new;
    }

    /**
     * create Uri String
     *
     * @param string $scheme
     * @param string $authority
     * @param string $path
     * @param string $query
     * @param string $fragment
     *
     * @return string
     */
    protected static function createUriString($scheme, $authority, $path, $query, $fragment)
    {
        $uri = '';
        if ('' !== $scheme) {
            $uri .= $scheme . ':';
        }

        if ('' !== $authority) {
            $uri .= '//' . $authority;
        }

        if ('' !== $path) {
            if (!empty($path[0]) && '/' !== $path[0]) {
                if ('' !== $authority) {
                    // If the path is rootless and an authority is present, the path MUST be prefixed by "/"
                    $path = '/' . $path;
                }
            } elseif (isset($path[1]) && '/' === $path[1]) {
                if ('' === $authority) {
                    // If the path is starting with more than one "/" and no authority is present, the
                    // starting slashes MUST be reduced to one.
                    $path = '/' . \ltrim($path, '/');
                }
            }

            $uri .= $path;
        }

        if ('' !== $query) {
            $uri .= '?' . $query;
        }

        if ('' !== $fragment) {
            $uri .= '#' . $fragment;
        }

        return $uri;
    }

    /**
     * Validate port
     *
     * @param string $scheme
     * @param int|null $port
     *
     * @return bool
     */
    protected static function isNonStandardPort($scheme, $port)
    {
        return !isset(self::$SCHEMES[$scheme]) || $port !== self::$SCHEMES[$scheme];
    }

    /**
     * Validate port
     *
     * @param int|null $port
     *
     * @return int|null
     *
     * @throws \InvalidArgumentException
     */
    protected function filterPort($port)
    {
        if (null === $port) {
            return null;
        }

        $port = (int) $port;
        if (0 > $port || 0xFFFF < $port) {
            throw new \InvalidArgumentException(\sprintf('Invalid port: %d. Must be between 0 and 65535', $port));
        }

        return self::isNonStandardPort($this->scheme, $port) ? $port : null;
    }

    /**
     * Validate QueryAndFragment
     *
     * @param string $path
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function filterPath($path)
    {
        if (!\is_string($path)) {
            throw new \InvalidArgumentException('Path must be a string');
        }

        return (string) \preg_replace_callback(
            '/(?:[^' . self::$CHAR_UNRESERVED . self::$CHAR_SUB_DELIMS . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
            [
                __CLASS__,
                'rawurlencodeMatchZero',
            ],
            $path
        );
    }

    /**
     * Validate QueryAndFragment
     *
     * @param string $str
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function filterQueryAndFragment($str)
    {
        if (!\is_string($str)) {
            throw new \InvalidArgumentException('Query and fragment must be a string');
        }

        return (string) \preg_replace_callback(
            '/(?:[^' . self::$CHAR_UNRESERVED . self::$CHAR_SUB_DELIMS . '%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/',
            [
                __CLASS__,
                'rawurlencodeMatchZero',
            ],
            $str
        );
    }

    /**
     * Raw urlencode Match Zero
     *
     * @param array<int,string> $match
     *
     * @return string
     */
    protected static function rawurlencodeMatchZero(array $match)
    {
        return \rawurlencode($match[0]);
    }
}
