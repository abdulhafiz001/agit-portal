<?php
/**
 * Student Assignments API
 */

function getStudentAssignments() {
    $db = getDB();
    $classId = $_SESSION['class_id'] ?? null;
    $studentId = $_SESSION['user_id'];
    if (!$classId) { jsonResponse(['success' => true, 'data' => []]); return; }

    $stmt = $db->prepare("
        SELECT a.*, s.name as subject_name, s.code as subject_code, l.name as lecturer_name,
            (SELECT asub.id FROM assignment_submissions asub WHERE asub.assignment_id = a.id AND asub.student_id = ?) as submission_id,
            (SELECT asub.score FROM assignment_submissions asub WHERE asub.assignment_id = a.id AND asub.student_id = ?) as my_score,
            (SELECT asub.status FROM assignment_submissions asub WHERE asub.assignment_id = a.id AND asub.student_id = ?) as my_status,
            (SELECT asub.feedback FROM assignment_submissions asub WHERE asub.assignment_id = a.id AND asub.student_id = ?) as my_feedback
        FROM assignments a
        JOIN subjects s ON s.id = a.subject_id
        JOIN lecturers l ON l.id = a.lecturer_id
        WHERE a.class_id = ? AND a.status = 'active'
        ORDER BY a.due_date IS NULL, a.due_date ASC, a.created_at DESC
    ");
    $stmt->execute([$studentId, $studentId, $studentId, $studentId, $classId]);
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}

function submitAssignment($assignmentId) {
    $db = getDB();
    $studentId = $_SESSION['user_id'];
    $classId = $_SESSION['class_id'] ?? null;

    // Verify assignment is for student's class
    $check = $db->prepare("SELECT id FROM assignments WHERE id = ? AND class_id = ? AND status = 'active'");
    $check->execute([$assignmentId, $classId]);
    if (!$check->fetch()) jsonResponse(['success' => false, 'message' => 'Assignment not found or closed.'], 400);

    // Check if already submitted
    $existing = $db->prepare("SELECT id FROM assignment_submissions WHERE assignment_id = ? AND student_id = ?");
    $existing->execute([$assignmentId, $studentId]);
    if ($existing->fetch()) jsonResponse(['success' => false, 'message' => 'You have already submitted this assignment.'], 400);

    $answerText = $_POST['answer_text'] ?? '';
    $filePath = null; $fileName = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['file'], 'submissions', null, 20 * 1024 * 1024);
        if ($result['success']) { $filePath = $result['path']; $fileName = $_FILES['file']['name']; }
    }

    if (!$answerText && !$filePath) {
        jsonResponse(['success' => false, 'message' => 'Please provide an answer or upload a file.'], 400);
    }

    $stmt = $db->prepare("INSERT INTO assignment_submissions (assignment_id, student_id, answer_text, file_path, file_name) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$assignmentId, $studentId, $answerText, $filePath, $fileName]);
    logActivity('student', $studentId, 'submit_assignment', 'Submitted assignment #' . $assignmentId);
    jsonResponse(['success' => true, 'message' => 'Assignment submitted successfully.']);
}
