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

if (php_sapi_name() != 'cli') {
    die('Forbidden access');
}

include_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config/config.inc.php');
include_once(dirname(dirname(__FILE__)) . '/src/Core/Action/ActionInterface.php');
include_once(dirname(dirname(__FILE__)) . '/src/Core/Action/AbstractAction.php');
include_once(dirname(dirname(__FILE__)) . '/src/Action/OfferTaskAction.php');
include_once(dirname(dirname(__FILE__)) . '/src/Component/Configuration.php');
include_once(dirname(dirname(__FILE__)) . '/src/Core/Module.php');
include_once(dirname(dirname(__FILE__)) . '/src/Core/Tools.php');

$shop_id = (isset($argv[1]) ? (int)$argv[1] : 0);

if (!$shop_id) {
    die('No parameters founds');
}

try {
    $context = \Context::getContext();
    $context->cart = new Cart();
    $context->shop = new Shop($shop_id);
    Shop::setContext(Shop::CONTEXT_SHOP, $shop_id);
    $result = (new Scaledev\Adeo\Action\OfferTaskAction())
        ->execute()
    ;
    die(json_encode($result));

} catch (Exception $e) {
    Scaledev\Adeo\Component\Configuration::updateValue(Scaledev\Adeo\Component\Configuration::OFFER_FLOW_IN_PROGRESS, false);
    die(print_r($e));
}
