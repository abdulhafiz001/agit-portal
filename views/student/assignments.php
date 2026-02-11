<!-- Student Assignments -->
<div class="space-y-6">
    <div><h2 class="text-xl font-bold text-gray-900">Assignments</h2>
        <p class="text-sm text-gray-500 mt-1">View assignments and submit your work</p></div>
    <div id="assignments-list" class="space-y-4"><div class="text-center py-8 text-gray-400">Loading...</div></div>
</div>

<!-- Submit Modal -->
<div id="submit-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 id="submit-title" class="text-lg font-semibold text-gray-900">Submit Assignment</h3>
            <button onclick="Modal.close('submit-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"><i class="fas fa-times"></i></button>
        </div>
        <form id="submit-form" class="p-6 space-y-4">
            <input type="hidden" id="sub-assignment-id">
            <div id="submit-question"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Your Answer</label>
                <textarea id="sub-answer" rows="5" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Write your answer here..."></textarea></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Or Upload File</label>
                <input type="file" id="sub-file" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                <p class="text-xs text-gray-400 mt-1">PDF, DOC, ZIP, images accepted. Max 20MB.</p></div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="Modal.close('submit-modal')" class="flex-1 py-2.5 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Cancel</button>
                <button type="submit" id="submit-btn" class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Submit</button>
            </div>
        </form>
    </div>
</div>

<script>
async function loadAssignments() {
    const data = await API.get('/api/student/assignments');
    if (!data || !data.success) return;
    const container = document.getElementById('assignments-list');
    if (data.data.length === 0) { container.innerHTML = '<div class="text-center py-12 text-gray-400"><i class="fas fa-tasks text-4xl mb-3 block"></i>No assignments available</div>'; return; }
    container.innerHTML = data.data.map(a => {
        const submitted = a.my_status;
        const overdue = a.due_date && new Date(a.due_date) < new Date();
        let actionHtml = '';
        if (submitted === 'graded') {
            actionHtml = `<div class="mt-3 p-3 bg-green-50 rounded-lg text-sm"><div class="flex items-center justify-between"><span class="text-green-700 font-semibold">Score: ${a.my_score}/${a.total_marks}</span><span class="text-green-600 text-xs">Graded</span></div>${a.my_feedback ? `<p class="text-green-700 text-xs mt-1">Feedback: ${escapeHtml(a.my_feedback)}</p>` : ''}</div>`;
        } else if (submitted === 'submitted') {
            actionHtml = `<div class="mt-3 p-3 bg-blue-50 rounded-lg text-sm text-blue-700"><i class="fas fa-check mr-1"></i>Submitted - Awaiting grading</div>`;
        } else if (!overdue) {
            actionHtml = `<button onclick='showSubmit(${a.id},${JSON.stringify(a.title)},${JSON.stringify(a.description||"")})' class="mt-3 w-full py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"><i class="fas fa-paper-plane mr-2"></i>Submit Answer</button>`;
        } else {
            actionHtml = `<div class="mt-3 p-3 bg-red-50 rounded-lg text-sm text-red-700"><i class="fas fa-clock mr-1"></i>Deadline passed - Not submitted</div>`;
        }
        return `<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h4 class="font-semibold text-gray-900 mb-1">${escapeHtml(a.title)}</h4>
            <p class="text-sm text-gray-500">${escapeHtml(a.subject_name)} (${escapeHtml(a.subject_code)}) &middot; by ${escapeHtml(a.lecturer_name)}</p>
            ${a.description ? `<div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-100"><p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1"><i class="fas fa-question-circle mr-1"></i>Assignment Question</p><p class="text-sm text-gray-700">${escapeHtml(a.description)}</p></div>` : ''}
            <div class="flex flex-wrap gap-3 text-xs text-gray-400 mt-3">
                <span><i class="fas fa-star mr-1"></i>${a.total_marks} marks</span>
                ${a.due_date ? `<span class="${overdue?'text-red-500':''}"><i class="fas fa-calendar mr-1"></i>Due: ${formatDate(a.due_date)}</span>` : ''}
                ${a.file_name ? `<a href="${APP_URL}/uploads/${a.file_path}" target="_blank" class="text-blue-600 hover:underline"><i class="fas fa-paperclip mr-1"></i>${escapeHtml(a.file_name)}</a>` : ''}
            </div>
            ${actionHtml}
        </div>`;
    }).join('');
}
function showSubmit(id, title, description) {
    document.getElementById('sub-assignment-id').value = id;
    document.getElementById('submit-title').textContent = 'Submit: ' + title;
    document.getElementById('sub-answer').value = '';
    document.getElementById('sub-file').value = '';
    const qEl = document.getElementById('submit-question');
    if (qEl) qEl.innerHTML = description ? `<div class="p-3 bg-blue-50 rounded-lg border border-blue-100 mb-4"><p class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1"><i class="fas fa-question-circle mr-1"></i>Assignment Question</p><p class="text-sm text-gray-700">${escapeHtml(description)}</p></div>` : '';
    Modal.open('submit-modal');
}
document.getElementById('submit-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('submit-btn'); setLoading(btn, true);
    const id = document.getElementById('sub-assignment-id').value;
    const fd = new FormData();
    fd.append('answer_text', document.getElementById('sub-answer').value);
    if (document.getElementById('sub-file').files[0]) fd.append('file', document.getElementById('sub-file').files[0]);
    const data = await API.upload(`/api/student/assignments/${id}/submit`, fd);
    setLoading(btn, false);
    if (data && data.success) { Toast.success(data.message); Modal.close('submit-modal'); loadAssignments(); } else if (data) Toast.error(data.message);
});
document.addEventListener('DOMContentLoaded', loadAssignments);
</script>
