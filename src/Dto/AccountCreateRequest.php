<?php namespace App\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'AccountCreateRequest',
    description: 'Data required to create a new user Account'
)]
class AccountCreateRequest
{
    #[OA\Property(description: 'The user\'s chosen username', example: 'myusername')]
    #[Assert\NotBlank(message: 'Username cannot be blank.')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'Username must be at least {{ limit }} characters long.')]
    public string $username;

    #[OA\Property(description: 'The user\'s password', format: 'password', example: 'StrongPassword123')]
    #[Assert\NotBlank(message: 'Password cannot be blank.')]
    #[Assert\Length(min: 8, max: 255, minMessage: 'Password must be at least {{ limit }} characters long.')]
    public string $password;

    #[OA\Property(description: 'The user\'s email address', format: 'email', example: 'user@example.com')]
    #[Assert\NotBlank(message: 'Email cannot be blank.')]
    #[Assert\Email(message: 'The email "{{ value }}" is not a valid email.')]
    #[Assert\Length(min: 3, max: 320, minMessage: 'Email address must be at least {{ limit }} characters long.')]
    public string $email;

    #[OA\Property(description: 'An flag indicating whether this is an admin account')]
    public bool $admin = false;

    #[OA\Property(description: 'An optional description for the Account (useful if you have more than one)', nullable: true)]
    public ?string $description = null;
}