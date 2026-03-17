<?php

namespace Revolut\Plugin\Services\Config\Api;

use Revolut\Plugin\Types\CaptureMode;

interface ConfigInterface
{
    public function getMode(): string;

    public function getClientId(): string;

    public function getOAuthEndpoint(): string;

    public function getConnectServerUrl(): string;

    public function getBaseUrl(): string;

    public function setBaseUrl($baseUrl): void;

    public function setSecretKey(string $secretKey): void;

    public function getSecretKey(): string;

    public function setPublicKey(string $publicKey): void;

    public function getPublicKey(): string;

    public function isLive(): bool;

    public function isDev(): bool;

    public function isSandbox(): bool;

    public function setCaptureMode(string $captureMode);

    public function getCaptureMode(): CaptureMode;
}
