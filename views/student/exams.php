<!-- Student Exams -->
<div id="exams-section" class="space-y-6">
    <div><h2 class="text-xl font-bold text-gray-900">Exams</h2>
        <p class="text-sm text-gray-500 mt-1">View available exams and take them online</p></div>
    <div id="exams-list" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="col-span-full text-center py-8 text-gray-400">Loading...</div>
    </div>
</div>

<!-- Exam Code Entry Modal -->
<div id="code-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center">
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-key text-blue-600 text-2xl"></i></div>
        <h3 class="text-lg font-bold text-gray-900 mb-1" id="code-modal-title">Enter Exam Code</h3>
        <p class="text-sm text-gray-500 mb-4" id="code-modal-desc">Enter the 6-character code provided by your admin to start the exam.</p>
        <input type="text" id="exam-code-input" maxlength="6" class="w-full text-center text-2xl font-mono tracking-widest px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none uppercase" placeholder="______">
        <input type="hidden" id="exam-code-id">
        <div class="flex gap-3 mt-4">
            <button onclick="Modal.close('code-modal')" class="flex-1 py-2.5 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Cancel</button>
            <button onclick="verifyCodeAndStart()" id="start-exam-btn" class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Start Exam</button>
        </div>
    </div>
</div>

<!-- CBT Exam Interface (Full Screen) -->
<div id="cbt-section" class="hidden fixed inset-0 z-[100] bg-white">
    <div class="h-full flex flex-col">
        <!-- Exam Header -->
        <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-blue-600 to-indigo-600 flex-shrink-0">
            <div><h3 id="cbt-title" class="text-white font-bold text-lg">Exam</h3>
                <p id="cbt-info" class="text-white/70 text-sm"></p></div>
            <div class="flex items-center gap-4">
                <div class="bg-white/20 rounded-lg px-4 py-2 text-center">
                    <div id="cbt-timer" class="text-white font-bold text-xl font-mono">00:00</div>
                    <div class="text-white/60 text-xs">Time Left</div>
                </div>
                <button onclick="submitExam()" class="px-4 py-2 bg-white text-blue-600 rounded-lg font-semibold text-sm hover:bg-blue-50">Submit Exam</button>
            </div>
        </div>
        <!-- Question Navigation -->
        <div class="p-3 border-b border-gray-100 bg-gray-50 flex-shrink-0 overflow-x-auto">
            <div id="question-nav" class="flex gap-2 flex-nowrap"></div>
        </div>
        <!-- Question Display -->
        <div id="question-area" class="flex-1 p-6 overflow-y-auto"></div>
        <!-- Bottom Navigation -->
        <div class="p-4 border-t border-gray-100 flex items-center justify-between flex-shrink-0 bg-white">
            <button onclick="prevQuestion()" id="prev-btn" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200"><i class="fas fa-chevron-left mr-2"></i>Previous</button>
            <span id="question-counter" class="text-sm text-gray-500"></span>
            <button onclick="nextQuestion()" id="next-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Next<i class="fas fa-chevron-right ml-2"></i></button>
        </div>
    </div>
</div>

<script>
let cbtData = null, currentQIndex = 0, answers = {}, timerInterval = null, currentExamId = null, attemptId = null;

async function loadExams() {
    const data = await API.get('/api/student/exams');
    if (!data || !data.success) return;
    const grid = document.getElementById('exams-list');
    if (data.data.length === 0) {
        grid.innerHTML = '<div class="col-span-full text-center py-12 text-gray-400"><i class="fas fa-laptop-code text-4xl mb-3 block"></i>No exams available</div>';
        return;
    }
    grid.innerHTML = data.data.map(e => {
        const isActive = e.status === 'active';
        const attempted = e.attempt_status === 'submitted' || e.attempt_status === 'graded' || e.attempt_status === 'timed_out';
        const inProgress = e.attempt_status === 'in_progress';
        let actionBtn = '';
        if (isActive && !attempted && !inProgress) {
            actionBtn = `<button onclick="showCodeModal(${e.id},'start')" class="w-full py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"><i class="fas fa-play mr-2"></i>Enter Code & Start</button>`;
        } else if (inProgress) {
            actionBtn = `<button onclick="showCodeModal(${e.id},'resume')" class="w-full py-2.5 bg-amber-500 text-white rounded-lg text-sm font-medium hover:bg-amber-600"><i class="fas fa-redo mr-2"></i>Resume Exam</button>`;
        } else if (attempted) {
            actionBtn = `<div class="text-center"><span class="text-green-600 font-bold text-lg">${e.attempt_score ?? '-'}</span><span class="text-gray-400 text-sm">/${e.total_marks || 100}</span><p class="text-xs text-gray-500 mt-1">Completed</p></div>`;
        } else {
            actionBtn = `<p class="text-sm text-gray-400 text-center">Exam not started by admin</p>`;
        }
        return `<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 ${isActive ? 'ring-2 ring-blue-400' : ''}">
            <div class="flex items-start justify-between mb-3"><span class="px-2 py-0.5 text-xs font-medium rounded-full ${isActive ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'}">${isActive ? 'Active' : 'Ended'}</span><span class="text-xs text-gray-400">${e.question_count} Qs</span></div>
            <h4 class="font-semibold text-gray-900 mb-1">${escapeHtml(e.title)}</h4>
            <p class="text-sm text-gray-500 mb-3">${escapeHtml(e.subject_name)} (${escapeHtml(e.subject_code)})</p>
            <div class="flex flex-wrap gap-3 text-xs text-gray-400 mb-4"><span><i class="fas fa-clock mr-1"></i>${e.duration_minutes} min</span><span><i class="fas fa-user mr-1"></i>${escapeHtml(e.lecturer_name)}</span></div>
            ${actionBtn}
        </div>`;
    }).join('');
}

