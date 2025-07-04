<?php
/**
 * This file is part of the performancepro package.
 *
 * @author Mathias Reker
 * @copyright Mathias Reker
 * @license Commercial Software License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\Module\PerformancePro\install;

use PrestaShop\Module\PerformancePro\domain\service\file\PublicHtaccessFile;
use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\exception\PerformanceProInvalidResourceException;
use PrestaShop\Module\PerformancePro\resources\config\Config;
use Tools;

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
        Tools::deleteDirectory(Config::getVarPath());
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
