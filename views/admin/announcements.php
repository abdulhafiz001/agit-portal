<!-- Admin Announcements -->
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><h2 class="text-xl font-bold text-gray-900">Announcements</h2>
            <p class="text-sm text-gray-500 mt-1">Post announcements visible to students and lecturers</p></div>
        <button onclick="showCreate()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium"><i class="fas fa-plus mr-2"></i>New Announcement</button>
    </div>
    <div id="announcements-list" class="space-y-4"><div class="text-center py-8 text-gray-400">Loading...</div></div>
</div>

<!-- Create/Edit Modal -->
<div id="ann-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 id="ann-modal-title" class="text-lg font-semibold text-gray-900">New Announcement</h3>
            <button onclick="Modal.close('ann-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-6 space-y-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" id="a-title" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Announcement title"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Content <span class="text-red-500">*</span></label>
                <textarea id="a-content" rows="4" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none" placeholder="Write your announcement..."></textarea></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Target Audience</label>
                    <select id="a-target" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                        <option value="all">Everyone</option><option value="students">Students Only</option><option value="lecturers">Lecturers Only</option>
                    </select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <select id="a-priority" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                        <option value="normal">Normal</option><option value="important">Important</option><option value="urgent">Urgent</option>
                    </select></div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="Modal.close('ann-modal')" class="flex-1 py-2.5 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Cancel</button>
                <button type="button" onclick="saveAnnouncement()" id="save-ann-btn" class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Post</button>
            </div>
        </div>
    </div>
</div>

<script>
let editingAnnId = null;
async function loadAnnouncements() {
    const data = await API.get('/api/admin/announcements');
    if (!data || !data.success) return;
    const container = document.getElementById('announcements-list');
    if (data.data.length === 0) {
        container.innerHTML = '<div class="text-center py-12 text-gray-400"><i class="fas fa-bullhorn text-4xl mb-3 block"></i>No announcements yet</div>';
        return;
    }
    const pColors = { normal: 'bg-blue-100 text-blue-700', important: 'bg-amber-100 text-amber-700', urgent: 'bg-red-100 text-red-700' };
    const tIcons = { all: 'fa-globe', students: 'fa-user-graduate', lecturers: 'fa-chalkboard-teacher' };
    container.innerHTML = data.data.map(a => `<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 ${!a.is_active ? 'opacity-50' : ''}">
        <div class="flex items-start justify-between gap-3 mb-2">
            <div class="flex items-center gap-2 flex-wrap"><h4 class="font-semibold text-gray-900">${escapeHtml(a.title)}</h4>
                <span class="px-2 py-0.5 text-xs rounded-full ${pColors[a.priority]}">${a.priority}</span>
                <span class="text-xs text-gray-400"><i class="fas ${tIcons[a.target_audience]} mr-1"></i>${a.target_audience}</span></div>
            <div class="flex gap-1"><button onclick="editAnnouncement(${a.id},'${escapeHtml(a.title)}','${escapeHtml(a.content).replace(/'/g,"\\'")}','${a.target_audience}','${a.priority}')" class="p-1.5 text-gray-400 hover:text-blue-600"><i class="fas fa-edit text-xs"></i></button>
                <button onclick="deleteAnnouncement(${a.id})" class="p-1.5 text-gray-400 hover:text-red-600"><i class="fas fa-trash text-xs"></i></button></div>
        </div>
        <p class="text-sm text-gray-600 whitespace-pre-line mb-2">${escapeHtml(a.content)}</p>
        <p class="text-xs text-gray-400">${a.author_name || 'Admin'} &middot; ${formatDate(a.created_at)}</p>
    </div>`).join('');
}
function showCreate() { editingAnnId = null; document.getElementById('ann-modal-title').textContent = 'New Announcement'; ['a-title','a-content'].forEach(id=>document.getElementById(id).value=''); document.getElementById('a-target').value='all'; document.getElementById('a-priority').value='normal'; Modal.open('ann-modal'); }
function editAnnouncement(id,title,content,target,priority) { editingAnnId = id; document.getElementById('ann-modal-title').textContent = 'Edit Announcement'; document.getElementById('a-title').value=title; document.getElementById('a-content').value=content; document.getElementById('a-target').value=target; document.getElementById('a-priority').value=priority; Modal.open('ann-modal'); }
async function saveAnnouncement() {
    const btn = document.getElementById('save-ann-btn'); setLoading(btn, true);
    const payload = { title: document.getElementById('a-title').value, content: document.getElementById('a-content').value, target_audience: document.getElementById('a-target').value, priority: document.getElementById('a-priority').value };
    const data = editingAnnId ? await API.put(`/api/admin/announcements/${editingAnnId}`, {...payload, is_active: 1}) : await API.post('/api/admin/announcements', payload);
    setLoading(btn, false);
    if (data && data.success) { Toast.success(data.message); Modal.close('ann-modal'); loadAnnouncements(); } else if (data) Toast.error(data.message);
}
async function deleteAnnouncement(id) {
    const yes = await confirmAction('Delete this announcement?'); if (!yes) return;
    const data = await API.delete(`/api/admin/announcements/${id}`);
    if (data && data.success) { Toast.success(data.message); loadAnnouncements(); } else if (data) Toast.error(data.message);
}
document.addEventListener('DOMContentLoaded', loadAnnouncements);
</script>
