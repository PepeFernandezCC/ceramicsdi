<?php
/**
 * Migas de pan (breadcrumbs) de categoría y producto, generadas en PHP
 * en el momento del render (Smarty), sin depender de JavaScript.
 *
 * Sustituye a la lógica que antes vivía en
 * modules/addsampleoncatalog/views/js/front.js (bloque
 * "bread-crumps-container"): mismo comportamiento (mismas reglas de
 * negrita/omisión de miga, mismos textos por idioma), pero calculado
 * server-side a partir de la URL actual y de las variables que ya
 * calculan catalog/product.tpl y _partials/info-category.tpl, y
 * devuelto como HTML listo para pintar (más el JSON-LD de
 * BreadcrumbList, que antes se inyectaba con JS tras montar el DOM).
 *
 * Uso desde las plantillas (igual que otras llamadas directas a
 * clases PHP ya presentes en este tema, p.ej. Category::getFatherCategory()):
 *
 *   {CcBreadcrumbs::renderProduct($category, $productColor, $validAspect, $validAspectId, $validAspectName) nofilter}
 *   {CcBreadcrumbs::renderCategory($category, $normalized_title, $normalized_father_title) nofilter}
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class CcBreadcrumbs extends Module
{
    /**
     * IDs de categoría "padre" que activan las reglas especiales de
     * miga en negrita/omitida (rama de "Aspecto": imitación madera,
     * piedra, etc.). Mismos valores que ya usaba el JS original.
     */
    const SPECIAL_FATHER_CATEGORY_IDS = [88, 1770, 1771];

    const BOLD_STYLE = 'style="font-weight:bolder; color:black"';

    /** Textos "inicio"/"azulejos" del breadcrumb, por idioma (código de idioma en la URL). */
    const TILE_TEXT_BY_LANG = [
        'es' => 'Azulejos',
        'fr' => 'Carrelage',
        'en' => 'Tiles',
        'de' => 'Fliesen',
        'pt' => 'Azulejos',
        'nl' => 'Tegels',
    ];

    public function __construct()
    {
        $this->name = 'ccbreadcrumbs';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Ceramic Connection';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Migas de pan (breadcrumbs) sin JS');
        $this->description = $this->l('Genera en PHP las migas de pan de categoría y producto que antes construía front.js.');

        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * No pinta nada: el único propósito de engancharse a este hook es que
     * PrestaShop cargue esta clase (via Module::getInstanceByName) en toda
     * página de tienda, para que las plantillas puedan llamar a
     * CcBreadcrumbs::renderCategory()/renderProduct() directamente. Sin un
     * hook activo el fichero del módulo nunca se incluye (los módulos no
     * están en el autoload de clases del core) y esas llamadas estáticas
     * lanzan ClassNotFoundException.
     */
    public function hookDisplayHeader($params)
    {
        return '';
    }

    /**
     * Migas de pan de una ficha de producto.
     *
     * @param array|object $category      El $category de la plantilla (array Smarty u objeto Category).
     * @param string       $productColor  data-color original: nombre del color del producto, o 'none'.
     * @param bool         $validAspect   Si el producto tiene un "aspecto" válido (imitación madera, etc.).
     * @param int|null     $validAspectId id_feature_value del aspecto válido.
     * @param string|null  $validAspectName Nombre del aspecto válido.
     */
    public static function renderProduct($category, $productColor, $validAspect, $validAspectId = null, $validAspectName = null)
    {
        $context = Context::getContext();
        $idCategory = (int) self::getCategoryField($category, 'id_category', self::getCategoryField($category, 'id'));

        $categoryLink = $context->link->getCategoryLink($idCategory);

        return self::render([
            'location' => 'product',
            'color' => $productColor !== null && $productColor !== '' ? $productColor : 'none',
            'lastSegmentLink' => $validAspect
                ? ['href' => $context->link->getCategoryLinkByIdFeatureValue((int) $validAspectId), 'label' => $validAspectName]
                : null,
            'middleIndex1Link' => $categoryLink,
            'middleIndex1Plural' => (bool) $validAspect,
        ]);
    }

    /**
     * Migas de pan de una página de categoría.
     *
     * @param array|object $category              El $category de la plantilla.
     * @param string       $normalizedTitle        data-title original ($normalized_title).
     * @param string       $normalizedFatherTitle  data-father original ($normalized_father_title).
     */
    public static function renderCategory($category, $normalizedTitle, $normalizedFatherTitle)
    {
        $idParent = (int) self::getCategoryField($category, 'id_parent');

        return self::render([
            'location' => 'category',
            'color' => 'none',
            'metaTitle' => $normalizedTitle,
            'fatherTitle' => $normalizedFatherTitle,
            'fatherCategory' => $idParent,
        ]);
    }

    private static function getCategoryField($category, $field, $default = 0)
    {
        if (is_array($category)) {
            return isset($category[$field]) ? $category[$field] : $default;
        }
        if (is_object($category)) {
            return isset($category->$field) ? $category->$field : $default;
        }

        return $default;
    }

    /**
     * Motor común: replica 1:1 la lógica que antes vivía en front.js
     * (parseo de segmentos de la URL actual, reglas de negrita/omisión
     * por índice, textos por idioma) pero en PHP y a partir de
     * $_SERVER, en vez de window.location.
     */
    private static function render(array $opts)
    {
        $context = Context::getContext();
        $isoLang = $context->language->iso_code;

        $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        $path = parse_url($requestUri, PHP_URL_PATH);
        $segments = array_values(array_filter(explode('/', trim((string) $path, '/')), function ($s) {
            return $s !== '';
        }));
        $segments = array_map('rawurldecode', $segments);

        if (empty($segments)) {
            return '';
        }

        $protocol = Tools::getCurrentUrlProtocolPrefix();
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $origin = $protocol . $host;
        $baseUrl = $origin . '/' . $segments[0];
        $currentUrl = $origin . $requestUri;

        $lang = $segments[0];
        $tileText = isset(self::TILE_TEXT_BY_LANG[$lang]) ? self::TILE_TEXT_BY_LANG[$lang] : self::TILE_TEXT_BY_LANG['es'];

        $location = $opts['location'];
        $color = $opts['color'];
        $fatherCategory = isset($opts['fatherCategory']) ? (int) $opts['fatherCategory'] : null;

        $html = '';
        $currentPath = $baseUrl;
        $itemListElement = [];
        $lastCount = count($segments);

        foreach ($segments as $index => $segment) {
            if ($index === 0) {
                continue;
            }

            $isLast = ($index === $lastCount - 1);

            // Reglas de omisión de miga (solo aplican en breadcrumb de categoría).
            $ignore = false;
            if ($location === 'category') {
                if ($fatherCategory !== null && !in_array($fatherCategory, self::SPECIAL_FATHER_CATEGORY_IDS, true) && $index === 2) {
                    $ignore = true;
                }
                if ($lastCount >= 5 && $index === 1) {
                    $ignore = true;
                }
                if ($fatherCategory !== null && in_array($fatherCategory, self::SPECIAL_FATHER_CATEGORY_IDS, true) && $index === 1) {
                    $ignore = true;
                }
            }

            // Negrita: la miga índice 1 (raíz de categoría, "azulejos"/"carrelage"/...) siempre en negrita.
            if ($index === 1) {
                $bold = self::BOLD_STYLE;
            } else {
                $bold = '';
                if ($lastCount >= 5 && $index === 3) {
                    $bold = self::BOLD_STYLE;
                }
                if ($fatherCategory !== null && in_array($fatherCategory, self::SPECIAL_FATHER_CATEGORY_IDS, true) && $index === 2) {
                    $bold = self::BOLD_STYLE;
                }
            }

            $currentPath .= '/' . $segment;

            if ($isLast) {
                if ($location === 'product') {
                    if (!empty($opts['lastSegmentLink'])) {
                        // Producto con "aspecto" válido (imitación madera, piedra...).
                        $href = $opts['lastSegmentLink']['href'];
                        $label = $tileText . ' ' . $opts['lastSegmentLink']['label'];
                        $html .= self::crumbLink($href, $label, $bold, $index);
                        $itemListElement[] = self::ldItem(count($itemListElement) + 1, $label, $href);
                    } elseif ($color !== 'none' && $color !== '') {
                        $colorPath = self::getColorPath($baseUrl, $color, $lang);
                        $html .= self::crumbLink($colorPath['url'], $colorPath['name'], $bold, $index);
                        $itemListElement[] = self::ldItem(count($itemListElement) + 1, $colorPath['name'], $colorPath['url']);
                    }
                }

                $lastText = $location === 'category' ? $opts['metaTitle'] : self::convertSlugToTitle($segment);
                $html .= $lastText;

                if ($lastText !== '') {
                    $itemListElement[] = self::ldItem(count($itemListElement) + 1, $lastText, $currentUrl);
                }

                continue;
            }

            // Miga intermedia.
            if ($location === 'category') {
                if (!$ignore) {
                    $label = ($bold !== '' && $index === 3) ? $opts['fatherTitle'] : self::convertSlugToTitle($segment);
                    $html .= self::crumbLink($currentPath, $label, $bold, $index);
                    $itemListElement[] = self::ldItem(count($itemListElement) + 1, $label, $currentPath);
                }
            } else {
                if ($index === 1) {
                    $label = self::convertSlugToTitle($segment) . (!empty($opts['middleIndex1Plural']) ? 's' : '');
                    $href = $opts['middleIndex1Link'];
                } else {
                    $label = self::convertSlugToTitle($segment);
                    $href = $currentPath;
                }
                $html .= self::crumbLink($href, $label, $bold, $index);
                $itemListElement[] = self::ldItem(count($itemListElement) + 1, $label, $href);
            }
        }

        return $html . self::jsonLd($itemListElement);
    }

    private static function crumbLink($href, $label, $bold, $index)
    {
        return ' <a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '" data-index="' . (int) $index . '" ' . $bold . '>'
            . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a> > ';
    }

    private static function ldItem($position, $name, $url)
    {
        return [
            '@type' => 'ListItem',
            'position' => $position,
            'item' => [
                'name' => $name,
                '@id' => $url,
            ],
        ];
    }

    private static function jsonLd(array $itemListElement)
    {
        if (empty($itemListElement)) {
            return '';
        }

        $breadcrumbLd = [
            '@context' => 'http://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement,
        ];

        return '<script type="application/ld+json">'
            . json_encode($breadcrumbLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
            . '</script>';
    }

    /**
     * Igual que convertSlugToTitle() en el front.js original: quita
     * ".html", cambia guiones por espacios y capitaliza cada palabra.
     */
    private static function convertSlugToTitle($slug)
    {
        $s = str_replace('.html', '', $slug);
        $s = str_replace('-', ' ', $s);

        return mb_convert_case($s, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Igual que getColorPath() en el front.js original: construye el
     * enlace "azulejos/color/xxx" (por idioma) a partir del nombre de
     * color del producto.
     */
    private static function getColorPath($baseUrl, $color, $lang)
    {
        $originalColorName = $color;

        $slug = self::stripAccents($color);
        $slug = mb_strtolower($slug, 'UTF-8');
        $slug = preg_replace('/\s+/', '-', trim($slug));

        $byLang = [
            'es' => ['/azulejos/color/', 'Azulejos Color ' . $originalColorName],
            'fr' => ['/carrelage/couleur/', 'Carrelage Couleur ' . $originalColorName],
            'en' => ['/tiles/color/', $originalColorName . ' Tiles'],
            'de' => ['/fliesen/farbe/', $originalColorName . ' Fliesen'],
            'pt' => ['/azulejos/cor/', 'Azulejos Cor ' . $originalColorName],
            'nl' => ['/tegels/kleur/', $originalColorName . ' Tegels'],
        ];

        [$urlPart, $name] = isset($byLang[$lang]) ? $byLang[$lang] : $byLang['es'];

        return [
            'url' => $baseUrl . $urlPart . $slug,
            'name' => $name,
        ];
    }

    private static function stripAccents($string)
    {
        $transliterated = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);

        return $transliterated !== false ? $transliterated : $string;
    }
}
