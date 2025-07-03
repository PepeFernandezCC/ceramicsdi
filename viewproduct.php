<?php
// PrestaShop core
require_once('config/config.inc.php');
require_once('classes/Link.php');
require_once('classes/Product.php');

$link = new Link();

// Obtener el ID del producto desde la URL
$id_product = isset($_GET['idproduct']) ? (int)$_GET['idproduct'] : 0;

// Si no hay producto, se puede manejar con un error simple
if ($id_product <= 0) {
    die('Producto no válido');
}

// Si se hace clic en un idioma
if (isset($_GET['id_lang'])) {
    $id_lang = (int)$_GET['id_lang'];

    // Validar que el idioma esté entre los aceptados
    if (in_array($id_lang, [1,2,3,4,5,6])) {
        $product_url = $link->getProductLink($id_product, null, null, null, $id_lang);
        header("Location: $product_url");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Language</title>
    <style>
        body {
            background-color: #f9f9f9;
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            text-align: center;
            padding: 80px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }
        img.logo {
            margin-bottom: 80px;
        }
        .language-button {
            display: inline-block;
            margin: 15px;
            padding: 30px 20px;
            font-size: 49px;
            font-weight: 600;
            background:rgb(255, 255, 255);
            color: black;
            border: 1px solid;
            cursor: pointer;
            text-decoration: none;
        }

        .flag {
            padding-right: 10px;
        }
        .text {
            width: 470px;
        }
        

    </style>
</head>
<body>
    <div class="container">
        <img src="https://ceramicconnection.com/img/logo-1682330097.jpg" alt="Ceramic Connection logo" class="logo">

        <a class="language-button" href="?idproduct=<?= $id_product ?>&id_lang=1">
            <div style="display:flex;align-items: center;justify-content: center;">
                <div class="flag"><img src="themes/child_classic/assets/img/web/spain.png" width="55px"></div>
                <div class="text">Español</div>
            </div>
        </a>
        <a class="language-button" href="?idproduct=<?= $id_product ?>&id_lang=2">
            <div style="display:flex;align-items: center;justify-content: center;">
                <div class="flag"><img src="themes/child_classic/assets/img/web/france.png" width="55px"></div>
                <div class="text">Français</div>
            </div>
        </a>
        <a class="language-button" href="?idproduct=<?= $id_product ?>&id_lang=3">
            <div style="display:flex;align-items: center;justify-content: center;">
                <div class="flag"><img src="themes/child_classic/assets/img/web/united-kingdom.png" width="55px"></div>
                <div class="text">English</div>
            </div>
        </a>
        <a class="language-button" href="?idproduct=<?= $id_product ?>&id_lang=4">
            <div style="display:flex;align-items: center;justify-content: center;">
                <div class="flag"><img src="themes/child_classic/assets/img/web/germany.png" width="55px"></div>
                <div class="text">Deutsch</div>
            </div>
        </a>
        <a class="language-button" href="?idproduct=<?= $id_product ?>&id_lang=5">
            <div style="display:flex;align-items: center;justify-content: center;">
                <div class="flag"><img src="themes/child_classic/assets/img/web/portugal.png" width="55px"></div>
                <div class="text">Português</div>
            </div>
        </a>
        <a class="language-button" href="?idproduct=<?= $id_product ?>&id_lang=6">
            <div style="display:flex;align-items: center;justify-content: center;">
                <div class="flag"><img src="themes/child_classic/assets/img/web/netherland.png" width="55px"></div>
                <div class="text">Nederlands</div>
            </div> 
        </a>
    </div>
</body>
</html>

