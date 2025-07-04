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
@ini_set('display_errors', 'off');
error_reporting(0);
require_once _PS_MODULE_DIR_ . 'exportproducts/src/PEExportProcess.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/PETask.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/form/PEAdminPanel.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PEMailer.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PELogger.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PEFilePreview.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PECronExpression.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/configuration/PEConfigurationCore.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/exception/PEExportProcessException.php';
require_once _PS_MODULE_DIR_ . 'exportproducts/src/tools/PEGoogleCategory.php';

class AdminProductsExportController extends ModuleAdminController
{
    private $controller_link;
    private $ajax_response;

    public function __construct()
    {
        parent::__construct();

        $this->setUpErrorLog();
        $this->setUpPhpScriptConfig();

        $this->bootstrap = true;
        $this->multishop_context = -1;
        $this->multishop_context_group = true;
        $this->display = 'edit';
        $this->controller_link = PEAdminPanel::getControllerLink();
        $this->ajax_response = [];

        $secure_key = Tools::getValue('secure_key');
        $is_ajax_request = Tools::getValue('ajax');

        if (!$secure_key) {
            return false;
        } elseif ($secure_key != \Configuration::getGlobalValue('MPM_PRODUCT_EXPORT_TASKS_KEY')) {
            die('Invalid secure_key');
        }

        if (!$is_ajax_request) {
            Configuration::updateGlobalValue('MPM_PRODUCT_EXPORT_ERROR_MESSAGE', '');

            try {
                if (Tools::getValue('is_automatic')) {
                    $this->blockBrowser();

                    ob_start();
                    PETask::runAllScheduledTasks();
                    ob_end_clean();

                    die;
                } else {
                    $id_export_process = \Tools::getValue('id_export_process') ? \Tools::getValue('id_export_process') : false;
                    $iteration = \Tools::getValue('iteration') ? \Tools::getValue('iteration') : 0;

                    $export_process = new PEExportProcess($id_export_process);
                    $export_process->run($iteration);
                }
            } catch (PEExportProcessException $e) {
                $id_export_process = $e->getExportProcessId();
                $this->handleException($e, $id_export_process);
            } catch (Exception $e) {
                $id_export_process = PEExportProcess::getLatestProcessId();
                $this->handleException($e, $id_export_process);
            }
        }
    }

    private function blockBrowser()
    {
        ignore_user_abort(true);
        set_time_limit(0);
        ob_start();
        echo 'Tasks run';
        header('Connection: close');
        header('Content-Length: ' . ob_get_length());
        ob_end_flush();
        ob_flush();
        flush();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    public function initContent()
    {
      session_write_close();
      parent::initContent();
    }

    public function saveSettings()
    {
      $file = _PS_ROOT_DIR_ . '/modules/exportproducts/upload/settings.txt';
      $file_info = pathinfo($file);
      $file_type = "application/force-download";
      $file_name = $file_info['basename'];
      header('Content-type: ' . $file_type);
      header('Content-Disposition: attachment; filename=' . $file_name . '');
      readfile('' . $file . '');
      exit();
    }

    public function displayAjax()
    {
        if (empty(Tools::getValue('action'))) {
            return false;
        }

        try {
            $this->callActionMethod(Tools::getValue('action'));
        } catch (Exception $e) {
            $this->handleException($e);
            $this->ajax_response['error'] = PEAdminPanel::getErrorForm($e->getMessage());
        }

        die(json_encode($this->ajax_response));
    }

    private function callActionMethod($action)
    {
        $current_object = $this;
        if (method_exists($current_object, $action)) {
            return $this->$action();
        }
    }

    private function handleException(Exception $exception, $id_export_process = false)
    {
        if ($id_export_process) {
            $export_process = new PEExportProcess($id_export_process);
            $export_process->updateStatus(PEExportProcess::STATUS_ERROR);

            $id_task = $export_process->getTaskId();

            if ($id_task) {
                $task = PETask::getById($id_task);
                PEMailer::sendEmail($task, $export_process, $exception->getFullMessage());
            }

            Configuration::updateGlobalValue('MPM_PRODUCT_EXPORT_ERROR_MESSAGE', $exception->getMessage());
        }

        $error = $exception->getMessage() . '|' . $exception->getFile() . '|' . $exception->getLine();
        PELogger::logError($error);

        if (Configuration::getGlobalValue('MPM_PRODUCT_EXPORT_DEBUG_MODE')) {
            //This debug test is not active by default.
            print_r($error);die;
        }
    }

    public function display()
    {
      if( Tools::getValue('action') == 'saveSettings' ){
        $this->saveSettings();
      }
      else{
        parent::display();
      }
    }

    public function renderForm()
    {
        $module = Module::getInstanceByName('exportproducts');
        $module->database_version = Db::getInstance()->getValue("SELECT version FROM " . _DB_PREFIX_ . "module WHERE id_module = '".(int)$module->id."'");

        if (Tools::version_compare($module->version, $module->database_version, '>')) {
            $tpl = Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'exportproducts/views/templates/admin/need_upgrade.tpl');

            return $tpl->fetch();
        }

        return PEAdminPanel::get();
    }

