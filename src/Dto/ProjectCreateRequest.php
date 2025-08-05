<?php namespace App\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'ProjectCreateRequest',
    description: 'Data required to create a new Project'
)]
class ProjectCreateRequest
{
    #[OA\Property(description: 'The name of the new Project', example: 'My Project')]
    #[Assert\NotBlank(message: 'Project name cannot be blank.')]
    #[Assert\Length(min: 1, max: 255, minMessage: 'Project name must be at least {{ limit }} characters long.')]
    public string $name;

    #[OA\Property(description: 'The ID of the containing folder of the new Project', example: '123')]
    #[Assert\NotBlank(message: 'Account cannot be blank.')]
    public int $folder;

    #[OA\Property(description: 'A numerical position for manually sorting Projects', example: '0')]
    public int $sort = 0;

    #[OA\Property(description: 'An optional external identifier for the Project', nullable: true)]
    public ?string $externalIdent = null;

    #[OA\Property(description: 'An optional external URL for the Project', nullable: true)]
    public ?string $externalUrl = null;

    #[OA\Property(description: 'An optional description for the Project', nullable: true)]
    public ?string $description = null;
}