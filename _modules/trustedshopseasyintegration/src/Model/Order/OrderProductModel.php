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

namespace TrustedshopsAddon\Model\Order;

class OrderProductModel implements \JsonSerializable
{
    /**
     * @var string
     */
    private $tsCheckoutProductUrl;

    /**
     * @var string
     */
    private $tsCheckoutProductImageUrl;

    /**
     * @var string
     */
    private $tsCheckoutProductName;

    /**
     * @var string
     */
    private $tsCheckoutProductSKU;

    /**
     * @var string
     */
    private $tsCheckoutProductGTIN;

    /**
     * @var string
     */
    private $tsCheckoutProductMPN;

    /**
     * @var string
     */
    private $tsCheckoutProductBrand;

    /**
     * @return string
     */
    public function getTsCheckoutProductUrl()
    {
        return $this->tsCheckoutProductUrl;
    }

    /**
     * @param string $tsCheckoutProductUrl
     *
     * @return static
     */
    public function setTsCheckoutProductUrl($tsCheckoutProductUrl)
    {
        $this->tsCheckoutProductUrl = $tsCheckoutProductUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getTsCheckoutProductImageUrl()
    {
        return $this->tsCheckoutProductImageUrl;
    }

    /**
     * @param string $tsCheckoutProductImageUrl
     *
     * @return static
     */
    public function setTsCheckoutProductImageUrl($tsCheckoutProductImageUrl)
    {
        $this->tsCheckoutProductImageUrl = $tsCheckoutProductImageUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getTsCheckoutProductName()
    {
        return $this->tsCheckoutProductName;
    }

    /**
     * @param string $tsCheckoutProductName
     *
     * @return static
     */
    public function setTsCheckoutProductName($tsCheckoutProductName)
    {
        $this->tsCheckoutProductName = $tsCheckoutProductName;

        return $this;
    }

    /**
     * @return string
     */
    public function getTsCheckoutProductSKU()
    {
        return $this->tsCheckoutProductSKU;
    }

    /**
     * @param string $tsCheckoutProductSKU
     *
     * @return static
     */
    public function setTsCheckoutProductSKU($tsCheckoutProductSKU)
    {
        $this->tsCheckoutProductSKU = $tsCheckoutProductSKU;

        return $this;
    }

    /**
     * @return string
     */
    public function getTsCheckoutProductGTIN()
    {
        return $this->tsCheckoutProductGTIN;
    }

    /**
     * @param string $tsCheckoutProductGTIN
     *
     * @return static
     */
    public function setTsCheckoutProductGTIN($tsCheckoutProductGTIN)
    {
        $this->tsCheckoutProductGTIN = $tsCheckoutProductGTIN;

        return $this;
    }

    /**
     * @return string
     */
    public function getTsCheckoutProductMPN()
    {
        return $this->tsCheckoutProductMPN;
    }

    /**
     * @param string $tsCheckoutProductMPN
     *
     * @return static
     */
    public function setTsCheckoutProductMPN($tsCheckoutProductMPN)
    {
        $this->tsCheckoutProductMPN = $tsCheckoutProductMPN;

        return $this;
    }

    /**
     * @return string
     */
    public function getTsCheckoutProductBrand()
    {
        return $this->tsCheckoutProductBrand;
    }

    /**
     * @param string $tsCheckoutProductBrand
     *
     * @return static
     */
    public function setTsCheckoutProductBrand($tsCheckoutProductBrand)
    {
        $this->tsCheckoutProductBrand = $tsCheckoutProductBrand;

        return $this;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
