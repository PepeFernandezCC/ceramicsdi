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

namespace TrustedshopsAddon\Utils;

use Configuration;
use TrustedshopsAddon\Model\Constant\GtinType;
use TrustedshopsAddon\Model\Constant\SkuType;
use Trustedshopseasyintegration;

class ModuleInitializer
{
    public function initialize()
    {
        $properties = $this->getProperties();
        if (empty($properties)) {
            Configuration::updateGlobalValue(Trustedshopseasyintegration::SKU_TYPE, SkuType::SKU);
            Configuration::updateGlobalValue(Trustedshopseasyintegration::GTIN_TYPE, GtinType::EAN13);
            Configuration::updateGlobalValue(Trustedshopseasyintegration::ORDER_STATUS_EVENTS_CONFIG, '{}');

            return true;
        }

        $sku = empty($properties['skuType']) ? 'sku' : $properties['skuType'];
        $gtin = empty($properties['gtinType']) ? 'ean13' : $properties['gtinType'];
        if (empty($properties['order_status_reviews'])
            || !$this->validateOrderStatusEventsConfig($properties['order_status_reviews'])) {
            $orderStatusConfig = '{}';
        } else {
            $orderStatusConfig = json_encode($properties['order_status_reviews']);
        }

        Configuration::updateGlobalValue(Trustedshopseasyintegration::SKU_TYPE, SkuType::getSkuTypeBySku($sku));
        Configuration::updateGlobalValue(Trustedshopseasyintegration::GTIN_TYPE, GtinType::getGtinTypeByType($gtin));
        Configuration::updateGlobalValue(Trustedshopseasyintegration::ORDER_STATUS_EVENTS_CONFIG, $orderStatusConfig);

        return true;
    }

    /**
     * @return array|mixed
     */
    protected function getProperties()
    {
        if (@file_exists(_PS_MODULE_DIR_ . 'trustedshopseasyintegration/.properties.json')) {
            $properties = \Tools::file_get_contents(_PS_MODULE_DIR_ . 'trustedshopseasyintegration/.properties.json');
        } else {
            $properties = \Tools::file_get_contents(_PS_MODULE_DIR_ . 'trustedshopseasyintegration/.properties.json.master');
        }

        if (!empty($properties)) {
            return json_decode($properties, true);
        }

        return [];
    }

    protected function validateOrderStatusEventsConfig($config)
    {
        if (!isset($config['all'])
        || !isset($config['all']['trigger_order_status_id'])
        || !isset($config['all']['reviews_nb_days'])) {
            return false;
        }

        return true;
    }
}
