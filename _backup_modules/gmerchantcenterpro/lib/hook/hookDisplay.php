<?php
/**
 * Google Merchant Center Pro
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

namespace Gmerchantcenterpro\Hook;

if (!defined('_PS_VERSION_')) {
    exit;
}
use Gmerchantcenterpro\Configuration\moduleConfiguration;
use Gmerchantcenterpro\Dao\moduleDao;
use Gmerchantcenterpro\ModuleLib\moduleTools;

class hookDisplay extends hookBase
{
    /**
     * Magic Method __destruct
     */
    public function __destruct()
    {
    }

    /**
     * run() method execute hook
     *
     * @param array $aParams
     *
     * @return array
     */
    public function run(array $aParams = null)
    {
        // set variables
        $aDisplayHook = [];

        switch ($this->sHook) {
            case 'header':
                $aDisplayHook = call_user_func_array([$this, 'display' . ucfirst($this->sHook)], [$aParams]);
                // no break
            default:
                break;
        }

        return $aDisplayHook;
    }

    /**
     *  method display header
     *
     * @param array $aParams
     *
     * @return array
     */
    private function displayHeader(array $aParams = null)
    {
        try {
            $useGcr = \Gmerchantcenterpro::$conf['GMCP_GCR_ACTIVATE'];
            $useBadge = \Gmerchantcenterpro::$conf['GMCP_GCR_BADGE'];
            $dataAssign = [];
            $orderId = 0;
            $useReviewForm = false;

            // Assign value for badge
            if (!empty($useBadge) && !empty($useGcr)) {
                $dataAssign['GmcpUseBadge'] = $useBadge;
                $dataAssign['GmcpUseGcr'] = $useGcr;
                $dataAssign['GmcpMerchantId'] = \Gmerchantcenterpro::$conf['GMCP_MERCHANT_ID'];
            }

            // Use case handle detection for order page and get the order id
            if (!empty(\Tools::getvalue('id_order'))) {
                $orderId = \Tools::getvalue('id_order');
            } elseif (!empty(\Context::getContext()->controller->id_order)) {
                $orderId = (int) \Context::getContext()->controller->id_order;
            }

            if (empty($orderId)) {
                $cartId = !empty(\Tools::getValue('id_cart')) ? \Tools::getValue('id_cart') : \Context::getContext()->cart->id;
                $orderId = moduleDao::getOrderIdFromCart($cartId);
            }

            // When we have the order id we handle the case for google reviews form
            if (!empty($orderId)) {
                $order = new \Order($orderId);
                $validStatus = explode(',', \GMerchantCenterPro::$conf['GMCP_ORDER_STATE']);
                $useProductGtin = \GMerchantCenterPro::$conf['GMCP_GCR_PRODUCT_GTIN'];

                if (in_array($order->current_state, $validStatus)) {
                    $customer = new \Customer((int) $order->id_customer);
                    $estimateShippingDate = moduleTools::getEstimatedShippingDate($order);
                    $estimatedDelivery = moduleTools::getEstimatedDeliveryDate($order, $estimateShippingDate);
                    $useReviewForm = true;
                    $dataAssign['orderId'] = $order->reference;
                    $dataAssign['customerEmail'] = $customer->email;
                    $dataAssign['deliveryDate'] = $estimatedDelivery;
                    $dataAssign['deliveryCountry'] = moduleTools::getDeliveryCountryCode($order->id_address_delivery);
                    $dataAssign['useProductGtin'] = $useProductGtin;

                    // Only use call if the option is activated
                    if (!empty($useProductGtin)) {
                        $dataAssign['productGtins'] = moduleTools::getTagGtin($order->getCartProducts());
                    }
                }
            }

            $dataAssign['useReviewForm'] = $useReviewForm;

            return ['tpl' => moduleConfiguration::GMCP_TPL_HOOK_PATH . 'header.tpl', 'assign' => $dataAssign];
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }
    }
}
