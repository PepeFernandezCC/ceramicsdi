<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/Cex/CexRest.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/DetectPlatform.php';
require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/config.inc.php';

require_once dirname(__FILE__).'/../../vendor/ecommerce_common_lib/functions.php';

class AdminCEXRestRequestController extends ModuleAdminController
{
    /**
     * @var module
     */
    public $module;


    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        parent::__construct();

        return new CEXRest();
    }
}
