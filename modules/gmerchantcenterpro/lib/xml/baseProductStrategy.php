<?php
/**
 * Google Merchant Center Pro
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

namespace Gmerchantcenterpro\Xml;

if (!defined('_PS_VERSION_')) {
    exit;
}
use Gmerchantcenterpro\Common\fileClass;
use Gmerchantcenterpro\Configuration\moduleConfiguration;
use Gmerchantcenterpro\Dao\moduleDao;
use Gmerchantcenterpro\Models\Reporting;
use Gmerchantcenterpro\ModuleLib\moduleReporting;
use Gmerchantcenterpro\ModuleLib\moduleTools;

abstract class baseProductStrategy extends baseXml
{
    /**
     * @var array : array for all parameters provided to generate XMl files
     */
    protected static $aParamsForXml = [];

    /**
     * @var string : stock the strategy type
     */
    protected $sType = '';

    /**
     * @var string : store the XML content
     */
    public $sContent = '';

    /**
     * @var array : array of params
     */
    public $aParams = [];

    /**
     * @var int : count the number of product processed
     */
    public $iCounter = 0;

    /**
     * @var obj : store the current obj to handle
     */
    protected $oCurrentProd;

    /**
     * @var bool : define the export mode
     */
    protected $bExport;

    /**
     * @var obj : store currency / shipping / zone / carrier
     */
    public $data;

    /**
     * @param array $aParams
     */
    public function __construct($aParams = [])
    {
        $this->data = new \stdClass();
        $this->sContent = '';
        $this->aParams = $aParams;
        $this->iCounter = 0;
        $this->bExport = isset($aParams['bExport']) ? $aParams['bExport'] : 0;
        $this->bOutput = isset($aParams['bOutput']) ? $aParams['bOutput'] : 0;

        if (!empty($aParams['type'])) {
            $this->sType = $aParams['type'];
        }
    }

    /**
     * store into the matching object the product and combination
     *
     * @param obj $oData
     * @param obj $oProduct
     * @param array $aCombination
     *
     * @return array
     */
    abstract public function setProductData(&$oData, $oProduct, $aCombination);

    /**
     * construct the XML content
     *
     * @param obj $oData
     * @param obj $oProduct
     * @param array $aCombination
     */
    abstract public function buildProductXml($oData, $oProduct, $aCombination);

    /**
     * load Products for XML
     *
     * bool $bExportCombination
     * bool $bExcludedProduct
     *
     * @return array
     */
    public function loadProduct($bExportCombination = false, $bExcludedProduct = false)
    {
        // get currency ISO
        // $sCurrencyIso = moduleConfiguration::GMCP_AVAILABLE_COUNTRIES[$this->aParams['sLangIso']][$this->aParams['sCountryIso']]['currency'];

        // set different vars required to calculate some things
        $this->data->currencyId = \Currency::getIdByIsoCode(\Tools::strtolower($this->aParams['sCurrencyIso']));
        $this->data->currency = new \stdClass();
        $this->data->currency = new \Currency($this->data->currencyId);

        // store the current carrier
        $this->data->currentCarrier = new \stdClass();
        if (!empty(\GMerchantCenterPro::$conf['GMCP_SHIP_CARRIERS'][\Tools::strtoupper($this->aParams['sCountryIso'])])) {
            $carrier = new \Carrier((int) \GMerchantCenterPro::$conf['GMCP_SHIP_CARRIERS'][\Tools::strtoupper($this->aParams['sCountryIso'])]);

            if ((int) $carrier->id == (int) $carrier->id_reference) {
                $this->data->currentCarrier = $carrier;
            } else {
                $carrier_updated = \Carrier::getCarrierByReference($carrier->id_reference);
                $this->data->currentCarrier = $carrier_updated;
            }
        }
        $this->data->countryId = \Country::getByIso($this->aParams['sCountryIso']);
        $this->data->currentZone = new \stdClass();
        $this->data->currentZone = new \Zone((int) \Country::getIdZone((int) $this->data->countryId));
        $this->data->shippingConfig = \Configuration::getMultiple(['PS_SHIPPING_FREE_PRICE', 'PS_SHIPPING_FREE_WEIGHT', 'PS_SHIPPING_HANDLING', 'PS_SHIPPING_METHOD']);

        \Context::getContext()->currency = new \Currency((int) $this->data->currencyId);
        \Context::getContext()->cookie->id_country = $this->data->countryId;
        \Context::getContext()->cookie->id_currency = $this->data->currencyId;

        return moduleDao::getProductIds($this->aParams['iShopId'], $this->bExport, false, $this->aParams['iFloor'], $this->aParams['iStep'], $bExportCombination, $bExcludedProduct);
    }

    /**
     * generate get the XML for current data feed type
     */
    public function setParams(array $aParams)
    {
        $this->aParams = $aParams;
        $this->bExport = isset($aParams['bExport']) ? $aParams['bExport'] : 0;
        $this->bOutput = isset($aParams['bOutput']) ? $aParams['bOutput'] : 0;
    }

    /**
     * check if combinations and return them
     *
     * @param int $iProdId
     * @param bool $bExcludedProduct
     *
     * @return bool
     */
    public function hasCombination($iProdId, $bExcludedProduct)
    {
        // check if combinations
        return $this->oCurrentProd->hasCombination($iProdId, $bExcludedProduct);
    }

    /**
     * the number of products processed
     *
     * @return int
     */
    public function getProcessedProduct()
    {
        return (int) $this->iCounter;
    }

    /**
     * generate get the XML for current data feed type
     *
     * @params array $aParams
     *
     * @return array
     */
    public function generate(array $aParams = null)
    {
        // set
        $aAssign = [];
        $aProducts = [];
        $aCombinations = [];

        if (empty(self::$aParamsForXml)) {
            self::$aParamsForXml = moduleConfiguration::GMCP_PARAM_FOR_XML;
        }

        try {
            foreach (self::$aParamsForXml as $sParamName) {
                $mValue = \Tools::getValue($sParamName);
                if ($mValue !== false) {
                    $$sParamName = $mValue;
                } else {
                    throw new \Exception(\GMerchantCenterPro::$oModule->l('One or more of mandatory parameters have not been provided, please check the list in the current class', 'base-product-strategy_class') . '.', 800);
                }
            }

            // detect if we force the reporting or not
            $bForceReporting = !empty($aParams['reporting']) ? $aParams['reporting'] : false;
            $bForceReporting = ($bForceReporting !== false) ? $bForceReporting : \GMerchantCenterPro::$conf['GMCP_REPORTING'];

            $aFreeShippingProducts = [];

            if (!empty(\GMerchantCenterPro::$conf['GMCP_FREE_SHIP_PROD'])) {
                if (is_string(\GMerchantCenterPro::$conf['GMCP_FREE_SHIP_PROD'])) {
                    \GMerchantCenterPro::$conf['GMCP_FREE_SHIP_PROD'] = moduleTools::handleGetConfigurationData(\GMerchantCenterPro::$conf['GMCP_FREE_SHIP_PROD'], ['allowed_classes' => false]);
                }
                foreach (\GMerchantCenterPro::$conf['GMCP_FREE_SHIP_PROD'] as $sProdIds) {
                    list($iProdId, $iAttrId) = explode('¤', $sProdIds);
                    $aFreeShippingProducts[$iProdId][] = $iAttrId;
                }
            }
            // set params
            $aParams = [
                'bExport' => \GMerchantCenterPro::$conf['GMCP_EXPORT_MODE'],
                'iShopId' => (int) $iShopId,
                'iLangId' => (int) $iLangId,
                'sLangIso' => $sLangIso,
                'sCountryIso' => $sCountryIso,
                'sGmcLink' => \GMerchantCenterPro::$conf['GMCP_LINK'],
                'sCurrencyIso' => $sCurrencyIso,
                'iFloor' => (int) $iFloor,
                'iStep' => (int) $iStep,
                'iTotal' => (int) $iTotal,
                'iProcess' => (int) $iProcess,
                'bOutput' => \Tools::getValue('bOutput'),
                'sType' => !empty($sFeedType) ? $sFeedType : \Tools::getValue('feed_type'),
                'sFreeShipping' => $aFreeShippingProducts,
                'bUseTax' => moduleTools::isTax($sLangIso, $sCountryIso),
            ];

            // get the XMl strategy
            $this->setParams($aParams);

            // composition of File Obj into XMlStrategy
            $this->setFile(fileClass::create());

            // check if reporting is activated
            Reporting::cleanTable(\Tools::strtoupper($sLangIso) . '_' . $sCountryIso . '_' . $sCurrencyIso, \GMerchantCenterPro::$iShopId);

            // detect if this is the first step
            if ((int) $iFloor == 0) {
                // reset the XMl file
                $this->write(moduleConfiguration::GMCP_SHOP_PATH_ROOT . $sFilename, '');

                // create header
                $this->header($aParams);
            }

            // load products
            $aProducts = $this->loadProduct(\GMerchantCenterPro::$conf['GMCP_P_COMBOS'], $bExcludedProduct);

            foreach ($aProducts as $aProduct) {
                // get the instance of the product
                $oProduct = new \Product((int) $aProduct['id'], true, (int) $iLangId);

                // check if validate product
                if (
                    \Validate::isLoadedObject($oProduct)
                    && $oProduct->active
                    && ((isset($oProduct->available_for_order)
                        && $oProduct->available_for_order)
                        || empty($oProduct->available_for_order))
                ) {
                    // define the strategy
                    $sXmlProductType = $oProduct->hasAttributes() && !empty(\GMerchantCenterPro::$conf['GMCP_P_COMBOS']) ? 'Combination' : 'Product';

                    // set the matching object
                    $this->getProdType($sXmlProductType, $aParams);

                    // check if combinations
                    $aCombinations = $this->hasCombination($oProduct->id, $bExcludedProduct);

                    if (!empty($aCombinations)) {
                        foreach ($aCombinations as $aCombination) {
                            $this->buildProductXml($this->data, $oProduct, $aCombination);
                        }
                    }
                }
            }

            // get the number of products really processed
            $aAssign['process'] = (int) ($iProcess + $this->getProcessedProduct());

            // detect if the last step
            if (((int) $iFloor + (int) $iStep) >= $iTotal) {
                $this->footer($aParams);

                // store the nb of products really processed by the export action
                moduleReporting::create()->set('counter', ['products' => $aAssign['process']]);

                // define the status of the feed generation
                $aAssign['bContinueStatus'] = false;
                $aAssign['bFinishStatus'] = true;
            } else {
                // define the status of the feed generation
                $aAssign['bContinueStatus'] = true;
                $aAssign['bFinishStatus'] = false;
            }

            // write
            $this->write(moduleConfiguration::GMCP_SHOP_PATH_ROOT . $sFilename, $this->sContent, false, true);

            // merge reporting file's content + current reporting
            $aReporting = moduleReporting::create()->mergeData();

            if (!empty($aReporting)) {
                Reporting::addReporting(\Tools::strtoupper($sLangIso) . '_' . $sCountryIso . '_' . $sCurrencyIso, $aReporting, \GMerchantCenterPro::$iShopId);
            }
        } catch (\Exception $e) {
            $aErrorParam = ['msg' => $e->getMessage(), 'code' => $e->getCode()];

            if (moduleConfiguration::GMCP_DEBUG) {
                $aErrorParam['file'] = $e->getFile();
                $aErrorParam['trace'] = $e->getTraceAsString();
            }
            $aAssign['aErrors'][] = $aErrorParam;
        }

        return [
            'tpl' => 'admin/feed-generate-output.tpl',
            'assign' => $aAssign,
        ];
    }

    /**
     * instantiate matched strategy object
     *
     * @param string $strategy
     * @param array $params
     *
     * @return obj ctrl type
     *
     * @throws Exception
     */
    public static function get($strategy, array $params = null)
    {
        try {
            $strategy = strtolower($strategy);
            if ($strategy == 'product') {
                return new xmlProductStrategy($params);
            } elseif ($strategy == 'local') {
                return new xmlLocalStrategy($params);
            } else {
                return false;
            }
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }
    }

    /**
     * instantiate matched product object
     *
     * @param string $product_type
     * @param array $params
     *
     * @return obj ctrl type
     *
     * @throws Exception
     */
    public function getProdType($product_type, array $params = null)
    {
        try {
            $product_type = strtolower($product_type);

            if ($product_type == 'combination') {
                $this->oCurrentProd = new xmlCombination($params);
            } elseif ($product_type == 'product') {
                return $this->oCurrentProd = new xmlProduct($params);
            } else {
                return false;
            }
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }
    }
}
