<?php namespace App\Service;

use Exception;
use DateTimeImmutable;
use App\Dto\JournalCreateRequest;
use App\Dto\FolderCreateRequest;
use App\Dto\ProjectCreateRequest;
use App\Dto\ActivityCreateRequest;
use App\Dto\LocationCreateRequest;
use App\Dto\TagCreateRequest;
use App\Exception\NotFoundException;
use App\Exception\BlankValueException;

class JournalService extends BaseService
{
    public function __construct(
        DatabaseService $databaseService,
        private readonly FolderService $folderService,
        private readonly ProjectService $projectService,
        private readonly ActivityService $activityService,
        private readonly LocationService $locationService,
        private readonly TagService $tagService,
    )
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
            'is_ignored' => $journal->ignored ? 1 : 0,
            'is_reconciled' => $journal->reconciled ? 1 : 0,
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
     * @param string $uploadedFile
     * @param string $originalFileName
     * @param int $profileId
     *
     * @return int
     */
    public function import(string $uploadedFile, string $originalFileName, int $profileId): int
    {
        // Open file
        $fileHandle = fopen($uploadedFile, 'r');

        // Start transaction
        $this->db->begin();
        $succeeded = false;
        $lastStartTime = '';

        try {
            // Read lines
            $rowCount = 0;
            while (($row = fgetcsv($fileHandle)) !== false) {
                if ($rowCount > 0) {
                    // First up: load up column values
                    $startTime = $row[0];
                    $stopTime = $row[1];
                    $memo = $row[2];
                    $project = $row[3];
                    $activity = $row[4];
                    $location = $row[5];
                    $tags = $row[6];
                    $ignored = $row[7];
                    $reconciled = $row[8];

                    // For error handling only
                    $lastStartTime = $startTime;

                    // Next up: translate into whatever
                    $newJournalEntry = new JournalCreateRequest();
                    $newJournalEntry->profile = $profileId;
                    $newJournalEntry->startTime = $startTime;
                    $newJournalEntry->stopTime = $stopTime;
                    $newJournalEntry->memo = $memo;
                    $newJournalEntry->project = $this->_convertProject($project, $profileId);
                    $newJournalEntry->activity = $this->_convertActivity($activity, $profileId);
                    $newJournalEntry->location = $this->_convertLocation($location, $profileId);
                    $newJournalEntry->tags = $this->_convertTags($tags, $profileId);
                    $newJournalEntry->ignored = $ignored;
                    $newJournalEntry->reconciled = $reconciled;

                    // Save it
                    $this->create($newJournalEntry);
                }
                $rowCount++;
            }

            // Clean up
            $this->db->commit();
            fclose($fileHandle);
            $succeeded = true;
            $message = 'Successfully imported ' . ($rowCount - 1) . ' records from file ' . $originalFileName;
        }
        catch (\Exception $e) {
            $this->db->rollback();
            $message = sprintf("%s\nstart_time: %s, profile_id %d",
                $e->getMessage(), $lastStartTime, $profileId);
        }

        // Import log
        $log = [
            'original_file_name' => $originalFileName,
            'row_count' => ($rowCount - 1),
            'imported_at' => date('Y-m-d H:i:s'),
            'succeeded' => $succeeded ? 1 : 0,
            'message' => $message,
        ];
        $this->db->insert('import_log', $log);

        return $rowCount;
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

    /**
     * Convert the CSV-representation of a project into a project_id
     *
     * Sample input: Chores::House::Yard::Mowing and Trimming
     * Sample output: 375
     *
     * Behavior: Think of it as `mkdir -p` because if any part of
     * the path or project itself doesn't exist, it's created.
     *
     * @param string $project
     * @param int $profileId
     * @return int
     * @throws Exception
     */
    private function _convertProject(string $project, int $profileId): int
    {
        [$projectName, $folderId] = $this->_getEntityAndParent($project, $profileId);

        try {
            $project = $this->projectService->fetchByParent($projectName, $folderId);
            return $project['project_id'];
        }
        catch (NotFoundException) {
            $newProject = new ProjectCreateRequest();
            $newProject->name = $projectName;
            $newProject->description = 'Project created during CSV import';
            $newProject->folder = $folderId;
            return $this->projectService->create($newProject);
        }
    }

    /**
     * Convert the CSV-representation of an activity into an activity_id
     *
     * @param string $activity
     * @param int $profileId
     * @return int
     * @throws Exception
     */
    private function _convertActivity(string $activity, int $profileId): int
    {
        [$activityName, $folderId] = $this->_getEntityAndParent($activity, $profileId);

        try {
            $activity = $this->activityService->fetchByParent($activityName, $folderId);
            return $activity['activity_id'];
        }
        catch (NotFoundException) {
            $newActivity = new ActivityCreateRequest();
            $newActivity->name = $activityName;
            $newActivity->description = 'Activity created during CSV import';
            $newActivity->folder = $folderId;
            return $this->activityService->create($newActivity);
        }
    }

    /**
     * Convert the CSV-representation of a location into a location_id
     *
     * @param string $location
     * @param int $profileId
     * @return int
     * @throws Exception
     */
    private function _convertLocation(string $location, int $profileId): int
    {
        [$locationName, $folderId] = $this->_getEntityAndParent($location, $profileId);

        try {
            $location = $this->locationService->fetchByParent($locationName, $folderId);
            return $location['location_id'];
        }
        catch (NotFoundException) {
            $newLocation = new LocationCreateRequest();
            $newLocation->name = $locationName;
            $newLocation->description = 'Location created during CSV import';
            $newLocation->folder = $folderId;
            return $this->locationService->create($newLocation);
        }
    }

    /**
     * Convert the CSV-representation of an array of tags into an array of tag_id values
     *
     * @param string $tags
     * @param int $profileId
     * @return array
     * @throws Exception
     */
    private function _convertTags(string $tags, int $profileId): array
    {
        $tagArray = explode('||', $tags);
        $tagIdArray = [];
        foreach ($tagArray as $tag) {
            $tagIdArray[] = $this->_convertTag($tag, $profileId);
        }
        return $tagIdArray;
    }

    /**
     * Convert the CSV-representation of a tag into a tag_id
     *
     * @param string $tag
     * @param int $profileId
     * @return int
     * @throws Exception
     */
    private function _convertTag(string $tag, int $profileId): int
    {
        [$tagName, $folderId] = $this->_getEntityAndParent($tag, $profileId);

        try {
            $tag = $this->tagService->fetchByParent($tagName, $folderId);
            return $tag['tag_id'];
        }
        catch (NotFoundException) {
            $newTag = new TagCreateRequest();
            $newTag->name = $tagName;
            $newTag->description = 'Tag created during CSV import';
            $newTag->folder = $folderId;
            return $this->tagService->create($newTag);
        }
    }

    /**
     *
     * @param string $entity
     * @param int $profileId
     * @return array
     * @throws Exception
     */
    private function _getEntityAndParent(string $entity, int $profileId): array
    {
        if (empty($entity)) {
            throw new BlankValueException('entity cannot be blank');
        }

        $folders = explode('::', $entity);
        $entityName = array_pop($folders);
        $folderId = $this->_createFolderHierarchy($folders, $profileId);

        return [$entityName, $folderId];
    }

    /**
     * Create zero or more folders in a hierarchy returning folder_id of the last
     *
     * @param array $folders
     * @param int $profileId
     * @return int
     * @throws Exception
     */
    private function _createFolderHierarchy(array $folders, int $profileId): int
    {
        // Each profile has its own root folder
        $rootFolder = $this->folderService->fetchRootByProfile($profileId);
        $folderId = $rootFolder['folder_id'];

        // Samples: Note that a folder name alone is not unique!
        // Chores::House::Yard::Mowing and Trimming     project:Mowing and Trimming
        // Chores::House::Kitchen                       project:Kitchen
        // Creative::House::Media Room                  project:Media Room
        // Creative::Software::House::Design            project:Design

        foreach ($folders as $folderName) {
            // Make sure there's no leading or trailing whitespace
            $folderName = trim($folderName);
            try {
                $folder = $this->folderService->fetchByParent($folderName, $folderId);
                $folderId = $folder['folder_id'];
            }
            catch (NotFoundException $e) {
                $newFolder = new FolderCreateRequest();
                $newFolder->name = $folderName;
                $newFolder->profile = $profileId;
                $newFolder->parent = $folderId;
                $newFolderId = $this->folderService->create($newFolder);
                $folderId = $newFolderId;
            }
            // TODO: handle other or unexpected errors
            /*
            catch (Exception $e) {
                throw $e;
            }
            */
        }

        return $folderId;
    }

}