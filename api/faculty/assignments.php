<?php
/**
 * Faculty Assignments API
 */

function getMyAssignments() {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    $stmt = $db->prepare("
        SELECT a.*, c.name as class_name, s.name as subject_name, s.code as subject_code,
            (SELECT COUNT(*) FROM assignment_submissions asub WHERE asub.assignment_id = a.id) as submission_count,
            (SELECT COUNT(*) FROM students st WHERE st.class_id = a.class_id AND st.status = 'active') as total_students
        FROM assignments a
        JOIN classes c ON c.id = a.class_id
        JOIN subjects s ON s.id = a.subject_id
        WHERE a.lecturer_id = ?
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([$lecturerId]);
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}

function createAssignment() {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];

    $title = sanitize($_POST['title'] ?? '');
    $description = $_POST['description'] ?? '';
    $classId = $_POST['class_id'] ?? '';
    $subjectId = $_POST['subject_id'] ?? '';
    $dueDate = $_POST['due_date'] ?? null;
    $totalMarks = $_POST['total_marks'] ?? 100;

    if (!$title || !$classId || !$subjectId) {
        jsonResponse(['success' => false, 'message' => 'Title, class, and subject are required.'], 400);
    }

    $filePath = null; $fileName = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['file'], 'assignments', null, 20 * 1024 * 1024);
        if ($result['success']) { $filePath = $result['path']; $fileName = $_FILES['file']['name']; }
    }

    $stmt = $db->prepare("
        INSERT INTO assignments (title, description, class_id, subject_id, lecturer_id, due_date, total_marks, file_path, file_name)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$title, $description, $classId, $subjectId, $lecturerId, $dueDate ?: null, $totalMarks, $filePath, $fileName]);
    logActivity('lecturer', $lecturerId, 'create_assignment', 'Created assignment: ' . $title);
    jsonResponse(['success' => true, 'message' => 'Assignment created.']);
}

function getAssignmentSubmissions($id) {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    // Verify ownership
    $check = $db->prepare("SELECT id FROM assignments WHERE id = ? AND lecturer_id = ?");
    $check->execute([$id, $lecturerId]);
    if (!$check->fetch()) jsonResponse(['success' => false, 'message' => 'Assignment not found.'], 404);

    $stmt = $db->prepare("
        SELECT asub.*, st.name as student_name, st.matric_no
        FROM assignment_submissions asub
        JOIN students st ON st.id = asub.student_id
        WHERE asub.assignment_id = ?
        ORDER BY asub.submitted_at DESC
    ");
    $stmt->execute([$id]);
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}

function gradeSubmission($submissionId) {
    $db = getDB();
    $data = getPostData();
    $score = $data['score'] ?? null;
    $feedback = sanitize($data['feedback'] ?? '');
    if ($score === null) jsonResponse(['success' => false, 'message' => 'Score is required.'], 400);

    $stmt = $db->prepare("UPDATE assignment_submissions SET score = ?, feedback = ?, status = 'graded', graded_at = NOW() WHERE id = ?");
    $stmt->execute([$score, $feedback, $submissionId]);
    jsonResponse(['success' => true, 'message' => 'Submission graded.']);
}

function deleteAssignment($id) {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    $db->prepare("DELETE FROM assignments WHERE id = ? AND lecturer_id = ?")->execute([$id, $lecturerId]);
    jsonResponse(['success' => true, 'message' => 'Assignment deleted.']);
}
