-- AGIT Academy Management System (AAMS) - Complete Database Schema
-- Version: 1.0
-- Database: agit_aams

CREATE DATABASE IF NOT EXISTS agitacad_portaldb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agitacad_portaldb;

-- ============================================================
-- CORE TABLES
-- ============================================================

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('complete','exam_manager') NOT NULL DEFAULT 'complete',
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    last_login DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Classes table
CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('semester','professional') NOT NULL DEFAULT 'semester',
    semester_count INT NULL DEFAULT NULL COMMENT 'Number of semesters for semester-based classes',
    current_semester INT NULL DEFAULT 1,
    duration_weeks INT NULL DEFAULT NULL COMMENT 'Duration in weeks for professional classes',
    capacity INT NULL DEFAULT NULL,
    status ENUM('active','completed','archived') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Subjects table
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    description TEXT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Lecturers table
CREATE TABLE IF NOT EXISTS lecturers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) NULL,
    password VARCHAR(255) NOT NULL,
    status ENUM('active','restricted','inactive') NOT NULL DEFAULT 'active',
    last_login DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    matric_no VARCHAR(30) NOT NULL UNIQUE,
    class_id INT NULL,
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255) NULL,
    phone VARCHAR(20) NULL,
    gender ENUM('male','female','other') NULL,
    date_of_birth DATE NULL,
    address TEXT NULL,
    guardian_name VARCHAR(100) NULL,
    guardian_phone VARCHAR(20) NULL,
    status ENUM('active','restricted','graduated','withdrawn') NOT NULL DEFAULT 'active',
    last_login DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- JUNCTION / RELATIONSHIP TABLES
-- ============================================================

-- Lecturer-Class assignment
CREATE TABLE IF NOT EXISTS lecturer_classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lecturer_id INT NOT NULL,
    class_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_lecturer_class (lecturer_id, class_id),
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Lecturer-Subject assignment
CREATE TABLE IF NOT EXISTS lecturer_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lecturer_id INT NOT NULL,
    subject_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_lecturer_subject (lecturer_id, subject_id),
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Class-Subject assignment (which subjects are offered in which class)
CREATE TABLE IF NOT EXISTS class_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    subject_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_class_subject (class_id, subject_id),
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Class schedules (timetable)
CREATE TABLE IF NOT EXISTS class_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    subject_id INT NOT NULL,
    lecturer_id INT NOT NULL,
    day_of_week VARCHAR(20) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    room VARCHAR(100) NULL,
    notes TEXT NULL,
    status ENUM('active','cancelled') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- EXAM / CBT TABLES (Phase 2)
-- ============================================================

-- Exams
CREATE TABLE IF NOT EXISTS exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    class_id INT NOT NULL,
    subject_id INT NOT NULL,
    lecturer_id INT NOT NULL,
    exam_type ENUM('objective','theory','fill_in_gap','mixed') NOT NULL DEFAULT 'objective',
    duration_minutes INT NOT NULL DEFAULT 60,
    total_marks DECIMAL(6,2) NOT NULL DEFAULT 100,
    pass_mark DECIMAL(6,2) NULL DEFAULT 50,
    instructions TEXT NULL,
    shuffle_questions TINYINT(1) NOT NULL DEFAULT 0,
    show_result TINYINT(1) NOT NULL DEFAULT 1,
    status ENUM('draft','pending','approved','rejected','active','completed','archived') NOT NULL DEFAULT 'draft',
    approved_by INT NULL,
    approved_at DATETIME NULL,
    started_at DATETIME NULL,
    ended_at DATETIME NULL,
    rejection_reason TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Exam Questions
CREATE TABLE IF NOT EXISTS exam_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('mcq','true_false','fill_in','descriptive') NOT NULL DEFAULT 'mcq',
    option_a VARCHAR(500) NULL,
    option_b VARCHAR(500) NULL,
    option_c VARCHAR(500) NULL,
    option_d VARCHAR(500) NULL,
    correct_answer VARCHAR(500) NULL,
    marks DECIMAL(5,2) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Exam Attempts
