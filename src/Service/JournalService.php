<?php namespace App\Service;

use DateTimeImmutable;
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
        // TODO: don't forget this needs a TON of data validation
        // e.g., don't just accept "projectId = 1234" and insert it
        // projects (and activities and locations (and tags)) all belong
        // to folders and folders belong to profiles and profiles belong
        // to accounts. So all of that has to be sorted out! For now,
        // in my early prototyping, it's "anything goes."

        $row = [
            'profile_id' => $journal->profile,
            'start_time' => $journal->startTime,
            'stop_time' => $journal->stopTime,
            'duration' => $this->getElapsedSeconds($journal->startTime, $journal->stopTime),
            'memo' => $journal->memo,
            'project_id' => $journal->project,
            'activity_id' => $journal->activity,
            'location_id' => $journal->location,
            'is_ignored' => $journal->ignored,
        ];
        $journalId = $this->db->insert('journal', $row);

        foreach ($journal->tags as $tag) {
            $row = [
                'journal_id' => $journalId,
                'tag_id' => $tag,
            ];
            $this->db->insert('journal_tag', $row);
        }

        return $journalId;
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

    public function import(string $uploadedFile, string $originalFileName, int $profileId): int
    {
        // Open file
        $fileHandle = fopen($uploadedFile, 'r');

        // Read lines
        $i = 0;
        while (($row = fgetcsv($fileHandle)) !== false) {
            if ($i > 0) {
                // First up: load up column values
                $taskName = $row[0];
                $taskDescription = $row[1];
                $startTime = $row[2];
                $stopTime = $row[3];
                $duration = $row[4];
                $durationInHours = $row[5];
                $memo = $row[6];
                $tag = $row[7];

                // Next up: translate into whatever
                $newJournalEntry = new JournalCreateRequest();
                $newJournalEntry->profile = $profileId;
                $newJournalEntry->startTime = $startTime;
                $newJournalEntry->stopTime = $stopTime;
                $newJournalEntry->memo = $memo;
                $newJournalEntry->project = 1;
                $newJournalEntry->activity = 1;
                $newJournalEntry->location = 1;
                $newJournalEntry->ignored = false;

                // Save it
                $this->create($newJournalEntry);
            }
            $i++;
        }
        fclose($fileHandle);

        return $i;
    }

    private function getElapsedSeconds(string $startTime, string $endTime): int
    {
        try {
            // Create an immutable DateTime object from each string.
            // PHP's DateTime classes natively handle the ISO 8601 format.
            $start = new DateTimeImmutable($startTime);
            $end = new DateTimeImmutable($endTime);

            // Calculate the difference in seconds by subtracting the timestamps and return
            return $end->getTimestamp() - $start->getTimestamp();
        } catch (\Exception $e) {
            // Handle invalid date strings gracefully.
            return 0;
        }
    }

}