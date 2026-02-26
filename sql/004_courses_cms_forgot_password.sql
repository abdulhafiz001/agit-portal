-- Migration 004: Courses CMS, Contact, Forgot Password, Faculty Stats
-- Run this migration to add new features
-- Skip any statement that errors (column/table may already exist)

USE agitacad_portaldb;

-- 1. Subjects/Courses: Add image, duration, display_on_landing
ALTER TABLE subjects ADD COLUMN image VARCHAR(255) NULL AFTER description;
ALTER TABLE subjects ADD COLUMN duration VARCHAR(50) NULL COMMENT 'e.g. 12 weeks, 6 months' AFTER image;
ALTER TABLE subjects ADD COLUMN display_on_landing TINYINT(1) NOT NULL DEFAULT 1 AFTER duration;

-- 2. Course topics table (admin adds topics per course)
CREATE TABLE IF NOT EXISTS course_topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    topic_title VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    INDEX idx_subject (subject_id)
) ENGINE=InnoDB;

-- 3. Classes: Add suspended status (if enum allows)
-- MySQL: Modify enum to include suspended
ALTER TABLE classes MODIFY COLUMN status ENUM('active','suspended','completed','archived') NOT NULL DEFAULT 'active';

-- 4. Password reset codes for forgot password
CREATE TABLE IF NOT EXISTS password_reset_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('student','lecturer') NOT NULL,
    user_id INT NOT NULL,
    email VARCHAR(150) NOT NULL,
    code VARCHAR(6) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (code, user_type),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB;

-- 5. Contact/Enquiry form submissions (optional - for logging)
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(200) NULL,
    message TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
