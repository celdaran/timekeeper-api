<?php namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Service\AccountService;

final class AccountController extends BaseController
{
    private AccountService $accountService;

    public function __construct(AccountService $accountService) {
        $this->accountService = $accountService;
    }

    #[Route('/api/v1/account', name: 'account_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $account = $this->accountService->create($data['username'], $data['password'], $data['email']);
        return $this->json(ApiResponse::success(['account' => $account]));
    }

    #[Route('/api/v1/account/{accountId}', name: 'account_fetch', methods: ['GET'])]
    public function fetch(Request $request, int $accountId): JsonResponse
    {
        $account = $this->accountService->fetch($accountId);
        return $this->json(ApiResponse::success(['account' => $account]));
    }

    #[Route('/api/v1/account/{accountId}', name: 'account_update', methods: ['PUT'])]
    public function update(Request $request, int $accountId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
         if ($this->accountService->update($accountId, $data)) {
             return $this->json(ApiResponse::success());
         } else {
             return $this->json(ApiResponse::error(['message' => 'no rows updated']), 404);
         }
    }

    #[Route('/api/v1/account/{accountId}', name: 'account_delete', methods: ['DELETE'])]
    public function delete(Request $request, int $accountId): JsonResponse
    {
        if ($request->query->has('hide')) {
            if ($request->query->get('hide') === 'true') {
                $this->accountService->hide($accountId);
                return $this->json(ApiResponse::success());
            }
        }
        $this->accountService->delete($accountId);
        return $this->json(ApiResponse::success());
    }

    #[Route('/api/v1/account/{accountId}', name: 'account_undelete', methods: ['PATCH'])]
    public function undelete(Request $request, int $accountId): JsonResponse
    {
        if ($request->query->has('hide')) {
            if ($request->query->get('hide') === 'true') {
                $this->accountService->unhide($accountId);
                return $this->json(ApiResponse::success());
            }
        }
        $this->accountService->undelete($accountId);
        return $this->json(ApiResponse::success());
    }

    #[Route('/api/v1/account/{accountId}/password', name: 'account_update_password', methods: ['PATCH'])]
    public function changePassword(Request $request, int $accountId): JsonResponse
    {
        return $this->_patch($request, 'password', $accountId, $this->accountService);
    }

    #[Route('/api/v1/account/{accountId}/email', name: 'account_update_email', methods: ['PATCH'])]
    public function changeEmail(Request $request, int $accountId): JsonResponse
    {
        return $this->_patch($request, 'email', $accountId, $this->accountService);
    }

    #[Route('/api/v1/account/{accountId}/last_project', name: 'account_update_last_project', methods: ['PATCH'])]
    public function changeLastProject(Request $request, int $accountId): JsonResponse
    {
        return $this->_patch($request, 'last_project', $accountId, $this->accountService);
    }

    #[Route('/api/v1/account/{accountId}/last_location', name: 'account_update_last_location', methods: ['PATCH'])]
    public function changeLastLocation(Request $request, int $accountId): JsonResponse
    {
        return $this->_patch($request, 'last_location', $accountId, $this->accountService);
    }

}
