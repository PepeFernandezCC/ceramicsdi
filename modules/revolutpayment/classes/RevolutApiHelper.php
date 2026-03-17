<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    Revolut
 * @copyright Since 2020 Revolut
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use Revolut\Plugin\Infrastructure\Api\MerchantApi;
use Revolut\Plugin\Services\Log\RLog;
use Revolut\PrestaShop\ServiceProvider;

class RevolutApiHelper
{
    /**
     * Update Revolut order
     */
    public static function updateRevolutOrder($revolut_order_id, $params)
    {
        try {
            $path = '/orders/' . $revolut_order_id;
            $response = MerchantApi::private()->patch($path, $params);

            if (!isset($response['token']) || !isset($response['id'])) {
                throw new Exception('Error: Can not update Revolut order ' . $revolut_order_id . ' or it already be updated. ' . json_encode($response));
            }

            return $response['token'];
        } catch (Exception $e) {
            RLog::error('updateRevolutOrder : ' . $e->getMessage());

            return '';
        }
    }

    /**
     * $revolut_order_id
     */
    public static function cancelRevolutOrder($revolut_order_id)
    {
        try {
            $response = MerchantApi::privateLegacy()->post('/orders/' . $revolut_order_id . '/cancel');

            if (!isset($response['id'])) {
                throw new Exception('Error: Can not cancel Revolut order ' . $revolut_order_id . ' or it is already be canceled.');
            }

            return $response;
        } catch (Exception $e) {
            RLog::error('cancelRevolutOrder : ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Capture Revolut order
     *
     * @return bool
     */
    public static function captureRevolutOrder($revolut_order_id)
    {
        try {
            for ($i = 0; $i <= 5; ++$i) {
                // poll order state for 15 seconds until it moves from processing status
                $revolut_order = MerchantApi::private()->get('/orders/' . $revolut_order_id);

                if (!empty($revolut_order['state']) && $revolut_order['state'] != 'processing') {
                    break;
                }

                sleep(3);
            }

            if ('manual' !== $revolut_order['capture_mode']) {
                return true;
            }

            if ('completed' === $revolut_order['state']) {
                return true;
            }

            if ('authorised' !== $revolut_order['state']) {
                return false;
            }

            $response = MerchantApi::privateLegacy()->post('/orders/' . $revolut_order_id . '/capture');

            if (!isset($response['id'])) {
                throw new Exception('Error: Can not capture Revolut order ' . $revolut_order_id . ' or it already be captured.');
            }

            return true;
        } catch (Exception $e) {
            RLog::error('captureRevolutOrder : ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Retrieve Revolut order
     *
     * || boolean
     */
    public static function retrieveRevolutOrder($revolut_order_id)
    {
        try {
            $response = MerchantApi::privateLegacy()->get('/orders/' . $revolut_order_id);

            if (!isset($response['public_id']) || !isset($response['id'])) {
                throw new Exception('Error: Can not retrieve Revolut order ' . $revolut_order_id);
            }

            return $response;
        } catch (Exception $e) {
            RLog::error('retrieveRevolutOrder : ' . $e->getMessage());

            return [];
        }
    }

    public static function refundRevolutOrder($revolut_order_id, $params)
    {
        try {
            $path = '/orders/' . $revolut_order_id . '/refund';
            $response = MerchantApi::privateLegacy()->post($path, $params);

            if (!isset($response['id'])) {
                throw new Exception('Error: Can not refund Revolut order ' . $revolut_order_id . ' - ' . json_encode($response));
            }

            return $response;
        } catch (Exception $e) {
            RLog::error('refundRevolutOrder : ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Get Revolut webhook URLs
     */
    public static function getRevolutWebhookUrls()
    {
        try {
            $urls = [];

            $path = '/webhooks';

            $response = MerchantApi::privateLegacy()->get($path);

            if (is_array($response) && count($response)) {
                foreach ($response as $response_item) {
                    if (
                        isset($response_item['url']) && $response_item['url'] != '' && isset($response_item['events'])
                        && count($response_item['events']) == 2
                    ) {
                        $urls[] = $response_item['url'];
                    }
                }
            }

            return $urls;
        } catch (Exception $e) {
            RLog::error('getRevolutWebhookUrls : ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Set Revolut webhook URL
     */
    public static function setRevolutWebhookUrl($url)
    {
        try {
            $path = '/webhooks';

            // check webhook URL existed
            if (self::checkIfUrlExist($url)) {
                return true;
            }

            $params = [
                'url' => $url,
                'events' => ['ORDER_COMPLETED', 'ORDER_AUTHORISED'],
            ];

            $response = MerchantApi::privateLegacy()->post($path, $params);

            if (empty($response['url'])) {
                throw new Exception('Error: Can not set Revolut webhook URL ' . $params['url'] . ' with Revolut Merchant API.');
            }

            return true;
        } catch (Exception $e) {
            RLog::error('getRevolutWebhookUrls : ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Checks if URL already set for webhook
     */
    public static function checkIfUrlExist($url)
    {
        $webhook_urls = self::getRevolutWebhookUrls();

        return in_array($url, $webhook_urls);
    }

    /**
     * getAvailableCardBrands
     */
    public static function getAvailableCardBrands($currency)
    {
        $revolut_order = MerchantApi::public()->get('/available-payment-methods?amount=0&currency=' . $currency);
        if (isset($revolut_order['available_card_brands']) && is_array($revolut_order['available_card_brands'])) {
            return in_array('amex', $revolut_order['available_card_brands']);
        }

        return false;
    }

    public static function getAvailablePaymentOptions($currency)
    {
        $revolut_order = MerchantApi::public()->get('/available-payment-methods?amount=0&currency=' . $currency);
        if (isset($revolut_order['available_payment_methods']) && is_array($revolut_order['available_payment_methods'])) {
            return $revolut_order['available_payment_methods'];
        }

        return [];
    }

    /**
     * Returns merchant public features
     */
    public static function getMerchantFeatures()
    {
        $response = MerchantApi::public()->get('/merchant');

        if (isset($response['features']) && is_array($response['features'])) {
            return $response['features'];
        }

        return [];
    }

    /**
     * Fetch bank brands from api
     */
    public static function fetchBankBrands($currency)
    {
        $mode = ServiceProvider::apiConfigProvider()->getConfig()->getMode();

        if ($mode == 'sandbox') {
            return json_encode([]);
        }

        if (!in_array('open_banking', self::getAvailablePaymentOptions($currency))) {
            return json_encode([]);
        }

        $option_key = "revolut_{$mode}_{$currency}_openbanking_bank_info";

        $saved_bank_brands = Configuration::get($option_key);
        if (!empty($saved_bank_brands)) {
            return $saved_bank_brands;
        }

        $banks = MerchantApi::public()->get("/open-banking/external-institutions?currency={$currency}");

        if (!empty($banks) && is_array($banks)) {
            $bank_brands = json_encode($banks);
            Configuration::updateValue($option_key, $bank_brands);

            return $bank_brands;
        }

        return json_encode([]);
    }
}
