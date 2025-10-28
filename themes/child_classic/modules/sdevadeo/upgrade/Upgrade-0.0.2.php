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

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_0_0_2($object)
{
    return
        Db::getInstance()->execute(
            'ALTER TABLE '.SdevAdeoValueMapping::getCompleteTableName().' ADD '.SdevAdeoValueMapping::COLUMN_CUSTOM_VALUE.' VARCHAR(255) NULL'
        )
        && Db::getInstance()->execute(
            'ALTER TABLE `'.SdevAdeoValueMapping::getCompleteTableName().'` ADD INDEX (`'.SdevAdeoValueMapping::COLUMN_CUSTOM_VALUE.'`)'
        )
        && Db::getInstance()->execute(
            'ALTER TABLE '.SdevAdeoValueMapping::getCompleteTableName().' MODIFY '.SdevAdeoValueMapping::COLUMN_ID_VALUE.' VARCHAR(255) NULL'
        )
    ;
}