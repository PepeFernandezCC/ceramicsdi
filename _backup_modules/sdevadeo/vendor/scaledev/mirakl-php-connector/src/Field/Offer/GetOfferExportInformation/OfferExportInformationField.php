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

namespace Scaledev\MiraklPhpConnector\Field\Offer\GetOfferExportInformation;

use Scaledev\MiraklPhpConnector\Core\Field\AbstractField;
use Scaledev\MiraklPhpConnector\Field\Offer\GetOfferExportInformation\OfferExportInformationField\DateCreatedField;
use Scaledev\MiraklPhpConnector\Field\Offer\GetOfferExportInformation\OfferExportInformationField\HasErrorReportField;
use Scaledev\MiraklPhpConnector\Field\Offer\GetOfferExportInformation\OfferExportInformationField\ImportIdField;
use Scaledev\MiraklPhpConnector\Field\Offer\GetOfferExportInformation\OfferExportInformationField\LinesInErrorField;
use Scaledev\MiraklPhpConnector\Field\Offer\GetOfferExportInformation\OfferExportInformationField\LinesInPendingField;
use Scaledev\MiraklPhpConnector\Field\Offer\GetOfferExportInformation\OfferExportInformationField\LinesInSuccessField;
use Scaledev\MiraklPhpConnector\Field\Offer\GetOfferExportInformation\OfferExportInformationField\LinesReadField;
use Scaledev\MiraklPhpConnector\Field\Offer\GetOfferExportInformation\OfferExportInformationField\ModeField;
use Scaledev\MiraklPhpConnector\Field\Offer\GetOfferExportInformation\OfferExportInformationField\OfferDeletedField;
use Scaledev\MiraklPhpConnector\Field\Offer\GetOfferExportInformation\OfferExportInformationField\OfferInsertedField;
use Scaledev\MiraklPhpConnector\Field\Offer\GetOfferExportInformation\OfferExportInformationField\OfferUpdatedField;
use Scaledev\MiraklPhpConnector\Field\Offer\GetOfferExportInformation\OfferExportInformationField\ReasonStatusField;
use Scaledev\MiraklPhpConnector\Field\Offer\GetOfferExportInformation\OfferExportInformationField\StatusField;
use Scaledev\MiraklPhpConnector\Model\Export\OfferExportInformation;
use Scaledev\MiraklPhpConnector\Validator\RequiredValidator;
use Scaledev\MiraklPhpConnector\Validator\Type\ArrayTypeValidator;

/**
 * Class OfferExportInformationField
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class OfferExportInformationField extends AbstractField
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
    const TYPE = OfferExportInformation::class;

    /**
     * @inheritdoc
     */
    const CHILD_FIELD = array(
        DateCreatedField::class,
        HasErrorReportField::class,
        ImportIdField::class,
        LinesInErrorField::class,
        LinesInPendingField::class,
        LinesInSuccessField::class,
        LinesReadField::class,
        ModeField::class,
        OfferDeletedField::class,
        OfferInsertedField::class,
        OfferUpdatedField::class,
        ReasonStatusField::class,
        StatusField::class
    );
}