    public function getActiveExportData()
    {
      $is_active_export_tab = Tools::getValue('is_active_export_tab');

      $errors = false;
      $messages = [];
      $new_data = [];
      $data_export = false;
      $stop = false;
      $file_error_log = false;

      $active_process_id = Configuration::getGlobalValue('MPM_EXPORTPRODUCTS_ACTIVE_PROCESS_ID');
      $active_export_data = PEExportProcess::getById($active_process_id);
      $newExportStartTime = Configuration::getGlobalValue('MPM_EXPORTPRODUCTS_NEW_EXPORT_START_TIME');

      if (empty($active_export_data) ) {
        if( ($newExportStartTime > (time() - 30)) ){
          $this->ajax_response['pending'] = true;
        }
        else{
          $this->ajax_response['no_active_export'] = true;
        }
        return false;
      }

      $active_export_data['progress_label'] = '';
      $active_export_data['change'] = true;
      $active_export_data['progress_label'] = ($active_export_data['progress'] . Module::getInstanceByName('exportproducts')->l(' from ', 'send') . $active_export_data['num_of_products']);

      switch ($active_export_data['id_status']) {
        case PEExportProcess::STATUS_ACTIVE:
          $active_export_data['status'] = $active_export_data['progress'] . Module::getInstanceByName('exportproducts')->l(' from ', 'send') . $active_export_data['num_of_products'];
          break;
        case PEExportProcess::STATUS_SAVING:
          $active_export_data['status'] = Module::getInstanceByName('exportproducts')->l('Writing data to file... ', 'send');
          break;
        case PEExportProcess::STATUS_FINISHED:
          $messages[] = Module::getInstanceByName('exportproducts')->l('The file has been generated and can be downloaded below:',__CLASS__);

          if ($is_active_export_tab) {
            $active_export_data['success'] = PEAdminPanel::getSuccessForm($messages, false, $active_export_data['file_path']);
          }

          Configuration::updateGlobalValue('MPM_EXPORTPRODUCTS_ACTIVE_PROCESS_ID', false);
          Configuration::updateGlobalValue('MPM_EXPORTPRODUCTS_NEW_EXPORT_START_TIME',false);

          break;
        case PEExportProcess::STATUS_STOPPED:
          $active_export_data['status'] = Module::getInstanceByName('exportproducts')->l('Stopped', 'send');
          Configuration::updateGlobalValue('MPM_EXPORTPRODUCTS_ACTIVE_PROCESS_ID', false);
          Configuration::updateGlobalValue('MPM_EXPORTPRODUCTS_NEW_EXPORT_START_TIME',false);

          break;
        case PEExportProcess::STATUS_ERROR:
          $messages_error = Configuration::getGlobalValue('MPM_PRODUCT_EXPORT_ERROR_MESSAGE');

          if (!$messages_error) {
            $messages_error = Module::getInstanceByName('exportproducts')->l('Some error occurred please check. ',__CLASS__);
          }

          $file_error_log = Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/exportproducts/error.log';
          $errors[] = $messages_error;

          break;
        case PEExportProcess::STATUS_NO_DATA:
          $errors[] = Module::getInstanceByName('exportproducts')->l('No export data',__CLASS__);
          break;
        case PEExportProcess::STATUS_NO_PRODUCT:
          $errors[] = Module::getInstanceByName('exportproducts')->l('No of matching products.',__CLASS__);
          break;
        default:
          break;
      }

      if ($errors) {
        if ($is_active_export_tab) {
          $active_export_data['error'] = PEAdminPanel::getErrorForm($errors, $file_error_log);
        }

        Configuration::updateGlobalValue('MPM_EXPORTPRODUCTS_ACTIVE_PROCESS_ID', false);
        Configuration::updateGlobalValue('MPM_EXPORTPRODUCTS_NEW_EXPORT_START_TIME',false);
      }

      $this->ajax_response = $active_export_data;
    }

