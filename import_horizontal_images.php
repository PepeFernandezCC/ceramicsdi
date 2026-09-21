<?php


define('CCPROMOIMAGES_SCRIPT_TOKEN', '3a257189e4923ea2ef623e7699971280');

$isCli = php_sapi_name() === 'cli';

if (!$isCli) {
    if (!isset($_GET['token']) || !hash_equals(CCPROMOIMAGES_SCRIPT_TOKEN, (string) $_GET['token'])) {
        http_response_code(403);
        exit('Acceso denegado.');
    }

    set_time_limit(0);
    ignore_user_abort(true);
    header('Content-Type: text/plain; charset=utf-8');
}

require __DIR__ . '/config/config.inc.php';

define('CCPROMOIMAGES_SAMPLES_CATEGORY_ID', 1751);
define('CCPROMOIMAGES_TARGET_POSITION', 6);
define('CCPROMOIMAGES_TIPO', 'horizontal');
define('CCPROMOIMAGES_UPLOADS_DIR', _PS_MODULE_DIR_ . 'ccpromoimages/uploads/');
define('CCPROMOIMAGES_MAX_FILE_SIZE', 8388608); // 8 MB

// Mismas extensiones/mimes admitidos que el propio modulo ccpromoimages.
$allowedExt = array(
    'jpg' => array('image/jpeg', 'image/pjpeg'),
    'jpeg' => array('image/jpeg', 'image/pjpeg'),
    'png' => array('image/png'),
    'webp' => array('image/webp'),
);

/**
 * Copia $sourcePath a la carpeta de subidas del modulo y deja la fila
 * correspondiente en ps_ccpromoimages, sustituyendo la anterior si ya
 * habia una imagen de ese tipo para el producto. Replica exactamente
 * lo que hace el modulo al recibir una subida manual, pero sin llamar
 * a ninguno de sus metodos.
 *
 * @return array{success: bool, error?: string}
 */
function ccpromoimages_save_from_path($idProduct, $tipo, $sourcePath, array $allowedExt)
{
    if (!is_file($sourcePath) || !is_readable($sourcePath)) {
        return array('success' => false, 'error' => 'El archivo de origen no existe o no se puede leer: ' . $sourcePath);
    }

    $size = filesize($sourcePath);
    if ($size === false || $size > CCPROMOIMAGES_MAX_FILE_SIZE) {
        return array('success' => false, 'error' => 'El archivo supera el tamaño maximo permitido (8 MB)');
    }

    $ext = Tools::strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));

    if (!isset($allowedExt[$ext])) {
        return array('success' => false, 'error' => 'Formato de imagen no permitido (usa jpg, png o webp)');
    }

    $mimeType = null;
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $sourcePath);
        finfo_close($finfo);
    }

    if ($mimeType && !in_array($mimeType, $allowedExt[$ext], true)) {
        // El nombre del fichero no coincide con su contenido real (ej. una
        // imagen .jpg que en realidad es un PNG - pasa en el catalogo). Si
        // el contenido real es un formato admitido, usamos la extension
        // correcta en vez de rechazarla.
        $realExt = null;
        foreach ($allowedExt as $candidateExt => $mimes) {
            if (in_array($mimeType, $mimes, true)) {
                $realExt = $candidateExt;
                break;
            }
        }

        if (!$realExt) {
            return array('success' => false, 'error' => 'El contenido del archivo no coincide con una imagen valida');
        }

        $ext = $realExt;
    }

    if (!is_dir(CCPROMOIMAGES_UPLOADS_DIR)) {
        mkdir(CCPROMOIMAGES_UPLOADS_DIR, 0755, true);
    }

    $filename = (int) $idProduct . '-' . $tipo . '-' . time() . '.' . $ext;

    if (!copy($sourcePath, CCPROMOIMAGES_UPLOADS_DIR . $filename)) {
        return array('success' => false, 'error' => 'No se ha podido copiar el archivo al directorio destino');
    }

    $existing = Db::getInstance()->getRow(
        'SELECT `url_imagen` FROM `' . _DB_PREFIX_ . 'ccpromoimages`
         WHERE `id_producto` = ' . (int) $idProduct . ' AND `tipo` = "' . pSQL($tipo) . '"'
    );

    if ($existing && $existing['url_imagen'] && $existing['url_imagen'] !== $filename) {
        @unlink(CCPROMOIMAGES_UPLOADS_DIR . $existing['url_imagen']);
    }

    $now = date('Y-m-d H:i:s');

    if ($existing) {
        Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'ccpromoimages`
             SET `url_imagen` = "' . pSQL($filename) . '", `date_upd` = "' . $now . '"
             WHERE `id_producto` = ' . (int) $idProduct . ' AND `tipo` = "' . pSQL($tipo) . '"'
        );
    } else {
        Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'ccpromoimages`
             (`id_producto`, `tipo`, `url_imagen`, `date_add`, `date_upd`)
             VALUES (' . (int) $idProduct . ', "' . pSQL($tipo) . '", "' . pSQL($filename) . '", "' . $now . '", "' . $now . '")'
        );
    }

    return array('success' => true);
}

