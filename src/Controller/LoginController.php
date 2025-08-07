<?php namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use OpenApi\Attributes as OA;

use App\Service\LoginService;

#[OA\Tag(name: 'Login')]
final class LoginController extends BaseController
{
    private LoginService $loginService;

    public function __construct(LoginService $loginService) {
        $this->loginService = $loginService;
    }

    #[OA\Post(
        description: 'Accepts a username and password. If authenticated, returns an API token.',
        summary: 'Log in user'
    )]
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $body = $request->getContent();
        $payload = json_decode($body, true);
        $username = $payload['username'];
        $password = $payload['password'];
        $token = $this->loginService->login($username, $password);
        if (empty($token)) {
            return $this->json(ApiResponse::error(['message' => 'Invalid username or password']), 401);
        } else {
            return $this->json(ApiResponse::success(['token' => $token]), 200);
        }
    }

}
