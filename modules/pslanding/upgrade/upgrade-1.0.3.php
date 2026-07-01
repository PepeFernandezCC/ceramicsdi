<?php
function upgrade_module_1_0_3($module)
{
    $queries = [];

    $queries[] = 'ALTER TABLE `'._DB_PREFIX_.'pslanding_lang`
        ADD COLUMN `external_url` VARCHAR(255) NULL';

    foreach ($queries as $q) {
        try {
            Db::getInstance()->execute($q);
        } catch (Exception $e) {
            // si ya existe la columna, ignora (entornos donde se ejecutó antes)
        }
    }
    return true;
}
