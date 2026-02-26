<?php
$db = getDB();
$regEnabled = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'allow_registration'")->fetchColumn();
if ($regEnabled !== '1' && $regEnabled !== 'enabled') {
    header('Location: ' . APP_URL . '/login/student');
    exit;
}
$classes = $db->query("SELECT id, name FROM classes WHERE status = 'active' ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - <?= APP_NAME ?></title>
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
                <h1 class="text-4xl font-bold mb-4">Join AGIT Academy</h1>
                <p class="text-blue-200 text-lg leading-relaxed mb-8">
                    Create your student account to access courses, take exams, submit assignments, and track your academic progress.
                </p>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book-open text-sm"></i>
                        </div>
                        <span class="text-blue-100 text-sm">Access Course Materials</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-laptop text-sm"></i>
                        </div>
                        <span class="text-blue-100 text-sm">Take CBT Examinations</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-trophy text-sm"></i>
                        </div>
                        <span class="text-blue-100 text-sm">Track Your Progress</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Registration Form -->
        <div class="flex items-center justify-center p-6 sm:p-8 lg:p-12 overflow-y-auto">
            <div class="w-full max-w-md">
                <a href="<?= APP_URL ?>/" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 text-sm mb-6 transition">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>

                <div class="mb-6">
                    <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 rounded-full px-3 py-1 text-xs font-semibold mb-4">
                        <i class="fas fa-user-plus"></i> STUDENT REGISTRATION
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Create your account</h2>
                    <p class="text-gray-500 mt-2 text-sm sm:text-base">Fill in your details to register as a student.</p>
                </div>

                <form id="register-form" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-user"></i></span>
                                <input type="text" id="r-name" required
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm text-gray-900 placeholder-gray-400"
                                    placeholder="John Doe">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-envelope"></i></span>
                                <input type="email" id="r-email" required
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm text-gray-900 placeholder-gray-400"
                                    placeholder="student@email.com">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Gender <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-venus-mars"></i></span>
                                <select id="r-gender" required
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm text-gray-900 appearance-none cursor-pointer bg-white">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"><i class="fas fa-chevron-down text-xs"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Class <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-graduation-cap"></i></span>
                                <select id="r-class" required
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm text-gray-900 appearance-none cursor-pointer bg-white">
                                    <option value="">Select Class</option>
                                    <?php foreach ($classes as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"><i class="fas fa-chevron-down text-xs"></i></span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-phone"></i></span>
                                <input type="tel" id="r-phone"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm text-gray-900 placeholder-gray-400"
                                    placeholder="+234...">
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-lock"></i></span>
                                <input type="password" id="r-password" required
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm text-gray-900 placeholder-gray-400"
                                    placeholder="Min 6 characters">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-lock"></i></span>
                                <input type="password" id="r-confirm" required
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm text-gray-900 placeholder-gray-400"
                                    placeholder="Confirm password">
                            </div>
                        </div>
                    </div>
                    <button type="submit" id="reg-btn"
                        class="w-full py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/30 transition text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>

                <p class="text-center text-sm text-gray-500 mt-6">
                    Already have an account? <a href="<?= APP_URL ?>/login/student" class="text-blue-600 hover:underline font-medium">Login here</a>
                </p>
                <p class="text-center text-gray-400 text-xs mt-4">
                    &copy; <?= date('Y') ?> <?= APP_NAME ?>. Student registration.
                </p>
            </div>
        </div>
    </div>

    <script>window.APP_URL = <?= json_encode(APP_URL) ?>;</script>
    <script src="<?= APP_URL ?>/assets/js/app.js"></script>
    <script>
        document.getElementById('register-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('reg-btn');
            const pw = document.getElementById('r-password').value;
            const confirm = document.getElementById('r-confirm').value;

            if (pw !== confirm) {
                Toast.error('Passwords do not match.');
                return;
            }
            if (pw.length < 6) {
                Toast.error('Password must be at least 6 characters.');
                return;
            }

            setLoading(btn, true);

            try {
                const data = await API.post('/api/auth/register', {
                    name: document.getElementById('r-name').value,
                    email: document.getElementById('r-email').value,
                    gender: document.getElementById('r-gender').value,
                    class_id: document.getElementById('r-class').value,
                    phone: document.getElementById('r-phone').value,
                    password: pw
                });

                setLoading(btn, false);

                if (data && data.success) {
                    Toast.success(data.message);
                    setTimeout(() => window.location.href = data.redirect || (window.APP_URL + '/register/success'), 1000);
                } else if (data) {
                    Toast.error(data.message || 'Registration failed.');
                }
            } catch (err) {
                setLoading(btn, false);
                Toast.error(err.message || 'Network error. Please try again.');
            }
        });
    </script>
</body>
</html>
