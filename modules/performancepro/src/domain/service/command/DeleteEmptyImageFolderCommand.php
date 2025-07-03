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

use PrestaShop\Module\PerformancePro\domain\service\image\ImageCleaner;

final class DeleteEmptyImageFolderCommand implements Command
{
    /**
     * @var ImageCleaner
     */
    private $imageCleaner;

    public function __construct(ImageCleaner $imageCleaner)
    {
        $this->imageCleaner = $imageCleaner;
    }

    /**
     * @return array{result: bool, amount: int}
     */
    public function execute(): array
    {
        $amount = $this->imageCleaner->deleteEmptyImagesFolder();

        return [
            'amount' => $amount,
        ];
    }
}
