<?php
/**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code.
*
*  @author    Active Design <office@activedesign.ro>
*  @copyright 2018 Active Design
*  @license   LICENSE.txt
 */

class SaveCountryGroupController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function ajaxProcessSaveCountryGroup()
    {
        $country_id = Tools::getValue('country_id');
        $selected_group_id = Tools::getValue('customer_selected_group');
        Configuration::updateValue('VALIDATEVATNUMBER_COUNTRY', 1);
        if ($selected_group_id != 0) {
            if ($country_id) {
                Db::getInstance()->insert('validatevatnumber_country', array('id_country'=> (int)$country_id, 'id_customer_group'=> (int)$selected_group_id), false, true, Db::REPLACE);
                die(json_encode(array("sent" => true)));
            } else {
                die(json_encode(array("sent" => false)));
            }
        } else {
            Db::getInstance()->delete('validatevatnumber_country', '`id_country` = "'.(int)$country_id.'"');
            die(json_encode(array("sent" => true)));
        }
    }
}
