{extends file='page.tpl'}

{block name='page_content_container' prepend}
    {* PLANATEC *}
    <section id="content" class="section-confirmation">
        <div class="row checkout-grid">
            <div class="cart-grid-body col-xs-12 col-lg-7">
                {$tabsName = ['checkout-personal-information-step', 'checkout-addresses-step', 'checkout-delivery-step', 'checkout-payment-step', 'checkout-confirmation']}

                <div id="planatec-tabs">
                    <div id="planatec-step-title-1" class="planatec-step-title col-xs-12 col-md-2-5">
                        1.<br>{l s='Customer Account' d='Shop.Theme.Checkout'}
                    </div>
                    <div id="planatec-step-title-2" class="planatec-step-title col-xs-12 col-md-2-5">
                        2.<br>{l s='Address' d='Shop.Theme.Checkout'}
                    </div>
                    <div id="planatec-step-title-3" class="planatec-step-title col-xs-12 col-md-2-5">
                        3.<br>{l s='Shipping method' d='Shop.Theme.Checkout'}
                    </div>
                    <div id="planatec-step-title-4" class="planatec-step-title col-xs-12 col-md-2-5">
                        4.<br>{l s='Payment' d='Shop.Theme.Checkout'}
                    </div>
                    <div id="planatec-step-title-5" class="planatec-step-title col-xs-12 col-md-2-5"
                         style="background-color: black; color: white;">
                        5.<br>{l s='Confirmation' d='Shop.Theme.Checkout'}
                    </div>
                </div>

                <section class="block-left">
                    {block name='order_confirmation_header'}
                        <h4 class="h4 card-title">
                            {l s='Your order is confirmed!' d='Shop.Theme.Checkout'}
                        </h4>
                    {/block}

                    <div>
                        <p>
                            {l s='An email has been sent to your mail address' d='Shop.Theme.Checkout'}&nbsp;<strong>{$customer.email}</strong>.
                            {if $order.details.invoice_url}
                                {* [1][/1] is for a HTML tag. *}
                                {l
                                s='You can also [1]download your invoice[/1]'
                                d='Shop.Theme.Checkout'
                                sprintf=[
                                '[1]' => "<a href='{$order.details.invoice_url}'>",
                                '[/1]' => "</a>"
                                ]
                                }.
                            {/if}
                        </p>
                    </div>

                    {block name='hook_order_confirmation'}
                        {$HOOK_ORDER_CONFIRMATION nofilter}
                    {/block}

                    {block name='order_details'}
                        <div id="order-details" class="row">
                            <div class="col-md-3 col-xs-12">
                                {l s='Order reference' d='Shop.Theme.Checkout'}:
                            </div>
                            <div class="col-md-9 col-xs-12">
                                {$order.details.reference}
                            </div>

                            <div class="col-md-3 col-xs-12">
                                {l s='Payment method' d='Shop.Theme.Checkout'}:
                            </div>
                            <div class="col-md-9 col-xs-12">
                                {$order.details.payment}
                            </div>

                            {if !$order.details.is_virtual}
                                <div class="col-md-3 col-xs-12">
                                    {l s='Shipping method' d='Shop.Theme.Checkout'}:
                                </div>
                                <div class="col-md-9 col-xs-12">
                                    {$order.carrier.name} <em>({$order.carrier.delay})</em>
                                </div>
                            {/if}
                        </div>
                    {/block}

                    {block name='hook_payment_return'}
                        {if ! empty($HOOK_PAYMENT_RETURN)}
                            <section id="content-hook_payment_return" class="card definition-list">
                                <div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            {$HOOK_PAYMENT_RETURN nofilter}
                                        </div>
                                    </div>
                                </div>
                            </section>
                        {/if}
                    {/block}

                    {if $order.totals.total_including_tax.amount > 25}
                        <div class="instrucciones-recepcion-pedido">
                            {if $language.id == 1}
                                <div class="instrucciones-recepcion-titulo"> Aviso importante sobre recepción de material cerámico</div>
                                <div class="instrucciones-recepcion-contenido">
                                    <p>Nuestros productos son pesados, frágiles y se entregan habitualmente paletizados.</p>
                                    <p>Antes de desembalar o mover el pedido, <strong>es necesario hacer fotografías del palet completo</strong> desde varios ángulos, incluyendo embalaje, etiquetas y cualquier daño visible.</p>
                                    <p>Si observas golpes, humedad, cajas rotas, retractilado manipulado o cualquier anomalía, <strong> se debe indicar por escrito en el albarán del transportista antes de firmar.</strong></p>
                                    <p>Sin fotografías del palet en el momento de la recepción y antes de su manipulación, puede resultar imposible verificar si una rotura se produjo durante el transporte o por manipulaciones posteriores.</p>
                                </div>
                            {/if}

                            {if $language.id == 2}
                                <div class="instrucciones-recepcion-titulo"> Avis important concernant la réception du matériel céramique</div>
                                <div class="instrucciones-recepcion-contenido">
                                    <p>Nos produits sont lourds, fragiles et sont généralement livrés sur palette.</p>
                                    <p>Avant de déballer ou de déplacer la commande, <strong>il est nécessaire de prendre des photographies de la palette complète</strong> sous plusieurs angles, y compris l'emballage, les étiquettes et tout dommage visible.</p>
                                    <p>Si vous constatez des chocs, de l'humidité, des cartons cassés, un film rétractable manipulé ou toute autre anomalie, <strong>indiquez-le par écrit sur le bon de livraison du transporteur avant de signer.</strong></p>
                                    <p>Sans photographies de la palette au moment de la réception et avant sa manipulation, il peut être impossible de vérifier si une casse s'est produite pendant le transport ou en raison de manipulations ultérieures.</p>
                                </div>
                            {/if}

                            {if $language.id == 3}
                                <div class="instrucciones-recepcion-titulo"> Important notice regarding receipt of ceramic material</div>
                                <div class="instrucciones-recepcion-contenido">
                                    <p>Our products are heavy, fragile and are usually delivered palletised.</p>
                                    <p>Before unpacking or moving the order, <strong>it is necessary to take photographs of the complete pallet</strong> from several angles, including the packaging, labels and any visible damage.</p>
                                    <p>If you notice impacts, moisture, broken boxes, tampered shrink wrap or any other anomaly, <strong>state this in writing on the carrier's delivery note before signing.</strong></p>
                                    <p>Without photographs of the pallet at the time of receipt and before it is handled, it may be impossible to verify whether breakage occurred during transport or due to subsequent handling.</p>
                                </div>
                            {/if}

                            {if $language.id == 4}
                                <div class="instrucciones-recepcion-titulo"> Wichtiger Hinweis zum Empfang von keramischem Material</div>
                                <div class="instrucciones-recepcion-contenido">
                                    <p>Unsere Produkte sind schwer, zerbrechlich und werden in der Regel palettiert geliefert.</p>
                                    <p>Bevor Sie die Bestellung auspacken oder bewegen, <strong>ist es erforderlich, Fotos der vollständigen Palette</strong> aus mehreren Blickwinkeln zu machen, einschließlich Verpackung, Etiketten und eventueller sichtbarer Schäden.</p>
                                    <p>Wenn Sie Stöße, Feuchtigkeit, zerbrochene Kartons, manipulierte Schrumpffolie oder sonstige Auffälligkeiten feststellen, <strong>vermerken Sie dies vor der Unterschrift schriftlich auf dem Lieferschein des Transportunternehmens.</strong></p>
                                    <p>Ohne Fotos der Palette zum Zeitpunkt des Empfangs und vor deren Handhabung kann es unmöglich sein zu überprüfen, ob ein Bruch während des Transports oder durch spätere Handhabung entstanden ist.</p>
                                </div>
                            {/if}

                            {if $language.id == 5}
                                <div class="instrucciones-recepcion-titulo"> Aviso importante sobre a receção de material cerâmico</div>
                                <div class="instrucciones-recepcion-contenido">
                                    <p>Os nossos produtos são pesados, frágeis e costumam ser entregues paletizados.</p>
                                    <p>Antes de desembalar ou mover o pedido, <strong>é necessário tirar fotografias do palete completo</strong> a partir de vários ângulos, incluindo embalagem, etiquetas e qualquer dano visível.</p>
                                    <p>Se observar golpes, humidade, caixas partidas, retrátil manipulado ou qualquer anomalia, <strong>indique-o por escrito na guia de transporte antes de assinar.</strong></p>
                                    <p>Sem fotografias do palete no momento da receção e antes da sua manipulação, pode ser impossível verificar se uma quebra ocorreu durante o transporte ou devido a manipulações posteriores.</p>
                                </div>
                            {/if}

                            {if $language.id == 6}
                                <div class="instrucciones-recepcion-titulo"> Belangrijke mededeling over de ontvangst van keramisch materiaal</div>
                                <div class="instrucciones-recepcion-contenido">
                                    <p>Onze producten zijn zwaar, breekbaar en worden meestal gepalletiseerd geleverd.</p>
                                    <p>Voordat u de bestelling uitpakt of verplaatst, <strong>is het noodzakelijk om foto's te maken van de volledige pallet</strong> vanuit verschillende hoeken, inclusief de verpakking, etiketten en eventuele zichtbare schade.</p>
                                    <p>Als u stoten, vocht, gebroken dozen, gemanipuleerde krimpfolie of enige andere afwijking vaststelt, <strong>vermeld dit dan schriftelijk op de leveringsbon van de vervoerder voordat u tekent.</strong></p>
                                    <p>Zonder foto's van de pallet op het moment van ontvangst en vóór de hantering ervan kan het onmogelijk zijn te verifiëren of een breuk tijdens het transport is ontstaan of door latere hantering.</p>
                                </div>
                            {/if}
                        </div>
                    {/if}

                    {block name='customer_registration_form'}
                        {if $customer.is_guest}
                            <div id="registration-form" class="card" style="width:100%">
                                <div class="card-block" style="padding: 0">
                                    <h4 class="h4">{l s='Save time on your next order, sign up now' d='Shop.Theme.Checkout'}</h4>
                                    {render file='customer/_partials/customer-form.tpl' ui=$register_form  step="confirmation"}
                                </div>
                            </div>
                        {/if}
                    {/block}

                    {block name='hook_order_confirmation_1'}
                        {hook h='displayOrderConfirmation1'}
                    {/block}
                </section>
            </div>

            <div class="cart-grid-right col-xs-12 col-lg-5">
                <section class="card js-cart">
                    <div id="planatec-summary">
                        <h3>{l s='Order summary' d='Shop.Theme.Customeraccount'}</h3>
                    </div>
                    <div class="card-block">
                        <div class="cart-summary-products js-cart-summary-products">
                            {block name='cart_summary_product_list'}
                                <div class="{*PLANATEC collapse *}" id="cart-summary-product-list">
                                    <ul class="media-list">
                                        {foreach from=$order.products item=product}
                                            <li class="media">{include file='checkout/_partials/cart-summary-product-line.tpl' product=$product}</li>
                                        {/foreach}
                                    </ul>
                                </div>
                            {/block}
                        </div>

                        <div class="card-block cart-summary-subtotals-container js-cart-summary-subtotals-container">
                            {foreach from=$order.subtotals item="subtotal"}
                                {if $subtotal && $subtotal.value|count_characters > 0 && $subtotal.type !== 'tax'}
                                    <div class="cart-summary-line cart-summary-subtotals" id="cart-subtotal-{$subtotal.type}">
                                        <span class="label">
                                            {$subtotal.label}
                                        </span>
                                        <span class="value">
                                          {if 'discount' == $subtotal.type}-&nbsp;{/if}{$subtotal.value}
                                        </span>
                                    </div>
                                {/if}
                            {/foreach}
                           
                            {hook h='displayBlackFridayShippingDiscount' mod='blackfriday'}
                        </div>
                    </div>
                </section>

                <div class="card-block cart-summary-totals js-cart-summary-totals">
                    {block name='cart_summary_total'}
                        {if !$configuration.display_prices_tax_incl && $configuration.taxes_enabled}
                            <div class="cart-summary-line">
                                <span class="label">{$order.totals.total.label}&nbsp;{$order.labels.tax_short}</span>
                                <span class="value">{$order.totals.total.value}</span>
                            </div>
                            <div class="cart-summary-line cart-total">
                                <span class="label">{$order.totals.total_including_tax.label}</span>
                                <span class="value">{$order.totals.total_including_tax.value}</span>
                            </div>
                        {else}
                            <div class="cart-summary-line cart-total">
                                <span class="label">{$order.totals.total.label}&nbsp;{if $configuration.taxes_enabled}{$order.labels.tax_short}{/if}</span>
                                <span class="value">{$order.totals.total.value}</span>
                            </div>
                        {/if}
                    {/block}

                    {block name='cart_summary_tax'}
                        {if $cart.subtotals.tax}
                            <div class="cart-summary-line">
                                <span class="label sub">{l s='%label%:' sprintf=['%label%' => $order.subtotals.tax.label] d='Shop.Theme.Global'}</span>
                                <span class="value sub">{$order.subtotals.tax.value}</span>
                            </div>
                        {/if}
                    {/block}
                </div>
            </div>
        </div>
    </section>
    {* END PLANATEC *}
{* PLANATEC
    <section id="content-hook_order_confirmation" class="card">
        <div class="card-block">
            <div class="row">
                <div class="col-md-12">

                    {block name='order_confirmation_header'}
                        <h3 class="h1 card-title">
                            <i class="material-icons rtl-no-flip done">&#xE876;</i>{l s='Your order is confirmed' d='Shop.Theme.Checkout'}
                        </h3>
                    {/block}

                    <p>
                        {l s='An email has been sent to your mail address %email%.' d='Shop.Theme.Checkout' sprintf=['%email%' => $customer.email]}
                        {if $order.details.invoice_url}
                          
    {* PLANATEC 
    {l
    s='You can also [1]download your invoice[/1]'
    d='Shop.Theme.Checkout'
    sprintf=[
    '[1]' => "<a href='{$order.details.invoice_url}'>",
    '[/1]' => "</a>"
    ]
    }
{/if}
</p>

{block name='hook_order_confirmation'}
{$HOOK_ORDER_CONFIRMATION nofilter}
{/block}

</div>
</div>
</div>
</section>*}
{/block}

{* PLANATEC *}
{block name='page_content_container'}
{/block}

{block name='page_footer_container'}
{/block}
{* END PLANATEC *}


{block name='page_content_container'}
    <section id="content" class="page-content page-order-confirmation card">
        <div class="card-block">
            <div class="row">

                {block name='order_confirmation_table'}
                    {include
                    file='checkout/_partials/order-confirmation-table.tpl'
                    products=$order.products
                    subtotals=$order.subtotals
                    totals=$order.totals
                    labels=$order.labels
                    add_product_link=false
                    }
                {/block}

                {block name='order_details'}
                    <div id="order-details" class="col-md-4">
                        <h3 class="h3 card-title">{l s='Order details' d='Shop.Theme.Checkout'}:</h3>
                        <ul>
                            <li id="order-reference-value">{l s='Order reference: %reference%' d='Shop.Theme.Checkout' sprintf=['%reference%' => $order.details.reference]}</li>
                            <li>{l s='Payment method: %method%' d='Shop.Theme.Checkout' sprintf=['%method%' => $order.details.payment]}</li>
                            {if !$order.details.is_virtual}
                                <li>
                                    {l s='Shipping method: %method%' d='Shop.Theme.Checkout' sprintf=['%method%' => $order.carrier.name]}
                                    <br>
                                    <em>{$order.carrier.delay}</em>
                                </li>
                            {/if}
                        </ul>
                    </div>
                {/block}

            </div>
        </div>
    </section>
    {block name='hook_payment_return'}
        {if ! empty($HOOK_PAYMENT_RETURN)}
            <section id="content-hook_payment_return" class="card definition-list">
                <div class="card-block">
                    <div class="row">
                        <div class="col-md-12">
                            {$HOOK_PAYMENT_RETURN nofilter}
                        </div>
                    </div>
                </div>
            </section>
        {/if}
    {/block}

    {block name='customer_registration_form'}
        {if $customer.is_guest}
            <div id="registration-form" class="card">
                <div class="card-block">
                    <h4 class="h4">{l s='Save time on your next order, sign up now' d='Shop.Theme.Checkout'}</h4>
                    {render file='customer/_partials/customer-form.tpl' ui=$register_form}
                </div>
            </div>
        {/if}
    {/block}

    {block name='hook_order_confirmation_1'}
        {hook h='displayOrderConfirmation1'}
    {/block}

    {block name='hook_order_confirmation_2'}
        <section id="content-hook-order-confirmation-footer">
            {hook h='displayOrderConfirmation2'}
        </section>
    {/block}
{/block}
*}