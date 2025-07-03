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

class Tax extends TaxCore
{
    /**
     * Checking our table ps_validatevatnumber for validity (vat_number_status) and sets PS_TAX if it's true
     *
     * @return bool
     */
    public static function excludeTaxeOption()
    {
        if (Context::getContext()->cart && (Address::getCountryAndState(Context::getContext()->cart->id_address_invoice)['id_country'] !== Configuration::get('VALIDATEVATNUMBER_COUNTRY_ID'))) {
            $check = Db::getInstance()->getValue('SELECT `vat_number_status` FROM `' . _DB_PREFIX_ . 'validatevatnumber` WHERE `id_address` = "' . Context::getContext()->cart->id_address_invoice . '"');
            if ($check) {
                Configuration::set('PS_TAX', false);
                return true;
            }
        }
        return !Configuration::get('PS_TAX');
    }
}
