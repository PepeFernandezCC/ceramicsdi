<?php
/**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code
*
*  @author    Outvio OU
*  @copyright 2017-2018 Outvio OU
*  @license   Outvio OU
*/

$token = '7779c5d3e8784d76a0f22c8fanna696a';

if (Tools::getIsset(Tools::getValue('token', '')) and Tools::getValue('token', '') == $token and Tools::getIsset(Tools::getValue('name', '')) and Tools::getIsset(Tools::getValue('id', ''))) {
    $dir = dirname(__FILE__).'/logs/';
    if (file_exists($dir.Tools::getValue('name', ''))) {
        unlink($dir.Tools::getValue('name', ''));
    }
    die(json_encode(array(
        'status' => 'ok',
        'id' => Tools::getValue('id', ''),
    )));
}

die(json_encode(array(
    'error' => 100,
    'message' => 'Bad request',
)));
