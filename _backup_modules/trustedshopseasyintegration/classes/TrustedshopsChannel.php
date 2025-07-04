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

/**
 * Trusted shops channel Object Model
 */
class TrustedshopsChannel extends ObjectModel
{
    /**
     * @var int
     */
    public $id_trustedshops_channel;

    /**
     * @var int
     */
    public $id_lang;

    /**
     * @var int
     */
    public $id_shop;

    /**
     * @var string
     */
    public $id_client;

    /**
     * @var string
     */
    public $e_trusted_account_ref;

    /**
     * @var string
     */
    public $e_trusted_locale;

    /**
     * @var string
     */
    public $e_trusted_name;

    /**
     * @var string
     */
    public $e_trusted_url;

    /**
     * @var string
     */
    public $e_trusted_channel_ref;

    /**
     * @var string
     */
    public $id_trustbadge;

    /**
     * @var string
     */
    public $trustbadge_config;

    /**
     * @var string
     */
    public $widget_config;

    /**
     * @var bool
     */
    public $products_review_invites;

    /**
     * @var bool
     */
    public $order_status_events = false;

    /**
     * @var array
     */
    public static $definition = [
        'table' => 'trustedshops_channel',
        'primary' => 'id_trustedshops_channel',
        'fields' => [
            'id_lang' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true,
            ],
            'id_shop' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true,
            ],
            'id_client' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'size' => 512,
            ],
            'e_trusted_account_ref' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'size' => 512,
            ],
            'e_trusted_locale' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'size' => 255,
            ],
            'e_trusted_name' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'size' => 512,
            ],
            'e_trusted_url' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'size' => 512,
            ],
            'e_trusted_channel_ref' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'size' => 512,
            ],
            'id_trustbadge' => [
                'type' => self::TYPE_STRING,
                'required' => false,
                'size' => 512,
            ],
            'trustbadge_config' => [
                'type' => self::TYPE_HTML,
                'required' => false,
                'size' => 10000,
            ],
            'widget_config' => [
                'type' => self::TYPE_HTML,
                'required' => false,
                'size' => 10000,
            ],
            'products_review_invites' => [
                'type' => self::TYPE_BOOL,
                'validation' => 'isBool',
                'default' => 0,
            ],
            'order_status_events' => [
                'type' => self::TYPE_BOOL,
                'validation' => 'isBool',
                'default' => 0,
            ],
        ],
    ];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'id_lang' => $this->id_lang,
            'id_shop' => $this->id_shop,
            'id_client' => $this->id_client,
            'e_trusted_account_ref' => $this->e_trusted_account_ref,
            'e_trusted_locale' => $this->e_trusted_locale,
            'e_trusted_name' => $this->e_trusted_name,
            'e_trusted_url' => $this->e_trusted_url,
            'e_trusted_channel_ref' => $this->e_trusted_channel_ref,
            'id_trustbadge' => $this->id_trustbadge,
            'trustbadge_config' => $this->trustbadge_config,
            'widget_config' => $this->widget_config,
            'products_review_invites' => $this->products_review_invites,
            'order_status_events' => $this->order_status_events,
        ];
    }
}
