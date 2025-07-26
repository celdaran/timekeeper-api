<?php namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Service\ProfileService;

final class ProfileController extends BaseController
{
    private ProfileService $profileService;

    public function __construct(ProfileService $profileService) {
        $this->profileService = $profileService;
    }

    #[Route('/api/v1/profile/{profileId}', name: 'profile_fetch', methods: ['GET'])]
    public function fetch(Request $request, int $profileId): JsonResponse
    {
        $profile = $this->profileService->fetch($profileId);
        return $this->json(ApiResponse::success(['profile' => $profile]));
    }

    #[Route('/api/v1/profile', name: 'profile_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $profile = $this->profileService->create($data['profile'], $data['description'], $data['account']);
        return $this->json(ApiResponse::success(['account' => $profile]));
    }

    #[Route('/api/v1/profile/{profileId}', name: 'profile_update', methods: ['PUT'])]
    public function update(Request $request, int $profileId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->profileService->update($profileId, $data);
        return $this->json(ApiResponse::success());
    }

    #[Route('/api/v1/profile/{profileId}', name: 'profile_delete', methods: ['PATCH'])]
    public function delete(Request $request, int $profileId): JsonResponse
    {
        if ($request->query->has('hide')) {
            if ($request->query->get('hide') === 'true') {
                $this->profileService->hide($profileId);
                return $this->json(ApiResponse::success());
            }
        }
        $this->profileService->delete($profileId);
        return $this->json(ApiResponse::success());
    }

    #[Route('/api/v1/profile/{profileId}/name', name: 'profile_update_name', methods: ['PATCH'])]
    public function changeName(Request $request, int $profileId): JsonResponse
    {
        return $this->_patch($request, 'profile', $profileId, $this->profileService);
    }

    #[Route('/api/v1/profile/{profileId}/description', name: 'profile_update_description', methods: ['PATCH'])]
    public function changeDescription(Request $request, int $profileId): JsonResponse
    {
        return $this->_patch($request, 'description', $profileId, $this->profileService);
    }

}
