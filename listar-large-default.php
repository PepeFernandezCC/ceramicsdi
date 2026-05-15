<?php
/**
 * Listado visual de imágenes large_default por producto.
 *
 * USO:
 * 1. Subir este archivo a la raíz de PrestaShop.
 * 2. Cambiar el valor de $TOKEN.
 * 3. Abrir:
 *    https://tudominio.com/listar-large-default.php?token=TU_TOKEN
 * 4. Borrar el archivo cuando termines.
 */

$TOKEN = 'PFZ678CCE';

if (!isset($_GET['token']) || $_GET['token'] !== $TOKEN) {
    http_response_code(403);
    exit('Acceso denegado');
}

require_once __DIR__ . '/config/config.inc.php';
require_once __DIR__ . '/init.php';

$idLang = (int) Context::getContext()->language->id;
$idShop = (int) Context::getContext()->shop->id;

$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = min(200, max(20, (int) ($_GET['per_page'] ?? 80)));
$offset = ($page - 1) * $perPage;

$onlyCover = isset($_GET['cover']) && $_GET['cover'] === '1';
$q = trim((string) ($_GET['q'] ?? ''));

$where = [];
$where[] = 'pl.id_lang = ' . (int) $idLang;
$where[] = 'pl.id_shop = ' . (int) $idShop;

if ($onlyCover) {
    $where[] = 'i.cover = 1';
}

if ($q !== '') {
    $safeQ = pSQL($q);
    $where[] = '(pl.name LIKE "%' . $safeQ . '%" OR p.id_product = "' . (int) $q . '" OR i.id_image = "' . (int) $q . '")';
}

$whereSql = implode(' AND ', $where);

$countSql = '
    SELECT COUNT(*)
    FROM ' . _DB_PREFIX_ . 'image i
    INNER JOIN ' . _DB_PREFIX_ . 'product p
        ON p.id_product = i.id_product
    INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl
        ON pl.id_product = p.id_product
    WHERE ' . $whereSql . '
        AND i.position > 3
        AND NOT EXISTS (
            SELECT 1
            FROM ' . _DB_PREFIX_ . 'category_product cp
            WHERE cp.id_product = p.id_product
              AND cp.id_category IN (81,82,83,36,94,67)
        )';

$total = (int) Db::getInstance()->getValue($countSql);

$sql = '
    SELECT
        p.id_product,
        p.reference,
        pl.name,
        i.id_image,
        i.cover
    FROM ' . _DB_PREFIX_ . 'image i
    INNER JOIN ' . _DB_PREFIX_ . 'product p
        ON p.id_product = i.id_product
    INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl
        ON pl.id_product = p.id_product
    WHERE ' . $whereSql . '
        AND i.position > 3
        AND NOT EXISTS (
            SELECT 1
            FROM ' . _DB_PREFIX_ . 'category_product cp
            WHERE cp.id_product = p.id_product
              AND cp.id_category IN (81,82,83,36,94,67)
        )
    ORDER BY p.id_product DESC, i.cover DESC, i.position ASC, i.id_image ASC
    LIMIT ' . (int) $offset . ', ' . (int) $perPage;
$rows = Db::getInstance()->executeS($sql);

function getImageRelativePath($idImage, $type = 'large_default')
{
    $idImage = (string) $idImage;
    $folders = implode('/', str_split($idImage));
    return 'img/p/' . $folders . '/' . $idImage . '-' . $type . '.jpg';
}

function getOriginalImageRelativePath($idImage)
{
    $idImage = (string) $idImage;
    $folders = implode('/', str_split($idImage));
    return 'img/p/' . $folders . '/' . $idImage . '.jpg';
}

$baseUrl = Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__;
$adminDir = null;

foreach (scandir(__DIR__) as $file) {
    if (strpos($file, 'admin') === 0 && is_dir(__DIR__ . '/' . $file)) {
        $adminDir = $file;
        break;
    }
}

