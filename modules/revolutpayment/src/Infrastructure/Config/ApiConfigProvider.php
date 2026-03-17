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

namespace Revolut\PrestaShop\Infrastructure\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Revolut\Plugin\Infrastructure\Config\Api\DevConfig;
use Revolut\Plugin\Infrastructure\Config\Api\Environment;
use Revolut\Plugin\Infrastructure\Config\Api\ProdConfig;
use Revolut\Plugin\Infrastructure\Config\Api\SandboxConfig;
use Revolut\Plugin\Services\Config\Api\ConfigInterface;
use Revolut\Plugin\Services\Config\Api\ConfigProviderInterface;
use Revolut\PrestaShop\Infrastructure\PSConfigOptionRepository;

class ApiConfigProvider implements ConfigProviderInterface
{
    private $repository;

    public function __construct(PSConfigOptionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getConfig(string $mode = null): ConfigInterface
    {
        if (!$mode) {
            $mode = $this->repository->get('REVOLUT_API_MODE');
        }

        switch ($mode) {
            case Environment::PROD:
                $config = new ProdConfig();
                break;
            case Environment::DEV:
                $config = new DevConfig();
                break;
            case Environment::SANDBOX:
                $config = new SandboxConfig();
                break;
            default:
                $config = new ProdConfig();
                break;
        }

        $config->setSecretKey($this->getSecretKey($mode));
        $config->setPublicKey($this->getPublicKey($mode));

        return $config;
    }

    public function getSecretKey($mode)
    {
        return $this->repository->get('REVOLUT_API_SECRET_KEY_' . $mode);
    }

    public function getPublicKey($mode)
    {
        return $this->repository->get('REVOLUT_API_PUBLIC_KEY_' . $mode);
    }
}
