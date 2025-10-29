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

namespace Scaledev\MiraklPhpConnector\Model;

use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;

/**
 * Class Attribute
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class Attribute extends AbstractModel
{
    /**
     * @var array
     */
    private $channels;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $default_value;

    /**
     * @var string
     */
    private $description;

    /**
     * @var array
     */
    private $description_translations;

    /**
     * @var string
     */
    private $example;

    /**
     * @var string
     */
    private $label;

    /**
     * @var array
     */
    private $label_translations;

    /**
     * @var string
     */
    private $requirement_level;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $type_parameter;

    /**
     * @var array
     */
    private $type_parameters;

    /**
     * @var string
     */
    private $unique_code;

    /**
     * @var string
     */
    private $validations;

    /**
     * @var boolean
     */
    private $variant;

    /**
     * @var string
     */
    private $hierarchy_code;

    /**
     * @var string
     */
    private $transformations;

    /**
     * @return string
     */
    public function getTransformations()
    {
        return $this->transformations;
    }

    /**
     * @param string $transformations
     * @return $this
     */
    public function setTransformations($transformations)
    {
        $this->transformations = $transformations;
        return $this;
    }

    /**
     * @return array
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * @param array $channels
     * @return $this
     */
    public function setChannels($channels)
    {
        $this->channels = $channels;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->default_value;
    }

    /**
     * @param string $default_value
     * @return $this
     */
    public function setDefaultValue($default_value)
    {
        $this->default_value = $default_value;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return array
     */
    public function getDescriptionTranslations()
    {
        return $this->description_translations;
    }

    /**
     * @param array $description_translations
     * @return $this
     */
    public function setDescriptionTranslations($description_translations)
    {
        $this->description_translations = $description_translations;
        return $this;
    }

    /**
     * @return string
     */
    public function getExample()
    {
        return $this->example;
    }

    /**
     * @param string $example
     * @return $this
     */
    public function setExample($example)
    {
        $this->example = $example;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return array
     */
    public function getLabelTranslations()
    {
        return $this->label_translations;
    }

    /**
     * @param array $label_translations
     * @return $this
     */
    public function setLabelTranslations($label_translations)
    {
        $this->label_translations = $label_translations;
        return $this;
    }

    /**
     * @return string
     */
    public function getRequirementLevel()
    {
        return $this->requirement_level;
    }

    /**
     * @param string $requirement_level
     * @return $this
     */
    public function setRequirementLevel($requirement_level)
    {
        $this->requirement_level = $requirement_level;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeParameter()
    {
        return $this->type_parameter;
    }

    /**
     * @param string $type_parameter
     * @return $this
     */
    public function setTypeParameter($type_parameter)
    {
        $this->type_parameter = $type_parameter;
        return $this;
    }

    /**
     * @return array
     */
    public function getTypeParameters()
    {
        return $this->type_parameters;
    }

    /**
     * @param array $type_parameters
     * @return $this
     */
    public function setTypeParameters($type_parameters)
    {
        $this->type_parameters = $type_parameters;
        return $this;
    }

    /**
     * @return string
     */
    public function getUniqueCode()
    {
        return $this->unique_code;
    }

    /**
     * @param string $unique_code
     * @return $this
     */
    public function setUniqueCode($unique_code)
    {
        $this->unique_code = $unique_code;
        return $this;
    }

    /**
     * @return string
     */
    public function getValidations()
    {
        return $this->validations;
    }

    /**
     * @param string $validations
     * @return $this
     */
    public function setValidations($validations)
    {
        $this->validations = $validations;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVariant()
    {
        return $this->variant;
    }

    /**
     * @param bool $variant
     * @return $this
     */
    public function setVariant($variant)
    {
        $this->variant = $variant;
        return $this;
    }

    /**
     * @return string
     */
    public function getHierarchyCode()
    {
        return $this->hierarchy_code;
    }

    /**
     * @param string $hierarchy_code
     * @return $this
     */
    public function setHierarchyCode($hierarchy_code)
    {
        $this->hierarchy_code = $hierarchy_code;
        return $this;
    }
}
