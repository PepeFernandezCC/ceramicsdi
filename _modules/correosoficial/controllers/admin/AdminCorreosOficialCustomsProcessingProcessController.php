<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/DetectPlatform.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/config.inc.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/Dao/CorreosOficialCustomsProcessingDao.php';	
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/functions.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/CorreosOficialUtils.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/Commons/Normalization.php';

class AdminCorreosOficialCustomsProcessingProcessController extends ModuleAdminController
{

    private $custom_processing_dao;

    public function __construct()
    {
        parent::__construct();
        
        $this->getCustomFormData();
    }

    public function getCustomFormData (){
        // Obtenemos un objetoDao
        $this->custom_processing_dao = new CorreosOficialCustomsProcessingDao();

        // Obtenemos campos de los formularios
        $DefaultCustomsDescription = Normalization::normalizeData('DefaultCustomsDescription');
        $TranslatableInput = Normalization::normalizeData('TranslatableInput');
        $FormSwitchLanguage = Normalization::normalizeData('FormSwitchLanguage');
        $Tariff = Normalization::normalizeData('Tariff');
        $TariffDescription = Normalization::normalizeData('TariffDescription');
        $MessageToWarnBuyer = Normalization::normalizeData('MessageToWarnBuyer');
        $CustomsDesriptionAndTariff = Normalization::normalizeData('CustomsDesriptionAndTariff');
        $ShippCustomsReference = Normalization::normalizeData('ShippCustomsReference');

        // Traducción de los campos.
        $string_from_db=$this->custom_processing_dao->getField('TranslatableInput');
        $TranslatableInput=CorreosOficialUtils::translateStringsToDB($string_from_db->value,
                                                $FormSwitchLanguage,
                                                $TranslatableInput);

        // Los metemos en un array
        $fields=array(
            'DefaultCustomsDescription' => $DefaultCustomsDescription,
            'TranslatableInput' => $TranslatableInput,
            'FormSwitchLanguage' => $FormSwitchLanguage,
            'Tariff' => $Tariff,
            'TariffDescription' => $TariffDescription,
            'MessageToWarnBuyer' => $MessageToWarnBuyer,
            'ShippCustomsReference' => $ShippCustomsReference

        );
         
        if (isset($CustomsDesriptionAndTariff[0])) {
            if ($CustomsDesriptionAndTariff[0] == 0) {
                $fields['DescriptionRadio'] = 'on';
                $fields['TariffRadio'] = '';
            } elseif ($CustomsDesriptionAndTariff[0] == 1) {
                $fields['TariffRadio'] = 'on';
                $fields['DescriptionRadio'] = '';
            }
        }

        // Clave del registro
        $this->custom_processing_dao->updateFieldsSetRecord($fields);
    }
}
