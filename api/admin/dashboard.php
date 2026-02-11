<?php
/**
 * Admin Dashboard API
 */

function getDashboardStats() {
    $db = getDB();
    
    // Count stats
    $students = $db->query("SELECT COUNT(*) as count FROM students")->fetch()['count'];
    $lecturers = $db->query("SELECT COUNT(*) as count FROM lecturers")->fetch()['count'];
    $subjects = $db->query("SELECT COUNT(*) as count FROM subjects WHERE status='active'")->fetch()['count'];
    $classes = $db->query("SELECT COUNT(*) as count FROM classes WHERE status='active'")->fetch()['count'];
    $activeStudents = $db->query("SELECT COUNT(*) as count FROM students WHERE status='active'")->fetch()['count'];
    $exams = $db->query("SELECT COUNT(*) as count FROM exams")->fetch()['count'];
    
    // Monthly enrollment (last 6 months)
    $enrollment = $db->query("
        SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
        FROM students 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY month ORDER BY month
    ")->fetchAll();
    
    // Class distribution
    $classDistribution = $db->query("
        SELECT c.name, COUNT(s.id) as student_count 
        FROM classes c 
        LEFT JOIN students s ON s.class_id = c.id 
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
    
    // Gender distribution
    $genderStats = $db->query("
        SELECT gender, COUNT(*) as count FROM students WHERE gender IS NOT NULL GROUP BY gender
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
            ],
            'enrollment' => $enrollment,
            'class_distribution' => $classDistribution,
            'recent_activity' => $recentActivity,
            'gender_stats' => $genderStats,
        ]
    ]);
}
