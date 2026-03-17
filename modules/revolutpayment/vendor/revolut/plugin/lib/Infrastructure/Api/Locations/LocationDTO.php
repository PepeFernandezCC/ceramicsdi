<?php

namespace Revolut\Plugin\Infrastructure\Api\Locations;

use Exception;
use Revolut\Plugin\Types\ApiId;

class LocationDTO
{
    /** @var ApiId */
    public $id;

    /** @var string */
    public $name;

    public function __construct(ApiId $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function fromArray(array $location): LocationDTO
    {
        $id = ApiId::of($location['id']);
        return new LocationDTO($id, $location['name']);
    }

    /**
     * @return LocationDTO[]
     */
    public static function fromList(array $locations)
    {
        $locationDTOList = [];

        foreach ($locations as $location) {
            $locationDTOList[] = self::fromArray($location);
        }

        return $locationDTOList;
    }
}
