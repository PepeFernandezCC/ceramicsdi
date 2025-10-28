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

namespace Scaledev\MiraklPhpConnector\Field\Product\ExportStatus;

use Scaledev\MiraklPhpConnector\Core\Field\AbstractField;
use Scaledev\MiraklPhpConnector\Model\Export\ProductExportInformation;
use Scaledev\MiraklPhpConnector\Validator\RequiredValidator;
use Scaledev\MiraklPhpConnector\Validator\Type\ArrayTypeValidator;

/**
 * Class ExportStatusField
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class ExportStatusField extends AbstractField
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
    const TYPE = ProductExportInformation::class;

    /**
     * @inheritdoc
     */
    const CHILD_FIELD = array(
        DateCreatedField::class,
        HasErrorReportField::class,
        HasNewProductReportField::class,
        HasTransformationErrorReportField::class,
        HasTransformedFileField::class,
        ImportIdField::class,
        ImportStatusField::class,
        IntegrationDetailsField::class,
        ReasonStatusField::class,
        ShopIdField::class,
        TransformLinesInErrorField::class,
        TransformLinesInSuccessField::class,
        TransformLinesReadField::class,
        TransformLinesWithWarningField::class,
    );
}
