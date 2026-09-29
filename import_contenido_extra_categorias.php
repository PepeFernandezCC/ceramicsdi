<?php


define('CCCONTENIDOEXTRA_SCRIPT_TOKEN', '3a257189e4923ea2ef623e7699971280');

$isCli = php_sapi_name() === 'cli';

if (!$isCli) {

    set_time_limit(0);
    ignore_user_abort(true);
    header('Content-Type: text/plain; charset=utf-8');
}

require __DIR__ . '/config/config.inc.php';

// CSV exportado del Excel con las columnas: url, id_lang, content.
define('CCCONTENIDOEXTRA_CSV', __DIR__ . '/contenido_extra_lang.csv');

function cccontenidoextra_out($message)
{
    echo $message;

    if (ob_get_level() > 0) {
        ob_flush();
    }
    flush();
}

/**
 * Resuelve una url de categoria (https://.../es/azulejos/forma/cuadrado/60x60)
 * al id_category siguiendo la cadena de link_rewrite desde el primer tramo
 * hasta el ultimo, cada uno hijo del anterior. Si la cadena no cuadra, cae a
 * buscar solo por el ultimo tramo siempre que sea unico.
 *
 * @return array{id: int, method: string}|array{id: null, method: string}
 */
function cccontenidoextra_resolve_category($url, $idLang, $idShop)
{
    $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
    $segments = explode('/', $path);

    // Quitar el prefijo de idioma (es, fr, en...). Los link_rewrite de la url
    // estan en ese idioma, asi que se buscan en el aunque el CSV diga otro.
    if ($segments && ($idLangUrl = (int) Language::getIdByIso($segments[0]))) {
        array_shift($segments);
        $idLang = $idLangUrl;
    }

    if (!$segments || $segments[0] === '') {
        return array('id' => null, 'method' => 'url sin ruta de categoria');
    }

    $idParent = null;
    foreach ($segments as $segment) {
        $sql = 'SELECT c.id_category
            FROM `' . _DB_PREFIX_ . 'category` c
            INNER JOIN `' . _DB_PREFIX_ . 'category_lang` cl
                ON cl.id_category = c.id_category AND cl.id_lang = ' . (int) $idLang . ' AND cl.id_shop = ' . (int) $idShop . '
            WHERE cl.link_rewrite = "' . pSQL($segment) . '"'
            . ($idParent !== null ? ' AND c.id_parent = ' . (int) $idParent : '');

        $rows = Db::getInstance()->executeS($sql);

        if (count($rows) !== 1) {
            $idParent = null;
            break;
        }

        $idParent = (int) $rows[0]['id_category'];
    }

    if ($idParent) {
        return array('id' => $idParent, 'method' => 'ruta');
    }

    $last = end($segments);
    $rows = Db::getInstance()->executeS('
        SELECT cl.id_category FROM `' . _DB_PREFIX_ . 'category_lang` cl
        WHERE cl.id_lang = ' . (int) $idLang . ' AND cl.id_shop = ' . (int) $idShop . '
        AND cl.link_rewrite = "' . pSQL($last) . '"
    ');

    if (count($rows) === 1) {
        return array('id' => (int) $rows[0]['id_category'], 'method' => 'ultimo tramo');
    }

    return array(
        'id' => null,
        'method' => count($rows) ? 'link_rewrite "' . $last . '" ambiguo (' . count($rows) . ' categorias)' : 'no existe link_rewrite "' . $last . '"',
    );
}

$dryRun = $isCli
    ? in_array('--dry-run', $argv, true)
    : !empty($_GET['dry_run']);

if (!is_file(CCCONTENIDOEXTRA_CSV)) {
    exit('No se encuentra el CSV: ' . CCCONTENIDOEXTRA_CSV . "\n");
}

$raw = file_get_contents(CCCONTENIDOEXTRA_CSV);

// Excel en español guarda el CSV normal en Windows-1252; el "CSV UTF-8" lleva BOM.
if (strncmp($raw, "\xEF\xBB\xBF", 3) === 0) {
    $raw = substr($raw, 3);
} elseif (!mb_check_encoding($raw, 'UTF-8')) {
    $raw = mb_convert_encoding($raw, 'UTF-8', 'Windows-1252');
}

$firstLine = strtok($raw, "\n");
$delimiter = substr_count($firstLine, ';') >= substr_count($firstLine, ',') ? ';' : ',';

$handle = fopen('php://temp', 'r+');
fwrite($handle, $raw);
rewind($handle);

$idShop = (int) Configuration::get('PS_SHOP_DEFAULT');
$total = 0;
$done = 0;
$skipped = 0;
$errors = 0;

cccontenidoextra_out('Leyendo ' . CCCONTENIDOEXTRA_CSV . ($dryRun ? ' (dry-run: no se escribe nada)' : '') . "\n\n");

while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
    $url = isset($row[0]) ? trim($row[0]) : '';
    $idLang = isset($row[1]) ? (int) $row[1] : 0;
    $content = isset($row[2]) ? trim($row[2]) : '';

    // Cabecera o filas vacias.
    if (stripos($url, 'http') !== 0) {
        continue;
    }

    $total++;

    if (!$idLang || !Language::getLanguage($idLang)) {
        $errors++;
        cccontenidoextra_out("  [ERROR] $url: id_lang no valido (" . (isset($row[1]) ? $row[1] : '') . ")\n");
        continue;
    }

    if ($content === '') {
        $skipped++;
        cccontenidoextra_out("  [SKIP] $url: sin contenido en el CSV\n");
        continue;
    }

    $category = cccontenidoextra_resolve_category($url, $idLang, $idShop);

    if (!$category['id']) {
        $errors++;
        cccontenidoextra_out("  [ERROR] $url: categoria no encontrada (" . $category['method'] . ")\n");
        continue;
    }

    $idCategory = (int) $category['id'];

    $idExtra = (int) Db::getInstance()->getValue('
        SELECT id_planatec_categorias_contenido_extra FROM `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra`
        WHERE id_categoria = ' . $idCategory . '
        ORDER BY id_planatec_categorias_contenido_extra ASC
    ');

    $created = false;

    // Sin fila para la categoria: se crea con solo id_categoria (el id es autoincremental).
    if (!$idExtra && !$dryRun) {
        if (!Db::getInstance()->execute('
            INSERT INTO `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra` (id_categoria)
            VALUES (' . $idCategory . ')
        ')) {
            $errors++;
            cccontenidoextra_out("  [ERROR] $url: no se ha podido crear la fila de la categoria $idCategory: " . Db::getInstance()->getMsgError() . "\n");
            continue;
        }

        $idExtra = (int) Db::getInstance()->Insert_ID();
        $created = true;
    }

    $info = "categoria $idCategory (" . $category['method'] . "), contenido_extra "
        . ($idExtra ? $idExtra : 'nuevo') . ($created ? ' (creado)' : '')
        . ", id_lang $idLang, " . Tools::strlen($content) . ' caracteres';

    if ($dryRun) {
        $done++;
        cccontenidoextra_out("  [OK] $url -> $info\n");
        continue;
    }

    // ON DUPLICATE por si la fila de idioma todavia no existe; button_text se conserva.
    $ok = Db::getInstance()->execute('
        INSERT INTO `' . _DB_PREFIX_ . 'planatec_categorias_contenido_extra_lang`
            (id_planatec_categorias_contenido_extra, id_lang, content)
        VALUES (' . $idExtra . ', ' . $idLang . ', "' . pSQL($content, true) . '")
        ON DUPLICATE KEY UPDATE content = VALUES(content)
    ');

    if ($ok) {
        $done++;
        cccontenidoextra_out("  [OK] $url -> $info actualizado\n");
    } else {
        $errors++;
        cccontenidoextra_out("  [ERROR] $url: fallo al actualizar ($info): " . Db::getInstance()->getMsgError() . "\n");
    }
}

fclose($handle);

cccontenidoextra_out("\nResumen: $done ok, $skipped sin contenido, $errors con error, de $total filas.\n");
