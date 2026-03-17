<?php

namespace Revolut\Plugin\Services\Location;

use Revolut\Plugin\Types\ApiEnv;
use Revolut\Plugin\Types\ApiId;

interface LocationRepositoryInterface
{
    public function findByDomain(string $domain, ApiEnv $env): ?ApiId;
    public function add(LocationModel $model): void;
}
