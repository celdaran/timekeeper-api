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
        $data = json_decode($request->getContent(), true);
        $folder = $this->folderService->create($data['folder'], $data['description'], $data['profile'], $data['parent']);
        return $this->json(ApiResponse::success(['folder' => $folder]));
    }

    #[Route('/api/v1/folder/{folderId}', name: 'folder_fetch', methods: ['GET'])]
    public function fetch(Request $request, int $folderId): JsonResponse
    {
        $folder = $this->folderService->fetch($folderId);
        return $this->json(ApiResponse::success(['folder' => $folder]));
    }

    #[Route('/api/v1/folder/{folderId}', name: 'folder_update', methods: ['PUT'])]
    public function update(Request $request, int $folderId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->folderService->update($folderId, $data);
        return $this->json(ApiResponse::success());
    }

    #[Route('/api/v1/folder/{folderId}', name: 'folder_delete', methods: ['PATCH'])]
    public function delete(Request $request, int $folderId): JsonResponse
    {
        if ($request->query->has('hide')) {
            if ($request->query->get('hide') === 'true') {
                $this->folderService->hide($folderId);
                return $this->json(ApiResponse::success());
            }
        }
        $this->folderService->delete($folderId);
        return $this->json(ApiResponse::success());
    }

    #[Route('/api/v1/folder/{folderId}/name', name: 'folder_update_name', methods: ['PATCH'])]
    public function changeName(Request $request, int $folderId): JsonResponse
    {
        return $this->_patch($request, 'folder', $folderId, $this->folderService);
    }

    #[Route('/api/v1/folder/{folderId}/description', name: 'folder_update_description', methods: ['PATCH'])]
    public function changeDescription(Request $request, int $folderId): JsonResponse
    {
        return $this->_patch($request, 'description', $folderId, $this->folderService);
    }

    #[Route('/api/v1/folder/{folderId}/parent', name: 'folder_update_parent', methods: ['PATCH'])]
    public function changeParent(Request $request, int $folderId): JsonResponse
    {
        return $this->_patch($request, 'parent', $folderId, $this->folderService);
    }

    #[Route('/api/v1/folder/{folderId}/order', name: 'folder_update_order', methods: ['PATCH'])]
    public function changeSortOrder(Request $request, int $folderId): JsonResponse
    {
        return $this->_patch($request, 'sort_order', $folderId, $this->folderService);
    }

    #[Route('/api/v1/folder/{folderId}/open', name: 'folder_update_open', methods: ['PATCH'])]
    public function changeOpen(Request $request, int $folderId): JsonResponse
    {
        return $this->_patch($request, 'open', $folderId, $this->folderService);
    }
}
