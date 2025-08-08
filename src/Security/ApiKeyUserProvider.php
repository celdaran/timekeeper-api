<?php namespace App\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Service\DatabaseService;

class ApiKeyUserProvider implements UserProviderInterface
{
    public function __construct(private readonly DatabaseService $databaseService) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $account = $this->databaseService->selectRow('account', 'account_username', $identifier);

        if (!$account) {
            throw new \Exception('Account not found.');
        }

        return new ApiKeyUser(
            $account['account_id'],
            $account['account_username'],
            $account['account_email'],
            $account['account_descr'],
            $account['is_admin'],
        );
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