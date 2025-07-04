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

$tableConfiguration = CorreosOficialUtils::getPrefix() . "correos_oficial_configuration";
$tableOrders = CorreosOficialUtils::getPrefix() . "correos_oficial_orders";
$tableSavedOrders = CorreosOficialUtils::getPrefix() . "correos_oficial_saved_orders";

// Comprobamos que los registros necesarios existen en configuration
$checkConfiguration = Db::getInstance()->getRow(
    "SELECT `value` FROM " . $tableConfiguration . " WHERE `name` = 'ActivateDimensionsByDefault'"
);

if ($checkConfiguration == null) {
    Db::getInstance()->execute("INSERT INTO ". $tableConfiguration ." (name, value, type) VALUES
  ('ActivateDimensionsByDefault', '', 'checkbox'),
  ('DimensionsByDefaultHeight', '0', 'number'),
  ('DimensionsByDefaultLarge', '0', 'number'),
  ('DimensionsByDefaultWidth', '0', 'number');");
}

// Comprobamos los nuevos campos en la tabla orders
$fieldsToCheckOrders = [
    "added_values_cash_on_delivery" => "int(1) DEFAULT '0'",
    "added_values_insurance" => "int(1) DEFAULT '0'",
    "added_values_partial_delivery" => "int(1) DEFAULT '0'",
    "added_values_delivery_saturday" => "int(1) DEFAULT '0'",
    "added_values_cash_on_delivery_iban" => "varchar(50) DEFAULT NULL",
    "added_values_cash_on_delivery_value" => "FLOAT DEFAULT NULL",
    "added_values_insurance_value" => "FLOAT DEFAULT NULL",
];

foreach ($fieldsToCheckOrders as $key => $value) {
    $checkField = Db::getInstance()->executeS(
        "SHOW COLUMNS FROM " . $tableOrders . " WHERE Field = '" . $key . "';"
    );
    if (!$checkField) {
        Db::getInstance()->execute("ALTER TABLE " . $tableOrders . " ADD COLUMN `" . $key . "` ". $value);
    }
}

// Comprobamos los nuevos campos en la tabla saved orders
$fieldsToCheckSavedOrders = [
    "height" => "int(11) DEFAULT NULL",
    "width" => "int(11) DEFAULT NULL",
    "large" => "int(11) DEFAULT NULL",
    "weight" => "int(11) DEFAULT NULL",
    "reference" => "varchar(100) DEFAULT NULL",
    "observations" => "varchar(100) DEFAULT NULL",
];

foreach ($fieldsToCheckSavedOrders as $key => $value) {
    $checkField = Db::getInstance()->executeS(
        "SHOW COLUMNS FROM " . $tableSavedOrders . " WHERE Field = '" . $key . "';"
    );
    if (!$checkField) {
        Db::getInstance()->execute("ALTER TABLE " . $tableSavedOrders . " ADD COLUMN `" . $key . "` ". $value);
    }
}
