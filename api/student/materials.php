<?php
/**
 * Student Materials API
 */

function getStudentMaterials() {
    $db = getDB();
    $classId = $_SESSION['class_id'] ?? null;
    if (!$classId) {
        jsonResponse(['success' => true, 'data' => []]);
        return;
    }
    $subjectId = $_GET['subject_id'] ?? '';
    $where = ["m.class_id = ?", "m.status = 'active'"];
    $params = [$classId];
    if ($subjectId) { $where[] = "m.subject_id = ?"; $params[] = $subjectId; }

    $stmt = $db->prepare("
        SELECT m.id, m.title, m.description, m.file_name, m.file_type, m.file_size, m.download_count, m.created_at,
               s.name as subject_name, s.code as subject_code, l.name as lecturer_name
        FROM materials m
        JOIN subjects s ON s.id = m.subject_id
        JOIN lecturers l ON l.id = m.lecturer_id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY m.created_at DESC
    ");
    $stmt->execute($params);
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}

function downloadMaterial($id) {
    $db = getDB();
    $classId = $_SESSION['class_id'] ?? null;
    $stmt = $db->prepare("SELECT * FROM materials WHERE id = ? AND class_id = ? AND status = 'active'");
    $stmt->execute([$id, $classId]);
    $material = $stmt->fetch();
    if (!$material) {
        jsonResponse(['success' => false, 'message' => 'Material not found.'], 404);
    }
    // Increment download count
    $db->prepare("UPDATE materials SET download_count = download_count + 1 WHERE id = ?")->execute([$id]);
    $filePath = UPLOADS_PATH . '/' . $material['file_path'];
    if (!file_exists($filePath)) {
        jsonResponse(['success' => false, 'message' => 'File not found on server.'], 404);
    }
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $material['file_name'] . '"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
}
