<?php
/**
 * Manage Admins API
 */

function getAdmins() {
    $db = getDB();
    $stmt = $db->query("SELECT a.id, a.name, a.email, a.role, a.status, a.created_at, ap.allowed_pages FROM admins a LEFT JOIN admin_permissions ap ON ap.admin_id = a.id ORDER BY a.created_at DESC");
    $admins = $stmt->fetchAll();
    jsonResponse(['success' => true, 'data' => $admins]);
}

function createAdmin() {
    $db = getDB();
    $data = getPostData();
    $errors = validateRequired($data, ['name', 'email', 'password']);
    if ($errors) jsonResponse(['success' => false, 'message' => $errors[0]], 400);
    
    $exists = $db->prepare("SELECT id FROM admins WHERE email = ?");
    $exists->execute([strtolower(trim($data['email']))]);
    if ($exists->fetch()) jsonResponse(['success' => false, 'message' => 'Email already exists.'], 400);
    
    $stmt = $db->prepare("INSERT INTO admins (name, email, password, role, status) VALUES (?, ?, ?, ?, 'active')");
    $stmt->execute([sanitize($data['name']), strtolower(trim($data['email'])), hashPassword($data['password']), $data['role'] ?? 'limited']);
    $adminId = $db->lastInsertId();
    
    // Save permissions
    $pages = $data['allowed_pages'] ?? [];
    if (!empty($pages)) {
        $db->prepare("INSERT INTO admin_permissions (admin_id, allowed_pages) VALUES (?, ?)")->execute([$adminId, json_encode($pages)]);
    }
    
    logActivity('admin', $_SESSION['user_id'], 'create_admin', 'Created admin: ' . $data['name']);
    jsonResponse(['success' => true, 'message' => 'Admin created successfully.']);
}

function updateAdmin($id) {
    $db = getDB();
    $data = getPostData();
    
    $stmt = $db->prepare("UPDATE admins SET name = ?, email = ?, role = ?, status = ? WHERE id = ?");
    $stmt->execute([sanitize($data['name']), strtolower(trim($data['email'])), $data['role'] ?? 'limited', $data['status'] ?? 'active', $id]);
    
    if (isset($data['password']) && $data['password']) {
        $db->prepare("UPDATE admins SET password = ? WHERE id = ?")->execute([hashPassword($data['password']), $id]);
    }
    
    // Update permissions
    $pages = $data['allowed_pages'] ?? [];
    $db->prepare("DELETE FROM admin_permissions WHERE admin_id = ?")->execute([$id]);
    if (!empty($pages)) {
        $db->prepare("INSERT INTO admin_permissions (admin_id, allowed_pages) VALUES (?, ?) ON DUPLICATE KEY UPDATE allowed_pages = VALUES(allowed_pages)")->execute([$id, json_encode($pages)]);
    }
    
    jsonResponse(['success' => true, 'message' => 'Admin updated successfully.']);
}

function deleteAdmin($id) {
    $db = getDB();
    if ($id == $_SESSION['user_id']) jsonResponse(['success' => false, 'message' => 'Cannot delete yourself.'], 400);
    if ($id == 1) jsonResponse(['success' => false, 'message' => 'Cannot delete the super admin.'], 400);
    $db->prepare("DELETE FROM admins WHERE id = ?")->execute([$id]);
    jsonResponse(['success' => true, 'message' => 'Admin deleted.']);
}
