<?php namespace App\Service;

class AccountService extends BaseService
{
    private ProfileService $profileService;
    private FolderService $folderService;
    private ProjectService $projectService;

    public function __construct(
        DatabaseService $databaseService,
        ProfileService $profileService,
        FolderService $folderService,
        ProjectService $projectService)
    {
        parent::__construct($databaseService);

        $this->profileService = $profileService;
        $this->folderService = $folderService;
        $this->projectService = $projectService;

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

    public function create(array $data): array
    {
        // Extract key fields
        $username = $data['username'];
        $password = $data['password'];
        $email = $data['email'];

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

        // Create row in account table
        $row = [
            'account_username' => $username,
            'account_password' => $hashedPassword,
            'account_email' => $email,
            'is_hidden' => 0,
            'is_deleted' => 0,
        ];
        $accountId = $this->db->insert('account', $row);

        // Create default profile
        $payload = [
            'name' => 'Default Profile',
            'description' => 'This is the default profile.',
            'account' => $accountId,
        ];
        $profile = $this->profileService->create($payload);
        $profileId = $profile['id'];

        $payload = [
            'name' => 'f0143152-d8a6-4e26-a418-50763bb396bf',
            'description' => 'This is the root folder, required by the schema and hidden from the user.',
            'profile' => $profileId,
            'parent' => null,
        ];
        $folder = $this->folderService->create($payload);
        $folderId = $folder['id'];
        $this->folderService->hide($folderId);

        // Create default dimensions
        $payload = [
            'name' => 'Default Project',
            'description' => 'This is the default project. Feel free to use as-is or edit as needed.',
            'folder' => $folderId,
            'external_ident' => null,
            'external_url' => null,
        ];
        $this->projectService->create($payload);

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