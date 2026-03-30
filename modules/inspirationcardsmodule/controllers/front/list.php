<?php

class InspirationcardsmoduleListModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $idLang = (int)$this->context->language->id;

        $inspirations = Db::getInstance()->executeS('
            SELECT i.id_inspiration, i.image, il.name
            FROM '._DB_PREFIX_.'inspirationcards i
            LEFT JOIN '._DB_PREFIX_.'inspirationcards_lang il
                ON (il.id_inspiration = i.id_inspiration AND il.id_lang = '.(int)$idLang.')
            WHERE i.active = 1
            ORDER BY i.id_inspiration DESC
        ');

        $this->context->smarty->assign([
            'inspirations' => $inspirations,
            'espacios' => [
                ['id' => 13, 'name' => 'Salón'],
                ['id' => 12, 'name' => 'Cocina'],
                ['id' => 14, 'name' => 'Baño'],
                ['id' => 15, 'name' => 'Dormitorio'],
                ['id' => 16, 'name' => 'Exterior'],
                ['id' => 37, 'name' => 'Piscina'],
            ],
            'usos' => [
                ['id' => 1770, 'name' => 'Suelo'],
                ['id' => 1771, 'name' => 'Pared'],
                ['id' => 9999, 'name' => 'Moodboards'],
            ],
        ]);

        $this->setTemplate('module:inspirationcardsmodule/views/templates/front/list.tpl');
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->registerStylesheet(
            'module-inspirationcards-front',
            'modules/'.$this->module->name.'/views/assets/css/front.css',
            ['media' => 'all', 'priority' => 150]
        );

        $this->registerJavascript(
            'module-inspirationcards-front',
            'modules/'.$this->module->name.'/views/assets/js/front.js',
            ['position' => 'bottom', 'priority' => 150]
        );
    }
}