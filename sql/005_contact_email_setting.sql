-- Migration 005: Add contact_email setting for contact form recipient
-- Run this migration to make contact form recipient configurable from Settings > Email

USE agit_aams;

INSERT INTO settings (setting_key, setting_value, category) VALUES
('contact_email', 'admin@agitacademy.com', 'email')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
