<?php

require_once dirname(__FILE__) . '/FrontBase.php';

class InspirationcardsmoduleFilterModuleFrontController extends InspirationcardsmoduleFrontControllerBase
{
    public function displayAjax()
    {
        $space = json_decode(Tools::getValue('space', '[]'), true);
        $usage = json_decode(Tools::getValue('usage', '[]'), true);
        $aspecto = json_decode(Tools::getValue('aspecto', '[]'), true);
        $color = json_decode(Tools::getValue('color', '[]'), true);
        $tamano = json_decode(Tools::getValue('tamano', '[]'), true);
        $estilo = json_decode(Tools::getValue('estilo', '[]'), true);
        $producto = json_decode(Tools::getValue('producto', '[]'), true);

        if (!is_array($space)) {
            $space = [];
        }
        if (!is_array($usage)) {
            $usage = [];
        }
        if (!is_array($aspecto)) {
            $aspecto = [];
        }
        if (!is_array($color)) {
            $color = [];
        }
        if (!is_array($tamano)) {
            $tamano = [];
        }
        if (!is_array($estilo)) {
            $estilo = [];
        }

        if (!is_array($producto)) {
            $producto = [];
        }

        $limit = (int)Tools::getValue('limit', 12);
        $offset = (int)Tools::getValue('offset', 0);

        if ($limit <= 0) {
            $limit = 12;
        }

        if ($offset < 0) {
            $offset = 0;
        }

        $idLang = (int)$this->context->language->id;

        $fromSql = '
            FROM '._DB_PREFIX_.'inspirationcards i
            INNER JOIN '._DB_PREFIX_.'inspirationcards_lang il
                ON (il.id_inspiration = i.id_inspiration AND il.id_lang = '.(int)$idLang.')
            WHERE i.active = 1
        ';

        $fromSql .= $this->buildCategoryFilterSql($space, 'space');
        $fromSql .= $this->buildCategoryFilterSql($usage, 'usage');
        $fromSql .= $this->buildCategoryFilterSql($producto, 'producto');

        $fromSql .= $this->buildFeatureFilterSql($aspecto, 'aspecto');
        $fromSql .= $this->buildFeatureFilterSql($color, 'color');
        $fromSql .= $this->buildFeatureFilterSql($tamano, 'tamano');
        $fromSql .= $this->buildFeatureFilterSql($estilo, 'estilo');
       

        $total = (int)Db::getInstance()->getValue('
            SELECT COUNT(*) ' . $fromSql
        );

        $sql = '
            SELECT i.id_inspiration, i.image, il.name, il.slug
            ' . $fromSql . '
            ORDER BY i.position ASC
            LIMIT '.(int)$offset.', '.(int)$limit;

        $inspirations = Db::getInstance()->executeS($sql);

        $languageUrls = $this->getInspirationsLanguageUrls();
        $currentLangId = (int)$this->context->language->id;
        $currentBaseUrl = isset($languageUrls[$currentLangId]) ? $languageUrls[$currentLangId] : '';

        foreach ($inspirations as &$inspiration) {
            $inspiration['url'] = rtrim($currentBaseUrl, '/') . '/' . ltrim($inspiration['slug'], '/');
        }
        unset($inspiration);

        $this->context->smarty->assign([
            'inspirations' => $inspirations,
        ]);

        $html = $this->module->fetch('module:inspirationcardsmodule/views/templates/front/_grid.tpl');

        $loaded = $offset + count($inspirations);
        $hasMore = $loaded < $total;

        header('Content-Type: application/json');
        die(Tools::jsonEncode([
            'sql' => $sql,
            'success' => true,
            'html' => $html,
            'count' => count($inspirations),
            'loaded' => $loaded,
            'total' => $total,
            'has_more' => $hasMore,
        ]));
    }

    protected function buildCategoryFilterSql(array $values, $group)
    {
        if (empty($values)) {
            return '';
        }

        $categoryIds = $this->mapCategoryValuesToIds($values, $group);
        if (empty($categoryIds)) {
            return '';
        }

        $ids = implode(',', array_map('intval', $categoryIds));

        return '
            AND EXISTS (
                SELECT 1
                FROM '._DB_PREFIX_.'inspirationcards_category ic
                WHERE ic.id_inspiration = i.id_inspiration
                AND ic.id_category IN ('.$ids.')
            )
        ';
    }

