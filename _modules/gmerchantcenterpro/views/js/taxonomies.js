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

document.addEventListener('DOMContentLoaded', function() {
    $(".alert-success").delay(2000).slideUp();
    $(".alert-danger").delay(10000).slideUp();

    $('input.autocmp').each(function (index, element) {
        $(element).click(function () {
            $(this).val('');
        });
        $(element).keyup(function () {
            $.ajax({
                url: btGmcp.taxonomyController,
                dataType: "json",
                data: {
                    ajax: 1,
                    token: token,
                    tab: 'AdminGmcpTaxonomy',
                    action: 'autocomplete',
                    query: $(this).val(),
                },
                beforeSend: function () {
                    $('#' + $(element).attr('id')).css("background", "#dc3545");
                    $('#suggesstion-box_' + $(element).attr('category_id')).html();
                    $('.suggestion').hide();
                },
                success: function (taxonomies) {
                    if (taxonomies) {
                        htmlContent = '<ul class="suggestion" id="taxonomy_list">'
                        $.each(taxonomies, function(i, item) {
                            taxo = '';
                            taxo = item;
                            htmlContent += '<li id="result_search_' + i + '_' +$(element).attr('category_id') + '" onClick="oGmcPro.getSelectedTaxonomy('+ i +',' + $(element).attr('category_id') + ');">' + taxo + '</li>';

                        });
                        htmlContent += '</ul>'
                        $('#' + $(element).attr('id')).css("background", "#FFF");
                        $('#suggesstion-box_' + $(element).attr('category_id')).show();
                        $('#suggesstion-box_' + $(element).attr('category_id')).html(htmlContent);
                    } else {
                        $('#suggesstion-box_' + $(element).attr('category_id')).html();
                        $('#' + $(element).attr('id')).css("background", "#ffc107");
                        $('.suggestion').hide();
                    }
                },
            });
        });
    });
});