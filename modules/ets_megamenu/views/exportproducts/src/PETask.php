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
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PECronExpression.php';

class PETask
{
    const TABLE_NAME = 'pe_task';
    const TABLE_NAME_WITH_PREFIX =  _DB_PREFIX_ . self::TABLE_NAME;

    public static function createTableInDb()
    {
        self::dropTableFromDb();

        $query = "CREATE TABLE IF NOT EXISTS `" . self::TABLE_NAME_WITH_PREFIX . "` (
                `id_task` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_configuration` INT(11) UNSIGNED NOT NULL,
                `description` VARCHAR(255) NOT NULL,
                `frequency` VARCHAR(45) NOT NULL,
                `export_not_exported` INT(11) NOT NULL,
                `email_message` INT(11) NOT NULL,
                `export_emails` VARCHAR(256) NOT NULL,
                `one_shot` INT(11) NOT NULL,
                `is_one_shot_executed` INT(11) NULL,
                `attach_file` INT(11) NOT NULL,
                `active` INT(1) NOT NULL,
                PRIMARY KEY (`id_task`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8";

        return \Db::getInstance()->execute($query);
    }

    public static function dropTableFromDb()
    {
        return \Db::getInstance()->execute("DROP TABLE IF EXISTS `" . self::TABLE_NAME_WITH_PREFIX . "`");
    }

    public static function getForm($id_task)
    {
        $tpl = \Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/form_task.tpl');
        $settings_scheduled_tasks = [];

        if ($id_task) {
            $settings_scheduled_tasks = PETask::getById($id_task);
        }

        if (!empty($settings_scheduled_tasks['frequency'])) {
            $cron_expression = (new PECronExpression($settings_scheduled_tasks['frequency']))->get();
        } else {
            $cron_expression = PECronExpression::DEFAULT_EXPRESSION;
        }

        $tpl->assign([
            'id_task'                 => $id_task,
            'frequency'               => $cron_expression,
            'settings_scheduled_tasks' => $settings_scheduled_tasks,
            'settings'                => PEConfigurationCore::getSavedConfigurations(),
            'path_tpl'                => _PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/',
        ]);

        return $tpl->fetch();
    }

    public static function runAllScheduledTasks()
    {
        $tasks = self::getAllActiveTasks();

        if (empty($tasks)) {
            return false;
        }

        foreach ($tasks as $task) {
            if (PETask::isShouldBeExecuted($task) == true) {
                $_POST['id_task'] = $task['id_task'];

                if ($task['one_shot']) {
                    self::setOneShotIsExecuted($task['id_task'], true);
                }

                $export_process = new PEExportProcess(null, true);
                $export_process->run(0);
            }
        }

        return true;
    }

    public static function getAllActiveTasks()
    {
        $query = "SELECT * FROM " . self::TABLE_NAME_WITH_PREFIX . "
                  WHERE active = '1'";

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    public static function getById($id_task)
    {
        $query = "SELECT t.*, s.name 
                  FROM " . self::TABLE_NAME_WITH_PREFIX . "  AS t
                  LEFT JOIN " . PEConfigurationCore::TABLE_NAME_WITH_PREFIX . " AS s
                  ON s.id_configuration = t.id_configuration
                  WHERE t.id_task=" . (int)$id_task;

        $task = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if (empty($task[0])) {
            return [];
        }

        $task = $task[0];

        $cron_expression = new PECronExpression($task['frequency']);
        $task['next_run'] = date(\Context::getContext()->language->date_format_full, $cron_expression->getNextRunTime());

        return $task;
    }

    public static function getAllTasks()
    {
        $query = "SELECT t.*, c.name 
                  FROM " . self::TABLE_NAME_WITH_PREFIX . "  AS t
                  LEFT JOIN " . PEConfigurationCore::TABLE_NAME_WITH_PREFIX . " AS c
                  ON c.id_configuration = t.id_configuration
                  WHERE 1";

        $tasks = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if (empty($tasks)) {
            return [];
        }

        foreach ($tasks as &$task) {
            $cron_expression = new PECronExpression($task['frequency']);
            $next_run = date(PEExportProcess::DATE_FORMAT, $cron_expression->getNextRunTime());
            $task['next_run'] = $next_run;

            $last_task_export_process = PEExportProcess::getLastTaskProcess($task['id_task']);

            if ($last_task_export_process) {
                $task = array_merge($task, $last_task_export_process);
            }
        }

        return $tasks;
    }

    public static function save($task)
    {
        self::validate($task);

        $id_task = $task['id_task'];
        unset($task['id_task']);

        $task['active'] = 1;

        if ($id_task) {
            if (!$task['one_shot']) {
                $task['is_one_shot_executed'] = false;
            }

            \Db::getInstance(_PS_USE_SQL_SLAVE_)->update(self::TABLE_NAME, $task, 'id_task=' . (int)$id_task);
        } else {
            \Db::getInstance(_PS_USE_SQL_SLAVE_)->insert(self::TABLE_NAME, $task);
        }

        return true;
    }

    public static function deleteTask($id_task)
    {
        \Db::getInstance(_PS_USE_SQL_SLAVE_)->delete(self::TABLE_NAME, 'id_task=' . (int)$id_task);
    }

    public static function validate($values)
    {
        $cron_expression = new PECronExpression($values['frequency']);
        if (!$cron_expression->isValid()) {
            throw new \Exception(Module::getInstanceByName('exportproducts')->l('Please enter task frequency!',__CLASS__));
        }

        if (!$values['id_configuration']) {
            throw new \Exception(Module::getInstanceByName('exportproducts')->l('Please select export configuration!',__CLASS__));
        }

        if ($values['email_message'] && !$values['export_emails']) {
            throw new \Exception(Module::getInstanceByName('exportproducts')->l('Please enter email address!',__CLASS__));
        }

        if ($values['email_message'] && $values['export_emails']) {
            $emails = explode("\n", trim($values['export_emails']));
            foreach ($emails as $email) {
                $is_valid_email = \Validate::isEmail($email);

                if (!$is_valid_email) {
                    throw new \Exception(Module::getInstanceByName('exportproducts')->l('Email - ') . $email . Module::getInstanceByName('exportproducts')->l(' is not valid!',__CLASS__));
                }
            }
        }

        return true;
    }

    public static function getNumberOfTasks()
    {
        $query = "SELECT COUNT(id_task) as count_task
                  FROM " . self::TABLE_NAME_WITH_PREFIX . "
                  WHERE 1";

        $number_of_tasks = \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

        return $number_of_tasks ?: 0;
    }

    public static function getUrl()
    {
        $token = \Configuration::getGlobalValue('MPM_PRODUCT_EXPORT_TASKS_KEY');
        $admin_folder = str_replace(_PS_ROOT_DIR_ . '/', null, basename(_PS_ADMIN_DIR_));

        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            $path = \Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . $admin_folder . '/';
            $schedule_url = $path . \Context::getContext()->link->getAdminLink('AdminProductsExport', false);
            $schedule_url .= '&is_automatic=true&secure_key=' . $token;
        } else {
            $schedule_url = \Context::getContext()->link->getAdminLink('AdminProductsExport', false);
            $schedule_url .= '&is_automatic=true&secure_key=' . $token;
        }

        return $schedule_url;
    }

    public static function changeStatus($id_task, $status)
    {
        return \Db::getInstance()->update(self::TABLE_NAME, ['active' => (int)$status], 'id_task=' . (int)$id_task);
    }

    public static function setOneShotIsExecuted($id_task, $is_one_shot_executed)
    {
        return \Db::getInstance()->update(self::TABLE_NAME, ['is_one_shot_executed' => (int)$is_one_shot_executed], 'id_task=' . (int)$id_task);
    }

    public static function isOneShotExecuted($id_task)
    {
        $query = "SELECT `is_one_shot_executed` FROM " . self::TABLE_NAME_WITH_PREFIX . "
                  WHERE `id_task` = '".(int)$id_task."'";

        return \Db::getInstance()->getValue($query);
    }

    public static function isShouldBeExecuted($task)
    {
        if ($task['one_shot'] && self::isOneShotExecuted($task['id_task'])) {
            return false;
        }

        $cron_expression = new PECronExpression($task['frequency']);
        $next_run = $cron_expression->getNextRunTime();
        $now = time();

        if (($next_run - $now) <= 30) {
            return true;
        }

        return false;
    }

    public static function getTasksList()
    {
        $tpl = \Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/task-list.tpl');

        $tpl->assign([
            'scheduled_tasks' => PETask::getAllTasks(),
            'schedule_url'   => PETask::getUrl(),
            'img_folder'     => _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'modules/exportproducts/views/img/',
            'path_tpl'       => _PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/',
        ]);

        return $tpl->fetch();
    }
}