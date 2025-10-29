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
if (!defined('_PS_VERSION_')) {
    exit;
}

use TrustedshopsAddon\Model\Credentials\CredentialsModel;
use TrustedshopsAddon\Model\ExportOrders\ExportOrderModel;
use TrustedshopsAddon\Model\MappedChannel\MappedChannelModel;
use TrustedshopsAddon\Model\MappedChannel\MappedChannelsModel;
use TrustedshopsAddon\Model\OrderStatusEvents\OrderStatusEventsModel;
use TrustedshopsAddon\Model\ProductReview\ProductReviewModel;
use TrustedshopsAddon\Model\Response\ErrorResponse;
use TrustedshopsAddon\Model\Response\SuccessNotificationResponse;
use TrustedshopsAddon\Model\Response\SuccessResponse;
use TrustedshopsAddon\Model\Trustbadge\TrustbadgeModel;
use TrustedshopsAddon\Model\Widget\WidgetModel;
use TrustedshopsAddon\Service\ChannelService;
use TrustedshopsAddon\Service\CredentialsService;
use TrustedshopsAddon\Service\OrderStatusService;
use TrustedshopsAddon\Utils\ServiceContainer;

class AdminTrustedshopseasyintegrationConfigurationAjaxController extends ModuleAdminController
{
    /**
     * @var int
     */
    public $multishop_context = 0;

    /**
     * @var CredentialsService
     */
    protected $credentialsService;

    /**
     * @var ChannelService
     */
    protected $channelService;

    /**
     * @var OrderStatusService
     */
    protected $orderService;

    public function __construct()
    {
        parent::__construct();
        $this->credentialsService = ServiceContainer::getInstance()->get(CredentialsService::class);
        $this->channelService = ServiceContainer::getInstance()->get(ChannelService::class);
        $this->orderService = ServiceContainer::getInstance()->get(OrderStatusService::class);
    }

    public function displayAjaxGetCredentials()
    {
        try {
            $credentialModel = $this->credentialsService->getCredentials();
            $response = (new SuccessResponse())
                ->setData($credentialModel);
            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            $response = (new ErrorResponse())
                ->setMessage($this->l('Failed to get credentials'));
            $this->ajaxDie(json_encode($response));
        }
    }

    public function displayAjaxSaveCredentials()
    {
        try {
            $clientId = Tools::getValue('clientId', '');
            $clientSecret = Tools::getValue('clientSecret', '');

            if (empty($clientId) || empty($clientSecret)) {
                throw new PrestaShopException('Failed to save credentials');
            }

            $credentialModel = (new CredentialsModel())
                ->setClientId(trim($clientId))
                ->setClientSecret(trim($clientSecret));

            $this->credentialsService->saveCredentials($credentialModel);
            $this->channelService->clearChannels();

            $credentialModel = $this->credentialsService->getCredentials();

            $response = (new SuccessNotificationResponse())
                ->setData($credentialModel)
                ->setMessage($this->l('Credentials saved'));

            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            $response = (new ErrorResponse())
                ->setMessage($this->l('Failed to save credentials'));
            $this->ajaxDie(json_encode($response));
        }
    }

    public function displayAjaxGetMappedChannels()
    {
        try {
            $mappedChannelsModel = $this->channelService->getMappedChannels();
            $response = (new SuccessResponse())
                ->setData($mappedChannelsModel);
            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            $response = (new ErrorResponse())
                ->setMessage($this->l('Failed to get mapped channels'));
            $this->ajaxDie(json_encode($response));
        }
    }

    public function displayAjaxGetSalesChannels()
    {
        try {
            $salesChannelsModel = $this->channelService->getSalesChannels();
            $response = (new SuccessResponse())
                ->setData($salesChannelsModel);
            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            $response = (new ErrorResponse())
                ->setMessage($this->l('Failed to get sales channels'));
            $this->ajaxDie(json_encode($response));
        }
    }

