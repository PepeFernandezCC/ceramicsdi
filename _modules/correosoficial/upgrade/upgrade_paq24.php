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

$query2 = "SELECT count(name) as c FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_products WHERE codigoProducto='44'";
$record2 = Db::getInstance()->getRow($query2);

if (isset($record2['c']) && $record2['c'] == 0) {
    Db::getInstance()->insert(
        'correos_oficial_products',
        [
            'id' => '25',
            'name' => 'Paq 24 Oficina Elegida',
            'active' => 0,
            'delay' => 'Envíos con Correos OFICIAL',
            'company' => 'CEX',
            'url' => 'https://s.correosexpress.com/c?n=@',
            'codigoProducto' => '44',
            'id_carrier' => '0',
            'product_type' => 'office',
            'max_packages' => '99'
        ]
    );
}