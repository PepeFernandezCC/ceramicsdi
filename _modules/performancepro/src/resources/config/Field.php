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

namespace PrestaShop\Module\PerformancePro\resources\config;

use PrestaShop\Module\PerformancePro\domain\service\util\LinkService;
use Shop;

final class Field
{
    private function __construct()
    {
    }

    /**
     * @return array{PP_CONVERT_JPEG_TO_WEBP_QUALITY: int, PP_CONVERT_PNG_TO_WEBP_QUALITY: int, PP_LOG_EXCEPTIONS: true, PP_CACHE_WARMER_SITEMAPS: string}
     */
    public static function getPreconfiguredValues(): array
    {
        return [
            'PP_CONVERT_JPEG_TO_WEBP_QUALITY' => Config::DEFAULT_JPEG_TO_WEBP_QUALITY,
            'PP_CONVERT_PNG_TO_WEBP_QUALITY' => Config::DEFAULT_PNG_TO_WEBP_QUALITY,
            'PP_LOG_EXCEPTIONS' => true,
            'PP_CACHE_WARMER_SITEMAPS' => implode('|', self::getSitemapUrls()),
        ];
    }

    /**
     * Set values for the inputs. Define if they are multi-language.
     *
     * @return array{PP_DEBUG_PROFILING: true, PP_CART_TABLE_CLEANER: true, PP_CART_RULE_TABLE_CLEANER: true, PP_STATS_SEARCH_TABLE_CLEANER: true, PP_CONNECTION_TABLE_CLEANER: true, PP_PAGE_NOT_FOUND_TABLE_CLEANER: true, PP_LOG_TABLE_CLEANER: true, PP_MAIL_TABLE_CLEANER: true, PP_PRECONNECT_LINKS: true, PP_PRECONNECT_LINKS_TEXT: true, PP_PRELOAD_FONTS: true, PP_PRELOAD_FONTS_TEXT: true, PP_INSTANT_LOAD_LINK: true, PP_PAGE_CACHE: true, PP_MINIFY_HTML: true, PP_OPTIMIZE_ATTRIBUTES: true, PP_LAZY_LOAD_IMG: true, PP_LAZY_LOAD_IFRAME: true, PP_LAZY_LOAD_VIDEO: true, PP_LAZY_LOAD_FOOTER: true, PP_CONVERT_TO_WEBP_JPEG: true, PP_CONVERT_TO_WEBP_PNG: true, PP_CONVERT_JPEG_TO_WEBP_QUALITY: true, PP_CONVERT_PNG_TO_WEBP_QUALITY: true, PP_MINIFY_SVG: true, PP_IMG_SIZE: true, PP_LOAD_SCRIPT_ASYNC: true, PP_USE_PASSIVE_LISTENERS: true, PP_ADD_NOOPENER: true, PP_CSS_HTTP2_PUSH: true, PP_ORIGIN_AGENT_CLUSTER: true, PP_DECODE_IMG_ASYNC: true, PP_CLEAR_CART_TABLE: true, PP_HTACCESS_DEFLATE: true, PP_HTACCESS_CACHE_CONTROL: true, PP_LOG_EXCEPTIONS: true, PP_DISABLE_OPTIMIZATION_ORDER: true, PP_CACHE_WARMER_SITEMAPS: true}
     */
    public static function getFieldValues(): array
    {
        return [
            'PP_DEBUG_PROFILING' => false,
            'PP_CART_TABLE_CLEANER' => false,
            'PP_CART_RULE_TABLE_CLEANER' => false,
            'PP_STATS_SEARCH_TABLE_CLEANER' => false,
            'PP_CONNECTION_TABLE_CLEANER' => false,
            'PP_PAGE_NOT_FOUND_TABLE_CLEANER' => false,
            'PP_LOG_TABLE_CLEANER' => false,
            'PP_MAIL_TABLE_CLEANER' => false,
            'PP_PRECONNECT_LINKS' => false,
            'PP_PRECONNECT_LINKS_TEXT' => false,
            'PP_PRELOAD_FONTS' => false,
            'PP_PRELOAD_FONTS_TEXT' => false,
            'PP_INSTANT_LOAD_LINK' => false,
            'PP_PAGE_CACHE' => false,
            'PP_MINIFY_HTML' => false,
            'PP_OPTIMIZE_ATTRIBUTES' => false,
            'PP_LAZY_LOAD_IMG' => false,
            'PP_LAZY_LOAD_IFRAME' => false,
            'PP_LAZY_LOAD_VIDEO' => false,
            'PP_LAZY_LOAD_AUDIO' => false,
            'PP_LAZY_LOAD_FOOTER' => false,
            'PP_CONVERT_TO_WEBP_JPEG' => false,
            'PP_CONVERT_TO_WEBP_PNG' => false,
            'PP_CONVERT_JPEG_TO_WEBP_QUALITY' => false,
            'PP_CONVERT_PNG_TO_WEBP_QUALITY' => false,
            'PP_MINIFY_SVG' => false,
            'PP_IMG_SIZE' => false,
            'PP_LOAD_SCRIPT_ASYNC' => false,
            'PP_USE_PASSIVE_LISTENERS' => false,
            'PP_ADD_NOOPENER' => false,
            'PP_CSS_HTTP2_PUSH' => false,
            'PP_ORIGIN_AGENT_CLUSTER' => false,
            'PP_DECODE_IMG_ASYNC' => false,
            'PP_CLEAR_CART_TABLE' => false,
            'PP_HTACCESS_DEFLATE' => false,
            'PP_HTACCESS_CACHE_CONTROL' => false,
            'PP_LOG_EXCEPTIONS' => false,
            'PP_DISABLE_OPTIMIZATION_ORDER' => false,
            'PP_CACHE_WARMER_SITEMAPS' => false,
        ];
    }

    /**
     * @return string[]
     */
    private static function getSitemapUrls(): array
    {
        $shops = Shop::getShops(true, null, true);

        $result = [];

        foreach ($shops as $shop) {
            $file = $shop . '_index_sitemap.xml';

            if (file_exists(_PS_ROOT_DIR_ . \DIRECTORY_SEPARATOR . $file)) {
                $result[] = LinkService::getBaseLink() . $file;
            }
        }

        return $result;
    }
}
