<?php
/**
 * Student Approval/Decline - Token-based (from admin email links)
 * Public routes - token is the auth
 */

require_once __DIR__ . '/../helpers/mail.php';
require_once __DIR__ . '/../helpers/email_templates.php';

function generateNextMatricNo() {
    $db = getDB();
    $year = date('Y');
    $stmt = $db->prepare("SELECT matric_no FROM students WHERE matric_no LIKE ? AND approval_status = 'approved' ORDER BY id DESC LIMIT 1");
    $stmt->execute(["AGIT/{$year}/%"]);
    $last = $stmt->fetchColumn();
    if (!$last) {
        return "AGIT/{$year}/0001";
    }
    if (preg_match('/AGIT\/\d+\/(\d+)/', $last, $m)) {
        $seq = (int) $m[1] + 1;
        return "AGIT/{$year}/" . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
    return "AGIT/{$year}/0001";
}

function processApproveStudent($token) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT t.id as token_id, t.student_id, t.action 
        FROM student_approval_tokens t 
        JOIN students s ON s.id = t.student_id 
        WHERE t.token = ? AND t.action = 'approve' AND t.used_at IS NULL AND t.expires_at > NOW() AND s.approval_status = 'pending'
    ");
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    if (!$row) {
        return ['success' => false, 'message' => 'Invalid or expired link. This approval link may have already been used.'];
    }

    $matricNo = generateNextMatricNo();
    $db->beginTransaction();
    try {
        $db->prepare("UPDATE students SET approval_status = 'approved', matric_no = ?, approved_at = NOW() WHERE id = ?")
            ->execute([$matricNo, $row['student_id']]);
        $db->prepare("UPDATE student_approval_tokens SET used_at = NOW() WHERE id = ?")
            ->execute([$row['token_id']]);
        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'message' => 'An error occurred. Please try again.'];
    }

    $stmt = $db->prepare("SELECT name, email FROM students WHERE id = ?");
    $stmt->execute([$row['student_id']]);
    $student = $stmt->fetch();
    $loginUrl = APP_URL . '/login/student';
    $body = getStudentApprovedEmailTemplate([
        'name' => $student['name'],
        'matric_no' => $matricNo,
        'login_url' => $loginUrl,
    ]);
    sendSmtpEmail($student['email'], 'AGIT Academy – Your Application Has Been Approved!', $body, 'AGIT Academy');

    return ['success' => true, 'message' => 'Student approved successfully. Matric number ' . $matricNo . ' has been assigned and the student has been notified.'];
}

function processDeclineStudent($token, $reason) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT t.id as token_id, t.student_id 
        FROM student_approval_tokens t 
        JOIN students s ON s.id = t.student_id 
        WHERE t.token = ? AND t.action = 'decline' AND t.used_at IS NULL AND t.expires_at > NOW() AND s.approval_status = 'pending'
    ");
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    if (!$row) {
        return ['success' => false, 'message' => 'Invalid or expired link. This decline link may have already been used.'];
    }

    $reason = trim($reason ?? '');
    if (strlen($reason) < 10) {
        return ['success' => false, 'message' => 'Please provide a reason for rejection (at least 10 characters).'];
    }

    $stmt = $db->prepare("SELECT name, email FROM students WHERE id = ?");
    $stmt->execute([$row['student_id']]);
    $student = $stmt->fetch();

    $db->beginTransaction();
    try {
        $db->prepare("UPDATE students SET approval_status = 'rejected', rejection_reason = ? WHERE id = ?")
            ->execute([$reason, $row['student_id']]);
        $db->prepare("UPDATE student_approval_tokens SET used_at = NOW() WHERE id = ?")
            ->execute([$row['token_id']]);
        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'message' => 'An error occurred. Please try again.'];
    }

    $body = getStudentRejectedEmailTemplate(['name' => $student['name'], 'reason' => $reason]);
    sendSmtpEmail($student['email'], 'AGIT Academy – Application Update', $body, 'AGIT Academy');

    return ['success' => true, 'message' => 'Application declined. The student has been notified.'];
}
