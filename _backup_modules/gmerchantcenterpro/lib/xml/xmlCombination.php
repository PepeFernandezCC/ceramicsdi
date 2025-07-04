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

use Gmerchantcenterpro\Dao\moduleDao;
use Gmerchantcenterpro\Models\exclusionProduct;
use Gmerchantcenterpro\ModuleLib\moduleReporting;
use Gmerchantcenterpro\ModuleLib\moduleTools;

class xmlCombination extends baseProductXml
{
    /**
     * @param array $aParams
     */
    public function __construct(array $aParams = null)
    {
        parent::__construct($aParams);
    }

    /**
     * load products combination
     *
     * @param int $iProductId
     * @param bool $bExcludedProduct
     *
     * @return array
     */
    public function hasCombination($iProductId, $bExcludedProduct = false)
    {
        return moduleDao::getProductCombination($this->aParams['iShopId'], $iProductId, $bExcludedProduct);
    }

    /**
     * build product XML tags
     *
     * @return mixed
     */
    public function buildDetailProductXml()
    {
        // set the product ID
        $this->data->step->id = $this->data->p->id . \GMerchantCenterPro::$conf['GMCP_COMBO_SEPARATOR'] . $this->data->c['id_product_attribute'];
        $this->data->step->id_no_combo = $this->data->p->id;
        $id_lang = !empty((int) \Tools::getValue('gmcp_lang_id')) ? (int) \Tools::getValue('gmcp_lang_id') : (int) \Tools::getValue('iLangId');
        $id_shop = !empty((int) \Tools::getValue('id_shop')) ? (int) \Tools::getValue('id_shop') : (int) \Tools::getValue('iShopId');
        $separator = \Gmerchantcenterpro::$conf['GMCP_COMBO_SEPARATOR'];
        $idProduct = (int) $this->data->p->id;
        $idAttribute = (int) $this->data->c['id_product_attribute'];
        $country = \Tools::getValue('country');

        if (!empty(exclusionProduct::isIdProductExcluded($idProduct, $idAttribute))) {
            return false;
        }

        if (\Gmerchantcenterpro::$conf['GMCP_FEED_PREF_ID'] == 'tag-id-basic') {
            $this->data->step->id = ModuleTools::constructFeedIdsBasic($idProduct, $country, 'combination', $idAttribute, $separator);
        } elseif (\Gmerchantcenterpro::$conf['GMCP_FEED_PREF_ID'] == 'tag-id-ean') {
            $this->data->step->id = ModuleTools::constructFeedIdsEan($idProduct, $id_lang, 'combination', $idAttribute, $separator, $this->data->c['ean13']);
        } elseif (\Gmerchantcenterpro::$conf['GMCP_FEED_PREF_ID'] == 'tag-id-product-ref') {
            $this->data->step->id = ModuleTools::constructFeedIdsRef($idProduct, $id_lang, 'combination', $idAttribute, $separator, $this->data->c['reference']);
        }

        $this->data->step->url = moduleTools::buildProductUrl($this->data->p, (int) $id_lang, $this->data->currencyId, $id_shop, $this->data->c['id_product_attribute']);

        // get weight
        $this->data->step->weight = (float) $this->data->p->weight + (float) $this->data->c['weight'];

        // handle different prices and shipping fees
        $this->data->step->price_default_currency_no_tax = \Tools::convertPrice((float) \Product::getPriceStatic((int) $this->data->p->id, false, (int) $this->data->c['id_product_attribute']), $this->data->currency, false);

        // Exclude based on min price
        if (
            !empty(\GMerchantCenterPro::$conf['GMCP_MIN_PRICE'])
            && ((float) $this->data->step->price_default_currency_no_tax < (float) \GMerchantCenterPro::$conf['GMCP_MIN_PRICE'])
        ) {
            moduleReporting::create()->set('_no_export_min_price', ['productId' => $this->data->step->id_reporting]);

            return false;
        }

        // Exclude based on max weight
        if (
            !empty(\GMerchantCenterPro::$conf['GMCP_MAX_WEIGHT'])
            && ((float) $this->data->step->weight > (float) \GMerchantCenterPro::$conf['GMCP_MAX_WEIGHT'])
        ) {
            moduleReporting::create()->set('_no_export_max_weight', ['productId' => $this->data->step->id_reporting]);

            return false;
        }

        // handle both price and discounted price
        if (isset($this->aParams['bUseTax'])) {
            $bUseTax = !empty($this->aParams['bUseTax']) ? true : false;
        } else {
            $bUseTax = true;
        }

        $this->data->step->price_raw = \Product::getPriceStatic((int) $this->data->p->id, $bUseTax, (int) $this->data->c['id_product_attribute']);
        $this->data->step->price_raw_no_discount = \Product::getPriceStatic((int) $this->data->p->id, $bUseTax, (int) $this->data->c['id_product_attribute'], 6, null, false, false);
        $this->data->step->price = number_format(moduleTools::round($this->data->step->price_raw), 2, '.', '') . ' ' . $this->data->currency->iso_code;
        $this->data->step->price_no_discount = number_format(moduleTools::round($this->data->step->price_raw_no_discount), 2, '.', '') . ' ' . $this->data->currency->iso_code;

        if (\GMerchantCenterPro::$bAdvancedPack && \AdvancedPack::isValidPack($this->data->p->id)) {
            $oPack = new \AdvancedPack($this->data->p->id);
            $this->data->step->price_raw_no_discount = number_format(\AdvancedPack::getPackPrice($oPack->id, $bUseTax, false), 2, '.', '') . ' ' . $this->data->currency->iso_code;
            $this->data->step->price_raw = number_format(\AdvancedPack::getPackPrice($oPack->id), 2, '.', '') . ' ' . $this->data->currency->iso_code;
            $this->data->step->price_no_discount = number_format(\AdvancedPack::getPackPrice($oPack->id, $bUseTax, false), 2, '.', '') . ' ' . $this->data->currency->iso_code;
            $this->data->step->price = number_format(\AdvancedPack::getPackPrice($oPack->id), 2, '.', '') . ' ' . $this->data->currency->iso_code;
        }

        // Available date
        $this->data->step->availabilty_date = '';

        if ($this->data->c['available_date'] != '0000-00-00') {
            $this->data->step->availabilty_date = $this->data->c['available_date'];
        }

        // Cost price
        if (!empty((int) $this->data->c['wholesale_price'])) {
            $this->data->step->cost_price = number_format(moduleTools::round($this->data->c['wholesale_price']), 2, '.', '') . ' ' . $this->data->currency->iso_code;
        } elseif (!empty((int) $this->data->p->wholesale_price)) {
            $this->data->step->cost_price = number_format(moduleTools::round($this->data->p->wholesale_price), 2, '.', '') . ' ' . $this->data->currency->iso_code;
        }

        // shipping fees
        if (
            !empty(\GMerchantCenterPro::$conf['GMCP_SHIPPING_USE'])
            && !isset($this->aParams['sFreeShipping'][$this->data->p->id])
        ) {
            // reforce the price with default shop currency to have a good comparaison for price range
            $product_price_default_tax = \Tools::convertPrice((float) $this->data->step->price_raw, $this->data->currency, false);
            $fPrice = number_format((float) $this->getProductShippingFees((float) moduleTools::round($product_price_default_tax)), 2, '.', '');

            $fPrice = 0;
            $product_price_default_tax = \Tools::convertPrice((float) $this->data->step->price_raw, $this->data->currency, false);
            $fPrice = number_format((float) $this->getProductShippingFees((float) moduleTools::round($product_price_default_tax)), 2, '.', '');
            $fPrice = number_format($fPrice + $this->data->p->additional_shipping_cost, 2, '.', '');

        } else {
            $freeShipping = !empty($this->aParams['sFreeShipping'][$this->data->p->id]) ? $this->aParams['sFreeShipping'][$this->data->p->id] : [];
            if (in_array($this->data->c['id_product_attribute'], $freeShipping)) {
                $fPrice = number_format((float) 0, 2, '.', '');
            } else {
                // reforce the price with default shop currency to have a good comparaison for price range
                $product_price_default_tax = \Tools::convertPrice((float) $this->data->step->price_raw, $this->data->currency, false);
                $fPrice = number_format((float) $this->getProductShippingFees((float) moduleTools::round($this->data->step->price_raw)), 2, '.', '');
            }
        }

        $this->data->step->shipping_fees = $fPrice . ' ' . $this->data->currency->iso_code;

        // get images
        $this->data->step->images = $this->getImages($this->data->p, $this->data->c['id_product_attribute']);

        // quantity
        // Do not export if the quantity is 0 for the combination and export out of stock setting is not On
        if (
            (int) $this->data->c['combo_quantity'] <= 0
            && (int) \GMerchantCenterPro::$conf['GMCP_EXPORT_OOS'] == 0
        ) {
            moduleReporting::create()->set('_no_export_no_stock', ['productId' => $this->data->step->id_reporting]);

            return false;
        }
        $this->data->step->quantity = (int) $this->data->c['combo_quantity'];

        // Manage GTIN code
        if (!empty(moduleTools::getGtin(\GMerchantCenterPro::$conf['GMCP_GTIN_PREF'], $this->data->c))) {
            $this->data->step->gtin = moduleTools::getGtin(\GMerchantCenterPro::$conf['GMCP_GTIN_PREF'], $this->data->c);
        } else {
            $this->data->step->gtin = moduleTools::getGtin(\GMerchantCenterPro::$conf['GMCP_GTIN_PREF'], (array) $this->data->p);
        }

        // Exclude without EAN
        if (
            \GMerchantCenterPro::$conf['GMCP_EXC_NO_EAN']
            && empty($this->data->step->gtin)
        ) {
            moduleReporting::create()->set('_no_export_no_ean_upc', ['productId' => $this->data->step->id_reporting]);

            return false;
        }

        // supplier reference
        $this->data->step->mpn = $this->getSupplierReference($this->data->p->id, $this->data->p->id_supplier, $this->data->p->supplier_reference, $this->data->p->reference, (int) $this->data->c['id_product_attribute'], $this->data->c['supplier_reference'], $this->data->c['reference']);

        // exclude if mpn is empty
        if (
            !empty(\GMerchantCenterPro::$conf['GMCP_EXC_NO_MREF'])
            && !\GMerchantCenterPro::$conf['GMCP_INC_ID_EXISTS']
            && empty($this->data->step->mpn)
        ) {
            moduleReporting::create()->set('_no_export_no_supplier_ref', ['productId' => $this->data->step->id_reporting]);

            return false;
        }

        // Use case for the specific price
        if (!empty($this->data->p->specificPrice)) {
            // Use case for specific price on all combination on the from
            if (!empty($this->data->p->specificPrice['from'])) {
                $sFrom = $this->data->p->specificPrice['from'];
            } else {
                if (!empty($this->data->c['from'])) {
                    $sFrom = $this->data->c['from'];
                }
            }

            // Use case for specific price on all combination on the from
            if (!empty($this->data->p->specificPrice['to'])) {
                $sTo = $this->data->p->specificPrice['to'];
            } else {
                if (!empty($this->data->c['to'])) {
                    $sTo = $this->data->c['to'];
                }
            }
        } else {
            if (!empty($this->data->c['from'])) {
                $sFrom = $this->data->c['from'];
            }
            if (!empty($this->data->c['to'])) {
                $sTo = $this->data->c['to'];
            }
        }

        // handle the specific price feature
        $this->data->step->specificPriceFrom = !empty($sFrom) ? $sFrom : '0000-00-00 00:00:00';

        $this->data->step->specificPriceTo = !empty($sTo) ? $sTo : '0000-00-00 00:00:00';

        $this->data->step->visibility = $this->data->p->visibility;

        if ($this->data->c['minimal_quantity'] > 1) {
            $this->data->multipack = $this->data->c['minimal_quantity'];
        } else {
            $this->data->multipack = 0;
        }

        // Use case for dimension of shipping
        if (!empty(\GMerchantCenterPro::$conf['GMCP_DIMENSION'])) {
            $aDataDimension = moduleTools::getDimension($this->data->p->width, $this->data->p->height, $this->data->p->depth);
            if (!empty($aDataDimension)) {
                $this->data->step->shipping_width = $aDataDimension['shipping_width'];
                $this->data->step->shipping_height = $aDataDimension['shipping_height'];
                $this->data->step->shipping_length = $aDataDimension['shipping_length'];
            }
        }

        // Use case for dimension of shipping
        if (!empty(\GMerchantCenterPro::$conf['GMCP_PRODUCT_DIMENSION'])) {
            $aDataDimension = moduleTools::getDimension($this->data->p->width, $this->data->p->height, $this->data->p->depth, $this->data->p->weight);
            if (!empty($aDataDimension)) {
                $this->data->step->product_width = $aDataDimension['product_width'];
                $this->data->step->product_height = $aDataDimension['product_height'];
                $this->data->step->product_length = $aDataDimension['product_length'];
                $this->data->step->product_weight = $aDataDimension['product_weight'];
            }
        }

        $this->data->step->free_shipping = false;
        // Use case to set the free shipping
        if (!empty(\GMerchantCenterPro::$conf['GMCP_FREE_SHIPPING_PRICE'])) {
            if ((float) \Product::getPriceStatic((int) $this->data->p->id, false, null, 6, (int) $this->data->c['id_product_attribute']) >= (float) \GMerchantCenterPro::$conf['GMCP_FREE_SHIPPING_PRICE']) {
                $this->data->step->free_shipping = true;
            }
        }

        return true;
    }

