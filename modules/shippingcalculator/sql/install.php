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

// Insertar provincias argentinas por defecto
$provinces = [
    ['C', 'Ciudad Autónoma de Buenos Aires', 3],
    ['B', 'Buenos Aires', 5],
    ['K', 'Catamarca', 7],
    ['H', 'Chaco', 7],
    ['U', 'Chubut', 8],
    ['X', 'Córdoba', 5],
    ['W', 'Corrientes', 6],
    ['E', 'Entre Ríos', 6],
    ['P', 'Formosa', 7],
    ['Y', 'Jujuy', 8],
    ['L', 'La Pampa', 6],
    ['F', 'La Rioja', 7],
    ['M', 'Mendoza', 6],
    ['N', 'Misiones', 7],
    ['Q', 'Neuquén', 8],
    ['R', 'Río Negro', 8],
    ['A', 'Salta', 8],
    ['J', 'San Juan', 7],
    ['D', 'San Luis', 6],
    ['Z', 'Santa Cruz', 10],
    ['S', 'Santa Fe', 5],
    ['G', 'Santiago del Estero', 7],
    ['V', 'Tierra del Fuego', 12],
    ['T', 'Tucumán', 6],
];

foreach ($provinces as $province) {
    $insert = 'INSERT INTO `' . _DB_PREFIX_ . 'shipping_calculator_delays`
               (province_code, province_name, delivery_days)
               VALUES ("' . pSQL($province[0]) . '", "' . pSQL($province[1]) . '", ' . (int)$province[2] . ')
               ON DUPLICATE KEY UPDATE delivery_days = ' . (int)$province[2];
    Db::getInstance()->execute($insert);
}

return true;

