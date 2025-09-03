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

require_once _PS_MODULE_DIR_ . 'exportproducts/src/PEExportDataRepository.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/product/PEProductFilter.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/product/PEExportedProduct.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PESpreadsheetManager.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PEFTPManager.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PEFieldSplitter.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PEXMLWriter.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PEGMFWriter.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PELogger.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/exception/PEExportProcessException.php';

class PEExporter
{
    private $configuration;
    private $task;
    private $num_of_products_for_export;
    private $iteration;
    private $max_products_per_iteration;
    private $is_first_iteration;
    private $product_filter;
    private $export_process;
    private $spreadsheet_manager;
    private $ftp_manager;
    private $export_file;
    private $export_data_repository;

    public function __construct(PEExportProcess $export_process, $iteration, $configuration, $task = false)
    {
        $this->iteration = $iteration;
        $this->is_first_iteration = !$this->iteration;
        $this->configuration = $configuration;
        $this->max_products_per_iteration = $this->configuration['products_per_iteration'];
        $this->task = $task;
        $this->export_process = $export_process;
        $this->product_filter = new PEProductFilter($this->configuration, $task);

        $field_splitter = new PEFieldSplitter($this->configuration, $this->product_filter);
        $this->configuration['fields'] = $field_splitter->splitFieldsForExportInSeparateColumns();

        $this->export_data_repository = new PEExportDataRepository($this->configuration, $this->iteration);
        $this->spreadsheet_manager = new PESpreadsheetManager($this->configuration);
        $this->ftp_manager = new PEFTPManager($this->configuration);
    }

    public function export()
    {
        if ($this->export_process->getStatus() == PEExportProcess::STATUS_STOPPED) {
            $this->export_data_repository->clear();
            return false;
        }

        $this->export_process->updateStatus(PEExportProcess::STATUS_ACTIVE);
        \Configuration::updateGlobalValue('MPM_EXPORTPRODUCTS_ACTIVE_PROCESS_ID', $this->export_process->id);

        $is_first_iteration = !$this->iteration;

        if ($is_first_iteration) {
            PELogger::clearErrorLog();
            $this->export_data_repository->clear();
            $this->num_of_products_for_export = $this->product_filter->getTotalNumberOfProductsForExport();

            if (!$this->num_of_products_for_export) {
                $this->export_process->updateStatus(PEExportProcess::STATUS_NO_PRODUCT);

                throw new PEExportProcessException($this->export_process->id, Module::getInstanceByName('exportproducts')->l('There is no products for export!',__CLASS__));
            }

            $this->export_process->updateNumOfProductsToExport($this->num_of_products_for_export);
        } else {
            $this->num_of_products_for_export = $this->export_process->getNumberOfProductsForExport();
        }

        $ids_of_products_for_export = $this->product_filter->getExportProductIds($this->iteration, $this->max_products_per_iteration);

        if ($ids_of_products_for_export) {
            $this->export_data_repository->saveExportProductsToRepository($this->export_process, $ids_of_products_for_export);

            return [
                'status'            => 'need_to_run_next_iteration',
                'next_iteration'    => $this->iteration + 1,
                'id_export_process' => $this->export_process->id
            ];
        }

        $this->export_file = new PEExportFile($this->export_process, $this->configuration);
        $this->export_process->updateStatus(PEExportProcess::STATUS_SAVING);

        if ($this->configuration['format_file'] == 'xml') {
            $this->writeFromRepositoryToXMLFile();
        } elseif ($this->configuration['format_file'] == 'gmf') {
            $this->writeFromRepositoryToGMFFile();
        } else {
            $this->writeFromRepositoryToExcelCsvFile();
        }

        //make export file with unique name for history
        copy($this->export_file->getServerPathToFile(), $this->export_file->getServerPathToFile(true));

        if ($this->configuration['feed_target'] == 'ftp') {
            $this->ftp_manager->copyFileToServer($this->export_file);
        }

        $this->updateFinishedProcessData();

        if (Configuration::getGlobalValue('MPM_PRODUCT_EXPORT_DEBUG_MODE')) {
            //This debug test is not active by default.
            print_r($this->export_file->getLinkToExportedFile());die;
        }

        return [
            'status' => 'finished',
            'exported_file_link' => $this->export_file->getLinkToExportedFile()
        ];
    }

    private function updateFinishedProcessData()
    {
        $this->export_process->updateFilePath($this->export_file->getLinkToExportedFile());
        $this->export_process->updateDownloadFilePath($this->export_file->getLinkToExportedFile(true));
        $this->export_process->updateStatus(PEExportProcess::STATUS_FINISHED);
        $this->export_process->updateFinishTime(date(PEExportProcess::DATE_FORMAT));

        if (!empty($this->task) && !empty($this->task['export_not_exported'])) {
            $exported_products_ids = $this->product_filter->getExportProductIds(0, 100000);
            PEExportedProduct::saveExportedProductIdsToDb($exported_products_ids, $this->task['id_task']);
        }
    }

