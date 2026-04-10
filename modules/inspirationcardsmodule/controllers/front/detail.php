<?php

require_once dirname(__FILE__) . '/FrontBase.php';

class InspirationcardsmoduleDetailModuleFrontController extends InspirationcardsmoduleFrontControllerBase
{
    public const SLUGS = [
        'es' => 'inspiraciones',
        'fr' => 'inspirations',
        'en' => 'inspirations',
        'de' => 'inspirationen',
        'pt' => 'inspiracoes',
        'nl' => 'inspiraties',
    ];

    public function initContent()
    {
        parent::initContent();

        $slug = Tools::getValue('slug');
        $idLang = (int)$this->context->language->id;

        $inspiration = Db::getInstance()->getRow('
            SELECT i.id_inspiration, i.image, il.name, il.slug
            FROM '._DB_PREFIX_.'inspirationcards i
            INNER JOIN '._DB_PREFIX_.'inspirationcards_lang il
                ON (il.id_inspiration = i.id_inspiration AND il.id_lang = '.(int)$idLang.')
            WHERE i.active = 1
            AND il.slug = "'.pSQL($slug).'"
        ');

        if (!$inspiration) {
            header("HTTP/1.0 404 Not Found");
            $this->setTemplate('errors/404.tpl');
            return;
        }

        $this->assignHeaderLanguages($this->getInspirationDetailLanguageUrls((int)$inspiration['id_inspiration']));

        $languageUrls = $this->getLanguagesUrl(self::SLUGS);
   
        $currentBaseUrl = isset($languageUrls[$idLang]) ? $languageUrls[$idLang] : '';

        $related_products = $this->getRelatedProducts($inspiration);
        $inspirationCarousel = $this->getMoreInspirations($inspiration);
        $this->context->smarty->assign([
            'inspiration' => $inspiration,
            'floor_related_products' => $related_products['FLOOR'],
            'wall_related_products' => $related_products['WALL'],
            'more_inspirations' => $inspirationCarousel['CARDS'],
            'related_inspiration' => $inspirationCarousel['LABEL'],
            'back_url' => rtrim($currentBaseUrl, '/'),
        ]);

        $this->setTemplate('module:inspirationcardsmodule/views/templates/front/detail.tpl');
    }

    protected function getRelatedProducts($inspiration)
    {
        $idLang = (int)$this->context->language->id;

        $rows = Db::getInstance()->executeS('
            SELECT id_product, product_type
            FROM '._DB_PREFIX_.'inspirationcards_product
            WHERE id_inspiration = '.(int)$inspiration['id_inspiration'].'
            ORDER BY position ASC, id_product ASC
        ');

        foreach ($rows as $row) {
            $idProduct = (int)$row['id_product'];
            $productType = (string)$row['product_type'];

            $product = new Product($idProduct, true, $idLang);

            if (!Validate::isLoadedObject($product)) {
                continue;
            }

            $cover = Product::getCover($idProduct);
            $imageUrl = '';

            if (!empty($cover['id_image'])) {
                $imageUrl = $this->context->link->getImageLink(
                    $product->link_rewrite,
                    $cover['id_image'],
                    'medium_default'
                );
            }

            if($productType == 'suelo'){
                $products['FLOOR'][] = [
                    'name' => $product->name,
                    'reference' => $product->reference,
                    'url' => $this->context->link->getProductLink($product),
                    'image' => $imageUrl,
                    'dimensions' => Product::getProductAttribute($product->id, '4'),
                ];
            }else{
                $products['WALL'][] = [
                    'name' => $product->name,
                    'reference' => $product->reference,
                    'url' => $this->context->link->getProductLink($product),
                    'image' => $imageUrl,
                    'dimensions' => Product::getProductAttribute($product->id, '4'),
                ];
            }

        }

        return $products;
    }

    protected function getMoreInspirations($inspiration)
    {
        $idLang = (int)$this->context->language->id;

        $categoryIds = Db::getInstance()->executeS('
            SELECT id_category
            FROM '._DB_PREFIX_.'inspirationcards_category
            WHERE id_inspiration = '.(int)$inspiration['id_inspiration']
        );

        $categoryIds = array_column($categoryIds, 'id_category');

        $categoryLabels =  [
            'Baño' => '13',
            'Cocina' => '12', 
            'Salón' => '14', 
            'Dormitorio' => '15',
            'Exterior' => '16',
            'Piscina' => '37', 
            'Suelo' => '1770', 
            'Pared' => '1771', 
            'Moodboards' => '9999',
        ];

        // Primer ID
        $firstId = $categoryIds[0] ?? null;

        // Buscar el nombre
        $label = array_search($firstId, $categoryLabels);

        // Opcional: evitar false si no encuentra nada
        $label = $label !== false ? $label : null;

        $moreInspirations = [];
        $moreInspirations['LABEL'] = $label;


        if (!empty($categoryIds)) {
            $rows = Db::getInstance()->executeS('
                SELECT DISTINCT i.id_inspiration, i.image, il.name, il.slug
                FROM '._DB_PREFIX_.'inspirationcards i
                INNER JOIN '._DB_PREFIX_.'inspirationcards_lang il
                    ON (il.id_inspiration = i.id_inspiration AND il.id_lang = '.(int)$idLang.')
                INNER JOIN '._DB_PREFIX_.'inspirationcards_category ic
                    ON (ic.id_inspiration = i.id_inspiration)
                WHERE ic.id_category IN ('.implode(',', array_map('intval', $categoryIds)).')
                AND i.id_inspiration != '.(int)$inspiration['id_inspiration'].'
                AND i.active = 1
                LIMIT 8
            ');

            $languageUrls = $this->getLanguagesUrl(self::SLUGS);
        
            $currentLangId = (int)$this->context->language->id;
            $currentBaseUrl = isset($languageUrls[$currentLangId]) ? $languageUrls[$currentLangId] : '';

            foreach ($rows as $row) {
                $moreInspirations['CARDS'][] = [
                    'id_inspiration' => $row['id_inspiration'],
                    'name' => $row['name'],
                    'image' => $row['image'],
                    'slug' => $row['slug'],
                    'url' => rtrim($currentBaseUrl, '/') . '/' . ltrim($row['slug'], '/'),
                ];
            }
        }

        return $moreInspirations;
    }

    protected function getLanguagesUrl($slugs = []) {
        
        $languages = Language::getLanguages(true, $this->context->shop->id);
        $languageUrls = [];

        foreach ($languages as $lang) {
            $iso = $lang['iso_code'];
            $slug = isset($slugs[$iso]) ? $slugs[$iso] : 'inspirations';

            $languageUrls[(int)$lang['id_lang']] = $this->context->link->getBaseLink(
                $this->context->shop->id,
                null,
                null,
                false
            ) . $iso . '/' . $slug;
        }

        return $languageUrls;
    }

    protected function getInspirationDetailLanguageUrls($idInspiration)
    {
        $languages = Language::getLanguages(true, $this->context->shop->id);

        $routeSlugs = self::SLUGS;

        $urls = [];

        foreach ($languages as $lang) {
            $idLang = (int)$lang['id_lang'];
            $iso = $lang['iso_code'];

            $translatedSlug = Db::getInstance()->getValue('
                SELECT slug
                FROM '._DB_PREFIX_.'inspirationcards_lang
                WHERE id_inspiration = '.(int)$idInspiration.'
                AND id_lang = '.(int)$idLang
            );

            if (!$translatedSlug) {
                continue;
            }

            $routeSlug = isset($routeSlugs[$iso]) ? $routeSlugs[$iso] : 'inspirations';

            $urls[$idLang] = $this->context->link->getBaseLink(
                $this->context->shop->id,
                null,
                null,
                false
            ) . $iso . '/' . $routeSlug . '/' . $translatedSlug;
        }

        return $urls;
    }

    protected function assignHeaderLanguages(array $customUrls = [])
    {
        $languages = Language::getLanguages(true, $this->context->shop->id);
        $headerLanguages = [];

        foreach ($languages as $lang) {
            $idLang = (int)$lang['id_lang'];

            $headerLanguages[] = [
                'id_lang' => $idLang,
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'url' => isset($customUrls[$idLang])
                    ? $customUrls[$idLang]
                    : $this->context->link->getLanguageLink($idLang),
            ];
        }

        $this->context->smarty->assign([
            'header_languages' => $headerLanguages,
        ]);
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->registerStylesheet(
            'module-inspirationcardsmodule-front',
            'modules/'.$this->module->name.'/views/css/front.css',
            ['media' => 'all', 'priority' => 150]
        );

        $this->registerJavascript(
            'module-inspirationcardsmodule-front-js',
            'modules/'.$this->module->name.'/views/js/front.js',
            ['position' => 'bottom', 'priority' => 150]
        );
    }
}