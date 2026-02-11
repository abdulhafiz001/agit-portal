<?php
/**
 * Student Results API
 */

function getMyResults() {
    $db = getDB();
    $studentId = $_SESSION['user_id'];
    $classId = $_SESSION['class_id'] ?? null;

    // Check for results restriction
    $student = $db->prepare("SELECT status, restriction_type, restriction_reason FROM students WHERE id = ?");
    $student->execute([$studentId]);
    $stu = $student->fetch();
    if ($stu && $stu['status'] === 'restricted' && !empty($stu['restriction_type'])) {
        $restrictions = explode(',', $stu['restriction_type']);
        if (in_array('results', $restrictions)) {
            jsonResponse(['success' => false, 'message' => 'Your results have been restricted. Reason: ' . ($stu['restriction_reason'] ?? 'Contact admin.')], 403);
        }
    }

    // Scores (manual)
    $scores = $db->prepare("
        SELECT sc.*, s.name as subject_name, s.code as subject_code, c.name as class_name
        FROM scores sc
        JOIN subjects s ON s.id = sc.subject_id
        JOIN classes c ON c.id = sc.class_id
        WHERE sc.student_id = ?
        ORDER BY sc.semester, s.name
    ");
    $scores->execute([$studentId]);

    // Exam results
    $exams = $db->prepare("
        SELECT ea.score, ea.total_marks, ea.percentage, ea.status, ea.end_time,
            e.title as exam_title, s.name as subject_name, s.code as subject_code
        FROM exam_attempts ea
        JOIN exams e ON e.id = ea.exam_id
        JOIN subjects s ON s.id = e.subject_id
        WHERE ea.student_id = ? AND ea.status IN ('submitted','graded')
        ORDER BY ea.end_time DESC
    ");
    $exams->execute([$studentId]);

    // Summary
    $scoreData = $scores->fetchAll();
    $examData = $exams->fetchAll();
    $avgScore = 0;
    if (count($scoreData)) {
        $avgScore = round(array_sum(array_column($scoreData, 'total_score')) / count($scoreData), 1);
    }

    jsonResponse(['success' => true, 'data' => [
        'scores' => $scoreData,
        'exams' => $examData,
        'summary' => [
            'total_subjects' => count($scoreData),
            'avg_score' => $avgScore,
            'total_exams' => count($examData),
        ]
    ]]);
}
