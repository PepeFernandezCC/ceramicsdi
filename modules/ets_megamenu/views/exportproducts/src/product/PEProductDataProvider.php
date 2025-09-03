<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licensed under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the license agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    MyPrestaModules
 * @copyright 2013-2020 MyPrestaModules
 * @license LICENSE.txt
 */

require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PEExtraField.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PECategoryTreeGenerator.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PEGoogleCategory.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PESerializationChecker.php';

class PEProductDataProvider
{
    public $apply_price_decoration = true;

    private $product;
    private $combination;
    private $all_combinations;
    private $configuration;
    private $id_lang;
    private $id_shop;

    private $product_data_for_export = [];

    public function __construct($id_product, $configuration, $id_product_attribute = false)
    {
        $this->configuration = $configuration;
        $this->id_lang = $this->configuration['id_lang'];
        $this->id_shop = $this->configuration['id_shop'];

        Context::getContext()->cookie->__set('id_lang', $this->id_lang);
        Context::getContext()->cookie->write();

        if (!$this->isProductInShop($id_product)) {
            $this->id_shop = $this->getProductShopId($id_product);
        }

        Shop::setContext(Shop::CONTEXT_SHOP, (int)$this->id_shop);
        Context::getContext()->shop = new Shop((int)$this->id_shop);
        Context::getContext()->currency = new Currency((int)$this->configuration['currency']);
        Context::getContext()->language = new Language((int)$this->id_lang);
        Context::getContext()->country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'), (int)$this->id_lang, (int)$this->id_shop);

        $this->product = new \Product($id_product, true, $this->id_lang, $this->id_shop);

        $id_product_attribute = $id_product_attribute != false ? $id_product_attribute : 0;
        $this->combination = new \Combination($id_product_attribute, $this->id_lang, $this->id_shop);

        $this->all_combinations = $this->configuration['separate'] ? [] : $this->getAllProductCombinations();
    }

    public function getProductDataForExport()
    {
        foreach ($this->configuration['fields'] as $field_id => $field_data) {
            $export_field_value = $this->getExportFieldValue($field_id, $field_data);

            if ($export_field_value || $export_field_value == 0) {
                $this->product_data_for_export[$field_id] = $export_field_value;
            } else {
                $this->product_data_for_export[$field_id] = '';
            }
        }

        return $this->product_data_for_export;
    }

    public function getExportFieldValue($field_id, $field_data)
    {
        if ($field_id == 'id_product') {
            return $this->product->id;
        } elseif ($field_id === 'id_product_attribute') {
            return $this->getCombinationIds();
        } elseif ($field_id === 'name') {
            return $this->product->name;
        } elseif ($field_id === 'reference') {
            return $this->getProductReference();
        } elseif ($field_id === 'name_with_combination') {
            return $this->getNameWithCombination();
        } elseif ($field_id === 'ean13') {
            return $this->getProductEan();
        }elseif ($field_id === 'tax_rate') {
          return $this->getTaxRate();
        }elseif ($field_id === 'upc') {
            return $this->getProductUpc();
        } elseif ($field_id === 'isbn') {
            return $this->getIsbn();
        } elseif ($field_id === 'mpn') {
            return $this->getMpn();
        } elseif ($field_id === 'identifier_exists') {
            return $this->getIdentifierExists();
        } elseif ($field_id === 'default_category_name') {
            return $this->getDefaultCategoryName();
        } elseif ($field_id === 'default_category_tree') {
            return $this->getDefaultCategoryTree();
        } elseif ($field_id === 'google_category_id') {
            return $this->getGoogleCategoryId();
        } elseif ($field_id === 'google_category') {
            return $this->getGoogleCategory();
        } elseif (preg_match('/^images_value_(\d+)$/', $field_id, $matches)) {
            $image_number = $matches[1];
            return $this->getImageLinksForExportInSeparateColumns($image_number);
        } elseif ($field_id === 'categories_ids') {
            return $this->getCategoryIds();
        } elseif ($field_id === 'categories_names') {
            return $this->getCategoryNames();
        } elseif ($field_id === 'suppliers_ids') {
            return $this->getProductSuppliersID();
        } elseif ($field_id === 'suppliers_name') {
            return $this->getProductSuppliersNames();
        } elseif ($field_id === 'quantity') {
            return $this->getCombinationQuantity();
        } elseif ($field_id === 'availability') {
            return $this->getAvailability();
        } elseif ($field_id === 'total_quantity') {
            return $this->getTotalQuantity();
        } elseif ($field_id === 'out_of_stock') {
            return \StockAvailable::outOfStock($this->product->id);
        } elseif ($field_id === 'depends_on_stock') {
            return \StockAvailable::dependsOnStock($this->product->id);
        } elseif ($field_id === 'manufacturer_name') {
            return \Manufacturer::getNameById((int)$this->product->id_manufacturer);
        } elseif ($field_id === 'supplier_name') {
            return \Supplier::getNameById((int)$this->product->id_supplier);
        } elseif ($field_id === 'new') {
            return $this->product->isNew();
        } elseif ($field_id === 'supplier_reference') {
            return $this->getSupplierReference();
        } elseif ($field_id === 'supplier_price') {
            return $this->getSupplierPrice();
        } elseif ($field_id === 'supplier_price_currency') {
            return $this->getSupplierCurrency();
        } elseif ($field_id === 'base_price' || $field_id === 'unit_price_ratio' || $field_id === 'ecotax' || $field_id === 'additional_shipping_cost' || $field_id === 'unit_price' || $field_id === 'wholesale_price') {
            return $this->getFormattedPrice($this->product->$field_id);
        } elseif ( $field_id === 'unit_price_with_tax') {
            return $this->getUnitPriceWithTax();
        } elseif ($field_id === 'base_price_with_tax') {
            return $this->getBasePriceWithTax();
        } elseif ($field_id === 'price') {
            return $this->getPrice(false, true);
        } elseif ($field_id === 'final_price_with_tax') {
            return $this->getPrice(true, true);
        } elseif ($field_id === 'final_price_without_tax_and_no_reduction') {
            return $this->getPrice(false, false);
        } elseif ($field_id === 'final_price_with_tax_and_no_reduction') {
            return $this->getPrice(true, false);
        } elseif (preg_match('/^attribute_group_(\d+)$/', $field_id, $matches)) {
            $id_attribute_group = $matches[1];
            return $this->getAttributeGroupValue($id_attribute_group);
        } elseif ($field_id === 'is_default_combination') {
            return $this->isDefaultCombination();
        } elseif ($field_id === 'combination_final_price_pre_tax') {
            return $this->getDefaultCombinationFinalPrice(false);
        } elseif ($field_id === 'combination_final_price_with_tax') {
            return $this->getDefaultCombinationFinalPrice(true);
        } elseif ($field_id === 'combinations_name') {
            return $this->getCombinationName();
        } elseif ($field_id === 'combinations_price') {
            return $this->getCombinationPrice();
        } elseif ($field_id === 'combinations_price_with_tax') {
            return $this->getCombinationPriceWithTax();
        } elseif ($field_id === 'combinations_wholesale_price') {
            return $this->getCombinationWholesalePrice();
        } elseif ($field_id === 'combinations_unit_price_impact') {
            return $this->getCombinationUnitPriceImpact();
        } elseif ($field_id === 'combinations_reference') {
            return $this->getCombinationReference();
        } elseif ($field_id === 'combinations_location') {
            return $this->getCombinationLocation();
        } elseif ($field_id === 'combinations_weight') {
            return $this->getCombinationWeight();
        } elseif ($field_id === 'combinations_ecotax') {
            return $this->getCombinationEcotax();
        } elseif ($field_id === 'combinations_ean13') {
            return $this->getCombinationEan13();
        } elseif ($field_id === 'combinations_upc') {
            return $this->getCombinationUpc();
        } elseif ($field_id === 'combinations_isbn') {
            return $this->getCombinationIsbn();
        } elseif ($field_id === 'combinations_mpn') {
            return $this->getCombinationMpn();
        } elseif ($field_id === 'minimal_quantity') {
            return $this->getMinimalQuantity();
        } elseif ($field_id === 'location') {
            return $this->getLocation();
        } elseif ($field_id === 'low_stock_threshold') {
            return $this->getLowStockThreshold();
        } elseif ($field_id === 'low_stock_alert') {
            return $this->getLowStockAlert();
        } elseif ($field_id === 'available_date') {
            return $this->getAvailableDate();
        } elseif ($field_id === 'tags') {
            return $this->product->getTags($this->id_lang);
        } elseif ($field_id === 'id_attachments') {
            return $this->getAttachmentIds();
        } elseif ($field_id === 'attachments_name') {
            return $this->getAttachmentNames();
        } elseif ($field_id === 'attachments_description') {
            return $this->getAttachmentDescriptions();
        } elseif ($field_id === 'attachments_file') {
            return $this->getAttachmentFileLinks();
        } elseif ($field_id === 'id_carriers') {
            return $this->getCarrierIds();
        } elseif ($field_id === 'id_product_accessories') {
            return $this->getAccessoryIds();
        } elseif ($field_id === 'image_caption') {
            return $this->getImageCaptions();
        } elseif ($field_id === 'images') {
            return $this->getImageLinks();
        } elseif ($field_id === 'image_cover') {
            return $this->getImageCover();
        } elseif ($field_id === 'cover_image_url') {
            return $this->getImageCoverUrl();
        } elseif (preg_match('/^feature_(\d+)$/', $field_id, $matches)) {
            $id_feature = $matches[1];
            return $this->getFeatureValue($id_feature);
        } elseif ($field_id === 'product_link') {
            return $this->getProductLink();
        } elseif ($field_id === 'description') {
            return  $this->getDescription();
        } elseif ($field_id === 'description_short') {
            return  $this->getDescriptionShort();
        } elseif ($field_id === 'date_add' || $field_id == 'date_upd') {
            return $this->formatDateValue($this->product->$field_id);
        } elseif ($field_id === 'width' || $field_id === 'height' || $field_id === 'depth' || $field_id === 'weight') {
            return  $this->getProductPhysicalProperty($field_id);
        } elseif (in_array($field_id, $this->getListOfPackItemsFields())) {
            return  $this->getPackItemProperty($field_id);
        } elseif (preg_match('/^customization_field_.+$/', $field_id)) {
            return  $this->getCustomizationFieldProperty($field_id);
        } elseif (preg_match('/id_specific_price_\d+/', $field_id)) {
            return  $this->getSpecificPriceProperty('id_specific_price', $field_id);
        } elseif (preg_match('/specific_price_\d+/', $field_id)) {
            return  $this->getSpecificPriceProperty('price', $field_id);
        } elseif (preg_match('/^specific_price_from_quantity_\d+$/', $field_id)) {
            return  $this->getSpecificPriceProperty('from_quantity', $field_id);
        } elseif (preg_match('/^specific_price_reduction_\d+$/', $field_id)) {
            return  $this->getSpecificPriceProperty('reduction', $field_id);
        } elseif (preg_match('/^specific_price_reduction_type_\d+$/', $field_id)) {
            return  $this->getSpecificPriceProperty('reduction_type', $field_id);
        } elseif (preg_match('/^specific_price_from_\d+$/', $field_id)) {
            return  $this->getSpecificPriceProperty('from', $field_id);
        } elseif (preg_match('/^specific_price_to_\d+$/', $field_id)) {
            return  $this->getSpecificPriceProperty('to', $field_id);
        } elseif (preg_match('/^specific_price_id_group_\d+$/', $field_id)) {
            return  $this->getSpecificPriceProperty('id_group', $field_id);
        } elseif (preg_match('/^specific_price_id_customer_\d+$/', $field_id)) {
            return  $this->getSpecificPriceProperty('id_customer', $field_id);
        } else if (property_exists($this->product, $field_id)) {
            return  $this->product->$field_id;
        } elseif (preg_match('/^category_tree_(\d+)$/', $field_id, $matches)) {
            if (empty($matches[1])) {
                return false;
            }

            $tree_number = $matches[1];
            return $this->getCategoryTreeByTreeNumber($tree_number);
        } elseif (preg_match('/^separate_supplier_(\w+)_(\d+)$/', $field_id, $supplier_matches)) {
            $num_of_matches_in_valid_property_name = 3;
            if (count($supplier_matches) != $num_of_matches_in_valid_property_name) {
                return false;
            }

            $supplier_property = $supplier_matches[1];
            $supplier_id       = $supplier_matches[2];

            return $this->getProductSupplierPropertyForExport($supplier_property, $supplier_id);
        }  else if (preg_match('/static_field_\d+$/', $field_id)) {
            return isset($field_data['value']) ? $field_data['value'] : $field_data['default_value'];
        }  else if (preg_match('/extra_field_\d+$/', $field_id)) {
            if (empty($field_data['conditions'])) {
                return false;
            }

            $this_provider_clone = clone $this;
            $extra_field = new PEExtraField($field_data['conditions'], $this_provider_clone);
            $extra_field_value = $extra_field->getValue();

            return $extra_field_value;
        }  else {
            return '';
        }
    }

