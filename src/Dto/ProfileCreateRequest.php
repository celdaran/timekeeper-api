<?php namespace App\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'ProfileCreateRequest',
    description: 'Data required to create a new user Profile'
)]
class ProfileCreateRequest
{
    #[OA\Property(description: 'The name of the new Profile', example: 'My Profile')]
    #[Assert\NotBlank(message: 'Profile name cannot be blank.')]
    #[Assert\Length(min: 1, max: 255, minMessage: 'Profile name must be at least {{ limit }} characters long.')]
    public string $name;

    #[OA\Property(description: 'The ID of the owner Account of the new Profile', example: '123')]
    #[Assert\NotBlank(message: 'Account cannot be blank.')]
    public int $account;

    #[OA\Property(description: 'An optional description for the Profile', nullable: true)]
    public ?string $description = null;
}