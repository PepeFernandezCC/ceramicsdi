<link href="{$co_base_dir}views/css/all.css" rel="stylesheet">

<!-- Modal HTML -->
<div id="myModal" class="modal fadee" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="myModalTitle" class="modal-title">{l s='Confirmation?' mod='correosoficial'}</h5>
                {* <button id="myModalCloseButton" type="button" class="close" data-bs-dismiss="modal">&times;</button> *}
            </div>
            <div id="myModalDescription" class="modal-body">
                <p>...</p>
            </div>
            <div class="modal-footer">
                <button id="myModalCancelButton" type="button" class="btn btn-danger"
                    data-bs-dismiss="modal">{l s='Cancel' mod='correosoficial'}</button>
                <button id="myModalActionButtonCustomerData" type="button"
                    class="myModalActionButton btn btn-primary">{l s='Action' mod='correosoficial'}</button>
                <button id="myModalActionButtonSenders" type="button"
                    class="myModalActionButton btn btn-primary">{l s='Action' mod='correosoficial'}</button>
                <button id="myModalAcceptButton" type="button"
                    class="myModalActionButton btn btn-primary">{l s='Action' mod='correosoficial'}</button>
            </div>
        </div>
    </div>
</div>

<div id="co_header" class="container-fluid">

    <div class="col-md-12 header-logo clearfix">
        <img src="{$co_base_dir}views/img/logos/logo-header.png" alt="Correos">
    </div>
    <div class="module_version">
        <span>version {CORREOS_OFICIAL_VERSION}</span>
    </div>
</div>

<script>
    var addButton    = "{l s='Add' mod='correosoficial'}";
    var editButton   = "{l s='Edit' mod='correosoficial'}"; 
    var deleteButton = "{l s='Delete' mod='correosoficial'}";
    var acceptButton = "{l s='Accept' mod='correosoficial'}";
    var informationTitle = "{l s='Information' mod='correosoficial'}";
    var errorTitle = "{l s='An error has occurred' mod='correosoficial'}";
</script>