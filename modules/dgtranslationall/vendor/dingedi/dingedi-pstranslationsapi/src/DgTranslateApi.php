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

use Dingedi\PsTranslationsApi\TranslationsProviders\AbstractTranslationProvider;

class DgTranslateApi
{
    /** @var string $REGEX_DEFAULT elements not to be translated */

    // TODO: improve by excluding elements in html tags
    private static $REGEX_DEFAULT = '/(%[0-9a-zA-Z\_\-]{1,}\$d)|(%[0-9a-zA-Z\_\-]{1,}(?:%)?)|(\[?\(?\/?{[^}]+}\]?\)?)|(\$[a-zA-Z\_]+)|(\[\/?[0-9a-zA-Z\ ]+\])|(\[.+?\])/m';
    /**
     * @var string
     */
    private static $REGEX_URLS = "/(?:(?:http(?:s)?:\/\/)?(?:[\w-]+\.)+[\w-]+[.com]+(?:[\w\-\.,@?^=%&amp;:\/~\+#]*[\w\-\@?^=%&amp;\/~\+#])?)/";

    /**
     * @return string translated string
     * @throws \Exception
     * @param string $text
     * @param string $isoLangFrom
     * @param string $isoLangTo
     * @param int $latin
     */
    public static function translate($text, $isoLangFrom, $isoLangTo, $latin)
    {
        $text = str_replace("\r\n", "\n",$text);

        if (in_array($isoLangFrom, array('en', 'gb')) && in_array($isoLangTo, array('en', 'gb'))) {
            return $text;
        }

        if ($isoLangFrom === $isoLangTo) {
            return $text;
        }

        @set_time_limit(0);
        @ini_set('max_execution_time', 0);

        $text = html_entity_decode($text, ENT_QUOTES | ENT_COMPAT, 'UTF-8');

        if (in_array($latin, [1, 3], true)) {
            $text = \Dingedi\PsTranslationsApi\Tools\Transliterations\DgTransliterator::transliterate($isoLangFrom, $text, false);
        }

        if (\Dingedi\PsTranslationsApi\TranslationRequest::isCopyTextOnly()) {
            $translated = $text;
        } else {
            $translated = self::translateText($isoLangFrom, $isoLangTo, $text);
        }

        if (in_array($latin, [2, 3], true)) {
            $translated = \Dingedi\PsTranslationsApi\Tools\Transliterations\DgTransliterator::transliterate($isoLangTo, $translated);
        }

        return $translated;
    }

    /**
     * @param string $isoLangFrom
     * @param string $isoLangTo
     * @param string $text
     * @return string
     */
    private static function translateText($isoLangFrom, $isoLangTo, $text)
    {
        $isoLangFrom = (string) $isoLangFrom;
        $isoLangTo = (string) $isoLangTo;
        $text = (string) $text;
        $provider = null;
        if (\Dingedi\PsTranslationsApi\TranslationRequest::isRegenerateLinksOnly() === false) {
            $isHTML = self::isHTML($text);

            /** @var $provider AbstractTranslationProvider */
            $provider = \Dingedi\PsTranslationsApi\DgTranslationTools::getProvider(false);
            $provider->iso_from = $isoLangFrom;
            $provider->iso_to = $isoLangTo;

            $text = self::fixUrlsEncoded($text);

            $exclusions = array();

            // Text
            if (!$isHTML) {
                $_POST['translation_data']['content_type'] = "text";
                $exclusions = array_merge($exclusions, self::getExclusionForUnexceptedElements($text));

                $text = $provider->excludeWords($text, true, $exclusions);
            } else {
                // Html
                $_POST['translation_data']['content_type'] = "html";

                $parser = new \Dingedi\PsTranslationsApi\Html\DgHTMLParser($text);

                foreach ($parser->getTextNodes() as $node) {
                    $exclusions = array_merge($exclusions, self::getExclusionForUnexceptedElements($node->nodeValue));
                    $node->nodeValue = $provider->excludeWords($node->nodeValue, true, $exclusions);
                }

                $provider->addTemporaryExcludedWords($exclusions);

                $text = self::fixHtmlContentBeforeTranslate($parser->getHTMLOutput(), $provider);
            }

            if (\Tools::strlen($text) > ($provider->max_chars_per_request * 0.95)) {
                $splitted = Html\HTMLSplitter::split($text, ($provider->max_chars_per_request * 0.95));

                $translated = '';

                if (is_array($splitted)) {
                    foreach ($splitted as $part) {
                        $translated .= html_entity_decode((string)$provider->translate($part, $isoLangFrom, $isoLangTo), ENT_QUOTES | ENT_COMPAT, 'UTF-8');
                    }
                } else {
                    // text was not well splitted :(
                    $parser = new \Dingedi\PsTranslationsApi\Html\DgHTMLParser($text);

                    foreach ($parser->getTextNodes() as $node) {
                        $charsFix = [',', '.', '?', '!', ';'];
                        $start = '';
                        $end = '';

                        foreach ($charsFix as $char) {
                            $arr = [
                                ' ' . $char . ' ',
                                ' ' . $char,
                                $char . ' '
                            ];

                            foreach ($arr as $k) {
                                if (strncmp((string)$node->nodeValue, $k, strlen($k)) === 0) {
                                    $start = $k;
                                }

                                if (substr_compare((string)$node->nodeValue, $k, -strlen($k)) === 0) {
                                    $end = $k;
                                }
                            }
                        }

                        $spaceStart = strlen((string)$node->nodeValue) - strlen(ltrim((string)$node->nodeValue));
                        $spaceEnd = strlen((string)$node->nodeValue) - strlen(rtrim((string)$node->nodeValue));

                        $firstCharUppercase = $node->nodeValue === ucfirst((string)$node->nodeValue);

                        $exclusions = array_merge($exclusions, self::getExclusionForUnexceptedElements($node->nodeValue));

                        $provider->addTemporaryExcludedWords($exclusions);

                        $node->nodeValue = $provider->excludeWords($node->nodeValue, true, $exclusions);
                        $node->nodeValue = html_entity_decode((string)$provider->translate($node->nodeValue, $isoLangFrom, $isoLangTo), ENT_QUOTES | ENT_COMPAT, 'UTF-8');

                        $node->nodeValue = str_repeat(' ', $spaceStart) . trim($node->nodeValue) . str_repeat(' ', $spaceEnd);
                        $node->nodeValue = $start . ltrim(rtrim($node->nodeValue, $end), $start) . $end;

                        if ($firstCharUppercase) {
                            $node->nodeValue = ucfirst($node->nodeValue);
                        }
                    }

                    $translated = $parser->getHTMLOutput();
                }

                $text = $translated;
            } else {
                $text = html_entity_decode((string)$provider->translate($text, $isoLangFrom, $isoLangTo), ENT_QUOTES | ENT_COMPAT, 'UTF-8');
            }

            if ($isHTML) {
                $text = self::fixHtmlContentAfterTranslate($text, $provider);
            }

            $text = $provider->excludeWords($text, false, $exclusions);
        }

        // Translate shop urls
        return \Dingedi\PsTranslationsApi\DgUrlTranslation::translateContentUrls($text, self::getCorrectLanguageId($isoLangTo, $provider));
    }

