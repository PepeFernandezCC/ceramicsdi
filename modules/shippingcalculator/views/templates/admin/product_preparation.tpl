{*
* 2007-2024 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*}

<div class="panel">
    <div class="panel-heading">
        <i class="icon-truck"></i>
        {l s='Plazo de Preparación' mod='shippingcalculator'}
    </div>
    <div class="form-wrapper">
        <div class="form-group">
            <label class="control-label col-lg-3">
                {l s='Días de preparación' mod='shippingcalculator'}
            </label>
            <div class="col-lg-9">
                <input type="number" 
                       name="shipping_calculator_preparation_days" 
                       id="shipping_calculator_preparation_days"
                       value="{$preparation_days|intval}"
                       min="0"
                       max="365"
                       class="form-control"
                       placeholder="{l s='Ej: 3' mod='shippingcalculator'}" />
                <p class="help-block">
                    {l s='Número de días necesarios para preparar este producto antes del envío' mod='shippingcalculator'}
                </p>
            </div>
        </div>
    </div>
</div>

