<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Builds the Google/Facebook style product metadata CSV for one language,
 * with every product's active status, features and category path resolved
 * for that language.
 */
class ProductMetaCsvGenerator
{
    const FEATURE_FINISH_ID = 7;     // Acabado
    const FEATURE_MATERIAL_ID = 45;  // Material
    const FEATURE_SIZE_ID = 4;       // Medidas Pieza
    const FEATURE_WIDTH_ID = 5;      // Espesor
    const FEATURE_COLOR_ID = 46;     // Color

    const BRAND = 'Ceramic Connection';

    const HEADERS = [
        'id',
        'description',
        'image_link',
        'title',
        'availability',
        'finish',
        'material',
        'condition',
        'size',
        'height',
        'length',
        'width',
        'g:additional_image',
        'g:color',
        'g:google_product_category',
        'g:gtin',
        'price',
        'link',
        'fb_product_category',
        'additional_features',
        'brand',
    ];

    /**
     * Fixed strings that must be translated per language but never come
     * from a feature/DB value. Keyed by iso_code.
     */
    private static $translations = [
        'es' => [
            'google_product_category' => 'Bricolaje > Materiales de construcción > Baldosas',
            'additional_features' => 'Suelo y pared',
        ],
        'fr' => [
            'google_product_category' => 'Bricolage > Matériaux de construction > Carrelage',
            'additional_features' => 'Sol et mur',
        ],
        'en' => [
            'google_product_category' => 'Hardware > Building Materials > Tile',
            'additional_features' => 'Floor and wall',
        ],
        'de' => [
            'google_product_category' => 'Heimwerkerbedarf > Baumaterial > Fliesen',
            'additional_features' => 'Boden und Wand',
        ],
        'pt' => [
            'google_product_category' => 'Bricolagem > Materiais de construção > Ladrilhos',
            'additional_features' => 'Pavimento e parede',
        ],
        'nl' => [
            'google_product_category' => 'Doe-het-zelf > Bouwmaterialen > Tegels',
            'additional_features' => 'Vloer en wand',
        ],
    ];

    private $context;
    private $idLang;
    private $isoCode;
    private $idShop;

    public function __construct(Context $context, $idLang)
    {
        $this->context = $context;
        $this->idLang = (int) $idLang;
        $language = new Language($this->idLang);
        $this->isoCode = strtolower($language->iso_code);
        $idShop = ($context->shop instanceof Shop) ? (int) $context->shop->id : 0;
        $this->idShop = $idShop ?: (int) Configuration::get('PS_SHOP_DEFAULT');
    }

    /**
     * Streams the CSV for the given language directly to the browser.
     */
    public static function output(Context $context, $idLang)
    {
        $generator = new self($context, $idLang);
        $rows = $generator->buildRows();

        $filename = 'meta_' . $generator->isoCode . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $handle = fopen('php://output', 'w');
        fwrite($handle, self::csvLine(self::HEADERS));
        foreach ($rows as $row) {
            fwrite($handle, self::csvLine(array_map([__CLASS__, 'sanitizeField'], $row)));
        }
        fclose($handle);
    }

    /**
     * fputcsv() solo entrecomilla un campo si contiene el delimitador, la
     * comilla, un espacio o un salto de línea — un valor como "7,5x30"
     * (sin espacio) se queda SIN comillas, y como el texto libre del feed
     * (descripciones, etc.) está lleno de comas de verdad, algunos lectores
     * de CSV (Google Sheets incluido) detectan mal el delimitador real y
     * confunden esa coma suelta con un separador. Para evitarlo, forzamos
     * comillas en todos los campos siempre, sin excepción.
     */
    private static function csvLine(array $fields, $delimiter = ';', $enclosure = '"')
    {
        $encoded = array_map(function ($field) use ($enclosure) {
            $field = str_replace($enclosure, $enclosure . $enclosure, (string) $field);

            return $enclosure . $field . $enclosure;
        }, $fields);

        return implode($delimiter, $encoded) . "\r\n";
    }

