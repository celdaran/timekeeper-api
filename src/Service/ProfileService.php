<?php namespace App\Service;

use App\Dto\ProfileCreateRequest;

class ProfileService extends BaseService
{
    public function __construct(DatabaseService $databaseService)
    {
        parent::__construct($databaseService);
        $this->columnMap = [
            'name' => 'profile_name',
            'description' => 'profile_descr',
            'account' => 'account_id',
            'hidden' => 'is_hidden',
            'deleted' => 'is_deleted',
        ];
    }

    public function fetch(int $id): array
    {
        return $this->_fetch('profile', 'profile_id', $id);
    }

    public function create(ProfileCreateRequest $profile): int
    {
        $profileName = $profile->name;
        $profileDescr = $profile->description;
        $accountId = $profile->account;

        $row = [
            'profile_name' => $profileName,
            'profile_descr' => $profileDescr,
            'account_id' => $accountId,
            'is_hidden' => 0,
            'is_deleted' => 0,
        ];

        return $this->db->insert('profile', $row);
    }

    public function update(int $id, array $data): bool
    {
        return $this->_update('profile', 'profile_id', $id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->_delete('profile', 'profile_id', $id);
    }

    public function hide(int $id): bool
    {
        return $this->_hide('profile', 'profile_id', $id);
    }

    public function undelete(int $id): bool
    {
        return $this->_undelete('profile', 'profile_id', $id);
    }

    public function unhide(int $id): bool
    {
        return $this->_unhide('profile', 'profile_id', $id);
    }

}