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

require_once _PS_MODULE_DIR_ . 'exportproducts/src/product/PEProductFilter.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/configuration/PEConfigurationField.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PECategoryTreeGenerator.php';

class PEFieldSplitter
{
    private $configuration;
    private $product_filter;

    public function __construct($configuration, PEProductFilter $product_filter)
    {
        $this->configuration = $configuration;
        $this->product_filter = $product_filter;
    }

    public function splitFieldsForExportInSeparateColumns()
    {
        if (!empty(PEConfigurationField::getSelectedSpecificPriceFields($this->configuration['fields']))) {
            $this->configuration['fields'] = PEConfigurationField::splitSpecificPriceFields(
                $this->configuration['fields'],
                $this->product_filter->getMaxNumberOfSpecificPrices()
            );
        }

        if (!empty(PEConfigurationField::getSelectedSupplierFields($this->configuration['fields']))) {
            $this->configuration['fields'] = PEConfigurationField::splitSupplierFields(
                $this->configuration['fields'],
                $this->product_filter->getExportProductIdsAsString()
            );
        }

        if (isset($this->configuration['fields']['separated_categories'])) {
            $category_trees_generator = new PECategoryTreeGenerator($this->configuration['id_lang']);

            $products_ids = $this->product_filter->getProductIdsForCategoriesExport();
            $category_trees_generator->setMaxNumberOfTreesInOneProduct($products_ids);

            $this->configuration['fields'] = PEConfigurationField::splitCategoryTreeFields(
                $this->configuration['id_configuration'],
                $this->configuration['fields'],
                $category_trees_generator->getMaxNumberOfTreesInOneProduct()
            );
        }

        if (isset($this->configuration['fields']['combinations_value'])) {
            $this->configuration['fields'] = PEConfigurationField::splitAttributeGroupFields(
                $this->configuration['id_configuration'],
                $this->configuration['fields'],
                $this->product_filter->getAttributeGroupIds(),
                $this->configuration['id_lang'],
                $this->configuration['id_shop']
            );
        }

        if (isset($this->configuration['fields']['images_value'])) {
            $this->configuration['fields'] = PEConfigurationField::splitImageUrlFields(
                $this->configuration['id_configuration'],
                $this->configuration['fields'],
                $this->product_filter->getMaxNumberOfImages()
            );
        }

        return $this->configuration['fields'];
    }
}