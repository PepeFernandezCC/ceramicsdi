/**
 * 2007-2020 PrestaShop
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
 *  @copyright 2007-2020 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */
var MpmPEExportStatusModal = {
    container: ".exportproducts_status_export",
    close_btn: ".exportproducts_status_export .close-status-form",
    stop_btn: ".exportproducts_status_export .stop-current-export-process",
    progress_bar_container: ".exportproducts_status_export .progress_product_export",
    progress_bar: ".exportproducts_status_export .progress_bar_product_export",
    show: function() {
        $(MpmPEExportStatusModal.container).addClass("active");

        var default_status = $(MpmPEExportStatusModal.container + ' .export_product_notification').data("label");
        $(MpmPEExportStatusModal.container + ' .export_product_notification').html(default_status);
        $(MpmPEExportStatusModal.progress_bar_container).show();
    },
    hide: function() {
        $(MpmPEExportStatusModal.container).removeClass("active");
        removeAllOverlay();
    },
    isVisible: function() {
        return $(MpmPEExportStatusModal.container).hasClass("active");
    },
    setStatus: function(status) {
        $(MpmPEExportStatusModal.container + ' .export_product_notification').html(status);
    },
    updateProgress: function(num_of_exported, num_to_export) {
        var progress_data = {
            max: (num_to_export !== false) ? parseInt(num_to_export) : false,
            value: (num_of_exported !== false) ? parseInt(num_of_exported) : false,
        };

        $(MpmPEExportStatusModal.progress_bar).progressbar(progress_data);
    },
    setExportProcessIdToStopBtn: function(id_export_process) {
        $(this.stop_btn).data("id-export-process", id_export_process);

        if (id_export_process) {
            $(MpmPEExportStatusModal.stop_btn).css("display", "flex");
        }
    }
};

var PEExportProcessHistory = {
    container: "#mpm_pe_export_process_history",
    history_item: "#mpm_pe_export_process_history .item_settings",
    date_info_block: ".last_executed_settings",
    status_block: ".status_settings",
    download_link_block: ".link_to_file",
    delete_btn: "#mpm_pe_export_process_history .assignment_item .delete_option",
    setExportStartDate: function(id_export_process, start_time) {
        var export_process_item = PEExportProcessHistory.container + " .item_settings_" + id_export_process;
        $(export_process_item + " " + PEExportProcessHistory.date_info_block).html(start_time).addClass('data_isset').removeClass('no_data');
    },
    setStatus: function(id_export_process, status) {
        var export_process_item = PEExportProcessHistory.container + " .item_settings_" + id_export_process;
        $(export_process_item + " " + PEExportProcessHistory.status_block +  " span").html(status);

        this.toggleStatusBlock(false);
    },
    setExportedFileDownloadLink: function(id_export_process, download_link) {
        var random_number = Math.random() * 10000;
        var export_process_item = PEExportProcessHistory.container + " .item_settings_" + id_export_process;

        $(export_process_item + " " + PEExportProcessHistory.download_link_block).attr("href", download_link + "?v=" + random_number).addClass('active');
    },
    toggleStatusBlock: function(activate, id_export_process = false) {
        if (activate && id_export_process) {
            if (!$($(PEExportProcessHistory.history_item + ".item_settings_" + id_export_process)).length) {
                return false;
            }

            $(PEExportProcessHistory.history_item + ".item_settings_" + id_export_process + " .status_settings").addClass('active');
        } else {
            if (!$(PEExportProcessHistory.history_item).length) {
                return false;
            }

            $(PEExportProcessHistory.history_item + " .status_settings").removeClass('active');
        }
    },
    deleteExportProcess: function(id_export_process) {
        $.ajax({
            type: "POST",
            url: "index.php",
            dataType: 'json',
            data: {
                ajax: true,
                token: $('input[name=token_product_export]').val(),
                controller: 'AdminProductsExport',
                action: 'deleteExportProcess',
                id_export_process: id_export_process,
            },
            beforeSend: function () {
                showLoader();
            },
            success: function (json) {
                if (json['error']) {
                    return showError(json['error']);
                }
                
                if (json['success']) {
                    $('body').append(json['success']);
                    showSuccess();
                }
            },
            complete: function() {
                hideLoader();
            }
        });
    }
};

var MpmPESavedConfigurationList = {
    container: "#mpm_pe_saved_configurations",
    saved_configuration_item: "#mpm_pe_saved_configurations .item_settings",
    date_info_block: ".last_executed_settings",
    status_block: ".status_settings",
    download_link_block: ".link_to_file",
    delete_btn: "#mpm_pe_saved_configurations .assignment_item .delete_option",
    setExportStartDate: function(id_configuration, start_time) {
        var configuration_item = MpmPESavedConfigurationList.container + " .item_settings_" + id_configuration;

        if (!$(configuration_item).length) {
            return false;
        }

        $(configuration_item + " " + MpmPESavedConfigurationList.date_info_block).html(start_time).addClass('data_isset').removeClass('no_data');
    },
    setStatus: function(id_configuration, status) {
        var configuration_item = MpmPESavedConfigurationList.container + " .item_settings_" + id_configuration;

        if (!$(configuration_item).length) {
            return false;
        }

        $(configuration_item + " " + MpmPESavedConfigurationList.status_block +  " span").html(status);
        this.toggleStatusBlock(false);
    },
    setExportedFileDownloadLink: function(id_configuration, download_link) {
        var random_number = Math.random() * 10000;
        var configuration_item = MpmPESavedConfigurationList.container + " .item_settings_" + id_configuration;

        if (!$(configuration_item).length) {
            return false;
        }

        $(configuration_item + " " + MpmPESavedConfigurationList.download_link_block).attr("href", download_link + "?v=" + random_number).addClass('active');
    },
    toggleStatusBlock: function(activate, id_configuration = false) {
        if (activate && id_configuration) {
            if (!$(MpmPESavedConfigurationList.saved_configuration_item + ".item_settings_" + id_configuration).length) {
                return false;
            }

            $(MpmPESavedConfigurationList.saved_configuration_item + ".item_settings_" + id_configuration + " .status_settings").addClass('active');
        } else {
            if (!$(MpmPESavedConfigurationList.saved_configuration_item).length) {
                return false;
            }

            $(MpmPESavedConfigurationList.saved_configuration_item + " .status_settings").removeClass('active');
        }
    },
    deleteConfiguration: function(id_configuration) {
        $.ajax({
            type: "POST",
            url: "index.php",
            dataType: 'json',
            data: {
                ajax: true,
                token: $('input[name=token_product_export]').val(),
                controller: 'AdminProductsExport',
                action: 'deleteConfiguration',
                id_configuration: id_configuration,
            },
            beforeSend: function () {
                showLoader();
            },
            success: function (json) {
                if (json['error']) {
                    return showError(json['error']);
                }
                
                if (json['success']) {
                    $('body').append(json['success']);
                    showSuccess();
                }
            },
            complete: function() {
                hideLoader();
            }
        });
    }
};

