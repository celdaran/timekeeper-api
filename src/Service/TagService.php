<?php namespace App\Service;

use App\Dto\TagCreateRequest;

class TagService extends BaseService
{
    public function __construct(DatabaseService $databaseService)
    {
        parent::__construct($databaseService);
        $this->columnMap = [
            'name' => 'tag_name',
            'description' => 'tag_descr',
            'folder' => 'folder_id',
            'sort' => 'sort_order',
            'hidden' => 'is_hidden',
            'deleted' => 'is_deleted',
        ];
    }

    public function create(TagCreateRequest $tag): int
    {
        $row = [
            'tag_name' => $tag->name,
            'tag_descr' => $tag->description,
            'folder_id' => $tag->folder,
            'sort_order' => 0,
            'is_hidden' => 0,
            'is_deleted' => 0,
        ];
        return $this->db->insert('tag', $row);
    }

    public function fetch(int $id): array
    {
        return $this->_fetch('tag', 'tag_id', $id);
    }

    public function fetchByParent(string $name, ?int $parent): array
    {
        return $this->_fetch2('tag', 'tag_name', $name, 'folder_id', $parent);
    }

    public function update(int $id, array $data): bool
    {
        return $this->_update('tag', 'tag_id', $id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->_delete('tag', 'tag_id', $id);
    }

    public function hide(int $id): bool
    {
        return $this->_hide('tag', 'tag_id', $id);
    }

    public function undelete(int $id): bool
    {
        return $this->_undelete('tag', 'tag_id', $id);
    }

    public function unhide(int $id): bool
    {
        return $this->_unhide('tag', 'tag_id', $id);
    }

}