<?php
/**
 * Route Protection Middleware
 * AGIT Academy Management System
 */

/**
 * Protect admin routes
 */
function adminMiddleware() {
    requireAuth('admin');
}

/**
 * Protect faculty/lecturer routes
 */
function facultyMiddleware() {
    requireAuth('lecturer');
}

/**
 * Protect student routes
 */
function studentMiddleware() {
    requireAuth('student');
}

/**
 * Protect admin routes that require specific admin role
 */
function adminRoleMiddleware($requiredRole) {
    requireAuth('admin');
    if (!hasAdminPermission($requiredRole)) {
        if (isApiRequest()) {
            jsonResponse(['success' => false, 'message' => 'Insufficient permissions.'], 403);
        }
        header('Location: ' . APP_URL . '/admin/dashboard');
        exit;
    }
}

/**
 * Rate limiting (simple in-session implementation)
 */
function rateLimit($key, $maxAttempts = 5, $decayMinutes = 15) {
    initSession();
    $now = time();
    $cacheKey = 'rate_limit_' . $key;
    
    if (!isset($_SESSION[$cacheKey])) {
        $_SESSION[$cacheKey] = ['attempts' => 0, 'reset_at' => $now + ($decayMinutes * 60)];
    }
    
    if ($now > $_SESSION[$cacheKey]['reset_at']) {
        $_SESSION[$cacheKey] = ['attempts' => 0, 'reset_at' => $now + ($decayMinutes * 60)];
    }
    
    $_SESSION[$cacheKey]['attempts']++;
    
    if ($_SESSION[$cacheKey]['attempts'] > $maxAttempts) {
        return false;
    }
    
    return true;
}
