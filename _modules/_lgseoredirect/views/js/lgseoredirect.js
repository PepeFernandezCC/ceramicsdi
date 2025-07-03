/**
 * Copyright 2024 LÍNEA GRÁFICA E.C.E S.L.
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

changed_filters = false;

function LGGetRedirects(who) {

    var target = $(who).closest('form').find('table.table').first().find('tbody');
    var filters = {};
    var fredid = $("#filterid").val().trim();
    var foldurl = $("#filteroldurl").val().trim();
    var fnewurl = $("#filternewurl").val().trim();
    var ftype  = $("#filtertype").val().trim()
    var fdate  = $("#filterdate").val().trim();
    var ferror = $("#filtererror").val().trim();

    if ($(who).closest('form').find('table.table').attr('id') == 'tablepnf') {
        foldurl = $("#filter_pnf_oldurl").val().trim();
        fnewurl = $("#filter_pnf_newurl").val().trim();
        ftype  = $("#filter_pnf_type").val().trim()
    }

    if (fredid != '' && $(who).closest('form').find('table.table').attr('id') != 'tablepnf') {
        filters['id'] = fredid;
    }

    if (foldurl != '') {
        filters['url_old'] = foldurl;
    }

    if (fnewurl != '') {
        filters['url_new'] = fnewurl;
    }

    if (ftype != '' && (parseInt(ftype) == 301 || parseInt(ftype) == 302 || parseInt(ftype) == 303)) {
        filters['type'] = ftype;
    }

    if (fdate != '') {
        filters['date'] = fdate;
    }

    if (ferror > 0) {
        filters['error'] = ferror;
    }

    if (changed_filters) {
        if ($(who).closest('form').attr('id') == 'lgseoredirect_pnf_list_form') {
            $(who).closest('form').find('input.lgseoredirect_pnf-pagination-page').val(1);
        } else {
            $(who).closest('form').find('input.lgseoredirect-pagination-page').val(1);
        }
    }

    var data = {
        ajax: true,
        controller: 'AdminModules',
        module_name: 'AdminModules',
        configure: 'lgseoredirect',
        token: lgseoredirect_token,
        rand: new Date().getTime(),
        cache: false
    };

    if ($(who).closest('form').attr('id') == 'lgseoredirect_pnf_list_form') {
        data['lgseoredirect_pnf_pagination'] = $(who).closest('form').find('input.lgseoredirect_pnf-pagination-items-page').val();
        data['p_pnf']                       = $(who).closest('form').find('input.lgseoredirect_pnf-pagination-page').val();
        data['filters_pnf']                     = filters;
        data['action']                      = 'getPNF';
    } else {
        data['lgseoredirect_pagination'] = $(who).closest('form').find('input.lgseoredirect-pagination-items-page').val();
        data['p']                       = $(who).closest('form').find('input.lgseoredirect-pagination-page').val();
        data['filters']                 = filters;
        data['action']                  = 'getRedirects';
    }

    $(target).LoadingOverlay('show');
    $.ajax({
        method: 'get',
        url: 'index.php',
        data: data,
        dataType: 'json',
        timeout: 30000
    }).error(function(jqXHR, textStatus, errorThrown) {
        if (textStatus == 'timeout') {
            $(target).LoadingOverlay('hide', true);
            showErrorMessage(lgseoredirect_msg_error_timeout);
        } else {
            showErrorMessage(lgseoredirect_msg_error_unknown);
        }
    }).done(function(response) {
        $(target).LoadingOverlay('hide', true);
        if (response.status == 'ok') {
            $('#checkall').prop('checked', false);
            //$('#tableproduct tbody').('');
            $(target).html(response.rows);
            $(who).closest('form').find('.lgseoredirect_pagination').html(response.pagination);

            if ($(who).closest('form').find('table.table').attr('id') != 'tablepnf') {
                if (window.lgseoredirect_select_all) {
                    if (window.lgseoredirect_selected_items.length > 0) {
                        var some_deselected = false;
                        $('input[name^="selected_redirects"]').each(function(){
                            if (window.lgseoredirect_selected_items.indexOf(parseInt($(this).val())) >= 0) {
                                $(this).prop('checked', false);
                                some_deselected = true;
                            } else {
                                $(this).prop('checked', true);
                            }
                        });
                        $('#lgseoredirect_checkall').prop('checked', !some_deselected);
                    } else {
                        lgseoredirectCheckAll();
                    }
                } else {
                    if (window.lgseoredirect_selected_items.length > 0) {
                        $('input[name^="selected_redirects"]').each(function(){
                            if (window.lgseoredirect_selected_items.indexOf(parseInt($(this).val())) >= 0) {
                                $(this).prop('checked', true);
                            }
                        });
                    }
                    var all_selected = true && ($(who).closest('form').find('input[name^="selected_redirects"]').length > 0);
                    $(who).closest('form').find('input[name^="selected_redirects"]').each(function(){
                        if (!$(this).is(':checked')) {
                            all_selected = false;
                        }
                    });
                    if (all_selected) {
                        $('#lgseoredirect_checkall').prop('checked', true);
                    } else {
                        $('#lgseoredirect_checkall').prop('checked', false);
                    }
                }
            }

            if ($(who).closest('form').attr('id') == 'lgseoredirect_list_form') {
                $('#buttonlistredirects .lgseoredirect_total_products').html(response.total_products);
                $('#listredirects .lgseoredirect_total_products').html(response.total_products);
            }
        }
    });
}

function lgseoredirectUncheckAll()
{
    $('input[name^="selected_redirects"]').each(function(){
        $(this).prop('checked', false);
    });
    $('#lgseoredirect_checkall').prop('checked', false);
}

function lgseoredirectCheckAll()
{
    $('input[name^="selected_redirects"]').each(function(){
        $(this).prop('checked', true);
    });
    $('#lgseoredirect_checkall').prop('checked', true);
}

$(document).ready(function(){
    window.lgseoredirect_selected_items    = [];
    window.lgseoredirect_select_all        = 0;

    $(document).on('keyup',  "#filterid",     function(){changed_filters = true;LGGetRedirects($(this));});
    $(document).on('keyup',  "#filteroldurl", function(){changed_filters = true;LGGetRedirects($(this));});
    $(document).on('keyup',  "#filternewurl", function(){changed_filters = true;LGGetRedirects($(this));});
    $(document).on('change', "#filtertype",   function(){changed_filters = true;LGGetRedirects($(this));});
    $(document).on('keyup',  "#filterdate",   function(){changed_filters = true;LGGetRedirects($(this));});
    $(document).on('change', "#filtererror",  function(){changed_filters = true;LGGetRedirects($(this));});

    $(document).on('keyup',  "#filter_pnf_oldurl", function(){changed_filters = true;LGGetRedirects($(this));});
    $(document).on('keyup',  "#filter_pnf_newurl", function(){changed_filters = true;LGGetRedirects($(this));});
    $(document).on('change', "#filter_pnf_type",   function(){changed_filters = true;LGGetRedirects($(this));});
    $(document).on('keyup',  "#filter_pnf_date",   function(){changed_filters = true;LGGetRedirects($(this));});
    $(document).on('change', "#filter_pnf_error",  function(){changed_filters = true;LGGetRedirects($(this));});

    $(document).on('click','.pagination-link', function() {
        if (!$(this).closest('li').hasClass('disabled')) {
            //var selected_products = [];
            $('input[name="lgseoredirect_page"]').val($(this).attr('data-page'));
            changed_filters = false;
            if ($(this).closest('form').attr('id') == 'lgseoredirect_pnf_list_form') {
                $(this).closest('form').find('input.lgseoredirect_pnf-pagination-page').val($(this).attr('data-page'));
                LGGetRedirects($('#lgseoredirect_pnf_list_form table'));
            } else {
                $(this).closest('form').find('input.lgseoredirect-pagination-page').val($(this).attr('data-page'));
                LGGetRedirects($('#lgseoredirect_list_form table'));
            }
        }
    });

    $(document).on('click', '.pagination-items-page', function(e){
        e.preventDefault();
        //$(this).closest('div.pagination').find('.lgseoredirect-pagination-items-page').val($(this).data("items"));
        changed_filters = false;
        if ($(this).closest('form').attr('id') == 'lgseoredirect_pnf_list_form') {
            $(this).closest('form').find('input.lgseoredirect_pnf-pagination-items-page').val($(this).data("items"));
            LGGetRedirects($('#lgseoredirect_pnf_list_form table'));
        } else {
            $(this).closest('form').find('input.lgseoredirect-pagination-items-page').val($(this).data("items"));
            LGGetRedirects($('#lgseoredirect_list_form table'));
        }
    });

    $(document).on('click', '#lgseoredirect_checkall', function() {
        if ($(this).is(':checked')) {
            lgseoredirectCheckAll($(this));
        } else {
            lgseoredirectUncheckAll($(this));
        }

        $('input[name^="selected_redirects"]').each(function(){
            if (window.lgseoredirect_select_all) {
                if (!$(this).attr('checked') || !$(this).is(':checked')) {
                    if (window.lgseoredirect_selected_items.indexOf(parseInt($(this).val())) < 0) {
                        window.lgseoredirect_selected_items.push(parseInt($(this).val()));
                    }
                } else {
                    if (window.lgseoredirect_selected_items.indexOf(parseInt($(this).val())) >= 0) {
                        var pos = window.lgseoredirect_selected_items.indexOf(parseInt($(this).val()));
                        window.lgseoredirect_selected_items.splice(parseInt(pos), 1);
                    }
                }
            } else {
                if ($(this).attr('checked') || $(this).is(':checked')) {
                    if (window.lgseoredirect_selected_items.indexOf(parseInt($(this).val())) < 0) {
                        window.lgseoredirect_selected_items.push(parseInt($(this).val()));
                    }
                } else {
                    if (window.lgseoredirect_selected_items.indexOf(parseInt($(this).val())) >= 0) {
                        var pos = window.lgseoredirect_selected_items.indexOf(parseInt($(this).val()));
                        window.lgseoredirect_selected_items.splice(parseInt(pos), 1);
                    }
                }
            }
        });
    });

    $(document).on('click', '#lgseoredirect_clear_selection', function() {
        window.lgseoredirect_selected_items = [];
        window.lgseoredirect_select_all    = 0;
        lgseoredirectUncheckAll($(this));
    });

    $(document).on('click', '#lgseoredirect_select_all_redirects', function() {
        window.lgseoredirect_selected_items = [];
        window.lgseoredirect_select_all    = 1;
        lgseoredirectCheckAll($(this));
    });

    $(document).on('click', 'input[name^="selected_redirects"]', function () {
        if (window.lgseoredirect_selected_items.indexOf(parseInt($(this).val())) < 0) {
            window.lgseoredirect_selected_items.push(parseInt($(this).val()));
        } else {
            var pos = window.lgseoredirect_selected_items.indexOf(parseInt($(this).val()));
            window.lgseoredirect_selected_items.splice(parseInt(pos), 1);
        }

        var all_selected = true;
        $('input[name^="selected_redirects"]').each(function(){
            if (!$(this).is(':checked')) {
                all_selected = false;
            }
        });
        if (all_selected) {
            $('#lgseoredirect_checkall').prop('checked', true);
        } else {
            $('#lgseoredirect_checkall').prop('checked', false);
        }
    });

    $(document).on('click', 'button[name="lgseoredirect_deleteSelected"]', function() {
        $('#lgseoredirect_list_form').LoadingOverlay('show');
        $.ajax({
            method: 'get',
            url: 'index.php',
            data: {
                ajax: true,
                controller: 'AdminModules',
                module_name: 'AdminModules',
                configure: 'lgseoredirect',
                token: lgseoredirect_token,
                action: 'deleteRedirects',
                redirects: window.lgseoredirect_selected_items,
                allselected: window.lgseoredirect_select_all,
                rand: new Date().getTime(),
                cache: false
            },
            dataType: 'json'
        }).success(function (response) {
            $('#lgseoredirect_list_form').LoadingOverlay('hide');
            if (response.status == 'ok') {
                window.lgseoredirect_selected_items = [];
                window.lgseoredirect_select_all = 0;
                showSuccessMessage(response.message);
                LGGetRedirects($('#lgseoredirect_list_form table'));
            }
        }).error(function (response) {
            $('#lgseoredirect_list_form').LoadingOverlay('hide');
            if (response.status == 'ok') {
                window.lgseoredirect_selected_items = [];
                window.lgseoredirect_select_all = 0;
                showErrorMessage(response.message);
                LGGetRedirects($('#lgseoredirect_list_form table'));
            }
        });
    });

    $(document).on('click', 'button.deleteRedirect', function() {
        var id_redirect = $(this).data('id');
        var redirects  = [];
        redirects.push(id_redirect);
        $('#lgseoredirect_list_form').LoadingOverlay('show');
        $.ajax({
            method: 'get',
            url: 'index.php',
            data: {
                ajax: true,
                controller: 'AdminModules',
                module_name: 'AdminModules',
                configure: 'lgseoredirect',
                token: lgseoredirect_token,
                action: 'deleteRedirects',
                redirects: redirects,
                allselected: 0,
                rand: new Date().getTime()
            },
            dataType: 'json'
        }).success(function (response) {
            $('#lgseoredirect_list_form').LoadingOverlay('hide');
            if (response.status == 'ok') {
                if (window.lgseoredirect_selected_items.indexOf(id_redirect) >= 0) {
                    var pos = window.lgseoredirect_selected_items.indexOf(id_redirect);
                    window.lgseoredirect_selected_items.splice(parseInt(pos), 1);
                }
                showSuccessMessage(response.message);
            }
            LGGetRedirects($('#lgseoredirect_list_form table'));
            LGGetRedirects($('#lgseoredirect_pnf_list_form table'));
        }).error(function (response) {
            $('#lgseoredirect_list_form').LoadingOverlay('hide');
            if (response.status == 'ok') {
                showErrorMessage(response.message);
            }
        });
    });

    $(document).on('click', '.autofilter', function() {
        $('#filteroldurl').val($(this).attr('data-url'));
        $('#filteroldurl').keyup();
    });

    $(document).on('click', 'button.editRedirect', function(){
        $(this).closest('tr').find('.lgseoredirect-origin-url-text').hide();
        $(this).closest('tr').find('.lgseoredirect-target-url-text').hide();
        $(this).closest('tr').find('.lgseoredirect-target-type-text').hide();
        $(this).closest('tr').find('.lgseoredirect-origin-url-edit-container').show();
        $(this).closest('tr').find('.lgseoredirect-target-url-edit-container').show();
        $(this).closest('tr').find('.lgseoredirect-target-type-edit-container').show();
        $(this).hide();
        $(this).parent().find('button.saveRedirect').show();
        $(this).parent().find('button.cancelRedirect').show();
    });

    $(document).on('click', 'button.saveRedirect', function(){
        var obj = $(this);
        var id_redirect = $(this).data('id');
        var target     = $(this).closest('tr').find('input[name="lgseoredirect-target-url-input-'+id_redirect+'"]').val();
        var origin     = $(this).closest('tr').find('input[name="lgseoredirect-origin-url-input-'+id_redirect+'"]').val();
        var type       = $(this).closest('tr').find('select[name^="lgseoredirect-target-type-select-"]').val();
        var redirects  = [];
        redirects.push({id: id_redirect, target: target, origin: origin, type: type});
        $.ajax({
            method: 'get',
            url: 'index.php',
            data: {
                ajax: true,
                controller: 'AdminModules',
                module_name: 'lgseoredirect',
                configure: 'lgseoredirect',
                token: lgseoredirect_token,
                action: 'saveRedirects',
                redirects: redirects,
                allselected: 0,
                rand: new Date().getTime()
            },
            dataType: 'json'
        }).success(function (response) {
            if (response.status == 'ok') {
                showSuccessMessage(response.message);
            }
            $('#lgseoredirect_list_form').LoadingOverlay('hide');
            LGGetRedirects($('#lgseoredirect_list_form table'));
            LGGetRedirects($('#lgseoredirect_pnf_list_form table'));
        }).error(function (response) {
            if (response.status == 'ko') {
                showErrorMessage(response.message);
            }
            $('#lgseoredirect_list_form').LoadingOverlay('hide');
        });
    });

    $(document).on('click', 'button.cancelRedirect', function(){
        var id_redirect = $(this).data('id');
        $(this).closest('tr').find('.lgseoredirect-origin-url-text').show();
        $(this).closest('tr').find('.lgseoredirect-target-url-text').show();
        $(this).closest('tr').find('.lgseoredirect-target-type-text').show();
        $(this).closest('tr').find('.lgseoredirect-origin-url-edit-container').hide();
        $(this).closest('tr').find('.lgseoredirect-target-url-edit-container').hide();
        $(this).closest('tr').find('.lgseoredirect-target-type-edit-container').hide();
        $(this).closest('tr').find('.lgseoredirect-origin-url-edit-container input').val($(this).data('old-origin-value'));
        $(this).closest('tr').find('.lgseoredirect-target-url-edit-container input').val($(this).data('old-value'));
        $(this).closest('tr').find('select[name="lgseoredirect-target-type-select-'+id_redirect+'"]').val($(this).data('old-type'));
        $(this).closest('tr').find('button.editRedirect').show();
        $(this).closest('tr').find('button.cancelRedirect').hide();
        $(this).closest('tr').find('button.saveRedirect').hide();
    });

    $('.lgseoredirect-tabcontent').hide();
    $('#individualredirect').show();
    $(document).on('click', '.lgseoredirect_menubarbutton', function() {
        var target = $(this).attr('id').replace('button', '');
        $('.lgseoredirect_menubarbutton').removeClass("btn-primary").addClass("btn-default");
        $('this').removeClass("btn-default").addClass("btn-primary");
        $('.lgseoredirect-tabcontent').hide();
        $('#'+target).show();
    });

    $(document).on('keyup', 'input[name^="lgseoredirect-target-url-input"]', function() {
        if ($(this).closest('tr').find('select[name^="lgseoredirect-target-type-select"]').val() != 0
            && $(this).val() != ''
        ) {
            $(this).closest('tr').find('button.savePNF').removeClass('disabled');
        } else {
            $(this).closest('tr').find('button.savePNF').addClass('disabled');
        }
        if ($(this).closest('tr').find('select[name^="lgseoredirect-target-type-select"]').val() != 0
            || $(this).val() != ''
        ) {
            $(this).closest('tr').find('button.cancelPNF').removeClass('disabled');
        } else {
            $(this).closest('tr').find('button.cancelPNF').addClass('disabled');
        }
    });

    $(document).on('change', 'select[name^="lgseoredirect-target-type-select-"]', function() {
        if ($(this).closest('tr').find('input[name^="lgseoredirect-target-url-input-"]').val() != ''
            && $(this).val() != 0
        ) {
            $(this).closest('tr').find('button.savePNF').removeClass('disabled');
        } else {
            $(this).closest('tr').find('button.savePNF').addClass('disabled');
        }
        if ($(this).closest('tr').find('input[name^="lgseoredirect-target-url-input-"]').val() != ''
            || $(this).val() != 0
        ) {
            $(this).closest('tr').find('button.cancelPNF').removeClass('disabled');
        } else {
            $(this).closest('tr').find('button.cancelPNF').addClass('disabled');
        }
    });

    $(document).on('click', 'button.editPNF', function(){
        $(this).closest('tr').find('.lgseoredirect-target-url-text').hide();
        $(this).closest('tr').find('.lgseoredirect-target-url-edit-container').show();
        $(this).closest('tr').find('.lgseoredirect-target-type-text').hide();
        $(this).closest('tr').find('.lgseoredirect-target-type-edit-container').show();
        $(this).hide();
        $(this).parent().find('button.savePNF').show();
        $(this).parent().find('button.cancelPNF').show();
    });

    $(document).on('click', 'button.cancelPNF', function() {
        var id_pagenotfound = $(this).data('id');
        if ($(this).data('old-value').trim() == "") {
            $(this).closest('tr').find('select[name="lgseoredirect-target-type-select-'+id_pagenotfound+'"]').val(0);
            $(this).closest('tr').find('input[name="lgseoredirect-target-url-input-'+id_pagenotfound+'"]').val('');
            $(this).closest('tr').find('button.savePNF').addClass('disabled');
            $(this).closest('tr').find('button.cancelPNF').addClass('disabled');
        } else {
            $(this).closest('tr').find('.lgseoredirect-target-url-text').show();
            $(this).closest('tr').find('.lgseoredirect-target-type-text').show();
            $(this).closest('tr').find('.lgseoredirect-target-url-edit-container').hide();
            $(this).closest('tr').find('.lgseoredirect-target-type-edit-container').hide();
            $(this).closest('tr').find('select[name="lgseoredirect-target-type-select-'+id_pagenotfound+'"]').val($(this).data('old-type'));
            $(this).closest('tr').find('input[name="lgseoredirect-target-url-input-'+id_pagenotfound+'"]').val($(this).data('old-value'));
            $(this).closest('tr').find('button.savePNF').addClass('disabled');
            $(this).closest('tr').find('button.cancelPNF').hide();
            $(this).closest('tr').find('button.savePNF').hide();
            $(this).closest('tr').find('button.editPNF').show();
        }
    });

    $(document).on('click', 'button.savePNF', function() {
        var obj            = $(this);
        var id_pagenotfound = $(this).data('id');
        var request_uri    = encodeURIComponent($(this).data('request-uri'));
        var target         = $(this).closest('tr').find('input[name="lgseoredirect-target-url-input-'+id_pagenotfound+'"]').val();
        var type           = $(this).closest('tr').find('select[name^="lgseoredirect-target-type-select-"]').val();
        var pages_not_found = [];
        var block          = $(this).closest('tr');
        $(block).LoadingOverlay('show');

        pages_not_found.push({
            id: id_pagenotfound,
            'request_uri': request_uri,
            target: target,
            type: type
        });

        $.ajax({
            method: 'get',
            url: 'index.php',
            data: {
                ajax: true,
                controller: 'AdminModules',
                module_name: 'lgseoredirect',
                configure: 'lgseoredirect',
                token: lgseoredirect_token,
                action: 'savePagesNotFound',
                'pages_not_found': pages_not_found,
                allselected: 0,
                rand: new Date().getTime()
            },
            dataType: 'json'
        }).success(function (response) {
            if (response.status == 'ok') {
                showSuccessMessage(response.message);
            }
            $(block).LoadingOverlay('hide');
            LGGetRedirects(obj);
            LGGetRedirects($('#lgseoredirect_list_form table'));
        }).error(function (response) {
            $('#lgseoredirect_list_form').LoadingOverlay('hide');
            if (response.status == 'ko') {
                showErrorMessage(response.message);
            }
        });
    });

    $(document).on('click', 'button.deletePNF', function() {
        var request_uri    = $(this).data('request-uri');
        var block          = $(this).closest('tr');
        var pages_not_found = [];

        pages_not_found.push(request_uri);
        $(block).LoadingOverlay('show');
        $.ajax({
            method: 'get',
            url: 'index.php',
            data: {
                ajax: true,
                controller: 'AdminModules',
                module_name: 'AdminModules',
                configure: 'lgseoredirect',
                token: lgseoredirect_token,
                action: 'deletePNF',
                'pages_not_found': pages_not_found,
                allselected: 0,
                rand: new Date().getTime()
            },
            dataType: 'json'
        }).success(function (response) {
            if (response.status == 'ok') {
                showSuccessMessage(response.message);
            }
        }).error(function (response) {
            if (response.status == 'ko') {
                showErrorMessage(response.message);
            }
        });
        $(block).LoadingOverlay('hide');
        LGGetRedirects($(this));
        LGGetRedirects($('#lgseoredirect_list_form table'));
    });
});
