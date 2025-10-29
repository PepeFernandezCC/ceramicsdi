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
use Scaledev\Adeo\Core\Module;
use Scaledev\MiraklPhpConnector\Request\Product\ExportProductFileRequest;
use Scaledev\MiraklPhpConnector\Response\Product\ExportProductFileResponse;
use SdevAdeoImportLogs;

/**
 * Class SendProductFlowAction
 *
 * @package Scaledev\Adeo
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class SendProductFlowAction extends AbstractAction
{
    /** @var string Defines the file to send */
    private $filepath;

    /** @var string */
    private $api_key;

    /** @var string */
    private $api_env;

    /** @var string $filepath */
    public function setFilepath($filepath)
    {
        $this->filepath = $filepath;
        return $this;
    }

    /** @var string $api_key */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;
        return $this;
    }

    /** @var string $api_env */
    public function setApiEnv($api_env)
    {
        $this->api_env = $api_env;
        return $this;
    }

    public function execute()
    {
        $module = \Module::getInstanceByName(Module::NAME);
        $result = array(
            'hasError' => false,
            'errorMessage' => array(
            )
        );
        /** @var ExportProductFileResponse $response */
        $response = (new ExportProductFileRequest())
            ->setFilepath($this->filepath)
            ->execute(
                $this->api_key,
                $this->api_env
            )
            ->getResponse();
        if ($importId = $response->getImportNumber()) {
            $import = new SdevAdeoImportLogs();
            $import->setShopId(\Context::getContext()->shop->id)
                ->setIdImport($importId)
                ->setIsProductImport(true);
            if ($import->add()) {
                Configuration::updateValue(Configuration::DATE_PRODUCT_FLOW, date('d-m-Y H:i:s'));
            }
            $result['errorMessage'][] = $module->l('Product flow successfully sent. ID: ').$importId;
            return $result;
        }
        $result['hasError'] = true;
        $result['errorMessage'][] = $module->l('En error occured during product flow sending.');
        return $result;
    }
}
