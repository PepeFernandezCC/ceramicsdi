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

use Scaledev\Adeo\Action\AcceptOrderAction;
use Scaledev\Adeo\Action\ImportOrdersAction;
use Scaledev\Adeo\Action\RetrievesOrdersAction;
use Scaledev\Adeo\Core\Controller\Admin\AbstractModuleAdminController;
use Scaledev\Adeo\Component\Configuration;
use Scaledev\Adeo\Core\Tools;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'/../../autoload.php');

/**
 * Class AdminSdevAdeoOrdersFlowController
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class AdminSdevAdeoOrdersFlowController extends AbstractModuleAdminController
{
    const TEMPLATE = 'orders_flow';

    /**
     * @inheritdoc
     */
    public function renderList()
    {
        Media::addJsDef(array(
            'noElementNotification' => $this->module->l('No element is selected.'),
            'stateName' => [
                'WAITING_ACCEPTANCE' => $this->module->l('Waiting acceptance'),
                'WAITING_DEBIT_PAYMENT' => $this->module->l('Waiting debit payment'),
                'SHIPPING' => $this->module->l('Ready to be imported'),
                'TO_SHIP' => $this->module->l('Waiting to be shipped'),
                'ERROR' => $this->module->l('Error'),
                'SUCCESS' => $this->module->l('Success'),
            ]
        ));
        if (!($date = Configuration::getValue(Configuration::LAST_ORDER_UPDATE_DATE))) {
            $date = 0;
        }
        $this->context->smarty->assign(array(
            'date_last_flow' => $date
        ));

        return parent::renderList();
    }

    public function ajaxProcessGetOrders()
    {
        try {
            $action = new RetrievesOrdersAction();
            $mpOrders = $action->execute()['collection'][0];
            die(json_encode($mpOrders));
        } catch (Exception $e) {
            die(json_encode(array('error' => $e->getMessage())));
        }
    }

    public function ajaxProcessAcceptOrders()
    {
        $processed = array();
        $ordersToValidate = Tools::getRequest();
        $mpOrders = (new RetrievesOrdersAction())->execute();
        foreach ($mpOrders['collection'][0] as $mpOrder) {
            if (
                !array_key_exists('order_id', $mpOrder)
                || !in_array($mpOrder['order_id'], $ordersToValidate)
            ) {
                continue;
            }

            switch ($mpOrder['order_state']) {
                case 'WAITING_ACCEPTANCE':
                    $param = array();
                    foreach ($mpOrder['order_lines']['list'] as $line) {
                        $param[] = ['accepted' => true, 'id' => $line['order_line_id']];
                    }
                    (new AcceptOrderAction())
                        ->setImportId($mpOrder['order_id'])
                        ->setOrderLineArray($param)
                        ->execute();
                    $processed['accepted'][] = $mpOrder['order_id'];
                    break;

                case 'SHIPPING':
                    $newOrder = (new ImportOrdersAction())
                        ->setOrder($mpOrder)
                        ->execute();
                    if (array_key_exists('error', $newOrder)) {
                        $processed['shipping']['error'][] = [$mpOrder['order_id'] => $this->handleMessageError($newOrder)];
                    } else {
                        $processed['shipping']['success'][] = [$mpOrder['order_id'] => $newOrder->id];
                    }
                    break;
                default:
                    continue(2);
            }
        }
        die(json_encode($processed));
    }

    private function handleMessageError($messageCode)
    {
        switch ($messageCode['error']) {
            case 'totalPaidAmountError' :
                $message = $this->module->l('Price synchronization.');
                break;
            case 'customerError':
                $message = $this->module->l('Customer information.');
                break;
            case 'duplicateId':
                $message =  $this->module->l('Duplicate Marketplace ID:  Order # ') . $messageCode['id'];
                break;
            case 'addressBillingError':
                $message = $this->module->l('Billing address');
                break;
            case 'addressShippingError':
                $message = $this->module->l('Shipping address');
                break;
            case 'shippingError':
                $message = $this->module->l('Carrier: ') . $messageCode['shipping_code'];
                break;
            case 'cartError':
                $message = $this->module->l('Cart');
                break;
            case 'WrongIdentifierError':
                $message = $this->module->l('Wrong product identifier: '). $messageCode['offer_sku'];
                break;
            case 'NoIdentifierError':
                $message = $this->module->l('No product identifier: '). $messageCode['offer_sku'];
                break;
            case 'productNotFoundError':
                $message = $this->module->l('Cannot find product: '). $messageCode['offer_sku'];
                break;
            case 'productAttributeNotFoundError':
                $message = $this->module->l('Cannot find product attribute: '). $messageCode['offer_sku'];
                break;
            case 'addProductToCartError':
                $message = $this->module->l('Cannot add product to cart: '). $messageCode['offer_sku'];
                break;
            case 'orderCreateError':
                $message = $this->module->l('Cannot create order');
                break;
            case 'orderDetailError':
                $message = $this->module->l('Cannot add details to order');
                break;
            case 'orderStateNotSetError':
                $message = $this->module->l('No order state defined');
                break;
            case 'orderStateNotFoundError':
                $message = $this->module->l('Cannot find order state defined');
                break;
            default:
                $message = $this->module->l('Import process.');
                break;
        }
        return $message;
    }
}