function showCodeModal(examId, mode) {
    document.getElementById('exam-code-id').value = examId;
    document.getElementById('exam-code-input').value = '';
    if (mode === 'resume') {
        document.getElementById('code-modal-title').textContent = 'Resume Exam';
        document.getElementById('code-modal-desc').textContent = 'Enter the exam code or your continue key to resume.';
    } else {
        document.getElementById('code-modal-title').textContent = 'Enter Exam Code';
        document.getElementById('code-modal-desc').textContent = 'Enter the 6-character code provided by your admin to start the exam.';
    }
    Modal.open('code-modal');
}

async function verifyCodeAndStart() {
    const examId = document.getElementById('exam-code-id').value;
    const code = document.getElementById('exam-code-input').value.trim();
    if (!code || code.length < 6) { Toast.error('Please enter a valid 6-character code.'); return; }

    const yes = await confirmAction('Are you sure you are ready to begin? The timer will start immediately and you cannot leave the page.');
    if (!yes) return;

    const btn = document.getElementById('start-exam-btn'); setLoading(btn, true);
    const data = await API.post(`/api/student/exams/${examId}/start`, { code });
    setLoading(btn, false);
    if (data && data.success) {
        Modal.close('code-modal');
        attemptId = data.attempt_id;
        currentExamId = examId;
        loadExamQuestions(examId);
    } else if (data) Toast.error(data.message);
}

async function loadExamQuestions(examId) {
    const data = await API.get(`/api/student/exams/${examId}/questions`);
    if (!data || !data.success) { if (data) Toast.error(data.message); return; }
    cbtData = data.data; attemptId = cbtData.attempt_id; answers = cbtData.answers || {}; currentQIndex = 0;

    // Enter fullscreen CBT mode
    document.getElementById('exams-section').classList.add('hidden');
    document.getElementById('cbt-section').classList.remove('hidden');
    document.getElementById('cbt-title').textContent = cbtData.exam_title;
    document.getElementById('cbt-info').textContent = `${cbtData.questions.length} Questions`;

    // Try fullscreen
    try { document.documentElement.requestFullscreen?.(); } catch(e) {}

    startTimer(cbtData.time_left);
    renderQuestionNav(); renderQuestion();
    // Anti-cheat: detect page leave
    window._antiCheatEnabled = true;
}

// Anti-cheat: visibility change handler
document.addEventListener('visibilitychange', () => {
    if (window._antiCheatEnabled && document.hidden && cbtData) {
        Toast.warning('You left the exam page! Auto-submitting...');
        submitExam(true);
    }
});
// Anti-cheat: window blur (tab switch)
window.addEventListener('blur', () => {
    if (window._antiCheatEnabled && cbtData) {
        Toast.warning('Tab switch detected! Auto-submitting...');
        submitExam(true);
    }
});
// Prevent right-click
document.addEventListener('contextmenu', (e) => { if (window._antiCheatEnabled && cbtData) e.preventDefault(); });

function startTimer(seconds) {
    if (timerInterval) clearInterval(timerInterval);
    let timeLeft = seconds;
    updateTimerDisplay(timeLeft);
    timerInterval = setInterval(() => {
        timeLeft--;
        updateTimerDisplay(timeLeft);
        if (timeLeft <= 0) { clearInterval(timerInterval); Toast.warning('Time is up!'); submitExam(true); }
        if (timeLeft <= 60) document.getElementById('cbt-timer').classList.add('text-red-300');
    }, 1000);
}
function updateTimerDisplay(s) { const m = Math.floor(s/60); document.getElementById('cbt-timer').textContent = `${String(m).padStart(2,'0')}:${String(s%60).padStart(2,'0')}`; }

function renderQuestionNav() {
    document.getElementById('question-nav').innerHTML = cbtData.questions.map((q, i) => {
        const answered = answers[q.id] ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-200';
        const active = i === currentQIndex ? 'ring-2 ring-blue-400 ring-offset-1' : '';
        return `<button onclick="goToQuestion(${i})" class="w-9 h-9 rounded-lg text-xs font-bold flex-shrink-0 ${answered} ${active} hover:opacity-80 transition">${i+1}</button>`;
    }).join('');
}

