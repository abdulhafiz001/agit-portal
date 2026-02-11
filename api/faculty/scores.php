<?php
/**
 * Faculty Scores API
 */

function getScoreSheet() {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    $classId = $_GET['class_id'] ?? '';
    $subjectId = $_GET['subject_id'] ?? '';
    $semester = $_GET['semester'] ?? 1;

    if (!$classId || !$subjectId) {
        jsonResponse(['success' => false, 'message' => 'Class and subject are required.'], 400);
    }

    // Verify lecturer teaches this class/subject
    $checkClass = $db->prepare("SELECT id FROM lecturer_classes WHERE lecturer_id = ? AND class_id = ?");
    $checkClass->execute([$lecturerId, $classId]);
    if (!$checkClass->fetch()) {
        jsonResponse(['success' => false, 'message' => 'You are not assigned to this class.'], 403);
    }

    // Get students
    $students = $db->prepare("
        SELECT s.id, s.name, s.matric_no FROM students s
        WHERE s.class_id = ? AND s.status = 'active' ORDER BY s.name
    ");
    $students->execute([$classId]);
    $studentList = $students->fetchAll();

    // Get existing scores
    foreach ($studentList as &$st) {
        $scoreStmt = $db->prepare("
            SELECT ca_score, exam_score, total_score, grade, remark FROM scores
            WHERE student_id = ? AND subject_id = ? AND class_id = ? AND semester = ?
        ");
        $scoreStmt->execute([$st['id'], $subjectId, $classId, $semester]);
        $score = $scoreStmt->fetch();
        $st['ca_score'] = $score['ca_score'] ?? '';
        $st['exam_score'] = $score['exam_score'] ?? '';
        $st['total_score'] = $score['total_score'] ?? '';
        $st['grade'] = $score['grade'] ?? '';
        $st['remark'] = $score['remark'] ?? '';
    }

    jsonResponse(['success' => true, 'data' => $studentList]);
}

function saveScores() {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    $data = getPostData();

    $classId = $data['class_id'] ?? '';
    $subjectId = $data['subject_id'] ?? '';
    $semester = $data['semester'] ?? 1;
    $scores = $data['scores'] ?? [];

    if (!$classId || !$subjectId || empty($scores)) {
        jsonResponse(['success' => false, 'message' => 'Class, subject, and scores are required.'], 400);
    }

    $stmt = $db->prepare("
        INSERT INTO scores (student_id, subject_id, class_id, lecturer_id, ca_score, exam_score, total_score, grade, remark, semester)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE ca_score=VALUES(ca_score), exam_score=VALUES(exam_score),
        total_score=VALUES(total_score), grade=VALUES(grade), remark=VALUES(remark), lecturer_id=VALUES(lecturer_id)
    ");

    $saved = 0;
    foreach ($scores as $s) {
        $ca = floatval($s['ca_score'] ?? 0);
        $exam = floatval($s['exam_score'] ?? 0);
        $total = $ca + $exam;
        $grade = calculateGrade($total);
        $remark = $total >= 50 ? 'Pass' : 'Fail';

        $stmt->execute([
            $s['student_id'], $subjectId, $classId, $lecturerId,
            $ca, $exam, $total, $grade, $remark, $semester
        ]);
        $saved++;
    }

    logActivity('lecturer', $lecturerId, 'save_scores', "Saved $saved scores for class $classId, subject $subjectId");
    jsonResponse(['success' => true, 'message' => "$saved scores saved successfully."]);
}

function getMyTeachingOptions() {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];

    $classes = $db->prepare("
        SELECT c.id, c.name, c.type, c.current_semester, c.semester_count FROM classes c
        INNER JOIN lecturer_classes lc ON lc.class_id = c.id WHERE lc.lecturer_id = ? AND c.status = 'active'
    ");
    $classes->execute([$lecturerId]);

    $subjects = $db->prepare("
        SELECT s.id, s.name, s.code FROM subjects s
        INNER JOIN lecturer_subjects ls ON ls.subject_id = s.id WHERE ls.lecturer_id = ?
    ");
    $subjects->execute([$lecturerId]);

    jsonResponse(['success' => true, 'classes' => $classes->fetchAll(), 'subjects' => $subjects->fetchAll()]);
}

function calculateGrade($score) {
    if ($score >= 70) return 'A';
    if ($score >= 60) return 'B';
    if ($score >= 50) return 'C';
    if ($score >= 45) return 'D';
    if ($score >= 40) return 'E';
    return 'F';
}
