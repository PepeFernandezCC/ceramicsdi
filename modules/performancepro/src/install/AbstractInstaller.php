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

namespace PrestaShop\Module\PerformancePro\install;

use PrestaShop\Module\PerformancePro\resources\config\Field;
use PrestaShop\Module\PerformancePro\resources\config\Hook;

if (!defined('_PS_VERSION_')) {
    exit;
}

abstract class AbstractInstaller
{
    /**
     * @var \Module
     */
    protected $module;

    /**
     * @var string[]
     */
    protected $hooks = [];

    /**
     * @var bool[]
     */
    protected $fieldValues = [];

    /**
     * @var string
     */
    protected $className;

    /**
     * @var string[]
     */
    public function __construct(\Module $module)
    {
        $this->module = $module;

        $this->hooks = Hook::getHooks();

        $this->fieldValues = Field::getFieldValues();

        $this->className = (new \ReflectionClass($this))->getShortName();
    }

    abstract public function execute(): bool;

    protected function displayError(string $error): void
    {
        http_response_code(400);

        exit($error);
    }
}
