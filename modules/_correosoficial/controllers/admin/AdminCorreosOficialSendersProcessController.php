<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/DetectPlatform.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/config.inc.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/Dao/CorreosOficialSendersDao.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/functions.php';
require_once _PS_MODULE_DIR_ . 'correosoficial/classes/CorreosOficialSenders.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/Commons/Normalization.php';

class AdminCorreosOficialSendersProcessController extends ModuleAdminController
{
    /**
     * @var module
     */
    public $module;

    private $sender_dao;

    public function __construct()
    {
        parent::__construct();
        $this->sender_dao = new CorreosOficialSenders();

        $action = Tools::getValue('action');
        
        $sender_from_time = Normalization::normalizeData('sender_from_time');
        $sender_to_time = Normalization::normalizeData('sender_to_time');

        $correos_code = Normalization::normalizeData('correos_code');
        $cex_code = Normalization::normalizeData('cex_code');

        switch ($action) {
            case 'CorreosSendersInsertForm':

                $fields = array(
                    'sender_name'          => Normalization::normalizeData('sender_name'),
                    'sender_address'       => Normalization::normalizeData('sender_address'),
                    'sender_cp'            => Normalization::normalizeData('sender_cp'),
                    'sender_nif_cif'       => Normalization::normalizeData('sender_nif_cif'),
                    'sender_city'          => Normalization::normalizeData('sender_city'),
                    'sender_contact'       => Normalization::normalizeData('sender_contact'),
                    'sender_phone'         => Normalization::normalizeData('sender_phone'),
                    'sender_from_time'     => $sender_from_time != '' ? $sender_from_time : '00:00',
                    'sender_to_time'       => $sender_to_time != '' ? $sender_to_time : '00:00',
                    'sender_iso_code_pais' => Normalization::normalizeData('sender_iso_code_pais'),
                    'sender_email'         => Normalization::normalizeData('sender_email', 'email'),
                    'sender_default'       => Normalization::normalizeData('sender_default'),
                    'correos_code'         => $correos_code != '' ? $correos_code : 0,
                    'cex_code'             => $cex_code != '' ? $cex_code : 0,
                    'id_shop'              => $this->context->shop->id
                );

                $this->sender_dao->insertFieldsSetRecord($fields);
                break;
            
            case 'CorreosSendersUpdateForm':

                $fields = array(
                    'id'                   => Normalization::normalizeData('sender_id'),
                    'sender_name'          => Normalization::normalizeData('sender_name'),
                    'sender_address'       => Normalization::normalizeData('sender_address'),
                    'sender_cp'            => Normalization::normalizeData('sender_cp'),
                    'sender_nif_cif'       => Normalization::normalizeData('sender_nif_cif'),
                    'sender_city'          => Normalization::normalizeData('sender_city'),
                    'sender_contact'       => Normalization::normalizeData('sender_contact'),
                    'sender_phone'         => Normalization::normalizeData('sender_phone'),
                    'sender_from_time'     => $sender_from_time != '' ? $sender_from_time : '00:00',
                    'sender_to_time'       => $sender_to_time != '' ? $sender_to_time : '00:00',
                    'sender_iso_code_pais' => Normalization::normalizeData('sender_iso_code_pais'),
                    'sender_email'         => Normalization::normalizeData('sender_email', 'email'),
                    'correos_code'         => $correos_code != '' ? $correos_code : 0,
                    'cex_code'             => $cex_code != '' ? $cex_code : 0
                );

                $this->sender_dao->updateFieldsSetRecord($fields);
                break;

            case 'CorreosSenderSaveDefaultForm':
                $sender_default_id = Normalization::normalizeData('sender_default_id');
                $this->sender_dao->updateFieldSetRecord($sender_default_id);
                break;

            case 'CorreosSendersDeleteForm':
                $sender_id = Normalization::normalizeData('sender_id');
                $this->sender_dao->deleteFieldsSetRecord($sender_id);
                break;
            
        }

        // Actualizamos analitica
        (new Analitica())->configurationCall('undefined');

    }
}