function renderQuestion() {
    if (!cbtData || !cbtData.questions.length) return;
    const q = cbtData.questions[currentQIndex];
    const currentAns = answers[q.id] || '';
    let optionsHtml = '';
    if (q.question_type === 'mcq') {
        ['A','B','C','D'].forEach(opt => {
            const val = q['option_' + opt.toLowerCase()]; if (!val) return;
            const sel = currentAns === opt ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-200' : 'bg-white border-gray-200 hover:bg-gray-50';
            optionsHtml += `<button onclick="selectAnswer(${q.id},'${opt}')" class="w-full text-left p-4 rounded-xl border ${sel} transition flex items-center gap-3"><span class="w-8 h-8 rounded-full ${currentAns===opt?'bg-blue-600 text-white':'bg-gray-100 text-gray-700'} flex items-center justify-center text-sm font-bold flex-shrink-0">${opt}</span><span class="text-sm text-gray-800">${escapeHtml(val)}</span></button>`;
        });
    } else if (q.question_type === 'true_false') {
        ['True','False'].forEach(opt => {
            const sel = currentAns === opt ? 'bg-blue-50 border-blue-500 ring-2 ring-blue-200' : 'bg-white border-gray-200 hover:bg-gray-50';
            optionsHtml += `<button onclick="selectAnswer(${q.id},'${opt}')" class="w-full text-left p-4 rounded-xl border ${sel} transition flex items-center gap-3"><span class="w-8 h-8 rounded-full ${currentAns===opt?'bg-blue-600 text-white':'bg-gray-100 text-gray-700'} flex items-center justify-center text-sm font-bold flex-shrink-0">${opt[0]}</span><span class="text-sm text-gray-800">${opt}</span></button>`;
        });
    } else if (q.question_type === 'fill_in') {
        optionsHtml = `<input type="text" value="${escapeHtml(currentAns)}" onchange="selectAnswer(${q.id},this.value)" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Type your answer here...">`;
    } else if (q.question_type === 'descriptive') {
        optionsHtml = `<textarea onchange="selectAnswer(${q.id},this.value)" rows="6" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none" placeholder="Write your answer here...">${escapeHtml(currentAns)}</textarea>`;
    }
    document.getElementById('question-area').innerHTML = `<div class="max-w-2xl mx-auto"><div class="flex items-center gap-3 mb-6"><span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-lg text-sm font-bold">Q${currentQIndex+1}</span><span class="text-xs text-gray-400">${q.marks} mark${q.marks>1?'s':''} &middot; ${q.question_type.replace('_',' ').toUpperCase()}</span></div><p class="text-gray-900 text-lg mb-6 leading-relaxed">${escapeHtml(q.question_text)}</p><div class="space-y-3">${optionsHtml}</div></div>`;
    document.getElementById('question-counter').textContent = `Question ${currentQIndex+1} of ${cbtData.questions.length}`;
    document.getElementById('prev-btn').disabled = currentQIndex === 0;
    document.getElementById('next-btn').textContent = currentQIndex === cbtData.questions.length - 1 ? 'Finish' : 'Next';
}

async function selectAnswer(questionId, answer) {
    answers[questionId] = answer; renderQuestionNav(); renderQuestion();
    await API.post('/api/student/exams/answer', { attempt_id: attemptId, question_id: questionId, answer });
}
function goToQuestion(idx) { currentQIndex = idx; renderQuestion(); renderQuestionNav(); }
function prevQuestion() { if (currentQIndex > 0) { currentQIndex--; renderQuestion(); renderQuestionNav(); } }
function nextQuestion() { if (currentQIndex < cbtData.questions.length - 1) { currentQIndex++; renderQuestion(); renderQuestionNav(); } else submitExam(); }

async function submitExam(force = false) {
    if (!force) {
        const unanswered = cbtData.questions.filter(q => !answers[q.id]).length;
        const msg = unanswered > 0 ? `You have ${unanswered} unanswered question(s). Submit anyway?` : 'Are you sure you want to submit?';
        const yes = await confirmAction(msg); if (!yes) return;
    }
    window._antiCheatEnabled = false;
    if (timerInterval) clearInterval(timerInterval);
    const data = await API.post(`/api/student/exams/${currentExamId}/submit`);
    // Exit fullscreen
    try { document.exitFullscreen?.(); } catch(e) {}
    if (data && data.success) {
        Toast.success('Exam submitted successfully!');
        cbtData = null;
        document.getElementById('cbt-section').classList.add('hidden');
        document.getElementById('exams-section').classList.remove('hidden');
        loadExams();
    } else if (data) { Toast.error(data.message); }
}

document.addEventListener('DOMContentLoaded', loadExams);
</script>
