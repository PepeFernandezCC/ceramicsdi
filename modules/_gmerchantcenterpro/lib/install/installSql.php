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
use Gmerchantcenterpro\Configuration\moduleConfiguration;

class installSql implements installInterface
{
    /**
     * install of module
     *
     * @param mixed $mParam => sql install file
     *
     * @return bool $bReturn : true => validate install, false => invalidate install
     */
    public static function install($mParam = null)
    {
        return self::exec($mParam);
    }

    /**
     * uninstall of module
     *
     * @param mixed $mParam => sql uninstall file
     *
     * @return bool $bReturn : true => validate uninstall, false => invalidate uninstall
     */
    public static function uninstall($mParam = null)
    {
        return self::exec($mParam);
    }

    /**
     * make an execution generic for install and uninstall
     *
     * @param string $sFile
     *
     * @return bool $bReturn : true => validate uninstall, false => invalidate uninstall
     */
    private static function exec($sFile)
    {
        // set return execution
        $bReturn = false;

        // log jam to debug appli
        if (moduleConfiguration::GMCP_LOG_JAM_SQL) {
            $bReturn = moduleConfiguration::GMCP_LOG_JAM_SQL;
        } elseif (file_exists($sFile)) {
            // open file
            $rHandle = fopen($sFile, 'r');

            // test handler
            if ($rHandle) {
                $bReturn = true;
                while (($sLine = fgets($rHandle, 4096)) !== false) {
                    if (!empty($sLine)) {
                        if (strpos($sLine, 'PREFIX_')) {
                            $sLine = str_replace('PREFIX_', _DB_PREFIX_, $sLine);
                        }
                        // execute sql method declared in sql file (install or uninstall)
                        if (false == \Db::getInstance()->Execute(trim($sLine))) {
                            return false;
                        }
                    }
                }
                if (!feof($rHandle)) {
                    $bReturn = false;
                }
                fclose($rHandle);
            }
        }

        return $bReturn;
    }
}
