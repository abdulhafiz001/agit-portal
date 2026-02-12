<?php
/**
 * Faculty Assignments API
 */

function getAssignmentsTableColumns() {
    static $columns = null;
    if ($columns !== null) return $columns;
    $db = getDB();
    $rows = $db->query("SHOW COLUMNS FROM assignments")->fetchAll();
    $columns = [];
    foreach ($rows as $r) {
        $columns[$r['Field']] = $r;
    }
    return $columns;
}

function getMyAssignments() {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    $columns = getAssignmentsTableColumns();
    $marksExpr = isset($columns['total_marks']) ? 'a.total_marks' : (isset($columns['max_score']) ? 'a.max_score' : '100');
    $fileNameExpr = isset($columns['file_name']) ? 'a.file_name' : 'NULL';
    $stmt = $db->prepare("
        SELECT a.*,
            {$marksExpr} as total_marks,
            {$fileNameExpr} as file_name,
            c.name as class_name, s.name as subject_name, s.code as subject_code,
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
    $columns = getAssignmentsTableColumns();

    $title = sanitize($_POST['title'] ?? '');
    $description = $_POST['description'] ?? '';
    $classId = $_POST['class_id'] ?? '';
    $subjectId = $_POST['subject_id'] ?? '';
    $dueDate = $_POST['due_date'] ?? null;
    $totalMarks = $_POST['total_marks'] ?? 100;

    if (!$title || !$classId || !$subjectId) {
        jsonResponse(['success' => false, 'message' => 'Title, class, and subject are required.'], 400);
    }

    $marksColumn = isset($columns['total_marks']) ? 'total_marks' : (isset($columns['max_score']) ? 'max_score' : null);
    if ($marksColumn === null) {
        jsonResponse([
            'success' => false,
            'message' => 'Assignments schema is outdated (missing total_marks/max_score). Please run the latest SQL migration.'
        ], 500);
    }

    $dueDateValue = ($dueDate && trim($dueDate) !== '') ? $dueDate : null;
    if (($columns['due_date']['Null'] ?? 'YES') === 'NO' && $dueDateValue === null) {
        jsonResponse([
            'success' => false,
            'message' => 'This server requires a due date for assignments. Please set a due date or update the database schema.'
        ], 400);
    }

    $filePath = null; $fileName = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['file'], 'assignments', null, 20 * 1024 * 1024);
        if ($result['success']) {
            $filePath = $result['path'];
            $fileName = $_FILES['file']['name'];
        } else {
            jsonResponse(['success' => false, 'message' => $result['message'] ?? 'File upload failed.'], 400);
        }
    }

    $insertColumns = ['title', 'description', 'class_id', 'subject_id', 'lecturer_id', 'due_date', $marksColumn, 'file_path'];
    $insertValues = [$title, $description, $classId, $subjectId, $lecturerId, $dueDateValue, $totalMarks, $filePath];
    if (isset($columns['file_name'])) {
        $insertColumns[] = 'file_name';
        $insertValues[] = $fileName;
    }

    $placeholders = implode(', ', array_fill(0, count($insertColumns), '?'));
    $columnSql = implode(', ', $insertColumns);

    try {
        $stmt = $db->prepare("INSERT INTO assignments ({$columnSql}) VALUES ({$placeholders})");
        $stmt->execute($insertValues);
        logActivity('lecturer', $lecturerId, 'create_assignment', 'Created assignment: ' . $title);
        jsonResponse(['success' => true, 'message' => 'Assignment created.']);
    } catch (PDOException $e) {
        jsonResponse([
            'success' => false,
            'message' => 'Could not create assignment: ' . $e->getMessage()
        ], 500);
    }
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
