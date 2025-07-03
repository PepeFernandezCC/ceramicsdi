jQuery(document).ready(function ($) {
    
    var data_customer_code_request;
    var customerTable;
    var customerToBeRemoved;
    var row;

    // Ocultamos contenedores de conectado
    $('.connected').hide();

    validateCorreosUser();
    
    /* Validaciones Correos */
    $('#CorreosCustomerDataForm').validate({
        rules: {
            CorreosContract: {
                required: true,
                minlength: 8,
                maxlength: 8
            },
            CorreosCustomer: {
                required: true,
                minlength: 8,
                maxlength: 8
            },
            CorreosKey: {
                required: true,
                minlength: 4,
                maxlength: 4
            },
            CorreosUser: {
                required: true,
                accountTypeMethod: true
            },
            CorreosPassword: {
                required: true,
                minlength: 8,
                maxlength: 8
            },
            CorreosOv2Code: {
                required: true,
                email: true,
                minlength: 3,
                maxlength: 150
            }
        },
        messages: {
            CorreosContract: {
                required: requiredCustomMessage,
                minlength: contractNumberMsj,
                maxlength: contractNumberMsj
            },
            CorreosCustomer: {
                required: requiredCustomMessage,
                minlength: customerNumberMsj,
                maxlength: customerNumberMsj
            },
            CorreosKey: {
                required: requiredCustomMessage,
                minlength: labelingCodeMsj,
                maxlength: labelingCodeMsj
            },
            CorreosUser: {
                required: requiredCustomMessage,
                accountTypeMethod: systemsAccountMsj
            },
            CorreosPassword: {
                required: requiredCustomMessage,
                minlength: systemsPasswordMsj,
                maxlength: systemsPasswordMsj
            },
            CorreosOv2Code: {
                required: requiredCustomMessage,
                email: invalidEmailMsj
            }
        },
        submitHandler: function () {
            var formElement = document.getElementById('CorreosCustomerDataForm');

            /** Procesamos el formulario de Alta/Modificación Cliente de Correos */
            $.ajax({
                url: AdminCorreosOficialCustomerDataProcess,
                type: 'POST',
                data: new FormData(formElement),
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    var obj = JSON.parse(data);
                    if (obj.error == 'ERROR 100501') {
                        showModalErrorWindow(obj.desc);
                    } else if(obj.status_code == '409') {
                        showModalErrorWindow('ERROR ' + obj.codigoRetorno + ': ' + obj.mensajeRetorno);
                    } else {
                        $('#idCorreos').val(data);
                        $('#CustomerDataDataTable').DataTable().ajax.reload();

                        jQuery('#SendersDataTable').DataTable().ajax.reload();
                        reloadSenderContractsSelects();

                        if ($('#CorreosContract').prop('disabled') == false) {
   
                            signUpCorreosCustomer(false, data).then(isConnected => {
                                if (isConnected) {
                                    disableCorreosForm();
                                    jQuery('#cocexUserLoggin').addClass('hidden-block');
                                }else{
                                    jQuery('#CorreosCustomerDataSaveButton').val(editButton.toUpperCase());
                                    customerStatus('Correos', 'off');
                                    jQuery('#cocexUserLoggin').addClass('hidden-block');
                                }
                            }).catch(error => {
                                showModalErrorWindow(error);
                            });
                        } else {
                            enableCorreosForm();
                        }

                    }
                },
                error: function (e) {
                    showModalErrorWindow('ERROR 10502: ' + customer_technical_error);
                    jQuery('#cocexUserLoggin').addClass('hidden-block');
                }
            });
        }
    }); // Fin Validaciones Correos

    /* Validaciones Cex */
    $('#CEXCustomerDataForm').validate({
        rules: {
            CEXCustomer: {
                required: true,
                minlength: 9,
                maxlength: 9
            },
            CEXUser: {
                required: true,
                minlength: 3,
                maxlength: 20
            },
            CEXPassword: {
                required: true,
                minlength: 3,
                maxlength: 20
            }
        },
        messages: {
            CEXCustomer: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 9 ' + characters,
                maxlength: maxLengthMessage + ' 9 ' + characters
            },
            CEXUser: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 3 ' + characters,
                maxlength: maxLengthMessage + ' 20 ' + characters
            },
            CEXPassword: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 3 ' + characters,
                maxlength: maxLengthMessage + ' 20 ' + characters
            }
        },

        submitHandler: function () {
            var formElement = document.getElementById('CEXCustomerDataForm');
            
            /** Procesamos el formulario de Alta/Modificación Cliente de Correos*/
            $.ajax({
                url: AdminCorreosOficialCustomerDataProcess,
                type: 'POST',
                data: new FormData(formElement),
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    var obj = JSON.parse(data);

                    if (obj.error == 'ERROR 100501') {
                        showModalErrorWindow(obj.desc);
                    } else if (obj.status_code == '409') {
                        showModalErrorWindow('ERROR ' + obj.codigoRetorno + ': ' + obj.mensajeRetorno);
                    } else {
                        $('#idCEX').val(data);
                        $('#CustomerDataDataTable').DataTable().ajax.reload();

                        jQuery('#SendersDataTable').DataTable().ajax.reload();
                        reloadSenderContractsSelects();

                        if (jQuery('#CEXCustomer').prop('disabled') == false) {
                            
                            signUpCexCustomer(false, data).then(isConnected => {
                                if (isConnected) {
                                    disableCEXForm();
                                    jQuery('#cocexUserLoggin').addClass('hidden-block');
                                }else{
                                    jQuery('#CEXCustomerDataSaveButton').val(editButton.toUpperCase());                                   
                                    jQuery('#cocexUserLoggin').addClass('hidden-block');
                                    
                                }
                            }).catch(error => {
                                showModalErrorWindow(error);
                            });

                        } else {
                            enableCEXForm(data ? editButton.toUpperCase() : false);
                        }

                    }
                },
                error: function (e) {
                    showModalErrorWindow('ERROR 10503: ' + customer_technical_error);
                    jQuery('#cocexUserLoggin').addClass('hidden-block');
                }
            });
        }
    }); // Fin Validaciones CEX

    function signUpCorreosCustomer(pageReady, id_code = null) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'post',
                url: AdminCorreosSoapRequestURL,
                data: {
                    action: 'alta_cliente_Correos',
                    codes_id: id_code,
                },
                success: function (response) {
                    var obj = JSON.parse(response);

                    if (obj.message.type == 'success') {
                        if (pageReady == false) {
                            showResponseMessage(obj.message.error_code);
                        }
    
                        resolve(true);
                    } else {
                        showResponseMessage(obj.message.error_code);
    
                        resolve(false);
                    }
                },
                error: function () {
                    // Manejar errores de la llamada AJAX
                    reject(soapFeatureInstallErrorMessage);
                }
            });
        });
    }

    function signUpCexCustomer(pageReady, id_code = null) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'post',
                url: AdminCEXRestRequestURL,
                data: {
                    action: 'alta_cliente_CEX',
                    codes_id: id_code,
                },
                success: function (response) {
                    var obj = JSON.parse(response);

                    if (obj.message.type == 'success') {
                        if (pageReady == false) {
                            showResponseMessage(obj.message.error_code);
                        }
    
                        resolve(true);
                    } else {
                        showResponseMessage(obj.message.error_code);
    
                        resolve(false);
                    }
                },
                error: function () {
                    // Manejar errores de la llamada AJAX
                    reject(soapFeatureInstallErrorMessage);
                }
            });
        });
    }

    /** Muestra estado del cliente del conectado/no conectado */
    function customerStatus(customer, status) {
        if (status == 'on') {
            $('#' + customer + ' .connected').show();
            $('#' + customer + ' .connected').css('display', 'inline');
            $('#' + customer + ' .noconnected').hide();
        } else {
            $('#' + customer + ' .connected').hide();
            $('#' + customer + ' .noconnected').show();
        }
    }


    /* *******************************************************************************************************
        *                                 DATA TABLE
        **********************************************************************************************************/

    // Añadir
    $('#add-new-contract').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        logginForm = jQuery('#cocexUserLoggin');

        if(logginForm.hasClass('hidden-block')) {
            logginForm.removeClass('hidden-block');
        } else if (!logginForm.hasClass('hidden-block')) {
            logginForm.addClass('hidden-block');
        }

        const position = jQuery('#customer_data').offset().top;
        animateScroll(position, 500);

        disableCorreosForm(false);
        disableCEXForm(false);
    });

    // Modificar
    $('#CustomerDataDataTable').on('click', 'td.editor-edit', function (e) {
        e.preventDefault();

        const position = jQuery('#customer_data').offset().top;
        animateScroll(position, 500);

        // obtenemos la fila del datatable
        customerTable = $('#CustomerDataDataTable').DataTable();
        row = customerTable.row($(this).parent('tr'));
        var id = customerTable.row(row).data().id;
        jQuery('#cocexUserLoggin').removeClass('hidden-block');

        // Obtenemos datos a editar
        new Promise(function(resolve, reject) {
            jQuery.ajax({
                type: 'post',
                url: getCustomerCode,
                data: {
                    action: 'getCustomerCode',
                    id: id
                },
                success: function (data) {
                    resolve(data);
                },
                error: function (error) {
                    reject(error);
                }
            });
        }).then(function(data) {
            var obj = JSON.parse(data);

            if (obj.company == 'Correos') {
                /** @todo Logeo no deberia ser necesario al pulsar este boton, */
                /*signUpCorreosCustomer(false, obj.id).then(isConnected => {
                    if (isConnected) {
                        customerStatus('Correos', 'on');
                    }else{
                        customerStatus('Correos', 'off');
                    }
                })*/

                enableCorreosForm();
                disableCEXForm();
                $('#CorreosCompany').val(obj.company);
                $('#idCorreos').val(obj.id);
                $('#CorreosContract').val(obj.CorreosContract);
                $('#CorreosCustomer').val(obj.CorreosCustomer);
                $('#CorreosKey').val(obj.CorreosKey);
                $('#CorreosOv2Code').val(obj.CorreosOv2Code);
            } else if (obj.company == 'CEX') {
                /** @todo Logeo no deberia ser necesario al pulsar este boton, */
                /*signUpCexCustomer(false, obj.id).then(isConnected => {
                    if (isConnected) {
                        customerStatus('CEX', 'on');
                    }else{
                        customerStatus('CEX', 'off');
                    }
                })*/

                enableCEXForm();
                disableCorreosForm();
                $('#CEXCompany').val(obj.company);
                $('#idCEX').val(obj.id);
                $('#CEXCustomer').val(obj.CEXCustomer);
                $('#CEXUser').val(obj.CEXUser);
            } else {
                alert('ERROR CORREOS OFICIAL 10014: No se ha seleccionado ningún cliente');
            }
            
        }).catch(function(error) {
            console.error(error);
        });

    });

    // Eliminar
    $('#CustomerDataDataTable').on('click', 'td.editor-delete', function (e) {
        e.preventDefault();
        e.stopPropagation();

        let deleteAllowed = true;

        customerTable = $('#CustomerDataDataTable').DataTable();
        row = customerTable.row($(this).parent('tr'));

        var id = customerTable.row(row).data().id;
        customerToBeRemoved = $(this).prev().prev().html();

        // Comprobamos remitentes asociados
        let sendersTableData = $('#SendersDataTable').DataTable().ajax.json();
        sendersTableData.forEach(function(sender) {
            if(sender.correos_code == id || sender.cex_code == id){
                deleteAllowed = false;
                return;
            }
        });

        // Limpiamos formularios si hemos borrado.
        if (customerToBeRemoved == 'Correos') {
            disableCorreosForm();
        } else if (customerToBeRemoved == 'CEX') {
            disableCEXForm();
        }

        data_customer_code_request = {
            action: 'DeleteCustomerCode',
            CorreosOficialCustomerCode: id
        };

        $('#myModal').data('id', id).modal('show');

        if(deleteAllowed){
            $('#myModalTitle').html(confirmationTitle);
            $('#myModalDescription p').html(wantDeleteCustomer);
            $('#myModalActionButtonCustomerData').html(deleteButton);
            $('#myModal').find('.myModalActionButton').hide();
            $('#myModalCancelButton').show();
            $('#myModalActionButtonCustomerData').show();
        }else{
            $('#myModalTitle').html(errorTitle);
            $('#myModalDescription p').html(customerHaveSender);
            $('#myModal').find('.myModalActionButton').hide();
            $('#myModalCancelButton').show();
        }

        // Cancelar
        /* En back.js */
    });

    // Aceptar
    $('body').on('click', '#myModalActionButtonCustomerData', function (ev) {
        ev.preventDefault();
        ev.stopPropagation();
        var id = $('#myModal').data('id');
        customerTable.row(row).remove().draw();
        $('#myModal').modal('hide');

        jQuery.post(AdminCorreosOficialCustomerDataProcess, data_customer_code_request, function (response) {
            if (customerToBeRemoved == 'CEX') {
                enableCEXForm();
                disableProducts('CEX');
                $('#CEXCustomerDataSaveButton').val(addButton);
                customerStatus('CEX', 'off');
                $('#CEXCustomerDataForm').find('input[type=text], textarea').val('');
                reloadSenderContractsSelects();
            } else if (customerToBeRemoved == 'Correos') {
                enableCorreosForm();
                disableProducts('Correos');
                $('#CorreosCustomerDataSaveButton').val(addButton);
                customerStatus('Correos', 'off');
                $('#CorreosCustomerDataForm').find('input[type=number], textarea').val('');
                $('#CorreosCustomerDataForm').find('input[type=text], textarea').val('');
                $('#CorreosCustomerDataForm').find('input[type=email], textarea').val('');
                reloadSenderContractsSelects();
            } else {
                alert('ERROR CORREOS OFICIAL 10015: No se ha seleccionado ningún cliente');
            }

        });
    });

    // Datatable Clientes
    $('#CustomerDataDataTable').DataTable({
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        ajax: {
            url: getDataTableCustomerList,
            dataSrc: ''
        },
        language: {
            'url:': co_path_to_module+'/views/js/datatables/Spanish.json',
            emptyTable: noCustomersActive
        },
        columns: [
            { data: 'id' },
            {
                data: null,
                "render": function ( data, type, row ) {

                    if(row.status == true){
                        return '<div class="connected-status"><span>'+statusConnected+'</span></div>';
                    }else{
                        return '<div class="noconnected-status"><span>'+statusNotConnected+'</span></div>';
                    }
                    
                },
                orderable: false
            },
            { data: 'customer_code' },
            { data: 'company' },
            {
                data: null,
                className: 'dt-center editor-edit',
                defaultContent: '<a class="btn btn-primary"><i class="far fa-edit edit"></i></a>',
                orderable: false
            },
            {
                data: null,
                className: 'dt-center editor-delete',
                defaultContent: '<a class="btn btn-danger"><i class="far fa-trash-alt remove"></i></a>',
                orderable: false
            }
        ],
        columnDefs: [
            {
                targets: [0],
                visible: false
            }
        ]
    });

    // Acciones al cargar datos en la tabla de contratos
    $('#CustomerDataDataTable').on('draw.dt', function () {

        let customerTableData = $('#CustomerDataDataTable').DataTable().ajax.json();

        // Comprobamos productos de correos
        let findCorreos = customerTableData.find(function(code) {
            return code.company === "Correos";
        });

        if (findCorreos) {
            activeProducts("Correos");
        } else {
            disableProducts("Correos");
        }

        // Comprobamos productos de correos
        let findCEX = customerTableData.find(function(code) {
            return code.company === "CEX";
        });

        if (findCEX) {
            activeProducts("CEX");
        } else {
            disableProducts("CEX");
        }

        // Show hiden Aviso
        if(!findCorreos && !findCEX){
            $("#products_container_general").addClass('hidden-block');
            $("#advice_no_products").removeClass('hidden-block');
        }else{
            $("#advice_no_products").addClass('hidden-block');
            $("#products_container_general").removeClass('hidden-block');
        }

    });

    jQuery('#CorreosCustomerDataCancelButton').on('click', function() {
        jQuery('#cocexUserLoggin').addClass('hidden-block');
    });

    jQuery('#CEXCustomerDataCancelButton').on('click', function() {
        document.getElementById('CEXCustomerDataForm').reset();
        jQuery('#cocexUserLoggin').addClass('hidden-block');
    });    

    function activeProducts($company) {
        if ($company == 'Correos'){
            $("#products_container_correos").removeClass('hidden-block');
        } else if ($company == 'CEX'){
            $("#products_container_cex").removeClass('hidden-block');
        }
    }

    function disableProducts($company) {
        if ($company == 'Correos'){
            $("#products_container_correos").addClass('hidden-block');
        } else if ($company == 'CEX'){
            $("#products_container_cex").addClass('hidden-block');
        }
    }

    function disableCorreosForm(disabled = true) {

        // Limpiar validaciones	
        $('#CorreosCustomerDataForm').validate().resetForm();

        $('#idCorreos').val('');
        $('#CorreosContract').prop('disabled', disabled).val('');
        $('#CorreosCustomer').prop('disabled', disabled).val('');
        $('#CorreosKey').prop('disabled', disabled).val('');
        $('#CorreosUser').prop('disabled', disabled).val('');
        $('#CorreosPassword').prop('disabled', disabled).val('');
        $('#CorreosUser').prop('disabled', disabled).val('');
        $('#CorreosPassword').prop('disabled', disabled).val('');
        $('#CorreosOv2Code').prop('disabled', disabled).val('');
        if(disabled){
            $('#CorreosCustomerDataSaveButton').attr('disabled');
            $('#CorreosCustomerDataSaveButton').addClass('disabled');
        }else{
            $('#CorreosCustomerDataSaveButton').removeClass('disabled');
        }
        $('#CorreosCustomerDataSaveButton').val(addButton);
        customerStatus('Correos', 'off');
    }

    function enableCorreosForm() {

        // Limpiar validaciones	
        $('#CorreosCustomerDataForm').validate().resetForm();

        $('#CorreosContract').prop('disabled', false);
        $('#CorreosCustomer').prop('disabled', false);
        $('#CorreosKey').prop('disabled', false);
        $('#CorreosUser').prop('disabled', false);
        $('#CorreosPassword').prop('disabled', false);
        $('#CorreosUser').val('');
        $('#CorreosPassword').val('');
        $('#CorreosOv2Code').prop('disabled', false);
        $('#CorreosCustomerDataSaveButton').removeClass('disabled');
        $('#CorreosCustomerDataSaveButton').val(editButton);
    }

    function disableCEXForm(disabled = true) {

        // Limpiar validaciones	
        $('#CEXCustomerDataForm').validate().resetForm();

        $('#idCEX').val('');
        $('#CEXCustomer').prop('disabled', disabled).val('');
        $('#CEXUser').prop('disabled', disabled).val('');
        $('#CEXPassword').prop('disabled', disabled).val('');
        $('#CEXUser').prop('disabled', disabled).val('');
        $('#CEXPassword').prop('disabled', disabled).val('');
        if(disabled){
            $('#CEXCustomerDataSaveButton').attr('disabled');
            $('#CEXCustomerDataSaveButton').addClass('disabled');
        }else{
            $('#CEXCustomerDataSaveButton').removeClass('disabled');
        }
        $('#CEXCustomerDataSaveButton').val(addButton);
        customerStatus('CEX', 'off');
    }

    function enableCEXForm() {

        // Limpiar validaciones	
        $('#CEXCustomerDataForm').validate().resetForm();

        $('#CEXCustomer').prop('disabled', false);
        $('#CEXUser').prop('disabled', false);
        $('#CEXPassword').prop('disabled', false);
        $('#CEXUser').val('');
        $('#CEXPassword').val('');
        $('#CEXCustomerDataSaveButton').removeClass('disabled');
        $('#CEXCustomerDataSaveButton').val(editButton);
    }

    function animateScroll(position, timeSeq) {
        jQuery('html, body').animate({
            scrollTop: position,
        }, timeSeq);
    }

});