$totalPages = max(1, (int) ceil($total / $perPage));

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Listado large_default</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 24px;
            background: #f6f6f6;
            color: #222;
        }

        h1 {
            margin-bottom: 8px;
        }

        .summary {
            margin-bottom: 20px;
            color: #555;
        }

        form {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            background: #fff;
            padding: 14px;
            border: 1px solid #ddd;
        }

        input[type="text"],
        input[type="number"] {
            padding: 8px;
            border: 1px solid #bbb;
            min-width: 220px;
        }

        button,
        .button {
            padding: 8px 12px;
            background: #222;
            color: #fff;
            text-decoration: none;
            border: 0;
            cursor: pointer;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 18px;
        }

        .card {
            background: #ccc;
            border: 1px solid #ddd;
            padding: 12px;
        }

        .thumb-wrap {
            width: 100%;
            aspect-ratio: 1 / 1;
            background: repeating-conic-gradient(#eee 0% 25%, #fff 0% 50%) 50% / 20px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }

        .thumb-wrap img {
            max-width: 100%;
            max-height: 100%;
            display: block;
        }

        .missing {
            color: #b00020;
            font-weight: bold;
            text-align: center;
        }

        .meta {
            font-size: 13px;
            line-height: 1.45;
        }

        .meta strong {
            display: inline-block;
            min-width: 88px;
        }

        .name {
            font-weight: bold;
            margin-bottom: 8px;
        }

        .links {
            margin-top: 10px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .links a {
            font-size: 12px;
            color: #005b96;
        }

        .cover {
            display: inline-block;
            padding: 2px 6px;
            background: #d9f0d3;
            border: 1px solid #9ac58f;
            font-size: 12px;
            margin-left: 4px;
        }

        .pager {
            margin: 24px 0;
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .note {
            background: #fff3cd;
            border: 1px solid #ffe08a;
            padding: 12px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<h1>Listado de imágenes large_default</h1>

<div class="summary">
    Total encontradas: <strong><?php echo (int) $total; ?></strong> |
    Página <strong><?php echo (int) $page; ?></strong> de <strong><?php echo (int) $totalPages; ?></strong>
</div>

<div class="note">
    Revisa visualmente las miniaturas. Si ves marco blanco en esta pantalla, el marco está dentro del archivo generado
    <strong>large_default</strong>.
</div>

<form method="get">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($TOKEN); ?>">

    <label>
        Buscar producto / ID producto / ID imagen:
        <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>">
    </label>

    <label>
        Por página:
        <input type="number" name="per_page" value="<?php echo (int) $perPage; ?>" min="20" max="200">
    </label>

    <label>
        <input type="checkbox" name="cover" value="1" <?php echo $onlyCover ? 'checked' : ''; ?>>
        Solo portadas
    </label>

    <button type="submit">Filtrar</button>
</form>

<div class="grid">
    <?php foreach ($rows as $row): ?>
        <?php
        $idProduct = (int) $row['id_product'];
        $idImage = (int) $row['id_image'];

        $largePath = getImageRelativePath($idImage, 'large_default');
        $originalPath = getOriginalImageRelativePath($idImage);

        $largeFile = __DIR__ . '/' . $largePath;
        $originalFile = __DIR__ . '/' . $originalPath;

        $largeUrl = $baseUrl . $largePath;
        $originalUrl = $baseUrl . $originalPath;

        $largeExists = file_exists($largeFile);
        $originalExists = file_exists($originalFile);

        $largeInfo = $largeExists ? getimagesize($largeFile) : null;
        $originalInfo = $originalExists ? getimagesize($originalFile) : null;

        $adminProductUrl = '';
        if ($adminDir) {
            $adminProductUrl = $baseUrl . $adminDir . '/index.php/sell/catalog/products/' . $idProduct;
        }
        ?>

        <div class="card">
            <div class="thumb-wrap">
                <?php if ($largeExists): ?>
                    <a href="<?php echo htmlspecialchars($largeUrl); ?>" target="_blank">
                        <img src="<?php echo htmlspecialchars($largeUrl); ?>" alt="">
                    </a>
                <?php else: ?>
                    <div class="missing">No existe large_default</div>
                <?php endif; ?>
            </div>

            <div class="name">
                <?php echo htmlspecialchars($row['name']); ?>
                <?php if ((int) $row['cover'] === 1): ?>
                    <span class="cover">Portada</span>
                <?php endif; ?>
            </div>

            <div class="meta">
                <div><strong>ID producto:</strong> <?php echo $idProduct; ?></div>
                <div><strong>ID imagen:</strong> <?php echo $idImage; ?></div>
                <div><strong>Referencia:</strong> <?php echo htmlspecialchars($row['reference'] ?: '-'); ?></div>

                <div>
                    <strong>Large:</strong>
                    <?php
                    if ($largeInfo) {
                        echo (int) $largeInfo[0] . ' × ' . (int) $largeInfo[1] . ' px';
                        echo ' / ' . round(filesize($largeFile) / 1024, 1) . ' KB';
                    } else {
                        echo 'No existe';
                    }
                    ?>
                </div>

                <div>
                    <strong>Original:</strong>
                    <?php
                    if ($originalInfo) {
                        echo (int) $originalInfo[0] . ' × ' . (int) $originalInfo[1] . ' px';
                        echo ' / ' . round(filesize($originalFile) / 1024, 1) . ' KB';
                    } else {
                        echo 'No existe';
                    }
                    ?>
                </div>
            </div>

            <div class="links">
                <?php if ($adminProductUrl): ?>
                    <a href="<?php echo htmlspecialchars($adminProductUrl); ?>" target="_blank">Editar producto</a>
                <?php endif; ?>

                <?php if ($largeExists): ?>
                    <a href="<?php echo htmlspecialchars($largeUrl); ?>" target="_blank">Abrir large_default</a>
                <?php endif; ?>

                <?php if ($originalExists): ?>
                    <a href="<?php echo htmlspecialchars($originalUrl); ?>" target="_blank">Abrir original</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="pager">
    <?php
    $baseParams = [
        'token' => $TOKEN,
        'q' => $q,
        'per_page' => $perPage,
    ];

    if ($onlyCover) {
        $baseParams['cover'] = 1;
    }

    if ($page > 1) {
        $prevParams = array_merge($baseParams, ['page' => $page - 1]);
        echo '<a class="button" href="?' . htmlspecialchars(http_build_query($prevParams)) . '">Anterior</a>';
    }

    echo '<span>Página ' . (int) $page . ' de ' . (int) $totalPages . '</span>';

    if ($page < $totalPages) {
        $nextParams = array_merge($baseParams, ['page' => $page + 1]);
        echo '<a class="button" href="?' . htmlspecialchars(http_build_query($nextParams)) . '">Siguiente</a>';
    }
    ?>
</div>

</body>
</html>