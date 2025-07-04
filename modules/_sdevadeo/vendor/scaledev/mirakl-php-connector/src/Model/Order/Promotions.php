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

namespace Scaledev\MiraklPhpConnector\Model\Order;

use Scaledev\MiraklPhpConnector\Collection\Component\Order\Promotion\AppliedPromotionCollection;
use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;

/**
 * Class Promotion
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class Promotions extends AbstractModel
{
    /**
     * @var AppliedPromotionCollection
     */
    private $applied_promotion;

    /**
     * @var number
     */
    private $total_deduced_amount;

    /**
     * @return AppliedPromotionCollection
     */
    public function getAppliedPromotion()
    {
        return $this->applied_promotion;
    }

    /**
     * @param AppliedPromotionCollection $applied_promotion
     * @return $this
     */
    public function setAppliedPromotion($applied_promotion)
    {
        $this->applied_promotion = $applied_promotion;
        return $this;
    }

    /**
     * @return number
     */
    public function getTotalDeducedAmount()
    {
        return $this->total_deduced_amount;
    }

    /**
     * @param number $total_deduced_amount
     * @return $this
     */
    public function setTotalDeducedAmount($total_deduced_amount)
    {
        $this->total_deduced_amount = $total_deduced_amount;
        return $this;
    }
}