    /**
     * format the product name
     *
     * @param int $iAdvancedProdName
     * @param int $iAdvancedProdTitle
     * @param string $sProdName
     * @param string $sCatName
     * @param string $sManufacturerName
     * @param int $iLength
     * @param int $iProdAttrId
     *
     * @return string
     */
    public function formatProductName($iAdvancedProdName, $iAdvancedProdTitle, $sProdName, $sCatName, $sManufacturerName, $iLength, $iProdAttrId = null, $iLangId = null, $sPrefix = null, $sSuffix = null)
    {
        // Use case to add or not combination data
        if (!empty(\GMerchantCenterPro::$conf['GMCP_INCL_ATTR_VALUE'])) {
            // get the combination attributes to format the product name
            $aCombinationAttr = moduleDao::getProductComboAttributes($iProdAttrId, $this->aParams['iLangId'], $this->aParams['iShopId']);
            // $sProdName = moduleTools::getProductCombinationName();
            if (!empty($aCombinationAttr)) {
                $sExtraName = '';
                foreach ($aCombinationAttr as $c) {
                    $sExtraName .= ' ' . \Tools::stripslashes($c['name']);
                }
                $sProdName .= $sExtraName;
            }
        }
        // encode
        $sProdName = moduleTools::truncateProductTitle($iAdvancedProdName, $sProdName, $sCatName, $sManufacturerName, $iLength, $this->aParams['iLangId'], $sPrefix, $sSuffix);

        $sProdName = moduleTools::formatProductTitle($sProdName, $iAdvancedProdTitle);

        $sProdName = moduleTools::handleExcludedWords($sProdName);

        return $sProdName;
    }

