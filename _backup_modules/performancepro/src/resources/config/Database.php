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

final class Database
{
    private function __construct()
    {
    }

    /**
     * @return array<string, string>
     */
    public static function getRecommendedDatabaseVersions(): array
    {
        return [
            '1.7.4.0' => '7.1',
            '1.7.6.0' => '7.2',
            '1.7.7.0' => '7.3',
            '1.7.8.0' => '7.4',
            '8.0.0' => '8.1',
        ];
    }

    /**
     * @return array{query_cache_limit: string, query_cache_size: string, table_open_cache: int, thread_cache_size: int, host_cache_size: int, read_buffer_size: string, read_rnd_buffer_size: string, join_buffer_size: string, sort_buffer_size: string, innodb_buffer_pool_size: string, max_heap_table_size: string, tmp_table_size: string, key_buffer_size: string, max_allowed_packet: int, myisam_sort_buffer_size: string, innodb_flush_log_at_trx_commit: int}
     */
    public static function getDatabaseSettings(): array
    {
        return [
            'query_cache_limit' => '128K',
            'query_cache_size' => '32M',
            'table_open_cache' => 4000,
            'thread_cache_size' => 80,
            'host_cache_size' => 1000,
            'read_buffer_size' => '2M',
            'read_rnd_buffer_size' => '1M',
            'join_buffer_size' => '2M',
            'sort_buffer_size' => '2M',
            'innodb_buffer_pool_size' => '1G',
            'max_heap_table_size' => '32M',
            'tmp_table_size' => '32M',
            'key_buffer_size' => '256M',
            'max_allowed_packet' => 268435456,
            'myisam_sort_buffer_size' => '64M',
            'innodb_flush_log_at_trx_commit' => 0,
        ];
    }

    /**
     * @return array{PS_SSL_ENABLED: string, PS_SSL_ENABLED_EVERYWHERE: string, PS_SMARTY_CACHE: string, PS_SMARTY_LOCAL: string, PS_SMARTY_CACHING_TYPE: string, PS_SMARTY_CLEAR_CACHE: string, PS_CSS_THEME_CACHE: string, PS_JS_THEME_CACHE: string, PS_HTACCESS_CACHE_CONTROL: string}
     */
    public static function getSystemSettings(): array
    {
        return [
            'PS_SSL_ENABLED' => '1',
            'PS_SSL_ENABLED_EVERYWHERE' => '1',
            'PS_SMARTY_CACHE' => '1',
            'PS_SMARTY_LOCAL' => '0',
            'PS_SMARTY_CACHING_TYPE' => 'filesystem',
            'PS_SMARTY_CLEAR_CACHE' => 'never',
            'PS_CSS_THEME_CACHE' => '1',
            'PS_JS_THEME_CACHE' => '1',
            'PS_HTACCESS_CACHE_CONTROL' => '1',
        ];
    }

    /**
     * @return array{_PS_MODE_DEV_: string, _PS_DEBUG_PROFILING_: string}
     */
    public static function getConfigValues(): array
    {
        return [
            '_PS_MODE_DEV_' => 'false',
            '_PS_DEBUG_PROFILING_' => 'false',
        ];
    }
}
