<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/DetectPlatform.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/config.inc.php';

require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/functions.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/Dao/CorreosOficialActiveCustomersDao.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/Commons/Normalization.php';

class AdminCorreosOficialActiveCustomersController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();

        $active_customer_dao = new CorreosOficialActiveCustomersDao();

        $company = Normalization::normalizeData('company');
        $active = Normalization::normalizeData('active');
        $action = Tools::getValue('action');
        
        if ($action=='updateActiveCustomers') {
            $active_customer_dao->updateActiveCustomers($company, $active);
        }
        else if ($action=='getActivesCustomers'){
            $active_customer_dao->getActivesCustomers();
        }

    }
}
