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

class MicrosoftTranslateV3 extends AbstractTranslationProvider
{

    public function __construct()
    {
        $this->key = 'microsoft_v3';
        $this->title = 'Microsoft Translate';

        parent::__construct();

        $this->api_version = '3.0';
        $this->max_chars_per_request = 10000;
        $this->iso = array(
            'af',
            'sq',
            'am',
            'ar',
            'hy',
            'as',
            'az',
            'bn',
            'bs',
            'bg',
            'yue',
            'ca',
            'lzh',
            'zh-Hans',
            'zh-Hant',
            'hr',
            'cs',
            'da',
            'prs',
            'nl',
            'en',
            'et',
            'fj',
            'fil',
            'fi',
            'fr',
            'fr-ca',
            'de',
            'el',
            'gu',
            'ht',
            'he',
            'hi',
            'mww',
            'hu',
            'is',
            'id',
            'iu',
            'ga',
            'it',
            'ja',
            'kn',
            'kk',
            'km',
            'tlh-Latn',
            'tlh-Piqd',
            'ko',
            'ku',
            'kmr',
            'lo',
            'lv',
            'lt',
            'mg',
            'ms',
            'ml',
            'mt',
            'mi',
            'mr',
            'my',
            'ne',
            'nb',
            'or',
            'ps',
            'fa',
            'pl',
            'pt',
            'pt-pt',
            'pa',
            'otq',
            'ro',
            'ru',
            'sm',
            'sr-Cyrl',
            'sr-Latn',
            'sk',
            'sl',
            'es',
            'sw',
            'sv',
            'ty',
            'ta',
            'te',
            'th',
            'ti',
            'to',
            'tr',
            'uk',
            'ur',
            'vi',
            'cy',
            'yua'
        );
        $this->iso_replacements['zh'] = 'zh-Hans';
        $this->iso_replacements['tw'] = 'zh-Hant';
        $this->excluded_words_wrappers = array('<span translate="no">', '</span>');

        $this->errors = array(
            '400000' => 'One of the request inputs is not valid.',
            '400001' => 'The "scope" parameter is invalid.',
            '400002' => 'The "category" parameter is invalid.',
            '400003' => 'A language specifier is missing or invalid.',
            '400004' => 'A target script specifier ("To script") is missing or invalid.',
            '400005' => 'An input text is missing or invalid.',
            '400006' => 'The combination of language and script is not valid.',
            '400018' => 'A source script specifier ("From script") is missing or invalid.',
            '400019' => 'One of the specified languages is not supported.',
            '400020' => 'One of the elements in the array of input text is not valid.',
            '400021' => 'The API version parameter is missing or invalid.',
            '400023' => 'One of the specified language pair is not valid.',
            '400035' => 'The source language ("From" field) is not valid.',
            '400036' => 'The target language ("To" field) is missing or invalid.',
            '400042' => 'One of the options specified ("Options" field) is not valid.',
            '400043' => 'The client trace ID (ClientTraceId field or X-ClientTranceId header) is missing or invalid.',
            '400050' => 'The input text is too long. View request limits.',
            '400064' => 'The "translation" parameter is missing or invalid.',
            '400070' => 'The number of target scripts (ToScript parameter) does not match the number of target languages (To parameter).',
            '400071' => 'The value is not valid for TextType.',
            '400072' => 'The array of input text has too many elements.',
            '400073' => 'The script parameter is not valid.',
            '400074' => 'The body of the request is not valid JSON.',
            '400075' => 'The language pair and category combination is not valid.',
            '400077' => 'The maximum request size has been exceeded. View request limits.',
            '400079' => 'The custom system requested for translation between from and to language does not exist.',
            '400080' => 'Transliteration is not supported for the language or script.',
            '401000' => 'The request is not authorized because credentials are missing or invalid.',
            '401015' => 'The credentials provided are for the Speech API. This request requires credentials for the Text API. Use a subscription to Translator.',
            '403000' => 'The operation is not allowed.',
            '403001' => 'The operation is not allowed because the subscription has exceeded its free quota.',
            '405000' => 'The request method is not supported for the requested resource.',
            '408001' => 'The translation system requested is being prepared. Please retry in a few minutes.',
            '408002' => 'Request timed out waiting on incoming stream. The client did not produce a request within the time that the server was prepared to wait. The client may repeat the request without modifications at any later time.',
            '415000' => 'The Content-Type header is missing or invalid.',
            '429000' => 'The server rejected the request because the client has exceeded request limits.',
            '429001' => 'The server rejected the request because the client has exceeded request limits.',
            '429002' => 'The server rejected the request because the client has exceeded request limits.',
            '500000' => 'An unexpected error occurred. If the error persists, report it with date/time of error, request identifier from response header X-RequestId, and client identifier from request header X-ClientTraceId.',
            '503000' => 'Service is temporarily unavailable. Please retry. If the error persists, report it with date/time of error, request identifier from response header X-RequestId, and client identifier from request header X-ClientTraceId.'
        );

        $this->informations = array(
            'pricing_url'      => "https://azure.microsoft.com/en-us/pricing/details/cognitive-services/translator/",
            'registration_url' => "https://azure.microsoft.com/en-us/free/",
            'free_offer'       => "2 000 000",
            'trial_offer'      => array(
                'ammount'         => '200 $',
                'months_duration' => 12
            )
        );

        $this->configuration = new DgConfiguration('provider_' . $this->key, [
            'server' => 'api',
            'location' => '-'
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

        $url = "https://" . $this->configuration->get('server') . ".cognitive.microsofttranslator.com/translate?api-version=3.0";
        $url .= "&from=" . $isoFrom . "&to=" . $isoTo . "&textType=" . ($this->textIsHtml($text) ? "html" : "plain");

        $content = json_encode([['Text' => $text]]);

        $headers = array(
            'Content-type: application/json',
            'Content-length: ' . \Tools::strlen($content),
            'Ocp-Apim-Subscription-Key: ' . $this->api_key,
            'X-ClientTraceId: ' . $this->com_create_guid()
        );

        $bingLocation = $this->configuration->get('location');
        if ($bingLocation !== '-') {
            $headers[] = 'Ocp-Apim-Subscription-Region: ' . $bingLocation;
        }

        $response = $this->curlRequest($url, $content, $headers);

        return $this->response($response, [$text, $isoFrom . $isoTo]);
    }

    /**
     * @throws \Exception
     * @return string
     */
    public function com_create_guid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            rand(0, 0xffff),
            rand(0, 0xffff),
            rand(0, 0xffff),
            rand(0, 0x0fff) | 0x4000,
            rand(0, 0x3fff) | 0x8000,
            rand(0, 0xffff),
            rand(0, 0xffff),
            rand(0, 0xffff)
        );
    }

    /**
     * @throws ApiErrorException
     * @param mixed[] $response
     * @param mixed[] $cacheInfo
     * @return string
     */
    public function response($response, $cacheInfo)
    {
        $responseCode = (int)$response['code'];

        if ($responseCode != 200) {
            if (!$error = $this->catchErrorCode($response['data']['error']['code'])) {
                if (!empty($response['error']['message'])) {
                    $error = $response['error']['message'];
                } else {
                    $error = print_r($response, true);
                }
            }

            throw new \Dingedi\PsTranslationsApi\Exception\ApiErrorException($error);
        }

        $responseText = $response['data'][0]['translations'][0]['text'];

        $this->cache->addCache($responseText, $cacheInfo[0], $cacheInfo[1]);

        return $this->formatResponse($responseText);
    }
}
