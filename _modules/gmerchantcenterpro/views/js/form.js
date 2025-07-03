/*
*
* Google merchant center Pro
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
document.addEventListener('DOMContentLoaded', function() {
    $('.label-tooltip, .help-tooltip').tooltip();


    // Use case to display the hour day cut off
    $("input[name='same_day_process']").bind('change', function(event) {
        if ($(this).val() == 1) {
            $('#same_day_hours').show();
            $('#not_same_day_hours').hide();
        } else {
            $('#same_day_hours').hide();
            $('#not_same_day_hours').show();
        }
    });

    $("input[name='activate_gcr']").bind('change', function(event) {
        if ($(this).val() == 1) {
            $('#gcr_option').show();
        } else {
            $('#gcr_option').hide();
        }
    });
});