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

namespace Scaledev\MiraklPhpConnector\Field\Offer\ExportOffersFile\FileGenerator;

use Scaledev\MiraklPhpConnector\Collection\OfferCollection;
use Scaledev\MiraklPhpConnector\Core\Field\AbstractField;
use Scaledev\MiraklPhpConnector\Validator\RequiredValidator;

/**
 * Class OfferFileField
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class OfferFileField extends AbstractField
{
    /**
     * @inheritdoc
     */
    const CONSTRAINTS =  array(
        RequiredValidator::class
    );

    /**
     * @inheritdoc
     */
    const TYPE = OfferCollection::class;

    /**
     * @inheritdoc
     */
    const CHILD_FIELD = array(
        AllowQuoteRequestsField::class,
        AvailableEndDateField::class,
        AvailableStartDateField::class,
        DescriptionField::class,
        DiscountEndDateField::class,
        DiscountPriceField::class,
        DiscountRangesField::class,
        DiscountStartDateField::class,
        EcoContributionsField::class,
        EcotaxField::class,
        FavoriteRankField::class,
        GiftWrapField::class,
        InternalDescriptionField::class,
        LeadtimeToShipField::class,
        LogisticClassField::class,
        MaxOrderQuantityField::class,
        MinOrderQuantityField::class,
        MinQuantityAlertField::class,
        MinQuantityOrderedField::class,
        PackageQuantityField::class,
        PriceAdditionalInfoField::class,
        PriceField::class,
        PriceRangesField::class,
        PricingUnitField::class,
        ProductIdField::class,
        ProductIdTypeField::class,
        ProductTaxCodeField::class,
        QuantityField::class,
        SkuField::class,
        StateField::class,
        UpdateDeleteField::class,
    );
}
