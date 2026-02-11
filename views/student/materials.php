<!-- Student Materials -->
<div class="space-y-6">
    <div><h2 class="text-xl font-bold text-gray-900">Study Materials</h2>
        <p class="text-sm text-gray-500 mt-1">Download materials shared by your lecturers</p></div>
    <div id="materials-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="col-span-full text-center py-8 text-gray-400">Loading...</div>
    </div>
</div>
<script>
async function loadMaterials() {
    const data = await API.get('/api/student/materials');
    if (!data || !data.success) return;
    const grid = document.getElementById('materials-list');
    if (data.data.length === 0) {
        grid.innerHTML = '<div class="col-span-full text-center py-12 text-gray-400"><i class="fas fa-folder-open text-4xl mb-3 block"></i>No materials available yet</div>';
        return;
    }
    grid.innerHTML = data.data.map(m => {
        const icon = m.file_type?.includes('pdf') ? 'fa-file-pdf text-red-500' : m.file_type?.includes('image') ? 'fa-file-image text-blue-500' : 'fa-file text-gray-500';
        const size = m.file_size ? (m.file_size / 1024 / 1024).toFixed(1) + ' MB' : '';
        return `<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-start gap-3 mb-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas ${icon}"></i></div>
                <div class="flex-1 min-w-0"><h4 class="font-semibold text-gray-900 text-sm truncate">${escapeHtml(m.title)}</h4>
                    <p class="text-xs text-gray-400">${escapeHtml(m.subject_name)} (${escapeHtml(m.subject_code)})</p></div>
            </div>
            ${m.description ? `<p class="text-xs text-gray-500 mb-3">${escapeHtml(m.description)}</p>` : ''}
            <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                <span class="text-xs text-gray-400">By ${escapeHtml(m.lecturer_name)} &middot; ${size}</span>
                <a href="${APP_URL}/api/student/materials/${m.id}/download" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700"><i class="fas fa-download mr-1"></i>Download</a>
            </div>
        </div>`;
    }).join('');
}
document.addEventListener('DOMContentLoaded', loadMaterials);
</script>
