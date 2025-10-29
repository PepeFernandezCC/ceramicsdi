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
use Language;
use Shop;
use Tools;
use TrustedshopsAddon\Model\Constant\GtinType;
use TrustedshopsAddon\Model\Constant\ProductIdentifier;
use TrustedshopsAddon\Model\Constant\SkuType;
use TrustedshopsAddon\Model\Constant\WidgetLocation;
use TrustedshopsAddon\Model\ExportOrders\ExportOrderModel;
use TrustedshopsAddon\Model\ExportOrders\OrdersModel;
use TrustedshopsAddon\Model\MappedChannel\MappedChannelModel;
use TrustedshopsAddon\Model\MappedChannel\MappedChannelsModel;
use TrustedshopsAddon\Model\OrderStatus\OrderStatusModel;
use TrustedshopsAddon\Model\OrderStatusEvents\OrderStatusEventsModel;
use TrustedshopsAddon\Model\ProductIdentifiers\ProductIdentifierModel;
use TrustedshopsAddon\Model\ProductIdentifiers\ProductIdentifiersModel;
use TrustedshopsAddon\Model\ProductReview\ProductReviewModel;
use TrustedshopsAddon\Model\SalesChannel\SalesChannel;
use TrustedshopsAddon\Model\SalesChannel\SalesChannels;
use TrustedshopsAddon\Model\Trustbadge\TrustbadgeModel;
use TrustedshopsAddon\Model\Widget\WidgetLocationModel;
use TrustedshopsAddon\Model\Widget\WidgetLocationsModel;
use TrustedshopsAddon\Model\Widget\WidgetModel;
use TrustedshopsAddon\Repository\ChannelRepository;
use TrustedshopsChannel;
use TrustedshopsClasslib\Utils\Translate\TranslateTrait;
use Trustedshopseasyintegration;
use Validate;

class ChannelService
{
    use TranslateTrait;

    /**
     * @var ChannelRepository
     */
    protected $channelRepository;

    /**
     * @var CredentialsService
     */
    protected $credentialsService;

    /**
     * @var OrderProductService
     */
    protected $orderProductService;

    /**
     * @param ChannelRepository $channelRepository
     * @param CredentialsService $credentialsService
     * @param OrderProductService $orderProductService
     */
    public function __construct(ChannelRepository $channelRepository,
                                CredentialsService $credentialsService,
                                OrderProductService $orderProductService)
    {
        $this->channelRepository = $channelRepository;
        $this->credentialsService = $credentialsService;
        $this->orderProductService = $orderProductService;
    }

    /**
     * @return MappedChannelsModel
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getMappedChannels()
    {
        $credentials = $this->credentialsService->getCredentials();

        $channels = $this->channelRepository->getChannelsByClientId($credentials->getClientId());
        $mappedChannelsModel = new MappedChannelsModel();

        if (empty($channels)) {
            return $mappedChannelsModel;
        }

        $resultMappedChannels = [];
        foreach ($channels as $channel) {
            $channel = new TrustedshopsChannel($channel[TrustedshopsChannel::$definition['primary']]);
            $mappedChannelModel = $this->getMappedChannelModelFromChannel($channel);

            $resultMappedChannels[] = $mappedChannelModel;
        }

        $mappedChannelsModel->setMappedChannels($resultMappedChannels);

        return $mappedChannelsModel;
    }

    /**
     * @param MappedChannelsModel $mappedChannelsModel
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function saveMappedChannels(MappedChannelsModel $mappedChannelsModel)
    {
        $credentials = $this->credentialsService->getCredentials();
        $result = true;

        /** @var MappedChannelModel $mappedChannel */
        foreach ($mappedChannelsModel->getMappedChannels() as $mappedChannel) {
            list($idShop, $idLang) = $this->getShopLangFromSalesReference($mappedChannel->getSalesChannelRef());
            $channelId = $this->channelRepository->getChannelByClientIdLangShop(
                $credentials->getClientId(),
                $idShop,
                $idLang
            );

            $channel = new TrustedshopsChannel($channelId);
            if (!empty($channelId)
                && Validate::isLoadedObject($channel)
                && $channel->e_trusted_channel_ref == $mappedChannel->getETrustedChannelRef()) {
                continue;
            }

            $channel->id_client = $credentials->getClientId();
            $channel->id_lang = $idLang;
            $channel->id_shop = $idShop;
            $channel->e_trusted_channel_ref = $mappedChannel->getETrustedChannelRef();
            $channel->e_trusted_name = $mappedChannel->getETrustedName();
            $channel->e_trusted_locale = $mappedChannel->getETrustedLocale();
            $channel->e_trusted_account_ref = $mappedChannel->getETrustedAccountRef();
            $channel->e_trusted_url = $mappedChannel->getETrustedUrl();
            $channel->id_trustbadge = '';
            $channel->trustbadge_config = '';
            $channel->widget_config = '';
            $channel->products_review_invites = true;
            $channel->order_status_events = true;
            $result &= $channel->save();
        }

