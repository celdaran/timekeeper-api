<?php namespace App\Service;

class AccountService extends BaseService
{
    private ProfileService $profileService;

    public function __construct(DatabaseService $databaseService, ProfileService $profileService)
    {
        parent::__construct($databaseService);
        $this->profileService = $profileService;
        $this->columnMap = [
            'id' => 'account_id',
            'username' => 'account_username',
            'password' => 'account_password',
            'email' => 'account_email',
            'description' => 'account_descr',
            'last_project' => 'project_id__last',
            'last_location' => 'location_id__last',
            'hidden' => 'is_hidden',
            'deleted' => 'is_deleted',
        ];
    }

    public function create(string $username, string $password, string $email): array
    {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

        // Create row in account table
        $payload = [
            'account_username' => $username,
            'account_password' => $hashedPassword,
            'account_email' => $email,
            'is_hidden' => 0,
            'is_deleted' => 0,
        ];
        $accountId = $this->db->insert('account', $payload);

        // Create default profile
        $profileId = $this->profileService->create('Default', 'This is the default profile', $accountId);

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

    public function fetch(int $id): array
    {
        return $this->_fetch('account', 'account_id', $id);
    }

    public function update(int $id, array $data): bool
    {
        return $this->_update('account', 'account_id', $id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->_delete('account', 'account_id', $id);
    }

    public function hide(int $id): bool
    {
        return $this->_hide('account', 'account_id', $id);
    }

    public function undelete(int $id): bool
    {
        return $this->_undelete('account', 'account_id', $id);
    }

    public function unhide(int $id): bool
    {
        return $this->_unhide('account', 'account_id', $id);
    }

}