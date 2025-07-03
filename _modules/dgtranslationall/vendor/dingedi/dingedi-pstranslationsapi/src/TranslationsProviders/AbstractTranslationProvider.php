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
 * @copyright Copyright 2023 © Dingedi All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 * @category  Dingedi PrestaShop Modules
 */

namespace Dingedi\PsTranslationsApi\TranslationsProviders;

use Dingedi\PsTools\DgConfiguration;
use Dingedi\PsTools\DgTools;
use Dingedi\PsTranslationsApi\Exception\ApiCurlErrorException;
use Dingedi\PsTranslationsApi\Exception\ApiErrorException;
use Dingedi\PsTranslationsApi\Exception\NotSupportedLanguageException;
use Dingedi\PsTranslationsApi\Tools\DgSmartDictionary;
use Dingedi\PsTranslationsApi\TranslationsCache;

abstract class AbstractTranslationProvider implements \JsonSerializable
{
    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $title;

    /**
     * @var mixed
     */
    public $api_key;

    /**
     * @var mixed[]
     */
    public $excluded_words = array();

    /**
     * @var mixed[]
     */
    public $iso = array();

    /**
     * @var mixed[]
     */
    public $iso_replacements = array();

    /**
     * @var mixed[]
     */
    public $iso_replacements_from = array();

    /**
     * @var mixed[]
     */
    public $excluded_words_wrappers = array();

    /**
     * @var mixed[]
     */
    public $errors = array();

    /**
     * @var string
     */
    public $api_version;

    /**
     * @var int
     */
    public $max_chars_per_request = 5000;

    /**
     * @var mixed[]
     */
    public $informations;

    /**
     * @var string
     */
    public $iso_from;

    /**
     * @var string
     */
    public $iso_to;

    /**
     * @var mixed[]
     */
    public $languages = array();

    /**
     * @var \Dingedi\PsTranslationsApi\TranslationsCache
     */
    public $cache;

    /**
     * @var \Dingedi\PsTools\DgConfiguration
     */
    public $configuration;

