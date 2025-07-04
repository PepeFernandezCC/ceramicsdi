<?php
/* Smarty version 3.1.48, created on 2025-06-30 09:09:22
  from '/var/www/html/modules/correosoficial/vendor/ecommerce_common_lib/services/preregistro/cancelar-envio.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_68623822d2f8c0_76352176',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8c405e6e53aa383e87be47c9222b5822f38689b4' => 
    array (
      0 => '/var/www/html/modules/correosoficial/vendor/ecommerce_common_lib/services/preregistro/cancelar-envio.tpl',
      1 => 1741000686,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68623822d2f8c0_76352176 (Smarty_Internal_Template $_smarty_tpl) {
?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://www.correos.es/iris6/services/preregistroetiquetas">
    <soapenv:Header ></soapenv:Header>
    <soapenv:Body>
        <PeticionAnular> 
        <IdiomaErrores><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['idioma'], ENT_QUOTES, 'UTF-8', true);?>
</IdiomaErrores>
        <codCertificado><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shipping_data']->value['codCertificado'], ENT_QUOTES, 'UTF-8', true);?>
</codCertificado>
        </PeticionAnular>
    </soapenv:Body>
</soapenv:Envelope><?php }
}
