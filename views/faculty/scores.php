<!-- Faculty Scores Management -->
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Manage Scores</h2>
        <p class="text-sm text-gray-500 mt-1">Enter and manage student scores for your classes</p>
    </div>

    <!-- Selection -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                <select id="sc-class" onchange="onClassChange()" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    <option value="">Select Class</option>
                </select>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                <select id="sc-subject" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    <option value="">Select Subject</option>
                </select>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                <select id="sc-semester" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                    <option value="1">Semester 1</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="loadScoreSheet()" class="w-full px-4 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                    <i class="fas fa-search mr-2"></i>Load Students
                </button>
            </div>
        </div>
    </div>

    <!-- Score Table -->
    <div id="score-section" class="hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-table mr-2 text-emerald-600"></i>Score Sheet</h3>
                <button onclick="saveAllScores()" id="save-scores-btn" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                    <i class="fas fa-save mr-2"></i>Save All Scores
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500">#</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500">Student</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500">Matric No</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500">CA Score (40)</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500">Exam Score (60)</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500">Total</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500">Grade</th>
                        </tr>
                    </thead>
                    <tbody id="score-body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
let teachingClasses = [], teachingSubjects = [];

async function loadOptions() {
    const data = await API.get('/api/faculty/scores/options');
    if (!data || !data.success) return;
    teachingClasses = data.classes; teachingSubjects = data.subjects;
    document.getElementById('sc-class').innerHTML = '<option value="">Select Class</option>' + teachingClasses.map(c => `<option value="${c.id}">${escapeHtml(c.name)}</option>`).join('');
    document.getElementById('sc-subject').innerHTML = '<option value="">Select Subject</option>' + teachingSubjects.map(s => `<option value="${s.id}">${escapeHtml(s.name)} (${escapeHtml(s.code)})</option>`).join('');
}

function onClassChange() {
    const classId = document.getElementById('sc-class').value;
    const cls = teachingClasses.find(c => c.id == classId);
    const semSelect = document.getElementById('sc-semester');
    if (cls && cls.semester_count) {
        const count = parseInt(cls.semester_count) || 1;
        semSelect.innerHTML = Array.from({length: count}, (_, i) => `<option value="${i+1}">Semester ${i+1}</option>`).join('');
    } else {
        semSelect.innerHTML = '<option value="1">Semester 1</option>';
    }
}

async function loadScoreSheet() {
    const classId = document.getElementById('sc-class').value;
    const subjectId = document.getElementById('sc-subject').value;
    const semester = document.getElementById('sc-semester').value;
    if (!classId || !subjectId) { Toast.warning('Please select a class and subject.'); return; }

    const data = await API.get(`/api/faculty/scores?class_id=${classId}&subject_id=${subjectId}&semester=${semester}`);
    if (!data || !data.success) return;

    const tbody = document.getElementById('score-body');
    document.getElementById('score-section').classList.remove('hidden');

    if (data.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-400">No students found in this class</td></tr>';
        return;
    }

    tbody.innerHTML = data.data.map((s, i) => {
        const ca = s.ca_score || '';
        const exam = s.exam_score || '';
        const total = ca && exam ? parseFloat(ca) + parseFloat(exam) : '';
        const grade = total ? calculateGrade(total) : '';
        return `<tr class="border-t border-gray-50 hover:bg-gray-50" data-student-id="${s.id}">
            <td class="px-4 py-3 text-gray-500">${i+1}</td>
            <td class="px-4 py-3 font-medium text-gray-900">${escapeHtml(s.name)}</td>
            <td class="px-4 py-3 text-gray-500">${escapeHtml(s.matric_no || '-')}</td>
            <td class="px-4 py-3 text-center">
                <input type="number" value="${ca}" min="0" max="40" step="0.5" onchange="recalcRow(this)" class="ca-input w-20 px-2 py-1.5 text-center text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
            </td>
            <td class="px-4 py-3 text-center">
                <input type="number" value="${exam}" min="0" max="60" step="0.5" onchange="recalcRow(this)" class="exam-input w-20 px-2 py-1.5 text-center text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
            </td>
            <td class="px-4 py-3 text-center font-bold total-cell">${total}</td>
            <td class="px-4 py-3 text-center grade-cell"><span class="px-2 py-0.5 text-xs rounded-full ${gradeColor(grade)}">${grade}</span></td>
        </tr>`;
    }).join('');
}

function recalcRow(input) {
    const row = input.closest('tr');
    const ca = parseFloat(row.querySelector('.ca-input').value) || 0;
    const exam = parseFloat(row.querySelector('.exam-input').value) || 0;
    const total = ca + exam;
    const grade = calculateGrade(total);
    row.querySelector('.total-cell').textContent = total;
    row.querySelector('.grade-cell').innerHTML = `<span class="px-2 py-0.5 text-xs rounded-full ${gradeColor(grade)}">${grade}</span>`;
}

function calculateGrade(score) {
    if (score >= 70) return 'A'; if (score >= 60) return 'B'; if (score >= 50) return 'C';
    if (score >= 45) return 'D'; if (score >= 40) return 'E'; return 'F';
}

function gradeColor(grade) {
    const colors = { A: 'bg-green-100 text-green-700', B: 'bg-blue-100 text-blue-700', C: 'bg-yellow-100 text-yellow-700', D: 'bg-orange-100 text-orange-700', E: 'bg-red-100 text-red-700', F: 'bg-red-200 text-red-800' };
    return colors[grade] || 'bg-gray-100 text-gray-700';
}

async function saveAllScores() {
    const rows = document.querySelectorAll('#score-body tr[data-student-id]');
    if (rows.length === 0) return;
    const scores = [];
    rows.forEach(row => {
        scores.push({
            student_id: row.dataset.studentId,
            ca_score: row.querySelector('.ca-input').value || 0,
            exam_score: row.querySelector('.exam-input').value || 0,
        });
    });
    const btn = document.getElementById('save-scores-btn');
    setLoading(btn, true);
    const data = await API.post('/api/faculty/scores', {
        class_id: document.getElementById('sc-class').value,
        subject_id: document.getElementById('sc-subject').value,
        semester: document.getElementById('sc-semester').value,
        scores
    });
    setLoading(btn, false);
    if (data && data.success) Toast.success(data.message);
    else if (data) Toast.error(data.message);
}

document.addEventListener('DOMContentLoaded', loadOptions);
</script>
