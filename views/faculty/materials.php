<!-- Faculty Materials -->
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Study Materials</h2>
            <p class="text-sm text-gray-500 mt-1">Upload materials for your students to download</p>
        </div>
        <button onclick="Modal.open('upload-modal')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
            <i class="fas fa-upload mr-2"></i>Upload Material
        </button>
    </div>

    <div id="materials-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="col-span-full text-center py-8 text-gray-400">Loading...</div>
    </div>
</div>

<!-- Upload Modal -->
<div id="upload-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Upload Study Material</h3>
            <button onclick="Modal.close('upload-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"><i class="fas fa-times"></i></button>
        </div>
        <form id="upload-form" class="p-6 space-y-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" id="f-title" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Lecture Notes - Week 1">
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Class <span class="text-red-500">*</span></label>
                <select id="f-class" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none"></select>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Subject <span class="text-red-500">*</span></label>
                <select id="f-subject" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none"></select>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="f-desc" rows="2" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">File <span class="text-red-500">*</span></label>
                <input type="file" id="f-file" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                <p class="text-xs text-gray-400 mt-1">Max 20MB. PDF, DOC, ZIP, images accepted.</p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="Modal.close('upload-modal')" class="flex-1 py-2.5 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Cancel</button>
                <button type="submit" id="upload-btn" class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Upload</button>
            </div>
        </form>
    </div>
</div>

<script>
let myClasses = [], mySubjects = [];
async function loadOptions() {
    const data = await API.get('/api/faculty/scores/options');
    if (data && data.success) {
        myClasses = data.classes; mySubjects = data.subjects;
        document.getElementById('f-class').innerHTML = '<option value="">Select Class</option>' + myClasses.map(c => `<option value="${c.id}">${escapeHtml(c.name)}</option>`).join('');
        document.getElementById('f-subject').innerHTML = '<option value="">Select Subject</option>' + mySubjects.map(s => `<option value="${s.id}">${escapeHtml(s.name)} (${escapeHtml(s.code)})</option>`).join('');
    }
}

async function loadMaterials() {
    const data = await API.get('/api/faculty/materials');
    if (!data || !data.success) return;
    const grid = document.getElementById('materials-list');
    if (data.data.length === 0) {
        grid.innerHTML = '<div class="col-span-full text-center py-12 text-gray-400"><i class="fas fa-folder-open text-4xl mb-3 block"></i>No materials uploaded yet</div>';
        return;
    }
    grid.innerHTML = data.data.map(m => {
        const icon = m.file_type?.includes('pdf') ? 'fa-file-pdf text-red-500' : m.file_type?.includes('image') ? 'fa-file-image text-blue-500' : 'fa-file text-gray-500';
        const size = m.file_size ? (m.file_size / 1024 / 1024).toFixed(1) + ' MB' : 'N/A';
        return `<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center"><i class="fas ${icon}"></i></div>
                <button onclick="deleteMaterial(${m.id})" class="p-1.5 text-gray-400 hover:text-red-600 rounded"><i class="fas fa-trash text-xs"></i></button>
            </div>
            <h4 class="font-semibold text-gray-900 text-sm mb-1">${escapeHtml(m.title)}</h4>
            <p class="text-xs text-gray-400 mb-2">${escapeHtml(m.subject_name)} &middot; ${escapeHtml(m.class_name)}</p>
            <div class="flex items-center justify-between text-xs text-gray-400 pt-2 border-t border-gray-50">
                <span>${size}</span><span><i class="fas fa-download mr-1"></i>${m.download_count} downloads</span>
            </div>
        </div>`;
    }).join('');
}

async function deleteMaterial(id) {
    const yes = await confirmAction('Delete this material?');
    if (!yes) return;
    const data = await API.delete(`/api/faculty/materials/${id}`);
    if (data && data.success) { Toast.success(data.message); loadMaterials(); } else if (data) Toast.error(data.message);
}

document.getElementById('upload-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('upload-btn');
    setLoading(btn, true);
    const fd = new FormData();
    fd.append('title', document.getElementById('f-title').value);
    fd.append('class_id', document.getElementById('f-class').value);
    fd.append('subject_id', document.getElementById('f-subject').value);
    fd.append('description', document.getElementById('f-desc').value);
    fd.append('file', document.getElementById('f-file').files[0]);
    const data = await API.upload('/api/faculty/materials', fd);
    setLoading(btn, false);
    if (data && data.success) { Toast.success(data.message); Modal.close('upload-modal'); document.getElementById('upload-form').reset(); loadMaterials(); }
    else if (data) Toast.error(data.message);
});

document.addEventListener('DOMContentLoaded', () => { loadOptions(); loadMaterials(); });
</script>