$(document).ready(function () {
    direction = 0;

    window.onload = function () {
        if (window.addEventListener) window.addEventListener("DOMMouseScroll", mouse_wheel, false);
        window.onmousewheel = document.onmousewheel = mouse_wheel;
    };

    var mouse_wheel = function (event) {
        if (false == !!event) event = window.event;
        direction = ((event.wheelDelta) ? event.wheelDelta / 120 : event.detail / -3) || false;
    };

    $(document).scroll(function () {
        if ($('.form_preview_file').length > 0) {
            scrollForm($('.form_preview_file'), 0);
        }
    });
    
    $(document).on('input', 'input.search-filter', function () {
        var search_query = $(this).val().toLowerCase();
        var filter_options = $(".select_filter_list .filter_field");
        
        if (search_query === "") {
            filter_options.show();
        }
    
        filter_options.each(function() {
            var filter_name = $(this).text().toLowerCase();
            
            if (filter_name.includes(search_query)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    $(document).on('click', '.add_more_condition_button .add_more_condition', function () {
        getMoreCondition($(this).attr('data-count'));
    });

    $(document).on("change", "select.condition", function() {
        toggleExtraFieldModalFieldsVisibilityByConditionValue($(this));
    });

    toggleFTPFieldsVisibility();

    $(document).on("change", ".ftp_protocol, .ftp_authentication_type", function() {
        toggleFTPFieldsVisibility();
    });

    $(document).on('click', '.close_extra_field_row', function () {
        var condition_number = $('.add_more_condition').attr('data-count');
        var new_condition_number = (parseInt(condition_number) - 1);

        $('.add_more_condition').attr('data-count', new_condition_number);

        $(this).parent().remove();
    });

    $(document).mouseup(function (e) {

        if ($('.main_block_filter_product .select_filter').length > 0 && !$('.main_block_filter_product .select_filter').is(e.target)) {
            if (e.target.className === "search-filter") {
                return false;
            }
            
            $('.main_block_filter_product .select_filter').removeClass('active');
            $('.main_block_filter_product .select_filter_list').removeClass('active');
        }

        if ($('.filter_section_data .filter_checkbox_block').length > 0 && !$('.filter_section_data .filter_checkbox_block, .filter_section_data .filter_select, .checkbox_item_search').is(e.target)) {
            $(this).find('.filter_select').removeClass('active');
            $(this).find('.filter_checkbox_block').removeClass('active');
        }

        if ($('.item_settings  .settings_buttons').length > 0 && !$('.toggle_settings_buttons').is(e.target) && !$('.toggle_settings_buttons .mic-more').is(e.target)) {
            $('.item_settings .settings_buttons').removeClass('active');
        }

        if ($('.item_scheduled_task   .scheduled_tasks_buttons').length > 0 && !$('.toggle_scheduled_tasks_buttons').is(e.target) && !$('.toggle_scheduled_tasks_buttons .mic-more').is(e.target)) {
            $('.item_scheduled_task .scheduled_tasks_buttons').removeClass('active');
        }

    });

    $(document).on('click', '.extra_field_form .extra_field_button', function () {
        changeCustomField($(this).attr('data-field'), $(this).attr('data-type'));
    });

    $(document).on('click', '.task-status span', function () {
        var status = $(this).attr('data-status');
        var id_task = $(this).parents('.item_scheduled_task').attr('data-id-task');

        changeTaskStatus(status, id_task);
    });

    $(document).on('click', '.example_one_field .copy_field_name', function () {
        var field_id = $(this).attr('data-field');

        if (field_id.includes("specific_price")) {
            field_id = field_id.replace(/[\[\]]/g, "");
            field_id = "[" + field_id + "_1]";
        }

        copyToClipboard(field_id);
        $('.example_one_field .copy_field_name').removeClass('active');
        $(this).addClass('active');
    });

    $(document).on('click', '.preview_product_export_file', function () {
        getFormPreviewFile();
    });

    $(document).on('click', '.form_preview_file .close_block', function () {
        $('.form_preview_file').remove();
        removeAllOverlay();
    });


    $(document).on('click', '.extra_field_form .close_extra_field_form', function () {
        removeAllOverlay();
    });

    $(document).on('click', ".exportproducts_overflow, " + MpmPEExportStatusModal.close_btn, function () {
        MpmPEExportStatusModal.hide();
    });

    if ($('.related_modules_list').length > 0) {
        getRelatedModules();
    }

    setTimeout(function () {
        refreshExportData();
    },3000);

    $(document).on('click', '.static_custom', function () {
        createSelectedLine($(this), 0);
    });

    $(document).on('change', '.upload_settings', function () {
        uploadConfiguration();
    });

    $(document).on('click', '.form_task_footer .save_task', function () {
        saveScheduledTask($(this).attr('data-id'));
    });

    $(document).on('click', '.saved_settings_list .item_settings  .export_now', function () {
        var id_configuration = $(this).attr('data-id');
        var id_task = 0;

        startExportManually(id_configuration, id_task);
    });

    $(document).on('click', '.scheduled_tasks_list .item_scheduled_task  .export_now', function () {
        var id_configuration = 1;
        var id_task = $(this).attr('data-id');
        startExportManually(id_configuration, id_task);
    });

    $(document).on('click', MpmPEExportStatusModal.stop_btn, function () {
        stopProductExport();
    });

    $(document).on('click', '.saved_settings_list .item_settings  .stop_now', function () {
        stopProductExport();
    });

    $(document).on('click', '.scheduled_tasks_list .item_scheduled_task  .stop_now', function () {
        stopProductExport();
    });

    $(document).on('click', '.mpm_button_export', function () {
        var id_configuration = 0;
        var id_task = 0;

        startExportManually(id_configuration, id_task);
    });

    $(document).on('click', '.add_new_task', function () {
        formEditTask(0);
    });

    $(document).on('click', '.assignment_item_task .edit_option', function () {
        formEditTask($(this).attr('data-id'));
    });

    $(document).on('click', '.item_scheduled_task  .scheduled_tasks_name', function () {
        formEditTask($(this).parents('.item_scheduled_task').attr('data-id-task'));
    });

    $(document).on('click', '.back_to_task_list', function () {
        getTaskList();
    });

    $(document).on('click', MpmPESavedConfigurationList.delete_btn, function () {
        MpmPESavedConfigurationList.deleteConfiguration($(this).attr('data-id-configuration'));
    });

    $(document).on('click', PEExportProcessHistory.delete_btn, function () {
        PEExportProcessHistory.deleteExportProcess($(this).attr('data-id-export-process'));
    });

    $(document).on('click', '.assignment_item_task  .delete_option', function () {
        deleteTask($(this).attr('data-id'));
    });

    $(document).on('click', '.assignment_item .download_option', function () {
        downloadConfiguration($(this).attr('data-id'));
    });

    $(document).on('click', '.mpm_button_save', function () {
        saveExportConfiguration($(this).attr('data-id'));
    });

    $(document).on('click', '.exportproducts_overflow, .errors_form  button', function () {
        hideError();
    });

    $(document).on('click', '.exportproducts_overflow, .success_form  a.button_ok', function () {
        hideSuccess();
    });

    $(document).on('click', '.item_product_export_field', function () {
        createSelectedLine($(this), 1);
    });

    $(document).on('click', '.selected_export_field .remove_export_field', function () {
        removeSelectedLine($(this).parents('.selected_export_field'));
    });

    $(document).on("click", ".new_export_tab_button", function () {
        showNewExportTabContent($(this).attr('data-tab'));
    });

    $(document).on("click", ".mpm_button_next", function () {
        showNewExportTabContent($('.new_export_tab.active').attr('data-next-tab'));
    });

    $(document).on("click", ".mpm_button_prev", function () {
        showNewExportTabContent($('.new_export_tab.active').attr('data-prev-tab'));
    });

    $(document).on('click', '.automatically_state_item', function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active')
        }
        else {
            $(this).addClass('active')
        }
    });

    $(document).on('click', '.toggle_scheduled_tasks_buttons', function () {
        if ($(this).parent().hasClass('active')) {
            $(this).parent().removeClass('active');
        }
        else {
            $('.scheduled_tasks_buttons').removeClass('active');
            $(this).parent().addClass('active');
        }
    });

    $(document).on('click', '.toggle_settings_buttons', function () {
        if ($(this).parent().hasClass('active')) {
            $(this).parent().removeClass('active');
        }
        else {
            $('.settings_buttons').removeClass('active');
            $(this).parent().addClass('active');
        }
    });

    $(document).on('change', '.switch_myprestamodules .switch_content input[name=email_message]', function (e) {
        var messages = $('.switch_myprestamodules .switch_content input[name=email_message]:checked').val();
        if (messages == 1) {
            $('.export_one_line_messages_block').addClass('active');
        }
        else {
            $('.export_one_line_messages_block').removeClass('active');
        }
    });

    $(document).on('click', '.edit_export_field .edit_field', function () {
        if ($(this).parents('.selected_export_field').attr('data-tab') == 'staticTab') {
            getExtraFieldForm($(this).parents('.selected_export_field').attr('data-value'), $(this).parents('.selected_export_field').attr('data-line'));
        } else {
            $(this).hide();
            $(this).next().css('display', 'block');
            $(this).parents('.selected_export_field').addClass('change');
            $(this).parents('.selected_export_field').find('.change_name_field').show();
            $(this).parents('.selected_export_field').find('.change_name_field').focus();
        }
    });

    $(document).on('click', '.edit_export_field .save_field', function () {
        var new_field_name = $(this).parents('.selected_export_field').find('.change_name_field').val();
        
        var gmf_id = $(this).parents('.selected_export_field').data("gmf-id");
        var gmf_doc_link = $(this).parents('.selected_export_field').data("gmf-id");
        
        if (gmf_id && $("input[name='format_file']:checked").val() == 'gmf') {
            new_field_name += '<a class="gmf-label" href="'+gmf_doc_link+'" target="_blank" style="display:flex;">'+gmf_id+'<i class="mic-solid_external-link-alt"></i></a>';
        }
        
        $(this).hide();
        $(this).prev().css('display', 'block');
        $(this).parents('.selected_export_field').removeClass('change');
        $(this).parents('.selected_export_field').find('.change_name_field').hide();
        $(this).parents('.selected_export_field').find('.product_export_field_name').html(new_field_name);
        $(this).parents('.selected_export_field').attr('data-name', new_field_name);
    });

    $(document).on('click', '.add_all_fields', function () {
        $('.content_fields_tab.active li:visible').each(function () {
            createSelectedLine($(this), 0);
        });
    });

    $(document).on('click', '.remove_all_fields', function () {
        $('.selected_export_fields_list li').each(function () {
            removeSelectedLine($(this));
        });
    });
    
    $(document).on('keyup', '.checkbox_item_search', function () {
        var field = $(this).parents('.filter_section').attr('data-field');
        var search = $(this).val();
        var ids = {};
        $('.section_' + field + ' .selected_checkbox_list li').each(function (k, i) {
            ids[k] = $(this).attr('data-id');
        });
        searchFiltersFields(search, ids, field);
    });

    $(document).on('keyup', '.search_product_fields', function () {
        $('.content_fields_tab.active li').each(function () {
            if ($(this).text().toLowerCase().indexOf($('.search_product_fields').val().toLowerCase()) >= 0) {
                $(this).show();
            }
            else {
                $(this).hide();
            }
        });
    });

    $(document).on('keyup', '.search_selected_fields', function () {
        $('.selected_export_fields_list li').each(function () {
            if ($(this).text().toLowerCase().indexOf($('.search_selected_fields').val().toLowerCase()) >= 0) {
                $(this).show();
            }
            else {
                $(this).hide();
            }
        });
    });

    $(document).on('keyup', '.search_condition_fields', function () {
        $('.example_all_fields_list .example_one_field').each(function () {
            if ($(this).text().toLowerCase().indexOf($('.search_condition_fields').val().toLowerCase()) >= 0) {
                $(this).show();
            }
            else {
                $(this).hide();
            }
        });
    });

    MpmPECustomSelectField.init();

    $(document).on("click", ".export_tab_item", function () {
        getTabContent($(this).attr('data-tab'));
    });

    $(document).on("click", ".exportproducts_overflow", function () {
        removeAllOverlay();
    });

    $(document).on("click", ".categories_form_back", function () {
        removeAllOverlay();
    });
    $(document).on("click", ".item_product_field_tab", function () {
        var tab = $(this).attr('data-tab');
        $('.item_product_field_tab').removeClass('active');
        $('.content_fields_tab').removeClass('active');
        $(this).addClass('active');
        $('.' + tab + 'Content').addClass('active');
    });

    $(document).on("click", ".categories_form_continue", function () {
        setCategoriesTosection();
    });

    $(document).on("click", ".filter_section_remove", function () {
        $(this).parents('.filter_section').remove();
        if ($('.filter_section').length <= 0) {
            $('.not_has_selected_filter').addClass('active');
        }
    });

    $(document).on("click", ".section_categories .filter_select", function () {
        getCategoriesTree();
    });

    $(document).on("click", ".select_filter_list > div:not(.search-filter-container)", function () {
        if ($('.section_' + $(this).attr('data-id')).length > 0) {
            hideDropDownFilter();
        } else {
            getFilterBlock($(this).attr('data-type'), $(this).attr('data-id'), $(this).attr('data-label'));
        }
    });
    
    if ($("input[name='format_file']:checked").val() == 'gmf') {
        $(".gmf-label").css("display", "flex");
        $("#google_merchant_center_link").css("display", "flex");
        $(".item_product_export_field:not(.gmf-field)").hide();
    } else {
        $(".gmf-label").hide();
        $("#google_merchant_center_link").hide();
        $(".item_product_export_field:not(.gmf-field)").show();
    }

    $(document).on("change", ".select_file_format input[name=format_file]", function (e) {
        if ($(this).val() == 'csv') {
            $('.delimiter_separator').addClass('active');
        } else {
            $('.delimiter_separator').removeClass('active');
        }

        if ($(this).val() == 'gmf') {
            $(".gmf-label").css("display", "flex");
            $("#google_merchant_center_link").css("display", "flex");
            $(".item_product_export_field:not(.gmf-field)").hide();
        } else {
            $(".gmf-label").hide();
            $("#google_merchant_center_link").hide();
            $(".item_product_export_field:not(.gmf-field)").show();
        }
    });
    
    $(document).on("click", ".gmf-label", function(e) {
        e.stopPropagation();
    });
    
    $(".filter_option .select_type").each(function() {
        if ($(this).val() == "empty" || $(this).val() == "not_empty") {
            $(this).parents(".filter_option").siblings(".filter_values:not(.filter_date)").hide();
        } else {
            $(this).parents(".filter_option").siblings(".filter_values:not(.filter_date)").show();
        }
    });

    $(document).on("change", ".filter_option .select_type", function (e) {

        $(this).parents('.filter_section_data').find('.filter_values').removeClass('active');

        if ($(this).val() == "empty" || $(this).val() == "not_empty") {
            $(this).parents(".filter_option").siblings(".filter_values:not(.filter_date)").hide();
        } else {
            $(this).parents(".filter_option").siblings(".filter_values:not(.filter_date)").show();
        }
    
        if ($(this).val() == 'before_date' || $(this).val() == 'after_date') {
            $(this).parents('.filter_section_data').find('.filter_value_date_1').addClass('active');
            $(this).parents('.filter_section_data').find('.filter_value_date_2').removeClass('active');
        } else if ($(this).val() == 'period') {
            $(this).parents('.filter_section_data').find('.filter_value_date_1').addClass('active');
            $(this).parents('.filter_section_data').find('.filter_value_date_2').addClass('active');
        } else {
            $(this).parents('.filter_section_data').find('.filter_value_date_1').removeClass('active');
            $(this).parents('.filter_section_data').find('.filter_value_date_2').removeClass('active');
        }
    });

    $(document).on("change", ".feed_target_block input[name=feed_target]", function (e) {
        if ($(this).val() == 'ftp') {
            $('.mpm_button_default').removeClass('active');
            $('.mpm_button_default_second').addClass('active');
            $('.ftp_access_block').addClass('active');
        }
        else {
            $('.mpm_button_default').addClass('active');
            $('.mpm_button_default_second').removeClass('active');
            $('.ftp_access_block').removeClass('active');
        }
    });

    $(document).on("click", ".select_filter", function (e) {
        if ($(this).hasClass('active')) {
            hideDropDownFilter();
        } else {
            showDropDownFilter();
        }
    });

    $(document).on("click", ".selected_checkbox_list li span", function () {
        var id = $(this).parent().attr('data-id');
        var section = $(this).parents('.filter_section').attr('data-field');
        var class_section = 'section_' + section;
        $(this).parent().remove();
        $('.filter_section.' + class_section + ' .filter_checkbox_list .filter_checkbox_item_' + id).removeClass('active');
    })

    $(document).on("click", ".filter_checkbox_list li.filter_checkbox_item", function () {
        var id = $(this).attr('data-id');
        var label = $(this).attr('data-label');
        var section = $(this).parents('.filter_section').attr('data-field');
        var class_section = 'section_' + section;
        if ($('.filter_section.' + class_section + ' .filter_option_line_checkbox .selected_item_' + id).length > 0) {

        }
        else {
            $('.filter_section.' + class_section + ' .filter_checkbox_list .filter_checkbox_item_' + id).addClass('active');
            $('.filter_section.' + class_section + ' .selected_checkbox_list').prepend('<li data-id="' + id + '" class="selected_item_' + id + '">' + label + '<span><i class="mic-times-solid"></i></span> </li>');
        }
    });

    $(document).on("click", ".filter_option_line_checkbox .filter_select", function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $(this).parent().next().removeClass('active');
        }
        else {
            $('.filter_select ').removeClass('active')
            $('.filter_checkbox_block ').removeClass('active')
            $(this).addClass('active');
            $(this).parent().next().addClass('active');
        }
    });

    $(document).on("input", MpmPECustomSelectField.search_input, function (e) {
        MpmPECustomSelectField.search($(this));
    });

    $(document).on("click", MpmPECustomSelectField.container, function (e) {
        e.stopPropagation();

        if ($(e.target).parent().hasClass(MpmPECustomSelectField.list_of_options.replace('.', ''))) {
            return false;
        }

        if ($(this).hasClass(MpmPECustomSelectField.options_list_is_opened_class)) {
            MpmPECustomSelectField.hideOptions($(this).find(MpmPECustomSelectField.list_of_options));
        } else {
            MpmPECustomSelectField.showOptions($(this).find(MpmPECustomSelectField.list_of_options));
        }
    });

    $(document).on("click", MpmPECustomSelectField.option, function () {
        if ($(this).hasClass('search-option')) {
            return false;
        }

        var parent = $(this).parents(MpmPECustomSelectField.init_wrapper);
        parent.find("select").val($(this).attr("data-value"));
        parent.find(MpmPECustomSelectField.selected).html($(this).text());
        parent.find("select").change();
        MpmPECustomSelectField.hideOptions(parent.find(MpmPECustomSelectField.list_of_options));
    });

    $(document).on("change", MpmPECustomSelectField.init_wrapper + " select", function () {
        var selected = $(this).parent().find(MpmPECustomSelectField.selected);
        var currentText = $.trim(selected.text());
        var selectedText = $.trim($(this).find('option[value="' + $(this).val() + '"]').text());
        if (selectedText !== currentText) {
            selected.html(selectedText);
        }
    });

    $(document).mouseup(function (e) {
        var select_options = $(MpmPECustomSelectField.list_of_options);
        if (!select_options.is(e.target) && select_options.has(e.target).length === 0 && !$(MpmPECustomSelectField.selected).is(e.target) && !$(MpmPECustomSelectField.container).is(e.target)) {
            MpmPECustomSelectField.hideOptions(select_options);
        }
    });

    $(document).on('keyup', '.frequency_task_content .frequency_task', function () {
        var expression = $(this).val();
        if (!expression) {
            $('.frequency_task_content').addClass('error');
        }
        else {
            $.ajax({
                type: "POST",
                url: "index.php",
                dataType: 'json',
                data: {
                    ajax: true,
                    token: $('input[name=token_product_export]').val(),
                    controller: 'AdminProductsExport',
                    action: 'validateCronExpression',
                    expression: expression
                },
                success: function (json) {
                    if (json['human_description']) {
                        $('.task_frequency_description .description_text').html('«' + json['human_description'] + "»");
                        $('.frequency_task_content').removeClass('error');
                    }
                    if (json['expression']) {
                        $('.task_frequency_description .minutes .value').html(json['expression']['min']);
                        $('.task_frequency_description .hours .value').html(json['expression']['hour']);
                        $('.task_frequency_description .day_of_month .value').html(json['expression']['day_of_month']);
                        $('.task_frequency_description .month .value').html(json['expression']['month']);
                        $('.task_frequency_description .day_of_week .value').html(json['expression']['day_of_week']);


                        if (json['next_run']) {
                            $('.form_task_right_column .next_run_info').html(json['next_run']);
                        }
                    }
                    if (json['error']) {
                        $('.frequency_task_content').addClass('error');
                    }
                }
            });
        }
    });
    
    $(document).on("change", "input[name='mpm_pe_debug_mode']", function() {
        var debug_mode = $("#mpm_pe_debug_mode_yes").is(":checked") ? 1 : 0;
        
        $.ajax({
            type: "POST",
            url: "index.php",
            dataType: 'json',
            data: {
                ajax: true,
                token: $('input[name=token_product_export]').val(),
                controller: 'AdminProductsExport',
                action: 'changeDebugMode',
                debug_mode: debug_mode
            },
            beforeSend: function() {
                showLoader();
            },
            success: function (json) {
                if (json['message']) {
                    alert(json['message']);
                }
                
                setDebugModeStatus(debug_mode);
            },
            error: function() {
                alert("Ajax request has failed!");
            },
            complete: function() {
                hideLoader();
            }
        });
    });
    
    $(document).on("click", "#mpm_pe_google_categories_association_container input[name='google_category[]']", function() {
        if ($(this).is(":checked")) {
            return getGoogleCategoryAssocBlock($(this).val());
        }
    
        return removeGoogleCategoryAssocBlock($(this).val());
    });
    
    $(document).on("click", "#check-all-google_categories_tree", function() {
        var category_ids = [];
        $("#mpm_pe_google_categories_association_container input[name='google_category[]']").each(function() {
            if (isShopCategoryAlreadyAssociatedWithGoogle($(this).val())) {
                return;
            }
            
            category_ids.push($(this).val());
        });
        
        getAllGoogleCategoryAssocBlocks(category_ids);
    });
    
    $(document).on("click", "#uncheck-all-google_categories_tree", function() {
        $("#mpm_pe_google_categories_association_container input[name='google_category[]']").each(function() {
            removeGoogleCategoryAssocBlock($(this).val());
        });
    });
    
    $(document).on("click", "#mpm_pe_google_categories_association_container .exportproducts_block_title i", function() {
        var container = $(this).parents("#mpm_pe_google_categories_association_container");
        if (container.hasClass("opened")) {
            return container.removeClass("opened");
        }
    
        return container.addClass("opened");
    });
    
    var search_timeout;
    $(document).on("input", ".google-category-assoc-container .fixed-search", function () {
        if (search_timeout) {
            clearTimeout(search_timeout);
        }
        
        var google_category_container = $(this).parents(".google-category-assoc-container");
        var search_query = $(this).val();
    
        if (search_query.length < 3) {
            google_category_container.find(".mpm-fpe-select-list-of-options").html("");
            google_category_container.find("select.google-category").html("");
            var search_msg = "<span class='search-msg'>Type at least 3 characters for search</span>";
            google_category_container.find(".mpm-fpe-select-list-of-options").append(search_msg);
        
            return false;
        }
    
        search_timeout = setTimeout(function() {
            $.ajax({
                type: "POST",
                url: "index.php",
                dataType: 'json',
                data: {
                    ajax: true,
                    token: $('input[name=token_product_export]').val(),
                    controller: 'AdminProductsExport',
                    action: 'searchGoogleCategory',
                    search_query: search_query,
                },
                beforeSend: function () {
                    $(".fixed-search-container .search-mini-loader").show();
                },
                success: function (response) {
                    if (response['error']) {
                        google_category_container.find("select.google-category").html("");
                        google_category_container.find(".mpm-fpe-select-list-of-options").html("");
                        return false;
                    }
                    
                    if (response['search_results']) {
                        google_category_container.find(".mpm-fpe-select-list-of-options").html("");
                        google_category_container.find("select.google-category").html("");
                        var search_results = response['search_results'];
    
                        if (search_results.length === 0) {
                            var search_msg = "<span class='search-msg'>Can't find google category with this name</span>";
                            google_category_container.find(".mpm-fpe-select-list-of-options").append(search_msg);
                            
                            return false;
                        }
                        
                        $.each(search_results, function(index, google_category) {
                            var select_option = "<option value='"+google_category['id']+"'>"+google_category['title']+"</option>";
                            google_category_container.find("select.google-category").append(select_option);
                            
                            var custom_select_option = "<span class='mpm-fpe-select-option' data-value='"+google_category['id']+"'>"+google_category['title']+"</span>";
                            google_category_container.find(".mpm-fpe-select-list-of-options").append(custom_select_option);
                        });
                    }
                },
                error: function() {
                    alert('Ajax Request Has Failed!');
                },
                complete: function() {
                    $(".fixed-search-container .search-mini-loader").hide();
                }
            });
        }, 1000);
    });

    toggleExportFileNamePreview();
    $(document).on("change input", "input[name='file_name'], input[name='format_file']", function() {
        toggleExportFileNamePreview();
    });

    $(document).on("change", "select.extra_field_formula_type", function() {
       toggleFormatAsPriceSwitch($(this));
       toggleFindAndReplaceExample($(this));
    });
    
    $(document).on("click", "#mpm_pe_clear_history", function() {
        var confirmed = confirm("Be careful, this action will remove all previous exports data. Are you sure that you want to do that?");
        
        if (!confirmed) {
            return false;
        }
        
        $.ajax({
            type: "POST",
            url: "index.php",
            dataType: 'json',
            data: {
                ajax: true,
                token: $('input[name=token_product_export]').val(),
                controller: 'AdminProductsExport',
                action: 'clearHistory'
            },
            beforeSend: function() {
                showLoader();
            },
            success: function (json) {
                if (json['result']) {
                    location.reload();
                } else {
                    alert("Some error occurred. Please contact us!");
                }
            },
            error: function() {
                alert("Ajax request has failed!");
            },
            complete: function() {
                hideLoader();
            }
        });
    });
});

function toggleExportFileNamePreview()
{
    if (!$("input[name='file_name']").length || !$("input[name='format_file']:checked").length) {
        return false;
    }

    var export_file_name = $("input[name='file_name']").val();
    var export_file_format = $("input[name='format_file']:checked").val();

    if (export_file_format == 'gmf') {
        export_file_format = 'xml';
    }

    var export_file_link_preview_container = $(".export-file-link-preview-container");
    var export_files_path = export_file_link_preview_container.data("export-files-path");

    if (export_file_name == '' || export_file_name.match(/{.*}/)) {
        $(".export-file-link-preview-container").hide();
        return false;
    }

    $(".export-file-link-preview-container").show();

    var export_file_link = export_files_path + export_file_name + "." + export_file_format;
    $(".export-file-link-preview-container").find("a").attr("href", export_file_link);
    $(".export-file-link-preview-container").find("a").text(export_file_link);

    return true;
}

var MpmPECustomSelectField = {
    init_wrapper: ".mpm-fpe-select-wrapper",
    container: ".mpm-fpe-select-container",
    selected: ".mpm-fpe-select-selected-value",
    list_of_options: ".mpm-fpe-select-list-of-options",
    option: ".mpm-fpe-select-option",
    search_input: ".mpm-fpe-select-option.search-option input",
    options_list_is_opened_class: "opened-options-list",
    init: function () {
        $.each($(MpmPECustomSelectField.init_wrapper), function (value) {
            if ($(this).find(MpmPECustomSelectField.container).length > 0) {
                return;
            }

            var current_value = $(this).find("select").val();
            var custom_select = $("<span class='mpm-fpe-select-container'><span class='mpm-fpe-select-list-of-options'></span></span>");
            var selected = "";

            if ($(this).hasClass("search-enabled")) {
                var option = "<span class='mpm-fpe-select-option search-option'><input type='text' placeholder='Search...'></span>";
                custom_select.find(MpmPECustomSelectField.list_of_options).append(option);
            }

            $.each($(this).find('select option'), function (key, value) {
                if ($(this).attr("value") == current_value) {
                    selected = "<span class='mpm-fpe-select-selected-value'>" + $(this).text() + "</span>";
                }

                var option = "<span class='mpm-fpe-select-option' data-value='" + $(this).attr('value') + "'>" + $(this).text() + "</span>";
                custom_select.find(MpmPECustomSelectField.list_of_options).append(option);
            });
    
            if ($(this).hasClass("fixed-search-enabled")) {
                var search_loader_path = $(".exportproducts_form").data("img-folder") + "svg/loading.svg";
                custom_select.prepend("<div class='fixed-search-container'><input class='fixed-search' type='text' placeholder='Search...'><img class='search-mini-loader' src='"+search_loader_path+"'></div>");
            }
            
            custom_select.prepend(selected);
            $(this).append(custom_select);
        });
    },
    showOptions: function (this_options_list) {
        var container = this_options_list.parents(MpmPECustomSelectField.container);
        MpmPECustomSelectField.hideOptions($(MpmPECustomSelectField.list_of_options));
        this_options_list.show();
        $(container).addClass(MpmPECustomSelectField.options_list_is_opened_class);
        MpmPECustomSelectField.resetSearch();
        container.find(".fixed-search-container").show();
    },
    hideOptions: function (this_options_list) {
        var container = this_options_list.parents(MpmPECustomSelectField.container);
        container.find(".fixed-search-container").hide();
        this_options_list.hide();
        $(container).removeClass(MpmPECustomSelectField.options_list_is_opened_class);
        MpmPECustomSelectField.resetSearch();
    },
    search: function(search_input) {
        var search_value = search_input.val();

        if (!search_value) {
            MpmPECustomSelectField.resetSearch();
            return true;
        }

        search_input.parent().siblings(MpmPECustomSelectField.option).each(function() {
            var option_name = $(this).text().toLowerCase();

            if (option_name.includes(search_value.toLowerCase())) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    },
    resetSearch: function() {
        $(MpmPECustomSelectField.search_input).val("");
        $(MpmPECustomSelectField.option).show();
    }
};

function toggleFormatAsPriceSwitch(formula_type_select)
{
    var is_math_formula_type = (formula_type_select.val() == 2);

    if (is_math_formula_type) {
        formula_type_select.parents(".extra_fields_row").find(".format-as-price-input-group").show();
    } else {
        formula_type_select.parents(".extra_fields_row").find(".format-as-price-input-group").hide();
    }
}

function toggleFindAndReplaceExample(formula_type_select)
{
    var is_find_and_replace_formula_type = (formula_type_select.val() == 3);
    
    if (is_find_and_replace_formula_type) {
        formula_type_select.parents(".extra_fields_row").find(".find-and-replace-example").show();
    } else {
        formula_type_select.parents(".extra_fields_row").find(".find-and-replace-example").hide();
    }
}

function toggleExtraFieldModalFieldsVisibilityByConditionValue(condition_select)
{
    var selected_condition = condition_select.val();

    var condition_empty = 7;
    var condition_not_empty = 8;
    var condition_any = 10;

    if (selected_condition == condition_any) {
        $("#field_to_which_condition_is_applied_container").hide();
        $(".add_more_condition_button").hide();
        condition_select.parents(".extra_fields_row").find(".condition-value-input-group").addClass("hidden");
    } else if (selected_condition == condition_empty || selected_condition == condition_not_empty) {
        $("#field_to_which_condition_is_applied_container").show();
        $(".add_more_condition_button").show();
        condition_select.parents(".extra_fields_row").find(".condition-value-input-group").addClass("hidden");
    } else {
        $("#field_to_which_condition_is_applied_container").show();
        $(".add_more_condition_button").show();
        condition_select.parents(".extra_fields_row").find(".condition-value-input-group").removeClass("hidden");
    }
}

function setCategoriesTosection() {
    $.each($('#categories-tree .tree-selected'), function (key, value) {
        var id = $(this).find('input').val();
        var label = $(this).find('label').html();
        if ($('.filter_section.section_categories .selected_checkbox_list .selected_item_' + id).length <= 0) {
            $('.filter_section.section_categories .selected_checkbox_list').prepend('<li data-id="' + id + '" class="selected_item_' + id + '">' + label + '<span><i class="mic-times-solid"></i></span> </li>');
        }
    });
    
    removeAllOverlay();
}

function getFilterBlock(type, id, label) {
    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'getFilterBlock',
            type: type,
            id: id,
            label: label,
        },
        beforeSend: function() {
            showLoader();
        },
        success: function (json) {
            if (json['error']) {
                return showError(json['error']);
            }
            
            if (json['filter']) {
                hideDropDownFilter();
                $('.not_has_selected_filter').removeClass('active');
                $('.selected_filter_list').append(json['filter']);
                if (type == 'date') {
                    $('.section_' + id + ' .filter_date input').datepicker({
                        dateFormat: "yy-mm-dd",
                    });
                }
            }
            setTimeout(function () {
                MpmPECustomSelectField.init();
            }, 100);
        },
        complete: function() {
            hideLoader();
        }
    });
}

