<?php


class Cart extends CartCore {
	
	
	public function updateQty(
		$quantity,
		$id_product,
		$id_product_attribute = null,
		$id_customization = false,
		$operator = 'up',
		$id_address_delivery = 0,
		Shop $shop = null,
		$auto_add_cart_rule = true,
		$skipAvailabilityCheckOutOfStock = false,
		bool $preserveGiftRemoval = true,
		bool $useOrderPrices = false
	) {
		if (!$shop) {
			$shop = Context::getContext()->shop;
		}
		
		if (Validate::isLoadedObject(Context::getContext()->customer)) {
			if ($id_address_delivery == 0 && (int) $this->id_address_delivery) {
				$id_address_delivery = $this->id_address_delivery;
			} elseif ($id_address_delivery == 0) {
				$id_address_delivery = (int) Address::getFirstCustomerAddressId(
					(int) Context::getContext()->customer->id
				);
			} elseif (!Customer::customerHasAddress(Context::getContext()->customer->id, $id_address_delivery)) {
				$id_address_delivery = 0;
			}
		} else {
			$id_address_delivery = 0;
		}
		
		$quantity = (int) $quantity;
		$id_product = (int) $id_product;
		$id_product_attribute = (int) $id_product_attribute;
		$product = new Product($id_product, false, Configuration::get('PS_LANG_DEFAULT'), $shop->id);
		
		if ($id_product_attribute) {
			$combination = new Combination((int) $id_product_attribute);
			if ($combination->id_product != $id_product) {
				return false;
			}
		}
		
		
		if (!empty($id_product_attribute)) {
			$minimal_quantity = (int) Attribute::getAttributeMinimalQty($id_product_attribute);
		} else {
			$minimal_quantity = (int) $product->minimal_quantity;
		}
		
		if (!Validate::isLoadedObject($product)) {
			die(Tools::displayError());
		}
		
		if (isset(self::$_nbProducts[$this->id])) {
			unset(self::$_nbProducts[$this->id]);
		}
		
		if (isset(self::$_totalWeight[$this->id])) {
			unset(self::$_totalWeight[$this->id]);
		}
		
		$data = [
			'cart' => $this,
			'product' => $product,
			'id_product_attribute' => $id_product_attribute,
			'id_customization' => $id_customization,
			'quantity' => $quantity,
			'operator' => $operator,
			'id_address_delivery' => $id_address_delivery,
			'shop' => $shop,
			'auto_add_cart_rule' => $auto_add_cart_rule,
		];
		
		
		Hook::exec('actionCartUpdateQuantityBefore', $data);
		
		if ((int) $quantity <= 0) {
			return $this->deleteProduct($id_product, $id_product_attribute, (int) $id_customization, (int) $id_address_delivery, $preserveGiftRemoval, $useOrderPrices);
		}
		
		if (!$product->available_for_order
			|| (
				Configuration::isCatalogMode()
				&& !defined('_PS_ADMIN_DIR_')
			)
		) {
			return false;
		}
		
		
		$cartProductQuantity = $this->getProductQuantity(
			$id_product,
			$id_product_attribute,
			(int) $id_customization,
			(int) $id_address_delivery
		);
		
		
		if (!empty($cartProductQuantity['quantity'])) {
			
			if (isset($combination) && !empty($combination->getAttributesName(1))) {
				foreach ($combination->getAttributesName(1) as $customAttribute) {
					if($customAttribute['id_attribute'] == "5") {
						return false;
					}
				}
			}
			
			
			$productQuantity = Product::getQuantity($id_product, $id_product_attribute, null, $this);
			$availableOutOfStock = Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock($product->id));
			
			if ($operator == 'up') {
				$updateQuantity = '+ ' . $quantity;
				$newProductQuantity = $productQuantity - $quantity;
				
				if ($newProductQuantity < 0 && !$availableOutOfStock && !$skipAvailabilityCheckOutOfStock) {
					return false;
				}
			} elseif ($operator == 'down') {
				$cartFirstLevelProductQuantity = $this->getProductQuantity(
					(int) $id_product,
					(int) $id_product_attribute,
					$id_customization
				);
				$updateQuantity = '- ' . $quantity;
				
				if ($cartFirstLevelProductQuantity['quantity'] <= 1
					|| $cartProductQuantity['quantity'] - $quantity <= 0
				) {
					return $this->deleteProduct((int) $id_product, (int) $id_product_attribute, (int) $id_customization, (int) $id_address_delivery, $preserveGiftRemoval, $useOrderPrices);
				}
			} else {
				return false;
			}
			
			Db::getInstance()->execute(
				'UPDATE `' . _DB_PREFIX_ . 'cart_product`
                    SET `quantity` = `quantity` ' . $updateQuantity . '
                    WHERE `id_product` = ' . (int) $id_product .
				' AND `id_customization` = ' . (int) $id_customization .
				(!empty($id_product_attribute) ? ' AND `id_product_attribute` = ' . (int) $id_product_attribute : '') . '
                    AND `id_cart` = ' . (int) $this->id . (Configuration::get('PS_ALLOW_MULTISHIPPING') && $this->isMultiAddressDelivery() ? ' AND `id_address_delivery` = ' . (int) $id_address_delivery : '') . '
                    LIMIT 1'
			);
		} elseif ($operator == 'up') {
			
			
			$sql = 'SELECT stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity
                        FROM ' . _DB_PREFIX_ . 'product p
                        ' . Product::sqlStock('p', $id_product_attribute, true, $shop) . '
                        WHERE p.id_product = ' . $id_product;
			
			$result2 = Db::getInstance()->getRow($sql);
			if (Pack::isPack($id_product)) {
				$result2['quantity'] = Pack::getQuantity($id_product, $id_product_attribute, null, $this);
			}
			
			if (isset($result2['out_of_stock']) && !Product::isAvailableWhenOutOfStock((int) $result2['out_of_stock']) && !$skipAvailabilityCheckOutOfStock) {
				if ((int) $quantity > $result2['quantity']) {
					return false;
				}
			}
			
			if ((int) $quantity < $minimal_quantity) {
				return -1;
			}
			
			$result_add = Db::getInstance()->insert('cart_product', [
				'id_product' => (int) $id_product,
				'id_product_attribute' => (int) $id_product_attribute,
				'id_cart' => (int) $this->id,
				'id_address_delivery' => (int) $id_address_delivery,
				'id_shop' => $shop->id,
				'quantity' => (int) $quantity,
				'date_add' => date('Y-m-d H:i:s'),
				'id_customization' => (int) $id_customization,
			]);
			
			if (!$result_add) {
				return false;
			}
		}
		$this->_products = $this->getProducts(true);
		$this->update();
		$context = Context::getContext()->cloneContext();
		$context->cart = $this;
		Cache::clean('getContextualValue_*');
		CartRule::autoRemoveFromCart(null, $useOrderPrices);
		if ($auto_add_cart_rule) {
			CartRule::autoAddToCart($context, $useOrderPrices);
		}
		
