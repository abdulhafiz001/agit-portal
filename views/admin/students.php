<!-- Students Management -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Students Management</h2>
            <p class="text-sm text-gray-500 mt-1">Manage all registered students</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="Modal.open('import-modal')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium w-full sm:w-auto">
                <i class="fas fa-file-csv mr-2"></i>Import
            </button>
            <button onclick="openAddStudent()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium w-full sm:w-auto">
                <i class="fas fa-plus mr-2"></i>Add Student
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="search-input" placeholder="Search by name, email, or matric no..." 
                        class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
            </div>
            <select id="filter-class" class="px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">All Classes</option>
            </select>
            <select id="filter-status" class="px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="restricted">Restricted</option>
                <option value="graduated">Graduated</option>
            </select>
            <!-- View Toggle -->
            <div class="flex border border-gray-200 rounded-lg overflow-hidden">
                <button onclick="setView('grid')" id="view-grid-btn" class="px-3 py-2 text-sm bg-blue-600 text-white"><i class="fas fa-th-large"></i></button>
                <button onclick="setView('table')" id="view-table-btn" class="px-3 py-2 text-sm bg-white text-gray-500 hover:bg-gray-50"><i class="fas fa-list"></i></button>
            </div>
        </div>
    </div>

    <!-- Grid View (default) -->
    <div id="grid-view" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <div class="col-span-full text-center py-8 text-gray-400">Loading...</div>
    </div>

    <!-- Table View (hidden by default) -->
    <div id="table-view" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hidden">
        <div class="overflow-x-auto overflow-y-auto max-h-[calc(100vh-280px)] sm:max-h-[65vh] table-responsive">
            <table class="data-table w-full" style="min-width: 860px;">
                <thead class="sticky top-0 z-10">
                    <tr>
                        <th>Student</th>
                        <th>Matric No</th>
                        <th>Class</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="students-table">
                    <tr><td colspan="6" class="text-center py-8 text-gray-400">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="pagination" class="flex items-center justify-between"></div>
</div>

<!-- Add/Edit Student Modal -->
<div id="student-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white rounded-t-2xl z-10">
            <h3 id="modal-title" class="text-lg font-semibold text-gray-900">Add Student</h3>
            <button onclick="Modal.close('student-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"><i class="fas fa-times"></i></button>
        </div>
        <form id="student-form" class="p-6 space-y-4">
            <input type="hidden" id="student-id" value="">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" id="f-name" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="John Doe"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="f-email" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="student@agit.edu"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Matric No <span class="text-red-500">*</span></label>
                    <input type="text" id="f-matric" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="AGIT/2025/001"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                    <select id="f-class" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none"><option value="">Select Class</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" id="f-phone" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="+234..."></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <select id="f-gender" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Select Gender</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" id="f-dob" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-gray-400 text-xs">(leave blank to keep)</span></label>
                    <input type="password" id="f-password" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Default: password"></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <input type="text" id="f-address" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Home address"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Guardian Name</label>
                    <input type="text" id="f-guardian-name" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Guardian Phone</label>
                    <input type="text" id="f-guardian-phone" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none"></div>
            </div>
            <div id="status-field" class="hidden"><label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="f-status" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="active">Active</option><option value="restricted">Restricted</option><option value="graduated">Graduated</option></select></div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="Modal.close('student-modal')" class="flex-1 py-2.5 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Cancel</button>
                <button type="submit" id="save-btn" class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Save Student</button>
            </div>
        </form>
    </div>
</div>

<!-- View Student Modal -->
<div id="view-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Student Details</h3>
            <button onclick="Modal.close('view-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"><i class="fas fa-times"></i></button>
        </div>
        <div id="view-content" class="p-6"></div>
    </div>
</div>

