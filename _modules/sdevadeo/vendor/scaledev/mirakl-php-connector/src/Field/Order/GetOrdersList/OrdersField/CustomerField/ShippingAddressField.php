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
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\AdditionalInfoField as AddressAdditionalInfoBis;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\CityField as AddressCityBis;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\CivilityField as AddressCivilityBis;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\CompanyField as AddressCompanyBis;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\CountryField as AddressCountryBis;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\CountryIsoCodeField as AddressCountryIsoCodeBis;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\FirstnameField as AddressFirstnameBis;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\LastnameField as AddressLastnameBis;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\PhoneField as AddressPhoneBis;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\PhoneSecondaryField as AddressPhoneSecondaryBis;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\StateField as AddressStateBis;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\StreetField as AddressStreetBis;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\StreetSecondaryField as AddressStreetSecondaryBis;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\ZipCodeField as AddressZipCodeBis;
use Scaledev\MiraklPhpConnector\Model\Customer\CustomerShippingAddress;
use Scaledev\MiraklPhpConnector\Validator\RequiredValidator;
use Scaledev\MiraklPhpConnector\Validator\Type\ArrayTypeValidator;

/**
 * Class ShippingAddressField
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class ShippingAddressField extends AbstractField
{
    const CONSTRAINTS = array (
        RequiredValidator::class,ArrayTypeValidator::class
    );

    const TYPE = CustomerShippingAddress::class;

    const CHILD_FIELD = array(
        AddressAdditionalInfoBis::class,
        AddressCityBis::class,
        AddressCivilityBis::class,
        AddressCompanyBis::class,
        AddressCountryBis::class,
        AddressCountryIsoCodeBis::class,
        AddressFirstnameBis::class,
        AddressLastnameBis::class,
        AddressPhoneBis::class,
        AddressPhoneSecondaryBis::class,
        AddressStateBis::class,
        AddressStreetBis::class,
        AddressStreetSecondaryBis::class,
        AddressZipCodeBis::class
    );
}