        $result &= $this->cleanNotMappedChannels($mappedChannelsModel);

        return (bool) $result;
    }

    protected function cleanNotMappedChannels(MappedChannelsModel $mappedChannelsModel)
    {
        $credentials = $this->credentialsService->getCredentials();
        $salesChannels = $this->getSalesChannels();
        $result = true;

        foreach ($salesChannels->getSalesChannels() as $salesChannel) {
            $found = false;
            foreach ($mappedChannelsModel->getMappedChannels() as $mappedChannel) {
                if ($mappedChannel->getSalesChannelRef() == $salesChannel->getId()) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                list($idShop, $idLang) = $this->getShopLangFromSalesReference($salesChannel->getId());
                $channelId = $this->channelRepository->getChannelByClientIdLangShop(
                    $credentials->getClientId(),
                    $idShop,
                    $idLang
                );
                if (!empty($channelId)) {
                    $channelObj = new TrustedshopsChannel($channelId);
                    $result &= $channelObj->delete();
                }
            }
        }

        return $result;
    }

    /**
     * @return SalesChannels
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getSalesChannels()
    {
        $shops = Shop::getShops(false, null, true);

        $resultSalesChannels = [];
        foreach ($shops as $idShop) {
            $languages = Language::getLanguages(false, $idShop, true);
            foreach ($languages as $idLang) {
                $salesChannel = (new SalesChannel())
                    ->setId($this->getSalesChannelId($idShop, $idLang))
                    ->setName($this->getSalesChannelName($idShop, $idLang))
                    ->setLocale($this->getSalesChannelLocale($idLang))
                    ->setUrl($this->getSalesChannelUrl($idShop));

                $resultSalesChannels[] = $salesChannel;
            }
        }

        return (new SalesChannels())
            ->setSalesChannels($resultSalesChannels);
    }

    /**
     * @param string $idChannel
     * @param string $salesChannelRef
     *
     * @return ProductReviewModel
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getProductReviewForChannel($idChannel, $salesChannelRef)
    {
        $credentials = $this->credentialsService->getCredentials();
        $productReviewModel = (new ProductReviewModel())
            ->setSalesChannelRef($salesChannelRef)
            ->setIdChannel($idChannel);

        list($idShop, $idLang) = $this->getShopLangFromSalesReference($productReviewModel->getSalesChannelRef());

        $channels = $this->channelRepository->getChannelsByChannelId(
            $credentials->getClientId(),
            $idChannel,
            $idLang,
            $idShop
        );

        if (empty($channels)) {
            return $productReviewModel;
        }

        $productReviewModel->setIsProductReviewActivated(true);

        return $productReviewModel;
    }

    /**
     * @param ProductReviewModel $productReviewModel
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function saveProductReviewForChannel(ProductReviewModel $productReviewModel)
    {
        $credentials = $this->credentialsService->getCredentials();
        list($idShop, $idLang) = $this->getShopLangFromSalesReference($productReviewModel->getSalesChannelRef());

        $channels = $this->channelRepository->getChannelsByChannelId(
            $credentials->getClientId(),
            $productReviewModel->getIdChannel(),
            $idLang,
            $idShop
        );

        if (empty($channels)) {
            return false;
        }

        $channelResult = reset($channels);
        $channel = new TrustedshopsChannel($channelResult[TrustedshopsChannel::$definition['primary']]);
        $channel->products_review_invites = $productReviewModel->isProductReviewActivated();

        return $channel->save();
    }

    /**
     * @param string $idChannel
     * @param string $salesChannelRef
     *
     * @return OrderStatusEventsModel
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getOrderStatusEventsForChannel($idChannel, $salesChannelRef)
    {
        $credentials = $this->credentialsService->getCredentials();
        $orderStatusEventsModel = (new OrderStatusEventsModel())
            ->setSalesChannelRef($salesChannelRef)
            ->setETrustedChannelRef($idChannel);

        list($idShop, $idLang) = $this->getShopLangFromSalesReference($orderStatusEventsModel->getSalesChannelRef());

        $channels = $this->channelRepository->getChannelsByChannelId(
            $credentials->getClientId(),
            $idChannel,
            $idLang,
            $idShop
        );

        if (empty($channels)) {
            return $orderStatusEventsModel;
        }

        $channel = reset($channels);
        $channelObj = new TrustedshopsChannel($channel[TrustedshopsChannel::$definition['primary']]);
        $orderStatusEventsModel->setIsOrderStatusEventsActivated((bool) $channelObj->order_status_events);

        return $orderStatusEventsModel;
    }

    /**
     * @param string $idChannel
     * @param string $salesChannelRef
     *
     * @return OrderStatusModel
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getOrderStatusForChannel($idChannel, $salesChannelRef)
    {
        $credentials = $this->credentialsService->getCredentials();
        $orderStatusEventsModel = (new OrderStatusModel())
            ->setSalesChannelRef($salesChannelRef)
            ->setETrustedChannelRef($idChannel);

        list($idShop, $idLang) = $this->getShopLangFromSalesReference($orderStatusEventsModel->getSalesChannelRef());

        $channels = $this->channelRepository->getChannelsByChannelId(
            $credentials->getClientId(),
            $idChannel,
            $idLang,
            $idShop
        );

        if (empty($channels)) {
            return $orderStatusEventsModel;
        }

        $status = \OrderState::getOrderStates(\Context::getContext()->language->id);
        $orderStatusEventsModel->setStatus(array_map(function ($state) {
            return [
                'ID' => $state['id_order_state'],
                'name' => $state['name'],
                'type' => 'order_status_from_prestashop',
            ];
        }, $status));

        return $orderStatusEventsModel;
    }

    /**
     * @param OrderStatusEventsModel $orderStatusEventsModel
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function saveOrderStatusEventsForChannel(OrderStatusEventsModel $orderStatusEventsModel)
    {
        $credentials = $this->credentialsService->getCredentials();
        list($idShop, $idLang) = $this->getShopLangFromSalesReference($orderStatusEventsModel->getSalesChannelRef());

        $channels = $this->channelRepository->getChannelsByChannelId(
            $credentials->getClientId(),
            $orderStatusEventsModel->getETrustedChannelRef(),
            $idLang,
            $idShop
        );

        if (empty($channels)) {
            return false;
        }

        $channelResult = reset($channels);
        $channel = new TrustedshopsChannel($channelResult[TrustedshopsChannel::$definition['primary']]);
        $channel->order_status_events = $orderStatusEventsModel->isOrderStatusEventsActivated();

        return $channel->save();
    }

    /**
     * @param TrustbadgeModel $trustbadgeModel
     *
     * @return TrustbadgeModel
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getTrustbadgeConfig(TrustbadgeModel $trustbadgeModel)
    {
        list($idShop, $idLang) = $this->getShopLangFromSalesReference($trustbadgeModel->getSalesChannelRef());

        $channel = $this->getChannelFromIdShopIdLang($idShop, $idLang);

        if (empty($channel)) {
            return $trustbadgeModel;
        }

        $config = empty($channel->trustbadge_config)
            ? null
            : $channel->trustbadge_config;
        $trustbadgeModel->setTrustbadgeConfig($config);

        return $trustbadgeModel;
    }

    /**
     * @param TrustbadgeModel $trustbadgeModel
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function saveTrustbadgeConfig(TrustbadgeModel $trustbadgeModel)
    {
        list($idShop, $idLang) = $this->getShopLangFromSalesReference($trustbadgeModel->getSalesChannelRef());

        $channel = $this->getChannelFromIdShopIdLang($idShop, $idLang);

        if (empty($channel)) {
            return false;
        }

        $channel->trustbadge_config = $trustbadgeModel->getTrustbadgeConfig();
        $channel->id_trustbadge = $trustbadgeModel->getIdTrustbadge();

        return $channel->save();
    }

    /**
     * @param WidgetModel $widgetModel
     *
     * @return WidgetModel
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getWidgetConfig(WidgetModel $widgetModel)
    {
        $credentials = $this->credentialsService->getCredentials();

        list($idShop, $idLang) = $this->getShopLangFromSalesReference($widgetModel->getSalesChannelRef());

        $channels = $this->channelRepository->getChannelsByChannelId(
            $credentials->getClientId(),
            $widgetModel->getIdChannel(),
            $idLang,
            $idShop
        );

        if (empty($channels)) {
            return $widgetModel;
        }

        $channel = reset($channels);
        $channelObj = new TrustedshopsChannel($channel[TrustedshopsChannel::$definition['primary']]);
        $config = empty($channelObj->widget_config)
            ? null
            : $channelObj->widget_config;
        $widgetModel->setConfig($config);

        return $widgetModel;
    }

    /**
     * @param WidgetModel $widgetModel
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function saveWidgetConfig(WidgetModel $widgetModel)
    {
        $credentials = $this->credentialsService->getCredentials();

        list($idShop, $idLang) = $this->getShopLangFromSalesReference($widgetModel->getSalesChannelRef());

        $channels = $this->channelRepository->getChannelsByChannelId(
            $credentials->getClientId(),
            $widgetModel->getIdChannel(),
            $idLang,
            $idShop
        );

        if (empty($channels)) {
            return false;
        }

        $channelResult = reset($channels);
        $channel = new TrustedshopsChannel($channelResult[TrustedshopsChannel::$definition['primary']]);
        $channel->widget_config = $widgetModel->getConfig();

        return $channel->save();
    }

    /**
     * @param string $idChannel
     *
     * @return MappedChannelModel|null
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function findFirstMappedChannelByChannelId($idChannel, $salesChannelRef)
    {
        $credentials = $this->credentialsService->getCredentials();

        list($idShop, $idLang) = $this->getShopLangFromSalesReference($salesChannelRef);

        $channels = $this->channelRepository->getChannelsByChannelId(
            $credentials->getClientId(),
            $idChannel,
            $idLang,
            $idShop
        );

        if (empty($channels)) {
            return null;
        }

        $channel = reset($channels);
        $channel = new TrustedshopsChannel($channel[TrustedshopsChannel::$definition['primary']]);

        return $this->getMappedChannelModelFromChannel($channel);
    }

    /**
     * @return bool
     */
    public function clearChannels()
    {
        return $this->channelRepository->clearChannels();
    }

    public function getExportedOrders(ExportOrderModel $exportOrderModel)
    {
        $credentials = $this->credentialsService->getCredentials();
        list($idShop, $idLang) = $this->getShopLangFromSalesReference($exportOrderModel->getSalesChannelRef());
        $orderProductsModel = new OrdersModel();
        $channels = $this->channelRepository->getChannelsByChannelId(
            $credentials->getClientId(),
            $exportOrderModel->getIdChannel(),
            $idLang,
            $idShop
        );

        if (empty($channels)) {
            return $orderProductsModel;
        }

        $channelItem = reset($channels);
        $channel = new TrustedshopsChannel($channelItem[TrustedshopsChannel::$definition['primary']]);
        $resultOrderProducts = $this->orderProductService->getOrderProducts(
            $channel->id_shop,
            $channel->id_lang,
            $exportOrderModel->getNumberOfDays(),
            (bool) $exportOrderModel->getIncludeProductData()
        );

        $orderProductsModel->setOrderProducts($resultOrderProducts);

        return $orderProductsModel;
    }

    /**
     * @return ProductIdentifiersModel
     */
    public function getProductIdentifiers()
    {
        $productIdentifiers = new ProductIdentifiersModel();
        $identifiers = [];
        $skuType = Configuration::get(Trustedshopseasyintegration::SKU_TYPE);
        $skuIdentifier = new ProductIdentifierModel();
        $skuIdentifier->setId(ProductIdentifier::SKU);
        switch ($skuType) {
            case SkuType::ID:
                $skuIdentifier->setName($this->l('ID'));
                break;
            case SkuType::SKU:
            default:
                $skuIdentifier->setName($this->l('Reference'));
                break;
        }

        $identifiers[] = $skuIdentifier;

        $gtinType = Configuration::get(Trustedshopseasyintegration::GTIN_TYPE);
        $gtinIdentifier = new ProductIdentifierModel();
        $gtinIdentifier->setId(ProductIdentifier::GTIN);
        switch ($gtinType) {
            case GtinType::EAN13:
            default:
                $gtinIdentifier->setName($this->l('EAN13'));
                break;
        }

        $identifiers[] = $gtinIdentifier;

        $mpnIdentifier = (new ProductIdentifierModel())
            ->setId(ProductIdentifier::MPN)
            ->setName($this->l('MPN'));
        $identifiers[] = $mpnIdentifier;

        return $productIdentifiers
            ->setProductIdentifiers($identifiers);
    }

    public function getWidgetLocations()
    {
        return (new WidgetLocationsModel())
            ->setWidgetLocations([
                (new WidgetLocationModel())
                    ->setId(WidgetLocation::HEADER)
                    ->setName($this->l('Header')),
                (new WidgetLocationModel())
                    ->setId(WidgetLocation::FOOTER)
                    ->setName($this->l('Footer')),
                (new WidgetLocationModel())
                    ->setId(WidgetLocation::HOME_PAGE)
                    ->setName($this->l('Home page')),
                (new WidgetLocationModel())
                    ->setId(WidgetLocation::LEFT_RIGHT_MARGIN)
                    ->setName($this->l('Left/Right margin')),
                (new WidgetLocationModel())
                    ->setId(WidgetLocation::PRODUCT_PAGE)
                    ->setName($this->l('Product page')),
                (new WidgetLocationModel())
                    ->setId(WidgetLocation::PRODUCT_DESCRIPTION)
                    ->setName($this->l('Product description')),
                (new WidgetLocationModel())
                    ->setId(WidgetLocation::PRODUCT_NAME)
                    ->setName($this->l('Product Name')),
                (new WidgetLocationModel())
                    ->setId(WidgetLocation::PRODUCT_LIST)
                    ->setName($this->l('Product list')),
            ]);
    }

    /**
     * @param int $idShop
     * @param int $idLang
     *
     * @return TrustedshopsChannel|null
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getChannelFromIdShopIdLang($idShop, $idLang)
    {
        $credentials = $this->credentialsService->getCredentials();

        $idChannel = $this->channelRepository->getChannelByClientIdLangShop(
            $credentials->getClientId(),
            $idShop,
            $idLang
        );

        if (empty($idChannel)) {
            return null;
        }

        return new TrustedshopsChannel($idChannel);
    }

    // region Utils

    /**
     * @param TrustedshopsChannel $channel
     *
     * @return MappedChannelModel
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    protected function getMappedChannelModelFromChannel(TrustedshopsChannel $channel)
    {
        return (new MappedChannelModel())
            ->setETrustedChannelRef($channel->e_trusted_channel_ref)
            ->setETrustedLocale($channel->e_trusted_locale)
            ->setETrustedName($channel->e_trusted_name)
            ->setETrustedUrl($channel->e_trusted_url)
            ->setETrustedAccountRef($channel->e_trusted_account_ref)
            ->setSalesChannelRef($this->getSalesChannelId($channel->id_shop, $channel->id_lang))
            ->setSalesChannelLocale($this->getSalesChannelLocale($channel->id_lang))
            ->setSalesChannelName($this->getSalesChannelName($channel->id_shop, $channel->id_lang))
            ->setSalesChannelUrl($this->getSalesChannelUrl($channel->id_shop));
    }

    /**
     * @param int $idLang
     *
     * @return string
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    protected function getSalesChannelLocale($idLang)
    {
        $language = new Language($idLang);

        if (!Validate::isLoadedObject($language)) {
            return '';
        }

        return $language->language_code;
    }

    /**
     * @param int $idShop
     * @param int $idLang
     *
     * @return string
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    protected function getSalesChannelName($idShop, $idLang)
    {
        $language = new Language($idLang);
        $isoLang = '';
        $shop = new Shop($idShop);
        $shopName = '';

        if (Validate::isLoadedObject($language)) {
            $isoLang = $language->iso_code;
        }

        if (Validate::isLoadedObject($shop)) {
            $shopName = $shop->name;
        }

        return $shopName . ' ' . $isoLang;
    }

    /**
     * @param int $idShop
     *
     * @return string
     */
    protected function getSalesChannelUrl($idShop)
    {
        $shop = new Shop($idShop);
        if (!Validate::isLoadedObject($shop)) {
            return '';
        }

        $baseUrl = $shop->getBaseURL(true);

        return !empty($baseUrl) ? $baseUrl : '';
    }

    /**
     * @param int $idShop
     * @param int $idLang
     *
     * @return string
     */
    protected function getSalesChannelId($idShop, $idLang)
    {
        $shopLang = (int) $idShop . '-' . (int) $idLang;

        return 'shop-' . $shopLang . '-' . Tools::encrypt($shopLang);
    }

    /**
     * @param string $salesReference
     *
     * @return int[]
     */
    protected function getShopLangFromSalesReference($salesReference)
    {
        $data = explode('-', $salesReference);

        return [
            empty($data[1]) ? 0 : (int) $data[1],
            empty($data[2]) ? 0 : (int) $data[2],
        ];
    }

    // endregion
}