    private function getFeatureValue($id_feature)
    {
        if (\Module::getInstanceByName('pm_multiplefeatures')) {
            $all_product_features = \Module::getInstanceByName('pm_multiplefeatures')->getFrontFeatures($this->product->id);
        } else {
            $all_product_features = $this->product->getFrontFeatures($this->id_lang);
        }

        $feature_values = [];

        foreach ($all_product_features as $feature) {
            if ($id_feature != $feature['id_feature']) {
                continue;
            }

            $feature_values[] = $feature['value'];
        }

        if (empty($feature_values)) {
            return false;
        }

        return implode(',', $feature_values);
    }

    private function getCombinationIds()
    {
        if ($this->configuration['separate'] || empty($this->all_combinations)) {
            return $this->combination->id;
        }

        $combination_ids = [];
        foreach ($this->all_combinations as $id_product_attribute => $combination) {
            $combination_ids[] = $id_product_attribute;
        }

        return implode(',', $combination_ids);
    }

    private function getProductReference()
    {
        return $this->product->reference;
    }

    private function getProductEan()
    {
        return $this->product->ean13;
    }

    private function getProductUpc()
    {
        return $this->product->upc;
    }

    private function getNameWithCombination()
    {
        if ($this->configuration['separate'] || empty($this->all_combinations)) {
            return $this->getFullProductName($this->combination->id);
        }

        $names = [];
        foreach ($this->all_combinations as $id_product_attribute => $combination) {
            $names[] = $this->getFullProductName($id_product_attribute);
        }

        return implode(',', $names);
    }

    private function getIsbn()
    {
        if (!property_exists('Product', 'isbn')) {
            return false;
        }

        return $this->product->isbn;
    }

    private function getMpn()
    {
        if (!property_exists('Product', 'mpn')) {
            return false;
        }

        return $this->product->mpn;
    }

    private function getIdentifierExists()
    {
        if ($this->getProductEan() ||
            $this->getProductUpc() ||
            $this->getIsbn() ||
            $this->getMpn() ||
            $this->getCombinationEan13() ||
            $this->getCombinationUpc() ||
            $this->getCombinationIsbn() ||
            $this->getCombinationMpn()
        ) {
            return 'yes';
        }

        return 'no';
    }

    private function getGoogleCategoryId()
    {
        if (empty($this->configuration['google_categories'])) {
            return false;
        }

        if (is_array($this->configuration['google_categories'])) {
            $google_categories = $this->configuration['google_categories'];
        } else if (PESerializationChecker::isStringSerialized($this->configuration['google_categories'])) {
            $google_categories = Tools::unSerialize($this->configuration['google_categories']);
        } else {
            $google_categories = json_decode($this->configuration['google_categories'], true);
        }

        if (empty($google_categories) || !is_array($google_categories)) {
            return false;
        }

        if (!isset($google_categories[$this->product->id_category_default])) {
            return false;
        }

        return $google_categories[$this->product->id_category_default];
    }

    private function getGoogleCategory()
    {
        $google_category_id = $this->getGoogleCategoryId();

        if (!$google_category_id) {
            return false;
        }

        return PEGoogleCategory::getNameById($google_category_id);
    }

    private function getDefaultCategoryName()
    {
        $default_category = new \Category($this->product->id_category_default, $this->id_lang);
        return $default_category->name;
    }

