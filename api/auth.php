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
    try {
        $db = getDB();

        // Check if registration is enabled
        $regEnabled = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'allow_registration'")->fetchColumn();
        if ($regEnabled !== '1' && $regEnabled !== 'enabled') {
            jsonResponse(['success' => false, 'message' => 'Student registration is currently disabled.'], 403);
        }

        $data = getPostData();
        $errors = validateRequired($data, ['name', 'email', 'password']);
        if ($errors) jsonResponse(['success' => false, 'message' => $errors[0]], 400);

        $classId = !empty($data['class_id']) ? (int) $data['class_id'] : null;
        if ($classId) {
            $classCheck = $db->prepare("SELECT id FROM classes WHERE id = ? AND status = 'active'");
            $classCheck->execute([$classId]);
            if (!$classCheck->fetch()) {
                jsonResponse(['success' => false, 'message' => 'Please select a valid class.'], 400);
            }
        }

        $email = strtolower(trim($data['email']));

        // Check if migration 006 has been run
        $hasApproval = (bool) $db->query("SHOW COLUMNS FROM students LIKE 'approval_status'")->fetch();
        $hasTokens = (bool) $db->query("SHOW TABLES LIKE 'student_approval_tokens'")->fetch();
        if (!$hasApproval) {
            jsonResponse(['success' => false, 'message' => 'Database migration required. Please run sql/006_student_approval_flow.sql'], 500);
            return;
        }

        if (strlen($data['password']) < 6) {
            jsonResponse(['success' => false, 'message' => 'Password must be at least 6 characters.'], 400);
        }

        $gender = !empty($data['gender']) && in_array($data['gender'], ['male', 'female', 'other']) ? $data['gender'] : null;

        $existing = $db->prepare("SELECT id, approval_status FROM students WHERE email = ?");
        $existing->execute([$email]);
        $existingRow = $existing->fetch();

        if ($existingRow) {
            if ($existingRow['approval_status'] === 'approved') {
                jsonResponse(['success' => false, 'message' => 'Email already registered.'], 400);
            }
            if ($existingRow['approval_status'] === 'pending') {
                jsonResponse(['success' => false, 'message' => 'Email already registered. Please wait for admin approval.'], 400);
            }
            $studentId = (int) $existingRow['id'];
            $db->prepare("UPDATE students SET name = ?, class_id = ?, phone = ?, gender = ?, password = ?, approval_status = 'pending', rejection_reason = NULL, matric_no = NULL, approved_at = NULL, approved_by = NULL WHERE id = ?")
                ->execute([sanitize($data['name']), $classId, sanitize($data['phone'] ?? ''), $gender, hashPassword($data['password']), $studentId]);
            if ($hasTokens) {
                $db->prepare("DELETE FROM student_approval_tokens WHERE student_id = ?")->execute([$studentId]);
            }
        } else {
            $stmt = $db->prepare("INSERT INTO students (name, email, matric_no, class_id, phone, gender, password, status, approval_status) VALUES (?, ?, NULL, ?, ?, ?, ?, 'active', 'pending')");
            $stmt->execute([
                sanitize($data['name']),
                $email,
                $classId,
                sanitize($data['phone'] ?? ''),
                $gender,
                hashPassword($data['password'])
            ]);
            $studentId = $db->lastInsertId();
        }

        if ($hasTokens) {
            $approveToken = bin2hex(random_bytes(32));
            $declineToken = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + 86400 * 7);
            $db->prepare("INSERT INTO student_approval_tokens (student_id, token, action, expires_at) VALUES (?, ?, 'approve', ?), (?, ?, 'decline', ?)")
                ->execute([$studentId, $approveToken, $expiresAt, $studentId, $declineToken, $expiresAt]);
        }

        $className = 'N/A';
        if ($classId) {
            $classStmt = $db->prepare("SELECT name FROM classes WHERE id = ?");
            $classStmt->execute([$classId]);
            $className = $classStmt->fetchColumn() ?: 'N/A';
        }

        if ($hasTokens) {
            try {
                require_once __DIR__ . '/../helpers/mail.php';
                require_once __DIR__ . '/../helpers/email_templates.php';
                $contactEmail = getSetting('contact_email', 'admin@agitacademy.com');
                $approveUrl = APP_URL . '/approve-student?token=' . $approveToken;
                $declineUrl = APP_URL . '/decline-student?token=' . $declineToken;
                $adminEmailBody = getAdminNewStudentEmailTemplate([
                    'name' => $data['name'],
                    'email' => $email,
                    'phone' => $data['phone'] ?? 'N/A',
                    'class' => $className,
                    'gender' => $gender ?? 'N/A',
                    'approveUrl' => $approveUrl,
                    'declineUrl' => $declineUrl,
                ]);
                sendSmtpEmail($contactEmail, 'AGIT Academy – New Student Registration: ' . $data['name'], $adminEmailBody, 'AGIT Academy');
            } catch (Throwable $mailErr) {
                if (function_exists('logEmailError')) {
                    logEmailError('registration', getSetting('contact_email', 'admin@agitacademy.com'), $mailErr->getMessage());
                }
            }
        }

        jsonResponse(['success' => true, 'message' => 'Registration successful! Please wait for admin approval.', 'redirect' => APP_URL . '/register/success']);
    } catch (Exception $e) {
        jsonResponse(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()], 500);
    }
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
    // For students, also check approval_status
    if ($role === 'student') {
        $approval = $db->prepare("SELECT approval_status FROM students WHERE id = ?");
        $approval->execute([$user['id']]);
        $as = $approval->fetchColumn();
        if ($as === 'rejected' || $as === 'pending') {
            return ['success' => false, 'message' => 'No account found with this email.'];
        }
    }
    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiresAt = date('Y-m-d H:i:s', time() + 900);
    $db->prepare("INSERT INTO password_reset_codes (user_type, user_id, email, code, expires_at) VALUES (?, ?, ?, ?, ?)")
        ->execute([$role, $user['id'], $email, $code, $expiresAt]);
    require_once __DIR__ . '/../helpers/mail.php';
    require_once __DIR__ . '/../helpers/email_templates.php';
    $body = getForgotPasswordEmailTemplate($code);
    $result = sendSmtpEmail($email, 'AGIT Academy – Password Reset Code', $body);
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
