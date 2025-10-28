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

use Category;
use Context;
use Scaledev\Adeo\Component\Configuration;
use Scaledev\Adeo\Core\Action\AbstractAction;
use Scaledev\Adeo\Core\Tools;
use StockAvailable;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class GenerateProductsFlowAction
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class GenerateProductsFlowAction extends AbstractAction
{
    /** @var array Array to stock the id_features */
    private $featureList = array();

    /** @var array Array to stock the id_attribute_groups */
    private $attributeGroupList = array();

    /**
     * Type of flow currently proceeded
     *
     * @var string
     */
    const FLOW_TYPE = 'PRODUCT';

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
    private $attributes;

    /**
     * @var string
     */
    private $logs;

    /**
     * @var array
     */
    private $integrationStatus = array();

    /**
     * @var int $nbProductsMax Number total to proceed
     */
    private $nbProductsMax = 0;

    /**
     * @var int $products_filtered - Total of products filtered.
     */
    private $nbProductsFiltered = 0;

    /**
     * @var int $products_in_error - Total of products in error.
     */
    private $nbProductsInError = 0;

    /**
     * @var int $products_in_error - Total of products in error.
     */
    private $nbProductsProceeded = 0;

    /**
     * @var array $availableLanguages - Language usable in this context
     */
    private $availableLanguages = array();

    const COMMON_CATEGORY = 'product_category';
    const COMMON_SHOP_SKU = 'shop_sku';
    const COMMON_EAN = 'gtin_EAN13';
    const COMMON_BRAND = 'feature_06575_brand';
    const COMMON_SKU = 'sku';
    const COMMON_PRODUCT_ID = 'product-id';
    const COMMON_PRODUCT_ID_TYPE = 'product-id-type';
    const COMMON_PRICE = 'price';
    const COMMON_QUANTITY = 'quantity';
    const COMMON_MIN_QUANTITY = 'min-quantity-alert';

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
     * @return int
     */
    public function getNbProductsMax()
    {
        return $this->nbProductsMax;
    }

    /**
     * @param int $nbProductsMax
     * @return $this
     */
    public function setNbProductsMax($nbProductsMax)
    {
        $this->nbProductsMax = $nbProductsMax;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        Configuration::updateValue(Configuration::PRODUCT_FLOW_IN_PROGRESS, true);
        $this->context = \Context::getContext();
        $this->productBurst = Configuration::getValue(Configuration::PRODUCT_BURST) ?: 100;
        $this->startCsv();
        try {
            $this->addProducts();
        } catch (\Exception $e) {
            dump($e->getMessage(), $e->getTrace());die;
        }
        Configuration::updateValue(Configuration::PRODUCT_FLOW_IN_PROGRESS, false);
    }

    private function addProducts()
    {
        $integrateDisabled = Configuration::getValue(Configuration::DISABLED_PRODUCT);
        $productList = Tools::getProductListToProceed(false, $this->productBurst, $this->currentProductsNb);

        foreach ($productList as $productReference) {
            $product = new \Product($productReference['id_product']);
            $this->nbProductsProceeded++;
            // Disabled product
            if (!$product->active && !$integrateDisabled) {
                $this->logs[] = Tools::addProductFlowLogs($this->context->shop->id, self::FLOW_TYPE, 201, $product, $productReference['id_product_attribute']);
                $this->nbProductsFiltered++;
                continue;
            }
            $this->makeProduct($product, $productReference['id_product_attribute']);
        }
        // Unlock other functionalities
        if (Configuration::updateValue(Configuration::PRODUCT_FLOW_IN_PROGRESS, false)) {
            die(json_encode(array(
                'logs' => $this->logs,
                'nbProductsFiltered' => $this->nbProductsFiltered,
                'nbProductsInError' => $this->nbProductsInError,
                'nbProductsMax' => $this->nbProductsMax,
            )));
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
        foreach ([
            self::COMMON_PRODUCT_ID,
            self::COMMON_EAN,
            self::COMMON_BRAND,
            self::COMMON_CATEGORY,
            self::COMMON_SHOP_SKU,
            self::COMMON_SKU,
            self::COMMON_PRODUCT_ID_TYPE,
            self::COMMON_PRICE,
            self::COMMON_QUANTITY,
            self::COMMON_MIN_QUANTITY
         ] as $attribute) {
            $csvHeader[] = $attribute;
        }

        if (!($countries = json_decode(Configuration::getValue(Configuration::ENABLED_COUNTRIES)))) {
            Configuration::updateValue(Configuration::PRODUCT_FLOW_IN_PROGRESS, false);
            die(json_encode(['error' => $this->getModule()->l('No countries are mapped, first define them on Parameters tab')]));
        }

        foreach ($countries as $iso) {
            if (\Language::getIdByIso($iso)) {
                $this->availableLanguages[] = $iso;
            }
        }

        foreach ([
            'i18n_%s_12963_title',
            'i18n_%s_01022_longdescription',
            'media_'
        ] as $headerTitle) {
            foreach ($this->availableLanguages as $iso) {
                if ($headerTitle != 'media_') {
                    $csvHeader[] = sprintf(
                        $headerTitle,
                        strtolower($iso)
                    );
                } else {
                    for ($i = 0; $i<5; $i++) {
                        if ($iso == 'FR') {
                            $csvHeader[] = 'media_' . ($i + 1);
                        } else {
                            $csvHeader[] = 'media_' . ($i + 1). '_' . strtolower($iso) . '-' . $iso;
                        }
                    }
                }
            }
        }

        foreach (\AttributeGroup::getAttributesGroups($this->context->language->id) as $attributeGroup) {
            $this->attributeGroupList[$attributeGroup['id_attribute_group']] = $attributeGroup['name'];
            $csvHeader[] = $attributeGroup['name'];
        }
        foreach (\Feature::getFeatures($this->context->language->id) as $feature) {
            $this->featureList[$feature['id_feature']] = $feature['name'];
            $csvHeader[] = $feature['name'];
        }

        $this->attributes = $csvHeader;


        // Set csv file and write header
        $dir = dirname(dirname(dirname(__FILE__))) . '/fluxs/products/' . str_replace('-', '_', Tools::str2url($this->context->shop->name));

        if (!is_dir($dir)) {
            mkdir(
                $dir,
                0755,
                true
            );
        }
       
        $file_url = $dir . '/Products.csv';

        if ($this->currentProductsNb == 0) {
            $this->csvFile = fopen($file_url, 'w+');
            // Add header
            fputcsv($this->csvFile, $csvHeader, ';');
            // Reset log file
            $dir = \Module::getInstanceByName('sdevadeo')->getLocalPath().'logs/flux/product/';
            $logs_file = $dir.'product_'.Tools::strtolower(str_replace('-', '_', Tools::str2url($this->context->shop->name)).'.txt');
            if (file_exists($logs_file)) {
                file_put_contents($logs_file, '');
            }
        } else {
            $this->csvFile = fopen($file_url, 'a+');
        }
    }

    /**
     * Create a product line into the flow file.
     *
     * @param \Product $product
     * @param \Combination $product_attribute
     * @return void
     */
    private function makeProduct($product, $idProductAttribute = null)
    {
        if ($idProductAttribute) {
            $productAttribute = new \Combination($idProductAttribute);
        } else {
            $productAttribute = null;
        }

        // CATEGORY RULE
        if (!Tools::isCategoryEnabled($product->id_category_default, $this->context->shop->id)) {
            $this->nbProductsFiltered++;
            $this->logs[] = Tools::addProductFlowLogs($this->context->shop->id, self::FLOW_TYPE, 202, $product, $idProductAttribute);
            return;
        }

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
        $manufacturer = '';
        if (
            $product->id_manufacturer
            && in_array(
                $product->id_manufacturer,
                $manufacturers_exclude
            )
        ) {
            $this->nbProductsFiltered++;
            $this->logs[] = Tools::addProductFlowLogs($this->context->shop->id, self::FLOW_TYPE, 203, $product, $idProductAttribute);
            return;
        }
        $manufacturer = \Manufacturer::getNameById($product->id_manufacturer);

        // Check if supplier product is excluded.
        // If it is: SDEV-ERROR-204.
        if (
            $product->id_supplier
            && in_array(
                $product->id_supplier,
                $suppliers_exclude
            )
        ) {
            $this->nbProductsFiltered++;
            $this->logs[] = Tools::addProductFlowLogs($this->context->shop->id, self::FLOW_TYPE, 204, $product, $idProductAttribute);
            return;
        }
        // PRODUCT REFERENCE
        $eanReference = $productAttribute ? $productAttribute->ean13 : $product->ean13;
        if (!$eanReference || !is_numeric($eanReference) || (strlen($eanReference) != 13)) {
            $this->nbProductsInError++;
            $this->logs[] = Tools::addProductFlowLogs($this->context->shop->id, self::FLOW_TYPE, 302, $product, $idProductAttribute);
            return;
        }
        $shopReference = $productAttribute ? $productAttribute->reference : $product->reference;

        // GENERAL ATTRIBUTES
        $title = array();
        $description = array();
        $descriptionParameter = Configuration::getValue(Configuration::USED_DESCRIPTION);
        foreach($this->availableLanguages as $language) {
            $idLang = \Language::getIdByIso($language);
            if (!$idLang) {
                continue;
            }

            if (Configuration::getValue(Configuration::USED_DESCRIPTION) != 'none') {
                $tmpTitle = array_key_exists($idLang, $product->name) ? $product->name[$idLang] : '';
                if ($productAttribute && strlen($tmpTitle) < 150) {
                    foreach (Tools::getAttributesParams($product->id, $idProductAttribute, $idLang) as $prestashopAttribute) {
                        $tmpLength = strlen($tmpTitle. ' - ' .$prestashopAttribute['name']);
                        if ($tmpLength > 150) {
                            break;
                        }
                        $tmpTitle .= ' - ' .$prestashopAttribute['name'];
                    }
                }
                $title[$language] = strlen($tmpTitle) < 150 && strlen($tmpTitle) > 10 ? $tmpTitle : '';
            }

            switch ($descriptionParameter) {
                case 'short':
                    $description[$language] = array_key_exists($idLang, $product->description_short) ? $product->description_short[$idLang] : '' ;
                    break;
                case 'long':
                    $description[$language] = array_key_exists($idLang, $product->description) ? $product->description[$idLang] : '';
                    break;
                case 'both':
                    $description[$language] = array_key_exists($idLang, $product->description_short) && array_key_exists($idLang, $product->description) ? $product->description_short[$idLang].$product->description[$idLang] : '';
                    break;
                case 'none':
                    $description[$language] = '';
                    break;
            }
            if ($idProductAttribute) {
                $images_attr = Tools::_getAttributeImageAssociations($idProductAttribute);
                $images = array();
                foreach ($images_attr as $img) {
                    $images[]['id_image'] = $img;
                }
            }

            if (!$idProductAttribute || !isset($images) || !$images) {
                $images = $product->getImages($idLang);
            }
            for ($i = 0; $i < 5; $i++) {
                if (array_key_exists($i, $images) && array_key_exists($idLang, $product->link_rewrite)) {
                    $images_links[$language][] = $this->context->link->getImageLink($product->link_rewrite[$idLang], $images[$i]['id_image']);
                } else {
                    $images_links[$language][] = '';
                }
            }

            if (php_sapi_name() == 'cli' && version_compare(_PS_VERSION_, '1.6.0.0', '>=') && version_compare(_PS_VERSION_, '1.6.0.12', '<=')) {
                foreach ($images_links[$language] as &$image_link) {
                    $image_link = str_replace($this->getModule()->getPathUri(), null, $image_link);
                    $image_link = str_replace(_PS_ROOT_DIR_, null, $image_link);
                    $image_link = str_replace('./', '/', $image_link);
                    $image_link = str_replace(Context::getContext()->shop->domain . '/', Context::getContext()->shop->domain . Context::getContext()->shop->getBaseURI(), $image_link);
                }
            }
        }

        $productPriceVatIncl = \Product::getPriceStatic(
            $product->id,
            true,
            (int)$idProductAttribute,
            2,
            null,
            false,
            (bool)Configuration::getValue(Configuration::ENABLED_DISCOUNT)
        );
        if ($categoryRule = Tools::getCategoryRule($product->id_category_default, $this->context->shop->id)) {
            // CATEGORY RULE ADDITIONAL PRICE
            if ($categoryRule['additionalPrice']) {
                $productPriceVatIncl += (($productPriceVatIncl * $categoryRule['additionalPrice']) / 100);
            }
            // PRICING RULE
            if ($categoryRule['pricingRule']) {
                foreach ($categoryRule['pricingRule'] as $pricingRule) {
                    if (($productPriceVatIncl >= (float)$pricingRule['minAmount']) && ($productPriceVatIncl < (float)$pricingRule['maxAmount'])) {
                        if ($pricingRule['pricingRuleTypePercent']) {
                            $productPriceVatIncl += (float)$pricingRule['pricingRuleValue'];
                        } else {
                            $productPriceVatIncl += (($productPriceVatIncl * (float)$pricingRule['pricingRuleValue']) / 100);
                        }
                    }
                }
            }
            $productPriceVatIncl += $categoryRule['additionalPrice'];
        }

        $quantity = StockAvailable::getQuantityAvailableByProduct($product->id, $idProductAttribute);
        if ($quantity < 0) {
            $quantity = 0;
        }

        $minimalQuantity = $product->minimal_quantity;

        // Product category
        $category_label = (new Category($product->id_category_default))
            ->getName(
                $this->context->language->id
            )
        ;

        //PRODUCT LINE CREATION
        $productLine = array();
        // COMMON ATTRIBUTES
        foreach ($this->attributes as $attribute) {
            switch ($attribute) {
                case self::COMMON_SKU: case self::COMMON_SHOP_SKU:
                    $productLine[] = $shopReference;
                    break;
                case self::COMMON_PRODUCT_ID: case self::COMMON_EAN:
                    $productLine[] = $eanReference;
                    break;
                case self::COMMON_BRAND:
                    $productLine[] = $manufacturer;
                    break;
                case self::COMMON_CATEGORY:
                    $productLine[] = $category_label;
                    break;
                case self::COMMON_PRODUCT_ID_TYPE:
                    $productLine[] = 'EAN';
                    break;
                case self::COMMON_PRICE:
                    $productLine[] = $productPriceVatIncl;
                    break;
                case self::COMMON_QUANTITY:
                    $productLine[] = $quantity;
                    break;
                case self::COMMON_MIN_QUANTITY:
                    $productLine[] = $minimalQuantity;
                    break(2);
            }
        }

        // LANGUAGE ATTRIBUTES
        foreach (['title', 'description', 'images_links'] as $treatment) {
            foreach ($this->availableLanguages as $iso) {
                $array = $$treatment;
                switch ($treatment) {
                    case 'title':
                        if (array_key_exists($iso, $array)) {
                            $productLine[] = $array[$iso];
                        } else {
                            $productLine[] = '';
                        }
                        continue(2);
                    case 'description':
                        if (array_key_exists($iso, $array)) {
                            $productLine[] = $this->makeDescription($array[$iso]);
                        } else {
                            $productLine[] = '';
                        }
                        continue(2);
                    case 'images_links':
                        if (array_key_exists($iso, $array)) {
                            foreach ($array[$iso] as $imageUrl) {
                                $productLine[] = $imageUrl;
                            }
                        } else {
                            for ($i=0; $i<4; $i++) {
                                $productLine[] = '';
                            }
                        }
                        continue(2);
                }
            }
        }

        $mappedProceeded = array();
        // Check if required attributes are fulfilled and provide product feature
        $attributes = array();

        $productAttributes = $product::getAttributesParams($product->id, $idProductAttribute);
        if (!empty($productAttributes)) {
            foreach ($productAttributes as $productAttribute) {
                $attributes[$this->attributeGroupList[$productAttribute['id_attribute_group']]] = $productAttribute['name'];
            }
        }

        $productFeatures = $product->getFrontFeatures($this->context->language->id);
        if (!empty($productFeatures)) {
            foreach ($productFeatures as $productFeature) {
                $attributes[$this->featureList[$productFeature['id_feature']]] = $productFeature['value'];
            }
        }


        foreach (array_slice($this->attributes, count($productLine)) as $attributeToFill) {
            if (array_key_exists($attributeToFill, $attributes)) {
                $productLine[] = $attributes[$attributeToFill];
            } else {
                $productLine[] = '';
            }
        }

        if (fputcsv($this->csvFile, $productLine, ';')) {
            $this->logs[] = Tools::addProductFlowLogs($this->context->shop->id, self::FLOW_TYPE, 100, $product, $idProductAttribute);
        }
    }

    /**
    * Make product description.
    *
    * @param string $description
    * @param bool $html
    * @param bool $iframe
    * @return string
    */
    private function makeDescription($description)
    {
        $description = html_entity_decode($description);

        // remove prohibited tags
        $description = preg_replace("/<iframe[^>]*>(.*?)<\/iframe>/i", '', $description);
        $description = preg_replace("/<noscript[^>]*>(.*?)<\/noscript>/i", '', $description);
        $description = preg_replace("/<embed[^>]*>(.*?)<\/embed>/i", '', $description);
        $description = preg_replace("/<script[^>]*>(.*?)<\/script>/i", '', $description);

        $description = iconv('UTF-8', 'UTF-8//TRANSLIT', $description);
        $description = str_replace('&#39;', "'", $description);
        $description = str_replace('&', '&amp;', $description);
        $description = preg_replace('/[\x{0001}-\x{0009}]/u', '', $description);
        $description = preg_replace('/[\x{000b}-\x{001f}]/u', '', $description);
        $description = preg_replace('/[\x{0080}-\x{009F}]/u', '', $description);

        return trim($description);
    }

}
