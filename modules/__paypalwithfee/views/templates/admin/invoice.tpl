{*
* 2020 4webs
*
* DEVELOPED By 4webs Prestashop Platinum Partner
*
* @author    4webs
* @copyright 4webs 2017
* @version 5.1.4
* @category payment_gateways
* @license 4webs
*}
{$style_tab|escape:'html':'UTF-8'}


<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
    <!-- Invoicing -->
    <tr>
        <td colspan="12">

            {$addresses_tab|escape:'html':'UTF-8'}

        </td>
    </tr>

    <tr>
        <td colspan="12" height="30">&nbsp;</td>
    </tr>

    <!-- TVA Info -->
    <tr>
        <td colspan="12">

            {$summary_tab|escape:'html':'UTF-8'}

        </td>
    </tr>

    <tr>
        <td colspan="12" height="20">&nbsp;</td>
    </tr>

    <!-- Product -->
    <tr>
        <td colspan="12">

            {$product_tab|escape:'html':'UTF-8'}

        </td>
    </tr>

    <tr>
        <td colspan="12" height="10">&nbsp;</td>
    </tr>

    <!-- TVA -->
    <tr>
        <!-- Code TVA -->
        <td colspan="6" class="left">

            {$tax_tab|escape:'html':'UTF-8'}

        </td>
        <td colspan="1">&nbsp;</td>
        <!-- Calcule TVA -->
        <td colspan="5" rowspan="5" class="right">

            {$total_tab|escape:'html':'UTF-8'}

        </td>
    </tr>

    {$note_tab|escape:'html':'UTF-8'}

    <tr>
        <td colspan="12" height="10">&nbsp;</td>
    </tr>

    <tr>
        <td colspan="6" class="left">

            {$payment_tab|escape:'html':'UTF-8'}

        </td>
        <td colspan="1">&nbsp;</td>
    </tr>

    <tr>
        <td colspan="6" class="left">

            {$shipping_tab|escape:'html':'UTF-8'}

        </td>
        <td colspan="1">&nbsp;</td>
    </tr>

    <tr>
        <td colspan="12" height="10">&nbsp;</td>
    </tr>

    <tr>
        <td colspan="7" class="left small">

            <table>
                <tr>
                    <td>
                        <p>{$legal_free_text|escape:'html':'UTF-8'|nl2br}</p>
                    </td>
                </tr>
            </table>

        </td>
    </tr>

    <!-- Hook -->
    {if isset($HOOK_DISPLAY_PDF)}
        <tr>
            <td colspan="12" height="30">&nbsp;</td>
        </tr>

        <tr>
            <td colspan="2">&nbsp;</td>
            <td colspan="10">
                {$HOOK_DISPLAY_PDF|escape:'html':'UTF-8'}
            </td>
        </tr>
    {/if}

</table>
