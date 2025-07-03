jQuery(document).ready(function($) {

    jQuery('#UploadCorreosCronForm').validate({
        submitHandler: function () {
            let formElement = document.getElementById('UploadCorreosCronForm');

            /** Procesamos el formulario de Alta/Modificación Cliente de Correos */
            jQuery.ajax({
                url: AdminCorreoOficiaCronURL,
                type: 'POST',
                data: new FormData(formElement),
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    showModalInfoWindow(cronSavedSuccesfully);
                },
                error: function (e) {
                    showModalErrorWindow('ERROR 15010: ' + cronTechnicalError);
                }
            });
        }
    }); // Fin Validaciones Correos

/*     jQuery("#CronInterval").change(function() {
        let valor=(jQuery)('#CronInterval').val();
        switch(valor){
            case '2':
            (jQuery)('#CronInterval_TEXT').html("2 "+hours);
            break;
            case '3':
            (jQuery)('#CronInterval_TEXT').html("3 "+hours);
            break;
            case '4':
            (jQuery)('#CronInterval_TEXT').html("4 "+hours);
            break;
            case '5':
            (jQuery)('#CronInterval_TEXT').html("5 "+hours);
            break;
            case '6':
            (jQuery)('#CronInterval_TEXT').html("6 "+hours);
            break;
            case '7':
            (jQuery)('#CronInterval_TEXT').html("7 "+hours);
            break;
            case '8':
            (jQuery)('#CronInterval_TEXT').html("8 "+hours);
            break;
        }
    }); */

});
