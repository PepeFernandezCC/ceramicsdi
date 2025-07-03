<?php
/**
 * Google Merchant Center
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class GMerchantCenterProFlyModuleFrontController extends ModuleFrontController
{
    /**
     * method manage post data
     *
     * @return bool
     *
     * @throws Exception
     */
    public function postProcess()
    {
        // get the token
        $sToken = \Tools::getValue('token');

        if ($sToken == \GMerchantCenterPro::$conf['GMCP_FEED_TOKEN']) {
            // use case - handle to generate XML files
            $_POST['sAction'] = \Tools::getIsset('sAction') ? \Tools::getValue('sAction') : 'generate';
            $_POST['sType'] = \Tools::getIsset('sType') ? \Tools::getValue('sType') : 'flyOutput';

            $this->module->getContent();

            // No return text to not break the fly output
            exit;
        } else {
            exit($this->module->l('Internal server error! (security error)', 'cron'));
        }
    }
}
