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
use Dingedi\PsTranslationsApi\DgLeaveReview;
use Dingedi\PsTranslationsApi\Exception\ApiErrorException;

class DingediFreeOffer extends AbstractTranslationProvider
{

    public function __construct()
    {
        $this->key = 'dingedi_free_offer';
        $this->title = 'ChatGPT/Google/DeepL/Microsoft Free';
        parent::__construct();
        $this->api_key =  DgLeaveReview::hasReview() ? (new DingediTranslateV1())->api_key : '';
        $this->api_version = '1';
        $this->max_chars_per_request = 5000;
        $this->iso = array_unique(array_merge(
            (new GoogleTranslateV2())->iso,
            (new MicrosoftTranslateV3())->iso
        ));

        $this->excluded_words_wrappers = array('<span translate="no">', '</span>');

        $this->informations = array(
            'registration_url' => "https://addons.prestashop.com/en/ratings.php",
        );

        $this->configuration = new DgConfiguration('provider_' . $this->key, [
            'provider' => 'microsoft'
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

        $url = 'https://translate.dingedi.com/api/v1/translate';

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

        $params = array(
            'order_id' => $this->api_key,
            'format' => $this->textIsHtml($text) ? 'html' : 'text',
            'q' => $text,
            'source' => $isoFrom,
            'target' => $isoTo,
            'provider' => $this->configuration->get('provider')
        );

        $module = \Module::getInstanceByName("dgtranslationall");

        if ($module !== false) {
            $params['module'] = $module->name;
            $params['module_version'] = $module->version;
        }

        $response = $this->curlRequest($url, $params);

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
                    $error = $response['data']['message'];
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
