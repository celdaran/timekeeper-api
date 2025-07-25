<?php namespace App\Service;

abstract class BaseService
{
    protected DatabaseService $db;

    protected array $columnMap;

    //--------------------------------------------
    // Constructor
    //--------------------------------------------
    public function __construct(DatabaseService $databaseService)
    {
        $this->db = $databaseService;
    }

    //--------------------------------------------
    // Methods to be overridden
    //--------------------------------------------

    abstract protected function fetch(int $id): array;

    abstract protected function update(int $id, array $data): bool;

    abstract protected function delete(int $id): bool;

    abstract protected function hide(int $id): bool;

    abstract protected function undelete(int $id): bool;

    abstract protected function unhide(int $id): bool;

    //--------------------------------------------
    // Helpers
    //--------------------------------------------

    protected function _fetch(string $table, string $pk, int $id): array
    {
        return $this->db->selectRow($table, $pk, $id);
    }

    protected function _update(string $table, string $pk, int $id, array $data): bool
    {
        $payload = [];
        foreach ($data as $key => $value) {
            if ($key === 'password') {
                $payload[$this->columnMap[$key]] = password_hash($value, PASSWORD_ARGON2ID);
            } else {
                $payload[$this->columnMap[$key]] = $value;
            }
        }
        $payload['modified_at'] = date('Y-m-d H:i:s');
        return $this->db->update($table, $payload, $pk, $id);
    }

    protected function _delete(string $table, string $pk, int $id): bool
    {
        $payload = [
            'is_deleted' => 1,
            'deleted_at' => date('Y-m-d H:i:s'),
            'modified_at' => date('Y-m-d H:i:s'),
        ];
        return $this->db->update($table, $payload, $pk, $id);
    }

    protected function _hide(string $table, string $pk, int $id): bool
    {
        $payload = [
            'is_hidden' => 1,
            'hidden_at' => date('Y-m-d H:i:s'),
            'modified_at' => date('Y-m-d H:i:s'),
        ];
        return $this->db->update($table, $payload, $pk, $id);
    }

    protected function _undelete(string $table, string $pk, int $id): bool
    {
        $payload = [
            'is_deleted' => 0,
            'deleted_at' => null,
            'modified_at' => date('Y-m-d H:i:s'),
        ];
        return $this->db->update($table, $payload, $pk, $id);
    }

    protected function _unhide(string $table, string $pk, int $id): bool
    {
        $payload = [
            'is_hidden' => 0,
            'hidden_at' => null,
            'modified_at' => date('Y-m-d H:i:s'),
        ];
        return $this->db->update($table, $payload, $pk, $id);
    }

}