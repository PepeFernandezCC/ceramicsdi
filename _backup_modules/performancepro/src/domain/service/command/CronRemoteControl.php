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
