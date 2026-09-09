<?php
/**
 * Upgrade script para versión 2.5.25
 * Asegura que los directorios de archivos tengan los permisos correctos
 * y que los tabs no se borren innecesariamente en actualizaciones
 */

if (!defined('_PS_VERSION_'))
    exit;

function upgrade_module_2_5_25($module) {
    
    // Asegurar permisos correctos en directorios de archivos
    $filesDir = dirname(__FILE__) . '/../files';
    $directories = array(
        $filesDir,
        $filesDir . '/deliveries_invoices',
        $filesDir . '/deliveries_labels',
        $filesDir . '/deliveries_notes',
        $filesDir . '/deliveries_xml',
        $filesDir . '/logs'
    );

    foreach ($directories as $dir) {
        if (is_dir($dir)) {
            @chmod($dir, 0775);
        } else {
            @mkdir($dir, 0775, true);
        }
    }

    return $module;
}
