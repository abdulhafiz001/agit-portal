<!-- Admin Reports & Analytics -->
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Reports & Analytics</h2>
        <p class="text-sm text-gray-500 mt-1">Comprehensive platform insights and performance metrics</p>
    </div>

    <!-- Overview Stats -->
    <div id="overview-stats" class="grid grid-cols-2 md:grid-cols-4 gap-4"></div>

    <!-- Charts Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h4 class="text-sm font-semibold text-gray-900 mb-4"><i class="fas fa-chart-line mr-2 text-blue-600"></i>Enrollment Trends</h4>
            <canvas id="enrollChart" height="200"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h4 class="text-sm font-semibold text-gray-900 mb-4"><i class="fas fa-users mr-2 text-emerald-600"></i>Class Distribution</h4>
            <canvas id="classDistChart" height="200"></canvas>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h4 class="text-sm font-semibold text-gray-900 mb-4"><i class="fas fa-chart-pie mr-2 text-violet-600"></i>Grade Distribution</h4>
            <canvas id="gradeChart" height="220"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h4 class="text-sm font-semibold text-gray-900 mb-4"><i class="fas fa-check-circle mr-2 text-green-600"></i>Pass / Fail Rate</h4>
            <canvas id="passFailChart" height="220"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h4 class="text-sm font-semibold text-gray-900 mb-4"><i class="fas fa-file-alt mr-2 text-amber-600"></i>Exams by Status</h4>
            <canvas id="examStatusChart" height="220"></canvas>
        </div>
    </div>

    <!-- Top Lecturers & Subject Popularity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h4 class="text-sm font-semibold text-gray-900 mb-4"><i class="fas fa-trophy mr-2 text-amber-600"></i>Top Lecturers by Activity</h4>
            <div id="top-lecturers" class="space-y-3"></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h4 class="text-sm font-semibold text-gray-900 mb-4"><i class="fas fa-book mr-2 text-blue-600"></i>Subject Reach</h4>
            <canvas id="subjectChart" height="200"></canvas>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h4 class="text-sm font-semibold text-gray-900 mb-4"><i class="fas fa-history mr-2 text-gray-600"></i>Recent Activity</h4>
        <div id="activity-feed" class="space-y-3 max-h-80 overflow-y-auto"></div>
    </div>
</div>

<script>
let charts = {};

