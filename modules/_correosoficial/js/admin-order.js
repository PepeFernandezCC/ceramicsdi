/**
 * This program is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program.
 * If not, see https://www.gnu.org/licenses/.
 */
jQuery(document).ready(function () {

    const mainContentLoaded = document.getElementById('correos_oficial_main_container');
    
    if (mainContentLoaded) {
        /* INICIALIZACIÓN */
        let init_selected_carrier = jQuery('#input_select_carrier').find('option:selected');
        let company = init_selected_carrier.data('company');
        let pickupDone = false;
        let missingData = new Object();

        // COMPROBACION VISUAL DE RECOGIDAS SEGUN CORREOS O CEX
        let container = jQuery('#masive_pickup_container');
        let inputLabel = jQuery('#orderAdminPrintLabelPickup');
        let inputPackageSize = jQuery('#orderAdminPackageSize');
        let selectedCompany = '';
        let checkbox = jQuery('#inputCheckSavePickup');

        let bultos = jQuery('#correos-num-parcels').val();
        let require_customs_doc = jQuery('#require_customs_doc_hidden').val();

        jQuery('#input_select_carrier').on('change', function () {
            let selectedOption = jQuery(this).find('option:selected');

            selectedCompany = selectedOption.data('company');
            checkCorreosOrCEX(selectedCompany);
        });

        checkbox.on('change', function () {
            let isChecked = checkbox.prop('checked');
            showContent(isChecked);
        });

        if (company == 'Correos') {
            jQuery('#inputCheckSavePickup').prop('checked', false);
            setCorreosRangeDate('pickup_date');
            setCorreosRangeDate('return_pickup_date');
        } else {
            setCEXRangeDate('pickup_date');
            setCEXRangeDate('return_pickup_date');
        }

        if (jQuery('#order_done_hidden').val()) {
            disableForm('#container_customer');
            disableForm('#container_shipping');
            disableForm('#added_values');
        }

        // Gestiona CodeAT de CEX cuando cambia customer_country
        jQuery('#customer_country').on('change', function () {
            manageCodeAT();
        });

        // Ocultamos selector de bultos dependiendo de la compañía
        let selected_carrier_return = jQuery('#input_select_carrier_return').find('option:selected');
        let return_company = selected_carrier_return.data('company');

        jQuery('#input_select_carrier_return').on('change', function () {
            var selected_carrier_return = jQuery('#input_select_carrier_return').find('option:selected');
            var company = selected_carrier_return.data('company');

            if (company == 'Correos') {
                jQuery('#save-return-pickup-container').addClass('hidden-block');
                jQuery('#generate_return_pickup').removeClass('hidden-block');
                jQuery('#correos-options-pickup-return-container').removeClass('hidden-block');
                jQuery('.correos-num-parcels-return-container').addClass('hidden-block');
            } else if (company == 'CEX'){
                jQuery('#save-return-pickup-container').removeClass('hidden-block');
                // Ocultamos el blqoue de opciones de Correos y el botón de generar recogida para CEX  ya que se hace automáticamente
                jQuery('#generate_return_pickup').addClass('hidden-block');
                jQuery('#correos-options-pickup-return-container').addClass('hidden-block');
                jQuery('.correos-num-parcels-return-container').removeClass('hidden-block');
                jQuery('#pickupReturnButton').addClass('hidden-block');
            }

            manageReturnCustomDocPackage(company);
        });

        // Para cerrar alerts sin eliminación en el DOM
        jQuery('#success_register').on('close.bs.alert', function () {
            jQuery('#success_register').addClass('hidden-block');
            return false;
        });

        jQuery('#error_register').on('close.bs.alert', function () {
            jQuery('#error_register').addClass('hidden-block');
            return false;
        });

        jQuery('#success_register_return').on('close.bs.alert', function () {
            jQuery('#success_register_return').addClass('hidden-block');
            return false;
        });

        jQuery('#error_register_return').on('close.bs.alert', function () {
            jQuery('#error_register_return').addClass('hidden-block');
            return false;
        });

        jQuery('#no_offices_zip_message').on('close.bs.alert', function () {
            jQuery('#no_offices_zip_message').addClass('hidden-block');
            return false;
        });

        jQuery('#no_citypaqs_zip_message').on('close.bs.alert', function () {
            jQuery('#no_citypaqs_zip_message').addClass('hidden-block');
            return false;
        });

        /* FUNCIONALIDAD BULTOS */

        // EventListener para formulario de bulto
        jQuery("input[type='radio']").on('change', function () {
            let index_id = jQuery(this)[0].name.indexOf('_');
            let id_radio = jQuery(this)[0].name.substring(index_id + 1, jQuery(this)[0].name.length);
            if (this.value == '0') {
                jQuery('#packageCustomDesc_' + id_radio).prop('disabled', false);
                jQuery('#packageTariffCode_' + id_radio).prop('disabled', true);
                jQuery('#packageTariffDesc_' + id_radio).prop('disabled', true);
                jQuery('#packageTariffDesc_' + id_radio).prop('required', false);
            } else {
                jQuery('#packageCustomDesc_' + id_radio).prop('disabled', true);
                jQuery('#packageTariffCode_' + id_radio).prop('disabled', false);
                jQuery('#packageTariffDesc_' + id_radio).prop('disabled', false);
                jQuery('#packageTariffDesc_' + id_radio).prop('required', true);
            }

            if (this.value == '0') {
                jQuery('#packageCustomDescReturn_' + id_radio).prop('disabled', false);
                jQuery('#packageTariffCodeReturn_' + id_radio).prop('disabled', true);
                jQuery('#packageTariffDescReturn_' + id_radio).prop('disabled', true);
                jQuery('#packageTariffDescReturn_' + id_radio).prop('required', false);
            } else {
                jQuery('#packageCustomDescReturn_' + id_radio).prop('disabled', true);
                jQuery('#packageTariffCodeReturn_' + id_radio).prop('disabled', false);
                jQuery('#packageTariffDescReturn_' + id_radio).prop('disabled', false);
                jQuery('#packageTariffDescReturn_' + id_radio).prop('required', true);
            }
        });

        // Clonación de bultos devolución
        jQuery('#correos-num-parcels-return').change(function () {
            jQuery('.container-bulto-return-cloned').remove();

            let cloneId = 1;
            let id_bulto;

            for (let i = 1; i < jQuery(this).val(); i++) {
                cloneId++;
                id_bulto = 'containerBultoReturn_' + cloneId;
                let clone = jQuery('#containerBultoReturn_1').clone().attr({ id: id_bulto }).addClass('container-bulto-return-cloned');
                clone.find('.card-header').html('Devolución del paquete ' + cloneId);

                clone.find("input[name='DescriptionRadioReturn_1']").prop('name', 'DescriptionRadioReturn_' + cloneId);

                clone.find("select[name='packageCustomDescReturn_1']").attr('id', 'packageCustomDescReturn_' + cloneId);
                clone.find("select[name='packageCustomDescReturn_1']").prop('name', 'packageCustomDescReturn_' + cloneId);
                clone.find("input[name='packageTariffCodeReturn_1']").attr('id', 'packageTariffCodeReturn_' + cloneId);
                clone.find("input[name='packageTariffCodeReturn_1']").prop('name', 'packageTariffCodeReturn_' + cloneId);
                clone.find("input[name='packageTariffDescReturn_1']").attr('id', 'packageTariffDescReturn_' + cloneId);
                clone.find("input[name='packageTariffDescReturn_1']").prop('name', 'packageTariffDescReturn_' + cloneId);

                clone.find("input[name='packageWeightReturn_1']").prop('value', '');
                clone.find("input[name='packageWeightReturn_1']").prop('name', 'packageWeightReturn_' + cloneId);

                clone.find("input[name='packageAmountReturn_1']").prop('value', '');
                clone.find("input[name='packageAmountReturn_1']").prop('name', 'packageAmountReturn_' + cloneId);

                clone.find("input[name='packageLargeReturn_1']").attr('id', 'packageLargeReturn_' + cloneId);
                clone.find("input[name='packageWidthReturn_1']").attr('id', 'packageWidthReturn_' + cloneId);
                clone.find("input[name='packageHeightReturn_1']").attr('id', 'packageHeightReturn_' + cloneId);

                clone.find("input[name='packageLargeReturn_1']").prop('name', 'packageLargeReturn_' + cloneId);
                clone.find("input[name='packageWidthReturn_1']").prop('name', 'packageWidthReturn_' + cloneId);
                clone.find("input[name='packageHeightReturn_1']").prop('name', 'packageHeightReturn_' + cloneId);

                clone.find("textarea[name='deliveryRemarksReturn_1']").prop('name', 'deliveryRemarksReturn_' + cloneId);

                clone.appendTo('.container-bultos-return');
            }

            // EventListener para radiobuttons al clonar formulario bulto
            jQuery("input[type='radio']").on('change', function () {
                let index_id = jQuery(this)[0].name.indexOf('_');
                let id_radio = jQuery(this)[0].name.substring(index_id + 1, jQuery(this)[0].name.length);
                if (this.value == '0') {
                    jQuery('#packageCustomDescReturn_' + id_radio).prop('disabled', false);
                    jQuery('#packageTariffCodeReturn_' + id_radio).prop('disabled', true);
                    jQuery('#packageTariffDescReturn_' + id_radio).prop('disabled', true);
                    jQuery('#packageTariffDescReturn_' + id_radio).prop('required', false);
                } else {
                    jQuery('#packageCustomDescReturn_' + id_radio).prop('disabled', true);
                    jQuery('#packageTariffCodeReturn_' + id_radio).prop('disabled', false);
                    jQuery('#packageTariffDescReturn_' + id_radio).prop('disabled', false);
                    jQuery('#packageTariffDescReturn_' + id_radio).prop('required', true);
                }
            });
        });

        // Clonación de bultos
        jQuery('#correos-num-parcels').change(function () {
            let selected = jQuery('#input_select_carrier').find('option:selected');
            let carrier_type = selected.data('carrier_type');
            let max_packages = selected.data('max_packages');

            jQuery('.container-bulto-cloned').remove();

            if (jQuery(this).val() == 1) {
                jQuery('.all-packages-equal-container').addClass('hidden-block');
                jQuery('#partial_delivery_container').addClass('hidden-block');
            } else {
                jQuery('.all-packages-equal-container').removeClass('hidden-block');
                jQuery('#partial_delivery_container').removeClass('hidden-block');
            }

            let cloneId = 1;
            let id_bulto;
            for (let i = 1; i < jQuery(this).val(); i++) {
                cloneId++;
                id_bulto = 'containerBulto_' + cloneId;

                // Clonación de formulario sin eventos. Se asigna id único a cada campo
                let clone = jQuery('#containerBulto_1').clone(true, true).attr({ id: id_bulto }).addClass('container-bulto-cloned');
                clone.find('.card-header').html('Bulto ' + cloneId);

                clone.find('#DescriptionRadioDesc_1').attr('id', 'DescriptionRadioDesc_' + cloneId);
                clone.find('#DescriptionRadioTariff_1').attr('id', 'DescriptionRadioTariff_' + cloneId);

                clone.find("input[name='DescriptionRadio_1']").prop('name', 'DescriptionRadio_' + cloneId);

                clone.find("select[name='packageCustomDesc_1']").attr('id', 'packageCustomDesc_' + cloneId);
                clone.find("select[name='packageCustomDesc_1']").prop('name', 'packageCustomDesc_' + cloneId);
                clone.find("input[name='packageTariffCode_1']").attr('id', 'packageTariffCode_' + cloneId);
                clone.find("input[name='packageTariffCode_1']").prop('name', 'packageTariffCode_' + cloneId);
                clone.find("input[name='packageTariffDesc_1']").attr('id', 'packageTariffDesc_' + cloneId);
                clone.find("input[name='packageTariffDesc_1']").prop('name', 'packageTariffDesc_' + cloneId);

                clone.find("input[name='packageRef_1']").prop('value', '');
                clone.find("input[name='packageRef_1']").prop('name', 'packageRef_' + cloneId);

                clone.find("input[name='packageLarge_1']").prop('name', 'packageLarge_' + cloneId);
                clone.find("input[name='packageWidth_1']").prop('name', 'packageWidth_' + cloneId);
                clone.find("input[name='packageHeight_1']").prop('name', 'packageHeight_' + cloneId);

                clone.find("textarea[name='deliveryRemarks_1']").prop('name', 'deliveryRemarks_' + cloneId);

                /**
                 * Tabs de documentación aduanera
                 */
                clone.find('#tabs_customs_doc_1').attr('id', 'tabs_customs_doc_' + cloneId);

                clone.find('#customs_desc_1').attr('data-number', cloneId);
                clone.find('#customs_code_1').attr('data-number', cloneId);

                clone.find('#add_description_1').attr('data-number', cloneId);
                clone.find('#del_description_1').attr('data-number', cloneId);

                clone.find('#add_description_1').prop('disabled', false);
                clone.find('#add_description_1').attr('id', 'add_description_' + cloneId);
                clone.find('#del_description_1').attr('id', 'del_description_' + cloneId);

                clone.find('#added_customs_description_1').html('');
                clone.find('#added_customs_description_1').attr('id', 'added_customs_description_' + cloneId);

                clone.find('#customs_desc_tab_1').attr('id', 'customs_desc_tab_' + cloneId);
                clone.find('#customs_code_tab_1').attr('id', 'customs_code_tab_' + cloneId);

                clone.find('#customs_desc_1').attr('id', 'customs_desc_' + cloneId);
                clone.find('#customs_code_1').attr('id', 'customs_code_' + cloneId);

                clone.find("input[name='packageWeight_1']").prop('value', '');
                clone.find("input[name='packageWeight_1']").attr('id', 'packageWeight_' + cloneId);
                clone.find("input[name='packageWeight_1']").prop('name', 'packageWeight_' + cloneId);

                clone.find("input[name='packageWeightDesc_1']").prop('value', '');
                clone.find("input[name='packageWeightDesc_1']").attr('id', 'packageWeightDesc_' + cloneId);
                clone.find("input[name='packageWeightDesc_1']").prop('name', 'packageWeightDesc_' + cloneId);

                clone.find("input[name='packageAmount_1']").prop('value', '');
                clone.find("input[name='packageAmount_1']").attr('id', 'packageAmount_' + cloneId);
                clone.find("input[name='packageAmount_1']").prop('name', 'packageAmount_' + cloneId);

                clone.find("input[name='packageUnits_1']").prop('value', '');
                clone.find("input[name='packageUnits_1']").prop('name', 'packageUnits_' + cloneId);
                clone.find('#packageUnits_1').attr('id', 'packageUnits_' + cloneId);

                clone.appendTo('.container-bultos');

                co_DescriptionCounter[cloneId] = 1;
            }

            // EventListener para radiobuttons al clonar formulario bulto
            jQuery("input[type='radio']").on('change', function () {
                let index_id = jQuery(this)[0].name.indexOf('_');
                let id_radio = jQuery(this)[0].name.substring(index_id + 1, jQuery(this)[0].name.length);
                if (this.value == '0') {
                    jQuery('#packageCustomDesc_' + id_radio).prop('disabled', false);
                    jQuery('#packageTariffCode_' + id_radio).prop('disabled', true);
                    jQuery('#packageTariffDesc_' + id_radio).prop('disabled', true);
                    jQuery('#packageTariffDesc_' + id_radio).prop('required', false);
                } else {
                    jQuery('#packageCustomDesc_' + id_radio).prop('disabled', true);
                    jQuery('#packageTariffCode_' + id_radio).prop('disabled', false);
                    jQuery('#packageTariffDesc_' + id_radio).prop('disabled', false);
                    jQuery('#packageTariffDesc_' + id_radio).prop('required', true);
                }
            });

            if (carrier_type == 'international' && jQuery(this).val() > max_packages) {
                jQuery('.alert-max-packages').removeClass('hidden-block');

                jQuery('#all_packages_equal').prop('disabled', true);
                jQuery('#all_packages_equal').prop('checked', true);

                jQuery('.container-bulto').each(function () {
                    if (jQuery(this)[0].id != 'containerBulto_1') {
                        jQuery('input', jQuery(this)).each(function () {
                            jQuery(this).prop('disabled', true);
                        });
                        jQuery('textarea', jQuery(this)).each(function () {
                            jQuery(this).prop('disabled', true);
                        });
                        jQuery('select', jQuery(this)).each(function () {
                            jQuery(this).prop('disabled', true);
                        });
                        jQuery('.card', this).addClass('package-off');
                    }
                });
            } else {
                jQuery('.alert-max-packages').addClass('hidden-block');
                jQuery('#all_packages_equal').prop('checked', false);
            }
        });

        // Todos los bultos iguales
        jQuery('#all_packages_equal').on('click', function () {
            if (jQuery(this).is(':checked')) {
                var pesoBulto1 = jQuery('#packageWeight_1').val();
                
                jQuery('.container-bulto').each(function () {
                    // Verificar que no sea el primer contenedor ni un contenedor de devoluciones
                    if (jQuery(this)[0].id !== 'containerBulto_1' && 
                        !jQuery(this)[0].id.includes('returns_container')) {
                        // Deshabilitar los campos
                        jQuery('input', jQuery(this)).prop('disabled', true);
                        jQuery('textarea', jQuery(this)).prop('disabled', true);
                        jQuery('select', jQuery(this)).prop('disabled', true);
                        
                        // Copiar el peso del bulto 1
                        jQuery('#' + jQuery(this)[0].id.replace('containerBulto', 'packageWeight')).val(pesoBulto1);
                        
                        jQuery('.card', this).addClass('package-off');
                    }
                });
            } else {
                jQuery('.container-bulto').each(function () {
                    if (jQuery(this)[0].id !== 'containerBulto_1' && 
                        !jQuery(this)[0].id.includes('returns_container')) {

                        jQuery('input', jQuery(this)).prop('disabled', false);
                        jQuery('textarea', jQuery(this)).prop('disabled', false);
                        jQuery('select', jQuery(this)).prop('disabled', false);
                        
                        jQuery('.card', this).removeClass('package-off');
                    }
                });
            }
        });

        /**
         * Cambio de País en bloque Destinatario
         */
        jQuery('#customer_country').on('change', function(e){
            let destination = jQuery(this).val();
            
            // $cp_source, $cp_dest, $country_source, $country_dest
            let rand = 'rand=' + new Date().getTime();
            let ajaxtrue = '&ajax=true';

            let data = {
                action: 'RequireCustom',
                ajax: true,
                token: static_token,
                action: 'RequireCustom',
                cp_source: jQuery('#order_form input[name="sender_cp"]').val(),
                cp_dest: jQuery('#order_form input[name="customer_cp"]').val(), 
                country_source: jQuery('#order_form input[name="sender_country"]').val(),
                country_dest: jQuery('#order_form select[name="customer_country"]').val(),
            };

            jQuery.ajax({
                url: AdminOrderURL + rand + ajaxtrue,
                type: 'POST',
                data: data,
                cache: false,
                processData: true,
                success: function (data) {
                    let parsed_data = JSON.parse(data);
                    if (parsed_data['require_custom']  == true) {
                        /** @todo Pendiente lógica Requiere aduanas */
                        jQuery('#customs_correos_container_shipping').removeClass('hidden-block');
                        console.log("DEST+ ",destination);
                        // Marcamos el pedido como "Requiere aduanas"
                        jQuery('#order_form input[name="require_customs_doc"]').val(1);
                    } else {
                        jQuery('#customs_correos_container_shipping').addClass('hidden-block');
                    }
                }
            });
        });

        /* FUNCIONALIDAD OFICINA */
        jQuery('#changeOffice').on('click', function (e) {
            jQuery('.change-container-office').toggle();
            jQuery('#mapOffice').hide();
        });

        jQuery('#searchOfficeButton').on('click', function (event) {
            jQuery('#office-list').find('option').remove();

            let postcode = jQuery('#input_cp_office').val();

            let data = {
                ajax: true,
                token: static_token,
                action: 'SearchOfficeByPostalCode',
                postcode: postcode,
            };
            let rand = 'rand=' + new Date().getTime();
            let ajaxtrue = '&ajax=true';

            // Datos de las oficinas del webservice de localizador de oficinas
            let offices = '';

            jQuery.ajax({
                url: FrontCheckoutAdminURL + rand + ajaxtrue,
                type: 'POST',
                data: data,
                cache: false,
                processData: true,
                success: function (data) {
                    let parsed_data = JSON.parse(data);
                    let dir_office, loc_office, cp_office, cod_office;

                    offices = parsed_data.json_retorno.soapenvBody.localizadorRespuesta.arrayOficina.item;

                    if (offices != undefined) {
                        if (offices.length > 1) {
                            offices.forEach(function (valor, indice, array) {
                                if (indice == 0) {
                                    dir_office = offices[0].direccion;
                                    loc_office = offices[0].descLocalidad;
                                    cp_office = offices[0].cp;
                                    cod_office = offices[0].unidad;

                                    // Informamos los campos ocultos con la primera oficina cuando hacemos click con el botón Buscar (3)
                                    jQuery('#reference_code').val(cod_office);
                                    jQuery('#request_data').val(JSON.stringify(offices[0]));

                                    document.getElementById('dir-office').innerHTML = dir_office;
                                    document.getElementById('loc-office').innerHTML = loc_office;
                                    document.getElementById('cp-office').innerHTML = cp_office;
                                    document.getElementById('cod_office').value = cod_office;

                                    document.getElementById('office_address').value = dir_office;
                                    document.getElementById('office_city').value = loc_office;
                                    document.getElementById('office_cp').value = cp_office;

                                    const myLatLng = {
                                        lat: parseFloat(offices[0].latitudETRS89),
                                        lng: parseFloat(offices[0].longitudETRS89),
                                    };

                                    if (typeof google !== 'undefined') {
                                        /* const image = "http://localhost/prestashop/modules/correosoficial/views/img/marker.png"; */ // NOSONAR
                                        let marker = new google.maps.Marker({
                                            position: myLatLng,
                                            title: offices[0].nombre,
                                            /* icon: image */ // NOSONAR
                                        });
                                        marker.setMap(mapOffice);
                                        mapOffice.setCenter(myLatLng);
                                        mapOffice.setZoom(14);
                                    }
                                }

                                jQuery('#inputSelectOffices').append('<option value=' + indice + '>' + array[indice].nombre + '</option>');
                            });

                            // // Acciones cuando cambia el selector de Oficinas
                            jQuery('#inputSelectOffices').on('change', function (e) {
                                // Se consigue el raw de la oficina escogida al cambiar el selector
                                let raw = JSON.stringify(offices[jQuery(this).val()]);

                                dir_office = offices[jQuery(this).val()].direccion;
                                loc_office = offices[jQuery(this).val()].descLocalidad;
                                cp_office = offices[jQuery(this).val()].cp;
                                cod_office = offices[jQuery(this).val()].unidad;

                                // Informamos los campos ocultos cambiar el selector de Oficinas (1)
                                jQuery('#reference_code').val(cod_office);
                                jQuery('#request_data').val(raw);

                                document.getElementById('dir-office').innerHTML = dir_office;
                                document.getElementById('loc-office').innerHTML = loc_office;
                                document.getElementById('cp-office').innerHTML = cp_office;
                                document.getElementById('cod_office').value = cod_office;

                                const myLatLng = {
                                    lat: parseFloat(offices[jQuery(this).val()].latitudETRS89),
                                    lng: parseFloat(offices[jQuery(this).val()].longitudETRS89),
                                };

                                if (typeof google !== 'undefined') {
                                    /* const image = "http://localhost/prestashop/"+co_path_to_module+"/views/img/marker.png"; */ // NOSONAR
                                    let marker = new google.maps.Marker({
                                        position: myLatLng,
                                        title: offices[jQuery(this).val()].nombre,
                                        //icon: image // NOSONAR
                                    });
                                    marker.setMap(mapOffice);
                                    mapOffice.setCenter(myLatLng);
                                    mapOffice.setZoom(14);
                                }
                            });

                            jQuery('#inputSelectOffices').show();
                            jQuery('#office-list').show();
                            jQuery('#no_offices_zip_message').addClass('hidden-block');
                        } else {
                            dir_office = offices.direccion;
                            loc_office = offices.descLocalidad;
                            cp_office = offices.cp;
                            cod_office = offices.unidad;

                            // Informamos los campos ocultos botón Buscar y solo hay una oficina (4)
                            jQuery('#reference_code').val(cod_office);
                            jQuery('#request_data').val(JSON.stringify(offices));

                            document.getElementById('dir-office').innerHTML = dir_office;
                            document.getElementById('loc-office').innerHTML = loc_office;
                            document.getElementById('cp-office').innerHTML = cp_office;
                            document.getElementById('cod_office').value = cod_office;

                            document.getElementById('office_address').value = dir_office;
                            document.getElementById('office_city').value = loc_office;
                            document.getElementById('office_cp').value = cp_office;

                            jQuery('#inputSelectOffices').append('<option value=0>' + offices.nombre + '</option>');

                            const myLatLng = {
                                lat: parseFloat(offices.latitudETRS89),
                                lng: parseFloat(offices.longitudETRS89),
                            };

                            if (typeof google !== 'undefined') {
                                /* const image = "http://localhost/prestashop/"+co_path_to_module+"/views/img/marker.png"; */ // NOSONAR
                                let marker = new google.maps.Marker({
                                    position: myLatLng,
                                    title: offices.nombre,
                                    //icon: image
                                });
                                marker.setMap(mapOffice);
                                mapOffice.setCenter(myLatLng);
                                mapOffice.setZoom(14);
                            }

                            jQuery('#inputSelectOffices').show();
                            jQuery('#office-list').show();
                        }

                        jQuery('.map-info-office').show();
                        jQuery('#mapOffice').show();
                        jQuery('#no_offices_zip_message').addClass('hidden-block');

                        jQuery('#selectOfficeButton').on('click', function (e) {
                            let offices_array;
                            jQuery('.change-container-office').hide();
                            document.getElementById('office_address').value = dir_office;
                            document.getElementById('office_city').value = loc_office;
                            document.getElementById('office_cp').value = cp_office;
                            document.getElementById('cod_office').value = cod_office;

                            // Informamos los campos ocultos cuando hacemos click con el botón Seleccionar Oficina (2)
                            jQuery('#reference_code').val(cod_office);

                            let officeSelectorContent = jQuery('#inputSelectOffices');

                            // Comprobamos si el selector tiene uno o mas options.
                            if(officeSelectorContent.find('option').length > 1) {
                                offices_array = Object.values(offices);
                            } else {
                                offices_array = [offices]; 
                            }

                            if (offices_array.length == 1) { // Si ha devuelto solo una oficina
                                jQuery('#request_data').val(JSON.stringify(offices));
                            } else { // Si ha devuelvo varias oficinas
                                jQuery('#request_data').val(JSON.stringify(offices[jQuery('#inputSelectOffices').val()]));
                            }
                            
                        });
                    } else {
                        jQuery('.map-info-office').hide();
                        jQuery('#mapOffice').hide();
                        jQuery('#inputSelectOffices').hide();
                        jQuery('#office-list').hide();
                        document.getElementById('office_address').value = '';
                        document.getElementById('office_city').value = '';
                        document.getElementById('office_cp').value = '';
                        document.getElementById('cod_office').value = '';
                        jQuery('#no_offices_zip_message').removeClass('hidden-block');
                    }
                },
            });
            event.preventDefault();
        });

        /* FUNCIONALIDAD CITYPAQ */
        jQuery('#changeCityPaq').on('click', function (e) {
            jQuery('.change-container-citypaq').toggle();
            jQuery('#mapCityPaq').hide();
        });

        jQuery('#searchCityPaqButton').on('click', function (event) {
            jQuery('#citypaq-list').find('option').remove();

            let postcode = jQuery('#input_cp_citypaq').val();

            let data = {
                ajax: true,
                token: static_token,
                action: 'SearchCityPaqByPostalCode',
                postcode: postcode,
            };
            let rand = 'rand=' + new Date().getTime();
            let ajaxtrue = '&ajax=true';

            // Datos de las oficinas del webservice de localizador de oficinas
            let citypaqs = '';

            jQuery.ajax({
                url: FrontCheckoutAdminURL + rand + ajaxtrue,
                type: 'POST',
                data: data,
                cache: false,
                processData: true,
                success: function (data) {
                    let parsed_data = JSON.parse(data);
                    let dir_citypaq, loc_citypaq, cp_citypaq, cod_homepaq;

                    citypaqs = parsed_data.json_retorno.soapenvBody.homePaqRespuesta1.listaHomePaq.homePaq;

                    if (citypaqs != undefined) {
                        if (citypaqs.length > 1) {
                            citypaqs.forEach(function (valor, indice, array) {
                                jQuery('#inputSelectCityPaqs').append('<option value=' + indice + '>' + array[indice].alias + '</option>');

                                if (indice == 0) {
                                    dir_citypaq = citypaqs[0].des_via + ' ' + citypaqs[0].direccion + ' ' + citypaqs[0].numero;
                                    loc_citypaq = citypaqs[0].desc_localidad;
                                    cp_citypaq = citypaqs[0].cod_postal;
                                    cod_homepaq = citypaqs[0].cod_homepaq;

                                    // Informamos los campos ocultos con el primer CityPaq cuando hacemos click con el botón Buscar (3)
                                    jQuery('#reference_code').val(cod_homepaq);
                                    jQuery('#request_data').val(JSON.stringify(citypaqs[0]));

                                    document.getElementById('dir-citypaq').innerHTML = dir_citypaq;
                                    document.getElementById('loc-citypaq').innerHTML = loc_citypaq;
                                    document.getElementById('cp-citypaq').innerHTML = cp_citypaq;
                                    document.getElementById('cod_homepaq').value = cod_homepaq;

                                    document.getElementById('citypaq_address').value = dir_citypaq;
                                    document.getElementById('citypaq_city').value = loc_citypaq;
                                    document.getElementById('citypaq_cp').value = cp_citypaq;
                                    document.getElementById('cod_homepaq').value = cod_homepaq;

                                    const myLatLng = {
                                        lat: parseFloat(citypaqs[0].latitudETRS89),
                                        lng: parseFloat(citypaqs[0].longitudETRS89),
                                    };

                                    if (typeof google !== 'undefined') {
                                        /* const image = "http://localhost/prestashop/modules/correosoficial/views/img/marker.png"; */ // NOSONAR
                                        let marker = new google.maps.Marker({
                                            position: myLatLng,
                                            title: citypaqs[0].alias,
                                            //icon: image
                                        });
                                        marker.setMap(mapCityPaq);
                                        mapCityPaq.setCenter(myLatLng);
                                        mapCityPaq.setZoom(14);
                                    }
                                }
                            });

                            // Acciones cuando cambia el selector de CityPaq
                            jQuery('#inputSelectCityPaqs').on('change', function (e) {
                                // Se consigue el raw de la oficina escogida al cambiar el selector
                                let raw = JSON.stringify(citypaqs[jQuery(this).val()]);

                                dir_citypaq = citypaqs[jQuery(this).val()].des_via + ' ' + citypaqs[jQuery(this).val()].direccion + ' ' + citypaqs[jQuery(this).val()].numero;
                                loc_citypaq = citypaqs[jQuery(this).val()].desc_localidad;
                                cp_citypaq = citypaqs[jQuery(this).val()].cod_postal;
                                cod_homepaq = citypaqs[jQuery(this).val()].cod_homepaq;

                                // Informamos los campos ocultos cambiar el selector de CityPaqs (1)
                                jQuery('#reference_code').val(cod_homepaq);
                                jQuery('#request_data').val(raw);

                                document.getElementById('dir-citypaq').innerHTML = dir_citypaq;
                                document.getElementById('loc-citypaq').innerHTML = loc_citypaq;
                                document.getElementById('cp-citypaq').innerHTML = cp_citypaq;
                                document.getElementById('cod_homepaq').value = cod_homepaq;

                                const myLatLng = {
                                    lat: parseFloat(citypaqs[jQuery(this).val()].latitudETRS89),
                                    lng: parseFloat(citypaqs[jQuery(this).val()].longitudETRS89),
                                };

                                if (typeof google !== 'undefined') {
                                    /* const image = "http://localhost/prestashop/"+co_path_to_module+"/views/img/marker.png"; */ // NOSONAR
                                    let marker = new google.maps.Marker({
                                        position: myLatLng,
                                        title: citypaqs[jQuery(this).val()].alias,
                                        //icon: image
                                    });
                                    marker.setMap(mapCityPaq);
                                    mapCityPaq.setCenter(myLatLng);
                                    mapCityPaq.setZoom(14);
                                }
                            });

                            jQuery('#inputSelectCityPaqs').show();
                            jQuery('#citypaq-list').show();
                            jQuery('#no_citypaqs_zip_message').addClass('hidden-block');
                        } else {
                            dir_citypaq = citypaqs.des_via + ' ' + citypaqs.direccion + ' ' + citypaqs.numero;
                            loc_citypaq = citypaqs.desc_localidad;
                            cp_citypaq = citypaqs.cod_postal;
                            cod_homepaq = citypaqs.cod_homepaq;

                            // Informamos los campos ocultos botón Buscar y solo hay una CityPaq (4)
                            jQuery('#reference_code').val(cod_homepaq);
                            jQuery('#request_data').val(JSON.stringify(citypaqs));

                            document.getElementById('dir-citypaq').innerHTML = dir_citypaq;
                            document.getElementById('loc-citypaq').innerHTML = loc_citypaq;
                            document.getElementById('cp-citypaq').innerHTML = cp_citypaq;
                            document.getElementById('cod_homepaq').value = cod_homepaq;

                            document.getElementById('citypaq_address').value = dir_citypaq;
                            document.getElementById('citypaq_city').value = loc_citypaq;
                            document.getElementById('citypaq_cp').value = cp_citypaq;

                            jQuery('#inputSelectCityPaqs').append('<option value=0>' + citypaqs.alias + '</option>');

                            const myLatLng = {
                                lat: parseFloat(citypaqs.latitudETRS89),
                                lng: parseFloat(citypaqs.longitudETRS89),
                            };

                            if (typeof google !== 'undefined') {
                                /* const image = "http://localhost/prestashop/"+co_path_to_module+"/views/img/marker.png"; */ // NOSONAR
                                let marker = new google.maps.Marker({
                                    position: myLatLng,
                                    title: citypaqs.alias,
                                    //icon: image // NOSONAR
                                });
                                marker.setMap(mapCityPaq);
                                mapCityPaq.setCenter(myLatLng);
                                mapCityPaq.setZoom(14);
                            }

                            jQuery('#inputSelectCityPaqs').show();
                            jQuery('#citypaq-list').show();
                            jQuery('#no_citypaqs_zip_message').addClass('hidden-block');
                        }

                        jQuery('.map-info-citypaq').show();
                        jQuery('#mapCityPaq').show();
                        jQuery('#no_citypaqs_zip_message').addClass('hidden-block');

                        jQuery('#selectCityPaqButton').on('click', function (e) {
                            let citypaqs_array;

                            jQuery('.change-container-citypaq').hide();
                            document.getElementById('citypaq_address').value = dir_citypaq;
                            document.getElementById('citypaq_city').value = loc_citypaq;
                            document.getElementById('citypaq_cp').value = cp_citypaq;
                            document.getElementById('cod_homepaq').value = cod_homepaq;

                            // Informamos los campos ocultos cuando hacemos click con el botón Seleccionar CityPaq (2)
                            jQuery('#reference_code').val(cod_homepaq);
                            
                            let cityPaqSelectorContent = jQuery('#inputSelectCityPaqs');

                            // Comprobamos si el selector tiene uno o mas options.
                            if(cityPaqSelectorContent.find('option').length > 1) {
                                citypaqs_array = Object.values(citypaqs);
                            } else {
                                citypaqs_array = [citypaqs]; 
                            }

                            if (citypaqs_array.length == 1) { // Si ha devuelto solo un CityPaq
                                jQuery('#request_data').val(JSON.stringify(citypaqs));
                            } else { // Si ha devuelvo varios CityPaqs
                                jQuery('#request_data').val(JSON.stringify(citypaqs[jQuery('#inputSelectCityPaqs').val()]));
                            }

                        });
                    } else {
                        jQuery('.map-info-citypaq').hide();
                        jQuery('#mapCityPaq').hide();
                        jQuery('#inputSelectCityPaqs').hide();
                        jQuery('#citypaq-list').hide();
                        document.getElementById('citypaq_address').value = '';
                        document.getElementById('citypaq_city').value = '';
                        document.getElementById('citypaq_cp').value = '';
                        document.getElementById('cod_homepaq').value = '';
                        jQuery('#no_citypaqs_zip_message').removeClass('hidden-block');
                    }
                },
            });
            event.preventDefault();
        });

        /* FUNCIONALIDAD VALORES AÑADIDOS */
        let ibanNumber;

        // Gestiona cuenta bancaria
        jQuery('#bank_acc_number').on('click', function () {
            if (jQuery('#bank_acc_number').val().substring(0, 4) === '****') {
                ibanNumber = jQuery('#bank_acc_number').val();
            }
            jQuery('#bank_acc_number').val('');
        });

        jQuery('#bank_acc_number').on('blur', function () {
            if (jQuery('#bank_acc_number').val() == '') {
                jQuery('#bank_acc_number').val(ibanNumber);
            }
        });

        // Contrareembolso
        jQuery('#contrareembolsoCheckbox').on('click', function () {
            let selected_carrier = jQuery('#input_select_carrier').find('option:selected');
            let company = selected_carrier.data('company');
            if (jQuery(this).is(':checked')) {
                if (company == 'Correos') {
                    jQuery('#cash_on_delivery_value_container').removeClass('hidden-block');
                    jQuery('#bank_acc_number_container').removeClass('hidden-block');
                } else {
                    jQuery('#cash_on_delivery_value_container').removeClass('hidden-block');
                    jQuery('#bank_acc_number_container').addClass('hidden-block');
                }
            } else {
                jQuery('#cash_on_delivery_value_container').addClass('hidden-block');
                jQuery('#bank_acc_number_container').addClass('hidden-block');
            }
        });

        // Seguro
        jQuery('#seguroCheckbox').on('click', function () {
            if (jQuery(this).is(':checked')) {
                jQuery('.seguro-info').removeClass('hidden-block');
            } else {
                jQuery('.seguro-info').addClass('hidden-block');
            }
        });

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                           PREREGISTRO DE ENVÍO EN PEDIDOS                            //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        /* Añadimos una nueva regla que compruebe que las dimensiones son 10x15x1 como mínimo,
        es decir, que sean mayores que 0, uno mayor que 10 y otro mayor de 15 */
        jQuery.validator.addMethod(
            'dimensionesValidadas',
            function (value, element) {
                // comprobamos que el carrier seleccionado se paq ligera o city paq, si no no validamos estos campos
                let carriers_default_dimensions = ['S0179', 'S0176', 'S0178'];
                if (!carriers_default_dimensions.includes(jQuery('#input_select_carrier').find('option:selected').val())) {
                    return true;
                }

                let container = element.closest('.container-bulto').id;
                let values = jQuery('#' + container)
                    .find('.validate-dimensions')
                    .map(function () {
                        return parseInt(jQuery(this).val());
                    })
                    .get();

                let mayorQue0 = values.every((num) => num > 0);
                let mayorQue10 = false;
                let mayorQue15 = false;

                for (let i = values.length - 1; i > -1; i--) {
                    if (values[i] >= 15 && mayorQue15 === false) {
                        mayorQue15 = true;
                        values.splice(i, 1);
                    }
                    if (values[i] >= 10 && mayorQue10 === false) {
                        mayorQue10 = true;
                        values.splice(i, 1);
                    }
                }

                return mayorQue0 && mayorQue10 && mayorQue15;
            },
            jQuery.validator.format(valuesDimensionDefault)
        );

        // Para añdir la regla de validación dinámicamente hacemos uso de esta class "validate-dimensions"
        jQuery.validator.addClassRules('validate-dimensions', { dimensionesValidadas: true });

        // Preregistro de envío
        jQuery('#order_form').validate({
            onkeyup: function (element) {
                jQuery(element).valid();
            },

            rules: {
                // DESTINATARIO
                customer_firstname: {
                    required: function (element) {
                        return jQuery('#order_form #customer_company').val() == '';
                    },
                    maxlength: 40,
                },
                customer_lastname: {
                    required: false,
                    maxlength: 40,
                },
                customer_company: {
                    required: function (element) {
                        return jQuery('#order_form #customer_firstname').val() == '';
                    },
                    maxlength: 40,
                },
                customer_contact: {
                    required: false,
                    maxlength: 40,
                },
                customer_address: {
                    required: true,
                    maxlength: 300,
                },
                customer_city: {
                    required: true,
                    maxlength: 40,
                },
                customer_cp: {
                    required: false,
                    maxlength: 8,
                },
                // customer_phone: {
                //     required: false,
                //     number: true,
                // },
                customer_email: {
                    required: false,
                    email: true,
                    maxlength: 50,
                },
                customer_dni: {
                    required: false,
                    maxlength: 15,
                    validate_nif_cif_nie: false,
                },
                order_reference: {
                    required: false,
                    maxlength: 20,
                },
                desc_reference_1: {
                    required: false,
                    maxlength: 100,
                },
                desc_reference_2: {
                    required: false,
                    maxlength: 100,
                },
                code_at: {
                    required: false,
                    maxlength: 30,
                },
                // VALORES AÑADIDOS
                cash_on_delivery_value: {
                    required: false,
                    number: true,
                    maxlength: 6,
                },
                insurance_value: {
                    required: false,
                    number: true,
                    maxlength: 100,
                },
                bank_acc_number: {
                    required: false,
                    maxlength: 34,
                    validate_acc_iban: false,
                },
                packageWeight_1: {
                    required: true,
                    number: true,
                },
                packageWeight_2: {
                    required: true,
                    number: true,
                },
                packageWeight_3: {
                    required: true,
                    number: true,
                },
                packageWeight_4: {
                    required: true,
                    number: true,
                },
                packageWeight_5: {
                    required: true,
                    number: true,
                },
                packageWeight_6: {
                    required: true,
                    number: true,
                },
                packageWeight_7: {
                    required: true,
                    number: true,
                },
                packageWeight_8: {
                    required: true,
                    number: true,
                },
                packageWeight_9: {
                    required: true,
                    number: true,
                },
                packageWeight_10: {
                    required: true,
                    number: true,
                },
                PickupDateRegister: {
                    required: function (element) {
                        const checkDateRegister = jQuery('#inputCheckSavePickup');
                        return checkDateRegister.checked;
                    },
                    date: true,
                },
            },
            messages: {
                // DESTINATARIO
                customer_firstname: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 40 ' + characters,
                },
                customer_lastname: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 40 ' + characters,
                },
                customer_company: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 40 ' + characters,
                },
                customer_contact: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 40 ' + characters,
                },
                customer_address: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 300 ' + characters,
                },
                customer_city: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 40 ' + characters,
                },
                customer_cp: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 8 ' + characters,
                },
                // customer_phone: {
                //     required: requiredCustomMessage,
                //     number: invalidNumber,
                //     maxlength: maxLengthMessage + ' 9 ' + characters,
                // },
                customer_email: {
                    required: requiredCustomMessage,
                    email: invalidEmail,
                    maxlength: maxLengthMessage + ' 50 ' + characters,
                },
                customer_dni: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 15 ' + characters,
                    validate_nif_cif_nie: wrongDniCif,
                },
                order_reference: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 20 ' + characters,
                },
                desc_reference_1: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 100 ' + characters,
                },
                desc_reference_2: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 100 ' + characters,
                },
                code_at: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 30 ' + characters,
                },
                // VALORES AÑADIDOS
                cash_on_delivery_value: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                    maxlength: maxLengthMessage + ' 6 ' + characters,
                },
                insurance_value: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                    maxlength: maxLengthMessage + ' 100 ' + characters,
                },
                bank_acc_number: {
                    required: requiredCustomMessage,
                    maxlength: maxLengthMessage + ' 34 ' + characters,
                    validate_acc_iban: wrongACCAndIBAN,
                },
                packageWeight_1: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
                packageWeight_2: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
                packageWeight_3: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
                packageWeight_4: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
                packageWeight_5: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
                packageWeight_6: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
                packageWeight_7: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
                packageWeight_8: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
                packageWeight_9: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
                packageWeight_10: {
                    required: requiredCustomMessage,
                    number: invalidNumber,
                },
            },
            // Añadimos los grupos para que solo aparezca un mensaje por bloque de inputs
            groups: {
                valuesDimensionDefault1: 'packageLarge_1 packageWidth_1 packageHeight_1',
                valuesDimensionDefault2: 'packageLarge_2 packageWidth_2 packageHeight_2',
                valuesDimensionDefault3: 'packageLarge_3 packageWidth_3 packageHeight_3',
                valuesDimensionDefault4: 'packageLarge_4 packageWidth_4 packageHeight_4',
                valuesDimensionDefault5: 'packageLarge_5 packageWidth_5 packageHeight_5',
                valuesDimensionDefault6: 'packageLarge_6 packageWidth_6 packageHeight_6',
                valuesDimensionDefault7: 'packageLarge_7 packageWidth_7 packageHeight_7',
                valuesDimensionDefault8: 'packageLarge_8 packageWidth_8 packageHeight_8',
                valuesDimensionDefault9: 'packageLarge_9 packageWidth_9 packageHeight_9',
                valuesDimensionDefault10: 'packageLarge_10 packageWidth_10 packageHeight_10',
            },

            submitHandler: function () {
                jQuery('#processingOrderButtonMsg').removeClass('hidden-block');
                jQuery('#generateOrderButtonMsg').addClass('hidden-block');
                jQuery('#generateOrderButton').prop('disabled', true);

                let id_order = jQuery('#id_order_hidden').val();

                let order_form = getFormData('order_form');

                Object.keys(missingData).forEach(function (key) {
                    order_form[key] = missingData[key];
                });

                let selected_carrier = jQuery('#input_select_carrier').find('option:selected');
                let company = selected_carrier.data('company');
                let delivery_mode = selected_carrier.data('carrier_type');
                let id_carrier = selected_carrier.data('id_carrier');
                let id_product = selected_carrier.data('id_product');
                let max_packages = selected_carrier.data('max_packages');
                let packages = jQuery('#correos-num-parcels').val();
                let id_sender = jQuery('#senderSelect').val();
                let added_values_cash_on_delivery = jQuery('#contrareembolsoCheckbox').is(':checked');
                let added_values_insurance = jQuery('#seguroCheckbox').is(':checked');
                let added_values_partial_delivery = jQuery('#partial_delivery').is(':checked');
                let added_values_delivery_saturday = jQuery('#delivery_saturday').is(':checked');
                let added_values_cash_on_delivery_iban = jQuery('#bank_acc_number').val();
                let added_values_cash_on_delivery_value = jQuery('#cash_on_delivery_value').val();
                let added_values_insurance_value = jQuery('#insurance_value').val();
                let all_packages_equal = jQuery('#all_packages_equal').is(':checked');
                let at_code = jQuery('#code_at').val();

                /* Recogemos los datos de todos los bultos */
                let info_bultos = {};
                document.querySelectorAll('.container-bulto-info').forEach(function (element) {
                    let reference = element.querySelector('input[name^="packageRef"]').value;
                    let weight = element.querySelector('input[name^="packageWeight"]').value == '' ? 0 : element.querySelector('input[name^="packageWeight"]').value;
                    let large = element.querySelector('input[name^="packageLarge"]').value == '' ? 0 : element.querySelector('input[name^="packageLarge"]').value;
                    let width = element.querySelector('input[name^="packageWidth"]').value == '' ? 0 : element.querySelector('input[name^="packageWidth"]').value;
                    let height = element.querySelector('input[name^="packageHeight"]').value == '' ? 0 : element.querySelector('input[name^="packageHeight"]').value;
                    let observations = element.querySelector('textarea[name^="deliveryRemarks"]').value;

                    info_bultos[element.getAttribute('id').split('_')[1]] = { reference: reference, weight: weight, large: large, width: width, height: height, observations: observations };
                });
                info_bultos = JSON.stringify(info_bultos);

                let pickupCheck = jQuery('#inputCheckSavePickup');
                let printLablPickupCheck = jQuery('#inputCheckPrintLabel');

                let needPickup = 'N';
                let PickupDateRegister = '';
                let PickupFromRegister = '';
                let PickupToRegister = '';
                let needPrintLablPickup = 'N';
                let select_input_tamanio_paquete = '';

                if (jQuery(pickupCheck).is(':checked')) {
                    needPickup = 'S';
                    PickupDateRegister = jQuery('#PickupDateRegister').val();
                    PickupFromRegister = jQuery('#PickupFromRegister').val();
                    PickupToRegister = jQuery('#PickupToRegister').val();
                    select_input_tamanio_paquete = jQuery('#input_tamanio_paquete').val();
                    if (company == 'Correos' && select_input_tamanio_paquete == 0) {
                        jQuery('#error_register strong').html('Error:  Debe seleccionar el tamaño del paquete');
                        jQuery('#error_register').removeClass('hidden-block');
                        jQuery('#processingOrderButtonMsg').addClass('hidden-block');
                        jQuery('#generateOrderButtonMsg').removeClass('hidden-block');
                        jQuery('#generateOrderButton').prop('disabled', false);
                        return;
                    }
                    let pickupDateComplete = new Date(PickupDateRegister);
                    pickupDateComplete.setHours(23);
                    pickupDateComplete.setMinutes(59);
                    pickupDateComplete.setSeconds(59);
                    if (pickupDateComplete < new Date() || (PickupFromRegister == '00:00:00' && PickupToRegister == '00:00:00')) {
                        jQuery('#error_register strong').html('Error:  Debe seleccionar fecha y rango de horas válidos en la recogida');
                        jQuery('#error_register').removeClass('hidden-block');
                        jQuery('#processingOrderButtonMsg').addClass('hidden-block');
                        jQuery('#generateOrderButtonMsg').removeClass('hidden-block');
                        jQuery('#generateOrderButton').prop('disabled', false);
                        return;
                    }
                }

                if (jQuery(printLablPickupCheck).is(':checked')) {
                    needPrintLablPickup = 'S';
                }

                order_form['AT_code'] = at_code;

                if (packages <= max_packages) {
                    let data = {
                        ajax: true,
                        token: static_token,
                        action: 'generateOrder',
                        id_order: id_order,
                        id_carrier: id_carrier,
                        id_product: id_product,
                        id_sender: id_sender,
                        company: company,
                        delivery_mode: delivery_mode,
                        order_form: order_form,
                        needPickup: needPickup,
                        pickupDateRegister: PickupDateRegister,
                        pickupFromRegister: PickupFromRegister,
                        pickupToRegister: PickupToRegister,
                        needPrintLablPickup: needPrintLablPickup,
                        packetSize: select_input_tamanio_paquete,
                        added_values_cash_on_delivery: added_values_cash_on_delivery,
                        added_values_insurance: added_values_insurance,
                        added_values_partial_delivery: added_values_partial_delivery,
                        added_values_delivery_saturday: added_values_delivery_saturday,
                        added_values_cash_on_delivery_iban: added_values_cash_on_delivery_iban,
                        added_values_cash_on_delivery_value: added_values_cash_on_delivery_value,
                        added_values_insurance_value: added_values_insurance_value,
                        info_bultos: info_bultos,
                        all_packages_equal: all_packages_equal,
                    };
                    let rand = 'rand=' + new Date().getTime();
                    let ajaxtrue = '&ajax=true';

                    jQuery.ajax({
                        url: AdminOrderURL + rand + ajaxtrue,
                        type: 'POST',
                        data: data,
                        cache: false,
                        processData: true,
                        success: function (data) {
                            if (isValidJson(data)) {
                                let parsed_data = JSON.parse(data);
                                let bultos = parsed_data.num_bultos_reg;
                                let pickupStatus = false;

                                if (parsed_data.codigoRetorno == '0') {
                                    disableForm('#container_sender');
                                    disableForm('#container_customer');
                                    disableForm('#container_shipping');
                                    disableForm('#added_values');

                                    jQuery('#order_exp_number_hidden').val(parsed_data.exp_number);

                                    let ship_codes = '';
                                    parsed_data.bultos_reg.forEach(function (item) {
                                        ship_codes = ship_codes + '<span class="order-done-info-text">' + 'Bulto ' + item.package_number + ': ' + item.shipping_number + '<span><br>';
                                        // Actualizar tracking_number al preregistrar en Prestashop
                                        showTrackingNumberInfo(item);
                                    });
                                    jQuery('.shipping-numbers-container').html(ship_codes);

                                    jQuery('#order-done-info').removeClass('hidden-block');
                                    jQuery('#input_format_etiqueta_container_reimpresion').removeClass('hidden-block');
                                    jQuery('.cancel-container').removeClass('hidden-block');
                                    jQuery('#cancelOrderButton').removeClass('hidden-block');
                                    jQuery('.send-container').addClass('hidden-block');

                                    if (company == 'Correos') {
                                        jQuery('#correos-options-pickup-container').removeClass('hidden-block');
                                        jQuery('#general-pickup-container').removeClass('hidden-block');
                                    } else {
                                        jQuery('#correos-options-pickup-container').addClass('hidden-block');
                                    }

                                    jQuery('#error_register').addClass('hidden-block');
                                    jQuery('#success_register').addClass('hidden-block');

                                    managePrintLabel(bultos);

                                    setDatatableHistory();

                                    jQuery('#save-pickup-container').addClass('hidden-block');

                                    pickupStatus = true;
                                } else {
                                    jQuery('#generateOrderButton').prop('disabled', false);
                                    jQuery('#success_register').addClass('hidden-block');
                                    jQuery('#error_register strong').html(parsed_data.mensajeRetorno);
                                    jQuery('#error_register').removeClass('hidden-block');
                                    jQuery('#processingOrderButtonMsg').removeClass('hidden-block');
                                    jQuery('#generateOrderButtonMsg').removeClass('hidden-block');
                                }

                                if (needPickup == 'S' && parsed_data.codigoRetorno == '0' && pickupStatus) {
                                    pickupDone = true;
                                }

                                if (!pickupDone && parsed_data.codigoRetorno == '0') {
                                    if (company === 'Correos') {
                                        jQuery('#processingOrderButtonMsg').addClass('hidden-block');
                                        jQuery('#generateOrderButtonMsg').removeClass('hidden-block');
                                        jQuery('#data-pickup-container').addClass('hidden-block');
                                        jQuery('#input_grabar_recogida_container').addClass('hidden-block');
                                        jQuery('#save-pickup-container').removeClass('hidden-block');
                                    } else if (company === 'CEX') {
                                        jQuery('#masive_pickup_container').addClass('hidden-block');
                                        jQuery('#inputCheckSavePickup').addClass('hidden-block');
                                        jQuery('#save-pickup-container').addClass('hidden-block');
                                        jQuery('#processingOrderButtonMsg').addClass('hidden-block');
                                        jQuery('#generateOrderButtonMsg').removeClass('hidden-block');
                                        jQuery('#data-pickup-container').addClass('hidden-block');
                                        jQuery('#input_grabar_recogida_container').addClass('hidden-block');
                                    }
                                    // Si la recogida se ha hecho correctamente y no ha devuelto ningún error
                                } else if (pickupDone === true && parsed_data.codigoRetorno == '0') {
                                    location.reload();
                                } else {
                                    jQuery('#data-pickup-container').removeClass('hidden-block');
                                    jQuery('#processingOrderButtonMsg').addClass('hidden-block');
                                    jQuery('#generateOrderButtonMsg').removeClass('hidden-block');
                                }

                                changeOrderStatusFromSelector(parsed_data.changeStatus);

                                if (needPickup == 'S' && parsed_data.codigoRetorno == 1111) {
                                    setTimeout(function () {
                                        location.reload();
                                    }, 5000);
                                }
                            } else {
                                console.error('[DEBUG MODE ON] Received data is not valid JSON:', data);
                                jQuery('#success_register').addClass('hidden-block');
                                jQuery('#error_register strong').html('[DEBUG MODE ON] Received data is not valid JSON');
                                jQuery('#error_register').removeClass('hidden-block');
                                jQuery('#processingOrderButtonMsg').addClass('hidden-block');
                                jQuery('#generateOrderButtonMsg').removeClass('hidden-block');
                                jQuery('#generateOrderButton').prop('disabled', false);                            
                            }
                        },
                    });
                } else if (id_carrier == 0) {
                    jQuery('#error_register strong').html('Error:  Seleccione transportista antes de generar el envío');
                    jQuery('#error_register').removeClass('hidden-block');
                    jQuery('#processingOrderButtonMsg').addClass('hidden-block');
                    jQuery('#generateOrderButtonMsg').removeClass('hidden-block');
                    jQuery('#input_select_carrier').addClass('error');
                    jQuery('#generateOrderButton').prop('disabled', false);
                } else {
                    jQuery('#success_register').hide();
                    jQuery('#error_register strong').html('Error bultos: El transportista seleccionado no permite envíos de varios bultos');
                    jQuery('#error_register').removeClass('hidden-block');
                    jQuery('#processingOrderButtonMsg').addClass('hidden-block');
                    jQuery('#generateOrderButtonMsg').removeClass('hidden-block');
                    jQuery('#generateOrderButton').prop('disabled', false);
                }
            },
        });

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                       IMPRIMIR ETIQUETA DE ENVÍO PREREGISTRADO                       //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        jQuery('#ReimprimirEtiquetasButton').on('click', function (event) {
            let exp_number = jQuery('#order_exp_number_hidden').val();
            let selectedTipoEtiquetaReimpresion = jQuery('#input_tipo_etiqueta_reimpresion').val();
            let selectedFormatEtiquetaReimpresion = jQuery('#input_format_etiqueta_reimpresion').val();
            let selectedPosicionEtiquetaReimpresion = jQuery('#input_pos_etiqueta_reimpresion').val();
            jQuery('#processingPrintLabelButtonMsg').removeClass('hidden-block');
            jQuery('#PrintLabelMessageButton').addClass('hidden-block');

            let id_order = jQuery('#id_order_hidden').val();
            let selected_carrier = jQuery('#input_select_carrier').find('option:selected');
            let company = selected_carrier.data('company');

            let data = {
                ajax: true,
                token: static_token,
                action: 'printLabel',
                exp_number: exp_number,
                selectedTipoEtiquetaReimpresion: selectedTipoEtiquetaReimpresion,
                selectedFormatEtiquetaReimpresion: selectedFormatEtiquetaReimpresion,
                selectedPosicionEtiquetaReimpresion: selectedPosicionEtiquetaReimpresion,
                id_order: id_order,
                company: company,
            };
            let rand = 'rand=' + new Date().getTime();
            let ajaxtrue = '&ajax=true';

            if(company == 'Correos' && selectedFormatEtiquetaReimpresion == '1' ) {
                jQuery('#processingPrintLabelButtonMsg').addClass('hidden-block');
                jQuery('#PrintLabelMessageButton').removeClass('hidden-block');
                showWrongLabelFormat();
                return;
            }

            jQuery.ajax({
                url: AdminOrderURL + rand + ajaxtrue,
                type: 'POST',
                data: data,
                cache: false,
                processData: true,
                success: function (data) {
                    let parsed_data = JSON.parse(data);

                    if (parsed_data.codigoRetorno == '403' || parsed_data.status_code == '404') {
                        jQuery('#success_register').addClass('hidden-block');
                        jQuery('#error_register strong').html(parsed_data.mensajeRetorno);
                        jQuery('#error_register').removeClass('hidden-block');
                    } else {
                        let hiddenIFrameID = 'hiddenDownloader';
                        let iframe = document.createElement('iframe');
                        iframe.id = hiddenIFrameID;
                        iframe.style.display = 'none';
                        document.body.appendChild(iframe);
                        iframe.src = co_path_to_module + '/descarga_etiqueta.php?filename=' + parsed_data + '&path=pdftmp';

                        //Borra archivos temporales
                        let data = {
                            ajax: true,
                            token: static_token,
                            action: 'deleteFiles',
                        };

                        setTimeout(function () {
                            jQuery.ajax({
                                url: AdminOrderURL + rand + ajaxtrue,
                                type: 'POST',
                                data: data,
                                cache: false,
                                processData: true,
                            });
                        }, 5000);
                    }
                    jQuery('#processingPrintLabelButtonMsg').addClass('hidden-block');
                    jQuery('#PrintLabelMessageButton').removeClass('hidden-block');
                },
            });
            event.preventDefault();
        });

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                           IMPRIMIR DOCS ADUANA PREREGISTRO                           //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        jQuery('#ImprimirCN23Button').on('click', function (event) {
            handleButtonClick('CN23');
        });

        jQuery('#ImprimirDUAButton').on('click', function (event) {
            handleButtonClick('DUA');
        });

        jQuery('#ImprimirDDPButton').on('click', function (event) {
            handleButtonClick('DDP');
        });

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                         CANCELACION DE PREREGISTRO DE ENVIO                          //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//
        let promiseModal;
        let revolvePromise;
        
        function showModalForOfficeAndCityPaq() {
            promiseModal = new Promise((resolve, reject) => {
                revolvePromise = resolve;
                let confirmationTitle = atention;
                let description = messageForCancelOfficeAndCityPaq;
                $('#myModalTitle').html(confirmationTitle);
                $('#myModalDescription p').html(description);
                $('#myModalActionButton').html(cancelOrderStr);
                $('#myModalCancelButton').html(cancelStr);
                $('#myModalCancelButton').removeAttr('disabled').show();
                $('#myModalActionButton').removeAttr('disabled').show();
                $('#myModalActionButton').on('click', function() {
                    revolvePromise(true);
                });
                $('#myModal').modal('show');
            });
        
            return promiseModal;
        }

        jQuery('#cancelOrderButton').on('click', function (event) {
            let oficinaOrCityPaq = false;
            let selectedValue = $('#input_select_carrier').val();

            if (selectedValue === 'S0176' || selectedValue === 'S0178' || selectedValue === 'S0236' || selectedValue === 'S0133' || selectedValue === '44') {
                oficinaOrCityPaq = true;
            }

            jQuery('#processingCancelOrderButtonMsg').removeClass('hidden-block');
            jQuery('#cancelOrderButtonMsg').addClass('hidden-block');
            // Eliminar tracking_number al cancelar un envío en Prestashop
            removeTrackingNumberInfo();

            let pickup_number = jQuery('#pickup_code_hidden').val();
            let selected_carrier = jQuery('#input_select_carrier').find('option:selected');
            let company = selected_carrier.data('company');

            if (company == 'CEX' || (company == 'Correos' && (pickup_number == '' || !pickup_number))) {

                if (oficinaOrCityPaq) {
                    showModalForOfficeAndCityPaq().then(() => {
                        cancelOrder();
                        
                    });
                } else {
                    cancelOrder();
                }
            } else {
                jQuery('#success_register').addClass('hidden-block');
                jQuery('#error_register strong').html('El envío tiene una recogida grabada. Para cancelar el envío, es necesario cancelar la recogida');
                jQuery('#error_register').removeClass('hidden-block');
                jQuery('#processingCancelOrderButtonMsg').addClass('hidden-block');
                jQuery('#cancelOrderButtonMsg').removeClass('hidden-block');
            }

            event.preventDefault();
        });


        function cancelOrder() {
            let id_order = jQuery('#id_order_hidden').val();
            let lang = jQuery('#customer_country').val();
            let expedition_number = jQuery('#order_exp_number_hidden').val();
            let selected_carrier = jQuery('#input_select_carrier').find('option:selected');
            let id_carrier = selected_carrier.data('id_carrier');
            let company = selected_carrier.data('company');
            let id_sender = jQuery('#senderSelect').val();

            let data = {
                ajax: true,
                action: 'cancelOrder',
                id_order: id_order,
                id_carrier: id_carrier,
                company: company,
                lang: lang,
                expedition_number: expedition_number,
                id_sender: id_sender,
            };
            let rand = 'rand=' + new Date().getTime();
            let ajaxtrue = '&ajax=true';

            jQuery.ajax({
                url: AdminOrderURL + rand + ajaxtrue,
                type: 'POST',
                data: data,
                cache: false,
                processData: true,
                success: function (data) {
                    let parsed_data = JSON.parse(data);
                    if (parsed_data.codigoRetorno == '0' && parsed_data.status_code == 200) {
                        jQuery('#generateOrderButton').prop('disabled', false);
                        enableForm('#container_customer');
                        enableForm('#container_shipping');
                        enableForm('#added_values');

                        jQuery('#senderSelect').attr('disabled', false);
                        jQuery('#client_code').attr('disabled', true);

                        jQuery('#order-done-info').addClass('hidden-block');

                        jQuery('.cancel-container').addClass('hidden-block');
                        jQuery('.send-container').removeClass('hidden-block');

                        jQuery('#save-pickup-container').addClass('hidden-block');
                        jQuery('#data-pickup-container').addClass('hidden-block');

                        jQuery('#success_register strong').html(parsed_data.mensajeRetorno);
                        jQuery('#success_register').removeClass('hidden-block');
                        jQuery('#error_register').addClass('hidden-block');

                        jQuery('#input_grabar_recogida_container').removeClass('hidden-block');
                        jQuery('#inputCheckSavePickup').removeClass('hidden-block');

                        if (company == 'CEX') {
                            jQuery('#inputCheckSavePickup').prop('checked', true);
                            jQuery('#masive_pickup_container').removeClass('hidden-block');
                        }

                        cleanStatusDatatable();
                        changeOrderStatusFromSelector(parsed_data.changeStatus);
                    } else if (parsed_data.status_code == 401) {
                        jQuery('#success_register').addClass('hidden-block');
                        jQuery('#error_register strong').html(parsed_data.mensajeRetorno);
                        jQuery('#error_register').removeClass('hidden-block');
                    } else {
                        jQuery('#success_register').addClass('hidden-block');
                        jQuery('#error_register strong').html(parsed_data.mensajeRetorno);
                        jQuery('#error_register').removeClass('hidden-block');
                    }
                    jQuery('#processingOrderButtonMsg').addClass('hidden-block');
                    jQuery('#generateOrderButtonMsg').removeClass('hidden-block');
                    jQuery('#processingCancelOrderButtonMsg').addClass('hidden-block');
                    jQuery('#cancelOrderButtonMsg').removeClass('hidden-block');
                    $('#myModal').modal('hide');
                },
            });
        }

        $('body').on('click', '#myModalCancelButton', function (ev) {
            ev.preventDefault();
            ev.stopPropagation();
            $('#myModal').modal('hide');
            jQuery('#processingCancelOrderButtonMsg').addClass('hidden-block');
            jQuery('#cancelOrderButtonMsg').removeClass('hidden-block');
        });


        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                                   GENERAR RECOGIDA                                   //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        jQuery('#generate_pickup').on('click', function (event) {
            jQuery('#processingPickupButtonMsg').removeClass('hidden-block');
            jQuery('#pickupButtonMsg').addClass('hidden-block');
            jQuery('#generate_pickup').attr('disabled', true);

            let selected_carrier = jQuery('#input_select_carrier').find('option:selected');
            let company = selected_carrier.data('company');
            let id_carrier = selected_carrier.data('id_carrier');

            let print_label;

            if (jQuery('#print_label').is(':checked')) {
                print_label = 1;
            } else {
                print_label = 0;
            }

            let data = {
                ajax: true,
                action: 'generatePickup',
                mode_pickup: 'pickup',
                id_order: jQuery('#id_order_hidden').val(),
                bultos: jQuery('#correos-num-parcels').val(),
                expedition_number: jQuery('#order_exp_number_hidden').val(),
                order_reference: jQuery('#order_reference').val(),
                pickup_date: jQuery('#pickup_date').val(),
                sender_from_time: jQuery('#sender_from_time').val(),
                sender_to_time: jQuery('#sender_to_time').val(),
                sender_address: jQuery('#sender_address').val(),
                sender_city: jQuery('#sender_city').val(),
                sender_cp: jQuery('#sender_cp').val(),
                sender_name: jQuery('#sender_name').val(),
                sender_contact: jQuery('#sender_contact').val(),
                sender_phone: jQuery('#sender_phone').val(),
                sender_email: jQuery('#sender_email').val(),
                sender_nif_cif: jQuery('#sender_nif_cif').val(),
                sender_country: jQuery('#sender_country').val(),
                producto: selected_carrier.val(),
                package_type: jQuery('#package_type').val(),
                print_label: print_label,
                company: company,
                id_carrier: id_carrier,
                id_sender: jQuery('#senderSelect').val(),
            };

            let rand = 'rand=' + new Date().getTime();
            let ajaxtrue = '&ajax=true';

            jQuery.ajax({
                url: AdminOrderURL + rand + ajaxtrue,
                type: 'POST',
                data: data,
                cache: false,
                processData: true,
                success: function (data) {
                    let parsed_data = JSON.parse(data);
                    if (parsed_data.codigoRetorno == '0') {
                        jQuery('#pickup_code_hidden').val(parsed_data.codSolicitud);
                        location.reload();
                        return;
                    } else {
                        jQuery('#error_register strong').html(parsed_data.mensajeRetorno);
                        jQuery('#error_register').removeClass('hidden-block');
                        jQuery('#success_register').addClass('hidden-block');
                    }
                    jQuery('#processingPickupButtonMsg').addClass('hidden-block');
                    jQuery('#pickupButtonMsg').removeClass('hidden-block');
                    jQuery('#generate_pickup').attr('disabled', false);
                },
            });
            event.preventDefault();
        });

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                                  CANCELAR RECOGIDA                                   //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        jQuery('#cancel_pickup').on('click', function (event) {
            jQuery('#processingCancelPickupButtonMsg').removeClass('hidden-block');
            jQuery('#pickupCancelButtonMsg').addClass('hidden-block');

            let selected_carrier = jQuery('#input_select_carrier').find('option:selected');
            let company = selected_carrier.data('company');
            let id_carrier = selected_carrier.data('id_carrier');
            let id_sender = jQuery('#senderSelect').val();

            let data = {
                ajax: true,
                action: 'cancelPickup',
                mode_pickup: 'pickup',
                id_order: jQuery('#id_order_hidden').val(),
                codSolicitud: jQuery('#pickup_code_hidden').val(),
                company: company,
                id_carrier: id_carrier,
                id_sender: id_sender,
            };

            let rand = 'rand=' + new Date().getTime();
            let ajaxtrue = '&ajax=true';

            jQuery.ajax({
                url: AdminOrderURL + rand + ajaxtrue,
                type: 'POST',
                data: data,
                cache: false,
                processData: true,
                success: function (data) {
                    let parsed_data = JSON.parse(data);
                    if (parsed_data.codigoRetorno == '0') {
                        jQuery('#success_register strong').html(parsed_data.mensajeRetorno);
                        jQuery('#success_register').removeClass('hidden-block');
                        jQuery('#error_register').addClass('hidden-block');

                        jQuery('#pickup_code_hidden').val('');

                        jQuery('#save-pickup-container').removeClass('hidden-block');
                        jQuery('#data-pickup-container').addClass('hidden-block');

                        jQuery('#input_grabar_recogida_container').removeClass('hidden-block');
                        jQuery('#inputCheckSavePickup').removeClass('hidden-block');
                        jQuery('#input_grabar_recogida_container').addClass('hidden-block');
                    } else {
                        jQuery('#error_register strong').html(parsed_data.mensajeRetorno);
                        jQuery('#error_register').removeClass('hidden-block');
                        jQuery('#success_register').addClass('hidden-block');
                    }
                    jQuery('#processingCancelPickupButtonMsg').addClass('hidden-block');
                    jQuery('#pickupCancelButtonMsg').removeClass('hidden-block');
                },
            });
            event.preventDefault();
        });

        jQuery('#generate_return_pickup').on('click', function (event) {
            generateReturnPickup();
        });

        jQuery('#cancel_return_pickup').on('click', function (event) {
            jQuery('#processingCancelReturnPickupButtonMsg').removeClass('hidden-block');
            jQuery('#returnPickupCancelButtonMsg').addClass('hidden-block');

            let selected_carrier_return = jQuery('#input_select_carrier_return').find('option:selected');
            let company = selected_carrier_return.data('company');
            let id_carrier = 0;
            let id_sender = jQuery('#senderSelect').val();

            let data = {
                ajax: true,
                action: 'cancelPickup',
                mode_pickup: 'return',
                id_order: jQuery('#id_order_hidden').val(),
                codSolicitud: jQuery('#pickup_return_code_hidden').val(),
                company: company,
                id_carrier: id_carrier,
                id_sender: id_sender,
            };

            let rand = 'rand=' + new Date().getTime();
            let ajaxtrue = '&ajax=true';

            jQuery.ajax({
                url: AdminOrderURL + rand + ajaxtrue,
                type: 'POST',
                data: data,
                cache: false,
                processData: true,
                success: function (data) {
                    let parsed_data = JSON.parse(data);
                    if (parsed_data.codigoRetorno == '0' || parsed_data.codigoRetorno == '20') {
                        jQuery('#success_register_return strong').html(parsed_data.mensajeRetorno);
                        jQuery('#success_register_return').removeClass('hidden-block');
                        jQuery('#error_register_return').addClass('hidden-block');

                        jQuery('#pickup_return_code_hidden').val('');
                        jQuery('#save-return-pickup-container').removeClass('hidden-block');
                        jQuery('#data-return-pickup-container').addClass('hidden-block');
                    } else {
                        jQuery('#error_register_return strong').html(parsed_data.mensajeRetorno);
                        jQuery('#error_register_return').removeClass('hidden-block');
                        jQuery('#success_register_return').addClass('hidden-block');
                    }
                    jQuery('#processingCancelReturnPickupButtonMsg').addClass('hidden-block');
                    jQuery('#returnPickupCancelButtonMsg').removeClass('hidden-block');
                },
            });
            event.preventDefault();
        });

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                                 DEVOLUCION DE ENVIO                                  //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        jQuery('#generateReturnButton').on('click', function (event) {
            let selected_carrier = jQuery('#input_select_carrier_return').find('option:selected');
            let company = selected_carrier.data('company');

            let container = jQuery('#containerBultoReturn_1');
            let weightField = jQuery('#packageWeightReturn_1');
            let amountField = jQuery('#packageAmountReturn_1');
            
            if (company == 'Correos' || company == 'CEX') {
            
                if (weightField.val() == '' || (company == 'Correos' && amountField.val() == '')) {
                    weightField.addClass('error');
                    amountField.addClass('error');
            
                    // Desplazarse hacia el campo de peso y luego hacer foco
                    jQuery('html, body').animate({
                        scrollTop: container.offset().top - 100
                    }, 100, function() {
                        //weightField.focus(); // Hacer foco después de que el desplazamiento ha terminado
                    });
                } else {
                    generateReturn();
                    jQuery('#ImprimirCN23Button2')[company == 'Correos' ? 'removeClass' : 'addClass']('hidden-block');
                }
            }
        });

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                         CANCELAR RECOGIDA  //  DEVOLUCIONES                          //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        jQuery('#cancelReturnButton').on('click', function (event) {
            jQuery('#processingCancelReturnButtonMsg').removeClass('hidden-block');
            jQuery('#cancelReturnButtonMsg').addClass('hidden-block');

            let selected_carrier = jQuery('#input_select_carrier_return').find('option:selected');
            let company = selected_carrier.data('company');

            let pickup_number_return = jQuery('#pickup_return_code_hidden').val();
            if (company == 'CEX' || company == 'Correos' && (pickup_number_return == '' || !pickup_number_return)) {
                let id_order = jQuery('#id_order_hidden').val();
                let lang = jQuery('#customer_country').val();
                let id_sender = jQuery('#senderSelect').val();

                if (company == 'Correos') {
                    jQuery('.customs-correos-container-return').removeClass('hidden-block');
                } else {
                    jQuery('.customs-correos-container-return').addClass('hidden-block');
                }

                let data = {
                    ajax: true,
                    action: 'cancelReturn',
                    id_order: id_order,
                    company: company,
                    lang: lang,
                    expedition_number: '',
                    id_sender: id_sender,
                    pickup_number_return: pickup_number_return,
                };
                let rand = 'rand=' + new Date().getTime();
                let ajaxtrue = '&ajax=true';

                jQuery.ajax({
                    url: AdminOrderURL + rand + ajaxtrue,
                    type: 'POST',
                    data: data,
                    cache: false,
                    processData: true,
                    success: function (data) {
                        let parsed_data = JSON.parse(data);
                        if (parsed_data.codigoRetorno == '0') {
                            jQuery('#success_register_return strong').html(parsed_data.mensajeRetorno);
                            jQuery('#success_register_return').removeClass('hidden-block');
                            jQuery('#error_register_return').addClass('hidden-block');

                            jQuery('#generate-return-container').removeClass('hidden-block');
                            jQuery('#cancel-return-container').addClass('hidden-block');
                            jQuery('.container-bultos-return').removeClass('hidden-block');
                            jQuery('#return-done-info').addClass('hidden-block');

                            if (!require_customs_doc) {
                                jQuery('#customs_correos_container_return').addClass('hidden-block');
                            }

                            if (company !== 'CEX') {
                                jQuery('#save-return-pickup-container').addClass('hidden-block');
                            } else if(company == 'CEX') {
                                jQuery('#save-return-pickup-container').removeClass('hidden-block');
                            }

                        } else {
                            jQuery('#success_register_return').addClass('hidden-block');
                            jQuery('#error_register_return strong').html(parsed_data.mensajeRetorno[0]);
                            jQuery('#error_register_return').removeClass('hidden-block');
                        }
                        jQuery('#processingCancelReturnButtonMsg').addClass('hidden-block');
                        jQuery('#cancelReturnButtonMsg').removeClass('hidden-block');
                        jQuery('#generateReturnButton').removeClass('hidden-block');
                        jQuery('#cancelReturnButton').addClass('hidden-block');
                    },
                });
            } else {
                jQuery('#success_register_return').addClass('hidden-block');
                jQuery('#error_register_return strong').html('La devolución tiene una recogida grabada. Para cancelar la devolución, es necesario cancelar la recogida');
                jQuery('#error_register_return').removeClass('hidden-block');
                jQuery('#processingCancelReturnButtonMsg').addClass('hidden-block');
                jQuery('#cancelReturnButtonMsg').removeClass('hidden-block');
            }

            event.preventDefault();
        });

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                  ENVIAR DOCUMENTACION POR CORREO  //  DEVOLUCIONES                   //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        jQuery('#SendDocumentationByEmail').on('click', function (event) {
            let selected_carrier = jQuery('#input_select_carrier_return').find('option:selected');
            let company = selected_carrier.data('company');

            let rand = 'rand=' + new Date().getTime();
            let ajaxtrue = '&ajax=true';

            jQuery('#ProcessingSendDocumentationByEmailButton').removeClass('hidden-block');
            jQuery('#ProcessingMsgSendDocumentationByEmailButton').addClass('hidden-block');

            let data = {
                ajax: true,
                action: 'sendEmail',
                id_order: jQuery('#id_order_hidden').val(),
                pickup_date: jQuery('#pickup_date').val(),
                sender_from_time: jQuery('#sender_from_time').val(),
                sender_address: jQuery('#sender_address').val(),
                sender_city: jQuery('#sender_city').val(),
                company: company,
                customer_email: jQuery('#customer_email').val(),
                default_sender_email: jQuery('#sender_email').val(),
                customer_cp: jQuery('#customer_cp').val(),
                customer_country: jQuery('#customer_country').val(),
                sender_cp: jQuery('#sender_cp').val(),
                sender_country: jQuery('#sender_country').val(),
                return_code_1: jQuery('#hidden_return_code_1').val(),
                return_code_2: jQuery('#hidden_return_code_2').val(),
                return_code_3: jQuery('#hidden_return_code_3').val(),
                return_code_4: jQuery('#hidden_return_code_4').val(),
                return_code_5: jQuery('#hidden_return_code_5').val(),
                return_code_6: jQuery('#hidden_return_code_6').val(),
                return_code_7: jQuery('#hidden_return_code_7').val(),
                return_code_8: jQuery('#hidden_return_code_8').val(),
                return_code_9: jQuery('#hidden_return_code_9').val(),
                return_code_10: jQuery('#hidden_return_code_10').val(),
            };

            jQuery.ajax({
                url: AdminOrderURL + rand + ajaxtrue,
                type: 'POST',
                data: data,
                cache: false,
                processData: true,
                success: function (data) {
                    let parsed_data = JSON.parse(data);

                    jQuery('#ProcessingSendDocumentationByEmailButton').addClass('hidden-block');
                    jQuery('#ProcessingMsgSendDocumentationByEmailButton').removeClass('hidden-block');

                    if (parsed_data.codigoRetorno == '0') {
                        jQuery('#success_register_return_email strong').html(parsed_data.mensajeRetorno);
                        jQuery('#success_register_return_email').removeClass('hidden-block');
                        jQuery('#error_register_return_email').addClass('hidden-block');
                    } else {
                        jQuery('#error_register_return_email strong').html('Error 22020: ' + parsed_data.mensajeRetorno);
                        jQuery('#success_register_return_email').addClass('hidden-block');
                        jQuery('#error_register_return_email').removeClass('hidden-block');
                    }
                    return data;
                },
                error: function (e) {
                    let parsed_data = JSON.parse(data);
                    jQuery('#error_register_return strong').html(parsed_data.mensajeRetorno);
                },
            });
        });

        // Comprobamos el tipo seleccionado
        labelsSelectActions(jQuery('#input_tipo_etiqueta_reimpresion').val());

        // Escuchamos cambios de tipo
        jQuery('#input_tipo_etiqueta_reimpresion').on('change', function () {
            labelsSelectActions(this.value);
        });

        switch (jQuery('#input_tipo_etiqueta_reimpresion_return').val()) {
            case '0':
                jQuery('#input_pos_etiqueta_reimpresion_return').empty();
                jQuery('#input_pos_etiqueta_reimpresion_return').append('<option value="1">1</option>');
                jQuery('#input_pos_etiqueta_reimpresion_return').append('<option value="2">2</option>');
                jQuery('#input_pos_etiqueta_reimpresion_return').append('<option value="3">3</option>');
                jQuery('#input_pos_etiqueta_reimpresion_return').append('<option value="4">4</option>');
                jQuery('#input_pos_etiqueta_container_reimpresion_return').show();
                break;
            case '2':
                jQuery('#input_pos_etiqueta_container_reimpresion_return').hide();
                break;
        }

        jQuery('#input_tipo_etiqueta_reimpresion_return').on('change', function () {
            switch (this.value) {
                case '0':
                    jQuery('#input_pos_etiqueta_reimpresion_return').empty();
                    jQuery('#input_pos_etiqueta_reimpresion_return').append('<option value="1">1</option>');
                    jQuery('#input_pos_etiqueta_reimpresion_return').append('<option value="2">2</option>');
                    jQuery('#input_pos_etiqueta_reimpresion_return').append('<option value="3">3</option>');
                    jQuery('#input_pos_etiqueta_reimpresion_return').append('<option value="4">4</option>');
                    jQuery('#input_pos_etiqueta_container_reimpresion_return').show();
                    break;
                case '2':
                    jQuery('#input_pos_etiqueta_container_reimpresion_return').hide();
                    break;
                default:
                    break;
            }
        });

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                        IMPRIMIR ETIQUETAS   //   DEVOLUCIONES                        //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        jQuery('#ReimprimirEtiquetasDevolucionButton').on('click', function (event) {
            let id_order = jQuery('#id_order_hidden').val();
            let selectedTipoEtiquetaReimpresionReturn = jQuery('#input_tipo_etiqueta_reimpresion_return').val();
            let selectedPosicionEtiquetaReimpresionReturn = jQuery('#input_pos_etiqueta_reimpresion_return').val();
            let selected_carrier = jQuery('#input_select_carrier_return').find('option:selected');
            let company = selected_carrier.data('company');
            let exp_number = jQuery('#order_exp_number_hidden').val();

            jQuery('#ProcessingMsgEtiquetasDevolucionButton').addClass('hidden-block');
            jQuery('#ProcessingReimprimirEtiquetasDevolucionButton').removeClass('hidden-block');

            let data = {
                ajax: true,
                token: static_token,
                action: 'printLabelReturn',
                id_order: id_order,
                selectedTipoEtiquetaReimpresionReturn: selectedTipoEtiquetaReimpresionReturn,
                selectedPosicionEtiquetaReimpresionReturn: selectedPosicionEtiquetaReimpresionReturn,
                company: company,
                exp_number: exp_number,
            };
            let rand = 'rand=' + new Date().getTime();
            let ajaxtrue = '&ajax=true';

            jQuery.ajax({
                url: AdminOrderURL + rand + ajaxtrue,
                type: 'POST',
                data: data,
                cache: false,
                processData: true,
                success: function (data) {
                    let parsed_data = JSON.parse(data);

                    if (parsed_data.codigoRetorno == '403' || parsed_data.status_code == '404') {
                        jQuery('#success_register_return').addClass('hidden-block');
                        jQuery('#error_register_return strong').html(parsed_data.mensajeRetorno);
                        jQuery('#error_register_return').removeClass('hidden-block');
                    } else {
                        let hiddenIFrameID = 'hiddenDownloader';
                        let iframe = document.createElement('iframe');
                        iframe.id = hiddenIFrameID;
                        iframe.style.display = 'none';
                        document.body.appendChild(iframe);
                        iframe.src = co_path_to_module + '/descarga_etiqueta.php?filename=' + parsed_data + '&path=pdftmp';

                        //Borra archivos temporales
                        let data = {
                            ajax: true,
                            token: static_token,
                            action: 'deleteFiles',
                        };

                        setTimeout(function () {
                            jQuery.ajax({
                                url: AdminOrderURL + rand + ajaxtrue,
                                type: 'POST',
                                data: data,
                                cache: false,
                                processData: true,
                            });
                        }, 5000);
                    }
                    jQuery('#ProcessingMsgEtiquetasDevolucionButton').removeClass('hidden-block');
                    jQuery('#ProcessingReimprimirEtiquetasDevolucionButton').addClass('hidden-block');
                },
            });
            event.preventDefault();
        });

        //--------------------------------------------------------------------------------------//
        //                                                                                      //
        //                          GENERAR DOC ADUANERA DEVOLUCIONES                           //
        //                                                                                      //
        //--------------------------------------------------------------------------------------//

        jQuery('.PrintGestionAduaneraLabels2').on('click', function (event) {
            let exp_number = jQuery('#id_order_hidden').val();
            let sender_name = jQuery('#sender_name').val();
            let sender_country = jQuery('#sender_country').val();

            jQuery('#ProcessingImprimirCN23Button2').removeClass('hidden-block');
            jQuery('#ProcessingMsgImprimirCN23Button2').addClass('hidden-block');

            let data = {
                ajax: true,
                token: static_token,
                action: 'getCustomsDoc',
                type: 'return',
                exp_number: exp_number,
                sender_name: sender_name,
                sender_country: sender_country,
                optionButton: event.target.id,
            };
            let rand = 'rand=' + new Date().getTime();
            let ajaxtrue = '&ajax=true';

            jQuery.ajax({
                url: AdminOrderURL + rand + ajaxtrue,
                type: 'POST',
                data: data,
                cache: false,
                processData: true,
                success: function (data) {
                    let parsed_data = JSON.parse(data);
                    let files = parsed_data['files'];
                    let errors = parsed_data['errors'];

                    if (files.length > 0) {
                        let count = 0;
                        files.forEach(function (item) {
                            downloadURL(co_path_to_module + '/descarga_etiqueta.php?filename=' + item + '&path=pdftmp', count);
                        });

                        //Borra archivos temporales
                        let data = {
                            ajax: true,
                            token: static_token,
                            action: 'deleteFiles',
                        };

                        setTimeout(function () {
                            jQuery.ajax({
                                url: AdminOrderURL + rand + ajaxtrue,
                                type: 'POST',
                                data: data,
                                cache: false,
                                processData: true,
                            });
                        }, 5000);
                    }

                    if (errors.length > 0) {
                        let error_msg = '';
                        errors.forEach(function (item) {
                            error_msg = error_msg + item.error_msg + '<br>';
                        });
                        jQuery('#success_register_return').addClass('hidden-block');
                        jQuery('#error_register_return strong').html(error_msg);
                        jQuery('#error_register_return').removeClass('hidden-block');
                    }

                    jQuery('#ProcessingImprimirCN23Button2').addClass('hidden-block');
                    jQuery('#ProcessingMsgImprimirCN23Button2').removeClass('hidden-block');
                },
            });
            event.preventDefault();
        });

        /* FUNCIONALIDAD SENDERS */
        jQuery('#senderSelect').on('change', function (e) {
            let sender_id = jQuery(this).val();

            let data = {
                ajax: true,
                token: static_token,
                action: 'getSenderById',
                sender_id: sender_id,
            };
            let rand = 'rand=' + new Date().getTime();
            let ajaxtrue = '&ajax=true';

            jQuery.ajax({
                url: AdminOrderURL + rand + ajaxtrue,
                type: 'POST',
                data: data,
                cache: false,
                processData: true,
                success: function (data) {
                    let parsed_data = JSON.parse(data);
                    jQuery('#sender_name').val(parsed_data['sender_name']);
                    jQuery('#sender_contact').val(parsed_data['sender_contact']);
                    jQuery('#sender_address').val(parsed_data['sender_address']);
                    jQuery('#sender_city').val(parsed_data['sender_city']);
                    jQuery('#sender_cp').val(parsed_data['sender_cp']);
                    jQuery('#sender_phone').val(parsed_data['sender_phone']);
                    jQuery('#sender_email').val(parsed_data['sender_email']);
                    jQuery('#sender_nif_cif').val(parsed_data['sender_nif_cif']);
                    jQuery('#sender_from_time').val(parsed_data['sender_from_time']);
                    jQuery('#sender_to_time').val(parsed_data['sender_to_time']);
                    jQuery('#sender_country').val(parsed_data['sender_iso_code_pais']);
                    jQuery('#correos_code').val(parsed_data['correos_code']);
                    jQuery('#cex_code').val(parsed_data['cex_code']);

                    manageCodeAT();

                    // Comprobamos compativiliad con producto seleccionado
                    let carrierSelected = jQuery('#input_select_carrier').find('option:selected');

                    if (carrierSelected.data('company') == 'Correos' && parsed_data['correos_code'] != 0) {
                        jQuery('#client_code').val(parsed_data['correos_code']);
                    } else if (carrierSelected.data('company') == 'CEX' && parsed_data['cex_code'] != 0) {
                        jQuery('#client_code').val(parsed_data['cex_code']);
                    } else {
                        jQuery('#client_code').val('');
                        senderErrorModal();
                    }
                },
            });
        });

        /* FUNCIONALIDAD CAMBIAR CARRIER */
        jQuery('#input_select_carrier').on('change', function (e) {
            let selected = jQuery(this).find('option:selected');
            let company = selected.data('company');
            let carrier_value = selected.val();
            let carrier_type = selected.data('carrier_type');
            let max_packages = selected.data('max_packages');
            let available_carriers_default_dimensions;

            // funcionalidad dimensiones por defecto para los siguiente transportistas.
            available_carriers_default_dimensions = ['S0179', 'S0176', 'S0178'];

            let containerBultoInfo = document.querySelectorAll('.container-bulto-info');
            for (let info of containerBultoInfo) {
                let element = info;
                if (available_carriers_default_dimensions.includes(carrier_value)) {
                    element.querySelector('input[name^="packageLarge"]').value = large_by_default;
                    element.querySelector('input[name^="packageWidth"]').value = width_by_default;
                    element.querySelector('input[name^="packageHeight"]').value = height_by_default;
                } else {
                    element.querySelector('input[name^="packageLarge"]').value = '';
                    element.querySelector('input[name^="packageWidth"]').value = '';
                    element.querySelector('input[name^="packageHeight"]').value = '';
                }
            }

            // client_code según remitente
            let client_code = '';
            if (company == 'Correos' && jQuery('#correos_code').val() != 0) {
                client_code = jQuery('#correos_code').val();
            } else if (company == 'CEX' && jQuery('#cex_code').val() != 0) {
                client_code = jQuery('#cex_code').val();
            } else {
                // El remitente no tiene contrato asociado
                senderErrorModal();
            }

            jQuery('#client_code').val(client_code);

            if (bultos > max_packages) {
                jQuery('.alert-max-packages').removeClass('hidden-block');
                jQuery('#all_packages_equal').prop('disabled', true);
                jQuery('#all_packages_equal').prop('checked', true);

                jQuery('.container-bulto').each(function () {
                    if (jQuery(this)[0].id != 'containerBulto_1') {
                        jQuery('input', jQuery(this)).each(function () {
                            jQuery(this).prop('disabled', true);
                        });
                        jQuery('textarea', jQuery(this)).each(function () {
                            jQuery(this).prop('disabled', true);
                        });
                        jQuery('select', jQuery(this)).each(function () {
                            jQuery(this).prop('disabled', true);
                        });
                        jQuery('.card', this).addClass('package-off');
                    }
                });
            } else {
                jQuery('.alert-max-packages').addClass('hidden-block');
                jQuery('#all_packages_equal').prop('disabled', false);
                jQuery('#all_packages_equal').prop('checked', false);

                jQuery('.container-bulto').each(function () {
                    if (jQuery(this)[0].id != 'containerBulto_1') {
                        jQuery('input', jQuery(this)).each(function () {
                            jQuery(this).prop('disabled', false);
                        });
                        jQuery('textarea', jQuery(this)).each(function () {
                            jQuery(this).prop('disabled', false);
                        });
                        jQuery('select', jQuery(this)).each(function () {
                            jQuery(this).prop('disabled', false);
                        });
                        jQuery('.card', this).removeClass('package-off');
                    }
                });
            }

            switch (company) {
                case 'Correos':
                    switch (carrier_type) {
                        case 'office':
                            jQuery('.office-container').removeClass('hidden-block');
                            jQuery('.citypaq-container').addClass('hidden-block');
                            break;
                        case 'citypaq':
                            jQuery('.office-container').addClass('hidden-block');
                            jQuery('.citypaq-container').removeClass('hidden-block');
                            break;
                        case 'homedelivery':
                        case 'international':
                            jQuery('.office-container').addClass('hidden-block');
                            jQuery('.citypaq-container').addClass('hidden-block');
                            break;
                    }

                    setCorreosRangeDate('pickup_date');

                    if (require_customs_doc) {
                        jQuery('.customs-correos-container').removeClass('hidden-block');
                        jQuery('#customs-labels-container').removeClass('hidden-block');
                    }

                    if (bultos > 1) {
                        jQuery('#partial_delivery_container').removeClass('hidden-block');
                    } else {
                        jQuery('#partial_delivery_container').addClass('hidden-block');
                    }

                    if (jQuery('#contrareembolsoCheckbox').is(':checked')) {
                        jQuery('#cash_on_delivery_value_container').removeClass('hidden-block');
                        jQuery('#bank_acc_number_container').removeClass('hidden-block');
                    } else {
                        jQuery('#cash_on_delivery_value_container').addClass('hidden-block');
                        jQuery('#bank_acc_number_container').addClass('hidden-block');
                    }

                    break;
                case 'CEX':
                    if (carrier_type == 'office') {
                        jQuery('.office-container').removeClass('hidden-block');
                        jQuery('.citypaq-container').addClass('hidden-block');
                    } else {
                        jQuery('.office-container').addClass('hidden-block');
                        jQuery('.citypaq-container').addClass('hidden-block');
                    }

                    jQuery('.alert-more-5-labels').addClass('hidden-block');
                    jQuery('#inputCheckPrintLabel').prop('disabled', false);

                    setCEXRangeDate('pickup_date');

                    jQuery('.customs-correos-container').addClass('hidden-block');
                    jQuery('#customs-labels-container').addClass('hidden-block');

                    jQuery('#partial_delivery_container').addClass('hidden-block');

                    if (jQuery('#contrareembolsoCheckbox').is(':checked')) {
                        jQuery('#cash_on_delivery_value_container').removeClass('hidden-block');
                        jQuery('#bank_acc_number_container').addClass('hidden-block');
                    } else {
                        jQuery('#cash_on_delivery_value_container').addClass('hidden-block');
                        jQuery('#bank_acc_number_container').addClass('hidden-block');
                    }
                    break;
            }

            manageCodeAT();
            manageDeliverySaturday(company);
        });

        /* MAPAS OFICINA Y CITYPAQ */
        let mapOffice;
        let mapCityPaq;

        // comprobamos si tenemos google
        if (typeof google !== 'undefined') {
            mapOffice = new google.maps.Map(document.getElementById('mapOffice'), {
                center: { lat: 40.234013044698884, lng: -3.768710630003362 },
                zoom: 13,
            });

            mapCityPaq = new google.maps.Map(document.getElementById('mapCityPaq'), {
                center: { lat: 40.234013044698884, lng: -3.768710630003362 },
                zoom: 13,
            });
        }

        jQuery.validator.addMethod('validate_nif_cif_nie', function (value) {
            let result;

            if (jQuery('#customer_dni').val() == '') {
                return true;
            } else {
                result = validate_nif_cif_nie(value);
                return result.valid;
            }
        });

        jQuery.validator.addMethod(
            'validate_acc_iban',
            function (value) {
                if (value.substring(0, 4) === '****') {
                    return true;
                } else {
                    return validate_acc_iban(value);
                }
            },
            wrongACCAndIBAN
        ); /* Retornamos el literal traducible del settings-user-configuration.tpl */

        // Validación select de recogida y recogida de devolución
        jQuery('#pickupButtonMsg').on('click', function () {
            if (!jQuery('#package_type').val()) {
                jQuery('#package_type').addClass('error');
            }
        });

        jQuery('#returnPickupButtonMsg').on('click', function () {
            if (!jQuery('#return_package_type').val()) {
                jQuery('#return_package_type').addClass('error');
            }
        });

        /**
         * Tabs de documentación aduanera
         */
        let co_cloneNumber = 1;
        let addingDesc = true;
        let addingTarriffCode = false;

        let type = '_shipping';
        let activeTab = '';

        jQuery('#customs_correos_container_shipping').on('mouseover', function (event) {
            type = '_shipping';
            co_DescriptionCounter[co_cloneNumber] = co_DescriptionCounter_shipping;
        });
        jQuery('#customs_correos_container_return').on('mouseover', function (event) {
            type = '_return';
            co_DescriptionCounter[co_cloneNumber] = co_DescriptionCounter_return;
        });

        activeTab = getActiveTab(co_cloneNumber, type);

        if (activeTab == 'desc_tab') {
            showCustomsDesc(co_cloneNumber, '_shipping');
            showCustomsDesc(co_cloneNumber, '_return');
        } else if (activeTab == 'code_tab') {
            showCustomsCode(co_cloneNumber, '_shipping');
            showCustomsCode(co_cloneNumber, '_return');
        }

        jQuery('#customs_correos_container_shipping .nav-link, #customs_correos_container_return .nav-link').on('click', function (event) {
            event.preventDefault();
            co_cloneNumber = jQuery(this).attr('data-number');
            jQuery(this).addClass('active');

            if (jQuery(this).attr('data-type') == 'customs_desc') {
                addingDesc = true;
                addingTarriffCode = false;
                showCustomsDesc(co_cloneNumber, type);
                setCustomsDescActive(co_cloneNumber, type);
                jQuery('#customs_correos_container' + type + ' #DescriptionRadioDesc_' + co_cloneNumber).val(1);
                jQuery('#customs_correos_container' + type + ' #DescriptionRadioTariff_' + co_cloneNumber).val(0);
            } else if (jQuery(this).attr('data-type') == 'customs_code') {
                addingDesc = false;
                addingTarriffCode = true;
                showCustomsCode(co_cloneNumber, type);
                setCustomsCodeActive(co_cloneNumber, type);
                jQuery('#customs_correos_container' + type + ' #DescriptionRadioTariff_' + co_cloneNumber).val(1);
                jQuery('#customs_correos_container' + type + ' #DescriptionRadioDesc_' + co_cloneNumber).val(0);
            }
        });

        let co_AddedDescription;
        let co_DescriptionCounter = {};
        let co_DescriptionCounter_shipping = 1;
        let co_DescriptionCounter_return = 1;
        co_DescriptionCounter[co_cloneNumber] = 1;

        jQuery('#customs_correos_container_shipping .add_description, #customs_correos_container_return .add_description').on('click', function (event) {
            event.preventDefault();
            co_cloneNumber = jQuery(this).attr('data-number');
            co_AddedDescription = jQuery('#customs_correos_container' + type + ' #added_customs_description_' + co_cloneNumber);

            let customsCode = jQuery('#customs_correos_container' + type + ' #packageCustomDesc_' + co_cloneNumber).val();
            let customsDesc = document.querySelector(`#packageCustomDesc_${co_cloneNumber} option:checked`).text;

            let TariffCode = jQuery('#customs_correos_container' + type + ' #packageTariffCode_' + co_cloneNumber).val();
            let TariffDesc = jQuery('#customs_correos_container' + type + ' #packageTariffDesc_' + co_cloneNumber).val();

            let AmountElement = jQuery('#customs_correos_container' + type + ' #packageAmount_' + co_cloneNumber);
            let WeightElement = jQuery('#customs_correos_container' + type + ' #packageWeightDesc_' + co_cloneNumber);
            let UnitsElement = jQuery('#customs_correos_container' + type + ' #packageUnits_' + co_cloneNumber);

            let Amount = AmountElement.val();
            let Weight = WeightElement.val();
            let Units = UnitsElement.val();

            if (Amount == '') {
                AmountElement.addClass('error');
                return;
            } else if (Weight == '') {
                WeightElement.addClass('error');
                return;
            } else if (Units == '') {
                UnitsElement.addClass('error');
                return;
            } else {
                missingData['packageAmount_' + co_cloneNumber] = Amount;
                missingData['packageWeightDesc_' + co_cloneNumber] = Weight;
                missingData['packageUnits_' + co_cloneNumber] = Units;
                AmountElement.removeClass('error');
                WeightElement.removeClass('error');
                UnitsElement.removeClass('error');
            }

            if (co_DescriptionCounter[co_cloneNumber] <= 5) {
                jQuery('#customs_correos_container' + type + ' #add_description_' + co_cloneNumber).prop('disabled', false);

                jQuery('#customs_correos_container' + type + ' #del_description_' + co_cloneNumber).prop('disabled', false);

                if (addingDesc) {
                    co_AddedDescription.append(
                        "<input class='chip col-sm-12' disabled id='customs_desc" +
                            type +
                            '_' +
                            co_cloneNumber +
                            co_DescriptionCounter[co_cloneNumber] +
                            "'     name='customs_desc" +
                            '[' +
                            co_cloneNumber +
                            '][' +
                            co_DescriptionCounter[co_cloneNumber] +
                            "]' value='" +
                            customsCode +
                            ' • ' +
                            customsDesc +
                            ' • ' +
                            Amount +
                            ' €' +
                            ' • ' +
                            Weight +
                            ' Kg' +
                            ' • ' +
                            Units +
                            " Unid.' />"
                    );
                } else if (addingTarriffCode) {
                    co_AddedDescription.append(
                        "<input class='chip col-sm-12' disabled id='customs_desc" +
                            type +
                            '_' +
                            co_cloneNumber +
                            co_DescriptionCounter[co_cloneNumber] +
                            "' name='customs_desc" +
                            '[' +
                            co_cloneNumber +
                            '][' +
                            co_DescriptionCounter[co_cloneNumber] +
                            "]' value='" +
                            TariffCode +
                            ' • ' +
                            TariffDesc +
                            ' • ' +
                            Amount +
                            ' €' +
                            ' • ' +
                            Weight +
                            ' Kg' +
                            ' • ' +
                            Units +
                            " Unid.' />"
                    );
                }
                co_DescriptionCounter[co_cloneNumber]++;

                if (type == '_shipping') {
                    co_DescriptionCounter_shipping++;
                } else if (type == '_return') {
                    co_DescriptionCounter_return++;
                }
            }

            if (co_DescriptionCounter[co_cloneNumber] > 5) {
                jQuery('#customs_correos_container' + type + ' #add_description_' + co_cloneNumber).prop('disabled', true);
            }

            AmountElement.val('');
            WeightElement.val('');
            UnitsElement.val('');
        });

        jQuery('#customs_correos_container_shipping .del_description, #customs_correos_container_return .del_description').on('click', function (event) {
            event.preventDefault();

            if (co_DescriptionCounter[co_cloneNumber] < 1) {
                return;
            }
            co_cloneNumber = jQuery(this).attr('data-number');
            co_DescriptionCounter[co_cloneNumber]--;

            if (type == '_shipping') {
                co_DescriptionCounter_shipping--;
            } else if (type == '_return') {
                co_DescriptionCounter_return--;
            }

            jQuery('#customs_correos_container' + type + ' #customs_desc' + type + '_' + co_cloneNumber + co_DescriptionCounter[co_cloneNumber]).remove();
            jQuery('#customs_correos_container' + type + ' #customs_tariff' + type + '_' + co_cloneNumber + co_DescriptionCounter[co_cloneNumber]).remove();

            if (co_DescriptionCounter[co_cloneNumber] == 1) {
                jQuery('#customs_correos_container' + type + ' #add_description_' + co_cloneNumber).prop('disabled', false);
                jQuery('#customs_correos_container' + type + ' #del_description_' + co_cloneNumber).prop('disabled', true);
            } else if (co_DescriptionCounter[co_cloneNumber] < 6) {
                jQuery('#customs_correos_container' + type + ' #add_description_' + co_cloneNumber).prop('disabled', false);
            }
        });

        /////////////////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////        LIMPIA EL +34                  ///////////////////
        /////////////////////////////////////////////////////////////////////////////////////////
        const phoneField = jQuery('#customer_phone').val();

        let newPhoneField = phoneField.replace(/0034|0034\s|\+34|\+34\s/g, '').trim();

        jQuery('#customer_phone').val(newPhoneField);

        // Copiar contenido citypack u oficina
        jQuery('#copyCityPaqContent').on('click', function () {
            getCityPaqContent();
        });

        jQuery('#copyOfficeContent').on('click', function () {
            getOfficeContent();
        });

        /////////////////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////        FUNCIONES                  ///////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////

        function getCityPaqContent () {
            let cityPaqAddress = jQuery('#citypaq_address').val();
            let cityPaqCity = jQuery('#citypaq_city').val();
            let cityPaqCp = jQuery('#citypaq_cp').val();

            let combinedText = co_address + cityPaqAddress + '\n' + co_city + cityPaqCity + '\n' + co_cp + cityPaqCp;
            let tempTextArea = document.createElement("textarea");

            tempTextArea.value = combinedText;
            document.body.appendChild(tempTextArea);

            // Seleccionar y copiar el contenido del textarea
            tempTextArea.select();
            document.execCommand("copy");
            tempTextArea.remove();

            showNotification('#copyCityPaqContent');
        }

        function getOfficeContent () {
            let officeAddress = jQuery('#office_address').val();
            let officePaqCity = jQuery('#office_city').val();
            let officePaqCp = jQuery('#office_cp').val();

            let combinedText = co_address + officeAddress + '\n' + co_city + officePaqCity + '\n' + co_cp + officePaqCp;
            let tempTextArea = document.createElement("textarea");

            tempTextArea.value = combinedText;
            document.body.appendChild(tempTextArea);

            // Seleccionar y copiar el contenido del textarea
            tempTextArea.select();
            document.execCommand("copy");
            tempTextArea.remove();

            showNotification('#copyOfficeContent');
        }

        function showNotification(buttonSelector) {
            // Crear el cuadro de notificación
            let notification = jQuery('#contentCopied');
            let button = jQuery(buttonSelector);
            button.offset();

            notification.removeClass('hidden-block').css({
                'position': 'absolute',
                'top': '8rem',
                'left': '30rem', // Posicionar a la derecha del botón con un margen de 10px
                'color': '#664d03',
                'background-color': '#fff3cd',
                'border-color': '#d2a63c',
                'padding': '10px',
                'border-radius': '5px',
                'font-size': '14px',
                'z-index': '1000',
                'white-space': 'nowrap',
                'display': 'block',
                'box-shadow': '0px 4px 8px rgba(0, 0, 0, 0.3)',
            });
        
            setTimeout(function () {
                notification.fadeOut(500, function () {
                    notification.addClass('hidden-block');
                    notification.removeAttr('style');
                });
            }, 1500);
        }

        function labelsSelectActions(label_type) {
            let format_selected;

            switch (label_type) {
                case '0': // Adhesiva
                    jQuery('#input_pos_etiqueta_container_reimpresion').show();
                    jQuery('#input_format_etiqueta_container_reimpresion').show();

                    format_selected = jQuery('#input_format_etiqueta_reimpresion').val();
                    
                    if (format_selected == '1') {
                        // 3/A4
                        loadLabelSelectPositions('#input_pos_etiqueta_reimpresion', 3);
                    } else {
                        // Estandar y 4/A4
                        loadLabelSelectPositions('#input_pos_etiqueta_reimpresion', 4);
                    }

                    jQuery('#input_format_etiqueta_reimpresion').on('change', function () {
                        if (this.value == '1') {
                            // 3/A4
                            loadLabelSelectPositions('#input_pos_etiqueta_reimpresion', 3);
                        } else {
                            // Estandar y 4/A4
                            loadLabelSelectPositions('#input_pos_etiqueta_reimpresion', 4);
                        }
                    });

                    break;

                case '1': // Medio Folio
                    loadLabelSelectPositions('#input_pos_etiqueta_reimpresion', 2);

                    jQuery('#input_pos_etiqueta_container_reimpresion').show();

                    break;

                case '2': // Térmica
                    jQuery('#input_pos_etiqueta_container_reimpresion').hide();

                    // Reset input formato
                    jQuery('#input_format_etiqueta_container_reimpresion').hide();
                    jQuery('#input_format_etiqueta_reimpresion').val(0);

                    break;

                default:
                    break;
            }
        }

        // Funcion que nos permite rellenar dinámicamente el select de posiciones de etiquetas
        function loadLabelSelectPositions(element, positions) {
            let select_input = jQuery(element);

            select_input.empty();
            for (let i = 1; i <= positions; i++) {
                select_input.append('<option value="' + i + '">' + i + '</option>');
            }
        }

        // Funciones auxiliares

        /**
         * Selector de Pedido->Nº de envío del apartado Transportista de Prestashop.
         * Para prestashop 1.7.7: #orderShippingTabContent .table td:last
         * Para prestashop 1.7.6: .shipping_number_show
         * @param {*} item nº de envío
         */
        function showTrackingNumberInfo(item) {
            let selector = jQuery('#orderShippingTabContent .table td:last').length ? jQuery('#orderShippingTabContent .table td:last') : jQuery('.shipping_number_show');

            let selected_carrier = jQuery('#input_select_carrier').find('option:selected');
            let company = selected_carrier.data('company');

            if (platform == 'ps') {
                if (company == 'Correos') {
                    let correos_link = "<a target='_blank' href='https://www.correos.es/es/es/herramientas/localizador/envios/detalle?tracking-number=" + item.shipping_number + "'>" + item.shipping_number + '</a>';
                    selector.html(correos_link);
                } else if (company == 'CEX') {
                    let cex_link = "<a target='_blank' href='https://s.correosexpress.com/c?n=" + item.shipping_number + "'>" + item.shipping_number + '</a>';
                    selector.html(cex_link);
                }
            }
        }

        function removeTrackingNumberInfo() {
            // Celda según prestashop 1.7.7 o 1.7.6
            let selector = jQuery('#orderShippingTabContent .table td:last').length ? jQuery('#orderShippingTabContent .table td:last') : jQuery('.shipping_number_show');
            if (platform == 'ps') {
                selector.html('');
            }
        }

        // Botón seleccionar remitente desde modal
        jQuery('#errorSender-change').on('click', (e) => {
            e.preventDefault();
            jQuery('.errorSender-screen').hide();
            jQuery('#senderSelect').focus();
        });

        function senderErrorModal() {
            // Sender Name
            let error_sender_name = jQuery('.errorSender-text .error_sender_name');
            let optionSenderText = jQuery('#senderSelect option:selected').text();
            error_sender_name.text(optionSenderText);

            // Company Name
            let error_company_name = jQuery('.errorSender-text .error_company_name');
            let optionDataCompany = jQuery('#input_select_carrier option:selected').attr('data-company');
            error_company_name.text(optionDataCompany);

            // Mostramos modal
            jQuery('.errorSender-screen').show();
            const position = jQuery('.errorSender-screen').offset().top - 200;
            jQuery('html, body').animate(
                {
                    scrollTop: position,
                },
                1000
            );
        }

        function changeOrderStatusFromSelector(status) {
            if (status) {
                let selector = jQuery('#update_order_status_action_input');
                let valueToSelect = status;
                let selectionElement = selector.siblings('.select2-container').find('.select2-selection__rendered');

                selector.find('option').removeAttr('selected');
                selector.val(valueToSelect);
                selectionElement.text(selector.find('option:selected').text());
            }
        }

        // COMPROBACION VISUAL DE RECOGIDAS SEGUN CORREOS O CEX

        function setCheckTrue() {
            jQuery('#inputCheckSavePickup').prop('checked', true);
            showContent(true);
        }

        function setCheckFalse() {
            jQuery('#inputCheckSavePickup').prop('checked', false);
            showContent(false);
        }

        function showContent(isChecked) {
            selectedCompany = selectedCompany == '' ? company : selectedCompany;

            selectedCompany == 'Correos' ? inputLabel.removeClass('hidden-block') && inputPackageSize.removeClass('hidden-block') 
            : inputLabel.addClass('hidden-block') && inputPackageSize.addClass('hidden-block');

            if (isChecked) {
                container.removeClass('hidden-block');
            } else {
                container.addClass('hidden-block');
            }
        }

        function checkCorreosOrCEX(selectedCompany) {
            selectedCompany = selectedCompany == '' ? company : selectedCompany;

            let fromTime = jQuery('#sender_from_time').val();
            let toTime = jQuery('#sender_to_time').val();

            if (selectedCompany == 'Correos') {
                setCheckFalse();
            } else if (selectedCompany == 'CEX' && fromTime != toTime) {
                setCheckTrue();
            } else {
                setCheckFalse();
            }
        }

        function handleButtonClick(type) {
            let button = jQuery(`#Imprimir${type}Button`);

            button.find('.spin').removeClass('hidden-block');
            button.find('.label-message').addClass('hidden-block');

            let exp_number = jQuery('#order_exp_number_hidden').val();
            let customer_country = jQuery('#customer_country').val();
            let customer_name = jQuery('#customer_firstname').val();
            let customer_lastname = jQuery('#customer_lastname').val();

            let data = {
                ajax: true,
                token: static_token,
                action: 'getCustomsDoc',
                type: 'order',
                exp_number: exp_number,
                customer_country: customer_country,
                customer_name: customer_name,
                customer_lastname: customer_lastname,
                optionButton: `Imprimir${type}Button`,
            };
            let rand = 'rand=' + new Date().getTime();
            let ajaxtrue = '&ajax=true';

            jQuery.ajax({
                url: AdminOrderURL + rand + ajaxtrue,
                type: 'POST',
                data: data,
                cache: false,
                processData: true,
                success: function (data) {
                    let errors;
                    let parsed_data = JSON.parse(data);

                    if (parsed_data.status_code == '404') {
                        jQuery('#success_register').addClass('hidden-block');
                        jQuery('#error_register strong').html(parsed_data.mensajeRetorno);
                        jQuery('#error_register').removeClass('hidden-block');
                    } else {
                        let files = parsed_data['files'];
                        errors = parsed_data['errors'];

                        if (files.length > 0) {
                            let count = 0;
                            files.forEach(function (item) {
                                downloadURL(co_path_to_module + '/descarga_etiqueta.php?filename=' + item + '&path=pdftmp', count);
                            });

                            //Borra archivos temporales
                            let data = {
                                ajax: true,
                                token: static_token,
                                action: 'deleteFiles',
                            };

                            setTimeout(function () {
                                jQuery.ajax({
                                    url: AdminOrderURL + rand + ajaxtrue,
                                    type: 'POST',
                                    data: data,
                                    cache: false,
                                    processData: true,
                                });
                            }, 5000);
                        }
                    }

                    if (errors.length > 0) {
                        let error_msg = '';
                        errors.forEach(function (item) {
                            error_msg = error_msg + item.error_msg + '<br>';
                        });
                        jQuery('#success_register_return').hide();
                        jQuery('#error_register_return strong').html(error_msg);
                        jQuery('#error_register_return').removeClass('hidden-block');
                    }
                    button.find('.spin').addClass('hidden-block');
                    button.find('.label-message').removeClass('hidden-block');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error:', textStatus, errorThrown);
                    jQuery('#error_register').removeClass('hidden-block');
                    jQuery('#error_register strong').html('An error occurred while processing your request.');
                    button.find('.spin').addClass('hidden-block');
                    button.find('.label-message').removeClass('hidden-block');
                },
            });
        }

        function downloadURL(url, count) {
            let hiddenIFrameID = 'hiddenDownloader' + count++;
            let iframe = document.createElement('iframe');
            iframe.id = hiddenIFrameID;
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
            iframe.src = url;
        }

        /**
         * Detecta que el dato que le pasamos sea un Json válido
         */
        function isValidJson(data) {
            try {
                JSON.parse(data);
                return true;
            } catch (e) {
                return false;
            }
        }   

        function showWrongLabelFormat() {
            promiseModal = new Promise((resolve, reject) => {
                revolvePromise = resolve;
                let confirmationTitle = atention;
                let description = messageWrongLabelFormat;
                jQuery('#myModalTitle').html(confirmationTitle);
                jQuery('#myModalDescription p').html(description);
                jQuery('#myModalCancelButton').html(cancelStr);
                jQuery('#myModalActionButton').hide();

                jQuery('#myModal').modal({
                    backdrop: 'static',
                    keyboard: false,
                });

                jQuery('#myModal').modal('show');
            });

            return promiseModal;
        }    
    }
});
