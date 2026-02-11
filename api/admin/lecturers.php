<?php
/**
 * Admin Lecturers API
 */

function getLecturers() {
    $db = getDB();
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    $page = max(1, (int)($_GET['page'] ?? 1));
    $all = isset($_GET['all']);
    
    $where = [];
    $params = [];
    
    if ($search) {
        $where[] = "(l.name LIKE ? OR l.email LIKE ? OR l.phone LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    if ($status) {
        $where[] = "l.status = ?";
        $params[] = $status;
    }
    
    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    
    if ($all) {
        $stmt = $db->prepare("SELECT id, name, email FROM lecturers $whereClause ORDER BY name");
        $stmt->execute($params);
        jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
        return;
    }
    
    $countStmt = $db->prepare("SELECT COUNT(*) as total FROM lecturers l $whereClause");
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];
    $pagination = paginate($total, $page);
    
    $stmt = $db->prepare("
        SELECT l.id, l.name, l.email, l.phone, l.status, l.profile_picture, l.last_login, l.created_at
        FROM lecturers l 
        $whereClause 
        ORDER BY l.created_at DESC 
        LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}
    ");
    $stmt->execute($params);
    $lecturers = $stmt->fetchAll();
    
    // Get assigned classes and subjects for each lecturer
    foreach ($lecturers as &$lec) {
        $clsStmt = $db->prepare("SELECT c.id, c.name FROM classes c INNER JOIN lecturer_classes lc ON lc.class_id = c.id WHERE lc.lecturer_id = ?");
        $clsStmt->execute([$lec['id']]);
        $lec['classes'] = $clsStmt->fetchAll();
        
        $subStmt = $db->prepare("SELECT s.id, s.name, s.code FROM subjects s INNER JOIN lecturer_subjects ls ON ls.subject_id = s.id WHERE ls.lecturer_id = ?");
        $subStmt->execute([$lec['id']]);
        $lec['subjects'] = $subStmt->fetchAll();
    }
    
    jsonResponse(['success' => true, 'data' => $lecturers, 'pagination' => $pagination]);
}

function getLecturer($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT id, name, email, phone, status, profile_picture, last_login, created_at FROM lecturers WHERE id = ?");
    $stmt->execute([$id]);
    $lecturer = $stmt->fetch();
    if (!$lecturer) jsonResponse(['success' => false, 'message' => 'Lecturer not found.'], 404);
    
    // Get assigned classes
    $clsStmt = $db->prepare("SELECT c.id, c.name FROM classes c INNER JOIN lecturer_classes lc ON lc.class_id = c.id WHERE lc.lecturer_id = ?");
    $clsStmt->execute([$id]);
    $lecturer['classes'] = $clsStmt->fetchAll();
    $lecturer['class_ids'] = array_column($lecturer['classes'], 'id');
    
    // Get assigned subjects
    $subStmt = $db->prepare("SELECT s.id, s.name, s.code FROM subjects s INNER JOIN lecturer_subjects ls ON ls.subject_id = s.id WHERE ls.lecturer_id = ?");
    $subStmt->execute([$id]);
    $lecturer['subjects'] = $subStmt->fetchAll();
    $lecturer['subject_ids'] = array_column($lecturer['subjects'], 'id');
    
    jsonResponse(['success' => true, 'data' => $lecturer]);
}

