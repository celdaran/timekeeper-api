<?php namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use OpenApi\Attributes as OA;

use App\Dto\ProjectGroupCreateRequest;
use App\Dto\ProjectGroupLinkRequest;
use App\Service\ProjectGroupService;

#[OA\Tag(name: 'Project Group Management')]
final class ProjectGroupController extends BaseController
{
    private ProjectGroupService $projectGroupService;

    public function __construct(ProjectGroupService $projectGroupService) {
        $this->projectGroupService = $projectGroupService;
    }

    #[Route('/api/v1/projectgroup', name: 'projectgroup_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] ProjectGroupCreateRequest $projectGroup): JsonResponse
    {
        $projectGroupId = $this->projectGroupService->create($projectGroup);
        return $this->json(ApiResponse::success(['projectgroup_group' => $projectGroupId]));
    }

    #[Route('/api/v1/projectgroup/{id}', name: 'projectgroup_fetch', methods: ['GET'])]
    public function fetch(Request $request, int $id): JsonResponse
    {
        return $this->_fetch($this->projectGroupService, $id, $request);
    }

    #[Route('/api/v1/projectgroup/{id}', name: 'projectgroup_update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->_update($this->projectGroupService, $id, $request);
    }

    #[Route('/api/v1/projectgroup/{id}', name: 'projectgroup_delete', methods: ['DELETE'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        return $this->_delete($this->projectGroupService, $id, $request);
    }

    #[Route('/api/v1/projectgroup/{id}', name: 'projectgroup_undelete', methods: ['PATCH'])]
    public function undelete(Request $request, int $id): JsonResponse
    {
        return $this->_undelete($this->projectGroupService, $id, $request);
    }

    #[Route('/api/v1/projectgroup/{id}/name', name: 'projectgroup_update_name', methods: ['PATCH'])]
    public function changeName(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->projectGroupService, 'name', $id, $request);
    }

    #[Route('/api/v1/projectgroup/{id}/description', name: 'projectgroup_update_description', methods: ['PATCH'])]
    public function changeDescription(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->projectGroupService, 'description', $id, $request);
    }

    #[Route('/api/v1/projectgroup/link', name: 'projectgroup_link_project', methods: ['POST'])]
    public function linkProjectGroup(#[MapRequestPayload] ProjectGroupLinkRequest $linkRequest): JsonResponse
    {
        $projectGroupProjectId = $this->projectGroupService->link($linkRequest);
        return $this->json(ApiResponse::success(['project_group_project' => $projectGroupProjectId]));
    }

}
