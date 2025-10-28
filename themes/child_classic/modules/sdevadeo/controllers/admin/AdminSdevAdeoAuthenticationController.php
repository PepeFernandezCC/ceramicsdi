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
use Scaledev\Adeo\Core\Tools;
use Scaledev\Adeo\Exception\TooLongConfigNameException;
use Scaledev\MiraklPhpConnector\Request\Platform\CheckEndpointHealthRequest;
use Scaledev\MiraklPhpConnector\Request\Shop\GetShopInformationRequest;
use Scaledev\MiraklPhpConnector\Response\Platform\CheckEndpointHealthResponse;
use Scaledev\MiraklPhpConnector\Response\Shop\GetShopInformationResponse;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'/../../autoload.php');

/**
 * Class AdminSdevAdeoAuthenticationController
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class AdminSdevAdeoAuthenticationController extends AbstractModuleAdminController
{
    const TEMPLATE = 'authentication';

    /**
     * @inheritdoc
     * @throws TooLongConfigNameException
     */
    public function renderList()
    {
        $this->context->smarty->assign(array(
            'api_key' => Configuration::getValue(Configuration::API_KEY),
            'test_mode' => (int)Configuration::getValue(Configuration::API_ENV),
            'lastShopInfoUpdate' => Configuration::getValue(Configuration::LAST_SHOP_INFO_IMPORT_DATE)
        ));
        return parent::renderList();
    }

    public function ajaxProcessSave()
    {
        $request = Tools::getRequest();
        $errorMessage = array();

        $hasError = (
            !array_key_exists(Configuration::API_KEY, $request)
            || !array_key_exists(Configuration::API_ENV, $request)
        );

        if (!$hasError) {
            try {
                $hasError = !(
                    Configuration::updateValue(Configuration::API_KEY, $request[Configuration::API_KEY])
                    && Configuration::updateValue(Configuration::API_ENV, $request[Configuration::API_ENV])
                );

                if ($hasError) {
                    $errorMessage[] = $this->module->l('An error occurred during parameters save.');
                } else {
                    $errorMessage[] = $this->module->l('Information successfully saved !');
                }
            } catch (TooLongConfigNameException $e) {
                $errorMessage[] = $e->getMessage();
            }
        } else {
            $errorMessage[] = $this->module->l('An error occurred about the integrity of the values sent internally.');
        }

        die(json_encode(array(
            'hasError' => $hasError,
            'errorMessage' => $errorMessage,
        )));
    }

    public function ajaxProcessTestConnection()
    {
        try {
            /** @var CheckEndpointHealthResponse $response */
            $response = (new CheckEndpointHealthRequest())
                ->execute(
                    Configuration::getValue(Configuration::API_KEY),
                    Configuration::getValue(Configuration::API_ENV)
                )
                ->getResponse();

            Configuration::updateValue(Configuration::LAST_SHOP_INFO_IMPORT_DATE, date('d-m-Y H:i:s'));
            die(json_encode(array(
                'testError' => false,
                'errorMessage' => array(
                    'Successfully connected !',
                    'API Version : '.$response->getVersion()
                )
            )));
        } catch (Exception $e) {
            if ($e->getCode() === 401) {
                die(json_encode(array(
                    'testError' => true,
                    'errorMessage' => array(
                        'Connection refused ! Please check your API key integrity. (Error code: ' .$e->getCode().')'
                    )
                )));
            } else {
                die(json_encode(array(
                    'testError' => true,
                    'errorMessage' => array(
                        'NOT connected ! Please check the API\'s server status. (Error code: ' .$e->getCode().')'
                    )
                )));
            }
        }
    }

    public function ajaxProcessUpdateShopInformation()
    {
        $result = array(
            'hasError' => false,
            'errorMessage' => array()
        );

        if (
            !Configuration::getValue(Configuration::API_KEY)
        ) {
            $result['hasError'] = true;
            $result['errorMessage'][] = $this->module->l('Missing API key');
            die(json_encode($result));
        }

        try {
            /** @var GetShopInformationResponse $response */
            $response = (new GetShopInformationRequest())
                ->execute(
                    Configuration::getValue(Configuration::API_KEY),
                    Configuration::getValue(Configuration::API_ENV)
                )
                ->getResponse();
            Configuration::updateValue(Configuration::SHOP_CHANNELS, implode($response->getShop()->getChannels(), ','));
            Configuration::updateValue(Configuration::LAST_SHOP_INFO_IMPORT_DATE, date('d-m-Y H:i:s'));

            $result['errorMessage'][] = $this->module->l('Information successfully updated !');
            die(json_encode($result));
        } catch (Exception $e) {
            $result['hasError'] = true;
            $result['errorMessage'][] = $e->getMessage();
        }
        die(json_encode($result));
    }
}
