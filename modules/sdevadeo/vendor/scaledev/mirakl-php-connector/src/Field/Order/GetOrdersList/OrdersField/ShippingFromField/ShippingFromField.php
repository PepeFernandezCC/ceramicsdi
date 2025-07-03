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

namespace Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingFromField;

use Scaledev\MiraklPhpConnector\Core\Field\AbstractField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingFromField\AddressField\CityField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingFromField\AddressField\CountryIsoCodeField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingFromField\AddressField\StateField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingFromField\AddressField\StreetField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingFromField\AddressField\StreetSecondaryField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\ShippingFromField\AddressField\ZipCodeField;
use Scaledev\MiraklPhpConnector\Model\Order\Address;
use Scaledev\MiraklPhpConnector\Validator\Type\ArrayTypeValidator;

/**
 * Class ShippingFromField
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class ShippingFromField extends AbstractField
{
    /**
     * @inheritdoc
     */
    const CONSTRAINTS =  array(
        ArrayTypeValidator::class
    );

    /**
     * @inheritdoc
     */
    const TYPE = Address::class;

    /**
     * @inheritdoc
     */
    const CHILD_FIELD = array(
        CityField::class,
        CountryIsoCodeField::class,
        StateField::class,
        StreetField::class,
        StreetSecondaryField::class,
        ZipCodeField::class,
    );
}
