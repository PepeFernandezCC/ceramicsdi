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

use PrestaShop\Module\PerformancePro\data\repository\DatabaseCleanerRepository;
use PrestaShop\Module\PerformancePro\data\repository\DatabaseOptimizerRepository;
use PrestaShop\Module\PerformancePro\domain\service\cache\CacheWarmer;
use PrestaShop\Module\PerformancePro\domain\service\cache\ClearCache;
use PrestaShop\Module\PerformancePro\domain\service\command\CacheWarmerCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ChangeEngineToInnoDbCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\CleanTablesCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ClearApcCacheCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ClearCacheCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ClearCartRuleTableCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ClearCartTableCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ClearConnectionTablesCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ClearHTTPCacheCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ClearImageTmpDirCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ClearImgCacheCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ClearLogsCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ClearLogTableCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ClearMailTableCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ClearMediaCacheCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ClearOpCacheCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ClearPageNotFoundCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ClearStatsSearchTableCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ClearXmlCacheCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ConfigurationUpdateCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ConfigurationUpdateConfigValueCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\CronRemoteControl;
use PrestaShop\Module\PerformancePro\domain\service\command\DeleteBrokenImagesCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\DeleteEmptyImageFolderCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\DeleteExpiredSpecificPricesCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\DeleteUnusedImagesCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\FlushQueryCacheCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\GetPreConnectLinksCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\GetPrefetchLinkCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\OptimizeTablesCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\RepairTablesCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\ResetQueryCacheCommand;
use PrestaShop\Module\PerformancePro\domain\service\command\UpdateCurrentSettingCommand;
use PrestaShop\Module\PerformancePro\domain\service\db\DatabaseCleaner;
use PrestaShop\Module\PerformancePro\domain\service\db\DatabaseConfiguration;
use PrestaShop\Module\PerformancePro\domain\service\db\DatabaseOptimizer;
use PrestaShop\Module\PerformancePro\domain\service\db\DatabaseSettings;
use PrestaShop\Module\PerformancePro\domain\service\image\ImageCleaner;
use PrestaShop\Module\PerformancePro\domain\service\log\LogService;
use PrestaShop\Module\PerformancePro\domain\service\provider\GooglePageSpeedProvider;
use PrestaShop\Module\PerformancePro\domain\service\util\DefineValueService;
use PrestaShop\Module\PerformancePro\resources\config\Config;
use PrestaShop\Module\PerformancePro\web\util\View;

final class PerformanceProCronModuleFrontController extends ModuleFrontController
{
    /**
     * @var bool
     */
    public $ssl = true;

    public function __construct()
    {
        $this->cronRemoteControl = new CronRemoteControl();

        $this->className = 'cron';

        parent::__construct();
    }

    public function displayAjax(): void
    {
        header('Access-Control-Allow-Origin: *');

        $this->verifyAccess('ajax');

        $content = json_encode($this->runAjax());

        try {
            $this->ajaxRender($content);
        } catch (PrestaShopException $prestaShopException) {
            LogService::error($prestaShopException->getMessage(), $prestaShopException->getTrace());
        }
    }

    public function display(): void
    {
        $this->runAsCron();
    }

    public function formatStrong(string $text): string
    {
        return $text;
    }

    private function verifyAccess(string $key): void
    {
        if (!Tools::isPHPCLI()) {
            $token = Tools::hashIV(Config::MODULE_NAME . '/' . $key . Tools::getValue('name'));

            $isValidToken = $token !== Tools::getValue('token');

            $isModuleInstalled = Module::isInstalled(Config::MODULE_NAME);

            if ($isValidToken || !$isModuleInstalled) {
                exit($this->module->l('Forbidden call.', $this->className));
            }
        }
    }

    /**
     * @return array<string, string>|string[]
     */
    private function runAjax(): array
    {
        if (Config::DEMO_MODE) {
            return [
                'result' => $this->module->l('Configuration has been disabled in demo version.', $this->className),
            ];
        }

        $name = (string) Tools::getValue('name');

        $key = (string) Tools::getValue('key');

        return $this->caller($name, $key);
    }

    /**
     * @return array<string, string>|string[]
     */
    private function caller(string $name, string $key): array
    {
        $name = $this->dashesToCamelCase($name);

        if (method_exists($this, $name)) {
            return $this->$name($key);
        }

        return [
            'result' => $this->module->l('The command does not exist.', $this->className),
        ];
    }

    private function dashesToCamelCase(string $string): string
    {
        return lcfirst(str_replace('-', '', ucwords($string, '-')));
    }

    private function runAsCron(): void
    {
        $this->module->cron = true;

        $this->verifyAccess('cron');

        $stopTime = $this->startTime();

        $this->runAjax();

        $executionTime = $stopTime();

        try {
            $this->ajaxRender(
                sprintf(
                    $this->module->l('%s Total execution time: %s sec.', $this->className),
                    'Success.',
                    $executionTime
                )
            );
        } catch (PrestaShopException $prestaShopException) {
            LogService::error($prestaShopException->getMessage(), $prestaShopException->getTrace());
        }
    }

    private function startTime(): Closure
    {
        $startTime = microtime(true);

        return static function () use ($startTime): string {
            return number_format(microtime(true) - $startTime, 2);
        };
    }

