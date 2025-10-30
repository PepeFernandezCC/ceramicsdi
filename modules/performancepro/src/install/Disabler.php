<?php
/**
 * This file is part of the performancepro package.
 *
 * @author Mathias Reker
 * @copyright Mathias Reker
 * @license Academic Free License (AFL 3.0)
 *
 * Additionally, this module is subject to a proprietary End User License Agreement (EULA).
 * For the full copyright, open source license, and EULA information, please view the LICENSE
 * that were distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\Module\PerformancePro\install;

use PrestaShop\Module\PerformancePro\domain\service\file\PublicHtaccessFile;
use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\exception\PerformanceProInvalidResourceException;
use PrestaShop\Module\PerformancePro\resources\config\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class Disabler extends AbstractInstaller
{
    public function execute(): bool
    {
        try {
            (new PublicHtaccessFile())->reset();
        } catch (PerformanceProInvalidResourceException $performanceProInvalidResourceException) {
            LogService::error(
                $performanceProInvalidResourceException->getMessage(),
                $performanceProInvalidResourceException->getTrace()
            );
        }

        $this->clearVarData();

        return $this->unregisterHooks();
    }

    private function clearVarData(): void
    {
        \Tools::deleteDirectory(Config::getVarPath());
    }

    private function unregisterHooks(): bool
    {
        if (empty($this->hooks)) {
            return true;
        }

        foreach ($this->hooks as $hook) {
            if (!$this->module->unregisterHook($hook)) {
                $error = sprintf(
                    $this->module->l('Hook %s has not been uninstalled.', $this->className),
                    $hook
                );

                $this->displayError($error);
            }
        }

        return true;
    }
}
