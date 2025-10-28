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

namespace Scaledev\Adeo\Action;

use Scaledev\Adeo\Component\Configuration;
use Scaledev\Adeo\Core\Action\AbstractAction;
use Scaledev\MiraklPhpConnector\Request\Order\ValidateShipmentRequest;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class ShipOrdersAction
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class ValidateShipmentAction extends AbstractAction
{
    /** @var object order proceeding */
    private $order;

    /**
     * @param object $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        (new ValidateShipmentRequest())
            ->setRequestParameter($this->order)
            ->execute(
                Configuration::getValue(Configuration::API_KEY),
                Configuration::getValue(Configuration::API_ENV)
            )
        ;
    }
}
