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

use Scaledev\Adeo\Action\RetrievesLogisticClassListAction;
use Scaledev\Adeo\Core\Controller\Admin\AbstractModuleAdminController;
use Scaledev\Adeo\Component\Configuration;
use Scaledev\Adeo\Core\Tools;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'/../../autoload.php');

/**
 * Class AdminSdevAdeoCategoriesRulesController
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class AdminSdevAdeoCategoriesRulesController extends AbstractModuleAdminController
{
    const TEMPLATE = 'categories_rules';

    /**
     * @inheritdoc
     */
    public function renderList()
    {
        Media::addJsDef(array(
            'nameFormatErrorMessage' => $this->module->l('An error has been encountered with the format of the name'),
            'shippingDelayErrorMessage' => $this->module->l('The shipping delay has to be an integer number.'),
            'minPriceFormatErrorMessage' => $this->module->l('An error occurred during with minimum price format (required).'),
            'maxPriceFormatErrorMessage' => $this->module->l('An error occurred during with maximum price format (required).'),
            'priceCompareErrorMessage' => $this->module->l('The maximum price appears to be lower than the minimum price.'),
            'valueFormatErrorMessage' => $this->module->l('An error occurred during with value format (required).'),
            'valueTypeErrorMessage' => $this->module->l('An error occurred during with type entry (required).'),
            'categoryLabel' => $this->module->l('Update of the API category list'),
            'attributeLabel' => $this->module->l('Update of the API attribute list'),
            'attributeUpdate' => $this->module->l('Mapping of the attributes by categories'),
            'deleteMessage' => $this->module->l('Do you really want to delete the rule ?'),
            'defaultLogisticClass' => SdevAdeoLogisticClass::DEFAULT_CODE
        ));
        $this->context->smarty->assign(array(
            'categoryRules' => SdevAdeoCategoryRule::findAll(),
            'marketplace_shipping' => json_decode(Configuration::getValue(Configuration::API_SHIPPING_METHODS), 1),
            'logisticClass' => SdevAdeoLogisticClass::findAll(),
            'defaultLogisticClass' => SdevAdeoLogisticClass::getLabelFromCode(SdevAdeoLogisticClass::DEFAULT_CODE)
        ));

        return parent::renderList();
    }

    public function ajaxProcessSaveCategoryRule()
    {
        $errorList = array(
            'hasError' => false,
            'errorMessage' => array(
            )
        );
        try {
            $request = Tools::getRequest();
            if ($request['idRule']) {
                $rule = new SdevAdeoCategoryRule($request['idRule']);
            } else {
                $rule = new SdevAdeoCategoryRule();
            }

            $rule->setName($request['categoryRuleName']);
            if (array_key_exists('shippingDelay', $request)) {
                $rule->setShippingDelay($request['shippingDelay']);
            }
            if (array_key_exists('additionalShippingCost', $request)) {
                $rule->setShippingCost($request['additionalShippingCost']);
            }
            if (array_key_exists('freeCarriers', $request) && json_encode($request['freeCarriers'])) {
                $rule->setFreeCarrierList(json_encode($request['freeCarriers']));
            }
            if (array_key_exists('priceAdjustment', $request)) {
                $rule->setAdditionalPrice($request['priceAdjustment']);
            }
            if (array_key_exists('adjustmentApplied', $request)) {
                $rule->setAddIfForcedPrice($request['adjustmentApplied']);
            }
            if (array_key_exists('logisticClass', $request) && $request['logisticClass']) {
                $rule->setLogisticClass($request['logisticClass']);
            } else {
                $rule->setLogisticClass(SdevAdeoLogisticClass::DEFAULT_CODE);
            }

            $rule->save();
            $errorList['idRule'] = $rule->getId();
            $errorList['errorMessage'][] = $this->module->l('Category rule successfully created.');
        } catch (Exception $e) {
            $errorList['hasError'] = true;
            $errorList['errorMessage'][] = $e->getMessage();
        }

        die(json_encode($errorList));
    }

    public function ajaxProcessEditCategoryRule()
    {
        $idRule = Tools::getRequest()['ruleId'];
        $rule = Db::getInstance()->executeS(sprintf(
            'SELECT * FROM `%s` WHERE id = %s;',
            SdevAdeoCategoryRule::getCompleteTableName(),
            $idRule
        ))[0];

        $rule['freeCarrierList'] = json_decode($rule['freeCarrierList']);
        $rule['pricingRule'] = Db::getInstance()->executeS(sprintf(
            'SELECT * FROM `%s` WHERE categoryRule = %s;',
            SdevAdeoPricingRule::getCompleteTableName(),
            $idRule
        ));

        die(json_encode($rule));
    }

    public function ajaxProcessDeleteCategoryRule()
    {
        $errorList = array(
            'hasError' => false,
            'errorMessage' => array(
            )
        );
        try {
            (new SdevAdeoCategoryRule(Tools::getRequest()['idRule']))->delete();
            $errorList['errorMessage'][] = $this->module->l("Category rule successfully deleted.");
        } catch (Exception $e) {
            $errorList['hasError'] = true;
            $errorList['errorMessage'][] = $e->getMessage();
        }
        die(json_encode($errorList));
    }

    public function ajaxProcessDeletePricingRule()
    {
        $errorList = array(
            'hasError' => false,
            'errorMessage' => array(
            )
        );
        try {
            (new SdevAdeoPricingRule(Tools::getRequest()['idRule']))->delete();
            $errorList['errorMessage'][] = $this->module->l("Pricing rule successfully deleted.");
        } catch (Exception $e) {
            $errorList['hasError'] = true;
            $errorList['errorMessage'][] = $e->getMessage();
        }
        die(json_encode($errorList));
    }

    public function ajaxProcessSavePricingRule()
    {
        $request = Tools::getRequest();
        $errorList = array(
            'hasError' => false,
            'errorMessage' => array(
            )
        );
        try {
            if ($new = !$request['idRule']) {
                $pricingRule = new SdevAdeoPricingRule();
            } else {
                $pricingRule = new SdevAdeoPricingRule($request['idRule']);
            }
            $pricingRule->setMinAmount($request['minPrice']);
            $pricingRule->setMaxAmount($request['maxPrice']);
            $pricingRule->setValue($request['valuePrice']);
            $pricingRule->setTypePercent($request['typePrice']);
            $pricingRule->setCategoryRule($request['categoryRule']);

            if ($new) {
                $pricingRule->add();
            } else {
                $pricingRule->update();
            }

            $errorList['idRule'] = $pricingRule->getId();
            $errorList['errorMessage'][] = $this->module->l("Pricing rule successfully created.");
        } catch (Exception $e) {
            $errorList['hasError'] = true;
            $errorList['errorMessage'][] = $e->getMessage();
        }
        die(json_encode($errorList));
    }

    public function ajaxProcessUpdateLogisticClasses()
    {
        $errorList = array(
            'hasError' => false
        );
        try {
            $classes = (new RetrievesLogisticClassListAction())->execute();
            SdevAdeoLogisticClass::resetTable();
        } catch (\Exception $e) {
            $errorList['hasError'] = true;
            $errorList['errorMessage'][] = $e->getMessage();
            die(json_encode($errorList));
        }

        /** @var \Scaledev\MiraklPhpConnector\Model\Platform\LogisticClass $mpClass */
        foreach ($classes as $mpClass) {
            try {
                $logisticClass = new SdevAdeoLogisticClass();
                if ($code = $mpClass->getCode()) {
                    $logisticClass->setCode($code);
                }
                if ($label = $mpClass->getLabel()) {
                    $logisticClass->setLabel($label);
                }
                if ($description = $mpClass->getDescription()) {
                    $logisticClass->setDescription($description);
                }
                $logisticClass->add();
            } catch (\Exception $e) {
                $errorList['hasError'] = true;
                $errorList['errorMessage'][] = $e->getMessage();
            }
        }

        if ($errorList['hasError'] == false) {
            $errorList['errorMessage'][] = $this->module->l('All the data have been correctly imported');
        }

        die(json_encode($errorList));
    }
}
