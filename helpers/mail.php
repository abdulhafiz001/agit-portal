<?php
/**
 * Email Helper - SMTP via PHPMailer
 * Requires: composer require phpmailer/phpmailer
 * Run: composer install
 */

/**
 * Send email via SMTP (port 465)
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $body HTML body
 * @param string $fromName Sender name
 * @return array ['success' => bool, 'message' => string]
 */
function sendSmtpEmail($to, $subject, $body, $fromName = 'AGIT Portal') {
    $vendorPath = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($vendorPath)) {
        return ['success' => false, 'message' => 'PHPMailer not installed. Run: composer install'];
    }

    require_once $vendorPath;

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = getSetting('smtp_host', 'smtp.gmail.com');
        $mail->SMTPAuth   = true;
        $mail->Username   = getSetting('smtp_username', '');
        $mail->Password   = getSetting('smtp_password', '');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = (int) getSetting('smtp_port', '465');
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(getSetting('smtp_username', 'noreply@agitsolutionsng.com'), $fromName);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        return ['success' => true, 'message' => 'Email sent successfully.'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Mail Error: ' . $mail->ErrorInfo];
    }
}
