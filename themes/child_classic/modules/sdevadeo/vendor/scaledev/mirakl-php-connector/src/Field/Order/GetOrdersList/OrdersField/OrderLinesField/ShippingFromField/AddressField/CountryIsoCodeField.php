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
 * @package Scaledev\MiraklPhpConnector
 * Support: support@scaledev.fr
 */

namespace Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\OrderLinesField\ShippingFromField\AddressField;

use Scaledev\MiraklPhpConnector\Core\Field\AbstractField;
use Scaledev\MiraklPhpConnector\Validator\RequiredValidator;
use Scaledev\MiraklPhpConnector\Validator\Type\StringTypeValidator;
use Scaledev\MiraklPhpConnector\Validator\ValueValidator;

/**
 * Class CountryIsoCodeField
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class CountryIsoCodeField extends AbstractField
{
    /**
     * @inheritdoc
     */
    const CONSTRAINTS =  array(
        StringTypeValidator::class,
        ValueValidator::class
    );

    /**
     * @inheritdoc
     */
    const VALUES = array (
        'AED',
        'ARS',
        'AUD',
        'BGN',
        'BHD',
        'BRL',
        'CAD',
        'CHF',
        'CLP',
        'CNY',
        'COP',
        'CZK',
        'DKK',
        'EGP',
        'EUR',
        'GBP',
        'HKD',
        'HRK',
        'HUF',
        'ILS',
        'INR',
        'JOD',
        'JPY',
        'KES',
        'KRW',
        'KWD',
        'LKR',
        'MXN',
        'MYR',
        'NGN',
        'NOK',
        'NZD',
        'OMR',
        'PEN',
        'PHP',
        'PKR',
        'PLN',
        'RON',
        'RSD',
        'RUB',
        'SAR',
        'SEK',
        'SGD',
        'TND',
        'TRY',
        'THB',
        'TWD',
        'UAH',
        'USD',
        'UYU',
        'VND',
        'ZAR',
    );
}
