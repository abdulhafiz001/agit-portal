<?php
/**
 * Faculty Exams API
 */

function getMyExams() {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    $status = $_GET['status'] ?? '';

    $where = ["e.lecturer_id = ?"];
    $params = [$lecturerId];
    if ($status) { $where[] = "e.status = ?"; $params[] = $status; }

    $stmt = $db->prepare("
        SELECT e.*, c.name as class_name, s.name as subject_name, s.code as subject_code,
            (SELECT COUNT(*) FROM exam_questions eq WHERE eq.exam_id = e.id) as question_count,
            (SELECT COUNT(*) FROM exam_attempts ea WHERE ea.exam_id = e.id) as attempt_count
        FROM exams e
        JOIN classes c ON c.id = e.class_id
        JOIN subjects s ON s.id = e.subject_id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY e.created_at DESC
    ");
    $stmt->execute($params);
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}

function getMyExam($id) {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    $stmt = $db->prepare("
        SELECT e.*, c.name as class_name, s.name as subject_name
        FROM exams e JOIN classes c ON c.id = e.class_id JOIN subjects s ON s.id = e.subject_id
        WHERE e.id = ? AND e.lecturer_id = ?
    ");
    $stmt->execute([$id, $lecturerId]);
    $exam = $stmt->fetch();
    if (!$exam) jsonResponse(['success' => false, 'message' => 'Exam not found.'], 404);

    $qStmt = $db->prepare("SELECT * FROM exam_questions WHERE exam_id = ? ORDER BY sort_order, id");
    $qStmt->execute([$id]);
    $exam['questions'] = $qStmt->fetchAll();

    jsonResponse(['success' => true, 'data' => $exam]);
}

function createExam() {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    $data = getPostData();

    $errors = validateRequired($data, ['title', 'class_id', 'subject_id', 'exam_type', 'duration_minutes']);
    if ($errors) jsonResponse(['success' => false, 'message' => $errors[0]], 400);

    $stmt = $db->prepare("
        INSERT INTO exams (title, class_id, subject_id, lecturer_id, exam_type, duration_minutes, total_marks, pass_mark, instructions, shuffle_questions, show_result, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft')
    ");
    $stmt->execute([
        sanitize($data['title']), $data['class_id'], $data['subject_id'], $lecturerId,
        $data['exam_type'], $data['duration_minutes'], $data['total_marks'] ?? 100,
        $data['pass_mark'] ?? 50, sanitize($data['instructions'] ?? ''),
        $data['shuffle_questions'] ?? 0, $data['show_result'] ?? 1
    ]);
    $examId = $db->lastInsertId();

    // Add questions if provided
    if (!empty($data['questions']) && is_array($data['questions'])) {
        saveQuestions($db, $examId, $data['questions']);
    }

    logActivity('lecturer', $lecturerId, 'create_exam', 'Created exam: ' . $data['title']);
    jsonResponse(['success' => true, 'message' => 'Exam created.', 'exam_id' => $examId]);
}

function updateExam($id) {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    $data = getPostData();

    // Only allow editing draft/rejected exams
    $check = $db->prepare("SELECT status FROM exams WHERE id = ? AND lecturer_id = ?");
    $check->execute([$id, $lecturerId]);
    $exam = $check->fetch();
    if (!$exam) jsonResponse(['success' => false, 'message' => 'Exam not found.'], 404);
    if (!in_array($exam['status'], ['draft', 'rejected'])) {
        jsonResponse(['success' => false, 'message' => 'Can only edit draft or rejected exams.'], 400);
    }

    $stmt = $db->prepare("
        UPDATE exams SET title=?, class_id=?, subject_id=?, exam_type=?, duration_minutes=?,
        total_marks=?, pass_mark=?, instructions=?, shuffle_questions=?, show_result=?, status='draft'
        WHERE id=? AND lecturer_id=?
    ");
    $stmt->execute([
        sanitize($data['title']), $data['class_id'], $data['subject_id'],
        $data['exam_type'], $data['duration_minutes'], $data['total_marks'] ?? 100,
        $data['pass_mark'] ?? 50, sanitize($data['instructions'] ?? ''),
        $data['shuffle_questions'] ?? 0, $data['show_result'] ?? 1, $id, $lecturerId
    ]);

    // Update questions
    if (isset($data['questions'])) {
        $db->prepare("DELETE FROM exam_questions WHERE exam_id = ?")->execute([$id]);
        if (is_array($data['questions'])) {
            saveQuestions($db, $id, $data['questions']);
        }
    }

    jsonResponse(['success' => true, 'message' => 'Exam updated.']);
}

function submitExamForApproval($id) {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];

    $check = $db->prepare("SELECT id, status, (SELECT COUNT(*) FROM exam_questions WHERE exam_id = e.id) as qcount FROM exams e WHERE e.id = ? AND e.lecturer_id = ?");
    $check->execute([$id, $lecturerId]);
    $exam = $check->fetch();
    if (!$exam) jsonResponse(['success' => false, 'message' => 'Exam not found.'], 404);
    if ($exam['qcount'] == 0) jsonResponse(['success' => false, 'message' => 'Add at least one question before submitting.'], 400);
    if (!in_array($exam['status'], ['draft', 'rejected'])) {
        jsonResponse(['success' => false, 'message' => 'Only draft/rejected exams can be submitted.'], 400);
    }

    $db->prepare("UPDATE exams SET status = 'pending' WHERE id = ?")->execute([$id]);
    logActivity('lecturer', $lecturerId, 'submit_exam', 'Submitted exam #' . $id . ' for approval');
    jsonResponse(['success' => true, 'message' => 'Exam submitted for approval.']);
}

function deleteExam($id) {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    $check = $db->prepare("SELECT status FROM exams WHERE id = ? AND lecturer_id = ?");
    $check->execute([$id, $lecturerId]);
    $exam = $check->fetch();
    if (!$exam) jsonResponse(['success' => false, 'message' => 'Exam not found.'], 404);
    if (!in_array($exam['status'], ['draft', 'rejected'])) {
        jsonResponse(['success' => false, 'message' => 'Can only delete draft/rejected exams.'], 400);
    }
    $db->prepare("DELETE FROM exams WHERE id = ?")->execute([$id]);
    jsonResponse(['success' => true, 'message' => 'Exam deleted.']);
}

function getExamAttempts($examId) {
    $db = getDB();
    $lecturerId = $_SESSION['user_id'];
    $check = $db->prepare("SELECT id FROM exams WHERE id = ? AND lecturer_id = ?");
    $check->execute([$examId, $lecturerId]);
    if (!$check->fetch()) jsonResponse(['success' => false, 'message' => 'Exam not found.'], 404);

    $stmt = $db->prepare("
        SELECT ea.*, st.name as student_name, st.matric_no
        FROM exam_attempts ea JOIN students st ON st.id = ea.student_id
        WHERE ea.exam_id = ? ORDER BY ea.start_time DESC
    ");
    $stmt->execute([$examId]);
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}

function getAttemptDetail($attemptId) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT ea.*, st.name as student_name, st.matric_no, e.title as exam_title, e.lecturer_id
        FROM exam_attempts ea
        JOIN students st ON st.id = ea.student_id
        JOIN exams e ON e.id = ea.exam_id
        WHERE ea.id = ?
    ");
    $stmt->execute([$attemptId]);
    $attempt = $stmt->fetch();
    if (!$attempt || $attempt['lecturer_id'] != $_SESSION['user_id']) {
        jsonResponse(['success' => false, 'message' => 'Attempt not found.'], 404);
    }

    // Get questions with student answers
    $qStmt = $db->prepare("
        SELECT eq.*, ans.student_answer, ans.is_correct, ans.marks_awarded, ans.id as answer_id
        FROM exam_questions eq
        LEFT JOIN exam_answers ans ON ans.question_id = eq.id AND ans.attempt_id = ?
        WHERE eq.exam_id = ?
        ORDER BY eq.sort_order, eq.id
    ");
    $qStmt->execute([$attemptId, $attempt['exam_id']]);
    $attempt['questions'] = $qStmt->fetchAll();
    unset($attempt['lecturer_id']);

    jsonResponse(['success' => true, 'data' => $attempt]);
}

function gradeAnswer() {
    $db = getDB();
    $data = getPostData();
    $answerId = $data['answer_id'] ?? '';
    $marks = $data['marks_awarded'] ?? 0;
    $isCorrect = $data['is_correct'] ?? 0;

    $stmt = $db->prepare("UPDATE exam_answers SET marks_awarded = ?, is_correct = ?, graded_by = ? WHERE id = ?");
    $stmt->execute([$marks, $isCorrect, $_SESSION['user_id'], $answerId]);

    // Recalculate attempt total
    $ans = $db->prepare("SELECT attempt_id FROM exam_answers WHERE id = ?")->execute([$answerId]);
    $ansData = $db->prepare("SELECT attempt_id FROM exam_answers WHERE id = ?");
    $ansData->execute([$answerId]);
    $row = $ansData->fetch();
    if ($row) {
        $totalScore = $db->prepare("SELECT COALESCE(SUM(marks_awarded),0) as total FROM exam_answers WHERE attempt_id = ?");
        $totalScore->execute([$row['attempt_id']]);
        $total = $totalScore->fetch()['total'];

        $attemptData = $db->prepare("SELECT total_marks FROM exam_attempts WHERE id = ?");
        $attemptData->execute([$row['attempt_id']]);
        $att = $attemptData->fetch();
        $pct = $att['total_marks'] > 0 ? round(($total / $att['total_marks']) * 100, 2) : 0;

        $db->prepare("UPDATE exam_attempts SET score = ?, percentage = ? WHERE id = ?")
            ->execute([$total, $pct, $row['attempt_id']]);
    }

    jsonResponse(['success' => true, 'message' => 'Answer graded.']);
}

function saveQuestions($db, $examId, $questions) {
    $stmt = $db->prepare("
        INSERT INTO exam_questions (exam_id, question_text, question_type, option_a, option_b, option_c, option_d, correct_answer, marks, sort_order)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    foreach ($questions as $i => $q) {
        $stmt->execute([
            $examId, $q['question_text'], $q['question_type'] ?? 'mcq',
            $q['option_a'] ?? null, $q['option_b'] ?? null, $q['option_c'] ?? null, $q['option_d'] ?? null,
            $q['correct_answer'] ?? null, $q['marks'] ?? 1, $i + 1
        ]);
    }
}