CREATE TABLE IF NOT EXISTS exam_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    student_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NULL,
    score DECIMAL(6,2) NULL,
    total_marks DECIMAL(6,2) NULL,
    percentage DECIMAL(5,2) NULL,
    status ENUM('in_progress','submitted','graded','timed_out') NOT NULL DEFAULT 'in_progress',
    ip_address VARCHAR(45) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_exam_student (exam_id, student_id),
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Exam Answers
CREATE TABLE IF NOT EXISTS exam_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    question_id INT NOT NULL,
    student_answer TEXT NULL,
    is_correct TINYINT(1) NULL,
    marks_awarded DECIMAL(5,2) NULL DEFAULT 0,
    graded_by INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES exam_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES exam_questions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- SCORES TABLE (Phase 2) - Manual score entry by lecturers
-- ============================================================

CREATE TABLE IF NOT EXISTS scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    class_id INT NOT NULL,
    lecturer_id INT NOT NULL,
    ca_score DECIMAL(5,2) NULL DEFAULT 0 COMMENT 'Continuous Assessment score',
    exam_score DECIMAL(5,2) NULL DEFAULT 0 COMMENT 'Exam score',
    total_score DECIMAL(5,2) NULL DEFAULT 0,
    grade VARCHAR(5) NULL,
    semester INT NULL DEFAULT 1,
    academic_session VARCHAR(20) NULL,
    remark VARCHAR(100) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_student_subject_sem (student_id, subject_id, class_id, semester),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- ASSIGNMENTS TABLE (Phase 2)
-- ============================================================

CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NULL,
    class_id INT NOT NULL,
    subject_id INT NOT NULL,
    lecturer_id INT NOT NULL,
    due_date DATETIME NOT NULL,
    max_score DECIMAL(5,2) NOT NULL DEFAULT 100,
    file_path VARCHAR(255) NULL,
    status ENUM('active','closed','archived') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS assignment_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    student_id INT NOT NULL,
    file_path VARCHAR(255) NULL,
    submission_text TEXT NULL,
    score DECIMAL(5,2) NULL,
    feedback TEXT NULL,
    submitted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    graded_at DATETIME NULL,
    UNIQUE KEY unique_assignment_student (assignment_id, student_id),
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- SETTINGS & CONFIGURATION TABLES (Phase 3)
-- ============================================================

-- Portal settings (key-value)
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    category VARCHAR(50) NOT NULL DEFAULT 'general',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Grading configurations
CREATE TABLE IF NOT EXISTS grading_configs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    min_score DECIMAL(5,2) NOT NULL,
    max_score DECIMAL(5,2) NOT NULL,
    grade VARCHAR(5) NOT NULL,
    remark VARCHAR(50) NULL,
    config_group INT NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Grading config class assignment
CREATE TABLE IF NOT EXISTS grading_config_classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_group INT NOT NULL,
    class_id INT NOT NULL,
    UNIQUE KEY unique_config_class (config_group, class_id),
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Promotion rules
CREATE TABLE IF NOT EXISTS promotion_rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    rule_type VARCHAR(50) NOT NULL COMMENT 'min_average, min_pass_subjects, etc.',
    rule_value DECIMAL(5,2) NOT NULL,
    class_id INT NULL COMMENT 'NULL means applies to all classes',
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Activity logs
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('admin','lecturer','student') NOT NULL,
    user_id INT NOT NULL,
    action VARCHAR(200) NOT NULL,
    description TEXT NULL,
    ip_address VARCHAR(45) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_type, user_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- ============================================================
-- NOTIFICATIONS TABLE
-- ============================================================

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('admin','lecturer','student') NOT NULL,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    link VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_read (user_type, user_id, is_read)
) ENGINE=InnoDB;

-- ============================================================
-- STUDY MATERIALS
-- ============================================================
CREATE TABLE IF NOT EXISTS materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NULL,
    class_id INT NOT NULL,
    subject_id INT NOT NULL,
    lecturer_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NULL,
    file_size INT NULL,
    download_count INT NOT NULL DEFAULT 0,
    status ENUM('active','archived') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Assignments
CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NULL,
    class_id INT NOT NULL,
    subject_id INT NOT NULL,
    lecturer_id INT NOT NULL,
    due_date DATETIME NULL,
    total_marks DECIMAL(5,2) NOT NULL DEFAULT 100,
    file_path VARCHAR(255) NULL,
    file_name VARCHAR(255) NULL,
    status ENUM('active','closed','archived') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS assignment_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    student_id INT NOT NULL,
    answer_text TEXT NULL,
    file_path VARCHAR(255) NULL,
    file_name VARCHAR(255) NULL,
    score DECIMAL(5,2) NULL,
    feedback TEXT NULL,
    status ENUM('submitted','graded') NOT NULL DEFAULT 'submitted',
    submitted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    graded_at DATETIME NULL,
    UNIQUE KEY unique_submission (assignment_id, student_id),
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Announcements
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    author_id INT NOT NULL,
    target_audience ENUM('all','students','lecturers') NOT NULL DEFAULT 'all',
    priority ENUM('normal','important','urgent') NOT NULL DEFAULT 'normal',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- DEFAULT DATA
-- ============================================================

-- Default admin account (password: password)
INSERT INTO admins (name, email, password, role, status) VALUES
('Super Admin', 'admin@agit.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'complete', 'active');

-- Ensure assignments table has all required columns
ALTER TABLE assignments ADD COLUMN IF NOT EXISTS total_marks DECIMAL(5,2) NOT NULL DEFAULT 100 AFTER due_date;
ALTER TABLE assignments ADD COLUMN IF NOT EXISTS file_name VARCHAR(255) NULL AFTER file_path;
ALTER TABLE assignment_submissions ADD COLUMN IF NOT EXISTS answer_text TEXT NULL AFTER student_id;
ALTER TABLE assignment_submissions ADD COLUMN IF NOT EXISTS file_name VARCHAR(255) NULL AFTER file_path;
ALTER TABLE assignment_submissions ADD COLUMN IF NOT EXISTS status ENUM('submitted','graded') NOT NULL DEFAULT 'submitted' AFTER feedback;

-- Profile picture columns
ALTER TABLE students ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) NULL;
ALTER TABLE lecturers ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) NULL;
ALTER TABLE admins ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) NULL;

-- Student restrictions
ALTER TABLE students ADD COLUMN IF NOT EXISTS restriction_type VARCHAR(100) NULL;
ALTER TABLE students ADD COLUMN IF NOT EXISTS restriction_reason TEXT NULL;
ALTER TABLE students ADD COLUMN IF NOT EXISTS restricted_at DATETIME NULL;
ALTER TABLE students ADD COLUMN IF NOT EXISTS restricted_by INT NULL;

-- Admin permissions table
CREATE TABLE IF NOT EXISTS admin_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    allowed_pages TEXT NOT NULL COMMENT 'JSON array of page slugs',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Class schedules table
CREATE TABLE IF NOT EXISTS class_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    subject_id INT NOT NULL,
    lecturer_id INT NOT NULL,
    day_of_week ENUM('monday','tuesday','wednesday','thursday','friday','saturday','sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    room VARCHAR(100) NULL,
    notes TEXT NULL,
    status ENUM('active','cancelled') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Default portal settings
INSERT INTO settings (setting_key, setting_value, category) VALUES
('portal_name', 'AGIT Academy', 'general'),
('portal_tagline', 'Excellence in Education', 'general'),
('portal_email', 'info@agit.edu', 'general'),
('portal_phone', '+234 000 000 0000', 'general'),
('student_registration', 'disabled', 'general'),
('allow_registration', '0', 'general'),
('grading_system', 'percentage', 'academic'),
('ca_weight', '40', 'academic'),
('exam_weight', '60', 'academic'),
('max_login_attempts', '5', 'security'),
('session_timeout', '3600', 'security');
