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
    if (!$subject) jsonResponse(['success' => false, 'message' => 'Course not found.'], 404);
    $subject['topics'] = [];
    try {
        $t = $db->prepare("SELECT id, topic_title, sort_order FROM course_topics WHERE subject_id = ? ORDER BY sort_order, id");
        $t->execute([$id]);
        $subject['topics'] = $t->fetchAll();
    } catch (Exception $e) {}
    jsonResponse(['success' => true, 'data' => $subject]);
}

function createSubject() {
    $db = getDB();
    $data = getPostData();
    
    $errors = validateRequired($data, ['name', 'code']);
    if ($errors) jsonResponse(['success' => false, 'message' => $errors[0]], 400);
    
    $stmt = $db->prepare("SELECT id FROM subjects WHERE code = ?");
    $stmt->execute([$data['code']]);
    if ($stmt->fetch()) jsonResponse(['success' => false, 'message' => 'Course code already exists.'], 400);
    
    $imagePath = null;
    if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $up = uploadFile($_FILES['image'], 'courses', ALLOWED_IMAGE_TYPES, 5 * 1024 * 1024);
        if ($up['success']) $imagePath = $up['path'];
    }
    
    $hasImage = (bool) $db->query("SHOW COLUMNS FROM subjects LIKE 'image'")->fetch();
    $hasDuration = (bool) $db->query("SHOW COLUMNS FROM subjects LIKE 'duration'")->fetch();
    
    $cols = ['name', 'code', 'description', 'status'];
    $vals = [sanitize($data['name']), strtoupper(sanitize($data['code'])), sanitize($data['description'] ?? ''), $data['status'] ?? 'active'];
    if ($hasImage) { $cols[] = 'image'; $vals[] = $imagePath; }
    if ($hasDuration) { $cols[] = 'duration'; $vals[] = sanitize($data['duration'] ?? ''); }
    
    $placeholders = implode(',', array_fill(0, count($vals), '?'));
    $stmt = $db->prepare("INSERT INTO subjects (" . implode(',', $cols) . ") VALUES ({$placeholders})");
    $stmt->execute($vals);
    $subjectId = $db->lastInsertId();
    
    saveCourseTopics($db, $subjectId, $data['topics'] ?? []);
    
    logActivity('admin', $_SESSION['user_id'], 'create_subject', 'Created course: ' . $data['name']);
    jsonResponse(['success' => true, 'message' => 'Course created successfully.']);
}

function updateSubject($id) {
    $db = getDB();
    $data = getPostData();
    
    if (!$id) jsonResponse(['success' => false, 'message' => 'Course ID required.'], 400);
    
    $errors = validateRequired($data, ['name', 'code']);
    if ($errors) jsonResponse(['success' => false, 'message' => $errors[0]], 400);
    
    $stmt = $db->prepare("SELECT id FROM subjects WHERE code = ? AND id != ?");
    $stmt->execute([$data['code'], $id]);
    if ($stmt->fetch()) jsonResponse(['success' => false, 'message' => 'Course code already exists.'], 400);
    
    $imagePath = null;
    if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $up = uploadFile($_FILES['image'], 'courses', ALLOWED_IMAGE_TYPES, 5 * 1024 * 1024);
        if ($up['success']) $imagePath = $up['path'];
    }
    
    $hasImage = (bool) $db->query("SHOW COLUMNS FROM subjects LIKE 'image'")->fetch();
    $hasDuration = (bool) $db->query("SHOW COLUMNS FROM subjects LIKE 'duration'")->fetch();
    
    $sets = ['name=?', 'code=?', 'description=?', 'status=?'];
    $vals = [sanitize($data['name']), strtoupper(sanitize($data['code'])), sanitize($data['description'] ?? ''), $data['status'] ?? 'active'];
    if ($hasImage && $imagePath) { $sets[] = 'image=?'; $vals[] = $imagePath; }
    if ($hasDuration) { $sets[] = 'duration=?'; $vals[] = sanitize($data['duration'] ?? ''); }
    $vals[] = $id;
    
    $stmt = $db->prepare("UPDATE subjects SET " . implode(',', $sets) . " WHERE id=?");
    $stmt->execute($vals);
    
    saveCourseTopics($db, $id, $data['topics'] ?? []);
    
    logActivity('admin', $_SESSION['user_id'], 'update_subject', 'Updated course ID: ' . $id);
    jsonResponse(['success' => true, 'message' => 'Course updated successfully.']);
}

function saveCourseTopics($db, $subjectId, $topics) {
    if (!is_array($topics)) $topics = json_decode($topics, true) ?: [];
    try {
        $db->prepare("DELETE FROM course_topics WHERE subject_id = ?")->execute([$subjectId]);
        $hasTable = (bool) $db->query("SHOW TABLES LIKE 'course_topics'")->fetch();
        if (!$hasTable) return;
        $stmt = $db->prepare("INSERT INTO course_topics (subject_id, topic_title, sort_order) VALUES (?, ?, ?)");
        foreach ($topics as $i => $t) {
            $title = is_string($t) ? $t : ($t['topic_title'] ?? $t['title'] ?? '');
            if (trim($title)) $stmt->execute([$subjectId, trim($title), $i]);
        }
    } catch (Exception $e) {}
}

function deleteSubject($id) {
    $db = getDB();
    if (!$id) jsonResponse(['success' => false, 'message' => 'Subject ID required.'], 400);
    
    $stmt = $db->prepare("DELETE FROM subjects WHERE id = ?");
    $stmt->execute([$id]);
    
    logActivity('admin', $_SESSION['user_id'], 'delete_subject', 'Deleted subject ID: ' . $id);
    jsonResponse(['success' => true, 'message' => 'Subject deleted successfully.']);
}
