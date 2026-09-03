<?php
/**
 * 1.3.0: nuevo campo "require_photos" en el tipo de incidencia. Si se
 * activa desde el Admin, el apartado de fotos del formulario publico
 * pasa a ser obligatorio para ese tipo (ver CcIncidenciasTipo).
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3_0($module)
{
    return Db::getInstance()->execute(
        'ALTER TABLE `' . _DB_PREFIX_ . 'ccincidencias_tipo`
         ADD COLUMN `require_photos` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `active`'
    );
}
