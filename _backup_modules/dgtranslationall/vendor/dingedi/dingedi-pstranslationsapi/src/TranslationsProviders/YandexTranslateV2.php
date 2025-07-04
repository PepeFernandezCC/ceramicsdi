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

class YandexTranslateV2 extends AbstractTranslationProvider
{

    public function __construct()
    {
        $this->key = 'yandex_v2';
        $this->title = 'Yandex Translate (cloud)';

        parent::__construct();

        $this->api_version = '2';
        $this->iso = array(
            'af', 'am', 'ar', 'az', 'ba', 'be', 'bg', 'bn', 'bs', 'ca', 'ceb', 'cs', 'cv', 'cy', 'da', 'de', 'el', 'en', 'eo', 'es', 'et', 'eu', 'fa', 'fi', 'fr', 'ga', 'gd', 'gl', 'gu', 'he', 'hi', 'hr', 'ht', 'hu', 'hy', 'id', 'is', 'it', 'ja', 'jv', 'ka', 'kazlat', 'kk', 'km', 'kn', 'ko', 'ky', 'la', 'lb', 'lo', 'lt', 'lv', 'mg', 'mhr', 'mi', 'mk', 'ml', 'mn', 'mr', 'mrj', 'ms', 'mt', 'my', 'ne', 'nl', 'no', 'pa', 'pap', 'pl', 'pt', 'ro', 'ru', 'sah', 'si', 'sk', 'sl', 'sq', 'sr', 'su', 'sv', 'sw', 'ta', 'te', 'tg', 'th', 'tl', 'tr', 'tt', 'udm', 'uk', 'ur', 'uz', 'uzbcyr', 'vi', 'xh', 'yi', 'zh', 'zu'
        );
        $this->max_chars_per_request = 10000;

        $this->excluded_words_wrappers = array("<span translate='no'>", "</span>");

        $this->errors = array();

        $this->informations = array(
            'pricing_url' => "https://cloud.yandex.com/en/docs/translate/pricing",
            'registration_url' => "https://cloud.yandex.com/en/docs/iam/operations/api-key/create",
        );

        $this->configuration = new DgConfiguration('provider_' . $this->key, [
            'folderId' => '',
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

        $url = "https://translate.api.cloud.yandex.net/translate/v2/translate";

        if ($this->isText() && !$this->isMail()) {
            $text = $this->excludeWords($text, true);
        }

        if ($return = parent::translate($text, $isoFrom, $isoTo)) {
            return $return;
        }

        $content = [
            'sourceLanguageCode' => $isoFrom,
            'targetLanguageCode' => $isoTo,
            'format' => $this->textIsHtml($text) ? 'HTML' : 'PLAIN_TEXT',
            'texts' => array(
                $text
            )
        ];

        $folderId = $this->configuration->get('folderId');
        if ($folderId !== '') {
            $content['folderId'] = $folderId;
        }

        $content = json_encode($content);

        $key = strncmp($this->api_key, 't1.', strlen('t1.')) === 0 ? 'Bearer' : 'Api-Key';
        $headers = array(
            'Content-Type: application/json',
            'Authorization: ' . $key . ' ' . $this->api_key
        );

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
                if (!empty($response['data']['message'])) {
                    $error = $response['data']['message'];
                } else {
                    $error = print_r($response, true);
                }
            }

            throw new ApiErrorException($error);
        }

        $responseText = $response['data']['translations'][0]['text'];

        $this->cache->addCache($responseText, $cacheInfo[0], $cacheInfo[1]);

        return $this->formatResponse($responseText);
    }
}
