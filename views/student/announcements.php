<!-- Student Announcements -->
<div class="space-y-6">
    <div><h2 class="text-xl font-bold text-gray-900">Announcements</h2>
        <p class="text-sm text-gray-500 mt-1">Stay updated with the latest announcements</p></div>
    <div id="announcements-list" class="space-y-4">
        <div class="text-center py-8 text-gray-400">Loading...</div>
    </div>
</div>
<script>
async function loadAnnouncements() {
    const data = await API.get('/api/student/announcements');
    const container = document.getElementById('announcements-list');
    if (!data?.success || !data.data.length) { container.innerHTML = '<div class="text-center py-12 text-gray-400"><i class="fas fa-bullhorn text-4xl mb-3 block"></i>No announcements</div>'; return; }
    const pColors = { normal: 'border-blue-200 bg-blue-50', important: 'border-amber-300 bg-amber-50', urgent: 'border-red-400 bg-red-50' };
    const pIcons = { normal: 'fa-info-circle text-blue-600', important: 'fa-exclamation-triangle text-amber-600', urgent: 'fa-exclamation-circle text-red-600' };
    container.innerHTML = data.data.map(a => `
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="border-l-4 ${pColors[a.priority] || 'border-blue-200 bg-blue-50'} px-4 py-2">
                <div class="flex items-center gap-2">
                    <i class="fas ${pIcons[a.priority] || 'fa-info-circle text-blue-600'}"></i>
                    <span class="text-xs font-semibold uppercase tracking-wider ${a.priority==='urgent'?'text-red-700':a.priority==='important'?'text-amber-700':'text-blue-700'}">${a.priority}</span>
                </div>
            </div>
            <div class="p-5">
                <h3 class="font-semibold text-gray-900 text-base">${escapeHtml(a.title)}</h3>
                <p class="text-sm text-gray-600 mt-2 whitespace-pre-line">${escapeHtml(a.content)}</p>
                <div class="flex items-center gap-3 mt-3 text-xs text-gray-400">
                    <span><i class="fas fa-clock mr-1"></i>${formatDate(a.created_at)}</span>
                    <span><i class="fas fa-user mr-1"></i>${escapeHtml(a.author_name || 'Admin')}</span>
                </div>
            </div>
        </div>
    `).join('');
}
document.addEventListener('DOMContentLoaded', loadAnnouncements);
</script>
