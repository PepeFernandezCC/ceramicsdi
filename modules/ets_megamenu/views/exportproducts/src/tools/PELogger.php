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

class PELogger
{
    private static $error_log_file_path = _PS_MODULE_DIR_ . 'exportproducts/error.log';
    private static $log_file_path = _PS_MODULE_DIR_ . 'exportproducts/log.log';

    public static function logError($error)
    {
        $log_entry = self::getCurrentDate() . '   ' . $error . PHP_EOL;
        file_put_contents(self::$error_log_file_path, $log_entry, FILE_APPEND);
    }

    public static function log($entry)
    {
        $log_entry = self::getCurrentDate() . '   ' . $entry . PHP_EOL;
        file_put_contents(self::$log_file_path, $log_entry, FILE_APPEND);
    }

    public static function clearErrorLog()
    {
        file_put_contents(self::$error_log_file_path, '');
    }

    public static function clearLog()
    {
        file_put_contents(self::$log_file_path, '');
    }

    private static function getCurrentDate()
    {
        $date_time = new \DateTime("now", new \DateTimeZone('Europe/Kiev'));
        $date_time->setTimestamp(time());
        return $date_time->format('d-m-Y H:i:s');
    }
}