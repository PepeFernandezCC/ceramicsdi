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
use Scaledev\MiraklPhpConnector\Request\Order\AcceptOrderRequest;

/**
 * Class AcceptOrderAction
 *
 * @package Scaledev\Adeo
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class AcceptOrderAction extends \Scaledev\Adeo\Core\Action\AbstractAction
{
    /** @var string */
    private $import_id;

    /** @var array */
    private $order_line_array;

    /**
     * @param string $import_id
     * @return $this
     */
    public function setImportId($import_id)
    {
        $this->import_id = $import_id;
        return $this;
    }

    /**
     * @param array $order_line_array
     * @return $this
     */
    public function setOrderLineArray($order_line_array)
    {
        $this->order_line_array = $order_line_array;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        (new AcceptOrderRequest())
            ->setRequestParameter($this->import_id)
            ->setPutFields(['order_lines' => $this->order_line_array])
            ->execute(
                Configuration::getValue(Configuration::API_KEY),
                Configuration::getValue(Configuration::API_ENV)
            )
        ;
    }
}
