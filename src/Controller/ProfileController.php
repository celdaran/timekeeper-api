<?php namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use OpenApi\Attributes as OA;

use App\Dto\ProfileCreateRequest;
use App\Service\ProfileService;

#[OA\Tag(name: 'Profile Management')]
final class ProfileController extends BaseController
{
    private ProfileService $profileService;

    public function __construct(ProfileService $profileService) {
        $this->profileService = $profileService;
    }

    #[Route('/api/v1/profile', name: 'profile_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] ProfileCreateRequest $profile): JsonResponse
    {
        $profileId = $this->profileService->create($profile);
        return $this->json(ApiResponse::created(['profile' => $profileId]));
    }

    #[Route('/api/v1/profile/{id}', name: 'profile_fetch', methods: ['GET'])]
    public function fetch(Request $request, int $id): JsonResponse
    {
        return $this->_fetch($this->profileService, $id, 'profile');
    }

    #[Route('/api/v1/profile/{id}', name: 'profile_update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->_update($this->profileService, $id, $request);
    }

    #[Route('/api/v1/profile/{id}', name: 'profile_delete', methods: ['DELETE'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        return $this->_delete($this->profileService, $id, $request);
    }

    #[Route('/api/v1/profile/{id}', name: 'profile_undelete', methods: ['PATCH'])]
    public function undelete(Request $request, int $id): JsonResponse
    {
        return $this->_undelete($this->profileService, $id, $request);
    }

    #[Route('/api/v1/profile/{id}/name', name: 'profile_update_name', methods: ['PATCH'])]
    public function changeName(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->profileService, 'profile', $id, $request);
    }

    #[Route('/api/v1/profile/{id}/description', name: 'profile_update_description', methods: ['PATCH'])]
    public function changeDescription(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->profileService, 'description', $id, $request);
    }

}
