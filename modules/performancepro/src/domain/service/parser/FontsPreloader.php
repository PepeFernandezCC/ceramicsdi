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

namespace PrestaShop\Module\PerformancePro\domain\service\parser;

final class FontsPreloader extends AbstractResourceHeader
{
    /**
     * @return array<string>
     */
    public function getPreloadLinks(): array
    {
        return $this->textAreaToArray('PP_PRELOAD_FONTS_TEXT');
    }
}
