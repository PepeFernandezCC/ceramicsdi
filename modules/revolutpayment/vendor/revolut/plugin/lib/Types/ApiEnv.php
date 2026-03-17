<?php

namespace Revolut\Plugin\Types;

use Exception;
use Revolut\Plugin\Infrastructure\Config\Api\Environment;
use Revolut\Plugin\Types\ValueObject;

class ApiEnv extends ValueObject
{
      /** @var string */
    private $env;

    /** @var array  */
    public const API_ENVS = [
        Environment::DEV,
        Environment::PROD,
        Environment::SANDBOX,
    ];

    private function __construct(string $env)
    {
        if (!in_array($env, self::API_ENVS)) {
            throw new Exception("Invalid api env value: $env");
        }

        $this->env = $env;
    }

    public function getValue(): string
    {
        return $this->env;
    }

    public function isProd(): bool
    {
        return $this->env == Environment::PROD;
    }

    public function isSandbox(): bool
    {
        return $this->env == Environment::SANDBOX;
    }

    public function isDev(): bool
    {
        return $this->env == Environment::DEV;
    }

    public static function of(string $method): ApiEnv
    {
        return new ApiEnv($method);
    }
}
