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

namespace Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField;

use Scaledev\MiraklPhpConnector\Core\Field\AbstractField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AccountingContactField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\BillingAddressField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\CivilityField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\CustomerIdField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\DeliveryContactField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\FirstnameField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\LastnameField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\LocaleField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\OrganizationField;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\ShippingAddressField;
use Scaledev\MiraklPhpConnector\Model\Customer;
use Scaledev\MiraklPhpConnector\Validator\RequiredValidator;
use Scaledev\MiraklPhpConnector\Validator\Type\ArrayTypeValidator;

/**
 * Class CustomerField
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class CustomerField extends AbstractField
{
    /**
     * @inheritdoc
     */
    const CONSTRAINTS =  array(
        RequiredValidator::class,
        ArrayTypeValidator::class
    );

    /**
     * @inheritdoc
     */
    const TYPE = Customer::class;

    /**
     * @inheritdoc
     */
    const CHILD_FIELD = array(
        AccountingContactField::class,
        BillingAddressField::class,
        CivilityField::class,
        CustomerIdField::class,
        DeliveryContactField::class,
        FirstnameField::class,
        LastnameField::class,
        LocaleField::class,
        OrganizationField::class,
        ShippingAddressField::class,
    );
}
