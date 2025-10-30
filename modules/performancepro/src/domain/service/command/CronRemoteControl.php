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

if (!defined('_PS_VERSION_')) {
    exit;
}

final class CronRemoteControl
{
    /**
     * @var object
     */
    private $command;

    /**
     * @var array
     */
    private $response = [];

    public function setCommand($command): self
    {
        $this->command = $command;

        return $this;
    }

    public function execute(): self
    {
        $this->response = $this->command->execute();

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getResponse(): array
    {
        return $this->response;
    }
}
