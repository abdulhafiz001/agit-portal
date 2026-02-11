<!-- Admin Schedules -->
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><h2 class="text-xl font-bold text-gray-900">Class Schedules</h2>
            <p class="text-sm text-gray-500 mt-1">Manage timetables for all classes</p></div>
        <button onclick="showAddSchedule()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium"><i class="fas fa-plus mr-2"></i>Add Schedule</button>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex gap-3 flex-wrap">
            <select id="sch-filter-class" onchange="loadSchedules()" class="px-3 py-2.5 border border-gray-200 rounded-lg text-sm"><option value="">All Classes</option></select>
        </div>
    </div>
    <div id="schedule-grid" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="table-responsive">
            <table class="data-table"><thead><tr><th>Day</th><th>Time</th><th>Subject</th><th>Class</th><th>Lecturer</th><th>Room</th><th class="text-right">Actions</th></tr></thead>
            <tbody id="sch-tbody"><tr><td colspan="7" class="text-center py-8 text-gray-400">Loading...</td></tr></tbody></table>
        </div>
    </div>
</div>
<div id="sch-modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] flex flex-col">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
            <h3 id="sch-modal-title" class="text-lg font-semibold text-gray-900">Add Schedule</h3>
            <button onclick="Modal.close('sch-modal')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"><i class="fas fa-times"></i></button>
        </div>
        <form id="sch-form" class="p-6 space-y-4 overflow-y-auto flex-1">
            <input type="hidden" id="sch-id">
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Class <span class="text-red-500">*</span></label><select id="sch-class" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Subject <span class="text-red-500">*</span></label><select id="sch-subject" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></select></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Lecturer <span class="text-red-500">*</span></label><select id="sch-lecturer" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></select></div>
            <div class="grid grid-cols-3 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Day <span class="text-red-500">*</span></label>
                    <select id="sch-day" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                        <option value="monday">Monday</option><option value="tuesday">Tuesday</option><option value="wednesday">Wednesday</option><option value="thursday">Thursday</option><option value="friday">Friday</option><option value="saturday">Saturday</option>
                    </select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label><input type="time" id="sch-start" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">End Time</label><input type="time" id="sch-end" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm"></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Room</label><input type="text" id="sch-room" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Hall A"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Notes</label><textarea id="sch-notes" rows="2" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm resize-none"></textarea></div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="Modal.close('sch-modal')" class="flex-1 py-2.5 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">Cancel</button>
                <button type="submit" id="save-sch-btn" class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Save</button>
            </div>
        </form>
    </div>
</div>
<script>
const dayColors = {monday:'blue',tuesday:'emerald',wednesday:'amber',thursday:'purple',friday:'pink',saturday:'red',sunday:'gray'};
async function loadDropdowns() {
    const [cls, sub, lec] = await Promise.all([API.get('/api/admin/classes?all=1'), API.get('/api/admin/subjects?all=1'), API.get('/api/admin/lecturers?all=1')]);
    if (cls?.success) { const o = cls.data.map(c=>`<option value="${c.id}">${escapeHtml(c.name)}</option>`).join(''); document.getElementById('sch-class').innerHTML='<option value="">Select</option>'+o; document.getElementById('sch-filter-class').innerHTML='<option value="">All Classes</option>'+o; }
    if (sub?.success) document.getElementById('sch-subject').innerHTML='<option value="">Select</option>'+sub.data.map(s=>`<option value="${s.id}">${escapeHtml(s.name)} (${escapeHtml(s.code)})</option>`).join('');
    if (lec?.success) document.getElementById('sch-lecturer').innerHTML='<option value="">Select</option>'+lec.data.map(l=>`<option value="${l.id}">${escapeHtml(l.name)}</option>`).join('');
}
async function loadSchedules() {
    const classId = document.getElementById('sch-filter-class').value;
    const data = await API.get('/api/admin/schedules' + (classId ? '?class_id='+classId : ''));
    const tbody = document.getElementById('sch-tbody');
    if (!data?.success || !data.data.length) { tbody.innerHTML='<tr><td colspan="7" class="text-center py-8 text-gray-400">No schedules found</td></tr>'; return; }
    tbody.innerHTML = data.data.map(s => `<tr class="hover:bg-gray-50">
        <td><span class="inline-block px-2 py-1 bg-${dayColors[s.day_of_week]||'gray'}-100 text-${dayColors[s.day_of_week]||'gray'}-700 rounded text-xs font-semibold capitalize">${s.day_of_week}</span></td>
        <td class="text-sm font-mono">${s.start_time?.substring(0,5)} - ${s.end_time?.substring(0,5)}</td>
        <td class="font-medium">${escapeHtml(s.subject_name)} (${escapeHtml(s.subject_code)})</td>
        <td>${escapeHtml(s.class_name)}</td><td>${escapeHtml(s.lecturer_name)}</td>
        <td>${s.room||'-'}</td>
        <td class="text-right"><button onclick="deleteScheduleItem(${s.id})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg"><i class="fas fa-trash text-xs"></i></button></td>
    </tr>`).join('');
}
function showAddSchedule() { document.getElementById('sch-form').reset(); document.getElementById('sch-id').value=''; Modal.open('sch-modal'); }
document.getElementById('sch-form').addEventListener('submit', async(e) => {
    e.preventDefault(); const btn=document.getElementById('save-sch-btn'); setLoading(btn,true);
    const body = {class_id:document.getElementById('sch-class').value, subject_id:document.getElementById('sch-subject').value, lecturer_id:document.getElementById('sch-lecturer').value, day_of_week:document.getElementById('sch-day').value, start_time:document.getElementById('sch-start').value, end_time:document.getElementById('sch-end').value, room:document.getElementById('sch-room').value, notes:document.getElementById('sch-notes').value};
    const id = document.getElementById('sch-id').value;
    const data = id ? await API.put(`/api/admin/schedules/${id}`, body) : await API.post('/api/admin/schedules', body);
    setLoading(btn,false);
    if(data?.success){Toast.success(data.message);Modal.close('sch-modal');loadSchedules();}else if(data)Toast.error(data.message);
});
async function deleteScheduleItem(id) { if(!await confirmAction('Delete this schedule?'))return; const d=await API.delete('/api/admin/schedules/'+id); if(d?.success){Toast.success(d.message);loadSchedules();} }
document.addEventListener('DOMContentLoaded',()=>{loadDropdowns();loadSchedules();});
</script>
