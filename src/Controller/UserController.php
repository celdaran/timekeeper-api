<?php namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Service\UserService;

final class UserController extends AbstractController
{
    private UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    #[Route('/api/v1/user', name: 'user_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = $this->userService->create($data['username'], $data['password'], $data['email']);
            return $this->json(ApiResponse::success(['user' => $user]));
        } catch (\Exception $e) {
            return $this->json(ApiResponse::error(['error' => $e->getMessage()]), 400);
        }
    }

}
