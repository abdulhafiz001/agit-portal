<?php
// Check admin page permissions
$allowedPages = $_SESSION['admin_allowed_pages'] ?? ['all'];
$isFullAccess = in_array('all', $allowedPages);
function canSee($page, $allowed, $full) {
    if ($full) return true;
    return in_array($page, $allowed);
}
// Block access to pages the admin is not allowed to see
if (!$isFullAccess && $currentPage !== 'dashboard' && $currentPage !== 'profile' && !canSee($currentPage, $allowedPages, $isFullAccess)) {
    header('Location: ' . APP_URL . '/admin/dashboard');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($currentPage) ?> - Admin Panel | <?= APP_NAME ?></title>
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/images/agit-logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/custom.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: {
            primary: { 50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a' }
        }}}}
    </script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">
            <!-- Logo -->
            <div class="flex items-center gap-3 px-5 py-5 border-b border-slate-700/50">
                <img src="<?= APP_URL ?>/assets/images/agit-logo.png" alt="AGIT Logo" class="h-9 w-auto ">
                <div>
                    <div class="text-white font-bold text-sm"><?= APP_NAME ?></div>
                    <div class="text-slate-400 text-xs">Admin Panel</div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                <!-- Dashboard -->
                <a href="<?= APP_URL ?>/admin/dashboard" class="sidebar-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-th-large w-5 text-center"></i>
                    <span class="sidebar-text ml-3">Dashboard</span>
                </a>

                <!-- Academic Management -->
                <div class="pt-4 pb-1">
                    <span class="sidebar-text text-xs font-semibold text-slate-500 uppercase tracking-wider px-3">Academic</span>
                </div>

                <?php if (canSee('students', $allowedPages, $isFullAccess)): ?>
                <a href="<?= APP_URL ?>/admin/students" class="sidebar-link <?= $currentPage === 'students' ? 'active' : '' ?>">
                    <i class="fas fa-user-graduate w-5 text-center"></i>
                    <span class="sidebar-text ml-3">Students</span>
                </a>
                <?php endif; ?>

                <?php if (canSee('lecturers', $allowedPages, $isFullAccess)): ?>
                <a href="<?= APP_URL ?>/admin/lecturers" class="sidebar-link <?= $currentPage === 'lecturers' ? 'active' : '' ?>">
                    <i class="fas fa-chalkboard-teacher w-5 text-center"></i>
                    <span class="sidebar-text ml-3">Lecturers</span>
                </a>
                <?php endif; ?>

                <?php if (canSee('subjects', $allowedPages, $isFullAccess)): ?>
                <a href="<?= APP_URL ?>/admin/subjects" class="sidebar-link <?= $currentPage === 'subjects' ? 'active' : '' ?>">
                    <i class="fas fa-book w-5 text-center"></i>
                    <span class="sidebar-text ml-3">Subjects</span>
                </a>
                <?php endif; ?>

                <?php if (canSee('classes', $allowedPages, $isFullAccess)): ?>
                <a href="<?= APP_URL ?>/admin/classes" class="sidebar-link <?= $currentPage === 'classes' ? 'active' : '' ?>">
                    <i class="fas fa-school w-5 text-center"></i>
                    <span class="sidebar-text ml-3">Classes</span>
                </a>
                <?php endif; ?>

                <?php if (canSee('classes', $allowedPages, $isFullAccess) || canSee('schedules', $allowedPages, $isFullAccess)): ?>
                <a href="<?= APP_URL ?>/admin/schedules" class="sidebar-link <?= $currentPage === 'schedules' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-alt w-5 text-center"></i>
                    <span class="sidebar-text ml-3">Schedules</span>
                </a>
                <?php endif; ?>

                <?php if (canSee('results', $allowedPages, $isFullAccess)): ?>
                <a href="<?= APP_URL ?>/admin/results" class="sidebar-link <?= $currentPage === 'results' ? 'active' : '' ?>">
                    <i class="fas fa-poll w-5 text-center"></i>
                    <span class="sidebar-text ml-3">Results</span>
                </a>
                <?php endif; ?>

                <?php if (canSee('exams', $allowedPages, $isFullAccess)): ?>
                <!-- Examination -->
                <div class="pt-4 pb-1">
                    <span class="sidebar-text text-xs font-semibold text-slate-500 uppercase tracking-wider px-3">Examination</span>
                </div>

                <a href="<?= APP_URL ?>/admin/exams" class="sidebar-link <?= $currentPage === 'exams' ? 'active' : '' ?>">
                    <i class="fas fa-file-alt w-5 text-center"></i>
                    <span class="sidebar-text ml-3">Manage Exams</span>
                </a>
                <?php endif; ?>

                <?php if (canSee('reports', $allowedPages, $isFullAccess)): ?>
                <!-- Reports & Analytics -->
                <div class="pt-4 pb-1">
                    <span class="sidebar-text text-xs font-semibold text-slate-500 uppercase tracking-wider px-3">Reports</span>
                </div>

                <a href="<?= APP_URL ?>/admin/reports" class="sidebar-link <?= $currentPage === 'reports' ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar w-5 text-center"></i>
                    <span class="sidebar-text ml-3">Reports</span>
                </a>
                <?php endif; ?>

                <?php if (canSee('announcements', $allowedPages, $isFullAccess)): ?>
                <!-- Communication -->
                <div class="pt-4 pb-1">
                    <span class="sidebar-text text-xs font-semibold text-slate-500 uppercase tracking-wider px-3">Communication</span>
                </div>

                <a href="<?= APP_URL ?>/admin/announcements" class="sidebar-link <?= $currentPage === 'announcements' ? 'active' : '' ?>">
                    <i class="fas fa-bullhorn w-5 text-center"></i>
                    <span class="sidebar-text ml-3">Announcements</span>
                </a>
                <?php endif; ?>

                <?php if (canSee('settings', $allowedPages, $isFullAccess)): ?>
                <!-- System -->
                <div class="pt-4 pb-1">
                    <span class="sidebar-text text-xs font-semibold text-slate-500 uppercase tracking-wider px-3">System</span>
                </div>

                <a href="<?= APP_URL ?>/admin/settings" class="sidebar-link <?= $currentPage === 'settings' ? 'active' : '' ?>">
                    <i class="fas fa-cog w-5 text-center"></i>
                    <span class="sidebar-text ml-3">Settings</span>
                </a>
                <?php endif; ?>

                <a href="<?= APP_URL ?>/admin/profile" class="sidebar-link <?= $currentPage === 'profile' ? 'active' : '' ?>">
                    <i class="fas fa-user-circle w-5 text-center"></i>
                    <span class="sidebar-text ml-3">My Profile</span>
                </a>
            </nav>

            <!-- User Info -->
            <div class="border-t border-slate-700/50 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user text-white text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-white text-sm font-medium truncate"><?= $_SESSION['user_name'] ?></div>
                        <div class="text-slate-400 text-xs truncate"><?= ucfirst($_SESSION['admin_role'] ?? 'admin') ?></div>
                    </div>
                    <button onclick="handleLogout()" class="text-slate-400 hover:text-red-400 transition" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Mobile sidebar overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

        <!-- Main Content -->
        <div class="flex-1 lg:ml-64 flex flex-col min-h-screen min-w-0">
            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
                <div class="flex items-center justify-between px-4 lg:px-6 py-3">
                    <div class="flex items-center gap-4">
                        <button onclick="toggleSidebar()" class="lg:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900"><?= ucfirst($currentPage) ?></h1>
                            <p class="text-xs text-gray-500">Welcome back, <?= $_SESSION['user_name'] ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400 hidden sm:inline"><?= date('l, M d, Y') ?></span>
                        <button class="relative p-2 text-gray-500 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-bell"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white text-xs font-bold"><?= strtoupper(substr($_SESSION['user_name'], 0, 2)) ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-6 page-content overflow-auto min-w-0">
                <?php require VIEWS_PATH . '/admin/' . $currentPage . '.php'; ?>
            </main>
        </div>
    </div>

    <script src="<?= APP_URL ?>/assets/js/app.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        async function handleLogout() {
            const yes = await confirmAction('Are you sure you want to logout?');
            if (yes) {
                const data = await API.post('/api/auth/logout');
                if (data && data.success) {
                    Toast.success('Logged out successfully');
                    setTimeout(() => window.location.href = APP_URL + '/login/admin', 1000);
                }
            }
        }
    </script>
    <?php if (file_exists(VIEWS_PATH . '/admin/' . $currentPage . '.js.php')): ?>
        <?php require VIEWS_PATH . '/admin/' . $currentPage . '.js.php'; ?>
    <?php endif; ?>
</body>
</html>
