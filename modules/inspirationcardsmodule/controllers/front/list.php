<?php

require_once dirname(__FILE__) . '/FrontBase.php';

class InspirationcardsmoduleListModuleFrontController extends InspirationcardsmoduleFrontControllerBase
{
    public function initContent()
    {
        parent::initContent();

        $this->assignHeaderLanguages($this->getInspirationsLanguageUrls());

        $idLang = (int)$this->context->language->id;
        $limit = 12;
        $offset = 0;

        $inspirations = Db::getInstance()->executeS('
            SELECT i.id_inspiration, i.image, il.name, il.slug
            FROM '._DB_PREFIX_.'inspirationcards i
            INNER JOIN '._DB_PREFIX_.'inspirationcards_lang il
                ON (il.id_inspiration = i.id_inspiration AND il.id_lang = '.(int)$idLang.')
            WHERE i.active = 1
            ORDER BY i.position ASC
            LIMIT '.(int)$offset.', '.(int)$limit
        );

        $totalInspirations = (int)Db::getInstance()->getValue('
            SELECT COUNT(*)
            FROM '._DB_PREFIX_.'inspirationcards i
            WHERE i.active = 1
        ');

        $languageUrls = $this->getInspirationsLanguageUrls();
        $currentLangId = (int)$this->context->language->id;
        $currentBaseUrl = isset($languageUrls[$currentLangId]) ? $languageUrls[$currentLangId] : '';

        foreach ($inspirations as &$inspiration) {
            $inspiration['url'] = rtrim($currentBaseUrl, '/') . '/' . ltrim($inspiration['slug'], '/');
        }
        unset($inspiration);

        $this->context->smarty->assign([
            'inspirations' => $inspirations,
            'filter_ajax_url' => $this->context->link->getModuleLink('inspirationcardsmodule', 'filter'),
            'load_more_step' => $limit,
            'total_inspirations' => $totalInspirations,
            'espacios' => [
                ['id' => 14, 'name' => 'Salón'],
                ['id' => 12, 'name' => 'Cocina'],
                ['id' => 13, 'name' => 'Baño'],
                ['id' => 15, 'name' => 'Dormitorio'],
                ['id' => 16, 'name' => 'Exterior'],
                ['id' => 37, 'name' => 'Piscina'],
                ['id' => 1770, 'name' => 'Suelo'],
                ['id' => 1771, 'name' => 'Pared'],
                ['id' => 9999, 'name' => 'Moodboards'],
            ],
            /*
            'usos' => [
                ['id' => 1770, 'name' => 'Suelo'],
                ['id' => 1771, 'name' => 'Pared'],
                ['id' => 9999, 'name' => 'Moodboards'],
            ],
            */
        ]);

        $this->setTemplate('module:inspirationcardsmodule/views/templates/front/list.tpl');
    }

    protected function getInspirationsLanguageUrls()
    {
        $languages = Language::getLanguages(true, $this->context->shop->id);
        $urls = [];

        $slugs = [
            'es' => 'inspiraciones',
            'fr' => 'inspirations',
            'en' => 'inspirations',
            'de' => 'inspirationen',
            'pt' => 'inspiracoes',
            'nl' => 'inspiraties',
        ];

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