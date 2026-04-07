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
        $hasEmailVerification = (bool) $db->query("SHOW TABLES LIKE 'email_verification_codes'")->fetch();
        $orderBy = $hasCreatedAt ? 'ORDER BY s.created_at DESC' : 'ORDER BY s.id DESC';
        $verificationSelect = $hasEmailVerification
            ? ", COALESCE(ev.email_verified, 0) as email_verified"
            : ", 1 as email_verified";
        $verificationJoin = $hasEmailVerification
            ? "LEFT JOIN (
                SELECT student_id, MAX(CASE WHEN used_at IS NOT NULL THEN 1 ELSE 0 END) as email_verified
                FROM email_verification_codes
                GROUP BY student_id
            ) ev ON ev.student_id = s.id"
            : "";

        $stmt = $db->prepare("
            SELECT s.id, s.name, s.email, s.phone, " . ($hasCreatedAt ? "s.created_at" : "NULL as created_at") . ", c.name as class_name {$verificationSelect}
            FROM students s
            LEFT JOIN classes c ON c.id = s.class_id
            {$verificationJoin}
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

        $data = getPostData();
        $classId = !empty($data['class_id']) ? (int) $data['class_id'] : null;
        if (!$classId) {
            jsonResponse(['success' => false, 'message' => 'Please select a class for the student.'], 400);
        }

        $classStmt = $db->prepare("SELECT id, name FROM classes WHERE id = ? AND status = 'active'");
        $classStmt->execute([$classId]);
        $class = $classStmt->fetch();
        if (!$class) {
            jsonResponse(['success' => false, 'message' => 'Selected class not found or inactive.'], 400);
        }

        $stmt = $db->prepare("SELECT id, name, email, approval_status FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $s = $stmt->fetch();
        if (!$s) jsonResponse(['success' => false, 'message' => 'Student not found.'], 404);
        if ($s['approval_status'] !== 'pending') jsonResponse(['success' => false, 'message' => 'Student is not pending approval.'], 400);

        $lockName = null;
        try {
            $lockName = acquireMatricNoLock($db);
            $db->beginTransaction();
            $matricNo = generateNextMatricNo($db);
            $db->prepare("UPDATE students SET approval_status = 'approved', matric_no = ?, class_id = ?, approved_at = NOW(), approved_by = ? WHERE id = ?")
                ->execute([$matricNo, $classId, $_SESSION['user_id'], $id]);
            $db->commit();
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        } finally {
            releaseMatricNoLock($db, $lockName);
        }

        $courses = $db->prepare("
            SELECT sub.name, sub.code FROM class_subjects cs
            JOIN subjects sub ON sub.id = cs.subject_id
            WHERE cs.class_id = ? AND sub.status = 'active'
            ORDER BY sub.name
        ");
        $courses->execute([$classId]);
        $courseList = $courses->fetchAll(PDO::FETCH_ASSOC);

        try {
            require_once __DIR__ . '/../../helpers/mail.php';
            require_once __DIR__ . '/../../helpers/email_templates.php';
            $body = getStudentApprovedEmailTemplate([
                'name' => $s['name'],
                'matric_no' => $matricNo,
                'class_name' => $class['name'],
                'courses' => $courseList,
                'login_url' => APP_URL . '/login/student',
            ]);
            $to = $s['email'];
            $subject = 'AGIT Academy – Your Application Has Been Approved!';
            register_shutdown_function(function () use ($to, $subject, $body) {
                if (function_exists('fastcgi_finish_request')) {
                    @fastcgi_finish_request();
                }
                sendSmtpEmail($to, $subject, $body, 'AGIT Academy');
            });
        } catch (Throwable $e) {
            if (function_exists('logEmailError')) {
                logEmailError('approve', $s['email'] ?? '', $e->getMessage());
            }
        }

        logActivity('admin', $_SESSION['user_id'], 'approve_student', "Approved student #$id - $matricNo - Class: {$class['name']}");
        jsonResponse(['success' => true, 'message' => 'Student approved. Matric: ' . $matricNo . ' | Class: ' . $class['name']]);
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
            $to = $s['email'];
            $subject = 'AGIT Academy – Application Update';
            register_shutdown_function(function () use ($to, $subject, $body) {
                if (function_exists('fastcgi_finish_request')) {
                    @fastcgi_finish_request();
                }
                sendSmtpEmail($to, $subject, $body, 'AGIT Academy');
            });
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
