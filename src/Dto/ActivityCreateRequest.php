<?php namespace App\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'ActivityCreateRequest',
    description: 'Data required to create a new Activity'
)]
class ActivityCreateRequest
{
    #[OA\Property(description: 'The name of the new Activity', example: 'My Activity')]
    #[Assert\NotBlank(message: 'Activity name cannot be blank.')]
    #[Assert\Length(min: 1, max: 255, minMessage: 'Activity name must be at least {{ limit }} characters long.')]
    public string $name;

    #[OA\Property(description: 'The ID of the containing folder of the new Activity', example: '123')]
    #[Assert\NotBlank(message: 'Account cannot be blank.')]
    public int $folder;

    #[OA\Property(description: 'A numerical position for manually sorting Activities', example: '0')]
    public int $sort = 0;

    #[OA\Property(description: 'An optional description for the Activity', nullable: true)]
    public ?string $description = null;
}