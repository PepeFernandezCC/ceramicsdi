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
 * @package Scaledev\MiraklPhpConnector
 * Support: support@scaledev.fr
 */

namespace Scaledev\MiraklPhpConnector\Model;

use Scaledev\MiraklPhpConnector\Core\Model\AbstractModel;

/**
 * Class Offer
 *
 * @package Scaledev\MiraklPhpConnector
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class Offer extends AbstractModel
{
    private $sku;
    private $product_id;
    private $product_id_type;
    private $description;
    private $internal_description;
    private $price;
    private $price_additional_info;
    private $quantity;
    private $min_quantity_alert;
    private $state;
    private $available_start_date;
    private $available_end_date;
    private $discount_price;
    private $discount_start_date;
    private $discount_end_date;
    private $discount_ranges;
    private $allow_quote_requests;
    private $leadtime_to_ship;
    private $min_order_quantity;
    private $max_order_quantity;
    private $package_quantity;
    private $update_delete;
    private $price_ranges;
    private $ecotax;
    private $gift_wrap;
    private $min_quantity_ordered;
    private $eco_contributions;
    private $favorite_rank;
    private $logistic_class;
    private $pricing_unit;
    private $product_tax_code;

    /**
     * @return mixed
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param mixed $sku
     * @return $this
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * @param mixed $product_id
     * @return $this
     */
    public function setProductId($product_id)
    {
        $this->product_id = $product_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductIdType()
    {
        return $this->product_id_type;
    }

    /**
     * @param mixed $product_id_type
     * @return $this
     */
    public function setProductIdType($product_id_type)
    {
        $this->product_id_type = $product_id_type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInternalDescription()
    {
        return $this->internal_description;
    }

    /**
     * @param mixed $internal_description
     * @return $this
     */
    public function setInternalDescription($internal_description)
    {
        $this->internal_description = $internal_description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriceAdditionalInfo()
    {
        return $this->price_additional_info;
    }

    /**
     * @param mixed $price_additional_info
     * @return $this
     */
    public function setPriceAdditionalInfo($price_additional_info)
    {
        $this->price_additional_info = $price_additional_info;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinQuantityAlert()
    {
        return $this->min_quantity_alert;
    }

    /**
     * @param mixed $min_quantity_alert
     * @return $this
     */
    public function setMinQuantityAlert($min_quantity_alert)
    {
        $this->min_quantity_alert = $min_quantity_alert;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAvailableStartDate()
    {
        return $this->available_start_date;
    }

    /**
     * @param mixed $available_start_date
     * @return $this
     */
    public function setAvailableStartDate($available_start_date)
    {
        $this->available_start_date = $available_start_date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAvailableEndDate()
    {
        return $this->available_end_date;
    }

    /**
     * @param mixed $available_end_date
     * @return $this
     */
    public function setAvailableEndDate($available_end_date)
    {
        $this->available_end_date = $available_end_date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDiscountPrice()
    {
        return $this->discount_price;
    }

    /**
     * @param mixed $discount_price
     * @return $this
     */
    public function setDiscountPrice($discount_price)
    {
        $this->discount_price = $discount_price;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDiscountStartDate()
    {
        return $this->discount_start_date;
    }

    /**
     * @param mixed $discount_start_date
     * @return $this
     */
    public function setDiscountStartDate($discount_start_date)
    {
        $this->discount_start_date = $discount_start_date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDiscountEndDate()
    {
        return $this->discount_end_date;
    }

    /**
     * @param mixed $discount_end_date
     * @return $this
     */
    public function setDiscountEndDate($discount_end_date)
    {
        $this->discount_end_date = $discount_end_date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDiscountRanges()
    {
        return $this->discount_ranges;
    }

    /**
     * @param mixed $discount_ranges
     * @return $this
     */
    public function setDiscountRanges($discount_ranges)
    {
        $this->discount_ranges = $discount_ranges;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllowQuoteRequests()
    {
        return $this->allow_quote_requests;
    }

    /**
     * @param mixed $allow_quote_requests
     * @return $this
     */
    public function setAllowQuoteRequests($allow_quote_requests)
    {
        $this->allow_quote_requests = $allow_quote_requests;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLeadtimeToShip()
    {
        return $this->leadtime_to_ship;
    }

    /**
     * @param mixed $leadtime_to_ship
     * @return $this
     */
    public function setLeadtimeToShip($leadtime_to_ship)
    {
        $this->leadtime_to_ship = $leadtime_to_ship;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinOrderQuantity()
    {
        return $this->min_order_quantity;
    }

    /**
     * @param mixed $min_order_quantity
     * @return $this
     */
    public function setMinOrderQuantity($min_order_quantity)
    {
        $this->min_order_quantity = $min_order_quantity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxOrderQuantity()
    {
        return $this->max_order_quantity;
    }

    /**
     * @param mixed $max_order_quantity
     * @return $this
     */
    public function setMaxOrderQuantity($max_order_quantity)
    {
        $this->max_order_quantity = $max_order_quantity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPackageQuantity()
    {
        return $this->package_quantity;
    }

    /**
     * @param mixed $package_quantity
     * @return $this
     */
    public function setPackageQuantity($package_quantity)
    {
        $this->package_quantity = $package_quantity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdateDelete()
    {
        return $this->update_delete;
    }

    /**
     * @param mixed $update_delete
     * @return $this
     */
    public function setUpdateDelete($update_delete)
    {
        $this->update_delete = $update_delete;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriceRanges()
    {
        return $this->price_ranges;
    }

    /**
     * @param mixed $price_ranges
     * @return $this
     */
    public function setPriceRanges($price_ranges)
    {
        $this->price_ranges = $price_ranges;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEcotax()
    {
        return $this->ecotax;
    }

    /**
     * @param mixed $ecotax
     * @return $this
     */
    public function setEcotax($ecotax)
    {
        $this->ecotax = $ecotax;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGiftWrap()
    {
        return $this->gift_wrap;
    }

    /**
     * @param mixed $gift_wrap
     * @return $this
     */
    public function setGiftWrap($gift_wrap)
    {
        $this->gift_wrap = $gift_wrap;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinQuantityOrdered()
    {
        return $this->min_quantity_ordered;
    }

    /**
     * @param mixed $min_quantity_ordered
     * @return $this
     */
    public function setMinQuantityOrdered($min_quantity_ordered)
    {
        $this->min_quantity_ordered = $min_quantity_ordered;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEcoContributions()
    {
        return $this->eco_contributions;
    }

    /**
     * @param mixed $eco_contributions
     * @return $this
     */
    public function setEcoContributions($eco_contributions)
    {
        $this->eco_contributions = $eco_contributions;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFavoriteRank()
    {
        return $this->favorite_rank;
    }

    /**
     * @param mixed $favorite_rank
     * @return $this
     */
    public function setFavoriteRank($favorite_rank)
    {
        $this->favorite_rank = $favorite_rank;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLogisticClass()
    {
        return $this->logistic_class;
    }

    /**
     * @param mixed $logistic_class
     * @return $this
     */
    public function setLogisticClass($logistic_class)
    {
        $this->logistic_class = $logistic_class;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPricingUnit()
    {
        return $this->pricing_unit;
    }

    /**
     * @param mixed $pricing_unit
     * @return $this
     */
    public function setPricingUnit($pricing_unit)
    {
        $this->pricing_unit = $pricing_unit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductTaxCode()
    {
        return $this->product_tax_code;
    }

    /**
     * @param mixed $product_tax_code
     * @return $this
     */
    public function setProductTaxCode($product_tax_code)
    {
        $this->product_tax_code = $product_tax_code;
        return $this;
    }
}
