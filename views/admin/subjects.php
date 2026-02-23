<!-- Courses Management -->
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Courses Management</h2>
            <p class="text-sm text-gray-500 mt-1">Manage all courses</p>
        </div>
        <button onclick="openAddSubject()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium w-full sm:w-auto">
            <i class="fas fa-plus mr-2"></i>Add Course
        </button>
    </div>

    <!-- Search & View Toggle -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="search-input" placeholder="Search by name or code..." 
                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>
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

    <!-- Table View (hidden) -->
    <div id="table-view" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hidden">
        <div class="table-responsive overflow-x-auto overflow-y-auto max-h-[calc(100vh-280px)] sm:max-h-[65vh]">
            <table class="data-table" style="min-width: 860px;">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Code</th>
                        <th>Duration</th>
                        <th>Classes</th>
                        <th>Lecturers</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="subjects-table">
                    <tr><td colspan="7" class="text-center py-8 text-gray-400">Loading...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="pagination" class="px-4 py-3 border-t border-gray-100 flex items-center justify-between"></div>
    </div>
</div>

<!-- Add/Edit Course Modal -->
<div id="subject-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white rounded-t-2xl z-10">
            <h3 id="modal-title" class="text-lg font-semibold text-gray-900">Add Course</h3>
            <button onclick="Modal.close('subject-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"><i class="fas fa-times"></i></button>
        </div>
        <form id="subject-form" class="p-6 space-y-4" enctype="multipart/form-data">
            <input type="hidden" id="subject-id" name="subject_id" value="">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Course Name <span class="text-red-500">*</span></label>
                    <input type="text" id="f-name" name="name" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Mathematics">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Course Code <span class="text-red-500">*</span></label>
                    <input type="text" id="f-code" name="code" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none uppercase" placeholder="MATH101">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="f-description" name="description" rows="2" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none" placeholder="Brief description..."></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                <input type="text" id="f-duration" name="duration" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g. 12 weeks, 6 months">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Course Image</label>
                <input type="file" id="f-image" name="image" accept="image/*" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" onchange="previewImage(this)">
                <div id="image-preview" class="mt-2 hidden">
                    <img id="image-preview-img" src="" alt="Preview" class="max-h-32 rounded-lg border border-gray-200 object-cover">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Topics</label>
                <div id="topics-list" class="space-y-2"></div>
                <button type="button" onclick="addTopic()" class="mt-2 text-sm text-blue-600 hover:text-blue-700 flex items-center gap-1"><i class="fas fa-plus"></i> Add Topic</button>
            </div>
            <div id="status-field" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="f-status" name="status" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="Modal.close('subject-modal')" class="flex-1 py-2.5 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Cancel</button>
                <button type="submit" id="save-btn" class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Save Course</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentPage = 1;
let currentView = 'grid';
let subjectsData = [];

function setView(view) {
    currentView = view;
    document.getElementById('grid-view').classList.toggle('hidden', view !== 'grid');
    document.getElementById('table-view').classList.toggle('hidden', view !== 'table');
    document.getElementById('view-grid-btn').className = `px-3 py-2 text-sm ${view === 'grid' ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50'}`;
    document.getElementById('view-table-btn').className = `px-3 py-2 text-sm ${view === 'table' ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50'}`;
    if (subjectsData.length) renderSubjects(subjectsData);
}

function addTopic(title = '') {
    const list = document.getElementById('topics-list');
    const id = 'topic-' + Date.now();
    const div = document.createElement('div');
    div.className = 'flex gap-2 items-center';
    div.innerHTML = `<input type="text" class="topic-input flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Topic title" value="${escapeHtml(title)}"><button type="button" onclick="this.parentElement.remove()" class="p-2 text-red-500 hover:bg-red-50 rounded-lg"><i class="fas fa-times text-xs"></i></button>`;
    list.appendChild(div);
}

function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const img = document.getElementById('image-preview-img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; preview.classList.remove('hidden'); };
        reader.readAsDataURL(input.files[0]);
    } else { preview.classList.add('hidden'); }
}

function getTopics() {
    return [...document.querySelectorAll('.topic-input')].map(i => i.value.trim()).filter(Boolean);
}

