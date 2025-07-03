/*
*
* Dynamic Ads + Pixel
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
    oGmcProFeatureByCat.handleOptionToDisplay($("#default_tag").val());
    $("#set_tag").change(function () {
        oGmcProFeatureByCat.handleOptionToDisplay($(this).val());
        $("#default_tag").val($(this).val());
    });

    $(".alert-success").delay(2000).slideUp();
    $(".alert-danger").delay(10000).slideUp();

    // handle case for new assocation mode assoction with module version 1.9.0
    // handle the 2 modes for gender tag
    if ($("#set_tag").val() == 'gender') {
        if ($("#set_tag_mode").val() == 'bulk') {
            $('select.gender_product').slideUp();
            $('#bulk_action_gender_product').slideUp();
            $('#bulk_action_gender').slideDown();
            $('select.gender').slideDown();
        } else if ($('#set_tag_mode').val() == 'product_data') {
            $('#bulk_action_gender').slideUp();
            $('#bulk_action_gender_product').slideDown();
            $('select.gender').slideUp();
            $('select.gender_product').slideDown();
        }
        $("#set_tag_mode").change(function () {

            if ($(this).val() == 'bulk') {
                $('#bulk_action_gender').slideDown();
                $('#bulk_action_gender_product').slideUp();
                $('select.gender').slideDown();
                $('select.gender_product').slideUp();

            } else if ($(this).val() == 'product_data') {
                $('#bulk_action_gender').slideUp();
                $('#bulk_action_gender_product').slideDown();
                $('select.gender').slideUp();
                $('select.gender_product').slideDown();
            }
        });
    }

    // handle the 2 modes for adult tag
    if ($("#set_tag").val() == 'adult') {
        if ($("#set_tag_mode").val() == 'bulk') {
            $('#bulk_action_tagadult').slideDown();
            $('#bulk_action_tagadult_product').slideUp();
            $('select.adult').slideDown();
            $('select.tagadult_product').slideUp();
        } else if ($('#set_tag_mode').val() == 'product_data') {
            $('#bulk_action_tagadult').slideUp();
            $('#bulk_action_tagadult_product').slideDown();
            $('select.adult').slideUp();
            $('select.tagadult_product').slideDown();
        }
        $("#set_tag_mode").change(function () {

            if ($(this).val() == 'bulk') {
                $('#bulk_action_tagadult').slideDown();
                $('#bulk_action_tagadult_product').slideUp();
                $('select.adult').slideDown();
                $('select.tagadult_product').slideUp();

            } else if ($(this).val() == 'product_data') {
                $('#bulk_action_tagadult').slideUp();
                $('#bulk_action_tagadult_product').slideDown();
                $('select.adult').slideUp();
                $('select.tagadult_product').slideDown();
            }
        });
    }

    if ($("#set_tag").val() == 'agegroup') {
        if ($("#set_tag_mode").val() == 'bulk') {
            $('select.agegroup_product').css('display', 'none');
            $('#bulk_action_adult_product').slideUp();
            $('#bulk_action_adult').slideDown();
            $('select.agegroup').slideDown();
        } else if ($('#set_tag_mode').val() == 'product_data') {
            $('#bulk_action_adult').slideUp();
            $('#bulk_action_adult_product').slideDown();
            $('select.agegroup').slideUp();
            $('select.agegroup_product').slideDown();
        }
        $("#set_tag_mode").change(function () {

            if ($(this).val() == 'bulk') {
                $('#bulk_action_adult').slideDown();
                $('#bulk_action_adult_product').slideUp();
                $('select.agegroup').slideDown();
                $('select.agegroup_product').css('display', 'none');

            } else if ($(this).val() == 'product_data') {
                $('#bulk_action_adult').slideUp();
                $('#bulk_action_adult_product').slideDown();
                $('select.agegroup').slideUp();
                $('select.agegroup_product').slideDown();
            }
        });
    }
});