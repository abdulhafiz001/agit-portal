<!-- Admin Exams Management -->
<div class="space-y-6">
    <div><h2 class="text-xl font-bold text-gray-900">Exam Management</h2>
        <p class="text-sm text-gray-500 mt-1">Approve, reject, start, stop and monitor exams</p></div>
    <div id="exam-stats" class="grid grid-cols-2 md:grid-cols-5 gap-4"></div>
    <div class="flex gap-2 flex-wrap">
        <button onclick="filterExams('')" class="exam-tab active px-3 py-1.5 text-sm rounded-lg border bg-gray-900 text-white">All</button>
        <button onclick="filterExams('pending')" class="exam-tab px-3 py-1.5 text-sm rounded-lg border text-gray-600 hover:bg-gray-50"><i class="fas fa-clock mr-1 text-yellow-500"></i>Pending</button>
        <button onclick="filterExams('approved')" class="exam-tab px-3 py-1.5 text-sm rounded-lg border text-gray-600 hover:bg-gray-50">Approved</button>
        <button onclick="filterExams('active')" class="exam-tab px-3 py-1.5 text-sm rounded-lg border text-gray-600 hover:bg-gray-50"><i class="fas fa-circle mr-1 text-green-500 text-xs"></i>Active</button>
        <button onclick="filterExams('completed')" class="exam-tab px-3 py-1.5 text-sm rounded-lg border text-gray-600 hover:bg-gray-50">Completed</button>
        <button onclick="filterExams('rejected')" class="exam-tab px-3 py-1.5 text-sm rounded-lg border text-gray-600 hover:bg-gray-50">Rejected</button>
    </div>
    <div id="exams-list" class="space-y-4"><div class="text-center py-8 text-gray-400">Loading exams...</div></div>
</div>

<!-- Exam Details Modal -->
<div id="exam-detail-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 max-h-[90vh] flex flex-col">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
            <h3 class="text-lg font-semibold text-gray-900">Exam Details & Monitoring</h3>
            <button onclick="Modal.close('exam-detail-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"><i class="fas fa-times"></i></button>
        </div>
        <div id="exam-detail-content" class="p-6 overflow-y-auto flex-1"></div>
    </div>
</div>

<!-- Reject Modal -->
<div id="reject-modal" class="fixed inset-0 z-[60] hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Exam</h3>
        <textarea id="reject-reason" rows="3" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm resize-none" placeholder="Reason (optional)..."></textarea>
        <div class="flex gap-3 mt-4">
            <button onclick="Modal.close('reject-modal')" class="flex-1 py-2.5 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Cancel</button>
            <button onclick="confirmReject()" class="flex-1 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">Reject</button>
        </div>
    </div>
</div>

<script>
let rejectingExamId = null;
function filterExams(status) { document.querySelectorAll('.exam-tab').forEach(t=>{t.classList.remove('active','bg-gray-900','text-white');t.classList.add('text-gray-600');}); event.target.closest('.exam-tab').classList.add('active','bg-gray-900','text-white'); event.target.closest('.exam-tab').classList.remove('text-gray-600'); loadExams(status); }

