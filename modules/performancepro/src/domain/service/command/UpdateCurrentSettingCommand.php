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

use PrestaShop\Module\PerformancePro\domain\service\db\DatabaseSettings;
use PrestaShop\Module\PerformancePro\resources\config\Database;

if (!defined('_PS_VERSION_')) {
    exit;
}

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
