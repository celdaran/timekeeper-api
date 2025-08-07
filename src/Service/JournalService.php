<?php namespace App\Service;

use App\Dto\JournalCreateRequest;

class JournalService extends BaseService
{
    public function __construct(DatabaseService $databaseService)
    {
        parent::__construct($databaseService);
        $this->columnMap = [
            'startTime' => 'start_time',
            'stopTime' => 'stop_time',
            'memo' => 'memo',
            'project' => 'project_id',
            'activity' => 'activity_id',
            'location' => 'location_id',
            'ignored' => 'is_ignored',
        ];
    }

    public function create(JournalCreateRequest $journal): int
    {
        $row = [
            'start_time' => $journal->startTime,
            'stop_time' => $journal->stopTime,
            'memo' => $journal->memo,
            'project_id' => $journal->project,
            'activity_id' => $journal->activity,
            'location_id' => $journal->location,
            'is_ignored' => $journal->ignored,
        ];
        return $this->db->insert('journal', $row);


    }

    public function fetch(int $id): array
    {
        return $this->_fetch('journal', 'journal_id', $id);
    }

    public function update(int $id, array $data): bool
    {
        return $this->_update('journal', 'journal_id', $id, $data);
    }

    public function delete(int $id): bool
    {
        return false;
    }

    public function hide(int $id): bool
    {
        return false;
    }

    public function undelete(int $id): bool
    {
        return false;
    }

    public function unhide(int $id): bool
    {
        return false;
    }

}