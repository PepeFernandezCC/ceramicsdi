
{*
<div class="contact-rich">
    <h4>{l s='Contact Information' d='Shop.Theme.Global'}</h4>

    <div class="block contact-block-title">
        <span>{l s='Ceramic Connection Logistic' d='Shop.Theme.Global'}</span>
    </div>

    <div class="block row">
        <div class="col-md-1" style="padding-right: 10px"><i class="fas fa-map-marker-alt"></i></div>
        <div class="col-md-11">
            <div class="data">
                <div>Avenida Real de Extremadura, 9 Onda 12200</div>
                <div>Castellón, {l s='Spain' d='Shop.Theme.Global'}</div>
            </div>

            <div style="margin-top: 5px;">
                <div class="data">
                    <a style="text-decoration: underline;"
                    href="https://www.google.com/maps/place/Ceramic+Connection/@39.9523827,-0.1899342,17z/data=!3m1!4b1!4m6!3m5!1s0xd60099c1b0adb19:0xc3c8af6c0bbf8d47!8m2!3d39.9523827!4d-0.1877402!16s%2Fg%2F11m79wxtc_"
                    target="_blank">{l s='View on Google Maps' d='Shop.Theme.Global'}</a>
                </div>
            </div>
        </div>

    </div>

    <div class="block row" style="margin-top: 30px;margin-bottom:30px">
        <div class="col-md-1" style="padding-right: 10px"><i class="far fa-clock"></i></div>
        <div class="data col-md-11">
            <div class="contact-journey-title">
                <span>{l s='Horario de atención al cliente' d='Shop.Theme.Global'}{l s=': ' d='Shop.Theme.Catalog'}</span>
            </div>
              
            <div class="row">     
                <div class="col-md-6">{l s='From Monday to Friday' d='Shop.Theme.Global'}:</div>
                <div class="col-md-6">{l s='From 9:00 to 14:00' d='Shop.Theme.Global'}</div>
            </div>


        </div>
    </div>

    <div class="block contact-block-title" style="padding-bottom: 0">
        <span>{l s='Contact us in the language you prefer:' d='Shop.Theme.Global'}</span>
    </div>
    
    <div class="block">
        <div class="row">
            <div class="contact-assist-card col-md-6">
                <div style="display:flex">
                    <div style="padding-right:15px">
                        <img class="contact-flag" loading="lazy" src="/themes/child_classic/assets/img/web/spain.png" alt="spain flag"/>
                        <span class="span-country">ES</span>
                    </div>
                    <div >
                        <img class="contact-flag" loading="lazy" src="/themes/child_classic/assets/img/web/germany.png" alt="germany flag"/>
                        <span class="span-country">DE</span>
                    </div>
                </div>
                <div class="contact-email">
                    <span class="at-contact">@</span>
                    <span class="span-email">
                        <a href="mailto:info@ceramicconnection.es" style="font-size: 15px;">
                           info@ceramicconnection.es
                        </a>
                    </span>
                </div>
                <div style="padding-bottom: 5px">
                    <i class="fas fa-phone-alt contact-phone"></i>
                    <span class="contact-phone-number">
                        <a href="https://wa.me/34623240148" target="_BLANK">
                            +34 647 145 062
                        </a> 
                    </span>
                </div>
                <div >
                    <i class="fa-brands fa-whatsapp contact-whatsapp"></i>
                    <span class="contact-phone-number">
                        <a href="https://wa.me/34623240148" target="_BLANK">
                            +34 623 240 148
                        </a>
                    </span>
                </div>
            </div>
            <div class="contact-assist-card col-md-6">
                <div style="display:flex">
                    <div style="padding-right:15px">
                        <img class="contact-flag" loading="lazy" src="/themes/child_classic/assets/img/web/france.png" alt="france flag"/>
                        <span class="span-country">FR</span>
                    </div>
                    <div>
                        <img class="contact-flag" loading="lazy" src="/themes/child_classic/assets/img/web/united-kingdom.png" alt="united kingdom flag"/>
                        <span class="span-country">EN</span>
                    </div>
                </div>

                <div class="contact-email">
                    <span class="at-contact">@</span>
                    <span class="span-email">
                        <a href="mailto:info@ceramicconnection.es" style="font-size: 15px;">
                           info@ceramicconnection.es
                        </a>
                    </span>
                </div>
                <div style="padding-bottom: 5px">
                    <i class="fas fa-phone-alt contact-phone"></i>
                    <span class="contact-phone-number">
                        <a href="https://wa.me/34623240148" target="_BLANK">
                            +34 623 240 148
                        </a>
                    </span>
                </div>
                <div >
                    <i class="fa-brands fa-whatsapp contact-whatsapp"></i>
                    <span class="contact-phone-number">
                        <a href="https://wa.me/34623240148" target="_BLANK">
                            +34 623 240 148
                        </a>
                    </span>
                </div>
            </div>
        </div>

    </div>


    <div class="block m-auto-mobile" style="max-width: 50%">
        {hook h="displayWhatsAppChat" id_whatsappchat="4"}
    </div>


    {block name='ps_social_follow'}
        <div class="block" style="margin-top: 50px;">
            <div style="font-weight: 600; color: black; font-size: 1rem; margin-bottom: 5px;">{l s='Follow us' d='Shop.Theme.Global'}:</div>
            {include file="themes/child_classic/modules/ps_socialfollow/ps_socialfollow.tpl" social_links=$socialLinksFooter title=false}
        </div>
    {/block}
</div>

*}