function createLecturer() {
    $db = getDB();
    $data = getPostData();
    
    $errors = validateRequired($data, ['name', 'email']);
    if ($errors) jsonResponse(['success' => false, 'message' => $errors[0]], 400);
    if (!isValidEmail($data['email'])) jsonResponse(['success' => false, 'message' => 'Invalid email format.'], 400);
    
    $stmt = $db->prepare("SELECT id FROM lecturers WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) jsonResponse(['success' => false, 'message' => 'Email already exists.'], 400);
    
    $password = hashPassword($data['password'] ?: 'password');
    
    $stmt = $db->prepare("INSERT INTO lecturers (name, email, phone, password, status) VALUES (?, ?, ?, ?, 'active')");
    $stmt->execute([
        sanitize($data['name']),
        sanitize($data['email']),
        sanitize($data['phone'] ?? ''),
        $password,
    ]);
    
    $lecturerId = $db->lastInsertId();
    
    // Assign classes
    if (!empty($data['class_ids']) && is_array($data['class_ids'])) {
        $clsStmt = $db->prepare("INSERT INTO lecturer_classes (lecturer_id, class_id) VALUES (?, ?)");
        foreach ($data['class_ids'] as $classId) {
            try { $clsStmt->execute([$lecturerId, $classId]); } catch (Exception $e) {}
        }
    }
    
    // Assign subjects
    if (!empty($data['subject_ids']) && is_array($data['subject_ids'])) {
        $subStmt = $db->prepare("INSERT INTO lecturer_subjects (lecturer_id, subject_id) VALUES (?, ?)");
        foreach ($data['subject_ids'] as $subjectId) {
            try { $subStmt->execute([$lecturerId, $subjectId]); } catch (Exception $e) {}
        }
    }
    
    logActivity('admin', $_SESSION['user_id'], 'create_lecturer', 'Created lecturer: ' . $data['name']);
    jsonResponse(['success' => true, 'message' => 'Lecturer created successfully.']);
}

function updateLecturer($id) {
    $db = getDB();
    $data = getPostData();
    
    if (!$id) jsonResponse(['success' => false, 'message' => 'Lecturer ID required.'], 400);
    
    $errors = validateRequired($data, ['name', 'email']);
    if ($errors) jsonResponse(['success' => false, 'message' => $errors[0]], 400);
    
    $stmt = $db->prepare("SELECT id FROM lecturers WHERE email = ? AND id != ?");
    $stmt->execute([$data['email'], $id]);
    if ($stmt->fetch()) jsonResponse(['success' => false, 'message' => 'Email already exists.'], 400);
    
    $sql = "UPDATE lecturers SET name=?, email=?, phone=?, status=?";
    $params = [sanitize($data['name']), sanitize($data['email']), sanitize($data['phone'] ?? ''), $data['status'] ?? 'active'];
    
    if (!empty($data['password'])) {
        $sql .= ", password=?";
        $params[] = hashPassword($data['password']);
    }
    
    $sql .= " WHERE id=?";
    $params[] = $id;
    
    $db->prepare($sql)->execute($params);
    
    // Update class assignments
    if (isset($data['class_ids'])) {
        $db->prepare("DELETE FROM lecturer_classes WHERE lecturer_id = ?")->execute([$id]);
        if (is_array($data['class_ids'])) {
            $clsStmt = $db->prepare("INSERT INTO lecturer_classes (lecturer_id, class_id) VALUES (?, ?)");
            foreach ($data['class_ids'] as $classId) {
                try { $clsStmt->execute([$id, $classId]); } catch (Exception $e) {}
            }
        }
    }
    
    // Update subject assignments
    if (isset($data['subject_ids'])) {
        $db->prepare("DELETE FROM lecturer_subjects WHERE lecturer_id = ?")->execute([$id]);
        if (is_array($data['subject_ids'])) {
            $subStmt = $db->prepare("INSERT INTO lecturer_subjects (lecturer_id, subject_id) VALUES (?, ?)");
            foreach ($data['subject_ids'] as $subjectId) {
                try { $subStmt->execute([$id, $subjectId]); } catch (Exception $e) {}
            }
        }
    }
    
    logActivity('admin', $_SESSION['user_id'], 'update_lecturer', 'Updated lecturer ID: ' . $id);
    jsonResponse(['success' => true, 'message' => 'Lecturer updated successfully.']);
}

function deleteLecturer($id) {
    $db = getDB();
    if (!$id) jsonResponse(['success' => false, 'message' => 'Lecturer ID required.'], 400);
    
    $db->prepare("DELETE FROM lecturers WHERE id = ?")->execute([$id]);
    
    logActivity('admin', $_SESSION['user_id'], 'delete_lecturer', 'Deleted lecturer ID: ' . $id);
    jsonResponse(['success' => true, 'message' => 'Lecturer deleted successfully.']);
}