    public function displayAjaxGetProductReview()
    {
        try {
            $idChannel = Tools::getValue('idChannel', false);
            $salesChannelRef = Tools::getValue('salesChannelRef', false);
            if (empty($idChannel) || empty($salesChannelRef)) {
                throw new PrestaShopException('Failed to load product review');
            }

            $productReviewModel = $this->channelService->getProductReviewForChannel($idChannel, $salesChannelRef);
            $response = (new SuccessResponse())
                ->setData(
                    $productReviewModel->isProductReviewActivated()
                        ? $this->channelService->findFirstMappedChannelByChannelId(
                            $productReviewModel->getIdChannel(),
                            $productReviewModel->getSalesChannelRef()
                        )
                        : null
                );
            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            $response = (new ErrorResponse())
                ->setMessage($this->l('Failed to load product review'));
            $this->ajaxDie(json_encode($response));
        }
    }

    public function displayAjaxGetTrustbadgeConfig()
    {
        try {
            $idTrustbadge = Tools::getValue('idTrustbadge', false);
            $salesChannelRef = Tools::getValue('salesChannelRef', false);
            if (empty($idTrustbadge) || empty($salesChannelRef)) {
                throw new PrestaShopException('Failed to load trustbadge');
            }

            $trustbadgeModel = (new TrustbadgeModel())
                ->setSalesChannelRef($salesChannelRef)
                ->setIdTrustbadge($idTrustbadge);

            $trustbadgeModel = $this->channelService->getTrustbadgeConfig($trustbadgeModel);
            $response = (new SuccessResponse())
                ->setData($trustbadgeModel);
            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            $response = (new ErrorResponse())
                ->setMessage($this->l('Failed to load trustbadge'));
            $this->ajaxDie(json_encode($response));
        }
    }

    public function displayAjaxSaveTrustbadgeConfig()
    {
        try {
            $idTrustbadge = Tools::getValue('idTrustbadge', false);
            $salesChannelRef = Tools::getValue('salesChannelRef', false);
            $config = Tools::getValue('config', false);
            if (empty($salesChannelRef) || empty($idTrustbadge) || empty($config)) {
                throw new PrestaShopException('Failed to save trustbadge');
            }

            $trustbadgeModel = (new TrustbadgeModel())
                ->setSalesChannelRef($salesChannelRef)
                ->setIdTrustbadge($idTrustbadge)
                ->setTrustbadgeConfig($config);

            $trustbadgeSaveResult = $this->channelService->saveTrustbadgeConfig($trustbadgeModel);
            $response = (new SuccessNotificationResponse())
                ->setData($trustbadgeSaveResult)
                ->setMessage($this->l('Trustbadge configuration saved successfully'));
            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            $response = (new ErrorResponse())
                ->setMessage($this->l('Failed to save trustabadge config'));
            $this->ajaxDie(json_encode($response));
        }
    }

    public function displayAjaxGetWidget()
    {
        try {
            $idChannel = Tools::getValue('idChannel', false);
            $salesChannelRef = Tools::getValue('salesChannelRef', false);
            if (empty($idChannel) || empty($salesChannelRef)) {
                throw new PrestaShopException('Failed to load widget');
            }

            $widgetModel = (new WidgetModel())
                ->setSalesChannelRef($salesChannelRef)
                ->setIdChannel($idChannel);

            $widgetModel = $this->channelService->getWidgetConfig($widgetModel);
            $response = (new SuccessResponse())
                ->setData($widgetModel);
            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            $response = (new ErrorResponse())
                ->setMessage($this->l('Failed to load widget'));
            $this->ajaxDie(json_encode($response));
        }
    }

    public function displayAjaxSaveWidget()
    {
        try {
            $idChannel = Tools::getValue('idChannel', false);
            $salesChannelRef = Tools::getValue('salesChannelRef', false);
            $config = Tools::getValue('config', false);
            if (empty($idChannel) || empty($salesChannelRef) || empty($config)) {
                throw new PrestaShopException('Failed to save widget');
            }

            $widgetModel = (new WidgetModel())
                ->setSalesChannelRef($salesChannelRef)
                ->setIdChannel($idChannel)
                ->setConfig($config);

            $widgetModel = $this->channelService->saveWidgetConfig($widgetModel);
            $response = (new SuccessNotificationResponse())
                ->setData($widgetModel)
                ->setMessage($this->l('Widget configuration saved successfully'));
            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            $response = (new ErrorResponse())
                ->setMessage($this->l('Failed to save widget config'));
            $this->ajaxDie(json_encode($response));
        }
    }

