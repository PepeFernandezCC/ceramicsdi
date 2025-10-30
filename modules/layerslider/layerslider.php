<?php
/**
 * Creative Slider - Responsive Slideshow Module
 * https://creativeslider.webshopworks.com
 *
 * @author    WebshopWorks <info@webshopworks.com>
 * @copyright 2015-2025 WebshopWorks
 * @license   One Domain Licence
 *
 * Not allowed to resell or redistribute this software
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class LayerSlider extends Module
{
    public static $instance;

    protected $controllerClass;
    protected $init = false;
    protected $template;
    protected $tabs = [
        'Creative Slider' => ['class' => 'AdminParentLayerSlider', 'active' => 1, 'icon' => 'collections'],
        'Sliders' => ['class' => 'AdminLayerSlider', 'active' => 1],
        'Media Manager' => ['class' => 'AdminLayerSliderMedia', 'active' => 0],
        'Revisions' => ['class' => 'AdminLayerSliderRevisions', 'active' => 1],
        'Transition Builder' => ['class' => 'AdminLayerSliderTransition', 'active' => 1],
        'Skin Editor' => ['class' => 'AdminLayerSliderSkin', 'active' => 1],
        'CSS Editor' => ['class' => 'AdminLayerSliderStyle', 'active' => 1],
    ];
    protected $lang = [
        'fr' => [
            'Creative Slider' => 'Creative Slider',
            'Sliders' => 'Diaporamas',
            'Media Manager' => 'Directeur des médias',
            'Revisions' => 'Révisions',
            'Transition Builder' => 'Effets de Transition',
            'Skin Editor' => 'Éditeur de skin',
            'CSS Editor' => 'Éditeur de CSS',
        ],
    ];

    public function __construct()
    {
        $this->name = 'layerslider';
        $this->tab = 'slideshows';
        $this->version = '6.6.12';
        $this->author = 'WebshopWorks';
        $this->module_key = 'b92dd49b8609431aeb010cb8db905a3f';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.5', 'max' => _PS_VERSION_];
        $this->bootstrap = false;
        $this->displayName = 'Creative Slider';
        $this->description = 'Responsive Slideshow Module';
        $this->confirmUninstall = 'Are you sure you want to uninstall?';
        self::$instance = $this;
        parent::__construct();

        if ($this->context->controller) {
            $this->controllerClass = str_replace('controller', '', strtolower(get_class($this->context->controller)));
        }
    }

    public function install()
    {
        Shop::isFeatureActive() && Shop::setContext(Shop::CONTEXT_ALL);

        $db = Db::getInstance();
        $result = $db->execute('
            CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'layerslider (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `author` int(11) NOT NULL DEFAULT 0,
                `name` varchar(100) DEFAULT "",
                `slug` varchar(100) DEFAULT "",
                `data` mediumtext NOT NULL,
                `date_c` int(11) NOT NULL,
                `date_m` int(11) NOT NULL,
                `schedule_start` int(11) NOT NULL DEFAULT 0,
                `schedule_end` int(11) NOT NULL DEFAULT 0,
                `flag_hidden` tinyint(1) NOT NULL DEFAULT 0,
                `flag_deleted` tinyint(1) NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_general_ci
        ') && $db->execute('
            CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'layerslider_revisions (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `slider_id` int(11) NOT NULL,
                `author` int(11) NOT NULL DEFAULT 0,
                `data` mediumtext NOT NULL,
                `date_c` int(11) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_general_ci
        ') && $db->execute('
            CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'layerslider_module (
                `id_slider` int(11) NOT NULL,
                `id_shop` int(11) NOT NULL DEFAULT 0,
                `id_lang` int(11) NOT NULL DEFAULT 0,
                `hook` varchar(64) NOT NULL DEFAULT "",
                `position` tinyint(2) NOT NULL DEFAULT 0,
                `pages` text NULL,
                KEY `id_slider` (`id_slider`),
                KEY `id_shop` (`id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_general_ci
        ');

        if (!$result) {
            $this->_errors[] = $db->getMsgError();

            return false;
        }

        Configuration::get('LS_DATE_INSTALLED') || Configuration::updateValue('LS_DATE_INSTALLED', time());

        return parent::install();
    }

    protected function addTabs()
    {
        $parent = version_compare(_PS_VERSION_, '1.7.0', '<') ? 0 : (int) Tab::getIdFromClassName('CONFIGURE');
        foreach ($this->tabs as $name => $t) {
            $tab = new Tab();
            $tab->active = $t['active'];
            $tab->class_name = $t['class'];
            $tab->name = [];
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = isset($this->lang[$lang['iso_code']]) ? $this->lang[$lang['iso_code']][$name] : $name;
            }
            if (isset($t['icon'])) {
                $tab->icon = $t['icon'];
            }
            $tab->module = $this->name;
            $tab->id_parent = $parent;
            $tab->add();

            if ('AdminParentLayerSlider' == $t['class']) {
                $parent = (int) Tab::getIdFromClassName($t['class']);
            }
        }
    }

    protected function deleteTabs()
    {
        foreach ($this->tabs as $t) {
            $id_tab = (int) Tab::getIdFromClassName($t['class']);
            if ($id_tab) {
                $tab = new Tab($id_tab);
                $tab->delete();
            }
        }
        Db::getInstance()->delete('tab', '`module` = "layerslider"');
    }

    public function enable($force_all = false)
    {
        if ($res = parent::enable($force_all)) {
            $this->addTabs();
            $this->registerHook('actionOutputHTMLBefore');
            $this->registerHook('displayHeader');
            version_compare(_PS_VERSION_, '1.7.1', '<')
                && $this->registerHook('displayBackOfficeHeader');

            $modules = Db::getInstance()->executeS(
                'SELECT DISTINCT `hook` FROM ' . _DB_PREFIX_ . 'layerslider_module WHERE `id_shop` > -1 AND `hook` != ""'
            ) ?: [];
            foreach ($modules as $mod) {
                $this->registerHook($mod['hook']);
            }
        }

        return $res;
    }

    public function disable($force_all = false)
    {
        $this->deleteTabs();
        $this->unregisterHook('actionOutputHTMLBefore');
        $this->unregisterHook('displayHeader');
        $this->unregisterHook('displayBackOfficeHeader');
        $modules = Db::getInstance()->executeS(
            'SELECT DISTINCT `hook` FROM ' . _DB_PREFIX_ . 'layerslider_module WHERE `id_shop` > -1 AND `hook` != ""'
        );
        foreach ($modules as $mod) {
            $this->unregisterHook($mod['hook']);
        }

        return parent::disable($force_all);
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminLayerSlider'));
    }

    public function generateSlider($id)
    {
        if (is_array($id)) {
            $id = empty($id[2]) ? $id[1] : $id[2];
        }
        require_once _PS_MODULE_DIR_ . 'layerslider/helper.php';
        require_once _PS_MODULE_DIR_ . 'layerslider/base/layerslider.php';

        return LsShortcode::handleShortcode(['id' => $id, 'filters' => '']);
    }

    protected function isOnPage(&$mod)
    {
        if ((0 == $mod['id_shop'] || $mod['id_shop'] == $this->context->shop->id) && (0 == $mod['id_lang'] || $mod['id_lang'] == $this->context->language->id)) {
            if (!isset($mod['pages']) || !$mod['pages']) {
                $mod['pages'] = '{"cat":"all","prod":"all","cms":"all","page":"all"}';
            }
            if ('{"cat":"all","prod":"all","cms":"all","page":"all"}' == $mod['pages']) {
                return true;
            }
            $pages = json_decode($mod['pages'], true);

            if (!empty($pages['groups']) && false === in_array('0', $pages['groups']) && !count(array_intersect($this->context->customer->getGroups(), $pages['groups']))) {
                return false;
            }

            switch ($this->controllerClass) {
                case 'index':
                    if ('all' === $pages['cat']) {
                        return true;
                    }

                    return isset($pages['index']);
                case 'category':
                    if ('all' === $pages['cat']) {
                        return true;
                    }
                    $id = Tools::getValue('id_category');

                    return in_array("$id", $pages['cat']);
                case 'product':
                    if ('all' === $pages['prod']) {
                        return true;
                    }
                    $id = Tools::getValue('id_product');

                    return in_array("$id", $pages['prod']);
                case 'cms':
                    if ('all' === $pages['cms']) {
                        return true;
                    }
                    if (isset($this->context->controller->cms->id)) {
                        return in_array("{$this->context->controller->cms->id}", $pages['page']);
                    }
                    if (isset($this->context->controller->cms_category->id)) {
                        return in_array("{$this->context->controller->cms_category->id}", $pages['cms']);
                    }

                    return false;
                case 'manufacturer':
                    if ('all' === $pages['cms']) {
                        return true;
                    }
                    if (isset($pages['manufacturer'])) {
                        $id = (int) Tools::getValue('id_manufacturer', 0);

                        return in_array($id, $pages['manufacturer']);
                    }

                    return false;
                case 'psblogpostsmodulefront':
                    return isset($pages[$this->controllerClass]) && empty($this->context->controller->id_post);
                case 'prestablogblogmodulefront':
                    if ('all' === $pages['cms']) {
                        return true;
                    }
                    if ($id = Tools::getValue('id', 0)) {
                        return isset($pages['bn']) && in_array("$id", $pages['bn']);
                    }
                    $c = Tools::getValue('c', 0);

                    return isset($pages['bc']) && in_array("$c", $pages['bc']);
                default:
                    if ('all' === $pages['cms']) {
                        return true;
                    }

                    return isset($pages[$this->controllerClass]);
            }
        }

        return false;
    }

    protected function displaySliders($hook)
    {
        $content = '';
        $modules = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'layerslider_module WHERE `hook` LIKE "' . pSQL($hook) . '" ORDER BY `position`'
        ) ?: [];
        foreach ($modules as &$mod) {
            if ($this->isOnPage($mod)) {
                $content .= $this->generateSlider($mod['id_slider']);
            }
        }

        return $content;
    }

    public function __call($method, $args)
    {
        if (0 === stripos($method, 'hook') && 0 !== stripos($method, 'hookAction')) {
            $hook = substr($method, 4);

            return $this->displaySliders($hook);
        }
    }

    public function hookActionOutputHTMLBefore($params)
    {
        $this->filterShortcode($params['html']);
    }

    public function filterShortcode(&$content)
    {
        if (false !== strpos($content, '[creativeslider id="')) {
            require_once _PS_MODULE_DIR_ . 'layerslider/helper.php';
            require_once _PS_MODULE_DIR_ . 'layerslider/base/layerslider.php';
            $content = preg_replace_callback(
                '`<[pP]>\s*\[creativeslider id="([\w\-]+)"\]\s*</[pP]>|\[creativeslider id="([\w\-]+)"\]`',
                [$this, 'generateSlider'],
                $content
            );
        }
        if (false !== strpos($content, '[cs-navigate id="')) {
            $content = preg_replace(
                '`\[cs-navigate id="([\w\-]+)" action="([\w\-]+)"\](.*?)\[/cs-navigate\]`',
                '<a class="ls-navigate" href="javascript:;" onclick="$(\'#layerslider_$1\').layerSlider(parseInt(\'$2\') || \'$2\')">$3</a>',
                $content
            );
        }
    }

    public function hookDisplayHeader()
    {
        require_once _PS_MODULE_DIR_ . 'layerslider/helper.php';
        require_once _PS_MODULE_DIR_ . 'layerslider/base/layerslider.php';
        ls_do_action('ls_enqueue_scripts');

        if (!$this->controllerClass) {
            $this->controllerClass = str_replace('controller', '', strtolower(get_class($this->context->controller)));
        }

        if (version_compare(_PS_VERSION_, '1.7.1', '<')) {
            // BC Fix for PS < 1.7.1
            $this->template = &Closure::bind(function &() {
                return $this->template;
            }, $this->context->controller, $this->context->controller)->__invoke();
            // Parse shortcodes
            $this->context->smarty->registerFilter('output', [$this, 'outputFilter']);
        }

        return ls_meta_generator();
    }

    public function outputFilter($out, $tpl)
    {
        if ($this->template === $tpl->template_resource) {
            $this->filterShortcode($out);
        }

        return $out;
    }

    public function hookDisplayBackOfficeHeader()
    {
        return $this->display(__FILE__, 'views/templates/admin/header.tpl');
    }
}

function creativeSlider($id)
{
    return LayerSlider::$instance->generateSlider($id);
}
