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

final class TextAreaValidator implements Validator
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var array<string>
     */
    private $array = [];

    /**
     * @var string
     */
    private $separator;

    public function __construct(string $value)
    {
        $this->field = $value;
    }

    public function removeEmptyKeys(): self
    {
        $this->array = array_filter($this->array);

        return $this;
    }

    public function removeInvalidUrls(): self
    {
        $temp = [];

        foreach ($this->array as $singleArray) {
            if (\Validate::isUrl($singleArray)) {
                $temp[] = $singleArray;
            }
        }

        $this->array = $temp;

        return $this;
    }

    public function removeDuplicates(): self
    {
        $this->array = \Tools::arrayUnique($this->array);

        return $this;
    }

    public function execute(): string
    {
        return $this->arrayToText();
    }

    private function arrayToText(): string
    {
        return implode($this->separator, $this->array);
    }

    public function removeWhitespace(): self
    {
        $temp = [];

        foreach ($this->array as $singleArray) {
            $temp[] = trim($singleArray);
        }

        $this->array = $temp;

        return $this;
    }

    public function setSeparator(string $separator): self
    {
        $this->separator = $separator;

        $this->array = explode($separator, $this->field);

        return $this;
    }
}
