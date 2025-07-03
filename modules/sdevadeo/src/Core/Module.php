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

namespace Scaledev\Adeo\Core;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class Module
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class Module
{
    const NAME = 'sdevadeo';
    const DOCUMENTATION = array(
        'EN' => 'https://support.marketplace.adeo.com/hc/en-150/articles/8780501182865-Prestashop-module-configuration-and-guidelines',
        'FR' => 'https://support.marketplace.adeo.com/hc/fr/articles/8780501182865-Installation-du-module-Prestashop-et-configuration',
        'IT' => 'https://support.marketplace.adeo.com/hc/it/articles/8780501182865-PrestaShop-configurazione-del-modulo-e-linee-guida',
        'ES' => 'https://support.marketplace.adeo.com/hc/es/articles/8780501182865-Instalaci%C3%B3n-del-m%C3%B3dulo-prestashop-y-configuraci%C3%B3n',
    );
    /**
     * Get the module's documentations list.
     *
     * @return array
     */
    public static function getDocumentationsList()
    {
        return self::DOCUMENTATION;
    }
}
