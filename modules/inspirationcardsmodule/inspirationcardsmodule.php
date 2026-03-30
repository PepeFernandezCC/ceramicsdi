<?php
if (!defined('_PS_VERSION_')) { exit; }

require_once dirname(__FILE__).'/classes/InspirationCards.php';

class InspirationcardsModule extends Module
{
    public function __construct()
    {
        $this->name = 'inspirationcardsmodule';
        $this->version = '1.2.0';
        $this->author = 'CERAMIC CONNECTION';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = 'Inspiration Cards Module';
        $this->description = 'Gestión de Fichas de inspiraciones';
    }

    public function install()
    {
        return parent::install() 
            && $this->runSql()
            && $this->registerHook('moduleRoutes');
    }

    private function runSql()
    {
        $sql = Tools::file_get_contents(dirname(__FILE__).'/install.sql');
        $queries = preg_split("/;\s*\n/", $sql);

        foreach ($queries as $query) {
            if (!empty($query)) {
                if (!Db::getInstance()->execute($query)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminInspirationcards'));
    }

    public function hookModuleRoutes($params)
    {
        $routes = [];

        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            $iso = $lang['iso_code'];
            $idLang = (int)$lang['id_lang'];

            $slug = $this->getInspirationsSlugByLang($iso);

            $routes['module-inspirationcardsmodule-list-'.$idLang] = [
                'controller' => 'list',
                'rule' => $slug,
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => $this->name,
                    'controller' => 'list',
                ],
            ];
        }

        return $routes;
    }
    
    protected function getInspirationsSlugByLang($iso)
    {
        $map = [
            'es' => 'inspiraciones',
            'fr' => 'inspirations',
            'en' => 'inspirations',
            'de' => 'inspirationen',
            'pt' => 'inspiracoes',
            'nl' => 'inspiraties',
        ];

        return isset($map[$iso]) ? $map[$iso] : 'inspirations';
    }
}
