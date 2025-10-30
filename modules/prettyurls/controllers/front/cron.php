<?php
/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

class PrettyurlsCronModuleFrontController extends ModuleFrontController
{
    protected $newpsversion = null;
    public const HOOK_ADD_URLS_SM = 'SitemapAppendUrls';
    public $cron = false;
    private $sql_checks = [];
    protected $type_array = null;

    public function __construct()
    {
        $this->newpsversion = Tools::version_compare(_PS_VERSION_, '1.7.8', '>=') ? 1 : 0;
        $this->type_array = ['home', 'meta', 'product', 'category', 'manufacturer', 'supplier', 'cms', 'module'];
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();
        $action = (int) Tools::getValue('action');
        $token = Configuration::get('FMM_PRETTY_TOKEN');
        if ($action <= 0 && $token === Tools::getValue('token')) {
            $this->emptySitemap();
            $this->createSitemap();
            $gsitemap_last_export = Configuration::get('GSITEMAP_LAST_EXPORT');
            echo 'updated: ' . $gsitemap_last_export;
            exit;
        } elseif ($action >= 1 && $token === Tools::getValue('token')) {
            echo 'Last Updated: ' . date('Y-m-d H:i:s', time());
            exit;
        }
    }

    public function emptySitemap($id_shop = 0)
    {
        if (!isset($this->context)) {
            $this->context = new Context();
        }
        if ($id_shop != 0) {
            $this->context->shop = new Shop((int) $id_shop);
        }
        $links = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'gsitemap_sitemap` WHERE id_shop = ' . (int) $this->context->shop->id);
        if ($links) {
            foreach ($links as $link) {
                @unlink($this->normalizeDirectory(_PS_ROOT_DIR_) . $link['link']);
            }

            return Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'gsitemap_sitemap` WHERE id_shop = ' . (int) $this->context->shop->id);
        }

        return true;
    }

    protected function normalizeDirectory($directory)
    {
        $last = $directory[Tools::strlen($directory) - 1];

        if (in_array($last, ['/', '\\'])) {
            $directory[Tools::strlen($directory) - 1] = DIRECTORY_SEPARATOR;

            return $directory;
        }

        $directory .= DIRECTORY_SEPARATOR;

        return $directory;
    }

    private function _getHomeLink(&$link_sitemap, $lang, &$index, &$i)
    {
        if (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        return $this->_addLinkToSitemap(
            $link_sitemap, [
                'type' => 'home',
                'page' => 'home',
                'link' => $protocol . Tools::getShopDomainSsl(false) . $this->context->shop->getBaseURI() . (method_exists('Language', 'isMultiLanguageActivated') ? Language::isMultiLanguageActivated() ? $lang['iso_code'] . '/' : '' : ''),
                'image' => false,
            ], $lang['iso_code'], $index, $i, -1
        );
    }

    private function _getMetaLink(&$link_sitemap, $lang, &$index, &$i, $id_meta = 0)
    {
        if (method_exists('ShopUrl', 'resetMainDomainCache')) {
            ShopUrl::resetMainDomainCache();
        }
        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            $metas = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'meta` WHERE `configurable` > 0 AND `id_meta` >= ' . (int) $id_meta . ' ORDER BY `id_meta` ASC');
        } else {
            $metas = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'meta` WHERE `id_meta` >= ' . (int) $id_meta . ' ORDER BY `id_meta` ASC');
        }
        foreach ($metas as $meta) {
            $url = '';
            if (!in_array($meta['id_meta'], explode(',', Configuration::get('GSITEMAP_DISABLE_LINKS')))) {
                $url_rewrite = Db::getInstance()->getValue('SELECT url_rewrite, id_shop FROM `' . _DB_PREFIX_ . 'meta_lang` WHERE `id_meta` = ' . (int) $meta['id_meta'] . ' AND `id_shop` =' . (int) $this->context->shop->id . ' AND `id_lang` = ' . (int) $lang['id_lang']);
                Dispatcher::getInstance()->addRoute($meta['page'], isset($url_rewrite) ? $url_rewrite : $meta['page'], $meta['page'], $lang['id_lang']);
                $uri_path = Dispatcher::getInstance()->createUrl($meta['page'], $lang['id_lang'], [], (bool) Configuration::get('PS_REWRITING_SETTINGS'));
                $url .= Tools::getShopDomainSsl(true) . (($this->context->shop->virtual_uri) ? __PS_BASE_URI__ . $this->context->shop->virtual_uri : __PS_BASE_URI__) . (Language::isMultiLanguageActivated() ? $lang['iso_code'] . '/' : '') . ltrim($uri_path, '/');

                if (!$this->_addLinkToSitemap(
                    $link_sitemap, [
                        'type' => 'meta',
                        'page' => $meta['page'],
                        'link' => $url,
                        'image' => false,
                    ], $lang['iso_code'], $index, $i, $meta['id_meta']
                )) {
                    return false;
                }
            }
        }

        return true;
    }

    private function _getProductLink(&$link_sitemap, $lang, &$index, &$i, $id_product = 0)
    {
        $link = new Link();
        if (method_exists('ShopUrl', 'resetMainDomainCache')) {
            ShopUrl::resetMainDomainCache();
        }

        $products_id = Db::getInstance()->ExecuteS('SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product_shop` WHERE `id_product` >= ' . (int) $id_product . ' AND `active` = 1 AND `id_shop`=' . $this->context->shop->id . ' ORDER BY `id_product` ASC');

        foreach ($products_id as $product_id) {
            $product = new Product((int) $product_id['id_product'], false, (int) $lang['id_lang']);

            $url = $link->getProductLink($product, $product->link_rewrite, htmlspecialchars(strip_tags($product->category)), $product->ean13, (int) $lang['id_lang'], (int) $this->context->shop->id, 0, true);

            $id_image = Product::getCover((int) $product_id['id_product']);
            if (isset($id_image['id_image'])) {
                if ($this->newpsversion) {
                    $img_type = ImageType::getFormattedName('large');
                } else {
                    $img_type = ImageType::getFormatedName('large');
                }
                $image_link = $this->context->link->getImageLink($product->link_rewrite, $product->id . '-' . (int) $id_image['id_image'], $img_type);
                $image_link = (!in_array(rtrim(Context::getContext()->shop->virtual_uri, '/'), explode('/', $image_link))) ? str_replace(
                    [
                        'https',
                        Context::getContext()->shop->domain . Context::getContext()->shop->physical_uri,
                    ], [
                        'http',
                        Context::getContext()->shop->domain . Context::getContext()->shop->physical_uri . Context::getContext()->shop->virtual_uri,
                    ], $image_link
                ) : $image_link;
            }
            $file_headers = (Configuration::get('GSITEMAP_CHECK_IMAGE_FILE')) ? @get_headers($image_link) : true;
            $image_product = [];
            if (isset($image_link) && $file_headers === true) {
                $image_product = [
                    'title_img' => htmlspecialchars(strip_tags($product->name)),
                    'caption' => htmlspecialchars(strip_tags($product->description_short)),
                    'link' => $image_link,
                ];
            }
            if (!$this->_addLinkToSitemap(
                $link_sitemap, [
                    'type' => 'product',
                    'page' => 'product',
                    'lastmod' => $product->date_upd,
                    'link' => $url,
                    'image' => $image_product,
                ], $lang['iso_code'], $index, $i, $product_id['id_product']
            )) {
                return false;
            }

            unset($image_link);
        }

        return true;
    }

    private function _getCategoryLink(&$link_sitemap, $lang, &$index, &$i, $id_category = 0)
    {
        $link = new Link();
        if (method_exists('ShopUrl', 'resetMainDomainCache')) {
            ShopUrl::resetMainDomainCache();
        }

        $categories_id = Db::getInstance()->ExecuteS(
            'SELECT c.id_category FROM `' . _DB_PREFIX_ . 'category` c
				INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON c.`id_category` = cs.`id_category`
				WHERE c.`id_category` >= ' . (int) $id_category . ' AND c.`active` = 1 AND c.`id_category` != 1 AND c.id_parent > 0 AND c.`id_category` > 0 AND cs.`id_shop` = ' . (int) $this->context->shop->id . ' ORDER BY c.`id_category` ASC'
        );

        foreach ($categories_id as $category_id) {
            $category = new Category((int) $category_id['id_category'], (int) $lang['id_lang']);
            $url = $link->getCategoryLink($category, urlencode($category->link_rewrite), (int) $lang['id_lang']);

            if ($category->id_image) {
                if ($this->newpsversion) {
                    $img_type = ImageType::getFormattedName('category');
                } else {
                    $img_type = ImageType::getFormatedName('category');
                }
                $image_link = $this->context->link->getCatImageLink($category->link_rewrite, (int) $category->id_image, $img_type);
                $image_link = (!in_array(rtrim(Context::getContext()->shop->virtual_uri, '/'), explode('/', $image_link))) ? str_replace(
                    [
                        'https',
                        Context::getContext()->shop->domain . Context::getContext()->shop->physical_uri,
                    ], [
                        'http',
                        Context::getContext()->shop->domain . Context::getContext()->shop->physical_uri . Context::getContext()->shop->virtual_uri,
                    ], $image_link
                ) : $image_link;
            }
            $file_headers = (Configuration::get('GSITEMAP_CHECK_IMAGE_FILE')) ? @get_headers($image_link) : true;
            $image_category = [];
            if (isset($image_link) && $file_headers === true) {
                $image_category = [
                    'title_img' => htmlspecialchars(strip_tags($category->name)),
                    'link' => $image_link,
                ];
            }

            if (!$this->_addLinkToSitemap(
                $link_sitemap, [
                    'type' => 'category',
                    'page' => 'category',
                    'lastmod' => $category->date_upd,
                    'link' => $url,
                    'image' => $image_category,
                ], $lang['iso_code'], $index, $i, (int) $category_id['id_category']
            )) {
                return false;
            }

            unset($image_link);
        }

        return true;
    }

    private function _getManufacturerLink(&$link_sitemap, $lang, &$index, &$i, $id_manufacturer = 0)
    {
        $link = new Link();
        if (method_exists('ShopUrl', 'resetMainDomainCache')) {
            ShopUrl::resetMainDomainCache();
        }
        $manufacturers_id = Db::getInstance()->ExecuteS(
            'SELECT m.`id_manufacturer` FROM `' . _DB_PREFIX_ . 'manufacturer` m
			INNER JOIN `' . _DB_PREFIX_ . 'manufacturer_lang` ml on m.`id_manufacturer` = ml.`id_manufacturer`' .
            ($this->tableColumnExists(_DB_PREFIX_ . 'manufacturer_shop') ? ' INNER JOIN `' . _DB_PREFIX_ . 'manufacturer_shop` ms ON m.`id_manufacturer` = ms.`id_manufacturer` ' : '') .
            ' WHERE m.`active` = 1  AND m.`id_manufacturer` >= ' . (int) $id_manufacturer .
            ($this->tableColumnExists(_DB_PREFIX_ . 'manufacturer_shop') ? ' AND ms.`id_shop` = ' . (int) $this->context->shop->id : '') .
            ' AND ml.`id_lang` = ' . (int) $lang['id_lang'] .
            ' ORDER BY m.`id_manufacturer` ASC'
        );
        foreach ($manufacturers_id as $manufacturer_id) {
            $manufacturer = new Manufacturer((int) $manufacturer_id['id_manufacturer'], $lang['id_lang']);
            $url = $link->getManufacturerLink($manufacturer, $manufacturer->link_rewrite, $lang['id_lang']);
            if ($this->newpsversion) {
                $img_type = ImageType::getFormattedName('medium');
            } else {
                $img_type = ImageType::getFormatedName('medium');
            }
            $image_link = 'http' . (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE') ? 's' : '') . '://' . Tools::getMediaServer(_THEME_MANU_DIR_) . _THEME_MANU_DIR_ . ((!file_exists(_PS_MANU_IMG_DIR_ . '/' . (int) $manufacturer->id . '-' . $img_type . '.jpg')) ? $lang['iso_code'] . '-default' : (int) $manufacturer->id) . '-' . $img_type . '.jpg';
            $image_link = (!in_array(rtrim(Context::getContext()->shop->virtual_uri, '/'), explode('/', $image_link))) ? str_replace(
                [
                    'https',
                    Context::getContext()->shop->domain . Context::getContext()->shop->physical_uri,
                ], [
                    'http',
                    Context::getContext()->shop->domain . Context::getContext()->shop->physical_uri . Context::getContext()->shop->virtual_uri,
                ], $image_link
            ) : $image_link;

            $file_headers = (Configuration::get('GSITEMAP_CHECK_IMAGE_FILE')) ? @get_headers($image_link) : true;
            $manifacturer_image = [];
            if ($file_headers === true) {
                $manifacturer_image = [
                    'title_img' => htmlspecialchars(strip_tags($manufacturer->name)),
                    'caption' => htmlspecialchars(strip_tags($manufacturer->short_description)),
                    'link' => $image_link,
                ];
            }
            if (!$this->_addLinkToSitemap(
                $link_sitemap, [
                    'type' => 'manufacturer',
                    'page' => 'manufacturer',
                    'lastmod' => $manufacturer->date_upd,
                    'link' => $url,
                    'image' => $manifacturer_image,
                ], $lang['iso_code'], $index, $i, $manufacturer_id['id_manufacturer']
            )) {
                return false;
            }
        }

        return true;
    }

    private function _getSupplierLink(&$link_sitemap, $lang, &$index, &$i, $id_supplier = 0)
    {
        $link = new Link();
        if (method_exists('ShopUrl', 'resetMainDomainCache')) {
            ShopUrl::resetMainDomainCache();
        }
        $suppliers_id = Db::getInstance()->ExecuteS(
            'SELECT s.`id_supplier` FROM `' . _DB_PREFIX_ . 'supplier` s
			INNER JOIN `' . _DB_PREFIX_ . 'supplier_lang` sl ON s.`id_supplier` = sl.`id_supplier` ' .
            ($this->tableColumnExists(_DB_PREFIX_ . 'supplier_shop') ? 'INNER JOIN `' . _DB_PREFIX_ . 'supplier_shop` ss ON s.`id_supplier` = ss.`id_supplier`' : '') . '
			WHERE s.`active` = 1 AND s.`id_supplier` >= ' . (int) $id_supplier .
            ($this->tableColumnExists(_DB_PREFIX_ . 'supplier_shop') ? ' AND ss.`id_shop` = ' . (int) $this->context->shop->id : '') . '
			AND sl.`id_lang` = ' . (int) $lang['id_lang'] . '
			ORDER BY s.`id_supplier` ASC'
        );
        foreach ($suppliers_id as $supplier_id) {
            $supplier = new Supplier((int) $supplier_id['id_supplier'], $lang['id_lang']);
            $url = $link->getSupplierLink($supplier, $supplier->link_rewrite, $lang['id_lang']);
            if ($this->newpsversion) {
                $img_type = ImageType::getFormattedName('medium');
            } else {
                $img_type = ImageType::getFormatedName('medium');
            }
            $image_link = 'http://' . Tools::getMediaServer(_THEME_SUP_DIR_) . _THEME_SUP_DIR_ . ((!file_exists(_THEME_SUP_DIR_ . '/' . (int) $supplier->id . '-' . $img_type . '.jpg')) ? $lang['iso_code'] . '-default' : (int) $supplier->id) . '-' . $img_type . '.jpg';
            $image_link = (!in_array(rtrim(Context::getContext()->shop->virtual_uri, '/'), explode('/', $image_link))) ? str_replace(
                [
                    'https',
                    Context::getContext()->shop->domain . Context::getContext()->shop->physical_uri,
                ], [
                    'http',
                    Context::getContext()->shop->domain . Context::getContext()->shop->physical_uri . Context::getContext()->shop->virtual_uri,
                ], $image_link
            ) : $image_link;

            $file_headers = (Configuration::get('GSITEMAP_CHECK_IMAGE_FILE')) ? @get_headers($image_link) : true;
            $supplier_image = [];
            if ($this->newpsversion) {
                $img_type = ImageType::getFormattedName('medium');
            } else {
                $img_type = ImageType::getFormatedName('medium');
            }
            if ($file_headers === true) {
                $supplier_image = [
                    'title_img' => htmlspecialchars(strip_tags($supplier->name)),
                    'link' => 'http' . (Configuration::get('PS_SSL_ENABLED') ? 's' : '') . '://' . Tools::getMediaServer(_THEME_SUP_DIR_) . _THEME_SUP_DIR_ . ((!file_exists(_THEME_SUP_DIR_ . '/' . (int) $supplier->id . '-' . $img_type . '.jpg')) ? $lang['iso_code'] . '-default' : (int) $supplier->id) . '-' . $img_type . '.jpg',
                ];
            }
            if (!$this->_addLinkToSitemap(
                $link_sitemap, [
                    'type' => 'supplier',
                    'page' => 'supplier',
                    'lastmod' => $supplier->date_upd,
                    'link' => $url,
                    'image' => $supplier_image,
                ], $lang['iso_code'], $index, $i, $supplier_id['id_supplier']
            )) {
                return false;
            }
        }

        return true;
    }

    private function _getCmsLink(&$link_sitemap, $lang, &$index, &$i, $id_cms = 0)
    {
        $link = new Link();
        if (method_exists('ShopUrl', 'resetMainDomainCache')) {
            ShopUrl::resetMainDomainCache();
        }
        $cmss_id = Db::getInstance()->ExecuteS(
            'SELECT c.`id_cms` FROM `' . _DB_PREFIX_ . 'cms` c INNER JOIN `' . _DB_PREFIX_ . 'cms_lang` cl ON c.`id_cms` = cl.`id_cms` ' .
            ($this->tableColumnExists(_DB_PREFIX_ . 'supplier_shop') ? 'INNER JOIN `' . _DB_PREFIX_ . 'cms_shop` cs ON c.`id_cms` = cs.`id_cms` ' : '') .
            'INNER JOIN `' . _DB_PREFIX_ . 'cms_category` cc ON c.id_cms_category = cc.id_cms_category AND cc.active = 1
				WHERE c.`active` =1 AND c.`indexation` =1 AND c.`id_cms` >= ' . (int) $id_cms .
            ($this->tableColumnExists(_DB_PREFIX_ . 'supplier_shop') ? ' AND cs.id_shop = ' . (int) $this->context->shop->id : '') .
            ' AND cl.`id_lang` = ' . (int) $lang['id_lang'] .
            ' ORDER BY c.`id_cms` ASC'
        );

        if (is_array($cmss_id)) {
            foreach ($cmss_id as $cms_id) {
                $cms = new CMS((int) $cms_id['id_cms'], $lang['id_lang']);
                $cms->link_rewrite = urlencode(is_array($cms->link_rewrite) ? $cms->link_rewrite[(int) $lang['id_lang']] : $cms->link_rewrite);
                $url = $link->getCMSLink($cms, null, null, $lang['id_lang']);

                if (!$this->_addLinkToSitemap(
                    $link_sitemap, [
                        'type' => 'cms',
                        'page' => 'cms',
                        'link' => $url,
                        'image' => false,
                    ], $lang['iso_code'], $index, $i, $cms_id['id_cms']
                )) {
                    return false;
                }
            }
        }

        return true;
    }

    private function _getModuleLink(&$link_sitemap, $lang, &$index, &$i, $num_link = 0)
    {
        $modules_links = Hook::exec(self::HOOK_ADD_URLS_SM, ['lang' => $lang], null, true);
        if (empty($modules_links) || !is_array($modules_links)) {
            return true;
        }
        $links = [];
        foreach ($modules_links as $module_links) {
            $links = array_merge($links, $module_links);
        }
        foreach ($module_links as $n => $link) {
            if ($num_link > $n) {
                continue;
            }
            $link['type'] = 'module';
            if (!$this->_addLinkToSitemap($link_sitemap, $link, $lang['iso_code'], $index, $i, $n)) {
                return false;
            }
        }

        return true;
    }

    public function createSitemap($id_shop = 0)
    {
        if (@fopen($this->normalizeDirectory(_PS_ROOT_DIR_) . '/test.txt', 'w') == false) {
            $this->context->smarty->assign('google_maps_error', $this->l('An error occured while trying to check your file permissions. Please adjust your permissions to allow PrestaShop to write a file in your root directory.'));

            return false;
        } else {
            @unlink($this->normalizeDirectory(_PS_ROOT_DIR_) . 'test.txt');
        }

        if ($id_shop != 0) {
            $this->context->shop = new Shop((int) $id_shop);
        }

        $type = Tools::getValue('type') ? Tools::getValue('type') : '';
        $languages = Language::getLanguages(true, $id_shop);
        $lang_stop = Tools::getValue('lang') ? true : false;
        $id_obj = Tools::getValue('id') ? (int) Tools::getValue('id') : 0;
        foreach ($languages as $lang) {
            $i = 0;
            $index = (Tools::getValue('index') && Tools::getValue('lang') == $lang['iso_code']) ? (int) Tools::getValue('index') : 0;
            if ($lang_stop && $lang['iso_code'] != Tools::getValue('lang')) {
                continue;
            } elseif ($lang_stop && $lang['iso_code'] == Tools::getValue('lang')) {
                $lang_stop = false;
            }

            $link_sitemap = [];
            foreach ($this->type_array as $type_val) {
                if ($type == '' || $type == $type_val) {
                    $function = '_get' . Tools::ucfirst($type_val) . 'Link';
                    if (!$this->$function($link_sitemap, $lang, $index, $i, $id_obj)) {
                        return false;
                    }
                    $type = '';
                    $id_obj = 0;
                }
            }
            $this->_recursiveSitemapCreator($link_sitemap, $lang['iso_code'], $index);
            $index = 0;
        }

        $this->_createIndexSitemap();
        Configuration::updateValue('GSITEMAP_LAST_EXPORT', date('r'));
        Tools::file_get_contents('http://www.google.com/webmasters/sitemaps/ping?sitemap=' . urlencode('http' . (Configuration::get('PS_SSL_ENABLED') ? 's' : '') . '://' . Tools::getShopDomain(false, true) . $this->context->shop->physical_uri . $this->context->shop->virtual_uri . $this->context->shop->id . '_index_sitemap.xml'));

        if ($this->cron) {
            exit;
        }
    }

    private function _saveSitemapLink($sitemap)
    {
        if ($sitemap) {
            return Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'gsitemap_sitemap` (`link`, id_shop) VALUES (\'' . pSQL($sitemap) . '\', ' . (int) $this->context->shop->id . ')');
        }

        return false;
    }

    private function _recursiveSitemapCreator($link_sitemap, $lang, &$index)
    {
        if (!count($link_sitemap)) {
            return false;
        }

        $sitemap_link = $this->context->shop->id . '_' . $lang . '_' . $index . '_sitemap.xml';
        $write_fd = fopen($this->normalizeDirectory(_PS_ROOT_DIR_) . $sitemap_link, 'w');

        fwrite($write_fd, '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\r\n");
        foreach ($link_sitemap as $file) {
            fwrite($write_fd, '<url>' . "\r\n");
            $lastmod = (isset($file['lastmod']) && !empty($file['lastmod'])) ? date('c', strtotime($file['lastmod'])) : null;
            $this->_addSitemapNode($write_fd, htmlspecialchars(strip_tags($file['link'])), $this->_getPriorityPage($file['page']), Configuration::get('GSITEMAP_FREQUENCY'), $lastmod);
            if ($file['image']) {
                $this->_addSitemapNodeImage(
                    $write_fd, htmlspecialchars(strip_tags($file['image']['link'])), isset($file['image']['title_img']) ? htmlspecialchars(
                        str_replace(
                            [
                                "\r\n",
                                "\r",
                                "\n",
                            ], '', strip_tags($file['image']['title_img'])
                        )
                    ) : '', isset($file['image']['caption']) ? htmlspecialchars(
                        str_replace(
                            [
                                "\r\n",
                                "\r",
                                "\n",
                            ], '', strip_tags($file['image']['caption'])
                        )
                    ) : ''
                );
            }
            fwrite($write_fd, '</url>' . "\r\n");
        }
        fwrite($write_fd, '</urlset>' . "\r\n");
        fclose($write_fd);
        $this->_saveSitemapLink($sitemap_link);
        ++$index;

        return true;
    }

    private function _getPriorityPage($page)
    {
        return Configuration::get('GSITEMAP_PRIORITY_' . Tools::strtoupper($page)) ? Configuration::get('GSITEMAP_PRIORITY_' . Tools::strtoupper($page)) : 0.1;
    }

    private function _addSitemapNode($fd, $loc, $priority, $change_freq, $last_mod = null)
    {
        fwrite($fd, '<loc>' . (Configuration::get('PS_REWRITING_SETTINGS') ? '<![CDATA[' . $loc . ']]>' : $loc) . '</loc>' . "\r\n" . '<priority>' . number_format($priority, 1, '.', '') . '</priority>' . "\r\n" . ($last_mod ? '<lastmod>' . date('c', strtotime($last_mod)) . '</lastmod>' : '') . "\r\n" . '<changefreq>' . $change_freq . '</changefreq>' . "\r\n");
    }

    private function _addSitemapNodeImage($fd, $link, $title, $caption)
    {
        fwrite($fd, '<image:image>' . "\r\n" . '<image:loc>' . (Configuration::get('PS_REWRITING_SETTINGS') ? '<![CDATA[' . $link . ']]>' : $link) . '</image:loc>' . "\r\n" . '<image:caption><![CDATA[' . $caption . ']]></image:caption>' . "\r\n" . '<image:title><![CDATA[' . $title . ']]></image:title>' . "\r\n" . '</image:image>' . "\r\n");
    }

    private function _createIndexSitemap()
    {
        $sitemaps = Db::getInstance()->ExecuteS('SELECT `link` FROM `' . _DB_PREFIX_ . 'gsitemap_sitemap` WHERE id_shop = ' . $this->context->shop->id);
        if (!$sitemaps) {
            return false;
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>';
        $xml_feed = new SimpleXMLElement($xml);

        foreach ($sitemaps as $link) {
            $sitemap = $xml_feed->addChild('sitemap');
            $sitemap->addChild('loc', 'http' . (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE') ? 's' : '') . '://' . Tools::getShopDomain(false, true) . __PS_BASE_URI__ . $link['link']);
            $sitemap->addChild('lastmod', date('c'));
        }
        file_put_contents($this->normalizeDirectory(_PS_ROOT_DIR_) . $this->context->shop->id . '_index_sitemap.xml', $xml_feed->asXML());

        return true;
    }

    private function tableColumnExists($table_name, $column = null)
    {
        if (array_key_exists($table_name, $this->sql_checks)) {
            if (!empty($column) && array_key_exists($column, $this->sql_checks[$table_name])) {
                return $this->sql_checks[$table_name][$column];
            } else {
                return $this->sql_checks[$table_name];
            }
        }

        $table = Db::getInstance()->ExecuteS('SHOW TABLES LIKE \'' . $table_name . '\'');
        if (empty($column)) {
            if (count($table) < 1) {
                return $this->sql_checks[$table_name] = false;
            } else {
                $this->sql_checks[$table_name] = true;
            }
        } else {
            $table = Db::getInstance()->ExecuteS('SELECT * FROM `' . $table_name . '` LIMIT 1');

            return $this->sql_checks[$table_name][$column] = array_key_exists($column, current($table));
        }

        return true;
    }

    public function _addLinkToSitemap(&$link_sitemap, $new_link, $lang, &$index, &$i)
    {
        if ($i <= 25000 && memory_get_usage() < 100000000) {
            $link_sitemap[] = $new_link;
            ++$i;

            return true;
        } else {
            $this->_recursiveSitemapCreator($link_sitemap, $lang, $index);
        }
    }
}
