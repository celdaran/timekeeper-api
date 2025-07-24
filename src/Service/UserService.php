<?php namespace App\Service;

use App\Service\DatabaseService;

class UserService
{
    private DatabaseService $db;

    public function __construct(DatabaseService $databaseService)
    {
        $this->db = $databaseService;
    }

    public function create(string $name, string $password, string $email): array
    {
        // Create row in users table
        $payload = [
            'user_name' => $name,
            'user_password' => $password,
            'user_descr' => $name,
            'user_email' => $email,
            'is_hidden' => 0,
            'is_deleted' => 0,
        ];
        $userId = $this->db->insert('users', $payload);

        // Create default profile
        $payload = [
            'profile_name' => 'Default',
            'profile_descr' => 'This is the default profile',
            'user_id' => $userId,
            'is_hidden' => 0,
            'is_deleted' => 0,
        ];
        $profileId = $this->db->insert('profile', $payload);

        // Create profile-specific, invisible root folder
        $payload = [
            'folder_name' => '06e4c5e9-9f05-4f82-9d70-f8a04ff205f1',
            'folder_descr' => 'This is the root folder, required by the schema and hidden from the user.',
            'profile_id' => $profileId,
            'is_hidden' => 1,
        ];
        $folderId = $this->db->insert('folder', $payload);

        // Create default dimensions
        $payload = [
            'project_name' => 'Default Project',
            'project_descr' => 'This is the default project. Feel free to use as-is or edit as needed.',
            'profile_id' => $profileId,
            'folder_id' => $folderId,
            'sort_order' => 1,
            'is_hidden' => 0,
            'is_deleted' => 0,
        ];
        $this->db->insert('project', $payload);

        $payload = [
            'activity_name' => 'Default Activity',
            'activity_descr' => 'This is the default activity. Feel free to use as-is or edit as needed.',
            'profile_id' => $profileId,
            'folder_id' => $folderId,
            'sort_order' => 1,
            'is_hidden' => 0,
            'is_deleted' => 0,
        ];
        $this->db->insert('activity', $payload);

        $payload = [
            'location_name' => 'Default Location',
            'location_descr' => 'This is the default location. Feel free to use as-is or edit as needed.',
            'user_id' => $userId,
            'folder_id' => $folderId,
            'sort_order' => 1,
            'is_hidden' => 0,
            'is_deleted' => 0,
            'ref_time_zone_id' => 337, // "Europe/London"
        ];
        $this->db->insert('location', $payload);

        return [
            'user_id' => $userId,
            'profile_id' => $profileId,
            'folder_id' => $folderId,
        ];
    }

}