<?php

namespace Revolut\Plugin\Infrastructure\Api\Locations;

use Revolut\Plugin\Infrastructure\Api\MerchantApiClientInterface;

class LocationsApi implements LocationsApiInterface
{
    private $merchantApiClient;

    public function __construct(MerchantApiClientInterface $merchantApiClient)
    {
        $this->merchantApiClient = $merchantApiClient;
    }

    public function create(array $location): LocationDTO
    {
        $location = $this->merchantApiClient->post('/locations', $location);
        return LocationDTO::fromArray($location);
    }

    public function retrieveLocationByName(string $location_name): ?LocationDTO
    {

        $locations = $this->retrieveLocations();

        foreach ($locations as $location) {
            if ($location->name == $location_name) {
                return $location;
            }
        }

        return null;
    }

    public function retrieve(string $id): ?LocationDTO
    {
        $location = $this->merchantApiClient->get("/locations/$id");
        if (empty($location['id'])) {
            return null;
        }
        return LocationDTO::fromArray($location);
    }

    /**
     * @return LocationDTO[]
     */
    public function retrieveLocations()
    {
        $locations = $this->merchantApiClient->get('/locations');
        return LocationDTO::fromList($locations);
    }
}
