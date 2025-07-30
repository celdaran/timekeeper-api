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
        return $this->_create($this->accountService, $request);
    }

    #[Route('/api/v1/account/{id}', name: 'account_fetch', methods: ['GET'])]
    public function fetch(Request $request, int $id): JsonResponse
    {
        return $this->_fetch($this->accountService, $id, 'account');
    }

    #[Route('/api/v1/account/{id}', name: 'account_update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->_update($this->accountService, $id, $request);
    }

    #[Route('/api/v1/account/{id}', name: 'account_delete', methods: ['DELETE'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        return $this->_delete($this->accountService, $id, $request);
    }

    #[Route('/api/v1/account/{id}', name: 'account_undelete', methods: ['PATCH'])]
    public function undelete(Request $request, int $id): JsonResponse
    {
        return $this->_undelete($this->accountService, $id, $request);
    }

    #[Route('/api/v1/account/{id}/password', name: 'account_update_password', methods: ['PATCH'])]
    public function changePassword(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->accountService, 'password', $id, $request);
    }

    #[Route('/api/v1/account/{id}/email', name: 'account_update_email', methods: ['PATCH'])]
    public function changeEmail(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->accountService, 'email', $id, $request);
    }

    #[Route('/api/v1/account/{id}/last_project', name: 'account_update_last_project', methods: ['PATCH'])]
    public function changeLastProject(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->accountService, 'last_project', $id, $request);
    }

    #[Route('/api/v1/account/{id}/last_location', name: 'account_update_last_location', methods: ['PATCH'])]
    public function changeLastLocation(Request $request, int $id): JsonResponse
    {
        return $this->_patch($this->accountService, 'last_location', $id, $request);
    }

}
