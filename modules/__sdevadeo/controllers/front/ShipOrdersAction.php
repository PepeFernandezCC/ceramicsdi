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

use Scaledev\Adeo\Component\Configuration;
use Scaledev\Adeo\Core\Controller\Front\AbstractModuleFrontController;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'/../../autoload.php');

/**
 * Class SdevAdeoShipOrdersActionModuleFrontController
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
class SdevAdeoShipOrdersActionModuleFrontController extends AbstractModuleFrontController
{
    /**
     * @inheritdoc
     */
    public function initContent()
    {
        parent::initContent();
        if (Configuration::getValue(Configuration::SHIPPING_CRON)) {
            $action = new Scaledev\Adeo\Action\UpdateTrackingAction();
            $action->execute();

            die(Configuration::updateValue(Configuration::LAST_SHIPPING, date('Y-m-d H:i:s')) == 1);
        }
        die('Cron shipping disabled');
    }
}
