$(document).ready(init);
const santiVar = '{$js_def}';
function init() {
    clickNotification();
}

function clickNotification() {
    $('.correosNotificationItem').on('click', function(){        
        $('.inView').removeClass('inView');        
        $(this).addClass('inView');
        inView();
    });
}

function inView() {
    if ($('.correosNotificationItem.inView').lenght == 0) {
        return false;
    }

    const inView = $('.correosNotificationItem.inView');

    const inViewId = inView.attr('data-id');
    const inViewText = $('.correosNotificationItem.inView').text();

    const html = getComponentInView(inViewId, inViewText);
    $('.correosNotificationsRight').html($html);
    $('.correosNotificationsRight').css({
        'box-shadow': '0 4px 2px #CBCBCB',
        'border-radius': '8px',
        'border': '1px solid #CBCBCB'
    })
    processChceck();
}

function getComponentInView(id, text) {
    $html = `
        <form id="notificationForm" method="post">
            <input type="hidden" name="notificationId" value="${id}">
            <div class="notificationtext">
                ${text}
            </div>
            <div class="notificationCheck">
                <div id="notificationsSendForm" class="notificationsSendForm"></div>
                ${correos_inView_check}
            </div>
        </form>
    `.trim();
}

function processChceck() {
    $('#notificationsSendForm').on('click', function(){
        $(this).addClass('clicked');
        $('body, html').css('overflow', 'hidden');
        $('.notificationLoader').removeClass('d-none');
        $('#notificationForm').submit();
        //location.reload();
    });
}