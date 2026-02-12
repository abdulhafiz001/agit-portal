<!-- Subjects Management -->
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Subjects Management</h2>
            <p class="text-sm text-gray-500 mt-1">Manage all subjects and courses</p>
        </div>
        <button onclick="openAddSubject()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium w-full sm:w-auto">
            <i class="fas fa-plus mr-2"></i>Add Subject
        </button>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="relative max-w-md">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" id="search-input" placeholder="Search by name or code..." 
                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="table-responsive overflow-x-auto overflow-y-auto max-h-[calc(100vh-280px)] sm:max-h-[65vh]">
            <table class="data-table" style="min-width: 860px;">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Code</th>
                        <th>Classes</th>
                        <th>Lecturers</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="subjects-table">
                    <tr><td colspan="6" class="text-center py-8 text-gray-400">Loading...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="pagination" class="px-4 py-3 border-t border-gray-100 flex items-center justify-between"></div>
    </div>
</div>

<!-- Add/Edit Subject Modal -->
<div id="subject-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 id="modal-title" class="text-lg font-semibold text-gray-900">Add Subject</h3>
            <button onclick="Modal.close('subject-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="subject-form" class="p-6 space-y-4">
            <input type="hidden" id="subject-id" value="">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subject Name <span class="text-red-500">*</span></label>
                <input type="text" id="f-name" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Mathematics">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subject Code <span class="text-red-500">*</span></label>
                <input type="text" id="f-code" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none uppercase" placeholder="MATH101">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="f-description" rows="3" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none" placeholder="Brief description..."></textarea>
            </div>
            <div id="status-field" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="f-status" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="Modal.close('subject-modal')" class="flex-1 py-2.5 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Cancel</button>
                <button type="submit" id="save-btn" class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Save Subject</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentPage = 1;

async function loadSubjects(page = 1) {
    currentPage = page;
    const search = document.getElementById('search-input').value;
    let url = `/api/admin/subjects?page=${page}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    
    const data = await API.get(url);
    if (!data || !data.success) return;
    
    const tbody = document.getElementById('subjects-table');
    if (data.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-400">No subjects found</td></tr>';
        document.getElementById('pagination').innerHTML = '';
        return;
    }
    
    tbody.innerHTML = data.data.map(s => `
        <tr>
            <td>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600">
                        <i class="fas fa-book text-xs"></i>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900 text-sm">${escapeHtml(s.name)}</div>
                        ${s.description ? `<div class="text-xs text-gray-400 truncate max-w-[200px]">${escapeHtml(s.description)}</div>` : ''}
                    </div>
                </div>
            </td>
            <td><span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">${escapeHtml(s.code)}</span></td>
            <td><span class="text-sm">${s.class_count} classes</span></td>
            <td><span class="text-sm">${s.lecturer_count} lecturers</span></td>
            <td><span class="badge ${s.status === 'active' ? 'badge-success' : 'badge-gray'}">${s.status}</span></td>
            <td class="text-right">
                <div class="flex items-center justify-end gap-1">
                    <button onclick="editSubject(${s.id})" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg" title="Edit">
                        <i class="fas fa-edit text-xs"></i>
                    </button>
                    <button onclick="deleteSubject(${s.id})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg" title="Delete">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
    
    // Pagination
    const p = data.pagination;
    if (p.total_pages <= 1) { document.getElementById('pagination').innerHTML = ''; return; }
    let html = `<span class="text-xs text-gray-500">Showing ${p.offset+1}-${Math.min(p.offset+p.per_page, p.total)} of ${p.total}</span><div class="flex gap-1">`;
    for (let i = 1; i <= p.total_pages; i++) {
        html += `<button onclick="loadSubjects(${i})" class="px-3 py-1 text-xs rounded-lg ${i === p.current_page ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}">${i}</button>`;
    }
    document.getElementById('pagination').innerHTML = html + '</div>';
}

function openAddSubject() {
    document.getElementById('modal-title').textContent = 'Add Subject';
    document.getElementById('subject-id').value = '';
    document.getElementById('subject-form').reset();
    document.getElementById('status-field').classList.add('hidden');
    Modal.open('subject-modal');
}

async function editSubject(id) {
    const data = await API.get(`/api/admin/subjects/${id}`);
    if (!data || !data.success) return;
    const s = data.data;
    
    document.getElementById('modal-title').textContent = 'Edit Subject';
    document.getElementById('subject-id').value = s.id;
    document.getElementById('f-name').value = s.name;
    document.getElementById('f-code').value = s.code;
    document.getElementById('f-description').value = s.description || '';
    document.getElementById('f-status').value = s.status;
    document.getElementById('status-field').classList.remove('hidden');
    Modal.open('subject-modal');
}

async function deleteSubject(id) {
    const yes = await confirmAction('Are you sure you want to delete this subject?');
    if (!yes) return;
    const data = await API.delete(`/api/admin/subjects/${id}`);
    if (data && data.success) { Toast.success(data.message); loadSubjects(currentPage); }
    else if (data) Toast.error(data.message);
}

document.getElementById('subject-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('save-btn');
    setLoading(btn, true);
    
    const id = document.getElementById('subject-id').value;
    const body = {
        name: document.getElementById('f-name').value,
        code: document.getElementById('f-code').value,
        description: document.getElementById('f-description').value,
        status: document.getElementById('f-status').value || 'active',
    };
    
    const data = id ? await API.put(`/api/admin/subjects/${id}`, body) : await API.post('/api/admin/subjects', body);
    setLoading(btn, false);
    
    if (data && data.success) {
        Toast.success(data.message);
        Modal.close('subject-modal');
        loadSubjects(currentPage);
    } else if (data) Toast.error(data.message);
});

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('search-input').addEventListener('input', debounce(() => loadSubjects(1), 400));
    loadSubjects();
});
</script>
