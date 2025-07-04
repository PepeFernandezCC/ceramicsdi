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

namespace TrustedshopsAddon\API\Model;

use InvalidArgumentException;

/**
 * Best Price Model Class
 */
class Error extends AbstractModel
{
    // PROPERTIES

    /**
     * @var string|null
     */
    private $type;

    /**
     * @var string|null
     */
    private $title;

    /**
     * @var int|null
     */
    private $status;

    /**
     * @var string|null
     */
    private $detail;

    /**
     * @var string|null
     */
    private $instance;

    /**
     * @var array|null
     */
    private $errors;

    // GETTERS & SETTERS

    /**
     * Get Type
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set Type
     *
     * @param string|null $type
     *
     * @return self
     */
    public function setType($type)
    {
        if (is_string($type) === true || is_null($type) === true) {
            $this->type = $type;

            return $this;
        }

        throw new InvalidArgumentException('Type must be a string or null but ' . gettype($type) . ' is given.');
    }

    /**
     * Get Title
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set Title
     *
     * @param string|null $title
     *
     * @return self
     */
    public function setTitle($title)
    {
        if (is_string($title) === true || is_null($title) === true) {
            $this->title = $title;

            return $this;
        }

        throw new InvalidArgumentException('Title must be a string or null but ' . gettype($title) . ' is given.');
    }

    /**
     * Get Status
     *
     * @return int|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set Status
     *
     * @param int|null $status
     *
     * @return self
     */
    public function setStatus($status)
    {
        if (is_int($status) === true || is_null($status) === true) {
            $this->status = $status;

            return $this;
        }

        throw new InvalidArgumentException('Status must be an integer or null but ' . gettype($status) . ' is given.');
    }

    /**
     * Get Detail
     *
     * @return string|null
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * Set Detail
     *
     * @param string|null $detail
     *
     * @return self
     */
    public function setDetail($detail)
    {
        if (is_string($detail) === true || is_null($detail) === true) {
            $this->detail = $detail;

            return $this;
        }

        throw new InvalidArgumentException('Detail must be a string or null but ' . gettype($detail) . ' is given.');
    }

    /**
     * Get Instance
     *
     * @return string|null
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * Set Instance
     *
     * @param string|null $instance
     *
     * @return self
     */
    public function setInstance($instance)
    {
        if (is_string($instance) === true || is_null($instance) === true) {
            $this->instance = $instance;

            return $this;
        }

        throw new InvalidArgumentException('Instance must be a string or null but ' . gettype($instance) . ' is given.');
    }

    /**
     * Get Errors
     *
     * @return array<mixed>|null
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set Errors
     *
     * @param array<mixed>|null $errors
     *
     * @return self
     */
    public function setErrors($errors)
    {
        if (is_array($errors) === true || is_null($errors) === true) {
            $this->errors = $errors;

            return $this;
        }

        throw new InvalidArgumentException('Errors must be an array or null but ' . gettype($errors) . ' is given.');
    }
}
