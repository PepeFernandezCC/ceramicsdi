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

namespace Gmerchantcenterpro\Admin;

if (!defined('_PS_VERSION_')) {
    exit;
}
abstract class baseController
{
    /**
     * @var string : defines action
     */
    protected static $sAction;

    /**
     * @var string : defines type
     */
    protected static $sType;

    /**
     * get params keys
     *
     * @param array $aParams
     */
    private function __construct(array $aParams = [])
    {
        $aParams = [];
        // defines type to execute
        // use case : no key sAction sent in POST mode (no form has been posted => first page is displayed with admin-display.class.php)
        // use case : key sAction sent in POST mode (form or ajax query posted ).
        $sAction = (!\Tools::getIsset('sAction') || (\Tools::getIsset('sAction') && 'display' == \Tools::getValue('sAction'))) ? (\Tools::getIsset('sAction') ? \Tools::getValue('sAction') : 'display') : \Tools::getValue('sAction');

        // set action
        $this->setAction($sAction);

        // set type
        $this->setType();
    }

    /**
     * set value to a property of object
     *
     * @param array $aRequest
     *
     * @return array
     */
    abstract public function run($aRequest);

    /**
     * set type of method each controller has to execute
     *
     * @param string $sType
     */
    public static function setType($sType = null)
    {
        self::$sType = $sType !== null ? $sType : \Tools::getValue('sType');
    }

    /**
     * set action and select which controller
     *
     * @param string $sAction
     */
    public static function setAction($sAction = null)
    {
        self::$sAction = $sAction !== null ? $sAction : \Tools::getValue('sAction');
    }

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
        $sCtrlType = strtolower($sCtrlType);

        if ($sCtrlType == 'admin') {
            return new adminController($aParams);
        }
    }
}
