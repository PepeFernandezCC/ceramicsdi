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
 * @copyright Copyright 2021 © Dingedi All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 * @category  Dingedi PrestaShop Modules
 */


namespace Dingedi\PsTranslationsApi\TranslationsProviders;

use Dingedi\PsTools\DgConfiguration;
use Dingedi\PsTranslationsApi\Exception\ApiErrorException;

class LectoAIV1 extends AbstractTranslationProvider
{
    public function __construct()
    {
        $this->key = 'lectoai_v1';
        $this->title = 'LectoAI';

        parent::__construct();

        $this->api_version = '1';
        $this->max_chars_per_request = 2000;
        $this->iso = array(
            "af",
            "sq",
            "am",
            "ar",
            "hy",
            "az",
            "be",
            "bel",
            "bn",
            "bs",
            "bg",
            "ca",
            "ceb",
            "zh-CN",
            "zh-TW",
            "hr",
            "cs",
            "da",
            "nl",
            "en",
            "et",
            "tl",
            "fi",
            "fr",
            "fy",
            "gl",
            "ka",
            "de",
            "el",
            "gu",
            "ht",
            "ha",
            "he",
            "hi",
            "hu",
            "is",
            "ig",
            "id",
            "ga",
            "it",
            "ja",
            "kn",
            "kk",
            "km",
            "ko",
            "lo",
            "lv",
            "lt",
            "lb",
            "mk",
            "mg",
            "ms",
            "ml",
            "mr",
            "mn",
            "my",
            "ne",
            "nb",
            "no",
            "or",
            "ps",
            "fa",
            "pl",
            "pt",
            "pt-BR",
            "pt-PT",
            "pa",
            "ro",
            "ru",
            "gd",
            "sr",
            "sd",
            "si",
            "sk",
            "sl",
            "so",
            "es",
            "su",
            "sw",
            "sv",
            "ta",
            "th",
            "tr",
            "uk",
            "ur",
            "uz",
            "vi",
            "cy",
            "xh",
            "yi",
            "yo",
            "zu"
        );

        $this->iso_replacements['pt'] = 'pt-PT';
        $this->iso_replacements['br'] = 'pt-BR';

        $this->excluded_words_wrappers = array('<span translate="no">', '</span>');

        $this->errors = array(
            '200' => 'The operation was completed successfully',
        );

        $this->informations = array(
            'pricing_url' => "https://dashboard.lecto.ai/pricing?ref=dingedi",
            'registration_url' => "https://dashboard.lecto.ai/signin?ref=dingedi",
        );

        $this->configuration = new DgConfiguration('provider_' . $this->key, [
        ]);
    }

    /**
     * @throws \Exception
     * @param string $text
     * @param string $isoFrom
     * @param string $isoTo
     * @return string
     */
    public function translate($text, $isoFrom, $isoTo)
    {
        if ($return = parent::translate($text, $isoFrom, $isoTo)) {
            return $return;
        }

        $isoFrom = $this->parseIso($isoFrom);
        $isoTo = $this->parseIso($isoTo);

        if ($this->isText() && !$this->isMail()) {
            $text = $this->excludeWords($text, true);
        }

        if ($return = parent::translate($text, $isoFrom, $isoTo)) {
            return $return;
        }

        $url = "https://api.lecto.ai/v1/translate/text";

        $headers = array(
            'Content-type: application/json',
            'X-API-Key: ' . $this->api_key,
            'Expect: 100-continue'
        );

        $content = array(
            'texts' => [$text],
            'to' => [$isoTo],
            'from' => $isoFrom
        );

        $content = json_encode($content);

        $response = $this->curlRequest($url, $content, $headers);

        return $this->response($response, [$text, $isoFrom . $isoTo]);
    }

    /**
     * @param mixed[] $response
     * @param mixed[] $cacheInfo
     * @return string
     */
    public function response($response, $cacheInfo)
    {
        $responseCode = (int)$response['code'];

        if ($responseCode != 200) {
            if (!$error = $this->catchErrorCode($responseCode)) {
                if (!empty($response['data']['error'])) {
                    $error = $response['data']['error'];
                } else {
                    $error = print_r($response, true);
                }
            }

            throw new ApiErrorException($error);
        }

        $responseText = $response['data']['translations'][0]['translated'][0];

        $this->cache->addCache($responseText, $cacheInfo[0], $cacheInfo[1]);

        return $this->formatResponse($responseText);
    }
}
