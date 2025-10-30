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

namespace PrestaShop\Module\PerformancePro\domain\service\command;

use PrestaShop\Module\PerformancePro\domain\service\util\DefineValueService;
use PrestaShop\Module\PerformancePro\exception\PerformanceProDefineValueException;
use PrestaShop\Module\PerformancePro\resources\config\Database;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class ConfigurationUpdateConfigValueCommand implements Command
{
    /**
     * @var DefineValueService
     */
    private $defineValueService;

    /**
     * @var string
     */
    private $key;

    public function __construct(DefineValueService $defineValueService, string $key)
    {
        $this->defineValueService = $defineValueService;

        $this->key = $key;
    }

    /**
     * @return array{result: bool}
     *
     * @throws PerformanceProDefineValueException
     */
    public function execute(): array
    {
        $value = Database::getConfigValues()[$this->key];

        $this->defineValueService->updateValue($this->key, $value);

        return [];
    }
}
