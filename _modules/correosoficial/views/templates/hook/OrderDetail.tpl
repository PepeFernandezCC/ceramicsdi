<link rel="stylesheet" type="text/css" href="{$co_base_dir}views/css/detailOrder.css" />

<div class="card card-custom detail-order-container">
  <div class="card-header">
    <h3 class="card-header-title">
      <img src="{$co_base_dir}views/img/logo.jpg" alt="Correos" width="100" />
      {l s='Correos Oficial' mod='correosoficial'}
    </h3>
  </div>

  <div class="card-body">
    <div class="card" id="card-rte">

      <div class="card-header card-header-blue">
        <h3 class="card-header-title">{l s='Correos Oficial Tracking Order' mod='correosoficial'}</h3>
      </div>

      <div class="do-click">
        <p>{l s='Do click in the link below to tracking your order' mod='correosoficial'}</p>

        <p>
          <a target="__blank"
            href="https://www.correos.es/es/es/herramientas/localizador/envios/detalle?tracking-number={$shipping_number}">{l s='Tracking your order' mod='correosoficial'}</a>
        </p>
      </div>

    </div>
  </div>
</div>