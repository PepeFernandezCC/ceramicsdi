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

/**
 * Clase de para generar los índices
 */
class CorreosOficialIndexes
{
    /**
     * Comprueba que los índices existes para las tablas de CorreosEcommerce
     */
    public static function checkIfIndexesExists() {
        // Obtener acceso a la base de datos
        $db = Db::getInstance();
        $indexes_list = '';

        // Definir los índices a comprobar
        $indexes = [
            'correos_oficial_orders' => [
                'idx_correos_oficial_orders_id_order',
                'idx_correos_oficial_orders_shipping_number',
                'idx_correos_oficial_orders_date_add'
            ],
            'correos_oficial_saved_orders' => [
                'idx_correos_oficial_saved_orders_id_order',
                'idx_correos_oficial_saved_orders_exp_number'
            ],
            'correos_oficial_saved_returns' => [
                'idx_correos_oficial_saved_returns_id_order',
                'idx_correos_oficial_saved_returns_exp_number'
            ],
            'correos_oficial_products' => [
                'idx_correos_oficial_products_id',
                'idx_correos_oficial_products_id_carrier',
                'idx_correos_oficial_products_company'
            ],
            'correos_oficial_requests' => [
                'idx_correos_oficial_requests_id_cart',
                'idx_correos_oficial_requests_reference_code'
            ],
            'correos_oficial_senders' => [
                'idx_correos_oficial_senders_id',
                'idx_correos_oficial_senders_id_shop',
                'idx_correos_oficial_senders_sender_default'
            ],
            'correos_oficial_pickups_returns' => [
                'idx_correos_oficial_pickups_returns_id_order'
            ]
        ];

        $allIndexesExist = true;

        foreach ($indexes as $table => $indexList) {
            $tableName = _DB_PREFIX_ . $table;
            foreach ($indexList as $index) {
                $query = "SHOW INDEX FROM $tableName WHERE Key_name = '$index'";
                $indexResult = $db->executeS($query);
                if (empty($indexResult)) {
                    $allIndexesExist = false;
                    break 2; // Salir de ambos bucles si algún índice no existe
                }
            }
        }

        if (!$allIndexesExist){

            /*
             * Comprueba que índice existen o no
             */
            foreach ($indexes as $table => $indexList) {
                $tableName = _DB_PREFIX_ . $table;
                foreach ($indexList as $index) {
                    $query = "SHOW INDEX FROM $tableName WHERE Key_name = '$index'";
                    $indexResult = $db->executeS($query);
                    $found = !empty($indexResult) ? 'SI' : 'NO';

                    if ($found == 'NO') {
                        // aquí crear el índice faltante
                        $columnName = str_replace(array('idx_', $table), '', $index);
                        $columnName = ltrim($columnName, '_');
                        $sql="CREATE INDEX $index ON $tableName ($columnName)";
                        $db->execute($sql);
                    }

                    $indexes_list.="$index: $found\n";
                }
            }
        }
        
    }

    /**
     * Crea los índices en las tables de CorreosEcommerce
     */
    public static function createIndexesOnCorreosOficialTables()
    {
            $db = Db::getInstance();

            // Crear índices en la tabla ps_correos_oficial_orders
            $db->execute('CREATE INDEX idx_correos_oficial_orders_id_order ON '._DB_PREFIX_.'correos_oficial_orders (id_order)');
            $db->execute('CREATE INDEX idx_correos_oficial_orders_shipping_number ON '._DB_PREFIX_.'correos_oficial_orders (shipping_number)');
            $db->execute('CREATE INDEX idx_correos_oficial_orders_date_add ON '._DB_PREFIX_.'correos_oficial_orders (date_add)');

            // Crear índice en la tabla ps_correos_oficial_saved_orders
            $db->execute('CREATE INDEX idx_correos_oficial_saved_orders_id_order ON '._DB_PREFIX_.'correos_oficial_saved_orders (id_order)');
            $db->execute('CREATE INDEX idx_correos_oficial_saved_orders_exp_number ON '._DB_PREFIX_.'correos_oficial_saved_orders (exp_number)');

            // Crear índice en la tabla ps_correos_oficial_saved_returns
            $db->execute('CREATE INDEX idx_correos_oficial_saved_returns_id_order ON '._DB_PREFIX_.'correos_oficial_saved_returns (id_order)');
            $db->execute('CREATE INDEX idx_correos_oficial_saved_returns_exp_number ON '._DB_PREFIX_.'correos_oficial_saved_returns (exp_number)');

            // Crear índices en la tabla ps_correos_oficial_products
            $db->execute('CREATE INDEX idx_correos_oficial_products_id ON '._DB_PREFIX_.'correos_oficial_products (id)');
            $db->execute('CREATE INDEX idx_correos_oficial_products_id_carrier ON '._DB_PREFIX_.'correos_oficial_products (id_carrier)');
            $db->execute('CREATE INDEX idx_correos_oficial_products_company ON '._DB_PREFIX_.'correos_oficial_products (company)');

            // Crear índices en la tabla ps_correos_oficial_requests
            $db->execute('CREATE INDEX idx_correos_oficial_requests_id_cart ON '._DB_PREFIX_.'correos_oficial_requests (id_cart)');
            $db->execute('CREATE INDEX idx_correos_oficial_requests_reference_code ON '._DB_PREFIX_.'correos_oficial_requests (reference_code)');

            // Crear índices en la tabla ps_correos_oficial_senders
            $db->execute('CREATE INDEX idx_correos_oficial_senders_id ON '._DB_PREFIX_.'correos_oficial_senders (id)');
            $db->execute('CREATE INDEX idx_correos_oficial_senders_id_shop ON '._DB_PREFIX_.'correos_oficial_senders (id_shop)');
            $db->execute('CREATE INDEX idx_correos_oficial_senders_sender_default ON '._DB_PREFIX_.'correos_oficial_senders (sender_default)');

            // Crear índices en la tabla ps_correos_oficial_pickup_returns
            $db->execute('CREATE INDEX idx_correos_oficial_pickups_returns_id_order ON '._DB_PREFIX_.'correos_oficial_pickups_returns(id_order)');
        
            return true;
    }
    
}
