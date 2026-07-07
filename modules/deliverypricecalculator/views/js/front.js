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

});
