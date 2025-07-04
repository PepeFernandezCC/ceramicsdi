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

use GMerchantCenterPro;
use Gmerchantcenterpro\Dao\moduleDao;
use Gmerchantcenterpro\Models\exclusionProduct;
use Gmerchantcenterpro\ModuleLib\moduleReporting;
use Gmerchantcenterpro\ModuleLib\moduleTools;

class xmlProduct extends baseProductXml
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
        return [$iProductId];
    }

    /**
     * build product XML tags
     *
     * @return mixed
     */
    public function buildDetailProductXml()
    {
        $id_lang = !empty((int) \Tools::getValue('gmcp_lang_id')) ? (int) \Tools::getValue('gmcp_lang_id') : (int) \Tools::getValue('iLangId');
        $idProduct = (int) $this->data->p->id;
        $country = !empty(\Tools::getValue('country')) ? \Tools::getValue('country') : \Tools::getValue('sCountryIso');

        if (!empty(exclusionProduct::isIdProductExcluded($idProduct))) {
            return false;
        }

        if (\GMerchantCenterPro::$conf['GMCP_FEED_PREF_ID'] == 'tag-id-basic') {
            $this->data->step->id = ModuleTools::constructFeedIdsBasic($idProduct, $country, 'product');
        } elseif (\GMerchantCenterPro::$conf['GMCP_FEED_PREF_ID'] == 'tag-id-ean') {
            $this->data->step->id = ModuleTools::constructFeedIdsEan($idProduct, $id_lang, 'product', null, null, $this->data->p->ean13, $country);
        } elseif (\GMerchantCenterPro::$conf['GMCP_FEED_PREF_ID'] == 'tag-id-product-ref') {
            $this->data->step->id = ModuleTools::constructFeedIdsRef($idProduct, $id_lang, 'product', null, null, $this->data->p->reference, $country);
        }

        /**
         * WEIGHT CALCULATION PROCESS
         * Example scenario:
         * - Base product: T-shirt with weight 0.2kg
         * - Combination: Size XL with additional weight 0.05kg
         * - Final weight would be: 0.2kg + 0.05kg = 0.25kg
         */

        // Set initial product weight (e.g., 0.2kg for base T-shirt)
        $this->data->step->weight = (float) $this->data->p->weight;

        // Check if we're in standard export mode (0) and if product has combinations
        // For example: T-shirt with different sizes (S, M, L, XL)
        if (GMerchantCenterPro::$conf['GMCP_EXPORT_MODE'] == '0' && ($aCombinations = \Product::getProductAttributesIds($idProduct))) {
            // Get the default combination (e.g., Size XL as default)
            if (($defaultCombination = \Product::getDefaultAttribute($idProduct))) {
                // Load the combination object
                $combination = new \Combination($defaultCombination);

                // Verify combination is valid and properly loaded
                if (\Validate::isLoadedObject($combination)) {
                    // If combination has additional weight (e.g., 0.05kg for XL size)
                    if (!empty((float) $combination->weight)) {
                        // Add combination weight to base product weight
                        // Example: 0.2kg (base) + 0.05kg (XL addition) = 0.25kg
                        $this->data->step->weight = (float) $this->data->p->weight + (float) $combination->weight;
                    } else {
                        // Keep original weight if combination has no additional weight
                        // Example: 0.2kg (base) + 0kg (no addition) = 0.2kg
                        $this->data->step->weight = (float) $this->data->p->weight;
                    }
                }
            }
        }

        // handle different prices and shipping fees
        $this->data->step->price_default_currency_no_tax = \Tools::convertPrice((float) \Product::getPriceStatic((int) $this->data->p->id, false, null), $this->data->currency, false);

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

        if ($this->data->p->minimal_quantity > 1) {
            $this->data->multipack = (int)$this->data->p->minimal_quantity;
        } else {
            $this->data->multipack = 0;
        }

        if (empty(\GMerchantCenterPro::$conf['GMCP_USE_GEOLOC'])) {
            if (empty( $this->data->multipack)) {
                $this->data->step->price_raw = \Product::getPriceStatic((int) $this->data->p->id, $bUseTax, null, 6);
                $this->data->step->price_raw_no_discount = \Product::getPriceStatic((int) $this->data->p->id, $bUseTax, null, 6, null, false, false);
            } else {
                $this->data->step->price_raw = $this->data->multipack * \Product::getPriceStatic((int) $this->data->p->id, $bUseTax, null, 6);
                $this->data->step->price_raw_no_discount = $this->data->multipack * \Product::getPriceStatic((int) $this->data->p->id, $bUseTax, null, 6, null, false, false);
            }
        } else {
            $geolocalized_price = moduleTools::handleGeolocPrice(\Tools::getValue('sCountryIso'), $this->data->p);

            if (empty( $this->data->multipack)) {
                $this->data->step->price_raw = (float)$geolocalized_price['price_raw'];
                $this->data->step->price_raw_no_discount = (float)$geolocalized_price['price_raw_no_discount'];;
            } else {
                $this->data->step->price_raw = $this->data->multipack * (float)$geolocalized_price['price_raw'];
                $this->data->step->price_raw_no_discount = $this->data->multipack * (float)$geolocalized_price['price_raw_no_discount'];
            }
        }

        $this->data->step->price = number_format(moduleTools::round($this->data->step->price_raw), 2, '.', '') . ' ' . $this->data->currency->iso_code;
        $this->data->step->price_no_discount = number_format(moduleTools::round($this->data->step->price_raw_no_discount), 2, '.', '') . ' ' . $this->data->currency->iso_code;
        // Use case override the price with the pack price
        if (
            \GMerchantCenterPro::$bAdvancedPack && \AdvancedPack::isValidPack($this->data->p->id)
        ) {
            $oPack = new \AdvancedPack($this->data->p->id);
            $this->data->step->price_raw_no_discount = number_format(\AdvancedPack::getPackPrice($oPack->id, $bUseTax, false), 2, '.', '') . ' ' . $this->data->currency->iso_code;
            $this->data->step->price_raw = number_format(\AdvancedPack::getPackPrice($oPack->id), 2, '.', '') . ' ' . $this->data->currency->iso_code;
            $this->data->step->price_no_discount = number_format(\AdvancedPack::getPackPrice($oPack->id, $bUseTax, false), 2, '.', '') . ' ' . $this->data->currency->iso_code;
            $this->data->step->price = number_format(\AdvancedPack::getPackPrice($oPack->id), 2, '.', '') . ' ' . $this->data->currency->iso_code;
        }

        // Available date
        $this->data->step->availabilty_date = '';

        if ($this->data->p->available_date != '0000-00-00') {
            $this->data->step->availabilty_date = $this->data->p->available_date;
        }

        // Cost price
        if (!empty((int) $this->data->p->wholesale_price)) {
            $this->data->step->cost_price = number_format(moduleTools::round($this->data->p->wholesale_price), 2, '.', '') . ' ' . $this->data->currency->iso_code;
        }
        $this->data->step->shipping_fees = moduleTools::calculateShippingFees(
            \GMerchantCenterPro::$conf['GMCP_SHIPPING_USE'],
            $this->aParams['sFreeShipping'],
            $this->data->p->id,
            $this->data->step->price_raw,
            $this->data->currency,
            $this->data->currentCarrier,
            $this->data->p->additional_shipping_cost,
            null,
            $this->data
        );

        // get images
        $this->data->step->images = $this->getImages($this->data->p);


        // quantity
        $this->data->step->quantity = (int) $this->data->p->quantity;

        // Manage GTIN code
        $this->data->step->gtin = moduleTools::getGtin(\GMerchantCenterPro::$conf['GMCP_GTIN_PREF'], (array) $this->data->p);

        // Exclude without EAN
        if (
            \GMerchantCenterPro::$conf['GMCP_EXC_NO_EAN']
            && empty($this->data->step->gtin)
        ) {
            moduleReporting::create()->set('_no_export_no_ean_upc', ['productId' => $this->data->step->id_reporting]);

            return false;
        }

        // supplier reference
        $this->data->step->mpn = $this->getSupplierReference($this->data->p->id, $this->data->p->id_supplier, $this->data->p->supplier_reference, $this->data->p->reference);

        // exclude if mpn is empty
        if (
            !empty(\GMerchantCenterPro::$conf['GMCP_EXC_NO_MREF'])
            && !\GMerchantCenterPro::$conf['GMCP_INC_ID_EXISTS']
            && empty($this->data->step->mpn)
        ) {
            moduleReporting::create()->set('_no_export_no_supplier_ref', ['productId' => $this->data->step->id_reporting]);

            return false;
        }

        // handle the specific price feature
        if (!empty($this->data->p->specificPrice['from'])) {
            $this->data->step->specificPriceFrom = $this->data->p->specificPrice['from'];
        }
        if (!empty($this->data->p->specificPrice['to'])) {
            $this->data->step->specificPriceTo = $this->data->p->specificPrice['to'];
        }

        $this->data->step->visibility = $this->data->p->visibility;

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
            if ((float) \Product::getPriceStatic((int) $this->data->p->id, false, null, 6, null, false, false) >= (float) \GMerchantCenterPro::$conf['GMCP_FREE_SHIPPING_PRICE']) {
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
     * @param int $iLangId
     *
     * @return string
     */
    public function formatProductName($iAdvancedProdName, $iAdvancedProdTitle, $sProdName, $sCatName, $sManufacturerName, $iLength, $iProdAttrId = null, $iLangId = null, $sPrefix = null, $sSuffix = null)
    {
        $sProdName = moduleTools::truncateProductTitle($iAdvancedProdName, $sProdName, $sCatName, $sManufacturerName, $iLength, $this->aParams['iLangId'], $sPrefix, $sSuffix);

        return moduleTools::formatProductTitle($sProdName, $iAdvancedProdTitle);
    }

    /**
     * get images of one product or one combination
     *
     * @param \Product $oProduct
     * @param int $iProdAttributeId
     *
     * @return array
     */
    public function getImages($oProduct, $iProdAttributeId = null)
    {
        // set vars
        $aResultImages = [];
        $iCounter = 1;
        $aImage = [];
        $coverPosition = \GMerchantCenterPro::$conf['GMCP_IMG_COVER_POSITION'];

        // Classical use case when we use default cover
        if ($coverPosition == 1) {
            // get cover
            $aImage = \Product::getCover($oProduct->id);

            // Additional images
            $aOtherImages = $oProduct->getImages(\GMerchantCenterPro::$iCurrentLang);
            if (!empty($aOtherImages) && is_array($aOtherImages)) {
                foreach ($aOtherImages as $aImg) {
                    if ((int) $aImg['id_image'] != (int) $aImage['id_image'] && $iCounter <= 10 && $aImg['cover'] != 1) {
                        $aResultImages[] = ['id_image' => (int) $aImg['id_image']];
                        ++$iCounter;
                    }
                }
            }
        } else {
            $aOtherImages = $oProduct->getImages(\GMerchantCenterPro::$iCurrentLang);
            if (isset($aOtherImages[$coverPosition])) {
                $aImage['id_image'] = $aOtherImages[$coverPosition]['id_image'];
            } else {
                $aImage = \Product::getCover($oProduct->id);
            }
            if (!empty($aOtherImages) && is_array($aOtherImages)) {
                foreach ($aOtherImages as $aImg) {
                    if ((int) $aImg['id_image'] != (int) $aImage['id_image'] && $iCounter <= 10 && $aImg['cover'] != 1) {
                        $aResultImages[] = ['id_image' => (int) $aImg['id_image']];
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
        $sReturnRef = '';

        $oProduct = new \Product($iProdId);
        $sReturnRef = $oProduct->mpn;

        return $sReturnRef;
    }
}
