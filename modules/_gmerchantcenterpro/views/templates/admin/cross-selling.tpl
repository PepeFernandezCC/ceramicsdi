{*
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

<div class="cross-selling-container">
    <header class="header">
        <div class="header-decoration"></div>
        <div class="header-blur header-blur-top"></div>
        <div class="header-blur header-blur-bottom"></div>

        <h1 class="header-title">
            {l s='Discover our' mod='gmerchantcenterpro'}
            <span class="header-title-highlight">
                {l s='Complete Suite' mod='gmerchantcenterpro'}
            </span>
            {l s='of' mod='gmerchantcenterpro'}
            <span class="header-title-highlight">
                {l s='PrestaShop Solutions' mod='gmerchantcenterpro'}
                <span class="header-title-highlight-dot"></span>
            </span> ✨
        </h1>

        <div class="header-content">
            <p>
                {l s='Explore our full range of PrestaShop modules designed to enhance every aspect of your online store. From marketing tools to payment solutions, we\'ve got everything you need to take your e-commerce business to the next level.' mod='gmerchantcenterpro'}
            </p>
        </div>
    </header>

    <div class="modules-grid">
        {assign var="moduleCount" value=0}
        {foreach from=$modules item=module}
            <a href="{$module.url|escape:'htmlall':'UTF-8'}" target="_blank" class="module-card {if $moduleCount >= 4}module-extra{/if}">
                <div class="module-image-container">
                    <img src="{$module.cover|escape:'htmlall':'UTF-8'}" alt="{$module.displayName|escape:'htmlall':'UTF-8'}" class="module-image">
                    {if isset($module.avgRate) && isset($module.nbRates) && $module.nbRates > 0}
                        <div class="module-rating">
                            <span class="module-rating-star">★</span>
                            <span class="module-rating-value">{$module.avgRate|escape:'htmlall':'UTF-8'}</span>
                            <span class="module-rating-count">({$module.nbRates|escape:'htmlall':'UTF-8'})</span>
                        </div>
                    {/if}
                    <div class="module-overlay">
                        <h3 class="module-title">{$module.displayName|escape:'htmlall':'UTF-8'}</h3>
                        <div class="module-description">
                            <p>{$module.description|escape:'htmlall':'UTF-8'}</p>
                        </div>
                        <div class="module-meta">
                            <span class="module-version">
                                <svg class="module-version-icon" style="width:1rem;height:1rem;animation:spin-slow 2s linear infinite" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                {l s='Version' mod='gmerchantcenterpro'} {$module.version|escape:'htmlall':'UTF-8'}
                            </span>
                            <span class="module-compatibility">PS {$module.compatibility_from|escape:'htmlall':'UTF-8'} - {$module.compatibility_to|escape:'htmlall':'UTF-8'}</span>
                        </div>
                        <div class="module-cta">
                            <span class="module-cta-button">
                                {l s='Discover Now →' mod='gmerchantcenterpro'}
                            </span>
                        </div>
                    </div>
                </div>
            </a>
            {assign var="moduleCount" value=$moduleCount+1}
        {/foreach}
    </div>

    {if $moduleCount > 4}
        <div class="show-more">
            <button class="show-more-button" onclick="showAllModules()">
                {l s='Show More' mod='gmerchantcenterpro'}
            </button>
        </div>

        <script>
            function showAllModules() {
                document.querySelectorAll('.module-extra').forEach(function(module, index) {
                    module.style.display = 'flex';
                    if (index === 0) {
                        setTimeout(() => {
                            module.scrollIntoView({ behavior: 'smooth' });
                        }, 100);
                    }
                });
                document.querySelector('.show-more').style.display = 'none';
            }
        </script>
    {/if}
</div>