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

use Scaledev\Adeo\Action\AcceptOrderAction;
use Scaledev\Adeo\Action\ImportOrdersAction;
use Scaledev\Adeo\Action\RetrievesOrdersAction;
use Scaledev\Adeo\Core\Controller\Front\AbstractModuleFrontController;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'/../../autoload.php');

/**
 * Class SdevAdeoAcceptOrdersActionModuleFrontController
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
class SdevAdeoAcceptOrdersActionModuleFrontController extends AbstractModuleFrontController
{
    /**
     * @inheritdoc
     */
    public function initContent()
    {
        parent::initContent();
        $mpOrders = (new RetrievesOrdersAction())->execute();
        $errors = array();
        foreach ($mpOrders['collection'][0] as $mpOrder) {
            switch ($mpOrder['order_state']) {
                case 'WAITING_ACCEPTANCE':
                    $param = array();
                    foreach ($mpOrder['order_lines']['list'] as $line) {
                        $param[] = ['accepted' => true, 'id' => $line['order_line_id']];
                    }
                    (new AcceptOrderAction())
                        ->setImportId($mpOrder['order_id'])
                        ->setOrderLineArray($param)
                        ->execute();
                    $processed['accepted'][] = $mpOrder['order_id'];
                    break;

                case 'SHIPPING':
                    try {
                        $newOrder = (new ImportOrdersAction())
                            ->setOrder($mpOrder)
                            ->execute();
                    } catch (Exception $e) {
                        $errors[$mpOrder] = $e->getMessage();
                    }
                    break;
                default:
                    continue(2);
            }
        }
        if (empty($errors)) {
            $errors = 'ok';
        }
        die($errors);
    }
}
