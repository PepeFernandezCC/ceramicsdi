<div class="panel" id="ccpromoimages-panel">
  <div class="panel-heading">
    <i class="icon-picture"></i> {l s='Imágenes promocionales (uso interno / feed)' mod='ccpromoimages'}
  </div>

  <p class="alert alert-info">
    {l s='Estas imágenes son para uso externo (p. ej. feed de Channable). No se muestran en la tienda.' mod='ccpromoimages'}
  </p>

  <div class="row">
    {foreach from=['vertical', 'horizontal'] item=tipo}
      {assign var=img value=$ccpromoimages_images[$tipo]}
      <div class="col-lg-6">
        <div class="ccpromoimages-block" data-tipo="{$tipo}">
          <h4>
            {if $tipo == 'vertical'}
              {l s='Imagen vertical' mod='ccpromoimages'}
            {else}
              {l s='Imagen horizontal' mod='ccpromoimages'}
            {/if}
          </h4>

          <div class="ccpromoimages-preview">
            <img
              src="{if $img}{$img.media_url}{/if}"
              class="ccpromoimages-preview-img"
              style="max-width:100%;max-height:220px;{if !$img}display:none;{/if}"
              alt=""
            >
          </div>

          <p class="ccpromoimages-status text-muted small">
            {if $img}{l s='Imagen cargada.' mod='ccpromoimages'}{else}{l s='Sin imagen.' mod='ccpromoimages'}{/if}
          </p>

          <input type="file" class="ccpromoimages-input" accept="image/jpeg,image/png,image/webp">
          <button type="button" class="btn btn-default ccpromoimages-delete" {if !$img}style="display:none;"{/if}>
            <i class="icon-trash"></i> {l s='Eliminar' mod='ccpromoimages'}
          </button>
        </div>
      </div>
    {/foreach}
  </div>
</div>

<style>
  .ccpromoimages-block { border: 1px solid #ddd; border-radius: 4px; padding: 15px; margin-bottom: 15px; }
  .ccpromoimages-preview { min-height: 60px; margin-bottom: 10px; }
  .ccpromoimages-block .ccpromoimages-delete { margin-top: 8px; }
</style>

<script>
  (function () {
    // El nucleo de Prestashop no tiene ningun hook entre las imagenes del
    // producto y el bloque de Resumen/Descripcion: el hook mas cercano
    // (displayAdminProductsMainStepLeftColumnMiddle) se pinta justo
    // DESPUES del Resumen. Como el resto de la pestaña se renderiza en el
    // mismo HTML (no por AJAX), en cuanto este script se ejecuta el
    // panel de imagenes y el bloque de Resumen ya existen en el DOM, asi
    // que movemos el panel a donde lo pide negocio: debajo de las
    // imagenes, antes del Resumen.
    var panel = document.getElementById('ccpromoimages-panel');
    var imagesContainer = document.getElementById('product-images-container');
    if (panel && imagesContainer && imagesContainer.parentNode) {
      imagesContainer.parentNode.insertBefore(panel, imagesContainer.nextSibling);
    }

    var ajaxUrl = {$ccpromoimages_ajax_url|json_encode nofilter};
    var idProduct = {$ccpromoimages_id_product|intval};

    function buildUrl(action) {
      var sep = ajaxUrl.indexOf('?') === -1 ? '?' : '&';
      return ajaxUrl + sep + 'ajax=1&action=' + action;
    }

    document.querySelectorAll('#ccpromoimages-panel .ccpromoimages-block').forEach(function (block) {
      var tipo = block.getAttribute('data-tipo');
      var input = block.querySelector('.ccpromoimages-input');
      var previewImg = block.querySelector('.ccpromoimages-preview-img');
      var status = block.querySelector('.ccpromoimages-status');
      var deleteBtn = block.querySelector('.ccpromoimages-delete');

      input.addEventListener('change', function () {
        if (!input.files || !input.files[0]) {
          return;
        }

        var formData = new FormData();
        formData.append('image', input.files[0]);
        formData.append('id_product', idProduct);
        formData.append('tipo', tipo);

        status.textContent = 'Subiendo...';

        fetch(buildUrl('Upload'), {
          method: 'POST',
          body: formData,
          credentials: 'same-origin',
        })
          .then(function (response) { return response.json(); })
          .then(function (data) {
            if (data.success) {
              previewImg.src = data.image.media_url;
              previewImg.style.display = '';
              status.textContent = 'Imagen cargada.';
              deleteBtn.style.display = '';
            } else {
              status.textContent = data.error || 'Error al subir la imagen.';
            }
          })
          .catch(function () {
            status.textContent = 'Error al subir la imagen.';
          })
          .finally(function () {
            input.value = '';
          });
      });

      deleteBtn.addEventListener('click', function () {
        if (!confirm('¿Eliminar esta imagen?')) {
          return;
        }

        fetch(buildUrl('Delete'), {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'id_product=' + encodeURIComponent(idProduct) + '&tipo=' + encodeURIComponent(tipo),
          credentials: 'same-origin',
        })
          .then(function (response) { return response.json(); })
          .then(function (data) {
            if (data.success) {
              previewImg.src = '';
              previewImg.style.display = 'none';
              status.textContent = 'Sin imagen.';
              deleteBtn.style.display = 'none';
            }
          });
      });
    });
  })();
</script>
