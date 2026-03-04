-- Migration 009: Force password change for admin-created students
-- Adds must_change_password flag on students table.

ALTER TABLE students
    ADD COLUMN must_change_password TINYINT(1) NOT NULL DEFAULT 0 AFTER password;

