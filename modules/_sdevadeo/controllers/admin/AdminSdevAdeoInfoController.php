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
use Scaledev\Adeo\Core\Controller\Admin\AbstractModuleAdminController;
use Scaledev\MiraklPhpConnector\Request\Platform\CheckEndpointHealthRequest;
use Scaledev\MiraklPhpConnector\Response\Platform\CheckEndpointHealthResponse;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'/../../autoload.php');

/**
 * Class AdminSdevAdeoInfoController
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class AdminSdevAdeoInfoController extends AbstractModuleAdminController
{
    const TEMPLATE = 'info';

    /**
     * @inheritdoc
     */
    public function renderList()
    {
        try {
            /** @var CheckEndpointHealthResponse $response */
            $response = (new CheckEndpointHealthRequest())
                ->execute(
                    Configuration::getValue(Configuration::API_KEY),
                    Configuration::getValue(Configuration::API_ENV)
                )
                ->getResponse();

            $apiState = array(
                'connected' => true,
                'message' => $response->getVersion()
            );
        } catch (Exception $e) {
            $apiState = array(
                'connected' => false,
                'message' => 'Not connected.'
            );
        }
        $this->context->smarty->assign(
        'apiState',
            $apiState
        );

        $overridesList = array(
            'classes' => $this->module->getClassesListFromDir(
                'override/classes/',
                defined('_PS_HOST_MODE')
            ),
            'controllers' => $this->module->getClassesListFromDir(
                'override/controllers/',
                defined('_PS_HOST_MODE_')
            ),
        );

        $this->context->smarty->assign(array(
            'hasOverrides' => $this->module->hasOverrides()
                || !empty($overridesList['classes'])
                || !empty($overridesList['controllers'])
            ,
            'overridesList' => $overridesList,
        ));

        return parent::renderList();
    }
}
