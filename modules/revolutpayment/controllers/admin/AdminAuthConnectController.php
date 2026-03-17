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

use Revolut\PrestaShop\ServiceProvider;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminAuthConnectController extends ModuleAdminController
{
    private $authConnectService;

    public function __construct()
    {
        parent::__construct();
        $this->authConnectService = ServiceProvider::authConnectService();
    }

    public function ajaxProcessHandleTokenExchange()
    {
        $code = Tools::getValue('code');
        $verifier = Tools::getValue('verifier');
        $mode = Tools::getValue('mode');

        try {
            $token = $this->authConnectService->exchangeAuthorizationCode($mode, $code, $verifier);
            $this->jsonResponse(json_encode(['success' => true, 'access_token' => $token->accessToken]));
        } catch (Exception $e) {
            $this->jsonResponse(json_encode(['success' => false]));
        }
    }

    public function ajaxProcessHandleDisconnect()
    {
        $mode = Tools::getValue('mode');

        try {
            $this->authConnectService->disconnect($mode);
            $this->jsonResponse(json_encode(['success' => true]));
        } catch (Exception $e) {
            $this->jsonResponse(json_encode(['success' => true, 'error' => $e->getMessage()]));
        }
    }

    private function jsonResponse($data)
    {
        print_r($data);
        exit;
    }
}
