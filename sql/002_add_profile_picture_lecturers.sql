-- Migration: Add profile_picture to lecturers and admins (if missing)
-- Run this if profile picture upload is used for lecturers/admins
-- Skip any line that errors (column may already exist)

USE agit_aams;

ALTER TABLE lecturers ADD COLUMN profile_picture VARCHAR(255) NULL;
ALTER TABLE admins ADD COLUMN profile_picture VARCHAR(255) NULL;
