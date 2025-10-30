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

use PrestaShop\Module\PerformancePro\resources\config\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class Uninstaller extends AbstractInstaller
{
    public function execute(): bool
    {
        $this->uninstallConfig();

        return $this->uninstallTab();
    }

    private function uninstallConfig(): void
    {
        if (empty($this->fieldValues)) {
            return;
        }

        foreach (array_keys($this->fieldValues) as $name) {
            \Configuration::deleteByName($name);
        }
    }

    private function uninstallTab(): bool
    {
        return (new TabBuilder(new \Tab()))
            ->className(Config::CONTROLLER_NAME)
            ->uninstall();
    }
}
