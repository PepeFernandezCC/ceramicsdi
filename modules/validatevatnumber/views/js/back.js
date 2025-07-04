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
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */

$(document).ready(function() {
    if ($('#VALIDATEVATNUMBER_COUNTRY_off').is(':checked') || $('#VALIDATEVATNUMBER_MANUAL_MODE_off').is(':checked')) {
        $('#validateVATTabs > ul > li:nth-child(2)').hide();
    }
    if ($('#VALIDATEVATNUMBER_COUNTRY_on').is(':checked')) {
        $('#validateVATTabs > ul > li:nth-child(2)').show();
    } else {
        $('#validateVATTabs > ul > li:nth-child(2)').hide();
    }
    $("input[name = 'VALIDATEVATNUMBER_COUNTRY']").click(function() {
        if ($('#VALIDATEVATNUMBER_COUNTRY_on').is(':checked')) {
            $('#validateVATTabs > ul > li:nth-child(2)').fadeIn(200);
        } else {
            $('#validateVATTabs > ul > li:nth-child(2)').fadeOut(200);
        }
    });
    /**
     * Switch tabs content and active state.
     * @type {number}
     */
    var previousActiveTabIndex = 0;
    $("#validateVATTabs > ul > li > a").on('click', function () {
        var $this = $(this);
        var tabClicked = $this.closest('li').data("tab-index");
        if(tabClicked != previousActiveTabIndex) {
            $(".validateVATTabsContent .validateVATTab").each(function () {
                if($(this).data("tab-index") == tabClicked) {
                    $(".validateVATTab").hide();
                    $(this).show();
                    $(".validateVATUl li").removeClass('active');
                    $this.closest('li').addClass('active');
                    previousActiveTabIndex = $(this).data("tab-index");
                    return;
                }
            });
        }
    });

    if ($('#VALIDATEVATNUMBER_MANUAL_MODE_on').is(':checked')) {
        $("input[name = 'VALIDATEVATNUMBER_COUNTRY']").attr("disabled", true);
        $("#VALIDATEVATNUMBER_COUNTRY_off").attr("checked", true);
        $('#validateVATTabs > ul > li:nth-child(2)').hide();
    } else {
        $("input[name = 'VALIDATEVATNUMBER_COUNTRY']").removeAttr("disabled");
    }

    $("input[name = 'VALIDATEVATNUMBER_MANUAL_MODE']").click(function() {
        if ($('#VALIDATEVATNUMBER_MANUAL_MODE_on').is(':checked')) {
            $("input[name = 'VALIDATEVATNUMBER_COUNTRY']").attr("disabled", true);
            $("#VALIDATEVATNUMBER_COUNTRY_off").attr("checked", true);
            $('#validateVATTabs > ul > li:nth-child(2)').fadeOut(200);
        } else {
            $("input[name = 'VALIDATEVATNUMBER_COUNTRY']").removeAttr("disabled");
        }
    });
});

function save_country_group_function(k,country_id) {
    var customer_selected_group = $( "#order_admin_select_carrier_" + k ).val();
    $.ajax({
        url: save_country_group,
        data: ({
            ajax: true,
            country_id: country_id,
            customer_selected_group: customer_selected_group,
            action: 'SaveCountryGroup',
        }),
        dataType: "json",
        success : function(data){
            if(data.sent) {
                return $.growl.notice({
                    title: "",
                    size: "large",
                    message: ajax_ok_message
                });
            } else {
                return $.growl.error({
                    title: "",
                    size: "large",
                    message: ajax_not_ok_message
                });
            }
        }
    });
}