<?php namespace App\Service;

use App\Service\DatabaseService;

/*
 * DROP TABLE IF EXISTS user;
CREATE TABLE user
(
    user_id       INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_name     TEXT    NOT NULL,
    user_password TEXT    NOT NULL,
    user_descr    TEXT NULL,
    user_email    TEXT NULL,

    is_hidden     BOOLEAN NOT NULL DEFAULT false,
    is_deleted    BOOLEAN NOT NULL DEFAULT false,
    hidden_at     TEXT NULL,
    deleted_at    TEXT NULL,

    created_at    TEXT    NOT NULL DEFAULT current_timestamp,
    modified_at   TEXT    NOT NULL DEFAULT current_timestamp
);
 */

class UserService
{
    private DatabaseService $db;

    public function __construct(DatabaseService $databaseService) {
        $this->db = $databaseService;
    }

    public function create(string $name, string $password, string $email): int
    {
        return 0;
    }

}