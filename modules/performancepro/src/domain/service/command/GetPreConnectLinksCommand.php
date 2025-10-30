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

use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\domain\service\provider\GooglePageSpeedProvider;
use PrestaShop\Module\PerformancePro\domain\service\util\LinkService;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class GetPreConnectLinksCommand implements Command
{
    /**
     * @var GooglePageSpeedProvider
     */
    private $googlePageSpeedProvider;

    public function __construct(GooglePageSpeedProvider $googlePageSpeedProvider)
    {
        $this->googlePageSpeedProvider = $googlePageSpeedProvider;
    }

    /**
     * @return array{result: bool, amount: int, content: string}
     */
    public function execute(): array
    {
        try {
            $userRelPreconnect = $this->googlePageSpeedProvider
                ->setUrl(LinkService::getBaseLink())
                ->getUserRelPreconnect();

            $amount = \is_array($userRelPreconnect) || $userRelPreconnect instanceof \Countable ? \count(
                $userRelPreconnect
            ) : 0;

            $content = implode('|', $userRelPreconnect);
        } catch (\Exception $exception) {
            LogService::error($exception->getMessage(), $exception->getTrace());

            $amount = 0;

            $content = '';
        }

        return [
            'amount' => $amount,
            'content' => $content,
        ];
    }
}
