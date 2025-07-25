<?php namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Service\AccountService;

final class AccountController extends AbstractController
{
    private AccountService $accountService;

    public function __construct(AccountService $accountService) {
        $this->accountService = $accountService;
    }

    #[Route('/api/v1/account/{accountId}', name: 'account_fetch', methods: ['GET'])]
    public function fetch(Request $request, int $accountId): JsonResponse
    {
        try {
            $account = $this->accountService->fetch($accountId);
            return $this->json(ApiResponse::success(['account' => $account]));
        } catch (\Exception $e) {
            return $this->json(ApiResponse::error(['error' => $e->getMessage()]), 400);
        }
    }

    #[Route('/api/v1/account', name: 'account_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $account = $this->accountService->create($data['username'], $data['password'], $data['email']);
            return $this->json(ApiResponse::success(['account' => $account]));
        } catch (\Exception $e) {
            return $this->json(ApiResponse::error(['error' => $e->getMessage()]), 400);
        }
    }

    #[Route('/api/v1/account/{accountId}', name: 'account_update', methods: ['PUT'])]
    public function update(Request $request, int $accountId): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $success = $this->accountService->update($accountId, $data);
            return $this->json(ApiResponse::success());
        } catch (\Exception $e) {
            return $this->json(ApiResponse::error(['error' => $e->getMessage()]), 400);
        }
    }

    #[Route('/api/v1/account/{accountId}', name: 'account_delete', methods: ['DELETE'])]
    public function delete(Request $request, int $accountId): JsonResponse
    {
        try {
            $this->accountService->delete($accountId);
            return $this->json(ApiResponse::success());
        } catch (\Exception $e) {
            return $this->json(ApiResponse::error(['error' => $e->getMessage()]), 400);
        }
    }

    #[Route('/api/v1/account/{accountId}/password', name: 'account_update_password', methods: ['PUT'])]
    public function changePassword(Request $request, int $accountId): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $keys = array_keys($data);
            if (count($keys) === 1 && $keys[0] === 'password') {
                $this->accountService->update($accountId, $data);
                return $this->json(ApiResponse::success());
            } else {
                throw new \Exception('Request body must only contain a password.');
            }
        } catch (\Exception $e) {
            return $this->json(ApiResponse::error(['error' => $e->getMessage()]), 400);
        }
    }

}
