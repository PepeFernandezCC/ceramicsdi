<?php

require_once __DIR__ . '/config/config.inc.php';

/**
 * Convierte los valores PERSONALIZADOS de características (ps_feature_value.custom = 1)
 * en valores PREDEFINIDOS, sin que los productos pierdan su valor.
 *
 * Para cada valor personalizado asignado a un producto:
 *  - Si en la misma característica ya existe un valor predefinido con el mismo texto
 *    (idioma por defecto, sin distinguir mayúsculas ni espacios sobrantes):
 *      -> el producto pasa a usar el predefinido y se borra el personalizado.
 *         Si al predefinido le falta traducción en algún idioma, se rellena con la
 *         del personalizado.
 *  - Si no existe:
 *      -> el propio valor se marca como predefinido (custom = 0). Así se conservan
 *         sus traducciones en todos los idiomas. Los siguientes productos con el
 *         mismo texto se unificarán contra él.
 *
 * HACER BACKUP ANTES:
 *   mysqldump <bd> ps_feature_value ps_feature_value_lang ps_feature_product > backup_features.sql
 *
 * Ejecutar:
 *   CLI:       php convert_custom_feature_values.php
 *   Navegador: https://ceramicconnection.com/convert_custom_feature_values.php?token=FVC7731KZD
 *
 * Después: limpiar caché y reconstruir índices de ps_facetedsearch.
 */
$token = 'FVC7731KZD';

if (php_sapi_name() !== 'cli') {
    if (!isset($_GET['token']) || $_GET['token'] !== $token) {
        die('Acceso denegado');
    }
    header('Content-Type: text/plain; charset=utf-8');
}

/**
 * Modo simulación.
 * true  = no modifica nada, solo muestra lo que haría.
 * false = aplica los cambios.
 */
$dryRun = false;

/**
 * Borrar también los valores personalizados huérfanos (no asignados a ningún producto).
 */
$deleteOrphans = true;

@set_time_limit(0);

$db = Db::getInstance();
$p = _DB_PREFIX_;
$idLangDefault = (int) Configuration::get('PS_LANG_DEFAULT');

function fvc_normalize($value)
{
    return mb_strtolower(preg_replace('/\s+/u', ' ', trim((string) $value)), 'UTF-8');
}

function fvc_out($line)
{
    echo $line . PHP_EOL;
    @flush();
}

fvc_out($dryRun ? '=== MODO SIMULACIÓN (no se modifica nada) ===' : '=== MODO REAL ===');

