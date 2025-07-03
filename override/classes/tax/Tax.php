<?php
class Tax extends TaxCore
{
    
    
    /*
    * module: validatevatnumber
    * date: 2024-05-28 09:28:55
    * version: 2.4.0
    */

    
    public static function excludeTaxeOption()
    {
        if (Context::getContext()->cart) {
            if (Address::getCountryAndState(Context::getContext()->cart->id_address_invoice > 0) && (Address::getCountryAndState(Context::getContext()->cart->id_address_invoice)['id_country'] !== Configuration::get('VALIDATEVATNUMBER_COUNTRY_ID'))) {
                $check = Db::getInstance()->getValue('SELECT `vat_number_status` FROM `' . _DB_PREFIX_ . 'validatevatnumber` WHERE `id_address` = "' . Context::getContext()->cart->id_address_invoice . '"');
                if ($check) {
                    Configuration::set('PS_TAX', false);
                    return true;
                }
            }
        }
        
        return !Configuration::get('PS_TAX');
       
    }
    

    public static function getStandardTaxByCountryId($id_country)
    {
        $query = 'SELECT t.rate 
        FROM ps_tax_rule tr
        JOIN ps_tax t ON tr.id_tax = t.id_tax
        WHERE tr.id_tax_rules_group = 28
        AND tr.id_country = '.$id_country;
        
        $rate = Db::getInstance()->getValue($query); // Obtiene el valor de la consulta
        
        if ($rate !== false) {
            return (float) $rate; 
        } 
        
        return 0;
    }
}