    public function __construct()
    {
        $this->cache = new TranslationsCache($this->key);
        $this->api_key = \Dingedi\PsTranslationsApi\DgTranslationTools::getApiKey($this->key);

        if (\Configuration::get('dingedi_exclude') === 'true') {
            $this->excluded_words = array_filter(explode(',', (string)\Configuration::get('dingedi_excluded')));
        }

        $this->iso_replacements = array(
            'ie' => 'ga', // Irish
            'at' => 'de', // Autrish language is German
            'gb' => 'en',
            'vn' => 'vi',
            'si' => 'sl',
            'nn' => 'no',
            'qc' => 'fr',
            'mx' => 'es',
            'br' => 'pt',
            'tw' => 'zh',
            'dk' => 'da'
        );


        $this->languages = array(
            "af" => "Afrikaans",
            "sq" => "Albanian",
            "am" => "Amharic",
            "ar" => "Arabic",
            "hy" => "Armenian",
            "az" => "Azerbaijani",
            "eu" => "Basque",
            "be" => "Belarusian",
            "bn" => "Bengali",
            "bs" => "Bosnian",
            "bg" => "Bulgarian",
            "ca" => "Catalan",
            "ceb" => "Cebuano",
            "zh" => "Chinese",
            "zh-CN" => "Chinese (Simplified)",
            "zh-TW" => "Chinese (Traditional)",
            "co" => "Corsican",
            "hr" => "Croatian",
            "cs" => "Czech",
            "da" => "Danish",
            "nl" => "Dutch",
            "en" => "English",
            "gb" => "English",
            "eo" => "Esperanto",
            "et" => "Estonian",
            "fi" => "Finnish",
            "fr" => "French",
            "fy" => "Frisian",
            "gl" => "Galician",
            "ka" => "Georgian",
            "de" => "German",
            "el" => "Greek",
            "gu" => "Gujarati",
            "ht" => "Haitian Creole",
            "ha" => "Hausa",
            "haw" => "Hawaiian",
            "he" => "Hebrew",
            "hi" => "Hindi",
            "hmn" => "Hmong",
            "hu" => "Hungarian",
            "is" => "Icelandic",
            "ig" => "Igbo",
            "id" => "Indonesian",
            "ga" => "Irish",
            "it" => "Italian",
            "ja" => "Japanese",
            "jv" => "Javanese",
            "kn" => "Kannada",
            "kk" => "Kazakh",
            "km" => "Khmer",
            "rw" => "Kinyarwanda",
            "ko" => "Korean",
            "ku" => "Kurdish",
            "ky" => "Kyrgyz",
            "lo" => "Lao",
            "la" => "Latin",
            "lv" => "Latvian",
            "lt" => "Lithuanian",
            "lb" => "Luxembourgish",
            "mk" => "Macedonian",
            "mg" => "Malagasy",
            "ms" => "Malay",
            "ml" => "Malayalam",
            "mt" => "Maltese",
            "mi" => "Maori",
            "mr" => "Marathi",
            "mn" => "Mongolian",
            "my" => "Myanmar (Burmese)",
            "ne" => "Nepali",
            "no" => "Norwegian",
            "ny" => "Nyanja (Chichewa)",
            "or" => "Odia (Oriya)",
            "ps" => "Pashto",
            "fa" => "Persian",
            "pl" => "Polish",
            "pt" => "Portuguese",
            "pt-br" => "Portuguese (Brazil)",
            "pt-pt" => "Portuguese (Portugal)",
            "pa" => "Punjabi",
            "ro" => "Romanian",
            "ru" => "Russian",
            "sm" => "Samoan",
            "gd" => "Scots Gaelic",
            "sr" => "Serbian",
            "st" => "Sesotho",
            "sn" => "Shona",
            "sd" => "Sindhi",
            "si" => "Sinhala (Sinhalese)",
            "sk" => "Slovak",
            "sl" => "Slovenian",
            "so" => "Somali",
            "es" => "Spanish",
            "su" => "Sundanese",
            "sw" => "Swahili",
            "sv" => "Swedish",
            "tl" => "Tagalog (Filipino)",
            "tg" => "Tajik",
            "ta" => "Tamil",
            "tt" => "Tatar",
            "te" => "Telugu",
            "th" => "Thai",
            "tr" => "Turkish",
            "tk" => "Turkmen",
            "uk" => "Ukrainian",
            "ur" => "Urdu",
            "ug" => "Uyghur",
            "uz" => "Uzbek",
            "vi" => "Vietnamese",
            "cy" => "Welsh",
            "xh" => "Xhosa",
            "yi" => "Yiddish",
            "yo" => "Yoruba",
            "zu" => "Zulu",
            "mx" => "Mexican",
        );
    }

    /**
     * @throws NotSupportedLanguageException
     * @param string $text
     * @param string $isoFrom
     * @param string $isoTo
     */
    public function translate($text, $isoFrom, $isoTo)
    {
        $trimmed = trim(trim($text, "\t\n\r\0\x0B\xc2\xa0"));
        if (in_array($trimmed, ["", ",", ".", "!", "?", ";", "-"])) {
            return $text;
        }

        $this->canTranslate($isoFrom, $isoTo);

        $cachedText = $this->cache->getCachedText($text, $isoFrom . $isoTo);

        if ($cachedText !== false) {
            return $this->formatResponse($cachedText);
        }

        if ($this->max_chars_per_request !== -1 && \Tools::strlen($text) > $this->max_chars_per_request) {
            return $text;
        }

        if ($this->isOnlyExcludedWords($text)) {
            return $this->formatResponse($text);
        }
    }

    /**
     * @param mixed[] $excludedWords
     * @return void
     */
    public function addTemporaryExcludedWords($excludedWords)
    {
        if (!empty($excludedWords)) {
            $this->excluded_words = array_unique(array_merge($this->excluded_words, $excludedWords));
        }
    }

    /**
     * @param string $text
     * @return bool
     */
    private function isOnlyExcludedWords($text)
    {
        $text = (string) $text;
        if ($this->isText()) {
            $text = $this->excludeWords($text, true);
        }

        $excluded = $this->excludeWords($text, false, null, true);

        if (trim($excluded) === "") {
            return true;
        }

        return false;
    }

    /**
     * @param string $text
     * @return string
     */
    public function unexcludeWords($text)
    {
        return $this->excludeWords($text, false);
    }

