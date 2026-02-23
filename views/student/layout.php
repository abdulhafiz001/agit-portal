<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($currentPage) ?> - Student Portal | <?= APP_NAME ?></title>
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/images/agit-logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/custom.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">
            <div class="flex items-center gap-3 px-5 py-5 border-b border-slate-700/50">
                <img src="<?= APP_URL ?>/assets/images/agit-logo.png" alt="AGIT Logo" class="h-9 w-auto ">
                <div>
                    <div class="text-white font-bold text-sm"><?= APP_NAME ?></div>
                    <div class="text-slate-400 text-xs">Student Portal</div>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                <a href="<?= APP_URL ?>/student/dashboard" class="sidebar-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-th-large w-5 text-center"></i>
                    <span class="sidebar-text ml-3">Dashboard</span>
                </a>

                <div class="pt-4 pb-1">
                    <span class="sidebar-text text-xs font-semibold text-slate-500 uppercase tracking-wider px-3">Academics</span>
                </div>

                <a href="<?= APP_URL ?>/student/courses" class="sidebar-link <?= $currentPage === 'courses' ? 'active' : '' ?>">
                    <i class="fas fa-book-open w-5 text-center"></i>
                    <span class="sidebar-text ml-3">My Courses</span>
                </a>

                <div class="pt-4 pb-1">
                    <span class="sidebar-text text-xs font-semibold text-slate-500 uppercase tracking-wider px-3">Assessment</span>
                </div>

                <a href="<?= APP_URL ?>/student/exams" class="sidebar-link <?= $currentPage === 'exams' ? 'active' : '' ?>">
                    <i class="fas fa-laptop-code w-5 text-center"></i>
                    <span class="sidebar-text ml-3">Exams</span>
                </a>

                <a href="<?= APP_URL ?>/student/results" class="sidebar-link <?= $currentPage === 'results' ? 'active' : '' ?>">
                    <i class="fas fa-chart-line w-5 text-center"></i>
                    <span class="sidebar-text ml-3">My Results</span>
                </a>

                <div class="pt-4 pb-1">
                    <span class="sidebar-text text-xs font-semibold text-slate-500 uppercase tracking-wider px-3">Resources</span>
                </div>

                <a href="<?= APP_URL ?>/student/materials" class="sidebar-link <?= $currentPage === 'materials' ? 'active' : '' ?>">
                    <i class="fas fa-folder-open w-5 text-center"></i>
                    <span class="sidebar-text ml-3">Study Materials</span>
                </a>

                <a href="<?= APP_URL ?>/student/schedules" class="sidebar-link <?= $currentPage === 'schedules' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-alt w-5 text-center"></i>
                    <span class="sidebar-text ml-3">Class Schedule</span>
                </a>

                <a href="<?= APP_URL ?>/student/assignments" class="sidebar-link <?= $currentPage === 'assignments' ? 'active' : '' ?>">
                    <i class="fas fa-tasks w-5 text-center"></i>
                    <span class="sidebar-text ml-3">Assignments</span>
                </a>

                <a href="<?= APP_URL ?>/student/announcements" class="sidebar-link <?= $currentPage === 'announcements' ? 'active' : '' ?>">
                    <i class="fas fa-bullhorn w-5 text-center"></i>
                    <span class="sidebar-text ml-3">Announcements</span>
                </a>

                <div class="pt-4 pb-1">
                    <span class="sidebar-text text-xs font-semibold text-slate-500 uppercase tracking-wider px-3">Account</span>
                </div>

                <a href="<?= APP_URL ?>/student/profile" class="sidebar-link <?= $currentPage === 'profile' ? 'active' : '' ?>">
                    <i class="fas fa-user-circle w-5 text-center"></i>
                    <span class="sidebar-text ml-3">My Profile</span>
                </a>
            </nav>

            <div class="border-t border-slate-700/50 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user text-white text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-white text-sm font-medium truncate"><?= $_SESSION['user_name'] ?></div>
                        <div class="text-slate-400 text-xs"><?= $_SESSION['matric_no'] ?? 'Student' ?></div>
                    </div>
                    <button onclick="handleLogout()" class="text-slate-400 hover:text-red-400 transition" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </div>
            </div>
        </aside>

        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

        <div class="flex-1 lg:ml-64 flex flex-col min-h-screen min-w-0">
            <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
                <div class="flex items-center justify-between px-4 lg:px-6 py-3">
                    <div class="flex items-center gap-4">
                        <button onclick="toggleSidebar()" class="lg:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900"><?= ucfirst($currentPage) ?></h1>
                            <p class="text-xs text-gray-500">Welcome, <?= $_SESSION['user_name'] ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400 hidden sm:inline"><?= date('l, M d, Y') ?></span>
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white text-xs font-bold"><?= strtoupper(substr($_SESSION['user_name'], 0, 2)) ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 lg:p-6 page-content overflow-auto min-w-0">
                <?php require VIEWS_PATH . '/student/' . $currentPage . '.php'; ?>
            </main>
        </div>
    </div>

    <script>window.APP_URL = <?= json_encode(APP_URL) ?>;</script>
    <script src="<?= APP_URL ?>/assets/js/app.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.toggle('hidden');
        }
        async function handleLogout() {
            const yes = await confirmAction('Are you sure you want to logout?');
            if (yes) {
                const data = await API.post('/api/auth/logout');
                if (data && data.success) {
                    Toast.success('Logged out');
                    setTimeout(() => window.location.href = APP_URL + '/login/student', 1000);
                }
            }
        }
    </script>
</body>
</html>
