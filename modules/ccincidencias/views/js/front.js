document.addEventListener('DOMContentLoaded', function () {
    var referenciaInput = document.getElementById('cc_referencia');
    var referenciaWarning = document.querySelector('#cc-incidencias .cc-referencia-warning');

    if (!referenciaInput || !referenciaWarning) {
        return;
    }

    function normalizedPreview(value) {
        return value.trim().toUpperCase().replace(/[\s\-.]/g, '');
    }

    function checkReferencia() {
        var normalized = normalizedPreview(referenciaInput.value || '');
        var isValid = /^[A-Z]{9}$/.test(normalized);
        referenciaWarning.style.display = (normalized !== '' && !isValid) ? 'block' : 'none';
    }

    referenciaInput.addEventListener('blur', checkReferencia);
    referenciaInput.addEventListener('input', checkReferencia);
});
