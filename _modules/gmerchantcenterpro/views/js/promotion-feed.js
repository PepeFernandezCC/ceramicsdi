/*
*
* @author    BusinessTech.fr - https://www.businesstech.fr
* @copyright Business Tech - https://www.businesstech.fr
* @license   Commercial
*
*           ____    _______
*          |  _ \  |__   __|
*          | |_) |    | |
*          |  _ <     | |
*          | |_) |    | |
*          |____/     |_|
*
*/
var GmcProPromotionFeed = {

    init: function() {
        // Initialize arrays for elements to show/hide
        var aShow = [];
        var aHide = [];

        // Cache jQuery selectors for better performance
        var $optionName = $("input[type=radio][name=bt_option-name]:checked");
        var $optionDate = $("input[type=radio][name=bt_option-date]:checked");
        var $optionMinAmount = $("input[type=radio][name=bt_option-min-amount]:checked");
        var $optionValue = $("input[type=radio][name=bt_option-value]:checked");
        var $optionType = $("input[type=radio][name=bt_option-type]:checked");
        var $optionCumulable = $("input[type=radio][name=bt_option-cumulable]:checked");

        // Initial setup based on configuration
        if ($optionName.val() === "true") {
            aShow.push('#bt_discount-name-group,#gmcp-example-info');
        } else {
            aHide.push('#bt_discount-name-group');
        }

        // Build show/hide arrays based on current values
        $optionName.val() === "true" ? aShow.push('#bt_discount-name-group') : aHide.push('#bt_discount-name-group');
        $optionMinAmount.val() === "true" ? aShow.push('#bt_min-amount-group') : aHide.push('#bt_min-amount-group');
        $optionValue.val() === "true" ? aShow.push('#bt_value-group,#gmcp_info_value') : aHide.push('#bt_value-group,#gmcp_info_value');
        $optionType.val() === "true" ? aShow.push('#bt_discount-type-group,#bt_discount-type-group-amount') : aHide.push('#bt_discount-type-group,#bt_discount-type-group-amount');
        $optionDate.val() === "true" ? aShow.push('#bt_date-group') : aHide.push('#bt_date-group');
        $optionCumulable.val() === "true" ? aShow.push('#bt_discount-cumulable-group') : aHide.push('#bt_discount-cumulable-group');

        // Initialize visibility
        oGmcPro.initHide(aHide);
        oGmcPro.initShow(aShow);

        this.initializeEventHandlers();
        this.initializeDatepicker();
    },

    initializeEventHandlers: function() {
        // Handle form events
        $("input[type=radio][name=bt_option-name]").change(function() {
            var isChecked = $(this).val() === "true";
            $('#bt_discount-name-group')[isChecked ? 'slideDown' : 'slideUp']();
            if (!isChecked) $('#bt_discount-name').val('');
        });

        $("input[type=radio][name=bt_option-date]").change(function() {
            var isChecked = $(this).val() === "true";
            $('#bt_date-group')[isChecked ? 'slideDown' : 'slideUp']();
            if (!isChecked) {
                $('#bt_discount-date-from, #bt_discount-date-to').val('');
            }
        });

        $("input[type=radio][name=bt_option-min-amount]").change(function() {
            var isChecked = $(this).val() === "true";
            $('#bt_min-amount-group')[isChecked ? 'slideDown' : 'slideUp']();
            if (!isChecked) $('#bt_discount-min-amount').val('');
        });

        $("input[type=radio][name=bt_option-value]").change(function() {
            var isChecked = $(this).val() === "true";
            $('#bt_value-group, #gmcp_info_value')[isChecked ? 'slideDown' : 'slideUp']();
            if (!isChecked) {
                $('#bt_discount-value-min, #bt_discount-value-max, #bt_discount-type').val('');
            }
        });

        $("input[type=radio][name=bt_option-type]").change(function() {
            var isChecked = $(this).val() === "true";
            $('#bt_discount-type-group, #bt_discount-type-group-amount')[isChecked ? 'slideDown' : 'slideUp']();
            if (!isChecked) {
                $('#bt_discount-currency_off, #bt_discount-amount_off').attr('checked', true);
            }
        });

        $("input[type=radio][name=bt_option-cumulable]").change(function() {
            var isChecked = $(this).val() === "true";
            $('#bt_discount-cumulable-group')[isChecked ? 'slideDown' : 'slideUp']();
            if (!isChecked) $('#bt_discount-cumulable_off').attr('checked', true);
        });

        // Handle currency and amount radio changes
        $("input[type=radio][name=bt_discount-currency]").change(function() {
            $("#bt_discount-amount_off").attr("checked", true);
        });

        $("input[type=radio][name=bt_discount-amount]").change(function() {
            $("#bt_discount-currency_off").attr("checked", true);
        });
    },

    initializeDatepicker: function() {
        $(".date-picker").datepicker({
            dateFormat: 'yy-mm-dd'
        });
    },

    initializeTooltips: function(bAjaxMode) {
        if (bAjaxMode) {
            $('.label-tooltip, .help-tooltip').tooltip();
        }
    }
};

// Initialize when document is ready
$(document).ready(function() {
    GmcProPromotionFeed.init();
});