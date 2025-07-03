<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SAS Comptoir du Code
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SAS Comptoir du Code is strictly forbidden.
 * In order to obtain a license, please contact us: contact@comptoirducode.com
 *
 * @author    Vincent - Comptoir du Code
 * @copyright Copyright(c) 2015-2022 SAS Comptoir du Code
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * @package   cdc_googletagmanager
 */

include_once(_CDCGTM_DIR_.'/services/Gtm_Product.php');

/**
 * Represent GTM Datalayer Product
 */
class Gtm_DataLayerProduct
{
	public $name;
	public $id;
    public $reference;
	public $price;
	public $price_tax_exc;
	public $brand;
	public $category;
	public $item_category;
    public $item_category2;
    public $item_category3;
    public $item_category4;
	public $variant;
    public $list;
    public $position;
	public $quantity;

    /**
     * Gtm_DataLayerProduct constructor.
     * @param $product
     * @param int $id_product_attribute
     */
	public function __construct($gtm, $product, $list = null) {
        $product = $product instanceof Product ? $product : (object)$product;
        $variant = null;

        // set correct ID
        if(empty($product->id)) {
            $product->id = !empty($product->id_product) ? $product->id_product : 0;
            if(!$product->id) {
                // error no product id
                return null;
            }
        }

        $id_product_attribute = !empty($product->id_product_attribute) ? $product->id_product_attribute : PrestashopUtils::getProductAttributeId($product);
        if($id_product_attribute) {
            $variant = new Combination($id_product_attribute);
            if(!Validate::isLoadedObject($variant)) {
                $variant = null;
                $id_product_attribute = 0;
            }
        }

        // product ID
        $this->id = (string) Gtm_Product::getInstance()->getProductId($product, $variant);


        // reference
        if(!empty($variant->reference)) {
            $this->reference = (string) $variant->reference;
        } else {
            $this->reference = (string) $product->reference;
        }

        // product name
        $this->name = Gtm_Product::getInstance()->getProductName($product);
        $this->link = Gtm_Product::getInstance()->getProductName($product, "link_rewrite");

        // variant name
        if($id_product_attribute) {
            $this->variant = $gtm->cleanString(PrestashopUtils::getAttributeSmall($id_product_attribute, $gtm->getDataLanguage()));
        }

        // product price
        $this->price = (string) round((float) Product::getPriceStatic($product->id, true, $id_product_attribute), _CDCGTM_PRICE_DECIMAL_);
        $this->price_tax_exc = (string) round((float) Product::getPriceStatic($product->id, false, $id_product_attribute), _CDCGTM_PRICE_DECIMAL_);

        // wholesale price
        if($gtm->display_wholesale_price) {
            $wholesale_price = $product->wholesale_price;
            if ($variant && $variant->wholesale_price > 0) {
                $wholesale_price = $variant->wholesale_price;
            }
            $this->wholesale_price = (string)round((float)$wholesale_price, _CDCGTM_PRICE_DECIMAL_);
        }

        // if list is set use it
        $this->list = !empty($list['name']) ? $list['name'] : null;
        $this->position = !empty($list['index']) ? $list['index'] : null;

        // category
        if(!empty($list['name'])) {
            $this->category = $list['name'];
        } else {
            $this->category = $gtm->getCategoryName($product->id_category_default);
        }

        // categories (by level)
        $category = new Category($product->id_category_default);
        $this->item_category = $gtm->getPageCategoryNameHierarchy($category, 1);
        $this->item_category2 = $gtm->getPageCategoryNameHierarchy($category, 2);
        $this->item_category3 = $gtm->getPageCategoryNameHierarchy($category, 3);
        $this->item_category4 = $gtm->getPageCategoryNameHierarchy($category, 4);

        // manufacturer
        if($product->id_manufacturer) {
            $manufacturer_name = $gtm->cleanString(Manufacturer::getNameById((int)$product->id_manufacturer));
            $this->brand = $manufacturer_name;
        }

        // order quantity
        if(!empty($product->product_quantity)) {
            $this->quantity = (int) $product->product_quantity;
        }
        // cart quantity
        elseif(!empty($product->cart_quantity)) {
            $this->quantity = (int) $product->cart_quantity;
        }
        // stock
        elseif($gtm->display_product_stock) {
            if(!empty($product->quantity)) {
                $this->quantity = (int)$product->quantity;
            } else {
                $this->quantity = (int) StockAvailable::getQuantityAvailableByProduct($product->id, $id_product_attribute);
            }
        }
        // fallback
        if(empty($this->quantity)) {
            $this->quantity = 1;
        }

        return $this;
    }

	public function removeNull()
	{
		$properties = get_object_vars($this);
		foreach ($properties as $p_key => $p_val) {
			if(is_null($p_val)) {
				unset($this->$p_key);
			}
		}
	}
}
