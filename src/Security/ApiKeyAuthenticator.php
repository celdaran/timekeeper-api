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

        // Look up the API key using the injected data
        // TODO: change to token column (we're just using account_descr until the next schema version is ready)
        $account = $this->databaseService->selectRow('account', 'account_descr', $apiKey);
        if (empty($account)) {
            // Bail if account not found
            // TODO: support token expiration at some point
            throw new CustomUserMessageAuthenticationException('Invalid API token.');
        }

        // Use the actual username from the database.
        // This is what the provider in security.yaml will use to look up the user.
        $username = $account['account_username'];

        // Get the roles from the account data, assuming you have a 'roles' column.
        // If not, you can build the roles array dynamically.
        // $roles = $account['is_admin'] ? ['ROLE_API_ADMIN'] : ['ROLE_API_CLIENT'];
        $roles = ['ROLE_API_ADMIN', 'ROLE_API_CLIENT']; // TODO: just for now, give my "1" user both roles

        // The UserBadge will now be able to retrieve the full User object
        // from the database using the username.
        return new SelfValidatingPassport(new UserBadge($username, function(string $userIdentifier) use ($roles) {
            return new ApiKeyUser($userIdentifier, $roles);
        }));
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