<!-- Import Students Modal -->
<div id="import-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] flex flex-col">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
            <h3 class="text-lg font-semibold text-gray-900"><i class="fas fa-file-import text-blue-600 mr-2"></i>Import Students</h3>
            <button onclick="Modal.close('import-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-6 space-y-4 overflow-y-auto flex-1">
            <div class="bg-blue-50 text-blue-700 rounded-lg p-3 text-xs">
                <p class="font-semibold mb-1"><i class="fas fa-info-circle mr-1"></i>Import Instructions:</p>
                <ul class="list-disc pl-4 space-y-0.5">
                    <li>Download the template below to see the required format</li>
                    <li>Supported formats: CSV, XLS, XLSX</li>
                    <li>Required columns: Name, Email, Matric No</li>
                    <li>Optional: Phone, Gender, Date of Birth, Address, Guardian Name, Guardian Phone</li>
                    <li>Default password will be: "password"</li>
                </ul>
            </div>
            <button onclick="downloadTemplate()" class="w-full py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 flex items-center justify-center gap-2">
                <i class="fas fa-download"></i> Download Template (CSV)
            </button>
            <form id="import-form" class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Select Class <span class="text-red-500">*</span></label>
                    <select id="import-class" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"><option value="">Select Class</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Assign Courses <span class="text-gray-400 text-xs">(optional)</span></label>
                    <div id="import-subjects" class="max-h-32 overflow-y-auto space-y-2 p-2 border border-gray-200 rounded-lg"><span class="text-xs text-gray-400">Loading subjects...</span></div></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Upload File <span class="text-red-500">*</span></label>
                    <input type="file" id="import-file" accept=".csv,.xls,.xlsx" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></div>
                <button type="submit" id="import-btn" class="w-full py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium"><i class="fas fa-upload mr-2"></i>Import Students</button>
            </form>
        </div>
    </div>
</div>

<!-- Restrict Student Modal -->
<div id="restrict-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900"><i class="fas fa-ban text-red-500 mr-2"></i>Restrict Student</h3>
            <button onclick="Modal.close('restrict-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-6 space-y-4">
            <input type="hidden" id="restrict-id">
            <p id="restrict-name" class="text-sm text-gray-600 font-medium"></p>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Restriction Type</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" value="login" class="restrict-type rounded border-gray-300 text-red-600"> Block Login</label>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" value="results" class="restrict-type rounded border-gray-300 text-red-600"> Hide Results</label>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" value="exams" class="restrict-type rounded border-gray-300 text-red-600"> Block Exams</label>
                </div></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Reason <span class="text-red-500">*</span></label>
                <textarea id="restrict-reason" rows="3" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm resize-none" placeholder="Provide a reason for this restriction..."></textarea></div>
            <button onclick="applyRestriction()" class="w-full py-2.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700"><i class="fas fa-ban mr-2"></i>Apply Restriction</button>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let classes = [];
let currentView = 'grid';
let studentsData = [];

function setView(view) {
    currentView = view;
    document.getElementById('grid-view').classList.toggle('hidden', view !== 'grid');
    document.getElementById('table-view').classList.toggle('hidden', view !== 'table');
    document.getElementById('view-grid-btn').className = `px-3 py-2 text-sm ${view === 'grid' ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50'}`;
    document.getElementById('view-table-btn').className = `px-3 py-2 text-sm ${view === 'table' ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50'}`;
    if (studentsData.length) renderStudents(studentsData);
}

async function loadClasses() {
    const data = await API.get('/api/admin/classes?all');
    if (data && data.success) {
        classes = data.data;
        const options = '<option value="">All Classes</option>' + classes.map(c => `<option value="${c.id}">${escapeHtml(c.name)}</option>`).join('');
        document.getElementById('filter-class').innerHTML = options;
        document.getElementById('f-class').innerHTML = '<option value="">Select Class</option>' + classes.map(c => `<option value="${c.id}">${escapeHtml(c.name)}</option>`).join('');
        document.getElementById('import-class').innerHTML = '<option value="">Select Class</option>' + classes.map(c => `<option value="${c.id}">${escapeHtml(c.name)}</option>`).join('');
    }
}

async function loadSubjectsForImport() {
    const data = await API.get('/api/admin/subjects');
    if (data && data.success) {
        document.getElementById('import-subjects').innerHTML = data.data.map(s => 
            `<label class="flex items-center gap-2 text-sm"><input type="checkbox" value="${s.id}" class="import-subject rounded border-gray-300 text-blue-600"> ${escapeHtml(s.name)} (${escapeHtml(s.code)})</label>`
        ).join('') || '<span class="text-xs text-gray-400">No subjects available</span>';
    }
}

