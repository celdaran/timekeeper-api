<?php namespace App\Service;

use App\Dto\FolderCreateRequest;

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
            'sort' => 'sort_order',
            'open' => 'is_open',
            'hidden' => 'is_hidden',
            'deleted' => 'is_deleted',
        ];
    }

    public function create(FolderCreateRequest $folder): int
    {
        $row = [
            'folder_name' => $folder->name,
            'folder_descr' => $folder->description,
            'profile_id' => $folder->profile,
            'folder_id__parent' => $folder->parent,
            'sort_order' => $folder->sort,
            'is_open' => $folder->open,
            'is_hidden' => $folder->hidden,
            'is_deleted' => 0,
        ];
        return $this->db->insert('folder', $row);
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