/**
 * echo + flush inmediato, para ver avance en directo en el navegador
 * en vez de esperar a que termine todo el script para ver algo.
 */
function ccpromoimages_out($message)
{
    echo $message;

    if (ob_get_level() > 0) {
        ob_flush();
    }
    flush();
}

$dryRun = $isCli
    ? in_array('--dry-run', $argv, true)
    : !empty($_GET['dry_run']);

$products = Db::getInstance()->executeS('
    SELECT p.id_product
    FROM `' . _DB_PREFIX_ . 'product` p
    WHERE p.id_product NOT IN (
        SELECT id_product FROM `' . _DB_PREFIX_ . 'category_product` WHERE id_category = ' . (int) CCPROMOIMAGES_SAMPLES_CATEGORY_ID . '
    )
    ORDER BY p.id_product ASC
');

$total = count($products);
$done = 0;
$skippedNoImage = 0;
$errors = 0;

ccpromoimages_out('Productos a procesar: ' . $total . ($dryRun ? ' (dry-run: no se escribe nada)' : '') . "\n\n");

foreach ($products as $row) {
    $idProduct = (int) $row['id_product'];

    $idImage = (int) Db::getInstance()->getValue('
        SELECT id_image FROM `' . _DB_PREFIX_ . 'image`
        WHERE id_product = ' . $idProduct . ' AND position = ' . (int) CCPROMOIMAGES_TARGET_POSITION
    );

    if (!$idImage) {
        $skippedNoImage++;
        ccpromoimages_out("  [SKIP] Producto $idProduct: no tiene imagen en la posicion " . CCPROMOIMAGES_TARGET_POSITION . "\n");
        continue;
    }

    $image = new Image($idImage);
    $sourcePath = _PS_PROD_IMG_DIR_ . $image->getExistingImgPath() . '.' . $image->image_format;

    if (!is_file($sourcePath)) {
        $errors++;
        ccpromoimages_out("  [ERROR] Producto $idProduct: no se encuentra el fichero de la imagen $idImage ($sourcePath)\n");
        continue;
    }

    if ($dryRun) {
        ccpromoimages_out("  [OK] Producto $idProduct -> imagen $idImage ($sourcePath)\n");
        $done++;
        continue;
    }

    $result = ccpromoimages_save_from_path($idProduct, CCPROMOIMAGES_TIPO, $sourcePath, $allowedExt);

    if ($result['success']) {
        $done++;
        ccpromoimages_out("  [OK] Producto $idProduct -> imagen $idImage guardada como horizontal\n");
    } else {
        $errors++;
        ccpromoimages_out("  [ERROR] Producto $idProduct: " . $result['error'] . "\n");
    }
}

ccpromoimages_out("\nResumen: $done ok, $skippedNoImage sin imagen en posicion " . CCPROMOIMAGES_TARGET_POSITION . ", $errors con error, de $total productos.\n");
