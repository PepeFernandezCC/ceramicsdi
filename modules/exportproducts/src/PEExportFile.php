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

class PEExportFile
{
    private $export_process;
    private $configuration;
    private $name;
    private $name_with_process_id;

    public function __construct(PEExportProcess $export_process, $configuration)
    {
        $this->export_process = $export_process;
        $this->configuration = $configuration;

        if ($this->configuration['format_file'] == 'gmf') {
            $this->configuration['format_file'] = 'xml';
        }

        $this->name = $this->getName();
        $this->name_with_process_id = $this->getName(true);
    }

    public function getLinkToExportedFile($name_with_process_id = false)
    {
        if ($name_with_process_id) {
            return \Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/exportproducts/files/' . $this->name_with_process_id;
        }

        return \Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/exportproducts/files/' . $this->name;
    }

    public function getServerPathToFile($name_with_process_id = false)
    {
        if ($name_with_process_id) {
            return _PS_MODULE_DIR_ . 'exportproducts/files/' . $this->name_with_process_id;
        }

        return _PS_MODULE_DIR_ . 'exportproducts/files/' . $this->name;
    }

    public function getName($name_with_process_id = false)
    {
        $file_name = $this->constructName();

        if ($name_with_process_id) {
            return $file_name . '_' . $this->export_process->id . '.' . $this->configuration['format_file'];
        }

        return $file_name . '.' . $this->configuration['format_file'];
    }

    public function getMimeType()
    {
        switch ($this->configuration['format_file']) {
            case 'xml':
                return 'application/xml';
            case 'csv':
                return 'application/csv';
            case 'xlsx':
                return 'application/vnd.ms-excel';
            default:
                return '';
        }
    }

    private function constructName()
    {
        $file_name = $this->configuration['file_name'];

        if (!$file_name) {
            $file_name = 'exported_product_{d-m-Y H:i:s}';
        }

        $date_from_file_name = self::getDateFromFileName($file_name);

        if (!$date_from_file_name) {
            return $file_name;
        }

        return preg_replace('/\{[\s\S]+\}/i', $date_from_file_name, $file_name);
    }

    private function getDateFromFileName($file_name)
    {
        if (!$file_name) {
            return false;
        }

        preg_match('/\{([\s\S]+)\}/i', $file_name, $matches);

        if (empty($matches) || !isset($matches[1])) {
            return false;
        }

        $date_pattern = $matches[1];
        $export_start_time = $this->export_process->getStartTime($this->export_process->id);
        $date = date($date_pattern, strtotime($export_start_time));

        if (!$date) {
            return false;
        }

        return $date;
    }
}