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

final class LinkPreconnector extends AbstractResourceHeader
{
    /**
     * @return array<string>
     */
    public function getPreconnectLinks(): array
    {
        return $this->textAreaToArray('PP_PRECONNECT_LINKS_TEXT');
    }
}
