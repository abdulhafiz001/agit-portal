<?php
/**
 * Email Helper - SMTP via PHPMailer
 * Requires: composer require phpmailer/phpmailer
 * Run: composer install
 */

/**
 * Log email error for debugging
 */
function logEmailError($context, $to, $error) {
    $logDir = defined('BASE_PATH') ? BASE_PATH . '/storage/logs' : __DIR__ . '/../storage/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $logFile = $logDir . '/email.log';
    $line = date('Y-m-d H:i:s') . " [$context] To: $to | Error: " . (is_string($error) ? $error : json_encode($error)) . "\n";
    @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}

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
        $msg = 'PHPMailer not installed. Run: composer install';
        logEmailError('send', $to, $msg);
        return ['success' => false, 'message' => $msg];
    }

    $username = trim(getSetting('smtp_username', ''));
    $password = getSetting('smtp_password', '');
    if (empty($username) || empty($password)) {
        $msg = 'SMTP not configured. Go to Admin → Settings and configure SMTP (host, username, password). For Gmail, use an App Password.';
        logEmailError('send', $to, $msg);
        return ['success' => false, 'message' => $msg];
    }

    require_once $vendorPath;

    $host = trim(getSetting('smtp_host', 'smtp.gmail.com'));
    $port = (int) getSetting('smtp_port', '465');
    $encryption = getSetting('smtp_encryption', 'ssl');
    $timeout = (int) getSetting('smtp_timeout', '12');
    $replyTo = getSetting('contact_email', $username);
    if (!$replyTo || !isValidEmail($replyTo)) {
        $replyTo = $username;
    }

    $altBody = html_entity_decode(
        trim(
            preg_replace('/[ \t]+/', ' ',
                preg_replace('/\s*\n\s*/', "\n",
                    strip_tags(
                        preg_replace('/<br\s*\/?>/i', "\n",
                            preg_replace('/<\/p>\s*/i', "\n\n", $body)
                        )
                    )
                )
            )
        ),
        ENT_QUOTES,
        'UTF-8'
    );

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $username;
        $mail->Password   = $password;
        $mail->CharSet    = 'UTF-8';
        $mail->Timeout    = $timeout > 0 ? $timeout : 12;
        $mail->XMailer    = 'AGIT Academy Mailer';
        $mail->addCustomHeader('X-Auto-Response-Suppress', 'All');

        if ($port === 587 || $encryption === 'tls') {
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $port ?: 587;
        } else {
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $port ?: 465;
        }

        $mail->setFrom($username, $fromName);
        $mail->addReplyTo($replyTo, 'AGIT Academy');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody;

        $mail->send();
        return ['success' => true, 'message' => 'Email sent successfully.'];
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        $errMsg = $mail->ErrorInfo ?? $e->getMessage();
        logEmailError('send', $to, $errMsg);
        $hint = '';
        if (stripos($errMsg, 'authenticate') !== false || stripos($errMsg, 'authentication') !== false) {
            $hint = ' For Gmail: use an App Password (not your regular password). Enable 2-Step Verification, then create one at myaccount.google.com/apppasswords';
        }
        return ['success' => false, 'message' => 'Mail Error: ' . $errMsg . $hint];
    }
}
