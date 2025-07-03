<?php
/**
 * GcInvoiceWTax.
 *
 * @author    Grégory Chartier <hello@gregorychartier.fr>
 * @copyright 2018 Grégory Chartier (https://www.gregorychartier.fr)
 * @license   Commercial license see license.txt
 *
 * @category  Prestashop
 * @category  Module
 */
class GcInvoiceWTaxManager implements TaxManagerInterface
{
    public static function isAvailableForThisAddress(Address $my_address)
    {
        $my_context = Context::getContext();

        if ($my_address && Validate::isLoadedObject($my_address)) {
            $my_id_customer = (int) $my_address->id_customer;
        }

        if ($my_context && $my_context->customer) {
            $my_id_customer = (int) $my_context->customer->id;
        }

        if (empty($my_id_customer)) {
            return false;
        }

        $my_customer_id_group = Customer::getDefaultGroupId($my_id_customer);

        $my_tax_excl_groups = explode('-', Configuration::get('GCIWT_GROUP'));

        if (!empty($my_context->cart)) {
            $my_delivery_address = new Address($my_context->cart->id_address_delivery);
        } elseif ($id_cart = Tools::getValue('id_cart')) {
            $id_cart = Tools::getValue('id_cart');
            $cart = new Cart((int) $id_cart);
            $my_delivery_address = new Address($cart->id_address_delivery);
        } else {
            $my_delivery_address = new Address(Tools::getValue('id_address_delivery'));
        }

        if (version_compare(_PS_VERSION_, '1.6', '>') && isset($my_context->employee) && $my_context->employee->isLoggedBack()) {
            $my_customer_delivery_id_country = $my_address->id_country;
        } else {
            $my_customer_delivery_id_country = $my_delivery_address->id_country;
        }

        $my_tax_incl_country = Configuration::get('GCIWT_COUNTRY');

        if (in_array($my_customer_id_group, $my_tax_excl_groups)) {
            if ($my_customer_delivery_id_country != $my_tax_incl_country) {
                return 1;
            }
        }

        return 0;
    }

    public function getTaxCalculator()
    {
        $my_tax = new Tax();
        $langs = Language::getLanguages(true, false, true);
        foreach ($langs as $id_lang) {
            $my_tax->name[$id_lang] = '0.0%';
        }
        $my_tax->rate = 0;

        return new TaxCalculator([$my_tax]);
    }
}