    /**
     * @return array<int, array<string>> rows in the same order as self::HEADERS
     */
    public function buildRows()
    {
        $products = $this->getActiveProducts();
        $rows = [];

        foreach ($products as $product) {
            $idProduct = (int) $product['id_product'];

            $sizeRaw = (string) Product::getProductAttribute($idProduct, self::FEATURE_SIZE_ID, $this->idLang);
            list($height, $length) = $this->parseSize($sizeRaw);

            $rows[] = [
                $idProduct,
                $this->cleanText($product['description']),
                $this->getCoverImageUrl($idProduct, $product['link_rewrite']),
                $this->cleanTitle($product['meta_title']),
                $this->getAvailability($idProduct),
                (string) Product::getProductAttribute($idProduct, self::FEATURE_FINISH_ID, $this->idLang),
                (string) Product::getProductAttribute($idProduct, self::FEATURE_MATERIAL_ID, $this->idLang),
                'new',
                $sizeRaw,
                $height,
                $length,
                $this->cleanNumeric(Product::getProductAttribute($idProduct, self::FEATURE_WIDTH_ID, $this->idLang)),
                $this->getAdditionalImageUrl($idProduct, $product['link_rewrite']),
                (string) Product::getProductAttribute($idProduct, self::FEATURE_COLOR_ID, $this->idLang),
                $this->translation('google_product_category'),
                $this->getGtin($idProduct),
                $this->getFormattedPrice($idProduct),
                $this->context->link->getProductLink($idProduct, null, null, null, $this->idLang, $this->idShop),
                $this->getCategoryPath((int) $product['id_category_default']),
                $this->translation('additional_features'),
                self::BRAND,
            ];
        }

        return $rows;
    }

    private function translation($key)
    {
        if (isset(self::$translations[$this->isoCode][$key])) {
            return self::$translations[$this->isoCode][$key];
        }

        // Fallback to Spanish if the shop ever adds a language we haven't mapped.
        return self::$translations['es'][$key];
    }

    private function getActiveProducts()
    {
        $sql = '
            SELECT p.id_product, p.ean13, p.id_category_default,
                   pl.description, pl.meta_title, pl.link_rewrite
            FROM `' . _DB_PREFIX_ . 'product` p
            INNER JOIN `' . _DB_PREFIX_ . 'product_shop` ps
                ON (ps.id_product = p.id_product AND ps.id_shop = ' . $this->idShop . ')
            INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (pl.id_product = p.id_product AND pl.id_lang = ' . $this->idLang . ' AND pl.id_shop = ' . $this->idShop . ')
            WHERE ps.active = 1
            ORDER BY p.id_product ASC
        ';

        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return is_array($rows) ? $rows : [];
    }

    private function getAvailability($idProduct)
    {
        $quantity = StockAvailable::getQuantityAvailableByProduct($idProduct, 0, $this->idShop);

        return $quantity > 0 ? 'in_stock' : 'out_of_stock';
    }

    private function getGtin($idProduct)
    {
        $ean13 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT ean13 FROM `' . _DB_PREFIX_ . 'product` WHERE id_product = ' . (int) $idProduct
        );

