<?php

 /**
 * This program is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program.
 * If not, see https://www.gnu.org/licenses/.
 */

// campo sender_phone ahora permite NULL
$tableSenders = CorreosOficialUtils::getPrefix() . "correos_oficial_senders";
Db::getInstance()->execute('ALTER TABLE '. $tableSenders .' MODIFY `sender_phone` varchar(14) NULL');


// insertamos dos nuevos productos
$tableProducts = CorreosOficialUtils::getPrefix() . "correos_oficial_products";
$tableProductsActive = CorreosOficialUtils::getPrefix() . "correos_oficial_products_shop";

// Comprobar si ambos productos existen
$query = "SELECT COUNT(*) as count FROM " . $tableProducts . " WHERE id IN (26, 27)";
$result = Db::getInstance()->getRow($query);

// Verificar si ambos productos existen
if ($result['count'] < 2) {
    Db::getInstance()->execute(
        "INSERT INTO ". $tableProducts ." (id, name, delay, company, url, codigoProducto, id_carrier, product_type, max_packages) VALUES
        (26, 'Carta Certificada Internacional', 'Envíos con Correos OFICIAL', 'Correos', 'https://www.correos.es/es/es/herramientas/localizador/envios/detalle?tracking-number=@', 'S0004', 0, 'international', 1),
        (27, 'Paquete Postal Económico Internacional', 'Envíos con Correos OFICIAL', 'Correos', 'https://www.correos.es/es/es/herramientas/localizador/envios/detalle?tracking-number=@', 'S0031', 0, 'international', 1);"
    );
}

// Comprobar si ambos productos existen en la tabla activa
$query = "SELECT COUNT(*) as count FROM " . $tableProductsActive . " WHERE id_product IN (26, 27)";
$result = Db::getInstance()->getRow($query);

// Verificar si ambos productos existen
if ($result['count'] < 2) {
    // Al menos uno de los productos no existe, insertamos ambos
    foreach ([26, 27] as $id) {
        Db::getInstance()->execute(
            "INSERT INTO " . $tableProductsActive . " (id_product, active, id_shop) VALUES ($id, 0, 1);"
        );
    }
}