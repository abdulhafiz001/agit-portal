<!-- Faculty Assignments -->
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><h2 class="text-xl font-bold text-gray-900">Assignments</h2>
            <p class="text-sm text-gray-500 mt-1">Create assignments and grade student submissions</p></div>
        <button onclick="showCreateAssignment()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium"><i class="fas fa-plus mr-2"></i>Create Assignment</button>
    </div>
    <div id="assignments-list" class="space-y-4"><div class="text-center py-8 text-gray-400">Loading...</div></div>
</div>

<!-- Create Modal -->
<div id="assign-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] flex flex-col">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Create Assignment</h3>
            <button onclick="Modal.close('assign-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"><i class="fas fa-times"></i></button>
        </div>
        <form id="assign-form" class="p-6 space-y-4 overflow-y-auto flex-1">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" id="as-title" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="Assignment title"></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Class <span class="text-red-500">*</span></label>
                    <select id="as-class" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Subject <span class="text-red-500">*</span></label>
                    <select id="as-subject" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></select></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Assignment Question <span class="text-red-500">*</span></label>
                <textarea id="as-desc" rows="6" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm resize-y" placeholder="Type the assignment question here..."></textarea></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                    <input type="datetime-local" id="as-due" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Total Marks</label>
                    <input type="number" id="as-marks" value="100" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Attachment <span class="text-gray-400 text-xs">(optional)</span></label>
                <input type="file" id="as-file" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                <p class="text-xs text-gray-400 mt-1">Upload supporting files (PDF, DOC, images). Max 20MB.</p></div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="Modal.close('assign-modal')" class="flex-1 py-2.5 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Cancel</button>
                <button type="submit" id="create-assign-btn" class="flex-1 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Submissions Modal -->
<div id="subs-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 max-h-[90vh] flex flex-col">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
            <h3 id="subs-title" class="text-lg font-semibold text-gray-900">Submissions</h3>
            <button onclick="Modal.close('subs-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"><i class="fas fa-times"></i></button>
        </div>
        <div id="subs-content" class="p-6 overflow-y-auto flex-1"></div>
    </div>
</div>

