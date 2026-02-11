<?php
/**
 * Admin Classes API
 */

function getClasses() {
    $db = getDB();
    $search = $_GET['search'] ?? '';
    $type = $_GET['type'] ?? '';
    $page = max(1, (int)($_GET['page'] ?? 1));
    $all = isset($_GET['all']);
    
    $where = [];
    $params = [];
    
    if ($search) {
        $where[] = "c.name LIKE ?";
        $params[] = "%$search%";
    }
    if ($type) {
        $where[] = "c.type = ?";
        $params[] = $type;
    }
    
    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    
    if ($all) {
        $stmt = $db->prepare("SELECT id, name, type, status FROM classes $whereClause ORDER BY name");
        $stmt->execute($params);
        jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
        return;
    }
    
    $countStmt = $db->prepare("SELECT COUNT(*) as total FROM classes c $whereClause");
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];
    $pagination = paginate($total, $page);
    
    $stmt = $db->prepare("
        SELECT c.*, 
            (SELECT COUNT(*) FROM students s WHERE s.class_id = c.id) as student_count,
            (SELECT COUNT(*) FROM lecturer_classes lc WHERE lc.class_id = c.id) as lecturer_count
        FROM classes c 
        $whereClause 
        ORDER BY c.created_at DESC 
        LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}
    ");
    $stmt->execute($params);
    
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll(), 'pagination' => $pagination]);
}

function getClass($id) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT c.*, 
            (SELECT COUNT(*) FROM students s WHERE s.class_id = c.id) as student_count
        FROM classes c WHERE c.id = ?
    ");
    $stmt->execute([$id]);
    $class = $stmt->fetch();
    if (!$class) jsonResponse(['success' => false, 'message' => 'Class not found.'], 404);
    
    // Get subjects for this class
    $subStmt = $db->prepare("
        SELECT s.* FROM subjects s 
        INNER JOIN class_subjects cs ON cs.subject_id = s.id 
        WHERE cs.class_id = ?
    ");
    $subStmt->execute([$id]);
    $class['subjects'] = $subStmt->fetchAll();
    
    jsonResponse(['success' => true, 'data' => $class]);
}

function createClass() {
    $db = getDB();
    $data = getPostData();
    
    $errors = validateRequired($data, ['name', 'type']);
    if ($errors) jsonResponse(['success' => false, 'message' => $errors[0]], 400);
    
    $stmt = $db->prepare("
        INSERT INTO classes (name, type, semester_count, current_semester, duration_weeks, capacity, status) 
        VALUES (?, ?, ?, ?, ?, ?, 'active')
    ");
    $stmt->execute([
        sanitize($data['name']),
        $data['type'],
        $data['type'] === 'semester' ? ($data['semester_count'] ?? 2) : null,
        $data['type'] === 'semester' ? 1 : null,
        $data['type'] === 'professional' ? ($data['duration_weeks'] ?? null) : null,
        $data['capacity'] ?? null,
    ]);
    
    $classId = $db->lastInsertId();
    
    // Assign subjects if provided
    if (!empty($data['subject_ids']) && is_array($data['subject_ids'])) {
        $subStmt = $db->prepare("INSERT INTO class_subjects (class_id, subject_id) VALUES (?, ?)");
        foreach ($data['subject_ids'] as $subjectId) {
            try { $subStmt->execute([$classId, $subjectId]); } catch (Exception $e) {}
        }
    }
    
    logActivity('admin', $_SESSION['user_id'], 'create_class', 'Created class: ' . $data['name']);
    jsonResponse(['success' => true, 'message' => 'Class created successfully.']);
}

function updateClass($id) {
    $db = getDB();
    $data = getPostData();
    
    if (!$id) jsonResponse(['success' => false, 'message' => 'Class ID required.'], 400);
    
    $errors = validateRequired($data, ['name', 'type']);
    if ($errors) jsonResponse(['success' => false, 'message' => $errors[0]], 400);
    
    $stmt = $db->prepare("
        UPDATE classes SET name=?, type=?, semester_count=?, current_semester=?, duration_weeks=?, capacity=?, status=? WHERE id=?
    ");
    $stmt->execute([
        sanitize($data['name']),
        $data['type'],
        $data['type'] === 'semester' ? ($data['semester_count'] ?? 2) : null,
        $data['type'] === 'semester' ? ($data['current_semester'] ?? 1) : null,
        $data['type'] === 'professional' ? ($data['duration_weeks'] ?? null) : null,
        $data['capacity'] ?? null,
        $data['status'] ?? 'active',
        $id
    ]);
    
    // Update subjects
    if (isset($data['subject_ids'])) {
        $db->prepare("DELETE FROM class_subjects WHERE class_id = ?")->execute([$id]);
        if (is_array($data['subject_ids'])) {
            $subStmt = $db->prepare("INSERT INTO class_subjects (class_id, subject_id) VALUES (?, ?)");
            foreach ($data['subject_ids'] as $subjectId) {
                try { $subStmt->execute([$id, $subjectId]); } catch (Exception $e) {}
            }
        }
    }
    
    logActivity('admin', $_SESSION['user_id'], 'update_class', 'Updated class ID: ' . $id);
    jsonResponse(['success' => true, 'message' => 'Class updated successfully.']);
}

function deleteClass($id) {
    $db = getDB();
    if (!$id) jsonResponse(['success' => false, 'message' => 'Class ID required.'], 400);
    
    // Check if students are enrolled
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM students WHERE class_id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetch()['count'] > 0) {
        jsonResponse(['success' => false, 'message' => 'Cannot delete class with enrolled students.'], 400);
    }
    
    $db->prepare("DELETE FROM classes WHERE id = ?")->execute([$id]);
    
    logActivity('admin', $_SESSION['user_id'], 'delete_class', 'Deleted class ID: ' . $id);
    jsonResponse(['success' => true, 'message' => 'Class deleted successfully.']);
}
