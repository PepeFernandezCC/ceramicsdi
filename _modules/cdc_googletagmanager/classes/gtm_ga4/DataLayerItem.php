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

/**
 * Represent GTM Datalayer Item
 * @property string wholesale_price
 */
class Gtm_DataLayerItem
{
	public $item_name;
	public $item_id;
	public $price;
	public $price_tax_exc;
	public $item_brand;
	public $item_category;
    public $item_category2;
    public $item_category3;
    public $item_category4;
	public $item_variant;
	public $item_list_name;
	public $item_list_id;
	public $index;
	public $quantity;

    /**
     * Gtm_DataLayerItem constructor.
     *
     * @param Cdc_GoogleTagManager $gtm
     * @param ProductCore $product
     * @param array|null $list
     */
    public function __construct($gtm, $product, $list = null) {
        $datalayerProduct = new Gtm_DataLayerProduct($gtm, $product, $list);
        $this->item_name = $datalayerProduct->name;
        $this->item_id = $datalayerProduct->id;
        $this->price = $datalayerProduct->price;
        $this->price_tax_exc = $datalayerProduct->price_tax_exc;
        $this->item_brand = $datalayerProduct->brand;
        $this->item_category = $datalayerProduct->item_category;
        $this->item_category2 = $datalayerProduct->item_category2;
        $this->item_category3 = $datalayerProduct->item_category3;
        $this->item_category4 = $datalayerProduct->item_category4;
        $this->item_variant = $datalayerProduct->variant;
        $this->item_list_name = !empty($list['name']) ? $list['name'] : null;
        $this->item_list_id = !empty($list['id']) ? $list['id'] : null;
        $this->index = !empty($list['index']) ? $list['index'] : null;
        $this->quantity = !empty($datalayerProduct->quantity) ? $datalayerProduct->quantity : 1;

        if(!empty($datalayerProduct->wholesale_price)) {
            $this->wholesale_price = $datalayerProduct->wholesale_price;
        }
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
