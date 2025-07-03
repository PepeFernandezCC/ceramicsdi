<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SAS Comptoir du Code
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SAS Comptoir du Code is strictly forbidden.
 * In order to obtain a license, please contact us: contact@comptoirducode.com
 *
 * @author    Vincent - Comptoir du Code
 * @copyright Copyright(c) 2015-2022 SAS Comptoir du Code
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * @package   cdc_googletagmanager
 */

class cdc_googletagmanagerAsyncModuleFrontController extends ModuleFrontController
{
	private $dataLayer = null;
	private $cdc_gtm = null;

	public function __construct()
	{
		// if page is called in https, force ssl
		if (Tools::usingSecureMode()) {
			$this->ssl = true;
		}
		return parent::__construct();
	}


	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
	    $action = Tools::getValue('action');
	    if(!empty($action)) {
            $this->cdc_gtm = new cdc_googletagmanager();
            $this->dataLayer = new Gtm_DataLayer($this->cdc_gtm, $this->cdc_gtm->datalayer_format);

	        switch ($action) {
                case 'user':
                    $this->dataLayer = $this->cdc_gtm->addUserInfosToDatalayer($this->dataLayer);
                    break;
                case 'cart-add':
                case 'cart-remove':
                    $this->dataLayer = $this->cdc_gtm->getDataLayerCartAction(
                        (int) Tools::getValue('id'),
                        (int) Tools::getValue('id_attribute'),
                        $action == 'cart-remove' ? 'remove' : 'add',
                        (int) Tools::getValue('qtity')
                    );
                    break;
                case 'product-click':
                    $this->dataLayer = $this->cdc_gtm->productClick(
                        (int) Tools::getValue('id'),
                        (int) Tools::getValue('id_attribute')
                    );
                    break;
            }
        }
	}


	public function display()
	{
	    if($this->dataLayer) {
            echo $this->dataLayer->toJson();
        } else {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' 301 Moved Permanently', false, 301);
            header("Location: ".Context::getContext()->link->getBaseLink());
            exit();
        }
	}

}