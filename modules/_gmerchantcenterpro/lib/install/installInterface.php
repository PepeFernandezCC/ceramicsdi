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

namespace Gmerchantcenterpro\Install;

if (!defined('_PS_VERSION_')) {
    exit;
}
interface installInterface
{
    /**
     * install() method make installation of module
     *
     * @param mixed $mParam : array (constant to update with Configuration:updateValue) in config install / string of sql filename in sql install / array of admin tab to install
     *
     * @return bool
     */
    public static function install($mParam = null);

    /**
     * uninstall() method make uninstallation of module
     *
     * @param mixed $mParam : array (constant to update with Configuration:deleteByName) in config install / string of sql filename in sql install / array of admin tab to uninstall
     *
     * @return bool
     */
    public static function uninstall($mParam = null);
}
