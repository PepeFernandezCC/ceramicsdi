<?php

declare(strict_types=1);

namespace Revolut\Plugin\Services\Location;

use Exception;
use Revolut\Plugin\Infrastructure\Api\Locations\LocationsApiInterface;
use Revolut\Plugin\Infrastructure\Api\Locations\LocationDTO;
use Revolut\Plugin\Services\Config\Api\ConfigInterface;
use Revolut\Plugin\Types\ApiEnv;

class LocationService implements LocationServiceInterface
{
    /** @var LocationRepositoryInterface */
    private $repo;

    /** @var ConfigInterface */
    private $config;

    /** @var LocationsApiInterface */
    private $locationApi;

    public function __construct(
        LocationsApiInterface $locationApi,
        LocationRepositoryInterface $repo,
        ConfigInterface $config
    ) {
        $this->repo = $repo;
        $this->config = $config;
        $this->locationApi = $locationApi;
    }

    public function create(string $domain): LocationDTO
    {
        $locationId = $this->repo->findByDomain($domain, ApiEnv::of($this->config->getMode()));

        if ($locationId) {
            try {
                $location = $this->locationApi->retrieve($locationId->getValue());
                if ($location) {
                    return $location;
                }
            } catch (Exception $e) {
                $location = null;
            }
        }

        $location_name = str_replace(array( 'https://', 'http://' ), '', $domain);

        $location = $this->locationApi->retrieveLocationByName($location_name);

        if (!$location) {
            $location = $this->locationApi->create(
                array(
                    'name'    => $location_name,
                    'type'    => 'online',
                    'details' => array(
                        'domain' => $domain,
                    ),
                )
            );
        }


        $locationModel = new LocationModel($location->id, $domain, ApiEnv::of($this->config->getMode()));
        $this->repo->add($locationModel);

        return $location;
    }
}