async function loadReports() {
    const data = await API.get('/api/admin/reports/stats');
    if (!data || !data.success) return;
    const d = data.data;
    const o = d.overview;

    // Overview stats
    document.getElementById('overview-stats').innerHTML = [
        { l: 'Students', v: o.total_students, i: 'fa-user-graduate', c: 'blue' },
        { l: 'Lecturers', v: o.total_lecturers, i: 'fa-chalkboard-teacher', c: 'emerald' },
        { l: 'Classes', v: o.total_classes, i: 'fa-school', c: 'violet' },
        { l: 'Subjects', v: o.total_subjects, i: 'fa-book', c: 'amber' },
        { l: 'Exams Created', v: o.total_exams, i: 'fa-file-alt', c: 'indigo' },
        { l: 'Materials', v: o.total_materials, i: 'fa-folder', c: 'green' },
        { l: 'Scores Recorded', v: o.total_scores, i: 'fa-clipboard-list', c: 'pink' },
        { l: 'Avg Score', v: o.avg_score, i: 'fa-chart-bar', c: 'cyan' },
    ].map(x => `<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
        <div class="flex items-center gap-3"><div class="w-10 h-10 bg-${x.c}-100 rounded-lg flex items-center justify-center"><i class="fas ${x.i} text-${x.c}-600"></i></div><div><div class="text-xl font-bold text-gray-900">${x.v}</div><div class="text-xs text-gray-500">${x.l}</div></div></div>
    </div>`).join('');

    // Enrollment chart
    buildChart('enrollChart', 'line', {
        labels: d.enrollment.map(e => e.month),
        datasets: [{ label: 'New Students', data: d.enrollment.map(e => e.count), borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)', fill: true, tension: 0.4 }]
    }, { scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } });

    // Class distribution
    buildChart('classDistChart', 'bar', {
        labels: d.class_distribution.map(c => c.name),
        datasets: [{ label: 'Students', data: d.class_distribution.map(c => c.count), backgroundColor: '#10b981', borderRadius: 8 }]
    }, { indexAxis: 'y', scales: { x: { beginAtZero: true } }, plugins: { legend: { display: false } } });

    // Grade distribution
    const gradeColors = ['#10b981','#3b82f6','#f59e0b','#f97316','#ef4444','#6b7280'];
    buildChart('gradeChart', 'doughnut', {
        labels: d.grades.map(g => 'Grade ' + g.grade),
        datasets: [{ data: d.grades.map(g => g.count), backgroundColor: gradeColors }]
    }, { plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 12, font: { size: 11 } } } } });

    // Pass/Fail
    buildChart('passFailChart', 'doughnut', {
        labels: ['Pass', 'Fail'],
        datasets: [{ data: [d.pass_fail.pass, d.pass_fail.fail], backgroundColor: ['#10b981', '#ef4444'] }]
    }, { plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 12, font: { size: 11 } } } } });

    // Exam status
    const statusColors = { draft: '#9ca3af', pending: '#f59e0b', approved: '#3b82f6', active: '#10b981', completed: '#8b5cf6', rejected: '#ef4444' };
    buildChart('examStatusChart', 'doughnut', {
        labels: d.exams_by_status.map(e => e.status.charAt(0).toUpperCase() + e.status.slice(1)),
        datasets: [{ data: d.exams_by_status.map(e => e.count), backgroundColor: d.exams_by_status.map(e => statusColors[e.status] || '#9ca3af') }]
    }, { plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 12, font: { size: 11 } } } } });

    // Subject popularity
    buildChart('subjectChart', 'bar', {
        labels: d.subject_popularity.map(s => s.code),
        datasets: [{ label: 'Classes Using', data: d.subject_popularity.map(s => s.class_count), backgroundColor: '#8b5cf6', borderRadius: 8 }]
    }, { scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }, plugins: { legend: { display: false } } });

    // Top lecturers
    document.getElementById('top-lecturers').innerHTML = d.top_lecturers.length === 0 ?
        '<p class="text-gray-400 text-sm">No lecturer data</p>' :
        d.top_lecturers.map((l, i) => `<div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
            <div class="w-8 h-8 ${i < 3 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500'} rounded-full flex items-center justify-center text-xs font-bold">${i+1}</div>
            <div class="flex-1"><div class="text-sm font-medium text-gray-900">${escapeHtml(l.name)}</div>
                <div class="text-xs text-gray-400">${l.exam_count} exams &middot; ${l.material_count} materials &middot; ${l.score_count} scores</div></div>
        </div>`).join('');

    // Recent activity
    document.getElementById('activity-feed').innerHTML = d.recent_activity.length === 0 ?
        '<p class="text-gray-400 text-sm">No recent activity</p>' :
        d.recent_activity.map(a => {
            const icons = { login: 'fa-sign-in-alt text-blue-500', create: 'fa-plus text-green-500', update: 'fa-edit text-amber-500', delete: 'fa-trash text-red-500', upload_material: 'fa-upload text-indigo-500', create_exam: 'fa-file-alt text-violet-500', start_exam: 'fa-play text-green-500', submit_exam: 'fa-paper-plane text-blue-500' };
            const icon = icons[a.action] || 'fa-circle text-gray-400';
            return `<div class="flex items-start gap-3"><div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0"><i class="fas ${icon} text-xs"></i></div>
                <div class="flex-1"><p class="text-sm text-gray-800">${escapeHtml(a.description)}</p><p class="text-xs text-gray-400">${a.user_type} &middot; ${formatDate(a.created_at)}</p></div></div>`;
        }).join('');
}

function buildChart(id, type, data, options = {}) {
    if (charts[id]) charts[id].destroy();
    const ctx = document.getElementById(id)?.getContext('2d');
    if (!ctx) return;
    charts[id] = new Chart(ctx, { type, data, options: { responsive: true, maintainAspectRatio: true, ...options } });
}

document.addEventListener('DOMContentLoaded', loadReports);
</script>
