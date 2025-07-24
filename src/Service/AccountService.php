<?php namespace App\Service;

use App\Service\DatabaseService;

class AccountService
{
    private DatabaseService $db;

    private array $columnMap;

    public function __construct(DatabaseService $databaseService)
    {
        $this->db = $databaseService;
        $this->columnMap = [
            'id' => 'account_id',
            'username' => 'account_username',
            'password' => 'account_password',
            'email' => 'account_email',
            'description' => 'account_descr',
            'hidden' => 'is_hidden',
            'deleted' => 'is_deleted',
        ];
    }

    public function fetch(int $accountId): array
    {
        return $this->db->selectRow('account', 'account_id', $accountId);
    }

    public function create(string $username, string $password, string $email): array
    {
        // Create row in account table
        $payload = [
            'account_username' => $username,
            'account_password' => $password,
            'account_email' => $email,
            'is_hidden' => 0,
            'is_deleted' => 0,
        ];
        $accountId = $this->db->insert('account', $payload);

        // Create default profile
        $payload = [
            'profile_name' => 'Default',
            'profile_descr' => 'This is the default profile',
            'account_id' => $accountId,
            'is_hidden' => 0,
            'is_deleted' => 0,
        ];
        $profileId = $this->db->insert('profile', $payload);

        // Create profile-specific, invisible root folder
        $payload = [
            'folder_name' => '06e4c5e9-9f05-4f82-9d70-f8a04ff205f1',
            'folder_descr' => 'This is the root folder, required by the schema and hidden from the user.',
            'profile_id' => $profileId,
            'is_hidden' => 1,
        ];
        $folderId = $this->db->insert('folder', $payload);

        // Create default dimensions
        $payload = [
            'project_name' => 'Default Project',
            'project_descr' => 'This is the default project. Feel free to use as-is or edit as needed.',
            'folder_id' => $folderId,
            'sort_order' => 1,
            'is_hidden' => 0,
            'is_deleted' => 0,
        ];
        $this->db->insert('project', $payload);

        $payload = [
            'activity_name' => 'Default Activity',
            'activity_descr' => 'This is the default activity. Feel free to use as-is or edit as needed.',
            'folder_id' => $folderId,
            'sort_order' => 1,
            'is_hidden' => 0,
            'is_deleted' => 0,
        ];
        $this->db->insert('activity', $payload);

        $payload = [
            'location_name' => 'Default Location',
            'location_descr' => 'This is the default location. Feel free to use as-is or edit as needed.',
            'folder_id' => $folderId,
            'sort_order' => 1,
            'is_hidden' => 0,
            'is_deleted' => 0,
            'ref_time_zone_id' => 337, // "Europe/London"
        ];
        $this->db->insert('location', $payload);

        return [
            'account_id' => $accountId,
            'profile_id' => $profileId,
            'folder_id' => $folderId,
        ];
    }

    public function update(int $accountId, array $data): bool
    {
        $payload = [];
        foreach ($data as $key => $value) {
            $payload[$this->columnMap[$key]] = $value;
        }
        $payload['modified_at'] = date('Y-m-d H:i:s');
        return $this->db->update('account', $payload, 'account_id', $accountId);
    }

    public function delete(int $accountId): bool
    {
        $payload = [
            'is_deleted' => 1,
            'deleted_at' => date('Y-m-d H:i:s'),
            'modified_at' => date('Y-m-d H:i:s'),
        ];
        return $this->db->update('account', $payload, 'account_id', $accountId);
    }

}