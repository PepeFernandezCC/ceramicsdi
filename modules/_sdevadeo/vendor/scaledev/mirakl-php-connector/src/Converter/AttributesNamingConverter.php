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
 * Class AttributesNamingConverter
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class AttributesNamingConverter extends AbstractConverter
{

    /**
     * @inheritDoc
     */
    public static function convert($field, $option = null)
    {
        $attributes = array();
        foreach ($field as $attribute) {
            if (!$attribute['hierarchy_code']) {
                $attributes[$attribute['code']] = $attribute;
                continue;
            }
            $attributeName = strstr($attribute['code'], $attribute['hierarchy_code'], true) ?: $attribute['code'];

            $attribute['code'] = $attributeName;
            $attribute['hierarchy_code'] = array($attribute['hierarchy_code']);
            unset($attribute['hierarchy_code']);
            $attributes[$attributeName] = $attribute;
        }

        return $attributes;
    }
}
