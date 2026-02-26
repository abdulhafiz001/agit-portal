USE agit_aams;

ALTER TABLE students ADD COLUMN approval_status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending' AFTER status;
ALTER TABLE students ADD COLUMN rejection_reason TEXT NULL AFTER approval_status;
ALTER TABLE students ADD COLUMN approved_at DATETIME NULL AFTER rejection_reason;
ALTER TABLE students ADD COLUMN approved_by INT NULL AFTER approved_at;
ALTER TABLE students MODIFY COLUMN matric_no VARCHAR(30) NULL UNIQUE;
UPDATE students SET approval_status = 'approved', approved_at = COALESCE(approved_at, created_at) WHERE matric_no IS NOT NULL AND matric_no != '';
UPDATE students SET matric_no = CONCAT('AGIT/', YEAR(NOW()), '/', LPAD(id, 4, '0')), approval_status = 'approved', approved_at = created_at WHERE (matric_no IS NULL OR matric_no = '') AND approval_status != 'pending';
CREATE TABLE IF NOT EXISTS student_approval_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    action ENUM('approve','decline') NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_student (student_id),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB;
