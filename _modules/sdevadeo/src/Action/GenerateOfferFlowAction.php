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

use Context;
use Scaledev\Adeo\Component\Configuration;
use Scaledev\Adeo\Core\Action\AbstractAction;
use Scaledev\Adeo\Core\Tools;
use StockAvailable;

/**
 * Class GenerateOfferFlowAction
 *
 * @package Scaledev\Adeo
 * @author Louis Pavoine <contact@scaledev.fr>
 */
final class GenerateOfferFlowAction extends AbstractAction
{
    /** @var int Defines the stats "new" of a product */
    const PRODUCT_CONDITION = 11;

    /** @var array Array to stock the skus in order to not duplicate them */
    private $skus = array();

    /** @var string Type of offer flow */
    private $flowType;

    /** @var bool */
    private $isAutomaticTask = false;

    /**
     * State of the product's number integrated to the flow
     *
     * @var null|int
     */
    private $currentProductsNb = null;

    /**
     * Defines the burst size
     *
     * @var null|int
     */
    private $productBurst = null;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var false|resource
     */
    private $csvFile;

    /**
     * @var array
     */
    private $logs = array();

    /**
     * @var array
     */
    private $fields = array();

    /**
     * @var int $nbOffersMax Number total to proceed
     */
    private $nbOffersMax = 0;

    /**
     * @var int $nbOffersFiltered - Total of products filtered.
     */
    private $nbOffersFiltered = 0;

    /**
     * @var int $nbOffersInError - Total of products in error.
     */
    private $nbOffersInError = 0;

    /**
     * @var int $nbOffersProceeded - Total of products in error.
     */
    private $nbOffersProceeded = 0;

    /** @var string */
    private $shippingCountry;

    const COMMON_SKU = 'sku';
    const COMMON_PRODUCT_ID = 'product-id';
    const COMMON_PRODUCT_TYPE = 'product-id-type';
    const COMMON_PRICE = 'price';
    const COMMON_DISCOUNT_PRICE = 'discount-price';
    const COMMON_LEAD_TIME = 'leadtime-to-ship';
    const COMMON_STATE = 'state';
    const COMMON_QUANTITY = 'quantity';
    const COMMON_SHIPPING_COUNTRY = 'shipment-origin';
    const COMMON_LOGICTIC_CLASS = 'logistic-class';
    const COMMON_PLATFORMS = array('vat-lmfr', 'vat-bmfr', 'vat-lmit', 'vat-lmes', 'vat-lmpt');

    /**
     * @return int|null
     */
    public function getCurrentProductsNb()
    {
        return $this->currentProductsNb;
    }

    /**
     * @param int|null $currentProductsNb
     * @return $this
     */
    public function setCurrentProductsNb($currentProductsNb)
    {
        $this->currentProductsNb = $currentProductsNb;
        return $this;
    }

    /**
     * @return string
     */
    public function getFlowType()
    {
        return $this->flowType;
    }

    /**
     * @param string $flowType
     * @return $this
     */
    public function setFlowType($flowType)
    {
        $this->flowType = $flowType;
        return $this;
    }

    /**
     * @return int
     */
    public function getNbOffersMax()
    {
        return $this->nbOffersMax;
    }

    /**
     * @param int $nbOffersMax
     * @return $this
     */
    public function setNbOffersMax($nbOffersMax)
    {
        $this->nbOffersMax = $nbOffersMax;
        return $this;
    }

    /**
     * @param int|null $productBurst
     * @return $this
     */
    public function setProductBurst($productBurst)
    {
        $this->productBurst = $productBurst;
        return $this;
    }

    /**
     * @param bool $isAutomaticTask
     * @return $this
     */
    public function setIsAutomaticTask($isAutomaticTask)
    {
        $this->isAutomaticTask = $isAutomaticTask;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $error = [];
        if (!($shippingCountry = Configuration::getValue(Configuration::SHIPPING_COUNTRY))) {
            $error[] = $this->getModule()->l('Set a shipping country from parameters first');
        }
        if (!empty($error)) {
            Configuration::updateValue(Configuration::OFFER_FLOW_IN_PROGRESS, false);
            die(json_encode(['error' => $error]));
        }
        $this->shippingCountry = $shippingCountry;
        $this->context = \Context::getContext();
        if (!$this->productBurst) {
            $this->productBurst = Configuration::getValue(Configuration::PRODUCT_BURST) ?: 100;
        }
        try {
            $this->startCsv();
            $this->addOffers();
        } catch (\Exception $e) {
            Configuration::updateValue(Configuration::OFFER_FLOW_IN_PROGRESS, false);
            die(json_encode([
                'error' => $e->getMessage()
            ]));
        }
    }

