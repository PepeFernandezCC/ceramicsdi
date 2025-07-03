<div class="panel">
    <h3>
        <i class="icon-list-ul"></i>{l s='Listado de páginas de secciones' d='Modules.PlanatecRecomendaciones.Admin'}
        <span class="panel-heading-action">
            <a id="desc-product-new" class="list-toolbar-btn"
               href="{$link->getAdminLink('AdminModules')}&configure=planatec_recomendaciones&addRecomendacionSeccion=1&idPaginaRelacionada={$idRecomendacionPagina}">
                <span title="" data-toggle="tooltip" class="label-tooltip"
                      data-original-title="{l s='Add new' d='Admin.Actions'}" data-html="true">
                    <i class="process-icon-new"></i>
                </span>
            </a>
        </span>
    </h3>

    <div id="recomendacionesSeccionesContent">
        <div id="recomendacionesSecciones">
            {foreach from=$recomendacionesSecciones item=recomendacionSeccion}
                <div id="recomendacionesSecciones_{$recomendacionSeccion.id_recomendacion_seccion}" class="panel">
                    <div class="row">
                        <div class="col-lg-1">
                            <span><i class="icon-arrows "></i></span>
                        </div>
                        <div class="col-md-3">
                            <img src="{$image_baseurl}{$recomendacionSeccion.imagen}"
                                 alt="{$recomendacionSeccion.titulo}"
                                 class="img-thumbnail"
                                 loading="lazy"/>
                        </div>
                        <div class="col-md-8">
                            <h4 class="pull-left">
                                #{$recomendacionSeccion.id_recomendacion_seccion} - {$recomendacionSeccion.titulo}
                            </h4>

                            <div class="btn-group-action pull-right">
                                {$recomendacionSeccion.status}

                                <a class="btn btn-default"
                                   href="{$link->getAdminLink('AdminModules')}&configure=planatec_recomendaciones&idRecomendacionSeccion={$recomendacionSeccion.id_recomendacion_seccion}&idPaginaRelacionada={$idRecomendacionPagina}">
                                    <i class="icon-edit"></i>
                                    {l s='Edit' d='Admin.Actions'}
                                </a>
                                <a class="btn btn-danger"
                                   onclick="return confirm('{l s='Are you sure?' d='Module.PlanatecRecomendaciones.Admin'}')"
                                   href="{$link->getAdminLink('AdminModules')}&configure=planatec_recomendaciones&deleteRecomendacionSeccion&idRecomendacionSeccion={$recomendacionSeccion.id_recomendacion_seccion}&idPaginaRelacionada={$idRecomendacionPagina}">
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