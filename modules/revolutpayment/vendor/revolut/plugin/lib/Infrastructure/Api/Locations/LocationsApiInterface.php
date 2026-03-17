<?php

namespace Revolut\Plugin\Infrastructure\Api\Locations;

interface LocationsApiInterface
{
    public function create(array $location): LocationDTO;
    public function retrieve(string $id): ?LocationDTO;
    public function retrieveLocationByName(string $location_name): ?LocationDTO;

    /**
     * @return LocationDTO[]
     */
    public function retrieveLocations();
}
