<?php
/**
 * Copyright 2024 LÍNEA GRÁFICA E.C.E S.L.
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require realpath(dirname(__FILE__)) . '/config/config.inc.php';

class LGSEORedirect extends Module
{
    const NUMBER_OF_PRODUCTS = 10;

    protected $pagination = [];
    protected $total_pages = 0;
    protected $total_redirects = 0;
    protected $total_selected_redirects = 0;
    protected $selected_pagination = 2;
    protected $last_selected_pagination = 1;
    protected $last_offset = 1;
    protected $limit = 100;
    protected $offset = 1;
    protected $filters = [];
    protected $pnf_filters = [];
    protected $sql = '';
    protected $sql_count = '';
    protected $redirects = [];
    protected $html = '';

    protected $pagesnotfoundinstalled = false;
    protected $pnf_pagination = [];
    protected $pnf_total_pages = 0;
    protected $pnf_total_redirects = 0;
    protected $pnf_total_selected_redirects = 0;
    protected $pnf_selected_pagination = 2;
    protected $pnf_last_selected_pagination = 1;
    protected $pnf_last_offset = 1;
    protected $pnf_limit = 100;
    protected $pnf_offset = 1;
    protected $pages_not_found = [];

    protected $lgdebug = false;

    public $bootstrap;

    public function __construct()
    {
        $this->lgdebug = defined('_PS_MODE_DEV_');

        $this->name = 'lgseoredirect';
        $this->tab = 'seo';
        $this->version = '1.4.8';
        $this->author = 'Línea Gráfica';
        $this->module_key = 'f95aace4e5d00f07742643a87be835fe';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();
        $this->displayName = $this->l('301, 302, 303 URL Redirects - SEO');
        $this->description = $this->l('Create an unlimited number of 301, 302 and 303 URL redirects.');

        $this->ps_versions_compliancy = [
            'min' => '1.5',
            'max' => _PS_VERSION_,
        ];

        if (Tools::getIsset('ajax')
            && !in_array(
                Tools::getValue('action'),
                [
                    'getPNF',
                    'getRedirects',
                    'deleteRedirects',
                    'deletePNF',
                    'saveRedirects',
                    'savePagesNotFound',
                ]
            )
        ) {
            return;
        }

        // Check about Pages not found module is installed
        $this->pagesnotfoundinstalled = Module::isInstalled('pagesnotfound') && Module::isEnabled('pagesnotfound');

        // Modificaciones para la paginación de redirecciones
        $this->selected_pagination = Tools::getValue('lgseoredirect_pagination', self::NUMBER_OF_PRODUCTS);
        $this->limit = $this->selected_pagination;
        $this->offset = Tools::getValue('p', 1);
        $this->pagination = [2, 5, 10, 100, 500, 1000];
        $this->total_pages = 0;
        $this->total_redirects = 0;
        $this->filters = Tools::getValue('filters', []);

        // Modificaciones para la paginación páginas no encontradas
        $this->pnf_selected_pagination = Tools::getValue('lgseoredirect_pnf_pagination', self::NUMBER_OF_PRODUCTS);
        $this->pnf_limit = $this->pnf_selected_pagination;
        $this->pnf_offset = Tools::getValue('p_pnf', 1);
        $this->pnf_pagination = [2, 5, 10, 100, 500, 1000];
        $this->pnf_total_pages = 0;
        $this->pnf_total_redirects = 0;
        $this->pnf_filters = Tools::getValue('filters_pnf', []);

        if (Tools::getIsset('ajax')) {
            $this->content_only = true;
        }

        // Para las redirecciones
        if ($this->context->cookie->__isset('lgseoredirect_user_pagination_' . $this->context->shop->id)) {
            $this->last_selected_pagination = $this->context->cookie->__get(
                'lgseoredirect_user_pagination_' . $this->context->shop->id
            );

            if ($this->context->cookie->__isset('lgseoredirect_last_offset_' . $this->context->shop->id)) {
                $this->last_offset = $this->context->cookie->__get(
                    'lgseoredirect_last_offset_' . $this->context->shop->id
                );
            }

            if (!is_string($this->last_offset)) {
                if ($this->last_offset - 1 <= 0) {
                    $this->last_offset = 1;
                }
            } else {
                $this->last_offset = 1;
            }

            $last_products_reference_index = ($this->last_offset - 1) * $this->last_selected_pagination;

            if ($this->last_selected_pagination != $this->selected_pagination) {
                $final_page = (int) ($last_products_reference_index / $this->selected_pagination);
                if ($last_products_reference_index % $this->selected_pagination > 0) {
                    $final_page++;
                }
                $this->offset = $final_page;
                if ($this->offset <= 0) {
                    $this->offset = 1;
                }
                $this->context->cookie->__set(
                    'lgseoredirect_user_pagination_' . $this->context->shop->id,
                    $this->selected_pagination
                );
            }
        } else {
            $this->context->cookie->__set(
                'lgseoredirect_user_pagination_' . $this->context->shop->id,
                $this->selected_pagination
            );
        }

        // Para las páginas no encontradas
        if ($this->context->cookie->__isset('lgseoredirect_pnf_user_pagination_' . $this->context->shop->id)) {
            $this->pnf_last_selected_pagination = $this->context->cookie->__get(
                'lgseoredirect_pnf_user_pagination_' . $this->context->shop->id
            );

            if ($this->context->cookie->__isset('lgseoredirect_pnf_last_offset_' . $this->context->shop->id)) {
                $this->pnf_last_offset = $this->context->cookie->__get(
                    'lgseoredirect_pnf_last_offset_' . $this->context->shop->id
                );
            }

            if ($this->pnf_last_offset - 1 <= 0) {
                $this->pnf_last_offset = 1;
            }

            $last_products_reference_index = ($this->pnf_last_offset - 1) * $this->pnf_last_selected_pagination;

            if ($this->last_selected_pagination != $this->selected_pagination) {
                $final_page = (int) ($last_products_reference_index / $this->pnf_selected_pagination);
                if ($last_products_reference_index % $this->pnf_selected_pagination > 0) {
                    $final_page++;
                }
                $this->pnf_offset = $final_page;
                if ($this->pnf_offset <= 0) {
                    $this->pnf_offset = 1;
                }
                $this->context->cookie->__set(
                    'lgseoredirect_pnf_user_pagination_' . $this->context->shop->id,
                    $this->pnf_selected_pagination
                );
            }
        } else {
            $this->context->cookie->__set(
                'lgseoredirect_pnf_user_pagination_' . $this->context->shop->id,
                $this->pnf_selected_pagination
            );
        }

        // Actualizamos el indice de la pagina anterior
        // Para las redirecciones
        $this->context->cookie->__set(
            'lgseoredirect_last_offset_' . $this->context->shop->id,
            $this->offset
        );
        // Para las páginas no encontradas
        $this->context->cookie->__set(
            'lgseoredirect_pnf_last_offset_' . $this->context->shop->id,
            $this->pnf_offset
        );

        $this->context->cookie->write();

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            if (Tools::getIsset('ajax')) {
                switch (Tools::getValue('action')) {
                    case 'saveRedirects':
                        $this->ajaxProcessSaveRedirects();
                        break;
                    case 'deleteRedirects':
                        $this->ajaxProcessDeleteRedirects();
                        break;
                    case 'savePagesNotFound':
                        $this->ajaxProcessSavePagesNotFound();
                        break;
                    case 'deletePNF':
                        $this->ajaxProcessDeletePNF();
                        break;
                    case 'getPNF':
                        $this->ajaxProcessGetPNF();
                        break;
                    case 'getRedirects':
                    default:
                        $this->ajaxProcessGetRedirects();
                        break;
                }
            }
        }
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('moduleRoutes')
            || !$this->registerHook('actionDispatcher')
            || !$this->registerHook('displayBackOfficeHeader')
        ) {
            return false;
        }
        $queries = [
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lgseoredirect` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `url_old` text NOT NULL,
              `url_new` text NOT NULL,
              `redirect_type` varchar(10) NOT NULL,
              `update` datetime NOT NULL,
              `id_shop` int(11) NOT NULL,
              `pnf` VARCHAR(256) NOT NULL DEFAULT 0,
              PRIMARY KEY (`id`),
              KEY `redirect_type` (`redirect_type`),
              KEY `pnf` (`redirect_type`)
            ) ENGINE=' . (defined('ENGINE_TYPE') ? ENGINE_TYPE : 'Innodb') . ' CHARSET=utf8'
        ];

        foreach ($queries as $query) {
            if (!Db::getInstance()->Execute($query)) {
                parent::uninstall();
                return false;
            } else {
                return true;
            }
        }
    }

    public function uninstall()
    {
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'lgseoredirect`');
        return $this->unregisterHook('moduleRoutes')
            && $this->unregisterHook('actionDispatcher')
            && $this->unregisterHook('displayBackOfficeHeader')
            && parent::uninstall();
    }

    public function getContent()
    {
        $this->registerHook('actionDispatcher');
        $this->check();
        $this->postProcess();

        $shop_ssl = $this->context->shop->domain_ssl;
        $shop_dom = $this->context->shop->domain;
        $shop_domain = (Tools::usingSecureMode() ? 'https://' . $shop_ssl : 'http://' . $shop_dom);
        $shop_uri = $this->context->shop->getBaseURI();

        // Obtenemos las redirecciones
        $offset = $this->offset - 1;
        $limit = $offset * $this->limit;
        $this->redirects = $this->getRedirects($limit, $this->limit);

        // Obtenemos las páginas no encontradas
        $pnf_offset = $this->pnf_offset - 1;
        $pnf_limit = $pnf_offset * $this->pnf_limit;
        $this->pages_not_found = $this->pagesnotfoundinstalled ? $this->getPagesNotFound($pnf_limit, $this->pnf_limit) : '';

        $this->context->smarty->assign(
            [
                // Variables Globales
                'lgseoredirect_displayName' => $this->displayName,
                'lgseoredirect_pagesnotfoundenabled' => $this->pagesnotfoundinstalled,
                'lgseoredirect_pagesnotfound_installed' => $this->pagesnotfoundinstalled,
                'lgseoredirect_is_rtl' => $this->context->language->is_rtl,
                'lgseoredirect_ps16' => !version_compare(_PS_VERSION_, '1.6', '<'),
                'lgseoredirect_shop_domain' => $shop_domain,
                'module_name' => $this->name,
                'lgseoredirect_shop_uri' => $this->rtrim($shop_uri, '/'),
                'domain_base' => $this->getAdminUrl(),
                'lgseoredirect_token' => Tools::getAdminTokenLite('AdminModules'),
                'simple_header' => false,
                'redirects' => $this->redirects,
                'filters' => $this->filters,
                'countredirects' => $this->total_redirects,
                'total_selected_redirects' => $this->total_selected_redirects,

                // Para evitar reenviar el fichero si se recarga
                'lgseoredirect_file_uploaded' => (int) Tools::isSubmit('newCSV'),

                // Estos son para la paginación de las redirecciones
                'list_total' => $this->total_redirects,
                'total_pages' => $this->total_pages,
                'selected_pagination' => $this->selected_pagination,
                'pagination' => $this->pagination,
                'page' => $this->offset,
                'list_id' => 'lgseoredirect',
                'lgseoredirects_pagesnotfound' => $this->pages_not_found,
                'lgseoredirects_pnf_filters' => $this->pnf_filters,
                'lgseoredirects_count_pages_not_found' => $this->pnf_total_redirects,
                'lgseoredirects_total_selected_redirects' => $this->pnf_total_selected_redirects,

                // Estos son para la paginación de las páginas no encontradas
                'lgseoredirects_pnf_list_total' => $this->pnf_total_redirects,
                'lgseoredirects_pnf_total_pages' => $this->pnf_total_pages,
                'lgseoredirects_pnf_selected_pagination' => $this->pnf_selected_pagination,
                'lgseoredirects_pnf_pagination' => $this->pnf_pagination,
                'lgseoredirects_pnf_page' => $this->pnf_offset,
                'lgseoredirects_pnf_list_id' => 'lgseoredirect_pnf',
            ]
        );

        $this->debug[] = [
            'lgseoredirect_is_rtl' => $this->context->language->is_rtl,
            'lgseoredirects_pagesnotfound' => $this->pages_not_found,
            'lgseoredirects_pnf_filters' => $this->pnf_filters,
            'lgseoredirects_count_pages_not_found' => $this->pnf_total_redirects,
            'lgseoredirects_total_selected_redirects' => $this->pnf_total_selected_redirects,
            'lgseoredirects_pnf_list_total' => $this->pnf_total_redirects,
            'lgseoredirects_pnf_total_pages' => $this->pnf_total_pages,
            'lgseoredirects_pnf_selected_pagination' => $this->pnf_selected_pagination,
            'lgseoredirects_pnf_pagination' => $this->pnf_pagination,
            'lgseoredirects_pnf_page' => $this->pnf_offset,
            'lgseoredirects_pnf_list_id' => 'lgseoredirect_pnf',
            'pages_not-found' => $this->pages_not_found,
        ];

        if (!empty($this->debug)) {
            $this->context->smarty->assign('lgseoredirect_debug', $this->debug);
        }

        $this->html .= $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name .
            DIRECTORY_SEPARATOR . 'views' .
            DIRECTORY_SEPARATOR . 'templates' .
            DIRECTORY_SEPARATOR . 'admin' .
            DIRECTORY_SEPARATOR . 'lgseoredirect.tpl'
        );

        return LGSeoRedirectPubli::getInstance()->getHeader()
            . $this->html
            . LGSeoRedirectPubli::getInstance()->getFooter();
    }

    public function postProcess()
    {
        // Create a redirect
        if (Tools::isSubmit('newRedirect')) {
            $this->createRedirect();
        }

        // Import CSV file
        if (Tools::isSubmit('newCSV')) {
            $this->importCSV();
        }

        // Export CSV file
        if (Tools::isSubmit('export')) {
            $this->exportCSV();
        }

        // Pages not found
        if (Tools::isSubmit('pagesNotFound')) {
            $this->pagesNotFound();
        }

        if (Tools::isSubmit('deleteAll')) {
            $this->deleteAll();
        }
    }

    /**
     * AJAX CALLS
     */
    public function ajaxProcessGetPNF()
    {
        $shop_ssl = $this->context->shop->domain_ssl;
        $shop_dom = $this->context->shop->domain;
        $shop_domain = (Tools::usingSecureMode() ? 'https://' . $shop_ssl : 'http://' . $shop_dom);

        $response = [];

        $shop = new Shop($this->context->shop->id);
        $shop_uri = $shop->getBaseURI();

        // Obtenemos las páginas no encontradas
        $pnf_offset = $this->pnf_offset - 1;
        $pnf_limit = $pnf_offset * $this->pnf_limit;
        $this->pages_not_found = $this->pagesnotfoundinstalled ? $this->getPagesNotFound($pnf_limit, $this->pnf_limit) : '';

        $this->context->smarty->assign(
            [
                'lgseoredirect_is_rtl' => $this->context->language->is_rtl,
                'lgseoredirect_shop_domain' => $shop_domain,
                'lgseoredirect_module_name' => 'lgseoredirects',
                'lgseoredirect_shop_uri' => Tools::rtrimString($shop_uri, '/'),
                'countredirects' => $this->pnf_total_redirects,
                'lgseoredirects_pagesnotfound' => $this->pages_not_found,
            ]
        );

        $response['rows'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name .
            DIRECTORY_SEPARATOR . 'views' .
            DIRECTORY_SEPARATOR . 'templates' .
            DIRECTORY_SEPARATOR . 'admin' .
            DIRECTORY_SEPARATOR . 'pages_not_found_rows.tpl'
        );

        $pagination_vars = [
            // Estos son para la paginación
            'lgseoredirect_ps16' => !version_compare(_PS_VERSION_, '1.6', '<'),
            'lgseoredirect_token' => Tools::getAdminTokenLite('AdminModules'),
            'simple_header' => false,
            'list_total' => $this->pnf_total_redirects,
            'total_pages' => $this->pnf_total_pages,
            'selected_pagination' => $this->pnf_selected_pagination,
            'pagination' => $this->pnf_pagination,
            'page' => $this->pnf_offset,
            'list_id' => 'lgseoredirect_pnf',
            'domain_base' => $this->getAdminUrl(),
            'lgseoredirect_shop_domain' => $shop_domain,
            'lgseoredirect_module_name' => 'lgseoredirects',
            'lgseoredirect_shop_uri' => $shop_uri,
        ];

        $this->context->smarty->assign($pagination_vars);

        $response['pagination'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name .
            DIRECTORY_SEPARATOR . 'views' .
            DIRECTORY_SEPARATOR . 'templates' .
            DIRECTORY_SEPARATOR . 'admin' .
            DIRECTORY_SEPARATOR . 'pagination.tpl'
        );

        // Tools::dieObject($response['pagination']);

        if (_PS_MODE_DEV_) {
            $response['debug']['sql'] = $this->sql;
            $response['debug']['sql_count'] = $this->sql_count;
            $response['debug']['total_redirects'] = $this->pnf_total_redirects;
            $response['debug']['total_pages'] = $this->pnf_total_pages;
            $response['debug']['selected_pagination'] = $this->pnf_selected_pagination;
            $response['debug']['pagination'] = $this->pnf_pagination;
            $response['debug']['pagination_vars'] = $pagination_vars;
            $response['debug']['page'] = $this->pnf_offset;
            $response['debug']['limit'] = $this->pnf_limit;
            $response['debug']['offset'] = $this->pnf_offset;
        }

        $response['status'] = 'ok';

        LGJsonApi::returnResponse($response);
    }

    public function ajaxProcessGetRedirects()
    {
        $shop_ssl = $this->context->shop->domain_ssl;
        $shop_dom = $this->context->shop->domain;
        $shop_domain = (Tools::usingSecureMode() ? 'https://' . $shop_ssl : 'http://' . $shop_dom);

        $response = [];

        $shop = new Shop($this->context->shop->id);
        $shop_uri = $shop->getBaseURI();

        // Obtenemos las redirecciones
        $this->redirects = $this->getRedirects((($this->offset - 1) * $this->limit), $this->limit);

        $this->context->smarty->assign(
            [
                'lgseoredirect_shop_domain' => $shop_domain,
                'lgseoredirect_module_name' => 'lgseoredirects',
                'lgseoredirect_shop_uri' => Tools::rtrimString($shop_uri, '/'),
                'countredirects' => $this->total_redirects,
                'redirects' => $this->redirects,
                ]
        );

        // TODO: Change fetch for display
        $response['rows'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name .
            DIRECTORY_SEPARATOR . 'views' .
            DIRECTORY_SEPARATOR . 'templates' .
            DIRECTORY_SEPARATOR . 'admin' .
            DIRECTORY_SEPARATOR . 'list_rows.tpl'
        );

        $pagination_vars = [
            // Estos son para la paginación
            'lgseoredirect_is_rtl' => $this->context->language->is_rtl,
            'lgseoredirect_ps16' => !version_compare(_PS_VERSION_, '1.6', '<'),
            'lgseoredirect_token' => Tools::getAdminTokenLite('AdminModules'),
            'simple_header' => false,
            'list_total' => $this->total_redirects,
            'total_pages' => $this->total_pages,
            'selected_pagination' => $this->selected_pagination,
            'pagination' => $this->pagination,
            'page' => $this->offset,
            'list_id' => 'lgseoredirect',
            'domain_base' => $this->getAdminUrl(),
            'lgseoredirect_shop_domain' => $shop_domain,
            'lgseoredirect_module_name' => 'lgseoredirects',
            'lgseoredirect_shop_uri' => $shop_uri,
        ];

        $this->context->smarty->assign($pagination_vars);

        $response['pagination'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name .
            DIRECTORY_SEPARATOR . 'views' .
            DIRECTORY_SEPARATOR . 'templates' .
            DIRECTORY_SEPARATOR . 'admin' .
            DIRECTORY_SEPARATOR . 'pagination.tpl'
        );

        if (_PS_MODE_DEV_) {
            $response['debug']['sql'] = $this->sql;
            $response['debug']['sql_count'] = $this->sql_count;
            $response['debug']['total_redirects'] = $this->total_redirects;
            $response['debug']['total_pages'] = $this->total_pages;
            $response['debug']['selected_pagination'] = $this->selected_pagination;
            $response['debug']['pagination'] = $this->pagination;
            $response['debug']['pagination_vars'] = $pagination_vars;
            $response['debug']['page'] = $this->offset;
            $response['debug']['limit'] = $this->limit;
            $response['debug']['offset'] = $this->offset;
        }

        $response['total_products'] = $this->total_redirects;

        $response['status'] = 'ok';

        LGJsonApi::returnResponse($response);
    }

    public function ajaxProcessSaveRedirects()
    {
        $response = [];
        $redirects = Tools::getValue('redirects', []);

        if (!empty($redirects)) {
            foreach ($redirects as $r) {
                $sql = 'UPDATE `' . _DB_PREFIX_ . 'lgseoredirect` '
                    . 'SET '
                    . '`url_old` = "' . pSQL(trim($r['origin'])) . '", '
                    . '`url_new` = "' . pSQL(trim($r['target'])) . '", '
                    . '`redirect_type` = "' . (int) trim($r['type']) . '" '
                    . 'WHERE `id` = ' . (int) $r['id'];
                try {
                    if (Db::getInstance()->execute($sql, false)) {
                        $response['status'] = 'ok';
                        $response['message'] = $this->l('All selected redirects updated with success');
                    } else {
                        $response['status'] = 'ko';
                        $response['message'] = $this->l('Error updating selected redirects. Please try again.');
                    }
                } catch (Exception $e) {
                    $response['status'] = 'ko';
                    $response['message'] = $this->l('Exception updating selected redirects. Please try again.');
                }
            }
        } else {
            $response['status'] = 'ko';
            $response['message'] = $this->l('Error. Selection empty');
        }
        LGJsonApi::returnResponse($response);
    }

    public function ajaxProcessSavePagesNotFound()
    {
        $response = [];
        $pages_not_found = Tools::getValue('pages_not_found', []);

        if (!empty($pages_not_found)) {
            foreach ($pages_not_found as $r) {
                $fecha = new DateTime();

                $request_uri = trim(urldecode($r['request_uri']));

                $sql_exists = 'SELECT `id` '
                    . 'FROM `' . _DB_PREFIX_ . 'lgseoredirect` '
                    . 'WHERE `pnf` LIKE "' . pSQL($request_uri) . '"'
                    . '  AND `id_shop` = ' . $this->context->shop->id;
                $id = Db::getInstance()->getValue($sql_exists, false);
                if (!$id) {
                    $sql = 'INSERT INTO `' . _DB_PREFIX_
                        . 'lgseoredirect`(`url_old`, `url_new`, `redirect_type`, `update`, `id_shop`, `pnf`) '
                        . 'VALUES('
                        . '"' . pSQL($request_uri) . '", '
                        . '"' . pSQL(trim($r['target'])) . '", '
                        . '"' . (int) $r['type'] . '", '
                        . '"' . pSQL($fecha->format('Y-m-d H:i:s')) . '", '
                        . (int) $this->context->shop->id . ', '
                        . '"' . pSQL($request_uri) . '" '
                        . ')';
                } else {
                    $sql = 'UPDATE `' . _DB_PREFIX_ . 'lgseoredirect` '
                        . 'SET'
                        . '   `url_old` = "' . pSQL($request_uri) . '", '
                        . '   `url_new` = "' . pSQL(trim($r['target'])) . '", '
                        . '   `redirect_type` = ' . (int) $r['type'] . ', '
                        . '   `update` = "' . pSQL($fecha->format('Y-m-d H:i:s')) . '", '
                        . '   `id_shop` = ' . (int) $this->context->shop->id . ', '
                        . '   `pnf` = "' . pSQL($request_uri) . '" '
                        . 'WHERE `id` = ' . (int) $id
                        . '  AND `id_shop` = ' . (int) $this->context->shop->id;
                }
                try {
                    if (Db::getInstance()->execute($sql)) {
                        $response['status'] = 'ok';
                        $response['message'] = $this->l('Page not found url redirected with success');
                    } else {
                        $response['status'] = 'ko';
                        $response['message'] = $this->l('Error adding selected Page not found url redirection.')
                                . ' '
                                . $this->l('Please try again.');
                    }
                } catch (Exception $e) {
                    $response['status'] = 'ko';
                    $response['message'] = $this->l('Exception adding selected Page not found url redirection.')
                                . ' '
                                . $this->l('Please try again.');
                    $response['debug'] = [
                        'sql' => $sql,
                        'code' => $e->getCode(),
                        'message' => $e->getMessage(),
                    ];
                }
            }
        } else {
            $response['status'] = 'ko';
            $response['message'] = $this->l('Error. Selection empty');
        }
        LGJsonApi::returnResponse($response);
    }

    public function ajaxProcessDeleteRedirects()
    {
        $response = [];
        $all_selected = (int) Tools::getValue('allselected', -1);
        $redirects = Tools::getValue('redirects', []);
        if ($all_selected >= 0) {
            if (empty($redirects) && $all_selected == 0) {
                $response['status'] = 'ko';
                $response['message'] = $this->l('Error. Selection empty');
            }
            if ($all_selected == 0) {
                $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'lgseoredirect` ' .
                    'WHERE `id` IN (' . implode(', ', array_map(null, $redirects)) . ')';
                if (Db::getInstance()->execute($sql)) {
                    $response['status'] = 'ok';
                    $response['message'] = $this->l('All selected redirects deleted with success');
                } else {
                    $response['status'] = 'ko';
                    $response['message'] = $this->l('Error deleting selected redirects. Please try again.');
                }
            } else {
                if (!empty($redirects)) {
                    $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'lgseoredirect` ' .
                        'WHERE `id` NOT IN (' . implode(', ', $redirects) . ')';
                } else {
                    $sql = 'TRUNCATE `' . _DB_PREFIX_ . 'lgseoredirect`';
                }

                if (Db::getInstance()->execute($sql)) {
                    $response['status'] = 'ok';
                    $response['message'] = $this->l('All selected redirects deleted with success');
                } else {
                    $response['status'] = 'ko';
                    $response['message'] = $this->l('Error deleting selected redirects. Please try again.');
                }
            }

            LGJsonApi::returnResponse($response);
        } else {
            LGJsonApi::returnResponse($response, 400);
        }
    }

    public function ajaxProcessDeletePNF()
    {
        $response = [];
        $pages_not_found = Tools::getValue('pages_not_found', []);

        if (!empty($pages_not_found)) {
            foreach ($pages_not_found as $r) {
                $sql_exists = 'SELECT `id` '
                    . 'FROM `' . _DB_PREFIX_ . 'lgseoredirect` '
                    . 'WHERE `pnf` LIKE "' . pSQL(trim($r)) . '"'
                    . '  AND `id_shop` = ' . $this->context->shop->id;
                $id = Db::getInstance()->getValue($sql_exists, false);
                if ($this->lgdebug) {
                    $response['debug']['sql_exists'] = $sql_exists;
                    $response['debug']['id'] = $id;
                }
                if ($id) {
                    $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'lgseoredirect` '
                        . 'WHERE `id` = ' . (int) $id
                        . '  AND `id_shop` = ' . $this->context->shop->id;
                    if ($this->lgdebug) {
                        $response['debug']['sql_Deletion'] = $sql;
                    }
                    try {
                        if (Db::getInstance()->execute($sql)) {
                            $response['status'] = 'ok';
                            $response['message'] = $this->l('Page not found url deleted with success');
                        } else {
                            $response['status'] = 'ko';
                            $response['message'] = $this->l('Error deleting selected Page not found url redirection.')
                                . ' '
                                . $this->l('Please try again.');
                        }
                    } catch (Exception $e) {
                        $response['status'] = 'ko';
                        $response['message'] = $this->l('Exception deleting selected Page not found url redirection.')
                                . ' '
                                . $this->l('Please try again.');

                        if ($this->lgdebug) {
                            $response['debug']['exception'] = [
                                'code' => $e->getCode(),
                                'message' => $e->getMessage(),
                            ];
                        }
                    }
                } else {
                    $response['status'] = 'ko';
                    $response['message'] = $this->l('Error. selected page not found does not exists.');
                }
            }
        } else {
            $response['status'] = 'ko';
            $response['message'] = $this->l('Error. Selection empty');
        }

        LGJsonApi::returnResponse($response);
    }

    /**
     * HOOKS
     */

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookDisplayBackOfficeHeader()
    {
        if ($this->context->controller instanceof AdminModulesController
            && pSQL(Tools::getValue('configure')) == $this->name) {
            $this->context->controller->addJQuery();
            $this->context->controller->addJS(_MODULE_DIR_ . $this->name . '/views/js/loadingoverlay.min.js');
            $this->context->controller->addJS(_MODULE_DIR_ . $this->name . '/views/js/lgseoredirect.js');

            if (version_compare(_PS_VERSION_, '1.6.0', '<')) {
                $this->context->controller->addJS(_MODULE_DIR_ . $this->name . '/views/js/bootstrap.js');
                $this->context->controller->addJS(_MODULE_DIR_ . $this->name . '/views/js/admin15.js');
                $this->context->controller->addCSS(_MODULE_DIR_ . $this->name . '/views/css/admin15.css');
            }
            $this->context->controller->addCSS(_MODULE_DIR_ . $this->name . '/views/css/' . $this->name . '.css');
            $this->context->controller->addCSS($this->_path . '/views/css/publi/lgpubli.css');
        }
    }

    public function hookModuleRoutes($params)
    {
        $this->checkRedirection();
        return [];
    }

    public function hookActionDispatcher($params)
    {
        $this->checkRedirection();
    }

    /**
     * USEFUL METHODS
     */

    /**
     * @param string $str Original string
     * @param string $needle String to trim from the end of $str
     * @param bool|true $caseSensitive Perform case sensitive matching, defaults to true
     * @return string Trimmed string
     */
    public function rtrim($str, $needle, $caseSensitive = true)
    {
        $strPosFunction = $caseSensitive ? 'strpos' : 'stripos';
        if ($strPosFunction($str, $needle, Tools::strlen($str) - Tools::strlen($needle)) !== false) {
            $str = Tools::substr($str, 0, -Tools::strlen($needle));
        }
        return $str;
    }

    /**
     * Get a valid URL to use from BackOffice (On versions < 1.6 this functionality is not included on classTools)
     *
     * @param string $url An URL to use in BackOffice
     * @param bool $entites Set to true to use htmlentities function on URL param
     */
    protected function getAdminUrl($url = null, $entities = false)
    {
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $link = Tools::getHttpHost(true) . __PS_BASE_URI__;

            if (isset($url)) {
                $link .= ($entities ? Tools::htmlentitiesUTF8($url) : $url);
            }

            return $link;
        } else {
            return Tools::getAdminUrl($url, $entities);
        }
    }

    public function checkRedirection()
    {
        if (Module::isEnabled($this->name)) {
            $context = Context::getContext();
            $uri_var = $_SERVER['REQUEST_URI'];
            if ($context->language->is_rtl) {
                $uri_var = rawurldecode($uri_var);
            }
            $shop_id = $context->shop->id;
            $baseuri = Tools::rtrimString($context->shop->getBaseURI(), '/');
            $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'lgseoredirect ' .
                'WHERE (CONCAT("' . $baseuri . '", url_old) LIKE BINARY "' . pSQL($uri_var) . '" ' .
                'OR CONCAT("' . $baseuri . '", url_old) LIKE BINARY "' . pSQL($uri_var) . '#%") ' .
                'AND id_shop = "' . (int) $shop_id . '" ' .
                'ORDER BY id DESC';
            $redirect = Db::getInstance()->getRow($sql);
            if ($redirect
                && $uri_var == preg_replace('/(#.*)/', '', $baseuri . $redirect['url_old'])
                && $shop_id == $redirect['id_shop']
            ) {
                if ($redirect['redirect_type'] == 301) {
                    $header = 'HTTP/1.1 301 Moved Permanently';
                }
                if ($redirect['redirect_type'] == 302) {
                    $header = 'HTTP/1.1 302 Moved Temporarily';
                }
                if ($redirect['redirect_type'] == 303) {
                    $header = 'HTTP/1.1 303 See Other';
                }
                Tools::redirect($redirect['url_new'], __PS_BASE_URI__, null, $header);
            }
        }
    }

    protected function getRedirects($limit = 0, $offset = 100)
    {
        $ands = ' AND id_shop = ' . Context::getContext()->shop->id;
        if (!empty($this->filters)) {
            foreach ($this->filters as $filter => $value) {
                switch ($filter) {
                    case 'id':
                        $ands .= ' AND lg.`id` LIKE "%' . $value . '%" ';
                        break;
                    case 'url_old':
                        $ands .= ' AND lg.`url_old` LIKE BINARY "%' . $value . '%" ';
                        break;
                    case 'url_new':
                        $ands .= ' AND lg.`url_new` LIKE "%' . $value . '%" ';
                        break;
                    case 'type':
                        $ands .= ' AND lg.`redirect_type` LIKE "%' . $value . '%" ';
                        break;
                    case 'date':
                        $fecha = date('d/m/Y H:m:s', $value);
                        $ands .= ' AND lg.`update` LIKE "%' . $fecha . '%" ';
                        break;
                    case 'error':
                        if ($value == 2) {
                            $ands .= ' AND ( ';
                            $ands .= '    IF( ';
                            $ands .= '        lg.`redirect_type`!= 301';
                            $ands .= '        AND lg.`redirect_type` != 302';
                            $ands .= '        AND lg.`redirect_type`!= 303, ';
                            $ands .= '        1,';
                            $ands .= '        0';
                            $ands .= '    ) > 0';
                            $ands .= '    OR IF(LOCATE("/", lg.`url_old`) = 0,1,0) > 0';
                            $ands .= '    OR IF(LOCATE("http", lg.`url_new`) = 0,1,0) > 0';
                            $ands .= ' ) ';
                        }
                        break;
                }
            }
        }
        $sql_total_rows = 'SELECT COUNT(*) ';

        $sql_selected_rows = 'SELECT ' .
            ' lg.`id` as id, ' .
            ' TRIM(lg.`url_old`) as url_old, ' .
            ' TRIM(lg.`url_new`) as url_new, ' .
            ' lg.`redirect_type` as redirect_type, ' .
            ' lg.`update` as "update", ';
        if (isset($this->filters['error']) && $this->filters['error'] == 1) {
            $sql_selected_rows .= ' b.`error_checkduplicate`, ';
        }
        $sql_selected_rows .= ' lg.`id_shop` as id_shop, ' .
            ' IF(LOCATE("/", lg.`url_old`) = 0,1,0) AS error_startwith, ' .
            ' IF(LOCATE("http", lg.`url_new`) = 0,1,0) AS error_startwith2, ' .
            ' IF(' .
            '    lg.`redirect_type`!= 301 ' .
            '    AND lg.`redirect_type` != 302 ' .
            '    AND lg.`redirect_type`!= 303,' .
            '    1,' .
            '    0' .
            ' ) AS error_wrong_redirect_type ';

        $sql = 'FROM `' . _DB_PREFIX_ . 'lgseoredirect` lg ';

        if (isset($this->filters['error']) && $this->filters['error'] == 1) {
            $sql .= 'RIGHT JOIN (' .
                ' SELECT a.`url_old`, COUNT(a.`url_old`) AS "error_checkduplicate" ' .
                ' FROM `' . _DB_PREFIX_ . 'lgseoredirect` a' .
                ((isset($this->filters['url_old']) && $this->filters['url_old'] != '') ?
                    ' WHERE a.`url_old` LIKE BINARY "%' . $this->filters['url_old'] . '%" ' :
                    ' ') .
                ' GROUP BY a.`url_old`, a.`id_shop` ' .
                ' HAVING COUNT(a.`url_old`) > 1 ' .
                ') b ON (lg.`url_old` = b.`url_old`)';
        }
        $sql .= 'WHERE 1 ' . $ands;

        $sql1 = $sql_selected_rows . $sql;
        if (isset($this->filters['error']) && $this->filters['error'] != 1) {
            $sql1 .= ' GROUP BY lg.`url_old`, lg.`id_shop` ';
        }
        $sql1 .= ' ORDER BY lg.id DESC '; // Todo: Add results ordering
        $sql1 .= 'LIMIT ' . $limit . ', ' . $offset;

        $sql2 = $sql_total_rows . $sql;
        $db = Db::getInstance();
        $this->total_redirects = $db->getValue($sql2, false);
        $redirects = $db->ExecuteS($sql1, true, false);
        $this->sql_count = $sql2; // For debug purposes only
        $this->sql = $sql1; // For debug purposes only
        $this->total_selected_redirects = $db->numRows();

        if ($this->total_redirects > 0) {
            $this->total_pages = (int) ($this->total_redirects / $this->selected_pagination);
            if ($this->total_redirects % $this->selected_pagination > 0) {
                $this->total_pages++;
            }
        }

        // Por rapidez, quitamos la subconsulta cuando no se filtra por duplicados y lo que hacemos es que chequeamos
        // los dupliacdos sólo para los resultados
        if (!isset($this->filters['error']) || (isset($this->filters['error']) && $this->filters['error'] != 1)) {
            $ids = [];
            foreach ($redirects as $redirect) {
                $ids[] = $redirect['url_old'];
            }

            $ids2 = array_unique($ids);
            $v_comunes1 = array_diff_assoc($ids, $ids2);
            $v_comunes2 = array_unique($v_comunes1); // Eliminamos los elementos repetidos
            $duplicate_count = $v_comunes2;

            $dc_final = [];
            foreach ($duplicate_count as $dc) {
                $dc_final[$dc] = '2';
            }
            unset($duplicate_count);

            foreach ($redirects as $index => $redirect) {
                $redirects[$index]['error_checkduplicate'] = $dc_final[$redirect['url_old']];
            }
        }

        if ($redirects) {
            foreach ($redirects as $index => $redirect) {
                $date = new DateTime($redirect['update']);
                $redirects[$index]['fecha'] = $date->format('Y-m-d H:i:s');
            }
        }

        return $redirects;
    }

    public function getPagesNotFound($limit = 0, $offset = 100)
    {
        $db = Db::getInstance();
        $this->sql = 'SELECT *  FROM `' . _DB_PREFIX_ . 'pagenotfound` as pnf WHERE 1 GROUP BY `request_uri`';
        if (!empty($this->pnf_filters)) {
            foreach ($this->pnf_filters as $filter => $value) {
                switch ($filter) {
                    case 'url_old':
                        $this->sql .= ' AND pnf.`request_uri` LIKE "%' . $value . '%" ';
                        break;
                    case 'url_new':
                        $str = $db->getValue(
                            'SELECT CONCAT(\'"\', GROUP_CONCAT(`pnf` SEPARATOR \'","\'),\'"\') ' .
                            'FROM `' . _DB_PREFIX_ . 'lgseoredirect` ' .
                            'WHERE `pnf` != "0" AND `url_new` LIKE "%' . $value . '%"'
                        );
                        if ($str) {
                            $this->sql .= ' AND pnf.`request_uri` IN(' . $str . ') ';
                        }
                        break;
                    case 'type':
                        $str = $db->getValue(
                            'SELECT CONCAT(\'"\', GROUP_CONCAT(`pnf` SEPARATOR \'","\'),\'"\') ' .
                            'FROM `' . _DB_PREFIX_ . 'lgseoredirect` ' .
                            'WHERE `pnf` != "0" AND `redirect_type` LIKE "%' . $value . '%"'
                        );
                        if ($str) {
                            $this->sql .= ' AND pnf.`request_uri` IN(' . $str . ') ';
                        }
                        break;
                    case 'date':
                        $fecha = date('d/m/Y H:m:s', $value);
                        $str = $db->getValue(
                            'SELECT CONCAT(\'"\', GROUP_CONCAT(`pnf` SEPARATOR \'","\'),\'"\') ' .
                            'FROM `' . _DB_PREFIX_ . 'lgseoredirect` ' .
                            'WHERE `pnf` != "0" AND `update` LIKE "%' . $fecha . '%"'
                        );
                        if ($str) {
                            $this->sql .= ' AND pnf.`request_uri` IN(' . $str . ') ';
                        }
                        break;
                    case 'error':
                        $this->sql .= ' AND pnf.`request_uri` ';
                        break;
                }
            }
        }
        $this->sql .= 'ORDER BY pnf.`id_pagenotfound` ASC LIMIT ' . $limit . ', ' . $offset;

        $this->sql_count = (int) $db->getValue('SELECT COUNT(DISTINCT `request_uri`) FROM `' . _DB_PREFIX_ . 'pagenotfound`', false);

        $this->pnf_total_redirects = $this->sql_count;

        if ($this->pnf_total_redirects > 0) {
            $this->pnf_total_pages = (int) ($this->pnf_total_redirects / $this->pnf_selected_pagination);
            if ($this->pnf_total_redirects % $this->pnf_selected_pagination > 0) {
                $this->pnf_total_pages++;
            }
        }

        $pagesNotFound = $db->executeS($this->sql, true, false);
        if (!empty($pagesNotFound)) {
            foreach ($pagesNotFound as $k => $page) {
                $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'lgseoredirect` lsr ' .
                    'WHERE lsr.`pnf` = "' . pSQL($page['request_uri']) . '"';
                if ($redirection = Db::getInstance()->getRow($sql)) {
                    $pagesNotFound[$k] = array_merge($pagesNotFound[$k], $redirection);
                } else {
                    $pagesNotFound[$k] = array_merge(
                        $pagesNotFound[$k],
                        [
                            'id' => null,
                            'old_url' => null,
                            'url_new' => null,
                            'redirect_type' => null,
                            'id_shop' => null,
                        ]
                    );
                }
            }
        }
        $this->pnf_total_selected_redirects = count($pagesNotFound); // $db->numRows();
        return $pagesNotFound;
    }

    public function getSmartyLink($link, $title, $target = '_blank', $force_spaces = false)
    {
        $this->context->smarty->assign(
            [
                // Estos son para la paginación
                'lgseoredirect_link' => $link,
                'lgseoredirect_title' => $title,
                'lgseoredirect_target' => $target,
                'lgseoredirect_forcespaces' => $force_spaces,
            ]
        );

        return $this->display(
            _PS_MODULE_DIR_ . $this->name,
            DIRECTORY_SEPARATOR . 'views' .
            DIRECTORY_SEPARATOR . 'templates' .
            DIRECTORY_SEPARATOR . 'admin' .
            DIRECTORY_SEPARATOR . 'link.tpl'
        );
    }

    public function check()
    {
        $this->checkOverridesDisabled();
        $this->checkNativeModulesEnabled();
        $this->checkVipAdvencedUrlRedirect();
        $this->checkPagesNotFountInstalled();
    }

    /**
     * check if the overrides are not disabled
     */
    protected function checkOverridesDisabled()
    {
        if ((int) Configuration::get('PS_DISABLE_OVERRIDES') > 0) {
            $url = 'index.php?tab=AdminPerformance&token='
                . Tools::getAdminTokenLite('AdminPerformance');

            $this->html .= $this->displayError(
                $this->l('The overrides are currently disabled on your store.') . '&nbsp;' .
                $this->l('Please change the configuration') .
                $this->getSmartyLink($url, $this->l('here'), '_blank', true) .
                $this->l('and choose "Disable all overrides: NO".')
            );
        }
    }

    /**
     * check if the native modules are not disabled
     */
    protected function checkNativeModulesEnabled()
    {
        if ((int) Configuration::get('PS_DISABLE_NON_NATIVE_MODULE') > 0) {
            $url = 'index.php?tab=AdminPerformance&token='
                . Tools::getAdminTokenLite('AdminPerformance');

            $this->html .= $this->displayError(
                $this->l('Non PrestaShop modules are currently disabled on your store.') . '&nbsp;' .
                $this->l('Please change the configuration') .
                $this->getSmartyLink($url, $this->l('here'), '_blank', true) .
                $this->l('and choose "Disable non PrestaShop module: NO".')
            );
        }
    }

    /**
     * check if the redirect option in the module "Advanced URL" is disabled
     */
    protected function checkVipAdvencedUrlRedirect()
    {
        if (Module::isInstalled('vipadvancedurl')
            && (int) Configuration::get('VIP_ADVANCED_URL_REDIRECT') > 0
        ) {
            $url = 'index.php?tab=AdminModules&token='
                . Tools::getAdminTokenLite('AdminModules')
                . '&configure=vipadvancedurl';

            $this->html .= $this->displayError(
                $this->l('The redirects must be disabled inside the module "Advanced URL".') . '&nbsp;' .
                $this->l('Please change the configuration') .
                $this->getSmartyLink($url, $this->l('here'), '_blank', true) .
                $this->l('and choose "Redirect: none".')
            );
        }
    }

    protected function checkPagesNotFountInstalled()
    {
        if (!$this->pagesnotfoundinstalled) {
            $url = 'index.php?controller=AdminModules&token='
                . Tools::getAdminTokenLite('AdminModules');
            $this->html .= $this->displayError(
                $this->l('The module "Pages not found" is not installed on your shop.') . '&nbsp;' .
                $this->l('Please install the module if you want be able to redirect pages not founded by it.') .
                '&nbsp;' . $this->l('Click ') .
                $this->getSmartyLink($url, $this->l('here'), '_blank', true) .
                $this->l(' to go to installation page.')
            );
        }
    }

    protected function createRedirect()
    {
        if (stripos(Tools::getValue('url_old'), '/') > 0 or stripos(Tools::getValue('url_old'), '/') === false) {
            $this->html .= Module::DisplayError(
                $this->l('The format of the old URL is not valid, the URI must start with "/".') .
                '&nbsp;' . $this->l('Please correct it.')
            );
        } elseif (Tools::substr(Tools::getValue('url_old'), -1) == ' ') {
            $this->html .= Module::DisplayError(
                $this->l('The old URL can not end up with a whitespace.') .
                '&nbsp;' . $this->l('Please correct it.')
            );
        } elseif (stripos(Tools::getValue('url_new'), 'http') > 0
            or stripos(Tools::getValue('url_new'), 'http') === false
        ) {
            $this->html .= Module::DisplayError(
                $this->l('The format of the new URL is not valid, it must start with "http://" or "https://".') .
                '&nbsp;' . $this->l('Please correct it.')
            );
        } elseif (Tools::substr(Tools::getValue('url_new'), -1) == ' ') {
            $this->html .= Module::DisplayError(
                $this->l('The new URL can not end up with a whitespace.') .
                '&nbsp;' . $this->l('Please correct it.')
            );
        } else {
            Db::getInstance()->Execute(
                'INSERT INTO ' . _DB_PREFIX_ . 'lgseoredirect ' .
                'VALUES (
                        NULL,
                        \'' . pSQL(trim(Tools::getValue('url_old'))) . '\',
                        \'' . pSQL(trim(Tools::getValue('url_new'))) . '\',
                        \'' . pSQL(trim(Tools::getValue('type'))) . '\',
                        NOW(),
                        \'' . (int) $this->context->shop->id . '\',
                        0
                    )'
            );
            $this->html .= Module::DisplayConfirmation($this->l('The redirect has been successfully created'));
        }
    }

    protected function deleteAll()
    {
        $sql = 'TRUNCATE `' . _DB_PREFIX_ . 'lgseoredirect`;';

        return Db::getInstance()->execute($sql);
    }

    protected function importCSV()
    {
        $separator = Tools::getValue('separator');
        if ($separator == 2) {
            $sp = ',';
        } else {
            $sp = ';';
        }
        if (is_uploaded_file($_FILES['csv']['tmp_name'])) {
            $type = explode('.', $_FILES['csv']['name']);
            if (Tools::strtolower(end($type)) == 'csv') {
                if (move_uploaded_file(
                    $_FILES['csv']['tmp_name'],
                    dirname(__FILE__) . '/csv/' . $_FILES['csv']['name']
                )) {
                    $archivo = $_FILES['csv']['name'];
                    $fp = fopen(dirname(__FILE__) . '/csv/' . $archivo, 'r');
                    while (($datos = fgetcsv($fp, 1000, '' . $sp . '')) !== false) {
                        Db::getInstance()->Execute(
                            'INSERT INTO ' . _DB_PREFIX_ . 'lgseoredirect ' .
                            'VALUES (
                                    NULL,
                                    \'' . pSQL(trim($datos[0])) . '\',
                                    \'' . pSQL(trim($datos[1])) . '\',
                                    \'' . pSQL(trim($datos[2])) . '\',
                                    NOW(),
                                    \'' . pSQL((int) trim($datos[3])) . '\',
                                    0
                                )'
                        );
                    }
                    fclose($fp);
                    $this->html .=
                        Module::DisplayConfirmation(
                            $this->l('The redirects of the CSV file have been successfully created')
                        );
                }
            } else {
                $this->html .=
                    Module::DisplayError(
                        $this->l('The format of the file is not valid, it must be saved in ".csv" format.') .
                        '&nbsp;' . $this->l('Please correct it.')
                    );
            }
        } else {
            $this->html .= Module::DisplayError($this->l('An error occurred while uploading the CSV file'));
        }
    }

    protected function exportCSV()
    {
        $separator = Tools::getValue('separator');
        if ($separator == 2) {
            $sp = ',';
        } else {
            $sp = ';';
        }
        $ln = "\n";
        $fp = fopen(_PS_ROOT_DIR_ . '/modules/' . $this->name . '/csv/saveredirects.csv', 'w');
        $getredirects = Db::getInstance()->ExecuteS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'lgseoredirect ' .
            'ORDER BY id ASC'
        );
        foreach ($getredirects as $getredirect) {
            fwrite(
                $fp,
                mb_convert_encoding($getredirect['url_old'] . $sp . $getredirect['url_new'], 'ISO-8859-1', 'UTF-8') .
                $sp . $getredirect['redirect_type'] . $sp . $getredirect['id_shop'] . $ln
            );
        }
        fclose($fp);
        if ($getredirects != false) {
            $context = Context::getContext();
            $context->smarty->assign([
                'name' => $this->name,
                'type' => 'saveredirects',
            ]);
            $saveredirects = $context->smarty->fetch($this->getTemplatePath(
                'views/templates/admin/export_csv.tpl'
            ));
            $this->html .=
                Module::DisplayConfirmation(
                    $this->l('The redirects have been correctly exported,') . $saveredirects
                );
        } else {
            $this->html .= Module::DisplayError($this->l('There are no redirects to export'));
        }
    }

    protected function pagesNotFound()
    {
        $sp = ';';
        $ln = "\n";
        $fp = fopen(_PS_ROOT_DIR_ . '/modules/' . $this->name . '/csv/pagesnotfound.csv', 'w');
        $pagesNotFound = Db::getInstance()->ExecuteS(
            'SELECT DISTINCT pnf.request_uri, pnf.id_shop, su.domain ' .
            'FROM ' . _DB_PREFIX_ . 'pagenotfound as pnf ' .
            'LEFT JOIN ' . _DB_PREFIX_ . 'lgseoredirect as lsr ' .
            'ON pnf.request_uri = lsr.url_old ' .
            'INNER JOIN ' . _DB_PREFIX_ . 'shop_url as su ' .
            'ON pnf.id_shop = su.id_shop ' .
            'WHERE lsr.url_old IS NULL ' .
            'ORDER BY pnf.date_add DESC'
        );
        $domain_base = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://');
        $redirect_type = '301';
        foreach ($pagesNotFound as $pageNotFound) {
            fwrite(
                $fp,
                mb_convert_encoding($pageNotFound['request_uri'], 'ISO-8859-1', 'UTF-8') . '' .
                $sp . $domain_base . $pageNotFound['domain'] .
                $sp . $redirect_type . $sp . $pageNotFound['id_shop'] . $ln
            );
        }
        fclose($fp);
        if ($pagesNotFound != false) {
            $context = Context::getContext();
            $context->smarty->assign([
                'name' => $this->name,
                'type' => 'pagesnotfound',
            ]);
            $pagesnotfound = $context->smarty->fetch($this->getTemplatePath(
                'views/templates/admin/export_csv.tpl'
            ));
            $this->html .=
                Module::DisplayConfirmation(
                    $this->l('The list of pages not found has been correctly generated,') . $pagesnotfound
                );
        } else {
            $this->html .= Module::DisplayError($this->l('There are no pages not found on your shop'));
        }
    }
}
