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

namespace Dingedi\PsTranslationsApi;

class TranslationsCache
{
    /**
     * @var string
     */
    private $key;
    /**
     * @var mixed[]
     */
    private $cache = [];

    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public static function getDirectory()
    {
        return dirname(__DIR__) . '/cache';
    }

    /**
     * @return void
     */
    public static function clearCache()
    {
        $cache_dir = self::getDirectory();

        $modules = ['dgcontenttranslation', 'dgtranslationall', 'dgcreativeelementstranslation'];

        foreach ($modules as $module) {
            for ($i = 0; $i < count($modules); $i++) {
                $dir = str_replace($module, $modules[$i], $cache_dir);

                if (file_exists($dir)) {
                    \Tools::deleteDirectory($dir, false);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getFilePath($isoKey)
    {
        return self::getDirectory() . '/cache_' . $this->key . '_' . $isoKey . '.json';
    }

    /**
     * @return mixed[]
     */
    private function getDefaultCache()
    {
        return ['CREATED' => time()];
    }

    /**
     * @param string $isoKey
     * @return mixed[]
     */
    private function getCacheContent($isoKey)
    {
        $isoKey = (string) $isoKey;
        if (isset($this->cache[$isoKey])) {
            return $this->cache[$isoKey];
        }

        $path = $this->getFilePath($isoKey);

        if (!file_exists($path)) {
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path));
            }

            file_put_contents($path, json_encode($this->getDefaultCache()));
        }

        $cache = file_get_contents($path);

        try {
            $cache = json_decode($cache, JSON_OBJECT_AS_ARRAY === null ?: JSON_OBJECT_AS_ARRAY);
        } catch (\Exception $e) {
            $cache = null;
        }

        if ($cache === null) {
            $cache = [];
            $this->write($cache, $isoKey);
        }

        $this->cache[$isoKey] = $cache;

        return $cache;
    }

    /**
     * @return void
     * @param string $isoKey
     */
    private function write(array $data, $isoKey)
    {
        $isoKey = (string) $isoKey;
        $reset = filesize($this->getFilePath($isoKey)) > 8388608 || count($data) > 5000;

        if (empty($data)) {
            $cache = [];
        } else {
            $cache = $this->getCacheContent($isoKey);
        }

        if (isset($cache['CREATED'])) {
            $reset = $reset || ($cache['CREATED'] < (time() - (24 * 60 * 60))); // h * m * s
        } else {
            $reset = true;
        }

        if ($reset) {
            $data = $this->getDefaultCache();
        }

        file_put_contents($this->getFilePath($isoKey), json_encode($data));

        $this->cache[$isoKey] = $data;
    }

    /**
     * @return bool|string
     * @param string $text
     * @param string $isoKey
     */
    public function getCachedText($text, $isoKey)
    {
        $cache = $this->getCacheContent($isoKey);
        $key = md5($text);

        if (isset($cache[$key])) {
            return base64_decode($cache[$key]);
        }

        return false;
    }

    /**
     * @param string $translated
     * @param string $original
     * @param string $isoKey
     * @return void
     */
    public function addCache($translated, $original, $isoKey)
    {
        if ($this->getCachedText($original, $isoKey) === false) {
            $cache = $this->getCacheContent($isoKey);

            $cache[md5($original)] = base64_encode($translated);

            $this->write($cache, $isoKey);
        }
    }
}
