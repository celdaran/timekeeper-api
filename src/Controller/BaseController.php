<?php namespace App\Controller;

use App\Service\BaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use App\Service\AccountService;

class BaseController extends AbstractController
{
    protected function _patch(Request $request, string $patchColumn, int $id, BaseService $service): JsonResponse
    {
        $data = $this->_canPatch($request, $patchColumn);
        if (empty($data)) {
            return $this->json(ApiResponse::error(['Malformed Request']));
        } else {
            $service->update($id, $data);
            return $this->json(ApiResponse::success());
        }
    }

    protected function _canPatch(Request $request, string $patchColumn): array
    {
        $data = json_decode($request->getContent(), true);
        $keys = array_keys($data);
        if (count($keys) === 1 && $keys[0] === 'value') {
            return [$patchColumn => $data['value']];
        } else {
            return [];
        }
    }

}
