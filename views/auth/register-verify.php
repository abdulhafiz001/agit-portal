<?php
$token = trim($_GET['t'] ?? '');
if (!$token) {
    header('Location: ' . APP_URL . '/register/student');
    exit;
}
$db = getDB();
$regEnabled = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'allow_registration'")->fetchColumn();
if ($regEnabled !== '1' && $regEnabled !== 'enabled') {
    header('Location: ' . APP_URL . '/login/student');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - <?= APP_NAME ?></title>
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/images/agit-logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/custom.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="login-split">
        <!-- Left Brand Panel -->
        <div class="login-brand bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 flex items-center justify-center p-12 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 left-20 w-72 h-72 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 right-20 w-96 h-96 bg-blue-400 rounded-full blur-3xl"></div>
            </div>
            <div class="relative z-10 text-white max-w-md">
                <div class="flex items-center gap-3 mb-8">
                    <img src="<?= APP_URL ?>/assets/images/agit-logo.png" alt="AGIT Logo" class="h-12 w-auto">
                    <span class="text-2xl font-bold"><?= APP_NAME ?></span>
                </div>
                <h1 class="text-4xl font-bold mb-4">Verify Your Email</h1>
                <p class="text-blue-200 text-lg leading-relaxed mb-8">
                    We've sent a 6-digit verification code to your email. Enter it below to complete your registration.
                </p>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-envelope text-sm"></i>
                        </div>
                        <span class="text-blue-100 text-sm">Check your inbox</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-sm"></i>
                        </div>
                        <span class="text-blue-100 text-sm">Code expires in 15 minutes</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shield-alt text-sm"></i>
                        </div>
                        <span class="text-blue-100 text-sm">Secure verification</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Verification Form -->
        <div class="flex items-center justify-center p-6 sm:p-8 lg:p-12 overflow-y-auto">
            <div class="w-full max-w-md">
                <a href="<?= APP_URL ?>/register/student" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 text-sm mb-6 transition">
                    <i class="fas fa-arrow-left"></i> Back to Registration
                </a>

                <div id="verify-loading" class="text-center py-12 text-gray-500">
                    <div class="w-8 h-8 border-2 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                    <p>Loading...</p>
                </div>

                <div id="verify-form" class="hidden">
                    <div class="mb-6">
                        <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 rounded-full px-3 py-1 text-xs font-semibold mb-4">
                            <i class="fas fa-envelope-open-text"></i> EMAIL VERIFICATION
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Enter verification code</h2>
                        <p class="text-gray-500 mt-2 text-sm sm:text-base">We sent a 6-digit code to <span id="email-display" class="font-medium text-gray-700"></span></p>
                    </div>

                    <form id="verify-form-el" class="space-y-4">
                        <input type="hidden" id="verify-token" value="<?= htmlspecialchars($token) ?>">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-gray-400 text-xs">(change if wrong)</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-envelope"></i></span>
                                <input type="email" id="verify-email" required
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm text-gray-900 placeholder-gray-400"
                                    placeholder="your@email.com">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">6-Digit Code <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-key"></i></span>
                                <input type="text" id="verify-code" required maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autocomplete="one-time-code"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm text-gray-900 placeholder-gray-400 font-mono text-center text-2xl tracking-[0.5em]"
                                    placeholder="000000">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Enter the code from your email</p>
                        </div>
                        <div id="form-message" class="hidden rounded-xl p-4 text-sm"></div>
                        <button type="submit" id="verify-btn"
                            class="w-full py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/30 transition text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i> Verify Email
                        </button>
                        <div class="text-center">
                            <button type="button" id="resend-btn" disabled
                                class="text-sm text-gray-500 hover:text-blue-600 transition disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:text-gray-500">
                                Resend code <span id="resend-timer" class="font-medium"></span>
                            </button>
                        </div>
                    </form>
                </div>

                <div id="verify-error" class="hidden text-center py-12">
                    <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Invalid or Expired Link</h3>
                    <p class="text-gray-500 text-sm mb-6">This verification link is invalid or has expired. Please register again.</p>
                    <a href="<?= APP_URL ?>/register/student" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition">
                        <i class="fas fa-user-plus"></i> Register Again
                    </a>
                </div>

                <p class="text-center text-gray-400 text-xs mt-8">
                    &copy; <?= date('Y') ?> <?= APP_NAME ?>. Student registration.
                </p>
            </div>
        </div>
    </div>

    <script>window.APP_URL = <?= json_encode(APP_URL) ?>;</script>
    <script src="<?= APP_URL ?>/assets/js/app.js"></script>
    <script>
        (function() {
            const token = document.getElementById('verify-token').value;
            const loadingEl = document.getElementById('verify-loading');
            const formEl = document.getElementById('verify-form');
            const errorEl = document.getElementById('verify-error');
            const emailInput = document.getElementById('verify-email');
            const codeInput = document.getElementById('verify-code');
            const resendBtn = document.getElementById('resend-btn');
            const resendTimer = document.getElementById('resend-timer');
            const RESEND_COOLDOWN = 60;

            function showMessage(el, text, type) {
                const msg = document.getElementById('form-message');
                if (!msg) return;
                msg.textContent = text;
                msg.className = 'rounded-xl p-4 text-sm ' + (type === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200');
                msg.classList.remove('hidden');
            }

            function hideMessage() {
                const msg = document.getElementById('form-message');
                if (msg) msg.classList.add('hidden');
            }

            fetch(APP_URL + '/api/auth/verification-details?token=' + encodeURIComponent(token))
                .then(r => r.json())
                .then(data => {
                    loadingEl.classList.add('hidden');
                    if (data.success && data.email) {
                        emailInput.value = data.email;
                        document.getElementById('email-display').textContent = data.email;
                        formEl.classList.remove('hidden');
                        startResendTimer();
                    } else {
                        errorEl.classList.remove('hidden');
                    }
                })
                .catch(() => {
                    loadingEl.classList.add('hidden');
                    errorEl.classList.remove('hidden');
                });

            function startResendTimer() {
                let secs = RESEND_COOLDOWN;
                resendBtn.disabled = true;
                const iv = setInterval(() => {
                    secs--;
                    resendTimer.textContent = '(' + secs + 's)';
                    if (secs <= 0) {
                        clearInterval(iv);
                        resendBtn.disabled = false;
                        resendTimer.textContent = '';
                    }
                }, 1000);
            }

            resendBtn.addEventListener('click', function() {
                if (resendBtn.disabled) return;
                const email = emailInput.value.trim();
                if (!email) {
                    showMessage(null, 'Please enter your email.', 'error');
                    return;
                }
                resendBtn.disabled = true;
                resendBtn.textContent = 'Sending...';
                fetch(APP_URL + '/api/auth/resend-verification', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ token: token, email: email })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showMessage(null, data.message || 'New code sent!', 'success');
                        startResendTimer();
                    } else {
                        showMessage(null, data.message || 'Failed to resend.', 'error');
                        resendBtn.disabled = false;
                    }
                })
                .catch(() => {
                    showMessage(null, 'Network error. Please try again.', 'error');
                    resendBtn.disabled = false;
                })
                .finally(() => {
                    resendBtn.textContent = 'Resend code ';
                });
            });

            document.getElementById('verify-form-el').addEventListener('submit', function(e) {
                e.preventDefault();
                hideMessage();
                const code = codeInput.value.trim();
                if (code.length !== 6) {
                    showMessage(null, 'Please enter the 6-digit code.', 'error');
                    return;
                }
                const btn = document.getElementById('verify-btn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                fetch(APP_URL + '/api/auth/verify-email', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ token: token, code: code })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        if (typeof Toast !== 'undefined') Toast.success(data.message);
                        window.location.href = data.redirect || (APP_URL + '/register/success');
                    } else {
                        showMessage(null, data.message || 'Invalid code.', 'error');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-check-circle"></i> Verify Email';
                    }
                })
                .catch(() => {
                    showMessage(null, 'Network error. Please try again.', 'error');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-circle"></i> Verify Email';
                });
            });

            codeInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 6);
            });
        })();
    </script>
</body>
</html>
