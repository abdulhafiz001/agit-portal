<?php
/**
 * Faculty Materials API
 */

function getMyMaterials() {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    $classId = $_GET['class_id'] ?? '';
    $subjectId = $_GET['subject_id'] ?? '';

    $where = ["m.lecturer_id = ?"];
    $params = [$lecturerId];

    if ($classId) { $where[] = "m.class_id = ?"; $params[] = $classId; }
    if ($subjectId) { $where[] = "m.subject_id = ?"; $params[] = $subjectId; }

    $whereClause = 'WHERE ' . implode(' AND ', $where);

    $stmt = $db->prepare("
        SELECT m.*, c.name as class_name, s.name as subject_name, s.code as subject_code
        FROM materials m
        JOIN classes c ON c.id = m.class_id
        JOIN subjects s ON s.id = m.subject_id
        $whereClause
        ORDER BY m.created_at DESC
    ");
    $stmt->execute($params);
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}

function uploadMaterial() {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];

    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $classId = $_POST['class_id'] ?? '';
    $subjectId = $_POST['subject_id'] ?? '';

    if (!$title || !$classId || !$subjectId) {
        jsonResponse(['success' => false, 'message' => 'Title, class, and subject are required.'], 400);
    }

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        jsonResponse(['success' => false, 'message' => 'Please select a file to upload.'], 400);
    }

    $result = uploadFile($_FILES['file'], 'materials', null, 20 * 1024 * 1024);
    if (!$result['success']) {
        jsonResponse($result, 400);
    }

    $stmt = $db->prepare("
        INSERT INTO materials (title, description, class_id, subject_id, lecturer_id, file_name, file_path, file_type, file_size)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $title, $description, $classId, $subjectId, $lecturerId,
        $_FILES['file']['name'], $result['path'], $_FILES['file']['type'], $_FILES['file']['size']
    ]);

    logActivity('lecturer', $lecturerId, 'upload_material', 'Uploaded material: ' . $title);
    jsonResponse(['success' => true, 'message' => 'Material uploaded successfully.']);
}

function deleteMaterial($id) {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    $stmt = $db->prepare("DELETE FROM materials WHERE id = ? AND lecturer_id = ?");
    $stmt->execute([$id, $lecturerId]);
    if ($stmt->rowCount() === 0) {
        jsonResponse(['success' => false, 'message' => 'Material not found.'], 404);
    }
    jsonResponse(['success' => true, 'message' => 'Material deleted.']);
}
