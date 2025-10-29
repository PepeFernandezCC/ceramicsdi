<?php
/**
 * Copyright since 2022 Trusted shops
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to tech@202-ecommerce.com so we can send you a copy immediately.
 *
 * @author 202 ecommerce <tech@202-ecommerce.com>
 * @copyright 2022 Trusted shops
 * @license https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 *
 * This source file is loading the components connector.umd.js and eventsLib.js
 * (itself subject to the Trusted Shops EULA https://policies.etrusted.com/IE/en/plugin-licence.html)  to connect to Trusted Shops. For these components, you will find below a list of the open source libraries we use for our Services.
 * Please note that the following list may be subject to amendments and modifications, and does not thus claim (perpetual) exhaustiveness. You can always refer to the following website for up-to-date information on the open source software Trusted Shops uses:
 * https://policies.etrusted.com/IE/en/plugin-licence.html
 *
 * Name                Licence         Copyright Disclaimer
 * axios               MIT     Copyright (c) 2014-present (Matt Zabriskie)
 * babel               MIT     Copyright (c) 2014-present (Sebastian McKenzie and other Contributors)
 * follow-redirects    MIT     Copyright (c) 2014–present (Olivier Lalonde, James Talmage, Ruben Verborgh)
 * history             MIT     Copyright (c) 2016-2020 (React Training), Copyright (c) 2020-2021 (Remix Software)
 * hookform/resolvers  MIT     Copyright (c) 2019-present (Beier(Bill) Luo)
 * inherits            ISC     Copyright (c) 2011-2022 (Isaac Z. Schlueter)
 * js-tokens           MIT     Copyright (c) 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021 (Simon Lydell)
 * lodash              MIT     Copyright (c) (OpenJS Foundation and other contributors (https://openjsf.org/)
 * lodash-es           MIT     Copyright (c) (OpenJS Foundation and other contributors (https://openjsf.org/)
 * loose-envify        MIT     Copyright (c) 2015 (Andreas Suarez)
 * nanoclone           MIT     Copyright (c) 2017 (Anton Kosykh)
 * path                MIT     Copyright (c) (Joyent, Inc. and other Node contributors.)
 * preact              MIT     Copyright (c) 2015-present (Jason Miller)
 * preact-router       MIT     Copyright (c) 2015 (Jason Miller)
 * process             MIT     Copyright (c) 2013 (Roman Shtylman)
 * property-expr       MIT     Copyright (c) 2014 (Jason Quense)
 * react-hook-form     MIT     Copyright (c) 2019-present (Beier(Bill) Luo)
 * regenerator-runtime MIT     Copyright (c) 2014-present (Facebook, Inc.)
 * resolve-pathname    MIT     Copyright (c) 2016-2018 (Michael Jackson)
 * tiny-invariant      MIT     Copyright (c) 2019 (Alexander Reardon)
 * tiny-warning        MIT     Copyright (c) 2019 (Alexander Reardon)
 * toposort            MIT     Copyright (c) 2012 (Marcel Klehr)
 * types/lodash        MIT     (none)
 * util                MIT     Copyright (c) (Joyent, Inc. and other Node contributors)
 * value-equal         MIT     Copyright (c) 2016-2018 (Michael Jackson)
 * yup                 MIT     Copyright (c) 2014 (Jason Quense)
 * zustand             MIT     Copyright (c) 2019 (Paul Henschel)
 */

namespace TrustedshopsAddon\API\Logger;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ApiLogger
{
    public $logname;

    const LOGGER_NAME = 'trustedshops';

    const MAX_LOG_FILE_SIZE = 2000000000;

    private static $instance = null;

    private $stream;

    /**
     * @var Logger
     */
    private $logger;

    final protected function __construct()
    {
        $date = date('Ymd');
        $hashOfDay = md5(_COOKIE_KEY_ . $date);
        $this->logname = 'trustedshops-' . $date . '-' . $hashOfDay . '.log';
        $this->build();
    }

    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton.');
    }

    protected function build()
    {
        $logFile = _PS_MODULE_DIR_ . 'trustedshopseasyintegration/logs/' . $this->logname;
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile));
        }
        if (file_exists($logFile)) {
            $fileSize = filesize($logFile);
            if ($fileSize > self::MAX_LOG_FILE_SIZE) {
                unlink($logFile);
            }
        }
        $dateSixDaysAgo = date('Ymd', strtotime('-6 days'));
        $hashOfDay = md5(_COOKIE_KEY_ . $dateSixDaysAgo);
        $logFileNameSixDaysAgo = 'trustedshops-' . $dateSixDaysAgo . '-' . $hashOfDay . '.log';
        $logFileSixDaysAgo = _PS_MODULE_DIR_ . 'trustedshopseasyintegration/logs/' . $logFileNameSixDaysAgo;
        if (file_exists($logFileSixDaysAgo)) {
            unlink($logFileSixDaysAgo);
        }
        $this->stream = fopen($logFile, 'a+');
        $this->logger = new Logger(self::LOGGER_NAME, [new StreamHandler($this->stream)]);
    }

    /**
     * @param LogModel $logModel
     *
     * @return void
     */
    public function log($logModel)
    {
        $this->logger->addInfo(json_encode($logModel->toArray()));
    }

    /**
     * @param string $info
     *
     * @return void
     */
    public function logInfo($info)
    {
        $this->logger->addInfo($info);
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function __destruct()
    {
        fclose($this->stream);
    }
}
