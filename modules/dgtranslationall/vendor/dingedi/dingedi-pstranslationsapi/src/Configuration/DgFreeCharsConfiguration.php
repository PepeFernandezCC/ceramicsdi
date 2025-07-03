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
use Dingedi\PsTools\DgTools;

class DgFreeCharsConfiguration extends DgHttpConfiguration
{
    /**
     * @param string $key
     */
    public function __construct($key = '', array $params = [], $expireInMinutes = 1)
    {
        $key = (string) $key;
        parent::__construct('free_chars', [
            'remaining' => "0",
        ], $expireInMinutes);
    }

    /**
     * @return string
     */
    public function refresh()
    {
        try {
            $response = \Tools::file_get_contents("https://translate.dingedi.com/api/v1/check_free/" . \Dingedi\PsTranslationsApi\DgLeaveReview::getApiKey(), false, null, 2);

            if (DgTools::isJson($response)) {

                $response = json_decode($response, true);

                $this->update('remaining', $response['remaining'] ? "1" : "0");
            }

        } catch (\Exception $e) {
        }

        return parent::refresh();
    }
}