    /**
     * @return array{result: string}
     */
    private function buildCache(): array
    {
        $sitemaps = explode(config::PIPE_SEPARATOR, Configuration::get('PP_CACHE_WARMER_SITEMAPS'));

        $response = $this->cronRemoteControl
            ->setCommand(new CacheWarmerCommand(new CacheWarmer($sitemaps)))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s pages warmed up.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function clearSmartyCacheAndSfCache(): array
    {
        $this->cronRemoteControl
            ->setCommand(new ClearCacheCommand(new ClearCache()))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('Smarty- and SF-cache cleared.', $this->className),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function clearImageCache(): array
    {
        $this->cronRemoteControl
            ->setCommand(new ClearImgCacheCommand(new ClearCache()))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('Image cache cleared.', $this->className),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function clearHttpCache(): array
    {
        $this->cronRemoteControl
            ->setCommand(new ClearHTTPCacheCommand($this->module))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('HTTP cache cleared.', $this->className),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function clearMediaCache(): array
    {
        $this->cronRemoteControl
            ->setCommand(new ClearMediaCacheCommand(new ClearCache()))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('Theme cache cleared.', $this->className),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function clearXmlCache(): array
    {
        $this->cronRemoteControl
            ->setCommand(new ClearXmlCacheCommand(new ClearCache()))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('XML cache cleared.', $this->className),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function clearOpCache(): array
    {
        $this->cronRemoteControl
            ->setCommand(new ClearOpCacheCommand(new ClearCache()))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('OP cache cleared.', $this->className),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function clearApcCache(): array
    {
        $this->cronRemoteControl
            ->setCommand(new ClearApcCacheCommand(new ClearCache()))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('APC cache cleared.', $this->className),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function clearLogs(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new ClearLogsCommand(new ClearCache()))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s log(s) removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function cleanTables(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new CleanTablesCommand(new DatabaseCleaner(new DatabaseCleanerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s row(s) fixed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function clearCartTable(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new ClearCartTableCommand(new DatabaseCleaner(new DatabaseCleanerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s cart(s) removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function clearStatsSearchTable(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new ClearStatsSearchTableCommand(new DatabaseCleaner(new DatabaseCleanerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s row(s) removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function clearLogTable(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new ClearLogTableCommand(new DatabaseCleaner(new DatabaseCleanerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s row(s) removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function clearMailTable(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new ClearMailTableCommand(new DatabaseCleaner(new DatabaseCleanerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s row(s) removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function clearPageNotFoundTable(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new ClearPageNotFoundCommand(new DatabaseCleaner(new DatabaseCleanerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s row(s) removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function clearConnectionTables(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new ClearConnectionTablesCommand(new DatabaseCleaner(new DatabaseCleanerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s row(s) removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function resetQueryCache(): array
    {
        $this->cronRemoteControl
            ->setCommand(new ResetQueryCacheCommand(new ClearCache()))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('Query cache has been reset.', $this->className),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function flushQueryCache(): array
    {
        $this->cronRemoteControl
            ->setCommand(new FlushQueryCacheCommand(new ClearCache()))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('Query cache flushed.', $this->className),
        ];
    }

    /**
     * @return array{result: string, amount: string}
     */
    private function updateDbValue(string $key): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new UpdateCurrentSettingCommand(new DatabaseSettings(), $key))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('The setting %s has been updated to %s.', $this->className),
                View::formatStrong($key),
                View::formatStrong((string) $response['value'])
            ),
            'amount' => $response['value'],
        ];
    }

    /**
     * @return array{result: string, content: string}
     */
    private function getPrefetchLink(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new GetPrefetchLinkCommand(new GooglePageSpeedProvider()))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s link(s) has been added.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
            'content' => $response['content'],
        ];
    }

    /**
     * @return array{result: string}
     */
    private function changeEngineToInnodb(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new ChangeEngineToInnoDbCommand(new DatabaseOptimizer(new DatabaseOptimizerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s table(s) has been converted to InnoDb.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function repairTables(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new RepairTablesCommand(new DatabaseOptimizer(new DatabaseOptimizerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s table(s) has been repaired.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function optimizeTables(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new OptimizeTablesCommand(new DatabaseOptimizer(new DatabaseOptimizerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s table(s) has been optimized.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function clearCartRuleTable(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new ClearCartRuleTableCommand(new DatabaseCleaner(new DatabaseCleanerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s row(s) has been removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function deleteEmptyImagesFolder(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new DeleteEmptyImageFolderCommand(new ImageCleaner()))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s folder(s) has been removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function deleteExpiredSpecificPrices(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new DeleteExpiredSpecificPricesCommand(new DatabaseCleaner(new DatabaseCleanerRepository())))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s row(s) has been removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function deleteBrokenImages(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new DeleteBrokenImagesCommand(new ImageCleaner()))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s row(s) has been removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function clearImageTmpDir(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new ClearImageTmpDirCommand(new ImageCleaner()))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s image(s) has been removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function deleteUnusedImages(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new DeleteUnusedImagesCommand(new ImageCleaner()))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s image(s) has been removed.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
        ];
    }

    /**
     * @return array{result: string, content: string}
     */
    private function getPreConnectLinks(): array
    {
        $response = $this->cronRemoteControl
            ->setCommand(new GetPreConnectLinksCommand(new GooglePageSpeedProvider()))
            ->execute()
            ->getResponse();

        return [
            'result' => sprintf(
                $this->module->l('%s link(s) has been added.', $this->className),
                View::formatStrong((string) $response['amount'])
            ),
            'content' => $response['content'],
        ];
    }

    /**
     * @return array{result: string}
     */
    private function configurationUpdate(string $key): array
    {
        $this->cronRemoteControl
            ->setCommand(new ConfigurationUpdateCommand(new DatabaseConfiguration(), $key))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('The setting has been updated.', $this->className),
        ];
    }

    /**
     * @return array{result: string}
     */
    private function configurationUpdateConfigValue(string $key): array
    {
        $this->cronRemoteControl
            ->setCommand(new ConfigurationUpdateConfigValueCommand(new DefineValueService(), $key))
            ->execute()
            ->getResponse();

        return [
            'result' => $this->module->l('The setting has been updated.', $this->className),
        ];
    }
}
