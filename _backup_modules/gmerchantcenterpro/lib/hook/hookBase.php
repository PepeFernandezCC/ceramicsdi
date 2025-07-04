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

namespace Gmerchantcenterpro\Hook;

if (!defined('_PS_VERSION_')) {
    exit;
}
abstract class hookBase
{
    /**
     * @var string : define hook display or action
     */
    protected $sHook;

    /**
     * Magic Method __construct assigns few information about hook
     *
     * @param string $sHook
     */
    public function __construct($sHook)
    {
        // set hook
        $this->sHook = $sHook;
    }

    /**
     * run() method execute hook
     *
     * @category hook collection
     *
     * @uses
     *
     * @param array $aParams
     *
     * @return array
     */
    abstract public function run(array $aParams = null);
}
