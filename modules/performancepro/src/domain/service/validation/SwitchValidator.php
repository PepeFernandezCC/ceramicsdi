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

namespace PrestaShop\Module\PerformancePro\domain\service\validation;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class SwitchValidator implements Validator
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function disableIfEmptyField(string $value): self
    {
        if (empty(\Tools::getValue($value))) {
            $this->value = '';
        }

        return $this;
    }

    public function disableIfFalse(string $value): self
    {
        if (!\Configuration::get($value)) {
            $this->value = '';
        }

        return $this;
    }

    public function execute(): string
    {
        return $this->value;
    }
}
