<div class="notificationContent">
    <div class="correosImg">
        <img src="{$img}" alt="correos">
    </div>
    <div class="notificationsMsgs">
        {l s='Yo have ' mod='correosoficial'}
        <span class="notificationsCount">{$notifications}</span>
        {l s=' notifications without read in the Correosoficial module' mod='correosoficial'}
    </div>
    <div class="notificationsButton">
        <a href="{$link}">
            <button class="btn btn-primary co_primary_button">{l s='Go to notifications' mod='correosoficial'}</button>
        </a>
    </div>
</div>