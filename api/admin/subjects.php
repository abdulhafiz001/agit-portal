<?php
/**
 * Admin Subjects API
 */

function getSubjects() {
    $db = getDB();
    $search = $_GET['search'] ?? '';
    $page = max(1, (int)($_GET['page'] ?? 1));
    $all = isset($_GET['all']); // For dropdowns
    
    $where = [];
    $params = [];
    
    if ($search) {
        $where[] = "(name LIKE ? OR code LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    
    if ($all) {
        $stmt = $db->prepare("SELECT id, name, code, status FROM subjects $whereClause ORDER BY name");
        $stmt->execute($params);
        jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
        return;
    }
    
    $countStmt = $db->prepare("SELECT COUNT(*) as total FROM subjects $whereClause");
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];
    $pagination = paginate($total, $page);
    
    $stmt = $db->prepare("
        SELECT s.*, 
            (SELECT COUNT(*) FROM class_subjects cs WHERE cs.subject_id = s.id) as class_count,
            (SELECT COUNT(*) FROM lecturer_subjects ls WHERE ls.subject_id = s.id) as lecturer_count
        FROM subjects s 
        $whereClause 
        ORDER BY s.name 
        LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}
    ");
    $stmt->execute($params);
    
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll(), 'pagination' => $pagination]);
}

function getSubject($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM subjects WHERE id = ?");
    $stmt->execute([$id]);
    $subject = $stmt->fetch();
    if (!$subject) jsonResponse(['success' => false, 'message' => 'Subject not found.'], 404);
    jsonResponse(['success' => true, 'data' => $subject]);
}

function createSubject() {
    $db = getDB();
    $data = getPostData();
    
    $errors = validateRequired($data, ['name', 'code']);
    if ($errors) jsonResponse(['success' => false, 'message' => $errors[0]], 400);
    
    $stmt = $db->prepare("SELECT id FROM subjects WHERE code = ?");
    $stmt->execute([$data['code']]);
    if ($stmt->fetch()) jsonResponse(['success' => false, 'message' => 'Subject code already exists.'], 400);
    
    $stmt = $db->prepare("INSERT INTO subjects (name, code, description, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        sanitize($data['name']),
        strtoupper(sanitize($data['code'])),
        sanitize($data['description'] ?? ''),
        $data['status'] ?? 'active'
    ]);
    
    logActivity('admin', $_SESSION['user_id'], 'create_subject', 'Created subject: ' . $data['name']);
    jsonResponse(['success' => true, 'message' => 'Subject created successfully.']);
}

function updateSubject($id) {
    $db = getDB();
    $data = getPostData();
    
    if (!$id) jsonResponse(['success' => false, 'message' => 'Subject ID required.'], 400);
    
    $errors = validateRequired($data, ['name', 'code']);
    if ($errors) jsonResponse(['success' => false, 'message' => $errors[0]], 400);
    
    $stmt = $db->prepare("SELECT id FROM subjects WHERE code = ? AND id != ?");
    $stmt->execute([$data['code'], $id]);
    if ($stmt->fetch()) jsonResponse(['success' => false, 'message' => 'Subject code already exists.'], 400);
    
    $stmt = $db->prepare("UPDATE subjects SET name=?, code=?, description=?, status=? WHERE id=?");
    $stmt->execute([
        sanitize($data['name']),
        strtoupper(sanitize($data['code'])),
        sanitize($data['description'] ?? ''),
        $data['status'] ?? 'active',
        $id
    ]);
    
    logActivity('admin', $_SESSION['user_id'], 'update_subject', 'Updated subject ID: ' . $id);
    jsonResponse(['success' => true, 'message' => 'Subject updated successfully.']);
}

function deleteSubject($id) {
    $db = getDB();
    if (!$id) jsonResponse(['success' => false, 'message' => 'Subject ID required.'], 400);
    
    $stmt = $db->prepare("DELETE FROM subjects WHERE id = ?");
    $stmt->execute([$id]);
    
    logActivity('admin', $_SESSION['user_id'], 'delete_subject', 'Deleted subject ID: ' . $id);
    jsonResponse(['success' => true, 'message' => 'Subject deleted successfully.']);
}
