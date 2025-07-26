-- --------------------------------------------------
-- Clear out database
-- --------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;

SET @tables = NULL;
SELECT GROUP_CONCAT(TABLE_NAME) INTO @tables
FROM information_schema.tables
WHERE table_schema = (SELECT DATABASE())
  AND table_type = 'BASE TABLE';

SELECT IFNULL(@tables, 'dummy') INTO @tables;

SET @drop_statement = CONCAT('DROP TABLE IF EXISTS ', @tables);

PREPARE stmt FROM @drop_statement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------
-- Run DDL from dbdiagram.io
-- --------------------------------------------------

SOURCE timekeeper5.ddl.sql
SOURCE timekeeper5.ddl-ext.sql

-- --------------------------------------------------
-- Popualate tables
-- --------------------------------------------------

SOURCE timekeeper5.data.sql