async function loadExams(status = '') {
    const data = await API.get('/api/admin/exams' + (status ? `?status=${status}` : '')); if (!data || !data.success) return;
    const exams = data.data;
    const stats = {total:exams.length,pending:0,active:0,completed:0,rejected:0}; exams.forEach(e=>{if(stats[e.status]!==undefined)stats[e.status]++;});
    if (!status) { document.getElementById('exam-stats').innerHTML = [{label:'Total',value:stats.total,icon:'fa-file-alt',color:'blue'},{label:'Pending',value:stats.pending,icon:'fa-clock',color:'yellow'},{label:'Active',value:stats.active,icon:'fa-play-circle',color:'green'},{label:'Completed',value:stats.completed,icon:'fa-check-circle',color:'purple'},{label:'Rejected',value:stats.rejected,icon:'fa-times-circle',color:'red'}].map(s=>`<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4"><div class="flex items-center gap-3"><div class="w-10 h-10 bg-${s.color}-100 rounded-lg flex items-center justify-center"><i class="fas ${s.icon} text-${s.color}-600"></i></div><div><div class="text-xl font-bold text-gray-900">${s.value}</div><div class="text-xs text-gray-500">${s.label}</div></div></div></div>`).join(''); }
    const container = document.getElementById('exams-list');
    if (exams.length === 0) { container.innerHTML = '<div class="text-center py-12 text-gray-400"><i class="fas fa-file-alt text-4xl mb-3 block"></i>No exams found</div>'; return; }
    container.innerHTML = exams.map(e => {
        const sc = {draft:'bg-gray-100 text-gray-700',pending:'bg-yellow-100 text-yellow-700',approved:'bg-blue-100 text-blue-700',active:'bg-green-100 text-green-700',completed:'bg-purple-100 text-purple-700',rejected:'bg-red-100 text-red-700'};
        let actions = `<button onclick="viewExamDetail(${e.id})" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100"><i class="fas fa-eye mr-1"></i>View</button>`;
        if (e.status==='pending') { actions += ` <button onclick="approveExam(${e.id})" class="px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 rounded-lg hover:bg-green-100"><i class="fas fa-check mr-1"></i>Approve</button> <button onclick="showRejectModal(${e.id})" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100"><i class="fas fa-times mr-1"></i>Reject</button>`; }
        if (e.status==='approved'||e.status==='completed') { actions += ` <button onclick="startExam(${e.id})" class="px-3 py-1.5 text-xs font-medium text-emerald-600 bg-emerald-50 rounded-lg hover:bg-emerald-100"><i class="fas fa-play mr-1"></i>Start</button>`; }
        if (e.status==='active') { actions += ` <button onclick="stopExam(${e.id})" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100"><i class="fas fa-stop mr-1"></i>Stop</button> <button onclick="viewExamDetail(${e.id})" class="px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100"><i class="fas fa-desktop mr-1"></i>Monitor</button>`; }
        const codeHtml = e.exam_code ? `<span class="text-xs font-mono bg-indigo-50 text-indigo-700 px-2 py-1 rounded-lg"><i class="fas fa-key mr-1"></i>Code: <strong>${e.exam_code}</strong></span>` : '';
        return `<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 ${e.status==='active'?'ring-2 ring-green-400':''}">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3"><div><h4 class="font-semibold text-gray-900">${escapeHtml(e.title)}</h4><p class="text-sm text-gray-500">${escapeHtml(e.subject_name)} (${escapeHtml(e.subject_code)}) &middot; ${escapeHtml(e.class_name)} &middot; by ${escapeHtml(e.lecturer_name)}</p></div>
                <span class="px-2.5 py-1 text-xs font-medium rounded-full ${sc[e.status]||'bg-gray-100'} self-start flex items-center gap-1">${e.status==='active'?'<span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>':''}${e.status.charAt(0).toUpperCase()+e.status.slice(1)}</span></div>
            <div class="flex flex-wrap gap-4 text-xs text-gray-500 mb-3"><span><i class="fas fa-clock mr-1"></i>${e.duration_minutes}m</span><span><i class="fas fa-question-circle mr-1"></i>${e.question_count} Qs</span><span><i class="fas fa-star mr-1"></i>${e.total_marks}m</span><span><i class="fas fa-users mr-1"></i>${e.total_students} eligible / ${e.attempt_count} attempts / ${e.submitted_count} submitted</span>${codeHtml}</div>
            <div class="flex flex-wrap gap-2">${actions}</div></div>`;
    }).join('');
}

async function approveExam(id) {
    const yes = await confirmAction('Approve this exam? A unique exam code will be generated.');
    if (!yes) return;
    const data = await API.post(`/api/admin/exams/${id}/approve`, { action: 'approve' });
    if (data && data.success) { Toast.success(data.message + (data.exam_code ? ' Code: ' + data.exam_code : '')); loadExams(); } else if (data) Toast.error(data.message);
}
function showRejectModal(id) { rejectingExamId = id; document.getElementById('reject-reason').value = ''; Modal.open('reject-modal'); }
async function confirmReject() { const data = await API.post(`/api/admin/exams/${rejectingExamId}/approve`, {action:'reject', reason: document.getElementById('reject-reason').value}); Modal.close('reject-modal'); if (data && data.success) { Toast.success(data.message); loadExams(); } else if (data) Toast.error(data.message); }
async function startExam(id) { const yes = await confirmAction('Start this exam? Students can take it immediately.'); if (!yes) return; const data = await API.post(`/api/admin/exams/${id}/start`); if (data && data.success) { Toast.success(data.message); loadExams(); } else if (data) Toast.error(data.message); }
async function stopExam(id) { const yes = await confirmAction('Stop exam? In-progress attempts will be auto-submitted.'); if (!yes) return; const data = await API.post(`/api/admin/exams/${id}/stop`); if (data && data.success) { Toast.success(data.message); loadExams(); } else if (data) Toast.error(data.message); }

