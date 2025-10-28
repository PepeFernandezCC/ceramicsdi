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

namespace Scaledev\MiraklPhpConnector\Field\Shop\GetShopInformation;

use Scaledev\MiraklPhpConnector\Core\Field\AbstractField;
use Scaledev\MiraklPhpConnector\Field\Shop\GetShopInformation\Address\CityField;
use Scaledev\MiraklPhpConnector\Field\Shop\GetShopInformation\Address\CivilityField;
use Scaledev\MiraklPhpConnector\Field\Shop\GetShopInformation\Address\CountryField;
use Scaledev\MiraklPhpConnector\Field\Shop\GetShopInformation\Address\EmailField;
use Scaledev\MiraklPhpConnector\Field\Shop\GetShopInformation\Address\FaxField;
use Scaledev\MiraklPhpConnector\Field\Shop\GetShopInformation\Address\FirstnameField;
use Scaledev\MiraklPhpConnector\Field\Shop\GetShopInformation\Address\LastnameField;
use Scaledev\MiraklPhpConnector\Field\Shop\GetShopInformation\Address\PhoneField;
use Scaledev\MiraklPhpConnector\Field\Shop\GetShopInformation\Address\PhoneSecondaryField;
use Scaledev\MiraklPhpConnector\Field\Shop\GetShopInformation\Address\StateField;
use Scaledev\MiraklPhpConnector\Field\Shop\GetShopInformation\Address\StreetField;
use Scaledev\MiraklPhpConnector\Field\Shop\GetShopInformation\Address\StreetSecondaryField;
use Scaledev\MiraklPhpConnector\Field\Shop\GetShopInformation\Address\WebSiteField;
use Scaledev\MiraklPhpConnector\Field\Shop\GetShopInformation\Address\ZipCodeField;
use Scaledev\MiraklPhpConnector\Model\Shop\ShopAddress;
use Scaledev\MiraklPhpConnector\Validator\Type\ArrayTypeValidator;

/**
 * Class ContactInformationsField
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class ContactInformationsField extends AbstractField
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
    const TYPE = ShopAddress::class;

    /**
     * @inheritdoc
     */
    const CHILD_FIELD = array(
        CityField::class,
        CivilityField::class,
        CountryField::class,
        EmailField::class,
        FaxField::class,
        FirstnameField::class,
        LastnameField::class,
        PhoneField::class,
        PhoneSecondaryField::class,
        StateField::class,
        StreetField::class,
        StreetSecondaryField::class,
        WebSiteField::class,
        ZipCodeField::class
    );
}
