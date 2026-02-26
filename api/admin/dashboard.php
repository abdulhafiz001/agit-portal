<?php
/**
 * Admin Dashboard API
 */

function getDashboardStats() {
    $db = getDB();
    
    $hasApproval = (bool) $db->query("SHOW COLUMNS FROM students LIKE 'approval_status'")->fetch();
    $approvedFilter = $hasApproval ? " AND approval_status = 'approved'" : '';

    // Count stats - only approved students
    $students = $db->query("SELECT COUNT(*) as count FROM students WHERE 1=1" . ($hasApproval ? " AND approval_status = 'approved'" : ""))->fetch()['count'];
    $lecturers = $db->query("SELECT COUNT(*) as count FROM lecturers")->fetch()['count'];
    $subjects = $db->query("SELECT COUNT(*) as count FROM subjects WHERE status='active'")->fetch()['count'];
    $classes = $db->query("SELECT COUNT(*) as count FROM classes WHERE status='active'")->fetch()['count'];
    $activeStudents = $db->query("SELECT COUNT(*) as count FROM students WHERE status='active'" . $approvedFilter)->fetch()['count'];
    $exams = $db->query("SELECT COUNT(*) as count FROM exams")->fetch()['count'];

    // Additional stats: concluded classes, suspended classes, graduated, restricted (all approved only)
    $concludedClasses = 0;
    $suspendedClasses = 0;
    try {
        $concludedClasses = $db->query("SELECT COUNT(*) FROM classes WHERE status IN ('completed','archived')")->fetchColumn();
        $suspendedClasses = $db->query("SELECT COUNT(*) FROM classes WHERE status = 'suspended'")->fetchColumn();
    } catch (Exception $e) {}
    $graduatedStudents = $db->query("SELECT COUNT(*) as count FROM students WHERE status='graduated'" . $approvedFilter)->fetch()['count'];
    $restrictedStudents = $db->query("SELECT COUNT(*) as count FROM students WHERE status='restricted'" . $approvedFilter)->fetch()['count'];

    // Monthly enrollment (last 6 months) - approved students only
    $enrollment = $db->query("
        SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
        FROM students 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)" . ($hasApproval ? " AND approval_status = 'approved'" : "") . "
        GROUP BY month ORDER BY month
    ")->fetchAll();

    // Class distribution - approved students only
    $classDistribution = $db->query("
        SELECT c.name, COUNT(s.id) as student_count 
        FROM classes c 
        LEFT JOIN students s ON s.class_id = c.id " . ($hasApproval ? " AND s.approval_status = 'approved'" : "") . "
        WHERE c.status = 'active'
        GROUP BY c.id, c.name
        ORDER BY student_count DESC
        LIMIT 6
    ")->fetchAll();
    
    // Recent activities
    $recentActivity = $db->query("
        SELECT action, description, user_type, created_at 
        FROM activity_logs 
        ORDER BY created_at DESC 
        LIMIT 5
    ")->fetchAll();
    
    // Gender distribution - approved students only
    $genderStats = $db->query("
        SELECT gender, COUNT(*) as count FROM students WHERE gender IS NOT NULL" . $approvedFilter . " GROUP BY gender
    ")->fetchAll();

    jsonResponse([
        'success' => true,
        'data' => [
            'stats' => [
                'total_students' => (int)$students,
                'total_lecturers' => (int)$lecturers,
                'total_subjects' => (int)$subjects,
                'total_classes' => (int)$classes,
                'active_students' => (int)$activeStudents,
                'total_exams' => (int)$exams,
                'concluded_classes' => (int)$concludedClasses,
                'suspended_classes' => (int)$suspendedClasses,
                'graduated_students' => (int)$graduatedStudents,
                'restricted_students' => (int)$restrictedStudents,
            ],
            'enrollment' => $enrollment,
            'class_distribution' => $classDistribution,
            'recent_activity' => $recentActivity,
            'gender_stats' => $genderStats,
        ]
    ]);
}