    private function writeFromRepositoryToExcelCsvFile()
    {
        $this->spreadsheet_manager->writeHeaderData();
        $current_file_row = $this->configuration['display_header'] ? 2 : 1;

        $num_of_products_for_export = $this->export_data_repository->getNumOfProductsInRepository();

        for ($row = 1; $row <= $num_of_products_for_export; $row++) {
            if ($this->export_process->getStatus() == PEExportProcess::STATUS_STOPPED) {
                $this->export_data_repository->clear();
                return false;
            }

            $product = $this->export_data_repository->getProductByRowNumber($row);
            $this->spreadsheet_manager->writeProductDataToFile($product, $current_file_row);

            $current_file_row++;
        }

        if ($this->configuration['style_spreadsheet']) {
            $this->spreadsheet_manager->setStyle($current_file_row);
        }

        $this->spreadsheet_manager->save($this->export_file->getServerPathToFile());

        return true;
    }

    private function writeFromRepositoryToXMLFile()
    {
        $num_of_products_for_export = $this->export_data_repository->getNumOfProductsInRepository();

        $xml_writer = new PEXMLWriter($this->export_file->getServerPathToFile());
        $xml_writer->openTag(Module::getInstanceByName('exportproducts')->l('Products',__CLASS__));

        for ($row = 1; $row <= $num_of_products_for_export; $row++) {
            if ($this->export_process->getStatus() == PEExportProcess::STATUS_STOPPED) {
                $this->export_data_repository->clear();
                return false;
            }

            $xml_writer->openTag(Module::getInstanceByName('exportproducts')->l('Product',__CLASS__), PHP_EOL,1);

            $product = $this->export_data_repository->getProductByRowNumber($row);

            foreach ($this->configuration['fields'] as $field_id => $field) {
                if (empty($product[$field_id]) && strpos($field_id, 'feature') !== false) {
                    continue;
                }

                $xml_writer->openTag($field['name'], '',2);

                if (!empty($product[$field_id])) {
                    $xml_writer->writeTagValue($product[$field_id]);
                }

                $xml_writer->closeTag($field['name']);
            }

            $xml_writer->closeTag(Module::getInstanceByName('exportproducts')->l('Product',__CLASS__), PHP_EOL, 1);
        }

        $xml_writer->closeTag(Module::getInstanceByName('exportproducts')->l('Products',__CLASS__));
        $xml_writer->closeFile();

        return true;
    }

    private function writeFromRepositoryToGMFFile()
    {
        $num_of_products_for_export = $this->export_data_repository->getNumOfProductsInRepository();

        $xml_writer = new PEGMFWriter($this->export_file->getServerPathToFile());

        for ($row = 1; $row <= $num_of_products_for_export; $row++) {
            if ($this->export_process->getStatus() == PEExportProcess::STATUS_STOPPED) {
                $this->export_data_repository->clear();
                return false;
            }

            $xml_writer->openTag('item', PHP_EOL,1);

            $product = $this->export_data_repository->getProductByRowNumber($row);
            $added_gmf_ids = [];

            $price_value = false;
            $sale_price_value = false;
            foreach ($this->configuration['fields'] as $field_id => $field) {
                if (empty($product[$field_id]) || empty($field['gmf_id'])) {
                    continue;
                }

                if ($field['gmf_id'] == 'price') {
                    $price_value = $product[$field_id];
                }

                if ($field['gmf_id'] == 'sale_price') {
                    $sale_price_value = $product[$field_id];
                }
            }

            foreach ($this->configuration['fields'] as $field_id => $field) {
                if (empty($product[$field_id]) || empty($field['gmf_id'])) {
                    continue;
                }

                if ($field['gmf_id'] == 'sale_price' && $price_value == $sale_price_value) {
                    continue;
                }

                if ($field_id == 'id_product' || $field['gmf_id'] == 'id|item_group_id') {
                   if (!empty($product['id_product_attribute'])) {
                       $field['gmf_id'] = 'item_group_id';
                   } else {
                       $field['gmf_id'] = 'id';
                   }
                } else if ($field_id == 'reference') {
                    if (!empty($product['id_product_attribute']) && !in_array('item_group_id', $added_gmf_ids)) {
                        $field['gmf_id'] = 'item_group_id';
                    } else if (!in_array('id', $added_gmf_ids)) {
                        $field['gmf_id'] = 'id';
                    } else {
                        continue;
                    }
                }

                if (in_array($field['gmf_id'], $added_gmf_ids) && $field['gmf_id'] != 'additional_image_link') {
                    continue;
                }

                $xml_writer->openTag($field['gmf_id'], '',2, true);
                $xml_writer->writeTagValue($product[$field_id]);
                $xml_writer->closeTag($field['gmf_id'], PHP_EOL, 0, true);

                $added_gmf_ids[] = $field['gmf_id'];
            }

            $xml_writer->closeTag('item', PHP_EOL, 1);
        }

        $xml_writer->closeFile();

        return true;
    }
}