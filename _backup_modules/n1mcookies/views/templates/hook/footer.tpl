<!-- n1mcookies/views/templates/hook/footer.tpl -->

<script>
{literal}
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('consent', 'default', {
    'ad_storage': 'denied',
    'ad_user_data': 'denied',
    'ad_personalization': 'denied',
    'analytics_storage': 'denied'
});
{/literal}
</script>

<script async src="https://www.googletagmanager.com/gtag/js?id={$n1mcookies_code}"></script>

<script>
{literal}
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '{/literal}{$n1mcookies_code}{literal}');
{/literal}
</script>

{if $n1mcookies_analytics}
<!-- Google Analytics Code -->
<script type="text/plain" data-cookie-consent="tracking">
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', '{$n1mcookies_analytics}', 'auto');
ga('send', 'pageview');
</script>
<!-- End Google Analytics Code -->
{/if}

<script type="text/javascript" src="{$module_dir}js/cookie-consent.js"></script>

<script type="text/javascript" charset="UTF-8">
{literal}
document.addEventListener('DOMContentLoaded', function () {
    cookieconsent.run({
        "notice_banner_type":"simple",
        "consent_type":"express",
        "palette":"{/literal}{$n1mcookies_palette}{literal}",
        "language":"{/literal}{$n1mcookies_language}{literal}",
        "page_load_consent_levels":["strictly-necessary"],
        "notice_banner_reject_button_hide":false,
        "preferences_center_close_button_hide":false,
        "page_refresh_confirmation_buttons":false,
        "callbacks": {
            "scripts_specific_loaded": (level) => {
                switch(level) {
                    case 'targeting':
                        gtag('consent', 'update', {
                            'ad_storage': 'granted',
                            'ad_user_data': 'granted',
                            'ad_personalization': 'granted',
                            'analytics_storage': 'granted'
                        });
                        break;
                }
            }
        },
        "callbacks_force": true
    });
});
{/literal}
</script>
