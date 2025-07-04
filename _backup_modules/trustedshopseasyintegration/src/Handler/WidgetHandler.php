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

namespace TrustedshopsAddon\Handler;

use Configuration;
use Product;
use TrustedshopsAddon\Model\Constant\GtinType;
use TrustedshopsAddon\Model\Constant\ProductIdentifier;
use TrustedshopsAddon\Model\Constant\SkuType;
use TrustedshopsAddon\Model\Constant\WidgetLocation;
use TrustedshopsAddon\Model\Widget\WidgetFrontModel;
use Trustedshopseasyintegration;

class WidgetHandler
{
    /**
     * @var int
     */
    protected $idShop;

    /**
     * @var int
     */
    protected $idLang;

    const PRODUCT_IDENTIFIER_ATTRIBUTE = 'productIdentifier';

    /**
     * @param string $config
     * @param string $location
     * @param int|null $idProduct
     *
     * @return array|null
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function handle($config, $location, $idProduct = null)
    {
        $config = json_decode($config, true);

        if (empty($config) || empty($config['children'])) {
            return null;
        }

        $models = [];

        foreach ($config['children'] as $child) {
            if (empty($child['attributes']) || empty($child['children'])) {
                continue;
            }

            foreach ($child['children'] as $widget) {
                if (empty($widget['widgetLocation'])
                    || empty($widget['widgetLocation']['id'])
                    || $widget['widgetLocation']['id'] != $location) {
                    continue;
                }
                $widgetFrontModel = new WidgetFrontModel();
                $widgetFrontModel->setTag($widget['tag']);
                $widgetFrontModel->setType($widget['applicationType']);

                foreach ($widget['attributes'] as $attributeName => $attribute) {
                    if ($attributeName == self::PRODUCT_IDENTIFIER_ATTRIBUTE) {
                        $widgetFrontModel->add(
                            $attribute['attributeName'],
                            $this->getProductIdentifier($attribute['attributeName'], $idProduct)
                        );
                        continue;
                    }

                    $widgetFrontModel->add(
                        $attribute['attributeName'],
                        isset($attribute['value']) ? $attribute['value'] : ''
                    );
                }

                $models[] = $widgetFrontModel;

                $tagExtension = isset($widget['extensions']['product_star']['tag'])
                    && empty($widget['extensions']['product_star']['tag']) === false
                    ? $widget['extensions']['product_star']['tag']
                    : '';

                if ($tagExtension !== '' && $location !== WidgetLocation::PRODUCT_LIST) {
                    $widgetFrontModel = new WidgetFrontModel();
                    $widgetFrontModel->setTag($widget['extensions']['product_star']['tag']);
                    $widgetFrontModel->setType('product_star');
                    foreach ($widget['attributes'] as $attributeName => $attribute) {
                        if ($attributeName == self::PRODUCT_IDENTIFIER_ATTRIBUTE) {
                            $widgetFrontModel->add(
                                $attribute['attributeName'],
                                $this->getProductIdentifier($attribute['attributeName'], $idProduct)
                            );
                            continue;
                        } elseif ($attributeName == 'id') {
                            $widgetFrontModel->add(
                                $attribute['attributeName'],
                                isset($attribute['value']) ? 'child-' . $attribute['value'] : ''
                            );
                        }
                    }
                    $models[] = $widgetFrontModel;
                }
            }
        }

        return $models;
    }

    /**
     * @param string $attributeName
     * @param int|null $idProduct
     *
     * @return string|int|null
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    protected function getProductIdentifier($attributeName, $idProduct)
    {
        if (empty($idProduct)) {
            return '';
        }

        $product = new Product($idProduct, false, $this->getIdLang(), $this->getIdShop());

        switch ($attributeName) {
            case ProductIdentifier::MPN:
                if (version_compare(_PS_VERSION_, '1.7.7.0', '<')) {
                    return $product->id;
                }

                return !empty($product->mpn) ? $product->mpn : $product->id;
            case ProductIdentifier::GTIN:
                $gtin = Configuration::get(Trustedshopseasyintegration::GTIN_TYPE);
                switch ($gtin) {
                    case GtinType::EAN13:
                    default:
                        return !empty($product->ean13) ? $product->ean13 : $product->id;
                }
                break;
            case ProductIdentifier::SKU:
            default:
                $sku = Configuration::get(Trustedshopseasyintegration::SKU_TYPE);
                switch ($sku) {
                    case SkuType::ID:
                        return $product->id;
                    case SkuType::SKU:
                    default:
                        return !empty($product->reference) ? $product->reference : $product->id;
                }
        }
    }

    /**
     * @return int
     */
    public function getIdShop()
    {
        return $this->idShop;
    }

    /**
     * @param int $idShop
     *
     * @return WidgetHandler
     */
    public function setIdShop($idShop)
    {
        $this->idShop = $idShop;

        return $this;
    }

    /**
     * @return int
     */
    public function getIdLang()
    {
        return $this->idLang;
    }

    /**
     * @param int $idLang
     *
     * @return WidgetHandler
     */
    public function setIdLang($idLang)
    {
        $this->idLang = $idLang;

        return $this;
    }
}
