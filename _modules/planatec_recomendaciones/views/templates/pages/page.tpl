{extends file="page.tpl"}
{block name="page_content"}
    <div id="recomendacion-secciones" class="hidden-sm-down">
        {foreach from=$recomendacionPagina['secciones'] item="recomendacionSeccion"}
            <div id="recomendacion-seccion-{$recomendacionSeccion['id_recomendacion_seccion']}-wrapper"
                 class="col-lg-4 col-sm-6 col-xs-12 recomendacion-seccion"
                 data-show-id="{$recomendacionSeccion['id_recomendacion_seccion']}">
                <div>
                    <img src="{$image_baseurl}{$recomendacionSeccion['imagen']}" loading="lazy"
                         title="{$recomendacionSeccion['titulo']}">
                    <h5 class="title">{$recomendacionSeccion['titulo']}</h5>
                </div>
            </div>
        {/foreach}
    </div>
    <div id="recomendacion-apartados-seccion"
         class="recomendacion-apartados d-none">
        <div class="col-xs-12 col-sm-4 col-md-3 recomendacion-apartados-lista">
            {foreach from=$recomendacionPagina['secciones'] item="recomendacionSeccion"}
                <div id="recomendacion-botones-seccion-{$recomendacionSeccion['id_recomendacion_seccion']}"
                     class="recomendacion-seccion">
                    <div class="recomendacion-seccion-titulo">
                        {$recomendacionSeccion['titulo']}
                    </div>
                    {foreach from=$recomendacionSeccion['apartados'] item="recomendacionApartado"}
                        <div class="recomendacion-apartado-titulo d-none">
                            <span data-show-id="{$recomendacionApartado['id_recomendacion_apartado']}">{$recomendacionApartado['titulo']}</span>
                        </div>
                    {/foreach}
                </div>
            {/foreach}
        </div>
        <div class="col-xs-12 col-sm-8 col-md-9 recomendacion-apartados-contenido-global">
            {foreach from=$recomendacionPagina['secciones'] item="recomendacionSeccion" name="iterationSecciones"}
                <div id="recomendacion-seccion-{$recomendacionSeccion['id_recomendacion_seccion']}"
                     class="col-xs-12 recomendacion-apartados-contenido {if $smarty.foreach.iterationSecciones.iteration != 1}d-none{/if}">
                    <div class="recomendacion-seccion-titulo">{$recomendacionSeccion['titulo']}</div>
                    {foreach from=$recomendacionSeccion['apartados'] item="recomendacionApartado" name="iterationApartados"}
                        <div id="recomendacion-apartado-{$recomendacionApartado['id_recomendacion_apartado']}"
                             class="recomendacion-apartado-contenido">
                            <div class="col-xl-2 col-sm-4 col-xs-12 recomendacion-apartado-titulo">
                                <span class="iteracion">{$smarty.foreach.iterationApartados.iteration}.</span> {$recomendacionApartado['titulo']}
                            </div>
                            <div class="col-xl-10 col-sm-8 col-xs-12 recomendacion-apartado-texto">
                                {$recomendacionApartado['contenido'] nofilter}
                            </div>
                        </div>
                    {/foreach}
                </div>
            {/foreach}
        </div>
    </div>

    {* MOBILE *}
    <div id="recomendacion-apartados-seccion-mobile" class="recomendacion-apartados hidden-md-up">
        <div class="col-xs-12 col-sm-4 col-md-3 recomendacion-apartados-lista">
            {foreach from=$recomendacionPagina['secciones'] item="recomendacionSeccion" name="iterationSecciones"}
                <div id="recomendacion-botones-seccion-{$recomendacionSeccion['id_recomendacion_seccion']}"
                     class="recomendacion-seccion">
                    <div class="recomendacion-seccion-titulo">
                        {$recomendacionSeccion['titulo']}
                    </div>
                    {foreach from=$recomendacionSeccion['apartados'] item="recomendacionApartado" name="iterationApartados"}
                        <div class="recomendacion-apartado-titulo d-none">
                            <span data-show-id="{$recomendacionApartado['id_recomendacion_apartado']}">
                                {$smarty.foreach.iterationApartados.iteration}. {$recomendacionApartado['titulo']}
                            </span>
                        </div>
                        <div class="recomendacion-apartados-contenido-global d-none">
                            <div class="recomendacion-apartados-contenido">
                                <div id="recomendacion-apartado-{$recomendacionApartado['id_recomendacion_apartado']}"
                                     class="recomendacion-apartado-contenido">
                                    <div class="col-xl-10 col-sm-8 col-xs-12 recomendacion-apartado-texto">
                                        {$recomendacionApartado['contenido'] nofilter}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>
            {/foreach}
        </div>
    </div>
{/block}