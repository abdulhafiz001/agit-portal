-- Allow admin role in password_reset_codes
ALTER TABLE password_reset_codes
    MODIFY COLUMN user_type ENUM('student','lecturer','admin') NOT NULL;
