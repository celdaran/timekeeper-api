<?php namespace App\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'FolderCreateRequest',
    description: 'Data required to create a new Folder'
)]
class FolderCreateRequest
{
    #[OA\Property(description: 'The name of the new Folder', example: 'Work Items')]
    #[Assert\NotBlank(message: 'Folder name cannot be blank.')]
    #[Assert\Length(min: 1, max: 255, minMessage: 'Folder name must be at least {{ limit }} characters long.')]
    public string $name;

    #[OA\Property(description: 'The ID of the owner Profile of the new Folder', example: '456')]
    #[Assert\NotBlank(message: 'Profile cannot be blank.')]
    public int $profile;

    #[OA\Property(description: 'A numerical position for manually sorting Folders', example: '0')]
    public int $sort = 0;

    #[OA\Property(description: 'A boolean value indicating if the Folder is a system-managed folder', example: '1')]
    public int $system = 0;

    #[OA\Property(description: 'A boolean value indicating if the Folder is open', example: '1')]
    public int $open = 1;

    #[OA\Property(description: 'A boolean value indicating if the Folder is hidden', example: '1')]
    public int $hidden = 0;

    #[OA\Property(description: 'The ID of the parent Folder', example: '789')]
    public ?int $parent = null;

    #[OA\Property(description: 'An optional description for the Folder', nullable: true)]
    public ?string $description = null;
}