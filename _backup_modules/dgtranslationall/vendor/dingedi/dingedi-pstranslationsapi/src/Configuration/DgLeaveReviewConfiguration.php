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

namespace Dingedi\PsTranslationsApi\Configuration;

use Dingedi\PsTools\DgHttpConfiguration;
use Dingedi\PsTranslationsApi\DgLeaveReview;

class DgLeaveReviewConfiguration extends DgHttpConfiguration
{
    /**
     * @param string $key
     */
    public function __construct($key = '', array $params = [], $expireInMinutes = 1)
    {
        $key = (string) $key;
        parent::__construct('leave_review_' . DgLeaveReview::getApiKey(), [
            'need' => "1",
            'can' => "0",
            'has' => "0",
        ], $expireInMinutes);
    }

    /**
     * @return string
     */
    public function refresh()
    {
        if (strpos(\Tools::getValue('action'), 'Translate') === false) {
            $apiKey = DgLeaveReview::getApiKey();

            if (trim($apiKey) !== "") {
                try {
                    $response = \Tools::file_get_contents("https://translate.dingedi.com/api/v1/check_review/" . DgLeaveReview::getApiKey(), false, null, 2);

                    if (\Dingedi\PsTools\DgTools::isJson($response)) {
                        $response = json_decode($response, true);

                        $this->updateFromArray([
                            'need' => $response['need_review'] ? "1" : "0",
                            'can' => $response['can_review'] ? "1" : "0",
                            'has' => $response['has_review'] ? "1" : "0"
                        ]);
                    }
                } catch (\Exception $e) {
                }
            }
        }

        return parent::refresh();
    }
}
