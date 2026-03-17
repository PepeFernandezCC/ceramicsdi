<?php

namespace Revolut\Plugin\Services\Location;

use Revolut\Plugin\Types\ApiEnv;
use Revolut\Plugin\Types\ApiId;

class LocationModel
{
    /** @var string */
    private $domain;

    /** @var ApiId */
    private $revolutId;

    /** @var ApiEnv */
    private $apiEnv;

    public function __construct(
        ApiId $revolutId,
        string $domain,
        ApiEnv $apiEnv
    ) {
        $this->revolutId = $revolutId;
        $this->domain = $domain;
        $this->apiEnv = $apiEnv;
    }

    public function getRevolutId(): ApiId
    {
        return $this->revolutId;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getApiEnv(): ApiEnv
    {
        return $this->apiEnv;
    }
}
