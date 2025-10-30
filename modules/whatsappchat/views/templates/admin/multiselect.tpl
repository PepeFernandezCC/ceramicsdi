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
<script src="../modules/whatsappchat/views/js/plugin/ui.multiselect.js?time={$smarty.now|escape:'htmlall':'UTF-8'}"></script>
<script type="text/javascript">
{if isset($customers_available)}
    var customers_append = [];
    {foreach $customers_available as $c}
        customers_append.push('<option value="{$c['id_customer']|escape:'htmlall':'UTF-8'}">{$c['email']|escape:'htmlall':'UTF-8'}</option>');
    {/foreach}
    $("select[name='customers[]']").append(customers_append.join(''));
{/if}

{if isset($customers_selected)}
    var customers_selected = "{$customers_selected|escape:'htmlall':'UTF-8'}";
    var customers_sel_array = customers_selected.split(";");
    for (i = 0; i < customers_sel_array.length; ++i) {
        setSelectedIndex($("select[name='customers[]']")[0], customers_sel_array[i]);
    }
{/if}
$(document).ready(function() {
    $(".multiple_select").each(function() {
        $(this).multiselect();
    });
});
function setSelectedIndex(s, v) {
    for ( var i = 0; i < s.options.length; i++ ) {
        if ( s.options[i].value == v ) {
            s.options[i].selected = true;
            return;
        }
    }
}
</script>