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

namespace PrestaShop\Module\PerformancePro\web\form;

if (!defined('_PS_VERSION_')) {
    exit;
}

abstract class AbstractForm
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var \Module
     */
    protected $module;

    public function __construct(\Module $module)
    {
        $this->className = (new \ReflectionClass($this))->getShortName();

        $this->module = $module;
    }

    /**
     * @return string[]
     */
    abstract public function getFields(): array;
}
