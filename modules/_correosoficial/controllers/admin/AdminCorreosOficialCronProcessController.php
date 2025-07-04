<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/DetectPlatform.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/config.inc.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialCronDao.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/functions.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Cron/CronCorreosOficial.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Commons/Normalization.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Commons/CorreosOficialLog.php';

class AdminCorreosOficialCronProcessController extends ModuleAdminController
{

    private $cron_dao;

    public function __construct()
    {
        parent::__construct();

        $operation = Normalization::normalizeData('operation');

        if ($operation === 'CRONFORM') {
            $this->updateCronSettings();
        } elseif ($operation === 'EXECUTECRON') {
            if (self::mustExecuteCron()) {
                $this->cronExecute();
                die("CRON: Ejecución finalizada");
            } else {
                die("CRON: Ninguna acción a ejecutar");
            }
        }
    }

    /**
     * Se guardan los ajustes del cron.
     */
    public function updateCronSettings()
    {

        // Obtenemos campos de los formularios
        $ActivateOrderStatusChangeAfterSave = Normalization::normalizeData('ActivateOrderStatusChangeAfterSave');
        $StatusSelector = Normalization::normalizeData('StatusSelector');
        $ActivateAutomaticTracking = Normalization::normalizeData('ActivateAutomaticTracking');
        $ActivateOrderStatusChange = Normalization::normalizeData('ActivateOrderStatusChange');
        $CurrentState = Normalization::normalizeData('CurrentState');
        $DeliveredState = Normalization::normalizeData('DeliveredState');
        $CancelledStateValue = Normalization::normalizeData('CancelledStateValue');
        $ReturnedState = Normalization::normalizeData('ReturnedState');
        $CronInterval = Normalization::normalizeData('CronInterval');

        // Los metemos en un array
        $fields = array(
            'ActivateOrderStatusChangeAfterSave' => $ActivateOrderStatusChangeAfterSave,
            'StatusSelector' => $StatusSelector,
            'ActivateAutomaticTracking' => $ActivateAutomaticTracking,
            'ActivateOrderStatusChange' => $ActivateOrderStatusChange,
            'CurrentState' => $CurrentState,
            'DeliveredState' => $DeliveredState,
            'CancelledStateValue' => $CancelledStateValue,
            'ReturnedState' => $ReturnedState,
            'CronInterval' => $CronInterval
        );

        // Obtenemos un objetoDao
        $this->cron_dao = new CorreosOficialCronDao();

        // Clave del registro
        $this->cron_dao->updateFieldsSetRecord($fields);
        die;
    }

    /**
     * Función de Cron desde el controlador. Ejecuta el Cron de Ajustes.
     */
    public function cronExecute()
    {
        $ini_time = CorreosOficialLog::logDate();

        try {
            $cron = new CronCorreosOficial();
            $cron->cronInit();
            $this->updateCronLastFailedExecutionTime('1971-01-01 00:00:00');
        } catch (Exception $e) {
            $cron_error_log = dirname(__FILE__) . "/../../log/cron_error_log.txt";
            $this->updateCronLastFailedExecutionTime(date("Y-m-d H:i:s"));

            file_put_contents($cron_error_log, "[" . $ini_time . "] ", FILE_APPEND);
            file_put_contents($cron_error_log, $e->getMessage(), FILE_APPEND);

            $end_time = CorreosOficialLog::logDate();

            file_put_contents($cron_error_log, " [" . $end_time . "]" . PHP_EOL . PHP_EOL, FILE_APPEND);
            error_log('Excepción capturada 15500: ' . $e->getMessage() . "\n");
            die('Excepción capturada 15500: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Comprueba la diferencia entre ejecuciones del cron.
     * Si es mayor que max_diference_between_cron_execution
     * lo ejecuta.
     */
    public static function mustExecuteCron()
    {
        $dao = new CorreosOficialDAO();

        $isCronActivated = $dao->readSettings('ActivateAutomaticTracking');

        if ($isCronActivated->value != 'on' || empty($isCronActivated->value)) {
            die('El cron está inactivo');
            return false;
        }

        // Ejecuciones del cron de la configuración de Ajustes->Configuración de usuario
        $last_cron_execution = $dao->readSettings('CronLastExecutionTime');
        $max_diference_between_cron_execution = $dao->readSettings('CronInterval');

        $datetime1 = new DateTime($last_cron_execution->value);
        $datetime2 = new DateTime(date("Y-m-d H:i:s"));

        $interval = $datetime1->diff($datetime2);
        $diff_time = $interval->format('%h');

        if ($interval->d >= 1) {
            $diff_time = $interval->d * 24;
        }

        // Hay que sumar 1 al intervalo de la hora
        $diff_time +=1;

        $last_cron_failed_execution = $dao->readSettings('CronLastFailedExecutionTime');

        // Primera ejecución para log de errores
        if (!isset($last_cron_failed_execution->value)) {
            $context = Context::getContext();

            $data = array(
                'name' => 'CronLastFailedExecutionTime',
                'value' => '1971-01-01 00:00:00',
                'type' => 'datetime',
                'id_shop' => $context->shop->id
            );

            $dao->insertRecord('correos_oficial_configuration', $data);
            return true;
        }

        // Ejecuciones del cron anteriormente fallidas
        $max_diference_between_failed_execution = $max_diference_between_cron_execution->value;
        $datetime3 = new DateTime($last_cron_failed_execution->value);
        $datetime4 = new DateTime(date("Y-m-d H:i:s"));

        $interval2 = $datetime3->diff($datetime4);
        $diff_time_between_failures = $interval2->format('%h');

        if ($interval2->d >= 1) {
            $diff_time_between_failures = $diff_time_between_failures = $interval2->d * 24;
        }

        if ($diff_time > $max_diference_between_cron_execution->value &&
            ($diff_time_between_failures > $max_diference_between_failed_execution)) {
            return true;
        }
        return false;
    }

    /**
     * @param $date datime: fecha de la última ejecución fallida
     */
    public function updateCronLastFailedExecutionTime($date)
    {
        $dao = new CorreosOficialDAO();

        $table = 'correos_oficial_configuration';
        $record = array('value'=>$date, 'id_shop'=>$this->context->shop->id);
        $where = " WHERE name = 'CronLastFailedExecutionTime' AND id_shop = ".$this->context->shop->id;

        $dao->updateRecord($table, $record, $where);
    }

}
