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
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PEMailer.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/configuration/PEConfigurationCore.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/exception/PEExportProcessException.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/PEExporter.php';

class PEExportProcess
{
    public $id;
    public $task;
    public $configuration;

    const TABLE_NAME = 'pe_export_process';
    const TABLE_NAME_WITH_PREFIX = _DB_PREFIX_ . self::TABLE_NAME;
    const DATE_FORMAT = 'd-m-Y H:i:s';

    const STATUS_ACTIVE = 1;
    const STATUS_FINISHED = 2;
    const STATUS_STOPPED = 3;
    const STATUS_ERROR = 4;
    const STATUS_SAVING = 5;
    const STATUS_NO_DATA = 6;
    const STATUS_NO_PRODUCT = 7;

    public function __construct($id_export_process, $is_automatic = false)
    {
        $id_task = \Tools::getValue('id_task');
        $id_configuration = \Tools::getValue('id_configuration');

        if (!empty($id_task)) {
            $this->task = PETask::getById($id_task);
            $id_configuration = $this->task['id_configuration'];
        } else {
            $this->task = false;
        }

        $this->configuration = PEConfigurationCore::getCompleteConfiguration($id_configuration);

        if (!$id_export_process) {
            $this->id = $this->createNewExportProcessInDb($is_automatic);
        } else {
            $this->id = $id_export_process;
        }
    }

