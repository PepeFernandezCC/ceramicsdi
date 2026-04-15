<?php
if (!defined('_PS_VERSION_')) { exit; }

require_once dirname(__FILE__).'/classes/Inspirationcards.php';

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
        return [
            'module-inspirationcardsmodule-list-es' => [
                'controller' => 'list',
                'rule'       => 'inspiraciones',
                'keywords'   => [],
                'params'     => [
                    'fc'         => 'module',
                    'module'     => $this->name,
                    'controller' => 'list',
                ],
            ],
            'module-inspirationcardsmodule-list-fr' => [
                'controller' => 'list',
                'rule'       => 'inspirations',
                'keywords'   => [],
                'params'     => [
                    'fc'         => 'module',
                    'module'     => $this->name,
                    'controller' => 'list',
                    'id_lang'    => (int)Language::getIdByIso('fr'),
                ],
            ],
            'module-inspirationcardsmodule-list-en' => [
                'controller' => 'list',
                'rule'       => 'inspirations',
                'keywords'   => [],
                'params'     => [
                    'fc'         => 'module',
                    'module'     => $this->name,
                    'controller' => 'list',
                    'id_lang'    => (int)Language::getIdByIso('en'),
                ],
            ],
            'module-inspirationcardsmodule-list-de' => [
                'controller' => 'list',
                'rule'       => 'inspirationen',
                'keywords'   => [],
                'params'     => [
                    'fc'         => 'module',
                    'module'     => $this->name,
                    'controller' => 'list',
                    'id_lang'    => (int)Language::getIdByIso('de'),
                ],
            ],
            'module-inspirationcardsmodule-list-pt' => [
                'controller' => 'list',
                'rule'       => 'inspiracoes',
                'keywords'   => [],
                'params'     => [
                    'fc'         => 'module',
                    'module'     => $this->name,
                    'controller' => 'list',
                    'id_lang'    => (int)Language::getIdByIso('pt'),
                ],
            ],
            'module-inspirationcardsmodule-list-nl' => [
                'controller' => 'list',
                'rule'       => 'inspiraties',
                'keywords'   => [],
                'params'     => [
                    'fc'         => 'module',
                    'module'     => $this->name,
                    'controller' => 'list',
                    'id_lang'    => (int)Language::getIdByIso('nl'),
                ],
            ],
            'module-inspirationcardsmodule-detail-es' => [
                'controller' => 'detail',
                'rule'       => 'inspiraciones/{slug}',
                'keywords'   => [
                    'slug' => ['regexp' => '[_a-zA-Z0-9\-]+', 'param' => 'slug'],
                ],
                'params'     => [
                    'fc'         => 'module',
                    'module'     => $this->name,
                    'controller' => 'detail',
                    'id_lang'    => (int)Language::getIdByIso('es'),
                ],
            ],
            'module-inspirationcardsmodule-detail-fr' => [
                'controller' => 'detail',
                'rule'       => 'inspirations/{slug}',
                'keywords'   => [
                    'slug' => ['regexp' => '[_a-zA-Z0-9\-]+', 'param' => 'slug'],
                ],
                'params'     => [
                    'fc'         => 'module',
                    'module'     => $this->name,
                    'controller' => 'detail',
                    'id_lang'    => (int)Language::getIdByIso('fr'),
                ],
            ],
            'module-inspirationcardsmodule-detail-en' => [
                'controller' => 'detail',
                'rule'       => 'inspirations/{slug}',
                'keywords'   => [
                    'slug' => ['regexp' => '[_a-zA-Z0-9\-]+', 'param' => 'slug'],
                ],
                'params'     => [
                    'fc'         => 'module',
                    'module'     => $this->name,
                    'controller' => 'detail',
                    'id_lang'    => (int)Language::getIdByIso('en'),
                ],
            ],
            'module-inspirationcardsmodule-detail-de' => [
                'controller' => 'detail',
                'rule'       => 'inspirationen/{slug}',
                'keywords'   => [
                    'slug' => ['regexp' => '[_a-zA-Z0-9\-]+', 'param' => 'slug'],
                ],
                'params'     => [
                    'fc'         => 'module',
                    'module'     => $this->name,
                    'controller' => 'detail',
                    'id_lang'    => (int)Language::getIdByIso('de'),
                ],
            ],
            'module-inspirationcardsmodule-detail-pt' => [
                'controller' => 'detail',
                'rule'       => 'inspiracoes/{slug}',
                'keywords'   => [
                    'slug' => ['regexp' => '[_a-zA-Z0-9\-]+', 'param' => 'slug'],
                ],
                'params'     => [
                    'fc'         => 'module',
                    'module'     => $this->name,
                    'controller' => 'detail',
                    'id_lang'    => (int)Language::getIdByIso('pt'),
                ],
            ],
            'module-inspirationcardsmodule-detail-nl' => [
                'controller' => 'detail',
                'rule'       => 'inspiraties/{slug}',
                'keywords'   => [
                    'slug' => ['regexp' => '[_a-zA-Z0-9\-]+', 'param' => 'slug'],
                ],
                'params'     => [
                    'fc'         => 'module',
                    'module'     => $this->name,
                    'controller' => 'detail',
                    'id_lang'    => (int)Language::getIdByIso('nl'),
                ],
            ],
        ];
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
        $this->context->controller->registerJavascript(
            'landing-frontend',
            'modules/'.$this->name.'/views/js/front.js',
            ['position' => 'bottom', 'priority' => 150]
        );;
    }

}