    public function startExportManually()
    {
        $active_process_id = PEExportProcess::getActiveProcessId();
        if ($active_process_id && !Configuration::getGlobalValue('MPM_PRODUCT_EXPORT_DEBUG_MODE')) {
            throw new PEExportProcessException($active_process_id, Module::getInstanceByName('exportproducts')->l('Other export is running now. Please wait until it will finish.',__CLASS__));
        }

        Configuration::updateGlobalValue('MPM_EXPORTPRODUCTS_NEW_EXPORT_START_TIME', time());
        Configuration::updateGlobalValue('MPM_EXPORTPRODUCTS_ACTIVE_PROCESS_ID', false);

        $id_configuration = \Tools::getValue('id_configuration');
        $id_task = \Tools::getValue('id_task');

        if (!$id_configuration && $id_task) {
            $task = PETask::getById($id_task);
            $id_configuration = $task['id_configuration'];
        }

        if (!$id_configuration) {
            $configuration = \Tools::getValue('export_configuration');

            if (empty($configuration['fields'])) {
                throw new \Exception(Module::getInstanceByName('exportproducts')->l('You have not selected any fields to export',__CLASS__));
            }

            $id_configuration = PEConfigurationCore::createNewConfiguration($configuration);
        }
        
        $url = PEExportProcess::getExportLink($id_configuration, 0, $id_task);
        return PEExportProcess::runExportLink($url);
    }

    public function validateCronExpression()
    {
        $response = [];

        $expression = new PECronExpression(\Tools::getValue('expression'));

        if (!$expression->isValid()) {
            throw new \Exception(Module::getInstanceByName('exportproducts')->l('This cron job expression is not valid!',__CLASS__));
        }

        $exploded_expression = explode(' ', $expression->get());

        $response['human_description'] = $expression->explainToHumans();
        $response['next_run'] = date(\Context::getContext()->language->date_format_full, $expression->getNextRunTime());
        $response['expression']['min'] = $exploded_expression[0];
        $response['expression']['hour'] = $exploded_expression[1];
        $response['expression']['day_of_month'] = $exploded_expression[2];
        $response['expression']['month'] = $exploded_expression[3];
        $response['expression']['day_of_week'] = $exploded_expression[4];

        $this->ajax_response = $response;
    }

    public function getTabContent()
    {
        $this->ajax_response = [
            'tab_url' => $this->controller_link . '&export_tab=' . Tools::getValue('tab'),
            'tab_content' => PEAdminPanel::getTabContent(Tools::getValue('tab'))
        ];
    }

    public function getRelatedModules()
    {
        $this->ajax_response['related_modules'] = PEAdminPanel::getRelatedModulesBlock();
    }

    public function getCategoriesTree()
    {
        $categories = Tools::getValue('categories') ? Tools::getValue('categories') : [];
        $this->ajax_response['tree'] = PEAdminPanel::getCategoriesTree('categories-tree', 'categoryBox', $categories);
    }

    public function getGoogleCategoryAssocBlock()
    {
        $id_category = Tools::getValue('id_category');
        $this->ajax_response['tpl'] = PEGoogleCategory::getGoogleCategoryAssocBlockTpl($id_category);
    }

    public function getAllGoogleCategoryAssocBlocks()
    {
        $category_ids = Tools::getValue('category_ids');
        $google_category_association_blocks = '';

        if (empty($category_ids)) {
            $this->ajax_response['tpl'] = $google_category_association_blocks;
            return true;
        }

        foreach ($category_ids as $category_id) {
            $google_category_association_blocks .= PEGoogleCategory::getGoogleCategoryAssocBlockTpl($category_id);
        }

        $this->ajax_response['tpl'] = $google_category_association_blocks;
    }

    public function searchGoogleCategory()
    {
        $search_query = Tools::getValue('search_query');
        $this->ajax_response['search_results'] = PEGoogleCategory::searchGoogleCategory($search_query);
    }

    public function getFilterBlock()
    {
        $this->ajax_response['filter'] = PEAdminPanel::getFilterBlock(Tools::getValue('type'), Tools::getValue('id'), Tools::getValue('label'));
    }

    public function deleteConfiguration()
    {
        PEConfigurationCore::delete(Tools::getValue('id_configuration'));
        $link = $this->controller_link;
        $messages = [Module::getInstanceByName('exportproducts')->l('Configuration is successfully deleted!',__CLASS__)];
        $this->ajax_response['success'] = PEAdminPanel::getSuccessForm($messages, $link);
    }

