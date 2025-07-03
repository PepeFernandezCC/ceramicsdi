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

namespace PrestaShop\Module\PerformancePro\domain\service\validation;

use Module;
use PrestaShop\Module\PerformancePro\exception\PerformanceProValidationException;
use PrestaShop\Module\PerformancePro\resources\config\Config;
use Validate;

final class TextValidator implements Validator
{
    /**
     * @var string
     */
    private $field;

    public function __construct(string $value)
    {
        $this->field = $value;
    }

    /**
     * @throws PerformanceProValidationException
     */
    public function mustBeEmptyOrAnInteger(): self
    {
        if (!empty($this->field) && !Validate::isInt($this->field)) {
            $message = $this->getModuleInstance()->l('The value must be empty or an integer.');

            throw new PerformanceProValidationException($message);
        }

        return $this;
    }

    public function mustBeBetween(int $from, int $to, int $default): self
    {
        if ($this->field < $from || $this->field > $to || !Validate::isInt($this->field)) {
            $this->field = (string) $default;
        }

        return $this;
    }

    /**
     * @throws PerformanceProValidationException
     */
    public function mustBeAColor(): self
    {
        if (!Validate::isColor($this->field)) {
            $message = $this->getModuleInstance()->l('The value must be of a valid color format.');

            throw new PerformanceProValidationException($message);
        }

        return $this;
    }

    public function execute(): string
    {
        return $this->field;
    }

    private function getModuleInstance()
    {
        return Module::getInstanceByName(Config::MODULE_NAME);
    }
}
