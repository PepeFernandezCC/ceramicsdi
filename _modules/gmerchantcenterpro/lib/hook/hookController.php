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
class hookController
{
    /**
     * @var hookController : defines hook object to display
     */
    private $oHook;

    /**
     * Magic Method __construct instantiate the matching hook class
     *
     * @param string $sType : type of interface to execute
     * @param string $sAction
     *
     * @return mixed
     *
     */
    public function __construct($sType, $sAction)
    {
        if ($sType == 'display') {
            $this->oHook = new hookDisplay($sAction);
        } elseif ($sType == 'action') {
            $this->oHook = new hookAction($sAction);
        } else {
            exit(0);
        }
    }

    /**
     * method execute hook
     *
     * @category hook collection
     *
     * @param array $aParams
     *
     * @return array $aDisplay : empty => false / not empty => true
     */
    public function run(array $aParams = null)
    {
        return $this->oHook->run($aParams);
    }
}
