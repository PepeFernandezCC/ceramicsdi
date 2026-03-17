<?php

declare(strict_types=1);

namespace Revolut\Plugin\Services\Payment;

use Exception;
use Revolut\Plugin\Infrastructure\Api\Orders\OrdersApiInterface;
use Revolut\Plugin\Services\Config\Api\ConfigInterface;
use Revolut\Plugin\Services\Customer\CustomerParams;
use Revolut\Plugin\Services\Customer\CustomerRepositoryInterface;
use Revolut\Plugin\Services\Customer\CustomerServiceInterface;
use Revolut\Plugin\Services\Location\LocationServiceInterface;
use Revolut\Plugin\Services\Log\RLog;
use Revolut\Plugin\Types\CaptureMode;
use Revolut\Plugin\Types\PaymentAmount;
use Revolut\Plugin\Types\PaymentMethod;
use Revolut\Plugin\Types\PaymentToken;

class PaymentService implements PaymentServiceInterface
{
    /** @var OrdersApiInterface */
    private $paymentApi;

    /** @var PaymentRepositoryInterface */
    private $repo;

    /** @var CustomerRepositoryInterface */
    private $customerRepo;

    /** @var ConfigInterface */
    private $config;

    /** @var bool */
    private $autoCancelFailedOrders;

    /** @var CustomerServiceInterface */
    private $customerService;

    /** @var LocationServiceInterface */
    private $locationService;

    /** @var string */
    public const WC_REVOLUT_AUTO_CANCEL_TIMEOUT = 'PT2M';

    public function __construct(
        OrdersApiInterface $ordersApi,
        PaymentRepositoryInterface $repo,
        CustomerServiceInterface $customerService,
        LocationServiceInterface $locationService,
        ConfigInterface $config,
        bool $autoCancelFailedOrders = true
    ) {
        $this->autoCancelFailedOrders = $autoCancelFailedOrders;
        $this->paymentApi = $ordersApi;
        $this->config = $config;
        $this->repo = $repo;
        $this->customerService = $customerService;
        $this->locationService = $locationService;
    }

    public function revolutPay(
        PaymentParams $params,
        ?CustomerParams $customerParams = null
    ): PaymentToken {
        return $this->createOrUpdate($params, $customerParams, PaymentMethod::of(PaymentMethod::REVOLUT_PAY));
    }

    public function cardPayment(
        PaymentParams $params,
        ?CustomerParams $customerParams = null
    ): PaymentToken {
        return $this->createOrUpdate($params, $customerParams, PaymentMethod::of(PaymentMethod::CARD));
    }

    public function paymentRequest(
        PaymentParams $params,
        ?CustomerParams $customerParams = null
    ): PaymentToken {
        return $this->createOrUpdate($params, $customerParams, PaymentMethod::of(PaymentMethod::PAYMENT_REQUEST));
    }

    public function revolutPayFastCheckout(
        PaymentParams $params,
        ?CustomerParams $customerParams = null
    ): PaymentToken {
        return $this->createOrUpdate($params, $customerParams, PaymentMethod::of(PaymentMethod::RPAY_FAST_CHECKOUT));
    }

    public function payByBank(
        PaymentParams $params,
        ?CustomerParams $customerParams = null
    ): PaymentToken {
        return $this->createOrUpdate($params, $customerParams, PaymentMethod::of(PaymentMethod::PAY_BY_BANK), true);
    }

    public function cardSubscriptionPayment(
        PaymentParams $params,
        ?CustomerParams $customerParams = null
    ): PaymentToken {
        return $this->createOrUpdate($params, $customerParams, PaymentMethod::of(PaymentMethod::CARD_SUBSCRIPTION), true);
    }

    private function createOrUpdate(
        PaymentParams $params,
        ?CustomerParams $customerParams,
        PaymentMethod $paymentMethod,
        $mustBeAutoCaptureMode = false
    ): PaymentToken {
        $cartId = $params->getCartId();
        $existPayment = $this->repo->findByPlatformCartId($cartId, $paymentMethod);

        if ($existPayment && $this->paymentApi->isPending($existPayment->getPaymentId())) {
            try {
                return $this->update($params, $existPayment);
            } catch (Exception $e) {
            }
        }

        return $this->create($params, $customerParams, $paymentMethod, $mustBeAutoCaptureMode);
    }

