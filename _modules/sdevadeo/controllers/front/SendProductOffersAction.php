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


use Scaledev\Adeo\Action\OfferTaskAction;
use Scaledev\Adeo\Core\Controller\Front\AbstractModuleFrontController;
use Scaledev\Adeo\Component\Configuration;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'/../../autoload.php');
require_once(dirname(dirname(dirname(__FILE__))).'/src/Action/OfferTaskAction.php');
require_once(dirname(dirname(dirname(__FILE__))).'/src/Core/Tools.php');

/**
 * Class SdevAdeoSendProductOffersActionModuleFrontController
 *
 * @package Scaledev\Adeo
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class SdevAdeoSendProductOffersActionModuleFrontController extends AbstractModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        try {
            $shopId = Tools::getValue('shop_id');
            $context = Context::getContext();
            $originalIdShop = $context->shop->id;
            $context->shop = new Shop($shopId);
            Shop::setContext(Shop::CONTEXT_SHOP, $shopId);
            if (Tools::getValue('token') !== Configuration::getValue(Configuration::MODULE_TOKEN)) {
                Scaledev\Adeo\Component\Configuration::updateValue(Scaledev\Adeo\Component\Configuration::OFFER_FLOW_IN_PROGRESS, false);
                die('ko');
            }

            $result = (new OfferTaskAction())->execute();
            $context->shop = new Shop($originalIdShop);
            Shop::setContext(Shop::CONTEXT_SHOP, $originalIdShop);

            Scaledev\Adeo\Component\Configuration::updateValue(Scaledev\Adeo\Component\Configuration::OFFER_FLOW_IN_PROGRESS, false);
            die(json_encode($result));
        } catch (Exception $e) {
            Scaledev\Adeo\Component\Configuration::updateValue(Scaledev\Adeo\Component\Configuration::OFFER_FLOW_IN_PROGRESS, false);
            die(print_r($e));
        }
    }
}
