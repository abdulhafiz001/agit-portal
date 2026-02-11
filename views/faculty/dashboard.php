<!-- Faculty Dashboard -->
<div class="space-y-6">
    <!-- Profile Header -->
    <div class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
        <div id="dashboard-avatar" class="w-16 h-16 rounded-full overflow-hidden bg-emerald-100 flex items-center justify-center flex-shrink-0">
            <img id="dashboard-profile-img" src="" alt="" class="w-full h-full object-cover hidden">
            <span id="dashboard-initials" class="text-2xl font-bold text-emerald-700"><?= strtoupper(substr($_SESSION['user_name'] ?? 'FX', 0, 2)) ?></span>
        </div>
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Welcome back, <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></h2>
            <p class="text-sm text-gray-500">Here's your teaching overview today</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">My Classes</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" id="stat-classes">--</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-school text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">My Courses</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" id="stat-subjects">--</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-book text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Students</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" id="stat-students">--</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-graduate text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Exams Created</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" id="stat-exams">--</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-alt text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Assignments</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" id="stat-assignments">--</p>
                    <p class="text-xs text-gray-400 mt-1">Posted</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-tasks text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Today's Classes</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" id="stat-today">--</p>
                    <p class="text-xs text-emerald-600 mt-1">Scheduled today</p>
                </div>
                <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-day text-teal-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Materials</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" id="stat-materials">--</p>
                    <p class="text-xs text-gray-400 mt-1">Uploaded</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-folder-open text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- My Classes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-900 text-sm mb-4">My Classes</h3>
            <div id="classes-list" class="space-y-3">
                <div class="text-center py-4 text-gray-400 text-sm">Loading...</div>
            </div>
        </div>

        <!-- My Subjects -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-900 text-sm mb-4">My Courses</h3>
            <div id="subjects-list" class="space-y-3">
                <div class="text-center py-4 text-gray-400 text-sm">Loading...</div>
            </div>
        </div>
    </div>

    <!-- Announcements -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mt-6">
        <h3 class="font-semibold text-gray-900 text-sm mb-4"><i class="fas fa-bullhorn text-emerald-600 mr-2"></i>Announcements</h3>
        <div id="announcements-widget"><p class="text-sm text-gray-400 text-center py-3">Loading...</p></div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-900 text-sm mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="<?= APP_URL ?>/faculty/classes" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-blue-50 hover:bg-blue-100 transition">
                <i class="fas fa-users text-blue-600 text-lg"></i>
                <span class="text-xs font-medium text-gray-700">View Students</span>
            </a>
            <a href="<?= APP_URL ?>/faculty/exams" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-purple-50 hover:bg-purple-100 transition">
                <i class="fas fa-plus-circle text-purple-600 text-lg"></i>
                <span class="text-xs font-medium text-gray-700">Create Exam</span>
            </a>
            <a href="<?= APP_URL ?>/faculty/scores" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-emerald-50 hover:bg-emerald-100 transition">
                <i class="fas fa-clipboard-check text-emerald-600 text-lg"></i>
                <span class="text-xs font-medium text-gray-700">Manage Scores</span>
            </a>
            <a href="<?= APP_URL ?>/faculty/exams" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-amber-50 hover:bg-amber-100 transition">
                <i class="fas fa-chart-bar text-amber-600 text-lg"></i>
                <span class="text-xs font-medium text-gray-700">View Results</span>
            </a>
        </div>
    </div>
</div>

<script>
async function loadFacultyDashboard() {
    const data = await API.get('/api/faculty/dashboard/stats');
    if (!data || !data.success) return;
    
    const { stats, classes, subjects, profile } = data.data;
    
    // Profile picture in header
    if (profile) {
        const imgEl = document.getElementById('dashboard-profile-img');
        const initialsEl = document.getElementById('dashboard-initials');
        if (profile.profile_picture && imgEl && initialsEl) {
            imgEl.src = APP_URL + '/uploads/' + profile.profile_picture;
            imgEl.classList.remove('hidden');
            initialsEl.classList.add('hidden');
        }
    }
    
    document.getElementById('stat-classes').textContent = stats.total_classes;
    document.getElementById('stat-subjects').textContent = stats.total_subjects;
    document.getElementById('stat-students').textContent = stats.total_students;
    document.getElementById('stat-exams').textContent = stats.total_exams;
    if (stats.assignment_count !== undefined) document.getElementById('stat-assignments').textContent = stats.assignment_count;
    if (stats.today_classes !== undefined) document.getElementById('stat-today').textContent = stats.today_classes;
    if (stats.material_count !== undefined) document.getElementById('stat-materials').textContent = stats.material_count;
    
    // Classes
    const classesList = document.getElementById('classes-list');
    if (classes.length === 0) {
        classesList.innerHTML = '<div class="text-center py-4 text-gray-400 text-sm">No classes assigned yet</div>';
    } else {
        classesList.innerHTML = classes.map(c => `
            <a href="${APP_URL}/faculty/classes" class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-school text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">${escapeHtml(c.name)}</div>
                        <div class="text-xs text-gray-400">${c.type} class</div>
                    </div>
                </div>
                <span class="text-sm font-semibold text-gray-900">${c.student_count} <span class="text-gray-400 font-normal">students</span></span>
            </a>
        `).join('');
    }
    
    // Subjects
    const subjectsList = document.getElementById('subjects-list');
    if (subjects.length === 0) {
        subjectsList.innerHTML = '<div class="text-center py-4 text-gray-400 text-sm">No subjects assigned yet</div>';
    } else {
        subjectsList.innerHTML = subjects.map(s => `
            <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50">
                <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-amber-600 text-sm"></i>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-900">${escapeHtml(s.name)}</div>
                    <div class="text-xs text-gray-400">${escapeHtml(s.code)}</div>
                </div>
            </div>
        `).join('');
    }
}

async function loadAnnouncements() {
    const data = await API.get('/api/faculty/announcements');
    if (!data || !data.success) return;
    const container = document.getElementById('announcements-widget');
    if (!container) return;
    if (data.data.length === 0) { container.innerHTML = '<p class="text-sm text-gray-400 text-center py-3">No announcements</p>'; return; }
    const pColors = { normal: 'border-blue-200', important: 'border-amber-300', urgent: 'border-red-400' };
    container.innerHTML = data.data.slice(0, 5).map(a => `<div class="p-3 rounded-lg border-l-4 ${pColors[a.priority] || 'border-blue-200'} bg-gray-50 mb-2"><h5 class="text-sm font-semibold text-gray-900">${escapeHtml(a.title)}</h5><p class="text-xs text-gray-600 mt-1 line-clamp-2">${escapeHtml(a.content)}</p><p class="text-xs text-gray-400 mt-1">${formatDate(a.created_at)}</p></div>`).join('');
}

document.addEventListener('DOMContentLoaded', () => { loadFacultyDashboard(); loadAnnouncements(); });
</script>