async function viewExamDetail(id) {
    const data = await API.get(`/api/admin/exams/${id}`); if (!data || !data.success) return;
    const e = data.data; const questions = e.questions||[]; const attempts = e.attempts||[];
    const sb = {draft:'bg-gray-100 text-gray-700',pending:'bg-yellow-100 text-yellow-700',approved:'bg-blue-100 text-blue-700',active:'bg-green-100 text-green-700',completed:'bg-purple-100 text-purple-700',rejected:'bg-red-100 text-red-700'};
    let attemptsHtml = attempts.length === 0 ? '<p class="text-gray-400 text-sm">No attempts yet</p>' :
        `<table class="w-full text-sm"><thead class="bg-gray-50"><tr><th class="text-left px-3 py-2 text-xs font-semibold text-gray-500">Student</th><th class="text-left px-3 py-2 text-xs font-semibold text-gray-500">Matric</th><th class="text-center px-3 py-2 text-xs font-semibold text-gray-500">Status</th><th class="text-center px-3 py-2 text-xs font-semibold text-gray-500">Score</th><th class="text-center px-3 py-2 text-xs font-semibold text-gray-500">Actions</th></tr></thead><tbody>` +
        attempts.map(a => {
            const aSc = {in_progress:'text-blue-600',submitted:'text-green-600',timed_out:'text-orange-600',graded:'text-purple-600'};
            const continueBtn = (a.status==='submitted'||a.status==='timed_out') ? `<button onclick="generateContinueKey(${a.id})" class="px-2 py-1 text-xs text-amber-600 bg-amber-50 rounded-lg hover:bg-amber-100" title="Allow student to re-enter"><i class="fas fa-redo mr-1"></i>Continue Key</button>` : '';
            const keyDisplay = a.continue_key ? `<span class="text-xs font-mono bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded ml-1">${a.continue_key}</span>` : '';
            return `<tr class="border-t border-gray-50"><td class="px-3 py-2">${escapeHtml(a.student_name)}</td><td class="px-3 py-2 text-gray-500">${escapeHtml(a.matric_no||'-')}</td><td class="px-3 py-2 text-center"><span class="${aSc[a.status]||'text-gray-600'} text-xs font-medium">${a.status}</span>${keyDisplay}</td><td class="px-3 py-2 text-center font-bold">${a.score??'-'}/${a.total_marks}</td><td class="px-3 py-2 text-center">${continueBtn}</td></tr>`;
        }).join('') + '</tbody></table>';

    document.getElementById('exam-detail-content').innerHTML = `<div class="space-y-6">
        <div class="flex items-start justify-between gap-4"><div><h4 class="text-lg font-bold">${escapeHtml(e.title)}</h4><p class="text-sm text-gray-500">${escapeHtml(e.subject_name)} &middot; ${escapeHtml(e.class_name)} &middot; by ${escapeHtml(e.lecturer_name)}</p></div><span class="px-3 py-1 text-xs font-medium rounded-full ${sb[e.status]}">${e.status}</span></div>
        ${e.exam_code ? `<div class="p-3 bg-indigo-50 rounded-xl flex items-center justify-between"><div class="text-sm text-indigo-700"><i class="fas fa-key mr-2"></i><strong>Exam Code:</strong> <span class="font-mono text-lg">${e.exam_code}</span></div><button onclick="navigator.clipboard.writeText('${e.exam_code}');Toast.success('Code copied!')" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"><i class="fas fa-copy mr-1"></i>Copy</button></div>` : ''}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-gray-50 rounded-lg p-3 text-center"><div class="text-lg font-bold">${e.duration_minutes}</div><div class="text-xs text-gray-500">Minutes</div></div>
            <div class="bg-gray-50 rounded-lg p-3 text-center"><div class="text-lg font-bold">${questions.length}</div><div class="text-xs text-gray-500">Questions</div></div>
            <div class="bg-gray-50 rounded-lg p-3 text-center"><div class="text-lg font-bold">${e.total_students}</div><div class="text-xs text-gray-500">Eligible</div></div>
            <div class="bg-gray-50 rounded-lg p-3 text-center"><div class="text-lg font-bold text-blue-600">${attempts.filter(a=>a.status==='in_progress').length}</div><div class="text-xs text-gray-500">In Progress</div></div>
        </div>
        ${e.status==='active'?'<div class="p-3 bg-green-50 rounded-lg text-sm text-green-700 flex items-center gap-2"><span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>Exam is LIVE</div>':''}
        <div><h5 class="font-semibold text-gray-900 mb-3">Attempts (${attempts.length})</h5>${attemptsHtml}</div>
        <details><summary class="font-semibold text-gray-900 cursor-pointer">Questions (${questions.length})</summary><div class="space-y-2 mt-3">${questions.map((q,i)=>`<div class="p-3 bg-gray-50 rounded-lg text-sm"><span class="text-xs font-medium text-gray-500">Q${i+1} (${q.question_type}) - ${q.marks}m</span><p class="text-gray-800 mt-1">${escapeHtml(q.question_text)}</p>${q.correct_answer?`<span class="text-xs text-green-600">Answer: ${escapeHtml(q.correct_answer)}</span>`:''}</div>`).join('')||'<p class="text-gray-400 text-sm">No questions</p>'}</div></details>
    </div>`;
    Modal.open('exam-detail-modal');
}

async function generateContinueKey(attemptId) {
    const yes = await confirmAction('Generate a continue key so the student can re-enter this exam?');
    if (!yes) return;
    const data = await API.post(`/api/admin/exams/attempts/${attemptId}/continue-key`);
    if (data && data.success) { Toast.success(data.message + (data.continue_key ? ' Key: ' + data.continue_key : '')); } else if (data) Toast.error(data.message);
}

document.addEventListener('DOMContentLoaded', () => loadExams());
</script>
