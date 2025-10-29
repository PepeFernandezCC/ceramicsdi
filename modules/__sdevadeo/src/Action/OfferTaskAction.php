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
use Scaledev\Adeo\Core\Tools;

/**
 * Class OfferTaskAction
 *
 * @package Scaledev\Adeo
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class OfferTaskAction extends AbstractAction
{

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $module = \Module::getInstanceByName(Module::NAME);
        if (Configuration::getValue(Configuration::OFFER_FLOW_IN_PROGRESS)) {
            die(json_encode(['error' => $module->l('A similar action is currently in progress on the server')]));
        }
        // Block other functionalities
        Configuration::updateValue(Configuration::OFFER_FLOW_IN_PROGRESS, true);

        $action = (new GenerateOfferFlowAction())
            ->setFlowType(Configuration::getValue(Configuration::DEFAULT_FLOW_TYPE))
            ->setNbOffersMax($max = Tools::getProductListToProceed(true))
            ->setProductBurst($max)
            ->setIsAutomaticTask(true)
        ;
        if (Configuration::updateValue(Configuration::DATE_OFFER_FLOW, date('d-m-Y H:i:s'))) {
            $action->execute();
        }

        // Send the generated flow
        $import_type = Configuration::getValue(Configuration::DEFAULT_FLOW_TYPE);
        ($sendAction = (new SendOfferFlowAction()))
            ->setFilepath(dirname(dirname(dirname(__FILE__))) . '/fluxs/offers/' . str_replace('-', '_', Tools::str2url(\Context::getContext()->shop->name)).'/Offers.csv')
            ->setImportType($import_type)
            ->setApiKey(Configuration::getValue(Configuration::API_KEY))
            ->setApiEnv(Configuration::getValue(Configuration::API_ENV))
        ;
        $import = $sendAction->execute();

        // Unlock functionality
        Configuration::updateValue(Configuration::OFFER_FLOW_IN_PROGRESS, false);
        return $import;
    }
}
