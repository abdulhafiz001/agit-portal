<!-- Lecturers Management -->
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Lecturers Management</h2>
            <p class="text-sm text-gray-500 mt-1">Manage faculty members and their assignments</p>
        </div>
        <button onclick="openAddLecturer()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium w-full sm:w-auto">
            <i class="fas fa-plus mr-2"></i>Add Lecturer
        </button>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="search-input" placeholder="Search by name, email, or phone..." 
                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <select id="filter-status" class="px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="restricted">Restricted</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="table-responsive overflow-x-auto overflow-y-auto max-h-[calc(100vh-280px)] sm:max-h-[65vh]">
            <table class="data-table" style="min-width: 900px;">
                <thead>
                    <tr>
                        <th>Lecturer</th>
                        <th>Phone</th>
                        <th>Classes</th>
                        <th>Subjects</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="lecturers-table">
                    <tr><td colspan="6" class="text-center py-8 text-gray-400">Loading...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="pagination" class="px-4 py-3 border-t border-gray-100 flex items-center justify-between"></div>
    </div>
</div>

<!-- Add/Edit Lecturer Modal -->
<div id="lecturer-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white rounded-t-2xl z-10">
            <h3 id="modal-title" class="text-lg font-semibold text-gray-900">Add Lecturer</h3>
            <button onclick="Modal.close('lecturer-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="lecturer-form" class="p-6 space-y-4">
            <input type="hidden" id="lecturer-id" value="">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" id="f-name" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Dr. John Doe">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="f-email" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="lecturer@agit.edu">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" id="f-phone" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="+234...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-gray-400 text-xs">(default: password)</span></label>
                    <input type="password" id="f-password" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Leave blank for default">
                </div>
            </div>
            
            <!-- Classes Assignment -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assign Classes</label>
                <div id="classes-checkboxes" class="max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-2">
                    <div class="text-xs text-gray-400">Loading classes...</div>
                </div>
            </div>
            
            <!-- Subjects Assignment -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assign Subjects</label>
                <div id="subjects-checkboxes" class="max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-2">
                    <div class="text-xs text-gray-400">Loading subjects...</div>
                </div>
            </div>

            <div id="status-field" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="f-status" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="active">Active</option>
                    <option value="restricted">Restricted</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="Modal.close('lecturer-modal')" class="flex-1 py-2.5 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Cancel</button>
                <button type="submit" id="save-btn" class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Save Lecturer</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentPage = 1;
let allClasses = [];
let allSubjects = [];

async function loadOptions() {
    const [classData, subjectData] = await Promise.all([
        API.get('/api/admin/classes?all'),
        API.get('/api/admin/subjects?all')
    ]);
    if (classData && classData.success) allClasses = classData.data;
    if (subjectData && subjectData.success) allSubjects = subjectData.data;
}

function renderCheckboxes(containerId, items, selectedIds = [], labelKey = 'name', cbClass = '') {
    const container = document.getElementById(containerId);
    if (items.length === 0) {
        container.innerHTML = `<div class="text-xs text-gray-400">No items available.</div>`;
        return;
    }
    container.innerHTML = items.map(item => `
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" value="${item.id}" ${selectedIds.includes(item.id) ? 'checked' : ''} 
                class="${cbClass} w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
            <span class="text-sm text-gray-700">${escapeHtml(item[labelKey])} ${item.code ? '<span class="text-gray-400">(' + escapeHtml(item.code) + ')</span>' : ''}</span>
        </label>
    `).join('');
}

