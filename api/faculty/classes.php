<?php
/**
 * Faculty Classes API
 */

function getMyClasses() {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    
    $stmt = $db->prepare("
        SELECT c.*, 
            (SELECT COUNT(*) FROM students s WHERE s.class_id = c.id) as student_count
        FROM classes c 
        INNER JOIN lecturer_classes lc ON lc.class_id = c.id 
        WHERE lc.lecturer_id = ? AND c.status = 'active'
        ORDER BY c.name
    ");
    $stmt->execute([$lecturerId]);
    
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}

function getClassStudents($classId) {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    
    // Verify lecturer is assigned to this class
    $check = $db->prepare("SELECT id FROM lecturer_classes WHERE lecturer_id = ? AND class_id = ?");
    $check->execute([$lecturerId, $classId]);
    if (!$check->fetch()) {
        jsonResponse(['success' => false, 'message' => 'You are not assigned to this class.'], 403);
    }
    
    $stmt = $db->prepare("
        SELECT s.id, s.name, s.email, s.matric_no, s.phone, s.gender, s.status, s.profile_picture 
        FROM students s 
        WHERE s.class_id = ? AND s.status = 'active'
        ORDER BY s.name
    ");
    $stmt->execute([$classId]);
    
    // Get class info
    $classStmt = $db->prepare("SELECT name FROM classes WHERE id = ?");
    $classStmt->execute([$classId]);
    $className = $classStmt->fetch()['name'] ?? 'Unknown';
    
    jsonResponse([
        'success' => true, 
        'data' => $stmt->fetchAll(),
        'class_name' => $className
    ]);
}