    /**
     * @param string $string
     * @return string
     */
    public static function preg_quote($string)
    {
        $string = preg_quote($string, '/');

        if (\Tools::version_compare(PHP_VERSION, '7.3', '<')) {
            $string = str_replace('#', '\#', $string);
        }

        return $string;
    }

    /**
     * @param string $text
     * @return string
     */
    public function getExcludedWordWrapped($text)
    {
        return $this->excluded_words_wrappers[0] . $text . $this->excluded_words_wrappers[1];
    }

    /**
     * @param string $text
     * @param bool $replace
     * @param mixed[]|null $excludedWords
     * @param bool $replaceByEmptyValue
     * @return string
     */
    public function excludeWords($text, $replace, $excludedWords = null, $replaceByEmptyValue = false)
    {
        \Dingedi\PsTranslationsApi\Tools\DgSmartDictionary::init(\Dingedi\PsTranslationsApi\DgTranslateApi::getCorrectLanguageId($this->iso_from, $this), \Dingedi\PsTranslationsApi\DgTranslateApi::getCorrectLanguageId($this->iso_to, $this));

        $excluded = array_merge($this->excluded_words, ...DgSmartDictionary::getExclusions());

        if (is_array($excludedWords)) {
            $excluded = array_unique(array_merge($excluded, $excludedWords));

            $this->excluded_words = $excluded;
        }

        if (empty($excluded)) {
            return $text;
        }

        if ($replace === true) {
            $text = $this->replaceExcludedWords($text, $excluded);
        } else {
            $text = $this->unreplaceExcludedWords($text, $excluded, $replaceByEmptyValue);
        }

        return $text;
    }

    /**
     * @param string $text
     * @param bool $replaceByEmptyValue
     * @return string
     */
    private function unreplaceExcludedWords($text, array $excluded, $replaceByEmptyValue = false)
    {
        $text = (string) $text;
        $replaceByEmptyValue = (bool) $replaceByEmptyValue;
        if (strpos($text, $this->excluded_words_wrappers[0]) === false) {
            return $text;
        }

        $excluded = array_unique($excluded);

        usort($excluded, function ($a, $b) {
            return strlen((string)$a) > strlen((string)$b);
        });

        foreach ($excluded as $excluded_word) {
            $smartDictionaryReplacement = DgSmartDictionary::getReplacement($excluded_word);

            $replacement = $excluded_word;

            if ($smartDictionaryReplacement !== false) {
                $replacement = $smartDictionaryReplacement;
            }

            if ($replaceByEmptyValue === true) {
                $replacement = '';
            }

            $re = $this->getPattern([$excluded_word], true);

            $textReplaced = preg_replace_callback($re, function ($ma) use ($replacement) {
                $word_unwrapped = strip_tags($ma[0]);

                if ($replacement !== '' && DgSmartDictionary::isInsensitive($word_unwrapped)) {
                    if ($word_unwrapped === ucfirst($word_unwrapped)) {
                        $replacement = ucfirst($replacement);
                    } else if ($word_unwrapped === lcfirst($word_unwrapped)) {
                        $replacement = lcfirst($replacement);
                    } else if ($word_unwrapped === strtoupper($word_unwrapped)) {
                        $replacement = strtoupper($replacement);
                    } else if ($word_unwrapped === strtolower($word_unwrapped)) {
                        $replacement = strtolower($replacement);
                    }
                }

                return $replacement;
            }, $text);

            if ($textReplaced !== null) {
                $text = $textReplaced;
            }
        }

        return $text;
    }


    /**
     * @param string $word
     * @return string
     */
    private function getWrappedWord($word)
    {
        $word = (string) $word;
        $wrapped = $this->getExcludedWordWrapped("A");
        $wrapped = $this->preg_quote($wrapped);
        $wrapped = str_replace('A', $word, $wrapped);

        return $wrapped;
    }

