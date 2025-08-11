<?php namespace App\Service;

use App\Dto\LocationCreateRequest;

class LocationService extends BaseService
{
    public function __construct(DatabaseService $databaseService)
    {
        parent::__construct($databaseService);
        $this->columnMap = [
            'name' => 'location_name',
            'description' => 'location_descr',
            'folder' => 'folder_id',
            'sort' => 'sort_order',
            'timezone' => 'ref_time_zone_id',
            'hidden' => 'is_hidden',
            'deleted' => 'is_deleted',
        ];
    }

    public function create(LocationCreateRequest $location): int
    {
        $row = [
            'location_name' => $location->name,
            'location_descr' => $location->description,
            'folder_id' => $location->folder,
            'sort_order' => 0,
            'ref_time_zone_id' => $location->timeZone,
            'is_hidden' => 0,
            'is_deleted' => 0,
        ];
        return $this->db->insert('location', $row);
    }

    public function fetch(int $id): array
    {
        return $this->_fetch('location', 'location_id', $id);
    }

    public function fetchByParent(string $name, ?int $parent): array
    {
        return $this->_fetchByParent('location', 'location_name', $name, 'folder_id', $parent);
    }

    public function update(int $id, array $data): bool
    {
        return $this->_update('location', 'location_id', $id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->_delete('location', 'location_id', $id);
    }

    public function hide(int $id): bool
    {
        return $this->_hide('location', 'location_id', $id);
    }

    public function undelete(int $id): bool
    {
        return $this->_undelete('location', 'location_id', $id);
    }

    public function unhide(int $id): bool
    {
        return $this->_unhide('location', 'location_id', $id);
    }

}