<!-- Student Dashboard -->
<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10"><svg class="w-full h-full" viewBox="0 0 400 200"><circle cx="50" cy="50" r="80" fill="white"/><circle cx="350" cy="150" r="120" fill="white"/></svg></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div id="dash-avatar" class="w-14 h-14 rounded-full border-2 border-white/30 bg-white/20 flex items-center justify-center overflow-hidden flex-shrink-0">
                    <span class="text-xl font-bold text-white"><?= strtoupper(substr($_SESSION['user_name'], 0, 2)) ?></span>
                </div>
                <div>
                    <h2 class="text-xl font-bold">Welcome back, <?= $_SESSION['user_name'] ?>!</h2>
                    <p class="text-blue-100 text-sm mt-1">Matric No: <?= $_SESSION['matric_no'] ?? 'N/A' ?></p>
                    <p class="text-blue-200 text-xs mt-1" id="class-info">Loading...</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="<?= APP_URL ?>/student/courses" class="px-4 py-2 bg-white/10 backdrop-blur-sm rounded-lg text-sm font-medium hover:bg-white/20 transition border border-white/20">
                    <i class="fas fa-book-open mr-2"></i>My Courses
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Exams Taken</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" id="stat-exams">--</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-laptop-code text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Classmates</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" id="stat-classmates">--</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">My Class</p>
                    <p class="text-lg font-bold text-gray-900 mt-1 truncate" id="stat-class">--</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-school text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- My Subjects -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-900 text-sm mb-4">My Co</h3>
            <div id="subjects-list" class="space-y-3">
                <div class="text-center py-4 text-gray-400 text-sm">Loading...</div>
            </div>
        </div>

        <!-- My Lecturers -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-900 text-sm mb-4">My Lecturers</h3>
            <div id="lecturers-list" class="space-y-3">
                <div class="text-center py-4 text-gray-400 text-sm">Loading...</div>
            </div>
        </div>
    </div>

    <!-- Announcements -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mt-6">
        <h3 class="font-semibold text-gray-900 text-sm mb-4"><i class="fas fa-bullhorn text-blue-600 mr-2"></i>Announcements</h3>
        <div id="announcements-widget"><p class="text-sm text-gray-400 text-center py-3">Loading...</p></div>
    </div>

    <!-- Today's Schedule -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-900 text-sm mb-4"><i class="fas fa-calendar-day text-emerald-600 mr-2"></i>Today's Schedule</h3>
        <div id="today-schedule"><p class="text-sm text-gray-400 text-center py-3">Loading...</p></div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-900 text-sm mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="<?= APP_URL ?>/student/courses" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-amber-50 hover:bg-amber-100 transition">
                <i class="fas fa-book-open text-amber-600 text-lg"></i>
                <span class="text-xs font-medium text-gray-700">View Courses</span>
            </a>
            <a href="<?= APP_URL ?>/student/exams" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-purple-50 hover:bg-purple-100 transition">
                <i class="fas fa-laptop-code text-purple-600 text-lg"></i>
                <span class="text-xs font-medium text-gray-700">Take Exam</span>
            </a>
            <a href="<?= APP_URL ?>/student/results" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-emerald-50 hover:bg-emerald-100 transition">
                <i class="fas fa-chart-line text-emerald-600 text-lg"></i>
                <span class="text-xs font-medium text-gray-700">My Results</span>
            </a>
            <a href="<?= APP_URL ?>/student/courses" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-blue-50 hover:bg-blue-100 transition">
                <i class="fas fa-download text-blue-600 text-lg"></i>
                <span class="text-xs font-medium text-gray-700">Materials</span>
            </a>
        </div>
    </div>
</div>