    public function displayAjaxSaveProductReview()
    {
        try {
            $idChannel = Tools::getValue('idChannel', false);
            $salesChannelRef = Tools::getValue('salesChannelRef', false);
            $isActive = Tools::getValue('isActive') == 'true';
            if (empty($idChannel) || empty($salesChannelRef)) {
                throw new PrestaShopException('Failed to set product review value');
            }

            $productReviewModel = (new ProductReviewModel())
                ->setIdChannel($idChannel)
                ->setSalesChannelRef($salesChannelRef)
                ->setIsProductReviewActivated($isActive);

            $productReviewModel = $this->channelService->saveProductReviewForChannel($productReviewModel);
            $response = (new SuccessNotificationResponse())
                ->setData($productReviewModel)
                ->setMessage(
                    $isActive
                        ? $this->l('Product review for channel activated')
                        : $this->l('Product review for channel deactivated')
                );
            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            $response = (new ErrorResponse())
                ->setMessage($this->l('Failed to save product review value'));
            $this->ajaxDie(json_encode($response));
        }
    }

    public function displayAjaxSaveMappedChannels()
    {
        try {
            $channels = Tools::getValue('channels', []);
            $mappedChannels = [];

            foreach ($channels as $channel) {
                $mappedChannels[] = (new MappedChannelModel())
                    ->build($channel);
            }

            $mappedChannelsModel = (new MappedChannelsModel())
                ->setMappedChannels($mappedChannels);

            $result = $this->channelService->saveMappedChannels($mappedChannelsModel);

            $response = (new SuccessNotificationResponse())
                ->setData($this->channelService->getMappedChannels())
                ->setMessage($this->l('Mapped channels saved'));

            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            $response = (new ErrorResponse())
                ->setMessage($this->l('Failed to save mapped channels'));
            $this->ajaxDie(json_encode($response));
        }
    }

    public function displayAjaxDisconnect()
    {
        try {
            $removeCredentials = $this->credentialsService->removeCredentials();
            $removeChannels = $this->channelService->clearChannels();
            $response = (new SuccessResponse())
                ->setData($removeCredentials && $removeChannels);
            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            $response = (new ErrorResponse())
                ->setMessage($this->l('Failed to disconnect'));
            $this->ajaxDie(json_encode($response));
        }
    }

    public function displayAjaxGetOrders()
    {
        try {
            $idChannel = Tools::getValue('idChannel', false);
            $salesChannelRef = Tools::getValue('salesChannelRef', false);
            $numberOfDays = (int) Tools::getValue('numberOfDays', 0);
            $includeProductData = Tools::getValue('includeProductData', 'false');

            if (empty($idChannel) || empty($salesChannelRef)) {
                throw new PrestaShopException('Failed to export orders');
            }

            $exportModel = (new ExportOrderModel())
                ->setIncludeProductData($includeProductData === 'false' ? false : true)
                ->setIdChannel($idChannel)
                ->setSalesChannelRef($salesChannelRef)
                ->setNumberOfDays($numberOfDays);

            $orderProducts = $this->channelService->getExportedOrders($exportModel);

            $response = (new SuccessResponse())
                ->setData($orderProducts);
            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            $response = (new ErrorResponse())
                ->setMessage($this->l('Failed to export orders'));
            $this->ajaxDie(json_encode($response));
        }
    }

    public function displayAjaxGetOrderStatusEvents()
    {
        try {
            $idChannel = Tools::getValue('idChannel', false);
            $salesChannelRef = Tools::getValue('salesChannelRef', false);
            if (empty($idChannel) || empty($salesChannelRef)) {
                throw new PrestaShopException('Failed to load order status events state');
            }

            $orderStatusEventsModel = $this->channelService->getOrderStatusEventsForChannel($idChannel, $salesChannelRef);
            $response = (new SuccessResponse())
                ->setData($orderStatusEventsModel->isOrderStatusEventsActivated());
            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            $response = (new ErrorResponse())
                ->setMessage($this->l('Failed to load order status events state'));
            $this->ajaxDie(json_encode($response));
        }
    }

