{extends file='checkout/_partials/steps/checkout-step.tpl'}

{block name='step_content'}
    {hook h='displayPersonalInformationTop' customer=$customer}

    {if $customer.is_logged && !$customer.is_guest}

        <p class="identity">
            {* [1][/1] is for a HTML tag. *}
            {l s='Connected as [1]%firstname% %lastname%[/1].'
            d='Shop.Theme.Customeraccount'
            sprintf=[
            '[1]' => "<a href='{$urls.pages.identity}'>",
            '[/1]' => "</a>",
            '%firstname%' => $customer.firstname,
            '%lastname%' => $customer.lastname
            ]
            }
        </p>
        <p>
            {* [1][/1] is for a HTML tag. *}
            {l
            s='Not you? [1]Log out[/1]'
            d='Shop.Theme.Customeraccount'
            sprintf=[
            '[1]' => "<a href='{$urls.actions.logout}'>",
            '[/1]' => "</a>"
            ]
            }
        </p>
        {if !isset($empty_cart_on_logout) || $empty_cart_on_logout}
            <p><small>{l s='If you sign out now, your cart will be emptied.' d='Shop.Theme.Checkout'}</small></p>
        {/if}

        <div class="clearfix">
            <form method="GET" action="{$urls.pages.order}">
                <button
                        class="continue btn btn-primary float-xs-right"
                        name="controller"
                        type="submit"
                        value="order"
                >
                    {l s='Continue' d='Shop.Theme.Actions'}
                </button>
            </form>

        </div>

    {else}
       
        <div id="checkout-login-form" class="col-md-6 col-xs-12 mobile-pb-15 mobile-pt-15">
            {render file='checkout/_partials/login-form.tpl' ui=$login_form}
        </div>
        <div id="checkout-guest-form" class="col-md-6 col-xs-12 mobile-pb-0 mobile-pt-0">
            {render file='checkout/_partials/customer-form.tpl' ui=$register_form guest_allowed=$guest_allowed step="registration"}
        </div>
        <div id="nc-continue-button" style="display:none">
            {include file='checkout/_partials/planatec-checkout-bottom.tpl'}
        </div>



    {/if}
{/block}
