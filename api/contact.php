<?php
/**
 * Contact/Enrollment Form API - Public
 * Sends email to contact_email from settings (default: admin@agitacademy.com)
 */

require_once __DIR__ . '/../helpers/mail.php';
require_once __DIR__ . '/../helpers/middleware.php';

function submitContact() {
    $data = getPostData();

    // Basic rate limiting: 5 submissions per 15 minutes per IP
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if (!rateLimit('contact_' . $ip, 5, 15)) {
        jsonResponse(['success' => false, 'message' => 'Too many submissions. Please try again later.'], 429);
    }

    $name = sanitize($data['name'] ?? '');
    $email = sanitize($data['email'] ?? '');
    $subject = sanitize($data['subject'] ?? 'AGIT Academy Enrollment Inquiry');
    $message = sanitize($data['message'] ?? '');
    $phone = sanitize($data['phone'] ?? '');

    if (!$name || !$email || !$message) {
        jsonResponse(['success' => false, 'message' => 'Name, email and message are required.'], 400);
    }
    if (!isValidEmail($email)) {
        jsonResponse(['success' => false, 'message' => 'Invalid email address.'], 400);
    }

    $to = getSetting('contact_email', 'admin@agitacademy.com');
    if (!$to || !isValidEmail($to)) {
        $to = 'admin@agitacademy.com';
    }

    // Build message body: include phone if provided
    $messageBody = $message;
    if ($phone) {
        $messageBody = "Phone: {$phone}\n\n" . $messageBody;
    }

    $body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 0; }
        .container { max-width: 560px; margin: 0 auto; padding: 24px; }
        .header { background: linear-gradient(135deg, #4a4de5 0%, #5b6cf1 100%); color: white; padding: 24px; border-radius: 12px 12px 0 0; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 600; }
        .content { background: #f8fafc; padding: 24px; border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 12px 12px; }
        .field { margin-bottom: 16px; }
        .label { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .value { font-size: 15px; color: #1e293b; }
        .message-box { background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; white-space: pre-wrap; }
        .footer { margin-top: 20px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>AGIT Academy – New Enrollment Inquiry</h1>
        </div>
        <div class="content">
            <div class="field">
                <div class="label">Name</div>
                <div class="value">' . htmlspecialchars($name) . '</div>
            </div>
            <div class="field">
                <div class="label">Email</div>
                <div class="value"><a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a></div>
            </div>
            ' . ($phone ? '<div class="field"><div class="label">Phone</div><div class="value">' . htmlspecialchars($phone) . '</div></div>' : '') . '
            <div class="field">
                <div class="label">Program / Subject</div>
                <div class="value">' . htmlspecialchars($subject) . '</div>
            </div>
            <div class="field">
                <div class="label">Message</div>
                <div class="message-box">' . nl2br(htmlspecialchars($messageBody)) . '</div>
            </div>
            <div class="footer">
                Submitted at ' . date('F j, Y \a\t g:i A') . ' from ' . htmlspecialchars($ip) . '
            </div>
        </div>
    </div>
</body>
</html>';

    $result = sendSmtpEmail($to, 'AGIT Academy: ' . $subject, $body, 'AGIT Academy');

    if ($result['success']) {
        try {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO contact_submissions (name, email, subject, message, ip_address) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $messageBody, $ip]);
        } catch (Exception $e) {}
        jsonResponse(['success' => true, 'message' => 'Thank you! Your inquiry has been sent successfully. We will get back to you soon.']);
    } else {
        jsonResponse(['success' => false, 'message' => $result['message']], 500);
    }
}