// 1. Índice de valores predefinidos: [id_feature][texto normalizado] => id_feature_value
$predefined = [];
$rows = $db->executeS('
    SELECT fv.id_feature, fv.id_feature_value, fvl.value
    FROM ' . $p . 'feature_value fv
    INNER JOIN ' . $p . 'feature_value_lang fvl
        ON fvl.id_feature_value = fv.id_feature_value AND fvl.id_lang = ' . $idLangDefault . '
    WHERE fv.custom = 0
    ORDER BY fv.id_feature_value ASC
');
foreach ($rows as $row) {
    $key = fvc_normalize($row['value']);
    if ($key !== '' && !isset($predefined[(int) $row['id_feature']][$key])) {
        $predefined[(int) $row['id_feature']][$key] = (int) $row['id_feature_value'];
    }
}

// 2. Valores personalizados asignados a productos, agrupados por valor
$rows = $db->executeS('
    SELECT fv.id_feature_value, fv.id_feature, fp.id_product, fvl.value
    FROM ' . $p . 'feature_value fv
    INNER JOIN ' . $p . 'feature_product fp ON fp.id_feature_value = fv.id_feature_value
    LEFT JOIN ' . $p . 'feature_value_lang fvl
        ON fvl.id_feature_value = fv.id_feature_value AND fvl.id_lang = ' . $idLangDefault . '
    WHERE fv.custom = 1
    ORDER BY fv.id_feature_value ASC
');
$customValues = [];
foreach ($rows as $row) {
    $id = (int) $row['id_feature_value'];
    if (!isset($customValues[$id])) {
        $customValues[$id] = [
            'id_feature' => (int) $row['id_feature'],
            'value' => (string) $row['value'],
            'products' => [],
        ];
    }
    $customValues[$id]['products'][] = (int) $row['id_product'];
}

fvc_out(sprintf('Valores personalizados asignados a productos: %d', count($customValues)));

$stats = ['merged' => 0, 'converted' => 0, 'skipped_empty' => 0, 'errors' => 0, 'orphans' => 0];
$converted = [];

foreach ($customValues as $idCustom => $info) {
    $idFeature = $info['id_feature'];
    $key = fvc_normalize($info['value']);

    if ($key === '') {
        ++$stats['skipped_empty'];
        fvc_out(sprintf('[SALTADO] valor #%d (característica %d) vacío en idioma por defecto. Productos: %s',
            $idCustom, $idFeature, implode(',', $info['products'])));
        continue;
    }

    if (isset($predefined[$idFeature][$key])) {
        // Ya existe un predefinido igual: reasignar productos y borrar el personalizado
        $idTarget = $predefined[$idFeature][$key];
        ++$stats['merged'];

        if ($dryRun) {
            continue;
        }

        $db->execute('START TRANSACTION');
        $ok = true;
        foreach ($info['products'] as $idProduct) {
            $alreadyHasTarget = (bool) $db->getValue('
                SELECT 1 FROM ' . $p . 'feature_product
                WHERE id_product = ' . $idProduct . ' AND id_feature = ' . $idFeature . '
                  AND id_feature_value = ' . $idTarget);

            if ($alreadyHasTarget) {
                $ok = $ok && $db->delete('feature_product',
                    'id_product = ' . $idProduct . ' AND id_feature_value = ' . $idCustom);
            } else {
                $ok = $ok && $db->update('feature_product',
                    ['id_feature_value' => $idTarget],
                    'id_product = ' . $idProduct . ' AND id_feature_value = ' . $idCustom);
            }
        }

        // Completar traducciones vacías/inexistentes del predefinido con las del personalizado
        $ok = $ok && $db->execute('
            INSERT INTO ' . $p . 'feature_value_lang (id_feature_value, id_lang, value)
            SELECT ' . $idTarget . ', c.id_lang, c.value
            FROM ' . $p . 'feature_value_lang c
            LEFT JOIN ' . $p . 'feature_value_lang t
                ON t.id_feature_value = ' . $idTarget . ' AND t.id_lang = c.id_lang
            WHERE c.id_feature_value = ' . $idCustom . ' AND TRIM(c.value) <> \'\'
              AND (t.id_feature_value IS NULL OR TRIM(t.value) = \'\')
            ON DUPLICATE KEY UPDATE value = VALUES(value)');

        $ok = $ok && $db->delete('feature_value_lang', 'id_feature_value = ' . $idCustom);
        $ok = $ok && $db->delete('feature_value', 'id_feature_value = ' . $idCustom);

        if ($ok) {
            $db->execute('COMMIT');
        } else {
            $db->execute('ROLLBACK');
            --$stats['merged'];
            ++$stats['errors'];
            fvc_out(sprintf('[ERROR] unificando #%d -> #%d: %s', $idCustom, $idTarget, $db->getMsgError()));
        }
    } else {
        // No existe: el propio valor pasa a ser predefinido (conserva traducciones)
        if (!$dryRun && !$db->update('feature_value', ['custom' => 0], 'id_feature_value = ' . $idCustom)) {
            ++$stats['errors'];
            fvc_out(sprintf('[ERROR] convirtiendo #%d: %s', $idCustom, $db->getMsgError()));
            continue;
        }
        $predefined[$idFeature][$key] = $idCustom;
        ++$stats['converted'];
        $converted[] = sprintf('  característica %d: "%s" (#%d)', $idFeature, $info['value'], $idCustom);
    }
}

// 3. Huérfanos
if ($deleteOrphans) {
    $orphanWhere = 'FROM ' . $p . 'feature_value fv
        LEFT JOIN ' . $p . 'feature_product fp ON fp.id_feature_value = fv.id_feature_value
        WHERE fv.custom = 1 AND fp.id_feature_value IS NULL';
    $stats['orphans'] = (int) $db->getValue('SELECT COUNT(*) ' . $orphanWhere);

    if (!$dryRun && $stats['orphans'] > 0) {
        $db->execute('
            DELETE fvl FROM ' . $p . 'feature_value_lang fvl
            INNER JOIN ' . $p . 'feature_value fv ON fv.id_feature_value = fvl.id_feature_value
            LEFT JOIN ' . $p . 'feature_product fp ON fp.id_feature_value = fv.id_feature_value
            WHERE fv.custom = 1 AND fp.id_feature_value IS NULL');
        $db->execute('DELETE fv ' . $orphanWhere);
    }
}

fvc_out('');
fvc_out('Nuevos valores predefinidos (convertidos desde personalizado):');
fvc_out($converted ? implode(PHP_EOL, $converted) : '  (ninguno)');
fvc_out('');
fvc_out('=== RESUMEN ===');
fvc_out(sprintf('Unificados con un predefinido existente: %d', $stats['merged']));
fvc_out(sprintf('Convertidos a predefinido:               %d', $stats['converted']));
fvc_out(sprintf('Saltados (valor vacío):                  %d', $stats['skipped_empty']));
fvc_out(sprintf('Huérfanos %s:               %d', $dryRun ? 'a borrar' : 'borrados', $stats['orphans']));
fvc_out(sprintf('Errores:                                 %d', $stats['errors']));

if (!$dryRun) {
    Tools::clearSmartyCache();
    Cache::clean('*');
    fvc_out('Caché limpiada. Recuerda reconstruir los índices de ps_facetedsearch.');
}
