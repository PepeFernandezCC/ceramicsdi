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

use PrestaShop\Module\PerformancePro\domain\service\db\DatabaseConfiguration;
use PrestaShop\Module\PerformancePro\resources\config\Database;

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
