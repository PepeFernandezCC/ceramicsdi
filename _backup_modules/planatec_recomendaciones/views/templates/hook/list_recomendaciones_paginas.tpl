<div class="panel">
    <h3>
        <i class="icon-list-ul"></i>{l s='Listado de páginas de recomendaciones' d='Modules.PlanatecRecomendaciones.Admin'}
        <span class="panel-heading-action">
            <a id="desc-product-new" class="list-toolbar-btn"
               href="{$link->getAdminLink('AdminModules')}&configure=planatec_recomendaciones&addRecomendacionPagina=1">
                <span title="" data-toggle="tooltip" class="label-tooltip"
                      data-original-title="{l s='Add new' d='Admin.Actions'}" data-html="true">
                    <i class="process-icon-new"></i>
                </span>
            </a>
        </span>
    </h3>

    <div id="recomendacionesPaginasContent">
        <div id="recomendacionesPaginas">
            {foreach from=$recomendacionesPaginas item=recomendacionPagina}
                <div id="recomendacionesPaginas_{$recomendacionPagina.id_recomendacion_pagina}" class="panel">
                    <div class="row">
                        <div class="col-lg-1">
                            <span><i class="icon-arrows "></i></span>
                        </div>
                        <div class="col-md-11">
                            <h4 class="pull-left">
                                #{$recomendacionPagina.id_recomendacion_pagina} - {$recomendacionPagina.titulo}
                            </h4>

                            <div class="btn-group-action pull-right">
                                {$recomendacionPagina.status}

                                <a class="btn btn-default"
                                   href="{$link->getAdminLink('AdminModules')}&configure=planatec_recomendaciones&idRecomendacionPagina={$recomendacionPagina.id_recomendacion_pagina}">
                                    <i class="icon-edit"></i>
                                    {l s='Edit' d='Admin.Actions'}
                                </a>
                                <a class="btn btn-danger"
                                   onclick="return confirm('{l s='Are you sure?' d='Module.PlanatecRecomendaciones.Admin'}')"
                                   href="{$link->getAdminLink('AdminModules')}&configure=planatec_recomendaciones&deleteRecomendacionPagina&idRecomendacionPagina={$recomendacionPagina.id_recomendacion_pagina}">
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