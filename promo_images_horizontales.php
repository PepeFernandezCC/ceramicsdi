<?php
/**
 * Listado de las imágenes promocionales HORIZONTALES del módulo ccpromoimages.
 *
 * Cada tarjeta muestra la imagen horizontal con la referencia y el nombre del producto,
 * y dos botones: descargar la horizontal y subir (o sustituir) la vertical. La subida usa
 * CcPromoImages::saveUploadedImage(), así que aplica las mismas validaciones que la ficha
 * de producto del admin (jpg/png/webp, 8 MB, tipo real del fichero) y la imagen aparece
 * igual en el admin y en el feed de Channable.
 *
 * Acceso:
 *   https://ceramicconnection.com/promo_images_horizontales.php?token=27fcc294f33f60e18fe365a5000517cb
 */

require_once __DIR__ . '/config/config.inc.php';

const PROMO_TOKEN = '27fcc294f33f60e18fe365a5000517cb';
const PROMO_PER_PAGE = 48;

header('X-Robots-Tag: noindex, nofollow');

$token = (string) Tools::getValue('token');
if (!hash_equals(PROMO_TOKEN, $token)) {
    header('HTTP/1.1 403 Forbidden');
    exit('Acceso denegado');
}

/** @var CcPromoImages|false $module */
$module = Module::getInstanceByName('ccpromoimages');
if (!$module || !Module::isEnabled('ccpromoimages')) {
    exit('El módulo ccpromoimages no está instalado o está desactivado.');
}

$db = Db::getInstance();
$idLang = (int) Configuration::get('PS_LANG_DEFAULT');
$idShop = (int) Context::getContext()->shop->id;
$uploadsDir = _PS_MODULE_DIR_ . 'ccpromoimages/uploads/';

/**
 * Límite real de subida: el menor entre el del módulo y los de PHP.
 */
function promo_ini_bytes($value)
{
    $value = trim((string) $value);
    $number = (float) $value;
    switch (strtolower(substr($value, -1))) {
        case 'g':
            $number *= 1024;
            // no break
        case 'm':
            $number *= 1024;
            // no break
        case 'k':
            $number *= 1024;
    }

    return (int) $number;
}

$maxUpload = min(
    CcPromoImages::MAX_FILE_SIZE,
    promo_ini_bytes(ini_get('upload_max_filesize')),
    promo_ini_bytes(ini_get('post_max_size'))
);

function promo_json(array $data)
{
    header('Content-Type: application/json; charset=utf-8');
    exit(json_encode($data));
}

