<?php
/**
 * Authentication Helper Functions
 * AGIT Academy Management System
 */

/**
 * Start secure session
 */
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_start();
    }
}

/**
 * Login user by role
 */
function loginUser($email, $password, $role) {
    $db = getDB();
    $table = '';
    switch ($role) {
        case 'admin': $table = 'admins'; break;
        case 'lecturer': $table = 'lecturers'; break;
        case 'student': $table = 'students'; break;
        default: return ['success' => false, 'message' => 'Invalid role.'];
    }

    $stmt = $db->prepare("SELECT * FROM {$table} WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        return ['success' => false, 'message' => 'Invalid email or password.'];
    }

    if ($user['status'] !== 'active' && $role !== 'student') {
        return ['success' => false, 'message' => 'Your account has been restricted. Contact admin.'];
    }

    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid email or password.'];
    }

    // Check for login restriction
    if ($role === 'student' && $user['status'] === 'restricted' && !empty($user['restriction_type'])) {
        $restrictions = explode(',', $user['restriction_type']);
        if (in_array('login', $restrictions)) {
            return ['success' => false, 'message' => 'Your account has been restricted. Reason: ' . ($user['restriction_reason'] ?? 'Contact admin for details.')];
        }
    }

    // Set session
    initSession();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $role;
    $_SESSION['login_time'] = time();

    if ($role === 'admin') {
        $_SESSION['admin_role'] = $user['role'];
        // Load page permissions for limited admins
        if ($user['role'] !== 'complete') {
            $perms = $db->prepare("SELECT allowed_pages FROM admin_permissions WHERE admin_id = ?");
            $perms->execute([$user['id']]);
            $permData = $perms->fetchColumn();
            $_SESSION['admin_allowed_pages'] = $permData ? json_decode($permData, true) : [];
        } else {
            $_SESSION['admin_allowed_pages'] = ['all'];
        }
    }
    if ($role === 'student') {
        $_SESSION['matric_no'] = $user['matric_no'];
        $_SESSION['class_id'] = $user['class_id'];
    }

    // Update last login
    $stmt = $db->prepare("UPDATE {$table} SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);

    // Log activity
    logActivity($role, $user['id'], 'login', 'User logged in');

    $redirect = '';
    switch ($role) {
        case 'admin': $redirect = APP_URL . '/admin/dashboard'; break;
        case 'lecturer': $redirect = APP_URL . '/faculty/dashboard'; break;
        case 'student': $redirect = APP_URL . '/student/dashboard'; break;
    }

    return ['success' => true, 'message' => 'Login successful!', 'redirect' => $redirect];
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    initSession();
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
        return false;
    }
    // Check session timeout
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > SESSION_LIFETIME)) {
        logoutUser();
        return false;
    }
    return true;
}

/**
 * Get current user data
 */
function currentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role'],
        'admin_role' => $_SESSION['admin_role'] ?? null,
        'matric_no' => $_SESSION['matric_no'] ?? null,
        'class_id' => $_SESSION['class_id'] ?? null,
    ];
}

/**
 * Require authentication for a specific role
 */
function requireAuth($role = null) {
    if (!isLoggedIn()) {
        if (isApiRequest()) {
            jsonResponse(['success' => false, 'message' => 'Unauthorized. Please login.'], 401);
        }
        header('Location: ' . APP_URL . '/login/' . ($role ?? 'admin'));
        exit;
    }
    if ($role && $_SESSION['user_role'] !== $role) {
        if (isApiRequest()) {
            jsonResponse(['success' => false, 'message' => 'Access denied.'], 403);
        }
        header('Location: ' . APP_URL . '/login/' . $role);
        exit;
    }
}

/**
 * Check admin sub-role permission
 */
function hasAdminPermission($requiredRole) {
    if ($_SESSION['user_role'] !== 'admin') return false;
    if ($_SESSION['admin_role'] === 'complete') return true;
    return $_SESSION['admin_role'] === $requiredRole;
}

/**
 * Logout user
 */
function logoutUser() {
    initSession();
    $role = $_SESSION['user_role'] ?? 'admin';
    if (isset($_SESSION['user_id'])) {
        logActivity($role, $_SESSION['user_id'], 'logout', 'User logged out');
    }
    session_unset();
    session_destroy();
    return $role;
}

/**
 * Log activity
 */
function logActivity($userType, $userId, $action, $description = '') {
    try {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO activity_logs (user_type, user_id, action, description, ip_address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userType, $userId, $action, $description, $_SERVER['REMOTE_ADDR'] ?? '']);
    } catch (Exception $e) {
        // Silently fail - logging should never break the app
    }
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}
