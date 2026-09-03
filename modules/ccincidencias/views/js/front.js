document.addEventListener('DOMContentLoaded', function () {
    var referenciaInput = document.getElementById('cc_referencia');
    var referenciaWarning = document.querySelector('#cc-incidencias .cc-referencia-warning');

    if (referenciaInput && referenciaWarning) {
        var normalizedPreview = function (value) {
            return value.trim().toUpperCase().replace(/[\s\-.]/g, '');
        };

        var checkReferencia = function () {
            var normalized = normalizedPreview(referenciaInput.value || '');
            var isValid = /^[A-Z]{9}$/.test(normalized);
            referenciaWarning.style.display = (normalized !== '' && !isValid) ? 'block' : 'none';
        };

        referenciaInput.addEventListener('blur', checkReferencia);
        referenciaInput.addEventListener('input', checkReferencia);
    }

    // Validacion antes de enviar: si falta algo, no se llega a mandar el
    // formulario. Asi el <input type="file"> de fotos no se pierde (los
    // navegadores no dejan rellenar de nuevo un campo de fichero tras
    // recargar la pagina, asi que evitar la recarga cuando el error es
    // detectable en el propio navegador es la unica forma de no perder
    // las fotos ya seleccionadas). El servidor sigue validando todo de
    // nuevo: esto es solo para evitar viajes de ida y vuelta evitables.
    var form = document.querySelector('#cc-incidencias .cc-form');
    var l10n = window.ccIncidenciasL10n || {};

    if (!form) {
        return;
    }

    // Fotos obligatorias o no segun el tipo elegido (campo "Requiere
    // fotos" del tipo de incidencia, gestionado en Admin). Se refleja
    // tanto en el asterisco de obligatoriedad como en la validacion.
    var tipoSelect = form.querySelector('#cc_tipo');
    var fotosRequiredMark = document.getElementById('cc_fotos_required');

    var tipoRequiresPhotos = function () {
        if (!tipoSelect || !tipoSelect.value) {
            return false;
        }
        var selectedOption = tipoSelect.options[tipoSelect.selectedIndex];

        return !!selectedOption && selectedOption.getAttribute('data-require-photos') === '1';
    };

    var syncFotosRequiredMark = function () {
        if (fotosRequiredMark) {
            fotosRequiredMark.style.display = tipoRequiresPhotos() ? '' : 'none';
        }
    };

    if (tipoSelect) {
        tipoSelect.addEventListener('change', syncFotosRequiredMark);
        syncFotosRequiredMark();
    }

    var errorsBox = document.querySelector('#cc-incidencias .cc-client-errors');
    var errorsList = errorsBox ? errorsBox.querySelector('ul') : null;

    var collectErrors = function () {
        var errors = [];

        var tipo = form.querySelector('#cc_tipo');
        if (tipo && !tipo.value) {
            errors.push({ field: tipo, message: l10n.error_required_tipo });
        }

        var referencia = form.querySelector('#cc_referencia');
        if (referencia && !referencia.value.trim()) {
            errors.push({ field: referencia, message: l10n.error_required_referencia });
        }

        var descripcion = form.querySelector('#cc_descripcion');
        if (descripcion && !descripcion.value.trim()) {
            errors.push({ field: descripcion, message: l10n.error_required_descripcion });
        }

        var consentimiento = form.querySelector('input[name="consentimiento"]');
        if (consentimiento && !consentimiento.checked) {
            errors.push({ field: consentimiento, message: l10n.error_required_consentimiento });
        }

        var fotos = form.querySelector('#cc_fotos');
        if (fotos && tipoRequiresPhotos() && fotos.files.length === 0) {
            errors.push({ field: fotos, message: l10n.error_required_fotos });
        }

        return errors;
    };

    var showErrors = function (errors) {
        if (!errorsBox || !errorsList) {
            return;
        }

        errorsList.innerHTML = '';
        errors.forEach(function (error) {
            if (!error.message) {
                return;
            }
            var li = document.createElement('li');
            li.textContent = error.message;
            errorsList.appendChild(li);
        });

        errorsBox.style.display = errors.length ? 'block' : 'none';
    };

    form.addEventListener('submit', function (event) {
        var errors = collectErrors();

        if (!errors.length) {
            showErrors([]);
            return;
        }

        event.preventDefault();
        showErrors(errors);
        errorsBox.scrollIntoView({ behavior: 'smooth', block: 'start' });

        if (errors[0].field && typeof errors[0].field.focus === 'function') {
            errors[0].field.focus();
        }
    });
});
