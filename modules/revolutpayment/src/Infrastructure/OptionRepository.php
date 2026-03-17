<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    Revolut
 * @copyright Since 2020 Revolut
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 */

namespace Revolut\PrestaShop\Infrastructure;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Revolut\Plugin\Services\Repositories\OptionRepositoryInterface;

class OptionRepository implements OptionRepositoryInterface
{
    private $table;
    private $tableExist;

    public function __construct()
    {
        $this->table = _DB_PREFIX_ . 'revolut_options';
        $this->checkIsTableExist();
    }

    private function checkIsTableExist()
    {
        $res = \Db::getInstance()->executeS("SHOW TABLES LIKE '{$this->table}'");
        $this->tableExist = !empty($res);
    }

    private function maybeEncode($value)
    {
        if (is_array($value)) {
            return json_encode($value);
        }

        return $value;
    }

    private function maybeDecode($value)
    {
        if (is_string($value) && $this->isJson($value)) {
            $decoded = json_decode($value, true);

            return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $value;
        }

        return $value;
    }

    private function isJson($string)
    {
        if (!is_string($string)) {
            return false;
        }
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }

    public function add($name, $value)
    {
        if (!$this->tableExist) {
            return false;
        }

        $value = $this->maybeEncode($value);

        try {
            $res = \Db::getInstance()->execute("INSERT INTO {$this->table} (option_name, option_value) VALUES ('" . pSql($name) . "', '" . pSql($value) . "')", false);
        } catch (\Exception $e) {
            $res = false;
        }

        // RLog::info($this->wp\Db->last_query);
        return $res;
    }

    public function addOrUpdate($name, $value)
    {
        if (!$this->tableExist) {
            return false;
        }

        $value = $this->maybeEncode($value);

        $res = \Db::getInstance()->execute(
            'INSERT INTO ' . $this->table . ' (option_name, option_value)
             VALUES ("' . pSql($name) . '", "' . pSql($value) . '")
             ON DUPLICATE KEY UPDATE option_value = "' . pSql($value) . '"'
        );

        return $res;
    }

    public function get($name)
    {
        if (!$this->tableExist) {
            return '';
        }

        $value = \Db::getInstance()->getValue("SELECT option_value FROM {$this->table} WHERE option_name = '" . pSql($name) . "'");

        return $this->maybeDecode($value);
    }

    public function update($name, $value)
    {
        if (!$this->tableExist) {
            return false;
        }

        $value = $this->maybeEncode($value);
        $res = \Db::getInstance()->execute("UPDATE {$this->table} SET option_value = '" . pSql($value) . "' WHERE option_name = '" . pSql($name) . "'");

        // RLog::info($this->wp\Db->last_query);
        return $res;
    }

    public function delete($name)
    {
        if (!$this->tableExist) {
            return false;
        }

        return \Db::getInstance()->execute("DELETE FROM {$this->table} WHERE option_name = '" . pSql($name) . "'");
    }

    public function exists($name)
    {
        if (!$this->tableExist) {
            return false;
        }

        $count = \Db::getInstance()->getValue("SELECT COUNT(*) FROM {$this->table} WHERE option_name = '" . pSql($name) . "'");

        return $count > 0;
    }

    public function addCached(string $key, $value, int $ttlSeconds): bool
    {
        try {
            $expKey = $key . '_time';

            $success1 = \Configuration::updateValue($expKey, time() + $ttlSeconds);
            $success2 = \Configuration::updateValue($key, $this->maybeEncode($value));

            return $success1 && $success2;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getCached(string $key)
    {
        try {
            $expKey = $key . '_time';
            $expTime = (int) \Configuration::get($expKey);

            if (!$expTime) {
                return null;
            }

            if ($expTime < time()) {
                \Configuration::deleteByName($expKey);
                \Configuration::deleteByName($key);

                return null;
            }
            $cachedValue = \Configuration::get($key);

            return $this->maybeDecode($cachedValue);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
