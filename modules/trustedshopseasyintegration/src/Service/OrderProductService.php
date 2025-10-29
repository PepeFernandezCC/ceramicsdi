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

namespace TrustedshopsAddon\Service;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Configuration;
use Context;
use Image;
use Manufacturer;
use Product;
use TrustedshopsAddon\Model\Constant\GtinType;
use TrustedshopsAddon\Model\Constant\SkuType;
use TrustedshopsAddon\Model\ExportOrders\OrderModel;
use TrustedshopsAddon\Model\ExportOrders\OrderProductModel;
use TrustedshopsAddon\Repository\OrderProductRepository;
use Trustedshopseasyintegration;

class OrderProductService
{
    /**
     * @var OrderProductRepository
     */
    protected $orderProductRepository;

    /**
     * @param OrderProductRepository $orderProductRepository
     */
    public function __construct(OrderProductRepository $orderProductRepository)
    {
        $this->orderProductRepository = $orderProductRepository;
    }

    /**
     * @param int $idShop
     * @param int $idLang
     * @param int $days
     * @param bool $withProductData
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getOrderProducts($idShop, $idLang, $days, $withProductData = true)
    {
        if ($days < 0) {
            return [];
        }

        $orderDetails = $this->orderProductRepository->getOrderDetails($idShop, $idLang, $days, $withProductData);
        $result = [];

        foreach ($orderDetails as $orderDetail) {
            if ($withProductData) {
                $orderProductModel = new OrderProductModel();
                $product = new Product($orderDetail['product_id'], false, $idLang, $idShop);
                $productLink = Context::getContext()->link->getProductLink(
                    $product,
                    null,
                    null,
                    null,
                    (int) $idLang,
                    (int) $idShop
                );
                $brandName = Manufacturer::getNameById($product->id_manufacturer);
                $coverImage = Image::getCover($product->id);
                if (!empty($coverImage) && !empty($coverImage['id_image'])) {
                    $imageLink = Context::getContext()->link->getImageLink(
                        $product->link_rewrite,
                        $coverImage['id_image']
                    );
                } else {
                    $imageLink = '';
                }

                $orderProductModel
                    ->setProductName($product->name)
                    ->setProductSku($this->getSkuFromOrderDetail($product))
                    ->setProductGtin($this->getProductGtinFromOrderDetail($product))
                    ->setProductMpn($this->getProductMpnFromOrderDetail($product))
                    ->setProductImageUrl($imageLink)
                    ->setProductBrand($brandName)
                    ->setProductUrl($productLink);
            } else {
                $orderProductModel = new OrderModel();
            }

            $orderProductModel->setEmail($orderDetail['email'])
                ->setFirstName($orderDetail['firstname'])
                ->setLastName($orderDetail['lastname'])
                ->setReference($orderDetail['reference'])
                ->setTransactionDate($this->formatDate($orderDetail['transaction_date']));

            $result[] = $orderProductModel;
        }

        return $result;
    }

    /**
     * @param string $date
     *
     * @return array|string|string[]
     */
    protected function formatDate($date)
    {
        return str_replace(' ', 'T', trim($date));
    }

    /**
     * @param Product $product
     *
     * @return string|int|null
     */
    protected function getSkuFromOrderDetail(Product $product)
    {
        $skuType = Configuration::get(Trustedshopseasyintegration::SKU_TYPE);
        switch ($skuType) {
            case SkuType::ID:
                return $product->id;
            case SkuType::SKU:
            default:
                return !empty($product->reference) ? $product->reference : $product->id;
        }
    }

    /**
     * @param Product $product
     *
     * @return string
     */
    protected function getProductGtinFromOrderDetail(Product $product)
    {
        $gtinType = Configuration::get(Trustedshopseasyintegration::GTIN_TYPE);

        switch ($gtinType) {
            case GtinType::EAN13:
            default:
                return $product->ean13;
        }
    }

    /**
     * @param Product $product
     *
     * @return string|int|null
     */
    protected function getProductMpnFromOrderDetail(Product $product)
    {
        if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
            return $product->mpn;
        }

        return $product->id;
    }
}
