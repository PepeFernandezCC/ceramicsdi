$(document).ready(function () {

    if (document.getElementById('deliveryPriceCalculator')) {

        const countrySelector = document.getElementById('field-id_country');
        const deliverySearchButton = document.getElementById('calculateMyDeliveryButton');
        const provinceSelector = document.getElementById('field-id_state');
        const language = document.getElementById('language').value;
        let provinceMessage = 'Select a state';

        if (language == 1) {
            provinceMessage = 'Selecciona una provincia';
        }
        if (language == 2) {
            provinceMessage = 'Sélectionnez une province';
        }
        if (language == 4) {
            provinceMessage = 'Wählen Sie eine Provinz';
        }
        if (language == 5) {
            provinceMessage = 'Selecione uma província';
        }
        if (language == 6) {
            provinceMessage = 'Selecteer een provincie';
        }

        countrySelector.addEventListener('change', function () {
            let countryId = this.value;

            provinceSelector.innerHTML = '<option value=""> ... </option>';

            fetch(deliveryPriceCalculatorProvincesUrl + '?id_country=' + countryId)
                .then((response) => response.json())
                .then((data) => {
                    provinceSelector.innerHTML = "<option value=''>" + provinceMessage + '</option>';
                    data.forEach((province) => {
                        const option = document.createElement('option');
                        option.value = province.id_state;
                        option.textContent = province.name;
                        provinceSelector.appendChild(option);
                    });
                })
                .catch((error) => {
                    console.error('Error cargando provincias:', error);
                    provinceSelector.innerHTML = '<option value=""> - . Error . - </option>';
                });
        });

        deliverySearchButton.addEventListener('click', function () {
            let countryId = document.getElementById('field-id_country').value;
            let stateId = document.getElementById('field-id_state').value;
            let postal = document.getElementById('postalzip').value;
            let cartId = document.getElementById('cartId').value;
            let packageWeight = document.getElementById('packageWeight').value;
            let showTaxes = document.getElementById('showTaxes').value;

            if (!countryId.trim() || !stateId.trim() || !postal.trim()) {
                document.getElementById('messageContainer').style.display = 'block';
            } else {
                document.getElementById('messageContainer').style.display = 'none';

                $.ajax({
                    url: deliveryPriceCalculatorPriceUrl,
                    method: 'POST',
                    data: {
                        id_country: countryId,
                        id_state: stateId,
                        postal: postal,
                        id_cart: cartId,
                        weight: packageWeight,
                        taxes: showTaxes
                    },
                    success: function (response) {
                        if (response.shipping_cost) {
                            document.getElementById('euros-input').value = response.shipping_cost;

                            const shippingCalculatorWrapper = document.getElementById('shippingCalculatorDeliveryWrapper');
                            if (shippingCalculatorWrapper && response.shipping_estimate_html) {
                                shippingCalculatorWrapper.innerHTML = response.shipping_estimate_html;
                            }
                        } else if (response.error) {
                            console.error('Error:', response.error);
                        }
                    },
                    error: function (err) {
                        console.error('Error en la solicitud AJAX:', err);
                    }
                });
            }
        });

    }

    if (document.getElementById('deliveryEstimateModal')) {

        const productCountrySelector = document.getElementById('deliveryEstimateCountry');
        const productStateSelector = document.getElementById('deliveryEstimateState');
        const productEstimateButton = document.getElementById('deliveryEstimateSubmit');
        const productEstimateMessage = document.getElementById('deliveryEstimateMessage');
        const productEstimateResults = document.getElementById('deliveryEstimateResults');
        const productEstimateLanguage = document.getElementById('deliveryEstimateLanguage').value;
        let productProvinceMessage = 'Select a state';

        if (productEstimateLanguage == 1) {
            productProvinceMessage = 'Selecciona una provincia';
        }
        if (productEstimateLanguage == 2) {
            productProvinceMessage = 'Sélectionnez une province';
        }
        if (productEstimateLanguage == 4) {
            productProvinceMessage = 'Wählen Sie eine Provinz';
        }
        if (productEstimateLanguage == 5) {
            productProvinceMessage = 'Selecione uma província';
        }
        if (productEstimateLanguage == 6) {
            productProvinceMessage = 'Selecteer een provincie';
        }

        const deliveryEstimateTranslations = {
            1: {
                product: 'Producto',
                sample: 'Muestra',
                preparation: 'Preparación',
                shipping: 'Envío',
                days: 'día(s)',
                estimatedDelivery: 'Entrega estimada',
                shippingCost: 'Coste de envío',
                requestError: 'Error al calcular el plazo de entrega'
            },
            2: {
                product: 'Produit',
                sample: 'Échantillon',
                preparation: 'Préparation',
                shipping: 'Livraison',
                days: 'jour(s)',
                estimatedDelivery: 'Livraison estimée',
                shippingCost: "Frais d'expédition",
                requestError: "Erreur lors du calcul du délai de livraison"
            },
            3: {
                product: 'Product',
                sample: 'Sample',
                preparation: 'Preparation',
                shipping: 'Shipping',
                days: 'day(s)',
                estimatedDelivery: 'Estimated delivery',
                shippingCost: 'Shipping cost',
                requestError: 'Error calculating the estimated delivery'
            },
            4: {
                product: 'Produkt',
                sample: 'Muster',
                preparation: 'Vorbereitung',
                shipping: 'Versand',
                days: 'Tag(e)',
                estimatedDelivery: 'Geschätzte Lieferung',
                shippingCost: 'Versandkosten',
                requestError: 'Fehler bei der Berechnung der Lieferzeit'
            },
            5: {
                product: 'Produto',
                sample: 'Amostra',
                preparation: 'Preparação',
                shipping: 'Envio',
                days: 'dia(s)',
                estimatedDelivery: 'Entrega estimada',
                shippingCost: 'Custo de envio',
                requestError: 'Erro ao calcular o prazo de entrega'
            },
            6: {
                product: 'Product',
                sample: 'Monster',
                preparation: 'Voorbereiding',
                shipping: 'Verzending',
                days: 'dag(en)',
                estimatedDelivery: 'Geschatte levering',
                shippingCost: 'Verzendkosten',
                requestError: 'Fout bij het berekenen van de levertijd'
            }
        };

        const deliveryEstimateI18n = deliveryEstimateTranslations[productEstimateLanguage] || deliveryEstimateTranslations[3];

        const deliveryEstimateBoxesInput = document.getElementById('deliveryEstimateBoxes');

        function normalizeBoxesValue() {
            let value = parseInt(deliveryEstimateBoxesInput.value, 10);

            if (isNaN(value) || value < 1) {
                value = 1;
            }

            deliveryEstimateBoxesInput.value = value;

            return value;
        }

        deliveryEstimateBoxesInput.addEventListener('change', normalizeBoxesValue);

        productCountrySelector.addEventListener('change', function () {
            let countryId = this.value;

            productStateSelector.innerHTML = '<option value=""> ... </option>';

            fetch(deliveryPriceCalculatorProvincesUrl + '?id_country=' + countryId)
                .then((response) => response.json())
                .then((data) => {
                    productStateSelector.innerHTML = "<option value=''>" + productProvinceMessage + '</option>';
                    data.forEach((province) => {
                        const option = document.createElement('option');
                        option.value = province.id_state;
                        option.textContent = province.name;
                        productStateSelector.appendChild(option);
                    });
                })
                .catch((error) => {
                    console.error('Error cargando provincias:', error);
                    productStateSelector.innerHTML = '<option value=""> - . Error . - </option>';
                });
        });

        function renderDeliveryEstimateBlock(title, data) {
            if (!data) {
                return '';
            }

            if (data.error) {
                return '<div class="alert alert-danger" style="margin-top:10px">' + title + ': ' + data.error + '</div>';
            }

            let html = '<div style="margin-top:10px; padding-top:10px; border-top:1px solid #eee">';
            html += '<strong>' + title + '</strong><br>';

            if (data.has_delivery_info && data.estimated_delivery) {
                const delivery = data.estimated_delivery;
                html += deliveryEstimateI18n.preparation + ': ' + delivery.preparation_days + ' ' + deliveryEstimateI18n.days + '<br>';
                html += deliveryEstimateI18n.shipping + ': ' + delivery.shipping_days + ' ' + deliveryEstimateI18n.days + '<br>';
                html += deliveryEstimateI18n.estimatedDelivery + ': ' + delivery.start_date_formatted + ' - ' + delivery.end_date_formatted + '<br>';
            }

            if (data.shipping_cost_formatted) {
                html += deliveryEstimateI18n.shippingCost + ': ' + data.shipping_cost_formatted;

                if (data.carrier_name) {
                    html += ' (' + data.carrier_name + ')';
                }
            }

            html += '</div>';

            return html;
        }

        productEstimateButton.addEventListener('click', function () {
            const idProduct = document.getElementById('deliveryEstimateIdProduct').value;
            const boxes = normalizeBoxesValue();
            const countryId = productCountrySelector.value;
            const stateId = productStateSelector.value;
            const postal = document.getElementById('deliveryEstimatePostal').value;

            if (!countryId.trim() || !stateId.trim() || !postal.trim() || !boxes || boxes < 1) {
                productEstimateMessage.style.display = 'block';
                return;
            }

            productEstimateMessage.style.display = 'none';
            productEstimateResults.innerHTML = '';

            $.ajax({
                url: deliveryPriceCalculatorProductEstimateUrl,
                method: 'POST',
                data: {
                    id_product: idProduct,
                    boxes: boxes,
                    id_country: countryId,
                    id_state: stateId,
                    postal: postal
                },
                success: function (response) {
                    let html = renderDeliveryEstimateBlock(deliveryEstimateI18n.product, response.product);
                    html += renderDeliveryEstimateBlock(deliveryEstimateI18n.sample, response.sample);
                    productEstimateResults.innerHTML = html;
                },
                error: function (err) {
                    console.error('Error en la solicitud AJAX:', err);
                    productEstimateResults.innerHTML = '<div class="alert alert-danger">' + deliveryEstimateI18n.requestError + '</div>';
                }
            });
        });

    }

});
