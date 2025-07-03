jQuery(document).ready(function() {

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    var checkbox = jQuery('#changeShip'), chShipBlock = jQuery('#changeShipInputs');
    var checkbox2 = jQuery('#ActivateAutomaticTracking'), chShipBlock2 = jQuery('#ActivateOrderStatusChangeCheck');
    var checkbox3 = jQuery('#ActivateOrderStatusChange'), chShipBlock3 = jQuery('#StatesSelectorBlock1'), chShipBlock4 = jQuery('#StatesSelectorBlock2');
    var ShowActivateWeightByDefaultInput = jQuery('#ActivateWeightByDefault'), BlockWeightByDefaultInput = jQuery('#ActivateWeightByDefaultInput');
    var ShowChangeLogoOnLabel = jQuery('#ChangeLogoOnLabel'), BlockChangeLogoOnLabel = jQuery('#UploadLogoLabelsBlock');
    var ShowLabelAlternativeText = jQuery('#CustomerAlternativeText'), BlockLabelAlternativeText = jQuery('#LabelAlternativeTextInput');
    var ActivateOrderStatusChangeAfterSaveCheckbox = jQuery('#ActivateOrderStatusChangeAfterSave'), BlockStatusSelector = jQuery('#StatusSelectorInput');
    
    var MessageToWarnBuyerCheckBox = jQuery('#MessageToWarnBuyer');
    var MessageToWarnBuyer2 = jQuery('#MessageToWarnBuyer2');
    var ActivateDimensionsByDefault = jQuery('#ActivateDimensionsByDefault'), ActivateDimensionsByDefaultBLock = jQuery('#ActivateDimensionsByDefaultBLock');

    chShipBlock.hide();
    chShipBlock2.hide();
    chShipBlock3.hide();
    chShipBlock4.hide();
    BlockWeightByDefaultInput.hide();
    BlockChangeLogoOnLabel.hide();
    BlockLabelAlternativeText.hide();
    BlockStatusSelector.hide();
    MessageToWarnBuyer2.hide();
    ActivateDimensionsByDefaultBLock.hide()

    if (jQuery('#ActivateWeightByDefault').is(':checked')){
        jQuery('#ActivateWeightByDefaultInput').show();
    };
    if (jQuery('#ActivateDimensionsByDefault').is(':checked')){
      jQuery('#ActivateDimensionsByDefaultBLock').show();
    };
    if (jQuery('#ChangeLogoOnLabel').is(':checked')){
        jQuery('#UploadLogoLabelsBlock').show();
    };
    if (jQuery('#CustomerAlternativeText').is(':checked')){
        jQuery('#LabelAlternativeTextInput').show();
    };
   
    // Si Activar cambio de estado de la orden tras grabación mostramos Selecciona un estado
    if (jQuery('#ActivateOrderStatusChangeAfterSave').is(':checked')){
        jQuery('#StatusSelectorInput').show();
    }
    // Si Activar tracking automático y Activar cambio de estado de la orden 
    // mostramos Activar cambio de estado de la orden y sus 2 bloques de estados
    if (jQuery('#ActivateAutomaticTracking').is(':checked') && jQuery('#ActivateOrderStatusChange').is(':checked')){
        jQuery('#StatesSelectorBlock1').show();
        jQuery('#StatesSelectorBlock2').show();
        jQuery('#ActivateOrderStatusChangeCheck').show();
    } 
    // Si NO Activar tracking automático y Activar cambio de estado de la orden ocultamos los 2 bloques de estados
    else if (!jQuery('#ActivateAutomaticTracking').is(':checked') && jQuery('#ActivateOrderStatusChange').is(':checked')){
        jQuery('#StatesSelectorBlock1').hide();
        jQuery('#StatesSelectorBlock2').hide();
    }
    // Si Activar Tracking Automático mostramos Activar cambio de estado de la orden
    else if (jQuery('#ActivateAutomaticTracking').is(':checked')){
        jQuery('#ActivateOrderStatusChangeCheck').show();
    }

    ActivateDimensionsByDefault.on('click', function() {
      if(jQuery(this).is(':checked')) {
         ActivateDimensionsByDefaultBLock.show("slow");
         ActivateDimensionsByDefaultBLock.find('input').attr('required', true);
      } else {
         ActivateDimensionsByDefaultBLock.hide("slow");
         ActivateDimensionsByDefaultBLock.find('input').attr('required', false);
      }
   });

    checkbox.on('click', function() {
       if(jQuery(this).is(':checked')) {
          chShipBlock.show();
          chShipBlock.find('input').attr('required', true);
       } else {
          chShipBlock.hide();
          chShipBlock.find('input').attr('required', false);
       }
    });

    checkbox2.on('click', function() {
        if(jQuery(this).is(':checked')) {
           chShipBlock2.show();
           chShipBlock2.find('input').attr('required', true);
        } else {
           chShipBlock2.hide();
           chShipBlock2.find('input').attr('required', false);
        }
         if (!jQuery('#ActivateAutomaticTracking').is(':checked')){
            jQuery('#StatesSelectorBlock1').hide();
            jQuery('#StatesSelectorBlock2').hide();
         }
         else if (!jQuery('#ActivateOrderStatusChangeCheck').is(':checked')){
            jQuery('#StatesSelectorBlock1').hide();
            jQuery('#StatesSelectorBlock2').hide();
         }
     });

     checkbox3.on('click', function() {
        if(jQuery(this).is(':checked')) {
           chShipBlock3.show();
           chShipBlock3.find('input').attr('required', true);
           chShipBlock4.show();
           chShipBlock4.find('input').attr('required', true);
        } else {
           chShipBlock3.hide();
           chShipBlock3.find('input').attr('required', false);
           chShipBlock4.hide();
           chShipBlock4.find('input').attr('required', false);
        }
     });

     ShowActivateWeightByDefaultInput.on('click', function() {
        if(jQuery(this).is(':checked')) {
           BlockWeightByDefaultInput.show("slow");
           BlockWeightByDefaultInput.find('input').attr('required', true);
        } else {
           BlockWeightByDefaultInput.hide("slow");
           BlockWeightByDefaultInput.find('input').attr('required', false);
        }
     });

     ShowChangeLogoOnLabel.on('click', function() {
      if(jQuery(this).is(':checked')) {
         BlockChangeLogoOnLabel.show("slow");
         BlockChangeLogoOnLabel.find('input').attr('required', true);
      } else {
         BlockChangeLogoOnLabel.hide("slow");
         BlockChangeLogoOnLabel.find('input').attr('required', false);
         $('#UploadLogoLabelsImg').attr('src', co_base_dir + 'media/logo_label/default.jpg');
      }
   });

     ShowLabelAlternativeText.on('click', function() {
      if(jQuery(this).is(':checked')) {
         BlockLabelAlternativeText.show("slow");
         BlockLabelAlternativeText.find('input').attr('required', true);
      } else {
         BlockLabelAlternativeText.hide("slow");
         BlockLabelAlternativeText.find('input').attr('required', false);
      }
   });
   
   ActivateOrderStatusChangeAfterSaveCheckbox.on('click', function() {
      if(jQuery(this).is(':checked')) {
         BlockStatusSelector.show();
         BlockStatusSelector.find('input').attr('required', true);
      } else {
         BlockStatusSelector.hide();
         BlockStatusSelector.find('input').attr('required', false);
      }
   });

    /* **********************************************************************
    *             TRAMITES ADUANEROS: MENSAJE PARA ADVERTIR AL COMMPRADOR.       
    *********************************************************************** */
   MessageToWarnBuyerCheckBox.on('click', function() {
      if(jQuery(this).is(':checked')) {
         MessageToWarnBuyer2.show("slow");
         MessageToWarnBuyer2.find('input').attr('required', true);
      } else {
         MessageToWarnBuyer2.hide("slow");
         MessageToWarnBuyer2.find('input').attr('required', false);
      }
   });

   if ( MessageToWarnBuyerCheckBox.is(':checked')){
      MessageToWarnBuyer2.show();
   }

   /* **********************************************************************************************************
    *                                  MODAL      
    ********************************************************************************************************* */
   jQuery("#myModalCancelButton").click(function(){
      closeMyModalConfirmation();
    });
    jQuery("#myModalAcceptButton").click(function(){
      closeMyModalConfirmation();
    });

   // Cerramos modal al pulsar la tecla enter
   jQuery( "#myModal" ).keydown(function() {
      jQuery(this).keypress(function(e) {
         if(e.which == 13) {
            closeMyModalConfirmation();
         }
      });
   });

});

