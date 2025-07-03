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

class ChatGPTV1 extends AbstractTranslationProvider
{

    public function __construct()
    {
        $this->key = 'chatgpt';
        $this->title = 'OpenAI (ChatGPT)';

        parent::__construct();

        $this->api_version = '1';
        $this->max_chars_per_request = 5000;
        $this->iso = array(
            'af', 'am', 'ar', 'az', 'be', 'bg', 'bn', 'bs', 'ca', 'ceb', 'co', 'cs', 'cy', 'da', 'de', 'el',
            'en', 'eo', 'es', 'et', 'eu', 'fa', 'fi', 'fr', 'fy', 'ga', 'gd', 'gl', 'gu', 'ha', 'haw', 'hi',
            'hmn', 'hr', 'ht', 'hu', 'hy', 'id', 'ig', 'is', 'it', 'iw', 'ja', 'jw', 'ka', 'kk', 'km', 'kn',
            'ko', 'ku', 'ky', 'la', 'lb', 'lo', 'lt', 'lv', 'mg', 'mi', 'mk', 'ml', 'mn', 'mr', 'ms', 'mt',
            'my', 'ne', 'nl', 'no', 'ny', 'pa', 'pl', 'ps', 'pt', 'ro', 'ru', 'sd', 'si', 'sk', 'sl', 'sm',
            'sn', 'so', 'sq', 'sr', 'st', 'su', 'sv', 'sw', 'ta', 'te', 'tg', 'th', 'tl', 'tr', 'uk', 'ur',
            'uz', 'vi', 'xh', 'yi', 'yo', 'zh', 'zh-TW', 'he', 'zu', 'mx'
        );

        $this->excluded_words_wrappers = array('<span translate="no">', '</span>');
        $this->informations = array(
            'registration_url' => "https://platform.openai.com",
        );

        $this->configuration = new DgConfiguration('provider_' . $this->key, [
            'temperature' => 0.7,
            'sentence' => 'Translate this html from [from_lang] to [to_lang]',
            'model' => 'gpt-3.5-turbo'
        ]);

        $this->iso_replacements['mx'] = 'mx';
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

        $url = 'https://api.openai.com/v1/chat/completions';

        if ($this->isText()) {
            if ($this->isMail()) {
                $text = "<pre>" . $text . "</pre>";
            } else {
                $text = $this->excludeWords($text, true);
            }
        }

        if ($return = parent::translate($text, $isoFrom, $isoTo)) {
            return $return;
        }

        $data = [
            'model' => $this->configuration->get('model'),
            'temperature' => (float)$this->configuration->get('temperature'),
            'messages' => []
        ];

        $format = $this->textIsHtml($text) ? 'html' : 'text';

        if ($format === "html") {
            $data['messages'][] = ["role" => "system", "content" => "Don't translate span tag with translate attribute set to no"];
        }

        $sentence = $this->configuration->get('sentence');
        $sentence = str_replace(
            ['[from_lang]', '[to_lang]'],
            [
                isset((new \Language(\Language::getIdByIso($isoFrom)))->name) ? (new \Language(\Language::getIdByIso($isoFrom)))->name : $isoFrom,
                isset((new \Language(\Language::getIdByIso($isoTo)))->name) ? (new \Language(\Language::getIdByIso($isoTo)))->name : $isoTo
            ],
            $sentence);

        if ($format === "text") {
            $sentence = str_replace('html', 'text', $sentence);
        }

        $data['messages'][] = ["role" => "system", "content" => $sentence];
        $data['messages'][] = ["role" => "user", "content" => $text];

        $params = json_encode($data);

        $headers = ['Content-Type: application/json', 'Authorization: Bearer ' . $this->api_key];

        $response = $this->curlRequest($url, $params, $headers);

        return $this->response($response, [$text, $isoFrom . $isoTo]);
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
            if (!$error = $this->catchErrorCode($responseCode)) {
                if (!empty($response['data']['error'])) {
                    $error = $response['data']['error']['message'];
                } else {
                    $error = print_r($response, true);
                }
            }

            throw new ApiErrorException($error);
        }

        $responseText = $response['data']['choices'][0]['message']['content'];

        $this->cache->addCache($responseText, $cacheInfo[0], $cacheInfo[1]);

        return $this->formatResponse($responseText);
    }
}
