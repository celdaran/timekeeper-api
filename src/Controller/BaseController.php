<?php namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\BaseService;


class BaseController extends AbstractController
{
    protected function _fetch(BaseService $service, int $id, string $entityName): JsonResponse
    {
        $entity = $service->fetch($id);
        return $this->json(ApiResponse::success([$entityName => $entity]));
    }

    protected function _update(BaseService $service, int $projectId, Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        if ($service->update($projectId, $payload)) {
            return $this->json(ApiResponse::success());
        } else {
            return $this->json(ApiResponse::error(['message' => 'no rows updated']), 404);
        }

    }

    protected function _patch(BaseService $service, string $patchColumn, int $id, Request $request): JsonResponse
    {
        $data = $this->_canPatch($patchColumn, $request);
        if (empty($data)) {
            return $this->json(ApiResponse::error(['malformed request: request payload should be in the form {"value":"<your-value-here>"}']), 422);
        } else {
            if ($service->update($id, $data)) {
                return $this->json(ApiResponse::success());
            } else {
                return $this->json(ApiResponse::error(['message' => 'no rows patched']), 404);
            }
        }
    }

    protected function _patchBoolean(BaseService $service, string $patchColumn, int $id, bool $truth): JsonResponse
    {
        $data = [$patchColumn => $truth ? 'true' : 'false'];
        if ($service->update($id, $data)) {
            return $this->json(ApiResponse::success());
        } else {
            return $this->json(ApiResponse::error(['message' => 'no rows patched']), 404);
        }
    }

    private function _canPatch(string $patchColumn, Request $request): array
    {
        $payload = json_decode($request->getContent(), true);
        $keys = array_keys($payload);
        if (count($keys) === 1 && $keys[0] === 'value') {
            return [$patchColumn => $payload['value']];
        } else {
            return [];
        }
    }

    protected function _delete(BaseService $service, int $id, Request $request): JsonResponse
    {
        if ($this->_hide($request)) {
            $service->hide($id);
            return $this->json(ApiResponse::success());
        }
        $service->delete($id);
        return $this->json(ApiResponse::success());
    }

    protected function _undelete(BaseService $service, int $id, Request $request): JsonResponse
    {
        if ($this->_hide($request)) {
            $service->unhide($id);
            return $this->json(ApiResponse::success());
        }
        $service->undelete($id);
        return $this->json(ApiResponse::success());
    }

    private function _hide(Request $request): bool
    {
        if ($request->query->has('hide')) {
            return ($request->query->get('hide') === 'true');
        }
        return false;
    }

}