    /**
     * @param string $text
     * @return mixed[]
     */
    private static function getExclusionForUnexceptedElements($text)
    {
        $text = (string) $text;
        preg_match_all(self::$REGEX_DEFAULT, $text, $matches, PREG_SET_ORDER, 0);
        preg_match_all(self::$REGEX_URLS, $text, $matchesUrls, PREG_SET_ORDER, 0);

        $tempExcluded = array();

        if (!empty($matches)) {
            $tempExcluded = array_unique(call_user_func_array('array_merge', $matches));
        }

        if (!empty($matchesUrls)) {
            $tempExcluded = array_merge($tempExcluded, array_unique(call_user_func_array('array_merge', $matchesUrls)));
        }

        return array_map(function ($e) {
            return trim((string)$e);
        }, array_filter($tempExcluded));
    }

    /**
     * @param string $text
     * @return string
     */
    private static function fixUrlsEncoded($text)
    {
        $text = (string) $text;
        return preg_replace_callback(self::$REGEX_URLS, function ($elem) {
            return urldecode((string)$elem[0]);
        }, $text);
    }

    /**
     * @param string $text
     * @return string
     */
    private static function fixHtmlContentBeforeTranslate($text, AbstractTranslationProvider $provider)
    {
        $text = (string) $text;
        $replaces = array(
            "%7B" => "{",
            "%7D" => "}",
            "%24" => "$",
            "<title>" => "<data-title>",
            "</title>" => "</data-title>",
            "<p>\xc2\xa0</p>" => $provider->getExcludedWordWrapped("<p>\xc2\xa0</p>")
        );


        return str_replace(array_keys($replaces), array_values($replaces), $text);
    }

    /**
     * @param string $text
     * @return string
     */
    private static function fixHtmlContentAfterTranslate($text, AbstractTranslationProvider $provider)
    {
        $text = (string) $text;
        $replaces = array(
            "<data-title>" => "<title>",
            "</data-title>" => "</title>",
            $provider->getExcludedWordWrapped("<p>\xc2\xa0</p>") => "<p>\xc2\xa0</p>",
            $provider->getExcludedWordWrapped("<p> </p>") => "<p> </p>"
        );

        return str_replace(array_keys($replaces), array_values($replaces), $text);
    }

    /**
     * @param string $text
     * @return bool
     */
    private static function isHTML($text)
    {
        $text = (string) $text;
        return $text !== strip_tags($text);
    }

    /**
     * Reverse iso replacement
     * @param string $isoLangTo
     * @param \Dingedi\PsTranslationsApi\TranslationsProviders\AbstractTranslationProvider $provider
     * @return int
     */
    public static function getCorrectLanguageId($isoLangTo, $provider)
    {
        $id_lang = \Language::getIdByIso($isoLangTo);
        if ($id_lang === false) {
            $search = array_search($isoLangTo, array_values($provider->iso_replacements));

            if ($search !== false) {
                $id_lang = \Language::getIdByIso(array_keys($provider->iso_replacements)[$search]);
            }
        }

        return (int)$id_lang;
    }
}
