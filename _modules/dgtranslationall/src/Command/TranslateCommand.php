<?php
/**
 * License limited to a single site, for use on another site please purchase a license for this module.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Dingedi.com
 * @copyright Copyright 2023 © Dingedi All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 * @category  Dingedi PrestaShop Modules
 */

namespace Dingedi\Dgtranslationall\Command;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle)
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}

class TranslateCommand extends Command
{
    protected function configure()
    {
        $this->setName('dgtranslationall:translate')
            ->addOption('from_lang', null, InputOption::VALUE_REQUIRED)
            ->addOption('dest_lang', null, InputOption::VALUE_REQUIRED)
            ->addOption('tables', null, InputOption::VALUE_REQUIRED)
            ->addOption('overwrite', null, InputOption::VALUE_OPTIONAL, 'Overwrite translations. "on" or "off" (default: off)', 'off')
            ->addOption('range', null, InputOption::VALUE_OPTIONAL, 'Range of ID to translate (default: off)', 'off');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach (['from_lang', 'dest_lang', 'tables'] as $option) {
            if (empty($input->getOption($option))) {
                throw new \Exception(sprintf('Option --%s must be set.', $option));
            }
        }

        require_once 'TranslateContentCron.php';

        \TranslateContentCron::translate(
            $input->getOption('from_lang'),
            $input->getOption('dest_lang'),
            $input->getOption('tables'),
            $input->getOption('overwrite'),
            $input->getOption('range')
        );

        $output->write('Data has been translated');

        return 0;
    }
}
