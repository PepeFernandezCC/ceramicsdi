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

include(dirname(__FILE__).'/../../config/config.inc.php');

$m = Module::getInstanceByName('outvio');
$m->check(Tools::getValue('id_order', 0));