    private function create(PaymentParams $params, ?CustomerParams $customerParams, PaymentMethod $paymentMethod, $mustBeAutoCaptureMode): PaymentToken
    {
        $paymentAmount = $params->getPaymentAmount();
        $payload = $paymentAmount->getValue();

        $this->addCaptureMode($payload, $mustBeAutoCaptureMode);
        $this->addCustomer($payload, $customerParams);
        $this->addLocation($payload, $params);

        $payment = $this->paymentApi->create($payload);

        if (!isset($payment['id'])) {
            throw new Exception("Could not create payment resource - response: " . json_encode($payment));
        }

        $this->repo->add(PaymentModel::newWithPayment($payment['id'], $payment['token'], $paymentMethod));
        return PaymentToken::of($payment['token']);
    }

    private function addLocation(array &$payload, PaymentParams $params): void
    {
        try {
            if ($params->getDomain()) {
                $location = $this->locationService->create($params->getDomain());
                $payload['location_id'] = $location->id->getValue();
            }
        } catch (Exception $e) {
            RLog::error('addLocation ' . $e->getMessage());
        }
    }

    private function addCustomer(array &$payload, ?CustomerParams $customerParams): void
    {
        try {
            $customer = $customerParams ? $this->customerService->create($customerParams) : null;

            if ($customer) {
                $payload['customer'] = ['id' => $customer->id->getValue()];
            }
        } catch (Exception $e) {
            RLog::error('addCustomer ' . $e->getMessage());
        }
    }

    private function addCaptureMode(array &$payload, bool $mustBeAutoCaptureMode): void
    {
        $captureMode = $this->config->getCaptureMode();

        $payload['capture_mode'] = $mustBeAutoCaptureMode ? CaptureMode::AUTO_CAPTURE_MODE : CaptureMode::MANUAL_CAPTURE_MODE;

        if ($this->autoCancelFailedOrders && $captureMode->isAutoMode() && !$mustBeAutoCaptureMode) {
            $payload['cancel_authorised_after'] = PaymentService::WC_REVOLUT_AUTO_CANCEL_TIMEOUT;
        }
    }

    private function update(PaymentParams $params, PaymentModel $existPayment): PaymentToken
    {
        $paymentAmount = $params->getPaymentAmount();

        $payment = $this->paymentApi->update($existPayment->getPaymentId(), $paymentAmount->getValue());

        if (!isset($payment['id'])) {
            throw new Exception("Could not udpate payment resource - response: " . json_encode($payment));
        }

        return PaymentToken::of($existPayment->getPublicToken());
    }

    public function refund(string $orderId, PaymentAmount $amount): void
    {
        $payment = $this->repo->findByPlatformOrderId($orderId);

        if ($this->paymentApi->isCompleted($payment->getPaymentId())) {
            throw new Exception("Only Comleted payments can be refunded");
        }

        $refund = $this->paymentApi->refund($payment->getPaymentId(), $amount->getValue());

        if (! empty($refund['id'])) {
            throw new Exception("Could not run refund action - " . json_encode($refund));
        }
    }

    public function cancel(string $orderId): void
    {
        $payment = $this->repo->findByPlatformOrderId($orderId);

        if ($this->paymentApi->isAuthorised($payment->getPaymentId())) {
            throw new Exception("Only Authorised payments can be canceled");
        }

        $cancel = $this->paymentApi->cancel($payment->getPaymentId());

        if (! empty($cancel['id'])) {
            throw new Exception("Could not run cancel action - " . json_encode($cancel));
        }
    }

    public function capture(string $orderId): void
    {
        $payment = $this->repo->findByPlatformOrderId($orderId);

        if ($this->paymentApi->isAuthorised($payment->getPaymentId())) {
            throw new Exception("Only Authorised payments can be captured");
        }

        $capture = $this->paymentApi->capture($payment->getPaymentId());

        if (! empty($capture['id'])) {
            throw new Exception("Could not run cancel capture - " . json_encode($capture));
        }
    }
}