async function loadStudents(page = 1) {
    currentPage = page;
    const search = document.getElementById('search-input').value;
    const classId = document.getElementById('filter-class').value;
    const status = document.getElementById('filter-status').value;
    let url = `/api/admin/students?page=${page}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (classId) url += `&class_id=${classId}`;
    if (status) url += `&status=${status}`;
    const data = await API.get(url);
    if (!data || !data.success) return;
    studentsData = data.data;
    renderStudents(data.data);
    if (data.pagination) renderPagination(data.pagination);
    else document.getElementById('pagination').innerHTML = '';
}

function renderStudents(students) {
    // Grid view
    const grid = document.getElementById('grid-view');
    if (students.length === 0) {
        grid.innerHTML = '<div class="col-span-full text-center py-12 text-gray-400"><i class="fas fa-user-graduate text-4xl mb-3 block"></i>No students found</div>';
        document.getElementById('students-table').innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-400">No students found</td></tr>';
        return;
    }

    grid.innerHTML = students.map(s => {
        const statusColor = {active:'bg-green-100 text-green-700',restricted:'bg-red-100 text-red-700',graduated:'bg-blue-100 text-blue-700'}[s.status]||'bg-gray-100 text-gray-700';
        const avatar = s.profile_picture 
            ? `<img src="${APP_URL}/uploads/${s.profile_picture}" class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-sm">`
            : `<div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold text-xl border-2 border-white shadow-sm">${s.name.charAt(0).toUpperCase()}</div>`;
        return `<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
            <div class="flex flex-col items-center text-center">
                ${avatar}
                <h4 class="font-semibold text-gray-900 text-sm mt-3 truncate w-full">${escapeHtml(s.name)}</h4>
                <p class="text-xs text-gray-400 font-mono">${escapeHtml(s.matric_no)}</p>
                <p class="text-xs text-gray-500 mt-1">${s.class_name||'<span class="text-gray-400">Unassigned</span>'}</p>
                <span class="mt-2 px-2 py-0.5 text-xs rounded-full ${statusColor}">${s.status}</span>
            </div>
            <div class="flex justify-center gap-1 mt-3 pt-3 border-t border-gray-50">
                <button onclick="viewStudent(${s.id})" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg" title="View"><i class="fas fa-eye text-xs"></i></button>
                <button onclick="editStudent(${s.id})" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg" title="Edit"><i class="fas fa-edit text-xs"></i></button>
                <button onclick="${s.status==='restricted'?`unrestrictStudent(${s.id})`:`showRestrict(${s.id},'${escapeHtml(s.name).replace(/'/g,"\\'")}')`}" class="p-2 ${s.status==='restricted'?'text-green-600 hover:bg-green-50':'text-gray-400 hover:text-orange-600 hover:bg-orange-50'} rounded-lg" title="${s.status==='restricted'?'Unrestrict':'Restrict'}"><i class="fas ${s.status==='restricted'?'fa-unlock':'fa-ban'} text-xs"></i></button>
                <button onclick="deleteStudent(${s.id})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg" title="Delete"><i class="fas fa-trash text-xs"></i></button>
            </div>
        </div>`;
    }).join('');

    // Table view
    document.getElementById('students-table').innerHTML = students.map(s => {
        const statusBadge = {active:'badge-success',restricted:'badge-danger',graduated:'badge-info',withdrawn:'badge-warning'}[s.status]||'badge-gray';
        return `<tr>
            <td><div class="flex items-center gap-3">
                ${s.profile_picture ? `<img src="${APP_URL}/uploads/${s.profile_picture}" class="w-8 h-8 rounded-full object-cover">` : `<div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-xs">${s.name.charAt(0).toUpperCase()}</div>`}
                <div><div class="font-medium text-gray-900 text-sm">${escapeHtml(s.name)}</div><div class="text-xs text-gray-400">${escapeHtml(s.email)}</div></div>
            </div></td>
            <td><span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">${escapeHtml(s.matric_no)}</span></td>
            <td>${s.class_name ? escapeHtml(s.class_name) : '<span class="text-gray-400">Unassigned</span>'}</td>
            <td><span class="badge ${statusBadge}">${s.status}</span></td>
            <td class="text-xs text-gray-500">${formatDate(s.created_at)}</td>
            <td class="text-right"><div class="flex items-center justify-end gap-1">
                <button onclick="viewStudent(${s.id})" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg" title="View"><i class="fas fa-eye text-xs"></i></button>
                <button onclick="editStudent(${s.id})" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg" title="Edit"><i class="fas fa-edit text-xs"></i></button>
                <button onclick="${s.status==='restricted'?`unrestrictStudent(${s.id})`:`showRestrict(${s.id},'${escapeHtml(s.name).replace(/'/g,"\\'")}')`}" class="p-2 ${s.status==='restricted'?'text-green-600 hover:bg-green-50':'text-gray-400 hover:text-orange-600 hover:bg-orange-50'} rounded-lg"><i class="fas ${s.status==='restricted'?'fa-unlock':'fa-ban'} text-xs"></i></button>
                <button onclick="deleteStudent(${s.id})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg" title="Delete"><i class="fas fa-trash text-xs"></i></button>
            </div></td>
        </tr>`;
    }).join('');
}

