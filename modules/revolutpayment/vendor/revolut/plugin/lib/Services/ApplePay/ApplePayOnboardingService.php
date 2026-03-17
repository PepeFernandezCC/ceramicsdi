<?php

namespace Revolut\Plugin\Services\ApplePay;

use Revolut\Plugin\Infrastructure\Api\ApplePay\ApplePayApiInterface;
use Revolut\Plugin\Infrastructure\FileSystem\FileSystemInterface;
use Revolut\Plugin\Services\Log\RLog;

class ApplePayOnboardingService implements ApplePayOnboardingInterface
{
    public const ONBOARDING_FILE_REMOTE_DOWNLOAD_LINK = 'https://assets.revolut.com/api-docs/merchant-api/files/apple-developer-merchantid-domain-association';
    public const ONBOARDING_FILE_NAME = 'apple-developer-merchantid-domain-association';

    private $fileSystem;
    private $onboardingFilePath;
    private $onboardingFileDir;
    private $applePayApi;
    public function __construct(FileSystemInterface $fileSystemAdapter, ApplePayApiInterface $applePayApi)
    {
        $this->fileSystem = $fileSystemAdapter;
        $this->onboardingFileDir = rtrim($this->fileSystem->getRootDir(), '/') . '/.well-known/';
        $this->onboardingFilePath = $this->onboardingFileDir . self::ONBOARDING_FILE_NAME;
        $this->applePayApi = $applePayApi;
    }

    public function downloadOnboardingCertificate()
    {
        if (!$this->fileSystem->fileExists($this->onboardingFileDir) && ! $this->fileSystem->makeDirectory($this->onboardingFileDir)) {
            throw new \Exception("ApplePayOnboardingService, Unable to create .well-known folder");
        }

        $onboardingCertificateContent = $this->fileSystem->readFile(self::ONBOARDING_FILE_REMOTE_DOWNLOAD_LINK);

        if (! $this->fileSystem->writeFile($this->onboardingFilePath, $onboardingCertificateContent)) {
            throw new \Exception("ApplePayOnboardingService, Unable to write onboarding file : " . $this->onboardingFilePath);
        }

        return true;
    }

    public function removeOnboardingCertificate()
    {
        if (! $this->fileSystem->fileExists($this->onboardingFilePath)) {
            throw new \Exception("ApplePayOnboardingService, unable to remove onboarding file because it does not exist");
        }

        if (! $this->fileSystem->deleteFile($this->onboardingFilePath)) {
            throw new \Exception("ApplePayOnboardingService, unable to remove onboarding file");
        }

        return true;
    }

    public function onBoardDomain($domain)
    {
        $this->downloadOnboardingCertificate();
        $this->applePayApi->registerDomain($domain);
        $this->removeOnboardingCertificate();
        return true;
    }
}
