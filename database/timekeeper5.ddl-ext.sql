-- -----------------------------------------------
-- Add auto-update for modified_at columns
-- -----------------------------------------------

-- Step 1: Disable foreign key checks (optional but recommended for safety)
SET FOREIGN_KEY_CHECKS = 0;

-- Step 2: Make sure all modified_at columns auto-update
ALTER TABLE `tag` MODIFY COLUMN `modified_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `project_group_project` MODIFY COLUMN `modified_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `_schema` MODIFY COLUMN `modified_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `ref_date_preset` MODIFY COLUMN `modified_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `ref_time_zone` MODIFY COLUMN `modified_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `project` MODIFY COLUMN `modified_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `journal` MODIFY COLUMN `modified_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `ref_group_by` MODIFY COLUMN `modified_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `project_group` MODIFY COLUMN `modified_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `folder` MODIFY COLUMN `modified_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `account` MODIFY COLUMN `modified_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `activity` MODIFY COLUMN `modified_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `profile` MODIFY COLUMN `modified_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `location` MODIFY COLUMN `modified_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `ref_time_display` MODIFY COLUMN `modified_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Step 3: Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------
-- Create unique constraints on folder hierarchy
-- -----------------------------------------------

ALTER TABLE folder
    ADD folder_id__parent_normalized INT GENERATED ALWAYS AS (
        IFNULL(folder_id__parent, 0)
        ) STORED
;

CREATE UNIQUE INDEX `idx_folder_name`
    ON folder (folder_name, folder_id__parent_normalized, profile_id)
;
