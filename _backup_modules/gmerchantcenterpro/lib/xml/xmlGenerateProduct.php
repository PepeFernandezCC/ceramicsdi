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

namespace Gmerchantcenterpro\Xml;

if (!defined('_PS_VERSION_')) {
    exit;
}
use Gmerchantcenterpro\Admin\adminUpdate;

class xmlGenerateProduct
{
    /**
     * @var array : array for all parameters provided to generate XMl files
     */
    protected static $aParamsForXml = [];

    /**
     * @param array $aParams
     */
    public function __construct($sType = null, $aParams)
    {
    }

    /**
     * generate get the XML for current data feed type
     */
    public function generate(array $aPost = null)
    {
        try {
            // detect the floor step
            $iFloor = \Tools::getValue('iFloor');

            if ($iFloor == 0) {
                $oUpdate = adminUpdate::create();
                $oUpdate->run('customLabelDate');
                $oUpdate->run('customCheck');
            }

            return baseProductStrategy::get('product', ['type' => 'product'])->generate(['reporting' => \Tools::getValue('bReporting')]);
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }
    }
}
