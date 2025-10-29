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
        'EN' => 'https://support.marketplace.adeo.com/hc/en-150/articles/13953425767825-Prestashop-Module-Configuration-and-Guidelines',
        'FR' => 'https://support.marketplace.adeo.com/hc/fr/articles/13953425767825-Module-Prestashop-Configuration-et-Guide',
        'IT' => 'https://support.marketplace.adeo.com/hc/it/articles/13953425767825-Modulo-Prestashop-Configurazione-e-guida',
        'ES' => 'https://support.marketplace.adeo.com/hc/es/articles/13953425767825-M%C3%B3dulo-de-Prestashop-Configuraci%C3%B3n-e-instrucciones',
        'PT' => 'https://support.marketplace.adeo.com/hc/pt-pt/articles/13953425767825-M%C3%B3dulo-Prestashop-Configura%C3%A7%C3%A3o-e-guia',
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
