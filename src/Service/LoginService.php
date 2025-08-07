<?php namespace App\Service;

use Ramsey\Uuid\Uuid;

class LoginService
{
    private DatabaseService $databaseService;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    public function login(string $username, string $password): ?string
    {
        $user = $this->databaseService->selectRow('account', 'account_username', $username);

        $storedPassword = $user['account_password'];

        if (password_verify($password, $storedPassword)) {
            // We're authenticated: generate access token
            $token = Uuid::uuid4()->toString();
            // Store with account
            $row['account_descr'] = $token;
            $this->databaseService->update('account', $row, 'account_id', 1);
            // Return to caller
            return $token;
        } else {
            return null;
        }
    }

}