    private function getImageLinksForExportInSeparateColumns($image_number)
    {
        $link = new \Link(null, 'http://');

        $product_images = $this->getProductImageIds();

        if (empty($product_images[$image_number - 1])) {
            return false;
        }

        $image = $product_images[$image_number - 1];

        $img_link = $link->getImageLink($this->product->link_rewrite, $image['id'], $this->getImageType());
        $img_link = $img_link ? $this->getImageLinkWithProperShopProtocol($img_link) : '';

        if ($this->configuration['format_file'] == 'gmf' && $img_link == $this->getImageCoverUrl()) {
            return false;
        }

        return $img_link;
    }

    private function getCategoryIds()
    {
        $category_ids = [];
        $all_categories_ids = $this->product->getWsCategories();

        if (!empty($all_categories_ids)) {
            foreach ($all_categories_ids as $category_id_container) {
                $category_ids[] = $category_id_container['id'];
            }
        }

        return implode(',', $category_ids);
    }

    private function getCategoryNames()
    {
        $category_names = [];
        $all_categories_ids = $this->product->getWsCategories();

        if (!empty($all_categories_ids)) {
            foreach ($all_categories_ids as $category_id_container) {
                $category_instance = new \Category($category_id_container['id'], $this->id_lang, $this->id_shop);
                $category_names[] = $category_instance->name;
            }
        }

        return implode(',', $category_names);
    }

    private function getCategoryTreeByTreeNumber($tree_number)
    {
        $product_category_ids = self::getProductCategoryIds($this->product->id);
        $category_tree_generator = new PECategoryTreeGenerator($this->id_lang);
        $current_tree_number = 1;

        foreach ($product_category_ids as $category_id_container) {
            if (!$tree = $category_tree_generator->getCategoryTree($category_id_container['id'])) {
                continue;
            }

            if ($current_tree_number != $tree_number) {
                $current_tree_number++;
                continue;
            }

            return implode('->', $tree);
        }

        return false;
    }

    private function getDefaultCategoryTree()
    {
        $category_tree_generator = new PECategoryTreeGenerator($this->id_lang);
        $tree = $category_tree_generator->getCategoryTree($this->product->id_category_default);

        if ($tree) {
            return implode('->', $tree);
        }

        return false;
    }

    private function getAvailability()
    {
        $quantity = $this->getCombinationQuantity();
        $min_quantity_allowed_for_order = $this->getMinimalQuantity();
        $allowed_out_of_stock_orders = Configuration::get('PS_ORDER_OUT_OF_STOCK');

        if ($allowed_out_of_stock_orders && $quantity == 0 && $min_quantity_allowed_for_order == 0) {
            return 'preorder';
        }

        return $quantity > 0 ? 'in stock' : 'out of stock';
    }

    private function getCombinationQuantity()
    {
        if ($this->configuration['separate'] || empty($this->all_combinations)) {
            return StockAvailable::getQuantityAvailableByProduct($this->product->id, $this->combination->id, $this->id_shop);
        }

        $combination_quantity = [];
        foreach ($this->all_combinations as $id_product_attribute => $combination) {
            $combination_quantity[] = StockAvailable::getQuantityAvailableByProduct($this->product->id, $id_product_attribute, $this->id_shop);
        }

        return implode(',', $combination_quantity);
    }

    private function getTotalQuantity()
    {
        return StockAvailable::getQuantityAvailableByProduct($this->product->id, 0, $this->id_shop);
    }

    private function getSupplierReference()
    {
        if ($this->configuration['separate'] || empty($this->all_combinations)) {
            return \ProductSupplier::getProductSupplierReference($this->product->id, $this->combination->id, $this->product->id_supplier);
        }

        $supplier_references = [];
        foreach ($this->all_combinations as $id_product_attribute => $combination) {
            $supplier_reference = \ProductSupplier::getProductSupplierReference($this->product->id, $id_product_attribute, $this->product->id_supplier);

            if (empty($supplier_reference)) {
                continue;
            }

            if ($supplier_reference) {
                $supplier_references[] = $supplier_reference;
            }
        }

        return implode(',', $supplier_references);
    }

    private function getSupplierPrice()
    {
        if ($this->configuration['separate'] || empty($this->all_combinations)) {
            $supplier_price = \ProductSupplier::getProductSupplierPrice($this->product->id, $this->combination->id, $this->product->id_supplier);
            return $this->getFormattedPrice($supplier_price);
        }

        $supplier_prices = [];
        foreach ($this->all_combinations as $id_product_attribute => $combination) {
            $supplier_price = \ProductSupplier::getProductSupplierPrice($this->product->id, $id_product_attribute, $this->product->id_supplier);
            $supplier_prices[] = $this->getFormattedPrice($supplier_price);
        }

        return implode(',', $supplier_prices);
    }

    private function getSupplierCurrency()
    {
        if ($this->configuration['separate'] || empty($this->all_combinations)) {
            return $this->retrieveSupplierCurrencyFromPrice($this->combination->id);
        }

        $supplier_currencies = [];
        foreach ($this->all_combinations as $id_product_attribute => $combination) {
            $supplier_currency = $this->retrieveSupplierCurrencyFromPrice($id_product_attribute);

            if ($supplier_currency) {
                $supplier_currencies[] = $this->retrieveSupplierCurrencyFromPrice($id_product_attribute);
            }
        }

        return implode(',', $supplier_currencies);
    }

    private function retrieveSupplierCurrencyFromPrice($id_product_attribute)
    {
        $supplier_price_currency = '';
        $supplier_price_with_currency = \ProductSupplier::getProductSupplierPrice($this->product->id, $id_product_attribute, $this->product->id_supplier, true);

        if (isset($supplier_price_with_currency['id_currency'])) {
            $currency = new \Currency($supplier_price_with_currency['id_currency']);
            $supplier_price_currency = $currency->iso_code;
        }

        return $supplier_price_currency;
    }

    private function getBasePriceWithTax()
    {
        $base_price_with_tax = $this->product->base_price;
        $tax_rate = $this->getTaxRate();

        if ($tax_rate) {
            $base_price_with_tax = $this->product->base_price + ($this->product->base_price * ($tax_rate / 100));
        }

        return $this->getFormattedPrice($base_price_with_tax);
    }

    private function getTaxRate()
    {
        $id_address = null;
        if (is_object(\Context::getContext()->cart) && \Context::getContext()->cart->{\Configuration::get('PS_TAX_ADDRESS_TYPE')} != null) {
            $id_address = \Context::getContext()->cart->{\Configuration::get('PS_TAX_ADDRESS_TYPE')};
        }

        if(!$id_address) {
          $address = new Address();
          $address->id_country = Configuration::get('PS_COUNTRY_DEFAULT');
        }
        else {
          $address = new Address($id_address);
        }

        $tax_rate = $this->product->getTaxesRate($address);

        return $tax_rate;
    }

    private function getPrice($with_tax, $with_reduction)
    {
        if ($this->configuration['separate'] || empty($this->all_combinations)) {
            $price = $this->calculatePrice(
              $this->product->id,
              $with_tax,
              $this->combination->id,
              $this->configuration['round_value'],
              null,
              false,
              $with_reduction
            );

            return $this->getFormattedPrice($price);
        }

        $prices = [];
        foreach ($this->all_combinations as $id_product_attribute => $combination) {
            $price = $this->calculatePrice(
                $this->product->id,
                $with_tax,
                $id_product_attribute,
                $this->configuration['round_value'],
                null,
                false,
                $with_reduction
            );

            $prices[] = $this->getFormattedPrice($price);
        }

        return implode(',', $prices);
    }