    /**
     * Create or edit CSV file.
     *
     * @return void
     */
    private function startCsv()
    {
        $csvHeader = array();
        foreach (
            array_merge([
                self::COMMON_SKU,
                self::COMMON_PRODUCT_ID,
                self::COMMON_PRODUCT_TYPE,
                self::COMMON_PRICE,
                self::COMMON_DISCOUNT_PRICE,
                self::COMMON_STATE,
                self::COMMON_QUANTITY,
                self::COMMON_SHIPPING_COUNTRY,
                self::COMMON_LOGICTIC_CLASS,
                self::COMMON_LEAD_TIME
            ], self::COMMON_PLATFORMS) as $field) {
            $csvHeader[] = $field;
        }
        foreach ([self::COMMON_PRICE, self::COMMON_DISCOUNT_PRICE] as $field) {
            foreach (explode(',', Configuration::getValue(Configuration::SHOP_CHANNELS)) as $channelCode) {
                $csvHeader[] = $field.'[channel='.$channelCode.']';
            }
        }

        $this->fields = $csvHeader;

        // Set csv file and write header
        $dir = dirname(dirname(dirname(__FILE__))) . '/fluxs/offers/' . str_replace('-', '_', Tools::str2url($this->context->shop->name));
        if (!is_dir($dir)) {
            mkdir(
                $dir,
                0755,
                true
            );
        }
        $file_url = $dir . '/Offers.csv';

        if (!$this->currentProductsNb) {
            $dir = \Module::getInstanceByName('sdevadeo')->getLocalPath().'logs/flux/offer/';
            $logs_file = $dir.'offer_'.Tools::strtolower(str_replace('-', '_', Tools::str2url($this->context->shop->name)).'.txt');
            if (file_exists($logs_file)) {
                file_put_contents($logs_file, '');
            }
            $this->csvFile = fopen($file_url, 'w+');
            // Add header
            fputcsv($this->csvFile, $csvHeader, ';');
        } else {
            $this->csvFile = fopen($file_url, 'a+');
        }
    }

    private function addOffers()
    {
        $integrateDisabled = Configuration::getValue(Configuration::DISABLED_PRODUCT);
        $productList = Tools::getProductListToProceed(false, $this->productBurst, $this->currentProductsNb);
        $productList = array_unique($productList, SORT_REGULAR);

        if (empty($productList)) {
            Configuration::updateValue(Configuration::OFFER_FLOW_IN_PROGRESS, false);
            die(json_encode([
                'error' => $this->getModule()->l('No products found on this context is offer ready')
            ]));
        }

        foreach ($productList as $productReference) {
            $product = new \Product($productReference['id_product']);
            $this->nbOffersProceeded++;
            // Disabled product
            if (!$product->active && !$integrateDisabled) {
                $this->logs[] = Tools::addProductFlowLogs($this->context->shop->id, $this->flowType, 201, $product, $productReference['id_product_attribute']);
                $this->nbOffersFiltered++;
                continue;
            }
            $this->makeOffer($product, $productReference['id_product_attribute']);
        }
        if (Configuration::updateValue(Configuration::OFFER_FLOW_IN_PROGRESS, false)) {
            if (!$this->isAutomaticTask) {
                die(json_encode(array(
                    'logs' => $this->logs,
                    'nbOffersFiltered' => $this->nbOffersFiltered,
                    'nbOffersInError' => $this->nbOffersInError,
                    'nbOffersMax' => $this->nbOffersMax,
                )));
            }
        }
    }

