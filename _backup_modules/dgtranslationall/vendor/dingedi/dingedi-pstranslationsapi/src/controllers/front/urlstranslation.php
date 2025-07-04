<?php
/**
 * License limited to a single site, for use on another site please purchase a license for this module.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Dingedi.com
 * @copyright Copyright 2020 © Dingedi All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  Dingedi PrestaShop Modules
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class DgtranslationallUrlstranslationModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();

        if (\Tools::getValue('dingedi_secret_key') !== \Configuration::get('dingedi_secret_key')) {
            die();
        }

        $_GET_saved = $_GET;
        $_SERVER_saved = $_SERVER;
        $dispatcher_saved = \Dispatcher::$instance;

        $urlsToTranslate = \Tools::getValue('dingedi_urlstotranslate');

        if (!is_array($urlsToTranslate)) {
            $urlsToTranslate = array();
        }

        $id_lang = (int)\Tools::getValue('dingedi_idlang');
        $urlsTranslated = array();

        try {
            foreach (array_unique($urlsToTranslate) as $url) {
                $urlsTranslated[$url] = $this->translateUrl($url, $id_lang);
            }
        } catch (\Exception $exception) {
        }

        $_GET = $_GET_saved;
        $_SERVER = $_SERVER_saved;
        \Dispatcher::$instance = $dispatcher_saved;

        \Dingedi\PsTools\DgTools::jsonResponse($urlsTranslated);
    }

    /**
     * @param string $url
     * @param int $id_lang
     * @return string
     */
    private function translateUrl($url, $id_lang)
    {
        $url = (string) $url;
        $id_lang = (int) $id_lang;
        $language = \Language::getLanguage($id_lang);

        if ($language['active'] === false || (string)$language['active'] === "0") {
            return $url;
        }

        $slug = $this->getSlug($url);

        $_GET = array();
        $_SERVER['REQUEST_URI'] = $slug;
        $_SERVER['HTTP_X_REWRITE_URL'] = $slug;

        \Dispatcher::$instance = null;

        $controller = \Dispatcher::getInstance()->getController();

        if (in_array($controller, array('product', 'category', 'supplier', 'manufacturer', 'cms', 'module'))) {
            $error = false;
            foreach (array('id_product', 'id_category', 'id_supplier', 'id_manufacturer', 'id_cms', 'id_cms_category') as $v) {
                if (\Tools::getValue($v)) {
                    $error = true;
                    break;
                }
            }

            if (!$error) {

                $urls_modules = ['sturls'];

                foreach ($urls_modules as $module) {
                    if (\Module::isInstalled($module) && \Module::isEnabled($module)) {
                        $html = \Tools::file_get_contents($url);
                        $parser = new \Dingedi\PsTranslationsApi\Html\DgHTMLParser($html);
                        $domxpath = $parser->getDOMXPath();
                        $language = \Language::getLanguage($id_lang);
                        $links = $domxpath->query('//link[@rel="alternate"]');

                        foreach ($links as $link) {
                            $hreflang = $link->getAttribute('hreflang');
                            $match = [
                                $language['locale'],
                                strtoupper($language['locale']),
                                strtolower($language['locale']),
                                strtolower($language['iso_code']),
                                strtoupper($language['iso_code']),
                            ];

                            if (in_array($hreflang, $match)) {
                                $url = $link->getAttribute('href');
                                $url = $this->removeGetParameter($url, 'rewrite');

                                return $url;
                            }
                        }
                    }
                }

                return $url;
            }
        }

        if ($controller == 'pagenotfound') {
            return $url;
        }

        $new_url = \Context::getContext()->link->getLanguageLink($id_lang);

        $new_url = preg_replace("/(\??id_shop=\d?)/", "", (string)$new_url);
        $new_url = $new_url . $this->getUrlParameters($url);

        if ($url[0] === '/' && $new_url[0] !== '/') {
            $new_url = '/' . $new_url;
        }

        return $new_url;
    }

    /**
     * @param string $url
     * @param string $parameter
     * @return string
     */
    private function removeGetParameter($url, $parameter)
    {
        $url = (string) $url;
        $parameter = (string) $parameter;
        $parsedUrl = parse_url($url);
        $query = array();

        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $query);
            unset($query[$parameter]);
        }

        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $query = !empty($query) ? '?' . http_build_query($query) : '';

        return $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $path . $query;
    }

    /**
     * @param string $url
     * @return string
     */
    private function getUrlParameters($url)
    {
        $url = (string) $url;
        $parameters = '';

        if (\Tools::strpos($url, '?') !== false) {
            $parameters = \Tools::substr($url, \Tools::strpos($url, '?'));
        } elseif (\Tools::strpos($url, '#') !== false) {
            $parameters = \Tools::substr($url, \Tools::strpos($url, '#'));
        }

        return $parameters;
    }

    /**
     * @param string $url
     * @return string
     */
    private function getSlug($url)
    {
        $url = (string) $url;
        if (!preg_match('/.+\..+/', $url)) {
            $url = \Dingedi\PsTranslationsApi\DgUrlTranslation::getHost() . $url;
        }

        if (\Tools::strpos($url, 'http') === false) {
            $url = 'http://' . $url;
        }

        $parts = parse_url($url);
        if ($parts) {
            $slug = '/' . ltrim($parts['path'], '/');
        } else {
            $slug = $url;
        }

        return $slug;
    }
}
