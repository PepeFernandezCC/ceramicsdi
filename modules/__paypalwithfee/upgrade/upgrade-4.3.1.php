<?php
/**
 * 2020 4webs
 *
 * DEVELOPED By 4webs.es Prestashop Platinum Partner
 *
 * @author    4webs
 * @copyright 4webs 2019
 * @license   4webs
 * @version 5.1.4
 * @category payment_gateways
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_4_3_1($object)
{
    if (!$object->isRegisteredInHook('displayOrderDetail')) {
        $object->registerHook('displayOrderDetail');
    }


    return true;
}
