<?php namespace App\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'ProjectGroupLinkRequest',
    description: 'Data required to create a new link between Project and Project Group'
)]
class ProjectGroupLinkRequest
{
    #[OA\Property(description: 'The ID of the Project Group', example: '123')]
    #[Assert\NotBlank(message: 'Project Group cannot be blank.')]
    public int $project_group;

    #[OA\Property(description: 'The ID of the Project', example: '456')]
    #[Assert\NotBlank(message: 'Project cannot be blank.')]
    public int $project;
}