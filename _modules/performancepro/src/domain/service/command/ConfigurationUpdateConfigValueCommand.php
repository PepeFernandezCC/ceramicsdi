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

namespace PrestaShop\Module\PerformancePro\domain\service\command;

use PrestaShop\Module\PerformancePro\domain\service\util\DefineValueService;
use PrestaShop\Module\PerformancePro\exception\PerformanceProDefineValueException;
use PrestaShop\Module\PerformancePro\resources\config\Database;

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
     * @throws PerformanceProDefineValueException
     *
     * @return array{result: bool}
     */
    public function execute(): array
    {
        $value = Database::getConfigValues()[$this->key];

        $this->defineValueService->updateValue($this->key, $value);

        return [];
    }
}
