CREATE TABLE `_schema` (
    `schema_id`      INT PRIMARY KEY    NOT NULL AUTO_INCREMENT,
    `schema_version` VARCHAR(31) UNIQUE NOT NULL,
    `schema_descr`   VARCHAR(255)       NOT NULL,
    `commit_sha`     VARCHAR(40)        NOT NULL,
    `applied_at`     DATETIME           NOT NULL DEFAULT (CURRENT_TIMESTAMP),
    `applied_by`     VARCHAR(255)       NOT NULL,
    `status_id`      INT                NOT NULL,
    `created_at`     DATETIME           NOT NULL DEFAULT (CURRENT_TIMESTAMP),
    `modified_at`    DATETIME           NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `ref_date_preset` (
    `ref_date_preset_id`    INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `ref_date_preset_name`  VARCHAR(255)    NOT NULL,
    `ref_date_preset_descr` TEXT,
    `created_at`            DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP),
    `modified_at`           DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `ref_group_by` (
    `ref_group_by_id`    INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `ref_group_by_name`  VARCHAR(255)    NOT NULL,
    `ref_group_by_descr` TEXT,
    `created_at`         DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP),
    `modified_at`        DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `ref_time_display` (
    `ref_time_display_id`    INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `ref_time_display_name`  VARCHAR(255)    NOT NULL,
    `ref_time_display_descr` TEXT,
    `created_at`             DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP),
    `modified_at`            DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `ref_time_zone` (
    `ref_time_zone_id`    INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `ref_time_zone_name`  VARCHAR(255)    NOT NULL,
    `ref_time_zone_descr` TEXT,
    `created_at`          DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP),
    `modified_at`         DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `account` (
    `account_id`        INT PRIMARY KEY     NOT NULL AUTO_INCREMENT,
    `account_username`  VARCHAR(255) UNIQUE NOT NULL,
    `account_password`  VARCHAR(255)        NOT NULL,
    `account_email`     VARCHAR(320) UNIQUE NOT NULL,
    `account_descr`     TEXT,
    `token`             VARCHAR(36),
    `project_id__last`  INT,
    `location_id__last` INT,
    `is_admin`          BOOL                NOT NULL DEFAULT FALSE,
    `login_at`          DATETIME,
    `is_hidden`         BOOL                NOT NULL DEFAULT FALSE,
    `is_deleted`        BOOL                NOT NULL DEFAULT FALSE,
    `hidden_at`         DATETIME,
    `deleted_at`        DATETIME,
    `created_at`        DATETIME            NOT NULL DEFAULT (CURRENT_TIMESTAMP),
    `modified_at`       DATETIME            NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `profile` (
    `profile_id`    INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `profile_name`  VARCHAR(255)    NOT NULL,
    `profile_descr` TEXT,
    `account_id`    INT             NOT NULL,
    `is_hidden`     BOOL            NOT NULL DEFAULT FALSE,
    `is_deleted`    BOOL            NOT NULL DEFAULT FALSE,
    `hidden_at`     DATETIME,
    `deleted_at`    DATETIME,
    `created_at`    DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP),
    `modified_at`   DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `folder` (
    `folder_id`         INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `folder_name`       VARCHAR(255)    NOT NULL,
    `folder_descr`      TEXT,
    `profile_id`        INT             NOT NULL,
    `folder_id__parent` INT             NOT NULL,
    `sort_order`        INT             NOT NULL DEFAULT 0,
    `is_system`         BOOL            NOT NULL DEFAULT FALSE,
    `is_open`           BOOL            NOT NULL DEFAULT TRUE,
    `is_hidden`         BOOL            NOT NULL DEFAULT FALSE,
    `is_deleted`        BOOL            NOT NULL DEFAULT FALSE,
    `hidden_at`         DATETIME,
    `deleted_at`        DATETIME,
    `created_at`        DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP),
    `modified_at`       DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `activity` (
    `activity_id`    INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `activity_name`  VARCHAR(255)    NOT NULL,
    `activity_descr` TEXT,
    `folder_id`      INT             NOT NULL,
    `sort_order`     INT             NOT NULL DEFAULT 0,
    `is_hidden`      BOOL            NOT NULL DEFAULT FALSE,
    `is_deleted`     BOOL            NOT NULL DEFAULT FALSE,
    `hidden_at`      DATETIME,
    `deleted_at`     DATETIME,
    `created_at`     DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP),
    `modified_at`    DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `location` (
    `location_id`      INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `location_name`    VARCHAR(255)    NOT NULL,
    `location_descr`   TEXT,
    `ref_time_zone_id` INT             NOT NULL,
    `folder_id`        INT             NOT NULL,
    `sort_order`       INT             NOT NULL DEFAULT 0,
    `is_hidden`        BOOL            NOT NULL DEFAULT FALSE,
    `is_deleted`       BOOL            NOT NULL DEFAULT FALSE,
    `hidden_at`        DATETIME,
    `deleted_at`       DATETIME,
    `created_at`       DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP),
    `modified_at`      DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `project` (
    `project_id`        INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `project_name`      VARCHAR(255)    NOT NULL,
    `project_descr`     TEXT,
    `activity_id__last` INT,
    `external_ident`    TEXT,
    `external_url`      TEXT,
    `folder_id`         INT             NOT NULL,
    `sort_order`        INT             NOT NULL DEFAULT 0,
    `is_hidden`         BOOL            NOT NULL DEFAULT FALSE,
    `is_deleted`        BOOL            NOT NULL DEFAULT FALSE,
    `hidden_at`         DATETIME,
    `deleted_at`        DATETIME,
    `created_at`        DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP),
    `modified_at`       DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `tag` (
    `tag_id`      INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `tag_name`    VARCHAR(255)    NOT NULL,
    `tag_descr`   TEXT,
    `folder_id`   INT             NOT NULL,
    `sort_order`  INT             NOT NULL DEFAULT 0,
    `is_hidden`   BOOL            NOT NULL DEFAULT FALSE,
    `is_deleted`  BOOL            NOT NULL DEFAULT FALSE,
    `hidden_at`   DATETIME,
    `deleted_at`  DATETIME,
    `created_at`  DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP),
    `modified_at` DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `journal` (
    `journal_id`    INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `start_time`    DATETIME        NOT NULL,
    `stop_time`     DATETIME        NOT NULL,
    `duration`      INT             NOT NULL,
    `memo`          TEXT,
    `profile_id`    INT             NOT NULL,
    `project_id`    INT             NOT NULL,
    `activity_id`   INT             NOT NULL,
    `location_id`   INT             NOT NULL,
    `is_locked`     BOOL            NOT NULL DEFAULT FALSE,
    `is_ignored`    BOOL            NOT NULL DEFAULT FALSE,
    `is_reconciled` BOOL            NOT NULL DEFAULT FALSE,
    `created_at`    DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP),
    `modified_at`   DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `journal_tag` (
    `journal_tag_id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `journal_id`     INT             NOT NULL,
    `tag_id`         INT             NOT NULL,
    `created_at`     DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `project_group` (
    `project_group_id`    INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `project_group_name`  VARCHAR(255)    NOT NULL,
    `project_group_descr` TEXT,
    `account_id`          INT             NOT NULL,
    `is_hidden`           BOOL            NOT NULL DEFAULT FALSE,
    `is_deleted`          BOOL            NOT NULL DEFAULT FALSE,
    `hidden_at`           DATETIME,
    `deleted_at`          DATETIME,
    `created_at`          DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP),
    `modified_at`         DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `project_group_project` (
    `project_group_project_id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `project_group_id`         INT             NOT NULL,
    `project_id`               INT             NOT NULL,
    `created_at`               DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `import_log` (
    `import_id`          INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `original_file_name` TEXT,
    `row_count`          INT,
    `succeeded`          BOOL            NOT NULL DEFAULT FALSE,
    `imported_at`        DATETIME        NOT NULL DEFAULT (CURRENT_TIMESTAMP)
);

CREATE UNIQUE INDEX `idx_schema_version` ON `_schema` (`schema_version`);

CREATE UNIQUE INDEX `idx_profile_name` ON `profile` (`account_id`, `profile_name`);

CREATE INDEX `idx_folder_profile_id` ON `folder` (`profile_id`);

CREATE INDEX `idx_folder_parent_id` ON `folder` (`folder_id__parent`);

CREATE UNIQUE INDEX `idx_folder_name` ON `folder` (`folder_name`, `folder_id__parent`, `profile_id`);

CREATE INDEX `idx_activity_folder_id` ON `activity` (`folder_id`);

CREATE UNIQUE INDEX `idx_activity_name` ON `activity` (`folder_id`, `activity_name`);

CREATE INDEX `idx_location_folder_id` ON `location` (`folder_id`);

CREATE UNIQUE INDEX `idx_location_name` ON `location` (`folder_id`, `location_name`);

CREATE INDEX `idx_project_folder_id` ON `project` (`folder_id`);

CREATE UNIQUE INDEX `idx_project_name` ON `project` (`folder_id`, `project_name`);

CREATE INDEX `idx_tag_folder_id` ON `tag` (`folder_id`);

CREATE UNIQUE INDEX `idx_tag_name` ON `tag` (`folder_id`, `tag_name`);

CREATE INDEX `idx_journal_profile_id` ON `journal` (`profile_id`);

CREATE INDEX `idx_journal_project_id` ON `journal` (`project_id`);

CREATE INDEX `idx_journal_activity_id` ON `journal` (`activity_id`);

CREATE INDEX `idx_journal_location_id` ON `journal` (`location_id`);

CREATE UNIQUE INDEX `idx_journal_start_time` ON `journal` (`start_time`, `profile_id`);

CREATE UNIQUE INDEX `idx_project_group_accounts` ON `project_group` (`project_group_name`, `account_id`);

CREATE UNIQUE INDEX `idx_project_group_projects` ON `project_group_project` (`project_group_id`, `project_id`);

ALTER TABLE `profile`
    ADD FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`);

ALTER TABLE `folder`
    ADD FOREIGN KEY (`profile_id`) REFERENCES `profile` (`profile_id`);

ALTER TABLE `folder`
    ADD FOREIGN KEY (`folder_id__parent`) REFERENCES `folder` (`folder_id`);

ALTER TABLE `activity`
    ADD FOREIGN KEY (`folder_id`) REFERENCES `folder` (`folder_id`);

ALTER TABLE `location`
    ADD FOREIGN KEY (`folder_id`) REFERENCES `folder` (`folder_id`);

ALTER TABLE `location`
    ADD FOREIGN KEY (`ref_time_zone_id`) REFERENCES `ref_time_zone` (`ref_time_zone_id`);

ALTER TABLE `project`
    ADD FOREIGN KEY (`folder_id`) REFERENCES `folder` (`folder_id`);

ALTER TABLE `account`
    ADD FOREIGN KEY (`project_id__last`) REFERENCES `project` (`project_id`);

ALTER TABLE `project`
    ADD FOREIGN KEY (`activity_id__last`) REFERENCES `activity` (`activity_id`);

ALTER TABLE `account`
    ADD FOREIGN KEY (`location_id__last`) REFERENCES `location` (`location_id`);

ALTER TABLE `tag`
    ADD FOREIGN KEY (`folder_id`) REFERENCES `folder` (`folder_id`);

ALTER TABLE `journal`
    ADD FOREIGN KEY (`profile_id`) REFERENCES `profile` (`profile_id`);

ALTER TABLE `journal`
    ADD FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`);

ALTER TABLE `journal`
    ADD FOREIGN KEY (`activity_id`) REFERENCES `activity` (`activity_id`);

ALTER TABLE `journal`
    ADD FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`);

ALTER TABLE `journal_tag`
    ADD FOREIGN KEY (`journal_id`) REFERENCES `journal` (`journal_id`);

ALTER TABLE `journal_tag`
    ADD FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`);

ALTER TABLE `project_group`
    ADD FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`);

ALTER TABLE `project_group_project`
    ADD FOREIGN KEY (`project_group_id`) REFERENCES `project_group` (`project_group_id`);

ALTER TABLE `project_group_project`
    ADD FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`);