function renderPagination(p) {
    if (!p || p.total_pages <= 1) { document.getElementById('pagination').innerHTML = ''; return; }
    let html = `<span class="text-xs text-gray-500">Showing ${p.offset + 1}-${Math.min(p.offset + p.per_page, p.total)} of ${p.total}</span><div class="flex gap-1 flex-wrap">`;
    for (let i = 1; i <= p.total_pages; i++) {
        html += `<button onclick="loadStudents(${i})" class="px-3 py-1 text-xs rounded-lg ${i === p.current_page ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}">${i}</button>`;
    }
    document.getElementById('pagination').innerHTML = html + '</div>';
}

function openAddStudent() {
    document.getElementById('modal-title').textContent = 'Add Student';
    document.getElementById('student-id').value = '';
    document.getElementById('student-form').reset();
    document.getElementById('status-field').classList.add('hidden');
    Modal.open('student-modal');
}

async function editStudent(id) {
    const data = await API.get(`/api/admin/students/${id}`);
    if (!data || !data.success) return;
    const s = data.data;
    document.getElementById('modal-title').textContent = 'Edit Student';
    document.getElementById('student-id').value = s.id;
    document.getElementById('f-name').value = s.name;
    document.getElementById('f-email').value = s.email;
    document.getElementById('f-matric').value = s.matric_no;
    document.getElementById('f-class').value = s.class_id || '';
    document.getElementById('f-phone').value = s.phone || '';
    document.getElementById('f-gender').value = s.gender || '';
    document.getElementById('f-dob').value = s.date_of_birth || '';
    document.getElementById('f-address').value = s.address || '';
    document.getElementById('f-guardian-name').value = s.guardian_name || '';
    document.getElementById('f-guardian-phone').value = s.guardian_phone || '';
    document.getElementById('f-status').value = s.status;
    document.getElementById('f-password').value = '';
    document.getElementById('status-field').classList.remove('hidden');
    Modal.open('student-modal');
}

async function viewStudent(id) {
    const data = await API.get(`/api/admin/students/${id}`);
    if (!data || !data.success) return;
    const s = data.data;
    const avatar = s.profile_picture 
        ? `<img src="${APP_URL}/uploads/${s.profile_picture}" class="w-20 h-20 rounded-full object-cover border-2 border-blue-100 shadow-sm">`
        : `<div class="w-20 h-20 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center text-white font-bold text-2xl border-2 border-blue-100 shadow-sm">${s.name.charAt(0).toUpperCase()}</div>`;
    
    document.getElementById('view-content').innerHTML = `
        <div class="flex flex-col items-center text-center mb-6">
            ${avatar}
            <h4 class="font-bold text-gray-900 text-lg mt-3">${escapeHtml(s.name)}</h4>
            <p class="text-sm text-gray-500 font-mono">${escapeHtml(s.matric_no)}</p>
        </div>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between py-2 border-b border-gray-50"><span class="text-gray-500">Email</span><span class="text-gray-900">${escapeHtml(s.email)}</span></div>
            <div class="flex justify-between py-2 border-b border-gray-50"><span class="text-gray-500">Class</span><span class="text-gray-900">${s.class_name || 'Unassigned'}</span></div>
            <div class="flex justify-between py-2 border-b border-gray-50"><span class="text-gray-500">Phone</span><span class="text-gray-900">${s.phone || 'N/A'}</span></div>
            <div class="flex justify-between py-2 border-b border-gray-50"><span class="text-gray-500">Gender</span><span class="text-gray-900">${s.gender || 'N/A'}</span></div>
            <div class="flex justify-between py-2 border-b border-gray-50"><span class="text-gray-500">Status</span><span class="badge ${s.status === 'active' ? 'badge-success' : 'badge-danger'}">${s.status}</span></div>
            <div class="flex justify-between py-2 border-b border-gray-50"><span class="text-gray-500">Guardian</span><span class="text-gray-900">${s.guardian_name || 'N/A'}</span></div>
            <div class="flex justify-between py-2"><span class="text-gray-500">Joined</span><span class="text-gray-900">${formatDate(s.created_at)}</span></div>
            ${s.restriction_type ? `<div class="mt-3 p-3 bg-red-50 rounded-lg border border-red-100"><p class="text-xs font-semibold text-red-600 uppercase mb-1"><i class="fas fa-ban mr-1"></i>Restricted</p><p class="text-sm text-red-700">Type: ${s.restriction_type}</p><p class="text-sm text-red-600 mt-1">Reason: ${s.restriction_reason || 'N/A'}</p></div>` : ''}
        </div>`;
    Modal.open('view-modal');
}

