<?php
/**
 * AGIT Academy Management System - Main Router
 * All requests are routed through this file
 */

// Load configuration
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/functions.php';
require_once __DIR__ . '/helpers/auth.php';
require_once __DIR__ . '/helpers/middleware.php';

// Start session
initSession();

// Get request URI and parse route
$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH) ?: '/';
$basePath = rtrim((string) parse_url(APP_URL, PHP_URL_PATH), '/');
if ($basePath && strpos($requestPath, $basePath) === 0) {
    $route = substr($requestPath, strlen($basePath));
} else {
    $route = $requestPath;
}
$route = rtrim($route, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

// ============================================================
// API ROUTES
// ============================================================
if (strpos($route, '/api/') === 0) {
    header('Content-Type: application/json');

    // Auth API
    if ($route === '/api/auth/login' && $method === 'POST') {
        require __DIR__ . '/api/auth.php';
        handleLogin();
        exit;
    }
    if ($route === '/api/auth/logout' && $method === 'POST') {
        require __DIR__ . '/api/auth.php';
        handleLogout();
        exit;
    }
    if ($route === '/api/auth/register' && $method === 'POST') {
        require __DIR__ . '/api/auth.php';
        handleRegister();
        exit;
    }
    if ($route === '/api/auth/forgot-password' && $method === 'POST') {
        require __DIR__ . '/api/auth.php';
        handleForgotPassword();
        exit;
    }
    if ($route === '/api/auth/reset-password' && $method === 'POST') {
        require __DIR__ . '/api/auth.php';
        handleResetPassword();
        exit;
    }

    // Public APIs (no auth)
    if ($route === '/api/contact' && $method === 'POST') {
        require __DIR__ . '/api/contact.php';
        submitContact();
        exit;
    }
    if ($route === '/api/landing/courses' && $method === 'GET') {
        require __DIR__ . '/api/landing.php';
        getLandingCourses();
        exit;
    }

    // Profile API (all authenticated users)
    if ($route === '/api/profile' && $method === 'GET') {
        if (!isLoggedIn()) jsonResponse(['success' => false, 'message' => 'Unauthorized.'], 401);
        require __DIR__ . '/api/profile.php';
        getProfile();
        exit;
    }
    if ($route === '/api/profile' && $method === 'PUT') {
        if (!isLoggedIn()) jsonResponse(['success' => false, 'message' => 'Unauthorized.'], 401);
        require __DIR__ . '/api/profile.php';
        updateProfile();
        exit;
    }
    if ($route === '/api/profile/picture' && $method === 'POST') {
        if (!isLoggedIn()) jsonResponse(['success' => false, 'message' => 'Unauthorized.'], 401);
        require __DIR__ . '/api/profile.php';
        uploadProfilePicture();
        exit;
    }
    if ($route === '/api/profile/password' && $method === 'POST') {
        if (!isLoggedIn()) jsonResponse(['success' => false, 'message' => 'Unauthorized.'], 401);
        require __DIR__ . '/api/profile.php';
        changePassword();
        exit;
    }

    // Admin API routes
    if (strpos($route, '/api/admin/') === 0) {
        adminMiddleware();

        // Dashboard stats
        if ($route === '/api/admin/dashboard/stats' && $method === 'GET') {
            require __DIR__ . '/api/admin/dashboard.php';
            getDashboardStats();
            exit;
        }

        // Students CRUD
        if (preg_match('#^/api/admin/students(?:/(\d+))?$#', $route, $m)) {
            require __DIR__ . '/api/admin/students.php';
            $id = $m[1] ?? null;
            switch ($method) {
                case 'GET': $id ? getStudent($id) : getStudents(); break;
                case 'POST': createStudent(); break;
                case 'PUT': updateStudent($id); break;
                case 'DELETE': deleteStudent($id); break;
            }
            exit;
        }
        if (preg_match('#^/api/admin/students/(\d+)/restrict$#', $route, $m) && $method === 'POST') {
            require __DIR__ . '/api/admin/students.php';
            restrictStudent($m[1]);
            exit;
        }
        if (preg_match('#^/api/admin/students/(\d+)/unrestrict$#', $route, $m) && $method === 'POST') {
            require __DIR__ . '/api/admin/students.php';
            unrestrrictStudent($m[1]);
            exit;
        }
        if ($route === '/api/admin/students/import' && $method === 'POST') {
            require __DIR__ . '/api/admin/students.php';
            importStudents();
            exit;
        }

        // Subjects CRUD
        if (preg_match('#^/api/admin/subjects(?:/(\d+))?$#', $route, $m)) {
            require __DIR__ . '/api/admin/subjects.php';
            $id = $m[1] ?? null;
            switch ($method) {
                case 'GET': $id ? getSubject($id) : getSubjects(); break;
                case 'POST': $id ? updateSubject($id) : createSubject(); break;
                case 'PUT': updateSubject($id); break;
                case 'DELETE': deleteSubject($id); break;
            }
            exit;
        }

        // Classes CRUD
        if (preg_match('#^/api/admin/classes(?:/(\d+))?$#', $route, $m)) {
            require __DIR__ . '/api/admin/classes.php';
            $id = $m[1] ?? null;
            switch ($method) {
                case 'GET': $id ? getClass($id) : getClasses(); break;
                case 'POST': createClass(); break;
                case 'PUT': updateClass($id); break;
                case 'DELETE': deleteClass($id); break;
            }
            exit;
        }

        // Lecturers CRUD
        if (preg_match('#^/api/admin/lecturers(?:/(\d+))?$#', $route, $m)) {
            require __DIR__ . '/api/admin/lecturers.php';
            $id = $m[1] ?? null;
            switch ($method) {
                case 'GET': $id ? getLecturer($id) : getLecturers(); break;
                case 'POST': createLecturer(); break;
                case 'PUT': updateLecturer($id); break;
                case 'DELETE': deleteLecturer($id); break;
            }
            exit;
        }

        // Admin Exams
        if ($route === '/api/admin/exams' && $method === 'GET') {
            require __DIR__ . '/api/admin/exams.php';
            getAdminExams();
            exit;
        }
        if (preg_match('#^/api/admin/exams/(\d+)$#', $route, $m)) {
            require __DIR__ . '/api/admin/exams.php';
            if ($method === 'GET') { getAdminExam($m[1]); exit; }
        }
        if (preg_match('#^/api/admin/exams/(\d+)/approve$#', $route, $m) && $method === 'POST') {
            require __DIR__ . '/api/admin/exams.php';
            approveExam($m[1]);
            exit;
        }
        if (preg_match('#^/api/admin/exams/(\d+)/start$#', $route, $m) && $method === 'POST') {
            require __DIR__ . '/api/admin/exams.php';
            startExam($m[1]);
            exit;
        }
        if (preg_match('#^/api/admin/exams/(\d+)/stop$#', $route, $m) && $method === 'POST') {
            require __DIR__ . '/api/admin/exams.php';
            stopExam($m[1]);
            exit;
        }

        // Admin Reports
        if ($route === '/api/admin/reports/stats' && $method === 'GET') {
            require __DIR__ . '/api/admin/reports.php';
            getReportStats();
            exit;
        }

        // Admin Continue Key
        if (preg_match('#^/api/admin/exams/attempts/(\d+)/continue-key$#', $route, $m) && $method === 'POST') {
            require __DIR__ . '/api/admin/exams.php';
            generateContinueKey($m[1]);
            exit;
        }

        // Manage Admins
        if (preg_match('#^/api/admin/manage-admins(?:/(\d+))?$#', $route, $m)) {
            require __DIR__ . '/api/admin/manage-admins.php';
            $id = $m[1] ?? null;
            switch ($method) {
                case 'GET': getAdmins(); break;
                case 'POST': createAdmin(); break;
                case 'PUT': updateAdmin($id); break;
                case 'DELETE': deleteAdmin($id); break;
            }
            exit;
        }

        // Admin Announcements
        if ($route === '/api/admin/announcements' && $method === 'GET') {
            require __DIR__ . '/api/admin/announcements.php';
            getAnnouncements();
            exit;
        }
        if ($route === '/api/admin/announcements' && $method === 'POST') {
            require __DIR__ . '/api/admin/announcements.php';
            createAnnouncement();
            exit;
        }
        if (preg_match('#^/api/admin/announcements/(\d+)$#', $route, $m)) {
            require __DIR__ . '/api/admin/announcements.php';
            if ($method === 'PUT') { updateAnnouncement($m[1]); exit; }
            if ($method === 'DELETE') { deleteAnnouncement($m[1]); exit; }
        }

        // Admin Settings
        if ($route === '/api/admin/settings' && $method === 'GET') {
            require __DIR__ . '/api/admin/settings.php';
            getSettings();
            exit;
        }
        if ($route === '/api/admin/settings' && $method === 'POST') {
            require __DIR__ . '/api/admin/settings.php';
            updateSettings();
            exit;
        }
        if ($route === '/api/admin/settings/cms' && $method === 'GET') {
            require __DIR__ . '/api/admin/settings.php';
            getCmsCourses();
            exit;
        }
        if ($route === '/api/admin/settings/cms' && $method === 'POST') {
            require __DIR__ . '/api/admin/settings.php';
            updateCmsCourses();
            exit;
        }

        // Grading Configurations
        if ($route === '/api/admin/grading' && $method === 'GET') {
            require __DIR__ . '/api/admin/settings.php';
            getGradingConfigs();
            exit;
        }
        if ($route === '/api/admin/grading' && $method === 'POST') {
            require __DIR__ . '/api/admin/settings.php';
            saveGradingConfig();
            exit;
        }
        if (preg_match('#^/api/admin/grading/(\d+)$#', $route, $m) && $method === 'DELETE') {
            require __DIR__ . '/api/admin/settings.php';
            deleteGradingConfig($m[1]);
            exit;
        }

        // Promotion Rules
        if ($route === '/api/admin/promotions' && $method === 'GET') {
            require __DIR__ . '/api/admin/settings.php';
            getPromotionRules();
            exit;
        }
        if ($route === '/api/admin/promotions' && $method === 'POST') {
            require __DIR__ . '/api/admin/settings.php';
            savePromotionRule();
            exit;
        }
        if (preg_match('#^/api/admin/promotions/(\d+)$#', $route, $m) && $method === 'DELETE') {
            require __DIR__ . '/api/admin/settings.php';
            deletePromotionRule($m[1]);
            exit;
        }
        if ($route === '/api/admin/promotions/process' && $method === 'POST') {
            require __DIR__ . '/api/admin/settings.php';
            processPromotions();
            exit;
        }

        // Activity Logs
        if ($route === '/api/admin/activity-logs' && $method === 'GET') {
            require __DIR__ . '/api/admin/settings.php';
            getActivityLogs();
            exit;
        }

        // Admin Schedules
        if (preg_match('#^/api/admin/schedules(?:/(\d+))?$#', $route, $m)) {
            require __DIR__ . '/api/admin/schedules.php';
            $id = $m[1] ?? null;
            switch ($method) {
                case 'GET': getSchedules(); break;
                case 'POST': createSchedule(); break;
                case 'PUT': if (!$id) jsonResponse(['success' => false, 'message' => 'Schedule ID required'], 400); updateSchedule($id); break;
                case 'DELETE': if (!$id) jsonResponse(['success' => false, 'message' => 'Schedule ID required'], 400); deleteSchedule($id); break;
            }
            exit;
        }

        // Admin Results
        if ($route === '/api/admin/results' && $method === 'GET') {
            require __DIR__ . '/api/admin/results.php';
            getAdminResults();
            exit;
        }
        if ($route === '/api/admin/results/summary' && $method === 'GET') {
            require __DIR__ . '/api/admin/results.php';
            getResultsSummary();
            exit;
        }
    }

    // Faculty API routes
    if (strpos($route, '/api/faculty/') === 0) {
        facultyMiddleware();

        if ($route === '/api/faculty/dashboard/stats' && $method === 'GET') {
            require __DIR__ . '/api/faculty/dashboard.php';
            getFacultyDashboardStats();
            exit;
        }
        if ($route === '/api/faculty/classes' && $method === 'GET') {
            require __DIR__ . '/api/faculty/classes.php';
            getMyClasses();
            exit;
        }
        if (preg_match('#^/api/faculty/classes/(\d+)/students$#', $route, $m)) {
            require __DIR__ . '/api/faculty/classes.php';
            getClassStudents($m[1]);
            exit;
        }
        if ($route === '/api/faculty/courses' && $method === 'GET') {
            require __DIR__ . '/api/faculty/courses.php';
            getMyCourses();
            exit;
        }

        // Faculty Exams
        if ($route === '/api/faculty/exams' && $method === 'GET') {
            require __DIR__ . '/api/faculty/exams.php';
            getMyExams();
            exit;
        }
        if (preg_match('#^/api/faculty/exams/(\d+)$#', $route, $m)) {
            require __DIR__ . '/api/faculty/exams.php';
            $id = $m[1];
            switch ($method) {
                case 'GET': getMyExam($id); break;
                case 'PUT': updateExam($id); break;
                case 'DELETE': deleteExam($id); break;
            }
            exit;
        }
        if ($route === '/api/faculty/exams' && $method === 'POST') {
            require __DIR__ . '/api/faculty/exams.php';
            createExam();
            exit;
        }
        if (preg_match('#^/api/faculty/exams/(\d+)/submit$#', $route, $m) && $method === 'POST') {
            require __DIR__ . '/api/faculty/exams.php';
            submitExamForApproval($m[1]);
            exit;
        }

        // Faculty Scores
        if ($route === '/api/faculty/scores/options' && $method === 'GET') {
            require __DIR__ . '/api/faculty/scores.php';
            getMyTeachingOptions();
            exit;
        }
        if ($route === '/api/faculty/scores' && $method === 'GET') {
            require __DIR__ . '/api/faculty/scores.php';
            getScoreSheet();
            exit;
        }
        if ($route === '/api/faculty/scores' && $method === 'POST') {
            require __DIR__ . '/api/faculty/scores.php';
            saveScores();
            exit;
        }

        // Faculty Exam Reviews
        if (preg_match('#^/api/faculty/exams/(\d+)/attempts$#', $route, $m) && $method === 'GET') {
            require __DIR__ . '/api/faculty/exams.php';
            getExamAttempts($m[1]);
            exit;
        }
        if (preg_match('#^/api/faculty/exams/attempts/(\d+)$#', $route, $m) && $method === 'GET') {
            require __DIR__ . '/api/faculty/exams.php';
            getAttemptDetail($m[1]);
            exit;
        }
        if ($route === '/api/faculty/exams/grade-answer' && $method === 'POST') {
            require __DIR__ . '/api/faculty/exams.php';
            gradeAnswer();
            exit;
        }

        // Faculty Assignments
        if ($route === '/api/faculty/assignments' && $method === 'GET') {
            require __DIR__ . '/api/faculty/assignments.php';
            getMyAssignments();
            exit;
        }
        if ($route === '/api/faculty/assignments' && $method === 'POST') {
            require __DIR__ . '/api/faculty/assignments.php';
            createAssignment();
            exit;
        }
        if (preg_match('#^/api/faculty/assignments/(\d+)$#', $route, $m) && $method === 'DELETE') {
            require __DIR__ . '/api/faculty/assignments.php';
            deleteAssignment($m[1]);
            exit;
        }
        if (preg_match('#^/api/faculty/assignments/(\d+)/submissions$#', $route, $m) && $method === 'GET') {
            require __DIR__ . '/api/faculty/assignments.php';
            getAssignmentSubmissions($m[1]);
            exit;
        }
        if (preg_match('#^/api/faculty/assignments/submissions/(\d+)/grade$#', $route, $m) && $method === 'POST') {
            require __DIR__ . '/api/faculty/assignments.php';
            gradeSubmission($m[1]);
            exit;
        }

        // Faculty Announcements
        if ($route === '/api/faculty/announcements' && $method === 'GET') {
            require __DIR__ . '/api/common/announcements.php';
            getMyAnnouncements();
            exit;
        }

        // Faculty Schedules
        if ($route === '/api/faculty/schedules' && $method === 'GET') {
            require __DIR__ . '/api/faculty/schedules.php';
            getMySchedules();
            exit;
        }
        if ($route === '/api/faculty/schedules' && $method === 'POST') {
            require __DIR__ . '/api/faculty/schedules.php';
            createMySchedule();
            exit;
        }

        // Faculty Materials
        if ($route === '/api/faculty/materials' && $method === 'GET') {
            require __DIR__ . '/api/faculty/materials.php';
            getMyMaterials();
            exit;
        }
        if ($route === '/api/faculty/materials' && $method === 'POST') {
            require __DIR__ . '/api/faculty/materials.php';
            uploadMaterial();
            exit;
        }
        if (preg_match('#^/api/faculty/materials/(\d+)$#', $route, $m) && $method === 'DELETE') {
            require __DIR__ . '/api/faculty/materials.php';
            deleteMaterial($m[1]);
            exit;
        }
    }

    // Student API routes
    if (strpos($route, '/api/student/') === 0) {
        studentMiddleware();

        if ($route === '/api/student/dashboard/stats' && $method === 'GET') {
            require __DIR__ . '/api/student/dashboard.php';
            getStudentDashboardStats();
            exit;
        }
        if ($route === '/api/student/courses' && $method === 'GET') {
            require __DIR__ . '/api/student/courses.php';
            getMyCourses();
            exit;
        }

        // Student Exams
        if ($route === '/api/student/exams' && $method === 'GET') {
            require __DIR__ . '/api/student/exams.php';
            getAvailableExams();
            exit;
        }
        if (preg_match('#^/api/student/exams/(\d+)/start$#', $route, $m) && $method === 'POST') {
            require __DIR__ . '/api/student/exams.php';
            startExamAttempt($m[1]);
            exit;
        }
        if (preg_match('#^/api/student/exams/(\d+)/questions$#', $route, $m) && $method === 'GET') {
            require __DIR__ . '/api/student/exams.php';
            getExamQuestions($m[1]);
            exit;
        }
        if ($route === '/api/student/exams/answer' && $method === 'POST') {
            require __DIR__ . '/api/student/exams.php';
            saveAnswer();
            exit;
        }
        if (preg_match('#^/api/student/exams/(\d+)/submit$#', $route, $m) && $method === 'POST') {
            require __DIR__ . '/api/student/exams.php';
            submitExamAttempt($m[1]);
            exit;
        }

        // Student Results
        if ($route === '/api/student/results' && $method === 'GET') {
            require __DIR__ . '/api/student/results.php';
            getMyResults();
            exit;
        }

        // Student Assignments
        if ($route === '/api/student/assignments' && $method === 'GET') {
            require __DIR__ . '/api/student/assignments.php';
            getStudentAssignments();
            exit;
        }
        if (preg_match('#^/api/student/assignments/(\d+)/submit$#', $route, $m) && $method === 'POST') {
            require __DIR__ . '/api/student/assignments.php';
            submitAssignment($m[1]);
            exit;
        }

        // Student Announcements
        if ($route === '/api/student/announcements' && $method === 'GET') {
            require __DIR__ . '/api/common/announcements.php';
            getMyAnnouncements();
            exit;
        }

        // Student Schedules
        if ($route === '/api/student/schedules' && $method === 'GET') {
            require __DIR__ . '/api/student/schedules.php';
            getMyClassSchedule();
            exit;
        }

        // Student Materials
        if ($route === '/api/student/materials' && $method === 'GET') {
            require __DIR__ . '/api/student/materials.php';
            getStudentMaterials();
            exit;
        }
        if (preg_match('#^/api/student/materials/(\d+)/download$#', $route, $m) && $method === 'GET') {
            require __DIR__ . '/api/student/materials.php';
            downloadMaterial($m[1]);
            exit;
        }
    }

    // 404 for unmatched API routes
    jsonResponse(['success' => false, 'message' => 'API endpoint not found.'], 404);
}

// ============================================================
// VIEW ROUTES
// ============================================================

// Landing page
if ($route === '/') {
    require VIEWS_PATH . '/landing.php';
    exit;
}

// Login pages (handle both GET and POST for server-side fallback)
if ($route === '/login/admin') {
    if (isLoggedIn() && $_SESSION['user_role'] === 'admin') {
        header('Location: ' . APP_URL . '/admin/dashboard');
        exit;
    }
    $loginError = null;
    if ($method === 'POST' && !empty($_POST['email']) && !empty($_POST['password'])) {
        $result = loginUser(sanitize($_POST['email']), $_POST['password'], 'admin');
        if ($result['success']) {
            header('Location: ' . $result['redirect']);
            exit;
        }
        $loginError = $result['message'];
    }
    require VIEWS_PATH . '/auth/admin-login.php';
    exit;
}
if ($route === '/login/faculty') {
    if (isLoggedIn() && $_SESSION['user_role'] === 'lecturer') {
        header('Location: ' . APP_URL . '/faculty/dashboard');
        exit;
    }
    $loginError = null;
    if ($method === 'POST' && !empty($_POST['email']) && !empty($_POST['password'])) {
        $result = loginUser(sanitize($_POST['email']), $_POST['password'], 'lecturer');
        if ($result['success']) {
            header('Location: ' . $result['redirect']);
            exit;
        }
        $loginError = $result['message'];
    }
    require VIEWS_PATH . '/auth/faculty-login.php';
    exit;
}
if ($route === '/login/student') {
    if (isLoggedIn() && $_SESSION['user_role'] === 'student') {
        header('Location: ' . APP_URL . '/student/dashboard');
        exit;
    }
    $loginError = null;
    if ($method === 'POST' && !empty($_POST['email']) && !empty($_POST['password'])) {
        $result = loginUser(sanitize($_POST['email']), $_POST['password'], 'student');
        if ($result['success']) {
            header('Location: ' . $result['redirect']);
            exit;
        }
        $loginError = $result['message'];
    }
    require VIEWS_PATH . '/auth/student-login.php';
    exit;
}
if ($route === '/register/student') {
    require VIEWS_PATH . '/auth/register.php';
    exit;
}

// Forgot password pages (standard page flow)
if ($route === '/forgot-password/faculty' || $route === '/forgot-password/student') {
    $forgotRole = ($route === '/forgot-password/faculty') ? 'lecturer' : 'student';
    $forgotStep = 1;
    $forgotEmail = '';
    $forgotError = '';
    $forgotSuccess = '';

    if ($method === 'POST') {
        require_once __DIR__ . '/api/auth.php';
        $step = (int) ($_POST['step'] ?? 1);
        if ($step === 1) {
            $email = trim($_POST['email'] ?? '');
            $result = processForgotPasswordRequest($email, $forgotRole);
            if ($result['success']) {
                $forgotStep = 2;
                $forgotEmail = $email;
                $forgotSuccess = $result['message'];
            } else {
                $forgotError = $result['message'];
                $forgotEmail = $email;
            }
        } else {
            $email = trim($_POST['email'] ?? '');
            $code = trim($_POST['code'] ?? '');
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $result = processResetPassword($email, $code, $newPassword, $confirmPassword, $forgotRole);
            if ($result['success']) {
                $loginUrl = $forgotRole === 'lecturer' ? (APP_URL . '/login/faculty') : (APP_URL . '/login/student');
                header('Location: ' . $loginUrl . '?reset=success');
                exit;
            }
            $forgotError = $result['message'];
            $forgotStep = 2;
            $forgotEmail = $email;
        }
    }
    require VIEWS_PATH . '/auth/forgot-password.php';
    exit;
}

// Admin panel routes
if (strpos($route, '/admin') === 0) {
    adminMiddleware();
    $adminPage = str_replace('/admin/', '', $route);
    $adminPage = $adminPage ?: 'dashboard';
    
    // Map routes to view files
    $adminViews = [
        'dashboard' => 'dashboard',
        'students' => 'students',
        'subjects' => 'subjects',
        'classes' => 'classes',
        'lecturers' => 'lecturers',
        'schedules' => 'schedules',
        'exams' => 'exams',
        'results' => 'results',
        'reports' => 'reports',
        'settings' => 'settings',
        'announcements' => 'announcements',
        'profile' => 'profile',
    ];
    
    if (isset($adminViews[$adminPage])) {
        $pageFile = VIEWS_PATH . '/admin/' . $adminViews[$adminPage] . '.php';
        if (file_exists($pageFile)) {
            $currentPage = $adminPage;
            require VIEWS_PATH . '/admin/layout.php';
            exit;
        }
    }
}

// Faculty panel routes
if (strpos($route, '/faculty') === 0) {
    facultyMiddleware();
    $facultyPage = str_replace('/faculty/', '', $route);
    $facultyPage = $facultyPage ?: 'dashboard';
    
    $facultyViews = [
        'dashboard' => 'dashboard',
        'classes' => 'classes',
        'courses' => 'courses',
        'exams' => 'exams',
        'scores' => 'scores',
        'schedules' => 'schedules',
        'materials' => 'materials',
        'assignments' => 'assignments',
        'announcements' => 'announcements',
        'profile' => 'profile',
    ];
    
    if (isset($facultyViews[$facultyPage])) {
        $pageFile = VIEWS_PATH . '/faculty/' . $facultyViews[$facultyPage] . '.php';
        if (file_exists($pageFile)) {
            $currentPage = $facultyPage;
            require VIEWS_PATH . '/faculty/layout.php';
            exit;
        }
    }
}

// Student panel routes
if (strpos($route, '/student') === 0) {
    studentMiddleware();
    $studentPage = str_replace('/student/', '', $route);
    $studentPage = $studentPage ?: 'dashboard';
    
    $studentViews = [
        'dashboard' => 'dashboard',
        'courses' => 'courses',
        'exams' => 'exams',
        'results' => 'results',
        'schedules' => 'schedules',
        'materials' => 'materials',
        'assignments' => 'assignments',
        'announcements' => 'announcements',
        'profile' => 'profile',
    ];
    
    if (isset($studentViews[$studentPage])) {
        $pageFile = VIEWS_PATH . '/student/' . $studentViews[$studentPage] . '.php';
        if (file_exists($pageFile)) {
            $currentPage = $studentPage;
            require VIEWS_PATH . '/student/layout.php';
            exit;
        }
    }
}

// Logout
if ($route === '/logout') {
    $role = logoutUser();
    $loginPage = $role === 'student' ? 'student' : ($role === 'lecturer' ? 'faculty' : 'admin');
    header('Location: ' . APP_URL . '/login/' . $loginPage);
    exit;
}

// 404
http_response_code(404);
echo '<!DOCTYPE html><html><head><title>404 - Page Not Found</title>
<script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
<div class="text-center"><h1 class="text-6xl font-bold text-gray-300">404</h1>
<p class="text-xl text-gray-500 mt-4">Page not found</p>
<a href="' . APP_URL . '" class="mt-6 inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">Go Home</a>
</div></body></html>';
