jQuery(document).ready(function () {
    jQuery.ajax({
        url: AdminCorreosOficialExecuteCron + '&token=' + token,
        type: 'POST',
        async: true,
        contentType: false,
        cache: false,
        processData: false,
        success: function () {},
        error: function () {
            showModalErrorWindow('ERROR 15011: Ha habido un problema con el CRON');
        }
    });
});
