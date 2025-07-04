/**
 *  Selector de Oficinas y CityPaq
 */

jQuery(document).ready(function (e) {
    let rand = 'rand=' + new Date().getTime();
    const ajaxtrue = '&ajax=true';

    const actionCityPaq = 'SearchCityPaqByPostalCode';
    const actionOffice = 'SearchOfficeByPostalCode';

    let googleMap = [];

    let carriersData = [];

    let static_token;
    if (typeof prestashop !== typeof undefined) {
        static_token = prestashop.static_token;
    } else {
        static_token = 'token';
    }

    // SABER SI HA CARGADO EL MODULO ONEPAGECHECKOUTPS
    if (jQuery('#onepagecheckoutps').length > 0) {
        let isFirstTime = false;
        
        //BOTONES PARA CHECKOUT Y SELECCION DE TRANSPORTISTAS 
        jQuery('#hide_carrier_embed').on('click', hideElements);
        jQuery('#onepagecheckoutps_step_two').on('click', function() {
            manageCarriers();
            isFirstTime = true;
        });
        
        // OCULTA EL CONTENIDO SOBRANTE DE LOS TRANSPOSTISTAS
        function hideElements() {
            setTimeout(() => {
                jQuery('.carrier-extra-content').hide();
            }, 2000);
        }
        
        // MUESTRA SOLO EL TRANSPORTISTA SELECCIONADO.
        function manageCarriers() {
            let actualCarrierSelected = jQuery('input[type="radio"]:checked');
            let hideCarrierEmbedVisible = jQuery('#hide_carrier_embed').is(':visible');
    
            if (!hideCarrierEmbedVisible) return;
    
            jQuery('.carrier-extra-content_selected').attr('id', 'carrier-extra-content');
    
            let elementoCorreosOficial = actualCarrierSelected.closest('.delivery-option').find('.carrier-extra-content .correos_oficial');
            elementoCorreosOficial.attr('id', 'carrier-extra-content_selected');
    
            elementoCorreosOficial.show();
            
            // Comprueba si es la primera vez que accede a la seleccion de transportistas
            if (!isFirstTime) return; 
    
            let radioButton = jQuery('.delivery-options-list input[type="radio"]:checked');
            radioButton.removeAttr('checked');
    
            isFirstTime = true;
        }
    }

    // EVENTO CAMBIO DE TRANSPORTISTA
    let carrierSelected = jQuery('input[type="radio"][name^="delivery_option["][name$="]"]:checked');
    let radioButtonsCarriers = jQuery('input[type="radio"][name^="delivery_option["][name$="]"]');
    radioButtonsCarriers.on('change', function () {
        carrierSelected = jQuery(this).val();
        let carrierData = carriersData[parseInt(carrierSelected)];
        if (carrierData != undefined) {
            if (carrierData.action == actionCityPaq) {
                insertCityPaq(carrierData.selected_location, carrierSelected);
            }
            if (carrierData.action == actionOffice) {
                insertOffice(carrierData.selected_location, carrierSelected);
            }
        }
    });

    // Para algunos temas que no llegan a tiempo a cargar el código postal
    setTimeout(function(){
        let carrierData = carriersData[parseInt(carrierSelected.val())];
        if (carrierData != undefined) {
            if (carrierData.action == actionCityPaq) {
                insertCityPaq(carrierData.selected_location, carrierSelected);
            }
            if (carrierData.action == actionOffice) {
                insertOffice(carrierData.selected_location, carrierSelected);
            }
        }
    }, 3000);

    // OBTENEMOS SELECTORES CARGADOS
    let citypaqCarriers = jQuery('[id^="citypaq_selector_"]');
    let officeCarriers = jQuery('[id^="office_selector_"]');

    // CITYPAQS
    citypaqCarriers.each(function (index, element) {
        // Obtenemos el value del elemento
        let carrierId = jQuery(element).val();

        // Elementos del DOM
        let currentReference = jQuery('#citypaq_reference_' + carrierId);
        let currentPostcode = jQuery('#citypaq_postcode_' + carrierId);
        let inputSearch = jQuery('#SearchCityPaqByCPInput_' + carrierId);
        let buttonSearch = jQuery('#SearchCityPaqByCpButton_' + carrierId);
        let selectCityPaqs = jQuery('#CityPaqSelect_' + carrierId);
        let scheduleAndMap = jQuery('#scheduleAndMap_' + carrierId);

        // Ocultamos horario y mapa
        scheduleAndMap.hide();

        // Obtenemos locations para los citypaq
        setLocations(actionCityPaq, currentPostcode.val(), carrierId, selectCityPaqs, currentReference.val());

        // Añadimos postcode al input de búsqueda
        inputSearch.val(currentPostcode.val());
        inputSearch.keydown(function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                buttonSearch.click();
            }
        });

        // Añadimos evento click al botón de buscar citipaq
        buttonSearch.on('click', async function () {
            // Obtenemos el valor del input
            let searchPostcode = inputSearch.val();

            let searchPostalCodeSub;
            let shippingAddressPostalCodeSub;
            if (searchPostcode) {
                searchPostalCodeSub = searchPostcode.substring(0, 2);
            }

            if (currentPostcode.val()) {
                let shippingAddressPostalCode = currentPostcode.val();
                shippingAddressPostalCodeSub = shippingAddressPostalCode.substring(0, 2);
            }

            if (searchPostalCodeSub && shippingAddressPostalCodeSub) {
                if(searchPostalCodeSub != shippingAddressPostalCodeSub) {
                    alert(pickupPointSameProvince);
                    return false;
                }  
            }


            // set de locations
            try {
                let locations = await setLocations(actionCityPaq, searchPostcode, carrierId, selectCityPaqs, null);
                let selected_location = locations[0];
                insertCityPaq(selected_location, carrierId);
                currentReference.val(selected_location.reference); // set current reference
                currentPostcode.val(searchPostcode); // set current postcode
            } catch (error) {
                alert(cityPaqPostCodeNotFound);
                inputSearch.val(currentPostcode.val()); // reset search input
            }
        });

        // Map
        if (defined_google_api_key == 1) {
            // verificar si google está cargado
            if (typeof google !== 'undefined') {
                let newGoogleMaps = new google.maps.Map(document.getElementById('GoogleMapCorreos_' + carrierId), {
                    center: { lat: 40.234013044698884, lng: -3.768710630003362 },
                    zoom: 13,
                });

                googleMap[carrierId] = {
                    map: newGoogleMaps,
                    markers: [],
                };
            }
        }
    });

    // OFICINAS
    officeCarriers.each(function (index, element) {
        // Obtenemos el value del elemento
        let carrierId = jQuery(element).val();

        // Elementos del DOM
        let currentReference = jQuery('#office_reference_' + carrierId);
        let currentPostcode = jQuery('#office_postcode_' + carrierId);
        let inputSearch = jQuery('#SearchOfficeByCPInput_' + carrierId);
        let buttonSearch = jQuery('#SearchOfficeByCpButton_' + carrierId);
        let selectOffices = jQuery('#OfficeSelect_' + carrierId);
        let scheduleAndMap = jQuery('#scheduleAndMap_' + carrierId);

        // Ocultamos horario y mapa
        scheduleAndMap.hide();

        // Obtenemos locations para las oficinas
        setLocations(actionOffice, currentPostcode.val(), carrierId, selectOffices, currentReference.val());

        // Añadimos postcode al input de búsqueda
        inputSearch.val(currentPostcode.val());
        inputSearch.keydown(function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                buttonSearch.click();
            }
        });

        // Añadimos evento click al botón de buscar oficinas
        buttonSearch.on('click', async function () {
            // Obtenemos el valor del input
            let searchPostcode = inputSearch.val();

            let searchPostalCodeSub;
            let shippingAddressPostalCodeSub;
            if (searchPostcode) {
                searchPostalCodeSub = searchPostcode.substring(0, 2);
            }

            if (currentPostcode.val()) {
                let shippingAddressPostalCode = currentPostcode.val();
                shippingAddressPostalCodeSub = shippingAddressPostalCode.substring(0, 2);
            }

            if (searchPostalCodeSub && shippingAddressPostalCodeSub) {
                if(searchPostalCodeSub != shippingAddressPostalCodeSub) {
                    alert(pickupPointSameProvince);
                    return false;
                }  
            }
            
            // set de locations
            try {
                let locations = await setLocations(actionOffice, searchPostcode, carrierId, selectOffices, null);
                let selected_location = locations[0];
                insertOffice(selected_location, carrierId);
                currentReference.val(selected_location.reference); // set current reference
                currentPostcode.val(searchPostcode); // set current postcode
            } catch (error) {
                alert(officePostCodeNotFound);
                inputSearch.val(currentPostcode.val()); // reset search input
            }
        });

        // Map
        if (defined_google_api_key == 1) {
            // verificar si google está cargado
            if (typeof google !== 'undefined') {
                let newGoogleMaps = new google.maps.Map(document.getElementById('GoogleMapCorreos_' + carrierId), {
                    center: { lat: 40.234013044698884, lng: -3.768710630003362 },
                    zoom: 13,
                });

                googleMap[carrierId] = {
                    map: newGoogleMaps,
                    markers: [],
                };
            }
        }
    });

    // CONTROL MARCADORES EN MAPA
    function setGoogleMapsMarkers(carrierId, myLatLng, title) {
        if (defined_google_api_key == 1) {
            let carrierMap = googleMap[carrierId];
            if (carrierMap != undefined) {
                let map = googleMap[carrierId].map;
                let markers = googleMap[carrierId].markers;

                // Verificar si hay marcadores existentes para este transportista y eliminamos
                if (markers.length > 0) {
                    markers.forEach(function (marker) {
                        marker.setMap(null);
                    });
                }
                let marker = new google.maps.Marker({
                    position: myLatLng,
                    title: title,
                });

                marker.setMap(map);
                markers.push(marker);

                map.setCenter(myLatLng);
                map.setZoom(14);
            }
        }
    }

    // CARGA EL SELECT DEL CARRIER PASADO
    function fillSelect(id_carrier, action, select, locations, currentReference, selectedOutput = null) {
        // Desactivamos evento change en el select
        select.off('change');

        // Eliminamos todos los options
        select.find('option').remove();

        // Si no tenemos currentReference, seleccionamos el primero
        if (!currentReference) {
            currentReference = locations[0].reference;
            if (selectedOutput != null) {
                selectedOutput.val(currentReference);
            }
        }

        // Carrier data
        updateCarrierData(
            id_carrier,
            action,
            locations.find((location) => location.reference == currentReference)
        );

        locations.forEach(function (location) {
            if (currentReference == location.reference) {
                select.append('<option value=' + location.reference + ' selected>' + location.terminal + '</option>');
            } else {
                select.append('<option value=' + location.reference + '>' + location.terminal + '</option>');
            }
        });

        // Mostramos horario, direccion y mapa
        setScheduleAndMap(id_carrier, locations, currentReference, action);

        // Tras Cargar los options, activamos el evento change
        select.on('change', function () {
            // obtenemos el valor de la option seleccionada y asignamos al hidden del carrier
            if (selectedOutput != null) {
                selectedOutput.val(jQuery(this).val());

                // Configuramos horarios
                setScheduleAndMap(id_carrier, locations, jQuery(this).val(), action);

                let selected_location = locations.find((location) => location.reference == jQuery(this).val());

                // Guardamos en BD
                if (action == actionCityPaq) {
                    insertCityPaq(selected_location, id_carrier);
                }

                if (action == actionOffice) {
                    insertOffice(selected_location, id_carrier);
                }
            }
        });
    }

    // ACIONES HORARIOS Y MAPA
    function setScheduleAndMap(id_carrier, locations, reference, action) {
        let scheduleAndMap = jQuery('#scheduleAndMap_' + id_carrier);

        // buscamos en el array de locations el que tenga el reference seleccionado
        let locationSelected = locations.find((location) => location.reference == reference);

        // Mostramos horario, direccion y mapa
        scheduleAndMap.show();

        // Actualizamos horario, dirección y mapa del carrier para CityPaq
        if (action == actionCityPaq) {
            scheduleAndMap.find('.citypaq-address-info p.address').text(locationSelected.address);
            scheduleAndMap.find('.citypaq-address-info p.city').text(locationSelected.city);
            scheduleAndMap.find('.citypaq-address-info p.cp').text(locationSelected.cp);
            scheduleAndMap.find('.citypaq-terminal-info p').text(locationSelected.terminal);
            scheduleAndMap.find('.scheduleInfo p').text(locationSelected.schedule === '1' ? openingInfo : opening24hInfo);
        }

        // Actualizamos horario, dirección y mapa del carrier para Oficinas
        if (action == actionOffice) {
            scheduleAndMap.find('.office-address-info p.address').text(locationSelected.address);
            scheduleAndMap.find('.office-address-info p.city').text(locationSelected.city);
            scheduleAndMap.find('.office-address-info p.cp').text(locationSelected.cp);
            scheduleAndMap.find('.office-address-info p.phone').text(locationSelected.phone);
            scheduleAndMap.find('.office-terminal-info p').text(locationSelected.terminal);
            scheduleAndMap.find('.scheduleInfo p.timeScheduleLV').text(locationSelected.schedule.horarioLV);
            scheduleAndMap.find('.scheduleInfo p.timeScheduleS').text(locationSelected.schedule.horarioS);
            scheduleAndMap.find('.scheduleInfo p.timeScheduleF').text(locationSelected.schedule.horarioF);
        }
        // map
        setGoogleMapsMarkers(id_carrier, { lat: locationSelected.lat, lng: locationSelected.lng }, locationSelected.terminal);
    }

    // OBTIENE Y CARGA LOCATIONS
    function setLocations(action, postcode, id_carrier, select, currentReference) {
        return new Promise(function (resolve, reject) {
            let results = new Array();
            let selectedOutput = null;

            jQuery.ajax({
                url: FrontCheckoutAdminURL + rand + ajaxtrue,
                type: 'POST',
                data: {
                    ajax: true,
                    token: static_token,
                    action: action,
                    postcode: postcode,
                    id_carrier: id_carrier,
                },
                cache: false,
                processData: true,
                success: function (data) {
                    let parsed_data = JSON.parse(data);

                    if (parsed_data.json_retorno.soapenvBody == undefined) {
                        reject(false);
                    } else {
                        // Parseamos para CityPaq
                        if (action == actionCityPaq) {
                            let homepaqs = parsed_data.json_retorno.soapenvBody.homePaqRespuesta1.listaHomePaq.homePaq;
                            selectedOutput = jQuery('#citypaq_reference_' + id_carrier);

                            if (homepaqs != undefined) {
                                // reindexamos solo 1 resultado
                                if (homepaqs.cod_homepaq != undefined) {
                                    let homepaq = homepaqs;
                                    homepaqs = [];
                                    homepaqs.push(homepaq);
                                }

                                homepaqs.forEach(function (valor, indice, array) {
                                    let address_info = array[indice].des_via + ' ' + array[indice].direccion + ' ' + array[indice].numero;
                                    let data = {
                                        reference: array[indice].cod_homepaq,
                                        address: address_info,
                                        city: array[indice].desc_localidad,
                                        cp: array[indice].cod_postal,
                                        terminal: array[indice].alias,
                                        schedule: array[indice].ind_horario,
                                        lat: parseFloat(array[indice].latitudETRS89),
                                        lng: parseFloat(array[indice].longitudETRS89),
                                        raw: array[indice],
                                    };
                                    results.push(data);
                                });
                            }
                        }

                        // Parseamos para Oficinas
                        if (action == actionOffice) {
                            let offices = parsed_data.json_retorno.soapenvBody.localizadorRespuesta.arrayOficina.item;
                            selectedOutput = jQuery('#office_reference_' + id_carrier);

                            if (offices != undefined) {
                                // reindexamos solo 1 resultado
                                if (offices.unidad != undefined) {
                                    let office = offices;
                                    offices = [];
                                    offices.push(office);
                                }

                                offices.forEach(function (valor, indice, array) {
                                    let address_info = array[indice].direccion;
                                    let data = {
                                        reference: array[indice].unidad,
                                        address: address_info,
                                        city: array[indice].descLocalidad,
                                        cp: array[indice].cp,
                                        phone: array[indice].telefono,
                                        terminal: array[indice].nombre,
                                        schedule: {
                                            horarioLV: array[indice].horarioLV,
                                            horarioS: array[indice].horarioS,
                                            horarioF: array[indice].horarioF,
                                        },
                                        lat: parseFloat(array[indice].latitudETRS89),
                                        lng: parseFloat(array[indice].longitudETRS89),
                                        raw: array[indice],
                                    };
                                    results.push(data);
                                });
                            }
                        }

                        // Si tenemos resultados
                        if (results.length > 0) {
                            fillSelect(id_carrier, action, select, results, currentReference, selectedOutput);
                            resolve(results);
                        } else {
                            reject(false);
                        }
                    }
                },
                error: function (e) {
                    reject(false);
                },
            });
        });
    }

    function updateCarrierData(id_carrier, action, selected_location) {
        carriersData[id_carrier] = {
            action: action,
            selected_location: selected_location,
        };
    }

    function insertCityPaq(selected_location, id_carrier) {
        let data = {
            ajax: true,
            token: static_token,
            action: 'insertCityPaq',
            data: selected_location.raw,
            citypaq: selected_location.reference,
        };

        // Carrier data
        updateCarrierData(id_carrier, actionCityPaq, selected_location);

        insertReferenceCode(data);
    }

    function insertOffice(selected_location, id_carrier) {
        let data = {
            ajax: true,
            token: static_token,
            action: 'insertOffice',
            data: selected_location.raw,
            office: selected_location.reference,
        };

        // Carrier data
        updateCarrierData(id_carrier, actionOffice, selected_location);

        insertReferenceCode(data);
    }

    function insertReferenceCode(data) {
        jQuery.ajax({
            url: FrontCheckoutAdminURL + rand + ajaxtrue,
            type: 'POST',
            data: data,
            cache: false,
            processData: true,
            success: function (data) {},
            error: function (e) {
                alert('ERROR 18034: ' + ajaxError);
            },
        });
    }
});