function getCategoriesTree() {
    var categories = {};
    $('.filter_section.section_categories .selected_checkbox_list li').each(function (n) {
        categories[n] = $(this).attr('data-id');
    });
    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'getCategoriesTree',
            categories: categories,
        },
        success: function (json) {
            if (json['error']) {
                return showError(json['error']);
            }
            
            if (json['tree']) {
                $('body').append(json['tree']);
                positionForm($('.categories_form_product_export.modal-tree'))
                showOverlayForModalWindow();
            }
        }
    });
}

function getGoogleCategoryAssocBlock(id_category) {
    if (isShopCategoryAlreadyAssociatedWithGoogle(id_category)) {
        return false;
    }
    
    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'getGoogleCategoryAssocBlock',
            id_category: id_category,
        },
        beforeSend: function () {
            showLoader();
        },
        success: function (json) {
            if (json['error']) {
                hideLoader();
                return showError(json['error']);
            }
            
            if (json['tpl']) {
                $("#mpm_pe_google_categories_association .main-content").append(json['tpl']);
                setTimeout(function () {
                    MpmPECustomSelectField.init();
                    hideLoader();
                }, 100);
    
                addShopCategoryIdToListOfAssociatedWithGoogle(id_category);
            }
        },
        error: function() {
            alert('Ajax Request Has Failed!');
            hideLoader();
        }
    });
}

