<?php namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use OpenApi\Attributes as OA;

use App\Dto\TagCreateRequest;
use App\Service\TagService;

#[OA\Tag(name: 'Tag Management')]
final class TagController extends BaseController
{
    private TagService $tagService;

    public function __construct(TagService $tagService) {
        $this->tagService = $tagService;
    }

    #[Route('/api/v1/tag', name: 'tag_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] TagCreateRequest $tag): JsonResponse
    {
        $tagId = $this->tagService->create($tag);
        return $this->json(ApiResponse::success(['tag' => $tagId]));
    }

    #[Route('/api/v1/tag/{id}', name: 'tag_fetch', methods: ['GET'])]
    public function fetch(Request $request, int $id): JsonResponse
    {
        return $this->_fetch($this->tagService, $id, $request);
    }

    #[Route('/api/v1/tag/{id}', name: 'tag_update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->_update($this->tagService, $id, $request);
    }

    #[Route('/api/v1/tag/{id}', name: 'tag_delete', methods: ['DELETE'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        return $this->_delete($this->tagService, $id, $request);
    }

    #[Route('/api/v1/tag/{id}', name: 'tag_undelete', methods: ['PATCH'])]
    public function undelete(Request $request, int $id): JsonResponse
    {
        return $this->_undelete($this->tagService, $id, $request);
    }

    #[Route('/api/v1/tag/{id}/name', name: 'tag_update_name', methods: ['PATCH'])]
    public function changeName(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->tagService, 'name', $id, $request);
    }

    #[Route('/api/v1/tag/{id}/description', name: 'tag_update_description', methods: ['PATCH'])]
    public function changeDescription(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->tagService, 'description', $id, $request);
    }

    #[Route('/api/v1/tag/{id}/order', name: 'tag_update_order', methods: ['PATCH'])]
    public function changeSortOrder(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->tagService, 'sort', $id, $request);
    }

    #[Route('/api/v1/tag/{id}/parent', name: 'tag_update_parent', methods: ['PATCH'])]
    public function changeParent(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->tagService, 'folder', $id, $request);
    }

}
