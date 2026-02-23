<?php
/**
 * Contact/Enquiry Form API - Public
 * Sends email to admin@agitsolutionsng.com
 */

require_once __DIR__ . '/../helpers/mail.php';

function submitContact() {
    $data = getPostData();
    
    $name = sanitize($data['name'] ?? '');
    $email = sanitize($data['email'] ?? '');
    $subject = sanitize($data['subject'] ?? 'AGIT Solutions Enquiry');
    $message = sanitize($data['message'] ?? '');

    if (!$name || !$email || !$message) {
        jsonResponse(['success' => false, 'message' => 'Name, email and message are required.'], 400);
    }
    if (!isValidEmail($email)) {
        jsonResponse(['success' => false, 'message' => 'Invalid email address.'], 400);
    }

    $to = 'admin@agitsolutionsng.com';
    $body = "
    <h3>New Enquiry from AGIT Portal</h3>
    <p><strong>Name:</strong> {$name}</p>
    <p><strong>Email:</strong> {$email}</p>
    <p><strong>Subject:</strong> {$subject}</p>
    <p><strong>Message:</strong></p>
    <p>" . nl2br($message) . "</p>
    <hr>
    <p><small>Submitted at " . date('Y-m-d H:i:s') . " from " . ($_SERVER['REMOTE_ADDR'] ?? '') . "</small></p>
    ";

    $result = sendSmtpEmail($to, 'AGIT Solutions Enquiry: ' . $subject, $body, 'AGIT Portal');

    if ($result['success']) {
        // Optionally log to database
        try {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO contact_submissions (name, email, subject, message, ip_address) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message, $_SERVER['REMOTE_ADDR'] ?? null]);
        } catch (Exception $e) {}
        jsonResponse(['success' => true, 'message' => 'Thank you! Your enquiry has been sent successfully.']);
    } else {
        jsonResponse(['success' => false, 'message' => $result['message']], 500);
    }
}
