<?php namespace App\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'FolderCreateRequest',
    description: 'Data required to create a new folder'
)]
class FolderCreateRequest
{
    #[OA\Property(description: 'The name of the new folder', example: 'My Profile')]
    #[Assert\NotBlank(message: 'Profile name cannot be blank.')]
    #[Assert\Length(min: 1, max: 255, minMessage: 'Profile name must be at least {{ limit }} characters long.')]
    public string $name;

    #[OA\Property(description: 'The ID of the owner profile of the new folder', example: '456')]
    #[Assert\NotBlank(message: 'Profile cannot be blank.')]
    public int $profile;

    #[OA\Property(description: 'A numerical position for manually sorting folders', example: '0')]
    public int $sort = 0;

    #[OA\Property(description: 'A boolean value indicating if the folder is open', example: '1')]
    public int $open = 1;

    #[OA\Property(description: 'A boolean value indicating if the folder is hidden', example: '1')]
    public int $hidden = 0;

    #[OA\Property(description: 'The ID of the parent folder of the new folder', example: '789')]
    public ?int $parent = null;

    #[OA\Property(description: 'An optional description for the profile', nullable: true)]
    public ?string $description = null;
}