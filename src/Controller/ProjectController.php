<?php namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use OpenApi\Attributes as OA;

use App\Dto\ProjectCreateRequest;
use App\Service\ProjectService;

#[OA\Tag(name: 'Project Management')]
final class ProjectController extends BaseController
{
    private ProjectService $projectService;

    public function __construct(ProjectService $projectService) {
        $this->projectService = $projectService;
    }

    #[Route('/api/v1/project', name: 'project_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] ProjectCreateRequest $project): JsonResponse
    {
        $projectId = $this->projectService->create($project);
        return $this->json(ApiResponse::success(['project' => $projectId]));
    }

    #[Route('/api/v1/project/{id}', name: 'project_fetch', methods: ['GET'])]
    public function fetch(Request $request, int $id): JsonResponse
    {
        return $this->_fetch($this->projectService, $id, $request);
    }

    #[Route('/api/v1/project/{id}', name: 'project_update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->_update($this->projectService, $id, $request);
    }

    #[Route('/api/v1/project/{id}', name: 'project_delete', methods: ['DELETE'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        return $this->_delete($this->projectService, $id, $request);
    }

    #[Route('/api/v1/project/{id}', name: 'project_undelete', methods: ['PATCH'])]
    public function undelete(Request $request, int $id): JsonResponse
    {
        return $this->_undelete($this->projectService, $id, $request);
    }

    #[Route('/api/v1/project/{id}/name', name: 'project_update_name', methods: ['PATCH'])]
    public function changeName(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->projectService, 'name', $id, $request);
    }

    #[Route('/api/v1/project/{id}/description', name: 'project_update_description', methods: ['PATCH'])]
    public function changeDescription(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->projectService, 'description', $id, $request);
    }

    #[Route('/api/v1/project/{id}/order', name: 'project_update_order', methods: ['PATCH'])]
    public function changeSortOrder(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->projectService, 'sort', $id, $request);
    }

    #[Route('/api/v1/project/{id}/parent', name: 'project_update_parent', methods: ['PATCH'])]
    public function changeParent(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->projectService, 'folder', $id, $request);
    }

    #[Route('/api/v1/project/{id}/last_activity', name: 'project_update_last_activity', methods: ['PATCH'])]
    public function changeLastActivity(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->projectService, 'last_activity', $id, $request);
    }

    #[Route('/api/v1/project/{id}/external_ident', name: 'project_update_external_ident', methods: ['PATCH'])]
    public function changeExternalIdent(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->projectService, 'external_ident', $id, $request);
    }

    #[Route('/api/v1/project/{id}/external_url', name: 'project_update_external_url', methods: ['PATCH'])]
    public function changeExternalUrl(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->projectService, 'external_url', $id, $request);
    }

}
