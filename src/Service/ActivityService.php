<?php namespace App\Service;

use App\Dto\ActivityCreateRequest;

class ActivityService extends BaseService
{
    public function __construct(DatabaseService $databaseService)
    {
        parent::__construct($databaseService);
        $this->columnMap = [
            'name' => 'activity_name',
            'description' => 'activity_descr',
            'folder' => 'folder_id',
            'sort' => 'sort_order',
            'hidden' => 'is_hidden',
            'deleted' => 'is_deleted',
        ];
    }

    public function create(ActivityCreateRequest $activity): int
    {
        $row = [
            'activity_name' => $activity->name,
            'activity_descr' => $activity->description,
            'folder_id' => $activity->folder,
            'sort_order' => 0,
            'is_hidden' => 0,
            'is_deleted' => 0,
        ];
        return $this->db->insert('activity', $row);
    }

    public function fetch(int $id): array
    {
        return $this->_fetch('activity', 'activity_id', $id);
    }

    public function fetchByParent(string $name, ?int $parent): array
    {
        return $this->_fetchByParent('activity', 'activity_name', $name, 'folder_id', $parent);
    }

    public function update(int $id, array $data): bool
    {
        return $this->_update('activity', 'activity_id', $id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->_delete('activity', 'activity_id', $id);
    }

    public function hide(int $id): bool
    {
        return $this->_hide('activity', 'activity_id', $id);
    }

    public function undelete(int $id): bool
    {
        return $this->_undelete('activity', 'activity_id', $id);
    }

    public function unhide(int $id): bool
    {
        return $this->_unhide('activity', 'activity_id', $id);
    }

}