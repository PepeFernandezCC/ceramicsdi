<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from ScaleDEV.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from ScaleDEV is strictly forbidden.
 * In order to obtain a license, please contact us: contact@scaledev.fr
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise à une licence commerciale
 * concédée par la société ScaleDEV.
 * Toute utilisation, reproduction, modification ou distribution du présent
 * fichier source sans contrat de licence écrit de la part de ScaleDEV est
 * expressément interdite.
 * Pour obtenir une licence, veuillez nous contacter : contact@scaledev.fr
 * ...........................................................................
 * @author ScaleDEV <contact@scaledev.fr>
 * @copyright Copyright (c) ScaleDEV - 12 RUE CHARLES MORET - 10120 SAINT-ANDRE-LES-VERGERS - FRANCE
 * @license Commercial license
 * @package Scaledev\Adeo
 * Support: support@scaledev.fr
 */

namespace Scaledev\Adeo\Action;

use Scaledev\Adeo\Component\Configuration;
use Scaledev\Adeo\Core\Action\AbstractAction;
use Scaledev\Adeo\Core\Tools;
use Scaledev\MiraklPhpConnector\Request\Order\GetOrdersListRequest;
use Scaledev\MiraklPhpConnector\Response\Order\GetOrdersListResponse;

/**
 * Class RetrievesOrdersAction
 *
 * @package Scaledev\Adeo
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class RetrievesOrdersAction extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = array();
        /** @var GetOrdersListResponse $response */
        $response = (new GetOrdersListRequest())
            ->execute(
                Configuration::getValue(Configuration::API_KEY),
                Configuration::getValue(Configuration::API_ENV),
                ['order_state_codes' => 'STAGING,WAITING_ACCEPTANCE,WAITING_DEBIT,WAITING_DEBIT_PAYMENT,SHIPPING']
            )
            ->getResponse();

        $orderList = Tools::array_values_recursive($response->getOrderCollection()->getList());

        if (($total = $response->getTotalCount()) > 10) {
            $offset = 10;
            while($offset < $total) {
                /** @var GetOrdersListResponse $response */
                $response = (new GetOrdersListRequest())
                    ->execute(
                        Configuration::getValue(Configuration::API_KEY),
                        Configuration::getValue(Configuration::API_ENV),
                        [
                            'max' => 10,
                            'offset' => $offset,
                            'order_state_codes' => 'STAGING,WAITING_ACCEPTANCE,WAITING_DEBIT,WAITING_DEBIT_PAYMENT,SHIPPING'
                        ]
                    )
                    ->getResponse();
                $orderList = array_merge($orderList, Tools::array_values_recursive($response->getOrderCollection()->getList()));
                $offset += 10;
            }
        }

        $refundOrders = array();
        if (!empty($orderList)) {
            foreach ($orderList as $orderKey => $order) {
                if (
                    array_key_exists('order_lines', $order)
                    && array_key_exists('list', $order['order_lines'])
                ) {
                    foreach ($order['order_lines']['list'] as $order_line) {
                        if (
                            array_key_exists('refunds', $order_line)
                            && array_key_exists('list', $order_line['refunds'])
                            && !empty($order_line['refunds']['list'])
                        ) {
                            $refundOrders[] = $orderKey;
                            break(2);
                        }
                    }
                }
            }
        }

        if (!empty($refundOrders) && rsort($refundOrders)){
            foreach ($refundOrders as $index) {
                unset($orderList[$index]);
            }
        }

        $result['collection'][] = $orderList;

        if (Configuration::updateValue(Configuration::LAST_ORDER_UPDATE_DATE, date('d-m-Y H:i:s'))) {
            return $result;
        }
        return false;
    }
}
