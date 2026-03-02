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
    $('#history #content .table tbody tr a').each(function () {
        href = $(this);
        var hrefs = $.trim(this.href);
        var textTofind = "pdf-invoice";

        if (hrefs.indexOf(textTofind) != -1) {
            id_order_ajax = $(this).attr("href").split("id_order=").pop();
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: ppwf_ajax_url,
                async: false,
                data: 'ajax=true&action=IsOrderPpwf&id_order=' + id_order_ajax + '&token=' + prestashop.static_token,
                dataType: "json",
                success: function (res) {
                    if (res.is_ppwf)
                    {
                        href.attr('href', res.href);
                    }
                }
            });
        }
    });

    $('#order-detail #content #order-infos a').each(function () {
        var hrefs = $.trim(this.href);
        var textTofind = "pdf-invoice";
        href = $(this);
        if (hrefs.indexOf(textTofind) != -1) {
            id_order_ajax = $(this).attr("href").split("id_order=").pop();
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: ppwf_ajax_url,
                async: false,
                data: 'ajax=true&action=IsOrderPpwf&id_order=' + id_order_ajax + '&token=' + prestashop.static_token,
                dataType: "json",
                success: function (res) {
                    if (res.is_ppwf)
                    {
                        href.attr('href', res.href);
                    }
                }
            });
        }
    });
});
