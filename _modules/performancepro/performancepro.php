<?php
/**
 * This file is part of the performancepro package.
 *
 * @author Mathias Reker
 * @copyright Mathias Reker
 * @license Commercial Software License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use PrestaShop\Module\PerformancePro\domain\service\cache\HTTPCache;
use PrestaShop\Module\PerformancePro\domain\service\h2\ServerPush;
use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\domain\service\parser\FontsPreloader;
use PrestaShop\Module\PerformancePro\domain\service\parser\HTMLTweak;
use PrestaShop\Module\PerformancePro\domain\service\parser\LinkPreconnector;
use PrestaShop\Module\PerformancePro\domain\service\util\PathService;
use PrestaShop\Module\PerformancePro\install\Disabler;
use PrestaShop\Module\PerformancePro\install\Enabler;
use PrestaShop\Module\PerformancePro\install\Installer;
use PrestaShop\Module\PerformancePro\install\Uninstaller;
use PrestaShop\Module\PerformancePro\resources\config\Config;
use PrestaShop\Module\PerformancePro\web\util\HTTP;
use voku\helper\HtmlMin;

final class PerformancePro extends Module
{
    /**
     * @var bool
     */
    public $cron = false;

    public function __construct()
    {
        $this->name = 'performancepro';

        $this->tab = 'front_office_features';

        $this->version = '2.6.0';

        $this->author = 'Mathias R.';

        $this->need_instance = 0;

        $this->module_key = '1fe1a28d99b05d9ea0f3feb726f061dd';

        $this->bootstrap = true;

        parent::__construct();

        $this->autoLoad();

        $this->displayName = $this->l('Performance Pro');

        $this->description = $this->l('This module increases the overall performance of your website.');

        $this->ps_versions_compliancy = [
            'min' => '1.7.1',
            'max' => _PS_VERSION_,
        ];
    }

    /**
     * Autoload project files from /src directory.
     */
    public function autoLoad(): void
    {
        require_once $this->getLocalPath() . 'vendor/autoload.php';
    }

    /**
     * Alias method used by smarty. This method must be in the scope of the module main class.
     */
    public static function parseHtml(string $html): string
    {
        $configuration = [
            'noopener' => (bool)Configuration::get('PP_ADD_NOOPENER'),
            'loadScriptAsync' => (bool)Configuration::get('PP_LOAD_SCRIPT_ASYNC'),
            'minifySvg' => (bool)Configuration::get('PP_MINIFY_SVG'),
            'decodeImgAsync' => (bool)Configuration::get('PP_DECODE_IMG_ASYNC'),
            'lazyLoadImg' => (bool)Configuration::get('PP_LAZY_LOAD_IMG'),
            'lazyLoadIframe' => (bool)Configuration::get('PP_LAZY_LOAD_IFRAME'),
            'lazyLoadVideo' => (bool)Configuration::get('PP_LAZY_LOAD_VIDEO'),
            'lazyLoadAudio' => (bool)Configuration::get('PP_LAZY_LOAD_AUDIO'),
            'imgSizes' => (bool)Configuration::get('PP_IMG_SIZE'),
            'imgExtJpg' => (bool)Configuration::get('PP_CONVERT_TO_WEBP_JPEG'),
            'imgExtPng' => (bool)Configuration::get('PP_CONVERT_TO_WEBP_PNG'),
            'minifyHtml' => (bool)Configuration::get('PP_MINIFY_HTML'),
            'optimizeAttributes' => (bool)Configuration::get('PP_OPTIMIZE_ATTRIBUTES'),
        ];

        if (!in_array(true, $configuration, true)) {
            return $html;
        }

        $html = (new HTMLTweak())
            ->setHtml($html)
            ->doOptimizeNoopener($configuration['noopener'])
            ->doConvertJpgToWebp($configuration['imgExtJpg'])
            ->doConvertPngToWebp($configuration['imgExtPng'])
            ->setImgPath(PathService::createPath(Config::getImgCachePath()))
            ->setImgLink(Config::getImgCacheLink())
            ->doLoadScriptAsync($configuration['loadScriptAsync'])
            ->doMinifySvg($configuration['minifySvg'])
            ->doDecodeImgAsync($configuration['decodeImgAsync'])
            ->doLazyLoadImg($configuration['lazyLoadImg'])
            ->doLazyLoadIframe($configuration['lazyLoadIframe'])
            ->doLazyLoadAudio($configuration['lazyLoadAudio'])
            ->doLazyLoadVideo($configuration['lazyLoadVideo'])
            ->doAddImageSizes($configuration['imgSizes'])
            ->doMinifyJson(false)
            ->build()
            ->getHtml();

        return (new HtmlMin())
            ->doOptimizeViaHtmlDomParser(true)
            ->doRemoveComments($configuration['minifyHtml'])
            ->doSumUpWhitespace($configuration['minifyHtml'])
            ->doRemoveWhitespaceAroundTags($configuration['minifyHtml'])
            ->doOptimizeAttributes($configuration['optimizeAttributes'])
            ->doRemoveDeprecatedAnchorName($configuration['optimizeAttributes'])
            ->doRemoveDeprecatedTypeFromScriptTag($configuration['optimizeAttributes'])
            ->doRemoveEmptyAttributes($configuration['optimizeAttributes'])
            ->doRemoveValueFromEmptyInput($configuration['optimizeAttributes'])
            ->doRemoveDeprecatedScriptCharsetAttribute($configuration['optimizeAttributes'])
            ->doRemoveDefaultMediaTypeFromStyleAndLinkTag($configuration['optimizeAttributes'])
            ->doSortCssClassNames($configuration['optimizeAttributes'])
            ->doSortHtmlAttributes($configuration['optimizeAttributes'])
            ->doRemoveOmittedQuotes($configuration['optimizeAttributes'])
            ->doRemoveOmittedHtmlTags($configuration['optimizeAttributes'])
            ->doRemoveDeprecatedTypeFromStyleAndLinkTag(false)
            ->doRemoveDeprecatedTypeFromStylesheetLink(false)
            ->doRemoveDefaultAttributes(false)
            ->doRemoveHttpsPrefixFromAttributes(false)
            ->doRemoveHttpPrefixFromAttributes(false)
            ->doRemoveDefaultTypeFromButton(false)
            ->doRemoveSpacesBetweenTags(false)
            ->minify($html);
    }

    public function hookActionDispatcherBefore(): void
    {
        $this->autoLoad();
    }

    public function install(): bool
    {
        $this->setShopContextAll();

        try {
            if (!(new Installer($this))->execute() || !parent::install()) {
                $this->uninstall();

                return false;
            }
        } catch (Exception $exception) {
            LogService::error($exception->getMessage(), $exception->getTrace());

            return false;
        }

        return true;
    }

    private function setShopContextAll(): void
    {
        if (Shop::isFeatureActive()) {
            try {
                Shop::setContext(Shop::CONTEXT_ALL);
            } catch (PrestaShopException $prestaShopException) {
                LogService::error($prestaShopException->getMessage(), $prestaShopException->getTrace());
            }
        }
    }

    public function uninstall(): bool
    {
        $this->setShopContextAll();

        if (!parent::uninstall()) {
            return false;
        }

        return (new Uninstaller($this))->execute();
    }

    public function enable($force_all = true): bool
    {
        if (!parent::enable($force_all)) {
            return false;
        }

        return (new Enabler($this))->execute();
    }

    public function disable($force_all = true): bool
    {
        if (!parent::disable($force_all)) {
            return false;
        }

        return (new Disabler($this))->execute();
    }

    public function hookActionClearCache(): void
    {
        $this->clearServerCache();
    }

    /**
     * Clear the HTTP cache. Creates a new instance of the object if it does not already exist.
     */
    public function clearServerCache(): void
    {
        HTTPCache::getInstance()->clear();
    }

    public function hookActionClearCompileCache(): void
    {
        $this->clearServerCache();
    }

    /**
     * Protects the website from attacks in demo mode.
     */
    public function hookDisplayBackOfficeTop(): void
    {
        if (!Config::DEMO_MODE) {
            return;
        }

        if (Config::CONTROLLER_NAME !== $this->context->controller->controller_name) {
            $this->redirectToModuleAdminController();
        }
    }

    /**
     * Redirects the user to the admin front controller.
     */
    private function redirectToModuleAdminController(): void
    {
        $redirect = $this->context->link->getAdminLink(
            Config::CONTROLLER_NAME,
            true,
            false
        );

        Tools::redirectAdmin($redirect);
    }

    /**
     * Gets the content of the module page.
     */
    public function getContent(): void
    {
        $this->redirectToModuleAdminController();
    }

    public function hookHeader(): string
    {
        $this->h2ServerPush();

        $this->originAgentCluster();

        return $this->getPreloadTemplate() . $this->getHeaderTemplate();
    }

    private function h2ServerPush(): void
    {
        if (!Configuration::get('PP_CSS_HTTP2_PUSH')) {
            return;
        }

        if (!Configuration::get('PS_CSS_THEME_CACHE')) {
            return;
        }

        if (HTTP::isAjax()) {
            return;
        }

        if (null === $this->getStylesheetUri()) {
            return;
        }

        (new ServerPush($this->getStylesheetUri()))->pushCSS();
    }

    private function getStylesheetUri(): ?string
    {
        $styleSheets = $this->context->controller->getStylesheets();

        if (null === $styleSheets) {
            return null;
        }

        return $styleSheets['external']['theme-ccc']['uri'];
    }

    private function originAgentCluster(): void
    {
        if (Configuration::get('PP_ORIGIN_AGENT_CLUSTER')) {
            header('Origin-Agent-Cluster: ?1');
        }
    }

    public function getPreloadTemplate(): string
    {
        if (!Configuration::get('PP_INSTANT_LOAD_LINK')) {
            return '';
        }

        if (!in_array($_SERVER['HTTP_USER_AGENT'], ['CriOS', 'Chrome'], true)) {
            return '';
        }

        $ignoreKeywords = ['#', '?'];

        $controllers = [
            'address',
            'addresses',
            'authentication',
            'cart',
            'identity',
            'my-account',
            'order',
            'order-slip',
        ];

        foreach ($controllers as $controller) {
            $ignoreKeywords[] = '/' . basename($this->context->link->getPageLink($controller));
        }

        $params = [
            'pp_ignoreKeywords' => $ignoreKeywords,
        ];

        return $this->renderTemplate('flyingPages.tpl', $params);
    }

    private function renderTemplate(string $template, array $params = []): string
    {
        $device = 'd';

        if ($this->context->isMobile()) {
            $device = 'm';
        } elseif ($this->context->isTablet()) {
            $device = 't';
        }

        $id = sha1($this->name . $template . $this->context->language->id . $this->context->shop->id . $device);

        $cacheId = $this->getCacheId($id);

        if (!$this->isCached($template, $cacheId)) {
            $this->context->smarty->assign($params);
        }

        return $this->display(__FILE__, $template, $cacheId);
    }

    public function getHeaderTemplate(): string
    {
        $params = [];

        if (Configuration::get('PP_PRELOAD_FONTS')) {
            $params += [
                'pp_preload_links' => (new FontsPreloader())->getPreloadLinks(),
            ];
        } else {
            $params += [
                'pp_preload_links' => [],
            ];
        }

        if (Configuration::get('PP_PRECONNECT_LINKS')) {
            $params += [
                'pp_preconnect_links' => (new LinkPreconnector())->getPreconnectLinks(),
            ];
        } else {
            $params += [
                'pp_preconnect_links' => [],
            ];
        }

        if (!empty($params)) {
            return $this->renderTemplate('header.tpl', $params);
        }

        return '';
    }

    public function getSuccessTemplate(string $message): string
    {
        $params = [
            'pp_message' => $message,
        ];

        return $this->renderTemplate('success.tpl', $params);
    }

    public function getWarningTemplate(string $message): string
    {
        $params = [
            'pp_message' => $message,
        ];

        return $this->renderTemplate('warning.tpl', $params);
    }

    public function hookDisplayBeforeBodyClosingTag(): void
    {
        if (Configuration::get('PP_DISABLE_OPTIMIZATION_ORDER') && in_array(
            $this->context->controller->php_self,
            ['cart', 'order'],
            true
        )) {
            return;
        }

        $this->registerSmartyFilter();
    }

    public function registerSmartyFilter(): void
    {
        try {
            $this->context->smarty->registerFilter(
                'output',
                [
                    $this->name,
                    'parseHtml',
                ]
            );
        } catch (SmartyException $smartyException) {
            LogService::error($smartyException->getMessage(), $smartyException->getTrace());
        }
    }

    /**
     * Load the cache content.
     */
    public function hookActionDispatcher(array $params): void
    {
        if (!$this->isCacheActive()) {
            return;
        }

        if (!$this->isCacheablePage()) {
            return;
        }

        $key = sha1($_SERVER['REQUEST_URI'] . $params['cookie']->id_currency);

        $item = HTTPCache::getInstance()->getItem($key);

        // This make sure the PrestaShop cookie is set
        $this->context->cookie->write();

        if (null !== $item) {
            header('Cache-Control: no-store, no-cache, must-revalidate');

            header('Pragma: no-cache');

            header('Expires: 0');

            header('x-performance-pro-cache: hit');

            exit($item);
        }
        header('x-performance-pro-cache: miss');
    }

    /**
     * Check whether the cache should be used.
     */
    private function isCacheActive(): bool
    {
        if (!Configuration::get('PP_PAGE_CACHE')) {
            return false;
        }

        if ($this->isDevMode()) {
            return false;
        }

        if ($this->isLoggedIn()) {
            return false;
        }

        if ($this->hasProductsInCart()) {
            return false;
        }

        if (HTTP::isAjax()) {
            return false;
        }

        return HTTP::isGet();
    }

    private function isDevMode(): bool
    {
        return _PS_MODE_DEV_ || _PS_DEBUG_PROFILING_;
    }

    private function isLoggedIn(): bool
    {
        return ($this->context->customer instanceof Customer) && ($this->context->customer->id > 0);
    }

    private function hasProductsInCart(): bool
    {
        return (int)Cart::getNbProducts($this->context->cookie->id_cart) > 0;
    }

    private function isCacheablePage(): bool
    {
        if (is_subclass_of($this->context->controller, 'OrderController')) {
            return false;
        }

        if (is_subclass_of($this->context->controller, 'OrderOpcController')) {
            return false;
        }

        return in_array($this->context->controller->php_self, ['index', 'category', 'product', 'cms'], true);
    }

    /**
     * Saves the HTML to the cache if the clint is in static context.
     */
    public function hookActionOutputHTMLBefore(array $params): void
    {
        if (!$this->isCacheActive()) {
            return;
        }

        if (!$this->isCacheablePage()) {
            return;
        }

        $html = $params['html'];

        if (is_object($html)) {
            return;
        }

        if (empty($html)) {
            return;
        }

        $key = sha1($_SERVER['REQUEST_URI'] . $params['cookie']->id_currency);

        HTTPCache::getInstance()->save($html, $key);
    }

    public function hookActionCategoryAdd(): void
    {
        $this->clearServerCache();
    }

    public function hookActionCategoryDelete(): void
    {
        $this->clearServerCache();
    }

    public function hookActionCategoryUpdate(): void
    {
        $this->clearServerCache();
    }

    public function hookActionProductAdd(): void
    {
        $this->clearServerCache();
    }

    public function hookActionProductDelete(): void
    {
        $this->clearServerCache();
    }

    public function hookActionProductUpdate(): void
    {
        $this->clearServerCache();
    }

    public function hookActionAdminControllerSetMedia(): void
    {
        if (Config::CONTROLLER_NAME !== $this->context->controller->controller_name) {
            return;
        }

        $currentIndex = $this->context->link->getAdminLink(
            Config::CONTROLLER_NAME,
            true,
            false
        );

        if (!$this->active) {
            $error = $this->l('You must activate the module before running this command.');
        } else {
            $error = $this->l('An error occurred.');
        }

        Media::addJsDef([
            $this->name => [
                'moduleVersion' => $this->version,
                'cmsName' => Config::CMS_NAME,
                'cmsVersion' => _PS_VERSION_,
                'versionName' => $this->l('Version'),
                'currentIndex' => $currentIndex,
                'canceled' => $this->l('Canceled.'),
                'copy' => $this->l('Copied to clipboard.'),
                'copyError' => $this->l('Copy failed. Your browser does not allow copy.'),
                'error' => $error,
                'reset' => $this->l('Well done!'),
            ],
        ]);

        $this->context->controller->addJS([Config::getJsLink() . 'back.js', Config::getJsLink() . 'menu.js']);

        $this->context->controller->addCSS(Config::getCssLink() . 'back.css');
    }

    public function hookActionFrontControllerSetMedia(): void
    {
        $css = [];

        if (Configuration::get('PP_LAZY_LOAD_FOOTER')) {
            $css[] = Config::getCssLink() . 'lazy-load-footer.css';
        }

        if (Configuration::get('PP_IMG_SIZE')) {
            $css[] = Config::getCssLink() . 'resize-img.css';
        }

        if (!empty($css)) {
            $this->context->controller->addCSS($css);
        }

        $js = [];

        if (Configuration::get('PP_USE_PASSIVE_LISTENERS')) {
            $js[] = Config::getJsLink() . 'default-passive-events.js';
        }

        if (Configuration::get('PP_INSTANT_LOAD_LINK')) {
            $js[] = Config::getJsLink() . 'preload.js';
        }

        if (!empty($js)) {
            $this->context->controller->addJS($js);
        }
    }

    public function getContext(): ?Context
    {
        return $this->context;
    }
}
