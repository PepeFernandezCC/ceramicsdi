jQuery(document).ready(function($) {
    var myModal = new bootstrap.Modal(document.getElementById('myModal'));

    function split_time_seconds(time_to_split){
      var time_split = time_to_split.split(':');
      return time_split[0] + ':' + time_split[1];
   };

   /* Validaciones de campos */
    jQuery.validator.addMethod( "validate_nif_cif_nie", function (value) {
        result = validate_nif_cif_nie(value);
        return result.valid;

    }, jQuery.validator.format(wrongDniCif)); /* Literal traducible en senders.tpl que mostramos si el DNI/CIF es incorrecto. */

    /* Reglas */

    $.validator.addMethod("selectOneRequired", function(value, element, options) {
        var correosCodeValue = $('#correos_code').val();
        var cexCodeValue = $('#cex_code').val();
        return (correosCodeValue !== '' || cexCodeValue !== '');
    }, selectAContract);

   $("#CorreosSendersForm").validate({

      rules: {
          sender_name : {
              required: true,
              minlength: 3,
              maxlength: 40
          },
          sender_contact : {
              required: true,
              minlength: 3,
              maxlength: 40
          },
          sender_address : {
              required: true,
              minlength: 3,
              maxlength: 100
          },
          sender_city : {
              required: true,
              minlength: 3,
              maxlength: 40
          },
          sender_cp : {
              required: true,
              minlength: 3,
              maxlength: 8
          },
          sender_iso_code_pais : {
              required: true,
          },
          sender_phone : {
              required: false
          },
          sender_email : {
              required: false,
              minlength: 3,
              maxlength: 50
          },
          sender_nif_cif : {
              required: true,
              maxlength: 30,
              validate_nif_cif_nie: false
          },
          correos_code: {
            selectOneRequired: true
          },
          cex_code: {
            selectOneRequired: true
          }
      }, 
        messages: {
            sender_name: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 3 ' + characters,
                maxlength: maxLengthMessage + ' 40 ' + characters
            },
            sender_contact: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 3 ' + characters,
                maxlength: maxLengthMessage + ' 40 ' + characters
            },
            sender_address: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 3 ' + characters,
                maxlength: maxLengthMessage + ' 100 ' + characters
            },
            sender_city: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 3 ' + characters,
                maxlength: maxLengthMessage + ' 40 ' + characters
            },
            sender_cp: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 3 ' + characters,
                maxlength: maxLengthMessage + ' 8 ' + characters
            },
            sender_iso_code_pais: {
                required: requiredCustomMessage                
            },
            sender_phone: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 3 ' + characters,
                maxlength: maxLengthMessage + ' 15 ' + characters
            },
            sender_email: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 3 ' + characters,
                maxlength: maxLengthMessage + ' 50 ' + characters,
                email: invalidEmail
            },
            sender_nif_cif: {
                required: requiredCustomMessage
            },
     }, /* Fin validaciones */
      
      submitHandler: function() {

        var table = $('#SendersDataTable').DataTable();
        var rows  = table.rows();

        if (rows['0'].length == 0){
            is_first_sender = "1";
        } else {
            is_first_sender = "0";
        }
         
        var data_senders_request  = {
            action: 'CorreosSendersInsertForm',
            sender_name:    $('#sender_name').val(),
            sender_address: $('#sender_address').val(),
            sender_cp:      $('#sender_cp').val(),
            sender_nif_cif: $('#sender_nif_cif').val(),
            sender_city:    $('#sender_city').val(),
            sender_contact: $('#sender_contact').val(),
            sender_phone:   $('#sender_phone').val(),
            sender_from_time: $('#sender_from_time').val(),
            sender_to_time: $('#sender_to_time').val(),
            sender_iso_code_pais: $('#sender_iso_code_pais').val(),
            sender_email: $('#sender_email').val(),
            correos_code: $('#correos_code').val(),
            cex_code: $('#cex_code').val(),
            sender_default: is_first_sender
         };    
         
         jQuery.post(AdminCorreosOficialSendersProcess, data_senders_request, function(response) {
            $('#SendersDataTable').DataTable().ajax.reload();
            document.getElementById("CorreosSendersForm").reset();
            reloadSenderContractsSelects();

            showModalInfoWindow(senderDefaultSaved);
         });
          
      }
  }); // Fin Validaciones

   
   $('#SendersDataTable').DataTable( {
      "searching": false,
      "paging":   false,
      "ordering": false,
      "info":     false,
      "ajax": {
          "url": AdminCorreosOficialSettingsGetDataTable,
          "dataSrc": ""
      },
      "columns": [
          { "data": "id" },
          { "data": "sender_name" },
          { "data": "CorreosCustomer" },
          { "data": "CEXCustomer" },
          { "data": "sender_address" },
          { "data": "sender_cp" },
          { "data": "sender_nif_cif" },
          { "data": "sender_city" },
          { "data": "sender_contact" },
          { "data": "sender_phone" },
          { "data": null, 
            render: function (data, type, row) {
               return split_time_seconds(row.sender_from_time);
            }
          },
          { "data": null, 
            render: function (data, type, row) {
               return split_time_seconds(row.sender_to_time);
            }
          },
          { "data": "sender_iso_code_pais" },
          { "data": "sender_email" },
          { "data": "sender_default",
            render: function(data, type, row) {
                const isDefault = data == 1 ? 'checked disabled' : '';
                return '<input type="checkbox" class="correosSenderDefault" data-id="' + row.id + '"' + isDefault + '>';
            }
          },
          {
            data: null,
            className: "",
            defaultContent: '<a class="btn btn-primary edit"><i class="far fa-edit"></i></a>',
            orderable: false
          },
          {
                orderable: false,
                defaultContent: '',
                render: function (data, type, full, meta) {
                    if (full.sender_default == 1 ) {
                        return '<a class="btn btn-danger remove disabled"><i class="far fa-trash-alt"></i></a>';
                    } else {
                        return '<a class="btn btn-danger remove"><i class="far fa-trash-alt"></i></a>';
                    }
                }
            },
          
      ]
   });
  
    // Borrado de remitente
    $('#SendersDataTable').on('click', '.remove', function () {
      
        var table = $('#SendersDataTable').DataTable();
        var row  = table.row($(this).parents('tr')[0]);
        
        var data_senders_request  = {
            action      : 'CorreosSendersDeleteForm',
            sender_id:    table.row( row ).data().id
        };  

        $('#myModal').find('#myModalActionButtonSenders').html('Eliminar');
        $('#myModal').find('.myModalActionButton').hide();
        $("#myModalActionButtonSenders").show();
        $('#myModal').find('#myModalDescription').html('<p>¿Está seguro de borrar el remitente?</p>');
        $('#myModal').data('id', table.row( row ).data().id).modal('show');

        //Aceptar
        $("body").on('click', '#myModalActionButtonSenders', function() {
            var id = $('#myModal').data('id');
            table.row('#' . id).remove().draw();
            $('#myModal').modal('hide');
            
            jQuery.post(AdminCorreosOficialSendersProcess, data_senders_request, function(response) {
                $('#SendersDataTable').DataTable().ajax.reload();
                document.getElementById("CorreosSendersForm").reset();
            });
        });

        //Cancelar
        $("body").on('click', '#myModalCancelButton', function() {
            $('#myModal').modal('hide');
        });
   });

   // Edición de remitente
   $('#SendersDataTable').on('click', '.edit', function () {

      // Limpiar validaciones	
      $('#CorreosSendersForm').validate().resetForm();

      document.getElementById("SendersEditButton").disabled = false;
      document.getElementById("SendersSaveButton").disabled = true;

      var table = $('#SendersDataTable').DataTable();
      var row  = table.row($(this).parents('tr')[0]);

      let correos_code = parseInt(table.row( row ).data().correos_code);
      let cex_code = parseInt(table.row( row ).data().cex_code);

      document.getElementById('sender_id').value = table.row( row ).data().id;
      document.getElementById('sender_name').value = table.row( row ).data().sender_name;
      document.getElementById('sender_address').value = table.row( row ).data().sender_address;
      document.getElementById('sender_cp').value = table.row( row ).data().sender_cp;
      document.getElementById('sender_nif_cif').value = table.row( row ).data().sender_nif_cif;
      document.getElementById('sender_city').value = table.row( row ).data().sender_city;
      document.getElementById('sender_contact').value = table.row( row ).data().sender_contact;
      document.getElementById('sender_phone').value = table.row( row ).data().sender_phone;
      document.getElementById('sender_from_time').value = split_time_seconds(table.row( row ).data().sender_from_time);
      document.getElementById('sender_to_time').value = split_time_seconds(table.row( row ).data().sender_to_time);
      document.getElementById('sender_iso_code_pais').value = table.row( row ).data().sender_iso_code_pais;
      document.getElementById('sender_email').value = table.row( row ).data().sender_email;
      document.getElementById('correos_code').value = correos_code != 0 ? correos_code : '';
      document.getElementById('cex_code').value = cex_code != 0 ? cex_code : '';

   });

   // Guarda remitente por defecto
   $('#SendersDataTable').on('click', '.correosSenderDefault', function () {

        let data_sender_default_request  = {
            action: 'CorreosSenderSaveDefaultForm',
            sender_default_id: $(this).data('id')
        };    

        jQuery.post(AdminCorreosOficialSendersProcess, data_sender_default_request, function(response) {
            showModalInfoWindow(senderDefaultSaved);
            $('#SendersDataTable').DataTable().ajax.reload();
        });

   });

   //Limpia formulario
   $("#SendersCleanButton").click(function(event) {
      // Limpiar validaciones	
      $('#CorreosSendersForm').validate().resetForm();
      document.getElementById("SendersEditButton").disabled = true;
      document.getElementById("SendersSaveButton").disabled = false;
   });	

   //AJAX Edición de remitente
   $("#SendersEditButton").click(function(event) { 


        if(jQuery('#CorreosSendersForm').valid()){

            var data_senders_request  = {
                action      : 'CorreosSendersUpdateForm',
                sender_id:      $('#sender_id').val(),
                sender_name:    $('#sender_name').val(),
                sender_address: $('#sender_address').val(),
                sender_cp:      $('#sender_cp').val(),
                sender_nif_cif: $('#sender_nif_cif').val(),
                sender_city:    $('#sender_city').val(),
                sender_contact: $('#sender_contact').val(),
                sender_phone:   $('#sender_phone').val(),
                sender_from_time: $('#sender_from_time').val(),
                sender_to_time: $('#sender_to_time').val(),
                sender_iso_code_pais: $('#sender_iso_code_pais').val(),
                sender_email: $('#sender_email').val(),
                correos_code: $('#correos_code').val(),
                cex_code: $('#cex_code').val()
            };    
            
            jQuery.post(AdminCorreosOficialSendersProcess, data_senders_request, function(response) {
                document.getElementById("CorreosSendersForm").reset();
                document.getElementById("SendersEditButton").disabled = true;
                document.getElementById("SendersSaveButton").disabled = false;
                reloadSenderContractsSelects();

                showModalInfoWindow(senderDefaultSaved);

                $('#SendersDataTable').DataTable().ajax.reload();
            });

        }

   });	

   // Scroll y focus al editar remitente
    $('#SendersDataTable').on('click', '.edit', function () {
        scrollToAnchor('#sender-anchor');
        $("#sender_name").focus();
    });

    function scrollToAnchor(aid){
        var aTag = $(aid);
        $('html,body').animate({scrollTop: aTag.offset().top},'slow');
    }

    // Si viene de pedido abrimos bloque Remitente en Ajustes
    if (document.location.hash == '#sender-anchor'){
        scrollToAnchor('#sender_block');
        $('#sender_block').click();
    }

});