    public static function createTableInDb()
    {
        self::dropTableFromDb();

        $query = "CREATE TABLE IF NOT EXISTS `" . self::TABLE_NAME_WITH_PREFIX . "` (
                `id_export_process` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_configuration` INT(11) UNSIGNED NOT NULL,
                `id_task` INT(11) NULL,
                `status` VARCHAR(128) NULL,
                `progress` INT(11) NULL,
                `start` VARCHAR(128) NULL,
                `finish` VARCHAR(128) NULL,
                `num_of_products` INT(11) NULL,
                `file_path` VARCHAR(256) NULL,
                `download_file_path` VARCHAR(256) NULL,
                `is_automatic` INT(11) NULL,
                PRIMARY KEY (`id_export_process`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

        return \Db::getInstance()->execute($query);
    }

    public static function dropTableFromDb()
    {
        $query = "DROP TABLE IF EXISTS `" . self::TABLE_NAME_WITH_PREFIX . "`";
        return \Db::getInstance()->execute($query);
    }

    public function run($iteration)
    {
        $active_process_id = PEExportProcess::getActiveProcessId();
        if (!$iteration && $active_process_id && !Configuration::getGlobalValue('MPM_PRODUCT_EXPORT_DEBUG_MODE')) {
            throw new PEExportProcessException($active_process_id, Module::getInstanceByName('exportproducts')->l('Other export is running now. Please wait until it will finish.',__CLASS__));
        }

        if ($iteration && $this->getStatus() == self::STATUS_STOPPED) {
            return false;
        }

        $this->updateStatus(PEExportProcess::STATUS_ACTIVE);

        $exporter = new PEExporter($this, $iteration, $this->configuration, $this->task);
        $result = $exporter->export();

        if ($result['status'] === 'need_to_run_next_iteration') {
            $id_task = $this->task ? $this->task['id_task'] : false;
            $link = self::getExportLink($this->configuration['id_configuration'], $result['next_iteration'], $id_task, $this->id);

            return self::runExportLink($link);
        }

        if ($this->task) {
            PEMailer::sendEmail($this->task, $this);
        }

        return true;
    }

    public static function stop()
    {
        $active_export_identification_data = self::getActiveProcessIdData();

        if (!$active_export_identification_data) {
            throw new \Exception(Module::getInstanceByName('exportproducts')->l('There is no active export processes right now.',__CLASS__));
        }

        $id_export_process = $active_export_identification_data['id_export_process'];
        $is_stopped = \Db::getInstance(_PS_USE_SQL_SLAVE_)->update(self::TABLE_NAME, ['status' => self::STATUS_STOPPED], 'id_export_process=' . (int)$id_export_process);

        if (!$is_stopped) {
            throw new \Exception(Module::getInstanceByName('exportproducts')->l('Can not stop export process with ID - ',__CLASS__) . $id_export_process);
        }

        return $active_export_identification_data;
    }

    public static function runExportLink($url)
    {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36';
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 0);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            if (Configuration::getGlobalValue('MPM_PRODUCT_EXPORT_DEBUG_MODE')) {
                //This debug test is not active by default.
                print_r(curl_exec($ch));die;
            } else {
              curl_exec($ch);
              $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
              if( $responseCode != 200 ){
                throw new Exception(Module::getInstanceByName('exportproducts')->l('Some error occurred please try again or contact us!',__CLASS__));
              }
            }
        } else {
            \Tools::file_get_contents($url);
        }

        return true;
    }

    public static function getExportLink(
        $id_configuration,
        $iteration = 0,
        $id_task = false,
        $id_export_process = false
    ) {
        $token = \Configuration::getGlobalValue('MPM_PRODUCT_EXPORT_TASKS_KEY');
        $admin_folder = str_replace(_PS_ROOT_DIR_ . '/', null, basename(_PS_ADMIN_DIR_));

        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            $path = \Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . $admin_folder . '/';
            $url = $path . \Context::getContext()->link->getAdminLink('AdminProductsExport', false);
        } else {
            $url = \Context::getContext()->link->getAdminLink('AdminProductsExport', false);
        }

        $url .= '&id_configuration=' . $id_configuration . '&secure_key=' . $token . '&iteration=' . $iteration;

        if ($id_task) {
            $url .= '&id_task=' . $id_task;
        }

        if ($id_export_process) {
            $url .= '&id_export_process=' . $id_export_process;
        }

        return $url;
    }

    private function createNewExportProcessInDb($is_automatic)
    {
        $export_process_data = [
            'id_configuration' => (int)$this->configuration['id_configuration'],
            'id_task'          => $this->task ? (int)$this->task['id_task'] : 0,
            'progress'         => 0,
            'start'            => pSQL(date(self::DATE_FORMAT)),
            'finish'           => null,
            'num_of_products'  => (int)0,
            'status'           => (int)PEExportProcess::STATUS_NO_DATA,
            'is_automatic'     => (int)$is_automatic
        ];

        \Db::getInstance(_PS_USE_SQL_SLAVE_)->insert(self::TABLE_NAME, $export_process_data);
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
    }

    public static function getById($id_export_process)
    {
        $query = "SELECT * FROM `" . self::TABLE_NAME_WITH_PREFIX . "`
                  WHERE `id_export_process` = '".(int)$id_export_process."'";

        $export_process_data = \Db::getInstance()->executeS($query);

        if (!isset($export_process_data[0])) {
            return [];
        }

        return self::prepareProcessDataForPresentation($export_process_data[0]);
    }

    public static function count()
    {
        $query = '
			SELECT COUNT(s.id_configuration) as count_export_processes
            FROM ' . self::TABLE_NAME_WITH_PREFIX . '  AS s';

        $res = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        if (isset($res[0]['count_export_processes']) && $res[0]['count_export_processes']) {
            return $res[0]['count_export_processes'];
        }

        return 0;
    }

    public static function delete($id_export_process)
    {
        $query = "DELETE FROM `" . self::TABLE_NAME_WITH_PREFIX . "` WHERE `id_export_process` = '".(int)$id_export_process."'";
        $is_deleted = \Db::getInstance()->execute($query);

        if (!$is_deleted) {
            throw new \Exception(Module::getInstanceByName('exportproducts')->l('Can not delete export process',__CLASS__));
        }

        return true;
    }

    public static function getActiveProcessId()
    {
        $active_process_statuses = self::STATUS_ACTIVE . ',' . self::STATUS_SAVING;

        $query = "SELECT `id_export_process` FROM " . self::TABLE_NAME_WITH_PREFIX . "
                  WHERE `status` IN (".$active_process_statuses.")";

        return \Db::getInstance()->getValue($query);
    }

    public static function getActiveProcessIdData()
    {
        $active_process_statuses = self::STATUS_ACTIVE . ',' . self::STATUS_SAVING;

        $query = "SELECT `id_export_process`, `id_configuration` FROM " . self::TABLE_NAME_WITH_PREFIX . "
                  WHERE `status` IN (".$active_process_statuses.")
                  ORDER BY `id_export_process` DESC
                  LIMIT 1";

        $result = \Db::getInstance()->executeS($query);

        if (empty($result[0])) {
            return false;
        }

        return $result[0];
    }

    public static function getLatestProcessId()
    {
        $query = "SELECT `id_export_process` FROM " . self::TABLE_NAME_WITH_PREFIX . "
                  ORDER BY `id_export_process` DESC";

        return \Db::getInstance()->getValue($query);
    }

    public static function update($id_export_process, $export_process_data)
    {
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->update(self::TABLE_NAME, $export_process_data, 'id_export_process=' . (int)$id_export_process);
    }

    public function updateFilePath($new_file_path)
    {
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->update(self::TABLE_NAME, ['file_path' => pSQL($new_file_path)], 'id_export_process=' . (int)$this->id);
    }

    public function updateDownloadFilePath($new_file_path)
    {
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->update(self::TABLE_NAME, ['download_file_path' => pSQL($new_file_path)], 'id_export_process=' . (int)$this->id);
    }

    public function getConfigurationId()
    {
        $query = 'SELECT `id_configuration` FROM `' . self::TABLE_NAME_WITH_PREFIX . '` WHERE `id_export_process` = ' . (int)$this->id;

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    public function getFilePath()
    {
        $query = 'SELECT `file_path` FROM `' . self::TABLE_NAME_WITH_PREFIX . '` WHERE `id_export_process` = ' . (int)$this->id;

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    public function getDownloadFilePath()
    {
        $query = 'SELECT `download_file_path` FROM `' . self::TABLE_NAME_WITH_PREFIX . '` WHERE `id_export_process` = ' . (int)$this->id;

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    public function updateProgress($new_progress)
    {
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->update(self::TABLE_NAME, ['progress' => (int)$new_progress], 'id_export_process=' . (int)$this->id);
    }

    public function updateStatus($new_status)
    {
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->update(self::TABLE_NAME, ['status' => (int)$new_status], 'id_export_process=' . (int)$this->id);
    }

    public function getStatus()
    {
        $query = 'SELECT `status` FROM `' . self::TABLE_NAME_WITH_PREFIX . '`
                WHERE `id_export_process` = ' . (int)$this->id;

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    public function updateIsAutomatic($is_automatic)
    {
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->update(self::TABLE_NAME, ['is_automatic' => (int)$is_automatic], 'id_export_process=' . (int)$this->id);
    }

    public function isAutomatic()
    {
        $query = 'SELECT `is_automatic` FROM `' . self::TABLE_NAME_WITH_PREFIX . '`
                WHERE `id_export_process` = ' . (int)$this->id;

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    public function updateTaskId($id_task)
    {
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->update(self::TABLE_NAME, ['id_task' => (int)$id_task], 'id_export_process=' . (int)$this->id);
    }

    public function getTaskId()
    {
        $query = 'SELECT `id_task` FROM `' . self::TABLE_NAME_WITH_PREFIX . '`
                WHERE `id_export_process` = ' . (int)$this->id;

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    public function updateNumOfProductsToExport($num_of_products)
    {
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->update(self::TABLE_NAME, ['num_of_products' => (int)$num_of_products], 'id_export_process=' . (int)$this->id);
    }

    public function getNumberOfProductsForExport()
    {
        $query = 'SELECT num_of_products
                FROM ' . self::TABLE_NAME_WITH_PREFIX . '
                WHERE id_export_process = ' . (int)$this->id;

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    public static function getFinishTime($id_export_process)
    {
        $query = "SELECT `finish` FROM `" . self::TABLE_NAME_WITH_PREFIX . "` WHERE `id_export_process` = '" . (int)$id_export_process . "'";
        return \Db::getInstance()->getValue($query);
    }

    public function updateFinishTime($finish_time)
    {
        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->update(self::TABLE_NAME, ['finish' => pSQL($finish_time)],
            'id_export_process=' . (int)$this->id);
    }

    public function getProgress()
    {
        $query = 'SELECT progress FROM ' . self::TABLE_NAME_WITH_PREFIX . '
                WHERE id_export_process = ' . (int)$this->id;

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    public static function getLastTaskProcess($id_task)
    {
        $query = "SELECT * FROM " . self::TABLE_NAME_WITH_PREFIX . " 
                  WHERE `id_task` = " . (int)$id_task . "
                  ORDER BY `id_export_process` DESC
                  LIMIT 1";

        $export_process = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if (empty($export_process[0])) {
            return false;
        }

        return self::prepareProcessDataForPresentation($export_process[0]);
    }

    public static function getStartTime($id_export_process)
    {
        $query = 'SELECT start FROM ' . self::TABLE_NAME_WITH_PREFIX . '
                WHERE id_export_process = ' . (int)$id_export_process;

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    public static function getLastExecutedProcessByTaskId($id_task)
    {
        $query = "SELECT * FROM " . self::TABLE_NAME_WITH_PREFIX . "
                 WHERE `id_task` = '" . (int)$id_task . "'
                 ORDER BY `id_export_process` DESC
                 LIMIT 1";

        $export_process = \Db::getInstance()->executeS($query);

        if (empty($export_process[0])) {
            return false;
        }

        return $export_process[0];
    }

    public static function getStatusNameById($id_status)
    {
        $data = [
            self::STATUS_ACTIVE => Module::getInstanceByName('exportproducts')->l('Active',__CLASS__),
            self::STATUS_FINISHED => Module::getInstanceByName('exportproducts')->l('Finished',__CLASS__),
            self::STATUS_STOPPED => Module::getInstanceByName('exportproducts')->l('Stopped',__CLASS__),
            self::STATUS_ERROR => Module::getInstanceByName('exportproducts')->l('Error',__CLASS__),
            self::STATUS_SAVING => Module::getInstanceByName('exportproducts')->l('Saving exported file',__CLASS__),
            self::STATUS_NO_DATA => Module::getInstanceByName('exportproducts')->l('No Data',__CLASS__),
            self::STATUS_NO_PRODUCT => Module::getInstanceByName('exportproducts')->l('No Product',__CLASS__)
        ];

        if (isset($data[$id_status]) && $data[$id_status]) {
            return $data[$id_status];
        }

        return Module::getInstanceByName('exportproducts')->l('No Data',__CLASS__);
    }

    public static function getLastExecutedProcessesData($limit)
    {
        $query = "SELECT * FROM " . self::TABLE_NAME_WITH_PREFIX . " as ep
                  LEFT JOIN " . PEConfigurationCore::TABLE_NAME_WITH_PREFIX . " as c
                  ON ep.id_configuration = c.id_configuration
                  ORDER BY ep.id_export_process DESC
                  LIMIT " . (int)$limit;

        $last_export_processes = \Db::getInstance()->executeS($query);

        if (!empty($last_export_processes)) {
            foreach ($last_export_processes as $key => &$export_process) {
                if (empty($export_process['id_export_process']) || empty($export_process['id_configuration'])) {
                    unset($last_export_processes[$key]);
                    continue;
                }

                $export_process = self::prepareProcessDataForPresentation($export_process);
            }
        }

        return $last_export_processes;
    }

    private static function prepareProcessDataForPresentation($export_process_data)
    {
        if (!empty($export_process_data['start']) && !empty($export_process_data['finish'])) {
            $export_process_data['time'] = self::getExportTime($export_process_data['id_export_process']);
        }

        $export_process_data['id_status'] = $export_process_data['status'];

        if (!empty($export_process_data['status'])) {
            $export_process_data['status'] = self::getStatusNameById($export_process_data['status']);
        } else {
            $export_process_data['status'] = self::getStatusNameById(PEExportProcess::STATUS_NO_DATA);
        }

        return $export_process_data;
    }

    public static function getExportTime($id_export_process)
    {
        $start_time = self::getStartTime($id_export_process);
        $finish_time = self::getFinishTime($id_export_process);

        if (!$start_time || !$finish_time) {
            return '';
        }

        $start_time = \DateTime::createFromFormat(PEExportProcess::DATE_FORMAT, $start_time);
        $finish_time = \DateTime::createFromFormat(PEExportProcess::DATE_FORMAT, $finish_time);
        $export_time = '';

        $difference = $start_time->diff($finish_time);

        if ($difference->h) {
            $export_time .= $difference->h . Module::getInstanceByName('exportproducts')->l(' hours ',__CLASS__);
        }

        if ($difference->i) {
            $export_time .= $difference->i . Module::getInstanceByName('exportproducts')->l(' minutes ',__CLASS__);
        }

        $export_time .= $difference->s . Module::getInstanceByName('exportproducts')->l(' seconds ',__CLASS__);

        return $export_time;
    }

    public static function getLastProcessByConfigurationId($id_configuration)
    {
        $result = \Db::getInstance()->executeS("SELECT * FROM " . self::TABLE_NAME_WITH_PREFIX . " 
                                                WHERE id_configuration = " . (int)$id_configuration . "
                                                ORDER BY id_export_process DESC");

        if (!empty($result)) {
            return self::prepareProcessDataForPresentation($result[0]);
        }

        return false;
    }

    public static function getAllActiveProcesses()
    {
        $active_process_statuses = self::STATUS_ACTIVE . ',' . self::STATUS_SAVING;

        $query = "SELECT GROUP_CONCAT(`id_export_process`) as active_process_ids FROM " . self::TABLE_NAME_WITH_PREFIX . "
                  WHERE `status` IN (".$active_process_statuses.")";

        return \Db::getInstance()->getValue($query);
    }

    public static function stopAllActiveProcesses()
    {
        $active_process_ids = self::getAllActiveProcesses();

        if (empty($active_process_ids)) {
            return true;
        }

        $is_stopped = Db::getInstance()->execute("UPDATE " . self::TABLE_NAME_WITH_PREFIX . " 
                                                SET `status` = '".(int)self::STATUS_STOPPED."'
                                                WHERE `id_export_process` IN (".$active_process_ids.")");

        return $is_stopped;
    }

    public static function clearHistory()
    {
        self::stopAllActiveProcesses();

        $exported_files = scandir(_PS_MODULE_DIR_ . 'exportproducts/files');
        $exported_files = array_diff($exported_files, ['.', '..', 'index.php']);

        $database_is_cleared = Db::getInstance()->execute("TRUNCATE TABLE " . self::TABLE_NAME_WITH_PREFIX);

        if (!$database_is_cleared) {
            return false;
        }

        if (!empty($exported_files)) {
            foreach ($exported_files as $file_name) {
                $file_path = _PS_MODULE_DIR_ . 'exportproducts/files/' . $file_name;

                if (!file_exists($file_path)) {
                    continue;
                }

                unlink($file_path);
            }
        }

        return true;
    }
}