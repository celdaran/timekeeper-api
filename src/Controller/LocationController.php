<?php namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use OpenApi\Attributes as OA;

use App\Dto\LocationCreateRequest;
use App\Service\LocationService;

#[OA\Tag(name: 'Location Management')]
final class LocationController extends BaseController
{
    private LocationService $locationService;

    public function __construct(LocationService $locationService) {
        $this->locationService = $locationService;
    }

    #[Route('/api/v1/location', name: 'location_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] LocationCreateRequest $location): JsonResponse
    {
        $locationId = $this->locationService->create($location);
        return $this->json(ApiResponse::success(['location' => $locationId]));
    }

    #[Route('/api/v1/location/{id}', name: 'location_fetch', methods: ['GET'])]
    public function fetch(Request $request, int $id): JsonResponse
    {
        return $this->_fetch($this->locationService, $id, $request);
    }

    #[Route('/api/v1/location/{id}', name: 'location_update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->_update($this->locationService, $id, $request);
    }

    #[Route('/api/v1/location/{id}', name: 'location_delete', methods: ['DELETE'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        return $this->_delete($this->locationService, $id, $request);
    }

    #[Route('/api/v1/location/{id}', name: 'location_undelete', methods: ['PATCH'])]
    public function undelete(Request $request, int $id): JsonResponse
    {
        return $this->_undelete($this->locationService, $id, $request);
    }

    #[Route('/api/v1/location/{id}/name', name: 'location_update_name', methods: ['PATCH'])]
    public function changeName(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->locationService, 'name', $id, $request);
    }

    #[Route('/api/v1/location/{id}/description', name: 'location_update_description', methods: ['PATCH'])]
    public function changeDescription(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->locationService, 'description', $id, $request);
    }

    #[Route('/api/v1/location/{id}/order', name: 'location_update_order', methods: ['PATCH'])]
    public function changeSortOrder(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->locationService, 'sort', $id, $request);
    }

    #[Route('/api/v1/location/{id}/parent', name: 'location_update_parent', methods: ['PATCH'])]
    public function changeParent(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->locationService, 'folder', $id, $request);
    }

    #[Route('/api/v1/location/{id}/timezone', name: 'location_update_time_zone', methods: ['PATCH'])]
    public function changeTimeZone(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->locationService, 'timezone', $id, $request);
    }

}
