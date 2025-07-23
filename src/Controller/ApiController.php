<?php namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted; // For Symfony 6.x+ attributes

class ApiController extends AbstractController
{
    #[Route('/api/v1/endpoint', name: 'api_create_endpoint', methods: ['POST'])]
    #[IsGranted('ROLE_API_ADMIN')] // Only clients with ROLE_API_ADMIN can create users
    public function createUser(): JsonResponse
    {
        // ... create user logic ...
        return new JsonResponse(['message' => 'User created!'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/v1/endpoint', name: 'api_list_endpoint', methods: ['GET'])]
    #[IsGranted('ROLE_API_CLIENT')] // Any authenticated API client can list users
    public function listUsers(): JsonResponse
    {
        // ... list users logic ...
        return new JsonResponse(['users' => []]);
    }

    #[Route('/', name: 'api_home_endpoint', methods: ['GET'])]
    public function home(): JsonResponse
    {
        // This endpoint doesn't have an IsGranted, so it will be protected by the
        // general access_control rule ^/api unless you make it PUBLIC_ACCESS
        return new JsonResponse(['message' => 'Welcome to your API!']);
    }
}