		if ($product->customizable) {
			return $this->_updateCustomizationQuantity(
				(int) $quantity,
				(int) $id_customization,
				(int) $id_product,
				(int) $id_product_attribute,
				(int) $id_address_delivery,
				$operator
			);
		}
		
		return true;
	}
    /*
    * module: orderfees_shipping
    * date: 2024-02-02 08:19:55
    * version: 1.23.11
    */
    public function getPackageShippingCost(
        $id_carrier = null,
        $use_tax = true,
        Country $default_country = null,
        $product_list = null,
        $id_zone = null,
        bool $keepOrderPrices = false
    ) {
        if ($this->isVirtualCart()) {
            return 0;
        }
        
        static $cache = [];
        static $module = null;
        
        if ($module === null) {
            $module = Module::getInstanceByName('orderfees_shipping');
        }
        
        $cache_key = crc32(json_encode(func_get_args()));
        
        if (!isset($cache[$cache_key])) {
            $total = 0;
            $return = false;
            $cache[$cache_key] = false;
            Hook::exec('actionCartGetPackageShippingCost', array(
                'object' => &$this,
                'id_carrier' => &$id_carrier,
                'use_tax' => &$use_tax,
                'default_country' => &$default_country,
                'product_list' => &$product_list,
                'id_zone' => &$id_zone,
                'keepOrderPrices' => &$keepOrderPrices,
                'total' => &$total,
                'return' => &$return
            ));
            if ($return) {
                $cache[$cache_key] = ($total !== false ? (float) Tools::ps_round((float) $total, 2) : false);
            } else {
                $shipping_cost = parent::getPackageShippingCost(
                    $id_carrier,
                    $use_tax,
                    $default_country,
                    $product_list,
                    $id_zone,
                    $keepOrderPrices
                );
                if ($shipping_cost !== false) {
                    $cache[$cache_key] = $shipping_cost + (float) Tools::ps_round((float) $total, 2);
                }
            }
        }
        
        return $cache[$cache_key];
    }

	public function getParentPackageShippingCost (
		$id_carrier,
		$use_tax,
		$default_country,
		$product_list,
		$id_zone,
		$keepOrderPrices
	){
		return parent::getPackageShippingCost(
			$id_carrier,
			$use_tax,
			$default_country,
			$product_list,
			$id_zone,
			$keepOrderPrices
		);
	}
	
	/*
    * module: orderfees_shipping
    * date: 2024-02-02 08:19:55
    * version: 1.23.11
    */
    public function getTotalWeight($products = null)
    {
        $total_weight = 0;
        $return = false;
        
        Hook::exec('actionCartGetTotalWeight', array(
            'object' => &$this,
            'products' => &$products,
            'total_weight' => &$total_weight,
            'return' => &$return
        ));
        
        if ($return) {
            return $total_weight;
        }
        
        return parent::getTotalWeight($products) + $total_weight;
    }

	/**
     * @param int $productId
     * @param int $combinationId
     * @param int $customizationId
     * @param bool $withTaxes
     * @param bool $useReduction
     * @param bool $withEcoTax
     * @param int $productQuantity
     * @param int|null $addressId
     * @param Context $shopContext
     * @param array|false|null $specificPriceOutput
     *
     * @return float|null
     */
    private function getCartPriceFromCatalog(
        int $productId,
        int $combinationId,
        int $customizationId,
        bool $withTaxes,
        bool $useReduction,
        bool $withEcoTax,
        int $productQuantity,
        ?int $addressId,
        Context $shopContext,
        &$specificPriceOutput
    ): ?float {
        return Product::getPriceStatic(
            $productId,
            $withTaxes,
            $combinationId,
            6,
            null,
            false,
            $useReduction,
            $productQuantity,
            false,
            (int) $this->id_customer ? (int) $this->id_customer : null,
            (int) $this->id,
            $addressId,
            $specificPriceOutput,
            $withEcoTax,
            true,
            $shopContext,
            true,
            $customizationId
        );
    }
	
}