async function deleteStudent(id) {
    const yes = await confirmAction('Are you sure you want to delete this student? This action cannot be undone.');
    if (!yes) return;
    const data = await API.delete(`/api/admin/students/${id}`);
    if (data && data.success) { Toast.success(data.message); loadStudents(currentPage); } else if (data) Toast.error(data.message);
}

function showRestrict(id, name) {
    document.getElementById('restrict-id').value = id;
    document.getElementById('restrict-name').textContent = 'Restricting: ' + name;
    document.querySelectorAll('.restrict-type').forEach(c => c.checked = false);
    document.getElementById('restrict-reason').value = '';
    Modal.open('restrict-modal');
}

async function applyRestriction() {
    const id = document.getElementById('restrict-id').value;
    const types = [...document.querySelectorAll('.restrict-type:checked')].map(c => c.value);
    const reason = document.getElementById('restrict-reason').value.trim();
    if (!types.length) { Toast.error('Select at least one restriction type.'); return; }
    if (!reason) { Toast.error('Please provide a reason.'); return; }
    const data = await API.post(`/api/admin/students/${id}/restrict`, { restriction_type: types.join(','), reason });
    if (data && data.success) { Toast.success(data.message); Modal.close('restrict-modal'); loadStudents(currentPage); }
    else if (data) Toast.error(data.message);
}

async function unrestrictStudent(id) {
    if (!await confirmAction('Remove all restrictions for this student?')) return;
    const data = await API.post(`/api/admin/students/${id}/unrestrict`);
    if (data && data.success) { Toast.success(data.message); loadStudents(currentPage); }
}

document.getElementById('student-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('save-btn'); setLoading(btn, true);
    const id = document.getElementById('student-id').value;
    const body = {
        name: document.getElementById('f-name').value, email: document.getElementById('f-email').value,
        matric_no: document.getElementById('f-matric').value, class_id: document.getElementById('f-class').value,
        phone: document.getElementById('f-phone').value, gender: document.getElementById('f-gender').value,
        date_of_birth: document.getElementById('f-dob').value, address: document.getElementById('f-address').value,
        guardian_name: document.getElementById('f-guardian-name').value, guardian_phone: document.getElementById('f-guardian-phone').value,
        password: document.getElementById('f-password').value, status: document.getElementById('f-status').value,
    };
    const data = id ? await API.put(`/api/admin/students/${id}`, body) : await API.post('/api/admin/students', body);
    setLoading(btn, false);
    if (data && data.success) { Toast.success(data.message); Modal.close('student-modal'); loadStudents(currentPage); } else if (data) Toast.error(data.message);
});

document.getElementById('import-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('import-btn'); setLoading(btn, true);
    const formData = new FormData();
    formData.append('csv_file', document.getElementById('import-file').files[0]);
    formData.append('class_id', document.getElementById('import-class').value);
    const subjects = [...document.querySelectorAll('.import-subject:checked')].map(c => c.value);
    formData.append('subject_ids', JSON.stringify(subjects));
    const data = await API.upload('/api/admin/students/import', formData);
    setLoading(btn, false);
    if (data && data.success) { Toast.success(data.message); Modal.close('import-modal'); loadStudents(1); } else if (data) Toast.error(data.message);
});

function downloadTemplate() {
    const csv = 'Name,Email,Matric No,Phone,Gender,Date of Birth,Address,Guardian Name,Guardian Phone\nJohn Doe,john@example.com,AGIT/2025/001,+234000000000,male,2000-01-15,123 Main St,Jane Doe,+234000000001\n';
    const blob = new Blob([csv], { type: 'text/csv' });
    const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'student_import_template.csv'; a.click();
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('search-input').addEventListener('input', debounce(() => loadStudents(1), 400));
    document.getElementById('filter-class').addEventListener('change', () => loadStudents(1));
    document.getElementById('filter-status').addEventListener('change', () => loadStudents(1));
    loadClasses();
    loadSubjectsForImport();
    loadStudents();
});
</script>
