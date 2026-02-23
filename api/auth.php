<?php
/**
 * Authentication API
 * AGIT Academy Management System
 */

function handleLogin() {
    $data = getPostData();
    
    $email = sanitize($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $role = sanitize($data['role'] ?? '');

    // Validate
    if (empty($email) || empty($password) || empty($role)) {
        jsonResponse(['success' => false, 'message' => 'All fields are required.'], 400);
    }

    if (!isValidEmail($email)) {
        jsonResponse(['success' => false, 'message' => 'Invalid email format.'], 400);
    }

    if (!in_array($role, ['admin', 'lecturer', 'student'])) {
        jsonResponse(['success' => false, 'message' => 'Invalid role.'], 400);
    }

    // Rate limiting
    if (!rateLimit('login_' . $email, 5, 15)) {
        jsonResponse(['success' => false, 'message' => 'Too many login attempts. Try again in 15 minutes.'], 429);
    }

    $result = loginUser($email, $password, $role);
    jsonResponse($result, $result['success'] ? 200 : 400);
}

function handleRegister() {
    $db = getDB();

    // Check if registration is enabled
    $regEnabled = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'allow_registration'")->fetchColumn();
    if ($regEnabled !== '1' && $regEnabled !== 'enabled') {
        jsonResponse(['success' => false, 'message' => 'Student registration is currently disabled.'], 403);
    }

    $data = getPostData();
    $errors = validateRequired($data, ['name', 'email', 'matric_no', 'class_id', 'password']);
    if ($errors) jsonResponse(['success' => false, 'message' => $errors[0]], 400);

    $email = strtolower(trim($data['email']));
    $exists = $db->prepare("SELECT id FROM students WHERE email = ? OR matric_no = ?");
    $exists->execute([$email, trim($data['matric_no'])]);
    if ($exists->fetch()) {
        jsonResponse(['success' => false, 'message' => 'Email or matric number already exists.'], 400);
    }

    if (strlen($data['password']) < 6) {
        jsonResponse(['success' => false, 'message' => 'Password must be at least 6 characters.'], 400);
    }

    $stmt = $db->prepare("INSERT INTO students (name, email, matric_no, class_id, phone, password, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
    $stmt->execute([
        sanitize($data['name']),
        $email,
        trim($data['matric_no']),
        $data['class_id'],
        sanitize($data['phone'] ?? ''),
        hashPassword($data['password'])
    ]);

    jsonResponse(['success' => true, 'message' => 'Registration successful! You can now login.']);
}

function handleLogout() {
    $role = logoutUser();
    jsonResponse(['success' => true, 'message' => 'Logged out successfully.', 'role' => $role]);
}

/**
 * Process forgot password request - returns result (for page or API)
 */
function processForgotPasswordRequest($email, $role) {
    $email = strtolower(trim($email));
    if (!$email || !isValidEmail($email)) {
        return ['success' => false, 'message' => 'Valid email is required.'];
    }
    if (!in_array($role, ['student', 'lecturer'])) {
        return ['success' => false, 'message' => 'Invalid role.'];
    }
    $db = getDB();
    $table = $role === 'lecturer' ? 'lecturers' : 'students';
    $stmt = $db->prepare("SELECT id FROM {$table} WHERE email = ? AND status = 'active'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user) {
        return ['success' => false, 'message' => 'No account found with this email.'];
    }
    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiresAt = date('Y-m-d H:i:s', time() + 900);
    $db->prepare("INSERT INTO password_reset_codes (user_type, user_id, email, code, expires_at) VALUES (?, ?, ?, ?, ?)")
        ->execute([$role, $user['id'], $email, $code, $expiresAt]);
    require_once __DIR__ . '/../helpers/mail.php';
    $body = "<h3>Password Reset - AGIT Portal</h3><p>Your verification code is: <strong>{$code}</strong></p><p>This code expires in 15 minutes. If you didn't request this, please ignore this email.</p>";
    $result = sendSmtpEmail($email, 'AGIT Portal - Password Reset Code', $body);
    if ($result['success']) {
        return ['success' => true, 'message' => 'A 6-digit code has been sent to your email.'];
    }
    return ['success' => false, 'message' => 'Failed to send email. Please try again or contact admin.'];
}

/**
 * Process reset password - returns result (for page or API)
 */
function processResetPassword($email, $code, $newPassword, $confirmPassword, $role) {
    $email = strtolower(trim($email));
    $code = trim($code);
    if (!$email || !$code || !$newPassword || !$confirmPassword) {
        return ['success' => false, 'message' => 'All fields are required.'];
    }
    if (strlen($newPassword) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters.'];
    }
    if ($newPassword !== $confirmPassword) {
        return ['success' => false, 'message' => 'Passwords do not match.'];
    }
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM password_reset_codes WHERE email = ? AND code = ? AND user_type = ? AND used_at IS NULL AND expires_at > NOW() ORDER BY id DESC LIMIT 1");
    $stmt->execute([$email, $code, $role]);
    $row = $stmt->fetch();
    if (!$row) {
        return ['success' => false, 'message' => 'Invalid or expired code. Please request a new one.'];
    }
    $table = $role === 'lecturer' ? 'lecturers' : 'students';
    $db->prepare("UPDATE {$table} SET password = ? WHERE id = ?")->execute([hashPassword($newPassword), $row['user_id']]);
    $db->prepare("UPDATE password_reset_codes SET used_at = NOW() WHERE id = ?")->execute([$row['id']]);
    return ['success' => true, 'message' => 'Password reset successfully. You can now login.'];
}

/**
 * Forgot Password - Send 6-digit code to email (API)
 */
function handleForgotPassword() {
    $data = getPostData();
    $result = processForgotPasswordRequest($data['email'] ?? '', $data['role'] ?? 'student');
    $status = $result['success'] ? 200 : ($result['message'] === 'No account found with this email.' ? 404 : 400);
    jsonResponse($result, $status);
}

/**
 * Reset Password - Verify code and set new password (API)
 */
function handleResetPassword() {
    $data = getPostData();
    $result = processResetPassword(
        $data['email'] ?? '',
        $data['code'] ?? '',
        $data['new_password'] ?? '',
        $data['confirm_password'] ?? '',
        $data['role'] ?? 'student'
    );
    jsonResponse($result, $result['success'] ? 200 : 400);
}
