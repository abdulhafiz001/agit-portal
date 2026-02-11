<?php
/**
 * Admin Reports API
 */

function getReportStats() {
    $db = getDB();

    // Platform overview
    $totalStudents = $db->query("SELECT COUNT(*) as c FROM students WHERE status='active'")->fetch()['c'];
    $totalLecturers = $db->query("SELECT COUNT(*) as c FROM lecturers WHERE status='active'")->fetch()['c'];
    $totalClasses = $db->query("SELECT COUNT(*) as c FROM classes WHERE status='active'")->fetch()['c'];
    $totalSubjects = $db->query("SELECT COUNT(*) as c FROM subjects")->fetch()['c'];
    $totalExams = $db->query("SELECT COUNT(*) as c FROM exams")->fetch()['c'];
    $totalMaterials = $db->query("SELECT COUNT(*) as c FROM materials WHERE status='active'")->fetch()['c'];
    $totalScores = $db->query("SELECT COUNT(*) as c FROM scores")->fetch()['c'];
    $avgScore = $db->query("SELECT ROUND(AVG(total_score), 1) as a FROM scores")->fetch()['a'] ?? 0;

    // Enrollment by month (last 6 months)
    $enrollment = $db->query("
        SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count
        FROM students WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY month ORDER BY month
    ")->fetchAll();

    // Class distribution
    $classDist = $db->query("
        SELECT c.name, COUNT(s.id) as count
        FROM classes c LEFT JOIN students s ON s.class_id = c.id AND s.status='active'
        WHERE c.status='active' GROUP BY c.id, c.name ORDER BY count DESC LIMIT 10
    ")->fetchAll();

    // Subject popularity (by enrollment)
    $subjectPop = $db->query("
        SELECT s.name, s.code, COUNT(cs.class_id) as class_count
        FROM subjects s LEFT JOIN class_subjects cs ON cs.subject_id = s.id
        GROUP BY s.id, s.name, s.code ORDER BY class_count DESC LIMIT 10
    ")->fetchAll();

    // Exam stats
    $examsByType = $db->query("SELECT exam_type, COUNT(*) as count FROM exams GROUP BY exam_type")->fetchAll();
    $examsByStatus = $db->query("SELECT status, COUNT(*) as count FROM exams GROUP BY status")->fetchAll();

    // Lecturer activity (top lecturers by number of exams)
    $topLecturers = $db->query("
        SELECT l.name, COUNT(e.id) as exam_count,
            (SELECT COUNT(*) FROM materials m WHERE m.lecturer_id = l.id) as material_count,
            (SELECT COUNT(*) FROM scores sc WHERE sc.lecturer_id = l.id) as score_count
        FROM lecturers l LEFT JOIN exams e ON e.lecturer_id = l.id
        WHERE l.status='active' GROUP BY l.id, l.name ORDER BY exam_count DESC LIMIT 10
    ")->fetchAll();

    // Pass/Fail breakdown
    $passCount = $db->query("SELECT COUNT(*) as c FROM scores WHERE total_score >= 50")->fetch()['c'];
    $failCount = $db->query("SELECT COUNT(*) as c FROM scores WHERE total_score < 50")->fetch()['c'];

    // Grade distribution
    $grades = $db->query("SELECT grade, COUNT(*) as count FROM scores GROUP BY grade ORDER BY grade")->fetchAll();

    // Recent activity
    $recentActivity = $db->query("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 15")->fetchAll();

    jsonResponse(['success' => true, 'data' => [
        'overview' => [
            'total_students' => (int)$totalStudents,
            'total_lecturers' => (int)$totalLecturers,
            'total_classes' => (int)$totalClasses,
            'total_subjects' => (int)$totalSubjects,
            'total_exams' => (int)$totalExams,
            'total_materials' => (int)$totalMaterials,
            'total_scores' => (int)$totalScores,
            'avg_score' => $avgScore,
        ],
        'enrollment' => $enrollment,
        'class_distribution' => $classDist,
        'subject_popularity' => $subjectPop,
        'exams_by_type' => $examsByType,
        'exams_by_status' => $examsByStatus,
        'top_lecturers' => $topLecturers,
        'pass_fail' => ['pass' => (int)$passCount, 'fail' => (int)$failCount],
        'grades' => $grades,
        'recent_activity' => $recentActivity,
    ]]);
}
