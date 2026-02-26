<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful - <?= APP_NAME ?></title>
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/images/agit-logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/custom.css">
    <style>
        .checkmark { animation: scaleIn 0.5s ease-out; }
        @keyframes scaleIn { from { transform: scale(0); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    </style>
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
                <h1 class="text-4xl font-bold mb-4">You're All Set!</h1>
                <p class="text-blue-200 text-lg leading-relaxed mb-8">
                    Your application has been received. We'll review it and notify you once approved. Check your email for updates.
                </p>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-envelope text-sm"></i>
                        </div>
                        <span class="text-blue-100 text-sm">Email notification on approval</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-id-card text-sm"></i>
                        </div>
                        <span class="text-blue-100 text-sm">Matriculation number upon approval</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-sign-in-alt text-sm"></i>
                        </div>
                        <span class="text-blue-100 text-sm">Login access after approval</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Success Content -->
        <div class="flex items-center justify-center p-6 sm:p-8 lg:p-12">
            <div class="w-full max-w-md">
                <a href="<?= APP_URL ?>/" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 text-sm mb-6 transition">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
                <div class="text-center sm:text-left">
                    <div class="w-20 h-20 mx-auto sm:mx-0 mb-6 rounded-full bg-emerald-100 flex items-center justify-center checkmark">
                        <i class="fas fa-check text-4xl text-emerald-600"></i>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">Registration Successful!</h2>
                    <p class="text-gray-500 text-base leading-relaxed mb-6">
                        Thank you for registering with <?= APP_NAME ?>. Your application has been received and is now awaiting admin approval.
                    </p>
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 text-left mb-8">
                        <p class="text-gray-700 text-sm leading-relaxed">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            You will receive an email once your application has been reviewed. Upon approval, you will get your matriculation number and can log in to access your dashboard.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center sm:justify-start">
                        <a href="<?= APP_URL ?>/" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition focus:ring-4 focus:ring-blue-500/30">
                            <i class="fas fa-home"></i> Back to Home
                        </a>
                        <a href="<?= APP_URL ?>/login/student" class="inline-flex items-center justify-center gap-2 px-6 py-3 border border-gray-300 hover:border-gray-400 text-gray-700 font-medium rounded-xl transition bg-white">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </div>
                </div>
                <p class="text-center text-gray-400 text-xs mt-8">
                    &copy; <?= date('Y') ?> <?= APP_NAME ?>. Student registration.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
