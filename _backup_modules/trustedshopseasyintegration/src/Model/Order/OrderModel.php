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

class OrderModel implements \JsonSerializable
{
    /**
     * @var string
     */
    private $tsCheckoutOrderNr;

    /**
     * @var string
     */
    private $tsCheckoutBuyerEmail;

    /**
     * @var string
     */
    private $tsCheckoutOrderAmount;

    /**
     * @var string
     */
    private $tsCheckoutOrderCurrency;

    /**
     * @var string
     */
    private $tsCheckoutOrderPaymentType;

    /**
     * @var bool
     */
    private $sendProducts;

    /**
     * @var OrderProductModel[]
     */
    private $products = [];

    /**
     * @return string
     */
    public function getTsCheckoutOrderNr()
    {
        return $this->tsCheckoutOrderNr;
    }

    /**
     * @param string $tsCheckoutOrderNr
     *
     * @return static
     */
    public function setTsCheckoutOrderNr($tsCheckoutOrderNr)
    {
        $this->tsCheckoutOrderNr = $tsCheckoutOrderNr;

        return $this;
    }

    /**
     * @return string
     */
    public function getTsCheckoutBuyerEmail()
    {
        return $this->tsCheckoutBuyerEmail;
    }

    /**
     * @param string $tsCheckoutBuyerEmail
     *
     * @return static
     */
    public function setTsCheckoutBuyerEmail($tsCheckoutBuyerEmail)
    {
        $this->tsCheckoutBuyerEmail = $tsCheckoutBuyerEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getTsCheckoutOrderAmount()
    {
        return $this->tsCheckoutOrderAmount;
    }

    /**
     * @param string $tsCheckoutOrderAmount
     *
     * @return static
     */
    public function setTsCheckoutOrderAmount($tsCheckoutOrderAmount)
    {
        $this->tsCheckoutOrderAmount = $tsCheckoutOrderAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getTsCheckoutOrderCurrency()
    {
        return $this->tsCheckoutOrderCurrency;
    }

    /**
     * @param string $tsCheckoutOrderCurrency
     *
     * @return static
     */
    public function setTsCheckoutOrderCurrency($tsCheckoutOrderCurrency)
    {
        $this->tsCheckoutOrderCurrency = $tsCheckoutOrderCurrency;

        return $this;
    }

    /**
     * @return string
     */
    public function getTsCheckoutOrderPaymentType()
    {
        return $this->tsCheckoutOrderPaymentType;
    }

    /**
     * @param string $tsCheckoutOrderPaymentType
     *
     * @return static
     */
    public function setTsCheckoutOrderPaymentType($tsCheckoutOrderPaymentType)
    {
        $this->tsCheckoutOrderPaymentType = $tsCheckoutOrderPaymentType;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSendProducts()
    {
        return $this->sendProducts;
    }

    /**
     * @param bool $sendProducts
     *
     * @return static
     */
    public function setSendProducts($sendProducts)
    {
        $this->sendProducts = $sendProducts;

        return $this;
    }

    /**
     * @return OrderProductModel[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param OrderProductModel[] $products
     *
     * @return OrderModel
     */
    public function setProducts($products)
    {
        $this->products = $products;

        return $this;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
