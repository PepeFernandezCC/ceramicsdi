{block name='ps_social_follow'}
    <div class="ps-social-follow">
        {if $title|default:true}
            <p>{l s='Follow us' d='Modules.Socialfollow.Shop'}</p>
        {/if}
        <ul>
            {foreach from=$social_links item='social_link'}
                <li>
                    <a class="my-icon" href="{$social_link.url}" title="{$social_link.label}" target="_blank">
                        <i class="fa fa-{$social_link.class}"></i>
                    </a>
                </li>
            {/foreach}
        </ul>
    </div>
{/block}
