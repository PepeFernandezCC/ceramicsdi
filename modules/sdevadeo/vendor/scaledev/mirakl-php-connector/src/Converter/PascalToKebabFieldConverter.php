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

namespace Scaledev\MiraklPhpConnector\Converter;

use Scaledev\MiraklPhpConnector\Core\Converter\AbstractConverter;

/**
 * Class PascalToKebabFieldConverter
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class PascalToKebabFieldConverter extends AbstractConverter
{
    /**
     * @inheritDoc
     */
    public static function convert($field, $option = null)
    {
        $fieldName = substr($field, strrpos($field, '\\') + 1);
        $rawClassName = substr($fieldName, 0, strrpos($fieldName, 'Field'));
        $className = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $rawClassName));
        return self::handleFieldException($className);
    }

    /**
     * Handle difference between file name and field name
     *
     * @param string $name
     * @return string $name
     */
    private static function handleFieldException($name)
    {
        if (!in_array($name, ['street', 'street_secondary'])) {
            return $name;
        }
        switch ($name) {
            case 'street':
                $name = 'street1';
                break;
            case 'street_secondary':
                $name = 'street2';
                break;
            default:
                break;
        }
        return $name;
    }
}
