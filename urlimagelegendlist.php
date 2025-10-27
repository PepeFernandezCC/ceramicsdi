<?php
/**
 * Exporta: id_image, id_product, name (producto), url_imagen, id_lang, legend
 * Tablas usadas: ps_image, ps_image_lang, ps_product_lang
 * Uso: súbelo a la raíz de tu tienda (o a /admin si prefieres) y ejecútalo en el navegador.
 * Opcional: ?type=home_default para cambiar el tipo de imagen (home_default por defecto)
 */

// Núcleo PrestaShop
require_once __DIR__ . '/config/config.inc.php';
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/classes/Link.php';

// Instancias
$link = new Link();
$db   = Db::getInstance();

// Permitir elegir el tipo de imagen por query string (?type=large_default, etc.)
$imageType = Tools::getValue('type', 'home_default');

// Consulta: une imágenes con su idioma y el producto (mismo id_lang)
$sql = '
    SELECT
        i.id_image,
        i.id_product,
        pl.id_lang,
        pl.name,
        pl.link_rewrite,
        il.legend
    FROM ' . _DB_PREFIX_ . 'image i
    INNER JOIN ' . _DB_PREFIX_ . 'image_lang il
        ON il.id_image = i.id_image
    INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl
        ON pl.id_product = i.id_product
       AND pl.id_lang = il.id_lang
    ORDER BY i.id_product, i.id_image, pl.id_lang
';

$rows = $db->executeS($sql);

// Salida HTML
echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Export imágenes</title>';
echo '<style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;padding:16px}
        table{border-collapse:collapse;width:100%}
        th,td{border:1px solid #ddd;padding:8px;font-size:14px;vertical-align:top}
        th{background:#f5f5f7;text-align:left;position:sticky;top:0}
        tr:nth-child(even){background:#fafafa}
        code{background:#f2f2f2;padding:2px 4px;border-radius:4px}
      </style></head><body>';

echo '<h1>Listado de imágenes de productos</h1>';
echo '<p>Tipo de imagen: <code>' . htmlspecialchars($imageType) . '</code> &middot; Cambia con <code>?type=home_default</code>, <code>?type=large_default</code>, etc.</p>';

echo '<table>';
echo '<tr>
        <th>id_image</th>
        <th>id_product</th>
        <th>name (product)</th>
        <th>url_imagen</th>
        <th>id_lang</th>
        <th>legend</th>
      </tr>';

if ($rows) {
    foreach ($rows as $r) {
        $idImage    = (int)$r['id_image'];
        $idProduct  = (int)$r['id_product'];
        $idLang     = (int)$r['id_lang'];
        $name       = htmlspecialchars((string)$r['name'] ?? '');
        $legend     = htmlspecialchars((string)$r['legend'] ?? '');
        $rewrite    = (string)$r['link_rewrite'] ?? '';

        // Construir URL de imagen al estilo PrestaShop
        // Ejemplo que pides: https://tudominio.com/12342-home_default/azulejo-porcelanico-trentino.jpg
        $imgUrl = $link->getImageLink($rewrite, $idImage, $imageType, $idLang);

        echo '<tr>';
        echo '<td>' . $idImage . '</td>';
        echo '<td>' . $idProduct . '</td>';
        echo '<td>' . $name . '</td>';
        echo '<td><a href="' . htmlspecialchars($imgUrl) . '" target="_blank" rel="noopener">' . htmlspecialchars($imgUrl) . '</a></td>';
        echo '<td>' . $idLang . '</td>';
        echo '<td>' . $legend . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="6">No se encontraron resultados.</td></tr>';
}

echo '</table>';
echo '</body></html>';
