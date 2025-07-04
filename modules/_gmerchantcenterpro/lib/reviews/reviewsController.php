<?php
/**
 * Google Merchant Center Pro
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

namespace Gmerchantcenterpro\Reviews;

if (!defined('_PS_VERSION_')) {
    exit;
}
abstract class reviewsController
{
    /**
     * instantiate matched ctrl object
     *
     * @param string $sCtrlType
     * @param array $aParams
     *
     * @return mixed
     */
    public static function get($sCtrlType, array $aParams = null)
    {
        try {
            if (!empty($sCtrlType)) {
                $sCtrlType = strtolower($sCtrlType);

                if ($sCtrlType == 'gsnippetsreviews') {
                    return new reviewsGsnippets($aParams);
                } elseif ($sCtrlType == 'productcomments') {
                    return new reviewsProductcomments($aParams);
                } else {
                    return false;
                }
            }
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }
    }
}
