<?php namespace App\Service;

class ProfileService extends BaseService
{
    public function __construct(DatabaseService $databaseService)
    {
        parent::__construct($databaseService);
        $this->columnMap = [
            'profile' => 'profile_name',
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

    public function create(string $profileName, string $profileDescr, int $accountId): int
    {
        $payload = [
            'profile_name' => $profileName,
            'profile_descr' => $profileDescr,
            'account_id' => $accountId,
            'is_hidden' => 0,
            'is_deleted' => 0,
        ];
        return $this->db->insert('profile', $payload);
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