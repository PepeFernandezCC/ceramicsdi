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

class RevolutpaymentOrderModuleFrontController extends ModuleFrontController
{
    /**
     * Main module instance
     *
     * @var RevolutPayment
     */
    public $module;

    public function postProcess()
    {
        try {
            $payment_method = Tools::getValue('payment_method');

            if (!$payment_method) {
                throw new Error('Order creation request failed due to lack of necessary parameter : payment_method ');
            }

            $public_id = $this->module->createRevolutOrder(null, $payment_method);

            if (!$public_id) {
                throw new Error('Could not create revolut order for : ' . $payment_method . ' due to missing public_id');
            }

            $data['public_id'] = $public_id;
            $data['success'] = true;

            $this->module->jsonDie($data);
        } catch (Throwable $e) {
            PrestaShopLogger::addLog('Order front controller error : ' . $e->getMessage(), 3);
            $this->module->jsonDie([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
