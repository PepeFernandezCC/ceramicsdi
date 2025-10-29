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
use Scaledev\MiraklPhpConnector\Model\Shop;
use Scaledev\MiraklPhpConnector\Validator\Type\ArrayTypeValidator;

/**
 * Class ShopField
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class ShopField extends AbstractField
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
    const TYPE = Shop::class;

    /**
     * @inheritdoc
     */
    const CHILD_FIELD = array(
        ApplicableTaxesField::class,
        ApprovalDelayField::class,
        ApprovalRateField::class,
        BannerField::class,
        ChannelsField::class,
        ClosedFromField::class,
        ClosedToField::class,
        ContactInformationsField::class,
        CurrencyIsoCodeField::class,
        DateCreatedField::class,
        DescriptionField::class,
        DomainsField::class,
        EvaluationsCountField::class,
        FreeShippingField::class,
        GradeField::class,
        IsProfessionalField::class,
        KycField::class,
        LastUpdatedDateField::class,
        LogoField::class,
        OffersCountField::class,
        OrderMessagesResponseDelayField::class,
        OrdersCountField::class,
        PaymentDetailsField::class,
        PremiumField::class,
        ProducerIdsField::class,
        RecyclingPolicyField::class,
        ReturnPolicyField::class,
        ShippingCountryField::class,
        ShippingsField::class,
        ShopAdditionalFieldsField::class,
        ShopIdField::class,
        ShopNameField::class,
        ShopStateField::class,
        SuspensionTypeField::class
    );
}
