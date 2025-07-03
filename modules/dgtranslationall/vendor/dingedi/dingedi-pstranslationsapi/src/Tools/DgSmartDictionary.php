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

namespace Dingedi\PsTranslationsApi\Tools;

use Dingedi\PsTranslationsApi\TranslationsProviders\AbstractTranslationProvider;

class DgSmartDictionary
{
    public static $smart_dictionary = null;

    /**
     * @param int $id_lang_from
     * @param int $id_lang_to
     * @return void
     */
    public static function init($id_lang_from, $id_lang_to)
    {
        $smartDictionary = \Dingedi\PsTranslationsApi\DgTranslationTools::getSmartDictionary();

        $smartDictionary = array_filter($smartDictionary, function ($elem) use ($id_lang_from, $id_lang_to) {
            return (int)$elem["from"] === $id_lang_from && isset($elem[$id_lang_to]);
        });

        $smartDictionary = array_map(function ($elem) use ($id_lang_from, $id_lang_to) {
            $arr = array(
                $elem[$id_lang_from] => $elem[$id_lang_to]
            );

            if (!isset($elem['caseSensitive'])) {
                $arr['caseSensitive'] = true;
            } else {
                $arr['caseSensitive'] = $elem['caseSensitive'] === "true";
            }

            return $arr;
        }, $smartDictionary);

        self::$smart_dictionary = $smartDictionary;
    }

    /**
     * First array: words sensitive
     * Second array: words insensitive
     * @return array[]
     */
    public static function getExclusions()
    {
        $smartDictionary = self::$smart_dictionary;

        $wordsSensitive = array();
        $wordsInsensitive = array();

        foreach ($smartDictionary as $k) {
            $elem = array_keys($k)[0];

            if ($k['caseSensitive'] === true) {
                $wordsSensitive[] = $elem;
            } else {
                $wordsInsensitive[] = $elem;
            }
        }

        return [$wordsSensitive, $wordsInsensitive];
    }

    /**
     * @param string $word
     * @param bool $exact
     * @return bool
     */
    public static function isInsensitive($word, $exact = false)
    {
        list($caseSensitiveExclusions, $caseInsensitiveExclusions) = self::getExclusions();

        foreach ($caseInsensitiveExclusions as $w) {
            if ($w === $word) {
                return true;
            }

            if (!$exact) {
                preg_match('/' . AbstractTranslationProvider::preg_quote($w) . '/mi', $word, $matches);

                if (!empty($matches) && isset($matches[0])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return string|false
     * @param string $word
     */
    public static function getReplacement($word)
    {
        $words = self::$smart_dictionary;

        foreach ($words as $elem) {
            if (array_keys($elem)[0] === $word) {
                return array_values($elem)[0];
            }
        }

        return false;
    }
}