<script>
async function loadStudentDashboard() {
    const data = await API.get('/api/student/dashboard/stats');
    if (!data || !data.success) return;
    
    const { stats, subjects, lecturers } = data.data;
    
    document.getElementById('class-info').textContent = `Class: ${stats.class_name} (${stats.class_type || 'N/A'})`;
    document.getElementById('stat-subjects').textContent = stats.total_subjects;
    document.getElementById('stat-exams').textContent = stats.total_exams;
    document.getElementById('stat-classmates').textContent = stats.classmates;
    document.getElementById('stat-class').textContent = stats.class_name;
    
    // Subjects list
    const subjectsList = document.getElementById('subjects-list');
    if (subjects.length === 0) {
        subjectsList.innerHTML = '<div class="text-center py-4 text-gray-400 text-sm">No subjects assigned to your class</div>';
    } else {
        subjectsList.innerHTML = subjects.map((s, i) => `
            <div class="flex items-center gap-3 p-3 rounded-lg ${i % 2 === 0 ? 'bg-gray-50' : ''}">
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
    
    // Lecturers list
    const lecturersList = document.getElementById('lecturers-list');
    if (lecturers.length === 0) {
        lecturersList.innerHTML = '<div class="text-center py-4 text-gray-400 text-sm">No lecturers assigned</div>';
    } else {
        lecturersList.innerHTML = lecturers.map(l => `
            <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50">
                <div class="w-9 h-9 bg-emerald-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-emerald-600 text-sm"></i>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-900">${escapeHtml(l.name)}</div>
                    <div class="text-xs text-gray-400">${escapeHtml(l.email)}</div>
                </div>
            </div>
        `).join('');
    }
}

async function loadAnnouncements() {
    const data = await API.get('/api/student/announcements');
    if (!data || !data.success) return;
    const container = document.getElementById('announcements-widget');
    if (!container) return;
    if (data.data.length === 0) { container.innerHTML = '<p class="text-sm text-gray-400 text-center py-3">No announcements</p>'; return; }
    const pColors = { normal: 'border-blue-200', important: 'border-amber-300', urgent: 'border-red-400' };
    container.innerHTML = data.data.slice(0, 5).map(a => `<div class="p-3 rounded-lg border-l-4 ${pColors[a.priority] || 'border-blue-200'} bg-gray-50 mb-2"><h5 class="text-sm font-semibold text-gray-900">${escapeHtml(a.title)}</h5><p class="text-xs text-gray-600 mt-1 line-clamp-2">${escapeHtml(a.content)}</p><p class="text-xs text-gray-400 mt-1">${formatDate(a.created_at)}</p></div>`).join('');
}

async function loadTodaySchedule() {
    const data = await API.get('/api/student/schedules');
    const container = document.getElementById('today-schedule');
    if (!data?.success) return;
    const days = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];
    const today = days[new Date().getDay()];
    const todayClasses = data.data.filter(s => s.day_of_week === today);
    if (todayClasses.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-400 text-center py-3"><i class="fas fa-coffee mr-2"></i>No classes scheduled for today</p>';
        return;
    }
    container.innerHTML = todayClasses.map(s => `
        <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 mb-2">
            <div class="text-center min-w-[50px]"><div class="text-sm font-bold text-gray-900">${s.start_time?.substring(0,5)}</div><div class="text-xs text-gray-400">${s.end_time?.substring(0,5)}</div></div>
            <div class="flex-1"><div class="text-sm font-medium text-gray-900">${escapeHtml(s.subject_name)}</div><div class="text-xs text-gray-500">${escapeHtml(s.lecturer_name)} ${s.room ? '· ' + escapeHtml(s.room) : ''}</div></div>
        </div>`).join('');
}

async function loadProfilePic() {
    const data = await API.get('/api/profile');
    if (data?.success && data.data.profile_picture) {
        const container = document.getElementById('dash-avatar');
        container.innerHTML = `<img src="${APP_URL}/uploads/${data.data.profile_picture}" class="w-full h-full object-cover">`;
    }
}

document.addEventListener('DOMContentLoaded', () => { loadStudentDashboard(); loadAnnouncements(); loadTodaySchedule(); loadProfilePic(); });
</script>
