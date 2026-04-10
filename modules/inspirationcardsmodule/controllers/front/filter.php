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

        $idLang = (int)$this->context->language->id;

        $sql = '
            SELECT i.id_inspiration, i.image, il.name, il.slug
            FROM '._DB_PREFIX_.'inspirationcards i
            INNER JOIN '._DB_PREFIX_.'inspirationcards_lang il
                ON (il.id_inspiration = i.id_inspiration AND il.id_lang = '.(int)$idLang.')
            WHERE i.active = 1
        ';

        $sql .= $this->buildCategoryFilterSql($space, 'space');
        $sql .= $this->buildCategoryFilterSql($usage, 'usage');
        $sql .= $this->buildFeatureFilterSql($aspecto, 'aspecto');
        $sql .= $this->buildFeatureFilterSql($color, 'color');
        $sql .= $this->buildFeatureFilterSql($tamano, 'tamano');
        $sql .= $this->buildFeatureFilterSql($estilo, 'estilo');

        $sql .= ' ORDER BY i.id_inspiration DESC';

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

        header('Content-Type: application/json');
        die(Tools::jsonEncode([
            'html' => $html,
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
            ];
        }

        if ($group === 'usage') {
            $map = [
                'suelo' => 1770,
                'pared' => 1771,
                'moodboards' => 9999,
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
        if (empty($values)) {
            return [];
        }

        $escaped = array_map('pSQL', $values);
        $in = "'" . implode("','", $escaped) . "'";

        $featureMap = [
            'aspecto' => 52,
            'color' => 46,
            'tamano' => 22,
        ];

        if (empty($featureMap[$group])) {
            return [];
        }

        $idLang = (int)$this->context->language->id;
        $idFeature = (int)$featureMap[$group];

        $rows = Db::getInstance()->executeS('
            SELECT fv.id_feature_value
            FROM '._DB_PREFIX_.'feature_value fv
            INNER JOIN '._DB_PREFIX_.'feature_value_lang fvl
                ON (fvl.id_feature_value = fv.id_feature_value AND fvl.id_lang = '.(int)$idLang.')
            WHERE fv.id_feature = '.(int)$idFeature.'
            AND fvl.value IN ('.$in.')
        ');

        return array_map('intval', array_column($rows, 'id_feature_value'));
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