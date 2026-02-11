<?php
/**
 * Student Courses API
 */

function getMyCourses() {
    $db = getDB();
    $classId = $_SESSION['class_id'] ?? null;
    
    if (!$classId) {
        jsonResponse(['success' => true, 'data' => [], 'class_name' => 'No class assigned']);
        return;
    }
    
    // Get class info
    $classStmt = $db->prepare("SELECT name FROM classes WHERE id = ?");
    $classStmt->execute([$classId]);
    $className = $classStmt->fetch()['name'] ?? 'Unknown';
    
    // Get subjects with their lecturers
    $stmt = $db->prepare("
        SELECT s.id, s.name, s.code, s.description,
            GROUP_CONCAT(DISTINCT l.name SEPARATOR ', ') as lecturer_names
        FROM subjects s 
        INNER JOIN class_subjects cs ON cs.subject_id = s.id 
        LEFT JOIN lecturer_subjects ls ON ls.subject_id = s.id 
        LEFT JOIN lecturer_classes lc ON lc.lecturer_id = ls.lecturer_id AND lc.class_id = cs.class_id
        LEFT JOIN lecturers l ON l.id = ls.lecturer_id
        WHERE cs.class_id = ? AND s.status = 'active'
        GROUP BY s.id, s.name, s.code, s.description
        ORDER BY s.name
    ");
    $stmt->execute([$classId]);
    
    jsonResponse([
        'success' => true, 
        'data' => $stmt->fetchAll(),
        'class_name' => $className
    ]);
}