function getAllGoogleCategoryAssocBlocks(category_ids) {
    if (category_ids.length == 0) {
        return false;
    }
    
    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'getAllGoogleCategoryAssocBlocks',
            category_ids: category_ids,
        },
        beforeSend: function () {
            showLoader();
        },
        success: function (json) {
            if (json['error']) {
                hideLoader();
                return showError(json['error']);
            }
            
            if (json['tpl']) {
                $("#mpm_pe_google_categories_association .main-content").append(json['tpl']);
                setTimeout(function () {
                    MpmPECustomSelectField.init();
                    hideLoader();
                }, 100);
                
                category_ids.forEach(function(id_category) {
                    addShopCategoryIdToListOfAssociatedWithGoogle(id_category);
                });
            }
        },
        error: function() {
            alert('Ajax Request Has Failed!');
            hideLoader();
        }
    });
}

function getGoogleCategories() {
    var google_categories = {};
    
    $("#mpm_pe_google_categories_association .google-category").each(function(index) {
        var id_google_category = $(this).val();
        
        if (!id_google_category) {
            return false;
        }
        
        var shop_category_id = $(this).parents(".google-category-assoc-container").data("id-category");
        google_categories[shop_category_id] = id_google_category;
    });
    
    return google_categories;
}

function removeGoogleCategoryAssocBlock(id_category) {
    $(".google-category-assoc-container[data-id-category='"+id_category+"']").remove();
    removeShopCategoryIdFromListOfAssociatedWithGoogle(id_category);
}

