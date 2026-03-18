<?php
// PrestaShop core
require_once('config/config.inc.php');
require_once('init.php'); // Necesario para la instancia de contexto en algunos casos
require_once('classes/Link.php');

// Crear instancia de Link
$link = new Link();

// Conexión con la base de datos de PrestaShop
$db = Db::getInstance();

// Consulta SQL
//$sql = 'SELECT id_product, id_lang, description, meta_title, name FROM ps_product_lang';
$sql = 'SELECT DISTINCT pl.id_product, pl.id_lang, pl.description, pl.meta_title, pl.name
            FROM ps_product_lang pl
            JOIN ps_product p ON pl.id_product = p.id_product
            JOIN ps_category_product cp ON pl.id_product = cp.id_product
            WHERE pl.id_lang = 1
            AND p.active = 1
            AND cp.id_category != 1751';
$results = $db->executeS($sql);

// Mostrar resultados en tabla HTML
echo '<table border="1" cellpadding="5" cellspacing="0">';
echo '<tr><th>ID Producto</th><th>ID Lenguaje</th><th>URL</th><th>MetaTitle(h1)</th><th>Descripción</th></tr>';

foreach ($results as $row) {
    $id_product = $row['id_product'];
    $id_lang = $row['id_lang'];
    $metaTitle = $row['name'];
    $description = htmlspecialchars($row['description']); // Evitar HTML roto
    $product_url = $link->getProductLink($id_product, null, null, null, $id_lang);

    echo '<tr>';
    echo '<td>' . $id_product . '</td>';
    echo '<td>' . $id_lang . '</td>';
    echo '<td> '. $product_url . '</td>';
    echo '<td>' . $metaTitle . '</td>';
    echo '<td>' . $description . '</td>';


    echo '</tr>';
}

echo '</table>';
?>
