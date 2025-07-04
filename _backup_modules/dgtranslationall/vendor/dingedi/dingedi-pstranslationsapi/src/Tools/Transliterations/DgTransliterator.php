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

namespace Dingedi\PsTranslationsApi\Tools\Transliterations;

class DgTransliterator
{
    /**
     * @var mixed[]
     */
    static $supportedLanguages = array('ru', 'sr', 'mk', 'ro');

    /**
     * If $toLatin is true, source text is in cyrillic
     * If $toLatin is false, source text is in latin
     *
     * @param string $text text to transliterate
     * @throws \Exception
     * @param string $isoLang
     * @param bool $toLatin
     * @return string
     */
    static function transliterate($isoLang, $text, $toLatin = true)
    {
        try {
            if (!in_array($isoLang, self::$supportedLanguages)) {
                return $text;
            }

            $resultWithIntl = self::withIntl($text, $isoLang);
            if ($toLatin && is_string($resultWithIntl) && $resultWithIntl !== $text) {
                return $resultWithIntl;
            }

            require_once $isoLang . '.php';
            $replace = dg_get_replacements();

            if (!$toLatin) {
                $replace = array_flip($replace);
            }

            if ($isoLang === 'ro') {
                return self::mb_strtr($text, $replace);
            }

            return strtr($text, $replace);
        } catch (\Exception $e) {
            throw new \Exception('Transliteration error: ' . $e->getMessage());
        }
    }

    static function mb_strtr($text, $array)
    {
        $result = '';

        foreach (mb_str_split($text) as $char) {
            $result .= isset($array[$char]) ? $array[$char] : $char;
        }

        return $result;
    }

    /**
     * @return bool|string
     * @param string $text
     * @param string $iso
     */
    static function withIntl($text, $iso)
    {
        if (!in_array($iso, ['ru', 'sr', 'mk'])) {
            return false;
        }

        if (class_exists("Transliterator")) {
            $transliterator = \Transliterator::create("Any-Latin;Latin-ASCII;");

            if (($transliterator instanceof \Transliterator)) {
                $result = $transliterator->transliterate($text);

                if (is_string($result)) {
                    return $result;
                }
            }
        }

        return false;
    }
}