function isShopCategoryAlreadyAssociatedWithGoogle(id_category) {
    var shop_categories_associated_with_google = getShopCategoriesAssociatedWithGoogle();
    return shop_categories_associated_with_google.includes(id_category);
}

function addShopCategoryIdToListOfAssociatedWithGoogle(id_category) {
    var shop_categories_associated_with_google = getShopCategoriesAssociatedWithGoogle();
    shop_categories_associated_with_google.push(id_category);
    shop_categories_associated_with_google = shop_categories_associated_with_google.join(",");
    
    return $("#mpm_pe_google_categories_association").data("selected-shop-categories", shop_categories_associated_with_google);
}

function removeShopCategoryIdFromListOfAssociatedWithGoogle(id_category) {
    var shop_categories_associated_with_google = getShopCategoriesAssociatedWithGoogle();
    shop_categories_associated_with_google = shop_categories_associated_with_google.filter(function(e) {
        return e !== id_category;
    });
    
    shop_categories_associated_with_google = shop_categories_associated_with_google.join(",");
    
    return $("#mpm_pe_google_categories_association").data("selected-shop-categories", shop_categories_associated_with_google);
}

function getShopCategoriesAssociatedWithGoogle() {
    var shop_categories_associated_with_google = $("#mpm_pe_google_categories_association").data("selected-shop-categories");
    shop_categories_associated_with_google = shop_categories_associated_with_google.toString();
    return shop_categories_associated_with_google.split(",");
}

function positionForm(el) {
    var top = $(document).scrollTop();
    var height_window = $(window).outerHeight();
    var width_window = $(window).outerWidth();
    var height_form = el.outerHeight();
    var width_form = el.outerWidth();
    var margin = (height_window - height_form) / 2;
    var margin_left = (width_window - width_form) / 2;
    var margin_top = margin + top - 50 - 160;

    if (margin_top < 10) {
        margin_top = 10;
    }

    el.css('top', margin_top + 'px');
    el.css('left', margin_left + 'px');
}

function removeSelectedLine(obj) {

    var value = obj.attr('data-value');
    var tab = obj.attr('data-tab');
    obj.remove();

    if ($('.' + value + tab).length <= 0) {
        $('.' + tab + value).removeClass('selected')
    }

    changePositionFields();

    if ($('.selected_export_fields_list li').length <= 0) {
        $('.selected_export_fields').removeClass('active');
        $('.no_selected_fields').addClass('active');
    }
}

function getExportFileFormat() {
    return $("input[name='format_file']:checked").val();
}

function changeCustomField(field, field_type) {
    var class_line = '.' + field + 'staticTab';
    var field_name = $('.extra_field_form .static_field_name').val();
    
    var gmf_id = 0;
    var gmf_doc_link = 0;
    
    if (getExportFileFormat() === 'gmf') {
        gmf_id = $('.extra_field_form .gmf-attribute option:selected').val();
        gmf_doc_link = $('.extra_field_form .gmf-attribute option:selected').data("gmf-doc-link");
    }
    
    $(class_line).attr('data-name', field_name);
    $(class_line).find('.product_export_field_name').html(field_name);


    if (field_type == 'static') {
        var field_value = $('.extra_field_form .default_static_field_value').val();
        $(class_line).attr('data-default-value', field_value);
        
        if (!$(class_line).find('.product_export_field_default_value').length) {
            var static_field_value_block = '<span class="product_export_field_default_value">'+field_value+'</span>';
            $(class_line).find('.export_field_name').append(static_field_value_block);
        } else {
            $(class_line).find('.product_export_field_default_value').html(field_value);
        }
    }

    if (field_type == 'extra') {

        var condition_field = $('.extra_field_form .condition_field').val();
        $(class_line).attr('data-condition-field', condition_field);

        var conditions = {};
        var condition_value = {};
        var formula_type = {};
        var formula = {};
        var format_as_price = {};
        var formula_label = "";

        $('.extra_field_item_condition').each(function (k, i) {
            var id_condition = $(this).attr('data-id');

            conditions[k] = $('.extra_field_form .extra_fields_row_'+id_condition+' .condition').val();
            condition_value[k] = $('.extra_field_form  .extra_fields_row_'+id_condition+' .condition_value').val();
            formula_type[k] = $('.extra_field_form .extra_fields_row_'+id_condition+' .extra_field_formula_type').val();
            formula[k] = $('.extra_field_form .extra_fields_row_'+id_condition+' .static_field_value').val();
            format_as_price[k] = $('.extra_field_form input[name="format_as_price_'+id_condition+'"]:checked').val();

            if (k == 0) {
                formula_label = $('.extra_field_form .extra_fields_row_'+id_condition+' .static_field_value').val();
            } else{
                formula_label = formula_label+','+$('.extra_field_form .extra_fields_row_'+id_condition+' .static_field_value').val();
            }
        });
        
        var formula_block = "<span class='product_export_field_default_value'>"+formula_label+"</span>";

        conditions = JSON.stringify(conditions);
        condition_value = JSON.stringify(condition_value);
        formula_type = JSON.stringify(formula_type);
        formula = JSON.stringify(formula);
        format_as_price = JSON.stringify(format_as_price);

        $(class_line).attr('data-condition', conditions);
        $(class_line).attr('data-condition-value', condition_value);
        $(class_line).attr('data-formula-type', formula_type);
        $(class_line).attr('data-default-value', formula);
        $(class_line).attr('data-format-as-price', format_as_price);
    
        if (getExportFileFormat() === 'gmf' && (gmf_id && gmf_id !== 'none')) {
            $(class_line).attr('data-gmf-id', gmf_id);
            $(class_line).attr('data-gmf-doc-link', gmf_doc_link);
            
            var gmf_label = '<a class="gmf-label" href="'+gmf_doc_link+'" target="_blank" style="display:flex;">'+gmf_id+'<i class="mic-solid_external-link-alt"></i></a>';
            
            $(class_line).find(".product_export_field_name").html(field_name + gmf_label);

            $(class_line).find(".export_field_name").find(".product_export_field_default_value").remove();
            $(class_line).find(".export_field_name").append(formula_block);
        } else {
            $(class_line).attr('data-gmf-id', gmf_id);
            $(class_line).attr('data-gmf-doc-link', gmf_doc_link);
            $(class_line).find(".product_export_field_name").html(field_name);
            $(class_line).find(".export_field_name").find(".product_export_field_default_value").remove();
            $(class_line).find(".export_field_name").append(formula_block);
        }
    }
    removeAllOverlay();
}

