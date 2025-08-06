<?php namespace App\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'ProjectGroupCreateRequest',
    description: 'Data required to create a new Project Group'
)]
class ProjectGroupCreateRequest
{
    #[OA\Property(description: 'The name of the new Project Group', example: 'Billable')]
    #[Assert\NotBlank(message: 'Project Group name cannot be blank.')]
    #[Assert\Length(min: 1, max: 255, minMessage: 'Project Group name must be at least {{ limit }} characters long.')]
    public string $name;

    #[OA\Property(description: 'The ID of the owner Account of the new Project Group', example: '123')]
    #[Assert\NotBlank(message: 'Account cannot be blank.')]
    public int $account;

    #[OA\Property(description: 'An optional description for the Tag', nullable: true)]
    public ?string $description = null;
}