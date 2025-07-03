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
include_once(dirname(dirname(__FILE__)) . '/src/Action/RetrievesOrdersAction.php');
include_once(dirname(dirname(__FILE__)) . '/src/Action/AcceptOrderAction.php');
include_once(dirname(dirname(__FILE__)) . '/src/Core/Module.php');
include_once(dirname(dirname(__FILE__)) . '/src/Core/Tools.php');

$shop_id = (isset($argv[1]) ? (int)$argv[1] : 0);

if (!$shop_id) {
    die('No parameters founds');
}

try {
    $context = \Context::getContext();
    $context->shop = new Shop($shop_id);
    $context->employee = new Employee();
    $context->language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
    Shop::setContext(Shop::CONTEXT_SHOP, $shop_id);
    foreach ((((new Scaledev\Adeo\Action\RetrievesOrdersAction())->execute())['collection'][0]) as $mpOrder) {
        if (
            $mpOrder['order_state'] == 'WAITING_ACCEPTANCE'
            && \Scaledev\Adeo\Component\Configuration::getValue(\Scaledev\Adeo\Component\Configuration::AUTO_VALIDATE)
        ) {
            $param = array();
            foreach ($mpOrder['order_lines']['list'] as $line) {
                $param[] = ['accepted' => true, 'id' => $line['order_line_id']];
            }
            (new Scaledev\Adeo\Action\AcceptOrderAction())
                ->setImportId($mpOrder['order_id'])
                ->setOrderLineArray($param)
                ->execute()
            ;
        } else if ($mpOrder['order_state'] == 'SHIPPING') {
            $newOrder = (new Scaledev\Adeo\Action\ImportOrdersAction())
                ->setOrder($mpOrder)
                ->execute();
        }
    }
    die(json_encode('ok'));
} catch (Exception $e) {
    die(print_r($e));
}