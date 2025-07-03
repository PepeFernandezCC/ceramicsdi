if (typeof prestashop !== typeof undefined) {
    var static_token = prestashop.static_token;
} else {
    var static_token = 'token';
}

var historic_table;

jQuery(document).ready(function () {
    /* DATATABLE HISTÓRICO DEL ENVÍO */
    historic_table = jQuery('#historic-table').DataTable({
        paging: false,
        info: false,
        searching: false,
        orderable: false,
        columns: [
            { data: 'codEnvio' },
            { data: 'codProducto' },
            { data: 'desTextoResumen', className: 'text-center' },
            { data: 'fecEvento', className: 'text-center' },
            { data: 'horEvento', className: 'text-center' }
        ],
        columnDefs: [
            {
                targets: 2,
                render: function (data, type, full, meta) {
                    // Correos
                    switch (data) {
                        // Correos
                        case 'Prerregistrado':
                            return '<div class="preregistrado">' + data + '</div>';
                        case 'Admitido':
                        case 'En tránsito':
                        case 'En reparto':
                        case 'Alta en la unidad de reparto':
                        case 'Clasificado':
                            return '<div class="en_curso">' + data + '</div>';
                        case 'Admisión anulada':
                            return '<div class="anulado">' + data + '</div>';
                        case 'A disposición del destinatario':
                        case 'Entregado':
                            return '<div class="entregado">' + data + '</div>';
                        case 'No informado':
                            return '<div class="no-informado">' + data + '</div>';
                        // CEX
                        case 'SIN RECEPCION':
                            return '<div class="preregistrado">' + data + '</div>';
                        case 'EN REPARTO':
                        case 'DELEGACION DESTINO':
                        case 'EN ARRASTRE':
                            return '<div class="en_curso">' + data + '</div>';
                        case 'ENTREGADO':
                            return '<div class="entregado">' + data + '</div>';
                        default:
                            return '<div class="intermedio">' + data + '</div>';
                    }
                }
            }
        ],
        order: [
            [3, 'desc'],
            [4, 'desc']
        ]
    });

    setDatatableHistory();
});

/* FUNCIONES AUXILIARES */
function setDatatableHistory() {
    var data_history = {
        ajax: true,
        action: 'getOrderStatus',
        id_order: jQuery('#id_order_hidden').val()
    };
    var rand = 'rand=' + new Date().getTime();
    var ajaxtrue = '&ajax=true';

    jQuery.ajax({
        url: AdminOrderURL + rand + ajaxtrue,
        type: 'POST',
        data: data_history,
        cache: false,
        processData: true,
        success: function (data) {
            parsed_data = JSON.parse(data);
            historic_table.clear().draw();
            historic_table.rows.add(parsed_data);
            historic_table.columns.adjust().draw();
            jQuery('.history-container').removeClass('hidden-block');
        }
    });
}

function getFormData($form_id) {
    var config = {};
    jQuery('#' + $form_id + ' input:hidden').each(function () {
        config[this.name] = this.value;
    });
    jQuery('#' + $form_id + ' input:text').each(function () {
        config[this.name] = this.value;
    });
    jQuery('#' + $form_id + ' input:checkbox').each(function () {
        if (jQuery(this).is(':checked')) {
            config[this.name] = 1;
        } else {
            config[this.name] = 0;
        }
    });
    jQuery('#' + $form_id + ' input:radio').each(function () {
        if (jQuery(this).is(':checked')) {
            config[this.name] = 1;
        } else {
            config[this.name] = 0;
        }
    });
    jQuery('#' + $form_id + ' select').each(function () {
        config[this.name] = this.value;
    });
    jQuery('#' + $form_id + ' textarea').each(function () {
        config[this.name] = this.value;
    });
    return config;
}

function disableForm(form_id) {
    jQuery('input', form_id).each(function (event) {
        this.disabled = true;
    });
    jQuery('select', form_id).each(function (event) {
        this.disabled = true;
    });
    jQuery('button', form_id).each(function (event) {
        if (this.id == 'copyOfficeContent' || this.id == 'copyCityPaqContent') {
            this.disabled = false;
        } else {
            this.disabled = true;
        }
    });
    jQuery('textarea', form_id).each(function (event) {
        this.disabled = true;
    });
}

