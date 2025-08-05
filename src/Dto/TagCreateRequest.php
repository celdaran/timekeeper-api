<?php namespace App\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'TagCreateRequest',
    description: 'Data required to create a new Tag'
)]
class TagCreateRequest
{
    #[OA\Property(description: 'The name of the new Tag', example: 'Of Interest')]
    #[Assert\NotBlank(message: 'Tag name cannot be blank.')]
    #[Assert\Length(min: 1, max: 255, minMessage: 'Tag name must be at least {{ limit }} characters long.')]
    public string $name;

    #[OA\Property(description: 'The ID of the containing folder of the new Tag', example: '123')]
    #[Assert\NotBlank(message: 'Account cannot be blank.')]
    public int $folder;

    #[OA\Property(description: 'A numerical position for manually sorting Tags', example: '0')]
    public int $sort = 0;

    #[OA\Property(description: 'An optional description for the Tag', nullable: true)]
    public ?string $description = null;
}