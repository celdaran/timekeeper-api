<?php namespace App\Service;

use App\Dto\ProjectCreateRequest;

class ProjectService extends BaseService
{
    public function __construct(DatabaseService $databaseService)
    {
        parent::__construct($databaseService);
        $this->columnMap = [
            'name' => 'project_name',
            'description' => 'project_descr',
            'folder' => 'folder_id',
            'external_ident' => 'external_ident',
            'external_url' => 'external_url',
        ];
    }

    public function create(ProjectCreateRequest $project): int
    {
        $row = [
            'project_name' => $project->name,
            'project_descr' => $project->description,
            'folder_id' => $project->folder,
            'sort_order' => 0,
            'is_hidden' => 0,
            'is_deleted' => 0,
            'external_ident' => $project->externalIdent,
            'external_url' => $project->externalUrl,
        ];
        return $this->db->insert('project', $row);
    }

    public function fetch(int $id): array
    {
        return $this->_fetch('project', 'project_id', $id);
    }

    public function update(int $id, array $data): bool
    {
        return $this->_update('project', 'project_id', $id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->_delete('project', 'project_id', $id);
    }

    public function hide(int $id): bool
    {
        return $this->_hide('project', 'project_id', $id);
    }

    public function undelete(int $id): bool
    {
        return $this->_undelete('project', 'project_id', $id);
    }

    public function unhide(int $id): bool
    {
        return $this->_unhide('project', 'project_id', $id);
    }

}