<?php namespace App\Service;

use App\Security\ApiKeyUser;
use Symfony\Bundle\SecurityBundle\Security;

class PermissionService
{
    private ?ApiKeyUser $account;

    public function __construct(private readonly Security $security)
    {
        $this->account = $this->getAccount();
    }

    public function canCreateAccount(): bool
    {
        return $this->account !== null;
    }

    public function canAccessProfileForAccount(int $accountId): bool
    {
        if ($this->account->isAdmin()) {
            return true;
        }

        if ($this->account->getAccountId() === $accountId) {
            return true;
        }

        return false;
    }

    private function getAccount(): ?ApiKeyUser
    {
        $account = $this->security->getUser();

        if ($account instanceof ApiKeyUser) {
            return $account;
        }

        return null;
    }

}