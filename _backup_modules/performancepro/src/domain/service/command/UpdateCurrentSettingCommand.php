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

use PrestaShop\Module\PerformancePro\domain\service\db\DatabaseSettings;
use PrestaShop\Module\PerformancePro\resources\config\Database;

final class UpdateCurrentSettingCommand implements Command
{
    /**
     * @var DatabaseSettings
     */
    private $databaseSettings;

    /**
     * @var string
     */
    private $key;

    public function __construct(DatabaseSettings $databaseSettings, string $key)
    {
        $this->databaseSettings = $databaseSettings;

        $this->key = $key;
    }

    /**
     * @return array{value: int|string}
     */
    public function execute(): array
    {
        $value = Database::getDatabaseSettings()[$this->key];

        $this->databaseSettings->updateValue($this->key, (string) $value);

        return [
            'value' => $value,
        ];
    }
}
