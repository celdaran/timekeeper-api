<?php namespace App\Service;

use App\Dto\AccountCreateRequest;
use App\Dto\ActivityCreateRequest;
use App\Dto\FolderCreateRequest;
use App\Dto\LocationCreateRequest;
use App\Dto\ProfileCreateRequest;
use App\Dto\ProjectCreateRequest;

class AccountService extends BaseService
{
    private ProfileService $profileService;
    private FolderService $folderService;
    private ProjectService $projectService;
    private ActivityService $activityService;
    private LocationService $locationService;

    public function __construct(
        DatabaseService $databaseService,
        ProfileService $profileService,
        FolderService $folderService,
        ProjectService $projectService,
        ActivityService $activityService,
        LocationService $locationService,
    )
    {
        parent::__construct($databaseService);

        $this->profileService = $profileService;
        $this->folderService = $folderService;
        $this->projectService = $projectService;
        $this->activityService = $activityService;
        $this->locationService = $locationService;

        $this->columnMap = [
            'id' => 'account_id',
            'username' => 'account_username',
            'password' => 'account_password',
            'email' => 'account_email',
            'description' => 'account_descr',
            'last_project' => 'project_id__last',
            'last_location' => 'location_id__last',
            'admin' => 'is_admin',
            'hidden' => 'is_hidden',
            'deleted' => 'is_deleted',
        ];
    }

    public function getEntityType(): string
    {
        // Note: not yet used
        return 'account';
    }

    public function create(AccountCreateRequest $account, bool $isSysAdmin = false): array
    {
        // Create new account
        $row = [
            'account_username' => $account->username,
            'account_password' => password_hash($account->password, PASSWORD_ARGON2ID),
            'account_email' => $account->email,
            'account_descr' => $account->description,
            'is_admin' => $account->admin ? 1 : 0,
            'is_hidden' => 0,
            'is_deleted' => 0,
        ];
        $accountId = $this->db->insert('account', $row);

        // Create default profile
        $profile = new ProfileCreateRequest();
        $profile->name = 'Default Profile';
        $profile->description = 'This is the default profile.';
        $profile->account = $accountId;
        $profileId = $this->profileService->create($profile);

        // Create profile's root folder
        $folder = new FolderCreateRequest();
        $folder->name = FolderService::PROFILE_ROOT_FOLDER_NAME;
        $folder->description = 'This is the root folder, required by the schema and hidden from the user.';
        $folder->profile = $profileId;
        $folder->parent = 1; // system-generated admin folder (hidden root of all folders as folders are NOT NULL)
        $folder->sort = 1;
        $folder->system = 1;
        $folder->hidden = 1;
        $folderId = $this->folderService->create($folder);

        if (!$isSysAdmin) {
            // Create default project
            $project = new ProjectCreateRequest();
            $project->name = 'Default Project';
            $project->description = 'This is the default project. Feel free to use as-is or edit as needed.';
            $project->folder = $folderId;
            $this->projectService->create($project);

            // Create default activity
            $activity = new ActivityCreateRequest();
            $activity->name = 'Default Activity';
            $activity->description = 'This is the default activity. Feel free to use as-is or edit as needed.';
            $activity->folder = $folderId;
            $this->activityService->create($activity);

            // Create default location
            $location = new LocationCreateRequest();
            $location->name = 'Default Location';
            $location->description = 'This is the default location. Feel free to use as-is or edit as needed.';
            $location->folder = $folderId;
            $this->locationService->create($location);
        }

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