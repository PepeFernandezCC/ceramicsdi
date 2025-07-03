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

$tableCodes = CorreosOficialUtils::getPrefix() . "correos_oficial_codes";
$tableSenders = CorreosOficialUtils::getPrefix() . "correos_oficial_senders";
$tableCodesActives = CorreosOficialUtils::getPrefix() . "correos_oficial_codes_actives";
$tableProductsShop = CorreosOficialUtils::getPrefix() . "correos_oficial_products_shop";
$tableProducts = CorreosOficialUtils::getPrefix() . "correos_oficial_products";
$tableConfiguration = CorreosOficialUtils::getPrefix() . "correos_oficial_configuration";
$tableCarrierProducts = CorreosOficialUtils::getPrefix() . "correos_oficial_carriers_products";
$shops = Shop::getShops(true, null, true);
$id_default_shop = (int)Configuration::get('PS_SHOP_DEFAULT');

/**
 * para correos_oficial_codes tableCodes
 */
$query = "SHOW INDEX FROM " . $tableCodes ." WHERE Key_name = 'customer_code';";
$record = Db::getInstance()->executeS($query);

if($record) {
    // Eliminamos la clave primaria de Codes
    Db::getInstance()->execute("ALTER TABLE " . $tableCodes . " DROP INDEX customer_code;");

    // Creamos el campo id_shop
    Db::getInstance()->execute("ALTER TABLE " . $tableCodes . " ADD id_shop INT NOT NULL DEFAULT $id_default_shop;");

}

/**
 * para correos_oficial_senders tableSenders
 */
$query = "SHOW INDEX FROM " . $tableSenders ." WHERE Key_name = 'Senders_unicos_completos';";
$record = Db::getInstance()->executeS($query);

if($record) {
    // Eliminamos la clave primaria de Codes
    Db::getInstance()->execute("ALTER TABLE " . $tableSenders . " DROP INDEX `Senders_unicos_completos`;");
    
    // Creamos el campo id_shop
    Db::getInstance()->execute("ALTER TABLE " . $tableSenders . " ADD id_shop INT NOT NULL DEFAULT $id_default_shop;");

}

/**
 * para correos_oficial_codes_actives tableCodesActives
 */
$query = "SHOW INDEX FROM " . $tableCodesActives ." WHERE Key_name = 'company';";
$record = Db::getInstance()->executeS($query);

if($record) {
    // Eliminamos la clave primaria de Codes
    Db::getInstance()->execute("ALTER TABLE " . $tableCodesActives . " DROP INDEX `company`;");
    
    // Creamos el campo id_shop
    Db::getInstance()->execute("ALTER TABLE " . $tableCodesActives . " ADD id_shop INT NOT NULL DEFAULT $id_default_shop;");

    // Creamos las relaciones nuevas
    Db::getInstance()->execute("ALTER TABLE " . $tableCodesActives . " ADD CONSTRAINT unique_company_per_shop UNIQUE (company, id_shop);");

    if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
        foreach ($shops as $id_shop) {
            $checkCodesActives = Db::getInstance()->getRow("SELECT * FROM " . $tableCodesActives . " WHERE `id_shop` = ".$id_shop);
            
            if (!$checkCodesActives) {
                Db::getInstance()->execute("INSERT INTO ". $tableCodesActives ." (company, active, id_shop) VALUES
                ('Correos', 0, ".$id_shop."),
                ('CEX', 0, ".$id_shop.");");
            }
        }
    }
}

/**
 * para correos_oficial_products_shop tableProductsShop
 */

// Creamos la tabla con el id_shop y el active
Db::getInstance()->execute("CREATE TABLE IF NOT EXISTS $tableProductsShop (
    id_product int(11) NOT NULL,
    active int(11) NOT NULL,
    id_shop int(10) NOT NULL,
    PRIMARY KEY (id_product, id_shop)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
$productsTableCheck = Db::getInstance()->executeS(
    "SHOW COLUMNS FROM " . $tableProducts . " WHERE Field = 'active';"
);
if ($productsTableCheck) {
    // Traspasamos el active de correos_oficial_products (tableProducts) a correos_oficial_products_shop antes de borrar el campo
    $productsActive = Db::getInstance()->executeS("SELECT * FROM $tableProducts;");
    foreach ($shops as $id_shop) {
        foreach ($productsActive as $prod) {
            // Si es la tienda por defecto mantenemos el estado, si no lo guardamos como desactivado para que se tengan que activar manualmente
            $active = ($id_shop == $id_default_shop) ? $prod['active'] : 0 ;
            Db::getInstance()->execute("INSERT INTO ". $tableProductsShop ." (id_product, active, id_shop) VALUES
            (".$prod['id'].", ".$active." , ".$id_shop.");");
        }
    }

    // Una vez traspasado el active borramos el campo active antiguo
    Db::getInstance()->execute("ALTER TABLE " . $tableProducts . " DROP `active`");
}

/**
 * para ps_correos_oficial_configuration tableConfiguration
 */

// Verificar si el campo id_shop existe en la tabla
$fieldExistsQuery = "SHOW COLUMNS FROM " . $tableConfiguration . " LIKE 'id_shop'";
$fieldExists = Db::getInstance()->executeS($fieldExistsQuery);

if(count($fieldExists) == 0) {
    // Eliminamos la clave primaria y creamos la nueva
    Db::getInstance()->execute("ALTER TABLE " . $tableConfiguration . " DROP PRIMARY KEY, ADD PRIMARY KEY (name, id_shop);");

    // Creamos el campo id_s
    Db::getInstance()->execute("ALTER TABLE " . $tableConfiguration . " ADD id_shop INT NOT NULL DEFAULT $id_default_shop;");
}

/**
 * para correos_oficial_carriers_products tableCarrierProducts
 */
$carrierProductTableCheck = Db::getInstance()->executeS(
    "SHOW COLUMNS FROM " . $tableCarrierProducts . " WHERE Field = 'id_shop';"
);

if (!$carrierProductTableCheck) {
    // Creamos el campo id_shop
    Db::getInstance()->execute("ALTER TABLE " . $tableCarrierProducts . " ADD id_shop INT NOT NULL DEFAULT $id_default_shop;");
}
