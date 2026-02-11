<!-- Classes Management -->
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Classes Management</h2>
            <p class="text-sm text-gray-500 mt-1">Manage semester-based and professional classes</p>
        </div>
        <button onclick="openAddClass()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
            <i class="fas fa-plus mr-2"></i>Add Class
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="search-input" placeholder="Search classes..." 
                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <select id="filter-type" class="px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">All Types</option>
                <option value="semester">Semester</option>
                <option value="professional">Professional</option>
            </select>
        </div>
    </div>

    <!-- Classes Grid -->
    <div id="classes-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="col-span-full text-center py-8 text-gray-400">Loading...</div>
    </div>
    <div id="pagination" class="flex items-center justify-between"></div>
</div>

<!-- Add/Edit Class Modal -->
<div id="class-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white rounded-t-2xl z-10">
            <h3 id="modal-title" class="text-lg font-semibold text-gray-900">Add Class</h3>
            <button onclick="Modal.close('class-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="class-form" class="p-6 space-y-4">
            <input type="hidden" id="class-id" value="">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Class Name <span class="text-red-500">*</span></label>
                <input type="text" id="f-name" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g., Computer Science - Year 1">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Class Type <span class="text-red-500">*</span></label>
                <select id="f-type" required onchange="toggleClassFields()" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="semester">Semester Based</option>
                    <option value="professional">Professional</option>
                </select>
            </div>
            <div id="semester-fields">
                <label class="block text-sm font-medium text-gray-700 mb-1">Number of Semesters</label>
                <input type="number" id="f-semester-count" min="1" max="10" value="2" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div id="professional-fields" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (weeks)</label>
                <input type="number" id="f-duration-weeks" min="1" max="52" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g., 15">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
                <input type="number" id="f-capacity" min="1" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Max students">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Subjects Offered</label>
                <div id="subjects-checkboxes" class="max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-2">
                    <div class="text-xs text-gray-400">Loading subjects...</div>
                </div>
            </div>
            <div id="status-field" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="f-status" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="Modal.close('class-modal')" class="flex-1 py-2.5 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Cancel</button>
                <button type="submit" id="save-btn" class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Save Class</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentPage = 1;
let allSubjects = [];

function toggleClassFields() {
    const type = document.getElementById('f-type').value;
    document.getElementById('semester-fields').classList.toggle('hidden', type !== 'semester');
    document.getElementById('professional-fields').classList.toggle('hidden', type !== 'professional');
}

async function loadSubjectsList() {
    const data = await API.get('/api/admin/subjects?all');
    if (data && data.success) {
        allSubjects = data.data;
        renderSubjectCheckboxes();
    }
}

function renderSubjectCheckboxes(selectedIds = []) {
    const container = document.getElementById('subjects-checkboxes');
    if (allSubjects.length === 0) {
        container.innerHTML = '<div class="text-xs text-gray-400">No subjects available. Create subjects first.</div>';
        return;
    }
    container.innerHTML = allSubjects.map(s => `
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" value="${s.id}" ${selectedIds.includes(s.id) ? 'checked' : ''} 
                class="subject-cb w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
            <span class="text-sm text-gray-700">${escapeHtml(s.name)} <span class="text-gray-400">(${escapeHtml(s.code)})</span></span>
        </label>
    `).join('');
}

