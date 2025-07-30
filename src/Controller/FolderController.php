<?php namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Service\FolderService;

final class FolderController extends BaseController
{
    private FolderService $folderService;

    public function __construct(FolderService $folderService) {
        $this->folderService = $folderService;
    }

    #[Route('/api/v1/folder', name: 'folder_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        return $this->_create($this->folderService, $request);
    }

    #[Route('/api/v1/folder/{id}', name: 'folder_fetch', methods: ['GET'])]
    public function fetch(Request $request, int $id): JsonResponse
    {
        return $this->_fetch($this->folderService, $id, $request);
    }

    #[Route('/api/v1/folder/{id}', name: 'folder_update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->_update($this->folderService, $id, $request);
    }

    #[Route('/api/v1/folder/{id}', name: 'folder_delete', methods: ['DELETE'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        return $this->_delete($this->folderService, $id, $request);
    }

    #[Route('/api/v1/folder/{id}', name: 'folder_undelete', methods: ['PATCH'])]
    public function undelete(Request $request, int $id): JsonResponse
    {
        return $this->_undelete($this->folderService, $id, $request);
    }

    #[Route('/api/v1/folder/{id}/name', name: 'folder_update_name', methods: ['PATCH'])]
    public function changeName(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->folderService, 'folder', $id, $request);
    }

    #[Route('/api/v1/folder/{id}/description', name: 'folder_update_description', methods: ['PATCH'])]
    public function changeDescription(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->folderService, 'description', $id, $request);
    }

    #[Route('/api/v1/folder/{id}/order', name: 'folder_update_order', methods: ['PATCH'])]
    public function changeSortOrder(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->folderService, 'sort_order', $id, $request);
    }

    #[Route('/api/v1/folder/{id}/parent', name: 'folder_update_parent', methods: ['PATCH'])]
    public function changeParent(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->folderService, 'parent', $id, $request);
    }

    #[Route('/api/v1/folder/{id}/open', name: 'folder_update_open', methods: ['PATCH'])]
    public function open(Request $request, int $id): JsonResponse
    {
        return $this->_patchBoolean($this->folderService, 'open', $id, true);
    }

    #[Route('/api/v1/folder/{id}/close', name: 'folder_update_close', methods: ['PATCH'])]
    public function close(Request $request, int $id): JsonResponse
    {
        return $this->_patchBoolean($this->folderService, 'open', $id, false);
    }

}