function copyToClipboard(text) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(text).select();
    document.execCommand("copy");
    $temp.remove();
}

function changeTaskStatus(status, id_task) {
    var new_status = (status == 1) ? 0 : 1;

    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'changeTaskStatus',
            new_status: new_status,
            id_task: id_task,
        },
        success: function (json) {
            if (json['error']) {
                return showError(json['error']);
            }
            
            if (json['success']) {
                if (new_status == 1) {
                    $('.item_scheduled_task.item_scheduled_task_' + id_task + ' .task-status').addClass('enabled').removeClass('disabled');
                } else {
                    $('.item_scheduled_task.item_scheduled_task_' + id_task + ' .task-status').removeClass('enabled').addClass('disabled');
                }

                $('.item_scheduled_task.item_scheduled_task_' + id_task + ' .task-status span').attr('data-val', new_status);
            }

        }
    });
}


function getExtraFieldForm(field, type) {

    var condition_field = false;
    var condition = false;
    var condition_value = false;
    var class_line = field + 'staticTab';
    var name_field = $('.' + class_line).attr('data-name');
    var value_default_field = $('.' + class_line).attr('data-default-value');
    var formula_type = false;
    var format_as_price = false;

    if (type == 'extra') {
        condition_field = $('.' + class_line).attr('data-condition-field');
        condition = $('.' + class_line).attr('data-condition');
        formula_type = $('.' + class_line).attr('data-formula-type');
        condition_value = $('.' + class_line).attr('data-condition-value');
        format_as_price = $('.' + class_line).attr('data-format-as-price');
    }

    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'getExtraFieldForm',
            type: type,
            custom_field: field,
            name_field: name_field,
            value_default_field: value_default_field,
            condition_field: condition_field,
            condition: condition,
            formula_type: formula_type,
            format_as_price: format_as_price,
            condition_value: condition_value,
            export_file_format: $("input[name='format_file']:checked").val(),
            selected_gmf_attribute: $('.' + class_line).attr('data-gmf-id')
        },
        success: function (json) {
            if (json['error']) {
                return showError(json['error']);
            }
            
            if (json['form']) {
                $('body').append(json['form']);
                positionForm($('.extra_field_form'))
                showOverlayForModalWindow();
                setTimeout(function () {
                    MpmPECustomSelectField.init();
                }, 100);
    
                $("select.condition").each(function() {
                    toggleExtraFieldModalFieldsVisibilityByConditionValue($(this));
                });

                $("select.extra_field_formula_type").each(function() {
                   toggleFormatAsPriceSwitch($(this));
                   toggleFindAndReplaceExample($(this));
                });
            }
        }
    });

}

function createSelectedLine(obj, animation) {

    if (animation) {
        var marginLeft = $('.product_field_tabs_list').outerWidth() + 20;
        var move_to = parseInt($('.static_custom_row').offset().top) - parseInt($('.product_fields_scroll').offset().top);
        var class_clone = 'clone_line_' + parseInt(Math.random() * 10000);
        var clone_line = obj.clone().addClass('clone_line').addClass(class_clone).css({
            width: obj.outerWidth(),
            top: parseInt(obj.offset().top) - parseInt($('.product_fields_scroll').offset().top)
        });
        
        obj.after(clone_line);
        $('.' + class_clone).animate({
            top: move_to,
            marginLeft: -marginLeft,
            width: obj.outerWidth() + marginLeft
        }, 1, function () {
            setTimeout(function () {
                $('.' + class_clone).remove()
            }, 2000)
        });
    }

    obj.addClass('selected');
    var name = obj.attr('data-name');
    var value = obj.attr('data-value');
    var gmf_id = obj.attr('data-gmf-id');
    var gmf_doc_link = obj.attr('data-gmf-doc-link');
    var gmf_label = "";
    var tab = obj.attr('data-tab');
    var line = 'default';

    if (tab == 'staticTab') {
        var id = parseInt(Math.random() * 10000);
        var value = obj.attr('data-value') + '_' + id;

        if (obj.attr('data-value') == 'static_field') {
            var line = 'static';
        }
        else {
            var line = 'extra';
        }
    }

    var id = 1;
    if ($('.selected_export_fields_list li:last-child').length > 0) {
        id = parseInt($('.selected_export_fields_list li:last-child').find('.id_export_field').attr('data-id')) + 1;
    }
    
    if (gmf_id) {
        gmf_label =  "<a class='gmf-label' href='"+gmf_doc_link+"' target='_blank'>" + gmf_id + "<i class='mic-solid_external-link-alt'></i></a></span>";
    }
    
    var line = "<li class='selected_export_field " + value + tab + "' data-line='" + line + "'  data-name='" + name + "' data-value='" + value + "' data-gmf-id='" + gmf_id + "' data-gmf-doc-link='" + gmf_doc_link + "' data-tab='" + tab + "' data-default-value=''>" +
            "<div class='id_field_column'><span class='id_export_field' data-id='" + id + "'>" + id + "</span></div>" +
            "<div class='move_export_column'><span><i class='mic-arrows-alt-v-solid'></i></span></div>" +
            "<div class='export_field_name'>" +
            "<span class='product_export_field_name'>" + name + gmf_label +
            "<span class='product_export_field_default_value'></span>" +
            "<input class='change_name_field' value='" + name + "'>";

    line = line + "</div>" +
            "<div class='edit_export_field'><span class='edit_field'><i class='mic-cogs-solid'></i></span><span class='save_field'><i class='mic-check-mark'></i></span></div>" +
            "<div class='remove_export_field'><span><i class='mic-minus-circle-solid'></i></span></div><span class='clear_both'></span>" +
            "</li>";

    if ($('.selected_export_fields_list tr:last-child').length > 0) {
        $('.selected_export_fields_list tr:last-child').after(line);
    } else {
        $('.no_selected_fields').removeClass('active');
        $('.selected_export_fields').addClass('active');
        $('.selected_export_fields_list').append(line);
    }
    
    if ($(".gmf-label").is(":visible") && gmf_id) {
        $(".gmf-label").css("display", "flex");
    }

    if(tab == 'staticTab'){
        getExtraFieldForm($('.selected_export_field.'+value + tab).attr('data-value'), $('.selected_export_field.'+value + tab).attr('data-line'));
    }
}

function searchFiltersFields(search, checked, field) {
    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'searchFiltersFields',
            search: search,
            checked: checked,
            field: field,
        },
        beforeSend: function () {

        },
        success: function (json) {
            if (json['error']) {
                return showError(json['error']);
            }
            
            if (json['success']) {
                $('.filter_section.section_' + field + ' .filter_checkbox_list').html(json['success'])
            }
        }
    });
}

function getTabContent(tab) {
    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'getTabContent',
            tab: tab,
            id_configuration: 0,
        },
        beforeSend: function () {
            showLoader();
        },
        success: function (json) {
            if (json['error']) {
                return showError(json['error']);
            }
            
            if (json['tab_content']) {
                $('.product_export_tab_content .tab_content').replaceWith(json['tab_content']);
                $('.export_tab_item').removeClass('active');
                $('.export_tab_item.tab_' + tab).addClass('active');

                if (json['tab_url']) {
                    if (window.history && history.pushState) {
                        if (json['tab_url'] != window.location) {
                            window.history.pushState(null, null, json['tab_url']);
                        }
                    }
                }

            }

            setTimeout(function () {
                MpmPECustomSelectField.init();
            }, 100)

            hideLoader();
        },
        complete: function() {
            hideLoader();
        }
    });
}

function getRelatedModules() {
    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'getRelatedModules',
        },
        beforeSend: function () {
            showLoader();
        },
        success: function (json) {
            if (json['error']) {
                return showError(json['error']);
            }
            
            if (json['related_modules']) {
                $('.related_modules_list').html(json['related_modules']);
            }
        },
        complete: function() {
            hideLoader();
        }
    });
}

function hideDropDownFilter() {
    $('.select_filter').removeClass('active');
    $('.select_filter_list').removeClass('active');
}

function showDropDownFilter() {
    $('.select_filter_list .filter_field').show();
    $('.search-filter').val("");
    
    $('.select_filter').addClass('active');
    $('.select_filter_list').addClass('active');
}

function showError(error_modal) {
    MpmPEExportStatusModal.hide();
    $('body').append(error_modal);
    
    $('.errors_form').addClass('active');
    showOverlayForModalWindow();
}

function showSuccess() {
    $('.success_form').addClass('active');
    showOverlayForModalWindow();
}

function hideError() {
    $('.errors_form').removeClass('active');
    setTimeout(function () {
        $('.errors_form').remove();
    }, 100);
    removeAllOverlay();
}

function hideSuccess() {
    $('.success_form').removeClass('active');
    setTimeout(function () {
        $('.success_form').remove();
    }, 100)
    removeAllOverlay();
}