async function loadLecturers(page = 1) {
    currentPage = page;
    const search = document.getElementById('search-input').value;
    const status = document.getElementById('filter-status').value;
    let url = `/api/admin/lecturers?page=${page}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (status) url += `&status=${status}`;
    
    const data = await API.get(url);
    if (!data || !data.success) return;
    
    const tbody = document.getElementById('lecturers-table');
    if (data.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-400">No lecturers found</td></tr>';
        document.getElementById('pagination').innerHTML = '';
        return;
    }
    
    tbody.innerHTML = data.data.map(l => `
        <tr>
            <td>
                <div class="flex items-center gap-3">
                    ${l.profile_picture 
                        ? `<img src="${APP_URL}/uploads/${l.profile_picture}" class="w-8 h-8 rounded-full object-cover">`
                        : `<div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 font-semibold text-xs">${l.name.charAt(0).toUpperCase()}</div>`
                    }
                    <div>
                        <div class="font-medium text-gray-900 text-sm">${escapeHtml(l.name)}</div>
                        <div class="text-xs text-gray-400">${escapeHtml(l.email)}</div>
                    </div>
                </div>
            </td>
            <td class="text-sm text-gray-600">${l.phone || '<span class="text-gray-400">N/A</span>'}</td>
            <td>
                <div class="flex flex-wrap gap-1">
                    ${l.classes.length ? l.classes.map(c => `<span class="badge badge-info text-xs">${escapeHtml(c.name)}</span>`).join('') : '<span class="text-gray-400 text-xs">None</span>'}
                </div>
            </td>
            <td>
                <div class="flex flex-wrap gap-1">
                    ${l.subjects.length ? l.subjects.slice(0,3).map(s => `<span class="badge badge-warning text-xs">${escapeHtml(s.code)}</span>`).join('') + (l.subjects.length > 3 ? `<span class="text-xs text-gray-400">+${l.subjects.length-3}</span>` : '') : '<span class="text-gray-400 text-xs">None</span>'}
                </div>
            </td>
            <td><span class="badge ${l.status === 'active' ? 'badge-success' : 'badge-danger'}">${l.status}</span></td>
            <td class="text-right">
                <div class="flex items-center justify-end gap-1">
                    <button onclick="editLecturer(${l.id})" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg" title="Edit">
                        <i class="fas fa-edit text-xs"></i>
                    </button>
                    <button onclick="deleteLecturer(${l.id})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg" title="Delete">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
    
    const p = data.pagination;
    if (p.total_pages <= 1) { document.getElementById('pagination').innerHTML = ''; return; }
    let html = `<span class="text-xs text-gray-500">Showing ${p.offset+1}-${Math.min(p.offset+p.per_page, p.total)} of ${p.total}</span><div class="flex gap-1">`;
    for (let i = 1; i <= p.total_pages; i++) {
        html += `<button onclick="loadLecturers(${i})" class="px-3 py-1 text-xs rounded-lg ${i === p.current_page ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}">${i}</button>`;
    }
    document.getElementById('pagination').innerHTML = html + '</div>';
}

function openAddLecturer() {
    document.getElementById('modal-title').textContent = 'Add Lecturer';
    document.getElementById('lecturer-id').value = '';
    document.getElementById('lecturer-form').reset();
    document.getElementById('status-field').classList.add('hidden');
    renderCheckboxes('classes-checkboxes', allClasses, [], 'name', 'class-cb');
    renderCheckboxes('subjects-checkboxes', allSubjects, [], 'name', 'subject-cb');
    Modal.open('lecturer-modal');
}

async function editLecturer(id) {
    const data = await API.get(`/api/admin/lecturers/${id}`);
    if (!data || !data.success) return;
    const l = data.data;
    
    document.getElementById('modal-title').textContent = 'Edit Lecturer';
    document.getElementById('lecturer-id').value = l.id;
    document.getElementById('f-name').value = l.name;
    document.getElementById('f-email').value = l.email;
    document.getElementById('f-phone').value = l.phone || '';
    document.getElementById('f-password').value = '';
    document.getElementById('f-status').value = l.status;
    document.getElementById('status-field').classList.remove('hidden');
    renderCheckboxes('classes-checkboxes', allClasses, l.class_ids || [], 'name', 'class-cb');
    renderCheckboxes('subjects-checkboxes', allSubjects, l.subject_ids || [], 'name', 'subject-cb');
    Modal.open('lecturer-modal');
}

async function deleteLecturer(id) {
    const yes = await confirmAction('Are you sure you want to delete this lecturer?');
    if (!yes) return;
    const data = await API.delete(`/api/admin/lecturers/${id}`);
    if (data && data.success) { Toast.success(data.message); loadLecturers(currentPage); }
    else if (data) Toast.error(data.message);
}

document.getElementById('lecturer-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('save-btn');
    setLoading(btn, true);
    
    const id = document.getElementById('lecturer-id').value;
    const classIds = [...document.querySelectorAll('.class-cb:checked')].map(cb => parseInt(cb.value));
    const subjectIds = [...document.querySelectorAll('.subject-cb:checked')].map(cb => parseInt(cb.value));
    
    const body = {
        name: document.getElementById('f-name').value,
        email: document.getElementById('f-email').value,
        phone: document.getElementById('f-phone').value,
        password: document.getElementById('f-password').value,
        status: document.getElementById('f-status').value || 'active',
        class_ids: classIds,
        subject_ids: subjectIds,
    };
    
    const data = id ? await API.put(`/api/admin/lecturers/${id}`, body) : await API.post('/api/admin/lecturers', body);
    setLoading(btn, false);
    
    if (data && data.success) {
        Toast.success(data.message);
        Modal.close('lecturer-modal');
        loadLecturers(currentPage);
    } else if (data) Toast.error(data.message);
});

document.addEventListener('DOMContentLoaded', async () => {
    document.getElementById('search-input').addEventListener('input', debounce(() => loadLecturers(1), 400));
    document.getElementById('filter-status').addEventListener('change', () => loadLecturers(1));
    await loadOptions();
    loadLecturers();
});
</script>
