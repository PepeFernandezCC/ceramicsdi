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

use Carrier;
use Db;
use OrderCarrier;
use Scaledev\Adeo\Component\Configuration;
use Scaledev\Adeo\Core\Action\AbstractAction;
use Scaledev\Adeo\Core\Module;
use Scaledev\Adeo\Core\Tools;
use Scaledev\MiraklPhpConnector\Request\Order\UpdateTrackingRequest;

require_once(dirname(__FILE__) . '/../../autoload.php');

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class UpdateTrackingAction
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class UpdateTrackingAction extends AbstractAction
{
    /** @var object order proceeding */
    private $order_id = false;

    /**
     * @param object $order_id
     * @return $this
     */
    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if (
            !($orderStates = Configuration::getValue(Configuration::SHIPPED_STATE))
            || empty(Tools::jsonDecode(Configuration::getValue(Configuration::SHIPPED_STATE), true))
        ) {
            return false;
        }

        if ($this->order_id) {
            $this->updateOrder($this->order_id);
        } else {
            foreach ($this->getOrdersToShip(\Context::getContext()->shop->id) as $order) {
                $this->updateOrder($order);
            }
        }
        return true;
    }

    public function getOrdersToShip($id_shop = null)
    {
        if (!$id_shop) {
            $id_shop = \Context::getContext()->shop->id;
        }

        $query = (new \DbQuery())
            ->select('DISTINCT o.id_order, o.mp_order_id')
            ->from('orders', 'o')
            ->leftJoin('order_history', 'oh', 'o.id_order = oh.id_order AND oh.id_order_state IN ('.pSQL(implode(', ', Tools::jsonDecode(Configuration::getValue(Configuration::SHIPPED_STATE), true))).')')
            ->where('o.module = \''.pSQL(Module::NAME).'\'')
            ->where('o.id_shop = '.(int)$id_shop)
            ->orderBy('o.id_order DESC')
        ;

        if (Configuration::getValue(Configuration::LAST_SHIPPING, null, null, $id_shop)) {
            $query->where('oh.date_add >= \''.pSQL(Configuration::getValue(Configuration::LAST_SHIPPING, null, null, $id_shop)).'\'');
        }
        $orders = Db::getInstance()->executeS($query);

        if (!$orders) {
            return array();
        }

        // Check state of the API state of the order
        $apiOrdersAction = new RetrievesOrdersAction();
        $apiOrders = $apiOrdersAction->execute();

        $apiOrdersList = array();
        foreach ($apiOrders['collection'][0] as $order) {
            $apiOrdersList[] = $order['order_id'];
        }

        $finalList = array();
        foreach ($orders as $order) {
            if (in_array($order['mp_order_id'], $apiOrdersList)) {
                $finalList[] = $order['id_order'];
            }
        }

        return $finalList;
    }

    public function updateOrder($order)
    {
        $order = new \Order($order);

        if ($order->module == Module::NAME) {
            $orderStateShippingIdList = Tools::jsonDecode(Configuration::getValue(Configuration::SHIPPED_STATE), true);
            if (!$orderStateShippingIdList) {
                return false;
            }

            $canShipOrder = in_array($order->current_state, $orderStateShippingIdList);

            if (!$canShipOrder) {
                foreach ($order->getHistory($order->id_lang) as $orderHistoryData) {
                    if (!$canShipOrder && in_array($orderHistoryData['id_order_state'], $orderStateShippingIdList)) {
                        $canShipOrder = true;
                    }
                }
            }

            if (!$canShipOrder) {
                return false;
            }

            $query = (new \DbQuery())
                ->select('id_reference')
                ->from('carrier')
                ->where('id_carrier = '.(int)$order->id_carrier)
            ;
            $carrier = Carrier::getCarrierByReference(Db::getInstance()->getValue($query));

            $orderCarrier = new OrderCarrier(Tools::getIdOrderCarrier($order->id));

            $mpOrderId = Tools::getMpOrderIdById($order->id);
            (new UpdateTrackingRequest())
                ->setRequestParameter($mpOrderId)
                ->setPutFields([
                    'carrier_name' => $carrier->name,
                    'carrier_url' => str_replace('@', $orderCarrier->tracking_number, $carrier->url),
                    'tracking_number' => $orderCarrier->tracking_number
                ])
                ->execute(
                    Configuration::getValue(Configuration::API_KEY),
                    Configuration::getValue(Configuration::API_ENV)
                );

            (new ValidateShipmentAction)
                ->setOrder($mpOrderId)
                ->execute();
        }
        return true;
    }

}