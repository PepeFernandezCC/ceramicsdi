<?php

abstract class InspirationcardsmoduleFrontControllerBase extends ModuleFrontController
{
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
}