<div class="panel">
    <h3>
        <i class="icon-list-ul"></i>{l s='Listado de apartados' d='Modules.PlanatecRecomendaciones.Admin'}
        <span class="panel-heading-action">
            <a id="desc-product-new" class="list-toolbar-btn"
               href="{$link->getAdminLink('AdminModules')}&configure=planatec_recomendaciones&addRecomendacionApartado=1&idSeccionRelacionada={$idRecomendacionSeccion}">
                <span title="" data-toggle="tooltip" class="label-tooltip"
                      data-original-title="{l s='Add new' d='Admin.Actions'}" data-html="true">
                    <i class="process-icon-new"></i>
                </span>
            </a>
        </span>
    </h3>

    <div id="recomendacionesApartadosContent">
        <div id="recomendacionesApartados">
            {foreach from=$recomendacionesApartados item=recomendacionApartado}
                <div id="recomendacionesApartados_{$recomendacionApartado.id_recomendacion_apartado}" class="panel">
                    <div class="row">
                        <div class="col-lg-1">
                            <span><i class="icon-arrows "></i></span>
                        </div>
                        <div class="col-md-11">
                            <h4 class="pull-left">
                                #{$recomendacionApartado.id_recomendacion_apartado} - {$recomendacionApartado.titulo}
                            </h4>

                            <div class="btn-group-action pull-right">
                                {$recomendacionApartado.status}

                                <a class="btn btn-default"
                                   href="{$link->getAdminLink('AdminModules')}&configure=planatec_recomendaciones&idRecomendacionApartado={$recomendacionApartado.id_recomendacion_apartado}&idSeccionRelacionada={$idRecomendacionSeccion}">
                                    <i class="icon-edit"></i>
                                    {l s='Edit' d='Admin.Actions'}
                                </a>
                                <a class="btn btn-danger"
                                   onclick="return confirm('{l s='Are you sure?' d='Module.PlanatecRecomendaciones.Admin'}')"
                                   href="{$link->getAdminLink('AdminModules')}&configure=planatec_recomendaciones&deleteRecomendacionApartado&idRecomendacionApartado={$recomendacionApartado.id_recomendacion_apartado}&idSeccionRelacionada={$idRecomendacionSeccion}">
                                    <i class="icon-trash"></i>
                                    {l s='Delete' d='Admin.Actions'}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
</div>