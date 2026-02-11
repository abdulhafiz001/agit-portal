<!-- Admin Dashboard -->
<div id="dashboard-content">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stat-card bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Students</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" id="stat-students">--</p>
                    <p class="text-xs text-emerald-600 mt-1"><i class="fas fa-arrow-up mr-1"></i><span id="stat-active-students">--</span> active</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Lecturers</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" id="stat-lecturers">--</p>
                    <p class="text-xs text-gray-400 mt-1">Faculty members</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Subjects</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" id="stat-subjects">--</p>
                    <p class="text-xs text-gray-400 mt-1">Active courses</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-book text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Classes</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1" id="stat-classes">--</p>
                    <p class="text-xs text-gray-400 mt-1">Active classes</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-school text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <!-- Enrollment Trend -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 text-sm">Enrollment Trend</h3>
                <span class="text-xs text-gray-400">Last 6 months</span>
            </div>
            <div class="chart-container" style="height:250px">
                <canvas id="enrollmentChart"></canvas>
            </div>
        </div>

        <!-- Class Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 text-sm">Students per Class</h3>
                <span class="text-xs text-gray-400">Top classes</span>
            </div>
            <div class="chart-container" style="height:250px">
                <canvas id="classChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-900 text-sm mb-4">Quick Actions</h3>
            <div class="space-y-2">
                <a href="<?= APP_URL ?>/admin/students" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                    <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-plus text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">Add Student</div>
                        <div class="text-xs text-gray-400">Register new student</div>
                    </div>
                </a>
                <a href="<?= APP_URL ?>/admin/lecturers" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                    <div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-tie text-emerald-600 text-sm"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">Add Lecturer</div>
                        <div class="text-xs text-gray-400">Register faculty member</div>
                    </div>
                </a>
                <a href="<?= APP_URL ?>/admin/classes" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                    <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-plus-circle text-purple-600 text-sm"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">Create Class</div>
                        <div class="text-xs text-gray-400">Add new class</div>
                    </div>
                </a>
                <a href="<?= APP_URL ?>/admin/subjects" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                    <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book-medical text-amber-600 text-sm"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">Add Subject</div>
                        <div class="text-xs text-gray-400">Create new subject</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-900 text-sm mb-4">Recent Activity</h3>
            <div id="activity-feed" class="space-y-3">
                <div class="text-center py-8 text-gray-400 text-sm">Loading activities...</div>
            </div>
        </div>
    </div>
</div>

<script>
let enrollmentChart, classChart;

async function loadDashboard() {
    const data = await API.get('/api/admin/dashboard/stats');
    if (!data || !data.success) return;
    
    const { stats, enrollment, class_distribution, recent_activity } = data.data;
    
    // Update stat cards
    document.getElementById('stat-students').textContent = stats.total_students;
    document.getElementById('stat-lecturers').textContent = stats.total_lecturers;
    document.getElementById('stat-subjects').textContent = stats.total_subjects;
    document.getElementById('stat-classes').textContent = stats.total_classes;
    document.getElementById('stat-active-students').textContent = stats.active_students;
    
    // Enrollment Chart
    const enrollLabels = enrollment.map(e => {
        const [y, m] = e.month.split('-');
        return new Date(y, m-1).toLocaleDateString('en', { month: 'short' });
    });
    const enrollData = enrollment.map(e => e.count);
    
    if (enrollmentChart) enrollmentChart.destroy();
    enrollmentChart = new Chart(document.getElementById('enrollmentChart'), {
        type: 'line',
        data: {
            labels: enrollLabels.length ? enrollLabels : ['No data'],
            datasets: [{
                label: 'New Students',
                data: enrollData.length ? enrollData : [0],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: '#3b82f6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: '#f1f5f9' } },
                x: { ticks: { font: { size: 11 } }, grid: { display: false } }
            }
        }
    });
    
    // Class Distribution Chart
    const classLabels = class_distribution.map(c => c.name.length > 15 ? c.name.substring(0,15)+'...' : c.name);
    const classData = class_distribution.map(c => c.student_count);
    const classColors = ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4'];
    
    if (classChart) classChart.destroy();
    classChart = new Chart(document.getElementById('classChart'), {
        type: 'doughnut',
        data: {
            labels: classLabels.length ? classLabels : ['No classes'],
            datasets: [{
                data: classData.length ? classData : [1],
                backgroundColor: classColors,
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { padding: 15, font: { size: 11 }, usePointStyle: true } } },
            cutout: '65%'
        }
    });
    
    // Recent Activity Feed
    const feed = document.getElementById('activity-feed');
    if (recent_activity.length === 0) {
        feed.innerHTML = '<div class="text-center py-8 text-gray-400 text-sm">No recent activity</div>';
    } else {
        feed.innerHTML = recent_activity.map(a => {
            const icons = { login: 'fa-sign-in-alt text-blue-500', logout: 'fa-sign-out-alt text-gray-400', create: 'fa-plus text-emerald-500', update: 'fa-edit text-amber-500', delete: 'fa-trash text-red-500' };
            const iconClass = Object.entries(icons).find(([k]) => a.action.includes(k))?.[1] || 'fa-circle text-gray-400';
            return `
                <div class="flex items-start gap-3 py-2 border-b border-gray-50 last:border-0">
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas ${iconClass} text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-700">${escapeHtml(a.description || a.action)}</p>
                        <p class="text-xs text-gray-400 mt-0.5">${escapeHtml(a.user_type)} &middot; ${formatDate(a.created_at)}</p>
                    </div>
                </div>
            `;
        }).join('');
    }
}

document.addEventListener('DOMContentLoaded', loadDashboard);
</script>
