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

namespace Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute;

use Scaledev\MiraklPhpConnector\Collection\AttributeCollection;
use Scaledev\MiraklPhpConnector\Core\Field\AbstractField;
use Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute\ProductAttribute\HierarchyCodeField;
use Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute\ProductAttribute\ChannelsField;
use Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute\ProductAttribute\CodeField;
use Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute\ProductAttribute\DefaultValueField;
use Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute\ProductAttribute\DescriptionField;
use Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute\ProductAttribute\DescriptionTranslationsField;
use Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute\ProductAttribute\ExampleField;
use Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute\ProductAttribute\LabelField;
use Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute\ProductAttribute\LabelTranslationsField;
use Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute\ProductAttribute\RequirementLevelField;
use Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute\ProductAttribute\RolesField;
use Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute\ProductAttribute\TransformationsField;
use Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute\ProductAttribute\TypeField;
use Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute\ProductAttribute\TypeParameterField;
use Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute\ProductAttribute\TypeParametersField;
use Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute\ProductAttribute\UniqueCodeField;
use Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute\ProductAttribute\ValidationsField;
use Scaledev\MiraklPhpConnector\Field\Product\GetProductAttribute\ProductAttribute\VariantField;
use Scaledev\MiraklPhpConnector\Validator\RequiredValidator;
use Scaledev\MiraklPhpConnector\Validator\Type\ArrayTypeValidator;

/**
 * Class ProductAttributeField
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class ProductAttributeField extends AbstractField
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
    const TYPE = AttributeCollection::class;

    /**
     * @inheritdoc
     */
    const CHILD_FIELD = array(
        HierarchyCodeField::class,
        ChannelsField::class,
        CodeField::class,
        DefaultValueField::class,
        DescriptionField::class,
        DescriptionTranslationsField::class,
        ExampleField::class,
        LabelField::class,
        LabelTranslationsField::class,
        RequirementLevelField::class,
        RolesField::class,
        TransformationsField::class,
        TypeField::class,
        TypeParameterField::class,
        TypeParametersField::class,
        UniqueCodeField::class,
        ValidationsField::class,
        VariantField::class,
    );
}
