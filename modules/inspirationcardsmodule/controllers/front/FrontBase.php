<?php

abstract class InspirationcardsmoduleFrontControllerBase extends ModuleFrontController
{
    /**
     * Bloquea con un 404 el acceso directo a la URL cruda del controlador
     * (p.ej. /module/inspirationcardsmodule/detail?slug=X), que siempre
     * convive con la URL "bonita" registrada via hookModuleRoutes() (p.ej.
     * /inspirations/X) y sirve el mismo contenido - dos URLs para el mismo
     * contenido es un problema de SEO (contenido duplicado). Solo la URL
     * bonita debe ser accesible; la ruta /module/... queda reservada para
     * uso interno (AJAX, etc. en otros controladores del modulo).
     *
     * @return bool true si se ha bloqueado la peticion (el llamador debe
     *              hacer return inmediatamente despues de llamar a esto)
     */
    protected function blockRawModuleAccess()
    {
        $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

        if (strpos($requestUri, '/module/' . $this->module->name . '/') !== false) {
            header('HTTP/1.0 404 Not Found');
            $this->setTemplate('errors/404.tpl');

            return true;
        }

        return false;
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
}