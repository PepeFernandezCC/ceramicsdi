/*
 *
 * NOTICE OF LICENSE
 *
 * @category payment_gateways
 * @author www.4webs.es
 * @copyright 4webs 2016
 * @version 5.1.4
 *
 * 
 *  paypalwithfee
 *  Languages: EN
 *  PS version: 1.7
 *
 */

$(document).ready(function () {

    $('#PPAL_CUSTOM_INVOICE_on').click(function () {
        $('#PPAL_TAX_FEE').removeAttr('disabled');
    });

    $('#PPAL_CUSTOM_INVOICE_off').click(function () {
        $('#PPAL_TAX_FEE').attr('disabled', 'disabled');
    });

});