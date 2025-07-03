{include file='./header.tpl'}
<h2 class="correosNotificationsTitle pt-4">{l s='Messages center' mod='correosoficial'}</h2>
{if $notifications}
    <div class="correosNotificationsContent">
        <div class="correosNotificationsLeft">
            {foreach from=$notifications item=item}
                <div class="correosNotificationItem" data-id="{$item->notificationId}">
                    <p>{$item->notificationText}</p>
                </div>
            {/foreach}
        </div>
        <div class="correosNotificationsRight">
            {l s='Click on any notification to see it in this space in more detail' mod='correosoficial'}
        </div>
    </div>
    <div class="notificationLoader d-none">
        <img src="{$co_base_dir}/views/img/ajax-loader.gif" alt="loader">
    </div>
{else}
    <div class="correosNotificationsError">
        {l s='You don\'t have any notification' mod='correosoficial'}
    </div>
{/if}