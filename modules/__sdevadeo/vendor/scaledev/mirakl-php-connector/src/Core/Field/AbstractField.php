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

namespace Scaledev\MiraklPhpConnector\Core\Field;

/**
 * Class AbstractField
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
abstract class AbstractField implements FieldInterface
{
    /**
     * Array of validators
     *
     * @var array
     */
    const CONSTRAINTS = null;

    /**
     * Type of the field
     *
     * @var string | object
     */
    const TYPE = self::DEFAULT_TYPE;

    /**
     * Array of the child field class
     *
     * @var array
     */
    const CHILD_FIELD = null;

    /**
     * Definition of the length field value
     *
     * @var array
     */
    const LENGTH = null;

    /**
     * Available Values for the field
     *
     * @var array
     */
    const VALUES = null;

    /**
     * Forbidden Values for the field
     *
     * @var array
     */
    const FORBIDDEN_VALUES = null;

    /**
     * Exact property typographie to look for
     *
     * @var string
     */
    const API_PROPERTY = null;
}
