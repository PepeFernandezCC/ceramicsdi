<?php
/**
 * 2007-2024 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * 
 * Script de migración para añadir campos de horquilla de días de envío
 */

// Verificar que los campos no existan ya
$table = _DB_PREFIX_ . 'shipping_calculator_delays';

// Verificar si la tabla existe
$table_exists = Db::getInstance()->executeS('SHOW TABLES LIKE "' . $table . '"');

if (empty($table_exists)) {
    // La tabla no existe, se creará en install.php
    return true;
}

// Verificar si los campos ya existen
$columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . $table . '`');
$column_names = array_column($columns, 'Field');

$sql_queries = [];

// Añadir campo delivery_days_min si no existe
if (!in_array('delivery_days_min', $column_names)) {
    $sql_queries[] = 'ALTER TABLE `' . $table . '` 
                      ADD COLUMN `delivery_days_min` int(11) UNSIGNED DEFAULT NULL 
                      AFTER `delivery_days`';
}

// Añadir campo delivery_days_max si no existe
if (!in_array('delivery_days_max', $column_names)) {
    $sql_queries[] = 'ALTER TABLE `' . $table . '` 
                      ADD COLUMN `delivery_days_max` int(11) UNSIGNED DEFAULT NULL 
                      AFTER `delivery_days_min`';
}

// Ejecutar las consultas
foreach ($sql_queries as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

return true;

