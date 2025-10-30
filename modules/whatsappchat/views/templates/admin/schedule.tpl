{**
* WhatsApp Chat
*
* ISC License
*
* Copyright (c) 2023 idnovate.com
* idnovate is a Registered Trademark & Property of idnovate.com, innovación y desarrollo SCP
*
* Permission to use, copy, modify, and/or distribute this software for any
* purpose with or without fee is hereby granted, provided that the above
* copyright notice and this permission notice appear in all copies.
*
* THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
* REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
* AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
* INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
* LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
* OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
* PERFORMANCE OF THIS SOFTWARE.
*
* @author    idnovate
* @copyright 2024 idnovate
* @license   https://www.isc.org/licenses/ https://opensource.org/licenses/ISC ISC License
*}

<input type="hidden" id="schedule" name="schedule" value=""/>
<div id="scheduleContainer"></div>
<script>
    var businessHoursManager = $("#scheduleContainer").businessHours({
        {if $schedule != ''}operationTime:{$schedule|escape:'quotes':'UTF-8'},{/if}
        weekdays:[{l s='\'Monday\',\'Tuesday\',\'Wednesday\',\'Thursday\',\'Friday\',\'Saturday\',\'Sunday\'' mod='whatsappchat'}],
        defaultOperationTimeFrom:"00:00",
        defaultOperationTimeTill:"23:59",
        postInit:function(){
            $('.operationTimeFrom, .operationTimeTill').timepicker({
            'timeFormat': 'H:i',
            'step': 15
            });
        },
        dayTmpl:'<div class="dayContainer" style="width: 80px;">' +
            '<div data-original-title="" class="colorBox"><input type="checkbox" class="invisible operationState"></div>' +
            '<div class="weekday"></div>' +
            '<div class="operationDayTimeContainer">' +
                '<div class="operationTime input-group"><span class="input-group-addon"><i class="icon icon-sun"></i></span><input type="text" name="startTime" class="mini-time form-control operationTimeFrom" value=""></div>' +
                '<div class="operationTime input-group"><span class="input-group-addon"><i class="icon icon-moon"></i></span><input type="text" name="endTime" class="mini-time form-control operationTimeTill" value=""></div>' +
                '</div></div>'
    });
    $('document').ready(function() {
        {if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
        $("#desc-whatsappchatblock-save, #desc-whatsappchatblock_agent-save").on("click", function() {
            $("input#schedule").val(JSON.stringify(businessHoursManager.serialize()));
        });
        $("div#scheduleContainer").on("click", function() {
            $("input#schedule").val("");
            $("input#schedule").val(JSON.stringify(businessHoursManager.serialize()));
        });
        $("input.mini-time").each(function() {
            $(this).change(function() {
                $("input#schedule").val("");
                $("input#schedule").val(JSON.stringify(businessHoursManager.serialize()));
            });
        });
        var eventList = $._data($("#desc-whatsappchatblock-save")[0], "events");
        eventList.click.unshift(eventList.click.pop());
        {else}
        $("#whatsappchatblock_form_submit_btn," +
            "#whatsappchatblock_form_submit_btn_4," +
            "button[name='submitAddwhatsappchatblock']," +
            "button[name='submitAddwhatsappchatblock_agent']," +
            "#whatsappchatblock_agent_form_submit_btn"
        ).on("click", function() {
            $("input#schedule").val(JSON.stringify(businessHoursManager.serialize()));
        });
        {/if}
    });
</script>