/* **********************************************************************************************************
 *                                  MODAL      
 ********************************************************************************************************* */
function closeMyModalConfirmation(){
   jQuery("#myModal").modal('hide');
   jQuery("#myModal").modal('dispose');
 }

  // Mostrar ventana Modal
  function showModalErrorWindow(desc) {
    jQuery("#myModalTitle").html(errorTitle);
    jQuery("#myModalDescription p").html(desc);
    jQuery("#myModalActionButtonCustomerData").hide();
    jQuery("#myModalActionButtonSenders").hide();
    jQuery("#myModalCancelButton").hide();
    jQuery("#myModalAcceptButton").html(acceptButton);
    jQuery("#myModalAcceptButton").show();
    jQuery("#myModal").modal("show");
  }

  // Mostrar ventana Modal de Información
 function showModalInfoWindow(desc) {
   jQuery("#myModalTitle").html(informationTitle);
   jQuery("#myModalDescription p").html(desc);
   jQuery("#myModalActionButtonCustomerData").hide();
   jQuery("#myModalActionButtonSenders").hide();
   jQuery("#myModalCancelButton").hide();
   jQuery("#myModalAcceptButton").html(acceptButton);
   jQuery("#myModalAcceptButton").show();
   jQuery("#myModal").modal("show");
   jQuery("#myModal").focus();
 }

