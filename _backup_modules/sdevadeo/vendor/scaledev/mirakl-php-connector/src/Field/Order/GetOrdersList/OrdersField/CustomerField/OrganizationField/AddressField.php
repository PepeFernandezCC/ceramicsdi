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

namespace Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\OrganizationField;

use Scaledev\MiraklPhpConnector\Core\Field\AbstractField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\OrganizationField\AddressField\CityField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\OrganizationField\AddressField\CountryIsoCodeField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\OrganizationField\AddressField\StateField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\OrganizationField\AddressField\StreetField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\OrganizationField\AddressField\StreetSecondaryField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\OrganizationField\AddressField\ZipCodeField;
use Scaledev\MiraklPhpConnector\Model\Customer\CustomerOrganization\CustomerOrganizationAddress;
use Scaledev\MiraklPhpConnector\Validator\Type\ArrayTypeValidator;

/**
 * Class addressField
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class AddressField extends AbstractField
{
    const CONSTRAINTS = array (
        ArrayTypeValidator::class
    );

    const TYPE = CustomerOrganizationAddress::class;

    const CHILD_FIELD = array(
        CityField::class,
        CountryIsoCodeField::class,
        StateField::class,
        StreetField::class,
        StreetSecondaryField::class,
        ZipCodeField::class,
    );
}
