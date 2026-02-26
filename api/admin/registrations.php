<?php
/**
 * Admin Student Registrations API
 * Pending approvals, approve/decline, stats
 */

function getRegistrationsStats() {
    try {
        $db = getDB();
        $hasApproval = (bool) $db->query("SHOW COLUMNS FROM students LIKE 'approval_status'")->fetch();

        if (!$hasApproval) {
            jsonResponse([
                'success' => true,
                'data' => [
                    'pending' => 0,
                    'approved' => (int) $db->query("SELECT COUNT(*) FROM students")->fetchColumn(),
                    'rejected' => 0,
                    'by_class' => [],
                ]
            ]);
            return;
        }

        $pending = (int) $db->query("SELECT COUNT(*) FROM students WHERE approval_status = 'pending'")->fetchColumn();
        $approved = (int) $db->query("SELECT COUNT(*) FROM students WHERE approval_status = 'approved'")->fetchColumn();
        $rejected = (int) $db->query("SELECT COUNT(*) FROM students WHERE approval_status = 'rejected'")->fetchColumn();

        $byClass = [];
        try {
            $byClass = $db->query("
                SELECT c.name as class_name, 
                       SUM(CASE WHEN s.approval_status = 'pending' THEN 1 ELSE 0 END) as pending,
                       SUM(CASE WHEN s.approval_status = 'approved' THEN 1 ELSE 0 END) as approved,
                       SUM(CASE WHEN s.approval_status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                       COUNT(*) as total
                FROM classes c
                LEFT JOIN students s ON s.class_id = c.id
                WHERE c.status = 'active'
                GROUP BY c.id, c.name
                ORDER BY c.name
            ")->fetchAll();
        } catch (Throwable $e) {
            // by_class is optional; continue without it
        }

        jsonResponse([
            'success' => true,
            'data' => [
                'pending' => $pending,
                'approved' => $approved,
                'rejected' => $rejected,
                'by_class' => $byClass,
            ]
        ]);
    } catch (Throwable $e) {
        jsonResponse(['success' => false, 'message' => 'Failed to load stats: ' . $e->getMessage()], 500);
    }
}

function getPendingRegistrations() {
    try {
        $db = getDB();
        $hasApproval = (bool) $db->query("SHOW COLUMNS FROM students LIKE 'approval_status'")->fetch();
        if (!$hasApproval) {
            jsonResponse(['success' => true, 'data' => []]);
            return;
        }

        $hasCreatedAt = (bool) $db->query("SHOW COLUMNS FROM students LIKE 'created_at'")->fetch();
        $orderBy = $hasCreatedAt ? 'ORDER BY s.created_at DESC' : 'ORDER BY s.id DESC';

        $stmt = $db->prepare("
            SELECT s.id, s.name, s.email, s.phone, " . ($hasCreatedAt ? "s.created_at" : "NULL as created_at") . ", c.name as class_name
            FROM students s
            LEFT JOIN classes c ON c.id = s.class_id
            WHERE s.approval_status = 'pending'
            $orderBy
        ");
        $stmt->execute();
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        jsonResponse(['success' => true, 'data' => $list]);
    } catch (Throwable $e) {
        jsonResponse(['success' => false, 'message' => 'Failed to load registrations: ' . $e->getMessage()], 500);
    }
}

function approveRegistration($id) {
    try {
        $db = getDB();
        require_once __DIR__ . '/../student_approval.php';

        $stmt = $db->prepare("SELECT id, name, email, approval_status FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $s = $stmt->fetch();
        if (!$s) jsonResponse(['success' => false, 'message' => 'Student not found.'], 404);
        if ($s['approval_status'] !== 'pending') jsonResponse(['success' => false, 'message' => 'Student is not pending approval.'], 400);

        $matricNo = generateNextMatricNo();
        $db->prepare("UPDATE students SET approval_status = 'approved', matric_no = ?, approved_at = NOW(), approved_by = ? WHERE id = ?")
            ->execute([$matricNo, $_SESSION['user_id'], $id]);

        try {
            require_once __DIR__ . '/../../helpers/mail.php';
            require_once __DIR__ . '/../../helpers/email_templates.php';
            $body = getStudentApprovedEmailTemplate([
                'name' => $s['name'],
                'matric_no' => $matricNo,
                'login_url' => APP_URL . '/login/student',
            ]);
            sendSmtpEmail($s['email'], 'AGIT Academy – Your Application Has Been Approved!', $body, 'AGIT Academy');
        } catch (Throwable $e) {
            if (function_exists('logEmailError')) {
                logEmailError('approve', $s['email'] ?? '', $e->getMessage());
            }
        }

        logActivity('admin', $_SESSION['user_id'], 'approve_student', "Approved student #$id - $matricNo");
        jsonResponse(['success' => true, 'message' => 'Student approved. Matric: ' . $matricNo]);
    } catch (Throwable $e) {
        jsonResponse(['success' => false, 'message' => 'Approval failed: ' . $e->getMessage()], 500);
    }
}

function declineRegistration($id) {
    try {
        $db = getDB();
        $data = getPostData();
        $reason = trim($data['reason'] ?? '');
        if (strlen($reason) < 10) jsonResponse(['success' => false, 'message' => 'Reason required (min 10 characters).'], 400);

        $stmt = $db->prepare("SELECT id, name, email, approval_status FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $s = $stmt->fetch();
        if (!$s) jsonResponse(['success' => false, 'message' => 'Student not found.'], 404);
        if ($s['approval_status'] !== 'pending') jsonResponse(['success' => false, 'message' => 'Student is not pending approval.'], 400);

        $db->prepare("UPDATE students SET approval_status = 'rejected', rejection_reason = ? WHERE id = ?")
            ->execute([$reason, $id]);

        try {
            require_once __DIR__ . '/../../helpers/mail.php';
            require_once __DIR__ . '/../../helpers/email_templates.php';
            $body = getStudentRejectedEmailTemplate(['name' => $s['name'], 'reason' => $reason]);
            sendSmtpEmail($s['email'], 'AGIT Academy – Application Update', $body, 'AGIT Academy');
        } catch (Throwable $e) {
            if (function_exists('logEmailError')) {
                logEmailError('decline', $s['email'] ?? '', $e->getMessage());
            }
        }

        logActivity('admin', $_SESSION['user_id'], 'decline_student', "Declined student #$id");
        jsonResponse(['success' => true, 'message' => 'Application declined. Student has been notified.']);
    } catch (Throwable $e) {
        jsonResponse(['success' => false, 'message' => 'Decline failed: ' . $e->getMessage()], 500);
    }
}
