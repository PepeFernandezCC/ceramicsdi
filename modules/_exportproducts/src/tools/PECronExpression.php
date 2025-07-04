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

require_once(_PS_MODULE_DIR_ . 'exportproducts/libraries/Schedule/CrontabValidator.php');
require_once(_PS_MODULE_DIR_ . 'exportproducts/libraries/Schedule/CronSchedule.php');
require_once(_PS_MODULE_DIR_ . 'exportproducts/libraries/Schedule/csd_parser.php');

class PECronExpression
{
    private $expression;

    const DEFAULT_EXPRESSION = '0 * * * *';

    public function __construct($expression)
    {
        $this->expression = trim($expression);
        $this->expression = preg_replace('#\s+#', ' ', $expression);
        $this->expression = $this->decode($expression);
    }

    public function get()
    {
        return $this->expression;
    }

    public function isValid()
    {
        $validator = new \CrontabValidator();
        return $validator->isExpressionValid($this->expression);
    }

    public function getNextRunTime()
    {
        $parser = new \csd_parser($this->expression);
        return $parser->get();
    }

    public function explainToHumans()
    {
        $expression = $this->expression . ' *';
        $schedule = \CronSchedule::fromCronString($expression);
        return $schedule->asNaturalLanguage();
    }

    private function decode($encoded_expression)
    {
        switch ($encoded_expression) {
            case "@yearly":
            case "@annually":
                return  "0 0 1 1 *";
            case "@monthly":
                return  "0 0 1 * *";
            case "@weekly":
                return  "0 0 * * 0";
            case "@daily":
            case "@midnight":
                return "0 0 * * *";
            case "@hourly":
                return "0 * * * *";
            default:
                return $encoded_expression;
        }
    }
}