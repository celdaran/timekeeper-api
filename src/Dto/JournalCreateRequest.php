<?php namespace App\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: 'JournalCreateRequest',
    description: 'Data required to create a new Journal entry'
)]
class JournalCreateRequest
{
    #[OA\Property(description: 'The Profile ID for the new Journal entry', example: '1')]
    #[Assert\NotBlank(message: 'Profile cannot be blank.')]
    public int $profile;

    #[OA\Property(description: 'The start time of the Journal entry in ISO-8601 format', example: 'YYYY-MM-DDThh:mm:ss±HH:MM')]
    #[Assert\NotBlank(message: 'Start time cannot be blank.')]
    #[Assert\Length(min: 25, max: 25, minMessage: 'Start time must be in the following format: YYYY-MM-DDThh:mm:ss±HH:MM')]
    public string $startTime;

    #[OA\Property(description: 'The stop or end time of the Journal entry in ISO-8601 format', example: 'YYYY-MM-DDThh:mm:ss±HH:MM')]
    #[Assert\NotBlank(message: 'Stop time cannot be blank.')]
    #[Assert\Length(min: 25, max: 25, minMessage: 'Stop time must be in the following format: YYYY-MM-DDThh:mm:ss±HH:MM')]
    public string $stopTime;

    #[OA\Property(description: 'The free-text body of the Journal Entry', nullable: true)]
    public ?string $memo = null;

    #[OA\Property(description: 'The ID of the Project', example: '456')]
    #[Assert\NotBlank(message: 'Project cannot be blank.')]
    public int $project;

    #[OA\Property(description: 'The ID of the Activity', example: '456')]
    #[Assert\NotBlank(message: 'Activity cannot be blank.')]
    public int $activity;

    #[OA\Property(description: 'The ID of the Location', example: '456')]
    #[Assert\NotBlank(message: 'Location cannot be blank.')]
    public int $location;

    #[OA\Property(description: 'A flag to ignore the journal entry from reporting', example: 'false')]
    public bool $ignored = false;
}