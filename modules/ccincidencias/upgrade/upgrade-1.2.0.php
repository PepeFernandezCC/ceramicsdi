<?php
/**
 * 1.2.0: formulario restringido a clientes logueados, boton por pedido
 * en el historial de compras (reutiliza el hook
 * displayCustomerOrderWithdrawalButton) y validacion de propiedad del
 * pedido cuando se escribe la referencia a mano. No hay cambios de
 * esquema de base de datos, solo un hook nuevo que registrar.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_0($module)
{
    return $module->installOrderButtonHook();
}
