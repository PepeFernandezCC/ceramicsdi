<?php
/**
 * 2007-2024 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 */

$sql = [];

// NOTA: Ya no creamos tabla para preparación por producto, usamos el campo nativo delivery_in_stock

// Tabla para almacenar plazos de envío por provincia
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'shipping_calculator_delays` (
    `id_delay` int(11) NOT NULL AUTO_INCREMENT,
    `province_code` varchar(10) NOT NULL,
    `province_name` varchar(255) NOT NULL,
    `delivery_days` int(11) UNSIGNED DEFAULT 5,
    `delivery_days_min` int(11) UNSIGNED DEFAULT NULL,
    `delivery_days_max` int(11) UNSIGNED DEFAULT NULL,
    `shipping_cost` decimal(20,6) DEFAULT 0.000000,
    PRIMARY KEY (`id_delay`),
    UNIQUE KEY `province_code` (`province_code`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

// NO insertar provincias por defecto - el usuario las configurará manualmente
// Las provincias se añadirán automáticamente cuando se guarden desde el panel de administración

return true;