function enableForm(form_id) {
    jQuery('input', form_id).each(function (event) {
        this.disabled = false;
    });
    jQuery('select', form_id).each(function (event) {
        this.disabled = false;
    });
    jQuery('button', form_id).each(function (event) {
        this.disabled = false;
    });
    jQuery('textarea', form_id).each(function (event) {
        this.disabled = false;
    });
}

function setCorreosRangeDate(inputField) {
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);

    var month = tomorrow.getMonth() + 1;
    var day = tomorrow.getDate();
    var year = tomorrow.getFullYear();

    if (day < 10) day = '0' + day;
    if (month < 10) month = '0' + month;

    var today_val = year + '-' + month + '-' + day;
    document.getElementById(inputField).setAttribute('min', today_val);
    document.getElementById(inputField).value = year + '-' + month + '-' + day;

    month++;
    if (month > 12) {
        month = 1;
        year++;
    }
    if (month < 10) month = '0' + month;

    var max_val = year + '-' + month + '-' + day;
    document.getElementById(inputField).setAttribute('max', max_val);
}

function setCEXRangeDate(inputField) {
    const today = new Date();

    var month = today.getMonth() + 1;
    var day = today.getDate();
    var year = today.getFullYear();

    if (day < 10) day = '0' + day;
    if (month < 10) month = '0' + month;

    var today_val = year + '-' + month + '-' + day;
    document.getElementById(inputField).setAttribute('min', today_val);
    document.getElementById(inputField).value = year + '-' + month + '-' + day;

    month++;
    if (month > 12) {
        month = 1;
        year++;
    }
    if (month < 10) month = '0' + month;

    var max_val = year + '-' + month + '-' + day;
    document.getElementById(inputField).setAttribute('max', max_val);
}

function managePrintLabel(bultos) {
    if (bultos > 5) {
        jQuery('#print_label').attr('checked', false);
        jQuery('#print_label').attr('disabled', true);
        jQuery('.alert-more-5-labels').removeClass('hidden-block');
    } else {
        jQuery('#print_label').attr('disabled', false);
        jQuery('.alert-more-5-labels').addClass('hidden-block');
    }
}

function manageDeliverySaturday(company) {
    if (company == 'CEX') {
        jQuery('#delivery_saturday_container').removeClass('hidden-block');
    } else {
        jQuery('#delivery_saturday_container').addClass('hidden-block');
    }
}

function manageReturnCustomDocPackage(company) {
    var require_customs_doc = jQuery('#require_customs_doc_hidden').val();
    if (require_customs_doc) {
        if (company == 'Correos') {
            jQuery('.customs-correos-container-return').removeClass('hidden-block');
            jQuery('.correos-num-parcels-return-container').addClass('hidden-block');
            jQuery('#general-return-pickup-container').removeClass('hidden-block');
            jQuery('#pickupReturnButton').removeClass('hidden-block');
            jQuery('#save-return-pickup-container').addClass('hidden-block');
        } else {
            jQuery('.customs-correos-container-return').addClass('hidden-block');
            jQuery('.correos-num-parcels-return-container').removeClass('hidden-block');
            jQuery('#general-return-pickup-container').removeClass('hidden-block');
            jQuery('#pickupReturnButton').addClass('hidden-block');
            jQuery('#save-return-pickup-container').removeClass('hidden-block');
            jQuery('#correos-options-pickup-return-container').addClass('hidden-block');
            jQuery('#generate_return_pickup').addClass('hidden-block');
        }
    } else {
        jQuery('.customs-correos-container-return').addClass('hidden-block');
    }
}

function manageCodeAT() {
    const selectedCarrier = jQuery('#input_select_carrier').find('option:selected');
    const company = selectedCarrier.data('company');
    const customerCountry = jQuery('#customer_country').val();
    const senderCountry = jQuery('#sender_country').val();

    const codeAtContainer = jQuery('#code_at_container');
    const requireCustomsDoc = jQuery('#require_customs_doc');

    if (company === 'CEX') {
        requireCustomsDoc.addClass('hidden-block');
        if (customerCountry === 'PT' && senderCountry === 'PT') {
            codeAtContainer.removeClass('hidden-block');
        } else {
            codeAtContainer.addClass('hidden-block');
        }
    } else if (company === 'Correos') {
        codeAtContainer.addClass('hidden-block');
        if (customerCountry !== senderCountry) {
            requireCustomsDoc.removeClass('hidden-block');
        } else {
            requireCustomsDoc.addClass('hidden-block');
        }
    }
}

