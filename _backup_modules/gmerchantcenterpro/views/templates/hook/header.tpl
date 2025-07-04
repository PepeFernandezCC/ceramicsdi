{*
*
* Google merchant center Pro
*
* @author    BusinessTech.fr - https://www.businesstech.fr
* @copyright Business Tech - https://www.businesstech.fr
* @license   Commercial
*
*           ____    _______
*          |  _ \  |__   __|
*          | |_) |    | |
*          |  _ <     | |
*          | |_) |    | |
*          |____/     |_|
*
*}
{if !empty($GmcpUseGcr)}
    {* Handle case for badge if the option is activtated *}
    {if !empty($GmcpUseBadge) && !empty($GmcpMerchantId)}
        {literal}
            <script src="https://apis.google.com/js/platform.js?onload=renderBadge" async defer></script>

            <script>
                window.renderBadge = function() {
                    var ratingBadgeContainer = document.createElement("div");
                    document.body.appendChild(ratingBadgeContainer);
                    window.gapi.load('ratingbadge', function() {
                        window.gapi.ratingbadge.render(ratingBadgeContainer, {"merchant_id":  {/literal}{$GmcpMerchantId|escape:'htmlall':'UTF-8'}{literal}});
                    });
                }
            </script>
        {/literal}
    {/if}

    {* Handle case for Google form*}
    {if !empty($useReviewForm) &&  !empty($GmcpMerchantId)}
        {if empty($useProductGtin)}
            {literal}
                <script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async defer></script>

                <script>
                    window.renderOptIn = function() {
                        window.gapi.load('surveyoptin', function() {
                            window.gapi.surveyoptin.render({
                                "merchant_id": "{/literal}{$GmcpMerchantId|escape:'htmlall':'UTF-8'}{literal}",
                                "order_id": "{/literal}{$orderId|escape:'htmlall':'UTF-8'}{literal}",
                                "email": "{/literal}{$customerEmail|escape:'htmlall':'UTF-8'}{literal}",
                                "delivery_country": "{/literal}{$deliveryCountry|escape:'htmlall':'UTF-8'}{literal}",
                                "estimated_delivery_date": "{/literal}{$deliveryDate|escape:'htmlall':'UTF-8'}{literal}",
                            });
                        });
                    }
                </script>
            {/literal}
        {else if !empty($useProductGtin)}
            {literal}
                <script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async defer></script>

                <script>
                    window.renderOptIn = function() {
                        window.gapi.load('surveyoptin', function() {
                            window.gapi.surveyoptin.render({
                                "merchant_id": "{/literal}{$GmcpMerchantId|escape:'htmlall':'UTF-8'}{literal}",
                                "order_id": "{/literal}{$orderId|escape:'htmlall':'UTF-8'}{literal}",
                                "email": "{/literal}{$customerEmail|escape:'htmlall':'UTF-8'}{literal}",
                                "delivery_country": "{/literal}{$deliveryCountry|escape:'htmlall':'UTF-8'}{literal}",
                                "estimated_delivery_date": "{/literal}{$deliveryDate|escape:'htmlall':'UTF-8'}{literal}",

                            {/literal}
                            {if !empty($productGtins)}
                                "products": [
                                    {foreach from=$productGtins item=sGtin}
                                        {literal}{"gtin":"{/literal}{$sGtin|escape:'htmlall':'UTF-8'}{literal}"},{/literal}
                                    {/foreach}
                                ]
                            {/if}
                            {literal}

                            });
                        });
                    }
                </script>
            {/literal}
        {/if}
    {/if}
{/if}