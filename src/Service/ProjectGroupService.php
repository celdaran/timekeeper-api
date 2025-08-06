<?php namespace App\Service;

use App\Dto\ProjectGroupCreateRequest;
use App\Dto\ProjectGroupLinkRequest;

class ProjectGroupService extends BaseService
{
    public function __construct(DatabaseService $databaseService)
    {
        parent::__construct($databaseService);
        $this->columnMap = [
            'name' => 'project_group_name',
            'description' => 'project_group_descr',
            'account' => 'account_id',
        ];
    }

    public function create(ProjectGroupCreateRequest $projectGroup): int
    {
        $row = [
            'project_group_name' => $projectGroup->name,
            'project_group_descr' => $projectGroup->description,
            'account_id' => $projectGroup->account,
        ];
        return $this->db->insert('project_group', $row);
    }

    public function fetch(int $id): array
    {
        return $this->_fetch('project_group', 'project_group_id', $id);
    }

    public function update(int $id, array $data): bool
    {
        return $this->_update('project_group', 'project_group_id', $id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->_delete('project_group', 'project_group_id', $id);
    }

    public function hide(int $id): bool
    {
        return $this->_hide('project_group', 'project_group_id', $id);
    }

    public function undelete(int $id): bool
    {
        return $this->_undelete('project_group', 'project_group_id', $id);
    }

    public function unhide(int $id): bool
    {
        return $this->_unhide('project_group', 'project_group_id', $id);
    }

    public function link(ProjectGroupLinkRequest $linkRequest): int
    {
        $row = [
            'project_group_id' => $linkRequest->project_group,
            'project_id' => $linkRequest->project,
        ];
        return $this->db->insert('project_group_project', $row);
    }

}