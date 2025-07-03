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

use Cart;
use Configuration;
use Context;
use Currency;
use Customer;
use Image;
use Manufacturer;
use Order;
use Product;
use TrustedshopsAddon\Model\Constant\GtinType;
use TrustedshopsAddon\Model\Constant\SkuType;
use TrustedshopsAddon\Model\Order\OrderModel;
use TrustedshopsAddon\Model\Order\OrderProductModel;
use Trustedshopseasyintegration;

class OrderConfirmationHandler
{
    /**
     * @var int
     */
    private $idShop;

    /**
     * @var int
     */
    private $idLang;

    /**
     * @param int $idOrder
     * @param bool $hasProducts
     *
     * @return OrderModel|null
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function handle($idOrder, $hasProducts = true)
    {
        $order = new Order($idOrder);
        $customer = new Customer($order->id_customer);
        $cart = new Cart($order->id_cart);
        $currency = new Currency($order->id_currency);

        $orderModel = new OrderModel();

        $orderTotal = $order->total_products_wt + $order->total_shipping_tax_incl;

        $orderModel->setSendProducts($hasProducts)
            ->setTsCheckoutOrderNr($order->reference)
            ->setTsCheckoutBuyerEmail($customer->email)
            ->setTsCheckoutOrderAmount(\Tools::ps_round($orderTotal, 2))
            ->setTsCheckoutOrderCurrency($currency->iso_code)
            ->setTsCheckoutOrderPaymentType($order->payment);

        if ($hasProducts) {
            $orderModel->setProducts($this->getOrderProductsModel($order));
        }

        return $orderModel;
    }

    /**
     * @param Order $order
     *
     * @return array
     *
     * @throws \PrestaShopException
     */
    protected function getOrderProductsModel(Order $order)
    {
        $products = $order->getProducts();
        $products = array_map(function ($product) {
            return $product['product_id'];
        }, $products);
        $products = array_unique($products);
        $models = [];

        foreach ($products as $productId) {
            $productObj = new Product($productId, false, $this->getIdLang(), $this->getIdShop());
            $productLink = Context::getContext()->link->getProductLink(
                $productObj,
                null,
                null,
                null,
                (int) $this->getIdLang(),
                (int) $this->getIdShop()
            );
            $brandName = Manufacturer::getNameById($productObj->id_manufacturer);
            $coverImage = Image::getCover($productObj->id);
            if (!empty($coverImage)) {
                $imageLink = Context::getContext()->link->getImageLink(
                    $productObj->link_rewrite,
                    $coverImage['id_image']
                );
            } else {
                $imageLink = '';
            }

            $productModel = new OrderProductModel();
            $productModel->setTsCheckoutProductName($productObj->name)
                ->setTsCheckoutProductUrl($productLink)
                ->setTsCheckoutProductImageUrl($imageLink)
                ->setTsCheckoutProductBrand($brandName)
                ->setTsCheckoutProductGTIN($this->getProductGtinFromProduct($productObj))
                ->setTsCheckoutProductMPN($this->getProductMpnFromProduct($productObj))
                ->setTsCheckoutProductSKU($this->getSkuFromProduct($productObj));
            $models[] = $productModel;
        }

        return $models;
    }

    /**
     * @param Product $product
     *
     * @return string|int|null
     */
    protected function getSkuFromProduct(Product $product)
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
    protected function getProductGtinFromProduct(Product $product)
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
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    protected function getProductMpnFromProduct(Product $product)
    {
        if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
            return $product->mpn;
        }

        return $product->id;
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
     * @return OrderConfirmationHandler
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
     * @return OrderConfirmationHandler
     */
    public function setIdLang($idLang)
    {
        $this->idLang = $idLang;

        return $this;
    }
}