function reloadSenderContractsSelects() {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'post',
            url: getCustomerCodes,
            data: {
                action: 'getCustomerCodes'
            },
            success: function (data) {
                resolve(data);
            },
            error: function (error) {
                reject(error);
            }
        });
    }).then(function(data) {
        let res = JSON.parse(data);

        // Eliminamos las optiones para actualizar las nuevas
        let selectCorreosCode = jQuery('#correos_code');
        selectCorreosCode.find('option[value!=""]').remove();

        res.correos.forEach(function(element) {
            selectCorreosCode.append('<option value="' + element.id + '">' + element.CorreosContract + '/' + element.CorreosCustomer + '</option>');
        });

        // si tenemos resultaos seleciconado el id más pequeño
        if (res.correos.length > 0) {
            selectCorreosCode.val(res.correos[0].id);
        }

        let selectCEXCode = jQuery('#cex_code');
        selectCEXCode.find('option[value!=""]').remove();

        // Añadimos las opciones
        res.cex.forEach(function(element) {
            selectCEXCode.append('<option value="' + element.id + '">' + element.CEXCustomer + '</option>');
        });

        // si tenemos resultaos seleciconado el id más pequeño
        if (res.cex.length > 0) {
            selectCEXCode.val(res.cex[0].id);
        }
        

    }).catch(function(error) {
        console.error(error);
    });

}