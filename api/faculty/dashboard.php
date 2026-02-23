<?php
/**
 * Faculty Dashboard API
 */

function getFacultyDashboardStats() {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    
    // My classes count
    $classCount = $db->prepare("SELECT COUNT(*) as count FROM lecturer_classes WHERE lecturer_id = ?");
    $classCount->execute([$lecturerId]);
    $totalClasses = $classCount->fetch()['count'];
    
    // My subjects count
    $subjectCount = $db->prepare("SELECT COUNT(*) as count FROM lecturer_subjects WHERE lecturer_id = ?");
    $subjectCount->execute([$lecturerId]);
    $totalSubjects = $subjectCount->fetch()['count'];
    
    // Total students in my classes
    $studentCount = $db->prepare("
        SELECT COUNT(DISTINCT s.id) as count 
        FROM students s 
        INNER JOIN lecturer_classes lc ON lc.class_id = s.class_id 
        WHERE lc.lecturer_id = ?
    ");
    $studentCount->execute([$lecturerId]);
    $totalStudents = $studentCount->fetch()['count'];
    
    // My exams count
    $examCount = $db->prepare("SELECT COUNT(*) as count FROM exams WHERE lecturer_id = ?");
    $examCount->execute([$lecturerId]);
    $totalExams = $examCount->fetch()['count'];
    
    // Additional faculty metrics
    $assignments = $db->prepare("SELECT COUNT(*) as count FROM assignments WHERE lecturer_id = ?");
    $assignments->execute([$lecturerId]);
    $assignmentCount = $assignments->fetch()['count'];
    
    // Exams created and marked
    $examsCreated = $db->prepare("SELECT COUNT(*) FROM exams WHERE lecturer_id = ?");
    $examsCreated->execute([$lecturerId]);
    $examsCreatedCount = $examsCreated->fetchColumn();
    
    $examsMarked = $db->prepare("SELECT COUNT(*) FROM exam_answers ea JOIN exam_attempts et ON et.id = ea.attempt_id JOIN exams e ON e.id = et.exam_id WHERE e.lecturer_id = ? AND ea.marks_awarded IS NOT NULL");
    $examsMarked->execute([$lecturerId]);
    $examsMarkedCount = $examsMarked->fetchColumn();
    
    // Assignments viewed (submissions)
    $assignmentsViewed = $db->prepare("SELECT COUNT(*) FROM assignment_submissions aps JOIN assignments a ON a.id = aps.assignment_id WHERE a.lecturer_id = ?");
    $assignmentsViewed->execute([$lecturerId]);
    $assignmentsViewedCount = $assignmentsViewed->fetchColumn();
    
    $todayDay = strtolower(date('l'));
    $todaySchedule = $db->prepare("SELECT COUNT(*) as count FROM class_schedules WHERE lecturer_id = ? AND day_of_week = ? AND status = 'active'");
    $todaySchedule->execute([$lecturerId, $todayDay]);
    $todayClasses = $todaySchedule->fetch()['count'];
    
    $totalMaterials = $db->prepare("SELECT COUNT(*) as count FROM materials WHERE lecturer_id = ?");
    $totalMaterials->execute([$lecturerId]);
    $materialCount = $totalMaterials->fetch()['count'];
    
    // Lecturer profile (name, profile_picture) for dashboard header
    $lecturerStmt = $db->prepare("SELECT * FROM lecturers WHERE id = ?");
    $lecturerStmt->execute([$lecturerId]);
    $lecturerRow = $lecturerStmt->fetch();
    $lecturer = $lecturerRow ? [
        'name' => $lecturerRow['name'],
        'profile_picture' => $lecturerRow['profile_picture'] ?? null,
    ] : [];

    
    // My classes with student count
    $classes = $db->prepare("
        SELECT c.id, c.name, c.type, 
            (SELECT COUNT(*) FROM students s WHERE s.class_id = c.id) as student_count
        FROM classes c 
        INNER JOIN lecturer_classes lc ON lc.class_id = c.id 
        WHERE lc.lecturer_id = ? AND c.status = 'active'
    ");
    $classes->execute([$lecturerId]);
    $myClasses = $classes->fetchAll();
    
    // My subjects
    $subjects = $db->prepare("
        SELECT s.id, s.name, s.code 
        FROM subjects s 
        INNER JOIN lecturer_subjects ls ON ls.subject_id = s.id 
        WHERE ls.lecturer_id = ?
    ");
    $subjects->execute([$lecturerId]);
    $mySubjects = $subjects->fetchAll();
    
    jsonResponse([
        'success' => true,
        'data' => [
            'profile' => [
                'name' => ($lecturer['name'] ?? null) ?: $_SESSION['user_name'],
                'profile_picture' => $lecturer['profile_picture'] ?? null,
            ],
            'stats' => [
                'total_classes' => (int)$totalClasses,
                'total_subjects' => (int)$totalSubjects,
                'total_students' => (int)$totalStudents,
                'total_exams' => (int)$totalExams,
                'assignment_count' => (int)$assignmentCount,
                'today_classes' => (int)$todayClasses,
                'material_count' => (int)$materialCount,
                'active_classes_count' => (int)$totalClasses,
                'exams_created' => (int)$examsCreatedCount,
                'exams_marked' => (int)$examsMarkedCount,
                'assignments_viewed' => (int)$assignmentsViewedCount,
            ],
            'classes' => $myClasses,
            'subjects' => $mySubjects,
        ]
    ]);
}
