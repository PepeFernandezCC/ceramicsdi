<?php

namespace Revolut\Plugin\Services\Location;

use Revolut\Plugin\Infrastructure\Api\Locations\LocationDTO;

interface LocationServiceInterface
{
    public function create(string $domain): LocationDTO;
}
