{if isset($inspirationalProducts) && $inspirationalProducts|@count}
<section id="carousel-materials" class="mb-3 carousel-materials-category">
  <div class="container-fluid text-center px-0">
    <h2 style="color:black;">
      {l s='Inspiration of' d='Shop.Theme.Catalog'}: {$categoryName|escape:'html':'UTF-8'}
    </h2>
     {assign var="altPreText" value={l s='Inspiration of' d='Shop.Theme.Catalog'}}
     {assign var="altText" value={$categoryName|escape:'html':'UTF-8'}}
    <div id="recipeCarousel" class="row mx-auto my-auto justify-content-center">
      <div class="owl-carousel inspiration-owl">
        {foreach from=$inspirationalProducts item=productItem}
          <a href="javascript:void(0);" class="inspiration-thumb"
             data-full="{$productItem.urlImageFull|escape:'html':'UTF-8'}"
             aria-label="open image">
            <img src="{$productItem.urlImageThumb|escape:'html':'UTF-8'}"
                 loading="lazy" alt="{$productItem.imageLegend|escape:'html':'UTF-8'}" />
          </a>
        {/foreach}
      </div>
    </div>
  </div>
</section>

{* Modal para ampliar *}
<div id="inspirationModal" class="inspiration-modal" aria-hidden="true">
  <button class="inspiration-modal__close" type="button" aria-label="close">&times;</button>
  <img id="inspirationModalImg" src="" alt="zoom - {$altPreText}: {$altText}" />
</div>
{/if}
