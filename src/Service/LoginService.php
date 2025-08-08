<?php namespace App\Service;

use App\Security\ApiKeyUser;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\SecurityBundle\Security;

class LoginService
{
    public function __construct(private readonly DatabaseService $databaseService)
    {
    }

    public function login(string $username, string $password): ?string
    {
        // Fetch account from database
        $account = $this->databaseService->selectRow('account', 'account_username', $username);

        // Verify password
        if (password_verify($password, $account['account_password'])) {
            // If verified, generate a token
            $token = Uuid::uuid4()->toString();

            // Store token with account
            $row['token'] = $token;
            $this->databaseService->update('account', $row, 'account_username', $username);

            // Return token to caller
            return $token;
        }

        return null;
    }

}