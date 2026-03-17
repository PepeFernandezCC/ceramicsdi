<?php

namespace Revolut\Plugin\Services\Customer;

use Revolut\Plugin\Types\ApiEnv;
use Revolut\Plugin\Types\ApiId;

class CustomerModel
{
    /** @var string */
    private $platformId;

    /** @var ApiId */
    private $revolutId;

    /** @var ApiEnv */
    private $apiEnv;

    public function __construct(
        ApiId $revolutId,
        string $platformId,
        ApiEnv $apiEnv
    ) {
        $this->revolutId = $revolutId;
        $this->platformId = $platformId;
        $this->apiEnv = $apiEnv;
    }

    public function setRevolutId(ApiId $revolutId): void
    {
        $this->revolutId = $revolutId;
    }

    public function getRevolutId(): ApiId
    {
        return $this->revolutId;
    }

    public function getPlatformId(): string
    {
        return $this->platformId;
    }

    public function getApiEnv(): ApiEnv
    {
        return $this->apiEnv;
    }
}
