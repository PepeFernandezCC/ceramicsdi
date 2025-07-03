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

use Address;
use Carrier;
use Configuration;
use Customer;
use Exception;
use Language;
use Order;
use TrustedshopsAddon\API\Client;
use TrustedshopsAddon\API\Logger\ApiLogger;
use TrustedshopsAddon\API\Logger\LogModel;
use TrustedshopsAddon\API\Model\Events\ChannelModel;
use TrustedshopsAddon\API\Model\Events\CustomerModel;
use TrustedshopsAddon\API\Model\Events\EventsModel;
use TrustedshopsAddon\API\Model\Events\ProductItemModel;
use TrustedshopsAddon\API\Model\Events\TransactionModel;
use TrustedshopsAddon\API\Request\EventsRequest;
use TrustedshopsAddon\Handler\OrderConfirmationHandler;
use TrustedshopsChannel;
use Trustedshopseasyintegration;
use Validate;

class OrderStatusService
{
    const DEFAULT_NB_DAYS = 3;

    const DEFAULT_ORDER_STATUS = _PS_OS_SHIPPING_;

    /**
     * @var ChannelService
     */
    protected $channelService;

    /**
     * @var CredentialsService
     */
    protected $credentialsService;

    /**
     * @var ApiLogger
     */
    protected $apiLogger;

    /**
     * @param ChannelService $channelService
     * @param CredentialsService $credentialsService
     */
    public function __construct(ChannelService $channelService, CredentialsService $credentialsService)
    {
        $this->channelService = $channelService;
        $this->credentialsService = $credentialsService;
        $this->apiLogger = ApiLogger::getInstance();
    }

    public function sendOrderStatusEvent(Order $order, $newOrderStatus)
    {
        $triggerStatusId = $this->getOrderStatus($order->id_carrier);

        $isSuccess = true;
        /** @var TrustedshopsChannel|null $channel */
        $channel = $this->channelService->getChannelFromIdShopIdLang($order->id_shop, $order->id_lang);
        if (empty($channel) || !Validate::isLoadedObject($channel) || !$channel->order_status_events) {
            return $isSuccess;
        }

        $logModel = (new LogModel())
            ->setOrderNumber($order->reference)
            ->setConfiguredTriggerStatus($triggerStatusId)
            ->setNewOrderStatus($newOrderStatus);

        if ((int) $triggerStatusId === (int) $newOrderStatus) {
            try {
                $body = $this->buildRequestBody($order, $channel);
                $client = new Client();
                $credentials = $this->credentialsService->getCredentials();
                $client->setCredential($credentials->getClientId(), $credentials->getClientSecret());
                $request = (new EventsRequest())
                    ->setModel($body);
                $response = $client->sendRequest($request);
            } catch (Exception $e) {
                $isSuccess = false;
            } finally {
                if (isset($response)) {
                    $logModel->setResponse($response);
                }
            }
        }
        $this->apiLogger->log($logModel);

        return $isSuccess;
    }

    protected function getConfig()
    {
        $config = Configuration::get(Trustedshopseasyintegration::ORDER_STATUS_EVENTS_CONFIG);
        if (empty($config)) {
            return [];
        }
        $config = json_decode($config, true);

        return empty($config) ? [] : $config;
    }

    /**
     * @param int $idCarrier
     *
     * @return int
     */
    protected function getNbDays($idCarrier)
    {
        $carrier = new Carrier($idCarrier);
        $idCarrierReference = $carrier->id_reference;
        $config = $this->getConfig();

        if (isset($config[$idCarrierReference]) && isset($config[$idCarrierReference]['reviews_nb_days'])) {
            return $config[$idCarrierReference]['reviews_nb_days'];
        }

        if (isset($config['all']) && isset($config['all']['reviews_nb_days'])) {
            return $config['all']['reviews_nb_days'];
        }

        return self::DEFAULT_NB_DAYS;
    }

    /**
     * @param int $idCarrier
     *
     * @return int
     */
    protected function getOrderStatus($idCarrier)
    {
        $carrier = new Carrier($idCarrier);
        $idCarrierReference = $carrier->id_reference;
        $config = $this->getConfig();

        if (isset($config[$idCarrierReference]) && isset($config[$idCarrierReference]['trigger_order_status_id'])) {
            return $config[$idCarrierReference]['trigger_order_status_id'];
        }

        if (isset($config['all']) && isset($config['all']['trigger_order_status_id'])) {
            return $config['all']['trigger_order_status_id'];
        }

        return self::DEFAULT_ORDER_STATUS;
    }

    protected function buildRequestBody(Order $order, TrustedshopsChannel $channel)
    {
        $language = new Language($order->id_lang);
        $customer = new Customer($order->id_customer);
        $address = new Address($order->id_address_delivery, $language->id);
        $nbDays = $this->getNbDays($order->id_carrier);

        $body = (new EventsModel())
            ->setCustomer(
                (new CustomerModel())
                    ->setFirstName($customer->firstname)
                    ->setLastName($customer->lastname)
                    ->setEmail($customer->email)
                    ->setAddress($address->address1 . ', ' . $address->city . ', ' . $address->country)
                    ->setMobile(!empty($address->phone_mobile) ? (string) $address->phone_mobile : (string) $address->phone)
                    ->setReference((string) $customer->id)
            )
            ->setChannel(
                (new ChannelModel())
                    ->setId($channel->e_trusted_channel_ref)
            )
            ->setTransaction(
                (new TransactionModel())
                    ->setReference($order->reference)
            )
            ->setEstimatedDeliveryDate(date('Y-m-d', strtotime("+$nbDays days", time())))
            ->setSystem('PrestaShop')
            ->setSystemVersion(_PS_VERSION_)
            ->setCreatedAt(date('Y-m-d\TH:i:s.000\Z', time()));

        if ($channel->products_review_invites) {
            $orderConfirmationHandler = new OrderConfirmationHandler();
            $orderModel = $orderConfirmationHandler
                ->setIdShop($order->id_shop)
                ->setIdLang($order->id_lang)
                ->handle($order->id, (bool) $channel->products_review_invites);

            $products = $orderModel->getProducts();
            $bodyProducts = [];
            foreach ($products as $product) {
                $bodyProduct = (new ProductItemModel())
                    ->setName((string) $product->getTsCheckoutProductName())
                    ->setUrl((string) $product->getTsCheckoutProductUrl())
                    ->setBrand((string) $product->getTsCheckoutProductBrand())
                    ->setGtin((string) $product->getTsCheckoutProductGTIN())
                    ->setImageUrl((string) $product->getTsCheckoutProductImageUrl())
                    ->setMpn((string) $product->getTsCheckoutProductMPN())
                    ->setSku((string) $product->getTsCheckoutProductSKU());
                $bodyProducts[] = $bodyProduct;
            }
            $body->setProducts($bodyProducts);
        }

        return $body;
    }
}