    private function calculatePrice(
        $id_product,
        $usetax = true,
        $id_product_attribute = null,
        $decimals = 6,
        $divisor = null,
        $only_reduc = false,
        $usereduc = true,
        $quantity = 1,
        $force_associated_tax = false,
        $id_customer = null,
        $id_cart = null,
        $id_address = null,
        &$specific_price_output = null,
        $with_ecotax = true,
        $use_group_reduction = true,
        Context $context = null,
        $use_customer_price = true,
        $id_customization = null
    ) {
        if (!$context) {
            $context = Context::getContext();
        }

        $cur_cart = $context->cart;

        if ($divisor !== null) {
            Tools::displayParameterAsDeprecated('divisor');
        }

        if (!Validate::isBool($usetax) || !Validate::isUnsignedId($id_product)) {
            die(Tools::displayError());
        }

        // Initializations
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int) $id_customer);
        }
        if (!$id_group) {
            $id_group = (int) Group::getCurrent()->id;
        }

        // If there is cart in context or if the specified id_cart is different from the context cart id
        if (!is_object($cur_cart) || (Validate::isUnsignedInt($id_cart) && $id_cart && $cur_cart->id != $id_cart)) {
            /*
            * When a user (e.g., guest, customer, Google...) is on PrestaShop, he has already its cart as the global (see /init.php)
            * When a non-user calls directly this method (e.g., payment module...) is on PrestaShop, he does not have already it BUT knows the cart ID
            * When called from the back office, cart ID can be inexistant
            */
            if (!$id_cart && !isset($context->employee)) {
                die(Tools::displayError());
            }
            $cur_cart = new Cart($id_cart);
            // Store cart in context to avoid multiple instantiations in BO
            if (!Validate::isLoadedObject($context->cart)) {
                $context->cart = $cur_cart;
            }
        }

        $cart_quantity = 0;
        if ((int) $id_cart) {
            $cache_id = 'Product::getPriceStatic_' . (int) $id_product . '-' . (int) $id_cart;
            if (!Cache::isStored($cache_id) || ($cart_quantity = Cache::retrieve($cache_id) != (int) $quantity)) {
                $sql = 'SELECT SUM(`quantity`)
                FROM `' . _DB_PREFIX_ . 'cart_product`
                WHERE `id_product` = ' . (int) $id_product . '
                AND `id_cart` = ' . (int) $id_cart;
                $cart_quantity = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                Cache::store($cache_id, $cart_quantity);
            } else {
                $cart_quantity = Cache::retrieve($cache_id);
            }
        }

        $id_currency = Validate::isLoadedObject($context->currency) ? (int) $context->currency->id : (int) Configuration::get('PS_CURRENCY_DEFAULT');

        if (!$id_address && Validate::isLoadedObject($cur_cart)) {
            $id_address = $cur_cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
        }

        // retrieve address informations
        $address = Address::initialize($id_address, true);
        $id_country = (int) $address->id_country;
        $id_state = (int) $address->id_state;
        $zipcode = $address->postcode;

        if (Tax::excludeTaxeOption()) {
            $usetax = false;
        }

        if ($usetax != false
            && !empty($address->vat_number)
            && $address->id_country != Configuration::get('VATNUMBER_COUNTRY')
            && Configuration::get('VATNUMBER_MANAGEMENT')) {
            $usetax = false;
        }

        if (null === $id_customer && Validate::isLoadedObject($context->customer)) {
            $id_customer = $context->customer->id;
        }

        $return = Product::priceCalculation(
            (int)$this->configuration['id_shop'],
            $id_product,
            $id_product_attribute,
            $id_country,
            $id_state,
            $zipcode,
            $id_currency,
            $id_group,
            $quantity,
            $usetax,
            $decimals,
            $only_reduc,
            $usereduc,
            $with_ecotax,
            $specific_price_output,
            $use_group_reduction,
            $id_customer,
            $use_customer_price,
            $id_cart,
            $cart_quantity,
            $id_customization
        );

        return $return;
    }

    private function getAttributeGroupValue($id_attribute_group)
    {
        if (!$id_attribute_group) {
            return false;
        }

        if ($this->configuration['separate']) {
            $combination_attributes = $this->getCombinationAttributes($this->combination->id);

            foreach ($combination_attributes as $attribute) {
                if ($id_attribute_group != $attribute['id_attribute_group']) {
                    continue;
                }

                return $attribute['name'];
            }
        } else {
            if (empty($this->all_combinations)) {
                return false;
            }

            $attributes = [];

            foreach ($this->all_combinations as $combination) {
                $combination_attributes = $this->getCombinationAttributes($combination->id);

                foreach ($combination_attributes as $attribute) {
                    if ($id_attribute_group != $attribute['id_attribute_group']) {
                        continue;
                    }

                    if (!in_array($attribute['name'], $attributes)) {
                        $attributes[] = $attribute['name'];
                    }
                }
            }

            return implode(',', $attributes);
        }

        return false;
    }

    private function isDefaultCombination()
    {
        if ($this->configuration['separate']) {
            return (int)$this->combination->default_on;
        }

        if (!empty($this->all_combinations)) {
            $is_default_combination_values = [];
            foreach ($this->all_combinations as $id_product_attribute => $combination) {
                $is_default_combination_values[] = (int)$combination->default_on;
            }

            return implode(',', $is_default_combination_values);
        }

        return '';
    }

    private function getDefaultCombinationFinalPrice($with_tax)
    {
        $default_product_attribute_id = $this->product->getDefaultIdProductAttribute();

        $default_combination_price = $this->calculatePrice(
            $this->product->id,
            $with_tax,
            $default_product_attribute_id,
            $this->configuration['round_value']
        );

        return $this->getFormattedPrice($default_combination_price);
    }

    private function getCombinationPrice()
    {
        if ($this->configuration['separate']) {
            return $this->getFormattedPrice($this->combination->price);
        } else if (!$this->configuration['separate'] && !empty($this->all_combinations)) {
            $prices = [];
            foreach ($this->all_combinations as $combination) {
                $prices[] = $this->getFormattedPrice($combination->price);
            }

            return implode(',', $prices);
        }

        return '';
    }

    private function getCombinationPriceWithTax()
    {
        $tax_rate = $this->getTaxRate();

        if ($this->configuration['separate']) {
            $combination_price_with_tax = ($this->combination->price + ($this->combination->price * ($tax_rate / 100)));
            return $this->getFormattedPrice($combination_price_with_tax);
        } else if (!$this->configuration['separate'] && !empty($this->all_combinations)) {
            $prices = [];
            foreach ($this->all_combinations as $id_product_attribute => $combination) {
                $combination_price_with_tax = ($combination->price + ($combination->price * ($tax_rate / 100)));
                $prices[] = $this->getFormattedPrice($combination_price_with_tax);
            }

            return implode(',', $prices);
        }

        return '';
    }

    private function getCombinationName()
    {
        $combination_name = '';

        if ($this->configuration['separate'] && $this->combination->id) {
            $combination_name = $this->getFullProductName($this->combination->id);
            $combination_name = str_replace($this->product->name . " : ", '', $combination_name);
        } else if (!$this->configuration['separate'] && !empty($this->all_combinations)) {
            $combination_names = [];
            foreach ($this->all_combinations as $id_product_attribute => $combination) {
                $combination_name = $this->getFullProductName($id_product_attribute);
                $combination_names[] = str_replace($this->product->name . " : ", '', $combination_name);
            }

            $combination_name = implode(',', $combination_names);
        }

        return $combination_name;
    }

    private function getCombinationWholesalePrice()
    {
        if ($this->configuration['separate']) {
            return $this->getFormattedPrice($this->combination->wholesale_price);
        }

        if (!empty($this->all_combinations)) {
            $prices = [];
            foreach ($this->all_combinations as $id_product_attribute => $combination) {
                $prices[] = $this->getFormattedPrice($combination->wholesale_price);
            }

            return implode(',', $prices);
        }

        return '';
    }

    private function getCombinationUnitPriceImpact()
    {
        if ($this->configuration['separate']) {
            return $this->getFormattedPrice($this->combination->unit_price_impact);
        }

        if (!empty($this->all_combinations)) {
            $prices = [];
            foreach ($this->all_combinations as $id_product_attribute => $combination) {
                $prices[] = $this->getFormattedPrice($combination->unit_price_impact);
            }

            return implode(',', $prices);
        }

        return '';
    }

    private function getCombinationReference()
    {
        if ($this->configuration['separate']) {
            return $this->combination->reference;
        }

        if (!empty($this->all_combinations)) {
            $references = [];
            foreach ($this->all_combinations as $id_product_attribute => $combination) {
                if (empty($combination->reference)) {
                    continue;
                }

                $references[] = $combination->reference;
            }

            return implode(',', $references);
        }

        return '';
    }

    private function getCombinationLocation()
    {
        if ($this->configuration['separate']) {
            return $this->getCombinationLocationProperty($this->combination);
        }

        if (!$this->configuration['separate'] && !empty($this->all_combinations)) {
            $locations = [];
            foreach ($this->all_combinations as $id_product_attribute => $combination) {
                if (empty($this->getCombinationLocationProperty($combination))) {
                    continue;
                }

                $locations[] = $this->getCombinationLocationProperty($combination);
            }

            if (empty($locations)) {
                return '';
            }

            return implode(',', $locations);
        }

        return '';
    }

    private function getCombinationLocationProperty(CombinationCore $combination)
    {
        if (method_exists(new \StockAvailable(), 'getLocation')) {
            return \StockAvailable::getLocation($this->product->id, $combination->id, $this->id_shop);
        } else {
            return $combination->location;
        }
    }

    private function getCombinationWeight()
    {
        if ($this->configuration['separate']) {
            $weight = \Tools::ps_round($this->combination->weight, $this->configuration['round_value']);
            return number_format($weight, $this->configuration['round_value'], '.', '');
        } else if (!$this->configuration['separate'] && !empty($this->all_combinations)) {
            $weights = [];
            foreach ($this->all_combinations as $id_product_attribute => $combination) {
                $weight = \Tools::ps_round($combination->weight, $this->configuration['round_value']);
                $weights[] = number_format($weight, $this->configuration['round_value'], '.', '');
            }

            return implode(',', $weights);
        }

        return '';
    }

    private function getCombinationEcotax()
    {
        if ($this->configuration['separate']) {
            return $this->getFormattedPrice($this->combination->ecotax);
        } else if (!$this->configuration['separate'] && !empty($this->all_combinations)) {
            $eco_taxes = [];
            foreach ($this->all_combinations as $id_product_attribute => $combination) {
                $eco_taxes[] = $this->getFormattedPrice($combination->ecotax);
            }

            return implode(',', $eco_taxes);
        }

        return '';
    }

    private function getCombinationEan13()
    {
        if ($this->configuration['separate']) {
            return $this->combination->ean13;
        }

        if (!$this->configuration['separate'] && !empty($this->all_combinations)) {
            $ean13 = [];
            foreach ($this->all_combinations as $id_product_attribute => $combination) {
                if (empty($combination->ean13)) {
                    continue;
                }

                $ean13[] = $combination->ean13;
            }

            return implode(',', $ean13);
        }

        return '';
    }

    private function getCombinationUpc()
    {
        if ($this->configuration['separate']) {
            return $this->combination->upc;
        } else if (!$this->configuration['separate'] && !empty($this->all_combinations)) {
            $combinations_upc = [];
            foreach ($this->all_combinations as $id_product_attribute => $combination) {
                if (empty($combination->upc)) {
                    continue;
                }

                $combinations_upc[] = $combination->upc;
            }

            return implode(',', $combinations_upc);
        }

        return '';
    }

    private function getCombinationIsbn()
    {
        if (!property_exists('Combination','isbn')) {
            return '';
        }

        if ($this->configuration['separate']) {
            return $this->combination->isbn;
        } else if (!$this->configuration['separate'] && !empty($this->all_combinations)) {
            $combinations_isbn = [];
            foreach ($this->all_combinations as $id_product_attribute => $combination) {
                if (empty($combination->isbn)) {
                    continue;
                }

                $combinations_isbn[] = $combination->isbn;
            }

            return implode(',', $combinations_isbn);
        }

        return '';
    }

    private function getCombinationMpn()
    {
        if (!property_exists('Combination','mpn')) {
            return '';
        }

        if ($this->configuration['separate']) {
            return $this->combination->mpn;
        } else if (!$this->configuration['separate'] && !empty($this->all_combinations)) {
            $combinations_mpn = [];
            foreach ($this->all_combinations as $id_product_attribute => $combination) {
                if (empty($combination->mpn)) {
                    continue;
                }

                $combinations_mpn[] = $combination->mpn;
            }

            return implode(',', $combinations_mpn);
        }

        return '';
    }

    private function getMinimalQuantity()
    {
        if ($this->configuration['separate'] || empty($this->all_combinations)) {
            return $this->combination->id ? $this->combination->minimal_quantity : $this->product->minimal_quantity;
        }

        $minimal_quantities = [];
        foreach ($this->all_combinations as $id_product_attribute => $combination) {
            $minimal_quantities[] = $combination->minimal_quantity;
        }

        return implode(',', $minimal_quantities);
    }

    private function getLocation()
    {
        if ($this->configuration['separate'] || empty($this->all_combinations)) {
            if ($this->combination->id) {
                return $this->getCombinationLocationProperty($this->combination);
            } else {
                return $this->product->location;
            }
        }

        $locations = [];
        foreach ($this->all_combinations as $id_product_attribute => $combination) {
            $location = $this->getCombinationLocationProperty($combination);
            
            if (empty($location)) {
                continue;
            }

            $locations[] = $location;
        }

        if (empty($locations)) {
            return '';
        }

        return implode(',', $locations);
    }

    private function getLowStockThreshold()
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
            return false;
        }

        if ($this->configuration['separate'] || empty($this->all_combinations)) {
            return $this->combination->id ? $this->combination->low_stock_threshold : $this->product->low_stock_threshold;
        }

        $low_stock_threshold = [];
        foreach ($this->all_combinations as $id_product_attribute => $combination) {
            if (empty($combination->low_stock_threshold)) {
                continue;
            }

            $low_stock_threshold[] = $combination->low_stock_threshold;
        }

        return implode(',', $low_stock_threshold);
    }

    private function getLowStockAlert()
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
            return false;
        }

        if ($this->configuration['separate'] || empty($this->all_combinations)) {
            return $this->combination->id ? $this->combination->low_stock_alert : $this->product->low_stock_alert;
        }

        $low_stock_alert = [];
        foreach ($this->all_combinations as $id_product_attribute => $combination) {
            $low_stock_alert[] = $combination->low_stock_alert;
        }

        return implode(',', $low_stock_alert);
    }

    private function getAvailableDate()
    {
        if ($this->configuration['separate'] || empty($this->all_combinations)) {
            return $this->combination->id ? $this->formatDateValue($this->combination->available_date) : $this->formatDateValue($this->product->available_date);
        }

        $available_date = [];
        foreach ($this->all_combinations as $id_product_attribute => $combination) {
            if (empty($combination->available_date) || $combination->available_date == '0000-00-00') {
                continue;
            }

            $available_date[] = $this->formatDateValue($combination->available_date);
        }

        return implode(',', $available_date);
    }

    private function getAttachmentIds()
    {
        $attachment_ids = [];
        $product_attachments = $this->product->getAttachments($this->id_lang);

        if (!empty($product_attachments)) {
            foreach ($product_attachments as $attachment) {
                $attachment_ids[] = $attachment['id_attachment'];
            }
        }

        return implode(',', $attachment_ids);
    }

    private function getAttachmentNames()
    {
        $attachment_names = [];
        $product_attachments = $this->product->getAttachments($this->id_lang);

        if (!empty($product_attachments)) {
            foreach ($product_attachments as $attachment) {
                $attachment_names[] = $attachment['name'];
            }
        }

        return implode(',', $attachment_names);
    }

    private function getAttachmentDescriptions()
    {
        $attachment_descriptions = [];
        $product_attachments = $this->product->getAttachments($this->id_lang);

        if (!empty($product_attachments)) {
            foreach ($product_attachments as $attachment) {
                if (empty($attachment['description'])) {
                    continue;
                }

                $attachment_descriptions[] = $attachment['description'];
            }
        }

        return implode(',', $attachment_descriptions);
    }

    private function getAttachmentFileLinks()
    {
        $attachment_links = [];
        $product_attachments = $this->product->getAttachments($this->id_lang);
        $link = new \Link(null, 'http://');

        if (!empty($product_attachments)) {
            foreach ($product_attachments as $attachment) {
                $attachment_links[] = $link->getPageLink('attachment', true, null, 'id_attachment=' . $attachment['id_attachment']);
            }
        }

        return implode(',', $attachment_links);
    }

    private function getCarrierIds()
    {
        $carrier_ids = [];
        $product_carriers = $this->product->getCarriers();

        if (!empty($product_carriers)) {
            foreach ($product_carriers as $carrier) {
                $carrier_ids[] = $carrier['id_carrier'];
            }
        }

        return implode(',', $carrier_ids);
    }

    private function getAccessoryIds()
    {
        $accessory_ids = [];
        $product_accessories = $this->product->getWsAccessories();

        if (!empty($product_accessories)) {
            foreach ($product_accessories as $accessory) {
                $accessory_ids[] = $accessory['id'];
            }
        }

        return implode(',', $accessory_ids);
    }

    private function getImageCaptions()
    {
        $image_captions = [];
        $product_image_ids = $this->getProductImageIds();

        if (empty($product_image_ids)) {
            return false;
        }

        foreach ($product_image_ids as $image_id_container) {
            $image = new \Image($image_id_container['id'], $this->id_lang);

            if (!empty($image->legend)) {
                $image_captions[] = $image->legend;
            }
        }

        return implode(',', $image_captions);
    }

    private function getImageLinks()
    {
        $image_links = [];
        $link = new \Link(null, 'http://');

        if ($this->configuration['separate']) {
            $all_images = $this->getCombinationImages(false);

            if (empty($all_images)) {
                $all_images = $this->getProductImageIds();
            }
        } else {
            $all_images = $this->getProductImageIds();
        }

        if (empty($all_images)) {
            return false;
        }

        foreach ($all_images as $image) {
            $image_link = $link->getImageLink($this->product->link_rewrite, $image['id'], $this->getImageType());
            $image_links[] = $this->getImageLinkWithProperShopProtocol($image_link);
        }

        return implode(',', $image_links);
    }

    private function getImageCover()
    {
        $cover = $this->product->getCover($this->product->id);
        $path_to_cover = '';
        $image_type =  $this->getImageType();

        if ($this->configuration['separate']) {
            $combination_images = $this->getCombinationImages(true);

            if (isset($combination_images['id_image'])) {
                $path_to_cover = _PS_ROOT_DIR_ . '/img/p/' . \Image::getImgFolderStatic($combination_images['id_image']) . $combination_images['id_image'] . ($image_type ? ('-' . $image_type) : '') . '.jpg';
            } else if (isset($cover['id_image'])) {
                $path_to_cover = _PS_ROOT_DIR_ . '/img/p/' . \Image::getImgFolderStatic($cover['id_image']) . $cover['id_image'] . ($image_type ? ('-' . $image_type) : '') . '.jpg';
            }
        } else if ($cover) {
            $path_to_cover = _PS_ROOT_DIR_.'/img/p/'.\Image::getImgFolderStatic($cover['id_image']).$cover['id_image'] . ($image_type ? ('-' . $image_type) : '') . '.jpg';
        }

        return $path_to_cover;
    }

    public function getImageCoverUrl()
    {
        $cover = $this->product->getCover($this->product->id);
        $link = new \Link(null, 'http://');
        $cover_url = '';

        if ($this->configuration['separate']) {
            $combination_images = $this->getCombinationImages(true);

            if (isset($combination_images['id_image'])) {
                $cover_url = $link->getImageLink($this->product->link_rewrite, $combination_images['id_image'], $this->getImageType());
            } else if (isset($cover['id_image'])) {
                $cover_url = $link->getImageLink($this->product->link_rewrite, $cover['id_image'], $this->getImageType());
            }
        } else {
            $cover_url = $link->getImageLink($this->product->link_rewrite, $cover['id_image'], $this->getImageType());
        }

        return !empty($cover_url) ? $this->getImageLinkWithProperShopProtocol($cover_url) : '';
    }

    private function getProductLink()
    {
        $link = new \Link();

        if ($this->configuration['separate']) {
            $product_link = $link->getProductLink($this->product->id, null, null, null, $this->id_lang, $this->id_shop, $this->combination->id, false, false, true);
        } else {
            $product_link = $link->getProductLink($this->product->id);
        }

        return $product_link;
    }

    private function getDescription()
    {
        return $this->configuration['strip_tags'] ? strip_tags(trim(preg_replace('/\s+/', ' ',$this->product->description))) : $this->product->description;
    }

    private function getDescriptionShort()
    {
        return $this->configuration['strip_tags'] ? strip_tags(trim(preg_replace('/\s+/', ' ',$this->product->description_short))) : $this->product->description_short;
    }

    private function getProductPhysicalProperty($field_id)
    {
        $field_value = \Tools::ps_round($this->product->$field_id, $this->configuration['round_value']);
        $field_value = number_format($field_value, $this->configuration['round_value'], '.', '');

        if ($this->configuration['format_file'] == 'gmf') {
            switch ($field_id) {
                case 'width':
                case 'height':
                case 'depth':
                    $field_value = (int)$field_value . ' ' . Configuration::get('PS_DIMENSION_UNIT', $this->id_lang, $this->id_shop);
                    break;
                case 'weight':
                    $field_value = (int)$field_value . ' ' . Configuration::get('PS_WEIGHT_UNIT', $this->id_lang, $this->id_shop);
                    break;
                default:
                    break;
            }
        }

        return $field_value;
    }

    private function getPackItemProperty($field_id)
    {
        $pack_items_property_values = '';

        if (\Pack::isPack((int)$this->product->id) && $pack_items_property = $this->getPackItemsFieldProperty($field_id)) {
            $pack_items_property_values = $this->getPackItemsPropertyValuesForExport($pack_items_property);
        }

        return $pack_items_property_values;
    }

    private function getCustomizationFieldProperty($field_id)
    {
        $field_param = explode('_', $field_id);
        $field_param = end($field_param);
        return $this->getCustomizationFieldsParameterValues($this->product->id, $field_param);
    }

    private function getImageLinkWithProperShopProtocol($image_link)
    {
        return str_replace('http://', \Tools::getShopProtocol(), $image_link);
    }

    private function getProductSuppliersID()
    {
        $query = 'SELECT GROUP_CONCAT(DISTINCT ps.id_supplier SEPARATOR ";") AS suppliers_ids
                FROM ' . _DB_PREFIX_ . 'product_supplier AS ps
                INNER JOIN ' . _DB_PREFIX_ . 'supplier AS s
                ON ps.id_supplier = s.id_supplier
                WHERE  ps.id_product = ' . (int)$this->product->id . '
                AND ps.id_product_attribute = 0';

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    private function getProductSuppliersNames()
    {
        $query = 'SELECT GROUP_CONCAT(DISTINCT s.name SEPARATOR ";") AS suppliers_name
                FROM ' . _DB_PREFIX_ . 'product_supplier AS ps
                INNER JOIN ' . _DB_PREFIX_ . 'supplier AS s
                ON ps.id_supplier = s.id_supplier
                WHERE  ps.id_product = ' . (int)$this->product->id . '
                AND ps.id_product_attribute = 0';

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    public static function getProductCategoryIds($id_product)
    {
        $result = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT cp.`id_category` AS id
			FROM `' . _DB_PREFIX_ . 'category_product` cp
			LEFT JOIN `' . _DB_PREFIX_ . 'category` c ON (c.id_category = cp.id_category)
			' . \Shop::addSqlAssociation('category', 'c') . '
			WHERE cp.`id_product` = ' . (int)$id_product . '
            ORDER BY c.level_depth DESC'
        );

        return $result;
    }

    private function getCombinationAttributes($id_combination)
    {
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT al.*, a.id_attribute_group
			FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac
			JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON (pac.id_attribute = al.id_attribute AND al.id_lang=' . (int)$this->id_lang . ')
			LEFT JOIN ' . _DB_PREFIX_ . 'attribute a ON (a.id_attribute = al.id_attribute)
			WHERE pac.id_product_attribute=' . (int)$id_combination . '
        ');
    }

    private function getCombinationAttributesAsString($id_combination)
    {
        return \Db::getInstance()->getValue("SELECT GROUP_CONCAT(al.name) FROM " . _DB_PREFIX_ . "product_attribute_combination as pac
                                                LEFT JOIN " . _DB_PREFIX_ . "attribute_lang as al
                                                ON (pac.id_attribute = al.id_attribute AND al.id_lang = '".(int)$this->id_lang."')
                                                WHERE pac.id_product_attribute = '".(int)$id_combination."'
                                                ");
    }

    private function getCombinationAttributeGroupsAsString($id_combination)
    {
        return \Db::getInstance()->getValue("SELECT GROUP_CONCAT(agl.name) FROM " . _DB_PREFIX_ . "product_attribute_combination as pac
                                                LEFT JOIN " . _DB_PREFIX_ . "attribute as a
                                                ON pac.id_attribute = a.id_attribute
                                                LEFT JOIN " . _DB_PREFIX_ . "attribute_group_lang as agl
                                                ON (a.id_attribute_group = agl.id_attribute_group AND agl.id_lang = '".(int)$this->id_lang."')
                                                WHERE pac.id_product_attribute = '".(int)$id_combination."'
                                                ");
    }

    private function getCombinationImages($cover = true)
    {
        if (!\Combination::isFeatureActive() || !$this->combination->id) {
            return false;
        }

        $result = \Db::getInstance()->executeS('
			SELECT pai.`id_image`,pai.`id_image` AS id, pai.`id_product_attribute`, il.`legend`
			FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai
			LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (il.`id_image` = pai.`id_image`)
			LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_image` = pai.`id_image`)
			WHERE pai.`id_product_attribute` = ' . (int)$this->combination->id . ' AND il.`id_lang` = ' . (int)$this->id_lang . ' ORDER BY i.`position`'
        );

        if (!$result) {
            return false;
        }

        if ($cover) {
            return $result[0];
        } else {
            return $result;
        }
    }

    private function getSpecificPriceProperty($specific_price_property, $export_field_id)
    {
        $specific_price_field_number = explode('_', $export_field_id);
        $specific_price_field_number = end($specific_price_field_number);
        $specific_prices = \SpecificPrice::getByProductId($this->product->id);

        if ($this->configuration['separate'] && isset($specific_prices[$specific_price_field_number - 1]['id_product_attribute'])) {
            if ($specific_prices[$specific_price_field_number - 1]['id_product_attribute'] != 0
                && $specific_prices[$specific_price_field_number - 1]['id_product_attribute'] != $this->combination->id
            ) {
                return '';
            }
        }

        if (isset($specific_prices[$specific_price_field_number - 1][$specific_price_property])) {
            $property_value = $specific_prices[$specific_price_field_number - 1][$specific_price_property];
            if ($specific_price_property == 'reduction') {
                return $this->getFormattedPrice($property_value,false);
            }

            if ($specific_price_property == 'price') {
                return $this->getFormattedPrice($property_value);
            }

            if ($specific_price_property == 'from' || $specific_price_property == 'to') {
                return $this->formatDateValue($property_value);
            }

            return $property_value;
        }
    }

    public function getFormattedPrice($price, $convert_to_currency = true)
    {
        $formatted_price = \Tools::ps_round($price, $this->configuration['round_value']);

        if ($convert_to_currency === true) {
            $formatted_price = \Tools::convertPrice($formatted_price, $this->configuration['currency'], true);
        }

        $formatted_price = number_format($formatted_price,
            $this->configuration['round_value'],
            $this->getDecimalSeparatorById($this->configuration['separator_decimal_points']),
            $this->getThousandsSeparatorById($this->configuration['thousands_separator'])
        );

        if ($this->apply_price_decoration) {
            $formatted_price = str_replace('[PRICE]', $formatted_price, $this->configuration['price_decoration']);
        }

        if ($this->configuration['format_file'] == 'gmf') {
            $currency_iso = $this->getCurrencyIsoCodeById($this->configuration['currency']);
            $formatted_price = $formatted_price . ' ' .  $currency_iso;
        }

        return $formatted_price;
    }

    private function getUnitPriceWithTax() {

        $unit_price_with_tax = $this->product->unit_price;
        $tax_rate = $this->getTaxRate();

        if ($tax_rate) {
            $unit_price_with_tax = $this->product->unit_price + ($this->product->unit_price * ($tax_rate / 100));
        }

        return $this->getFormattedPrice($unit_price_with_tax);
    }

    private function getFullProductName($id_product_attribute)
    {
        $query = new \DbQuery();

        $table_collation = $this->getAttributeGroupLangTableCollation();

        if ($id_product_attribute) {
            $query->select('IFNULL(CONCAT(pl.name COLLATE '.pSQL($table_collation).', \' : \', GROUP_CONCAT(DISTINCT agl.`name`, \' - \', al.name SEPARATOR \', \')),pl.name) as name');
        } else {
            $query->select('DISTINCT pl.name as name');
        }

        if ($id_product_attribute) {
            $query->from('product_attribute', 'pa');
            $query->join(\Shop::addSqlAssociation('product_attribute', 'pa'));
            $query->innerJoin('product_lang', 'pl',
                'pl.id_product = pa.id_product AND pl.id_lang = ' . (int)$this->id_lang . \Shop::addSqlRestrictionOnLang('pl', $this->id_shop));
            $query->leftJoin('product_attribute_combination', 'pac',
                'pac.id_product_attribute = pa.id_product_attribute');
            $query->leftJoin('attribute', 'atr', 'atr.id_attribute = pac.id_attribute');
            $query->leftJoin('attribute_lang', 'al',
                'al.id_attribute = atr.id_attribute AND al.id_lang = ' . (int)$this->id_lang);
            $query->leftJoin('attribute_group_lang', 'agl',
                'agl.id_attribute_group = atr.id_attribute_group AND agl.id_lang = ' . (int)$this->id_lang);
            $query->where('pa.id_product = ' . (int)$this->product->id . ' AND pa.id_product_attribute = ' . (int)$id_product_attribute);
        } else {
            $query->from('product_lang', 'pl');
            $query->where('pl.id_product = ' . (int)$this->product->id);
            $query->where('pl.id_lang = ' . (int)$this->id_lang . \Shop::addSqlRestrictionOnLang('pl', $this->id_shop));
        }

        return \Db::getInstance()->getValue($query);
    }

    private function getListOfPackItemsFields()
    {
        return [
            'pack_items_id',
            'pack_items_id_pack_product_attribute',
            'pack_items_reference',
            'pack_items_ean13',
            'pack_items_upc',
            'pack_items_name',
            'pack_items_quantity'
        ];
    }

    private function getPackItemsFieldProperty($full_field_name)
    {
        preg_match('/^pack_items_(.*)$/', $full_field_name, $matches);

        if (empty($matches)) {
            return false;
        }

        return $matches[count($matches) - 1];
    }

    private function getPackItemsPropertyValuesForExport($property_name)
    {
        $pack_items_property_values = '';
        $pack_items = \Pack::getItems($this->product->id, $this->id_lang);

        if (!empty($pack_items) && property_exists('Product', $property_name)) {
            if ($property_name == 'quantity') {
                $property_name = 'pack_quantity';
            }

            foreach ($pack_items as $pack_item) {
                $pack_items_property_values .= ($pack_item->$property_name) . ',';
            }
        }

        return trim($pack_items_property_values, ', ');
    }

    private function getCustomizationFieldsParameterValues($product_id, $customization_field_parameter)
    {
        $customization_fields = $this->getCustomizationFieldsByProductId($product_id);
        $customization_fields_value = '';

        if (!empty($customization_fields)) {
            foreach ($customization_fields as $customization_field) {
                $customization_fields_value .= ',' . $customization_field[$customization_field_parameter];
            }
        }

        return trim($customization_fields_value, ',');
    }

    private function getCustomizationFieldsByProductId($product_id)
    {
        $query = 'SELECT * 
        FROM `' . _DB_PREFIX_ . 'customization_field` cf
        LEFT JOIN `' . _DB_PREFIX_ . 'customization_field_lang` AS cfl
        ON cf.`id_customization_field` = cfl.`id_customization_field`
        WHERE cf.`id_product` = "' . (int)$product_id . '" 
        AND cfl.`id_lang` = "' . (int)$this->id_lang . '"';

        return \Db::getInstance()->executeS($query);
    }

    private function getAllProductCombinations()
    {
        $all_product_combinations = [];
        $all_product_combinations_ids = $this->product->getWsCombinations();

        foreach ($all_product_combinations_ids as $combination_id_wrapper) {
            $combination = new \Combination($combination_id_wrapper['id'], $this->id_lang, $this->id_shop);

            if (!empty($this->configuration['filters'])) {
                foreach ($this->configuration['filters'] as $filter) {
                    $filter['value'] = isset($filter['value']) ? $filter['value'] : [];

                    if (is_string($filter['value'])) {
                        if (PESerializationChecker::isStringSerialized($filter['value'])) {
                            $filter['value'] = Tools::unSerialize($filter['value']);
                        } else {
                            $filter['value'] = json_decode($filter['value'], true);
                        }
                    }

                    if (is_array($filter['value']) && (isset($filter['value']) || isset($filter['type'])) && $filter['field_type'] != 'date') {
                        if ($filter['value'] == '' && !in_array($filter['type'], ['empty', 'not_empty'])) {
                            continue;
                        }
                    }

                    if (empty($filter['value']) && $filter['value'] != 0) {
                        continue;
                    }

                    switch ($filter['field_type']) {
                        case 'string':
                        case 'number':
                            $filter_value = $filter['value']['value'];
                            break;
                        default:
                            $filter_value = $filter['value'];
                    }

                    if ($filter_value == '' && !in_array($filter['value']['type'], ['empty', 'not_empty'])) {
                        continue;
                    }

                    switch ($filter['field']) {
                        case 'quantity':
                            $filtered_value = StockAvailable::getQuantityAvailableByProduct($this->product->id, $combination->id, $this->id_shop);
                            break;
                        case 'combination_location':
                            $filter_value = $this->getCombinationLocationProperty($combination);

                            break;
                        case 'combination_reference':
                            $filtered_value = $combination->reference;
                            break;
                        case 'combination_price_impact':
                            $filtered_value = $combination->price;
                            break;
                        case 'combination_unit_price_impact':
                            $filtered_value = $combination->unit_price_impact;
                            break;
                        case 'combination_wholesale_price':
                            $filtered_value = $combination->wholesale_price;
                            break;
                        case 'combination_ean':
                            $filtered_value = $combination->ean13;
                            break;
                        case 'combination_upc':
                            $filtered_value = $combination->upc;
                            break;
                        case 'combination_isbn':
                            $filtered_value = $combination->isbn;
                            break;
                        case 'combination_ecotax':
                            $filtered_value = $combination->ecotax;
                            break;
                        case 'combination_weight_impact':
                            $filtered_value = $combination->weight;
                            break;
                        case 'attribute_group':
                            $filtered_value = $this->getCombinationAttributeGroupsAsString($combination->id);
                            break;
                        case 'attribute':
                            $filtered_value = $this->getCombinationAttributesAsString($combination->id);
                            break;
                        default:
                            $filtered_value = false;
                    }

                    if ($filtered_value === false) {
                        continue;
                    }

                    $is_passed_filter = PEProductFilter::filter($filtered_value, $filter);

                    if (!$is_passed_filter) {
                        continue 2;
                    }
                }
            }

            $all_product_combinations[$combination->id] = $combination;
        }

        return $all_product_combinations;
    }

    private function getProductSupplierPropertyForExport($supplier_property, $supplier_id)
    {
        if ($this->configuration['separate'] || empty($this->all_combinations)) {
            $product_supplier = $this->getProductSupplierData($supplier_id, $this->combination->id);
            $valid_supplier_property_name = $this->getValidSupplierPropertyNameByFieldName($supplier_property);

            return isset($product_supplier[$valid_supplier_property_name]) ? $product_supplier[$valid_supplier_property_name] : '';
        }

        $supplier_ready_for_export = [];
        foreach ($this->all_combinations as $combination) {
            $product_supplier = $this->getProductSupplierData($supplier_id, $combination->id);
            $valid_supplier_property_name = $this->getValidSupplierPropertyNameByFieldName($supplier_property);

            if (!empty($product_supplier[$valid_supplier_property_name])) {
                $supplier_property_value = $product_supplier[$valid_supplier_property_name];
                if ($supplier_property == 'price') {
                    $supplier_property_value = $this->getFormattedPrice($supplier_property_value, false);
                }

                $supplier_ready_for_export[] = $supplier_property_value;
            }
        }

        return implode(',', $supplier_ready_for_export);
    }

    private function getValidSupplierPropertyNameByFieldName($export_field_property_name)
    {
        switch ($export_field_property_name) {
            case 'reference':
                return 'product_supplier_reference';
            case 'price':
                return 'product_supplier_price_te';
            case 'currency':
                return 'id_currency';
            case 'id':
                return 'id_supplier';
            default:
                return $export_field_property_name;
        }
    }

    private function getProductSupplierData($id_supplier, $id_product_attribute = 0)
    {
        $result = \Db::getInstance()->executeS(
            "SELECT s.id_supplier, 
                  s.name, 
                  ps.product_supplier_reference, 
                  ps.product_supplier_price_te, 
                  ps.id_currency
            FROM " . _DB_PREFIX_ . "product_supplier as ps
            LEFT JOIN " . _DB_PREFIX_ . "supplier as s
            ON ps.id_supplier = s.id_supplier
            WHERE ps.id_supplier = " . (int)$id_supplier . "
            AND ps.id_product = " . (int)$this->product->id . "
            AND ps.id_product_attribute = " . (int)$id_product_attribute
        );

        return !empty($result) ? $result[0] : false;
    }

    private function getImageType()
    {
        if (empty($this->configuration['image_type'])) {
            $all_image_types = \ImageType::getImagesTypes('products');
            foreach ($all_image_types as $type) {
                if ($type['height'] > 150) {
                    return $type['name'];
                }
            }
        }

        if ($this->configuration['image_type'] == 'original_size') {
            return false;
        }

        if (\Validate::isImageTypeName($this->configuration['image_type'])) {
            return $this->configuration['image_type'];
        }

        return false;
    }

    private function isProductInShop($id_product)
    {
        return \Db::getInstance()->getValue("SELECT `id_product` FROM `" . _DB_PREFIX_ . "product_shop`
                                                WHERE `id_product` = '".(int)$id_product."'
                                                AND `id_shop` = '".(int)$this->id_shop."'");
    }

    private function getProductShopId($id_product)
    {
        return \Db::getInstance()->getValue("SELECT `id_shop` FROM `" . _DB_PREFIX_ . "product_shop`
                                                WHERE `id_product` = '".(int)$id_product."'");
    }

    private function formatDateValue($date_value)
    {
        if (empty($date_value) || $date_value == '0000-00-00 00:00:00' || $date_value == '0000-00-00') {
            return '';
        }

        return date($this->configuration['date_format'], strtotime($date_value));
    }

    private function getDecimalSeparatorById($separator_id)
    {
        switch ($separator_id) {
            case 2:
                return ',';
            default:
                return '.';
        }
    }

    private function getThousandsSeparatorById($separator_id)
    {
        switch ($separator_id) {
            case 1:
                return ' ';
            case 2:
                return '.';
            case 3:
                return ',';
            default:
                return '';
        }
    }

    private function getAttributeGroupLangTableCollation()
    {
        $collation = 'utf8_general_ci';
        $attribute_group_lang_table_info = \Db::getInstance()->executeS("SHOW TABLE STATUS WHERE name = '" . _DB_PREFIX_ . "attribute_group_lang'");

        if (!empty($attribute_group_lang_table_info)) {
            $collation = $attribute_group_lang_table_info[0]['Collation'];
        }

        return $collation;
    }

    private function getCurrencyIsoCodeById($id, $forceRefreshCache = false)
    {
        $cacheId = 'Currency::getIsoCodeById' . pSQL($id);
        if ($forceRefreshCache || !Cache::isStored($cacheId)) {
            $resultIsoCode = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `iso_code` FROM ' . _DB_PREFIX_ . 'currency WHERE `id_currency` = ' . (int) $id);
            Cache::store($cacheId, $resultIsoCode);

            return $resultIsoCode;
        }

        return Cache::retrieve($cacheId);
    }

    private function getProductImageIds()
    {
        $additional_join = '';
        $additional_where = '';

        if ($this->combination->id && $this->configuration['separate'] && $this->isCombinationHasImagesLinked($this->combination->id)) {
            $additional_join = ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_image` as pai ';
            $additional_join .= ' ON i.`id_image` = pai.`id_image`';
            $additional_where = ' AND pai.`id_product_attribute` = ' . (int)$this->combination->id;
        }

        $query = '
            SELECT DISTINCT(i.`id_image`) as id
            FROM `' . _DB_PREFIX_ . 'image` as i
            LEFT JOIN `'._DB_PREFIX_.'image_shop` as image_shop
            ON i.`id_image` = image_shop.`id_image` AND image_shop.`id_shop` = '.(int)$this->id_shop.'
            '.$additional_join.'
            WHERE i.`id_product` = ' . (int) $this->product->id . '
            '.$additional_where.'
            ORDER BY i.`position`';

        return Db::getInstance()->executeS($query);
    }

    private function isCombinationHasImagesLinked($id_combination)
    {
        if (!$id_combination) {
            return false;
        }

        return Db::getInstance()->getValue("SELECT `id_product_attribute` FROM `" . _DB_PREFIX_ . "product_attribute_image` 
                                            WHERE `id_product_attribute` = '".(int)$id_combination."'
                                            AND `id_image` != '0'");
    }
}