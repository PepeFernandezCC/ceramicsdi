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

use Scaledev\Adeo\Action\GenerateOfferFlowAction;
use Scaledev\Adeo\Action\SendOfferFlowAction;
use Scaledev\Adeo\Action\SendProductFlowAction;
use Scaledev\Adeo\Core\Controller\Admin\AbstractModuleAdminController;
use Scaledev\Adeo\Action\GenerateProductsFlowAction;
use Scaledev\Adeo\Component\Configuration;
use Scaledev\Adeo\Core\Tools;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'/../../autoload.php');

/**
 * Class AdminSdevAdeoProductsFlowController
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class AdminSdevAdeoProductsFlowController extends AbstractModuleAdminController
{
    const TEMPLATE = 'products_flow';

    /**
     * @inheritdoc
     */
    public function renderList()
    {
        $context = Context::getContext();
        $shopName = str_replace('-', '_', Tools::str2url($context->shop->name));

        Media::addJsDef(array(
            'issueMessage' => $this->module->l('An issue occurred during flow generation, please try later or contact support.'),
            'successButton' => $this->module->l('Download success report'),
            'errorButton' => $this->module->l('Download error report')
        ));
        $this->context->smarty->assign(array(
            'shop_name_formatted' => $shopName,
            'context' => $context,
            'module_url' => Tools::getShopDomain(true) . _MODULE_DIR_ . $this->module->name . '/',
            'token' => Configuration::getValue(Configuration::MODULE_TOKEN),
            'last_product_flow_date' => Configuration::getValue(Configuration::DATE_PRODUCT_FLOW),
            'last_offer_flow_date' => Configuration::getValue(Configuration::DATE_OFFER_FLOW),
            'log_file_url' => $this->module->getLocalPath(),
            'product_log_exist' => file_exists($this->module->getLocalPath().'logs/flux/product/product_'.$shopName.'.txt'),
            'offer_log_exist' => file_exists($this->module->getLocalPath().'logs/flux/offer/offer_'.$shopName.'.txt'),
            'mapping_wizard_url' => 'https://adeo-marketplace.mirakl.net/mmp/shop/catalog/mapping/wizard'
        ));

        return parent::renderList();
    }

    public function ajaxProcessGenerateProductFlow()
    {
        // Stop if a similar action is being processed
        if (Configuration::getValue(Configuration::PRODUCT_FLOW_IN_PROGRESS)) {
            die(json_encode(['error' => $this->module->l('A similar action is currently in progress on the server')]));
        }
        // Block other functionalities
        Configuration::updateValue(Configuration::PRODUCT_FLOW_IN_PROGRESS, true);
        $request = Tools::getRequest();
        $action = (new GenerateProductsFlowAction())
            ->setcurrentProductsNb($request['currentProductsNb']);
        if (!$request['nbProductsMax']) {
            Configuration::updateValue(Configuration::DATE_PRODUCT_FLOW, date('d-m-Y H:i:s'));
            $action->setNbProductsMax(Tools::getProductListToProceed(true));
        } else {
            $action->setNbProductsMax($request['nbProductsMax']);
        }
        try {
            $action->execute();
        } catch (Exception $e) {
            try {
                $isSuccess = Configuration::updateValue(Configuration::PRODUCT_FLOW_IN_PROGRESS, false);
            } catch (Exception $e_bis) {
                die(json_encode([
                    'hasError' => true,
                    'errorMessage' => array(
                        $e->getMessage(),
                        $e_bis->getMessage()
                    )
                ]));
            }

            $errorMessage = array($e->getMessage());
            if (!$isSuccess) {
                $errorMessage[] = $this->module->l('An issue occurs to flow security, please contact support');
            }
            die(json_encode([
                'hasError' => true,
                'errorMessage' => $errorMessage,
            ]));
        }
    }

    public function ajaxProcessSendProductFlow()
    {
        if (Configuration::getValue(Configuration::PRODUCT_FLOW_IN_PROGRESS)) {
            die(json_encode([
                'hasError' => true,
                'errorMessage' => $this->module->l('A similar action is currently in progress on the server')
            ]));
        }
        $shopName = Tools::str2url($this->context->shop->name);
        $result = (new SendProductFlowAction())
            ->setFilepath(dirname(dirname(dirname(__FILE__))) . '/fluxs/products/' . str_replace('-', '_', $shopName).'/Products.csv')
            ->setApiKey(Configuration::getValue(Configuration::API_KEY))
            ->setApiEnv(Configuration::getValue(Configuration::API_ENV))
        ;
        die(json_encode($result->execute()));
    }

    public function ajaxProcessGenerateOfferFlow()
    {
        $request = Tools::getRequest();
        // Stop if a similar action is being processed
        if (Configuration::getValue(Configuration::OFFER_FLOW_IN_PROGRESS) && !$request['currentOffersNb']) {
            die(json_encode(['error' => $this->module->l('A similar action is currently in progress on the server')]));
        }
        // Block other functionalities
        Configuration::updateValue(Configuration::OFFER_FLOW_IN_PROGRESS, true);

        $action = (new GenerateOfferFlowAction())
            ->setFlowType($request['flowType'])
            ->setcurrentProductsNb($request['currentOffersNb']);
        if (!$request['nbOffersMax']) {
            Configuration::updateValue(Configuration::DATE_OFFER_FLOW, date('d-m-Y H:i:s'));
            $action->setNbOffersMax(Tools::getProductListToProceed(true));
        } else {
            $action->setNbOffersMax($request['nbOffersMax']);
        }

        try {
            $action->execute();
        } catch (Exception $e) {
            try {
                $isSuccess = Configuration::updateValue(Configuration::OFFER_FLOW_IN_PROGRESS, false);
            } catch (Exception $e_bis) {
                die(json_encode([
                    'hasError' => true,
                    'errorMessage' => array(
                        $e->getMessage(),
                        $e_bis->getMessage()
                    )
                ]));
            }

            $errorMessage = array($e->getMessage());
            if (!$isSuccess) {
                $errorMessage[] = $this->module->l('An issue occurs to flow security, please contact support');
            }
            die(json_encode([
                'hasError' => true,
                'errorMessage' => $errorMessage,
            ]));
        }
    }

    public function ajaxProcessSendOfferFlow()
    {
        // Don't send the flow if currently generating
        if (Configuration::getValue(Configuration::OFFER_FLOW_IN_PROGRESS)) {
            die(json_encode([
                'hasError' => true,
                'errorMessage' => [$this->module->l('A similar action is currently in progress on the server')]
            ]));
        }

        $firstImport = false;
        $import_type = Tools::getRequest()['flowType'];
        $shopName = Tools::str2url($this->context->shop->name);
        $sendAction = (new SendOfferFlowAction())
            ->setFilepath(dirname(dirname(dirname(__FILE__))) . '/fluxs/offers/' . str_replace('-', '_', $shopName).'/Offers.csv')
            ->setImportType($import_type)
            ->setApiKey(Configuration::getValue(Configuration::API_KEY))
            ->setApiEnv(Configuration::getValue(Configuration::API_ENV))
        ;
        die(json_encode($sendAction->execute()));
    }

    public function ajaxProcessUpdateOfferFlowReports()
    {
        Configuration::updateValue(Configuration::DATE_OFFER_REPORT, $date = date('d-m-Y H:i:s'));
        die(json_encode(Tools::updateOfferReport($date)));
    }

    public function ajaxProcessUpdateProductFlowReports()
    {
        Configuration::updateValue(Configuration::DATE_PRODUCT_REPORT, date('d-m-Y H:i:s'));
        die(json_encode(Tools::updateProductReport()));
    }
}
