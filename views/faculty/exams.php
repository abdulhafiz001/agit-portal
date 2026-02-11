<!-- Faculty Exams Management -->
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><h2 class="text-xl font-bold text-gray-900">Exams</h2>
            <p class="text-sm text-gray-500 mt-1">Create exams, add questions, and submit for approval</p></div>
        <button onclick="showCreateExam()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium"><i class="fas fa-plus mr-2"></i>Create Exam</button>
    </div>
    <div class="flex gap-2 flex-wrap">
        <button onclick="filterExams('')" class="exam-tab active px-3 py-1.5 text-sm rounded-lg border bg-gray-900 text-white">All</button>
        <button onclick="filterExams('draft')" class="exam-tab px-3 py-1.5 text-sm rounded-lg border text-gray-600 hover:bg-gray-50">Draft</button>
        <button onclick="filterExams('pending')" class="exam-tab px-3 py-1.5 text-sm rounded-lg border text-gray-600 hover:bg-gray-50">Pending</button>
        <button onclick="filterExams('approved')" class="exam-tab px-3 py-1.5 text-sm rounded-lg border text-gray-600 hover:bg-gray-50">Approved</button>
        <button onclick="filterExams('active')" class="exam-tab px-3 py-1.5 text-sm rounded-lg border text-gray-600 hover:bg-gray-50">Active</button>
        <button onclick="filterExams('completed')" class="exam-tab px-3 py-1.5 text-sm rounded-lg border text-gray-600 hover:bg-gray-50">Completed</button>
    </div>
    <div id="exams-list" class="space-y-4"><div class="text-center py-8 text-gray-400">Loading...</div></div>
</div>

<!-- Create/Edit Exam Modal -->
<div id="exam-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 max-h-[90vh] flex flex-col">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
            <h3 id="exam-modal-title" class="text-lg font-semibold text-gray-900">Create Exam</h3>
            <button onclick="Modal.close('exam-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-6 overflow-y-auto flex-1 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Exam Title <span class="text-red-500">*</span></label>
                    <input type="text" id="e-title" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="Mid-Term Exam"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Class <span class="text-red-500">*</span></label>
                    <select id="e-class" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Subject <span class="text-red-500">*</span></label>
                    <select id="e-subject" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Exam Type <span class="text-red-500">*</span></label>
                    <select id="e-type" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                        <option value="objective">Objective (MCQ, T/F, Fill-in)</option><option value="theory">Theory</option><option value="mixed">Mixed</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes) <span class="text-red-500">*</span></label>
                    <input type="number" id="e-duration" value="60" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Total Marks</label>
                    <input type="number" id="e-total" value="100" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Marks Per Question (Objective)</label>
                    <input type="number" id="e-marks-per" value="2" min="1" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Instructions</label>
                    <textarea id="e-instructions" rows="2" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm resize-none" placeholder="Answer all questions."></textarea></div>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" id="e-shuffle" class="w-4 h-4 text-emerald-600 rounded"><span class="text-sm text-gray-700">Shuffle Questions</span></label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" id="e-show-result" checked class="w-4 h-4 text-emerald-600 rounded"><span class="text-sm text-gray-700">Show Result</span></label>
                </div>
            </div>
            <!-- Questions Section -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-semibold text-gray-900">Questions</h4>
                    <div class="flex gap-2">
                        <label class="px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-medium hover:bg-indigo-200 cursor-pointer"><i class="fas fa-file-upload mr-1"></i>Upload TXT
                            <input type="file" accept=".txt" onchange="importQuestionsFromFile(this)" class="hidden"></label>
                        <button onclick="addQuestion()" class="px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium hover:bg-emerald-200"><i class="fas fa-plus mr-1"></i>Add Question</button>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mb-2">TXT format: one question per line. For MCQ: <code>Question|OptionA|OptionB|OptionC|OptionD</code></p>
                <div id="questions-container" class="space-y-4"><p class="text-xs text-gray-400 text-center py-4">No questions added yet.</p></div>
            </div>
        </div>
        <div class="p-6 border-t border-gray-100 flex gap-3 flex-shrink-0">
            <button type="button" onclick="Modal.close('exam-modal')" class="flex-1 py-2.5 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Cancel</button>
            <button type="button" onclick="saveExam()" id="save-exam-btn" class="flex-1 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">Save Exam</button>
        </div>
    </div>
