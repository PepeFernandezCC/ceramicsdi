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

namespace Scaledev\MiraklPhpConnector\Core\Builder;

use Scaledev\MiraklPhpConnector\Builder\GenericBuilder;
use Scaledev\MiraklPhpConnector\Collection\Component\Order\OrderLine\OrderLineTaxCollection;
use Scaledev\MiraklPhpConnector\Core\Collection\CollectionInterface;
use Scaledev\MiraklPhpConnector\Converter\PascalToSnakeFieldConverter;
use Scaledev\MiraklPhpConnector\Core\Model\ModelInterface;
use Scaledev\MiraklPhpConnector\Exception\BadClassThrownException;
use Scaledev\MiraklPhpConnector\Exception\BadModelPropertyException;
use Scaledev\MiraklPhpConnector\Exception\NotFoundException;
use Scaledev\MiraklPhpConnector\Field\Order\GetOrdersList\OrdersField\CustomerField\AddressField\StreetField;
use Scaledev\MiraklPhpConnector\Model\Order\OrderLine\OrderLineTax;
use Scaledev\MiraklPhpConnector\Validator\LengthValidator;
use Scaledev\MiraklPhpConnector\Validator\RequiredValidator;
use Scaledev\MiraklPhpConnector\Validator\Type\MultipleTypeValidator;
use Scaledev\MiraklPhpConnector\Validator\ValueValidator;

/**
 * Class AbstractBuilder
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
abstract class AbstractBuilder implements BuilderInterface
{
    /**
     * Name of the field currently handling
     *
     * @var string
     */
    protected $field;

    /**
     * Name of the current subject handled
     *
     * @var string
     */
    protected $subjectName = null;

    /**
     * Result of the request to handle.
     *
     * @var array
     */
    protected $requestResult = array();

    /**
     * Built object for the response from the request result
     *
     * @var CollectionInterface | ModelInterface
     */
    private $objectBuilt;

    /**
     * Temporary model to populate
     *
     * @var object $tempModel
     */
    private $tempModel;

    /**
     * @inheritDoc
     */
    public function getBuilt()
    {
        return $this->objectBuilt;
    }

    /**
     * @inheritdoc
     * @throws BadModelPropertyException
     * @throws BadClassThrownException
     * @throws NotFoundException
     */
    public function __construct($resultList)
    {
        $this->requestResult = (array)$resultList;

        return $this->build();
    }

    /**
     * Build the object of the response.
     *
     * @return $this
     * @throws BadModelPropertyException
     * @throws BadClassThrownException
     * @throws NotFoundException
     */
    private function build()
    {
        // Get the name of the Api Parameter and the property name
        $this->subjectName = PascalToSnakeFieldConverter::convert($this->field);

        // Set data and object to handle
        $data = isset($this->requestResult[$this->subjectName]) ? $this->requestResult[$this->subjectName] : $this->requestResult;
        $objectName = ($this->field);
        /** BuilderInterface $objectname */
        $objectName = $objectName::TYPE;

        if (class_exists($objectName)) {
            $object = new $objectName();
            if ($object instanceof CollectionInterface ) {
                $modelName = $object::ELEMENT_NAME;
                // Add elements to the collection
                foreach ($data as $element) {
                    $this->tempModel = new $modelName();
                    // Handle properties check and set
                    $this->handleParameter($this->field, $element);
                    // Constraints checked
                    $object->add($this->tempModel);
                }
                $this->objectBuilt = $object;

                return $this;

            } else if ($object instanceof ModelInterface) {
                $this->tempModel = $object;
                // Handle properties check and set
                $this->handleParameter($this->field, $data);
                // Set built object to class property
                $this->objectBuilt = $this->tempModel;

                return $this;
            } else {
                throw (new BadClassThrownException($objectName, 'instance of ModelInterface or CollectionInterface'));
            }
        } else if (!is_array($data)) {
            $this->handleParameter($this->field, $data);
            $this->objectBuilt = $data;
            return $this;
        } else {
            foreach ($data as $result) {
                $this->handleParameter($this->field, $result);
                $this->objectBuilt = $result;
            }
            return $this;
        }
    }

    /**
     * @param $field
     * @param $data
     * @return void
     * @throws BadClassThrownException
     * @throws BadModelPropertyException
     * @throws NotFoundException
     */
    private function handleParameter($field, $data)
    {
        foreach ($field::CONSTRAINTS as $constraint) {
            // Set the validation
            switch ($constraint) {
                case MultipleTypeValidator::class:
                    $option = array(
                        'field' => $field,
                        'model' => $this->tempModel
                    );
                    break;
                case ValueValidator::class:
                    $option = $field::VALUES;
                    break;
                case LengthValidator::class:
                    $option = $field::LENGTH;
                    break;
                default:
                    $option = null;
            }
            // Validate the field and throw error if required
            if (($invalid = $constraint::validate($data, $option)) !== true) {
                if (!in_array(RequiredValidator::class, $field::CONSTRAINTS)) {
                    break;
                }
                throw (
                    new BadModelPropertyException(
                        $invalid,
                        PascalToSnakeFieldConverter::convert($field),
                        $constraint
                    )
                );
            }
        }

        if ($field::CHILD_FIELD == null || !is_array($field::CHILD_FIELD)) {
            return;
        }
        foreach ($field::CHILD_FIELD as $child_field) {
            $childFieldName = PascalToSnakeFieldConverter::convert($child_field);
            if (class_exists($className = $child_field::TYPE)) {
                $object = new $className();
                if (
                    !($object instanceof CollectionInterface ||
                    $object instanceof ModelInterface)
                ) {
                    throw new BadModelPropertyException(
                        get_class($this->tempModel),
                        $this->subjectName,
                        $child_field::TYPE
                    );
                }
                $param['field'] = $child_field;
                $param['data'] = $data;
                $value = (new GenericBuilder($param))->getBuilt();
            } else {
                if ($child_field::TYPE != 'PARAMETER') {
                    throw new BadModelPropertyException(
                        get_class($this->tempModel),
                        $this->subjectName,
                        $child_field::TYPE
                    );
                }

                $value = null;
                if (isset($data[$childFieldName])) {
                    $value = $data[$childFieldName];
                } else if (
                    $value == null
                    && $child_field::API_PROPERTY !== null
                    && isset($data[$child_field::API_PROPERTY])
                ) {
                    $value = $data[$child_field::API_PROPERTY];
                }
                $this->handleParameter($child_field, $value);
            }
            $this->tempModel->{'set' . str_replace('_', '', ucwords($childFieldName, '_'))}($value);
        }
    }
}
