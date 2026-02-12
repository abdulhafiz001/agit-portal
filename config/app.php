<?php
/**
 * Application Configuration
 * AGIT Academy Management System
 */

// App info
define('APP_NAME', 'AGIT Academy');
define('APP_TAGLINE', 'Excellence in Education');
define('APP_VERSION', '1.0.0');

/**
 * APP_URL resolution order:
 * 1) APP_URL environment variable (recommended for production)
 * 2) Auto-detect from current request host + script directory
 * 3) Localhost fallback for CLI/non-web contexts
 */
if (!defined('APP_URL')) {
    $envAppUrl = getenv('APP_URL');
    if ($envAppUrl) {
        define('APP_URL', rtrim($envAppUrl, '/'));
    } else {
        $host = $_SERVER['HTTP_HOST'] ?? null;
        if ($host) {
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
            $scheme = $isHttps ? 'https' : 'http';
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
            if ($basePath === '/' || $basePath === '.') {
                $basePath = '';
            }
            define('APP_URL', $scheme . '://' . $host . $basePath);
        } else {
            define('APP_URL', 'http://localhost/agit-portal');
        }
    }
}

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