</div>

<!-- View Exam / Review Attempts Modal -->
<div id="view-exam-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 max-h-[90vh] flex flex-col">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
            <h3 class="text-lg font-semibold text-gray-900">Exam Details</h3>
            <button onclick="Modal.close('view-exam-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"><i class="fas fa-times"></i></button>
        </div>
        <div id="view-exam-content" class="p-6 overflow-y-auto flex-1"></div>
    </div>
</div>

<!-- Review Student Attempt Modal -->
<div id="attempt-modal" class="fixed inset-0 z-[60] hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 max-h-[90vh] flex flex-col">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
            <h3 class="text-lg font-semibold text-gray-900">Review Student Answers</h3>
            <button onclick="Modal.close('attempt-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"><i class="fas fa-times"></i></button>
        </div>
        <div id="attempt-content" class="p-6 overflow-y-auto flex-1"></div>
    </div>
</div>

<script>
let myClasses = [], mySubjects = [], editingExamId = null, currentQuestions = [];

async function loadOptions() {
    const data = await API.get('/api/faculty/scores/options');
    if (data && data.success) {
        myClasses = data.classes; mySubjects = data.subjects;
        document.getElementById('e-class').innerHTML = '<option value="">Select Class</option>' + myClasses.map(c => `<option value="${c.id}">${escapeHtml(c.name)}</option>`).join('');
        document.getElementById('e-subject').innerHTML = '<option value="">Select Subject</option>' + mySubjects.map(s => `<option value="${s.id}">${escapeHtml(s.name)} (${escapeHtml(s.code)})</option>`).join('');
    }
}

function filterExams(status) {
    document.querySelectorAll('.exam-tab').forEach(t => { t.classList.remove('active','bg-gray-900','text-white'); t.classList.add('text-gray-600'); });
    event.target.classList.add('active','bg-gray-900','text-white'); event.target.classList.remove('text-gray-600');
    loadExams(status);
}

async function loadExams(status = '') {
    const data = await API.get('/api/faculty/exams' + (status ? `?status=${status}` : ''));
    if (!data || !data.success) return;
    const container = document.getElementById('exams-list');
    if (data.data.length === 0) { container.innerHTML = '<div class="text-center py-12 text-gray-400"><i class="fas fa-file-alt text-4xl mb-3 block"></i>No exams found</div>'; return; }
    container.innerHTML = data.data.map(e => {
        const sc = { draft:'bg-gray-100 text-gray-700', pending:'bg-yellow-100 text-yellow-700', approved:'bg-blue-100 text-blue-700', active:'bg-green-100 text-green-700', completed:'bg-purple-100 text-purple-700', rejected:'bg-red-100 text-red-700' };
        let actions = `<button onclick="viewExam(${e.id})" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100">View</button>`;
        if (e.status==='draft'||e.status==='rejected') actions += ` <button onclick="editExam(${e.id})" class="px-3 py-1.5 text-xs font-medium text-amber-600 bg-amber-50 rounded-lg hover:bg-amber-100">Edit</button> <button onclick="deleteExam(${e.id})" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100">Delete</button> <button onclick="submitForApproval(${e.id})" class="px-3 py-1.5 text-xs font-medium text-emerald-600 bg-emerald-50 rounded-lg hover:bg-emerald-100">Submit</button>`;
        if (e.attempt_count > 0) actions += ` <button onclick="viewAttempts(${e.id})" class="px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100"><i class="fas fa-users mr-1"></i>Attempts (${e.attempt_count})</button>`;
        return `<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3"><div><h4 class="font-semibold text-gray-900">${escapeHtml(e.title)}</h4><p class="text-sm text-gray-500">${escapeHtml(e.subject_name)} (${escapeHtml(e.subject_code)}) &middot; ${escapeHtml(e.class_name)}</p></div>
                <span class="px-2.5 py-1 text-xs font-medium rounded-full ${sc[e.status]||'bg-gray-100'} self-start">${e.status.charAt(0).toUpperCase()+e.status.slice(1)}</span></div>
            <div class="flex flex-wrap gap-4 text-xs text-gray-500 mb-4"><span><i class="fas fa-clock mr-1"></i>${e.duration_minutes}m</span><span><i class="fas fa-question-circle mr-1"></i>${e.question_count} Qs</span><span><i class="fas fa-star mr-1"></i>${e.total_marks}m</span><span><i class="fas fa-users mr-1"></i>${e.attempt_count} attempts</span></div>
            <div class="flex flex-wrap gap-2">${actions}</div>
        </div>`;
    }).join('');
}

