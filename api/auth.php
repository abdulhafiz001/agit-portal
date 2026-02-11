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
