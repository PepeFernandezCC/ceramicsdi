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
 * Class AdditionalField
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class AdditionalField extends AbstractModel
{
    /**
     * Additional field's code
     * API data: code
     *
     * @required
     * @var string
     */
    private $code;

    /**
     * Additional field's type
     * API data: type
     *
     * @value BOOLEAN, DATE, LINK, LIST, MULTIPLE_VALUES_LIST, NUMERIC, REGEX, STRING, TEXTAREA
     * @var string
     */
    private $type;

    /**
     * Additional field's value
     * API data: value
     *
     * @required
     * @var string
     */
    private $value;

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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function getValueParameterBuilder($type)
    {
        $constraint = $type == 'MULTIPLE_VALUES_LIST' ? 'array' : 'string';
        return array(
            'value' => array(
                'constraints' => array(
                    'is_required' => 0,
                    'type' => $constraint,
                ),
                'model_method' => 'setValue',
            )
        );
    }
}