function renderSubjects(subjects) {
    const grid = document.getElementById('grid-view');
    const tbody = document.getElementById('subjects-table');
    if (subjects.length === 0) {
        grid.innerHTML = '<div class="col-span-full text-center py-12 text-gray-400"><i class="fas fa-book text-4xl mb-3 block"></i>No courses found</div>';
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-400">No courses found</td></tr>';
        return;
    }
    grid.innerHTML = subjects.map(s => {
        const img = s.image ? `<img src="${APP_URL}/uploads/${s.image}" class="w-full h-32 object-cover rounded-t-xl">` : `<div class="w-full h-32 bg-amber-100 rounded-t-xl flex items-center justify-center"><i class="fas fa-book text-4xl text-amber-600"></i></div>`;
        return `<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
            ${img}
            <div class="p-4">
                <h4 class="font-semibold text-gray-900 text-sm">${escapeHtml(s.name)}</h4>
                <p class="text-xs text-gray-500 font-mono">${escapeHtml(s.code)}</p>
                ${s.duration ? `<p class="text-xs text-blue-600 mt-1"><i class="fas fa-clock mr-1"></i>${escapeHtml(s.duration)}</p>` : ''}
                <p class="text-xs text-gray-500 mt-1">${s.class_count} classes · ${s.lecturer_count} lecturers</p>
                <span class="inline-block mt-2 px-2 py-0.5 text-xs rounded-full ${s.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'}">${s.status}</span>
            </div>
            <div class="flex justify-center gap-1 p-3 border-t border-gray-50">
                <button onclick="editSubject(${s.id})" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg" title="Edit"><i class="fas fa-edit text-xs"></i></button>
                <button onclick="deleteSubject(${s.id})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg" title="Delete"><i class="fas fa-trash text-xs"></i></button>
            </div>
        </div>`;
    }).join('');
    tbody.innerHTML = subjects.map(s => `
        <tr>
            <td>
                <div class="flex items-center gap-3">
                    ${s.image ? `<img src="${APP_URL}/uploads/${s.image}" class="w-10 h-10 rounded-lg object-cover">` : `<div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600"><i class="fas fa-book text-xs"></i></div>`}
                    <div>
                        <div class="font-medium text-gray-900 text-sm">${escapeHtml(s.name)}</div>
                        ${s.description ? `<div class="text-xs text-gray-400 truncate max-w-[200px]">${escapeHtml(s.description)}</div>` : ''}
                    </div>
                </div>
            </td>
            <td><span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">${escapeHtml(s.code)}</span></td>
            <td><span class="text-sm">${s.duration || '-'}</span></td>
            <td><span class="text-sm">${s.class_count} classes</span></td>
            <td><span class="text-sm">${s.lecturer_count} lecturers</span></td>
            <td><span class="badge ${s.status === 'active' ? 'badge-success' : 'badge-gray'}">${s.status}</span></td>
            <td class="text-right">
                <div class="flex items-center justify-end gap-1">
                    <button onclick="editSubject(${s.id})" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg" title="Edit"><i class="fas fa-edit text-xs"></i></button>
                    <button onclick="deleteSubject(${s.id})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg" title="Delete"><i class="fas fa-trash text-xs"></i></button>
                </div>
            </td>
        </tr>
    `).join('');
}

async function loadSubjects(page = 1) {
    currentPage = page;
    const search = document.getElementById('search-input').value;
    let url = `/api/admin/subjects?page=${page}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    
    const data = await API.get(url);
    if (!data || !data.success) return;
    
    subjectsData = data.data;
    renderSubjects(data.data);
    
    const p = data.pagination;
    if (p && p.total_pages > 1) {
        let html = `<span class="text-xs text-gray-500">Showing ${p.offset+1}-${Math.min(p.offset+p.per_page, p.total)} of ${p.total}</span><div class="flex gap-1 flex-wrap">`;
        for (let i = 1; i <= p.total_pages; i++) {
            html += `<button onclick="loadSubjects(${i})" class="px-3 py-1 text-xs rounded-lg ${i === p.current_page ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}">${i}</button>`;
        }
        document.getElementById('pagination').innerHTML = html + '</div>';
    } else {
        document.getElementById('pagination').innerHTML = '';
    }
}

function openAddSubject() {
    document.getElementById('modal-title').textContent = 'Add Course';
    document.getElementById('subject-id').value = '';
    document.getElementById('subject-form').reset();
    document.getElementById('status-field').classList.add('hidden');
    document.getElementById('image-preview').classList.add('hidden');
    document.getElementById('topics-list').innerHTML = '';
    addTopic();
    Modal.open('subject-modal');
}

async function editSubject(id) {
    const data = await API.get(`/api/admin/subjects/${id}`);
    if (!data || !data.success) return;
    const s = data.data;
    
    document.getElementById('modal-title').textContent = 'Edit Course';
    document.getElementById('subject-id').value = s.id;
    document.getElementById('f-name').value = s.name;
    document.getElementById('f-code').value = s.code;
    document.getElementById('f-description').value = s.description || '';
    document.getElementById('f-duration').value = s.duration || '';
    document.getElementById('f-status').value = s.status;
    document.getElementById('f-image').value = '';
    document.getElementById('status-field').classList.remove('hidden');
    
    document.getElementById('topics-list').innerHTML = '';
    if (s.topics && s.topics.length) {
        s.topics.forEach(t => addTopic(t.topic_title));
    } else {
        addTopic();
    }
    
    const preview = document.getElementById('image-preview');
    const img = document.getElementById('image-preview-img');
    if (s.image) {
        img.src = APP_URL + '/uploads/' + s.image;
        preview.classList.remove('hidden');
    } else {
        preview.classList.add('hidden');
    }
    
    Modal.open('subject-modal');
}

async function deleteSubject(id) {
    const yes = await confirmAction('Are you sure you want to delete this course?');
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
    const formData = new FormData();
    formData.append('name', document.getElementById('f-name').value);
    formData.append('code', document.getElementById('f-code').value);
    formData.append('description', document.getElementById('f-description').value);
    formData.append('duration', document.getElementById('f-duration').value);
    formData.append('status', document.getElementById('f-status').value || 'active');
    formData.append('topics', JSON.stringify(getTopics()));
    if (document.getElementById('f-image').files[0]) {
        formData.append('image', document.getElementById('f-image').files[0]);
    }
    
    let data;
    if (id) {
        formData.append('subject_id', id);
        data = await API.upload(`/api/admin/subjects/${id}`, formData);
    } else {
        data = await API.upload('/api/admin/subjects', formData);
    }
    
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
