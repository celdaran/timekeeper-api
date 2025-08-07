<?php namespace App\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Service\DatabaseService;
use App\Security\ApiKeyUser;

class ApiKeyUserProvider implements UserProviderInterface
{
    private DatabaseService $databaseService;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // This is where you would look up the user in your database using PDO.
        // The $identifier here is the username.
        $user = $this->databaseService->selectRow('account', 'account_username', $identifier);

        if (!$user) {
            throw new \Exception('User not found.'); // or some other custom exception
        }

        // This method needs to return an object that implements UserInterface.
        // We'll create this class next.
        return new ApiKeyUser($user['username'], $user['roles']);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof ApiKeyUser) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return ApiKeyUser::class === $class;
    }
}