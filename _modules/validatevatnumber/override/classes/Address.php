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

class Address extends AddressCore
{
    public function validateController($htmlentities = true)
    {
        if (!$this->id) {
            $this->id = Tools::getValue('id_address');
        }
        if (Tools::substr(_PS_VERSION_, 0, 3) != '1.7') {
            if ((int)Configuration::get('VALIDATEVATNUMBER_MANUAL_MODE') == 0 && (int)Configuration::get('VALIDATEVATNUMBER_COUNTRY') == 1) {
                $check_vat_number_default_group = Module::getInstanceByName('validatevatnumber')->getSelectedCountryDefaultGroup(Tools::getValue('id_country'));
            } else {
                $check_vat_number_default_group = Configuration::get('VALIDATEVATNUMBER_DEFAULT_GROUP');
            }
            //Db::getInstance()->update('customer_group', array('id_group' => (int)$check_vat_number_default_group), 'id_customer = ' . (int)Context::getContext()->customer->id . '');
            if (Configuration::get('VALIDATEVATNUMBER_MANUAL_MODE') == false) {
                $errors = parent::validateController($htmlentities);
                if (Module::isEnabled('validatevatnumber') && $validateVatNumber = Module::getInstanceByName('validatevatnumber')) {
                    if ($this->vat_number) {
                        $vat_number_error = $validateVatNumber->verifyCountryAndVat($this->id_country, $this->vat_number);
                        if ($vat_number_error !== true) {
                            $errors['vatcountry'] = $vat_number_error;
                        }
                    }
                    if ($this->vat_number) {
                        $vat_number_error = $validateVatNumber->verifyVatNumberOnline($this->vat_number);
                        if ($vat_number_error !== true) {
                            $errors['vat'] = $vat_number_error;
                        }
                    }
                }

                if ($errors) {
                    return $errors;
                } else {
                    $this->save();
                    if ($this->vat_number && ($this->id_country !== Configuration::get('VALIDATEVATNUMBER_COUNTRY_ID') )) {
                        Db::getInstance()->insert('validatevatnumber', array('vat_number_status' => (int)1, 'id_address' => $this->id), false, true, Db::REPLACE);
                    } else {
                        Db::getInstance()->insert('validatevatnumber', array('vat_number_status' => (int)0, 'id_address' => $this->id), false, true, Db::REPLACE);
                    }
                    return null;
                }
            } else {
                $errors = parent::validateController($htmlentities);
                if (!Configuration::get('VATNUMBER_MANAGEMENT') || !Configuration::get('VATNUMBER_CHECKING')) {
                    return $errors;
                }
                include_once(_PS_MODULE_DIR_ . 'vatnumber/vatnumber.php');
                if (class_exists('VatNumber', false)) {
                    return array_merge($errors, VatNumber::WebServiceCheck($this->vat_number));
                }
                return $errors;
            }
        }
    }

    public function delete()
    {
        if (Validate::isUnsignedId($this->id_customer)) {
            Customer::resetAddressCache($this->id_customer, $this->id);
        }

        Db::getInstance()->delete('validatevatnumber', 'id_address = '.$this->id.'');

        if (!$this->isUsed()) {
            return parent::delete();
        } else {
            $this->deleted = true;
            return $this->update();
        }
    }

    public function add($autodate = true, $null_values = false)
    {
        $validateVatNumber = Module::getInstanceByName('validatevatnumber');
        $return = parent::add($autodate, $null_values);
        $administratorList = Configuration::get('VALIDATEVATNUMBER_ADMINMAILS');
        $administrators = explode(';', $administratorList);
        if ((int)Configuration::get('VALIDATEVATNUMBER_MANUAL_MODE') == 1) {
            Db::getInstance()->insert('validatevatnumber', array('vat_number_status' => '2', 'id_address' => $this->id));
        } else {
            Db::getInstance()->insert('validatevatnumber', array('vat_number_status' => $validateVatNumber->verifyVatNumberOnline($this->vat_number), 'id_address' => $this->id));
        }
        if ((int)Configuration::get('VALIDATEVATNUMBER_ADMINNOTIFY') == 1) {
            if ($administrators) {
                foreach ($administrators as $admin) {
                    Module::getInstanceByName('validatevatnumber')->sendNotificationAdmin($admin, $this->firstname, $this->lastname, $this->vat_number);
                }
            }
        }
        return $return;
    }
}