    public function deleteExportProcess()
    {
        PEExportProcess::delete(Tools::getValue('id_export_process'));
        $link = $this->controller_link;
        $messages = [Module::getInstanceByName('exportproducts')->l('Export Process is successfully deleted from history!',__CLASS__)];
        $this->ajax_response['success'] = PEAdminPanel::getSuccessForm($messages, $link);
    }

    public function downloadConfiguration()
    {
        $id_configuration = \Tools::getValue('id_configuration');
        $this->ajax_response = ['download' => PEConfigurationCore::getLinkForDownload($id_configuration)];
    }

    public function formEditTask()
    {
        $this->ajax_response['form'] = PETask::getForm(Tools::getValue('id_task'));
    }

    public function getTaskList()
    {
        $this->ajax_response['form'] = PETask::getTasksList();
    }

    public function deleteTask()
    {
        PETask::deleteTask(Tools::getValue('id_task'));

        $messages = [$this->module->l('Data successfully removed!')];
        $this->ajax_response['success'] = PEAdminPanel::getSuccessForm($messages);
    }

    public function saveScheduledTask()
    {
        $values = Tools::getValue('values');
        PETask::save($values);

        $messages = [$this->module->l('Task is successfully saved!')];
        $this->ajax_response['success'] = PEAdminPanel::getSuccessForm($messages);
        $this->ajax_response['form'] = PETask::getTasksList();
    }

    public function getFormPreviewFile()
    {
        $file_preview = new PEFilePreview(Tools::getValue('values'));
        $this->ajax_response['success'] = $file_preview->getTemplate();
    }

    public function getExtraFieldForm()
    {
        $this->ajax_response['form'] = PEAdminPanel::getExtraFieldForm();
    }

    public function searchFiltersFields()
    {
        if (Tools::getValue('field') == 'customers') {
            $this->ajax_response['success'] = PEAdminPanel::getSearchCustomers(Tools::getValue('search'), Tools::getValue('checked'));
        }
    }

    public function uploadConfiguration()
    {
        $this->ajax_response['success'] = PEConfigurationCore::upload();
    }

    public function stopProductExport()
    {
        $stopped_export_process_identification_data = PEExportProcess::stop();

        if (!empty($stopped_export_process_identification_data)) {
            $this->ajax_response['success'] = ['id_export_process' => $stopped_export_process_identification_data['id_export_process'],
                                               'id_configuration' => $stopped_export_process_identification_data['id_configuration'],
                                               'status_label' => PEExportProcess::getStatusNameById(PEExportProcess::STATUS_STOPPED)];
        } else {
            $this->ajax_response['error'] = true;
        }
    }

    public function getMoreCondition()
    {
        $this->ajax_response['condition'] = PEAdminPanel::getConditionLine((int)Tools::getValue('count_conditions'));
    }

    public function changeTaskStatus()
    {
        $id_task = Tools::getValue('id_task');
        $new_status = Tools::getValue('new_status');

        $this->ajax_response['success'] = PETask::changeStatus($id_task, $new_status);
    }

    public function saveExportConfiguration()
    {
        $configuration_data = Tools::getValue('values');
        $configuration_data['is_saved'] = true;

        $id_configuration = PEConfigurationCore::createNewConfiguration($configuration_data);
        $link = $this->controller_link . '&export_tab=new_export&id_configuration=' . $id_configuration;

        $messages = [$this->module->l('Data successfully saved!')];
        $this->ajax_response['success'] = PEAdminPanel::getSuccessForm($messages, $link);
    }

    public function changeDebugMode()
    {
        $debug_mode_new_status = Tools::getValue('debug_mode');
        Configuration::updateGlobalValue('MPM_PRODUCT_EXPORT_DEBUG_MODE', (int)$debug_mode_new_status);
        PEExportProcess::stopAllActiveProcesses();

        if ($debug_mode_new_status == true) {
            $this->ajax_response['message'] = Module::getInstanceByName('exportproducts')->l('DEBUG MODE IS ENABLED!',__CLASS__);
        } else {
            $this->ajax_response['message'] = Module::getInstanceByName('exportproducts')->l('DEBUG MODE IS DISABLED!',__CLASS__);
        }
    }

    public function clearHistory()
    {
        $this->ajax_response['result'] = PEExportProcess::clearHistory();
    }

    private function setUpErrorLog()
    {
        $write_fd = fopen(_PS_MODULE_DIR_ . 'exportproducts/error.log', 'w');
        fwrite($write_fd, ' ');
        fclose($write_fd);
        ini_set('log_errors', 1);
        ini_set('error_log', _PS_MODULE_DIR_ . 'exportproducts/error.log');

        return true;
    }

    private function setUpPhpScriptConfig()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
    }
}
