-- Migration: Make assignments schema compatible across old/new installs
-- Safe for MySQL/MariaDB versions that do not support "ADD COLUMN IF NOT EXISTS"

USE agit_aams;

-- assignments.total_marks (fallback from older max_score schema)
SET @col_exists := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'assignments'
      AND COLUMN_NAME = 'total_marks'
);
SET @sql := IF(@col_exists = 0,
    'ALTER TABLE assignments ADD COLUMN total_marks DECIMAL(5,2) NOT NULL DEFAULT 100 AFTER due_date',
    'SELECT ''assignments.total_marks already exists''');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- assignments.file_name
SET @col_exists := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'assignments'
      AND COLUMN_NAME = 'file_name'
);
SET @sql := IF(@col_exists = 0,
    'ALTER TABLE assignments ADD COLUMN file_name VARCHAR(255) NULL AFTER file_path',
    'SELECT ''assignments.file_name already exists''');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- assignment_submissions.answer_text
SET @col_exists := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'assignment_submissions'
      AND COLUMN_NAME = 'answer_text'
);
SET @sql := IF(@col_exists = 0,
    'ALTER TABLE assignment_submissions ADD COLUMN answer_text TEXT NULL AFTER student_id',
    'SELECT ''assignment_submissions.answer_text already exists''');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- assignment_submissions.file_name
SET @col_exists := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'assignment_submissions'
      AND COLUMN_NAME = 'file_name'
);
SET @sql := IF(@col_exists = 0,
    'ALTER TABLE assignment_submissions ADD COLUMN file_name VARCHAR(255) NULL AFTER file_path',
    'SELECT ''assignment_submissions.file_name already exists''');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- assignment_submissions.status
SET @col_exists := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'assignment_submissions'
      AND COLUMN_NAME = 'status'
);
SET @sql := IF(@col_exists = 0,
    'ALTER TABLE assignment_submissions ADD COLUMN status ENUM(''submitted'',''graded'') NOT NULL DEFAULT ''submitted'' AFTER feedback',
    'SELECT ''assignment_submissions.status already exists''');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
