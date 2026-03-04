{if $ccpr_count > 0}
  {assign var=avg value=$ccpr_avg|floatval}
  {assign var=pct value=($avg*20)}

  <div class="ccpr-rating-inline" aria-label="{$ccpr_avg|escape:'html':'UTF-8'} de 5">
    <span class="ccpr-avg__stars">
      <span class="ccpr-avg__fill" style="width: {$pct}%"></span>
    </span>
    <span class="ccpr-avg__num">{$ccpr_avg|escape:'html':'UTF-8'} / 5</span>
    <span class="ccpr-avg__count">({$ccpr_count|intval}) {l s='reviews' d='Shop.Theme.Catalog'}</span>
  </div>
{/if}