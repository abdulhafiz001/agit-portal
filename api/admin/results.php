<?php
/**
 * Admin Results API
 */

function getAdminResults() {
    $db = getDB();
    $classId = $_GET['class_id'] ?? '';
    $subjectId = $_GET['subject_id'] ?? '';
    $studentId = $_GET['student_id'] ?? '';
    
    $where = []; $params = [];
    if ($classId) { $where[] = "sc.class_id = ?"; $params[] = $classId; }
    if ($subjectId) { $where[] = "sc.subject_id = ?"; $params[] = $subjectId; }
    if ($studentId) { $where[] = "sc.student_id = ?"; $params[] = $studentId; }
    
    $wc = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $stmt = $db->prepare("SELECT sc.*, sc.total_score as total, s.name as student_name, s.matric_no, s.profile_picture,
        sub.name as subject_name, sub.code as subject_code,
        c.name as class_name
        FROM scores sc
        JOIN students s ON s.id = sc.student_id
        JOIN subjects sub ON sub.id = sc.subject_id
        JOIN classes c ON c.id = sc.class_id
        $wc ORDER BY s.name, sub.name");
    $stmt->execute($params);
    
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}

function getResultsSummary() {
    $db = getDB();
    $classId = $_GET['class_id'] ?? '';

    // Summary stats
    $totalScores = $db->query("SELECT COUNT(*) as c FROM scores")->fetch()['c'];
    $avgScore = $db->query("SELECT ROUND(AVG(total_score),1) as a FROM scores")->fetch()['a'] ?? 0;
    $passRate = 0;
    if ($totalScores > 0) {
        $passed = $db->query("SELECT COUNT(*) as c FROM scores WHERE total_score >= 50")->fetch()['c'];
        $passRate = round(($passed / $totalScores) * 100, 1);
    }

    // Grade distribution
    $grades = $db->query("SELECT grade, COUNT(*) as count FROM scores GROUP BY grade ORDER BY grade")->fetchAll();

    // Class performance
    $classPerf = $db->query("
        SELECT c.name, ROUND(AVG(sc.total_score),1) as avg_score, COUNT(sc.id) as total
        FROM scores sc JOIN classes c ON c.id = sc.class_id
        GROUP BY c.id, c.name ORDER BY avg_score DESC
    ")->fetchAll();

    jsonResponse(['success' => true, 'data' => [
        'total_scores' => (int)$totalScores,
        'avg_score' => $avgScore,
        'pass_rate' => $passRate,
        'grades' => $grades,
        'class_performance' => $classPerf
    ]]);
}
