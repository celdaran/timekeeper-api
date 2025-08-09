<?php namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use OpenApi\Attributes as OA;

use App\Dto\ProfileCreateRequest;
use App\Service\ProfileService;
use App\Service\PermissionService;

#[OA\Tag(name: 'Profile Management')]
final class ProfileController extends BaseController
{
    public function __construct(
        private readonly ProfileService $profileService,
        private readonly PermissionService $perm)
    {
    }

    #[Route('/api/v1/profile', name: 'profile_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] ProfileCreateRequest $profile): JsonResponse
    {
        if ($this->perm->canAccessProfileForAccount($profile->account)) {
            $profileId = $this->profileService->create($profile);
            return $this->json(ApiResponse::created(['profile' => $profileId]));
        } else {
            return $this->json(ApiResponse::error(['error' => 'Permission denied']), 403);
        }
    }

    #[Route('/api/v1/profile/{id}', name: 'profile_fetch', methods: ['GET'])]
    public function fetch(#[MapRequestPayload] ProfileCreateRequest $profile, int $id): JsonResponse
    {
        if ($this->perm->canAccessProfileForAccount($profile->account)) {
            return $this->_fetch($this->profileService, $id, 'profile');
        } else {
            return $this->json(ApiResponse::error(['error' => 'Permission denied']), 403);
        }
    }

    #[Route('/api/v1/profile/{id}', name: 'profile_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
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
        return $this->_patch($this->profileService, 'name', $id, $request);
    }

    #[Route('/api/v1/profile/{id}/description', name: 'profile_update_description', methods: ['PATCH'])]
    public function changeDescription(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->profileService, 'description', $id, $request);
    }

}
