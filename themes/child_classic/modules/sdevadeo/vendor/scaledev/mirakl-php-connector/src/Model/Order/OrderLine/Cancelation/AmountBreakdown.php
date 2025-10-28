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

namespace Scaledev\MiraklPhpConnector\Model\Order\OrderLine\Cancelation;

use Scaledev\MiraklPhpConnector\Collection\Component\Order\OrderLine\CancelationCollection\PartCollection;
use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;

/**
 * Class AmountBreakdown
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class AmountBreakdown extends AbstractModel
{
    /**
     * The parts which constitute the total amount.
     * Each part can have different invoicing rules. The sum of the amount of each part is equal to the total amount. There is at least one part and maximum two parts.
     *
     * @var PartCollection
     */
    private $parts;

    /**
     * @return PartCollection
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * @param PartCollection $parts
     * @return $this
     */
    public function setParts($parts)
    {
        $this->parts = $parts;
        return $this;
    }
}
