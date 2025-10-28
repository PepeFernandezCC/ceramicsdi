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
use Scaledev\Adeo\Core\Tools;
use Scaledev\Adeo\Core\Controller\Admin\AbstractModuleAdminController;
use Scaledev\Adeo\Exception\TooLongConfigNameException;
use Scaledev\MiraklPhpConnector\Model\ShippingMethod;
use Scaledev\MiraklPhpConnector\Request\Platform\CheckEndpointHealthRequest;
use Scaledev\MiraklPhpConnector\Request\Platform\GetShippingMethodListRequest;
use Scaledev\MiraklPhpConnector\Response\Platform\GetShippingMethodListResponse;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'/../../autoload.php');

/**
 * Class AdminSdevAdeoParametersController
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class AdminSdevAdeoParametersController extends AbstractModuleAdminController
{
    const TEMPLATE = 'parameters';

    /**
     * @inheritdoc
     * @throws PrestaShopDatabaseException|TooLongConfigNameException
     */
    public function renderList()
    {
        $shipping_countries = array('DE','AD','AT','BE','BG','CY','HR','DK','ES','EE','FI','FR','GR','HU','IE','IS','IT','LV','LI','LT','LU','MK','MT','MD','MC','NO','NL','PL','PT','CZ','RO','GB','SK','SI','CH','SE','UA','SM','IC','AL');
        sort($shipping_countries);

        $description = array(
            array(
                'value' => 'short',
                'text' => $this->module->l('Short description.')
            ),
            array(
                'value' => 'long',
                'text' => $this->module->l('Long description.')
            ),
            array(
                'value' => 'both',
                'text' => $this->module->l('Both description.')
            ),
            array(
                'value' => 'none',
                'text' => $this->module->l('No description and No title.')
            ),
        );

        Media::addJsDef(array(
            'missingParameter' => $this->module->l('A mandatory parameter is missing to save the data'),
            'parameters' => [
                'api_shipping' => $this->module->l('API carrier field')
            ]
        ));

        $manufacturers = Manufacturer::getManufacturers();

        $suppliers = Supplier::getSuppliers();
        $flowType = array(
            'selected' => Configuration::getValue(Configuration::DEFAULT_FLOW_TYPE),
            'list' => array(
                'NORMAL' => $this->module->l('Normal update'),
                'PARTIAL_UPDATE' => $this->module->l('Partial update'),
                'REPLACE' => $this->module->l('Replace offers in place')
            )
        );

        $this->context->smarty->assign(array(
            'ps_version' => _PS_VERSION_,

            // generals
            'discount' => (int)Configuration::getValue(Configuration::ENABLED_DISCOUNT),
            'sales' => (int)Configuration::getValue(Configuration::ENABLED_SALES),
            'description_value' => Configuration::getValue(Configuration::USED_DESCRIPTION),
            'description_options' => $description,
            'automatic_validation' => (int)Configuration::getValue(Configuration::AUTO_VALIDATE),
            'disabled_products' => (int)Configuration::getValue(Configuration::DISABLED_PRODUCT),
            'disabled_categories' => (int)Configuration::getValue(Configuration::DISABLED_CAT),
            'enabled_countries' => json_decode(Configuration::getValue(Configuration::ENABLED_COUNTRIES)),
            'shipping_countries' => $shipping_countries,
            'shipping_country' => Configuration::getValue(Configuration::SHIPPING_COUNTRY),

            // products
            'products' => Configuration::getValue(Configuration::PRODUCT_BURST),
            'flow_type' => $flowType,
            'cms_taxes' => Tax::getTaxes(Context::getContext()->language->id),
            'mp_taxes' => array('Standard', 'Reduced', 'SpecialReduced', 'Exoneration'),
            'mapped_taxes' => is_array($mappedTaxes = json_decode(Configuration::getValue(Configuration::TAX_MAPPING), 1)) ? $mappedTaxes : [],

            // orders
            'order_states' => Tools::getOnlyIdAndName(OrderState::getOrderStates($this->context->language->id)),
            'imported_state' => Configuration::getValue(Configuration::IMPORTED_STATE),
            'shipped_state' => json_decode(Configuration::getValue(Configuration::SHIPPED_STATE)),
            'cron_shipment' => Configuration::getValue(Configuration::SHIPPING_CRON),
            'last_shipment_cron' => Configuration::getValue(Configuration::LAST_SHIPPING),

            //carriers
            'shipping_additional' => Configuration::getValue(Configuration::SHIPPING_COST),
            'internal_carriers' => Carrier::getCarriers($this->context->language->id, true, false, false, null, 5),
            'apiShippingUpdateDate' => Configuration::getValue(Configuration::DATE_UPD_METHOD),

            // filters
            'manufacturer_list' => Tools::getOnlyIdAndName($manufacturers),
            'supplier_list' => Tools::getOnlyIdAndName($suppliers),
            'manufacturers_excluded' => json_decode(Configuration::getValue(Configuration::EXCL_MANUFACTURER)),
            'suppliers_excluded' => json_decode(Configuration::getValue(Configuration::EXCL_SUPPLIER)),
        ));

        $this->checkConnection();

        return parent::renderList();
    }

    public function ajaxProcessSave()
    {
        $hasError = false;
        $errorMessage = array();
        foreach(Tools::getRequest() as $key => $value) {
            try {
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                if ($value === 'empty') {
                    $value = null;
                }
                if (!(Configuration::updateValue($key, $value))) {
                    $hasError = true;
                    $errorMessage[] = $this->module->l('An error occurred during save of the parameter: ') . $key;
                }
            } catch (TooLongConfigNameException $e) {
                $errorMessage[] = $e->getMessage();
            }
        }
        if (!$hasError) {
            $errorMessage[] = $this->module->l('Information successfully saved !');
        }

        die(json_encode(array(
            'hasError' => $hasError,
            'errorMessage' => $errorMessage,
        )));
    }

    private function checkConnection()
    {
        try {
            (new CheckEndpointHealthRequest())
                ->execute(
                    Configuration::getValue(Configuration::API_KEY),
                    Configuration::getValue(Configuration::API_ENV)
                )
                ->getResponse();

            $shipping_methods = json_decode(Configuration::getValue(Configuration::API_SHIPPING_METHODS), 1);

            $carrier_rules = array();
            if (!empty($rules = SdevAdeoCarrierRule::findAll())) {
                foreach ($rules as $key => $rule) {
                    foreach ($shipping_methods as $method) {
                        if ($method['code'] == $rule['marketplaceShippingCode']) {
                            $carrier_rules[$method['code']] = $rule[SdevAdeoCarrierRule::COLUMN_INTERNAL_CARRIER_ID];
                            break;
                        }
                    }
                }
            }

            $this->context->smarty->assign(array(
                'carrier_rules' => $carrier_rules,
                'marketplace_shipping' => $shipping_methods,
                'isConnected' => true,
            ));
        } catch (Exception $e) {
            $this->context->smarty->assign(array(
                'isConnected' => false,
            ));
        }
    }

    /**
     * @throws TooLongConfigNameException
     */
    public function ajaxProcessUpdateShippingMethods()
    {
        try {
            /** @var GetShippingMethodListResponse $response */
            $response = (new GetShippingMethodListRequest())
                ->execute(
                    Configuration::getValue(Configuration::API_KEY),
                    Configuration::getValue(Configuration::API_ENV)
                )
                ->getResponse();
            $methods = array();
            /** @var ShippingMethod $method */
            foreach ($response->getShippingMethodCollection()->getList() as $method) {
                $methods[] = array(
                    'code' => $method->getCode(),
                    'description' => $method->getDescription(),
                    'label' => $method->getLabel()
                );
            }
            Configuration::updateValue(Configuration::API_SHIPPING_METHODS, json_encode($methods));
            Configuration::updateValue(Configuration::DATE_UPD_METHOD, date('d-m-Y'));

            die(json_encode(array(
                'hasError' => false,
                'errorMessage' => array(
                    $this->module->l('Marketplace\'s shipping methods successfully updated.')
                ),
                'methods' => $methods
            )));
        } catch (Exception $e) {
            die(json_encode(array(
                'hasError' => true,
                'errorMessage' => array(
                    $e->getMessage()
                )
            )));
        }
    }

    /**
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     * @throws TooLongConfigNameException
     */
    public function ajaxProcessSaveCarrierRule()
    {
        $hasError = false;
        $errorMessage = array();
        $model = false;
        $request = Tools::getRequest();

        if (!is_array($request['carrier_rules']) || empty($request['carrier_rules'])) {
            return json_encode(array(
                'hasError' => true,
                'errorMessage' => $this->module->l('No carrier rule detected.'),
            ));
        }

        try {
            foreach($request['carrier_rules'] as $ruleToProceed) {
                $id = Db::getInstance()->getValue((new \DbQuery())
                    ->select(SdevAdeoCarrierRule::COLUMN_ID)
                    ->from(SdevAdeoCarrierRule::getTableName())
                    ->where(SdevAdeoCarrierRule::COLUMN_MARKETPLACE_SHIPMENT . ' = \'' . pSQL($ruleToProceed[SdevAdeoCarrierRule::COLUMN_MARKETPLACE_SHIPMENT]).'\'')
                );
                if (!$id) {
                    $id = null;
                }
                $rule = new SdevAdeoCarrierRule($id);
                foreach ($ruleToProceed as $propertyName => $value) {
                    $method = 'set' . ucfirst($propertyName);
                    $rule->$method($value);
                }
                if (!$rule->save()) {
                    $hasError = true;
                }
            }
        } catch (Exception $e) {
            $hasError = true;
            $errorMessage[] = $e->getMessage();
        }

        if ($hasError) {
            $errorMessage[] = $this->module->l('An error occurred during the save of the carrier rule.');
        } else {
            $errorMessage[] = $this->module->l('The carrier rules has successfully been saved.');
        }

        die(json_encode(array(
            'hasError' => $hasError,
            'errorMessage' => $errorMessage
        )));
    }

    public function ajaxProcessDeleteCarrierRule()
    {
        $errorMessage = array();
        $model = false;
        $request = Tools::getRequest();

        try {
            $rule = new SdevAdeoCarrierRule($request['ruleId']);
            $rule->delete();

            die(json_encode(array(
                'hasError' => false,
                'errorMessage' => array(
                    $this->module->l('Carrier\'s rule successfully deleted.')
                )
            )));
        } catch (Exception $e) {
            die(json_encode(array(
                'hasError' => true,
                'errorMessage' => array(
                    $e->getMessage()
                )
            )));
        }
    }
}
