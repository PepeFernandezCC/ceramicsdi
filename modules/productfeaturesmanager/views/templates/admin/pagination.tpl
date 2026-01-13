{*
* 2007-2024 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*}

<nav aria-label="Page navigation">
    <ul class="pagination">
        {* Botón Primera Página *}
        {if $page > 1}
        <li>
            <a href="?controller=AdminProductFeaturesManager&token={$smarty.get.token|escape:'html':'UTF-8'}&id_feature={$selected_feature|intval}&id_lang={$selected_lang|intval}&page=1&per_page={$per_page|intval}&search={$search|escape:'url':'UTF-8'}" 
               aria-label="Primera">
                <span aria-hidden="true">&laquo;&laquo;</span>
            </a>
        </li>
        {/if}
        
        {* Botón Anterior *}
        {if $page > 1}
        <li>
            <a href="?controller=AdminProductFeaturesManager&token={$smarty.get.token|escape:'html':'UTF-8'}&id_feature={$selected_feature|intval}&id_lang={$selected_lang|intval}&page={$page-1}&per_page={$per_page|intval}&search={$search|escape:'url':'UTF-8'}" 
               aria-label="Anterior">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        {else}
        <li class="disabled">
            <span>&laquo;</span>
        </li>
        {/if}

        {* Páginas numéricas *}
        {assign var="start_page" value=max(1, $page-2)}
        {assign var="end_page" value=min($total_pages, $page+2)}
        
        {* Mostrar primera página si no está en el rango *}
        {if $start_page > 1}
        <li>
            <a href="?controller=AdminProductFeaturesManager&token={$smarty.get.token|escape:'html':'UTF-8'}&id_feature={$selected_feature|intval}&id_lang={$selected_lang|intval}&page=1&per_page={$per_page|intval}&search={$search|escape:'url':'UTF-8'}">1</a>
        </li>
        {if $start_page > 2}
        <li class="disabled"><span>...</span></li>
        {/if}
        {/if}

        {* Rango de páginas *}
        {for $i=$start_page to $end_page}
        <li {if $i == $page}class="active"{/if}>
            <a href="?controller=AdminProductFeaturesManager&token={$smarty.get.token|escape:'html':'UTF-8'}&id_feature={$selected_feature|intval}&id_lang={$selected_lang|intval}&page={$i}&per_page={$per_page|intval}&search={$search|escape:'url':'UTF-8'}">
                {$i}
            </a>
        </li>
        {/for}

        {* Mostrar última página si no está en el rango *}
        {if $end_page < $total_pages}
        {if $end_page < $total_pages-1}
        <li class="disabled"><span>...</span></li>
        {/if}
        <li>
            <a href="?controller=AdminProductFeaturesManager&token={$smarty.get.token|escape:'html':'UTF-8'}&id_feature={$selected_feature|intval}&id_lang={$selected_lang|intval}&page={$total_pages}&per_page={$per_page|intval}&search={$search|escape:'url':'UTF-8'}">{$total_pages}</a>
        </li>
        {/if}

        {* Botón Siguiente *}
        {if $page < $total_pages}
        <li>
            <a href="?controller=AdminProductFeaturesManager&token={$smarty.get.token|escape:'html':'UTF-8'}&id_feature={$selected_feature|intval}&id_lang={$selected_lang|intval}&page={$page+1}&per_page={$per_page|intval}&search={$search|escape:'url':'UTF-8'}" 
               aria-label="Siguiente">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
        {else}
        <li class="disabled">
            <span>&raquo;</span>
        </li>
        {/if}

        {* Botón Última Página *}
        {if $page < $total_pages}
        <li>
            <a href="?controller=AdminProductFeaturesManager&token={$smarty.get.token|escape:'html':'UTF-8'}&id_feature={$selected_feature|intval}&id_lang={$selected_lang|intval}&page={$total_pages}&per_page={$per_page|intval}&search={$search|escape:'url':'UTF-8'}" 
               aria-label="Última">
                <span aria-hidden="true">&raquo;&raquo;</span>
            </a>
        </li>
        {/if}
    </ul>
    
    <div class="pagination-info" style="display: inline-block; margin-left: 20px;">
        <span class="text-muted">
            {l s='Página' mod='productfeaturesmanager'} {$page} {l s='de' mod='productfeaturesmanager'} {$total_pages} 
            ({l s='Total' mod='productfeaturesmanager'}: {$total_products} {l s='productos' mod='productfeaturesmanager'})
        </span>
    </div>
</nav>

