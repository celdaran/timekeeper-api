<?php namespace App\Service;

use DateTimeImmutable;
use App\Dto\JournalCreateRequest;
use App\Exception\NotFoundException;

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

    /**
     * Import a CSV file
     *
     * This is *not* a generic import, it's 100% geared towards reading the
     * ATracker CSV file. Further, it's *my* version of that file, where I
     * use ATracker's "task" (i.e., a project) as a track. And what TK calls
     * projects, activities, locations, and tags are all ATracker tags. It's
     * working for me, but barely. It's definitely not sustainable. For now
     * this import is designed to get me past this hurdle. Long term, this
     * will become a canonical TK5 importer. And the ATracker export will
     * have to go through an adapter before going into TK.
     *
     * @param string $uploadedFile
     * @param string $originalFileName
     * @param int $profileId
     * @return int
     */
    public function import(string $uploadedFile, string $originalFileName, int $profileId): int
    {
        // Open file
        $fileHandle = fopen($uploadedFile, 'r');

        // Read lines
        $i = 0;
        while (($row = fgetcsv($fileHandle)) !== false) {
            if ($i > 0) {
                // First up: load up column values
                $taskName = $row[0];        // Task name is a "Track"
                $taskDescription = $row[1]; // Unused
                $startTime = $row[2];       // Start time
                $stopTime = $row[3];        // End time
                $duration = $row[4];        // Unused (we recalculate in the import)
                $durationInHours = $row[5]; // Unused
                $memo = $row[6];            // Newlines are stripped  :(
                $tag = $row[7];             // This needs to be parsed

                $tagParser = new JournalImportParserService($tag);

                // Next up: translate into whatever
                $newJournalEntry = new JournalCreateRequest();
                $newJournalEntry->profile = $profileId;
                $newJournalEntry->startTime = $startTime;
                $newJournalEntry->stopTime = $stopTime;
                $newJournalEntry->memo = $memo;
                $newJournalEntry->project = $this->_extractProject($tagParser);
                $newJournalEntry->activity = $this->_extractActivity($tagParser);
                $newJournalEntry->location = $this->_extractLocation($tagParser);
                $newJournalEntry->tags = [$this->_extractTag($tagParser)];
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

    // Tag - Default | Location - Home | Project - AFK | Activity - General:Participating

    private function _extractProject(JournalImportParserService $p): int
    {
        $projectName = $p->extractProject();
        try {
            $project = $this->db->selectRow('project', 'project_name', $projectName, 'project_id');
            return $project['project_id'];
        }
        catch (NotFoundException $e) {
            // If project wasn't found, create it
            // NOTE: this treats all projects as flat
            // So . . . never mind. I shouldn't even do this until I can actually do it
            // For example, if "Project" is "Track:Software:Timekeeper" then this means:
            // 1. Find or create folder "Track"
            // 2. Find or create folder "Software"
            // 3. Find or create project "Timekeeper"
            // ---ORRRRRR--- and this is probably better (as a hack)
            // A straight-up lookup table. Two columns:
            // column A: "The ATracker Project Name"
            // column B: "The Timekeeper project ID"
            // This means autovivification is out: I'd have to predefine these
            // So there's that upfront pain, but it gives me total control
            // and I can also do many-to-one mapping
            return 0;
        }
        catch (\Exception $e) {
            return 0;
        }
    }

    private function _extractActivity(JournalImportParserService $p): int
    {
        $activityName = $p->extractActivity();
        return 2;
    }

    private function _extractLocation(JournalImportParserService $p): int
    {
        $locationName = $p->extractLocation();
        return 3;
    }

    private function _extractTag(JournalImportParserService $p): int
    {
        $tagName = $p->extractTag();
        return 1;
    }

}