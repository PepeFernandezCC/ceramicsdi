jQuery(document).ready(function($) {
    
  $("#CorreosZonesCarriersForm").submit(function( e ) {
        $.ajax({
            url: AdminCorreosOficialZonesCarriersProcess,
            type: "POST",
            data:  new FormData(this),
            contentType: false,
            cache: false,
            processData:false,
            success: function(data)
            {
              showModalInfoWindow(zonesCarriersSaved);
            }            
        });
        e.preventDefault();
    }); 

    $('.hidden-product-option').hide();

    $('#showAllCarriersCheck').change(function () {
        if($('#showAllCarriersCheck').is(':checked')) {
            $('.hidden-product-option').show();
        } else {
          $('.hidden-product-option').hide();
        }
    });

});
