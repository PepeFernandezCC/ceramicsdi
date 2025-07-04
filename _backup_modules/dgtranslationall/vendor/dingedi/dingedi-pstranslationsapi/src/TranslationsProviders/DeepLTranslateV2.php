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

class DeepLTranslateV2 extends AbstractTranslationProvider
{
    public function __construct()
    {
        $this->key = 'deepl_v2';
        $this->title = 'DeepL';

        parent::__construct();

        $this->api_version = '2';
        $this->max_chars_per_request = 10000;

        // TODO: improve by removing \Dingedi\PsTools\DgTools::getLocale
        $this->iso = array(
            "bg",
            "cs",
            "da",
            "de",
            "el",
            "en",
            "en-gb",
            "en-us",
            "es",
            "et",
            "fi",
            "fr",
            "hu",
            "id",
            "it",
            "ja",
            "lt",
            "lv",
            "nl",
            "pl",
            "pt",
            "pt-br",
            "pt-pt",
            "ro",
            "ru",
            "sk",
            "sl",
            "sv",
            "tr",
            "uk",
            "zh",
            "ko",
            "nb",
        );

        unset($this->iso_replacements['nn']);

        $this->iso_replacements['pt'] = 'pt-pt';
        $this->iso_replacements['br'] = 'pt-br';
        $this->iso_replacements['gb'] = 'en-gb';
        $this->iso_replacements['us'] = 'en-us';
        $this->iso_replacements['no'] = 'nb';


        $this->iso_replacements_from = [
            'br' => 'pt',
            'gb' => 'en',
            'no' => 'nb'
        ];

        $this->excluded_words_wrappers = array('<span translate="no">', '</span>');

        $this->errors = array(
            '400' => 'Bad request. Please check error message and your parameters.',
            '403' => 'Authorization failed. Please supply a valid auth_key parameter.',
            '404' => 'The requested resource could not be found.',
            '413' => 'The request size exceeds the limit.',
            '429' => 'Too many requests. Please wait and resend your request.',
            '456' => 'Quota exceeded. The character limit has been reached.',
            '503' => 'Resource currently unavailable. Try again later.'
        );

        $this->informations = array(
            'pricing_url' => "https://www.deepl.com/pro-api",
            'registration_url' => "https://www.deepl.com/pro-api",
            'free_offer' => "500 000"
        );

        $this->configuration = new DgConfiguration('provider_' . $this->key, [
            'plan' => 'api-free',
            'formality' => 'default',
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

        $isoFrom = $this->parseIso($isoFrom, true);
        $isoTo = $this->parseIso($isoTo);

        $url = "https://" . $this->configuration->get('plan') . ".deepl.com/v2/translate";

        if ($this->isText() && !$this->isMail()) {
            $text = $this->excludeWords($text, true);
        }

        if ($return = parent::translate($text, $isoFrom, $isoTo)) {
            return $return;
        }

        $options = array(
            'auth_key' => $this->api_key,
            'source_lang' => \Tools::strtoupper($isoFrom),
            'target_lang' => \Tools::strtoupper($isoTo),
            'text' => $text,
            'preserve_formatting' => 1,
        );

        if ($this->textIsHtml($text)) {
            $options['tag_handling'] = 'html';
        }

        if ($this->supportFormality($isoTo)) {
            $options['formality'] = $this->configuration->get('formality');
        }

        $response = $this->curlRequest($url, $options);

        return $this->response($response, [$text, $isoFrom . $isoTo]);
    }

    /**
     * @param string $isoTo
     * @return bool
     */
    public function supportFormality($isoTo)
    {
        return in_array(\Tools::strtoupper($isoTo), array("DE", "FR", "IT", "ES", "NL", "PL", "PT", "PT-PT", "PT-BR", "RU"));
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
                if (!empty($response['data']['message'])) {
                    $error = $response['data']['message'];
                } else {
                    $error = print_r($response, true);
                }
            }

            throw new \Dingedi\PsTranslationsApi\Exception\ApiErrorException($error);
        }

        $responseText = $response['data']['translations'][0]['text'];

        $this->cache->addCache($responseText, $cacheInfo[0], $cacheInfo[1]);

        return $this->formatResponse($responseText);
    }
}