        return trim((string) $ean13);
    }

    private function getFormattedPrice($idProduct)
    {
        $price = Product::getPriceStatic($idProduct, true, null, 2);

        return number_format((float) $price, 2, '.', '') . ' EUR';
    }

    private function parseSize($size)
    {
        $height = '';
        $length = '';

        if (preg_match('/([0-9]+(?:[.,][0-9]+)?)\s*[xX]\s*([0-9]+(?:[.,][0-9]+)?)/', $size, $matches)) {
            $height = str_replace(',', '.', $matches[1]);
            $length = str_replace(',', '.', $matches[2]);
        }

        return [$height, $length];
    }

    private function getCoverImageUrl($idProduct, $linkRewrite)
    {
        $idImage = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT id_image FROM `' . _DB_PREFIX_ . 'image`
             WHERE id_product = ' . (int) $idProduct . ' AND cover = 1'
        );

        if (!$idImage) {
            $idImage = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
                'SELECT id_image FROM `' . _DB_PREFIX_ . 'image`
                 WHERE id_product = ' . (int) $idProduct . '
                 ORDER BY position ASC'
            );
        }

        return $this->getImageUrl($idImage, $linkRewrite);
    }

    private function getAdditionalImageUrl($idProduct, $linkRewrite)
    {
        $idImage = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT id_image FROM `' . _DB_PREFIX_ . 'image`
             WHERE id_product = ' . (int) $idProduct . ' AND position = 2'
        );

        return $this->getImageUrl($idImage, $linkRewrite);
    }

    /**
     * Builds the real store URL for a product image (same helper the shop
     * itself uses, Link::getImageLink()), instead of a filesystem path.
     */
    private function getImageUrl($idImage, $linkRewrite)
    {
        if ((int) $idImage <= 0) {
            return '';
        }

        return $this->context->link->getImageLink((string) $linkRewrite, (int) $idImage);
    }

    /**
     * Builds "Home > Category" following the product's default category
     * up to (and including) the shop's home category, using the names in
     * the requested language. Works whatever the actual category ids are
     * (106 "Azulejo", 80 "Otros materiales", or any other branch).
     */
    private function getCategoryPath($idCategoryDefault)
    {
        $idHomeCategory = (int) Configuration::get('PS_HOME_CATEGORY');
        $names = [];
        $idCategory = $idCategoryDefault;
        $guard = 0;

        while ($idCategory > 0 && $guard < 15) {
            $guard++;

            $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
                SELECT c.id_parent, cl.name
                FROM `' . _DB_PREFIX_ . 'category` c
                LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
                    ON (cl.id_category = c.id_category AND cl.id_lang = ' . $this->idLang . ' AND cl.id_shop = ' . $this->idShop . ')
                WHERE c.id_category = ' . (int) $idCategory
            );

            if (!$row) {
                break;
            }

            array_unshift($names, (string) $row['name']);

            if ((int) $idCategory === $idHomeCategory) {
                break;
            }

            $idCategory = (int) $row['id_parent'];
        }

        return implode(' > ', $names);
    }

    /**
     * Limpieza final antes de escribir cada campo: quita saltos de línea y
     * tabulaciones para que ninguna celda se "parta" visualmente en varias
     * líneas dentro del CSV. El delimitador ';' ya no necesita tratamiento
     * especial: csvLine() entrecomilla todos los campos siempre.
     */
    private static function sanitizeField($value)
    {
        $value = (string) $value;
        $value = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $value);
        $value = preg_replace('/\s+/u', ' ', $value);

        return trim($value);
    }

    private function cleanText($value)
    {
        $value = (string) $value;
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = strip_tags($value);
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', ' ', $value);
        $value = preg_replace('/\s+/u', ' ', $value);

        return trim($value);
    }

    /**
     * meta_title suele venir como "Nombre del producto | Ceramic Connection";
     * para el feed solo queremos el nombre.
     */
    private function cleanTitle($value)
    {
        $value = trim((string) $value);

        return trim(preg_replace('/\s*\|\s*Ceramic Connection\s*$/i', '', $value));
    }

    /**
     * Extrae el número de un valor de feature tipo "0,8 mm" y lo deja como
     * "0.8" (sin unidad, con punto decimal) para no romper el CSV.
     */
    private function cleanNumeric($value)
    {
        $value = trim((string) $value);

        if (preg_match('/([0-9]+(?:[.,][0-9]+)?)/', $value, $matches)) {
            return str_replace(',', '.', $matches[1]);
        }

        return '';
    }
}
