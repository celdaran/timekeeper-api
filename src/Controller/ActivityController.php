<?php namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use OpenApi\Attributes as OA;

use App\Dto\ActivityCreateRequest;
use App\Service\ActivityService;

#[OA\Tag(name: 'Activity Management')]
final class ActivityController extends BaseController
{
    private ActivityService $activityService;

    public function __construct(ActivityService $activityService) {
        $this->activityService = $activityService;
    }

    #[Route('/api/v1/activity', name: 'activity_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] ActivityCreateRequest $activity): JsonResponse
    {
        $activityId = $this->activityService->create($activity);
        return $this->json(ApiResponse::success(['activity' => $activityId]));
    }

    #[Route('/api/v1/activity/{id}', name: 'activity_fetch', methods: ['GET'])]
    public function fetch(Request $request, int $id): JsonResponse
    {
        return $this->_fetch($this->activityService, $id, $request);
    }

    #[Route('/api/v1/activity/{id}', name: 'activity_update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->_update($this->activityService, $id, $request);
    }

    #[Route('/api/v1/activity/{id}', name: 'activity_delete', methods: ['DELETE'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        return $this->_delete($this->activityService, $id, $request);
    }

    #[Route('/api/v1/activity/{id}', name: 'activity_undelete', methods: ['PATCH'])]
    public function undelete(Request $request, int $id): JsonResponse
    {
        return $this->_undelete($this->activityService, $id, $request);
    }

    #[Route('/api/v1/activity/{id}/name', name: 'activity_update_name', methods: ['PATCH'])]
    public function changeName(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->activityService, 'name', $id, $request);
    }

    #[Route('/api/v1/activity/{id}/description', name: 'activity_update_description', methods: ['PATCH'])]
    public function changeDescription(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->activityService, 'description', $id, $request);
    }

    #[Route('/api/v1/activity/{id}/order', name: 'activity_update_order', methods: ['PATCH'])]
    public function changeSortOrder(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->activityService, 'sort', $id, $request);
    }

    #[Route('/api/v1/activity/{id}/parent', name: 'activity_update_parent', methods: ['PATCH'])]
    public function changeParent(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->activityService, 'folder', $id, $request);
    }

}
