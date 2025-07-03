<?php
/* Smarty version 3.1.48, created on 2025-06-27 08:09:45
  from '/var/www/html/modules/correosoficial/vendor/ecommerce_common_lib/services/preregistro/preregistro-envio.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_685e35a9a6dbd1_76403223',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'aec495e633e09ccc0b0dc1a66e036e65828ccd0a' => 
    array (
      0 => '/var/www/html/modules/correosoficial/vendor/ecommerce_common_lib/services/preregistro/preregistro-envio.tpl',
      1 => 1741000686,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_685e35a9a6dbd1_76403223 (Smarty_Internal_Template $_smarty_tpl) {
?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://www.correos.es/iris6/services/preregistroetiquetas">
<soapenv:Header/>
    <soapenv:Body>
        <PreregistroEnvio>
            <CodEtiquetador><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['CorreosKey'], ENT_QUOTES, 'UTF-8', true);?>
</CodEtiquetador>
            <ModDevEtiqueta>2</ModDevEtiqueta>
            <TotalBultos>1</TotalBultos>
            <CanalOrigen><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['source_channel'], ENT_QUOTES, 'UTF-8', true);?>
</CanalOrigen>
            <Remitente>
                <Identificacion>
                    <?php if ($_smarty_tpl->tpl_vars['shipping_data']->value['carrier_code'] === 'S0148') {?>
                    <Nombre><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['sender_name'], ENT_QUOTES, 'UTF-8', true);?>
</Nombre>
                    <Apellido1></Apellido1>
                    <Apellido2></Apellido2>
                    <?php } else { ?>
                    <Empresa><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['sender_name'], ENT_QUOTES, 'UTF-8', true);?>
</Empresa>
                    <PersonaContacto><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['sender_contact'], ENT_QUOTES, 'UTF-8', true);?>
</PersonaContacto>
                    <?php }?>
                    <Nif><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['sender_nif_cif'], ENT_QUOTES, 'UTF-8', true);?>
</Nif> 
                </Identificacion>
                <DatosDireccion>
                    <Direccion><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['sender_address'], ENT_QUOTES, 'UTF-8', true);?>
</Direccion>
                    <Localidad><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['sender_city'], ENT_QUOTES, 'UTF-8', true);?>
</Localidad>
                </DatosDireccion>
                <CP><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['sender_cp'], ENT_QUOTES, 'UTF-8', true);?>
</CP>
                <Telefonocontacto><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['sender_phone'], ENT_QUOTES, 'UTF-8', true);?>
</Telefonocontacto>
                <Email><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['sender_email'], ENT_QUOTES, 'UTF-8', true);?>
</Email>
                <DatosSMS>
                    <NumeroSMS><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['sender_phone'], ENT_QUOTES, 'UTF-8', true);?>
</NumeroSMS>
                    <Idioma><?php if ($_smarty_tpl->tpl_vars['shipping_data']->value['sender_phone']) {?>1<?php }?></Idioma>
                </DatosSMS>
            </Remitente>
            <Destinatario>
                <Identificacion>
                    <?php if (empty($_smarty_tpl->tpl_vars['shipping_data']->value['customer_company'])) {?>
                    <Nombre><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['customer_firstname'], ENT_QUOTES, 'UTF-8', true);?>
</Nombre>
                    <Apellido1><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['customer_lastname1'], ENT_QUOTES, 'UTF-8', true);?>
</Apellido1>
                    <Apellido2><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['customer_lastname2'], ENT_QUOTES, 'UTF-8', true);?>
</Apellido2>
                    <?php } elseif (!empty($_smarty_tpl->tpl_vars['shipping_data']->value['customer_company']) && $_smarty_tpl->tpl_vars['shipping_data']->value['company'] == 'Correos') {?>
                    <Empresa><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['customer_company'], ENT_QUOTES, 'UTF-8', true);?>
</Empresa>
                    <?php }?>

                    <?php if (empty($_smarty_tpl->tpl_vars['shipping_data']->value['customer_contact'])) {?>
                        <PersonaContacto><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['customer_firstname'], ENT_QUOTES, 'UTF-8', true);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['customer_lastname1'], ENT_QUOTES, 'UTF-8', true);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['customer_lastname2'], ENT_QUOTES, 'UTF-8', true);?>
</PersonaContacto>
                    <?php } else { ?>
                        <PersonaContacto><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['customer_contact'], ENT_QUOTES, 'UTF-8', true);?>
 </PersonaContacto>
                    <?php }?>
                    <Nif><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['customer_dni'], ENT_QUOTES, 'UTF-8', true);?>
</Nif>
                </Identificacion>
                <DatosDireccion>
                    <Direccion><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['delivery_address'], ENT_QUOTES, 'UTF-8', true);?>
</Direccion>
                    <Localidad><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['delivery_city'], ENT_QUOTES, 'UTF-8', true);?>
</Localidad>
                    <Provincia><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['delivery_state'], ENT_QUOTES, 'UTF-8', true);?>
</Provincia>
                </DatosDireccion>
                <CP><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['delivery_postcode'], ENT_QUOTES, 'UTF-8', true);?>
</CP>
                <ZIP><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['delivery_zip'], ENT_QUOTES, 'UTF-8', true);?>
</ZIP>
                <Pais><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['delivery_country_iso'], ENT_QUOTES, 'UTF-8', true);?>
</Pais>
                <Telefonocontacto><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['phone'], ENT_QUOTES, 'UTF-8', true);?>
</Telefonocontacto>
                <Email><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['customer_email'], ENT_QUOTES, 'UTF-8', true);?>
</Email>
                <DatosSMS>
                    <NumeroSMS><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['phone_mobile_sms'], ENT_QUOTES, 'UTF-8', true);?>
</NumeroSMS>
                    <Idioma><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['mobile_lang'], ENT_QUOTES, 'UTF-8', true);?>
</Idioma>
                </DatosSMS>
            </Destinatario>
            <Envio>
                <CodProducto><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['carrier_code'], ENT_QUOTES, 'UTF-8', true);?>
</CodProducto>
                <ReferenciaCliente><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['order_reference'], ENT_QUOTES, 'UTF-8', true);?>
</ReferenciaCliente>
                <?php if (PLATFORM == 'PS') {?> 
                <ReferenciaCliente3>MODULO_<?php echo PLATFORM;?>
_<?php echo VERSION;?>
/<?php echo CORREOS_OFICIAL_VERSION;?>
</ReferenciaCliente3>
                <?php }?>
                <?php if (PLATFORM == 'WP') {?>
                    <ReferenciaCliente3>MODULO_<?php echo MODULE;?>
_<?php echo VERSION;?>
/<?php echo CORREOS_OFICIAL_VERSION;?>
</ReferenciaCliente3>
                <?php }?>
                <TipoFranqueo>FP</TipoFranqueo>
                <ModalidadEntrega><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['delivery_mode'], ENT_QUOTES, 'UTF-8', true);?>
</ModalidadEntrega>
                <?php if ($_smarty_tpl->tpl_vars['shipping_data']->value['delivery_mode'] == 'LS') {?><OficinaElegida><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['id_office'], ENT_QUOTES, 'UTF-8', true);?>
</OficinaElegida><?php }?>
                <?php if ($_smarty_tpl->tpl_vars['shipping_data']->value['delivery_mode'] == 'CP') {?><CodigoHomepaq><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['id_citypaq'], ENT_QUOTES, 'UTF-8', true);?>
</CodigoHomepaq><?php }?>
                <Pesos>
                    <Peso>
                        <TipoPeso>R</TipoPeso>
                        <Valor><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['weight'], ENT_QUOTES, 'UTF-8', true);?>
</Valor>
                    </Peso>
                    <?php if ($_smarty_tpl->tpl_vars['shipping_data']->value['has_size']) {?>
                    <Peso>
                        <TipoPeso>V</TipoPeso>
                        <Valor><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['v_weight'], ENT_QUOTES, 'UTF-8', true);?>
</Valor>
                    </Peso>
                    <?php }?>
                </Pesos>
                <?php if ($_smarty_tpl->tpl_vars['shipping_data']->value['has_size']) {?>
                <Largo><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['long'], ENT_QUOTES, 'UTF-8', true);?>
</Largo>
                <Alto><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['height'], ENT_QUOTES, 'UTF-8', true);?>
</Alto>
                <Ancho><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['width'], ENT_QUOTES, 'UTF-8', true);?>
</Ancho>
                <?php }?>
                <ValoresAnadidos>
                    <?php if ($_smarty_tpl->tpl_vars['shipping_data']->value['seguro'] == 1) {?>
                    <ImporteSeguro><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['insurance_value'], ENT_QUOTES, 'UTF-8', true);?>
</ImporteSeguro>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['shipping_data']->value['contra_reembolso'] == 1) {?>
                    <Reembolso>
                        <TipoReembolso><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['cashondelivery_type'], ENT_QUOTES, 'UTF-8', true);?>
</TipoReembolso>
                        <Importe><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['cashondelivery_value'], ENT_QUOTES, 'UTF-8', true);?>
</Importe>
                        <NumeroCuenta><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['cashondelivery_bankac'], ENT_QUOTES, 'UTF-8', true);?>
</NumeroCuenta>
                    </Reembolso>
                    <?php }?>
                                        <TextoAdicional><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['texto_adicional'], ENT_QUOTES, 'UTF-8', true);?>
</TextoAdicional>
                    <EntregaconRecogida>N</EntregaconRecogida>
                    <IndImprimirEtiqueta>N</IndImprimirEtiqueta>
                </ValoresAnadidos>
                <?php if ($_smarty_tpl->tpl_vars['shipping_data']->value['require_customs_doc'] == 1) {?>
                <Aduana>
                    <TipoEnvio>2</TipoEnvio>
                    <EnvioComercial>S</EnvioComercial>
                    <FacturaSuperiora500>N</FacturaSuperiora500>
                    <DUAConCorreos>N</DUAConCorreos>
                    <RefAduaneraExpedidor><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['customs_consignor_reference'], ENT_QUOTES, 'UTF-8', true);?>
</RefAduaneraExpedidor>
                    <DescAduanera>

                        <?php ob_start();
echo intval((isset($_smarty_tpl->tpl_vars['__smarty_foreach_shipping']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_shipping']->value['index'] : null))+1;
$_prefixVariable1 = ob_get_clean();
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['shipping_data']->value['customs_descs'][$_prefixVariable1], 'descs', false, NULL, 'outer', array (
));
$_smarty_tpl->tpl_vars['descs']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['descs']->value) {
$_smarty_tpl->tpl_vars['descs']->do_else = false;
?>
                               <DATOSADUANA>
                                   <Cantidad><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['descs']->value['unidades'], ENT_QUOTES, 'UTF-8', true);?>
</Cantidad>
                                   <Descripcion><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['descs']->value['descripcion_aduanera'], ENT_QUOTES, 'UTF-8', true);?>
</Descripcion>
                                   <NTarifario><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['descs']->value['numero_tarifario'], ENT_QUOTES, 'UTF-8', true);?>
</NTarifario>
                                   <Pesoneto><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['descs']->value['weight'], ENT_QUOTES, 'UTF-8', true);?>
</Pesoneto>
                                   <Valorneto><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['descs']->value['valor_neto'], ENT_QUOTES, 'UTF-8', true);?>
</Valorneto>
                               </DATOSADUANA>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        
                    </DescAduanera>
                </Aduana>
                <?php }?>
                <Observaciones1><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['observaciones1'], ENT_QUOTES, 'UTF-8', true);?>
</Observaciones1>
                <Observaciones2><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['observaciones2'], ENT_QUOTES, 'UTF-8', true);?>
</Observaciones2>
                <InstruccionesDevolucion>D</InstruccionesDevolucion>
            </Envio>
        </PreregistroEnvio>
    </soapenv:Body>
</soapenv:Envelope><?php }
}
