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

require_once _PS_MODULE_DIR_ . 'exportproducts/src/configuration/PEConfigurationCore.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/PEExportFile.php';

class PEMailer
{
    public static function sendEmail($task, $export_process, $error = false)
    {
        if (!$task['email_message'] || !$task['export_emails']) {
            return false;
        }

        $emails = $task['export_emails'];
        $emails = trim($emails);
        $emails = explode("\n", $emails);

        $id_configuration = $export_process->getConfigurationId();
        $configuration = PEConfigurationCore::getById($id_configuration);
        $export_file = new PEExportFile($export_process, $configuration);

        $export_data = [
            'name' => $configuration['name'],
            'progress' => $export_process->getProgress(),
            'num_of_products' => $export_process->getNumberOfProductsForExport(),
            'start' => $export_process::getStartTime($export_process->id),
            'finish' => $export_process::getFinishTime($export_process->id),
            'time' => $export_process::getExportTime($export_process->id),
            'file_path' => $export_process->getFilePath()
        ];

        $attachment = '';

        if ($task['attach_file'] && !$error) {
            $attachment = [
                'name' => $export_file->getName(),
                'mime' => $export_file->getMimeType(),
                'content' => \Tools::file_get_contents($export_file->getServerPathToFile())
            ];
        }

        $preheader = Module::getInstanceByName('exportproducts')->l('Product Export Process Completed!',__CLASS__);
        $successClass = 'success';
        $errorClass = '';
        $template_path = _PS_MODULE_DIR_ . 'exportproducts/mails/';

        if ($error) {
            $successClass = '';
            $errorClass = 'error';
            $preheader = Module::getInstanceByName('exportproducts')->l('Product Export Process Failed!') . ' ' . Module::getInstanceByName('exportproducts')->l('Error: ',__CLASS__) . $error;
        }

        $vars = [
            '{settings_name}'   => $export_data['name'],
            '{start_time}'      => $export_data['start'],
            '{exported_item}'   => (int)$export_data['progress'] . ' of ' . $export_data['num_of_products'],
            '{export_duration}' => $export_data['time'],
            '{error_class}'     => $errorClass,
            '{success_class}'   => $successClass,
            '{module_folder}'   => _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'modules/exportproducts/',
            '{error_message}'   => $error,
            '{preheader}'       => $preheader
        ];

        foreach ($emails as $users_email) {
            $users_email = trim($users_email);
            $mail = \Mail::Send(
                \Configuration::get('PS_LANG_DEFAULT'),
                'notification',
                'Product Export Report',
                $vars,
                "$users_email",
                null,
                null,
                null,
                $attachment,
                null,
                $template_path);

            if (!$mail) {
                echo Module::getInstanceByName('exportproducts')->l('Can not send email! Please contact us!',__CLASS__);
                die;
            }
        }

        return true;
    }
}