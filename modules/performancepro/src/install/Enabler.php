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

if (!defined('_PS_VERSION_')) {
    exit;
}

final class Enabler extends AbstractInstaller
{
    public function execute(): bool
    {
        return $this->registerHooks();
    }

    private function registerHooks(): bool
    {
        if (empty($this->hooks)) {
            return false;
        }

        foreach ($this->hooks as $hook) {
            if (!$this->module->registerHook($hook)) {
                $error = sprintf(
                    $this->module->l('Hook %s has not been installed.', $this->className),
                    $hook
                );

                $this->displayError($error);
            }
        }

        return true;
    }
}
