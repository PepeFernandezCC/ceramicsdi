<?php
// Incluye el entorno de Prestashop
include(dirname(__FILE__) . '/../config/config.inc.php');
include(dirname(__FILE__) . '/../init.php');

header('Content-Type: application/json');

// Obtén el ID del país desde la solicitud
$id_country = Tools::getValue('id_country');

if (!$id_country) {
    echo json_encode([]);
    exit;
}

// Llama al método sobrescrito para obtener las provincias
$provinces = State::getProvincesByCountry((int)$id_country);

// Devuelve las provincias como JSON
echo json_encode($provinces);
exit;