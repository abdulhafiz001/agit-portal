<?php
/**
 * Forgot Password Page - Standard page flow (not modal)
 * $forgotRole = 'lecturer' or 'student'
 * $forgotStep = 1 (enter email) or 2 (enter code + new password)
 * $forgotEmail = email from step 1 (for step 2)
 * $forgotError = error message to display
 * $forgotSuccess = success message to display
 */
$isFaculty = ($forgotRole ?? 'student') === 'lecturer';
$step = (int) ($forgotStep ?? 1);
$loginUrl = $isFaculty ? (APP_URL . '/login/faculty') : (APP_URL . '/login/student');
$title = $isFaculty ? 'Faculty' : 'Student';
$bgAccent = $isFaculty ? 'bg-emerald-100' : 'bg-blue-100';
$textAccent = $isFaculty ? 'text-emerald-600' : 'text-blue-600';
$ringAccent = $isFaculty ? 'focus:ring-emerald-500 focus:border-emerald-500' : 'focus:ring-blue-500 focus:border-blue-500';
$btnAccent = $isFaculty ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-blue-600 hover:bg-blue-700';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - <?= $title ?> - <?= APP_NAME ?></title>
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/images/agit-logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/custom.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <a href="<?= $loginUrl ?>" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 text-sm mb-8 transition">
            <i class="fas fa-arrow-left"></i> Back to Login
        </a>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 <?= $bgAccent ?> rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-key <?= $textAccent ?> text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Reset Password</h1>
                <p class="text-gray-500 text-sm mt-2"><?= $title ?> account</p>
            </div>

            <?php if (!empty($forgotError)): ?>
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($forgotError) ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($forgotSuccess)): ?>
            <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($forgotSuccess) ?>
            </div>
            <?php endif; ?>

            <?php if ($step === 1): ?>
            <form method="post" action="<?= APP_URL ?>/forgot-password/<?= $isFaculty ? 'faculty' : 'student' ?>" class="space-y-5">
                <input type="hidden" name="step" value="1">
                <input type="hidden" name="role" value="<?= htmlspecialchars($forgotRole) ?>">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                    <input type="email" name="email" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 <?= $ringAccent ?> outline-none"
                        placeholder="<?= $isFaculty ? 'lecturer@agit.edu' : 'student@agit.edu' ?>"
                        value="<?= htmlspecialchars($forgotEmail ?? '') ?>">
                </div>
                <p class="text-sm text-gray-500">Enter your email and we'll send you a 6-digit code to reset your password.</p>
                <button type="submit" class="w-full py-3 <?= $btnAccent ?> text-white font-semibold rounded-xl transition text-sm">
                    Send Reset Code
                </button>
            </form>
            <?php else: ?>
            <form method="post" action="<?= APP_URL ?>/forgot-password/<?= $isFaculty ? 'faculty' : 'student' ?>" class="space-y-5">
                <input type="hidden" name="step" value="2">
                <input type="hidden" name="role" value="<?= htmlspecialchars($forgotRole) ?>">
                <input type="hidden" name="email" value="<?= htmlspecialchars($forgotEmail ?? '') ?>">
                <p class="text-sm text-gray-500 mb-4">Enter the 6-digit code sent to <strong><?= htmlspecialchars($forgotEmail ?? '') ?></strong></p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Verification Code</label>
                    <input type="text" name="code" maxlength="6" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-center font-mono text-lg tracking-widest focus:ring-2 <?= $ringAccent ?> outline-none"
                        placeholder="000000">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">New Password</label>
                    <input type="password" name="new_password" required minlength="6"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 <?= $ringAccent ?> outline-none"
                        placeholder="At least 6 characters">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
                    <input type="password" name="confirm_password" required minlength="6"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 <?= $ringAccent ?> outline-none"
                        placeholder="Confirm new password">
                </div>
                <button type="submit" class="w-full py-3 <?= $btnAccent ?> text-white font-semibold rounded-xl transition text-sm">
                    Reset Password
                </button>
                <a href="<?= APP_URL ?>/forgot-password/<?= $isFaculty ? 'faculty' : 'student' ?>" class="block text-center text-sm text-gray-500 hover:text-gray-700">Request a new code</a>
            </form>
            <?php endif; ?>
        </div>
        <p class="text-center text-gray-400 text-xs mt-8">&copy; <?= date('Y') ?> <?= APP_NAME ?></p>
    </div>
</body>
</html>
