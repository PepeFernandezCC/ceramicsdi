jQuery(document).ready(function ($) {
    jQuery('#ShippingStatusProcess').hide();
    jQuery('#ShippingStatusProcessBlock').hide();

    if (jQuery('#ActivateAutomaticTracking').is(':checked')) {
        jQuery('#ShippingStatusProcessBlock').show('slow');
    } else {
        jQuery('#ShippingStatusProcessBlock').hide('slow');
    }

    /* Validaciones de campos */
    jQuery.validator.addMethod(
        'validate_acc_iban',
        function (value) {
            if (value.substring(0, 4) === '****' || value == '') {
                return true;
            } else {
                return validate_acc_iban(value);
            }
        },
        wrongACCAndIBAN
    ); /* Retornamos el literal traducible del settings-user-configuration.tpl */


    /* Añadimos una nueva regla que compruebe que las dimensiones son 10x15x1 como mínimo,
    es decir, que sean mayores que 0, uno mayor que 10 y otro mayor de 15 */
    jQuery.validator.addMethod('dimensionsValidation', function(value, element){
        var values = [
            parseInt(jQuery("#DimensionsByDefaultHeight").val()),
            parseInt(jQuery("#DimensionsByDefaultWidth").val()),
            parseInt(jQuery("#DimensionsByDefaultLarge").val())
        ];
        var mayorQue0 = values.every(num => num > 0);
        var mayorQue10 = false;
        var mayorQue15 = false;

        for (var i = values.length - 1; i > -1; i--) {
            if (values[i] >= 15 && mayorQue15 === false) {
                mayorQue15 = true;
                values.splice(i, 1);
            }
            if (values[i] >= 10 && mayorQue10 === false) {
                mayorQue10 = true;
                values.splice(i, 1);
            }
        }

        return mayorQue0 && mayorQue10 && mayorQue15
    })

    jQuery.validator.addMethod('logoValidation', function(value, element){
        console.log(jQuery("#UploadLogoLabels").val())

        if (jQuery("#UploadLogoLabels").hasClass('logo_uploaded') && jQuery("#UploadLogoLabels").val() == '') {
        return true;           
        } else if (!jQuery("#UploadLogoLabels").hasClass('logo_required') && jQuery("#UploadLogoLabels").val() == ''){
            return false;
        } else {
            return true;
        }
    })

    $('#UserConfigurationDataForm').validate({
        rules: {
            DefaultPackages: {
                required: true,
                min: 1,
                max: 10
            },
            BankAccNumberAndIBAN: {
                required: false,
                validate_acc_iban: false
            },
            GoogleMapsApi: {
                required: false,
                maxlength: 150
            },
            LabelAlternativeText: {
                required: true,
                minlength: 3,
                maxlength: 40
            },
            WeightByDefault: {
                required: true,
                min: 0.1,
                max: 30
            },
            DimensionsByDefaultHeight: {
                required: true,
                dimensionsValidation: true
            },
            DimensionsByDefaultWidth: {
                required: true,
                dimensionsValidation: true
            },
            DimensionsByDefaultLarge: {
                required: true,
                dimensionsValidation: true
            },
            ShowLabelData: {
                required: true,
                min: 1,
                max: 30
            },
            UploadLogoLabels: {
                logoValidation: true
            }
        },
        /* Mensaje custom por campo  */
        messages: {
            DefaultPackages: {
                min: minValue1,
                max: maxValue10
            },
            BankAccNumberAndIBAN: {
                required: requiredCustomMessage,
                validate_acc_iban: wrongACCAndIBAN
            },
            GoogleMapsApi: {
                maxlength: maxLengthMessage + ' 150 ' + characters
            },
            LabelAlternativeText: {
                required: requiredCustomMessage,
                minlength: minLengthMessage + ' 3 ' + characters,
                maxlength: maxLengthMessage + ' 40 ' + characters
            },
            WeightByDefault: {
                required: requiredCustomMessage,
                min: valuesWeightDefault,
                max: valuesWeightDefault
            },
            DimensionsByDefaultHeight: {
                dimensionsValidation: valuesDimensionDefault
            },
            DimensionsByDefaultWidth: {
                dimensionsValidation: valuesDimensionDefault
            },
            DimensionsByDefaultLarge: {
                dimensionsValidation: valuesDimensionDefault
            },
            UploadLogoLabels: {
                required: requiredCustomMessage
            }
        },
        groups: {
            valuesDimensionDefault: "DimensionsByDefaultHeight DimensionsByDefaultWidth DimensionsByDefaultLarge"
        },

        submitHandler: function () {
            var formElement = document.getElementById('UserConfigurationDataForm');
            if (!jQuery("#UploadLogoLabels").hasClass('logo_uploaded') || jQuery("#UploadLogoLabels").val() !== '') {
                if (typeof document.getElementById('UploadLogoLabels').files[0] !== 'undefined') {
                    var imgLogoName = document.getElementById('UploadLogoLabels').files[0].name;
                } else {
                    var imgLogoName = 'default.jpg';
                }
            }
            if (jQuery("#UploadLogoLabels").hasClass('logo_uploaded') && jQuery("#UploadLogoLabels").val() == '') {
                var inputElement = formElement.querySelector('input[name="UploadLogoLabels"]'); // Buscar el input dentro del formulario por su nombre
                inputElement.disabled = true;
            }

            $.ajax({
                url: AdminCorreosOficialUserConfigurationProcess,
                type: 'POST',
                data: new FormData(formElement),
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    $('#response').html(data);
                    var obj = JSON.parse(data);

                    if (obj.error == 'Error') {
                        showModalErrorWindow(obj.desc);
                    } else {
                        if (!jQuery("#UploadLogoLabels").hasClass('logo_uploaded') || jQuery("#UploadLogoLabels").val() !== ''){
                            $('#UploadLogoLabelsImg').attr('src', co_base_dir + 'media/logo_label/' + imgLogoName);
                            $('#UploadLogoLabelsText').html(imgLogoName);
                        }
                        showModalInfoWindow(userConfigurationSaved);
                    }
                },
                error: function (e) {
                    alert('ERROR 12000: Error al enviar el formulario Configuración de usuario.');
                }
            });
        }
    });

    // $('#BankAccNumberAndIBAN').on('click', function(){
    //     if ($("#BankAccNumberAndIBAN").val().substring(0, 4) === "****") {
    //     ibanNumber = $("#BankAccNumberAndIBAN").val();
    //     }
    //     $("#BankAccNumberAndIBAN").val("");
    // });

    // $('#BankAccNumberAndIBAN').on('blur', function(){

    //     if ($("#BankAccNumberAndIBAN").val() == ''){
    //         $("#BankAccNumberAndIBAN").val(ibanNumber);
    //     }

    /* Comportamiento de Tiempo de actualización de estados */
    jQuery('#CronInterval').change(function () {
        let valor = jQuery('#CronInterval').val();
        switch (valor) {
            case '2':
                jQuery('#CronInterval_TEXT').html('2 ' + hours);
                break;
            case '3':
                jQuery('#CronInterval_TEXT').html('3 ' + hours);
                break;
            case '4':
                jQuery('#CronInterval_TEXT').html('4 ' + hours);
                break;
            case '5':
                jQuery('#CronInterval_TEXT').html('5 ' + hours);
                break;
            case '6':
                jQuery('#CronInterval_TEXT').html('6 ' + hours);
                break;
            case '7':
                jQuery('#CronInterval_TEXT').html('7 ' + hours);
                break;
            case '8':
                jQuery('#CronInterval_TEXT').html('8 ' + hours);
                break;
        }
    });

    /* Evento para controlar visibilidad de bloque del progreso del envío en la tienda */
    jQuery('#ActivateAutomaticTracking').on('click', function () {
        if (jQuery(this).is(':checked')) {
            jQuery('#ShippingStatusProcessBlock').show('slow');

            if (jQuery('#ShowShippingStatusProcess').is(':checked')) {
                jQuery('#ShippingStatusProcess').show('slow');
            } else {
                jQuery('#ShippingStatusProcess').hide('slow');
            }
        } else {
            jQuery('#ShippingStatusProcessBlock').hide('slow');
            jQuery('#ShippingStatusProcess').hide('slow');
            jQuery('#ShowShippingStatusProcess').prop('checked', false);
        }
    });

    /* Mostramos/ocultamos progreso del estado del envío en la tienda */
    jQuery('#ShowShippingStatusProcess').on('click', function () {
        if (jQuery(this).is(':checked')) {
            jQuery('#ShippingStatusProcess').show('slow');
        } else {
            jQuery('#ShippingStatusProcess').hide('slow');
        }
    });

    if (jQuery('#ShowShippingStatusProcess').is(':checked')) {
        jQuery('#ShippingStatusProcess').show();
    }

    jQuery('#clean-upload').on('click', function() {
        jQuery('#UploadLogoLabels').val('');
    });
});
