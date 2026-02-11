<?php
/**
 * Profile API - Works for all user roles
 */

function getProfile() {
    $db = getDB();
    $role = $_SESSION['user_role'];
    $userId = $_SESSION['user_id'];
    $table = $role === 'admin' ? 'admins' : ($role === 'lecturer' ? 'lecturers' : 'students');

    $stmt = $db->prepare("SELECT * FROM {$table} WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if (!$user) jsonResponse(['success' => false, 'message' => 'User not found.'], 404);
    unset($user['password']);

    if ($role === 'student') {
        $cls = $db->prepare("SELECT name FROM classes WHERE id = ?");
        $cls->execute([$user['class_id'] ?? 0]);
        $c = $cls->fetch();
        $user['class_name'] = $c ? $c['name'] : 'Not Assigned';
    }

    jsonResponse(['success' => true, 'data' => $user]);
}

function updateProfile() {
    $db = getDB();
    $role = $_SESSION['user_role'];
    $userId = $_SESSION['user_id'];
    $data = getPostData();
    $table = $role === 'admin' ? 'admins' : ($role === 'lecturer' ? 'lecturers' : 'students');

    // Basic updates: name, phone
    $name = sanitize($data['name'] ?? '');
    $phone = sanitize($data['phone'] ?? '');
    if (!$name) jsonResponse(['success' => false, 'message' => 'Name is required.'], 400);

    $stmt = $db->prepare("UPDATE {$table} SET name = ?, phone = ? WHERE id = ?");
    $stmt->execute([$name, $phone, $userId]);
    $_SESSION['user_name'] = $name;

    jsonResponse(['success' => true, 'message' => 'Profile updated successfully.']);
}

function changePassword() {
    $db = getDB();
    $role = $_SESSION['user_role'];
    $userId = $_SESSION['user_id'];
    $data = getPostData();
    $table = $role === 'admin' ? 'admins' : ($role === 'lecturer' ? 'lecturers' : 'students');

    $current = $data['current_password'] ?? '';
    $newPass = $data['new_password'] ?? '';
    $confirm = $data['confirm_password'] ?? '';

    if (!$current || !$newPass || !$confirm) {
        jsonResponse(['success' => false, 'message' => 'All password fields are required.'], 400);
    }
    if (strlen($newPass) < 6) {
        jsonResponse(['success' => false, 'message' => 'New password must be at least 6 characters.'], 400);
    }
    if ($newPass !== $confirm) {
        jsonResponse(['success' => false, 'message' => 'New passwords do not match.'], 400);
    }

    $stmt = $db->prepare("SELECT password FROM {$table} WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if (!password_verify($current, $user['password'])) {
        jsonResponse(['success' => false, 'message' => 'Current password is incorrect.'], 400);
    }

    $hashed = hashPassword($newPass);
    $db->prepare("UPDATE {$table} SET password = ? WHERE id = ?")->execute([$hashed, $userId]);

    jsonResponse(['success' => true, 'message' => 'Password changed successfully.']);
}

function uploadProfilePicture() {
    $role = $_SESSION['user_role'];
    $userId = $_SESSION['user_id'];
    $table = $role === 'admin' ? 'admins' : ($role === 'lecturer' ? 'lecturers' : 'students');

    if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
        jsonResponse(['success' => false, 'message' => 'No file uploaded.'], 400);
    }

    $file = $_FILES['profile_picture'];
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed)) {
        jsonResponse(['success' => false, 'message' => 'Only JPG, PNG, GIF and WebP images are allowed.'], 400);
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        jsonResponse(['success' => false, 'message' => 'Image must be under 5MB.'], 400);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $role . '_' . $userId . '_' . time() . '.' . $ext;
    $uploadDir = __DIR__ . '/../uploads/profiles/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    // Delete old picture
    $db = getDB();
    $old = $db->prepare("SELECT profile_picture FROM {$table} WHERE id = ?");
    $old->execute([$userId]);
    $oldPic = $old->fetchColumn();
    if ($oldPic && file_exists(__DIR__ . '/../uploads/' . $oldPic)) {
        unlink(__DIR__ . '/../uploads/' . $oldPic);
    }

    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        jsonResponse(['success' => false, 'message' => 'Failed to upload file.'], 500);
    }

    $path = 'profiles/' . $filename;
    $db->prepare("UPDATE {$table} SET profile_picture = ? WHERE id = ?")->execute([$path, $userId]);

    jsonResponse(['success' => true, 'message' => 'Profile picture updated.', 'path' => $path]);
}