<script>
let myClasses = [], mySubjects = [];
async function loadOptions() {
    const data = await API.get('/api/faculty/scores/options');
    if (data && data.success) {
        myClasses = data.classes; mySubjects = data.subjects;
        document.getElementById('as-class').innerHTML = '<option value="">Select Class</option>' + myClasses.map(c => `<option value="${c.id}">${escapeHtml(c.name)}</option>`).join('');
        document.getElementById('as-subject').innerHTML = '<option value="">Select Subject</option>' + mySubjects.map(s => `<option value="${s.id}">${escapeHtml(s.name)} (${escapeHtml(s.code)})</option>`).join('');
    }
}
async function loadAssignments() {
    const data = await API.get('/api/faculty/assignments');
    if (!data || !data.success) return;
    const container = document.getElementById('assignments-list');
    if (data.data.length === 0) { container.innerHTML = '<div class="text-center py-12 text-gray-400"><i class="fas fa-tasks text-4xl mb-3 block"></i>No assignments yet</div>'; return; }
    container.innerHTML = data.data.map(a => {
        const overdue = a.due_date && new Date(a.due_date) < new Date();
        return `<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-start justify-between gap-3 mb-2">
                <div><h4 class="font-semibold text-gray-900">${escapeHtml(a.title)}</h4>
                    <p class="text-sm text-gray-500">${escapeHtml(a.subject_name)} (${escapeHtml(a.subject_code)}) &middot; ${escapeHtml(a.class_name)}</p></div>
                <div class="flex gap-2">
                    <button onclick="viewSubmissions(${a.id},'${escapeHtml(a.title)}')" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100"><i class="fas fa-eye mr-1"></i>Submissions (${a.submission_count}/${a.total_students})</button>
                    <button onclick="deleteAssignment(${a.id})" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100"><i class="fas fa-trash mr-1"></i></button>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 text-xs text-gray-400 mt-2">
                <span><i class="fas fa-star mr-1"></i>${a.total_marks} marks</span>
                ${a.due_date ? `<span class="${overdue ? 'text-red-500' : ''}"><i class="fas fa-calendar mr-1"></i>Due: ${formatDate(a.due_date)}</span>` : '<span>No deadline</span>'}
                <span><i class="fas fa-clock mr-1"></i>${formatDate(a.created_at)}</span>
            </div>
        </div>`;
    }).join('');
}
function showCreateAssignment() { document.getElementById('assign-form').reset(); Modal.open('assign-modal'); }
document.getElementById('assign-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('create-assign-btn'); setLoading(btn, true);
    const fd = new FormData();
    fd.append('title', document.getElementById('as-title').value);
    fd.append('class_id', document.getElementById('as-class').value);
    fd.append('subject_id', document.getElementById('as-subject').value);
    fd.append('description', document.getElementById('as-desc').value);
    fd.append('due_date', document.getElementById('as-due').value);
    fd.append('total_marks', document.getElementById('as-marks').value);
    if (document.getElementById('as-file').files[0]) fd.append('file', document.getElementById('as-file').files[0]);
    const data = await API.upload('/api/faculty/assignments', fd);
    setLoading(btn, false);
    if (data && data.success) { Toast.success(data.message); Modal.close('assign-modal'); loadAssignments(); } else if (data) Toast.error(data.message);
});
async function viewSubmissions(id, title) {
    const data = await API.get(`/api/faculty/assignments/${id}/submissions`);
    if (!data || !data.success) return;
    document.getElementById('subs-title').textContent = 'Submissions: ' + title;
    const subs = data.data;
    document.getElementById('subs-content').innerHTML = subs.length === 0 ? '<p class="text-gray-400 text-center py-8">No submissions yet</p>' :
        subs.map(s => `<div class="p-4 bg-gray-50 rounded-xl mb-3 border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <div><span class="font-semibold text-gray-900 text-sm">${escapeHtml(s.student_name)}</span> <span class="text-xs text-gray-400">${escapeHtml(s.matric_no||'')}</span></div>
                <span class="px-2 py-0.5 text-xs rounded-full ${s.status==='graded'?'bg-green-100 text-green-700':'bg-yellow-100 text-yellow-700'}">${s.status}</span>
            </div>
            ${s.answer_text ? `<p class="text-sm text-gray-700 mb-2 bg-white p-3 rounded-lg">${escapeHtml(s.answer_text)}</p>` : ''}
            ${s.file_name ? `<p class="text-xs text-blue-600 mb-2"><i class="fas fa-paperclip mr-1"></i>${escapeHtml(s.file_name)}</p>` : ''}
            <div class="flex items-center gap-3 mt-2">
                <input type="number" value="${s.score||''}" min="0" placeholder="Score" class="w-24 px-2 py-1.5 text-sm border border-gray-200 rounded-lg" id="sub-score-${s.id}">
                <input type="text" value="${s.feedback||''}" placeholder="Feedback" class="flex-1 px-2 py-1.5 text-sm border border-gray-200 rounded-lg" id="sub-fb-${s.id}">
                <button onclick="gradeSubmission(${s.id})" class="px-3 py-1.5 text-xs font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">Grade</button>
            </div>
        </div>`).join('');
    Modal.open('subs-modal');
}
async function gradeSubmission(id) {
    const score = document.getElementById(`sub-score-${id}`).value;
    const feedback = document.getElementById(`sub-fb-${id}`).value;
    const data = await API.post(`/api/faculty/assignments/submissions/${id}/grade`, { score, feedback });
    if (data && data.success) Toast.success(data.message); else if (data) Toast.error(data.message);
}
async function deleteAssignment(id) {
    const yes = await confirmAction('Delete this assignment?'); if (!yes) return;
    const data = await API.delete(`/api/faculty/assignments/${id}`);
    if (data && data.success) { Toast.success(data.message); loadAssignments(); }
}
document.addEventListener('DOMContentLoaded', () => { loadOptions(); loadAssignments(); });
</script>
