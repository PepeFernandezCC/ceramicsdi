<?php
/**
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
*/

class OutvioFrontAjaxModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function saveSelectedPointAction()
    {
        $point_info = Tools::getValue('point_info');
        $this->response = Outvio::saveSelectedPoint($point_info);
        echo $this->response;
        die;
    }

    protected function deleteSelectedPointAction()
    {
        $this->response = Outvio::saveSelectedPoint('', true);
        echo $this->response;
        die;
    }

    protected function getLocationsAction()
    {
        $address = Tools::getValue('address');
        $this->response = Outvio::requestLocations($address);
        echo $this->response;
        die;
    }

    protected function getCarrierSearchPointsAction()
    {
        $this->response = Outvio::getCarrierSearchPointsAjax();
        if (_PS_VERSION_ >= '8.0.0') {
            echo json_encode($this->response);
        } else {
            echo Tools::jsonEncode($this->response);
        }
        die;
    }

    protected function getCarrierPointsAction()
    {
        $this->response = Outvio::getCarrierPointsAjax();
        if (_PS_VERSION_ >= '8.0.0') {
            echo json_encode($this->response);
        } else {
            echo Tools::jsonEncode($this->response);
        }
        die;
    }

    public function postProcess()
    {
        if (!is_null(Shop::getContextShopID())) {
            $this->shop_id = Shop::getContextShopID();
        }

        $this->response = '';
        if (Tools::isSubmit('action')) {
            $actionName = Tools::getValue('action') . 'Action';
            if (method_exists($this, $actionName)) {
                $this->$actionName();
            }
        }
        die;
    }
}
