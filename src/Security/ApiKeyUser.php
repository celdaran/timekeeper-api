<?php namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class ApiKeyUser implements UserInterface
{
    private int $accountId;
    private string $accountUsername;
    private string $accountEmail;
    private ?string $accountDescr;
    private bool $isAdmin;

    private array $roles;

    public function __construct(
        int $accountId,
        string $accountUsername,
        string $accountEmail,
        ?string $accountDescr,
        bool $isAdmin)
    {
        $this->accountId = $accountId;
        $this->accountUsername = $accountUsername;
        $this->accountEmail = $accountEmail;
        $this->accountDescr = $accountDescr;
        $this->isAdmin = $isAdmin;

        $this->roles = $isAdmin
            ? ['ROLE_API_ADMIN', 'ROLE_API_CLIENT']
            : ['ROLE_API_CLIENT'];
    }

    //--------------------------------------------
    // Custom getters
    //--------------------------------------------

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    public function getAccountUsername(): string
    {
        return $this->accountUsername;
    }

    public function getAccountEmail(): string
    {
        return $this->accountEmail;
    }

    public function getAccountDescr(): ?string
    {
        return $this->accountDescr;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    //--------------------------------------------
    // These a required by UserInterface
    //--------------------------------------------

    public function getUserIdentifier(): string
    {
        return $this->getAccountUsername();
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
    }

}