    /**
     * @param \Product $product
     * @param $productAttribute
     * @throws \PrestaShopDatabaseException
     * @throws \Scaledev\Adeo\Exception\TooLongConfigNameException
     */
    private function makeOffer($product, $idProductAttribute)
    {
        if ($idProductAttribute) {
            $productAttribute = new \Combination($idProductAttribute);
        } else {
            $productAttribute = null;
        }

        // TAX RULE
        $mappedTaxes = json_decode(Configuration::getValue(Configuration::TAX_MAPPING), 1);
        $productTaxRulesGroup = $product->getIdTaxRulesGroup();
        $taxes = array();
        foreach (self::COMMON_PLATFORMS as $taxName) {
            $countryIso = substr($taxName, -2, 2);
            $tax_id = Tools::getTaxByTaxRulesGroupAndCountry($productTaxRulesGroup, \Country::getByIso($countryIso));
            $tax = array_search($tax_id, $mappedTaxes);
            if ($tax) {
                $taxes[$taxName] = $tax;
                continue;
            }
            $this->nbOffersInError++;
            $this->logs[] = Tools::addProductFlowLogs($this->context->shop->id, $this->flowType, 311, $product, $idProductAttribute);
            return;
        }

        // CATEGORY RULE
        if (!Tools::isCategoryEnabled($product->id_category_default, $this->context->shop->id)) {
            $this->nbOffersFiltered++;
            $this->logs[] = Tools::addProductFlowLogs($this->context->shop->id, $this->flowType, 202, $product, $idProductAttribute);
            return;
        }
        $categoryRule = Tools::getCategoryRule($product->id_category_default, $this->context->shop->id);

        // FILTERS
        $manufacturers_exclude = Tools::jsonDecode(Configuration::getValue(Configuration::EXCL_MANUFACTURER), true);
        $suppliers_exclude = Tools::jsonDecode(Configuration::getValue(Configuration::EXCL_SUPPLIER), true);

        if (!is_array($manufacturers_exclude)) {
            $manufacturers_exclude = array();
        }

        if (!is_array($suppliers_exclude)) {
            $suppliers_exclude = array();
        }

        // Check if manufacturer product is excluded.
        // If it is: SDEV-ERROR-203.
        if (
            $product->id_manufacturer
            && in_array(
                $product->id_manufacturer,
                $manufacturers_exclude
            )
        ) {
            $this->nbOffersFiltered++;
            $this->logs[] = Tools::addProductFlowLogs($this->context->shop->id, $this->flowType, 204, $product, $idProductAttribute);
            return;
        }

        // Check if supplier product is excluded.
        // If it is: SDEV-ERROR-204.
        if (
            $product->id_supplier
            && in_array($product->id_supplier
                , $suppliers_exclude
            )
        ) {
            $this->nbOffersFiltered++;
            $this->logs[] = Tools::addProductFlowLogs($this->context->shop->id, $this->flowType, 205, $product, $idProductAttribute);
            return;
        }

        // LOGISTIC CLASS
        $logisticClass = !$categoryRule || !array_key_exists('logisticClass', $categoryRule) || !$categoryRule['logisticClass']
            ?  \SdevAdeoLogisticClass::DEFAULT_CODE
            : $categoryRule['logisticClass']
        ;

        // SKU & PRODUCT-ID
        $eanReference = $productAttribute ? $productAttribute->ean13 : $product->ean13;
        if (!$eanReference || strlen($eanReference) !== 13) {
            $this->nbOffersInError++;
            $this->logs[] = Tools::addProductFlowLogs($this->context->shop->id, $this->flowType, 302, $product, $idProductAttribute);
            return;
        }
        $shopReference = $productAttribute ? $productAttribute->reference : $product->reference;

        if (in_array($shopReference, $this->skus)) {
            $this->nbOffersInError++;
            $this->logs[] = Tools::addProductFlowLogs($this->context->shop->id, $this->flowType, 307, $product, $idProductAttribute);
            return;
        }
        $this->skus[] = $shopReference;

        if ($product->condition !== 'new') {
            $this->nbOffersInError++;
            $this->logs[] = Tools::addProductFlowLogs($this->context->shop->id, $this->flowType, 309, $product, $idProductAttribute);
            return;
        }
        $state = self::PRODUCT_CONDITION;

        $productPriceVatIncl = array();
        $priceType = ['full'];
        if ((bool)Configuration::getValue(Configuration::ENABLED_DISCOUNT)) {
            $priceType[] = 'discount';
        }
        foreach ($priceType as $price) {
            $tempPrice = \Product::getPriceStatic(
                $product->id,
                true,
                (int)$idProductAttribute,
                2,
                null,
                false,
                !($price == 'full') && (bool)Configuration::getValue(Configuration::ENABLED_DISCOUNT)
            );
            if ($categoryRule) {
                // ATTRIBUTE RULE PRICE ADJUSTMENT
                if ($categoryRule['additionalPrice']) {
                    $tempPrice += (($tempPrice * $categoryRule['additionalPrice']) / 100);
                }
                // PRICING RULE
                if ($categoryRule['pricingRule']) {
                    foreach ($categoryRule['pricingRule'] as $pricingRule) {
                        if (($tempPrice >= (float)$pricingRule['minAmount']) && ($tempPrice < (float)$pricingRule['maxAmount'])) {
                            if ($pricingRule['pricingRuleTypePercent']) {
                                $tempPrice += (float)$pricingRule['pricingRuleValue'];
                            } else {
                                $tempPrice += (($tempPrice * (float)$pricingRule['pricingRuleValue']) / 100);
                            }
                        }
                    }
                }
            }

            if ($tempPrice <= 0) {
                if ($price == 'full') {
                    $this->nbOffersInError++;
                    $this->logs[] = Tools::addProductFlowLogs($this->context->shop->id, $this->flowType, 305, $product, $idProductAttribute);
                    return;
                }
                $productPriceVatIncl[$price] = '';
            } else {
                $productPriceVatIncl[$price] = $tempPrice;
            }
        }

        $quantity = StockAvailable::getQuantityAvailableByProduct($product->id, $idProductAttribute);
        if ($quantity < 0) {
            $quantity = 0;
        }

        $discount = array_key_exists('discount', $productPriceVatIncl)
            && (
                $productPriceVatIncl['discount'] == ''
                || $productPriceVatIncl['discount'] < $productPriceVatIncl['full']
            ) ? $productPriceVatIncl['discount'] : '';

        //PRODUCT LINE CREATION
        $offerLine = array();
        // COMMON ATTRIBUTES
        foreach ($this->fields as $field) {
            switch ($field) {
                case self::COMMON_SKU:
                    $offerLine[] = $shopReference;
                    break;
                case self::COMMON_PRODUCT_ID:
                    $offerLine[] = $eanReference;
                    break;
                case self::COMMON_PRODUCT_TYPE:
                    $offerLine[] = 'EAN';
                    break;
                case self::COMMON_PRICE:
                    $offerLine[] = $productPriceVatIncl['full'];
                    break;
                case self::COMMON_DISCOUNT_PRICE:
                    $offerLine[] = $discount;
                    break;
                case self::COMMON_STATE:
                    $offerLine[] = $state;
                    break;
                case self::COMMON_QUANTITY:
                    $offerLine[] = $quantity;
                    break;
                case self::COMMON_SHIPPING_COUNTRY:
                    $offerLine[] = $this->shippingCountry;
                    break;
                case self::COMMON_LOGICTIC_CLASS:
                    $offerLine[] = $logisticClass;
                    break;
                case self::COMMON_LEAD_TIME:
                    $offerLine[] = $categoryRule && $categoryRule['shippingDelay']
                        ? $categoryRule['shippingDelay']
                        : '0'
                    ;
                    break;
                default:
                    if (in_array($field, self::COMMON_PLATFORMS)) {
                        $offerLine[] = $taxes[$field];
                        break;
                    }
                    if (preg_match('('.self::COMMON_DISCOUNT_PRICE.'\[)' ,$field)) {
                        $offerLine[] = $discount;
                    } else if (preg_match('('.self::COMMON_PRICE.'\[)' ,$field)) {
                        $offerLine[] = $productPriceVatIncl['full'];
                    }
            }
        }

        if (fputcsv($this->csvFile, $offerLine, ';')) {
            $this->logs[] = Tools::addProductFlowLogs($this->context->shop->id, $this->flowType, 100, $product, $idProductAttribute);
        }
    }
}
