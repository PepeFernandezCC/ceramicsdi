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

// Obtener la instancia de la base de datos de PrestaShop
$db = Db::getInstance();

$tableExists = Db::getInstance()->ExecuteS('SHOW TABLES LIKE "'. _DB_PREFIX_ .'correos_oficial_requests"');

if (!$tableExists) {
    return false;
}

$table_requests = _DB_PREFIX_ . 'correos_oficial_requests';

try {
    $column_exists_requests = $db->executeS("SHOW COLUMNS FROM `$table_requests` LIKE 'id_carrier'");

    if ($column_exists_requests) {
            $db->execute("ALTER TABLE `$table_requests` DROP COLUMN id_carrier");
            $db->execute("ALTER TABLE `$table_requests` DROP COLUMN email");
        error_log("CORREOS ECOMMERCE PRESTASHOP: SE HAN ELIMINADO CORRECTAMENTE id_carrier y email de la tabla $table_requests");
    }
} catch (Exception $e) {
    error_log('Error: ' . $e->getMessage());
}