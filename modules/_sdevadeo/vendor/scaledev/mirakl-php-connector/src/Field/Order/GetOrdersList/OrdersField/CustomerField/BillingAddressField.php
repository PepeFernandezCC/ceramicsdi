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

namespace Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField;

use Scaledev\MiraklPhpConnector\Core\Field\AbstractField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\CityField as AddressCity;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\CivilityField as AddressCivility;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\CompanyField as AddressCompany;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\CountryField as AddressCountry;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\CountryIsoCodeField as AddressCountryIsoCode;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\FirstnameField as AddressFirstname;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\LastnameField as AddressLastname;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\PhoneField as AddressPhone;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\PhoneSecondaryField as AddressPhoneSecondary;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\StateField as AddressState;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\StreetField as AddressStreet;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\StreetSecondaryField as AddressStreetSecondary;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\ZipCodeField as AddressZipCode;
use Scaledev\MiraklPhpConnector\Model\Customer\CustomerBillingAddress;
use Scaledev\MiraklPhpConnector\Validator\RequiredValidator;
use Scaledev\MiraklPhpConnector\Validator\Type\ArrayTypeValidator;

/**
 * Class BillingAddressField
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class BillingAddressField extends AbstractField
{
    const CONSTRAINTS = array (
        RequiredValidator::class,
        ArrayTypeValidator::class
    );

    const TYPE = CustomerBillingAddress::class;

    const CHILD_FIELD = array(
        AddressCity::class,
        AddressCivility::class,
        AddressCompany::class,
        AddressCountry::class,
        AddressCountryIsoCode::class,
        AddressFirstname::class,
        AddressLastname::class,
        AddressPhone::class,
        AddressPhoneSecondary::class,
        AddressState::class,
        AddressStreet::class,
        AddressStreetSecondary::class,
        AddressZipCode::class
    );
}
