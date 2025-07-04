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
class xmlGenerateLocal
{
    /**
     * @param array $aParams
     */
    public function __construct($aParams = [])
    {
        $this->data = new \stdClass();
        $this->sContent = '';
        $this->aParams = $aParams;
        $this->bOutput = 1;
    }

    /**
     * get the XML for current data feed type
     */
    public function generate()
    {
        try {
            return baseProductStrategy::get('local', ['type' => 'local'])->generate(['reporting' => 0]);
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }
    }
}
