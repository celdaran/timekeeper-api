<?php namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

use App\Service\DatabaseService;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    public function __construct(private readonly DatabaseService $databaseService) {}

    /**
     * Called on every request to decide if this authenticator should be used.
     * Return `false` to let other authenticators try.
     */
    public function supports(Request $request): ?bool
    {
        return $request->headers->has('X-API-KEY') && str_starts_with($request->getPathInfo(), '/api/');
    }

    public function authenticate(Request $request): Passport
    {
        // Get key from header
        $apiKey = $request->headers->get('X-API-KEY');

        // Cannot be blank
        if (null === $apiKey) {
            throw new CustomUserMessageAuthenticationException('No API token provided.');
        }

        // Look up the API key
        $account = $this->databaseService->selectRow('account', 'token', $apiKey);

        if (empty($account)) {
            // TODO: support token expiration at some point
            throw new CustomUserMessageAuthenticationException('Invalid API token. User must log in again...');
        }

        if ($account['is_hidden']) {
            throw new CustomUserMessageAuthenticationException('Account has been hidden.');
        }

        if ($account['is_deleted']) {
            throw new CustomUserMessageAuthenticationException('Account has been deleted.');
        }

        // Save various account attributes
        $accountId = $account['account_id'];
        $accountUsername = $account['account_username'];  // This is Symfony's unique user key
        $accountDescr = $account['account_descr'];
        $accountEmail = $account['account_email'];
        $accountIsAdmin = $account['is_admin'];

        // Create passport: the closure here instantiates the ApiKeyUser object which we can fetch later
        return new SelfValidatingPassport(
            new UserBadge($accountUsername,
                function(string $userIdentifier) use ($accountId, $accountEmail, $accountDescr, $accountIsAdmin) {
                    return new ApiKeyUser($accountId, $userIdentifier, $accountEmail, $accountDescr, $accountIsAdmin);
                }
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}