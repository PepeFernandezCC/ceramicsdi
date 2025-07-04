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

use Countable;
use Exception;
use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\domain\service\provider\GooglePageSpeedProvider;
use PrestaShop\Module\PerformancePro\domain\service\util\LinkService;

final class GetPrefetchLinkCommand implements Command
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
            $results = $this->googlePageSpeedProvider
                ->setUrl(LinkService::getBaseLink())
                ->getFontDisplay();

            $amount = \is_array($results) || $results instanceof Countable ? \count($results) : 0;

            $content = implode('|', $results);
        } catch (Exception $exception) {
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
