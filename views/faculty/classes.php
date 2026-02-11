<!-- Faculty - My Classes -->
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">My Classes</h2>
        <p class="text-sm text-gray-500 mt-1">View your assigned classes and their students</p>
    </div>

    <!-- Classes Grid -->
    <div id="classes-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="col-span-full text-center py-8 text-gray-400">Loading...</div>
    </div>

    <!-- Students Panel (shown when a class is selected) -->
    <div id="students-panel" class="hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-900" id="class-title">Students</h3>
                    <p class="text-xs text-gray-400" id="student-count"></p>
                </div>
                <button onclick="document.getElementById('students-panel').classList.add('hidden')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Matric No</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="students-table">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
async function loadMyClasses() {
    const data = await API.get('/api/faculty/classes');
    if (!data || !data.success) return;
    
    const grid = document.getElementById('classes-grid');
    if (data.data.length === 0) {
        grid.innerHTML = '<div class="col-span-full text-center py-12 text-gray-400"><i class="fas fa-school text-4xl mb-3 block"></i>No classes assigned to you yet</div>';
        return;
    }
    
    grid.innerHTML = data.data.map(c => `
        <div onclick="loadStudents(${c.id})" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-emerald-200 transition cursor-pointer">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-school text-emerald-600"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 text-sm">${escapeHtml(c.name)}</h4>
                    <span class="badge badge-info text-xs">${c.type}</span>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500"><i class="fas fa-users mr-1"></i>${c.student_count} students</span>
                <i class="fas fa-chevron-right text-gray-300 text-sm"></i>
            </div>
        </div>
    `).join('');
}

async function loadStudents(classId) {
    const data = await API.get(`/api/faculty/classes/${classId}/students`);
    if (!data || !data.success) return;
    
    document.getElementById('students-panel').classList.remove('hidden');
    document.getElementById('class-title').textContent = data.class_name + ' - Students';
    document.getElementById('student-count').textContent = data.data.length + ' students';
    
    const tbody = document.getElementById('students-table');
    if (data.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-gray-400">No students in this class</td></tr>';
        return;
    }
    
    tbody.innerHTML = data.data.map(s => `
        <tr>
            <td>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-xs">
                        ${s.name.charAt(0).toUpperCase()}
                    </div>
                    <span class="font-medium text-gray-900 text-sm">${escapeHtml(s.name)}</span>
                </div>
            </td>
            <td><span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">${escapeHtml(s.matric_no)}</span></td>
            <td class="text-sm text-gray-600">${escapeHtml(s.email)}</td>
            <td class="text-sm text-gray-600">${s.gender || 'N/A'}</td>
            <td><span class="badge badge-success">${s.status}</span></td>
        </tr>
    `).join('');
    
    document.getElementById('students-panel').scrollIntoView({ behavior: 'smooth' });
}

document.addEventListener('DOMContentLoaded', loadMyClasses);
</script>
