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

require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PELogger.php';

class PEExportProcessException extends \Exception
{
    private $id_export_process;

    public function __construct($id_export_process, $message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);

        $this->id_export_process = $id_export_process;
    }

    public function getFullMessage()
    {
        $message = [];
        $message[] =  $this->getMessage();
        $message[] = Module::getInstanceByName('exportproducts')->l('File: ' . $this->getFile());
        $message[] = Module::getInstanceByName('exportproducts')->l('Line: ' . $this->getLine());

        $message_for_log = implode(' | ', $message);
        $message_for_template = $this->getMessage();

        PELogger::logError($message_for_log);

        return $message_for_template;
    }

    public function getExportProcessId()
    {
        return $this->id_export_process;
    }
}