    /**
     * @param string $text
     * @return string
     */
    private function replaceExcludedWords($text, array $excluded)
    {
        usort($excluded, function ($a, $b) {
            return strlen((string)$a) < strlen((string)$b);
        });

        $groups = array_chunk($excluded, 150);

        $toReplace = [];

        foreach ($groups as $group) {
            $group = array_filter($group, function ($e) {
                return trim((string)$e) !== "";
            });


            foreach ($group as $word) {
                $re = $this->getPattern([$word], true);

                $text = preg_replace_callback($re, function ($ma) {
                    return strip_tags($ma[0]);
                }, $text);
            }

            if (count($group) >= 1) {
                $text = preg_replace_callback($this->getPattern($group), function ($ma) use (&$toReplace) {

                    $key = strtoupper('DGTRH' . sha1($ma[0]) . 'DGTRH');
                    $toReplace[$key] = $this->getExcludedWordWrapped($ma[0]);

                    return $key;
                }, $text);
            }
        }

        foreach ($toReplace as $k => $v) {
            $text = str_replace($k, $v, $text);
        }

        return $text;
    }

    /**
     * @param bool $addWrapping
     * @return string
     */
    private function getPattern(array $group, $addWrapping = false)
    {
        $addWrapping = (bool) $addWrapping;
        $pattern = '/';
        foreach (array_filter($group) as $elem) {
            $isInsensitive = DgSmartDictionary::isInsensitive($elem, true);

            $elem = $this->preg_quote($elem);

            if ($addWrapping) {
                $elem = $this->getWrappedWord($elem);
            }

            $matchGroup = "(" . ($isInsensitive ? "?i:" : "?:") . $elem . ")";

            if (preg_match('/^\W|\W$/m', (string)$elem)) {
                $pattern .= "(?:\b|\B)" . $matchGroup . "(?:\b|\B)";
            } else {
                $pattern .= "\b" . $matchGroup . "\b";
            }

            if (next($group)) {
                $pattern .= '|';
            }
        }

        $pattern .= '/m';

        return $pattern;
    }

    /**
     * @throws NotSupportedLanguageException
     * @param string $iso_from
     * @param string $iso_to
     * @return void
     */
    protected function canTranslate($iso_from, $iso_to)
    {
        if (!empty($this->iso)) {
            if (!($this->isCompatibleIso($this->parseIso($iso_from)) && $this->isCompatibleIso($this->parseIso($iso_to)))) {
                throw new NotSupportedLanguageException();
            }
        }
    }

    /**
     * @param string $iso
     * @return bool
     */
    public function isCompatibleIso($iso)
    {
        return in_array($iso, $this->iso);
    }

    /**
     * @param string $iso
     * @return string
     */
    protected function parseIso($iso, $checkReplacementsFrom = false)
    {
        if ($checkReplacementsFrom && isset($this->iso_replacements_from[$iso])) {
            return $this->iso_replacements_from[$iso];
        }

        if (isset($this->iso_replacements[$iso])) {
            return $this->iso_replacements[$iso];
        }

        return $iso;
    }

    /**
     * @throws ApiCurlErrorException|\JsonException
     * @param string|mixed[] $posts
     * @param string $url
     * @param mixed[] $headers
     * @return mixed[]
     */
    protected function curlRequest($url, $posts, $headers = array())
    {
        for ($retry = 1; $retry <= 4; $retry++) {
            $response = $this->_curlRequest($url, $posts, $headers, $retry);

            if (in_array((int)$response['code'], [200])) {
                break;
            }

            sleep($retry / 2);
        }

        return $response;
    }

    /**
     * @throws ApiCurlErrorException
     * @throws \JsonException
     * @param string|mixed[] $posts
     * @param string $url
     * @param int $retry
     * @return mixed[]
     */
    private function _curlRequest($url, $posts, array $headers = array(), $retry = 0)
    {
        $url = (string) $url;
        $retry = (int) $retry;
        $handle = curl_init($url);

        $curl_headers = array(
            'Expect: */*'
        );

        if (is_array($posts)) {
            $curl_headers[] = 'Content-length: ' . \Tools::strlen(http_build_query($posts));
            $posts = http_build_query($posts);
        }

        if (empty($headers)) {
            $curl_headers[] = 'Content-Type: application/x-www-form-urlencoded';
        }

        if (defined('_PS_BASE_URL_SSL_')) {
            curl_setopt($handle, CURLOPT_REFERER, _PS_BASE_URL_SSL_);
        } else if (defined('_PS_BASE_URL_')) {
            curl_setopt($handle, CURLOPT_REFERER, _PS_BASE_URL_);
        }

        curl_setopt($handle, CURLOPT_HTTPHEADER, array_merge($headers, $curl_headers));
        curl_setopt($handle, CURLOPT_POSTFIELDS, $posts);

        if ($retry > 2) {
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        }

        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($handle, CURLOPT_MAXREDIRS, 5);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 8);

