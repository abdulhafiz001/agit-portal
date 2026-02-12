<!-- Admin Results -->
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><h2 class="text-xl font-bold text-gray-900">Student Results</h2>
            <p class="text-sm text-gray-500 mt-1">View and manage student academic results</p></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <select id="r-class" onchange="loadResults()" class="w-full sm:w-auto px-3 py-2.5 border border-gray-200 rounded-lg text-sm"><option value="">All Classes</option></select>
            <select id="r-subject" onchange="loadResults()" class="w-full sm:w-auto px-3 py-2.5 border border-gray-200 rounded-lg text-sm"><option value="">All Subjects</option></select>
            <div class="flex-1"><div class="relative"><i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="r-search" placeholder="Search student..." class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm outline-none" oninput="filterResults()"></div></div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="table-responsive overflow-x-auto overflow-y-auto max-h-[calc(100vh-280px)] sm:max-h-[65vh]">
            <table class="data-table" style="min-width: 980px;"><thead><tr>
                <th>Student</th><th>Matric No</th><th>Subject</th><th>Class</th>
                <th class="text-center">CA</th><th class="text-center">Exam</th><th class="text-center">Total</th>
                <th class="text-center">Grade</th><th class="text-center">Remark</th>
            </tr></thead>
            <tbody id="results-body"><tr><td colspan="9" class="text-center py-8 text-gray-400">Select filters to view results</td></tr></tbody></table>
        </div>
    </div>
</div>
<script>
let allResults = [];
async function loadDropdowns() {
    const [cls, sub] = await Promise.all([API.get('/api/admin/classes?all'), API.get('/api/admin/subjects')]);
    if(cls?.success) document.getElementById('r-class').innerHTML = '<option value="">All Classes</option>' + cls.data.map(c=>`<option value="${c.id}">${escapeHtml(c.name)}</option>`).join('');
    if(sub?.success) document.getElementById('r-subject').innerHTML = '<option value="">All Subjects</option>' + sub.data.map(s=>`<option value="${s.id}">${escapeHtml(s.name)} (${escapeHtml(s.code)})</option>`).join('');
}
async function loadResults() {
    const classId = document.getElementById('r-class').value;
    const subjectId = document.getElementById('r-subject').value;
    if (!classId && !subjectId) { document.getElementById('results-body').innerHTML='<tr><td colspan="9" class="text-center py-8 text-gray-400">Select a class or subject to view results</td></tr>'; return; }
    let url = '/api/admin/results?';
    if (classId) url += 'class_id=' + classId + '&';
    if (subjectId) url += 'subject_id=' + subjectId;
    const data = await API.get(url);
    if (!data?.success) return;
    allResults = data.data;
    renderResults(allResults);
}
function renderResults(results) {
    const tbody = document.getElementById('results-body');
    if (!results.length) { tbody.innerHTML='<tr><td colspan="9" class="text-center py-8 text-gray-400">No results found</td></tr>'; return; }
    const gc = {A:'bg-green-100 text-green-700',B:'bg-blue-100 text-blue-700',C:'bg-yellow-100 text-yellow-700',D:'bg-orange-100 text-orange-700',E:'bg-red-100 text-red-700',F:'bg-red-200 text-red-800'};
    tbody.innerHTML = results.map(r => `<tr class="hover:bg-gray-50">
        <td><div class="flex items-center gap-2">
            ${r.profile_picture ? `<img src="${APP_URL}/uploads/${r.profile_picture}" class="w-7 h-7 rounded-full object-cover">` : `<div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xs font-bold">${(r.student_name||'').charAt(0)||'?'}</div>`}
            <span class="font-medium text-sm">${escapeHtml(r.student_name||'')}</span></div></td>
        <td class="font-mono text-xs">${escapeHtml(r.matric_no||'')}</td>
        <td class="text-sm">${escapeHtml(r.subject_name||'')} (${escapeHtml(r.subject_code||'')})</td>
        <td class="text-sm">${escapeHtml(r.class_name||'')}</td>
        <td class="text-center">${r.ca_score||0}</td>
        <td class="text-center">${r.exam_score||0}</td>
        <td class="text-center font-bold">${r.total||0}</td>
        <td class="text-center"><span class="px-2 py-0.5 text-xs rounded-full ${gc[r.grade]||'bg-gray-100'}">${r.grade||'-'}</span></td>
        <td class="text-center text-sm ${r.remark==='Pass'?'text-green-600':'text-red-600'}">${r.remark||'-'}</td>
    </tr>`).join('');
}
function filterResults() {
    const q = document.getElementById('r-search').value.toLowerCase();
    const filtered = allResults.filter(r => (r.student_name||'').toLowerCase().includes(q) || (r.matric_no||'').toLowerCase().includes(q));
    renderResults(filtered);
}
document.addEventListener('DOMContentLoaded', loadDropdowns);
</script>
