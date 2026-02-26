<?php
/**
 * Email HTML Templates - AGIT Academy
 * Modern, professional email designs
 */

function getAdminNewStudentEmailTemplate($data) {
    $name = htmlspecialchars($data['name'] ?? '');
    $email = htmlspecialchars($data['email'] ?? '');
    $phone = htmlspecialchars($data['phone'] ?? '');
    $class = htmlspecialchars($data['class'] ?? '');
    $gender = htmlspecialchars($data['gender'] ?? 'N/A');
    $approveUrl = $data['approveUrl'] ?? '#';
    $declineUrl = $data['declineUrl'] ?? '#';

    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 0; background: #f1f5f9; }
        .container { max-width: 560px; margin: 0 auto; padding: 24px; }
        .card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -2px rgba(0,0,0,.1); }
        .header { background: linear-gradient(135deg, #4a4de5 0%, #5b6cf1 100%); color: white; padding: 28px 24px; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 600; }
        .header p { margin: 8px 0 0; opacity: 0.9; font-size: 14px; }
        .content { padding: 28px 24px; }
        .field { margin-bottom: 18px; }
        .label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .value { font-size: 15px; color: #1e293b; }
        .btn-wrap { margin-top: 28px; display: flex; gap: 12px; flex-wrap: wrap; }
        .btn { display: inline-block; padding: 14px 28px; border-radius: 10px; font-weight: 600; font-size: 14px; text-decoration: none; text-align: center; }
        .btn-approve { background: #10b981; color: white !important; }
        .btn-decline { background: #ef4444; color: white !important; }
        .footer { padding: 20px 24px; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>New Student Registration</h1>
                <p>A new student has registered and is awaiting your approval.</p>
            </div>
            <div class="content">
                <div class="field">
                    <div class="label">Name</div>
                    <div class="value">{$name}</div>
                </div>
                <div class="field">
                    <div class="label">Email</div>
                    <div class="value"><a href="mailto:{$email}" style="color:#4a4de5">{$email}</a></div>
                </div>
                <div class="field">
                    <div class="label">Phone</div>
                    <div class="value">{$phone}</div>
                </div>
                <div class="field">
                    <div class="label">Class</div>
                    <div class="value">{$class}</div>
                </div>
                <div class="field">
                    <div class="label">Gender</div>
                    <div class="value">{$gender}</div>
                </div>
                <div class="btn-wrap">
                    <a href="{$approveUrl}" class="btn btn-approve">Accept & Generate Matric</a>
                    <a href="{$declineUrl}" class="btn btn-decline">Decline Application</a>
                </div>
            </div>
            <div class="footer">
                Links expire in 7 days. You can also manage registrations from the Admin Panel.
            </div>
        </div>
    </div>
</body>
</html>
HTML;
}

function getStudentApprovedEmailTemplate($data) {
    $name = htmlspecialchars($data['name'] ?? '');
    $matricNo = htmlspecialchars($data['matric_no'] ?? '');
    $loginUrl = $data['login_url'] ?? '#';

    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 0; background: #f1f5f9; }
        .container { max-width: 560px; margin: 0 auto; padding: 24px; }
        .card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,.1); }
        .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 28px 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 600; }
        .header p { margin: 8px 0 0; opacity: 0.95; font-size: 15px; }
        .content { padding: 28px 24px; }
        .matric-box { background: #f0fdf4; border: 2px dashed #10b981; border-radius: 12px; padding: 20px; text-align: center; margin: 20px 0; }
        .matric-label { font-size: 12px; color: #64748b; margin-bottom: 4px; }
        .matric-value { font-size: 24px; font-weight: 700; color: #059669; letter-spacing: 1px; }
        .btn { display: inline-block; padding: 16px 32px; background: #4a4de5; color: white !important; border-radius: 10px; font-weight: 600; font-size: 15px; text-decoration: none; margin-top: 16px; }
        .footer { padding: 20px 24px; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>Welcome to AGIT Academy!</h1>
                <p>Hi {$name}, your application has been approved.</p>
            </div>
            <div class="content">
                <p>You have been approved to join AGIT Academy. Your matriculation number has been assigned:</p>
                <div class="matric-box">
                    <div class="matric-label">Your Matriculation Number</div>
                    <div class="matric-value">{$matricNo}</div>
                </div>
                <p>You can now log in to access your dashboard, courses, and more.</p>
                <p style="text-align:center"><a href="{$loginUrl}" class="btn">Go to Login</a></p>
            </div>
            <div class="footer">
                AGIT Academy – Excellence in Education
            </div>
        </div>
    </div>
</body>
</html>
HTML;
}

function getStudentRejectedEmailTemplate($data) {
    $name = htmlspecialchars($data['name'] ?? '');
    $reason = nl2br(htmlspecialchars($data['reason'] ?? 'Your application was not approved at this time.'));

    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 0; background: #f1f5f9; }
        .container { max-width: 560px; margin: 0 auto; padding: 24px; }
        .card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,.1); }
        .header { background: linear-gradient(135deg, #64748b 0%, #475569 100%); color: white; padding: 28px 24px; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 600; }
        .content { padding: 28px 24px; }
        .reason-box { background: #fef2f2; border-left: 4px solid #ef4444; padding: 16px; margin: 16px 0; border-radius: 0 8px 8px 0; }
        .footer { padding: 20px 24px; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>Application Update</h1>
                <p>Hi {$name}, regarding your AGIT Academy registration.</p>
            </div>
            <div class="content">
                <p>We regret to inform you that your application to join AGIT Academy was not approved.</p>
                <div class="reason-box">
                    <strong>Reason:</strong><br>
                    {$reason}
                </div>
                <p>If you have questions, please contact us.</p>
            </div>
            <div class="footer">
                AGIT Academy – Excellence in Education
            </div>
        </div>
    </div>
</body>
</html>
HTML;
}

function getForgotPasswordEmailTemplate($code) {
    $code = htmlspecialchars($code);
    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 0; background: #f1f5f9; }
        .container { max-width: 480px; margin: 0 auto; padding: 24px; }
        .card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,.1); }
        .header { background: linear-gradient(135deg, #4a4de5 0%, #5b6cf1 100%); color: white; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 600; }
        .content { padding: 24px; }
        .code-box { background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 12px; padding: 24px; text-align: center; font-size: 28px; font-weight: 700; letter-spacing: 8px; color: #1e293b; }
        .footer { padding: 16px 24px; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>Password Reset – AGIT Academy</h1>
            </div>
            <div class="content">
                <p>Your verification code is:</p>
                <div class="code-box">{$code}</div>
                <p style="margin-top:16px;font-size:14px;color:#64748b">This code expires in 15 minutes. If you didn't request this, please ignore this email.</p>
            </div>
            <div class="footer">
                AGIT Academy – Excellence in Education
            </div>
        </div>
    </div>
</body>
</html>
HTML;
}
