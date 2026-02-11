<?php
/**
 * Student Exams API
 */

function getAvailableExams() {
    $db = getDB();
    $studentId = $_SESSION['user_id'];
    $classId = $_SESSION['class_id'] ?? null;
    if (!$classId) { jsonResponse(['success' => true, 'data' => []]); return; }

    $stmt = $db->prepare("
        SELECT e.id, e.title, e.exam_type, e.duration_minutes, e.total_marks, e.instructions, e.status,
            s.name as subject_name, s.code as subject_code, l.name as lecturer_name,
            (SELECT COUNT(*) FROM exam_questions eq WHERE eq.exam_id = e.id) as question_count,
            (SELECT ea.id FROM exam_attempts ea WHERE ea.exam_id = e.id AND ea.student_id = ?) as attempt_id,
            (SELECT ea.status FROM exam_attempts ea WHERE ea.exam_id = e.id AND ea.student_id = ?) as attempt_status,
            (SELECT ea.score FROM exam_attempts ea WHERE ea.exam_id = e.id AND ea.student_id = ?) as attempt_score
        FROM exams e
        JOIN subjects s ON s.id = e.subject_id
        JOIN lecturers l ON l.id = e.lecturer_id
        WHERE e.class_id = ? AND e.status IN ('active','completed')
        ORDER BY e.status = 'active' DESC, e.created_at DESC
    ");
    $stmt->execute([$studentId, $studentId, $studentId, $classId]);
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}

function startExamAttempt($examId) {
    $db = getDB();
    $studentId = $_SESSION['user_id'];
    $classId = $_SESSION['class_id'] ?? null;
    $data = getPostData();
    $code = strtoupper(trim($data['code'] ?? ''));

    // Check for exam restriction
    $student = $db->prepare("SELECT status, restriction_type, restriction_reason FROM students WHERE id = ?");
    $student->execute([$studentId]);
    $stu = $student->fetch();
    if ($stu && $stu['status'] === 'restricted' && !empty($stu['restriction_type'])) {
        $restrictions = explode(',', $stu['restriction_type']);
        if (in_array('exams', $restrictions)) {
            jsonResponse(['success' => false, 'message' => 'You have been restricted from taking exams. Reason: ' . ($stu['restriction_reason'] ?? 'Contact admin.')], 403);
        }
    }

    // Verify exam is active and for student's class
    $exam = $db->prepare("SELECT * FROM exams WHERE id = ? AND class_id = ? AND status = 'active'");
    $exam->execute([$examId, $classId]);
    $examData = $exam->fetch();
    if (!$examData) jsonResponse(['success' => false, 'message' => 'This exam is not available.'], 400);

    // Verify exam code
    if (!$code) jsonResponse(['success' => false, 'message' => 'Please enter the exam code.'], 400);

    // Check if it's a continue key for existing attempt
    $existingAttempt = $db->prepare("SELECT id, status, continue_key FROM exam_attempts WHERE exam_id = ? AND student_id = ?");
    $existingAttempt->execute([$examId, $studentId]);
    $attempt = $existingAttempt->fetch();

    if ($attempt) {
        if ($attempt['status'] === 'in_progress') {
            jsonResponse(['success' => true, 'message' => 'Resuming exam.', 'attempt_id' => $attempt['id']]);
        }
        // Check if continue key matches
        if ($attempt['continue_key'] && strtoupper($attempt['continue_key']) === $code) {
            // Reset and continue - invalidate old key, generate new one for next time
            $newKey = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
            $db->prepare("UPDATE exam_attempts SET status = 'in_progress', end_time = NULL, continue_key = ? WHERE id = ?")->execute([$newKey, $attempt['id']]);
            logActivity('student', $studentId, 'continue_exam', 'Resumed exam with continue key');
            jsonResponse(['success' => true, 'message' => 'Exam resumed.', 'attempt_id' => $attempt['id']]);
        }
        // Check if it's the exam start code for already attempted exam
        if (strtoupper($examData['exam_code']) === $code) {
            jsonResponse(['success' => false, 'message' => 'You have already taken this exam.'], 400);
        }
        jsonResponse(['success' => false, 'message' => 'Invalid code.'], 400);
    }

    // New attempt - verify exam start code
    if (strtoupper($examData['exam_code'] ?? '') !== $code) {
        jsonResponse(['success' => false, 'message' => 'Invalid exam code.'], 400);
    }

    // Create new attempt
    $stmt = $db->prepare("INSERT INTO exam_attempts (exam_id, student_id, start_time, total_marks, status, ip_address) VALUES (?, ?, NOW(), ?, 'in_progress', ?)");
    $stmt->execute([$examId, $studentId, $examData['total_marks'], $_SERVER['REMOTE_ADDR'] ?? '']);
    $attemptId = $db->lastInsertId();

    logActivity('student', $studentId, 'start_exam', 'Started exam: ' . $examData['title']);
    jsonResponse(['success' => true, 'message' => 'Exam started.', 'attempt_id' => $attemptId]);
}

function getExamQuestions($examId) {
    $db = getDB();
    $studentId = $_SESSION['user_id'];

    // Verify student has an active attempt
    $attempt = $db->prepare("SELECT ea.*, e.duration_minutes, e.shuffle_questions, e.title
        FROM exam_attempts ea JOIN exams e ON e.id = ea.exam_id
        WHERE ea.exam_id = ? AND ea.student_id = ? AND ea.status = 'in_progress'");
    $attempt->execute([$examId, $studentId]);
    $attemptData = $attempt->fetch();
    if (!$attemptData) jsonResponse(['success' => false, 'message' => 'No active attempt found.'], 400);

    // Check time
    $startTime = strtotime($attemptData['start_time']);
    $timeLeft = ($attemptData['duration_minutes'] * 60) - (time() - $startTime);
    if ($timeLeft <= 0) {
        // Auto-submit
        autoSubmitAttempt($db, $attemptData['id'], $examId);
        jsonResponse(['success' => false, 'message' => 'Time is up! Exam auto-submitted.'], 400);
    }

    // Get questions (without correct answers for objective types)
    $questions = $db->prepare("SELECT id, question_text, question_type, option_a, option_b, option_c, option_d, marks, sort_order FROM exam_questions WHERE exam_id = ? ORDER BY sort_order, id");
    $questions->execute([$examId]);
    $qList = $questions->fetchAll();

    if ($attemptData['shuffle_questions']) shuffle($qList);

    // Get existing answers
    $answers = $db->prepare("SELECT question_id, student_answer FROM exam_answers WHERE attempt_id = ?");
    $answers->execute([$attemptData['id']]);
    $existingAnswers = [];
    foreach ($answers->fetchAll() as $a) $existingAnswers[$a['question_id']] = $a['student_answer'];

    jsonResponse(['success' => true, 'data' => [
        'attempt_id' => $attemptData['id'],
        'exam_title' => $attemptData['title'],
        'time_left' => max(0, $timeLeft),
        'questions' => $qList,
        'answers' => $existingAnswers
    ]]);
}

function saveAnswer() {
    $db = getDB();
    $studentId = $_SESSION['user_id'];
    $data = getPostData();

    $attemptId = $data['attempt_id'] ?? '';
    $questionId = $data['question_id'] ?? '';
    $answer = $data['answer'] ?? '';

    // Verify attempt
    $check = $db->prepare("SELECT id FROM exam_attempts WHERE id = ? AND student_id = ? AND status = 'in_progress'");
    $check->execute([$attemptId, $studentId]);
    if (!$check->fetch()) jsonResponse(['success' => false, 'message' => 'Invalid attempt.'], 400);

    $stmt = $db->prepare("
        INSERT INTO exam_answers (attempt_id, question_id, student_answer)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE student_answer = VALUES(student_answer)
    ");
    $stmt->execute([$attemptId, $questionId, $answer]);
    jsonResponse(['success' => true]);
}

function submitExamAttempt($examId) {
    $db = getDB();
    $studentId = $_SESSION['user_id'];

    $attempt = $db->prepare("SELECT id FROM exam_attempts WHERE exam_id = ? AND student_id = ? AND status = 'in_progress'");
    $attempt->execute([$examId, $studentId]);
    $attemptData = $attempt->fetch();
    if (!$attemptData) jsonResponse(['success' => false, 'message' => 'No active attempt.'], 400);

    autoSubmitAttempt($db, $attemptData['id'], $examId);

    // Auto-insert exam score into scores table
    try {
        $scoreRow = $db->prepare("SELECT score FROM exam_attempts WHERE id = ?");
        $scoreRow->execute([$attemptData['id']]);
        $totalScore = $scoreRow->fetch()['score'] ?? 0;

        $examInfo = $db->prepare("SELECT e.subject_id, e.class_id, e.lecturer_id FROM exams e WHERE e.id = ?");
        $examInfo->execute([$examId]);
        $examData = $examInfo->fetch();

        if ($examData) {
            $semester = 1;
            // Check if score record exists
            $existingScore = $db->prepare("SELECT id, exam_score, ca_score FROM scores WHERE student_id = ? AND subject_id = ? AND class_id = ? AND semester = ?");
            $existingScore->execute([$_SESSION['user_id'], $examData['subject_id'], $examData['class_id'], $semester]);
            $scoreRow = $existingScore->fetch();

            // Calculate percentage-based exam score (out of 60 which is typical exam portion)
            $totalPossible = $db->prepare("SELECT SUM(marks) as total FROM exam_questions WHERE exam_id = ?");
            $totalPossible->execute([$examId]);
            $maxMarks = $totalPossible->fetch()['total'] ?: 100;
            $examScorePercent = round(($totalScore / $maxMarks) * 60, 2); // 60% for exam

            if ($scoreRow) {
                $newTotal = ($scoreRow['ca_score'] ?? 0) + $examScorePercent;
                $grade = ($newTotal >= 70) ? 'A' : (($newTotal >= 60) ? 'B' : (($newTotal >= 50) ? 'C' : (($newTotal >= 45) ? 'D' : (($newTotal >= 40) ? 'E' : 'F'))));
                $remark = $newTotal >= 50 ? 'Pass' : 'Fail';
                $db->prepare("UPDATE scores SET exam_score = ?, total_score = ca_score + ?, grade = ?, remark = ? WHERE id = ?")->execute([$examScorePercent, $examScorePercent, $grade, $remark, $scoreRow['id']]);
            } else {
                $grade = ($examScorePercent >= 70) ? 'A' : (($examScorePercent >= 60) ? 'B' : (($examScorePercent >= 50) ? 'C' : (($examScorePercent >= 45) ? 'D' : (($examScorePercent >= 40) ? 'E' : 'F'))));
                $remark = $examScorePercent >= 50 ? 'Pass' : 'Fail';
                $db->prepare("INSERT INTO scores (student_id, subject_id, class_id, lecturer_id, ca_score, exam_score, total_score, grade, remark, semester) VALUES (?, ?, ?, ?, 0, ?, ?, ?, ?, ?)")->execute([
                    $_SESSION['user_id'], $examData['subject_id'], $examData['class_id'], $examData['lecturer_id'], $examScorePercent, $examScorePercent, $grade, $remark, $semester
                ]);
            }
        }
    } catch (Exception $e) {
        // Don't fail the exam submission if score insert fails
        error_log("Auto-score insert failed: " . $e->getMessage());
    }

    logActivity('student', $studentId, 'submit_exam', 'Submitted exam #' . $examId);
    jsonResponse(['success' => true, 'message' => 'Exam submitted successfully!']);
}

function autoSubmitAttempt($db, $attemptId, $examId) {
    // Auto-grade objective questions
    $questions = $db->prepare("SELECT id, question_type, correct_answer, marks FROM exam_questions WHERE exam_id = ?");
    $questions->execute([$examId]);

    $totalScore = 0;
    $totalMarks = 0;
    foreach ($questions->fetchAll() as $q) {
        $totalMarks += $q['marks'];
        $ans = $db->prepare("SELECT id, student_answer FROM exam_answers WHERE attempt_id = ? AND question_id = ?");
        $ans->execute([$attemptId, $q['id']]);
        $answer = $ans->fetch();

        if ($answer && in_array($q['question_type'], ['mcq', 'true_false', 'fill_in'])) {
            $isCorrect = strtolower(trim($answer['student_answer'])) === strtolower(trim($q['correct_answer']));
            $awarded = $isCorrect ? $q['marks'] : 0;
            $totalScore += $awarded;
            $db->prepare("UPDATE exam_answers SET is_correct = ?, marks_awarded = ? WHERE id = ?")->execute([$isCorrect ? 1 : 0, $awarded, $answer['id']]);
        }
    }

    $percentage = $totalMarks > 0 ? round(($totalScore / $totalMarks) * 100, 2) : 0;
    $db->prepare("UPDATE exam_attempts SET status = 'submitted', end_time = NOW(), score = ?, total_marks = ?, percentage = ? WHERE id = ?")
        ->execute([$totalScore, $totalMarks, $percentage, $attemptId]);
}