function cleanStatusDatatable() {
    historic_table.clear().draw();
    jQuery('.history-container').removeClass('hidden-block');
}

function generateReturnPickup() {
    jQuery('#processingReturnPickupButtonMsg').removeClass('hidden-block');
    jQuery('#returnPickupButtonMsg').addClass('hidden-block');
    jQuery('#generate_return_pickup').attr('disabled', true);

    var selected_carrier_return = jQuery('#input_select_carrier_return').find('option:selected');
    var company = selected_carrier_return.data('company');
    var id_carrier = 0;

    if (jQuery('#return_print_label').is(':checked')) {
        print_label = 1;
    } else {
        print_label = 0;
    }

    var data = {
        ajax: true,
        action: 'generatePickup',
        mode_pickup: 'return',
        id_order: jQuery('#id_order_hidden').val(),
        bultos: jQuery('#correos-num-parcels-return').val(),
        expedition_number: jQuery('#return_exp_number_hidden').val(),
        order_reference: jQuery('#order_reference').val(),
        pickup_date: jQuery('#return_pickup_date').val(),
        sender_from_time: jQuery('#return_sender_from_time').val(),
        sender_to_time: jQuery('#return_sender_to_time').val(),
        sender_address: jQuery('#customer_address').val(),
        sender_city: jQuery('#customer_city').val(),
        sender_cp: jQuery('#customer_cp').val(),
        sender_name: jQuery('#customer_firstname').val() + ' ' + jQuery('#customer_lastname').val(),
        sender_contact: jQuery('#customer_firstname').val() + ' ' + jQuery('#customer_lastname').val(),
        sender_phone: jQuery('#customer_phone').val(),
        sender_email: jQuery('#customer_email').val(),
        sender_nif_cif: jQuery('#customer_dni').val(),
        sender_country: jQuery('#customer_country').val(),
        id_sender: jQuery('#senderSelect').val(),
        producto: selected_carrier_return.val(),
        package_type: jQuery('#return_package_type').val(),
        print_label: print_label,
        company: company,
        id_carrier: id_carrier,
        default_sender_email: jQuery('#default_sender_email').val(),
        customer_cp: jQuery('#sender_cp').val(),
        customer_country: jQuery('#sender_country').val()
    };

    var rand = 'rand=' + new Date().getTime();
    var ajaxtrue = '&ajax=true';

    jQuery.ajax({
        url: AdminOrderURL + rand + ajaxtrue,
        type: 'POST',
        data: data,
        cache: false,
        processData: true,
        success: function (data) {
            parsed_data = JSON.parse(data);
            if (parsed_data.codigoRetorno == '0') {
                jQuery('#pickup_return_code_hidden').val(parsed_data.codSolicitud);
               location.reload();
                return;
            } else {
                jQuery('#error_register_return strong').html(parsed_data.mensajeRetorno);
                jQuery('#error_register_return').removeClass('hidden-block');
                jQuery('#success_register_return').addClass('hidden-block');
            }
            jQuery('#processingReturnPickupButtonMsg').addClass('hidden-block');
            jQuery('#returnPickupButtonMsg').removeClass('hidden-block');
            jQuery('#generate_return_pickup').attr('disabled', false);
        }
    });
}

