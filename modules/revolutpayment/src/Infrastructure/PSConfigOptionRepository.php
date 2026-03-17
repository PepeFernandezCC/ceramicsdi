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

class PSConfigOptionRepository implements OptionRepositoryInterface
{
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
        $value = $this->maybeEncode($value);

        return \Configuration::updateValue($name, $value);
    }

    public function addOrUpdate($name, $value)
    {
        $value = $this->maybeEncode($value);

        return \Configuration::updateValue($name, $value);
    }

    public function get($name)
    {
        $value = \Configuration::get($name);

        return $this->maybeDecode($value);
    }

    public function update($name, $value)
    {
        $value = $this->maybeEncode($value);

        return \Configuration::updateValue($name, $value);
    }

    public function delete($name)
    {
        return \Configuration::deleteByName($name);
    }

    public function exists($name)
    {
        return !empty(\Configuration::get($name));
    }

    public function addCached(string $key, $value, int $ttlSeconds): bool
    {
        try {
            \Cache::store($key, [
                'value' => $value,
                'exp' => time() + $ttlSeconds,
            ]);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getCached(string $key)
    {
        try {
            if (\Cache::isStored($key)) {
                $data = \Cache::retrieve($key);
                if (isset($data['exp']) && $data['exp'] < time()) {
                    \Cache::clean($key); // expired, remove it

                    return false;
                }

                return $data['value'];
            }
        } catch (\Throwable $e) {
            return false;
        }

        return false;
    }
}
