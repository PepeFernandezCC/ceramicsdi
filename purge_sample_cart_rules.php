<?php

require_once __DIR__ . '/config/config.inc.php';
require_once __DIR__ . '/init.php';

/**
 * Purga las reglas de carrito ("cart_rule") auto-generadas por el módulo
 * ccfreesamplediscount (código CCFREESAMPLE-<id_cart>) que ya han
 * caducado. Ver informe de incidencia de rendimiento del 14/09/2026.
 *
 * v2: borra por SQL en bruto (rápido, sin overhead de instanciar
 * CartRule por fila) pero en LOTES PEQUEÑOS dentro de su propia
 * transacción corta, para no acaparar locks sobre ps_cart_rule_combination
 * (11,4M filas) ni agotar el timeout del servidor web. Se auto-recarga
 * sola hasta terminar.
 *
 * Seguridad básica:
 * Cambia este token antes de subirlo.
 * Ejecutar en navegador:
 * https://ceramicconnection.com/purge_sample_cart_rules.php?token=CRX48219QP
 */
$token = 'CRX48219QP';

if (php_sapi_name() !== 'cli') {
    if (!isset($_GET['token']) || $_GET['token'] !== $token) {
        die('Acceso denegado');
    }
}

/**
 * Modo simulación.
 * true  = no borra nada, solo muestra el diagnóstico.
 * false = borra de verdad, en lotes, recargando la página solo hasta terminar.
 */
$dryRun = false;

/**
 * Filas a borrar por lote. Si sigue dando timeout de lock, baja este
 * número (p.ej. 50).
 */
$batchSize = 200;

const CODE_PREFIX = 'CCFREESAMPLE-';

set_time_limit(0);

echo '<pre>';

// -------------------------------------------------------------------
// Diagnóstico (rápido, son COUNT(), no hace falta lote)
// -------------------------------------------------------------------
$totalRow = Db::getInstance()->getRow('
    SELECT
        COUNT(*) AS total,
        SUM(date_to < NOW()) AS caducadas,
        SUM(date_to >= NOW()) AS vigentes
    FROM `' . _DB_PREFIX_ . 'cart_rule`
    WHERE code LIKE "' . pSQL(CODE_PREFIX) . '%"
');

echo "Reglas CCFREESAMPLE- encontradas:\n";
echo "  Total:     {$totalRow['total']}\n";
echo "  Caducadas: {$totalRow['caducadas']}\n";
echo "  Vigentes:  {$totalRow['vigentes']} (no se tocan, son de carritos activos ahora mismo)\n\n";

$totalCartRuleTienda = (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'cart_rule`');
echo "Total ps_cart_rule en la tienda ahora mismo: {$totalCartRuleTienda}\n\n";

if ($dryRun) {
    echo "MODO DRY RUN: no se ha borrado nada. Cambia \$dryRun a false para borrar de verdad (en lotes).\n";
    echo '</pre>';
    exit;
}

// -------------------------------------------------------------------
// Un lote: tabla temporal con los IDs de este lote, y borrado en
// cascada por SQL en bruto, tabla por tabla, en su propia transacción
// corta (para no acaparar locks).
// -------------------------------------------------------------------
$db = Db::getInstance();

$db->execute('DROP TEMPORARY TABLE IF EXISTS tmp_sample_rules_batch');
$db->execute('
    CREATE TEMPORARY TABLE tmp_sample_rules_batch AS
    SELECT id_cart_rule
    FROM `' . _DB_PREFIX_ . 'cart_rule`
    WHERE code LIKE "' . pSQL(CODE_PREFIX) . '%"
      AND date_to < NOW()
    ORDER BY id_cart_rule ASC
    LIMIT ' . (int) $batchSize
);

$batchCount = (int) $db->getValue('SELECT COUNT(*) FROM tmp_sample_rules_batch');

if ($batchCount === 0) {
    echo "No quedan reglas caducadas por borrar.\n";
    $db->execute('DROP TEMPORARY TABLE IF EXISTS tmp_sample_rules_batch');
    echo '</pre>';
    exit;
}

echo "Procesando lote de {$batchCount} reglas...\n";

$db->execute('START TRANSACTION');

$db->execute('DELETE FROM `' . _DB_PREFIX_ . 'cart_cart_rule` WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_batch)');
$db->execute('DELETE FROM `' . _DB_PREFIX_ . 'cart_rule_carrier` WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_batch)');
$db->execute('DELETE FROM `' . _DB_PREFIX_ . 'cart_rule_shop` WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_batch)');
$db->execute('DELETE FROM `' . _DB_PREFIX_ . 'cart_rule_group` WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_batch)');
$db->execute('DELETE FROM `' . _DB_PREFIX_ . 'cart_rule_country` WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_batch)');
// Combinaciones: dos DELETE separados (uno por columna) en vez de un
// OR, para poder usar el índice de cada columna por separado y no
// forzar un escaneo más pesado sobre las 11,4M filas de esta tabla.
$db->execute('DELETE FROM `' . _DB_PREFIX_ . 'cart_rule_combination` WHERE id_cart_rule_1 IN (SELECT id_cart_rule FROM tmp_sample_rules_batch)');
$db->execute('DELETE FROM `' . _DB_PREFIX_ . 'cart_rule_combination` WHERE id_cart_rule_2 IN (SELECT id_cart_rule FROM tmp_sample_rules_batch)');
$db->execute('DELETE FROM `' . _DB_PREFIX_ . 'cart_rule_lang` WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_batch)');
$db->execute('DELETE FROM `' . _DB_PREFIX_ . 'cart_rule` WHERE id_cart_rule IN (SELECT id_cart_rule FROM tmp_sample_rules_batch)');

$db->execute('COMMIT');
$db->execute('DROP TEMPORARY TABLE IF EXISTS tmp_sample_rules_batch');

echo "Lote borrado.\n";

// Cuántas quedan por borrar tras este lote
$remaining = (int) Db::getInstance()->getValue('
    SELECT COUNT(*)
    FROM `' . _DB_PREFIX_ . 'cart_rule`
    WHERE code LIKE "' . pSQL(CODE_PREFIX) . '%"
      AND date_to < NOW()
');

echo "\nQuedan por borrar: {$remaining}\n";

if ($remaining > 0) {
    $nextUrl = 'purge_sample_cart_rules.php?token=' . urlencode($token);
    echo "\nContinuando automáticamente en 2 segundos (lote siguiente)...\n";
    echo "Si el auto-refresco no funciona, entra manualmente en:\n{$nextUrl}\n";
    echo '</pre>';
    echo '<meta http-equiv="refresh" content="2;url=' . htmlspecialchars($nextUrl, ENT_QUOTES, 'UTF-8') . '">';
    echo '<p><a href="' . htmlspecialchars($nextUrl, ENT_QUOTES, 'UTF-8') . '">Siguiente lote ahora &raquo;</a></p>';
} else {
    $totalCartRuleTiendaDespues = (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'cart_rule`');
    echo "\n¡TERMINADO! Ya no quedan reglas CCFREESAMPLE- caducadas.\n";
    echo "Total ps_cart_rule en la tienda (después): {$totalCartRuleTiendaDespues}\n";
    echo '</pre>';
}