function showOverlayForModalWindow() {
    $('.exportproducts_overflow').addClass('active');
}

function showLoader() {
    $('.exportproducts_loader').addClass('active');
}

function hideLoader() {
    $('.exportproducts_loader').removeClass('active');
}

function removeAllOverlay() {
    if (!MpmPEExportStatusModal.isVisible()) {
        $('.exportproducts_overflow').removeClass('active');
    }

    if ($('.categories_form_product_export.modal-tree').length > 0) {
        $('.categories_form_product_export.modal-tree').remove();
    }

    if ($('.form_preview_file').length > 0) {
        $('.form_preview_file').remove();
    }

    if ($('.extra_field_form').length > 0) {
        $('.extra_field_form').remove();
    }
}

function changePositionFields() {
    setTimeout(function () {
        $('.selected_export_fields_list li').each(function (k, i) {
            $(this).find('.id_export_field').attr('data-id', k + 1);
            $(this).find('.id_export_field').html(k + 1);
        });
    }, 100)
}

function showNewExportTabContent(tab) {
    if (!tab) {
        return false;
    }
    
    $('.new_export_tab').removeClass('active');
    $('.' + tab + '_tab').addClass('active');
    
    if (tab == 'filter_product_fields') {
        $('.selected_export_fields_list').sortable({
            revert: false,
            axis: "y",
            cursor: "move",
            handle: ".move_export_column",
            items: "> li",
            out: function (event, ui) {
                changePositionFields();
            }
        });

        $('.selected_export_fields_list .selected_export_field').each(function (n) {
            var tab = $(this).attr('data-tab');
            var field = $(this).attr('data-value');
            $('.' + tab + field).addClass('selected');
        });
    }
}

function getExportFields() {
    var fields = {};
    $('.selected_export_fields_list .selected_export_field').each(function (n) {
        var field = {};
        field['id_configuration'] = 0;
        field['name'] = $(this).attr('data-name');
        field['field'] = $(this).attr('data-value');
        
        if ($(this).attr('data-gmf-id') && $(this).attr('data-gmf-id') != 'undefined') {
            field['gmf_id'] = $(this).attr('data-gmf-id');
        } else {
            field['gmf_id'] = 0;
        }
    
        if ($(this).attr('data-gmf-doc-link') && $(this).attr('data-gmf-doc-link') != 'undefined') {
            field['gmf_doc_link'] = $(this).attr('data-gmf-doc-link');
        } else {
            field['gmf_doc_link'] = 0;
        }
        
        field['tab'] = $(this).attr('data-tab');
        field['value'] = $(this).attr('data-default-value');

        if (!$(this).attr('data-formula-type')) {
            field['conditions'] = JSON.stringify([]);
        } else {
            field['conditions'] = {
                'condition_field': $(this).attr('data-condition-field'),
                'condition': $(this).attr('data-condition'),
                'condition_value': $(this).attr('data-condition-value'),
                'formula_type': $(this).attr('data-formula-type'),
                'format_as_price': $(this).attr('data-format-as-price'),
                'formula': $(this).attr('data-default-value')
            }
        }

        fields[$(this).attr('data-value')] = field;
    });
    
    return fields;
}

function getExportFilters() {

    var filters = {};
    $('.selected_filter_list .filter_section').each(function (n) {
        var field = {};
        filter = $(this).attr('data-field');
        type = $(this).attr('data-type');
        label = $(this).attr('data-label');

        if (type == 'checkbox' || type == 'tree') {
            var ids = {};
            $('.section_' + filter + ' .selected_checkbox_list li').each(function (k, i) {
                ids[k] = $(this).attr('data-id');
            });
            field['value'] = ids;
        }

        if (type == 'date') {
            var date_type = $('.section_' + filter + ' .filter_option .select_type').val();
            var val_1 = "";
            var val_2 = "";
            if (date_type == 'before_date' || date_type == 'after_date') {
                val_1 = $('.section_' + filter + ' .filter_value_date_1 input').val();
            }
            else if (date_type == 'period') {
                val_1 = $('.section_' + filter + ' .filter_value_date_1 input').val();
                val_2 = $('.section_' + filter + ' .filter_value_date_2 input').val();
            }
            field['value'] = {
                type: date_type,
                val_1: val_1,
                val_2: val_2,
            };
        }

        if (type == 'select') {
            field['value'] = $('.section_' + filter + ' .filter_option .select_type').val();
        }

        if (type == 'number' || type == 'string') {
            field['value'] = {
                type: $('.section_' + filter + ' .filter_option .select_type').val(),
                value: $('.section_' + filter + ' .filter_values input').val(),
            };
        }

        field['field'] = filter;
        field['field_type'] = type;
        field['label'] = label;
        filters[n] = field;

    });

    return filters;
}

function getTaskStatus() {

    var status = {};

    $('.automatically_state_list li.active').each(function (k) {
        status[k] = $(this).attr('data-id');
    });

    return status;
}

function getTaskData(id_task) {
    var values = {};
    values['id_task'] = id_task;
    values['description'] = $('.task_description').val();
    values['id_configuration'] = $('.task_settings').val();
    values['one_shot'] = $('.export_one_line_with_two_column input[name=one_shot]:checked').val();
    values['export_not_exported'] = $('.export_one_line_with_two_column input[name=export_not_exported]:checked').val();
    values['email_message'] = $('.export_one_line_with_two_column input[name=email_message]:checked').val();
    values['attach_file'] = $('.export_one_line_with_two_column input[name=attach_file]:checked').val();
    values['frequency'] = $('.frequency_task').val();
    values['export_emails'] = $('.export_emails').val();
    values['status'] = getTaskStatus();
    return values;
}

function getProductExportFields(id) {
    var export_values = {};
    export_values['id'] = id;
    export_values['id_shop'] = $("[name='id_shop']").val();
    export_values['id_lang'] = $("select[name='id_lang']").val();
    export_values['name'] = $('.export_template_name').val();
    export_values['format_file'] = $('.format_file:checked').val();
    export_values['delimiter_csv'] = $('.delimiter_csv').val();
    export_values['encoding'] = $('.encoding').val();
    export_values['ftp_port'] = $('.ftp_port').val();
    export_values['separator_csv'] = $('.separator_csv').val();
    export_values['feed_target'] = $('.feed_target:checked').val();
    export_values['ftp_protocol'] = $('.ftp_protocol').val();
    export_values['ftp_key_path'] = $('.ftp_key_path').val();
    export_values['ftp_authentication_type'] = $('.ftp_authentication_type').val();
    export_values['ftp_server'] = $('.ftp_server').val();
    export_values['ftp_password'] = $('.ftp_password').val();
    export_values['ftp_username'] = $('.ftp_username').val();
    export_values['ftp_folder_path'] = $('.ftp_folder_path').val();
    export_values['ftp_passive_mode'] = $('.switch_content input[name=ftp_passive_mode]:checked').val();
    export_values['ftp_file_transfer_mode'] = $('.ftp_file_transfer_mode').val();
    export_values['file_name'] = $('.file_name').val();
    export_values['display_header'] = $('.switch_content input[name=display_header]:checked').val();
    export_values['strip_tags'] = $('.switch_content input[name=strip_tags]:checked').val();
    export_values['date_format'] = $('.date_format').val();
    export_values['separator_decimal_points'] = $('.separator_decimal_points').val();
    export_values['thousands_separator'] = $('.thousands_separator').val();
    export_values['image_type'] = $('.image_type').val();
    export_values['round_value'] = $('.round_value').val();
    export_values['price_decoration'] = $('.price_decoration').val();
    export_values['currency'] = $('.currency').val();
    export_values['sort_by'] = $('.sort_by').val();
    export_values['order_way'] = $('.radio_content input[name=order_way]:checked').val();
    export_values['separate'] = $('.switch_content input[name=separate]:checked').val();
    export_values['style_spreadsheet'] = $('.switch_content input[name=style_spreadsheet]:checked').val();
    export_values['google_categories'] = getGoogleCategories();
    export_values['products_per_iteration'] = $('.products_per_iteration').val();
    export_values['is_saved'] = 0;
    export_values['filters'] = getExportFilters();
    export_values['fields'] = getExportFields();
    
    return export_values;
}

function uploadConfiguration() {
    var xlsxData = new FormData();
    xlsxData.append('file', $('.upload_settings')[0].files[0]);
    xlsxData.append('ajax', true);
    xlsxData.append('token', $('input[name=token_product_export]').val());
    xlsxData.append('controller', 'AdminProductsExport');
    xlsxData.append('action', 'uploadConfiguration');

    $.ajax({
        url: 'index.php',
        type: 'post',
        data: xlsxData,
        dataType: 'json',
        processData: false,
        contentType: false,
        beforeSend: function () {

        },
        success: function (json) {
            if (json['error']) {
                return showError(json['error']);
            }
            
            if (json['success']) {
                location.href = location.href;
            }
        }
    });
}

function downloadConfiguration(id_configuration) {
    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'downloadConfiguration',
            id_configuration: id_configuration,
        },
        beforeSend: function () {
            showLoader();
        },
        success: function (json) {
            if (json['error']) {
                return showError(json['error']);
            }
            
            if (json['download']) {
                location.href = json['download'];
            }
        },
        complete: function() {
            hideLoader();
        }
    });
}

function formEditTask(id_task) {
    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'formEditTask',
            id_task: id_task,
        },
        beforeSend: function () {
            showLoader();
        },
        success: function (json) {
            if (json['error']) {
                return showError(json['error']);
            }
            
            if (json['form']) {
                $('.scheduled_tasks_tab_container').html(json['form']);
                setTimeout(function () {
                    MpmPECustomSelectField.init();
                    if ($('.frequency_task_content').length > 0) {
                        $('.frequency_task_content .frequency_task').trigger('keyup');
                    }
                }, 100);
                hideLoader();
            }
        },
        complete: function() {
            hideLoader();
        }
    });
}

