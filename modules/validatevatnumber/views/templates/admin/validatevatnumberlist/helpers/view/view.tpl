{*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code.
*
*  @author    Active Design <office@activedesign.ro>
*  @copyright 2018 Active Design
*  @license   LICENSE.txt
*}

<section class="dash_news panel col-lg-2">
    <h3><i class="icon-bar-chart"></i> {l s='Address Details' mod='validatevatnumber'}</h3>
    <section id="dash_pending" class="">
        {if $vat_number_status == 1}
            <p style="color:green;font-weight: bold;font-size: 18px;">{l s='This Vat Number is verified' mod='validatevatnumber'}</p>
        {else}
            <p style="color:red;font-weight: bold;font-size: 18px;">{l s='This Vat Number is not verified' mod='validatevatnumber'}</p>
        {/if}
        <ul class="data_list">
            {if $address->firstname}
                <li>
                    <span class="data_label">{l s='Firstname:' mod='validatevatnumber'} </span>
                    <span class="data_value">
                              <span id="pending_orders">{$address->firstname|escape:"htmlall":"UTF-8"}</span>
                         </span>
                </li>
            {/if}
            {if $address->lastname}
                <li>
                    <span class="data_label">{l s='Lastname:' mod='validatevatnumber'} </span>
                    <span class="data_value">
                              <span id="pending_orders">{$address->lastname|escape:"htmlall":"UTF-8"}</span>
                         </span>
                </li>
            {/if}
            {if $address->phone}
                <li>
                    <span class="data_label">{l s='Phone:' mod='validatevatnumber'} </span>
                    <span class="data_value">
                              <span id="pending_orders">{$address->phone|escape:"htmlall":"UTF-8"}</span>
                         </span>
                </li>
            {/if}
            {if $address->phone_mobile}
                <li>
                    <span class="data_label">{l s='Phone Mobile:' mod='validatevatnumber'} </span>
                    <span class="data_value">
                              <span id="pending_orders">{$address->phone_mobile|escape:"htmlall":"UTF-8"}</span>
                         </span>
                </li>
            {/if}
            {if $address->country}
                <li>
                    <span class="data_label">{l s='Country:' mod='validatevatnumber'} </span>
                    <span class="data_value">
                              <span id="return_exchanges">{$address->country|escape:"htmlall":"UTF-8"}</span>
                         </span>
                </li>
            {/if}
            {if $address->city}
                <li>
                    <span class="data_label">{l s='City:' mod='validatevatnumber'} </span>
                    <span class="data_value">
                              <span id="return_exchanges">{$address->city|escape:"htmlall":"UTF-8"}</span>
                         </span>
                </li>
            {/if}
            {if $address->phone}
                <li>
                    <span class="data_label">{l s='Phone:' mod='validatevatnumber'} </span>
                    <span class="data_value">
                              <span id="abandoned_cart">{$address->phone|escape:"htmlall":"UTF-8"}</span>
                         </span>
                </li>
            {/if}
            {if $address->vat_number}
                <li>
                    <span class="data_label">{l s='Vat Number:' mod='validatevatnumber'} </span>
                    <span class="data_value">
                              <span id="products_out_of_stock">{$address->vat_number|escape:"htmlall":"UTF-8"}</span>
                         </span>
                </li>
            {/if}
            {if $address->address1}
                <li>
                    <span class="data_label">{l s='Address 1:' mod='validatevatnumber'} </span>
                    <span class="data_value">
                              <span id="products_out_of_stock">{$address->address1|escape:"htmlall":"UTF-8"}</span>
                         </span>
                </li>
            {/if}
            {if $address->address2}
                <li>
                    <span class="data_label">{l s='Address 2:' mod='validatevatnumber'} </span>
                    <span class="data_value">
                              <span id="products_out_of_stock">{$address->address2|escape:"htmlall":"UTF-8"}</span>
                         </span>
                </li>
            {/if}
            {if $address->postcode}
                <li>
                    <span class="data_label">{l s='Postcode:' mod='validatevatnumber'} </span>
                    <span class="data_value">
                              <span id="products_out_of_stock">{$address->postcode|escape:"htmlall":"UTF-8"}</span>
                         </span>
                </li>
            {/if}
        </ul>
    </section>
</section>