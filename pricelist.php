<?php
// Configuración de la base de datos
require_once('config/config.inc.php');
require_once('classes/Link.php');

$link = new Link(); 
$pageTitle = "Productos por Colección";
$products = Product::getAllProductsGroupedByCollection();
$anchor = false;

// Verificar si el parámetro "collection" está presente
if (isset($_GET['collection']) && !empty($_GET['collection'])) {
    $anchor = true;
}

if (!$products) {
    die('No se encontraron productos.');
}

// Agrupar productos por colección
$collections = [];
foreach ($products as $product) {
    $collections[$product['id_feature_value']][] = $product;
}

//Atributes
$formatAttributeID = 4;
$materialAttributeID = 45;
// Generar HTML dinámico
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="noindex">
        <title><?php echo $pageTitle; ?></title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                line-height: 1.6; 
                text-transform: uppercase;
                margin: 20px; 
                padding: 0;
            }

            .alpha {
                opacity: 0.35;
            }

            .contenedor {
                width: 80%; 
                margin: 0 auto;
            }
            .collection {
                margin-bottom: 40px;
            }

            .w-short {
                width: 15%;
            }
            .w-large {
                width: 70%
            }

            .collection h2 {
                margin-bottom: 20px;
                color: #333;
            }

            .product-list {
                display: flex;
                flex-direction: column;
            }

            .product-header, .product {
                display: flex;
                align-items: center;
                padding: 10px;
                border-bottom: 1px solid #ccc;
                font-size: 1rem;
            }

            .product-header div, .product div {
                text-align: left;
                padding: 0 10px;
            }

            .product-header {
                font-weight: bold;
                background-color: #f4f4f4;
            }

            /* Estilos para los enlaces (sin subrayado ni color azul) */
            .product a {
                text-decoration: none; /* Elimina el subrayado */
                color: inherit; /* Hereda el color del texto */
                display: block; /* Para que ocupe todo el área del div */
            }

            /* Cambio de fondo cuando el ratón pasa por encima */
            .product:hover {
                background-color: #f9f9f9; /* Fondo al pasar el ratón */
            }

            .product-price {
                text-align: right; /* Alinear precio a la derecha */
            }

            .iva-col {
                display:flex;
            }

            .visible-on-mobile {
                display: none;
            }

            .w-ref {
                width: 5%;
            }
            .w-name {
                width: 40%;
            }
            .w-material {
                width: 20%;
            }
            .w-format {
                width: 20%;
            }
            .w-iva {
                width: 15%;
            }

            @media(min-width:781px){
                .iva-left {
                    padding-right: 4px !important;
                    padding-left: 0 !important;
                }
                .iva-right {
                    padding-left: 0 !important;
                }

            }

            @media(max-width:780px){
                .contenedor{
                    width: 100%;
                }
                .w-ref {
                    width: 10%;
                }
                .w-name {
                    width: 55%
                }

                .w-iva {
                    width: 25%;
                }
                .iva-col {
                    display:block;
                }
                .visible-on-mobile {
                    display: block;
                }

                .visible-on-desktop {
                    display: none;
                }
                .w-material {
                    width: 0%;
                    display: none;
                }
                .w-format {
                    width: 0%;
                    display:none
                }
                .padding-name{
                    padding-left:20px !important;
                    padding-right: 0 !important;
                }
                .special-format{
                    font-size: 11px;
                    color:rgb(0, 0, 0);
                    font-style: italic;
                }
                .fs-20-mobile{
                    font-size:20px !important;
                }
                .fs-13-mobile{
                    font-size:13px !important;
                }
            }

        </style>
    </head>
    <body>
        <div class="contenedor">
            <h2 class="fs-20-mobile"><?php echo $pageTitle; ?></h2>

            <?php foreach ($collections as $collectionId => $products): ?>
                <div id="<?php echo $collectionId; ?>" class="collection <?php echo $anchor ? 'alpha' : ''; ?>">
                <h2 class="fs-20-mobile"><?php echo htmlspecialchars($products[0]['value']); ?>.</h2>
                    <div class="product-list fs-13-mobile">
                        <!-- Cabecera de columnas -->
                        <div class="product-header">
                            <div class="w-ref fs-13-mobile">Ref.</div>
                            <div class="w-name padding-name fs-13-mobile">Nombre</div>
                            <div class="w-format visible-on-desktop">Formato</div>
                            <div class="w-material visible-on-desktop">Material</div>
                            <div class="w-iva iva-col">
                                <div class="iva-left fs-13-mobile">Precio </div>
                                <div class="iva-right fs-13-mobile">(<span style="text-transform: lowercase">Sin </span>IVA)</div>
                            </div>
                        </div>

                        <!-- Lista de productos -->
                        <?php foreach ($products as $product): ?>
                            <?php $productLink = $link->getProductLink($product['id_product']); ?>
                            <?php $customPrice = Product::calculateCustomPrice($product['id_product'], false)?>
                            <?php $productFormat = Product::getProductAttribute($product['id_product'], $formatAttributeID)?>
                            <?php $productMaterial = Product::getProductAttribute($product['id_product'], $materialAttributeID)?>

                            <a href="<?php echo htmlspecialchars($productLink); ?>" style="text-decoration: none; color: black">
                                <div class="product">
                                    <div class="w-ref fs-13-mobile"><strong><?php echo htmlspecialchars($product['reference']); ?></strong></div>
                                    <div class="w-name fs-13-mobile">
                                        <div><?php echo htmlspecialchars($product['name']);?></div>
                                        <div class="visible-on-mobile special-format"><?php if($productFormat){echo htmlspecialchars($productFormat);}?></div>    
                                    </div>
                                    <div class="w-format visible-on-desktop"><?php if($productFormat){echo htmlspecialchars($productFormat);}?></div>
                                    <div class="w-material visible-on-desktop"><?php if($productMaterial){echo htmlspecialchars($productMaterial);}?></div>
                                    <div class="w-iva product-price fs-13-mobile">
                                        <strong>
                                            <?php $tipologia = $customPrice['tipologia'] === '/piece' ? '/pieza' : $customPrice['tipologia'];
                                            echo $customPrice['price'] . ' €' . $tipologia;?>
                                        </strong>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </body>

    <!-- JavaScript para hacer scroll al div correspondiente -->
    <script>
        // Obtener el valor del parámetro 'collection' de la URL
        const urlParams = new URLSearchParams(window.location.search);
        const collectionId = urlParams.get('collection');

        // Si el parámetro 'collection' está presente en la URL, desplazarse al div correspondiente
        if (collectionId) {
            const targetDiv = document.getElementById(collectionId);
            if (targetDiv) {
                targetDiv.classList.remove('alpha');
                targetDiv.scrollIntoView({ behavior: 'smooth' });
            }
        }
    </script>
</html>
