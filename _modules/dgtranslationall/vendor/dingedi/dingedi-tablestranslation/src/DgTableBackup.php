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
 * @copyright Copyright 2020 © Dingedi All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  Dingedi PrestaShop Modules
 */

namespace Dingedi\TablesTranslation;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DgTableBackup
{
    /**
     * @param string|mixed[] $tables
     * @return bool
     */
    public static function createBackup($tables)
    {
        if (!is_array($tables)) {
            $tables = [$tables];
        }

        $tables = array_unique($tables);

        foreach ($tables as $k => $table) {
            if (strlen((string) $table) < strlen((string) _DB_PREFIX_) || strncmp((string) $table, (string) _DB_PREFIX_, strlen((string) _DB_PREFIX_)) != 0) {
                $tables[$k] = _DB_PREFIX_ . $table;
            }
        }

        $pb = new \PrestaShopBackup();

        // Generate some random number, to make it extra hard to guess backup file names
        $rand = dechex(rand(0, min(0xffffffff, mt_getrandmax())));
        $date = time();
        $name = 'dg' . ((count($tables) === 1) ? str_replace('_', '', (string) $tables[0]) : '');
        $backupfile = $pb->getRealBackupPath() . $date . '-' . $name . $rand . '.sql';

        // Figure out what compression is available and open the file
        if (function_exists('bzopen')) {
            $backupfile .= '.bz2';
            $fp = @bzopen($backupfile, 'w');
        } elseif (function_exists('gzopen')) {
            $backupfile .= '.gz';
            $fp = @gzopen($backupfile, 'w');
        } else {
            $fp = @fopen($backupfile, 'wb');
        }

        if ($fp === false) {
            return false;
        }

        fwrite($fp, '/* Backup for ' . \Tools::getHttpHost(false, false) . __PS_BASE_URI__ . "\n *  at " . date($date) . "\n */\n");
        fwrite($fp, "\n" . 'SET NAMES \'utf8mb4\';');
        fwrite($fp, "\n" . 'SET FOREIGN_KEY_CHECKS = 0;');
        fwrite($fp, "\n" . 'SET SESSION sql_mode = \'\';' . "\n\n");

        // Find all tables
        $found = 0;

        foreach ($tables as $table) {
            // Export the table schema
            $schema = \Db::getInstance()->executeS('SHOW CREATE TABLE `' . $table . '`');

            if ((is_array($schema) || $schema instanceof \Countable ? count($schema) : 0) != 1 || !isset($schema[0]['Table']) || !isset($schema[0]['Create Table'])) {
                fclose($fp);
                unlink($backupfile);
                return false;
            }

            fwrite($fp, '/* Scheme for table ' . $schema[0]['Table'] . " */\n");

            fwrite($fp, 'DROP TABLE IF EXISTS `' . $schema[0]['Table'] . '`;' . "\n");

            fwrite($fp, $schema[0]['Create Table'] . ";\n\n");

            $data = \Db::getInstance()->query('SELECT * FROM `' . $schema[0]['Table'] . '`', false);
            $sizeof = \Db::getInstance()->numRows();
            $lines = explode("\n", (string) $schema[0]['Create Table']);

            if ($data && $sizeof > 0) {
                // Export the table data
                fwrite($fp, 'INSERT INTO `' . $schema[0]['Table'] . "` VALUES\n");
                $i = 1;
                while ($row = \Db::getInstance()->nextRow($data)) {
                    $s = '(';

                    foreach ($row as $field => $value) {
                        $tmp = "'" . pSQL($value, true) . "',";
                        if ($tmp != "'',") {
                            $s .= $tmp;
                        } else {
                            foreach ($lines as $line) {
                                if (strpos($line, '`' . $field . '`') !== false) {
                                    if (preg_match('/(.*NOT NULL.*)/Ui', $line)) {
                                        $s .= "'',";
                                    } else {
                                        $s .= 'NULL,';
                                    }

                                    break;
                                }
                            }
                        }
                    }
                    $s = rtrim($s, ',');

                    if ($i % 200 == 0 && $i < $sizeof) {
                        $s .= ");\nINSERT INTO `" . $schema[0]['Table'] . "` VALUES\n";
                    } elseif ($i < $sizeof) {
                        $s .= "),\n";
                    } else {
                        $s .= ");\n";
                    }

                    fwrite($fp, $s);
                    ++$i;
                }
            }
            ++$found;
        }

        fclose($fp);
        if ($found == 0) {
            unlink($backupfile);
            return false;
        }

        return true;
    }
}
