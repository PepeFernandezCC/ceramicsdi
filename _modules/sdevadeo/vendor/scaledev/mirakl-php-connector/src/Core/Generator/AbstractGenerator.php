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

namespace Scaledev\MiraklPhpConnector\Core\Generator;

use Scaledev\MiraklPhpConnector\Converter\PascalToKebabFieldConverter;
use Scaledev\MiraklPhpConnector\Converter\PascalToSnakeFieldConverter;
use Scaledev\MiraklPhpConnector\Core\Collection\CollectionInterface;
use Scaledev\MiraklPhpConnector\Exception\BadModelPropertyException;
use Scaledev\MiraklPhpConnector\Validator\ForbiddenValueValidator;
use Scaledev\MiraklPhpConnector\Validator\LengthValidator;
use Scaledev\MiraklPhpConnector\Validator\RequiredValidator;
use Scaledev\MiraklPhpConnector\Validator\Type\MultipleTypeValidator;
use Scaledev\MiraklPhpConnector\Validator\ValueValidator;

/**
 * Class AbstractGenerator
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
abstract class AbstractGenerator implements GeneratorInterface
{
    /**
     * Path of the file to populate
     *
     * @var string $filepath
     */
    private $filepath;

    /**
     * Collection to handle.
     *
     * @var CollectionInterface
     */
    protected $collection = null;

    /**
     * Name of the field currently handling
     *
     * @var string
     */
    protected $field;

    /**
     * File's content
     *
     * @var string $content
     */
    private $content;

    /**
     * Array to store model's properties
     *
     * @var array $temp_array
     */
    private $temp_array;

    /**
     * @inheritdoc
     */
    public function __construct($collection, $filepath)
    {
        $this->filepath = $filepath;
        $this->collection = $collection;

        return $this;
    }

    /**
     * @inheritdoc
     * @throws BadModelPropertyException
     */
    public function generateCsv()
    {
        $file = fopen($this->filepath, 'wb');
        // Handle headers
        $this->temp_array = array();
        $this->handleModel($this->field);
        fputcsv($file, $this->temp_array, ";");

        // Add elements to the collection
        foreach (($this->collection)->getList() as $model) {
            $this->temp_array = array();
            // Handle properties check and set
            $this->handleModel($this->field, $model);
            // Constraints checked
            fputcsv($file, $this->temp_array, ";");
        }

        return $this;
    }

    /**
     * @throws BadModelPropertyException
     */
    private function handleModel($field, $model = null)
    {
        // Handle header
        if ($model == null) {
            // Check every model's properties
            foreach ($field::CHILD_FIELD as $child_field) {
                $childFieldName = PascalToKebabFieldConverter::convert($child_field);
                if (defined("$child_field::PROPERTY_NAME")) {
                    $childFieldName = $child_field::PROPERTY_NAME;
                }
                $this->temp_array[] = $childFieldName;
            }
            return;
        }
        // Check values and add them
        foreach ($field::CONSTRAINTS as $constraint) {
            // Set the validation
            switch ($constraint) {
                case MultipleTypeValidator::class:
                    $option = array(
                        'field' => $field,
                        'model' => $model
                    );
                    break;
                case ValueValidator::class:
                    $option = $field::VALUES;
                    break;
                case ForbiddenValueValidator::class:
                    $option = $field::FORBIDDEN_VALUES;
                    break;
                case LengthValidator::class:
                    $option = $field::LENGTH;
                    break;
                default:
                    $option = null;
            }
            // Validate the field and throw error if required
            if (($result = $constraint::validate($model, $option)) != true) {
                if (!in_array(RequiredValidator::class, $field::CONSTRAINTS)) {
                    break;
                }
                throw (
                    new BadModelPropertyException(
                        $this->field,
                        PascalToSnakeFieldConverter::convert($field),
                        $constraint
                    )
                );
            }
        }

        if ($field::CHILD_FIELD == null || !is_array($field::CHILD_FIELD)) {
            return;
        }
        // Check every model's properties
        foreach ($field::CHILD_FIELD as $child_field) {
            $childFieldName = PascalToSnakeFieldConverter::convert($child_field);

            if ($child_field::TYPE != 'PARAMETER') {
                throw new BadModelPropertyException(
                    get_class($model),
                    $field,
                    $child_field::TYPE
                );
            }
            $value = $model->{'get' . str_replace('_', '', ucwords($childFieldName, '_'))}();

            // If validate field, assign to array
            if ($value != null) {
                $this->handleModel($child_field, $value);
            }
            $this->temp_array[] = $value;
        }
    }
}