function showCreateExam() {
    editingExamId = null; currentQuestions = [];
    document.getElementById('exam-modal-title').textContent = 'Create Exam';
    ['e-title','e-instructions'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('e-class').value = ''; document.getElementById('e-subject').value = '';
    document.getElementById('e-type').value = 'objective'; document.getElementById('e-duration').value = 60;
    document.getElementById('e-total').value = 100; document.getElementById('e-marks-per').value = 2;
    document.getElementById('e-shuffle').checked = false; document.getElementById('e-show-result').checked = true;
    renderQuestions(); Modal.open('exam-modal');
}

async function editExam(id) {
    const data = await API.get(`/api/faculty/exams/${id}`);
    if (!data || !data.success) return;
    const e = data.data; editingExamId = id;
    document.getElementById('exam-modal-title').textContent = 'Edit Exam';
    document.getElementById('e-title').value = e.title; document.getElementById('e-class').value = e.class_id;
    document.getElementById('e-subject').value = e.subject_id; document.getElementById('e-type').value = e.exam_type;
    document.getElementById('e-duration').value = e.duration_minutes; document.getElementById('e-total').value = e.total_marks;
    document.getElementById('e-instructions').value = e.instructions || '';
    document.getElementById('e-shuffle').checked = !!parseInt(e.shuffle_questions);
    document.getElementById('e-show-result').checked = !!parseInt(e.show_result);
    currentQuestions = (e.questions||[]).map(q => ({question_text:q.question_text, question_type:q.question_type, option_a:q.option_a, option_b:q.option_b, option_c:q.option_c, option_d:q.option_d, correct_answer:q.correct_answer, marks:q.marks}));
    renderQuestions(); Modal.open('exam-modal');
}

function importQuestionsFromFile(input) {
    const file = input.files[0]; if (!file) return;
    const reader = new FileReader();
    reader.onload = function(ev) {
        const lines = ev.target.result.split('\n').filter(l => l.trim());
        const examType = document.getElementById('e-type').value;
        const marksPerQ = parseInt(document.getElementById('e-marks-per').value) || 2;
        lines.forEach(line => {
            const parts = line.split('|').map(p => p.trim());
            if (examType === 'objective' || examType === 'mixed') {
                if (parts.length >= 5) {
                    currentQuestions.push({ question_text: parts[0], question_type: 'mcq', option_a: parts[1], option_b: parts[2], option_c: parts[3], option_d: parts[4], correct_answer: '', marks: marksPerQ });
                } else if (parts.length >= 3) {
                    currentQuestions.push({ question_text: parts[0], question_type: 'true_false', option_a: '', option_b: '', option_c: '', option_d: '', correct_answer: '', marks: marksPerQ });
                } else {
                    currentQuestions.push({ question_text: parts[0], question_type: 'mcq', option_a: '', option_b: '', option_c: '', option_d: '', correct_answer: '', marks: marksPerQ });
                }
            } else {
                currentQuestions.push({ question_text: parts[0], question_type: 'descriptive', option_a: '', option_b: '', option_c: '', option_d: '', correct_answer: '', marks: marksPerQ });
            }
        });
        renderQuestions();
        Toast.success(`${lines.length} questions imported from file.`);
    };
    reader.readAsText(file);
    input.value = '';
}

function addQuestion() {
    const marksPerQ = parseInt(document.getElementById('e-marks-per').value) || 2;
    const type = document.getElementById('e-type').value === 'theory' ? 'descriptive' : 'mcq';
    currentQuestions.push({ question_text: '', question_type: type, option_a: '', option_b: '', option_c: '', option_d: '', correct_answer: '', marks: marksPerQ });
    renderQuestions();
}
function removeQuestion(idx) { currentQuestions.splice(idx, 1); renderQuestions(); }
function updateQuestion(idx, field, value) { currentQuestions[idx][field] = value; if (field === 'question_type') renderQuestions(); }

function renderQuestions() {
    const container = document.getElementById('questions-container');
    if (currentQuestions.length === 0) { container.innerHTML = '<p class="text-xs text-gray-400 text-center py-4">No questions added yet.</p>'; return; }
    container.innerHTML = currentQuestions.map((q, i) => {
        const typeSelect = `<select onchange="updateQuestion(${i},'question_type',this.value)" class="px-2 py-1 text-xs border border-gray-200 rounded-lg"><option value="mcq" ${q.question_type==='mcq'?'selected':''}>MCQ</option><option value="true_false" ${q.question_type==='true_false'?'selected':''}>True/False</option><option value="fill_in" ${q.question_type==='fill_in'?'selected':''}>Fill in</option><option value="descriptive" ${q.question_type==='descriptive'?'selected':''}>Descriptive</option></select>`;
        let optionsHtml = '';
        if (q.question_type === 'mcq') {
            optionsHtml = `<div class="grid grid-cols-2 gap-2"><input type="text" value="${escapeHtml(q.option_a||'')}" onchange="updateQuestion(${i},'option_a',this.value)" placeholder="Option A" class="px-2 py-1.5 text-xs border border-gray-200 rounded-lg"><input type="text" value="${escapeHtml(q.option_b||'')}" onchange="updateQuestion(${i},'option_b',this.value)" placeholder="Option B" class="px-2 py-1.5 text-xs border border-gray-200 rounded-lg"><input type="text" value="${escapeHtml(q.option_c||'')}" onchange="updateQuestion(${i},'option_c',this.value)" placeholder="Option C" class="px-2 py-1.5 text-xs border border-gray-200 rounded-lg"><input type="text" value="${escapeHtml(q.option_d||'')}" onchange="updateQuestion(${i},'option_d',this.value)" placeholder="Option D" class="px-2 py-1.5 text-xs border border-gray-200 rounded-lg"></div>
                <select onchange="updateQuestion(${i},'correct_answer',this.value)" class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg mt-2"><option value="">Select Correct Answer</option><option value="A" ${q.correct_answer==='A'?'selected':''}>A</option><option value="B" ${q.correct_answer==='B'?'selected':''}>B</option><option value="C" ${q.correct_answer==='C'?'selected':''}>C</option><option value="D" ${q.correct_answer==='D'?'selected':''}>D</option></select>`;
        } else if (q.question_type === 'true_false') {
            optionsHtml = `<select onchange="updateQuestion(${i},'correct_answer',this.value)" class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg"><option value="">Select</option><option value="True" ${q.correct_answer==='True'?'selected':''}>True</option><option value="False" ${q.correct_answer==='False'?'selected':''}>False</option></select>`;
        } else if (q.question_type === 'fill_in') {
            optionsHtml = `<input type="text" value="${escapeHtml(q.correct_answer||'')}" onchange="updateQuestion(${i},'correct_answer',this.value)" placeholder="Correct answer" class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg">`;
        }
        return `<div class="bg-gray-50 rounded-xl p-4 border border-gray-100"><div class="flex items-center justify-between mb-3"><span class="text-xs font-semibold text-gray-700 bg-white px-2 py-1 rounded-lg">Q${i+1}</span><div class="flex items-center gap-2">${typeSelect}<input type="number" value="${q.marks||2}" onchange="updateQuestion(${i},'marks',this.value)" class="w-16 px-2 py-1 text-xs border border-gray-200 rounded-lg" title="Marks"><button onclick="removeQuestion(${i})" class="p-1 text-red-400 hover:text-red-600"><i class="fas fa-trash text-xs"></i></button></div></div><textarea onchange="updateQuestion(${i},'question_text',this.value)" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg mb-2 resize-none" placeholder="Enter question text...">${escapeHtml(q.question_text||'')}</textarea>${optionsHtml}</div>`;
    }).join('');
}

async function saveExam() {
    const btn = document.getElementById('save-exam-btn');
    const payload = { title: document.getElementById('e-title').value, class_id: document.getElementById('e-class').value, subject_id: document.getElementById('e-subject').value, exam_type: document.getElementById('e-type').value, duration_minutes: document.getElementById('e-duration').value, total_marks: document.getElementById('e-total').value, pass_mark: 0, instructions: document.getElementById('e-instructions').value, shuffle_questions: document.getElementById('e-shuffle').checked?1:0, show_result: document.getElementById('e-show-result').checked?1:0, questions: currentQuestions };
    if (!payload.title || !payload.class_id || !payload.subject_id) { Toast.error('Please fill required fields.'); return; }
    setLoading(btn, true);
    const data = editingExamId ? await API.put(`/api/faculty/exams/${editingExamId}`, payload) : await API.post('/api/faculty/exams', payload);
    setLoading(btn, false);
    if (data && data.success) { Toast.success(data.message); Modal.close('exam-modal'); loadExams(); } else if (data) Toast.error(data.message);
}

async function submitForApproval(id) { const yes = await confirmAction('Submit for admin approval?'); if (!yes) return; const data = await API.post(`/api/faculty/exams/${id}/submit`); if (data && data.success) { Toast.success(data.message); loadExams(); } else if (data) Toast.error(data.message); }
async function deleteExam(id) { const yes = await confirmAction('Delete this exam?'); if (!yes) return; const data = await API.delete(`/api/faculty/exams/${id}`); if (data && data.success) { Toast.success(data.message); loadExams(); } else if (data) Toast.error(data.message); }

async function viewExam(id) {
    const data = await API.get(`/api/faculty/exams/${id}`); if (!data || !data.success) return;
    const e = data.data; const sc = { draft:'bg-gray-100 text-gray-700', pending:'bg-yellow-100 text-yellow-700', approved:'bg-blue-100 text-blue-700', active:'bg-green-100 text-green-700', completed:'bg-purple-100 text-purple-700', rejected:'bg-red-100 text-red-700' };
    let questionsHtml = (e.questions||[]).map((q,i) => {
        let answerInfo = '';
        if (q.question_type==='mcq') answerInfo = `<div class="grid grid-cols-2 gap-1 text-xs mt-2"><span class="${q.correct_answer==='A'?'font-bold text-green-600':'text-gray-500'}">A: ${escapeHtml(q.option_a||'')}</span><span class="${q.correct_answer==='B'?'font-bold text-green-600':'text-gray-500'}">B: ${escapeHtml(q.option_b||'')}</span><span class="${q.correct_answer==='C'?'font-bold text-green-600':'text-gray-500'}">C: ${escapeHtml(q.option_c||'')}</span><span class="${q.correct_answer==='D'?'font-bold text-green-600':'text-gray-500'}">D: ${escapeHtml(q.option_d||'')}</span></div>`;
        else if (q.correct_answer) answerInfo = `<p class="text-xs text-green-600 mt-1">Answer: ${escapeHtml(q.correct_answer)}</p>`;
        return `<div class="p-3 bg-gray-50 rounded-lg"><span class="text-xs font-medium text-gray-500">Q${i+1} (${q.question_type}) - ${q.marks}m</span><p class="text-sm text-gray-800 mt-1">${escapeHtml(q.question_text)}</p>${answerInfo}</div>`;
    }).join('');
    document.getElementById('view-exam-content').innerHTML = `<div class="space-y-4"><div class="flex items-center justify-between"><h4 class="text-lg font-bold">${escapeHtml(e.title)}</h4><span class="px-3 py-1 text-xs rounded-full ${sc[e.status]}">${e.status}</span></div><div class="grid grid-cols-2 gap-3 text-sm"><p class="text-gray-500">Class: <strong>${escapeHtml(e.class_name)}</strong></p><p class="text-gray-500">Subject: <strong>${escapeHtml(e.subject_name)}</strong></p><p class="text-gray-500">Duration: <strong>${e.duration_minutes}m</strong></p><p class="text-gray-500">Total: <strong>${e.total_marks}m</strong></p></div><div><h5 class="font-semibold mb-3">Questions (${(e.questions||[]).length})</h5><div class="space-y-2">${questionsHtml||'<p class="text-gray-400 text-sm">No questions</p>'}</div></div></div>`;
    Modal.open('view-exam-modal');
}

async function viewAttempts(examId) {
    const data = await API.get(`/api/faculty/exams/${examId}/attempts`); if (!data || !data.success) return;
    const attempts = data.data;
    document.getElementById('view-exam-content').innerHTML = `<div class="space-y-3"><h4 class="text-lg font-bold mb-4">Student Attempts</h4>${attempts.length===0?'<p class="text-gray-400">No attempts yet</p>':attempts.map(a => {
        const aSc = {in_progress:'text-blue-600',submitted:'text-green-600',timed_out:'text-orange-600',graded:'text-purple-600'};
        return `<div class="p-4 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-between"><div><span class="font-semibold text-sm text-gray-900">${escapeHtml(a.student_name)}</span> <span class="text-xs text-gray-400">${escapeHtml(a.matric_no||'')}</span><div class="text-xs mt-1"><span class="${aSc[a.status]||''} font-medium">${a.status}</span> &middot; Score: ${a.score??'-'}/${a.total_marks} ${a.percentage?'('+a.percentage+'%)':''}</div></div><button onclick="reviewAttempt(${a.id})" class="px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100"><i class="fas fa-eye mr-1"></i>Review & Grade</button></div>`;
    }).join('')}</div>`;
    Modal.open('view-exam-modal');
}

async function reviewAttempt(attemptId) {
    const data = await API.get(`/api/faculty/exams/attempts/${attemptId}`); if (!data || !data.success) return;
    const a = data.data;
    document.getElementById('attempt-content').innerHTML = `<div class="space-y-4">
        <div class="flex items-center justify-between"><div><h4 class="font-bold text-gray-900">${escapeHtml(a.student_name)} <span class="text-sm font-normal text-gray-400">${escapeHtml(a.matric_no||'')}</span></h4><p class="text-sm text-gray-500">${escapeHtml(a.exam_title)}</p></div><div class="text-right"><div class="text-lg font-bold text-gray-900">${a.score??0}/${a.total_marks}</div><div class="text-xs text-gray-500">${a.percentage||0}%</div></div></div>
        <div class="space-y-3">${(a.questions||[]).map((q,i) => {
            const isCorrect = q.is_correct==1;
            const bgColor = q.student_answer ? (q.question_type==='descriptive' ? 'bg-blue-50 border-blue-100' : (isCorrect ? 'bg-green-50 border-green-100' : 'bg-red-50 border-red-100')) : 'bg-gray-50 border-gray-100';
            return `<div class="p-4 rounded-xl border ${bgColor}"><div class="flex justify-between mb-2"><span class="text-xs font-semibold text-gray-700">Q${i+1} (${q.question_type}) - ${q.marks}m</span><span class="text-xs font-medium ${isCorrect?'text-green-600':'text-red-600'}">${q.marks_awarded??0}/${q.marks}m</span></div><p class="text-sm text-gray-800 mb-2">${escapeHtml(q.question_text)}</p>${q.correct_answer?`<p class="text-xs text-green-600 mb-1">Correct: ${escapeHtml(q.correct_answer)}</p>`:''}
                <p class="text-xs text-gray-700"><strong>Student answer:</strong> ${q.student_answer ? escapeHtml(q.student_answer) : '<em class="text-gray-400">No answer</em>'}</p>
                ${q.answer_id ? `<div class="flex items-center gap-2 mt-2 pt-2 border-t border-gray-200"><input type="number" value="${q.marks_awarded||0}" min="0" max="${q.marks}" class="w-20 px-2 py-1 text-xs border rounded-lg" id="grade-${q.answer_id}"><button onclick="gradeAnswer(${q.answer_id},${q.marks})" class="px-2 py-1 text-xs bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Update Grade</button></div>` : ''}
            </div>`;
        }).join('')}</div></div>`;
    Modal.open('attempt-modal');
}

async function gradeAnswer(answerId, maxMarks) {
    const marks = parseFloat(document.getElementById(`grade-${answerId}`).value);
    if (isNaN(marks) || marks < 0 || marks > maxMarks) { Toast.error(`Marks must be between 0 and ${maxMarks}`); return; }
    const data = await API.post('/api/faculty/exams/grade-answer', { answer_id: answerId, marks_awarded: marks, is_correct: marks > 0 ? 1 : 0 });
    if (data && data.success) Toast.success(data.message); else if (data) Toast.error(data.message);
}

document.addEventListener('DOMContentLoaded', () => { loadOptions(); loadExams(); });
</script>
