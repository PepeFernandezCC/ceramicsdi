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
$sql = 'SELECT id_category, id_lang, name, meta_title FROM ps_category_lang WHERE id_category != 1';
$results = $db->executeS($sql);

// Mostrar resultados en tabla HTML
echo '<table border="1" cellpadding="5" cellspacing="0">';
echo '<tr><th>ID Categoría</th><th>ID Lenguaje</th><th>URL</th><th>name</th><th>MetaTitle(h1)</th></tr>';

foreach ($results as $row) {
    $id_category = $row['id_category'];
    $id_lang = $row['id_lang'];
    $metaTitle = $row['meta_title'];
    $name = htmlspecialchars($row['name']); // Evitar HTML roto
    $category_url = $link->getCategoryLink($id_category, null, $id_lang);

    echo '<tr>';
    echo '<td>' . $id_category . '</td>';
    echo '<td>' . $id_lang . '</td>';
    echo '<td> '. $category_url . '</td>';
    echo '<td>' . $name . '</td>';
    echo '<td>' . $metaTitle . '</td>';


    echo '</tr>';
}

echo '</table>';
?>