    protected function buildFeatureFilterSql(array $values, $group)
    {
        if (empty($values)) {
            return '';
        }

        $featureValueIds = $this->mapFeatureValuesToIds($values, $group);
        if (empty($featureValueIds)) {
            return '';
        }

        $ids = implode(',', array_map('intval', $featureValueIds));

        return '
            AND EXISTS (
                SELECT 1
                FROM '._DB_PREFIX_.'inspirationcards_feature ife
                WHERE ife.id_inspiration = i.id_inspiration
                AND ife.id_feature_value IN ('.$ids.')
            )
        ';
    }

    protected function mapCategoryValuesToIds(array $values, $group)
    {
        $map = [];

        if ($group === 'space') {
            $map = [
                'cocina' => 12,
                'salon' => 14,
                'bano' => 13,
                'dormitorio' => 15,
                'exterior' => 16,
                'piscina' => 37,
                //agregamos los usos para no separar filtros
                'suelo' => 1770,
                'pared' => 1771,
                'moodboards' => 9999,

            ];
        }

        if ($group === 'usage') {
            $map = [
                'suelo' => 1770,
                'pared' => 1771,
                'moodboards' => 9999,
            ];
        }

        if ($group === 'producto') {
            $map = [
                'azulejo' => 11,
                'piedra' => 82,
                'terracota' => 88,
                'mosaico' => 81,
            ];
        }

        $ids = [];
        foreach ($values as $value) {
            if (isset($map[$value])) {
                $ids[] = (int)$map[$value];
            }
        }

        return array_unique($ids);
    }

    protected function mapFeatureValuesToIds(array $values, $group)
    {
        $maps = [
            'aspecto' => [
                'barro' => 112060,
                'cemento' => 112061,
                'hidraulico' => 112062,
                'madera' => 112063,
                'marmol' => 112066,
                'metro' => 112064,
                'monocolor' => 112065,
                'piedra' => 112067,
                'pizarra' => 112069,
                'terrazo' => 112068,
                'zellige' => 112070,
            ],
            'color' => [
                'blanco' => 111962,
                'gris' => 111963,
                'beige' => 111964,
                'marron' => 111965,
                'amarillo' => 111966,
                'rojo' => 111967,
                'verde' => 111969,
                'azul' => 111970,
                'negro' => 111972,
                'multicolor' => 111973,
            ],
            'tamano' => [
                'Pequeño (hasta 30 cm)' => 756,
                'Mediano (hasta 60 cm)' => 757,
                'Grande (hasta 120 cm)' => 758,
                'Mosaico enmallado' => 2514,
            ],
            'estilo' => [
                'minimalista' => 165279,
                'industrial' => 165280,
                'vintage' => 165281,
                'rustico' => 165282,
                'nordico' => 165283,
                'mediterraneo' => 165284,
                'wabisabi' => 165285,
                'contemporaneo' => 165286,
            ],
        ];

        if (empty($maps[$group])) {
            return [];
        }

        $ids = [];
        foreach ($values as $value) {
            if (isset($maps[$group][$value])) {
                $ids[] = (int)$maps[$group][$value];
            }
        }

        return array_unique($ids);
    }

    protected function getInspirationsLanguageUrls()
    {
        $languages = Language::getLanguages(true, $this->context->shop->id);

        $slugs = [
            'es' => 'inspiraciones',
            'fr' => 'inspirations',
            'en' => 'inspirations',
            'de' => 'inspirationen',
            'pt' => 'inspiracoes',
            'nl' => 'inspiraties',
        ];

        $urls = [];

        foreach ($languages as $lang) {
            $iso = $lang['iso_code'];
            $slug = isset($slugs[$iso]) ? $slugs[$iso] : 'inspirations';

            $urls[(int)$lang['id_lang']] = $this->context->link->getBaseLink(
                $this->context->shop->id,
                null,
                null,
                false
            ) . $iso . '/' . $slug;
        }

        return $urls;
    }
}