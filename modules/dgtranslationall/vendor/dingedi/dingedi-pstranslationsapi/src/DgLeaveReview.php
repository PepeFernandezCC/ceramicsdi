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

namespace Dingedi\PsTranslationsApi;

use Dingedi\PsTranslationsApi\Configuration\DgFreeCharsConfiguration;
use Dingedi\PsTranslationsApi\Configuration\DgLeaveReviewConfiguration;
use Dingedi\PsTranslationsApi\TranslationsProviders\DingediTranslateV1;

class DgLeaveReview
{
    /**
     * @return string
     */
    public static function getApiKey()
    {
        return (new DingediTranslateV1())->api_key;
    }

    /**
     * @return bool
     */
    public static function hasFreeChars()
    {
        return (new DgFreeCharsConfiguration())->get('remaining') === "1";
    }

    /**
     * @return bool
     */
    public static function canReview()
    {
        return (new DgLeaveReviewConfiguration())->get('need') === "1" && (new DgLeaveReviewConfiguration())->get('can') === "1";
    }

    /**
     * @return bool
     */
    public static function hasReview()
    {
        return (new DgLeaveReviewConfiguration())->get('has') === "1";
    }
}
