{*
* NOTA SOBRE LA LICENCIA DE USO DEL SOFTWARE
*
* El uso de este software est� sujeto a las Condiciones de uso de software que
* se incluyen en el paquete en el documento "Aviso Legal.pdf". Tambi�n puede
* obtener una copia en la siguiente url:
* http://www.redsys.es/wps/portal/redsys/publica/areadeserviciosweb/descargaDeDocumentacionYEjecutables
*
* Redsys es titular de todos los derechos de propiedad intelectual e industrial
* del software.
*
* Quedan expresamente prohibidas la reproducci�n, la distribuci�n y la
* comunicaci�n p�blica, incluida su modalidad de puesta a disposici�n con fines
* distintos a los descritos en las Condiciones de uso.
*
* Redsys se reserva la posibilidad de ejercer las acciones legales que le
* correspondan para hacer valer sus derechos frente a cualquier infracci�n de
* los derechos de propiedad intelectual y/o industrial.
*
* Redsys Servicios de Procesamiento, S.L., CIF B85955367
*}
{if $status == 'ok'}
    {* PLANATEC *}
    <p>
        <strong>{l s='Your order on %shop% is complete.' sprintf=['%shop%' => $shop.name] d='Shop.Theme.Checkout'}</strong>
    </p>
    <div class="payment-info">
        <div class="col-xs-6">{l s='Payment amount.' mod='redsys'}</div>
        <div class="col-xs-6">{$total_to_pay|escape:'htmlall'}</div>
    </div>
    <p>{l s='An email has been sent to you with this information.' mod='redsys'}</p>
    <p>{l s='If you have questions, comments or problems, please contact our experienced' mod='redsys'} <a
                style="text-decoration: underline;"
                href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='customer service department.' mod='redsys'}</a>
    </p>
    {* END PLANATEC *}
{else}
    <p class="warning">
        {l s='We have noticed that there is a problem with your order. If you think this is an error, you can contact our' mod='redsys'}
        <a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='customer service department.' mod='redsys'}</a>.
    </p>
{/if}