    /**
     * get images of one product or one combination
     *
     * @param obj $oProduct
     * @param int $iProdAttributeId
     *
     * @return array
     */
    public function getImages($oProduct, $iProdAttributeId = null)
    {
        // set vars
        $aResultImages = [];
        $aImage = [];
        $iCounter = 1;
        $coverPosition = \GMerchantCenterPro::$conf['GMCP_IMG_COVER_POSITION'];

        // Classical use case when we use default cover
        if ($coverPosition == 1) {
            $aAttributeImages = $oProduct->getCombinationImages(\GMerchantCenterPro::$iCurrentLang);
            if (!empty($aAttributeImages[$iProdAttributeId]) && is_array($aAttributeImages[$iProdAttributeId])) {
                $aImage = ['id_image' => $aAttributeImages[$iProdAttributeId][0]['id_image']];
            } else {
                $aImage = \Product::getCover($oProduct->id);
            }

            if (!empty($aAttributeImages[$iProdAttributeId]) && is_array($aAttributeImages)) {
                foreach ($aAttributeImages[$iProdAttributeId] as $sImg) {
                    if ($iCounter <= 10) {
                        $aResultImages[] = ['id_image' => $sImg['id_image']];
                        ++$iCounter;
                    }
                }
            }

            // Additional images
            unset($aAttributeImages['id_image']);
        } else {
            $aAttributeImages = $oProduct->getCombinationImages(\GMerchantCenterPro::$iCurrentLang);
            if (!empty($aAttributeImages[$iProdAttributeId]) && is_array($aAttributeImages[$iProdAttributeId])) {
                // Search the image postion
                $positionKey = $coverPosition - 1;
                if (isset($aAttributeImages[$iProdAttributeId][$positionKey])) {
                    $aImage = ['id_image' => $aAttributeImages[$iProdAttributeId][$positionKey]['id_image']];
                } else {
                    $aImage = ['id_image' => $aAttributeImages[$iProdAttributeId][0]['id_image']];
                }
            } else {
                $aImage = \Product::getCover($oProduct->id);
            }

            if (!empty($aAttributeImages[$iProdAttributeId]) && is_array($aAttributeImages)) {
                foreach ($aAttributeImages[$iProdAttributeId] as $sImg) {
                    if ($iCounter <= 10) {
                        $aResultImages[] = ['id_image' => $sImg['id_image']];
                        ++$iCounter;
                    }
                }
            }
        }

        return ['image' => $aImage, 'others' => $aResultImages];
    }

    /**
     * get supplier reference
     *
     * @param int $iProdId
     * @param int $iSupplierId
     * @param string $sSupplierRef
     * @param string $sProductRef
     * @param int $iProdAttributeId
     * @param string $sCombiSupplierRef
     * @param string $sCombiRef
     *
     * @return string
     */
    public function getSupplierReference($iProdId, $iSupplierId, $sSupplierRef = null, $sProductRef = null, $iProdAttributeId = 0, $sCombiSupplierRef = null, $sCombiRef = null)
    {
        // set  vars
        $sReturnRef = '';

        if (empty(\GMerchantCenterPro::$bCompare1770)) {
            // detect the MPN type
            $sReturnRef = moduleDao::getProductSupplierReference($iProdId, $iSupplierId, $iProdAttributeId);
        } else {
            $oCombination = new \Combination($iProdAttributeId);

            if (!empty($oCombination->mpn)) {
                $sReturnRef = $oCombination->mpn;
            } else {
                $oProduct = new \Product($iProdId);
                $sReturnRef = $oProduct->mpn;
            }
        }

        return $sReturnRef;
    }
}