        $curlTimeout = 120;

        if ($this->key === 'chatgpt') {
            $curlTimeout = 300;
        }

        curl_setopt($handle, CURLOPT_TIMEOUT, $curlTimeout);

        $response = curl_exec($handle);
        $responseDecoded = null;

        if (DgTools::isJson($response)) {
            $responseDecoded = json_decode($response, true, 512, 0);
        }

        if (($response === false && $retry > 3) || ($retry > 3 && $responseDecoded === null)) {
            throw new ApiCurlErrorException('CRL001-' . curl_errno($handle) . ': ' . curl_error($handle));
        }

        $responseCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        return array(
            'code' => (int)$responseCode,
            'data' => $responseDecoded
        );
    }

    /**
     * @return bool|string
     * @param string $error_code
     */
    protected function catchErrorCode($error_code)
    {
        if (isset($this->errors[$error_code])) {
            return $this->errors[$error_code];
        }

        return false;
    }

    /**
     * @param string $contentType
     * @return bool
     */
    private function isContentType($contentType)
    {
        $contentType = (string) $contentType;
        $translation_data = \Tools::getValue('translation_data');

        if (!$translation_data) {
            return false;
        }

        return isset($translation_data['content_type']) && $translation_data['content_type'] === $contentType;
    }

    /**
     * @return bool
     */
    public function isMail()
    {
        $translation_data = \Tools::getValue('translation_data');

        if (!$translation_data) {
            return false;
        }

        return isset($translation_data['mail']) && $translation_data['mail'] === true;
    }

    /**
     * @return bool
     */
    public function textIsHtml($text)
    {
        return strip_tags($text) !== $text;
    }

    /**
     * @return bool
     */
    public function isHtml()
    {
        return $this->isContentType('html');
    }

    /**
     * @return bool
     */
    public function isText()
    {
        return $this->isContentType('text');
    }

    /**
     * @return mixed[]
     */
    private function getIsoCodeList()
    {
        $iso = array_merge($this->iso, array_keys($this->iso_replacements));

        return array_values(array_unique($iso));
    }

    /**
     * @return mixed[]
     */
    private function getLanguagesList()
    {
        foreach ($this->iso_replacements as $k => $v) {
            if (isset($this->languages[$v])) {
                $this->languages[$k] = $this->languages[$v];
            }
        }

        $languages = [];
        $iso = $this->getIsoCodeList();

        foreach ($iso as $i) {
            if (isset($this->languages[$i])) {
                $languages[] = array_filter($this->languages, function ($v, $k) use ($i) {
                    return $k === $i;
                }, ARRAY_FILTER_USE_BOTH);
            } else if (!isset($languages[$i])) {
                $languages[] = [$i => $i];
            }
        }

        return $languages;
    }

    /**
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $configuration = [];

        foreach ($this->configuration->params as $param => $v) {
            $configuration[$param] = $this->configuration->get($param);
        }

        return array(
            'key' => $this->key,
            'title' => $this->title,
            'api_key' => (string)$this->api_key,
            'api_version' => $this->api_version,
            'iso' => $this->getIsoCodeList(),
            'languages' => $this->getLanguagesList(),
            'informations' => $this->informations,
            'configuration' => $configuration
        );
    }

    /**
     * @param string $responseText
     * @return string
     */
    public function formatResponse($responseText)
    {
        if ($this->isText() || $this->isOnlyExcludedWords($responseText)) {
            $responseText = $this->unexcludeWords($responseText);
            $responseText = strip_tags((string)$responseText);
        }

        return $responseText;
    }

    /**
     * @throws ApiErrorException
     * @array $cacheInfo [$text, $isoFrom.$isoTo]
     * @param mixed[] $response
     * @param mixed[] $cacheInfo
     * @return string
     */
    abstract function response($response, $cacheInfo);
}
