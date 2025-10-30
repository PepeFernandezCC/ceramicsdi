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

use PrestaShop\Module\PerformancePro\domain\service\db\DatabaseConfiguration;
use PrestaShop\Module\PerformancePro\resources\config\Database;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class ConfigurationUpdateCommand implements Command
{
    /**
     * @var DatabaseConfiguration
     */
    private $databaseConfiguration;

    /**
     * @var string
     */
    private $key;

    public function __construct(DatabaseConfiguration $databaseConfiguration, string $key)
    {
        $this->databaseConfiguration = $databaseConfiguration;

        $this->key = $key;
    }

    /**
     * @return array{result: bool}
     */
    public function execute(): array
    {
        $value = Database::getSystemSettings()[$this->key];

        $this->databaseConfiguration->updateValue($this->key, $value);

        return [];
    }
}
