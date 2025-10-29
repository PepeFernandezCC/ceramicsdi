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

use Scaledev\Adeo\Core\ObjectModel\AbstractObjectModel;

function upgrade_module_1_3_1($object)
{
    $tablename  = _DB_PREFIX_ . 'tab';
    $query      = "SELECT * FROM `{$tablename}` WHERE class_name = 'AdminSdevAdeoFilter'";

    $result = \Db::getInstance()->getRow($query);

    if (!$result) {
        $parentTabId = \Tab::getIdFromClassName('AdminParentModules'.( Scaledev\Adeo\Core\Tools::version_compare(_PS_VERSION_, '1.7', '>=') ? 'Sf' : null));
        
        $tabIndex   = 'filter';
        $tabName    = 'Filter products';

        $tab = new \Tab();

        foreach (\Language::getLanguages() as $language) {
            $tab->name[$language['id_lang']] = $tabName;
        }

        $tab->class_name  = 'Admin';
        $tab->class_name .= 'SdevAdeo';
        $tab->class_name .= Scaledev\Adeo\Core\Tools::ucfirst($tabIndex);

        $tab->module    = 'sdevadeo';
        $tab->id_parent = (int) $parentTabId;
        $tab->position  = \Tab::getNewLastPosition((int)$parentTabId);
        $tab->active    = ($tabIndex == 'index');

        return (!$tab->add()) ? false : true;
    }

    return true;
}
                