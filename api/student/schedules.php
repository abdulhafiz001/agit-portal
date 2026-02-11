<?php
/**
 * Student Schedules API
 */

function getMyClassSchedule() {
    $db = getDB();
    $studentId = $_SESSION['user_id'];
    $student = $db->prepare("SELECT class_id FROM students WHERE id = ?");
    $student->execute([$studentId]);
    $classId = $student->fetchColumn();
    if (!$classId) {
        jsonResponse(['success' => true, 'data' => []]);
        return;
    }
    
    $stmt = $db->prepare("SELECT cs.*, c.name as class_name, s.name as subject_name, s.code as subject_code, l.name as lecturer_name
        FROM class_schedules cs
        JOIN classes c ON c.id = cs.class_id
        JOIN subjects s ON s.id = cs.subject_id
        JOIN lecturers l ON l.id = cs.lecturer_id
        WHERE cs.class_id = ? AND cs.status = 'active'
        ORDER BY FIELD(cs.day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday'), cs.start_time");
    $stmt->execute([$classId]);
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}
