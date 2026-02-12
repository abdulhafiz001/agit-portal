<?php
// Get settings to check if registration is enabled
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
    <title>Student Registration - AGIT Academy</title>
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/images/agit-logo.png">
    <link rel="stylesheet" href="https://cdn.tailwindcss.com">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/custom.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 flex items-center justify-center p-4 sm:p-6">
    <div class="w-full max-w-lg">
        <div class="text-center mb-8">
            <img src="<?= APP_URL ?>/assets/images/agit-logo.png" alt="AGIT Logo" class="h-16 w-auto mx-auto mb-4">
            <h1 class="text-2xl font-bold text-gray-900">Student Registration</h1>
            <p class="text-sm text-gray-500 mt-2">Create your AGIT Academy student account</p>
        </div>
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sm:p-8">
            <form id="register-form" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" id="r-name" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="John Doe"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="r-email" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="student@email.com"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Matric No <span class="text-red-500">*</span></label>
                        <input type="text" id="r-matric" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="AGIT/2025/001"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Class <span class="text-red-500">*</span></label>
                        <select id="r-class" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Select Class</option>
                            <?php foreach ($classes as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select></div>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" id="r-phone" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="+234..."></div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                        <input type="password" id="r-password" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Min 6 characters"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                        <input type="password" id="r-confirm" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Confirm"></div>
                </div>
                <button type="submit" id="reg-btn" class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition">
                    <i class="fas fa-user-plus mr-2"></i>Create Account
                </button>
            </form>
            <p class="text-center text-sm text-gray-500 mt-4">Already have an account? <a href="<?= APP_URL ?>/login/student" class="text-blue-600 hover:underline font-medium">Login here</a></p>
        </div>
    </div>
    <script>
    const APP_URL = '<?= APP_URL ?>';
    document.getElementById('register-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('reg-btn');
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
        
        const pw = document.getElementById('r-password').value;
        const confirm = document.getElementById('r-confirm').value;
        if (pw !== confirm) {
            Toastify({text:'Passwords do not match.',backgroundColor:'#ef4444',duration:3000}).showToast();
            btn.disabled = false; btn.innerHTML = '<i class="fas fa-user-plus mr-2"></i>Create Account';
            return;
        }
        if (pw.length < 6) {
            Toastify({text:'Password must be at least 6 characters.',backgroundColor:'#ef4444',duration:3000}).showToast();
            btn.disabled = false; btn.innerHTML = '<i class="fas fa-user-plus mr-2"></i>Create Account';
            return;
        }
        
        try {
            const res = await fetch(APP_URL + '/api/auth/register', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    name: document.getElementById('r-name').value,
                    email: document.getElementById('r-email').value,
                    matric_no: document.getElementById('r-matric').value,
                    class_id: document.getElementById('r-class').value,
                    phone: document.getElementById('r-phone').value,
                    password: pw
                })
            });
            const data = await res.json();
            if (data.success) {
                Toastify({text:'Registration successful! Redirecting to login...',backgroundColor:'#10b981',duration:2000}).showToast();
                setTimeout(() => window.location.href = APP_URL + '/login/student', 2000);
            } else {
                Toastify({text:data.message||'Registration failed.',backgroundColor:'#ef4444',duration:3000}).showToast();
                btn.disabled = false; btn.innerHTML = '<i class="fas fa-user-plus mr-2"></i>Create Account';
            }
        } catch(err) {
            Toastify({text:'Network error.',backgroundColor:'#ef4444',duration:3000}).showToast();
            btn.disabled = false; btn.innerHTML = '<i class="fas fa-user-plus mr-2"></i>Create Account';
        }
    });
    </script>
</body>
</html>
