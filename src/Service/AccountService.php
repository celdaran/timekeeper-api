<?php namespace App\Service;

use App\Dto\AccountCreateRequest;
use App\Dto\FolderCreateRequest;
use App\Dto\ProfileCreateRequest;
use App\Dto\ProjectCreateRequest;

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

    public function getEntityType(): string
    {
        // Note: not yet used
        return 'account';
    }

    public function create(AccountCreateRequest $account): array
    {
        // AccountCreateRequest row in account table
        $row = [
            'account_username' => $account->username,
            'account_password' => password_hash($account->password, PASSWORD_ARGON2ID),
            'account_email' => $account->email,
            'is_hidden' => 0,
            'is_deleted' => 0,
        ];
        $accountId = $this->db->insert('account', $row);

        // AccountCreateRequest default profile
        $profile = new ProfileCreateRequest();
        $profile->name = 'Default Profile';
        $profile->description = 'This is the default profile.';
        $profile->account = $accountId;
        $profileId = $this->profileService->create($profile);

        $folder = new FolderCreateRequest();
        $folder->name = 'f0143152-d8a6-4e26-a418-50763bb396bf';
        $folder->description = 'This is the root folder, required by the schema and hidden from the user.';
        $folder->profile = $profileId;
        $folder->parent = null;
        $folder->sort = 1;
        $folder->hidden = 1;
        $folderId = $this->folderService->create($folder);

        // AccountCreateRequest default dimensions
        $project = new ProjectCreateRequest();
        $project->name = 'Default Project';
        $project->description = 'This is the default project. Feel free to use as-is or edit as needed.';
        $project->folder = $folderId;
        $this->projectService->create($project);

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