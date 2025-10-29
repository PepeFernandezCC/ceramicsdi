<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from ScaleDEV.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from ScaleDEV is strictly forbidden.
 * In order to obtain a license, please contact us: contact@scaledev.fr
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise à une licence commerciale
 * concédée par la société ScaleDEV.
 * Toute utilisation, reproduction, modification ou distribution du présent
 * fichier source sans contrat de licence écrit de la part de ScaleDEV est
 * expressément interdite.
 * Pour obtenir une licence, veuillez nous contacter : contact@scaledev.fr
 * ...........................................................................
 * @author ScaleDEV <contact@scaledev.fr>
 * @copyright Copyright (c) ScaleDEV - 12 RUE CHARLES MORET - 10120 SAINT-ANDRE-LES-VERGERS - FRANCE
 * @license Commercial license
 * @package Scaledev\Adeo
 * Support: support@scaledev.fr
 */

use Scaledev\Adeo\Core\Controller\Admin\AbstractModuleAdminController;
use Scaledev\Adeo\Core\Tools;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__).'/../../autoload.php');

/**
 * Class AdminSdevAdeoIndexController
 *
 * @package Scaledev\Adeo
 * @author Pascal Fischer <contact@scaledev.fr>
 */
final class AdminSdevAdeoFilterController extends AbstractModuleAdminController
{
    const TEMPLATE = 'filter';

    /**
     * @inheritdoc
     */
    public function renderList()
    {
        Media::addJsDef(array(
            'deleteMessage' => $this->module->l('Do you really want to delete this filter ?')
        ));

        $this->context->smarty->assign(array(
            'productFilters' => SdevAdeoFilter::findAll(),
        ));

        return parent::renderList();
    }

    public function ajaxProcessSaveFilter()
    {
        $errorList = [
            'hasError' => false,
            'errorMessage' => []
        ];

        try {
            $request = Tools::getRequest();

            if ($request['idFilter']) {
                $filter = new SdevAdeoFilter($request['idFilter']);
            } else {
                $filter = new SdevAdeoFilter();
            }

            if (array_key_exists('filterType', $request)) {
                $filter->setFilterType($request['filterType']);
            }
            if (array_key_exists('filterTarget', $request)) {
                $filter->setFilterTarget($request['filterTarget']);
            }
            if (array_key_exists('filterValue', $request)) {
                $filter->setFilterValue($request['filterValue']);
            }

            $filter->save();
            $errorList['idFilter'] = $filter->getId();
            $errorList['errorMessage'][] = $this->module->l('Filter successfully created.');
        } catch (Exception $e) {
            $errorList['hasError'] = true;
            $errorList['errorMessage'][] = $e->getMessage();
        }

        die(json_encode($errorList));
    }

    public function ajaxProcessDeleteFilter()
    {
        $errorList = [
            'hasError' => false,
            'errorMessage' => []
        ];

        try {
            (new SdevAdeoFilter(Tools::getRequest()['idFilter']))->delete();
            $errorList['errorMessage'][] = $this->module->l( "Filter successfully deleted.");
        } catch (Exception $e) {
            $errorList['hasError'] = true;
            $errorList['errorMessage'][] = $e->getMessage();
        }

        die(json_encode($errorList));
    }

    public function ajaxProcessEditFilter()
    {
        $filter = Db::getInstance()->getRow(sprintf(
            'SELECT * FROM `%s` WHERE id = %s;',
            SdevAdeoFilter::getCompleteTableName(),
            Tools::getRequest()['filterId']
        ));

        die(json_encode($filter));
    }
}
