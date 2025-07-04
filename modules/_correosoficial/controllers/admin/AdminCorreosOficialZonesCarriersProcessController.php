<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/DetectPlatform.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/config.inc.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialZonesCarriersDao.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once dirname(__FILE__) . '/../../vendor/ecommerce_common_lib/functions.php';

class AdminCorreosOficialZonesCarriersProcessController extends ModuleAdminController
{
    /**
     * @var module
     */
    public $module;

    public function __construct()
    {
        parent::__construct();

        $dao = new CorreosOficialDAO();

        foreach ($_POST as $input => $value) {
            $id_product = Normalization::normalizeData($value, 'value');
            $data_explode_from_input = explode("_", $input);
            $id_zone = Normalization::normalizeData($data_explode_from_input[1], 'value');
            $id_carrier = Normalization::normalizeData($data_explode_from_input[2], 'value');
            
            if (!empty($id_product)) {
                $dao->updateCarrierProduct($id_product, $id_zone, $id_carrier);
            } else {
                $dao->deleteCarrierProductsById($id_carrier, $id_zone);
            }
        }
    }
}
