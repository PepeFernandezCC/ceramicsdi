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
 * @package Scaledev\Adeo
 * Support: support@scaledev.fr
 */

namespace Scaledev\Adeo\Exception;

use Scaledev\Adeo\Core\Exception\AbstractException;

/**
 * Class TooLongConfigNameException
 *
 * @package Scaledev\Adeo
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class TooLongConfigNameException extends AbstractException
{
    /**
     * TooLongConfigNameException constructor.
     *
     * @param string $configName
     * @param int $code
     */
    public function __construct($configName, $code = 0)
    {
        parent::__construct(
            sprintf(
                'The configuration\'s name must not exceed 32 characters! "%s" has %s characters.',
                $configName,
                strlen($configName)
            ),
            (int)$code
        );
    }

}
