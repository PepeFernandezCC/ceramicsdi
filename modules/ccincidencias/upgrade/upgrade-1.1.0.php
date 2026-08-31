<?php
/**
 * El modulo ya estaba instalado (solo con la tabla ccincidencias_log)
 * cuando se añadio el CRUD de tipos de incidencia. install() no se
 * vuelve a ejecutar solo, asi que esta es la que crea las tablas
 * ccincidencias_tipo / ccincidencias_tipo_lang, siembra los 6 tipos
 * originales del PDF y da de alta la pestaña de administracion.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_1_0($module)
{
    return $module->installDb()
        && $module->seedDefaultTipos()
        && $module->installAdminTab();
}