function generateReturn(event) {
    jQuery('#processingReturnButtonMsg').removeClass('hidden-block');
    jQuery('#generateReturnButtonMsg').addClass('hidden-block');

    var id_order = jQuery('#id_order_hidden').val();
    var order_form = getFormData('order_form');
    var selected_carrier = jQuery('#input_select_carrier_return').find('option:selected');
    var company = selected_carrier.data('company');
    var expedition_number = '';
    var id_sender = jQuery('#senderSelect').val();

    let needPickup = (company == 'CEX') ? 'S' : 'N';
    
    var data = {
        ajax: true,
        token: static_token,
        action: 'generateReturn',
        id_order: id_order,
        company: company,
        order_form: order_form,
        id_sender: id_sender,
        needPickup: needPickup,
        pickup_date: jQuery('#return_pickup_date').val(),
        order_reference: jQuery('#order_reference').val(),
        sender_from_time: jQuery('#return_sender_from_time').val(),
        sender_to_time: jQuery('#return_sender_to_time').val(),
    };

    var rand = 'rand=' + new Date().getTime();
    var ajaxtrue = '&ajax=true';
    jQuery.ajax({
        url: AdminOrderURL + rand + ajaxtrue,
        type: 'POST',
        data: data,
        cache: false,
        processData: true,
        success: function (data) {
            parsed_data = JSON.parse(data);
            mensajeRetorno = '';
            if (parsed_data['errores'].length != 0) {
                var mensajeRetorno = '';
                parsed_data['errores'].forEach(function (item) {
                    if (item.codigoRetorno == null) {
                        mensajeRetorno = 'ERROR 18002: ' + item.mensajeRetorno + '<br>';
                    } else {
                        mensajeRetorno =
                            mensajeRetorno + 'Bulto ' + item.num_bulto + ': ' + item.mensajeRetorno + '<br>';
                    }
                });
                jQuery('#error_register_return strong').html(mensajeRetorno);
                jQuery('#error_register_return').removeClass('hidden-block');
            } else {
                jQuery('#error_register_return').addClass('hidden-block');
                jQuery('#generate-return-container').addClass('hidden-block');
                jQuery('#general-return-pickup-container').removeClass('hidden-block');
                jQuery('#cancel-return-container').removeClass('hidden-block');
                jQuery('.container-bultos-return').addClass('hidden-block');
                jQuery('#return-status').text('Prerregistrado');
                jQuery('#generateReturnButton').addClass('hidden-block');
                jQuery('#cancelReturnButton').removeClass('hidden-block');
                jQuery('#save-return-pickup-container').removeClass('hidden-block');

            }

            if (parsed_data['aciertos'].length != 0) {
                var return_codes = '';
                parsed_data['aciertos'].forEach(function (item) {
                    return_codes =
                        return_codes +
                        '<span class="return-done-info-text">' +
                        'Bulto ' +
                        item.num_bulto +
                        ': ' +
                        item.shipping_number +
                        '<span><br>';
                    expedition_number = item.exp_number;
                });
                jQuery('.shipping-numbers-container-return').html(return_codes);
                jQuery('#return-done-info').removeClass('hidden-block');
                jQuery('#success_register_return').addClass('hidden-block');
                jQuery('#return_exp_number_hidden').val(expedition_number);
                jQuery('#pickup_return_code_hidden').val(parsed_data.codSolicitud);
                location.reload();
            } else {
                jQuery('#success_register_return').addClass('hidden-block');
                jQuery('#generate-return-container').removeClass('hidden-block');
                jQuery('#cancel-return-container').addClass('hidden-block');
                jQuery('.container-bultos-return').removeClass('hidden-block');
                jQuery('#error_register_return').removeClass('hidden-block');
                jQuery('#cancelReturnButton').addClass('hidden-block');
                jQuery('#error_register_return strong').html(parsed_data.mensajeRetorno);
            }

            jQuery('#processingReturnButtonMsg').addClass('hidden-block');
            jQuery('#generateReturnButtonMsg').removeClass('hidden-block');
        },
        error: function (e) {
            parsed_data = JSON.parse(data);
            jQuery('#error_register_return strong').html(parsed_data.mensajeRetorno);
        }
    });
}

function setCustomsCodeActive(n, type) {
    jQuery('#customs_correos_container' + type + ' #customs_desc_' + n).removeClass('active');
}

function showCustomsDesc(n, type) {
    jQuery('#customs_correos_container' + type + ' #customs_desc_tab_' + n).removeClass('hidden-block');
    jQuery('#customs_correos_container' + type + ' #customs_code_tab_' + n).addClass('hidden-block');
}

function setCustomsDescActive(n, type) {
    jQuery('#customs_correos_container' + type + ' #customs_code_' + n).removeClass('active');
}

function getActiveTab(n, type) {
    let tab = 'desc_tab';
    let classList = jQuery('#customs_correos_container' + type + ' #customs_code_' + n)
        .attr('class')
        .split(/\s+/);
    jQuery.each(classList, function (index, item) {
        if (item === 'active') {
            addingDesc = false;
            addingTarriffCode = true;
            tab = 'code_tab';
            return false;
        }
    });
    return tab;
}