    public function displayAjaxGetOrderStatusEvent()
    {
        try {
            $idChannel = Tools::getValue('idChannel', false);
            $salesChannelRef = Tools::getValue('salesChannelRef', false);
            if (empty($idChannel) || empty($salesChannelRef)) {
                throw new PrestaShopException('Failed to load order status events state');
            }

            $orderStatusModel = $this->channelService->getOrderStatusForChannel($idChannel, $salesChannelRef);
            $response = (new SuccessResponse())
                ->setData($orderStatusModel->getStatus());

            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            $response = (new ErrorResponse())
                ->setMessage($this->l('Failed to load order status Event'));
            $this->ajaxDie(json_encode($response));
        }
    }

    public function displayAjaxGetUserOrderStatus()
    {
        try {
            $idChannel = Tools::getValue('idChannel', false);
            $salesChannelRef = Tools::getValue('salesChannelRef', false);
            if (empty($idChannel) || empty($salesChannelRef)) {
                throw new PrestaShopException('Failed to load order status events state');
            }

            $orderStatus = $this->orderService->getOrderStatus(true, $idChannel);

            $response = (new SuccessResponse())->setData($orderStatus);
            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            $response = (new ErrorResponse())
                ->setMessage($this->l('Failed to load order status Event'));
            $this->ajaxDie(json_encode($response));
        }
    }

    public function displayAjaxSaveOrderStatusEvents()
    {
        try {
            $idChannel = Tools::getValue('idChannel', false);
            $salesChannelRef = Tools::getValue('salesChannelRef', false);
            $isActive = Tools::getValue('isActive') == 'true';
            if (empty($idChannel) || empty($salesChannelRef)) {
                throw new PrestaShopException('Failed to set order status events state');
            }

            $orderStatusEventsModel = (new OrderStatusEventsModel())
                ->setETrustedChannelRef($idChannel)
                ->setSalesChannelRef($salesChannelRef)
                ->setIsOrderStatusEventsActivated($isActive);

            $orderStatusEventsModel = $this->channelService->saveOrderStatusEventsForChannel($orderStatusEventsModel);
            $response = (new SuccessNotificationResponse())
                ->setData($orderStatusEventsModel)
                ->setMessage(
                    $isActive
                        ? $this->l('Order status events for channel activated')
                        : $this->l('Order status events for channel deactivated')
                );
            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            $response = (new ErrorResponse())
                ->setMessage($this->l('Failed to save order status events state'));
            $this->ajaxDie(json_encode($response));
        }
    }

    public function displayAjaxSaveOrderStatusProductsServices()
    {
        try {
            $idChannel = Tools::getValue('idChannel', false);
            $salesChannelRef = Tools::getValue('salesChannelRef', false);
            $activeStatus = Tools::getValue('activeStatus', false);
            if (empty($idChannel) || empty($salesChannelRef)) {
                throw new PrestaShopException('Failed to load order status events state');
            }

            $this->orderService->saveOrderStatus($activeStatus, $idChannel);

            $response = (new SuccessNotificationResponse())
                ->setData($activeStatus)
                ->setMessage($this->l('Order status for product and service successfully saved'));

            $this->ajaxDie(json_encode($response));
        } catch (Exception $e) {
            $response = (new ErrorResponse())->setMessage($this->l('Failed to save order status for product and service'));
            $this->ajaxDie(json_encode($response));
        }
    }

    public function displayAjaxSendInvite()
    {
        /** @var TrustedshopsAddon\Service\OrderStatusService $orderStatusService */
        $orderStatusService = ServiceContainer::getInstance()->get(TrustedshopsAddon\Service\OrderStatusService::class);

        $response = new ErrorResponse();
        try {
            $order = new Order(Tools::getValue('send_invite_order'));
            if (Validate::isLoadedObject($order)) {
                $response = new SuccessResponse();
                $responseMessage = $orderStatusService->sendOrderStatusEvent($order, (int) $order->current_state, true);
                $response->setData($responseMessage);
            } else {
                $responseMessage = sprintf($this->l('Order do not exist : %s'), Tools::getValue('send_invite_order'));
            }
        } catch (Exception $ex) {
            $responseMessage = 'Error on launching - ' . $ex->getTraceAsString();
        }
        if ($response instanceof ErrorResponse) {
            $response->setMessage($responseMessage);
        }
        $this->ajaxDie(json_encode($response));
    }

    /**
     * {@inheritdoc}
     */
    protected function ajaxDie($value = null, $controller = null, $method = null)
    {
        header('Content-Type: application/json');
        parent::ajaxDie($value, $controller, $method);
    }
}
