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

}
