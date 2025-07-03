<?php
/**
 * 2007-2021 PrestaShop
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2021 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
require_once dirname(__FILE__) . '/../../classes/CorreosOficialUtilitiesDataTable.php';

class AdminCorreosOficialUtilitiesAjaxController extends ModuleAdminController
{
    public $ajax = true;

    private $cudt;

    public function __construct()
    {
        parent::__construct();
        $this->cudt = new CorreosOficialUtilitiesDataTable();
    }

    public function initContent()
    {
        $data = [];
        $from = date('Y-m-d');
        $to = date('Y-m-d');
        $tab = Tools::getValue('actionTab');
        $datatable = Tools::getValue('datatable');
        $perPage = 10;
        $onlyCorreos = false;
        $searchByLabelingDate = filter_var(Tools::getValue('SearchByLabelingDate'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
		$searchBySender = Tools::getValue('SearchBySender');

        if (Tools::getIsset('onlyCorreos') && Tools::getValue('onlyCorreos') == 'active') {
            $onlyCorreos = true;
        }

        if (Tools::getIsset('FromDateOrdersReg') || !empty(Tools::getValue('FromDateOrdersReg'))) {
            $from = date('Y-m-d',strtotime(Tools::getValue('FromDateOrdersReg')));
        }
        if (Tools::getIsset('ToDateOrdersReg') || !empty(Tools::getValue('ToDateOrdersReg'))) {
            $to = date('Y-m-d',strtotime(Tools::getValue('ToDateOrdersReg')));
        }
        
        if (Tools::getIsset('length')) {
            $perPage = Tools::getValue('length');
        }
        
        $result = $this->gestionTab($tab, $from, $to, $datatable, $searchByLabelingDate, $searchBySender, $onlyCorreos);
    }

    protected function gestionTab($tab, $from, $to, $datatable, $searchByLabelingDate, $searchBySender, $onlyCorreos = false)
    {
        switch ($tab) {
            case 'GestionDataTable':
                return $this->cudt->getDataFromDataTables($from, $to);
            case 'EtiquetasDataTable':
                return $this->cudt->getDataFromDataTablesForReprintAndPickups($from, $to, $datatable, $onlyCorreos);
            case 'ResumenDataTable':
                return $this->cudt->getDataFromDataTablesForResumen($from, $to, $searchByLabelingDate, $searchBySender);
            case 'DocAduaneraDataTable':
                return $this->cudt->getDataFroShippingCustomDoc($from, $to);
            default:
                die('ERROR 17100: LLamada no válida');
        }
    }
}
