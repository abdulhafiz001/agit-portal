<?php
/**
 * Admin Exams API
 */

function getAdminExams() {
    $db = getDB();
    $status = $_GET['status'] ?? '';
    $where = [];
    $params = [];
    if ($status) { $where[] = "e.status = ?"; $params[] = $status; }
    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $stmt = $db->prepare("
        SELECT e.*, c.name as class_name, s.name as subject_name, s.code as subject_code,
            l.name as lecturer_name,
            (SELECT COUNT(*) FROM exam_questions eq WHERE eq.exam_id = e.id) as question_count,
            (SELECT COUNT(*) FROM exam_attempts ea WHERE ea.exam_id = e.id) as attempt_count,
            (SELECT COUNT(*) FROM exam_attempts ea WHERE ea.exam_id = e.id AND ea.status = 'submitted') as submitted_count,
            (SELECT COUNT(*) FROM students st WHERE st.class_id = e.class_id AND st.status = 'active') as total_students
        FROM exams e
        JOIN classes c ON c.id = e.class_id
        JOIN subjects s ON s.id = e.subject_id
        JOIN lecturers l ON l.id = e.lecturer_id
        $whereClause
        ORDER BY e.created_at DESC
    ");
    $stmt->execute($params);
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}

function getAdminExam($id) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT e.*, c.name as class_name, s.name as subject_name, l.name as lecturer_name,
            (SELECT COUNT(*) FROM students st WHERE st.class_id = e.class_id AND st.status = 'active') as total_students
        FROM exams e JOIN classes c ON c.id = e.class_id JOIN subjects s ON s.id = e.subject_id JOIN lecturers l ON l.id = e.lecturer_id
        WHERE e.id = ?
    ");
    $stmt->execute([$id]);
    $exam = $stmt->fetch();
    if (!$exam) jsonResponse(['success' => false, 'message' => 'Exam not found.'], 404);

    $qStmt = $db->prepare("SELECT * FROM exam_questions WHERE exam_id = ? ORDER BY sort_order, id");
    $qStmt->execute([$id]);
    $exam['questions'] = $qStmt->fetchAll();

    // Get attempts
    $aStmt = $db->prepare("
        SELECT ea.*, st.name as student_name, st.matric_no
        FROM exam_attempts ea JOIN students st ON st.id = ea.student_id
        WHERE ea.exam_id = ? ORDER BY ea.start_time DESC
    ");
    $aStmt->execute([$id]);
    $exam['attempts'] = $aStmt->fetchAll();

    jsonResponse(['success' => true, 'data' => $exam]);
}

function approveExam($id) {
    $db = getDB();
    $data = getPostData();
    $action = $data['action'] ?? ''; // approve or reject

    $check = $db->prepare("SELECT status FROM exams WHERE id = ?");
    $check->execute([$id]);
    $exam = $check->fetch();
    if (!$exam || $exam['status'] !== 'pending') {
        jsonResponse(['success' => false, 'message' => 'Only pending exams can be approved/rejected.'], 400);
    }

    if ($action === 'approve') {
        // Generate a 6-char exam code
        $examCode = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $db->prepare("UPDATE exams SET status = 'approved', approved_by = ?, approved_at = NOW(), exam_code = ? WHERE id = ?")->execute([$_SESSION['user_id'], $examCode, $id]);
        logActivity('admin', $_SESSION['user_id'], 'approve_exam', 'Approved exam #' . $id);
        jsonResponse(['success' => true, 'message' => "Exam approved. Exam Code: $examCode", 'exam_code' => $examCode]);
    } elseif ($action === 'reject') {
        $reason = sanitize($data['reason'] ?? '');
        $db->prepare("UPDATE exams SET status = 'rejected', rejection_reason = ? WHERE id = ?")->execute([$reason, $id]);
        logActivity('admin', $_SESSION['user_id'], 'reject_exam', 'Rejected exam #' . $id);
        jsonResponse(['success' => true, 'message' => 'Exam rejected.']);
    }

    jsonResponse(['success' => false, 'message' => 'Invalid action.'], 400);
}

function generateContinueKey($attemptId) {
    $db = getDB();
    $key = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    // Reset attempt to in_progress and set continue key
    $stmt = $db->prepare("UPDATE exam_attempts SET status = 'in_progress', end_time = NULL, continue_key = ? WHERE id = ?");
    $stmt->execute([$key, $attemptId]);
    logActivity('admin', $_SESSION['user_id'], 'generate_continue_key', "Continue key for attempt #$attemptId: $key");
    jsonResponse(['success' => true, 'message' => "Continue key: $key", 'continue_key' => $key]);
}

function startExam($id) {
    $db = getDB();
    $check = $db->prepare("SELECT status FROM exams WHERE id = ?");
    $check->execute([$id]);
    $exam = $check->fetch();
    if (!$exam || !in_array($exam['status'], ['approved', 'completed'])) {
        jsonResponse(['success' => false, 'message' => 'Only approved exams can be started.'], 400);
    }
    $db->prepare("UPDATE exams SET status = 'active', started_at = NOW(), ended_at = NULL WHERE id = ?")->execute([$id]);
    logActivity('admin', $_SESSION['user_id'], 'start_exam', 'Started exam #' . $id);
    jsonResponse(['success' => true, 'message' => 'Exam started! Students can now take the exam.']);
}

function stopExam($id) {
    $db = getDB();
    $check = $db->prepare("SELECT status FROM exams WHERE id = ?");
    $check->execute([$id]);
    $exam = $check->fetch();
    if (!$exam || $exam['status'] !== 'active') {
        jsonResponse(['success' => false, 'message' => 'Only active exams can be stopped.'], 400);
    }
    $db->prepare("UPDATE exams SET status = 'completed', ended_at = NOW() WHERE id = ?")->execute([$id]);
    // Auto-submit any in-progress attempts
    $db->prepare("UPDATE exam_attempts SET status = 'timed_out', end_time = NOW() WHERE exam_id = ? AND status = 'in_progress'")->execute([$id]);
    logActivity('admin', $_SESSION['user_id'], 'stop_exam', 'Stopped exam #' . $id);
    jsonResponse(['success' => true, 'message' => 'Exam stopped.']);
}
