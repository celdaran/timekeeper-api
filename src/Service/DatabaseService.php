<?php namespace App\Service;

use PDO;
use InvalidArgumentException;
use App\Exception\NotFoundException;

class DatabaseService
{
    private PDO $pdo;

    public function __construct(string $dsn, string $username, string $password)
    {
        $this->pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public function selectRow(string $table, string $columnName, mixed $value, string $columns = '*'): array
    {
        $sql = "SELECT $columns FROM $table WHERE $columnName = ?";

        $sth = $this->pdo->prepare($sql);
        $sth->execute([$value]);

        $row = $sth->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            throw new NotFoundException("no row found in table $table where $columnName = $value");
        }

        return $row;
    }

    public function selectRowByParent(
        string $table,
        string $columnName, mixed $value,
        string $parentColumnName, mixed $parentValue,
        string $columns = '*'): array
    {
        if ($parentValue === null) {
            $sql = "SELECT $columns FROM $table WHERE $columnName = ? AND $parentColumnName IS NULL";
            $sth = $this->pdo->prepare($sql);
            $sth->execute([$value]);
        } else {
            $sql = "SELECT $columns FROM $table WHERE $columnName = ? AND $parentColumnName = ?";
            $sth = $this->pdo->prepare($sql);
            $sth->execute([$value, $parentValue]);
        }

        $row = $sth->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            if ($parentValue === null) {
                $tmp = "`$parentColumnName` IS NULL";
            } else {
                $tmp = "`$parentColumnName` = '$parentValue'";
            }
            throw new NotFoundException("No row found in table `$table` where `$columnName` = '$value' AND $tmp");
        }

        return $row;
    }

    public function insert(string $table, array $row): int
    {
        $columns = array_keys($row);
        $columnPlaceholders = [];
        foreach ($columns as $column) {
            $columnPlaceholders[] = ':' . $column;
        }

        $sql = "INSERT INTO $table";
        $sql .= "(" . implode(", ", $columns) . ")";
        $sql .= " VALUES ";
        $sql .= "(" . implode(", ", $columnPlaceholders) . ")";

        $sth = $this->pdo->prepare($sql);
        $sth->execute($row);

        return $this->pdo->lastInsertId();
    }

    public function update(string $table, array $row, string $columnName, mixed $value): bool
    {
        $update = $this->buildUpdateStatement($table, $row, $columnName, $value);
        $sth = $this->pdo->prepare($update['sql']);
        $sth->execute($update['params']);
        $count = $sth->rowCount();
        return ($count > 0);
    }

    public function begin(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }

    private function buildUpdateStatement(string $tableName, array $data, string $pk, mixed $id): array
    {
        if (empty($data)) {
            throw new InvalidArgumentException("Data array cannot be empty for an UPDATE statement.");
        }

        $setParts = [];
        $params = [];

        foreach ($data as $column => $value) {
            // Sanitize column name (basic check to prevent SQL injection in column names)
            // For true robustness, you might want to whitelist allowed column names.
            // Assuming column names are safe and not user-provided.
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
                throw new InvalidArgumentException("Invalid column name: " . htmlspecialchars($column));
            }

            // Ensure the primary key column is not part of the SET clause
            if ($column === $pk) {
                // It's generally a bad practice to try and update the PK in the SET clause
                // while simultaneously using it in the WHERE clause for the same operation.
                // If the intent is to change the PK, it's a different operation.
                continue; // Skip the primary key column in the SET clause
            }

            $placeholder = ':' . $column; // Named placeholder for PDO
            $setParts[] = "`" . $column . "` = " . $placeholder; // Use backticks for column names (good practice)
            $params[$placeholder] = $value; // Add to parameters array
        }

        if (empty($setParts)) {
            throw new InvalidArgumentException("No valid columns to update found in data array.");
        }

        // Add the primary key condition to the WHERE clause
        $pkPlaceholder = ':' . $pk;
        $params[$pkPlaceholder] = $id;

        $sql = "UPDATE `" . $tableName . "` SET "
            . implode(', ', $setParts)
            . " WHERE `" . $pk . "` = " . $pkPlaceholder;

        return [
            'sql' => $sql,
            'params' => $params,
        ];
    }

}