async function loadClasses(page = 1) {
    currentPage = page;
    const search = document.getElementById('search-input').value;
    const type = document.getElementById('filter-type').value;
    let url = `/api/admin/classes?page=${page}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (type) url += `&type=${type}`;
    
    const data = await API.get(url);
    if (!data || !data.success) return;
    const grid = document.getElementById('classes-grid');
    
    if (data.data.length === 0) {
        grid.innerHTML = '<div class="col-span-full text-center py-8 text-gray-400">No classes found</div>';
        document.getElementById('pagination').innerHTML = '';
        return;
    }
    
    grid.innerHTML = data.data.map(c => {
        const typeColor = c.type === 'semester' ? 'blue' : 'purple';
        return `
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-${typeColor}-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-${c.type === 'semester' ? 'calendar-alt' : 'certificate'} text-${typeColor}-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 text-sm">${escapeHtml(c.name)}</h4>
                        <span class="badge badge-${typeColor === 'blue' ? 'info' : 'badge-gray'} text-xs">${c.type}</span>
                    </div>
                </div>
                <div class="flex gap-1">
                    <button onclick="editClass(${c.id})" class="p-1.5 text-gray-400 hover:text-amber-600 rounded" title="Edit"><i class="fas fa-edit text-xs"></i></button>
                    <button onclick="deleteClass(${c.id})" class="p-1.5 text-gray-400 hover:text-red-600 rounded" title="Delete"><i class="fas fa-trash text-xs"></i></button>
                </div>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between text-gray-500">
                    <span><i class="fas fa-users mr-2 text-gray-400"></i>Students</span>
                    <span class="font-semibold text-gray-900">${c.student_count}</span>
                </div>
                <div class="flex items-center justify-between text-gray-500">
                    <span><i class="fas fa-chalkboard-teacher mr-2 text-gray-400"></i>Lecturers</span>
                    <span class="font-semibold text-gray-900">${c.lecturer_count}</span>
                </div>
                ${c.type === 'semester' ? `
                <div class="flex items-center justify-between text-gray-500">
                    <span><i class="fas fa-layer-group mr-2 text-gray-400"></i>Semesters</span>
                    <span class="font-semibold text-gray-900">${c.current_semester || 1} / ${c.semester_count || 2}</span>
                </div>` : `
                <div class="flex items-center justify-between text-gray-500">
                    <span><i class="fas fa-clock mr-2 text-gray-400"></i>Duration</span>
                    <span class="font-semibold text-gray-900">${c.duration_weeks || 'N/A'} weeks</span>
                </div>`}
            </div>
            <div class="mt-3 pt-3 border-t border-gray-50 flex items-center justify-between">
                <span class="badge ${c.status === 'active' ? 'badge-success' : 'badge-gray'}">${c.status}</span>
                ${c.capacity ? `<span class="text-xs text-gray-400">${c.student_count}/${c.capacity} capacity</span>` : ''}
            </div>
        </div>`;
    }).join('');
    
    // Pagination
    const p = data.pagination;
    if (p.total_pages <= 1) { document.getElementById('pagination').innerHTML = ''; return; }
    let html = `<span class="text-xs text-gray-500">Page ${p.current_page} of ${p.total_pages}</span><div class="flex gap-1">`;
    for (let i = 1; i <= p.total_pages; i++) {
        html += `<button onclick="loadClasses(${i})" class="px-3 py-1 text-xs rounded-lg ${i === p.current_page ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}">${i}</button>`;
    }
    document.getElementById('pagination').innerHTML = html + '</div>';
}

function openAddClass() {
    document.getElementById('modal-title').textContent = 'Add Class';
    document.getElementById('class-id').value = '';
    document.getElementById('class-form').reset();
    document.getElementById('f-type').value = 'semester';
    document.getElementById('status-field').classList.add('hidden');
    toggleClassFields();
    renderSubjectCheckboxes();
    Modal.open('class-modal');
}

async function editClass(id) {
    const data = await API.get(`/api/admin/classes/${id}`);
    if (!data || !data.success) return;
    const c = data.data;
    
    document.getElementById('modal-title').textContent = 'Edit Class';
    document.getElementById('class-id').value = c.id;
    document.getElementById('f-name').value = c.name;
    document.getElementById('f-type').value = c.type;
    document.getElementById('f-semester-count').value = c.semester_count || 2;
    document.getElementById('f-duration-weeks').value = c.duration_weeks || '';
    document.getElementById('f-capacity').value = c.capacity || '';
    document.getElementById('f-status').value = c.status;
    document.getElementById('status-field').classList.remove('hidden');
    toggleClassFields();
    renderSubjectCheckboxes((c.subjects || []).map(s => s.id));
    Modal.open('class-modal');
}

async function deleteClass(id) {
    const yes = await confirmAction('Delete this class? Students in this class will become unassigned.');
    if (!yes) return;
    const data = await API.delete(`/api/admin/classes/${id}`);
    if (data && data.success) { Toast.success(data.message); loadClasses(currentPage); }
    else if (data) Toast.error(data.message);
}

document.getElementById('class-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('save-btn');
    setLoading(btn, true);
    
    const id = document.getElementById('class-id').value;
    const subjectIds = [...document.querySelectorAll('.subject-cb:checked')].map(cb => parseInt(cb.value));
    
    const body = {
        name: document.getElementById('f-name').value,
        type: document.getElementById('f-type').value,
        semester_count: document.getElementById('f-semester-count').value,
        duration_weeks: document.getElementById('f-duration-weeks').value,
        capacity: document.getElementById('f-capacity').value,
        status: document.getElementById('f-status').value || 'active',
        subject_ids: subjectIds,
    };
    
    const data = id ? await API.put(`/api/admin/classes/${id}`, body) : await API.post('/api/admin/classes', body);
    setLoading(btn, false);
    
    if (data && data.success) {
        Toast.success(data.message);
        Modal.close('class-modal');
        loadClasses(currentPage);
    } else if (data) Toast.error(data.message);
});

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('search-input').addEventListener('input', debounce(() => loadClasses(1), 400));
    document.getElementById('filter-type').addEventListener('change', () => loadClasses(1));
    loadSubjectsList();
    loadClasses();
});
</script>
