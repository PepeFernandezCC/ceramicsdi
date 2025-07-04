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

class LibreTranslateV1 extends AbstractTranslationProvider
{
    public function __construct()
    {
        $this->key = 'libretranslate_v1';
        $this->title = 'LibreTranslate';

        parent::__construct();

        $this->api_version = '1';
        $this->max_chars_per_request = 2000;
        $this->iso = array(
            "ar",
            "az",
            "cs",
            "da",
            "de",
            "el",
            "en",
            "eo",
            "es",
            "fa",
            "fi",
            "fr",
            "ga",
            "he",
            "hi",
            "hu",
            "id",
            "it",
            "ja",
            "ko",
            "nl",
            "pl",
            "pt",
            "ru",
            "ru",
            "sk",
            "sv",
            "tr",
            "uk",
            "zh"
          );

        $this->excluded_words_wrappers = array('<span translate="no">', '</span>');

        $this->errors = array(
            '200' => 'The operation was completed successfully',
        );

        $this->informations = array(
            'pricing_url'      => "https://portal.libretranslate.com/",
            'registration_url' => "https://portal.libretranslate.com/",
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

        $url = "https://libretranslate.com/translate";

        $response = $this->curlRequest($url, array(
            'q' => $text,
            'source' => $isoFrom,
            'target' => $isoTo,
            'format' => ($this->textIsHtml($text) ? 'html' : 'text'),
            'api_key' => $this->api_key
        ));

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

        $responseText = $response['data']['translatedText'];

        $this->cache->addCache($responseText, $cacheInfo[0], $cacheInfo[1]);

        return $this->formatResponse($responseText);
    }
}
