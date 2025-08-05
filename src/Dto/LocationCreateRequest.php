<?php namespace App\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'LocationCreateRequest',
    description: 'Data required to create a new Location'
)]
class LocationCreateRequest
{
    #[OA\Property(description: 'The name of the new Location', example: 'Home or Work')]
    #[Assert\NotBlank(message: 'Location name cannot be blank.')]
    #[Assert\Length(min: 1, max: 255, minMessage: 'Location name must be at least {{ limit }} characters long.')]
    public string $name;

    #[OA\Property(description: 'The ID of the containing folder of the new Location', example: '123')]
    #[Assert\NotBlank(message: 'Account cannot be blank.')]
    public int $folder;

    #[OA\Property(description: 'A numerical position for manually sorting Locations', example: '0')]
    public int $sort = 0;

    #[OA\Property(description: 'The ID of the time zone to be associated with the location', example: '337')]
    public int $timeZone = 337;

    #[OA\Property(description: 'An optional description for the Location', nullable: true)]
    public ?string $description = null;
}