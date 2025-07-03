<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/DetectPlatform.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/config.inc.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/Dao/CorreosOficialUserConfigurationDao.php';	
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/functions.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/CorreosOficialUtils.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/Commons/Normalization.php';

class AdminCorreosOficialUserConfigurationProcessController extends ModuleAdminController
{
    /**
     * @var module
     */
    public $module;

    private $user_configuration_dao;

    const MAX_LOGO_SIZE = 200000;

    public function __construct()
    {
        try {

            /*$betatester = false;
            if (Tools::getIsset('betatester') && Tools::getValue('betatester') === 'on') {
                $betatester = true;
                Db::getInstance(_PS_USE_SQL_SLAVE_)->update('correos_oficial_configuration',['value' => 1], 'name = "betatester" AND type = "analitica"');
            } else {
                Db::getInstance(_PS_USE_SQL_SLAVE_)->update('correos_oficial_configuration',['value' => 0], 'name = "betatester" AND type = "analitica"');
            }*/

            // Obtenemos un objetoDao
            $this->user_configuration_dao = new CorreosOficialUserConfigurationDao();

            parent::__construct();

            if (isset($_FILES['UploadLogoLabels']) && $_FILES['UploadLogoLabels']['size'] > self::MAX_LOGO_SIZE) {
                throw new Exception($this->l('Error 12041: Image too Big.'));
            }

            if (isset($_FILES['UploadLogoLabels']) && !empty($_FILES['UploadLogoLabels'])) {
                $file_name = $_FILES['UploadLogoLabels'];
            }
            // Obtenemos campos de los formularios
            $DefaultPackages = Normalization::normalizeData('DefaultPackages');
            $CashOnDeliveryMethod = Tools::getValue('CashOnDeliveryMethod');
            $DefaultLabel = Normalization::normalizeData('DefaultLabel');
            $ActivateTrackingLink = Normalization::normalizeData('ActivateTrackingLink');
            $ActivateWeightByDefault = Normalization::normalizeData('ActivateWeightByDefault');
            $WeightByDefault = Normalization::normalizeData('WeightByDefault');
            $ActivateDimensionsByDefault = Normalization::normalizeData('ActivateDimensionsByDefault');
			$DimensionsByDefaultHeight = Normalization::normalizeData('DimensionsByDefaultHeight');
			$DimensionsByDefaultWidth = Normalization::normalizeData('DimensionsByDefaultWidth');
			$DimensionsByDefaultLarge = Normalization::normalizeData('DimensionsByDefaultLarge');
            $AgreeToAlterReferences = Normalization::normalizeData('AgreeToAlterReferences');
            $ShowLabelData = Normalization::normalizeData('ShowLabelData');
            $CustomerAlternativeText = Normalization::normalizeData('CustomerAlternativeText');
            $LabelAlternativeText = Normalization::normalizeData('LabelAlternativeText');
            $GoogleMapsApi = Normalization::normalizeData('GoogleMapsApi', 'no_uppercase');
            $ChangeLogoOnLabel = Normalization::normalizeData('ChangeLogoOnLabel');
            $FormSwitchLanguage = Normalization::normalizeData('FormSwitchLanguage');
            $LabelObservations = Normalization::normalizeData('LabelObservations');
            $SSLAlternative = Normalization::normalizeData('SSLAlternative');
            $ShowShippingStatusProcess = Normalization::normalizeData('ShowShippingStatusProcess');
            $ShipmentPreregistered = Normalization::normalizeData('ShipmentPreregistered');
            $ShipmentInProgress = Normalization::normalizeData('ShipmentInProgress');
            $ShipmentDelivered = Normalization::normalizeData('ShipmentDelivered');
            $ShipmentCanceled = Normalization::normalizeData('ShipmentCanceled');
            $ShipmentReturned = Normalization::normalizeData('ShipmentReturned');
            $CronInterval = Normalization::normalizeData('CronInterval');
            $ActivateAutomaticTracking = Normalization::normalizeData('ActivateAutomaticTracking');
            
            if (isset($_FILES['UploadLogoLabels']) && !empty($_FILES['UploadLogoLabels'])) {
                $UploadLogoLabels = $file_name['name'];
            }
            
            // Los metemos en un array
            $fields=array(
                'DefaultPackages' => $DefaultPackages,
                'CashOnDeliveryMethod' => $CashOnDeliveryMethod,
                'DefaultLabel' => $DefaultLabel,
                'ActivateTrackingLink' => $ActivateTrackingLink,
                'ActivateWeightByDefault' => $ActivateWeightByDefault,
                'WeightByDefault' => $WeightByDefault,
                'ActivateDimensionsByDefault' => $ActivateDimensionsByDefault,
				'DimensionsByDefaultHeight' => ($ActivateDimensionsByDefault == 'on') ? $DimensionsByDefaultHeight : 0,
				'DimensionsByDefaultWidth' => ($ActivateDimensionsByDefault == 'on') ? $DimensionsByDefaultWidth : 0,
				'DimensionsByDefaultLarge' => ($ActivateDimensionsByDefault == 'on') ? $DimensionsByDefaultLarge : 0,
                'AgreeToAlterReferences' => $AgreeToAlterReferences,
                'ShowLabelData' => $ShowLabelData,
                'CustomerAlternativeText' => $CustomerAlternativeText,
                'LabelAlternativeText' => $LabelAlternativeText,
                'GoogleMapsApi' => $GoogleMapsApi,
                'ChangeLogoOnLabel' => $ChangeLogoOnLabel,
                'FormSwitchLanguage' => $FormSwitchLanguage,
                'LabelObservations' => $LabelObservations,
                'SSLAlternative'    => $SSLAlternative,
                'ShowShippingStatusProcess' => $ShowShippingStatusProcess,
                'ShipmentPreregistered' => $ShipmentPreregistered,
                'ShipmentInProgress' => $ShipmentInProgress,
                'ShipmentDelivered' => $ShipmentDelivered,
                'ShipmentCanceled' => $ShipmentCanceled,
                'ShipmentReturned' => $ShipmentReturned,
                'CronInterval' => $CronInterval,
                'ActivateAutomaticTracking' => $ActivateAutomaticTracking
            );

            if (substr(Tools::getValue('BankAccNumberAndIBAN'), 0, 4) != '****') {
				$fields['BankAccNumberAndIBAN'] = Crypto::encrypt(Normalization::normalizeData('BankAccNumberAndIBAN','nospaces'));
            }

            // Obtenemos un objetoDao
            $this->user_configuration_dao = new CorreosOficialUserConfigurationDao();

            if (isset($_FILES['UploadLogoLabels']) && !empty($_FILES['UploadLogoLabels'])) {
                $fields['UploadLogoLabels'] = $UploadLogoLabels;
            }

            $fields['ChangeLogoOnLabel'] = !isset($_REQUEST['ChangeLogoOnLabel']) ? '' : 'on';
            $fields['ActivateWeightByDefault'] = !isset($_REQUEST['ActivateWeightByDefault']) ? '' : 'on';
            $fields['ActivateDimensionsByDefault'] = !isset($_REQUEST['ActivateDimensionsByDefault']) ? '' : 'on';
            $fields['AgreeToAlterReferences'] = !isset($_REQUEST['AgreeToAlterReferences']) ? '' : 'on';
            $fields['ActivateTrackingLink'] = !isset($_REQUEST['ActivateTrackingLink']) ? '' : 'on';
            $fields['CustomerAlternativeText'] = !isset($_REQUEST['CustomerAlternativeText']) ? '' : 'on';
            $fields['SSLAlternative'] = !isset($_REQUEST['SSLAlternative']) ? '' : 'on';
            $fields['ShowShippingStatusProcess'] = !isset($_REQUEST['ShowShippingStatusProcess']) ? '' : 'on';

            $fields['ErrorLogoLabels'] = '';

            if (is_array($_FILES) && !empty($_FILES['UploadLogoLabels']) && is_uploaded_file($_FILES['UploadLogoLabels']['tmp_name'])) {
                $sourcePath = $_FILES['UploadLogoLabels']['tmp_name'];

                $targetPath = '';
                $result = Normalization::filterFiles($_FILES['UploadLogoLabels']['name']);
                if (!strstr($result, 'Error: 12010')) {
                    $targetPath = "../modules/correosoficial/media/logo_label/" . $result;
        
                    $moved = move_uploaded_file($sourcePath, $targetPath);
                    
                    if (!$moved) {
                        throw new Exception($this->l('Could not upload logo. Check permissions of the /correosofficial/media path of the module or plugin'));
                    }
                } else {
                    $fields['ErrorLogoLabels'] = $result;
                }
            }
            
            // Clave del registro
            $this->user_configuration_dao->updateFieldsSetRecord($fields);

            $obj = array();
            (new Analitica())->configurationCall(false);
            die(json_encode($obj));

        } catch (Exception $e) {
            $obj = array('error' => 'Error',
                          'desc' => $this->l('Error 12040: An error has ocurred when submitting data. ', 'AdminCorreosOficialUserConfiguration').$e->getMessage(), 'AdminCorreosOficialUserConfiguration');
                
            die(json_encode($obj));
        }
    }
}
