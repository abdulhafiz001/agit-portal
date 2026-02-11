<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Login - <?= APP_NAME ?></title>
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/images/agit-logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/custom.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="login-split">
        <!-- Left Brand Panel -->
        <div class="login-brand bg-gradient-to-br from-emerald-900 via-emerald-800 to-teal-900 flex items-center justify-center p-12 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 left-20 w-72 h-72 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 right-20 w-96 h-96 bg-emerald-400 rounded-full blur-3xl"></div>
            </div>
            <div class="relative z-10 text-white max-w-md">
                <div class="flex items-center gap-3 mb-8">
                    <img src="<?= APP_URL ?>/assets/images/agit-logo.png" alt="AGIT Logo" class="h-12 w-auto ">
                    <span class="text-2xl font-bold"><?= APP_NAME ?></span>
                </div>
                <h1 class="text-4xl font-bold mb-4">Faculty Portal</h1>
                <p class="text-emerald-200 text-lg leading-relaxed mb-8">
                    Manage your classes, create exams, grade assignments, and track student performance - all from one powerful dashboard.
                </p>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chalkboard text-sm"></i>
                        </div>
                        <span class="text-emerald-100 text-sm">Class & Student Management</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-edit text-sm"></i>
                        </div>
                        <span class="text-emerald-100 text-sm">Exam Creation & Grading</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-bar text-sm"></i>
                        </div>
                        <span class="text-emerald-100 text-sm">Performance Analytics</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Login Form -->
        <div class="flex items-center justify-center p-8 lg:p-12">
            <div class="w-full max-w-md">
                <a href="<?= APP_URL ?>/" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 text-sm mb-8 transition">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
                
                <div class="mb-8">
                    <div class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 rounded-full px-3 py-1 text-xs font-semibold mb-4">
                        <i class="fas fa-chalkboard-teacher"></i> FACULTY ACCESS
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">Welcome back</h2>
                    <p class="text-gray-500 mt-2">Sign in to your faculty account to continue.</p>
                </div>

                <form id="login-form" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" id="email" required
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition text-sm"
                                placeholder="lecturer@agit.edu">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" id="password" required
                                class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition text-sm"
                                placeholder="Enter your password">
                            <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="toggle-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500">
                            <span class="text-sm text-gray-600">Remember me</span>
                        </label>
                    </div>

                    <button type="submit" id="login-btn"
                        class="w-full py-3 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-500/30 transition text-sm">
                        Sign In to Faculty Portal
                    </button>
                </form>

                <p class="text-center text-gray-400 text-xs mt-8">
                    &copy; <?= date('Y') ?> <?= APP_NAME ?>. Faculty portal.
                </p>
            </div>
        </div>
    </div>

    <script src="<?= APP_URL ?>/assets/js/app.js"></script>
    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('toggle-icon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        document.getElementById('login-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('login-btn');
            setLoading(btn, true);
            
            const data = await API.post('/api/auth/login', {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                role: 'lecturer'
            });

            setLoading(btn, false);
            
            if (data && data.success) {
                Toast.success(data.message);
                setTimeout(() => window.location.href = data.redirect, 1000);
            } else if (data) {
                Toast.error(data.message);
            }
        });
    </script>
</body>
</html>