function getTaskList() {
    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'getTaskList',
        },
        beforeSend: function () {
            showLoader();
        },
        success: function (json) {
            if (json['error']) {
                return showError(json['error']);
            }
            
            if (json['form']) {
                $('.scheduled_tasks_tab_container').html(json['form']);
                hideLoader();
            }
        },
        complete: function() {
            hideLoader();
        }
    });
}

function deleteTask(id_task) {
    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'deleteTask',
            id_task: id_task
        },
        beforeSend: function () {
            showLoader();
        },
        success: function (json) {
            if (json['error']) {
                return showError(json['error']);
            }
            
            if (json['success']) {
                $('.item_scheduled_task.item_scheduled_task_' + id_task).remove();
                $('body').append(json['success']);
                showSuccess();
            }
        },
        complete: function() {
            hideLoader();
        }
    });
}

function saveScheduledTask(id_task) {

    var values = getTaskData(id_task);

    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'saveScheduledTask',
            values: values
        },
        beforeSend: function () {
            showLoader();
        },
        success: function (json) {
            if (json['error']) {
                return showError(json['error']);
            }
            
            if (json['success']) {
                $('body').append(json['success']);
                showSuccess();

                if (json['form']) {
                    $('.scheduled_tasks_tab_container').html(json['form']);
                }
            }
            hideLoader();
        },
        complete: function() {
            hideLoader();
        }
    });
}

function getFormPreviewFile() {
    var export_values = getProductExportFields(0);

    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'getFormPreviewFile',
            values: export_values,
        },
        beforeSend: function () {
            showLoader();
        },
        success: function (json) {
            if (json['error']) {
                return showError(json['error']);
            }
            
            if (json['success']) {
                $('body').append(json['success']);
                showOverlayForModalWindow();
                scrollForm($('.form_preview_file'), 1);
            }
        },
        complete: function() {
            hideLoader();
        }
    });

}

function getMoreCondition(count_conditions) {

    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'getMoreCondition',
            count_conditions: count_conditions,
        },
        success: function (json) {
            if (json['error']) {
                return showError(json['error']);
            }
            
            if (json['condition']) {
                $('.extra_fields_row.extra_fields_row_'+count_conditions).after(json['condition']);
                $('.add_more_condition').attr('data-count', parseInt(count_conditions)+1);
                setTimeout(function () {
                    MpmPECustomSelectField.init();
                }, 100);

                $("select.extra_field_formula_type").each(function() {
                    toggleFormatAsPriceSwitch($(this));
                    toggleFindAndReplaceExample($(this));
                });
            }
        }
    });

}

function scrollForm(el, show) {

    var top = $(document).scrollTop();
    var height_window = $(window).outerHeight();
    var height_form = el.outerHeight();
    var margin = (height_window - height_form) / 2;
    var form_offset = el.offset().top;

    if (show && margin < 11) {
        margin = 11;
    }

    if (margin > 10) {
        var margin_top = margin + top;
        el.css('top', margin_top + 'px');
    }
    else {
        if (direction >= 0) {
            if (top < form_offset) {
                el.css({top: (top + 10)});
            }
        }
        if (direction < 0) {
            if (top > (height_form + 10 - height_window)) {
                el.css({top: (top - (height_form - height_window + 10))});
            }
        }
    }
}

function saveExportConfiguration(id) {

    var export_values = getProductExportFields(id);

    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'saveExportConfiguration',
            values: export_values,
        },
        beforeSend: function () {
            showLoader();
        },
        success: function (json) {
            if (json['error']) {
                return showError(json['error']);
            }

            if (json['success']) {
                $('body').append(json['success']);
                showSuccess();
            }
        },
        complete: function() {
            hideLoader();
        }
    });
}

var MpmPEExportStatus = {
    active: 1,
    finished: 2,
    stopped: 3,
    error: 4,
    saving: 5,
    no_data: 6,
    no_product: 7
};

function refreshExportData() {
    if (isDebugModeEnabled() || !isActiveExportBrowserTab()) {
        return false;
    }

    var tab_active = $('.export_tabs_list .export_tab_item.active').attr('data-tab');

    $.ajax({
        url: 'index.php',
        type: 'post',
        data: {
            save: true,
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'getActiveExportData',
            tab_active: tab_active,
            is_active_export_tab: isActiveExportBrowserTab()
        },
        dataType: 'json',
        success: function (json) {
            setTimeout(function () {
                refreshExportData();
            },3000);

            if (!json || json['no_active_export']) {
                $(MpmPEExportStatusModal.stop_btn).hide();
                unsetAsActiveExportBrowserTab();
                return false;
            }

            if (json['error']) {
                unsetAsActiveExportBrowserTab();
                showError(json['error']);
                return false;
            }

            if (json['change']) {
                MpmPEExportStatusModal.setExportProcessIdToStopBtn(json['id_export_process']);

                if (json['start']) {
                    PEExportProcessHistory.setExportStartDate(json['id_export_process'], json['start']);
                    MpmPESavedConfigurationList.setExportStartDate(json['id_configuration'], json['start']);
                }

                if (json['id_status'] && json['status']) {
                    MpmPEExportStatusModal.setStatus(json['status']);
                    PEExportProcessHistory.setStatus(json['id_export_process'], json['status']);
                    MpmPESavedConfigurationList.setStatus(json['id_configuration'], json['status']);

                    if (json['id_status'] == MpmPEExportStatus.active || json['id_status'] == MpmPEExportStatus.saving) {
                        MpmPESavedConfigurationList.toggleStatusBlock(true, json['id_configuration']);
                        PEExportProcessHistory.toggleStatusBlock(true, json['id_export_process']);
                    }
                }

                if (json['progress'] && json['num_of_products'] && json['id_status'] == MpmPEExportStatus.active || json['id_status'] == MpmPEExportStatus.saving) {
                    MpmPEExportStatusModal.updateProgress(json['progress'], json['num_of_products']);
                } else {
                    $(MpmPEExportStatusModal.stop_btn).hide();
                    MpmPEExportStatusModal.updateProgress(false, false);
                }

                if (json['id_status'] === MpmPEExportStatus.stopped) {
                    $(MpmPEExportStatusModal.stop_btn).hide();
                    MpmPEExportStatusModal.hide();
                    unsetAsActiveExportBrowserTab();
                }
            }

            if (json['success'] && json['is_automatic'] == 0) {
                MpmPEExportStatusModal.hide();

                $('body').append(json['success']);
                showSuccess();

                if (json['file_path'] && json['id_export_process']) {
                    PEExportProcessHistory.setExportedFileDownloadLink(json['id_export_process'], json['file_path']);
                    MpmPESavedConfigurationList.setExportedFileDownloadLink(json['id_configuration'], json['file_path']);
                }
    
                unsetAsActiveExportBrowserTab();
            }
        }
    });
}

function toggleFTPFieldsVisibility()
{
    var ftp_protocol = $(".ftp_protocol").val();

    if (ftp_protocol == 'sftp') {
        $(".no-sftp-field").hide();
        $("#ftp_authentication_type_input_group").show();
        if ($("select.ftp_authentication_type").val() == 'key') {
            $("#ftp_key_path_input_group").show();
        } else {
            $("#ftp_key_path_input_group").hide();
        }
    }
    else {
        $(".no-sftp-field").show();
        $("#ftp_authentication_type_input_group").hide();
        $("#ftp_key_path_input_group").hide();
    }

}

function stopProductExport(id_export_process) {
    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'stopProductExport',
            id_export_process: id_export_process,
        },
        beforeSend: function () {
            showLoader();
        },
        success: function(response) {
            if (response['error']) {
                return showError(response['error']);
            }

            if (response['success']) {
                PEExportProcessHistory.setStatus(response['success']['id_export_process'], response['success']['status_label']);
                MpmPESavedConfigurationList.setStatus(response['success']['id_configuration'], response['success']['status_label']);
                $(".progress_product_export").hide();
            }
        },
        complete: function () {
            if ($(".exportproducts_status_export").hasClass("active")) {
                setTimeout(function () {
                    hideLoader();
                }, 2000);
            } else {
                hideLoader();
            }
        }
    });
}

function startExportManually(id_configuration, id_task) {
    setAsActiveExportBrowserTab();
    
    var export_configuration = {};

    if (!id_configuration) {
        export_configuration = getProductExportFields(0);
    }

    setTimeout(function () {
        refreshExportData();
    },3000);

    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: 'json',
        data: {
            ajax: true,
            token: $('input[name=token_product_export]').val(),
            controller: 'AdminProductsExport',
            action: 'startExportManually',
            export_configuration: export_configuration,
            id_configuration: id_configuration,
            id_task: id_task
        },
        beforeSend: function () {
            if (!isDebugModeEnabled()) {
                MpmPEExportStatusModal.show();
                MpmPEExportStatusModal.updateProgress(false, false);

                showOverlayForModalWindow();
            }
        },
        success: function (json) {
            if (!json || isDebugModeEnabled()) {
                unsetAsActiveExportBrowserTab();
                return false;
            }

            if (json['error']) {
                unsetAsActiveExportBrowserTab();
                showError(json['error']);
            }
        }
    });
}

function setAsActiveExportBrowserTab()
{
    $(".exportproducts_form").data("is-active-export-tab", 1);
}

function unsetAsActiveExportBrowserTab()
{
    $(".exportproducts_form").data("is-active-export-tab", 0);
}

function isActiveExportBrowserTab()
{
    return $(".exportproducts_form").data("is-active-export-tab");
}

function setDebugModeStatus(is_debug_mode_enabled)
{
    $(".debug-mode-switch-container").data("is-enabled", is_debug_mode_enabled);
}

function isDebugModeEnabled()
{
    return $(".debug-mode-switch-container").data("is-enabled");
}