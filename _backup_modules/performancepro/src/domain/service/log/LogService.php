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

namespace PrestaShop\Module\PerformancePro\domain\service\log;

use Configuration;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use PrestaShop\Module\PerformancePro\domain\service\util\PathService;
use PrestaShop\Module\PerformancePro\resources\config\Config;

final class LogService
{
    /**
     * @var Logger
     */
    private static $logger;

    /**
     * Add error to line.
     *
     * @param array<int, array<string, mixed>> $context
     */
    public static function error(string $message, array $context = []): void
    {
        if (!Configuration::get('PP_LOG_EXCEPTIONS')) {
            return;
        }

        self::getLogger()->addError($message, $context);
    }

    /**
     * Configure Monolog to use a rotating file-system.
     */
    public static function getInstance(): void
    {
        $fileName = PathService::createPath(Config::getLogPath()) . Config::EXCEPTION_LOG;

        if (file_exists($fileName) && filesize($fileName) > 102400) {
            return;
        }

        $logger = new Logger(Config::MODULE_NAME);

        $logger->pushHandler(new RotatingFileHandler($fileName, 5));

        self::$logger = $logger;
    }

    /**
     * Returns the Monolog instance
     */
    private static function getLogger(): Logger
    {
        if (!self::$logger) {
            self::getInstance();
        }

        return self::$logger;
    }
}
