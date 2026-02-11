<?php
/**
 * Application Configuration
 * AGIT Academy Management System
 */

// App info
define('APP_NAME', 'AGIT Academy');
define('APP_TAGLINE', 'Excellence in Education');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/agit-portal');

// Paths
define('BASE_PATH', dirname(__DIR__));
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('VIEWS_PATH', BASE_PATH . '/views');

// Session config
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_NAME', 'AAMS_SESSION');

// Upload limits
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_DOC_TYPES', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'image/jpeg', 'image/png']);

// Pagination
define('RECORDS_PER_PAGE', 15);
