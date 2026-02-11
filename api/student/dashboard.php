<?php
/**
 * Student Dashboard API
 */

function getStudentDashboardStats() {
    $db = getDB();
    $studentId = $_SESSION['user_id'];
    $classId = $_SESSION['class_id'] ?? null;
    
    // Get class info
    $className = 'Unassigned';
    $classType = '';
    if ($classId) {
        $stmt = $db->prepare("SELECT name, type, current_semester, semester_count FROM classes WHERE id = ?");
        $stmt->execute([$classId]);
        $classInfo = $stmt->fetch();
        if ($classInfo) {
            $className = $classInfo['name'];
            $classType = $classInfo['type'];
        }
    }
    
    // Subjects count (via class)
    $subjectCount = 0;
    $subjects = [];
    if ($classId) {
        $stmt = $db->prepare("
            SELECT s.id, s.name, s.code 
            FROM subjects s 
            INNER JOIN class_subjects cs ON cs.subject_id = s.id 
            WHERE cs.class_id = ?
        ");
        $stmt->execute([$classId]);
        $subjects = $stmt->fetchAll();
        $subjectCount = count($subjects);
    }
    
    // Exams taken
    $examCount = $db->prepare("SELECT COUNT(*) as count FROM exam_attempts WHERE student_id = ?");
    $examCount->execute([$studentId]);
    $totalExams = $examCount->fetch()['count'];
    
    // Class mates count
    $classMates = 0;
    if ($classId) {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM students WHERE class_id = ? AND id != ? AND status = 'active'");
        $stmt->execute([$classId, $studentId]);
        $classMates = $stmt->fetch()['count'];
    }
    
    // Lecturers assigned to my class
    $lecturers = [];
    if ($classId) {
        $stmt = $db->prepare("
            SELECT l.name, l.email 
            FROM lecturers l 
            INNER JOIN lecturer_classes lc ON lc.lecturer_id = l.id 
            WHERE lc.class_id = ? AND l.status = 'active'
        ");
        $stmt->execute([$classId]);
        $lecturers = $stmt->fetchAll();
    }
    
    jsonResponse([
        'success' => true,
        'data' => [
            'stats' => [
                'class_name' => $className,
                'class_type' => $classType,
                'total_subjects' => $subjectCount,
                'total_exams' => (int)$totalExams,
                'classmates' => (int)$classMates,
            ],
            'subjects' => $subjects,
            'lecturers' => $lecturers,
        ]
    ]);
}