function promo_e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// ---------------------------------------------------------------- Descarga de la horizontal
if (Tools::getValue('action') === 'download') {
    $idProduct = (int) Tools::getValue('id_product');
    $row = $db->getRow('
        SELECT c.url_imagen, p.reference
        FROM `' . _DB_PREFIX_ . 'ccpromoimages` c
        INNER JOIN `' . _DB_PREFIX_ . 'product` p ON p.id_product = c.id_producto
        WHERE c.id_producto = ' . $idProduct . ' AND c.tipo = "horizontal"');

    // El nombre del fichero sale de la base de datos; basename() evita salir de la carpeta
    $path = $row ? $uploadsDir . basename($row['url_imagen']) : null;
    if (!$path || !is_file($path)) {
        header('HTTP/1.1 404 Not Found');
        exit('Imagen no encontrada');
    }

    $ext = pathinfo($path, PATHINFO_EXTENSION);
    $base = $row['reference'] !== '' ? $row['reference'] : 'producto-' . $idProduct;
    $name = preg_replace('/[^A-Za-z0-9._-]+/', '-', $base) . '-horizontal.' . $ext;
    header('Content-Type: ' . (mime_content_type($path) ?: 'application/octet-stream'));
    header('Content-Disposition: attachment; filename="' . $name . '"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}

// ---------------------------------------------------------------- Subida de la vertical
if (Tools::getValue('action') === 'upload_vertical' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $idProduct = (int) Tools::getValue('id_product');
    $hasHorizontal = (bool) $db->getValue('
        SELECT 1 FROM `' . _DB_PREFIX_ . 'ccpromoimages`
        WHERE id_producto = ' . $idProduct . ' AND tipo = "horizontal"');

    if (!$hasHorizontal || !Validate::isLoadedObject(new Product($idProduct))) {
        promo_json(['success' => false, 'error' => 'El producto no existe o no tiene imagen horizontal.']);
    }
    if (empty($_FILES['image']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
        // Si el fichero supera post_max_size, PHP vacía $_FILES y llegamos aquí
        promo_json(['success' => false, 'error' => 'No se ha recibido la imagen (¿supera ' . round($maxUpload / 1048576, 1) . ' MB?).']);
    }

    $result = $module->saveUploadedImage($idProduct, 'vertical', $_FILES['image']);
    promo_json($result);
}

// ---------------------------------------------------------------- Listado
$search = trim((string) Tools::getValue('q'));
$filter = (string) Tools::getValue('vertical');
$filter = in_array($filter, ['con', 'sin'], true) ? $filter : '';

$where = ['h.tipo = "horizontal"'];
if ($search !== '') {
    $like = pSQL(addcslashes($search, '%_'));
    $where[] = '(p.reference LIKE "%' . $like . '%" OR pl.name LIKE "%' . $like . '%" OR p.id_product = ' . (int) $search . ')';
}
if ($filter === 'con') {
    $where[] = 'v.id_imagen IS NOT NULL';
} elseif ($filter === 'sin') {
    $where[] = 'v.id_imagen IS NULL';
}

// Categoría (opcionalmente con sus subcategorías, por el árbol nleft/nright)
$idCategory = (int) Tools::getValue('id_category');
$withSubcategories = !Tools::getIsset('id_category') || (bool) Tools::getValue('subcategories');
if ($idCategory > 0) {
    $categoryIds = [$idCategory];
    if ($withSubcategories) {
        $bounds = $db->getRow('SELECT nleft, nright FROM `' . _DB_PREFIX_ . 'category` WHERE id_category = ' . $idCategory);
        if ($bounds) {
            $categoryIds = array_map('intval', array_column($db->executeS('
                SELECT id_category FROM `' . _DB_PREFIX_ . 'category`
                WHERE nleft >= ' . (int) $bounds['nleft'] . ' AND nright <= ' . (int) $bounds['nright']) ?: [], 'id_category'));
        }
    }
    $where[] = 'EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
        WHERE cp.id_product = p.id_product AND cp.id_category IN (' . implode(',', $categoryIds ?: [0]) . '))';
}

// Proveedor: el predeterminado (p.id_supplier) no siempre está en product_supplier, se miran ambos
$idSupplier = (int) Tools::getValue('id_supplier');
if ($idSupplier > 0) {
    $where[] = '(p.id_supplier = ' . $idSupplier . ' OR EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'product_supplier` psu
        WHERE psu.id_product = p.id_product AND psu.id_supplier = ' . $idSupplier . '))';
}

// Opciones de los desplegables
$categoryOptions = [];
foreach ($db->executeS('
    SELECT c.id_category, c.level_depth, cl.name
    FROM `' . _DB_PREFIX_ . 'category` c
    INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON cs.id_category = c.id_category AND cs.id_shop = ' . $idShop . '
    LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
        ON cl.id_category = c.id_category AND cl.id_lang = ' . $idLang . ' AND cl.id_shop = ' . $idShop . '
    WHERE c.id_category <> ' . (int) Configuration::get('PS_ROOT_CATEGORY') . '
    ORDER BY c.nleft ASC') ?: [] as $category) {
    $depth = max(0, (int) $category['level_depth'] - 1);
    $categoryOptions[(int) $category['id_category']] = str_repeat('— ', $depth) . $category['name'];
}
$supplierOptions = [];
foreach ($db->executeS('SELECT id_supplier, name FROM `' . _DB_PREFIX_ . 'supplier` ORDER BY name ASC') ?: [] as $supplier) {
    $supplierOptions[(int) $supplier['id_supplier']] = $supplier['name'];
}
$hasFilters = $search !== '' || $filter !== '' || $idCategory > 0 || $idSupplier > 0;

$from = '
    FROM `' . _DB_PREFIX_ . 'ccpromoimages` h
    INNER JOIN `' . _DB_PREFIX_ . 'product` p ON p.id_product = h.id_producto
    LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
        ON pl.id_product = p.id_product AND pl.id_lang = ' . $idLang . ' AND pl.id_shop = ' . $idShop . '
    LEFT JOIN `' . _DB_PREFIX_ . 'ccpromoimages` v ON v.id_producto = h.id_producto AND v.tipo = "vertical"
    WHERE ' . implode(' AND ', $where);

$total = (int) $db->getValue('SELECT COUNT(*) ' . $from);
$withVertical = (int) $db->getValue('SELECT COUNT(v.id_imagen) ' . $from);
$pages = max(1, (int) ceil($total / PROMO_PER_PAGE));
$page = min(max(1, (int) Tools::getValue('page', 1)), $pages);

$rows = $db->executeS('
    SELECT p.id_product, p.reference, pl.name, h.url_imagen AS horizontal, v.url_imagen AS vertical
    ' . $from . '
    ORDER BY p.reference ASC, p.id_product ASC
    LIMIT ' . (($page - 1) * PROMO_PER_PAGE) . ', ' . PROMO_PER_PAGE) ?: [];

$link = Context::getContext()->link;
$mediaUrl = function ($file) use ($link) {
    return $file ? $link->getMediaLink(_MODULE_DIR_ . 'ccpromoimages/uploads/' . $file) : '';
};

$selfUrl = function (array $params = []) use ($search, $filter, $page, $idCategory, $withSubcategories, $idSupplier) {
    $query = array_merge([
        'token' => PROMO_TOKEN,
        'q' => $search,
        'vertical' => $filter,
        'id_category' => $idCategory ?: null,
        'subcategories' => $idCategory ? ($withSubcategories ? '1' : '0') : null,
        'id_supplier' => $idSupplier ?: null,
        'page' => $page,
    ], $params);

    return basename(__FILE__) . '?' . http_build_query(array_filter($query, function ($v) {
        return $v !== '' && $v !== null;
    }));
};
?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Imágenes promocionales horizontales</title>
<style>
    :root { --ink: #1f2933; --muted: #637381; --line: #dfe3e8; --bg: #f4f6f8; --accent: #0b2545; --ok: #1a7f37; --err: #b42318; }
    * { box-sizing: border-box; }
    body { margin: 0; font-family: 'Segoe UI', Roboto, Arial, sans-serif; color: var(--ink); background: var(--bg); }
    header { background: #fff; border-bottom: 1px solid var(--line); padding: 16px 24px; position: sticky; top: 0; z-index: 2; }
    h1 { font-size: 20px; margin: 0 0 4px; }
    .stats { color: var(--muted); font-size: 13px; }
    form.filters { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
    form.filters { align-items: center; }
    form.filters input[type=search] { flex: 1 1 240px; }
    form.filters select { max-width: 280px; }
    form.filters .check { font-size: 13px; display: flex; align-items: center; gap: 4px; white-space: nowrap; }
    form.filters .check input { padding: 0; }
    form.filters .clear { font-size: 13px; color: var(--muted); }
    input, select, button, .btn { font: inherit; font-size: 14px; padding: 8px 12px; border: 1px solid var(--line); border-radius: 6px; background: #fff; }
    main { padding: 24px; }
    .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 18px; }
    .card { background: #fff; border: 1px solid var(--line); border-radius: 8px; overflow: hidden; display: flex; flex-direction: column; }
    .card .img { aspect-ratio: 16 / 9; background: #e9edf1; display: flex; align-items: center; justify-content: center; }
    .card .img img { width: 100%; height: 100%; object-fit: contain; }
    .card .body { padding: 12px 14px 14px; display: flex; flex-direction: column; gap: 6px; flex: 1; }
    .ref { font-size: 12px; color: var(--muted); font-family: Consolas, monospace; }
    .name { font-weight: 600; font-size: 14px; line-height: 1.3; }
    .vertical-state { font-size: 12px; display: flex; align-items: center; gap: 8px; min-height: 40px; }
    .vertical-state img { width: 30px; height: 40px; object-fit: cover; border-radius: 3px; border: 1px solid var(--line); }
    .vertical-state.has { color: var(--ok); }
    .vertical-state.missing { color: var(--muted); }
    .actions { display: flex; gap: 8px; margin-top: auto; }
    .btn { flex: 1; text-align: center; text-decoration: none; color: var(--ink); cursor: pointer; }
    .btn:hover { border-color: var(--accent); }
    .btn.primary { background: var(--accent); border-color: var(--accent); color: #fff; }
    .btn.primary:hover { opacity: .9; }
    .btn[disabled] { opacity: .6; cursor: wait; }
    .msg { font-size: 12px; min-height: 16px; }
    .msg.error { color: var(--err); }
    .msg.ok { color: var(--ok); }
    .pagination { display: flex; gap: 6px; justify-content: center; align-items: center; margin: 28px 0 8px; flex-wrap: wrap; }
    .pagination a, .pagination span { padding: 6px 12px; border: 1px solid var(--line); border-radius: 6px; background: #fff; color: var(--ink); text-decoration: none; font-size: 14px; }
    .pagination .current { background: var(--accent); color: #fff; border-color: var(--accent); }
    .empty { text-align: center; color: var(--muted); padding: 60px 0; }
</style>
</head>
<body>
<header>
    <h1>Imágenes promocionales horizontales</h1>
    <div class="stats">
        <?php echo (int) $total; ?> productos con imagen horizontal<?php echo $hasFilters ? ' (filtrados)' : ''; ?>
        · <?php echo (int) $withVertical; ?> ya tienen vertical
        · subida máxima <?php echo promo_e(round($maxUpload / 1048576, 1)); ?> MB (jpg, png, webp)
    </div>
    <form class="filters" method="get">
        <input type="hidden" name="token" value="<?php echo promo_e(PROMO_TOKEN); ?>">
        <input type="search" name="q" value="<?php echo promo_e($search); ?>" placeholder="Buscar por referencia, nombre o ID">
        <select name="id_category" aria-label="Categoría">
            <option value="0">Todas las categorías</option>
            <?php foreach ($categoryOptions as $id => $label) { ?>
                <option value="<?php echo (int) $id; ?>"<?php echo $id === $idCategory ? ' selected' : ''; ?>><?php echo promo_e($label); ?></option>
            <?php } ?>
        </select>
        <label class="check"><input type="checkbox" name="subcategories" value="1"<?php echo $withSubcategories ? ' checked' : ''; ?>> Incluir subcategorías</label>
        <select name="id_supplier" aria-label="Proveedor">
            <option value="0">Todos los proveedores</option>
            <?php foreach ($supplierOptions as $id => $name) { ?>
                <option value="<?php echo (int) $id; ?>"<?php echo $id === $idSupplier ? ' selected' : ''; ?>><?php echo promo_e($name); ?></option>
            <?php } ?>
        </select>
        <select name="vertical" aria-label="Vertical">
            <option value="">Con y sin vertical</option>
            <option value="sin"<?php echo $filter === 'sin' ? ' selected' : ''; ?>>Sin vertical</option>
            <option value="con"<?php echo $filter === 'con' ? ' selected' : ''; ?>>Con vertical</option>
        </select>
        <button type="submit">Filtrar</button>
        <?php if ($hasFilters) { ?><a class="clear" href="<?php echo promo_e(basename(__FILE__) . '?token=' . PROMO_TOKEN); ?>">Quitar filtros</a><?php } ?>
    </form>
</header>

<main>
<?php if (!$rows) { ?>
    <p class="empty">No hay imágenes horizontales con estos filtros.</p>
<?php } else { ?>
    <div class="grid">
    <?php foreach ($rows as $row) {
        $idProduct = (int) $row['id_product'];
        $verticalUrl = $mediaUrl($row['vertical']);
        ?>
        <article class="card" data-id="<?php echo $idProduct; ?>">
            <div class="img">
                <img src="<?php echo promo_e($mediaUrl($row['horizontal'])); ?>" alt="<?php echo promo_e($row['name']); ?>" loading="lazy">
            </div>
            <div class="body">
                <div class="ref"><?php echo promo_e($row['reference'] !== '' ? $row['reference'] : '(sin referencia)'); ?> · ID <?php echo $idProduct; ?></div>
                <div class="name"><?php echo promo_e($row['name']); ?></div>
                <div class="vertical-state <?php echo $verticalUrl ? 'has' : 'missing'; ?>">
                    <?php if ($verticalUrl) { ?>
                        <a href="<?php echo promo_e($verticalUrl); ?>" target="_blank" rel="noopener"><img src="<?php echo promo_e($verticalUrl); ?>" alt="" loading="lazy"></a>
                        <span>Vertical subida</span>
                    <?php } else { ?>
                        <span>Sin vertical</span>
                    <?php } ?>
                </div>
                <div class="actions">
                    <a class="btn" href="<?php echo promo_e($selfUrl(['action' => 'download', 'id_product' => $idProduct, 'q' => null, 'vertical' => null, 'page' => null])); ?>">Descargar</a>
                    <button type="button" class="btn primary js-upload"><?php echo $verticalUrl ? 'Sustituir vertical' : 'Subir vertical'; ?></button>
                    <input type="file" class="js-file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" hidden>
                </div>
                <div class="msg"></div>
            </div>
        </article>
    <?php } ?>
    </div>

    <?php if ($pages > 1) { ?>
    <nav class="pagination">
        <?php if ($page > 1) { ?><a href="<?php echo promo_e($selfUrl(['page' => $page - 1])); ?>">&laquo; Anterior</a><?php } ?>
        <?php for ($i = 1; $i <= $pages; $i++) {
            if ($i === $page) { ?>
                <span class="current"><?php echo $i; ?></span>
            <?php } elseif ($i === 1 || $i === $pages || abs($i - $page) <= 2) { ?>
                <a href="<?php echo promo_e($selfUrl(['page' => $i])); ?>"><?php echo $i; ?></a>
            <?php } elseif (abs($i - $page) === 3) { ?>
                <span>…</span>
            <?php }
        } ?>
        <?php if ($page < $pages) { ?><a href="<?php echo promo_e($selfUrl(['page' => $page + 1])); ?>">Siguiente &raquo;</a><?php } ?>
    </nav>
    <?php } ?>
<?php } ?>
</main>

<script>
(function () {
    var uploadUrl = <?php echo json_encode(basename(__FILE__) . '?token=' . PROMO_TOKEN . '&action=upload_vertical'); ?>;
    var maxBytes = <?php echo (int) $maxUpload; ?>;

    document.querySelectorAll('.card').forEach(function (card) {
        var button = card.querySelector('.js-upload');
        var input = card.querySelector('.js-file');
        var msg = card.querySelector('.msg');
        var state = card.querySelector('.vertical-state');

        button.addEventListener('click', function () { input.click(); });

        input.addEventListener('change', function () {
            var file = input.files[0];
            input.value = '';
            if (!file) {
                return;
            }
            if (file.size > maxBytes) {
                show('error', 'La imagen pesa ' + (file.size / 1048576).toFixed(1) + ' MB y el máximo es ' + (maxBytes / 1048576).toFixed(1) + ' MB.');
                return;
            }

            var body = new FormData();
            body.append('id_product', card.getAttribute('data-id'));
            body.append('image', file);
            button.disabled = true;
            show('', 'Subiendo…');

            fetch(uploadUrl, { method: 'POST', body: body, credentials: 'same-origin' })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (!data.success) {
                        throw new Error(data.error || 'No se ha podido subir la imagen.');
                    }
                    var url = data.image.media_url + '?v=' + Date.now();
                    state.className = 'vertical-state has';
                    state.innerHTML = '<a href="' + url + '" target="_blank" rel="noopener"><img src="' + url + '" alt=""></a><span>Vertical subida</span>';
                    button.textContent = 'Sustituir vertical';
                    show('ok', 'Vertical guardada.');
                })
                .catch(function (error) {
                    show('error', error.message);
                })
                .then(function () {
                    button.disabled = false;
                });
        });

        function show(type, text) {
            msg.className = 'msg ' + type;
            msg.textContent = text;
        }
    });
})();
</script>
</body>
</html>
