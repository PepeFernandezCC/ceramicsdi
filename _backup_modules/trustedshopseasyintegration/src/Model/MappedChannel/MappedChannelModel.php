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

namespace TrustedshopsAddon\Model\MappedChannel;

class MappedChannelModel implements \JsonSerializable
{
    /**
     * @var string
     */
    private $eTrustedChannelRef;

    /**
     * @var string
     */
    private $eTrustedLocale;

    /**
     * @var string
     */
    private $eTrustedName;

    /**
     * @var string
     */
    private $eTrustedUrl;

    /**
     * @var string
     */
    private $salesChannelRef;

    /**
     * @var string
     */
    private $salesChannelLocale;

    /**
     * @var string
     */
    private $salesChannelName;

    /**
     * @var string
     */
    private $salesChannelUrl;

    /**
     * @var string
     */
    private $eTrustedAccountRef;

    /**
     * @return string
     */
    public function getETrustedChannelRef()
    {
        return $this->eTrustedChannelRef;
    }

    /**
     * @param string $eTrustedChannelRef
     *
     * @return MappedChannelModel
     */
    public function setETrustedChannelRef($eTrustedChannelRef)
    {
        $this->eTrustedChannelRef = $eTrustedChannelRef;

        return $this;
    }

    /**
     * @return string
     */
    public function getETrustedLocale()
    {
        return $this->eTrustedLocale;
    }

    /**
     * @param string $eTrustedLocale
     *
     * @return MappedChannelModel
     */
    public function setETrustedLocale($eTrustedLocale)
    {
        $this->eTrustedLocale = $eTrustedLocale;

        return $this;
    }

    /**
     * @return string
     */
    public function getETrustedName()
    {
        return $this->eTrustedName;
    }

    /**
     * @param string $eTrustedName
     *
     * @return MappedChannelModel
     */
    public function setETrustedName($eTrustedName)
    {
        $this->eTrustedName = $eTrustedName;

        return $this;
    }

    /**
     * @return string
     */
    public function getETrustedUrl()
    {
        return $this->eTrustedUrl;
    }

    /**
     * @param string $eTrustedUrl
     *
     * @return MappedChannelModel
     */
    public function setETrustedUrl($eTrustedUrl)
    {
        $this->eTrustedUrl = $eTrustedUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalesChannelRef()
    {
        return $this->salesChannelRef;
    }

    /**
     * @param string $salesChannelRef
     *
     * @return MappedChannelModel
     */
    public function setSalesChannelRef($salesChannelRef)
    {
        $this->salesChannelRef = $salesChannelRef;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalesChannelLocale()
    {
        return $this->salesChannelLocale;
    }

    /**
     * @param string $salesChannelLocale
     *
     * @return MappedChannelModel
     */
    public function setSalesChannelLocale($salesChannelLocale)
    {
        $this->salesChannelLocale = $salesChannelLocale;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalesChannelName()
    {
        return $this->salesChannelName;
    }

    /**
     * @param string $salesChannelName
     *
     * @return MappedChannelModel
     */
    public function setSalesChannelName($salesChannelName)
    {
        $this->salesChannelName = $salesChannelName;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalesChannelUrl()
    {
        return $this->salesChannelUrl;
    }

    /**
     * @param string $salesChannelUrl
     *
     * @return MappedChannelModel
     */
    public function setSalesChannelUrl($salesChannelUrl)
    {
        $this->salesChannelUrl = $salesChannelUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getETrustedAccountRef()
    {
        return $this->eTrustedAccountRef;
    }

    /**
     * @param string $eTrustedAccountRef
     *
     * @return MappedChannelModel
     */
    public function setETrustedAccountRef($eTrustedAccountRef)
    {
        $this->eTrustedAccountRef = $eTrustedAccountRef;

        return $this;
    }

    public function build($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
