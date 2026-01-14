<?php
function upgrade_module_1_0_2($module)
{
    $queries = [];

    $queries[] = 'ALTER TABLE `'._DB_PREFIX_.'pslanding` 
        ADD COLUMN `block3_image` VARCHAR(255) NULL,
        ADD COLUMN `block4_image` VARCHAR(255) NULL';

    $queries[] = 'ALTER TABLE `'._DB_PREFIX_.'pslanding_lang`
        ADD COLUMN `block3_title` VARCHAR(255) NULL,
        ADD COLUMN `block3_text` TEXT NULL,
        ADD COLUMN `block4_title` VARCHAR(255) NULL,
        ADD COLUMN `block4_text` TEXT NULL';

    foreach ($queries as $q) {
        try {
            Db::getInstance()->execute($q);
        } catch (Exception $e) {
            // si ya existe la columna, ignora (entornos donde se ejecutó antes)
        }
    }
    return true;
}
