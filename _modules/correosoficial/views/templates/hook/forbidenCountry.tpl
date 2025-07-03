<script type="text/javascript">
    let correosS0360 = document.getElementById('delivery_option_{$id_carrier}');
    const msg = '{$msgS0360}';
    let parent = correosS0360.closest('.row.delivery-option');
    let totalOptions = document.querySelectorAll('.row.delivery-option').length;
    let checkedOpt = parent.querySelector('input[type="radio"]');
    checkedOpt.removeAttribute('checked');
    parent.remove();
    if (totalOptions == 1) {
        document.getElementById('js-delivery').remove();
        let msgerror = document.querySelector('.delivery-options-list');
        msgerror.innerHTML = '<div class="alert alert-danger">' + msg + '</div>';
    }
</script>