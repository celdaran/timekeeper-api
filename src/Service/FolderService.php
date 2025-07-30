<?php namespace App\Service;

class FolderService extends BaseService
{
    public function __construct(DatabaseService $databaseService)
    {
        parent::__construct($databaseService);
        $this->columnMap = [
            'name' => 'folder_name',
            'description' => 'folder_descr',
            'profile' => 'profile_id',
            'parent' => 'folder_id__parent',
            'sort_order' => 'sort_order',
            'open' => 'is_open',
            'hidden' => 'is_hidden',
            'deleted' => 'is_deleted',
        ];
    }

    public function create(array $data): array
    {
        $row = [
            'folder_name' => $data['name'],
            'folder_descr' => $data['description'],
            'profile_id' => $data['profile'],
            'folder_id__parent' => $data['parent'],
            'sort_order' => 0,
            'is_open' => true,
            'is_hidden' => 0,
            'is_deleted' => 0,
        ];
        return [
            'id' => $this->db->insert('folder', $row),
        ];
    }

    public function fetch(int $id): array
    {
        return $this->_fetch('folder', 'folder_id', $id);
    }

    public function update(int $id, array $data): bool
    {
        return $this->_update('folder', 'folder_id', $id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->_delete('folder', 'folder_id', $id);
    }

    public function hide(int $id): bool
    {
        return $this->_hide('folder', 'folder_id', $id);
    }

    public function undelete(int $id): bool
    {
        return $this->_undelete('folder', 'folder_id', $id);
    }

    public function unhide(int $id): bool
    {
        return $this->_unhide('folder', 'folder_id', $id);
    }

}